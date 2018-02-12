<!DOCTYPE html>
<html>
  	<head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
        <title>章节练习-牌照<?php echo $ctype; ?>-科目<?php echo $stype; ?></title>
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
      <h1 class="mui-title">章节练习</h1>
    </header>
    <?php } ?>

    <div class="mui-content" id="chapter" style="background: #f5f5f5;">
      <div class="mui-loading" style="padding-top: 10px;">
        <div class="mui-spinner"></div>
        <p style="text-align: center; margin-top: 5px;">正在加载中</p>
      </div>
    </div>
    <script type="text/html" id="chapter_tmpl">
		<ul class="mui-table-view" id="">
		  	{@each chapter_list as item, index}
		    <li class="mui-table-view-cell" data-cid="${item.cid}" >
				<div style="float:left;width:90%">
				  <span style="background: #18b4ed; color:#fff; line-height: 23px; padding:2px; width:23px; height: 23px; font-size: 14px; text-align: center; border-radius: 3px; display: inline-block;">${index|numberPlus}</span> ${item.title}
				</div>
				<div style="float:right;width:10%;margin:0 auto;">
				   <span class="mui-badge " style="display:inline-block;">${item.total}</span>
				</div>
			</li>
			{@/each}
		</ul>
		
    </script>
	<script>
		var root_path = "<?php echo $root_path; ?>";
        var host = "<?php echo HOST; ?>";
        var ctype = "<?php echo $ctype; ?>";
        var stype = "<?php echo $stype; ?>";
        var t = "<?php echo $t; ?>";
        var sid = "<?php echo $sid; ?>";
        var os = "<?php echo $os; ?>";
	</script>
    <script src="<?php echo $root_path; ?>/assets/js/juicer-min.js"></script>
    <script>
    	(function($, doc) {
			var version = 'v2';
			var folder = 'exam';
			var aname = 'chapter_list.php';
			getChapterList(version, folder, aname, ctype, stype);
			// 点击章节列表
			$('#chapter').on('tap', '.mui-table-view-cell', function(e) {
				var data_cid = this.getAttribute('data-cid');
		        location.href = root_path+"exam/exercise-"+sid+"-"+ctype+"-"+stype+"-"+t+"-"+data_cid+"-"+os+".html";	
			})
    		
    		// 获取章节列表
			function getChapterList(version, folder, aname, ctype, stype) {
				var param = {
					'ctype':ctype,
					'stype':stype	
				};
				$.ajax({
					type:"post",
					url:host+"/"+version+"/"+folder+"/"+aname,
					async:false,
					data:param,
					success:function(data) {
						if(200 == data.code) {
						    var tpl = doc.getElementById('chapter_tmpl').innerHTML;
						    var numberplus = function(number) {
						    	return parseInt(number) + 1;
						    }
						    juicer.register('numberPlus', numberplus); //注册自定义函数
						    var html = juicer(tpl, data.data);
						    doc.getElementById('chapter').innerHTML = html; 
						} else {
							$.toast(data.data);
							return false;
						}
					},
					error:function() {
						$.toast('网络错误，请检查网络');
						return false;
					}
				});
			}

    	})(mui, document);
    </script>
  </body>
</html>
