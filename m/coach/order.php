<?php
	require_once '../include/config.php';
	$year = date('Y', time());
    $id = htmlspecialchars($_GET['id']);
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
		<link rel="stylesheet" href="../assets/css/swiper.min.css" />
		<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
		<script src="../assets/js/cookie.min.js"></script>
		<!--<script src="../assets/js/location.js"></script>-->
		<script src="../assets/js/layer/layer.js"></script>
		<style type="text/css">
			.mui-table-view-cell:after {left:0px;}
		</style>
		<script>
			var loginauth = localStorage.getItem('loginauth');
			if(loginauth == '') {
				location.href = "index.php?id=<?php echo $id; ?>&from=order";
			}
		</script>
		<script type="text/javascript">
			//通过config接口注入权限验证配置
            /*
			wx.config({
			    debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
			    appId: 'wx1baa85b75b6f2d60', // 必填，公众号的唯一标识
			    timestamp: '<?php echo time();?>', // 必填，生成签名的时间戳
			    nonceStr: '<?php echo @$nonceStr;?>', // 必填，生成签名的随机串
			    signature: '<?php echo @$signature;?>',// 必填，签名
			    jsApiList: [] // 必填，需要使用的JS接口列表
			});
			//通过ready接口处理成功验证
			wx.ready(function(){
				// config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后
			});
            */
		</script>
        <style>
        .mui-input-row {
            height: 50px;
            line-height: 50px;
            background: #fff;
            border-bottom: 1px solid #eee;
        }
        .order-row {
            height: auto !important;
            line-height: 40px;
        }
        .mui-input-row label {
            line-height: 2.0em;
        }
        .period-wp {
            width: 65%;
            float: right;
        }
        .mui-input-row .price {
            color: #db4147;
        }
        .period {
            border: 1px solid #d58212;
            border-radius: 4px;
            padding: 5px 10px;
            color: #db4147;
            /*margin-right:5px;*/
        }
        .notice {
            background: #eb9316;
            color: #fff;
        }
        .notice label {
            color: #fff;
        }
        .pay-type {
            padding-top: 0px;
            margin-top: -2px;
            width: auto;
            float: left;
            font-size: 14px; 
            margin-left: 20px;
        }
        .choose {
            color: #999;
        }
        .choosed {
            color: #00bd9c;
        }
        </style>
	</head>
	<body style="background: #f5f5f5;">
		<!--OK (appsercret:d9f6d541de1f95cfb1eb33f5c4bb27eb)-->
		<header class="mui-bar mui-bar-nav">
			<a class="mui-action-back mui-icon mui-icon-left iconfont" style="color: #00BD9C; font-size:1.3rem;">&#xe608;</a>
			<h1 class="mui-title">订单详情</h1>
		</header>
		<div class="mui-content" id="order-detail">
        	<p style="margin:10px;">
        		 订单信息
    		</p>
			
			<div class="mui-input-row">
			  <label>教　　练</label>
              <span id="coach_name"></span>
			</div>
			<div class="mui-input-row">
			  <label>车　　型</label>
              <span id="car_type">普桑普通型</span>
			</div>
			
			<div class="mui-input-row order-row">
			  <label>时　　间</label>
              <div>
                <span id="date_time"></span>
                <br />
                <div class="period-wp" id="date_time_list">
                <!--<span class="period">8:00-9:00</span>-->
                </div>
              </div>
			</div>
			
			<div class="mui-input-row">
			  <label>费　　用</label>
              <span class="price">￥<span id="final_price">0</span>元</span>
			</div>

			<div class="mui-input-row notice">
			  <label>备　　注</label>
              <span id="beizhu"></span>
			</div>
			
			
        	<p style="margin-top:10px; margin-left:10px;">支付方式</p> 
			<ul class="mui-table-view">

                <!--默认为支付宝支付-->
	            <li id="alipay" class="mui-table-view-cell" style="height: 60px;color: #333333; ">
                  <div style="width: 30px; float: left; line-height: 40px;">
                    <span class="iconfont" style="color: #00a0e9; font-size: 2.0rem;">&#xe617;</span>
                  </div>

                  <div class="pay-type">
                    <span>
                      <span style="color: #666; font-weight: bold;">支付宝支付</span><br />
                      <span style="color: #999; font-size: 12px;">支付宝支付，支付更安全</span>
                    </span>
                  </div>

                  <div style="width: 30px; float: right; line-height: 45px;">
                    <span class="iconfont choose choosed" style="font-size: 1.5rem;">&#xe602;</span>
                  </div>
                </li>

	            <li id="wxpay" class="mui-table-view-cell" style="height: 60px;color: #333333; ">
                  <div style="width: 30px; float: left; line-height: 40px;">
                    <span class="iconfont" style="color: #10cd1b; font-size: 2.0rem;">&#xe616;</span>
                  </div>

                  <div class="pay-type">
                    <span>
                      <span style="color: #666; font-weight: bold;">微信支付</span><br />
                      <span style="color: #999; font-size: 12px;">推荐安装微信5.0以上版本使用</span>
                    </span>
                  </div>

                  <div style="width: 30px; float: right; line-height: 40px;">
                    <span class="iconfont choose" style="font-size: 1.5rem;">&#xe61a;</span>
                  </div>
                </li>

	            <li id="creditpay" class="mui-table-view-cell" style="height: 60px;color: #333333; ">
                  <div style="width: 30px; float: left; line-height: 40px;">
                    <span class="iconfont" style="color: #2b81cf; font-size: 2.0rem;">&#xe615;</span>
                  </div>

                  <div class="pay-type">
                    <span>
                      <span style="color: #666; font-weight: bold;">银行卡支付</span><br />
                      <span style="color: #999; font-size: 12px;">支持储蓄卡信用卡</span>
                    </span>
                  </div>

                  <div style="width: 30px; float: right; line-height: 40px;">
                    <span class="iconfont choose" style="font-size: 1.5rem;">&#xe61a;</span>
                  </div>
                </li>

                <!--拿下线下支付-->
                <!--
	            <li id="offlinepay" class="mui-table-view-cell" style="height: 60px;color: #333333; ">
                  <div style="width: 30px; float: left; line-height: 40px;">
                    <span class="iconfont" style="color: #2ac845; font-size: 2.2rem;">&#xe618;</span>
                  </div>

                  <div class="pay-type">
                    <span>
                      <span style="color: #666; font-weight: bold;">线下支付</span><br />
                      <span style="color: #999; font-size: 12px;">到驾校支付</span>
                    </span>
                  </div>

                  <div style="width: 30px; float: right; line-height: 40px;">
                    <span class="iconfont choose" style="font-size: 1.5rem; ">&#xe61a;</span>
                  </div>
                </li>
                -->

            </ul>

            <div style="padding: 10px 20px; width: 100%;">
              <button style="padding: 10px 0px; border-radius: 4px;" id="paynow" class="mui-btn mui-btn-block mui-btn-red">立即支付</button>
            </div>

            <!--
			<div class="mui-loading" style="background: #f5f5f5; padding-top: 10px;">
				<div class="mui-spinner">
				</div>
				<p style="text-align: center;">正在加载中</p>
			</div>
            -->
	   
		</div>
		<script src="../assets/js/mui.min.js"></script>
		<script src="../assets/js/swiper.min.js"></script>
		<script src="../assets/js/template.js"></script>
		<script src="../assets/js/cookie.min.js"></script>
		<script type="text/javascript" charset="utf-8">
			
			mui.init({
				swipeBack:true //启用右滑关闭功能
			});

         	
         	(function($, doc){
                var loginauth = localStorage.getItem('loginauth');
                if ( loginauth != '{}' || loginauth ) {
                    var loginauth_json = JSON.parse(loginauth);
                    var uid = loginauth_json.l_user_id;
                } else {
                    localStorage.setItem( 'loginauth', '{}' );
                }
      				var timelist = Cookies.get('timelist');
      				var timeconfigids = Cookies.get('timeconfigids');
      				var _date = Cookies.get('date');
      				var car_type = Cookies.get('car_type');
      				var coach_name = Cookies.get('coach_name');
      				doc.getElementById('date_time').innerHTML = "<?php echo $year; ?>-" + _date;
      				var html = '';
      				$.each(JSON.parse(timelist), function(e,t) {
      					html += '<span class="period">'+t+'</span><br />';
      				});
      				doc.getElementById('date_time_list').innerHTML = html;
      				doc.getElementById('car_type').innerHTML = car_type;
      				doc.getElementById('coach_name').innerHTML = coach_name;
      				var coach_id = "<?php echo $id; ?>";
             		var config = {
                        'user_id': uid,
                        'coach_id': coach_id,
                        'time_config_id': timeconfigids,
                        'date': _date,
                        'coupon_id': 1,
                        'param_1':1,
              					'param_2':1,
              					'param_3':1,
              					'param_4':1
             		};

                $('.mui-table-view').on('tap', '.mui-table-view-cell', function(e) {
                    var id = this.getAttribute('id');
                    //var choose = doc.getElementById(id).getElementsByClassName('choose');
                    var child = doc.querySelector("#" + id + " .choose");
                    child.className += " choosed";
                    child.innerHTML = "&#xe602;";
                    var siblings = sibling(child.parentNode.parentNode);
                    //取消选中其它的支付方式
                    cancelSiblings(siblings, 'choose', 'iconfont choose', '&#xe61a;');
                })		

         		$.ajax({
         			type:"post",
         			url:"<?php echo HOST; ?>/order_check.php",
         			data:config,
         			dataType:"json",
         			timeout:10000,
         			async:false,
         			success:function(data) {
         				if(data.code == 200) {
         					doc.getElementById('final_price').innerHTML =data.data.final_price;
         					doc.getElementById('beizhu').innerHTML = data.data.msg;
         				} else {
							$.toast(data.data);
						}
         			},
         			error:function() {
         				$.toast('网络错误,请检查网络');
         			}
         		});
         		
         		   	var paynowbtn = document.getElementById('paynow');
            	 	paynowbtn.addEventListener('tap', function() {
                    /* 侦听用户有没有登陆， 如果无，则跳转到登陆页 */
                    if (loginauth != '{}') {
                        ; //已经登陆，无需操作
                    } else {
                        location.href = "login.php?id=<?php echo $id;?>";
                    }
	            	var timelist = Cookies.get('timelist');
      					var timeconfigids = Cookies.get('timeconfigids');
      					var _date = Cookies.get('date');
      					var coach_id = "<?php echo $id; ?>";
      					var _money = document.getElementById('final_price').innerHTML;
	            	var checked = document.querySelector('.choosed').parentNode.parentNode;
	            	var paytypeId = checked.getAttribute('id');
                var paynowbtn = document.getElementById('paynow');

                //线下支付
                if (paytypeId == 'offlinepay') {
	         		    var orderparam = {
	                        'user_id': uid,
	                        'coach_id': coach_id,
	                        'time_config_id': timeconfigids,
	                        'date': _date, 
	                        'type': 2, //线下支付  
	                        'money': _money             
	         		    };
      						$.ajax({
      							type:"post",
      							url:"<?php echo HOST; ?>/send_orders.php",
      							async:true,
      							data:orderparam,
      							dataType:"json",
                    beforeSend: function () {
                      paynowbtn.innerHTML = '正在下单中';
                      paynowbtn.setAttribute('disabled', true);
                    },
      							success:function(data) {
                      paynowbtn.innerHTML = '立即支付';
                      paynowbtn.removeAttribute('disabled');
      								if(data.code == 200) {
      									alert('下单成功');
      									location.href = "detail.php?id=<?php echo $id; ?>";	
      								}
      								$.toast(data.data);
      							},
      							error:function() {
                      paynowbtn.innerHTML = '立即支付';
                      paynowbtn.removeAttribute('disabled');
      								$.toast('网络错误,请检查网络');
      							}
      						});

                // 支付宝支付
	            	}else if(paytypeId == 'alipay') {
                   var config = {
                    'coach_id':coach_id,
                    'time_config_id':timeconfigids,
                    'user_id':uid,
                    'date':_date,
                    'money':_money,
                    'pay_type':1 //alipay
                  };
                 //请求下单接口send_signup_orders.php下单
                  $.ajax({
                    type:"post",
                    url:"<?php echo HOST; ?>/v2/order/send_appoint_orders.php",
                    async:true,
                    data:config,
                    dataType:"json",
                    beforeSend: function () {
                      paynowbtn.innerHTML = '正在下单中';
                      paynowbtn.setAttribute('disabled', true);
                    },
                    success:function(data) {
                      paynowbtn.innerHTML = '立即支付';
                      paynowbtn.removeAttribute('disabled');
                      // 可支付
                      if(data.code == 200) {
                        var order_no = data.data.order_info.order_id;
                        var body = {
                          'order_no' : order_no, 
                          'order_time' : "<?php echo date('Y-m-d H:i',time()); ?>",
                          'order_type' : 'appoint',
                          'order_money' : _money,
                          'id' : coach_id
                        };
                        var params = {
                          'WIDout_trade_no' : order_no,
                          'WIDsubject' : '预约学车支付宝支付费用',
                          'WIDtotal_fee' : _money,
                          'WIDshow_url' : "s.com/php/m/coach/myorder.html",
                          'WIDit_b_pay':'',
                          'WIDextern_token' :'',
                          // 'WIDbody' : '2|'+coach_id+'|'+timeconfigids+'|'+uid+'|'+_date+'|'+_money
                          'WIDbody' : JSON.stringify(body)
                        };
                        $.ajax({
                          type:"post",
                          url:"<?php echo HOST; ?>/v2/pay/wappay/alipay/alipayapi.php",
                          async:true,
                          data:params,
                          beforeSend: function () {
                            paynowbtn.innerHTML = '正在下单中';
                            paynowbtn.setAttribute('disabled', true);
                          },
                          dataType:"html",
                          success:function(data) {
                            paynowbtn.innerHTML = '立即支付';
                            paynowbtn.removeAttribute('disabled');
                            var html = '<div class="mui-input-row"><label>订单号</label><span id="no">'+order_no+'</span></div><div class="mui-input-row"><label>价 格</label><span id="price" style="color:red; font-weight:bold;">￥'+_money+'元</span></div>'; 
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
                            paynowbtn.innerHTML = '立即支付';
                            paynowbtn.removeAttribute('disabled');
                            $.toast('网络错误,请检查网络')
                          }
                        });

                      } else {
                        $.toast(data.data);
                      }
                    },
                    error:function() {
                      paynowbtn.innerHTML = '立即支付';
                      paynowbtn.removeAttribute('disabled');
                      $.toast('网络错误，请检查网络');
                    }
                  });
	            		

                // 微信支付
	            	} else if(paytypeId == 'wxpay') {
                  var config = {
                    'coach_id':coach_id,
                    'time_config_id':timeconfigids,
                    'user_id':uid,
                    'date':_date,
                    'money':_money,
                    'pay_type':3 //WxPay
                  };
                  $.ajax({
                    type:"post",
                    url:"<?php echo HOST; ?>/v2/order/send_appoint_orders.php",
                    async:true,
                    data:config,
                    dataType:"json",
                    // beforeSend: function () {
                    //   paynowbtn.innerHTML = '正在下单中';
                    //   paynowbtn.setAttribute('disabled', true);
                    // },
                    success:function(data) {
                      // paynowbtn.innerHTML = '立即支付';
                      // paynowbtn.removeAttribute('disabled');
                      // console.log(data);
                      if(data.code == 200) {
                        wxpay = data.data.pay.wxpay;
                        if ( wxpay != '' ) {
                          var attach = '2|'+coach_id+'|'+timeconfigids+'|'+uid+'|'+_date+'|'+_money;
                          location.href="<?php echo HOST; ?>/v2/pay/wappay/wxpay/example/jsapi.php??attach="+encodeURIComponent(attach);
                            // $.ajax({
                            //     type: "post",
                            //     url: "<?php echo HOST; ?>/v2/pay/wappay/wxpay/example/jsapi.php",
                            //     async: true,
                            //     data: wxpay,
                            //     dataType: "html",
                            //     success: function(data) {
                            //         alert('ok');
                            //     },
                            //     error: function(XMLHttpRequest, textStatus, errorThrown) {
                            //         //console.log(XMLHttpRequest);
                            //         alert(XMLHttpRequest.responseText);
                            //         //$.toast(data.data);
                            //     }
                            // });
                        }
                      } else {
                        $.toast(data.data);
                      }
                        
                    },
                    error:function() {
                      paynowbtn.innerHTML = '立即支付';
                      paynowbtn.removeAttribute('disabled');
                      $.toast('网络错误，请检查网络');
                    }
                  });
	            		// $.toast('暂无开通,敬请期待');
	            		// return false;

	            	} else if(paytypeId == 'creditpay') {
	            		$.toast('暂无开通,敬请期待');
	            		return false;	
	            	}
	            });
         	})(mui, document);
            //找到所有不包括自己的兄弟节点
            function sibling( elem ) {
                var r = [];
                var n = elem.parentNode.firstChild;
                for ( ; n; n = n.nextSibling ) {
                    if ( n.nodeType === 1 && n !== elem ) {
                        r.push( n );
                    }
                }

                return r;
            }

            //取消选中其它兄弟节点
            function cancelSiblings(obj, targetClass, newClass, text) {
                for (var i = 0; i < obj.length; i++) {
                    var id = obj[i].getAttribute('id');
                    var child = document.querySelector("#" + id + " ." + targetClass);
                    child.className = newClass;
                    child.innerHTML = text;
                }
            }
            
            
		</script>
    <script type="text/javascript">
	</body>
</html>
