<?php  
	/**
	 * 更新用户头像
	 * @param $user_id int 学员ID
	 * @param photo file 头像信息
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/
	
	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	require 'include/upload.php';

	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','updateUserPhoto');
	$app->run();

	// 获取教练学员列表
	function updateUserPhoto() {
		Global $app, $crypt;
		$request = $app->request();
		$user_id = $request->params('user_id');
		// $photo = $_FILES['photo'];

		try {
			$db = getConnection();
			//上传配置
			$config = array(
			    "savePath" => "upload/user/" ,             //存储文件夹
			    "maxSize" => 2000 ,                   //允许的文件最大尺寸，单位KB
			    "allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp" )  //允许的文件格式
			);
			//上传文件目录
			$Path = "../upload/user/";

			//背景保存在临时目录中
			$config[ "savePath" ] = $Path;
			$up = new Uploader("photo" , $config);

			$info = $up->getFileInfo();
			if($info['state'] == 'SUCCESS') {
				// 更新user信息
				$sql= "UPDATE `cs_users_info` SET `user_photo` = '".$info['url']."' WHERE `user_id` = $user_id";
				$stmt = $db->query($sql);
				if($stmt) {
					$data = array('code'=>200, 'data'=>$info['url']);
				} else {
					$data = array('code'=>-1, 'data'=>'上传失败');
				}
			} else {
				$data = array('code'=>-1, 'data'=>'上传失败');
			}
			echo json_encode($data);

		} catch(PDOException $e) {
			// $data = array('code'=>1, 'data'=>$e->getMessage());
			setapilog('update_user_photo:params[user_id:'.$user_id.'], error:'.$e->getMessage());	
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			exit;
		}
	}


?>