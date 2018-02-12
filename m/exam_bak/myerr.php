<?php
  // @author Gao Dcheng
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
      <h1 class="mui-title">我的错题</h1>
    </header>

    <div class="mui-content" style="background: #f5f5f5;">
      <ul class="mui-table-view mui-grid-view mui-grid-9" id="err_practise" style="background: #fff;">
        <li id="" data-id="1" class="mui-table-view-cell mui-media mui-col-xs-6 " style="border-bottom-style:dashed;">
          <a href="javascript:;">
            <span class="iconfont" style="font-size:2.2rem; color: #00bb9c;">&#xe620;</span>
            <div class="mui-media-body">练习错题</div>
          </a>
        </li>
        <li id="" data-id="2" class="mui-table-view-cell mui-media mui-col-xs-6 " style="border-bottom-style:dashed;">
          <a href="javascript:;" style="">
            <span class="iconfont" style="font-size:2.2rem; color:#56abe4">&#xe621;</span>
            <div class="mui-media-body" style="">模拟错题</div>
          </a>
        </li>
      </ul>
    </div>
    <script src="../assets/js/mui.min.js"></script>
    <script src="../assets/js/cookie.min.js"></script>
    <script>
    (function($, doc) {
      $('#err_practise').on('tap', '.mui-table-view-cell', function(e) {
        var data_id = this.getAttribute('data-id');
        if ( data_id == 1 )
        {
          location.href = "errorq.php";
          return false;
        } else {
          location.href = "terrorq.php";
          return false;
        }
      });
      
    })(mui, document);
    </script>
  </body>
</html>