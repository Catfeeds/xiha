<?php
    /**
    * ecoach/api/upload_exam_record.php
    * @param FILES $exam_history 训练历史
    * @param int $user_id 用户id
    * @param int site_id  场地id
    * @param int car_id 车辆id
    * @param string identity_no 身份id
    * @author gaodacheng
    **/

    require 'slim3/vendor/autoload.php';
    require 'include/common.php';
    require 'include/crypt.php';
    require 'include/functions.php';

    $app = new \Slim\App;
    $checkProxyHeaders = true; // Note: Never trust the IP address for security processes!
    $trustedProxies = ['10.0.0.1', '10.0.0.2']; // Note: Never trust the IP address for security processes!
    $app->add(new RKA\Middleware\IpAddress($checkProxyHeaders, $trustedProxies));
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'checkUploadExamHistory');
    $app->run();

    function checkUploadExamHistory($request, $response, $args) {

        // ready to return array $data
        $data = array();
        !defined('RES_PREFIX') && define('RES_PREFIX', HOST.'ecoach/api/');

        //验证请求方式 POST
        if ( !$request->isPost() ) {
            setapilog('[check_upload_exam_history] [:error] [client ' . $request->getAttribute('ip_address') . '] [method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            $data = array('code' => 106, 'data' => '请求错误');
            return $response->withJson($data, 200);
        }

        $ready_to_check = array(
            'user_id' => 'INT',
            'site_id' => 'INT',
            'car_id' => 'INT',
            'identity_no' => 'STRING',
        );
        $validate_ok = validate($ready_to_check, $request->getParams());
        if ( !$validate_ok['pass'] ) {
            $data = $validate_ok['data'];
            return $response->withJson($data, 200);
        }
        $p = $request->getParams();
        $user_id = $p['user_id'];
        $site_id = $p['site_id'];
        $car_id = $p['car_id'];
        $identity_no = $p['identity_no'];

        //保存
        $error = $_FILES['exam_history']['error'];
        if ( $error == UPLOAD_ERR_OK ) {
            $ext = '.txt';
            $tmp_name = $_FILES['exam_history']['tmp_name'];
            if ( $_FILES['exam_history']['type'] == 'text/plain' ) {
                $ext = '.txt';
            } else {
                $data = array(
                    'code' => '109',
                    'data' => '格式不支持',
                );
                setapilog('[check_upload_exam_history] [:error] [client ' . $request->getAttribute('ip_address') . '] [file_type ' . $_FILES['exam_history']['type'] . ']');
                return $response->withJson($data, 200);
            }
            $name = 'exam_history_' . time() . substr( str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6 ) . "$ext";
            $path = '../upload/' . date('Ymd', time());
            if ( !file_exists( $path ) ) {
                //目录不存在，创建目录
                if ( mkdir( $path, 0777, true ) ) {
                    //移动文件到目标文件夹
                    $move_ok = move_uploaded_file($tmp_name, "$path/$name");
                } else {
                    $data = array( 'code' => '400', 'data' => '上传失败' );
                    setapilog('[upload_exam_record] [:error] [client ' . $request->getAttribute('ip_address') . '] [400 创建目录失败]');
                    return $response->withJson($data, 200);
                }
            } else {
                //已经存在目录，移动文件到目标文件夹
                $move_ok = move_uploaded_file($tmp_name, "$path/$name");
            }
            if ( $move_ok ) {
                $data = array(
                    'code' => '200',
                    'data' => '上传保存成功',
                );
            } else {
                $data = array( 
                    'code' => '400', 
                    'data' => '上传保存失败',
                );
                setapilog('[upload_exam_record] [:error] [client ' . $request->getAttribute('ip_address') . '] [400 上传保存失败]');
                return $response->withJson($data, 200);
            }
        }

        //fake static file
        //$path = '../upload/20160509';
        //$name = 'exam_history_14627754484a7gEh.txt';

        //本地保存的copy
        if ( !file_exists("$path/$name") ) {
            $data = array(
                'code' => '400',
                'data' => '操作失败',
            );
            return $response->withJson($data, 200);
        } else {
            $exam_history_txt = file_get_contents("$path/$name");
            //unlink("$path/$name");
        }

        //读数据
        if ( empty($exam_history_txt) ) {
            $data = array(
                'code' => '400',
                'data' => '操作失败',
            );
            return $response->withJson($data, 200);
        }

        //数据格式单条: CHECK,site_id,car_id,user_id,identity_id,exam_id,time_interval;
        $exam_history = array_filter(explode(';', $exam_history_txt));

        //ready for storing the results of checking
        $check_results = array();
        $client_exam_history = array();
        try {
            //建立数据库连接
            $db = getConnection();

            //表名
            $table_buf = DBPREFIX . 'exam_history';

            //公共字段缓存
            $fields_buf = array(
                'site_id',
                'car_id',
                'user_id',
                'identity_no',
                'exam_id',
                'time_interval',
            );

            //循环查询
            foreach ( $exam_history as $key => $val ) {
                $sql = '';
                $original_exam_history = explode(',', $val);
                //数据头校验
                if ( $original_exam_history[0] !== 'CHECK' || count($original_exam_history) !== 7 ) {
                    //跳过有问题的数据
                    continue;
                }
                $sql = " SELECT `id` FROM `{$table_buf}` WHERE ";
                foreach ( $fields_buf as $index => $fields_single ) {
                    if ( count($fields_buf) === ($index+1) ) {
                        $sql .= " `{$fields_single}` = '{$original_exam_history[$index+1]}' ";
                        break;
                    }
                    $sql .= " `{$fields_single}` = '{$original_exam_history[$index+1]}' AND ";
                }
                $stmt = $db->query($sql);
                $has_uploaded = $stmt->fetch(PDO::FETCH_ASSOC);
                $client_exam_history[] = $original_exam_history[5];
                //如未查询到此记录
                if ( $has_uploaded === false ) {
                    $check_results['upload'][] = $original_exam_history[5];
                }
            }
            if ( !array_key_exists('upload', $check_results) ) {
                $check_results['upload'] = array();
            }

            $fields_buf = array(
                'user_id',
                'site_id',
                'car_id',
                'identity_no',
            );
            $values_buf = array(
                $user_id,
                $site_id,
                $car_id,
                $identity_no,
            );
            $table_buf = DBPREFIX . 'exam_history';

            $sql = " SELECT `exam_id` FROM `{$table_buf}` WHERE ";
            foreach ( $fields_buf as $key => $val ) {
                if ( ($key) === (count($fields_buf) - 1) ) {
                    $sql .= " `{$val}` = '{$values_buf[$key]}' ";
                    continue;
                }
                $sql .= " `{$val}` = '{$values_buf[$key]}' AND ";
            }
            $stmt = $db->query($sql);
            $server_exam_history = array();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ( !empty($res) ) {
                foreach ( $res as $key => $val ) {
                    $server_exam_history[] = $val['exam_id'];
                }
            }
            $download_exam_history = array_values(array_diff($server_exam_history, $client_exam_history));
            if ( !empty($download_exam_history) ) {
                $fields_buf = array(
                    'user_id',
                    'site_id',
                    'car_id',
                    'identity_no',
                );
                $values_buf = array(
                    $user_id,
                    $site_id,
                    $car_id,
                    $identity_no,
                );
                $sql = " SELECT `text_url` FROM `{$table_buf}` WHERE ";
                foreach ( $fields_buf as $key => $val ) {
                    $sql .= " `{$val}` = '{$values_buf[$key]}' AND ";
                }
                $sql .= " `exam_id` IN ('".implode("','", $download_exam_history)."') ";
                $stmt = $db->query($sql);
                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ( !empty($res) ) {
                    foreach ( $res as $key => $val ) {
                        $check_results['download'][] = RES_PREFIX.$val['text_url'];
                    }
                }
            } else {
                $check_results['download'] = array();
            }

            //关闭数据库
            $db = null;

            if ( empty($check_results) ) {
                $data = array(
                    'code' => '104',
                    'data' => '已是最新,不需要同步',
                );
                setapilog('[check_upload_exam_history] [不需要任何上传]');
            } else {
                $data = array(
                    'code' => 200,
                    'data' => $check_results,
                );
            }

            return $response->withJson($data, 200);
        } catch ( PDOException $e ) {
            setapilog('[check_upload_exam_history] [:error] [client ' . $request->getAttribute('ip_address') . '] [param ' . serialize($request->getParams()) . '] [' . $e->getLine() . ' '.$e->getMessage().']');
            $data = array('code' => 1, 'data' => '网络异常');
            return $response->withJson($data, 200);
        }
    } /* checkUploadExamHistory End */

	/**
	 * 模拟post进行url请求
	 * @param string $url
	 * @param array $post_data
	 */
	function request_post($url = '', $post_data = array()) {
	    if (empty($url) || empty($post_data)) {
	        return false;
	    }
	    
	    $o = "";
	    foreach ( $post_data as $k => $v ) 
	    { 
	        $o.= "$k=" . urlencode( $v ). "&" ;
	    }
	    $post_data = substr($o,0,-1);

	    $postUrl = $url;
	    $curlPost = $post_data;
	    $ch = curl_init();//初始化curl
	    curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
	    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
	    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
	    $data = curl_exec($ch);//运行curl
	    curl_close($ch);
	    
	    return $data;
	} /* request_post End */
?>
