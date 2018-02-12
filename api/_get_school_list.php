<?php  
	/**
	 * 抓取驾校列表
	 * @param $page 页码 
	 * @param $filtercityid 城市ID 
	 * @return string AES对称加密（加密字段xhxueche）
	 * @author chenxi
	 * 获取学校列表：http://api.jxedt.com/list/?os=android&time=1452210078833&lon=117.139461&productid=1&pageindex=1&filterparams={"":"","filtercityid":"234"}&type=jx&channel=7&lat=31.833969&version=3.9.6&cityid=176
	 * 获取学校详情：http://api.jxedt.com/detail/23441/?type=jx
	 **/
	/*
		获取学校列表
		{
		    "code": 0,
		    "msg": "OK",
		    "result": {
		        "jx": {
		            "infolist": [
		                {
		                    "action": {
		                        "actiontype": "loadpage",
		                        "pagetype": "detailmain",
		                        "title": "详情",
		                        "url": "http://api.jxedt.com/detail/3114774475642568729/?type=jx"
		                    },
		                    "amount": "4970元",
		                    "attentionnum": "362人关注",
		                    "distance": "距你9公里",
		                    "ifauthen": true,
		                    "ifpay": false,
		                    "infoid": 3114774475642568700,
		                    "name": "合肥硕大驾校",
		                    "picurl": "http://pic8.58cdn.com.cn/www/n_v1bkujjdzf4civmiuqjzia_80_60.jpg",
		                    "score": 5
		                },
		                {
		                    "action": {
		                        "actiontype": "loadpage",
		                        "pagetype": "detailmain",
		                        "title": "详情",
		                        "url": "http://api.jxedt.com/detail/23441/?type=jx"
		                    },
		                    "amount": "2580元",
		                    "attentionnum": "1.7万人关注",
		                    "ifauthen": true,
		                    "ifpay": false,
		                    "infoid": 23441,   // 可取
		                    "name": "合肥顺达驾校", //可取
		                    "picurl": "http://pic6.58cdn.com.cn/www/n_v1bl2lwtnjww2vkz5p7y4q_80_60.jpg", // 可取
		                    "score": 4.8  // 可取
		                },
		            ],
		            "lastpage": false,
		            "pageindex": 1,
		            "pagesize": 10
		        }
		    }
		}

		获取学校详情（infoid:23441 以合肥顺达驾校为例）
		{
		    "code": 0,
		    "msg": "OK",
		    "result": {
		        "info": {
		            "baseinfoarea": {
		                "mapaddr": {
		                    "action": {
		                        "actiontype": "loadpage",
		                        "extparam":  {
		                            "lat": 43.871988334359,
		                            "lon": 126.56454398883
		                        },
		                        "pagetype": "detaillocation",
		                        "title": "驾校地图"
		                    },
		                    "text": "合肥华地学府边（蜀山政务高新经开区） ,瑶海新站庐阳丹凤朝阳医院边, 包河望宁交口" // 可取
		                },
		                "rountline": {
		                    "action": {
		                        "actiontype": "loadpage",
		                        "pagetype": "link",
		                        "title": "班车路线",
		                        "url": "http://api.jxedt.com/detail/23441/bus/?type=jx"
		                    },
		                    "text": "班车路线"
		                },
		                "tel": [
		                    "18019966769",  // 可取
		                    "18019966769"
		                ]
		            },
		            "bbsgrouparea": {
		                "title": "顺达驾校",
		                "articletip": 14,
		                "authortip": 293,
		                "groupaction": {
		                    "actiontype": "loadpage",
		                    "extparam": {
		                        "infoid": 3724
		                    },
		                    "pagetype": "bbsgrouplist",
		                    "title": "顺达驾校"
		                },
		                "lstusers": []
		            },
		            "commentarea": {
		                "title": "学员点评",
		                "commentinfo": {
		                    "comment": "科目一四我最怕看书了朱老师的细心教导下哈哈在家没看书居然只花了1天半时间就顺利通过了很感谢",
		                    "commentid": 3087899441499078700,
		                    "face": "",
		                    "likecount": 16,
		                    "name": "王耀",
		                    "score": 20,
		                    "time": "2015-07-26"
		                },
		                "moretext": "查看全部13条评论",
		                "action": {
		                    "actiontype": "loadpage",
		                    "pagetype": "detailmoredianping",
		                    "title": "学员点评",
		                    "url": "http://api.jxedt.com/detail/23441/comment/list/?type=jx"
		                }
		            },
		            "descarea": {
		                "title": "驾校简介",
		                "text": "\n\n\n\n\n 顺达驾校官方报名地址：蜀山经开高新政务区：合肥市望江西路华地学府边瑶海新站庐阳区丹凤朝阳医院边门面房包河区滨湖地址望江路和宁国路交叉口合肥顺达官方报名电话1801996676945-60天特",
		                "morebutton": {
		                    "action": {
		                        "actiontype": "loadpage",
		                        "pagetype": "link",
		                        "title": "驾校简介",
		                        "url": "http://api.jxedt.com/detail/23441/summary/?type=jx"
		                    },
		                    "text": "更多"
		                },
		                "jxnewsbutton": {
		                    "action": {
		                        "actiontype": "loadpage",
		                        "pagetype": "link",
		                        "title": "驾校新闻",
		                        "url": "http://api.jxedt.com/detail/23441/news/list/?type=jx"
		                    },
		                    "text": "驾校新闻"
		                },
		                "characterbutton": {
		                    "action": {
		                        "actiontype": "loadpage",
		                        "pagetype": "link",
		                        "title": "驾校特色",
		                        "url": "http://api.jxedt.com/detail/23441/feature/?type=jx"
		                    },
		                    "text": "驾校特色"
		                }
		            },
		            "questionarea": {
		                "title": "学车问答",
		                "questioninfo": {
		                    "answer": "等待驾校回复中…",
		                    "answername": "驾校回复",
		                    "answertime": "",
		                    "askname": "夏倩",
		                    "asktime": "2016-01-10",
		                    "qusetion": "你好老师。C1普通班多少钱？大概多久能过呢？？"
		                },
		                "moretext": "查看全部113条问题",
		                "action": {
		                    "actiontype": "loadpage",
		                    "pagetype": "detailmorewenda",
		                    "title": "学员问答",
		                    "url": "http://api.jxedt.com/detail/23441/question/list/?type=jx"
		                }
		            },
		            "signuparea": {
		                "list": [
		                    {
		                        "name": "孙碧薇",
		                        "tel": "13705692223",
		                        "time": "2016-01-09",
		                        "type": "报名"
		                    },
		                    {
		                        "name": "雷显明",
		                        "tel": "15905685222",
		                        "time": "2016-01-05",
		                        "type": "报名"
		                    },
		                    {
		                        "name": "方先生",
		                        "tel": "18214747351",
		                        "time": "2016-01-04",
		                        "type": "报名"
		                    },
		                    {
		                        "name": "陈雨生",
		                        "tel": "15056021051",
		                        "time": "2016-01-02",
		                        "type": "报名"
		                    },
		                    {
		                        "name": "吴荣誉",
		                        "tel": "15155196577",
		                        "time": "2015-12-25",
		                        "type": "咨询"
		                    }
		                ],
		                "title": "合肥顺达驾校报名"
		            },
		            "titlearea": {
		                "name": "合肥顺达驾校",
		                "ifauthen": true,
		                "star": 4.8,
		                "attentionnum": "1.7万人关注",
		                "amount": "2580",
		                "priceunit": "元",
		                "imageurl": "http://pic1.58cdn.com.cn/www/n_v1bl2lwtnjww2vkz5p7y4q_6c7ea76f39c78102.jpg",
		                "imagecount": 4,
		                "action": {
		                    "actiontype": "loadpage",
		                    "pagetype": "detaillargeimage",
		                    "title": "驾校风采",
		                    "url": "http://api.jxedt.com/detail/23441/photo/list/?type=jx"
		                }
		            }
		        },
		        "other": {}
		    }
		}

	*/
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
		$db = getConnection();
		$request = $app->request();
		$page = $request->params('page');
		$page = isset($page) ? $page : 1;
		// $filtercityid =$request->params('filtercityid');
		$province_id =$request->params('province_id');
		$pagecnt =$request->params('pagecnt');
		$pagecnt = isset($pagecnt) ? $pagecnt : 1; // 总页码

		// 获取城市列表
		$sql = "SELECT `city_id`, `cid` FROM `cs_temp_city` WHERE `province_id` = '{$province_id}'";
		$stmt = $db->query($sql);
		$city_id_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$city_ids = array();
		if($city_id_list) {
			foreach ($city_id_list as $key => $value) {
				$city_ids[] = $value['cid'];
			}
		} else {
			$data = array('code'=>200, 'data'=>'暂无列表');
			echo json_encode($data);
			exit();
		}

		for ($i=1; $i <= $pagecnt; $i++) { 
			foreach ($city_ids as $k => $v) {
				// $data = array();
				$data = array(
					'os' => 'andriod',
					'time' => time(),
					'lon' => '117.139461', // 经度
					'productid' => 1, // 暂无用
					'type' => 'jx', // 驾校
					'channel' => 7, // 暂无用
					'lat' => '31.833969', // 纬度
					'version' => '3.9.6', // 版本
					'city_id' => '176' // 城市ID
				);
				$data['pageindex'] = $i;
				$data['filterparams'] = json_encode(array('filtercityid' => $v));

				$url = 'http://api.jxedt.com/list/';
				$res = request_post($url, $data);
				if(USE_JSON_BIGINT_AS_STRING) {
					$result = json_decode($res, true, 512, JSON_BIGINT_AS_STRING);
				} else {
					$result = json_decode($res, true);
				}

				$list = array();

				if($result['result']['jx']['pagesize'] == 0) {
					$data = array('code'=>200, 'data'=>'抓取完成');
					echo json_encode($data);
					exit();
				}
				foreach ($result['result']['jx']['infolist'] as $key => $value) {
				  $list[$key]['name'] = isset($value['name']) ? trim($value['name']) : '';
				  $list[$key]['star'] = isset($value['score']) ? trim($value['score']) : '';
				  if(USE_JSON_BIGINT_AS_STRING) {
				  	$list[$key]['infoid'] = isset($value['infoid']) ? $value['infoid'] : '';
				  } else {
				  	$list[$key]['infoid'] = isset($value['infoid']) ? number_format($value['infoid'], 0, '', '') : '';
				  }	
				  	
				  $list[$key]['picurl'] = isset($value['picurl']) ? trim($value['picurl']) : '';
				  $list[$key]['attentionnum'] = $value['attentionnum'];

				  // 获取驾校详情
				  $url = "http://api.jxedt.com/detail/".$value['infoid']."/?type=jx";
				  $detail_res = file_get_contents($url);
				  if(USE_JSON_BIGINT_AS_STRING) {
				  	$school_info = json_decode($detail_res, true, 512, JSON_BIGINT_AS_STRING);
				  } else {
				  	$school_info = json_decode($detail_res, true);
				  }
				  $list[$key]['address'] = isset($school_info['result']['info']['baseinfoarea']['mapaddr']['text']) ? trim($school_info['result']['info']['baseinfoarea']['mapaddr']['text']) : '';
				  $list[$key]['tel'] = isset($school_info['result']['info']['baseinfoarea']['tel']) ? $school_info['result']['info']['baseinfoarea']['tel'] : '';
				  $list[$key]['amount'] = isset($school_info['result']['info']['titlearea']['amount']) ? trim($school_info['result']['info']['titlearea']['amount']) : '';
				  $list[$key]['descarea'] = isset($school_info['result']['info']['descarea']['text']) ? trim($school_info['result']['info']['descarea']['text']) : '';
				  $list[$key]['lng'] = !empty($school_info['result']['info']['baseinfoarea']['mapaddr']['action']['extparam']) ? trim($school_info['result']['info']['baseinfoarea']['mapaddr']['action']['extparam']['lon']) : '';
				  $list[$key]['lat'] = !empty($school_info['result']['info']['baseinfoarea']['mapaddr']['action']['extparam']) ? trim($school_info['result']['info']['baseinfoarea']['mapaddr']['action']['extparam']['lat']) : '';
				  $list[$key]['s_imgurl'] = !empty($school_info['result']['info']['titlearea']['action']['url']) ? trim($school_info['result']['info']['titlearea']['action']['url']) : '';

				  // 获取驾校简介
				  // $url = isset($school_info['result']['info']['descarea']['morebutton']['action']['url']) ? $school_info['result']['info']['descarea']['morebutton']['action']['url'] : '';
				  // if($url) {
				  // 	$school_desc = file_get_contents($url);
				  // } else {
				  // 	$school_desc = '';
				  // }
				  // $file_name = 'school_desc-'.$value['infoid'].'.html';
				  // $path = 'schooldesc/'.$file_name;
				  // setschoolinfo($path, $school_desc);
				}

				try {
					foreach($list as $key => $value) {
						$phone = '';
						if($value['tel']) {
							$phone = implode(',', $value['tel']);
						}
							
						$sql = "SELECT * FROM `cs_temp_school` WHERE `infoid` = '{$value['infoid']}'";
						$stmt = $db->query($sql);
						$school_info = $stmt->fetch(PDO::FETCH_ASSOC);
						// 获取城市省份ID
						$sql = "SELECT p.`province_id`, c.`city_id` FROM `cs_temp_province` as p LEFT JOIN `cs_temp_city` as c ON c.`province_id` = p.`pid` WHERE c.`cid` = '{$v}'";
						$stmt = $db->query($sql);
						$province_city_id = $stmt->fetch(PDO::FETCH_ASSOC);

						if($school_info) {
							$sql = "UPDATE `cs_temp_school` SET ";
							$sql .= " `name` = '{$value['name']}', ";
							$sql .= " `amount` = '{$value['amount']}', ";
							$sql .= " `imageurl` = '{$value['picurl']}',";
							$sql .= " `s_imgurl` = '{$value['s_imgurl']}',";
							$sql .= " `infoid` = '{$value['infoid']}',";
							$sql .= " `star` = '{$value['star']}',";
							$sql .= " `address` = '{$value['address']}',";
							$sql .= " `descarea` = '{$value['descarea']}',";
							$sql .= " `attentionnum` = '{$value['attentionnum']}',";
							$sql .= " `province_id` = '{$province_city_id['province_id']}',";
							$sql .= " `city_id` = '{$province_city_id['city_id']}',";
							$sql .= " `area_id` = '0',";
							$sql .= " `tel` = '{$phone}',";
							$sql .= " `moredesc` = '{$value['descarea']}',";
							$sql .= " `lng` = '{$value['lng']}',";
							$sql .= " `lat` = '{$value['lat']}'";
							$sql .= " WHERE `infoid` = '{$value['infoid']}'";
							$res = $db->query($sql);
							$str = '第'.$pagecnt.'页：<br>';
							if($res) {
								$str .= "当前驾校ID：".$value['infoid']."，更新抓取完成！(".date('Y-m-d H:i', time()).")";
								echo $str.'<br>';
								setlog($str);
							} else {
								$str .= "当前驾校ID：".$value['infoid']."，更新抓取出错！(".date('Y-m-d H:i', time()).")";
								echo $str.'<br>';
								setlog($str);
							}
						} else {
							$sql = "INSERT INTO `cs_temp_school` (`id`, `name`, `amount`, `imageurl`, `s_imgurl`, `infoid`, `star`, `address`, `descarea`, `attentionnum`, `province_id`, `city_id`, `area_id`, `tel`, `moredesc`, `lng`, `lat`) VALUE (NULL, '{$value['name']}', '{$value['amount']}', '{$value['picurl']}', '{$value['s_imgurl']}', '{$value['infoid']}', '{$value['star']}', '{$value['address']}', '{$value['descarea']}', '{$value['attentionnum']}', '{$province_city_id['province_id']}', '{$province_city_id['city_id']}', '0', '{$phone}', '{$value['descarea']}', '{$value['lng']}', '{$value['lat']}')";
							$res = $db->query($sql);
							$str = '第'.$pagecnt.'页：<br>';
							if($res) {
								$str .= "当前驾校ID：".$value['infoid']."，新增抓取完成！(".date('Y-m-d H:i', time()).")";
								echo $str.'<br>';
								setlog($str);
							} else {
								$str .= "当前驾校ID：".$value['infoid']."，新增抓取出错！(".date('Y-m-d H:i', time()).")";
								echo $str.'<br>';
								setlog($str);
							}
						}
					}
				} catch(PDOException $e) {
					setapilog('_get_school_list:params[pagecnt:'.$pagecnt.', province_id:'.$province_id.'], error:'.$e->getMessage());	
					$data = array('code'=>1, 'data'=>'网络错误');
					echo json_encode($data);
					exit;
				}
			}
		}
		$db = null;
		$data = array('code'=>200, 'data'=>'抓取完成');
		echo json_encode($data);
		exit();

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

	// api错误日志记录
	function setschoolinfo($path, $word='') {
		$fp = fopen($path,"a");
		flock($fp, LOCK_EX) ;
		fwrite($fp, $word);
		flock($fp, LOCK_UN);
		fclose($fp);
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

?>