<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>嘻哈学车</title>
    <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
    <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/mui.min.css" />
    <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/style.css" />
    <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/font/iconfont/iconfont.css" />
    <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/swiper.min.css" />
    <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/exam/app.css" />
    <script src="<?php echo $root_path; ?>/assets/js/mui.min.js"></script>
    <script src="<?php echo $root_path; ?>/assets/js/device.min.js"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <!--<script src="<?php echo $root_path; ?>/assets/js/exam/share.js"></script>-->
    <script>
        var root_path = "<?php echo $root_path; ?>";
        var sid = "<?php echo $sid; ?>";
    	var os = "<?php echo $os; ?>";    
    	if(os != 'web') {
	    	if(!device.mobile()) {
	        	mui.alert('你当前设备不是移动端');
	        	location.href = root_path+"exam/index-"+sid+"-web.html";
	      } else {
	      	
	      }
    	}
    		
    </script>
    <style type="text/css">
        .mui-table-view-cell:after {left:0px;}
        .mui-table-view:after {height: 0px;}
        .mui-grid-view.mui-grid-9 .mui-table-view-cell {border-bottom: none;}
        .choosed {color:#00BD9C !important}
        .active {color:#00BD9C !important}
        .choose {color: #999 ;}
        #topPopover {position: fixed;top: 16px;right: 6px;}
        #topPopover .mui-popover-arrow {left: auto;right: 6px;}
        .mui-table-view-cell>a:not(.mui-btn) {margin:-10px -7px;}
        .mui-popover .mui-popover-arrow:after {border-radius:1px;}
        .layermchild {border-radius: 10px !important;}
	   	.layermbtn span:first-child {border-radius: 0 0 0 10px !important;}
    </style>
    <script>
        var _hmt = _hmt || [];
        (function() {
          var hm = document.createElement("script");
          hm.src = "//hm.baidu.com/hm.js?ec52a986344c1bab363e1ae39c0dd626";
          var s = document.getElementsByTagName("script")[0]; 
          s.parentNode.insertBefore(hm, s);
        })();
        
        window.onload=function(){
//			if(window.localStorage){
//				alert("你当前的浏览器支持本地存储");
//			}else{
//				alert("你当前浏览器不支持本地存储");
//			}
//			if(window.indexedDB) {
//				alert("你当前的浏览器支持indexdb本地存储");
//			}else{
//				alert("你当前浏览器不支持indexdb本地存储");
//			}
		}
		
    </script>
</head>
<body style="background: #f5f5f5;">
	<?php if($os == 'web') { ?>
    <header class="mui-bar mui-bar-nav">
        <a id="menu" href="#topPopover" class="mui-action-menu mui-icon mui-pull-right iconfont" style="color:#10AEFF; font-size: 1.5rem;" >&#xe620;</a>
        <h1 class="mui-title" id="school_name">驾考题库</h1>
    </header>
    <?php } ?>
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

        <div id="tikubanner" style="margin:0px;position:absolute; bottom:0px; right:0px;width:100%; height:63px;z-index: 9999;">
          <a href="javascript:void(0);" alt="新题库" style="display:block;">
            <img src="<?php echo $root_path; ?>/assets/images/tiku2016banner.png" style="width:100%;" alt="嘻哈学车2016最新驾考题库，让您驾考更便捷，打开更快，更省流量！" />
          </a>
          <div id="closebanner" style="position:absolute; right:0;top:0;background:#00bd9c; color:#fff; font-size:22px; line-height:25px; border-bottom-left-radius:100%;width:2rem;height:2rem;text-align:right; padding-right:5px;">×</div>
        </div>

        <!-- 引导页 -->
        <div id="update" style="position: absolute; width:100%; top:0px; height:100%; background:rgba(0,0,0,0.5); z-index:10; display:none;">
            <div class="" style="position: relative; width:300px; background:#fff; border-radius:13px; margin:0px auto; top:70px; ">
                <div class="container" style=" margin:0px auto; z-index:99">
                    <div class="swiper-container" style="border-radius:13px;">
                        <div class="swiper-wrapper" style=" width:100%; border-radius:4px;">
                            <div class="swiper-slide" style="background: #f5f5f5; width:300px; "><img src="<?php echo $root_path; ?>/assets/images/update/update_115256.png" width="300px" alt="" /></div>
                            <div class="swiper-slide" style="background: #f5f5f5; width:300px; "><img src="<?php echo $root_path; ?>/assets/images/update/update_115331.png" width="300px" alt="" /></div>
                            <div class="swiper-slide" style="background: #f5f5f5; width:300px; "><img src="<?php echo $root_path; ?>/assets/images/update/update_115351.png" width="300px" alt="" /></div>
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
    <footer class="mui-bar mui-bar-tab" style="background: none; border: none; box-shadow: none;"> 
    	<p style="text-align: center; padding:0px 20px; padding-bottom: 10px; color: #a0a0a0;"><a href="http://m.xihaxueche.com" style="color: #a0a0a0;">安徽嘻哈网络技术有限公司</a>版权所有 <br />皖ICP备15016679号</p> 
	</footer>

    <script src="<?php echo $root_path; ?>/assets/js/cookie.min.js"></script>
    <script src="<?php echo $root_path; ?>/assets/js/layer/layer.js"></script>
    <script src="<?php echo $root_path; ?>/assets/js/swiper.min.js"></script>
    <script src="<?php echo $root_path; ?>/assets/js/exam/index.js"></script>    
    <?php require_once 'cs.php'; echo '<img src="'._cnzzTrackPageView(1259944424).'" width="0" height="0"/>';?>
    <?php require_once 'cs.php'; echo '<img src="'._cnzzTrackPageView(1259966824).'" width="0" height="0"/>';?>
    <?php require_once 'cs.php'; echo '<img src="'._cnzzTrackPageView(1259966799).'" width="0" height="0"/>';?>
</body>
</html>
