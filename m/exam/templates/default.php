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
        <!--<link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/exam/app.css" />-->
        <style type="text/css">
            .mui-table-view-cell:after {left:0px;}
            .mui-table-view:after {height: 0px;}
            .mui-grid-view.mui-grid-9 .mui-table-view-cell {border-right-style:dashed;}
            .choosed {color:#00BD9C !important}
            .active {color:#00BD9C !important}
            .choose {color: #999 ;}
        </style>
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
    	<?php if($os == 'web') { ?>
        <header class="mui-bar mui-bar-nav">
            <a class="mui-action-back mui-icon mui-icon-left iconfont" style="color: #00BD9C; font-size:1.3rem;">&#xe608;</a>
            <h1 class="mui-title" id="index_title">驾考题库</h1>
        </header>
        <?php } ?>
        <div class="mui-content" style="background: #f5f5f5;">
            <div class="container">
                <div class="swiper-container" >
                      <div class="swiper-wrapper" style="height: 200px; width:100%;">
                          <div class="swiper-slide" style="background: #f5f5f5; width:100%; height: 200px;"><img src="<?php echo $root_path; ?>/assets/images/ads2.png" height="200px" width="100%" alt="" /></div>
                          <div class="swiper-slide" style="background: #f5f5f5; width:100%; height: 200px;"><img src="<?php echo $root_path; ?>/assets/images/ads1.png" height="200px" width="100%" alt="" /></div>
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
                <li id="collection_question" data-id="7" class="mui-table-view-cell mui-media mui-col-xs-6 " style="">
                  <a href="javascript:;" style="">
                    <!--<span class="iconfont" style="font-size:2.3rem; color:#eb4f38">&#xe625;</span>-->
                    <span class="iconfont" style="font-size:2rem; color:#ee4c4c">&#xe627;</span>
                    <div class="mui-media-body" style="">我的收藏</div>
                  </a>
                </li>
              
            </ul>
        </div>
        <footer class="" style="background: none; border: none; margin-top: 10px; box-shadow: none;"> 
	    	<p style="text-align: center; padding:0px 20px; padding-bottom: 10px; color: #a0a0a0;"><a href="http://m.xihaxueche.com" style="color:#a0a0a0;">安徽嘻哈网络技术有限公司</a>版权所有 <br />皖ICP备15016679号</p> 
		</footer>

        <script>
            var root_path = "<?php echo $root_path; ?>";
            var ctype = "<?php echo $ctype; ?>";
            var stype = "<?php echo $stype; ?>";
            var sid = "<?php echo $sid; ?>";
            var os = "<?php echo $os; ?>";
        </script>
        <script src="<?php echo $root_path; ?>/assets/js/mui.min.js"></script>
        <script src="<?php echo $root_path; ?>/assets/js/cookie.min.js"></script>
        <script src="<?php echo $root_path; ?>/assets/js/swiper.min.js"></script>
        <script src="<?php echo $root_path; ?>/assets/js/exam/default.js"></script>
    </body>
</html>
