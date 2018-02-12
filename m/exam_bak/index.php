<?php
	require_once '../include/config.php';
	$sid = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : 1;
	setcookie('sid', $sid);
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
		<title>嘻哈学车</title>
		<link rel="stylesheet" href="../assets/css/mui.min.css" />
		<link rel="stylesheet" href="../assets/css/style.css" />
		<link rel="stylesheet" href="../assets/font/iconfont/iconfont.css" />
		<link rel="stylesheet" href="../assets/css/swiper.min.css" />
		<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
		<script src="../assets/js/cookie.min.js"></script>
		<script src="../assets/js/layer/layer.js"></script>
		<style type="text/css">
			.mui-table-view-cell:after {left:0px;}
			.mui-table-view:after {height: 0px;}
			.mui-grid-view.mui-grid-9 .mui-table-view-cell {border-bottom: none;}
			.choosed {color:#00BD9C !important}
			.active {color:#00BD9C !important}
			.choose {color: #999 ;}
			#topPopover {
				position: fixed;
				top: 16px;
				right: 6px;
			}
			#topPopover .mui-popover-arrow {
				left: auto;
				right: 6px;
			}
			.mui-table-view-cell>a:not(.mui-btn) {margin:-10px -7px;}
			.mui-popover .mui-popover-arrow:after {border-radius:1px;}
		</style>
		<script type="text/javascript">
			//通过config接口注入权限验证配置
            /*
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
			})i;
            */
		</script>

		<script>
			var _hmt = _hmt || [];
			(function() {
			  var hm = document.createElement("script");
			  hm.src = "//hm.baidu.com/hm.js?ec52a986344c1bab363e1ae39c0dd626";
			  var s = document.getElementsByTagName("script")[0]; 
			  s.parentNode.insertBefore(hm, s);
			})();
		</script>
	</head>
	
	<body style="background: #f5f5f5;">
		<header class="mui-bar mui-bar-nav">
			<!--<a class="mui-action-back mui-icon mui-icon-left iconfont" style="color: #00BD9C; font-size:1.3rem;">&#xe608;</a>-->
	      	<a id="menu" href="#topPopover" class="mui-action-menu mui-icon mui-pull-right iconfont" style="color:#10AEFF; font-size: 1.5rem;" >&#xe620;</a>
			<h1 class="mui-title" id="school_name">驾考题库</h1>
		</header>
		
		<!--右上角弹出菜单-->
		<div id="topPopover" class="mui-popover" style="width: 150px !important;">
			<div class="mui-popover-arrow"></div>
			<div class="mui-scroll">
				<ul class="mui-table-view">
					<li class="mui-table-view-cell">
						<a href="javascript:;" onclick="javascript:clearcache();">
							<span class="iconfont" style="color: #00BD9C; font-size:1.3rem; margin-right:10px;">&#xe630;</span> 
							<span style="color: #333;">清除缓存</span>
						</a>
					</li>
					<li class="mui-table-view-cell">
						<a href="javascript:;" onclick="javascript:logout();">
							<span class="iconfont" style="color: #eb4f38; font-size:1.3rem; margin-right:10px;">&#xe62f;</span>
							<span style="color: #333;">退出登录</span>
						</a>
					</li>
				</ul>
			</div>
		</div>
		<div class="mui-content" style="">
			<p style="margin-top:10px; padding:10px; margin-bottom:0px; background: #fff;">
	      		驾照类型
	      	</p>
	        <ul class="mui-table-view mui-grid-view mui-grid-9" id="car_type" style="background: #fff;">
	            <li class="mui-table-view-cell mui-media mui-col-xs-3 mui-col-sm-3" id="C1">
	              <a href="javascript:;">
	                <span class="iconfont choose choosed" style="font-size:3rem;">&#xe61b;</span>
	                <div class="mui-media-body active">小车</div>
	              </a>
	            </li>
	            <li class="mui-table-view-cell mui-media mui-col-xs-3 mui-col-sm-3" id="A2">
	              <a href="javascript:;" style="padding-top:13px;">
	                <span class="iconfont choose" style="font-size:3rem; color:#BBBBBB">&#xe61f;</span>
	                <div class="mui-media-body" style="margin-top: 5px;">货车</div>
	              </a>
	            </li>
	            <li class="mui-table-view-cell mui-media mui-col-xs-3 mui-col-sm-3" id="A1">
	              <a href="javascript:;">
	                <span class="iconfont choose" style="font-size:3rem; color:#BBBBBB">&#xe61c;</span>
	                <div class="mui-media-body">客车</div>
	              </a>
	            </li>
	            <li class="mui-table-view-cell mui-media mui-col-xs-3 mui-col-sm-3" id="D">
	              <a href="javascript:;">
	                <span class="iconfont choose" style="font-size:2.7rem; color:#BBBBBB">&#xe61e;</span>
	                <div class="mui-media-body" style="margin-top: 9px;">摩托车</div>
	              </a>
	            </li>
            </ul>
            
            <p style="margin-top:10px; padding:10px; margin-bottom:0px; background: #fff;">
	      		科目选择
	      	</p>
	        <ul class="mui-table-view mui-grid-view mui-grid-9" id="lesson_type" style="background: #fff;">
	            <li class="mui-table-view-cell mui-media mui-col-xs-3 mui-col-sm-3" id="lesson1" data-id="1">
	              	<span class="lesson-span choosed">科目一</span>
	            </li>
	            <li class="mui-table-view-cell mui-media mui-col-xs-3 mui-col-sm-3" id="lesson4" data-id="4">
	              	<span class="lesson-span">科目四</span>
	            </li>
            </ul>
            
            <div class="" style="margin:30px 10px;">
				<button type="button" id='exam-btn' style='padding:10px 0px;border:none; background: #00BD9C;' class='mui-btn mui-btn-block mui-btn-red'>开始练习</button>
			</div>

            <div id="tikubanner" style="margin:0px;position:absolute; bottom:0px; right:0px;width:100%; height:63px;">
              <a href="javascript:void(0);" alt="新题库" style="display:block;">
                <img src="../assets/images/tiku2016banner.png" style="width:100%;" alt="嘻哈学车2016最新驾考题库，让您驾考更便捷，打开更快，更省流量！" />
              </a>
              <div id="closebanner" style="position:absolute; right:0;top:0;background:#00bd9c; color:#fff; font-size:22px; line-height:25px; border-bottom-left-radius:100%;width:2rem;height:2rem;text-align:right; padding-right:5px;">×</div>
            </div>

            <!-- 引导页 -->
            <div id="update" style="position: absolute; width:100%; top:0px; height:100%; background:rgba(0,0,0,0.5); z-index:10; display:none;">
                <div class="" style="position: relative; width:300px; background:#fff; border-radius:13px; margin:0px auto; top:70px; ">
                    <div class="container" style=" margin:0px auto; z-index:99">
                        <div class="swiper-container" style="border-radius:13px;">
                            <div class="swiper-wrapper" style=" width:100%; border-radius:4px;">
                                <div class="swiper-slide" style="background: #f5f5f5; width:300px; "><img src="../assets/images/update/update_115256.png" width="300px" alt="" /></div>
                                <div class="swiper-slide" style="background: #f5f5f5; width:300px; "><img src="../assets/images/update/update_115331.png" width="300px" alt="" /></div>
                                <div class="swiper-slide" style="background: #f5f5f5; width:300px; "><img src="../assets/images/update/update_115351.png" width="300px" alt="" /></div>
                            </div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                    
                    <div id="close">
                        <div class="closeupdate" style="position:absolute; top: 10px; z-index:100; right:10px; ">
                            <i class="iconfont" style="font-size:1.5rem; color:#999;">&#xe614;</i>
                        </div>

                        <div class="closeupdate" style="position:absolute; top: 370px; z-index:100; width:100%; text-align:center;">
                            <span class="iconfont" style="border:1px solid #f90; border-radius:4px; padding:10px 20px; font-size:1.2rem; color:#f90;">我知道了</span>
                        </div>
                    </div>    
                        
                </div>
            </div>

		</div>
		<script src="../assets/js/mui.min.js"></script>
		<script src="../assets/js/cookie.min.js"></script>
        <script src="../assets/js/swiper.min.js"></script>
		<script>
			(function($, doc) {
                var ischeck = localStorage.getItem('ischeck') ? localStorage.getItem('ischeck') : 0;
                if(ischeck == 0) {
                    // $('.update').css('display', 'initial');
                    document.getElementById('update').style.display = 'initial';
                }

                // 弹出引导页
                var mySwiper = new Swiper('.swiper-container',{
                   loop: false,
                   autoplay: 3000,
                   pagination: '.swiper-pagination',
                });

                $('#close').on('tap', '.closeupdate', function(e) {
                    document.getElementById('update').style.display = 'none';
                    localStorage.setItem('ischeck', 1);
                });
                
				Cookies.set('ctype', 'C1');
				Cookies.set('ltype', '1');
				Cookies.set('cid', '1');
				
				var exam_btn = doc.getElementById('exam-btn');
				//点击选择驾照类型
				$('#car_type').on('tap', '.mui-table-view-cell', function(e) {
					var car_type = this.getAttribute('id');
					var child = doc.querySelector('#'+car_type+' .choose');
					var f_child = doc.querySelector('#'+car_type+' .mui-media-body');
					Cookies.set('ctype', car_type);
                    child.className += " choosed";
                    f_child.className += " active";
                	var siblings = sibling(child.parentNode.parentNode);
                	var f_siblings = sibling(f_child.parentNode.parentNode);
                    cancelSiblings(siblings, 'choose', 'iconfont choose');
                    cancelSiblings(f_siblings, 'mui-media-body', 'mui-media-body choose');
				});
				//点击选择科目选择
				$('#lesson_type').on('tap', '.mui-table-view-cell', function(e) {
					var lesson_id = this.getAttribute('id');
					var lesson_type = this.getAttribute('data-id');
					Cookies.set('ltype', lesson_type);
					var l_child = doc.querySelector('#'+lesson_id+' .lesson-span');
                    l_child.className += " choosed";
                	var l_siblings = sibling(l_child.parentNode);
                    cancelSiblings(l_siblings, 'lesson-span', 'lesson-span choose');
				});
				
				//点击练习
				exam_btn.addEventListener('tap', function(e) {
					location.href = "default.html";
				});

                //关闭底部提示
                var closebannerbtn = document.getElementById('closebanner');
                closebannerbtn.addEventListener('tap', function(e) {
                    var tikubanner = document.getElementById('tikubanner');
                    if ('object' == typeof tikubanner) {
                        tikubanner.style.display = 'none';
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
            function cancelSiblings( obj, targetClass, newClass ) {
                for (var i = 0; i < obj.length; i++) {
                    var id = obj[i].getAttribute('id');
                    var child = document.querySelector("#" + id + " ." + targetClass);
//                  console.log(child);
                    child.className = newClass;
                }
            }
            //清除缓存
            function clearcache() {
		    	localStorage.removeItem('practicelist');
		    	localStorage.removeItem('qtotal');
		    	localStorage.removeItem('questions');
		    	layer.open({
				    content: '<span style="color:#555">清理成功</span>',
				    style: 'background-color:#fff; color:#fff; border:none;',
				    time:2,
				});
            }
            //退出登陆
      //       function logout() {
      //       	mui.ajax({
      //       		type:"post",
      //       		url:"logout.php",
      //       		dataType:'json',
      //       		beforeSend:function() {
      //       			var loading = '<div class="mui-loading" style="padding-top:10px; color:#fff"><div class="mui-spinner"></div><p style="text-align: center; margin-top:5px; color:#555;">正在退出中</p></div>';
				  //   	layer.open({
						//     content: loading,
						//     style: 'background-color:#fff; color:#fff; border:none;',
						//     time:2,
						// });
      //       		},
      //       		timeout:10000,
      //       		success:function(data) {
      //       			localStorage.removeItem('loginauth');
      //       			if(data.code == 200) {
      //       				layer.open({
						// 	    content: '<span style="color:#555">退出成功</span>',
						// 	    style: 'background-color:#fff; color:#fff; border:none;',
						// 	    time:1,
						// 	});
      //       			}
      //       		},
      //       		error:function() {
      //       			layer.open({
						//     content: '<span style="color:#555">退出失败</span>',
						//     style: 'background-color:#fff; color:#fff; border:none;',
						//     time:1,
						// });
      //       		}
      //       	})
      //       }

      		// 退出成功
      		function logout() {
    			localStorage.removeItem('loginauth');
      			layer.open({
				    content: '<span style="color:#555">退出成功</span>',
				    style: 'background-color:#fff; color:#fff; border:none;',
				    time:1,
				});		
      		}
		</script>

		<?php require_once 'cs.php';echo '<img src="'._cnzzTrackPageView(1259547373).'" width="0" height="0"/>';?>

	</body>
</html>
