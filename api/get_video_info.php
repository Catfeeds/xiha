<?php

	/**
	 * 获取视频信息
	 * @param $lesson_type int 科目类型 2：科目二 3：科目三
	 * @param $video_type int 视频类型 1 2 3 4
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 **/
	
	require 'Slim/Slim.php';
	require 'include/common.php';
	require 'include/crypt.php';
	require 'include/phpQuery/phpQuery.php';

	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
	$app->post('/','getVideoInfo');
	$app->run();
	// 
	// 随机生成6位验证码并保存到数据库
	function getVideoInfo() {
		Global $app, $crypt;
		$request = $app->request();
		$lesson_type = $request->params('lesson_type');
		$video_type = $request->params('video_type');
		$lesson_type = empty($lesson_type) ? 2 : $lesson_type;
		$video_type = empty($video_type) ? 1 : $video_type;

		try {
			// $video_arr = array(
			// 	'2'=>array(
			// 		'1'=>array(
			// 			'video_url'=>'http://180.153.52.71:8001/video/640_360_495kbps/k2_dcrk.mp4',
			// 			'video_title'=>'倒车入库',
			// 			'video_time'=>'4:42'
			// 		),
			// 		'2'=>array(
			// 			'video_url'=>'http://180.153.52.71:8001/video/640_360_495kbps/k2_qxxs.mp4',
			// 			'video_title'=>'曲线行驶',
			// 			'video_time'=>'2:54'
			// 		),
			// 		'3'=>array(
			// 			'video_url'=>'http://180.153.52.71:8001/video/640_360_495kbps/k2_zjzw.mp4',
			// 			'video_title'=>'直角转弯',
			// 			'video_time'=>'2:33'
			// 		),
			// 		'4'=>array(
			// 			'video_url'=>'http://180.153.52.71:8001/video/640_360_495kbps/k2_pdddtc.mp4',
			// 			'video_title'=>'坡道定点和起步',
			// 			'video_time'=>'3:09'
			// 		),
			// 		'5'=>array(
			// 			'video_url'=>'http://180.153.52.71:8001/video/640_360_495kbps/k2_cftc.mp4',
			// 			'video_title'=>'侧方停车',
			// 			'video_time'=>'3:45'
			// 		)
			// 	),
			// 	'3'=>array(
			// 		'1'=>array(
			// 			'video_url'=>'http://180.153.52.71:8001/video/640_360_495kbps/k3_cchcgc.mp4',
			// 			'video_title'=>'跟车+超车+会车',
			// 			'video_time'=>'2:34'
			// 		),
			// 		'2'=>array(
			// 			'video_url'=>'http://180.153.52.71:8001/video/640_360_495kbps/k3_tgxxlk.mp4',
			// 			'video_title'=>'倒车入库',
			// 			'video_time'=>'3:24'
			// 		),
			// 		'3'=>array(
			// 			'video_url'=>'http://180.153.52.71:8001/video/640_360_495kbps/k2_dcrk.mp4',
			// 			'video_title'=>'通过路口+通过人行横道+通过学校公交站',
			// 			'video_time'=>'3:15'
			// 		),
			// 		'4'=>array(
			// 			'video_url'=>'http://180.153.52.71:8001/video/640_360_495kbps/k3_dtbgcd.mp4',
			// 			'video_title'=>'变更车道+掉头+靠边停车',
			// 			'video_time'=>'2:56'
			// 		)
			// 	)	
			// );
			
			$video_arr = array(
				'2'=>array(//科目二
					'1'=>array(
						'video_url'=>'http://toutiao.com/i6223561274278543874/',
						'video_title'=>'倒车入库',
						'video_time'=>'4:42'
					),
					'2'=>array(
						'video_url'=>'http://toutiao.com/i6223609442529706498/',
						'video_title'=>'曲线行驶',
						'video_time'=>'2:54'
					),
					'3'=>array(
						'video_url'=>'http://toutiao.com/i6223610324461814273/',
						'video_title'=>'直角转弯',
						'video_time'=>'2:33'
					),
					'4'=>array(
						'video_url'=>'http://toutiao.com/i6223604730757644801/',
						'video_title'=>'坡道定点停车和起步',
						'video_time'=>'3:09'
					),
					'5'=>array(
						'video_url'=>'http://toutiao.com/i6223554707994968577/',
						'video_title'=>'侧方停车',
						'video_time'=>'3:45'
					),
					'6'=>array(
						'video_url'=>'http://toutiao.com/i6255192151274029570/',
						'video_title'=>'考试流程演示',
						'video_time'=>'5:36'
					),
					'7'=>array(
						'video_url'=>'http://toutiao.com/i6254373382691750401/',
						'video_title'=>'侧方位停车',
						'video_time'=>'3:06'
					),
					'8'=>array(
						'video_url'=>'http://toutiao.com/i6254375063852679681/',
						'video_title'=>'坡道定点停车与起步',
						'video_time'=>'2:59'
					),
					'9'=>array(
						'video_url'=>'http://toutiao.com/i6254371980611420673/',
						'video_title'=>'教学备注',
						'video_time'=>'0:21'
					),
				),
				'3'=>array( // 科目三
					'1'=>array(
						'video_url'=>'http://toutiao.com/i6224298883833397761/',
						'video_title'=>'跟车+超车+会车',
						'video_time'=>'2:34'
					),
					'2'=>array(
						'video_url'=>'http://toutiao.com/i6222939658863510017/',
						'video_title'=>'起步夜间行驶',
						'video_time'=>'3:24'
					),
					'3'=>array(
						'video_url'=>'http://toutiao.com/i6224312620556485121/',
						'video_title'=>'通过路口+通过人行横道+通过学校公交站',
						'video_time'=>'3:15'
					),
					'4'=>array(
						'video_url'=>'http://toutiao.com/i6224300591913370114/',
						'video_title'=>'变更车道+掉头+靠边停车',
						'video_time'=>'2:56'
					),
					'5'=>array(
						'video_url'=>'http://toutiao.com/i6254347508781154817/',
						'video_title'=>'考生考前须知',
						'video_time'=>'0:58'
					),
					'6'=>array(
						'video_url'=>'http://toutiao.com/i6254346578056708609/',
						'video_title'=>'考试路线介绍',
						'video_time'=>'1:23'
					),
					'7'=>array(
						'video_url'=>'http://toutiao.com/i6254344971550196226/',
						'video_title'=>'考试过程操作流程',
						'video_time'=>'11:51'
					),
				)	
			);

			$video_info = isset($video_arr[$lesson_type][$video_type]) ? $video_arr[$lesson_type][$video_type] : '';
			if($video_info) {
				$url = $video_info['video_url'];
				@phpQuery::newDocumentFile($url);

				$expire = '86400';
				$file_name = 'video-'.$lesson_type.'-'.$video_type.'.txt';

				$path = 'video/'.$file_name;
				$container_txt = '';
				if(file_exists($path)) {
					$container_txt = file_get_contents($path);
					if(empty($container_txt)) {
						$container_txt = pq("#container")->html();
						setvideoinfo($path, $container_txt);
					} else {
						if(time() - @filemtime($path) > $expire) {
							$container_txt = pq("#container")->html();
							setvideoinfo($path, $container_txt);	
						}
					}
						
				} else {
					$container_txt = pq("#container")->html();
					setvideoinfo($path, $container_txt);
				}
				$container_txt = file_get_contents($path);
				if($container_txt) {
					$tt_videoid = '';
					if(preg_match('/<div class=\"tt-video-box\"[\s]tt-videoid=\"([\w\S]*)\"/', $container_txt, $matches)) {
						$tt_videoid = $matches[1];
					}
					$url = "http://i.snssdk.com/video/urls/1/toutiao/mp4/".$tt_videoid;
				}

				$result = file_get_contents($url);
				$video_arr = json_decode($result, true);
				$video_list = array();
				if($video_arr['code'] == 0 && $video_arr['message'] == 'success') {
					$video_list = $video_arr['data']['video_list'];
					$main_url = $video_list['video_1']['main_url'];
					$basedecode = base64_decode($main_url);
				}
				$video_info['video_url'] = $basedecode;
				$video_size = get_headers($video_info['video_url'], true);
				$video_info['video_size'] = $video_size['Content-Length'];
				$data = array('code'=>200, 'data'=>$video_info);
			} else {
				$data = array('code'=>-1, 'data'=>'暂无内容');
			}
			echo json_encode($data);
			
		} catch(PDOException $e) {
			setapilog('get_video_info:params[lesson_type:'.$lesson_type.', video_type:'.$video_type.'], error:'.$e->getMessage());
			$data = array('code'=>1, 'data'=>'网络错误');
			echo json_encode($data);
			exit();
		}
	}

	// api错误日志记录
	function setvideoinfo($path, $word='') {
		$fp = fopen($path,"a");
		flock($fp, LOCK_EX) ;
		fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time()+8*3600)."\n".$word."\n");
		flock($fp, LOCK_UN);
		fclose($fp);
	}