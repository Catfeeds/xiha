<?php
    /**
    * ecoach/api/upload_exam_record.php
    * @param int $user_id 用户id
    * @param string $identity_no 身份证号
    * @param int $time_interval timestamp
    * @param int $exam_id 训练id
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
    $app->any('/', 'uploadExamRecord');
    $app->run();

    function uploadExamRecord($request, $response, $args) {

        // ready to return array $data
        $data = array();

        //验证请求方式 POST
        if ( !$request->isPost() ) {
            setapilog('[update_user_info] [:error] [client ' . $request->getAttribute('ip_address') . '] [method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            $data = array('code' => 106, 'data' => '请求错误');
            return $response->withJson($data, 200);
        }

        //验证请求参数列表
        $validate_ok = validate(array(
            'site_id'       => 'INT',
            'car_id'        => 'INT',
            'user_id'       => 'INT',
            'identity_no'   => 'STRING',
            'exam_id'       => 'INT',
            'time_interval' => 'INT',
        ), $request->getParams());
        if ( !$validate_ok['pass'] ) {
            $data = $validate_ok['data'];
            return $response->withJson($data, 200);
        } 

        $p = $request->getParams();
        $site_id = $p['site_id'];
        $car_id = $p['car_id']; 
        $user_id = $p['user_id'];
        $identity_no = $p['identity_no'];
        $exam_id = $p['exam_id'];
        $time_interval = $p['time_interval'];

        //保存
        $error = $_FILES['exam_record']['error'];
        if ( $error == UPLOAD_ERR_OK ) {
            $ext = '.txt';
            $tmp_name = $_FILES['exam_record']['tmp_name'];
            if ( $_FILES['exam_record']['type'] == 'text/plain' ) {
                $ext = '.txt';
            } else {
                $data = array(
                    'code' => '109',
                    'data' => '格式不支持',
                );
                setapilog('[upload_exam_record] [:error] [client ' . $request->getAttribute('ip_address') . '] [file_type ' . $_FILES['exam_record']['type'] . ']');
                return $response->withJson($data, 200);
            }
            $name = 'exam_record_' . time() . substr( str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6 ) . "$ext";
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
                    'data' => '上传保存失败' 
                );
                return $response->withJson($data, 200);
                setapilog('[upload_exam_record] [:error] [client ' . $request->getAttribute('ip_address') . '] [400 上传保存失败]');
            }
        }

        //return $response->withJson($data, 200);

        try {
            //建立数据库连接
            $db = getConnection();

            /*
             * 查询是否已经有插入历史训练记录
             */
            //字段缓存区
            $fields_buf = array(
                'site_id',
                'car_id',
                'user_id',
                'exam_id',
                'identity_no',
                'text_url',
                'time_interval',
            );
            //数值缓存区
            $values_buf = array(
                $site_id,
                $car_id,
                $user_id,
                $exam_id,
                $identity_no,
                "$path/$name",
                $time_interval,
            );
            //查询语句
            $sql = " SELECT `id` FROM `".DBPREFIX."exam_history` WHERE `{$fields_buf[0]}` = '{$values_buf[0]}' ";
            foreach ( $fields_buf as $key => $val ) {
                if ( $key === 0 ) {
                    continue;
                }
                $sql .= " AND `{$fields_buf[$key]}` = '{$values_buf[$key]}' ";
            }
            //返回查询语句
            //return $response->withJson($sql);
            //执行查询
            $stmt = $db->query($sql);
            $has_inserted = $stmt->fetch(PDO::FETCH_ASSOC);
            if ( $has_inserted !== false ) {
                $data = array(
                    'code' => '105',
                    'data' => '您已经上传过啦',
                );
                //删除刚上传的文件
                unlink("$path/$name");
                return $response->withJson($data, 200);
            }
            /**
             * 插入一条新的历史记录，并调用解析接口去导入其它教学数据
             */
            $sql = " INSERT INTO `".DBPREFIX."exam_history` (`".implode("`,`", $fields_buf)."`) VALUES ('".implode("','", $values_buf)."') ";
            //返回查询语句
            //return $response->withJson($sql);
            //执行插入查询
            $stmt = $db->query($sql);
            //var_dump($stmt);
            if ( is_object($stmt) && $stmt instanceof PDOStatement ) {
                //return $response->withJson($data, 200);
                $params = array(
                    'user_id' => $user_id,
                    'exam_record' => "$path/$name",
                );
                $update_data_ok = request_post(HOST . 'ecoach/api/parse_exam_record.php', $params);
                if ( $update_data_ok ) {
                    return $response->withJson($data, 200);
                } else {
                    $data = array(
                        'code' => 400,
                        'data' => '保存失败，请稍后重试',
                    );
                }
            }

            //关闭数据库
            $db = null;
        } catch ( PDOException $e ) {
            setapilog('[upload_exam_record] [:error] [client ' . $request->getAttribute('ip_address') . '] [user_id,type % ' . $user_id . '] [1 '.$e->getMessage().']');
            $data = array('code' => 1, 'data' => '网络异常');
            return $response->withJson($data, 200);
        }
    } /* uploadExamRecord End */

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
