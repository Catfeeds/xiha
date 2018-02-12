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
            body {
                background-image:url('<?php echo $root_path; ?>assets/images/share/beijingtu.png') ;
                background-size:100%;
                background-repeat:no-repeat;
            }
        </style>
    </head>
    <body>
        <div style="border: 0px solid red; width: 300px;height: 1px; margin: 27.5% auto;">
        </div>
        <div style="border: 0px solid red; width: 220px; margin: 0 auto; text-align: center; padding: 10px 10px;">
            <div style="padding: 10px 10px; border: 0px solid green;font-size: 1em; font-weight: bold; line-height: 30px">
                我 在 嘻 哈 学 车 的 <br/>模拟成绩：<span style="color: red"><?php echo $score; ?></span>
            </div>
            <div style="border: 0px solid blue; font-size: 0.8em;">
                快 来 和 我 一 起 PK 吧！
            </div>
            <div style="border: 0px solid red; margin-top: 40px">
                <div style="border: 1px solid #eee; border-radius: 100%; width: 100%; height: 190px; margin-top: 10px; background: #fff">
                    <div style="border: 0px solid red;width: 70%; height: 70%;margin: 28px auto">
                        <img src="<?php echo $root_path; ?>assets/images/share/erweimaimg.png" style="width: 95%">
                    </div>
                </div>
            </div>
        </div>
        <div style="border: 0px solid red; text-align: center; margin-top: 10px">
            长按二维码即可挑战
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
        <!--<script src="<?php echo $root_path; ?>/assets/js/exam/share.js"></script>-->
    </body>
</html>
