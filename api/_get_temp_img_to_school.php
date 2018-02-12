<?php 
	/**
	 * 批量移动图片
	 * @param $page 页码 
	 * @param $filtercityid 城市ID 
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 * 获取学校列表：http://api.jxedt.com/list/?os=android&time=1452210078833&lon=117.139461&productid=1&pageindex=1&filterparams={"":"","filtercityid":"234"}&type=jx&channel=7&lat=31.833969&version=3.9.6&cityid=176
	 * 获取学校详情：http://api.jxedt.com/detail/23441/?type=jx
	 **/
	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->get('/','moveSchoolImg');
	$app->run();

	function moveSchoolImg() {
		Global $app, $crypt;
		try {
			$res = checkdir('upload/school');  
			if($res) {
				echo "迁移成功";
			} else {
				echo "已迁移完成";
			}

		} catch(PDOException $e) {
			setapilog('_get_temp_img_to_school:params[], error:'.$e->getMessage());	
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			exit;
		}
	}

	function checkdir($basedir){ 
	  if ($dh = opendir($basedir)) { 
	    while (($file = readdir($dh)) !== false) { 
	      if ($file != '.' && $file != '..'){ 
	        if (!is_dir($basedir."/".$file)) { 
	        	if(!file_exists('../sadmin/'.$basedir."/".$file)) {
					$res = rename("$basedir/$file", "../sadmin/$basedir/$file");
					if($res) {
						return true;
					} else {
						return false;
					}
	        	}
	        }else{ 
	          $dirname = $basedir."/".$file;
	          if(is_dir($dirname) && !file_exists("../sadmin/$basedir/$file")) {
	          	mkdir("../sadmin/$basedir/$file");
	          }
     		  checkdir($dirname); 
	        } 
	      } 
	    } 
	  closedir($dh); 
	  } 
	}

	  