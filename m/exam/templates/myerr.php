<!DOCTYPE html>
<html>
  	<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
        <title>我的错题-牌照<?php echo $ctype; ?>-科目<?php echo $stype; ?></title>
        <meta name="Keywords" content="嘻哈学车,科目一,科目四,科目一模拟考试,科目四模拟考试,模拟考试,驾照,考驾照,驾驶员模拟考试">
        <meta name="description" content="嘻哈学车提供2016最新科目一考试和科目四模拟考试，采用公安部2016最先驾校模拟考试，考驾照模拟试题2016，驾校一点通模拟考试c1，驾驶员考试科目一，考驾照、做驾驶员模拟考试试题就来嘻哈学车！">
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/mui.min.css" />
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/style.css" />
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/font/iconfont/iconfont.css" />
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/swiper.min.css" />
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/exam/exercise.css" />
        <script src="<?php echo $root_path; ?>/assets/js/mui.min.js"></script>
        <style type="text/css">
		</style>
    </head>
  </head>

  <body style="background: #f5f5f5;">
	<?php if($os == 'web') { ?>
    <header class="mui-bar mui-bar-nav">
      <a class="mui-action-back mui-icon mui-icon-left iconfont" style="color: #00BD9C; font-size:1.3rem;">&#xe608;</a>
      <h1 class="mui-title">我的错题</h1>
    </header>
	<?php } ?>
    <div class="mui-content" style="background: #f5f5f5;">
      <ul class="mui-table-view mui-grid-view mui-grid-9" id="err_practise" style="background: #fff;">
        <li id="" data-id="5" class="mui-table-view-cell mui-media mui-col-xs-6 " style="border-bottom-style:dashed;">
          <a href="javascript:;">
            <span class="iconfont" style="font-size:2.2rem; color: #00bb9c;">&#xe620;</span>
            <div class="mui-media-body">练习错题</div>
          </a>
        </li>
        <li id="" data-id="6" class="mui-table-view-cell mui-media mui-col-xs-6 " style="border-bottom-style:dashed;">
          <a href="javascript:;" style="">
            <span class="iconfont" style="font-size:2.2rem; color:#56abe4">&#xe621;</span>
            <div class="mui-media-body" style="">模拟错题</div>
          </a>
        </li>
      </ul>
    </div>
	<script>
		var root_path = "<?php echo $root_path; ?>";
        var host = "<?php echo HOST; ?>";
        var ctype = "<?php echo $ctype; ?>";
        var stype = "<?php echo $stype; ?>";
        var t = "<?php echo $t; ?>";
        var sid = "<?php echo $sid; ?>";
        var os = "<?php echo $os; ?>";
	</script>
	
    <script>
	    (function($, doc) {
	      $('#err_practise').on('tap', '.mui-table-view-cell', function(e) {
	        var data_id = this.getAttribute('data-id');
	        if (5 == data_id) {
            	location.href = root_path+"exam/exercise-"+sid+"-"+ctype+"-"+stype+"-"+data_id+"-1"+"-"+os+".html";
	          	return false;
	        } else {
                location.href = root_path+"exam/exercise-"+sid+"-"+ctype+"-"+stype+"-"+data_id+"-1"+"-"+os+".html";
	          	return false; 
	        }
	      });
	      
	    })(mui, document);
    </script>
  </body>
</html>