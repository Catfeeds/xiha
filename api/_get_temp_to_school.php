<?php  
	/**
	 * 抓取驾校列表并存到正式库中
	 * @param $page 页码 
	 * @param $filtercityid 城市ID 
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 * 获取学校列表：http://api.jxedt.com/list/?os=android&time=1452210078833&lon=117.139461&productid=1&pageindex=1&filterparams={"":"","filtercityid":"234"}&type=jx&channel=7&lat=31.833969&version=3.9.6&cityid=176
	 * 获取学校详情：http://api.jxedt.com/detail/23441/?type=jx
	 * 获取学校轮播图：http://api.jxedt.com/detail/23441/photo/list/?type=jx
	 **/

	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','getSchoolList');
	$app->run();

	// 获取教练学员信息
	function getSchoolList() {
		Global $app, $crypt;
		$request = $app->request();
		$page = $request->params('page');
		$page = isset($page) ? $page : 1;
		$limit = 50;
		$start = ($page - 1) * $limit;

		try {
			$db = getConnection();
			$sql = "SELECT * FROM `cs_temp_school` WHERE `is_finished` = 1 ORDER BY `id` ASC LIMIT $start, $limit";
			$stmt = $db->query($sql);
			$school_temp_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
			// $sql = "";
			if($school_temp_list) {
				foreach ($school_temp_list as $key => $value) {
					$sql = "SELECT * FROM `cs_school` WHERE `s_school_name` = '{$value['name']}'";
					$stmt = $db->query($sql);
					$school_info = $stmt->fetch(PDO::FETCH_ASSOC);
					if($school_info) {

					} else {
						$tel = explode(',', $value['tel']);
						$sql = "INSERT INTO `cs_school` (";
						$sql .= " `l_school_id`,";
						$sql .= " `s_school_name`,";
						$sql .= " `s_frdb`,";
						$sql .= " `s_frdb_mobile`,";
						$sql .= " `s_frdb_tel`,";
						$sql .= " `s_yyzz`,";
						$sql .= " `s_zzjgdm`,";
						$sql .= " `i_dwxz`,";
						$sql .= " `i_wdid`,";
						$sql .= " `s_address`,";
						$sql .= " `dc_base_je`,";
						$sql .= " `dc_bili`,";
						$sql .= " `s_yh_name`,";
						$sql .= " `s_yh_zhanghao`,";
						$sql .= " `s_yh_huming`,";
						$sql .= " `s_shuoming`,";
						$sql .= " `province_id`,";
						$sql .= " `city_id`,";
						$sql .= " `area_id`,";
						$sql .= " `shifts_intro`,";
						$sql .= " `s_thumb`,";
						$sql .= " `s_location_x`,";
						$sql .= " `s_location_y`,";
						$sql .= " `s_imgurl`,";
						$sql .= " `addtime`,";
						$sql .= " `brand`,";
						$sql .= " `is_show`";
						$sql .= ") VALUES (";
						$sql .= "NULL,";
						$sql .= " '{$value['name']}',";
						$sql .= " '法人代表',";
						$sql .= " '{$tel[0]}',";
						$sql .= " '{$tel[0]}',";
						$sql .= " '{$value['imageurl']}',";
						$sql .= " '抓取',";
						$sql .= " '1',";
						$sql .= " '{$value['address']}',";
						$sql .= " '{$value['address']}',";
						$sql .= " '1.0',";
						$sql .= " '0.5',";
						$sql .= " '中国银行',";
						$sql .= " '银行账号',";
						$sql .= " '银行户名',";
						$sql .= " '{$value['moredesc']}',";
						$sql .= " '{$value['province_id']}',";
						$sql .= " '{$value['city_id']}',";
						$sql .= " '{$value['area_id']}',";
						$sql .= " '{$value['descarea']}',";
						$sql .= " '{$value['imageurl']}',";
						$sql .= " '{$value['lng']}',";
						$sql .= " '{$value['lat']}',";
						$sql .= " '',";
						$sql .= " '".time()."',";
						$sql .= " '1',";
						$sql .= " '2');";
						$stmt = $db->query($sql);
						if($stmt) {
							$last_insert_id = $db->lastInsertId();
							// 创建目录
							$upload = 'upload/school/banner';
							$path = $upload.'/'.$last_insert_id;
							if(!is_dir($path)) {
								$path = createFile($last_insert_id, $upload);
							}

							// 抓取轮播图
							$post_data = array(
								'type' => 'jx'	
							);
							$url = "http://api.jxedt.com/detail/".$value['infoid']."/photo/list";
							$res = request_post($url, $post_data);
							$result = json_decode($res, true);
							$photolist = array();
							if($result) {
								$photolist = $result['result']['list']['photolist'];
							}
							// print_r($photolist);
							$imgurl_list = array();
							foreach ($photolist as $k => $v) {
								$filename = $path.'/'.uniqid().'.jpg';
								$imgurl_list[] = getImage($v, $filename, 1);
							}
							$sql = "UPDATE `cs_school` SET `s_imgurl` = '".json_encode($imgurl_list)."' WHERE `l_school_id` = '{$last_insert_id}'";
							$stmt = $db->query($sql);

							$str = "当前驾校ID：".$value['infoid']."，上传完成！(".date('Y-m-d H:i', time()).")";
							echo $str.'<br>';
							setlog($str);
						} else {
							$str = "当前驾校ID：".$value['infoid']."，上传出错！(".date('Y-m-d H:i', time()).")";
							echo $str.'<br>';
							setlog($str);
						}
					}
				}
				$data = array('code'=>200, 'data'=>"操作完成");
				echo json_encode($data);		
			}

		} catch(PDOException $e) {
			setapilog('_get_temp_to_school:params[], error:'.$e->getMessage());	
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			exit;
		}
	}

	function setlog($word='') {
		$fp = fopen("schoollog.txt","a");
		flock($fp, LOCK_EX) ;
		fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time()+8*3600)."\n".$word."\n");
		flock($fp, LOCK_UN);
		fclose($fp);

	}

	/*
	*功能：php多种方式完美实现下载远程图片保存到本地
	*参数：文件url,保存文件名称，使用的下载方式
	*当保存文件名称为空时则使用远程文件原来的名称
	*/
	function getImage($url, $filename='', $type=0){
	    if($url==''){return false;}
	    if($filename==''){
	        $ext=strrchr($url,'.');
	        if($ext!='.gif' && $ext!='.jpg'){return false;}
	        $filename=time().$ext;
	    }
	    //文件保存路径
	    if($type){
	  $ch=curl_init();
	  $timeout=5;
	  curl_setopt($ch,CURLOPT_URL,$url);
	  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
	  $img=curl_exec($ch);
	  curl_close($ch);
	    }else{
	     ob_start();
	     readfile($url);
	     $img=ob_get_contents();
	     ob_end_clean();
	    }
	    $size=strlen($img);
	    //文件大小
	    $fp2=@fopen($filename,'a');
	    fwrite($fp2,$img);
	    fclose($fp2);
	    return $filename;
	}

	// 创建文件夹
	function createFile($id, $path) {
		if(!file_exists($path.'/'.$id)){ 
			if(mkdir($path.'/'.$id)) {
				return $path.'/'.$id;
			} else {
				return false;
			}
				
		} else {
			return false;
		}
	}

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
	}
?>