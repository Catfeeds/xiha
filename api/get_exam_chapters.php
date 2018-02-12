<?php 
	/**
	 * 获取章节名称接口
	 * @param integer $license 牌照C1/A1/A2/D
	 * @param integer $subject 科目1/4 
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
	$app->any('/','getChapterNames');
	$app->run();

	function getChapterNames() {
		global $app, $crypt;
        $request = $app->request();
        if ( !$request->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            setapilog('[get_exam_chapters] [:error] [client ' . $request->getIp() . '] [Method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            echo json_encode($data);
            exit();
        }
        //	获取请求参数并判断合法性
        $license = $request->params('license');//牌照C1/A1/A2/D
        $subject = $request->params('subject');//科目1/4 
        $validate_result = validate(array('license'=>'NOT_NULL', 'subject'=>'INT'), $request->params());
        if (!$validate_result['pass']) {
            echo json_encode($validate_result['data']);
            exit();
        }

        try{
        	$db = getConnection();
        	$sql = "SELECT `title`,`cid`,`id` FROM `cs_exam_chapters` WHERE `ctype` = '{$license}' AND `stype` = '{$subject}'";
        	$stmt = $db->query($sql);
        	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $list = array();
        	if ($res) {
                foreach ($res as $key => $value) {
                    $sql = "SELECT COUNT(`id`) as num FROM `cs_exams` WHERE `chapterid` = '{$value['cid']}' AND `ctype` = '{$license}' AND `stype` = '{$subject}' ";
                    $stmt = $db->query($sql);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $list[$key]['title'] = $value['title'];
                    $list[$key]['cid'] = $value['cid'];
                    $list[$key]['id'] = $value['id'];
                    $list[$key]['count'] = $result['num'];
                }
                 
        		$data = array('code'=>200, 'data'=>$list);
        	} else {
        		$data = array('code'=>104, 'data'=>array());
        	}
            $db = null;
    		echo json_encode($data);

        } catch (PDOException $e) {
            setapilog('get_exam_chapters: params[license: ' . $license . ', subject: '.$subject.'],error:' . $e->getMessage());
            $data = array('code' => 1, 'data' => '网络错误');
            echo json_encode($data);
        }

	}


?>
