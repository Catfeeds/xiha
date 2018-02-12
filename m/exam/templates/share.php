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
        </style>
    </head>
    <body style="background: #f5f5f5;">
    	<?php if($os == 'web') { ?>
        <header class="mui-bar mui-bar-nav">
            <a id="muiback" class="mui-action-back mui-icon mui-icon-left iconfont" style="color: #00BD9C; font-size:1.3rem;">&#xe608;</a>
            <h1 class="mui-title" id="index_title">模拟考试</h1>
            <!--<a href="" class="mui-icon mui-icon-right" style="color: #00BD9C; font-size:1.3rem; float: right">
                <img src="<?php echo $root_path; ?>/assets/images/share/share.png" style="width: 30px; height: 25px;color: #00BD9C;">
            </a>-->
        </header>
        <?php } ?>
        <div class="mui-content" style="background: #f5f5f5;">
            <div class="container">
                <div class="swiper-container" >
                    <?php if ($score >= 90) {?>
                        <img src="<?php echo $root_path; ?>/assets/images/share/beijing.png" style="width: 100%;">
                    <?php } else if ($score > 80 && $score < 90) {?>
                        <img src="<?php echo $root_path; ?>/assets/images/share/beijin2.png" style="width: 100%;">
                    <?php } else { ?>
                        <img src="<?php echo $root_path; ?>/assets/images/share/beijing1.png" style="width: 100%;">
                    <?php }?>
                </div>
                <div class="" style="margin: 20px auto; text-align: center">
                    <h3>模拟成绩：<span style="color: red; "><?php echo $score; ?></span></h3>
                </div>
                <div class="" style="margin: 20px auto; text-align: center; font-size: 1em;">
                    <?php if ($score >= 90) {?>
                        <span>登上王者之位！把成绩分享给好友吧！</span>
                    <?php } else if ($score > 80 && $score < 90) {?>
                        <span>离成绩只差一步！把成绩分享给好友吧！</span>
                    <?php } else { ?>
                        <span>万事开头难！把成绩分享给好友吧！</span>
                    <?php }?>
                </div>

                <div class="" style="margin: 20px auto; text-align: center;">
                    <div id="weixin" style="border: 0px solid red; width: 50%; float: left">
                        <img src="<?php echo $root_path; ?>assets/images/share/weixin.png" style="width: 30%;">
                    </div>
                    <div id="qq" style="border: 0px solid red; width: 50%; float: left">
                        <img src="<?php echo $root_path; ?>assets/images/share/qq.png" style="width: 30%;">
                    </div>
                </div>

                <div class="" style="margin-top: 10px; text-align: center; ">
                    <a style=" color: #f6f6f6; " id="exercise_again">
                        <span style="margin: 40px 10px; border: 1px solid green; border-radius: 5px; width: 90%; height: 45px; line-height: 45px; display: inline-block; text-align: center;  background: green; color: #f6f6f6;">
                            再战一局
                        </span>
                    </a>
                </div>
            </div>
        </div>
        <script>
            var root_path = "<?php echo $root_path; ?>";
            var sid = "<?php echo $sid; ?>";
            var ctype = "<?php echo $ctype; ?>";
            var stype = "<?php echo $stype; ?>";
            var os = "<?php echo $os; ?>";
            var score = "<?php echo $score; ?>";
        </script>
        <script src="<?php echo $root_path; ?>/assets/js/mui.min.js"></script>
        <script src="<?php echo $root_path; ?>/assets/js/cookie.min.js"></script>
        <script src="<?php echo $root_path; ?>/assets/js/swiper.min.js"></script>
        <script src="<?php echo $root_path; ?>/assets/js/exam/share.js"></script>
    </body>
</html>
