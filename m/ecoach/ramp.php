<?php
    include "../include/config.php";
    $root_path = rtrim(ROOT_PATH, '/');
?>
<!DOCTYPE html>
<html>
  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
      <title>坡道定点停车与起步</title>
      <meta name="Keywords" content="">
      <meta name="description" content="">
      <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/mui.min.css" />
      <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/style.css" />
      <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/font/iconfont/iconfont.css" />
      <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/swiper.min.css" />
      <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/ecoach/public.css" />
      <script src="<?php echo $root_path; ?>/assets/js/mui.min.js"></script>
  </head>
  <style>

  </style>
  <body style="background: #f5f5f5;">
    <!-- <div style="background: #fff;margin-bottom:50px;">
      <header class="mui-bar mui-bar-nav" style="background: #fff;">
        <div>
          <a class="mui-action-back mui-icon mui-icon-left iconfont" style="color: #00BD9C; font-size:1.3rem;">&#xe608;</a>
          <h1 class="mui-title">坡道定点停车与起步</h1>
        </div>
        
      </header>
    </div> -->
    <div style="height:100%;position:fixed;width:100%;">
      <div class="" style="background: #fff; margin-bottom: 1px;">
        <div id="slider" class="mui-slider" style="">
          <div id="sliderSegmentedControl" style="margin-top:10px;">
            <div id="first" style="float:left;text-align:center;width:14%; ">
              <a class="mui-control-item" href="#item1mobile" style="text-align:center;color:#333;font-size:15px;font-style:Microsoft YaHei">上坡</a> 
            </div>
            <div id="second" style="float:left;text-align:center;  width:72%;">
              <a class="mui-control-item" href="#item2mobile"  style="text-align:center;color:#333;font-size:15px;font-style:Microsoft YaHei" >停车</a>
            </div >
            <div id="third" style="float:left;text-align:center;width:14%; ">
              <a class="mui-control-item" href="#item3mobile"  style="text-align:center;color:#333;font-size:15px;font-style:Microsoft YaHei">起步</a>
            </div>
          </div>

          <div id="navbars" style="">
            <img src="../assets/images/ecoach/routestep3.1.png" width="100%" text-align="center" style="margin-bottom:10px;" />
          </div>
          <div id="navbars2" style="display:none;">
            <img src="../assets/images/ecoach/routestep3.2.png" width="100%" text-align="center" style="margin-bottom:10px;" />
          </div>
          <div id="navbars3" style="display:none;">
            <img src="../assets/images/ecoach/routestep3.3.png" width="100%" text-align="center" style="margin-bottom:10px;" />
          </div>
        </div>
      </div>
    <div class="mui-content" style="height:100%;position:fixed;width:100%; background: #fff;">
      <div style="padding-bottom:0px;">
          <div class="mui-slider-group" >
            <div id="item1mobile" class="mui-slider-item  mui-active">
              <div id="scroll1" class="mui-scroll-wrapper">
                  <div class="mui-scroll">
                      <ul>
                        <li><img src="../assets/images/ecoach/rampway1.png"  text-align="center"  /></li>
                        <li id="item1" style="margin-bottom:130px;margin-left:-20px;"><a href="#item2mobile"  style='padding:10px 0px; border:none; background: #7dd3c0;' class='mui-btn mui-btn-block mui-btn-success'>下一步</a></li>
                      </ul>
                  </div>
              </div>
            </div>
            <div id="item2mobile" class="mui-slider-item " style="display:none">
              <div class="mui-scroll-wrapper">
                  <div class="mui-scroll">
                      <ul >
                          <li><img src="../assets/images/ecoach/rampway2.png"  text-align="center"  /></li>
                          <li style="margin-bottom:130px;" id="item2"><a href="#item3mobile"  style='padding:10px 0px; border:none; background: #7dd3c0;' class='mui-btn mui-btn-block mui-btn-success'>下一步</a></li>
                      </ul>
                  </div>
              </div>
            </div>
            <div id="item3mobile" class="mui-slider-item " style="display:none">
              <div class="mui-scroll-wrapper">
                  <div class="mui-scroll">
                      <ul style="list-style:none;">                                    
                          <li ><img src="../assets/images/ecoach/rampway3.png"  text-align="center"  /></li>
                          <!-- <li id="item3" style="margin-bottom:130px;"><a href="#item3mobile"  style='padding:10px 0px; border:none; background: #7dd3c0;' class='mui-btn mui-btn-block mui-btn-success'>下一步</a></li> -->
                          <li id="" style="margin-bottom:130px;"></li>
                      </ul>
                  </div>
              </div>
            </div>
          
          </div>

      </div>
    </div>
  </div>
  </body>

    <script type="text/javascript">
        mui.init();
            (function($) {
                //阻尼系数
                var deceleration = mui.os.ios?0.003:0.0009;
                $('.mui-scroll-wrapper').scroll({
                    bounce: false,
                    indicators: true, //是否显示滚动条
                    deceleration:deceleration
                });
              
                var first = document.getElementById('first');
                var second = document.getElementById('second');
                var third = document.getElementById('third');
                var item1mobile = document.getElementById('item1mobile');
                var item2mobile = document.getElementById('item2mobile');
                var item3mobile = document.getElementById('item3mobile');
                var navbars = document.getElementById('navbars');
                var navbars2 = document.getElementById('navbars2');
                var navbars3 = document.getElementById('navbars3');
                var navbars4 = document.getElementById('navbars4');
                var navbars5 = document.getElementById('navbars5');
                first.addEventListener('tap', function() {
                  navbars.style.display = 'block';
                  navbars2.style.display = 'none';
                  navbars3.style.display = 'none';
                  item2mobile.style.display = 'none'
                  item1mobile.style.display = 'block';
                  item3mobile.style.display = 'none'
                }) 
                second.addEventListener('tap', function() {
                  navbars.style.display = 'none';
                  navbars2.style.display = 'block';
                  navbars3.style.display = 'none';
                  item1mobile.style.display = 'none'
                  item2mobile.style.display = 'block';
                  item3mobile.style.display = 'none'
                }) 
                third.addEventListener('tap', function() {
                  navbars.style.display = 'none';
                  navbars2.style.display = 'none';
                  navbars3.style.display = 'block';
                  item3mobile.style.display = 'block';
                  item2mobile.style.display = 'none'
                }) 
                
                var item1 = document.getElementById('item1');
                var item2 = document.getElementById('item2');
                // var item3 = document.getElementById('item3');
                item1.addEventListener('tap', function () {
                  item1mobile.style.display = 'none'
                  item2mobile.style.display = 'block';
                  item3mobile.style.display = 'none'
                  navbars.style.display = 'none';
                  navbars2.style.display = 'block';
                  navbars3.style.display = 'none';
                })
                item2.addEventListener('tap', function () {
                  item2mobile.style.display = 'none'
                  item3mobile.style.display = 'block';
                  item1mobile.style.display = 'none'
                  navbars.style.display = 'none';
                  navbars2.style.display = 'none';
                  navbars3.style.display = 'block';
                })
               
            })(mui);
    
   </script>
</html>
