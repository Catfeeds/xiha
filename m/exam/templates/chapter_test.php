<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
        <title>章节练习-牌照<?php echo $ctype; ?>-科目<?php echo $stype; ?></title>
        <meta name="Keywords" content="嘻哈学车,科目一,科目四,科目一模拟考试,科目四模拟考试,模拟考试,驾照,考驾照,驾驶员模拟考试">
        <meta name="description" content="嘻哈学车提供2016最新科目一考试和科目四模拟考试，采用公安部2016最先驾校模拟考试，考驾照模拟试题2016，驾校一点通模拟考试c1，驾驶员考试科目一，考驾照、做驾驶员模拟考试试题就来嘻哈学车！">
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/mui.min.css" />
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/style.css?=<?php echo $r; ?>" />
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/font/iconfont/iconfont.css" />
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/swiper.min.css" />
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/exam/exercise.css?=<?php echo $r; ?>" />
        <script src="<?php echo $root_path; ?>/assets/js/mui.min.js"></script>
        <script src="<?php echo $root_path; ?>/assets/js/exam/functions.js?=<?php echo $r; ?>"></script>
        <style type="text/css">
		</style>
    </head>
    
    <body style="background: #f5f5f5;">
    	<?php if($os == 'web') { ?>
        <header class="mui-bar mui-bar-nav">
            <a class="mui-action-back mui-icon mui-icon-left iconfont" style="color: #00BD9C; font-size:1.3rem;">&#xe608;</a>
            <h1 class="mui-title" id="title">章节练习</h1>
        </header>
        <?php } ?>
        <div class="mui-content" style="background: #f5f5f5;">
            	<div class="container" style="margin-bottom: 75px;">
                	<div class="swiper-container" >
                      	<div class="swiper-wrapper" style="width:100%;" id="question" >
			                <div class="mui-loading" style="background: #f5f5f5; padding-top:10px; margin:0px auto;">
			                    <div class="mui-spinner">
			                    </div>
			                    <p style="text-align: center; margin-top:5px;">正在加载中</p>
			                </div>
                     	</div>  
                 	</div>
                 	<!--解释-->
		            <div id="showexplain" style="background: #f5f5f5; margin-top: 10px; display: none; height: auto;">
		                <ul class="mui-table-view" style="margin: 0px;">
		                    <li class="mui-table-view-cell" title="" style="color: #333333;" id="explain">
		                    </li>
		                </ul>
		            </div>
              </div>
        </div>
        <nav class="mui-bar mui-bar-tab" style="height: 65px !important;">
			<div id="prev" class="button-prev nav-tab-item" style="display:table-cell; background: none; color: #5c5c5c;">
				<span class="mui-icon iconfont" style="font-size: 1.8rem;">&#xe633;</span>
				<span class="mui-tab-label">上一题</span>
			</div>
			<div id="next" class="button-next nav-tab-item" style="background: none; color: #5c5c5c;">
				<span class="mui-icon iconfont" style="font-size: 1.8rem;">&#xe634;</span>
				<span class="mui-tab-label">下一题</span>
			</div>
			<div id="collection" class="nav-tab-item" style="background: none; color: #5c5c5c;">
				<span class="mui-icon iconfont">&#xe636;</span>
				<span class="mui-tab-label">收藏</span>
			</div>
			<div id="questions_no_list" class="nav-tab-item" style="background: none; color: #5c5c5c;">
				<span class="mui-icon iconfont">&#xe621;</span>
				<span class="mui-tab-label"><span id="qid">0</span>/<span id="qtotal">0</span></span>
			</div>
		</nav>
        <script>
            var root_path = "<?php echo $root_path; ?>";
            var host = "<?php echo HOST; ?>";
            var ctype = "<?php echo $ctype; ?>";
            var stype = "<?php echo $stype; ?>";
            var t = "<?php echo $t; ?>";
            var sid = "<?php echo $sid; ?>";
            var chapid = "<?php echo $chapterid; ?>";
            var os = "<?php echo $os; ?>";
        </script>
        <script type="text/template" id="question_temp">

          	{@if question_list.length > 0}
              	{@each question_list as item, index}
                      
                      <div class="swiper-slide" style="background: #f5f5f5; width:100%; margin-top: 10px;">
                          <div style="padding:0px 10px;">
                            <p style="font-size:1rem; color: #555; line-height: 1.5em;">
                                <span style="color: #888888;">
                                    <!--(<em style="color: #CF2D28;">${index}</em>/${qtotal})-->
                                    {@if item.type == 1}
                                        <span style="background: #18b4ed; border-radius: 3px; color: #fff; width: 25px; height: 25px; text-align: center; display: inline-block; line-height: 25px; ">判</span>
                                    {@else if item.type == 2}
                                        <span style="background: #18b4ed; border-radius: 3px; color: #fff; width: 25px; height: 25px; text-align: center; display: inline-block; line-height: 25px; ">单</span>
                                    {@else}
                                        <span style="background: #18b4ed; border-radius: 3px; color: #fff; width: 25px; height: 25px; text-align: center; display: inline-block; line-height: 25px; ">多</span>
                                    {@/if}
                                </span> 
                                ${item.question}
                            </p>
                            {@if item.mediaType == 1}
                                <img src="${item.imageurl}" style="width: 100%; max-height: 250px;" alt="" />
                            {@else if item.mediaType == 2}
                                <div class="flowplayer">
                                    <video id="player" autobuffer style="width:100%; height: 200px;" controls autoplay preload="auto" webkit-playsinline>
                                      <source src="${item.imageurl}" type="video/mp4">
                                      <object data="${item.imageurl}" style="width:100%">
                                        <embed src="${item.imageurl}" style="width:100%">
                                      </object> 
                                    </video>
                                </div>                    
            
                            {@/if}
                        </div>
                        <ul class="mui-table-view mui-ul-click" style="">
                            {@if item.type == 1}
                                <li class="mui-table-view-cell mui-single" id="" title="1" style="color: #555;">
                                    <span class="xuanze_no">A</span> 正确
                                </li>
                                <li class="mui-table-view-cell mui-single" id="" title="2" style="color: #555;">
                                    <span class="xuanze_no">B</span> 错误
                                </li>
                            {@else if item.type == 2}
                                <li class="mui-table-view-cell mui-single" id="" title="1" style="color: #555;">
                                    <span class="xuanze_no">A</span> <span style="line-height: 2rem;">${item.an1}</span>
                                </li>
                                <li class="mui-table-view-cell mui-single" id="" title="2" style="color: #555;">
                                    <span class="xuanze_no">B</span> ${item.an2}
                                </li>
                                <li class="mui-table-view-cell mui-single" id="" title="3" style="color: #555;">
                                    <span class="xuanze_no">C</span> ${item.an3}
                                </li>
                                <li class="mui-table-view-cell mui-single" id="" title="4" style="color: #555;">
                                    <span class="xuanze_no">D</span> ${item.an4}
                                </li>
                            {@else}
                                <li class="mui-table-view-cell mui-multi" id="" title="1" style="color: #555;">
                                    <span class="xuanze_no">A</span> ${item.an1}
                                </li>
                                <li class="mui-table-view-cell mui-multi" id="" title="2" style="color: #555;">
                                    <span class="xuanze_no">B</span> ${item.an2}
                                </li>
                                <li class="mui-table-view-cell mui-multi" id="" title="3" style="color: #555;">
                                    <span class="xuanze_no">C</span> ${item.an3}
                                </li>
                                <li class="mui-table-view-cell mui-multi" id="" title="4" style="color: #555;">
                                    <span class="xuanze_no">D</span> ${item.an4}
                                </li>
                            {@/if}
                        </ul>
                    	{@if item.type == 3}
                    		<div class="" style="padding:10px 20px; background: #fff;">
    							<button type="button" id='exam-btn' style='padding:8px 0px; border:none; background: #999;' disabled="true" class='mui-btn mui-btn-block mui-btn-red'>确认</button>
    						</div>
                        {@/if}
                  	</div>
                    
              	{@/each}
          	{@/if}

        </script>
        <script src="<?php echo $root_path; ?>/assets/js/juicer-min.js"></script>
        <script src="<?php echo $root_path; ?>/assets/js/layer/layer.js"></script>
        <script src="<?php echo $root_path; ?>/assets/js/exam/chapter_test.js?=<?php echo $r; ?>"></script>
    </body>
</html>
