<?php 
	/**
	 * 添加我的错题或者收藏接口
	 * @param integer $user_id 用户id
	 * @param integer $exam_id 题目id
     * @param integer $type 添加类型：1错题 2收藏
     * @param integer $subject 科目类型  1:科目1 4:科目4
     * @param string $license 牌照类型 C1/A1/A2/D
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
	$app->any('/','insertUserPractice');
	$app->run();

	function insertUserPractice() {
		global $app, $crypt;
        $request = $app->request();
        if ( !$request->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            setapilog('[add_user_practice] [:error] [client ' . $request->getIp() . '] [Method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            echo json_encode($data);
            exit();
        }
        //	获取请求参数并判断合法性
        $user_id = $request->params('user_id');//用户id
        $exam_id = $request->params('exam_id');//题目id
        $license = $request->params('license');//牌照C1/A1/A2/D
        $subject = $request->params('subject');//科目1/4 
        $type = $request->params('type');//类型：1错题  2收藏
        $addtime = time();//添加时间
        $validate_result = validate(array('user_id'=>'INT','exam_id'=>'INT', 'license'=>'NOT_NULL', 'subject'=>'INT', 'type'=>'INT'), $request->params());
        if (!$validate_result['pass']) {
            echo json_encode($validate_result['data']);
            exit();
        }

        try{
        	//判断插入的错题收藏是否已经存在
            $db = getConnection();
            $sql = "SELECT * FROM `cs_user_exam_questions` WHERE `user_id` = '{$user_id}' AND `exam_id` = '{$exam_id}'";
            $stmt = $db->query($sql);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($res) {
                $data = array('code'=>105, 'data'=>'参数错误');
                echo json_encode($data);
                exit();    
            }
            //判断请求参数（exam_id&license&subject）的题目在exams表中（id&ctype&stype)是否存在
            $sql = "SELECT * FROM `cs_exams` WHERE `id` = '{$exam_id}' AND `ctype` = '{$license}' AND `stype` = '{$subject}'";
            $stmt = $db->query($sql);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$res) {
                $data = array('code'=>104, 'data'=>'参数错误');
                echo json_encode($data);
                exit();    
            }
            //插入数据
            $sql = "INSERT INTO `cs_user_exam_questions` (`user_id`, `ctype`, `stype`, `exam_id`, `type`, `addtime`) VALUES ('{$user_id}', '{$license}', '{$subject}', '{$exam_id}', '{$type}', '{$addtime}')";
            $result = $db->query($sql);
            if ($result) {
                $data = array('code'=>200, 'data'=>'操作成功');
            } else {
                $data = array('code'=>400, 'data'=>'操作失败');
            }
            $db = null;
            echo json_encode($data);

        } catch (PDOException $e) {
            setapilog('add_user_practice: params[user_id: ' . $user_id . ', exam_id: ' . $exam_id . ', license: ' . $license . ', subject: '.$subject.', type: '.$type.'],error:' . $e->getMessage());
            $data = array('code' => 1, 'data' => '网络错误');
            echo json_encode($data);
        }

	}


?>