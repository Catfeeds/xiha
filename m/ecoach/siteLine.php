<?php
    include "../include/config.php";
    $root_path = rtrim(ROOT_PATH, '/');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
        <title>场地标线</title>
        <meta name="Keywords" content="">
        <meta name="description" content="">
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/mui.min.css" />
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/style.css" />
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/font/iconfont/iconfont.css" />
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/swiper.min.css" />
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/ecoach/siteLine.css" />
        <script src="<?php echo $root_path; ?>/assets/js/mui.min.js"></script>
    </head>
  </head>
  
  <body style="background: #f5f5f5;">
    <!-- <header class="mui-bar mui-bar-nav" style="background: #fff;">
      <a class="mui-action-back mui-icon mui-icon-left iconfont" style="color: #00BD9C; font-size:1.3rem;">&#xe608;</a>
      <h1 class="mui-title">场地标线</h1>
    </header>
 -->
    <div class="mui-content" style="background: #f6f6f6;">
    <ul class="mui-table-view">
 
      <li class="mui-table-view-cell mui-collapse">
        <a class="mui-navigate-right" href="#">
<img src="../assets/images/ecoach/daoku.png" width="21" />
倒车入库</a>
        <div class="mui-collapse-content">
            <div class="rule-wrapper">
                <img src="../assets/images/ecoach/daocheruku.png" width="100%" />
            </div>
        </div>
      </li>

      <li class="mui-table-view-cell mui-collapse">
        <a class="mui-navigate-right" href="#">
<img src="../assets/images/ecoach/cefang.png" width="21" />
侧方停车</a>
        <div class="mui-collapse-content">
            <div class="rule-wrapper">
                <img src="../assets/images/ecoach/cefangtingche.png" width="100%" />
            </div>
        </div>
      </li>

      <li class="mui-table-view-cell mui-collapse">
        <a class="mui-navigate-right" href="#">
<img src="../assets/images/ecoach/zhijiao.png" width="21" />
直角转弯</a>
        <div class="mui-collapse-content">
            <div class="rule-wrapper">
<div style="padding: 30px;">
                <img src="../assets/images/ecoach/zhijiaozhuanwan.png" width="100%" />
</div>
            </div>
        </div>
      </li>

      <li class="mui-table-view-cell mui-collapse">
        <a class="mui-navigate-right" href="#">
<img src="../assets/images/ecoach/sLineDrive.png" width="21" />
曲线行驶</a>
        <div class="mui-collapse-content">
            <div class="rule-wrapper">
                <img src="../assets/images/ecoach/quxianxingshi.png" width="100%" />
            </div>
        </div>
      </li>

      <li class="mui-table-view-cell mui-collapse">
        <a class="mui-navigate-right" href="#">
<img src="../assets/images/ecoach/podao.png" width="21" />
坡道定点停车与起步</a>
        <div class="mui-collapse-content">
            <div class="rule-wrapper">
<div style="padding: 10px;">
                <img src="../assets/images/ecoach/podaodingdiantingcheyuqibu.png" width="100%" />
</div>
            </div>
        </div>
      </li>

    </ul>
    </div>
  </body>
</html>
