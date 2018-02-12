<?php 
	/**
	 * 获取我的错题或者收藏接口
	 * @param integer $user_id 用户id
     * @param integer $type 类型：1错题 2收藏
	 * @return object
	 * @author sunweiwei
	 **/
	require 'Slim/Slim.php';
	require 'include/common.php';
    require 'include/functions.php';
	require 'include/crypt.php';
    require 'include/exam.inc.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->any('/','getUserPractice');
	$app->run();

	function getUserPractice() {
		global $app, $crypt;
        $request = $app->request();
        if ( !$request->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            setapilog('[get_user_practice] [:error] [client ' . $request->getIp() . '] [Method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            echo json_encode($data);
            exit();
        }
        //	获取请求参数并判断合法性
        $user_id = $request->params('user_id');//用户id
        $type = $request->params('type');//类型：1错题  2收藏
        $validate_result = validate(array('user_id'=>'INT', 'type'=>'INT'), $request->params());
        if (!$validate_result['pass']) {
            echo json_encode($validate_result['data']);
            exit();
        }

        try{
        	//查看用户错题/收藏
            $db = getConnection();
            $sql = "SELECT e.* FROM `cs_exams` as e LEFT JOIN `cs_user_exam_questions` as u ON u.`exam_id` = e.`id` WHERE u.`user_id` = '{$user_id}' AND u.`type` = '{$type}'";
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
            } else {
                $data = array('code'=>104, 'data'=>array());
            }
            $db = null;
            echo json_encode($data);

        } catch (PDOException $e) {
            setapilog('get_user_practice: params[user_id: ' . $user_id . ', type: '.$type.'],error:' . $e->getMessage());
            $data = array('code' => 1, 'data' => '网络错误');
            echo json_encode($data);
        }

	}


?>
