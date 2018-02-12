<?php 
	/**
	 * 删除我的错题或者收藏接口
	 * @param integer $user_id 用户id
	 * @param integer $exam_id 题目id
	 * @return object
	 * @author sunweiwei
	 **/
	require 'Slim/Slim.php';
	require 'include/common.php';
    require 'include/functions.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->any('/','delUserPractice');
	$app->run();

	function delUserPractice() {
		global $app, $crypt;
        $request = $app->request();
        if ( !$request->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            setapilog('[del_user_practice] [:error] [client ' . $request->getIp() . '] [Method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            echo json_encode($data);
            exit();
        }
        //	获取请求参数并判断合法性
        $user_id = $request->params('user_id');//用户id
        $exam_id = $request->params('exam_id');//题目id
        $validate_result = validate(array('user_id'=>'INT','exam_id'=>'INT'), $request->params());
        if (!$validate_result['pass']) {
            echo json_encode($validate_result['data']);
            exit();
        }

        try{
            //删除用户错题/收藏
            $sql = "DELETE FROM `cs_user_exams_question` WHERE `user_id` = '{$user_id}' AND `exam_id` = '{$exam_id}'";
            $result = $db->query($sql);
            if ($result) {
                $data = array('code'=>200, 'data'=>'操作成功');
            } else {
                $data = array('code'=>400, 'data'=>'操作失败');
            }
            $db = null;
            echo json_encode($data);

        } catch (PDOException $e) {
            setapilog('del_user_practice: params[user_id: ' . $user_id . ', exam_id: ' . $exam_id . '],error:' . $e->getMessage());
            $data = array('code' => 1, 'data' => '网络错误');
            echo json_encode($data);
        }

	}


?>