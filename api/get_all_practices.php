<?php 
	/**
	 * 顺序练习接口
	 * @param integer $license 牌照C1/A1/A2/D
	 * @param integer $subject 科目1/4 
     * @param integer $number 第几条
	 * @return object
	 * @author sunweiwei
	 **/
	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
    require 'include/functions.php';
    require 'include/exam.inc.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->any('/','getAllPracticesInTurn');
	$app->run();

	function getAllPracticesInTurn() {
		global $app, $crypt;
        $request = $app->request();
        if ( !$request->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            setapilog('[get_all_practices] [:error] [client ' . $request->getIp() . '] [Method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            echo json_encode($data);
            exit();
        }
        //	获取请求参数并判断合法性
        $license = $request->params('license');//牌照C1/A1/A2/D
        $subject = $request->params('subject');//科目1/4 
        $number = $request->params('number');//第几条
        $validate_result = validate(array('license'=>'NOT_NULL', 'subject'=>'INT', 'number'=>'INT'), $request->params());
        if (!$validate_result['pass']) {
            echo json_encode($validate_result['data']);
            exit();
        }
                
        try{
        	$db = getConnection();
            $sql = "SELECT count(`id`) as c FROM `cs_exams`";
            $stmt = $db->query($sql);
            $count = $stmt->fetch(PDO::FETCH_ASSOC);
            $c = intval($count['c']);
            if ($c) {
                $_limit = $c - $number;
            	$sql = "SELECT * FROM `cs_exams` WHERE `ctype` = '{$license}' AND `stype` = '{$subject}' ORDER BY `id` DESC "; 
                $sql .= " LIMIT {$number}, {$_limit} ";
            	$stmt = $db->query($sql);
            	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            	$db = null;
            	if ($res) {
                    /* 题库里的配图或视频判断 */
                    $base_url = HOST . 'm/assets/images';
                    foreach ( $res as $k => $v ) {
                        //如果含有配图
                        $resource = $v['imageurl'];
                        if ( !empty($resource) ) {
                            $resource_uri = $base_url . $resource;
                            $res[$k]['imageurl'] = $resource_uri;
                            if ( !isset($v['mediatype']) ) {
                                $resource_path= MOBILEPATH . 'assets/images' . $v['imageurl'];
                                if ( file_exists($resource_path) ) {
                                    //获取文件类型
                                    $mime_type = getMimeType( $resource_path );
                                    if ( $mime_type ) {
                                        //如果文件类型探测成功
                                        $mime_type_ar = explode('/', $mime_type);
                                        $res[$k]['mimetype'] = $mime_type;
                                        //根据类型赋值 1 图片 2 视频 3 其它
                                        if ( $mime_type_ar[0] == 'image' ) {
                                            $res[$k]['mediatype'] = 1;
                                        } elseif ( $mime_type_ar[0] == 'video' ) {
                                            $res[$k]['mediatype'] = 2;
                                        } else {
                                            $res[$k]['mediatype'] = 3;
                                        }
                                    } else {
                                        $res[$k]['mediatype'] = 99;
                                        $res[$k]['mimetype'] = 'unknown';
                                    }
                                } else {
                                    //路径不存在
                                    $res[$k]['mediatype'] = 0;
                                    $res[$k]['mimetype'] = '';
                                    //将路径url置空
                                    $res[$k]['imageurl'] = '';
                                }
                            }
                        } else {
                            //无配图
                            $res[$k]['mediatype'] = 0;
                            $res[$k]['mimetype'] = '';
                        }
                    }
                    /* 题库里的配图或视频判断 */
            		$data = array('code'=>200, 'data'=>$res);
            		echo json_encode($data);	
            	} else {
            		$data = array('code'=>104, 'data'=>array());
            		echo json_encode($data);
            	}
            } else {
                $data = array('code'=>104, 'data'=>array());
                echo json_encode($data);
            }

        } catch (PDOException $e) {
            setapilog('get_all_practices: params[license: ' . $license . ', subject: '.$subject.'],error:' . $e->getMessage());
            $data = array('code' => 1, 'data' => '网络错误');
            echo json_encode($data);
        }

	}


?>
