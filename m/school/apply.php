<?php
	ini_set('date.timezone','Asia/Shanghai');
	require_once '../include/config.php';
	error_reporting(E_ERROR);
	$date = time();
	$sid = htmlspecialchars($_GET['sid']);
	$id = htmlspecialchars($_GET['id']);
	$uid = htmlspecialchars($_GET['uid']);
	$m = htmlspecialchars($_GET['m']);
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
		<title></title>
		<link rel="stylesheet" href="../assets/css/mui.min.css" />
		<link rel="stylesheet" href="../assets/css/style.css" />
		<link rel="stylesheet" href="../assets/font/iconfont/iconfont.css" />
		<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
		<script src="../assets/js/layer/layer.js"></script>
		<style type="text/css">
			.mui-table-view-cell:after {left:0px;}
			.ucenter-info {position: absolute; top:0px; width:100%;height:160px; z-index:2; text-align:center; padding-top:30px;}
			.ucenter-p {color: #fff; padding:0px; margin:0px;}
			.blur {	
			    filter: url(blur.svg#blur); /* FireFox, Chrome, Opera */
			    
			    -webkit-filter: blur(10px); /* Chrome, Opera */
			       -moz-filter: blur(10px);
			        -ms-filter: blur(10px);    
			            filter: blur(10px);
			    
			    filter: progid:DXImageTransform.Microsoft.Blur(PixelRadius=10, MakeShadow=false); /* IE6~IE9 */
			}
			.mui-input-clear {content:""}
		</style>
		<script>
			var loginauth = localStorage.getItem('loginauth');
			if(!loginauth) {
				location.href = "index.php?id=<?php echo $sid; ?>";
			}
		</script>
		<script type="text/javascript">
            /*
			//通过config接口注入权限验证配置
			wx.config({
			    debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
			    appId: 'wx1baa85b75b6f2d60', // 必填，公众号的唯一标识
			    timestamp: '<?php echo time();?>', // 必填，生成签名的时间戳
			    nonceStr: '<?php echo $nonceStr;?>', // 必填，生成签名的随机串
			    signature: '<?php echo $signature;?>',// 必填，签名
			    jsApiList: [] // 必填，需要使用的JS接口列表
			});
			//通过ready接口处理成功验证
			wx.ready(function(){
				// config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后
			});
            */
		</script>
	</head>
	<body style="background: #f5f5f5;">
		<!--OK (appsercret:d9f6d541de1f95cfb1eb33f5c4bb27eb)-->
		<header class="mui-bar mui-bar-nav">
			<a class="mui-action-back mui-icon mui-icon-left iconfont" style="color: #00BD9C; font-size:1.3rem;">&#xe608;</a>
			<h1 class="mui-title">报名</h1>
		</header>
		<div class="mui-content">
			<div class="ucenterbg" style="height: 160px; width:100%; position:relative; text-align: center;">
				<img id="s_bg_thumb" id="stackblur" class="blur" src="../assets/images/ucenterbg1.png" width="100%" height="160px" alt="" />

				<div class="ucenter-info">
					<img id="s_thumb" style="border-radius: 50%; border:2px solid #fff; width:80px; height:80px; " src="../assets/images/school_thumb.jpg" alt="" /><br />
					<p class="ucenter-p" id="school_name" style="font-size:1rem;"></p>
				</div>
			</div>
			<form class="mui-input-group">
				<div class="mui-input-row">
					<label>姓　　名</label>
					<input type="text" class="mui-input-clear" id="user_name" placeholder="请输入姓名" value="">
				</div>
				<div class="mui-input-row">
					<label>电　　话</label>
					<input type="text" class="mui-input-clear iconfont" id="user_phone" placeholder="请输入电话" value="">
				</div>
				<div class="mui-input-row">
					<label>身份证号</label>
					<button type="button"  id="check_identify" class="mui-btn mui-btn-green" style="border:none; margin-top:2px; background: #34C083;">
						检测
						<!--<div class="mui-loading">
							<div class="mui-spinner" style="width: 15px; height: 15px;">
							</div>
						</div>-->
					</button>
					<input type="text" id="user_identify_id" style="width:50%" placeholder="请输入身份证号码" value="">
				</div>
				<div class="mui-input-row">
					<label>驾照类型</label>
            		<span class="mui-pull-right iconfont" style="color:#888; right:0px; top:10px; position:absolute; font-size:1.5rem;">&#xe60f;</span>
					<select name="lisence_type" id="lisence_type">
						<option value="C1">C1</option>
						<option value="C2">C2</option>
					</select>
				</div>
				<div class="mui-input-row">
					<label>订单类型</label>
					<span class="mui-input-clear" style="line-height: 40px;" id="order_type"></span>
				</div>
				<div class="mui-input-row">
					<label>报名费用</label>
					<span class="mui-input-clear" style="line-height: 40px;" id="order_money"></span>
				</div>
				<div class="clearfix"></div>
				<div class="mui-content-padded" id="signup" >
					<a href="#pay_method">
						<button id='submit-btn' style='padding:10px 0px;background: #34C083; border:none' class='mui-btn mui-btn-block mui-btn-green'>下一步</button>					
					</a>
				</div>
			</form>
		</div>
		<div id="pay_method" class="mui-popover mui-popover-action mui-popover-bottom">
			<ul class="mui-table-view">
				<li class="mui-table-view-cell">
					<a href="#" id="alipay">支付宝支付</a>
				</li>
				<li class="mui-table-view-cell">
					<a href="#" id="wxpay">微信支付</a>
				</li>
				<li class="mui-table-view-cell">
					<a href="#" id="offline">线下支付</a>
				</li>
			</ul>
			<ul class="mui-table-view">
				<li class="mui-table-view-cell">
					<a href="#pay_method"><b>取消</b></a>
				</li>
			</ul>
		</div>

		<script src="../assets/js/mui.min.js"></script>

		<script>
			(function($, doc) {
				var sid = "<?php echo $sid; ?>";  // 学校ID
				var id = "<?php echo $id; ?>";   // 班制ID
				var uid = "<?php echo $uid; ?>"; // 学员ID
				var money = "<?php echo $m; ?>"; // 总价
				var user_name = doc.getElementById('user_name');
				var user_phone = doc.getElementById('user_phone');
				var user_identify_id = doc.getElementById('user_identify_id');
				var order_type = doc.getElementById('order_type');
				var order_money = doc.getElementById('order_money');
				var s_thumb = doc.getElementById('s_thumb');
				var s_bg_thumb = doc.getElementById('s_bg_thumb');
				var school_name = doc.getElementById('school_name');
				var check_identify = doc.getElementById('check_identify');
				var lisence_type = doc.getElementById('lisence_type');
				
				$.ajax({
					type:"post",
					url:"<?php echo HOST; ?>/apply.php",
					async:false,
					data:{'id':id, 'uid':uid, 'sid':sid},
					dataType:"json",
         			timeout:10000,
         			beforeSend:function() {
         				check_identify.innerHTML = '<div class="mui-loading"><div class="mui-spinner" style="width: 15px; height: 15px;"></div></div>';
         			},
					success:function(data) {
						check_identify.innerHTML = '检测';
						if(data.code == 200) {
							user_name.value = data.data.s_username;
							user_phone.value = data.data.s_phone;
							user_identify_id.value = data.data.identity_id;
							order_type.innerHTML = data.data.shifts_info;
							order_money.innerHTML = money;
							if(data.data.s_thumb != '') {
								s_thumb.src = data.data.s_thumb;
								s_bg_thumb.src = data.data.s_thumb;
							}
							school_name.innerHTML = data.data.s_school_name;
						} else {
							$.toast(data.data);
						}
						//检测身份证号码
						check_identify.addEventListener('tap', function(e) {
							var identify_id = user_identify_id.value;
							if(identify_id.trim() == '') {
								$.toast('请填写身份证号码');
								return false;
							}
							$.ajax({
								type:"get",
								url:"<?php echo HOST; ?>/identify_validate.php/id/"+identify_id,
								dataType:"json",
								async:true,
								timeout:10000,
								success:function(data) {
									if(data.errNum == 0) {
										$.toast('验证通过');
									} else if(data.errNum == '300209') {
										$.toast('服务器无响应，请重新检测');
									} else {
										$.toast('验证失败');
									}
								},
								error:function() {
									$.toast('网络错误，请检查网络');
								}
								
							});
						});
						
					},
					error:function() {
         				check_identify.innerHTML = '检测';
						$.toast('网络错误，请检查网络');
					}
				});

				//驾照类型
				$('body').on('tap', '.mui-popover-action li>a', function() {
					var a = this,
						parent;
					//根据点击按钮，反推当前是哪个actionsheet
					for (parent = a.parentNode; parent != document.body; parent = parent.parentNode) {
						if (parent.classList.contains('mui-popover-action')) {
							break;
						}
					}
					//关闭actionsheet
					mui('#' + parent.id).popover('toggle');
					var pmethod = a.getAttribute('id');

					// 支付宝支付
					if(pmethod == 'alipay') {
		                      		//请求下单接口send_signup_orders.php下单
		                      		var request_params = {
					                    'id':id,
					                    'sid':sid,
					                    'pay_type':1,
					                    'user_name':user_name.value,
					                    'user_phone':user_phone.value,
					                    'user_identify_id':user_identify_id.value,
					                    'licence':lisence_type.value,
					                    'uid':uid,
				                  	};
				                  	$.ajax({
				                  		type:"post",
				                  		url:"<?php echo HOST; ?>/v2/order/send_signup_orders.php",
				                  		async:true,
										data:request_params,
										dataType:"json",
										success:function(data) {
											//验证没通过（下单没成功）
						                 	if(data.code != 200) {
					                        	layer.open({
												    content: data.data+'，要返回首页吗？',
												    btn: ['确认', '取消'],
												    shadeClose: false,
												    yes: function(){
														location.href="index.php?id="+sid;
												    }, no: function(){
												        return false;
												    }
												});
						                        return false;
					                      	}
							            	//验证成功(下单成功)	
											if(data.code == 200) {
												var order_no = data.data.order_info.order_id;
												var body = {
							            				'order_no' :  order_no, 
							            				'order_time' : "<?php echo date('Y-m-d H:i',time()); ?>", 
							            				'shifts_id' : id, 
							            				'order_type' : 'signup',
							            				'order_money' : order_money.innerHTML,
							            				'id' : sid
							            			};
												var params = {
							            			'WIDout_trade_no' :  order_no,
							            			'WIDsubject' : '嘻哈学车WAP报名驾校支付',
							            			'WIDtotal_fee' : order_money.innerHTML,
							            			'WIDshow_url' : "",
							            			'WIDit_b_pay':'',
							            			'WIDextern_token' :'',
							            			// 'WIDbody' : '1|'+id+'|'+sid+'|1|'+user_name.value+'|'+user_phone.value+'|'+user_identify_id.value+'|'+lisence_type.value+
							            			// '|'+order_type.innerHTML+'|'+uid+'|alipayxihaxueche'
							            			'WIDbody' : JSON.stringify(body) 
							            		};
												$.ajax({
													type:"post",
													url:"<?php echo HOST; ?>/v2/pay/wappay/alipay/alipayapi.php",
													async:true,
													data:params,
													dataType:"html",
													success:function(data) {
														var html = '<div class="mui-input-row"><label>订单号</label><span id="no">'+order_no+'</span></div><div class="mui-input-row"><label>价 格</label><span id="price" style="color:red; font-weight:bold;">￥'+order_money.innerHTML+'元</span></div>'; 
														layer.closeAll();
														var pageii = layer.open({
															title:[
																'订单详情',
															],
														    type: 1,
														    content: html+data,
														    style: 'position:fixed; left:0; top:0; padding:20px; width:100%; height:100%; border:none; overflow-y:scroll'
														});
													},
													error:function() {
														$.toast('网络错误,请检查网络')
													}
												});
											} else {
												$.toast('下单失败');
												return false;
											}
										},
										error:function() {
											$.toast('网络错误,请检查网络')
										}
				                  	});	
	            	
	            	// 微信支付
					} else if(pmethod == 'wxpay') {
						// $.toast('暂未开通,敬请期待');
						// return false;
						var params = {
		                    'id':id,
		                    'sid':sid,
		                    'ptype':3,
		                    'user_name':user_name.value,
		                    'user_phone':user_phone.value,
		                    'user_identify_id':user_identify_id.value,
		                    'licence':lisence_type.value,
		                    'order_type':order_type.innerHTML,
		                    'uid':uid,
		                    'access_token':'xihaxueche@2016'
	                  	};
	                  	// 验证是否已报名
	                  	$.ajax({
		                    type:"post",
		                    url:"<?php echo HOST; ?>/school_order_check.php",
		                    async:true,
		                    data:params,
		                    dataType:"json",
		                    beforeSend:function(){
								doc.getElementById('submit-btn').innerHTML = '正在报名';
								doc.getElementById('submit-btn').setAttribute('disabled', true);
							},
		                    success:function(data) {
		                    	doc.getElementById('submit-btn').innerHTML = '下一步';
								doc.getElementById('submit-btn').removeAttribute('disabled');
		                      	if(data.code != 200) {
		                      		layer.open({
									    content: data.data+'，要返回首页吗？',
									    btn: ['确认', '取消'],
									    shadeClose: false,
									    yes: function(){
											location.href="index.php?id="+sid;
									    }, no: function(){
									        return false;
									    }
									});
			                        // $.toast(data.data);
			                        return false;
		                      	} else {
									var attach = '1|'+id+'|'+sid+'|3|'+user_name.value+'|'+user_phone.value+'|'+user_identify_id.value+'|'+lisence_type.value+
				            			'|'+order_type.innerHTML+'|'+uid+'|'+order_money.innerHTML+'|xihaxueche@2016';
									location.href="<?php echo WXPAY_URL; ?>/wappay/wxpay/example/jsapi.php??attach="+encodeURIComponent(attach);
									return false;
									$.toast('暂未开通,敬请期待');
									return false;
		                      	}
		                    },
		                    error:function() {
		                    	doc.getElementById('submit-btn').innerHTML = '下一步';
								doc.getElementById('submit-btn').removeAttribute('disabled');
		                      	$.toast('网络错误，请检查网络');
		                      	return false;
		                    }
	                  	});
					
					// 线下支付
					} else if(pmethod == 'offline') {
						var params = {
							'id':id,
							'sid':sid,
							'ptype':2,
							'user_name':user_name.value,
							'user_phone':user_phone.value,
							'user_identify_id':user_identify_id.value,
							'licence':lisence_type.value,
							'order_type':order_type.innerHTML,
							'uid':uid,
		                    'access_token':'xihaxueche@2016'
						};
						$.ajax({
							type:"post",
							url:"<?php echo HOST; ?>/school_payment.php",
							async:true,
							data:params,
							dataType:"json",
							beforeSend:function(){
								doc.getElementById('submit-btn').innerHTML = '正在报名';
								doc.getElementById('submit-btn').setAttribute('disabled', true);
							},
							success:function(data) {
								doc.getElementById('submit-btn').innerHTML = '下一步';
								doc.getElementById('submit-btn').removeAttribute('disabled');
								if(data.code == 200) {
									layer.open({
									    content: '你已报名成功，要返回首页吗？',
									    btn: ['确认', '取消'],
									    shadeClose: false,
									    yes: function(){
											location.href="index.php?id="+sid;
									    }, no: function(){
									        return false;
									    }
									});

								} else {
									// alert(data.data)
									layer.open({
									    content: data.data+'，要返回首页吗？',
									    btn: ['确认', '取消'],
									    shadeClose: false,
									    yes: function(){
											location.href="index.php?id="+sid;
									    }, no: function(){
									        return false;
									    }
									});
									// $.toast(data.data);
								}
									
							},
							error:function() {
								doc.getElementById('submit-btn').innerHTML = '下一步';
								doc.getElementById('submit-btn').removeAttribute('disabled');
								$.toast('网络错误，请检查网络');
							}
						});
					}
				})
				
			})(mui, document);
			
			//调用微信JS api 支付
			function jsApiCall()
			{
				WeixinJSBridge.invoke(
					'getBrandWCPayRequest',
					"<?php echo $jsApiParameters; ?>",
					function(res){
						WeixinJSBridge.log(res.err_msg);
						alert(res.err_code+res.err_desc+res.err_msg);
					}
				);
			}
		
			function callpay()
			{
				if (typeof WeixinJSBridge == "undefined"){
				    if( document.addEventListener ){
				        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
				    }else if (document.attachEvent){
				        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
				        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
				    }
				}else{
				    jsApiCall();
				}
			}
		</script>
	</body>
</html>
