<?php
	require_once '../include/config.php';
	$sid = isset($_COOKIE['sid']) ? htmlspecialchars($_COOKIE['sid']) : 1;

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
			.mui-grid-view.mui-grid-9 .mui-table-view-cell {border-right-style:dashed;}
			.choosed {color:#00BD9C !important}
			.active {color:#00BD9C !important}
			.choose {color: #999 ;}
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
	</head>
	
	<body style="background: #f5f5f5;">
		<header class="mui-bar mui-bar-nav">
			<a class="mui-action-back mui-icon mui-icon-left iconfont" style="color: #00BD9C; font-size:1.3rem;">&#xe608;</a>
			<h1 class="mui-title" id="index_title">驾考题库</h1>
		</header>
		<div class="mui-content" style="background: #f5f5f5;">
			<div class="container">
		        <div class="swiper-container" >
		          	<div class="swiper-wrapper" style="height: 200px; width:100%;">
		              	<div class="swiper-slide" style="background: #f5f5f5; width:100%; height: 200px;"><img src="../assets/images/ads2.png" height="200px" width="100%" alt="" /></div>
		              	<div class="swiper-slide" style="background: #f5f5f5; width:100%; height: 200px;"><img src="../assets/images/ads1.png" height="200px" width="100%" alt="" /></div>
		          	</div>
		          	<div class="swiper-pagination"></div>
		        </div>
	      	</div>
	        <ul class="mui-table-view mui-grid-view mui-grid-9" id="practise" style="background: #fff;">
	            <li id="order_practise" data-id="1" class="mui-table-view-cell mui-media mui-col-xs-6 " style="border-bottom-style:dashed;">
	              <a href="javascript:;">
	                <span class="iconfont" style="font-size:2.2rem; color: #00bb9c;">&#xe622;</span>
	                <div class="mui-media-body">顺序练习</div>
	              </a>
	            </li>
	            <li id="random_practise" data-id="2" class="mui-table-view-cell mui-media mui-col-xs-6 " style="border-bottom-style:dashed;">
	              <a href="javascript:;" style="">
	                <span class="iconfont" style="font-size:2.2rem; color:#56abe4">&#xe623;</span>
	                <div class="mui-media-body" style="">随机练习</div>
	              </a>
	            </li>
	            <li id="chapter_practise" data-id="3" class="mui-table-view-cell mui-media mui-col-xs-6 ">
	              <a href="javascript:;">
	                <span class="iconfont choose" style="font-size:2rem; color:#ea8010">&#xe620;</span>
	                <div class="mui-media-body">章节练习</div>
	              </a>
	            </li>
	            <li id="emulate_practise" data-id="4" class="mui-table-view-cell mui-media mui-col-xs-6 ">
	              <a href="javascript:;">
	                <span class="iconfont choose" style="font-size:2rem; color:#eb4f38">&#xe621;</span>
	                <div class="mui-media-body" style="">模拟考试</div>
	              </a>
	            </li>
            </ul>
            <ul class="mui-table-view mui-grid-view mui-grid-9" id="practise-album" style="background: #fff; margin-top: 15px;">
	            <li id="error_question" data-id="5" class="mui-table-view-cell mui-media mui-col-xs-6 " style="">
	              <a href="javascript:;">
	                <span class="iconfont" style="font-size:2rem; color: #EB9316;">&#xe626;</span>
	                <div class="mui-media-body">我的错题</div>
	              </a>
	            </li>
	            <li id="collection_question" data-id="6" class="mui-table-view-cell mui-media mui-col-xs-6 " style="">
	              <a href="javascript:;" style="">
	                <!--<span class="iconfont" style="font-size:2.3rem; color:#eb4f38">&#xe625;</span>-->
	                <span class="iconfont" style="font-size:2rem; color:#ee4c4c">&#xe627;</span>
	                <div class="mui-media-body" style="">我的收藏</div>
	              </a>
	            </li>
	          
            </ul>
		</div>
		<script src="../assets/js/mui.min.js"></script>
		<script src="../assets/js/cookie.min.js"></script>
		<script src="../assets/js/swiper.min.js"></script>
		<script>
			(function($, doc) {
				// 轮播图
	         	var mySwiper = new Swiper('.swiper-container',{
		           loop: true,
		           autoplay: 3000,
		           pagination: '.swiper-pagination',
	         	});
				if(!Cookies.get('ctype') || !Cookies.get('ltype') || !Cookies.get('cid')) {
          			location.href="index.html?id=<?php echo $sid; ?>";
					return false;
				}
				var t = [];
				t['C11'] = '小车科目一';
				t['C14'] = '小车科目四';
				t['A21'] = '货车科目一';
				t['A24'] = '货车科目四';
				t['A11'] = '客车科目一';
				t['A14'] = '客车科目四';
				t['D1'] = '摩托车科目一';
				t['D4'] = '摩托车科目四';

				var ctype = Cookies.get('ctype');
				var ltype = Cookies.get('ltype');
				var k = ctype+ltype;
				doc.getElementById('index_title').innerHTML = !t[k] ? '驾考题库' : t[k];
				//练习
				$('#practise').on('tap', '.mui-table-view-cell', function(e) {
					var data_id = this.getAttribute('data-id');
                    if ( data_id == 4 )
                    {
                        location.href = "test.html";
                        return false;
                    } else if(data_id == 3) {
                    	location.href = "chapters.html";
                    	return false;
                    } 
					location.href = "list.html?t="+data_id;
					return false;
				});
				//错题和收藏
				$('#practise-album').on('tap', '.mui-table-view-cell', function(e) {
					var data_id = this.getAttribute('data-id');
                    if ( data_id == 5 )
                    {
                        location.href = "myerr.html";
                        return false;
                    }
					location.href = "list.html?t="+data_id;
					return false;
				});
				
				//
			})(mui,document);
		</script>
	</body>
</html>
