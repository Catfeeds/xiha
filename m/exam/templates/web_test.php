<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
        <title>模拟考试-牌照<?php echo $ctype; ?>-科目<?php echo $stype; ?></title>
        <meta name="Keywords" content="嘻哈学车,科目一,科目四,科目一模拟考试,科目四模拟考试,模拟考试,驾照,考驾照,驾驶员模拟考试">
        <meta name="description" content="嘻哈学车提供2016最新科目一考试和科目四模拟考试，采用公安部2016最先驾校模拟考试，考驾照模拟试题2016，驾校一点通模拟考试c1，驾驶员考试科目一，考驾照、做驾驶员模拟考试试题就来嘻哈学车！">
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/mui.min.css" />
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/style.css?=<?php echo $r; ?>" />
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/font/iconfont/iconfont.css" />
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/swiper.min.css" />
        <link rel="stylesheet" href="<?php echo $root_path; ?>/assets/css/exam/exercise.css?=<?php echo $r; ?>" />
        <script src="<?php echo $root_path; ?>/assets/js/mui.min.js"></script>
        <script src="<?php echo $root_path; ?>/assets/js/exam/functions.js?=<?php echo $r; ?>"></script>
        <script>
            var root_path = "<?php echo $root_path; ?>";
            var host = "<?php echo HOST; ?>";
            var ctype = "<?php echo $ctype; ?>";
            var stype = "<?php echo $stype; ?>";
            var t = "<?php echo $t; ?>";
            var sid = "<?php echo $sid; ?>";
            var os = "<?php echo $os; ?>";
            var uid = 0;
            if(os == 'web') {
//              var loginauth = localStorage.getItem('loginauth');
                var loginauth = sessionStorage.getItem('loginauth');
                if(!loginauth) {
                    location.href = root_path+"exam/u/r="+sid+','+ctype+','+stype+','+t+',1,'+os;
                }
        	    uid = JSON.parse(loginauth).l_user_id;     
            } else {
                location.href = root_path+"exam/index-"+sid+"-web.html";
            }
        </script>
        <style type="text/css">
            .xuanze_right {background:#18B4ED; border-color:#18B4ED;}
            .xuanze_wrong {background:#F25E5E; border-color:#F25E5E;}
            .xuanze_choose {background:#666; border-color:#666;}
            .clock {background: url('./../assets/images/clock.png'); background-size: 80px; width: 80px; height: 49px;position: absolute; z-index: 9999; right:6px; bottom: 65px; padding-top:27px; color: #1dacf9;}
        </style>
    </head>
    
    <body style="background: #f5f5f5;">
        <?php if($os == 'web') { ?>
        <header class="mui-bar mui-bar-nav">
            <a class="mui-action-back mui-icon mui-icon-left iconfont" style="color: #00BD9C; font-size:1.3rem;">&#xe608;</a>
            <h1 class="mui-title" id="title">模拟考试</h1>
            <span class="mui-btn mui-btn-link mui-pull-right">
                  <i class="iconfont">&#xe62e;</i> 
                  <span id="exam_times"></span>
              </span>
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
            <div id="time_button" class="nav-tab-item" style="background: none; color: #18b4ed;">
                <span class="mui-icon iconfont">&#xe63f;</span>
                <span class="mui-tab-label">暂停</span>
            </div>
            <div id="handover" class="button-prev nav-tab-item" style="display:table-cell; background: none; color: #5c5c5c;">
                <span class="mui-icon iconfont" style="font-size: 1.1rem;">&#xe62a;</span>
                <span class="mui-tab-label">交卷</span>
            </div>
            <div class="button-next nav-tab-item" style="background: none; color: #5c5c5c;">
                <button id="next" class="mui-btn" style="background: none; color: #5c5c5c;">
                    <span class="mui-icon iconfont" style="font-size:28px;">&#xe634;</span>
                    <span class="mui-tab-label">下一题</span>
                </button>
                <!--<span class="mui-icon iconfont" style="font-size: 1.8rem;">&#xe634;</span>
                <span class="mui-tab-label">下一题</span>-->
            </div>
            <div id="questions_no_list" class="nav-tab-item" style="background: none; color: #5c5c5c;">
                <span class="mui-icon iconfont">&#xe621;</span>
                <span class="mui-tab-label"><span id="qid">0</span>/<span id="qtotal">0</span></span>
                <?php if($os == 'ios' || $os == 'an') { ?>
                <div id="exam_times" class="clock" style=""></div>
                <?php } ?>
            </div>
        </nav>
        
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
                                {@each item.chosen as value, key}
                                    {@if value.choose_id == 1}
                                        {@if value.is_true == 1}
                                            <li class="mui-table-view-cell mui-single" id="" title="1" style="color: #F25E5E;" disabled>
                                                <span class="iconfont" style="font-size: 2.1rem;">&#xe63a;</span> 正确
                                            </li>
                                        {@else if value.is_true == 2}
                                            <li class="mui-table-view-cell mui-single" id="" title="1" style="color: #18B4ED;" disabled>
                                                <span class="iconfont" style="font-size: 2.1rem;">&#xe639;</span> 正确
                                            </li>
                                        {@else}
                                        	<li class="mui-table-view-cell mui-single" id="" title="1" style="color: #555;">
			                                    <span class="xuanze_no">A</span> 正确
			                                </li>
                                        {@/if}
                                    {@else if value.choose_id == 2}
                                        {@if value.is_true == 1}
                                            <li class="mui-table-view-cell mui-single" id="" title="2" style="color: #F25E5E;">
                                                <span class="iconfont" style="font-size: 2.1rem;">&#xe63a;</span> 错误
                                            </li>
                                        {@else if value.is_true == 2}
                                            <li class="mui-table-view-cell mui-single" id="" title="2" style="color: #18B4ED;">
                                                <span class="iconfont" style="font-size: 2.1rem;">&#xe639;</span> 错误
                                            </li>
                                        {@else}
	                                        <li class="mui-table-view-cell mui-single" id="" title="2" style="color: #555;">
			                                    <span class="xuanze_no">B</span> 错误
			                                </li>
                                        {@/if}
                                    {@else}
	                                	<li class="mui-table-view-cell mui-single" id="" title="1" style="color: #555;">
		                                    <span class="xuanze_no">A</span> 正确
		                                </li>
	                                	<li class="mui-table-view-cell mui-single" id="" title="2" style="color: #555;">
		                                    <span class="xuanze_no">B</span> 错误
		                                </li>
                                    {@/if}
                                {@/each}
                            {@else}
                                {@each item.chosen as value, key}
                                    {@if value.choose_id == 1}
                                    	{@if value.is_true == 1}
	                                        <li class="mui-table-view-cell mui-single" id="" title="1" style="color: #F25E5E;">
	                                            <span class="iconfont" style="font-size: 2.1rem;">&#xe63a;</span> <span style="line-height: 2rem;">${item.an1}</span>
	                                        </li>
                                        {@else if value.is_true == 2}
	                                        <li class="mui-table-view-cell mui-single" id="" title="1" style="color: #18B4ED;">
	                                            <span class="iconfont" style="font-size: 2.1rem;">&#xe639;</span> <span style="line-height: 2rem;">${item.an1}</span>
	                                        </li>
                                        {@else}
	                                        <li class="mui-table-view-cell mui-single" id="" title="1" style="color: #555;">
	                                            <span class="xuanze_no">A</span> <span style="line-height: 2rem;">${item.an1}</span>
	                                        </li>
                                        {@/if}
                                   	{@else if value.choose_id == 2}
                                        {@if value.is_true == 1}
	                                        <li class="mui-table-view-cell mui-single" id="" title="2" style="color: #F25E5E;">
	                                            <span class="iconfont" style="font-size: 2.1rem;">&#xe63a;</span> <span style="line-height: 2rem;">${item.an2}</span>
	                                        </li>
                                        {@else if value.is_true == 2}
	                                        <li class="mui-table-view-cell mui-single" id="" title="2" style="color: #18B4ED;">
	                                            <span class="iconfont" style="font-size: 2.1rem;">&#xe639;</span> <span style="line-height: 2rem;">${item.an2}</span>
	                                        </li>
                                        {@else}
	                                        <li class="mui-table-view-cell mui-single" id="" title="2" style="color: #555;">
	                                            <span class="xuanze_no">B</span> <span style="line-height: 2rem;">${item.an2}</span>
	                                        </li>
                                        {@/if}
                                   	{@else if value.choose_id == 3}
                                   		{@if value.is_true == 1}
	                                        <li class="mui-table-view-cell mui-single" id="" title="3" style="color: #F25E5E;">
	                                            <span class="iconfont" style="font-size: 2.1rem;">&#xe63a;</span> <span style="line-height: 2rem;">${item.an3}</span>
	                                        </li>
                                        {@else if value.is_true == 2}
	                                        <li class="mui-table-view-cell mui-single" id="" title="3" style="color: #18B4ED;">
	                                            <span class="iconfont" style="font-size: 2.1rem;">&#xe639;</span> <span style="line-height: 2rem;">${item.an3}</span>
	                                        </li>
                                        {@else}
	                                        <li class="mui-table-view-cell mui-single" id="" title="3" style="color: #555;">
	                                            <span class="xuanze_no">C</span> <span style="line-height: 2rem;">${item.an3}</span>
	                                        </li>
                                        {@/if}
                                   	{@else if value.choose_id == 4}
                                   		{@if value.is_true == 1}
	                                        <li class="mui-table-view-cell mui-single" id="" title="4" style="color: #F25E5E;">
	                                            <span class="iconfont" style="font-size: 2.1rem;">&#xe63a;</span> <span style="line-height: 2rem;">${item.an4}</span>
	                                        </li>
                                        {@else if value.is_true == 2}
	                                        <li class="mui-table-view-cell mui-single" id="" title="4" style="color: #18B4ED;">
	                                            <span class="iconfont" style="font-size: 2.1rem;">&#xe639;</span> <span style="line-height: 2rem;">${item.an4}</span>
	                                        </li>
                                        {@else}
	                                        <li class="mui-table-view-cell mui-single" id="" title="4" style="color: #555;">
	                                            <span class="xuanze_no">D</span> <span style="line-height: 2rem;">${item.an4}</span>
	                                        </li>
                                        {@/if}
                                    {@else}
                                    	<li class="mui-table-view-cell mui-single" id="" title="1" style="color: #555;">
                                            <span class="xuanze_no">A</span> <span style="line-height: 2rem;">${item.an1}</span>
                                        </li>
                                        <li class="mui-table-view-cell mui-single" id="" title="2" style="color: #555;">
                                            <span class="xuanze_no">B</span> <span style="line-height: 2rem;">${item.an2}</span>
                                        </li>
                                        <li class="mui-table-view-cell mui-single" id="" title="3" style="color: #555;">
                                            <span class="xuanze_no">C</span> <span style="line-height: 2rem;">${item.an3}</span>
                                        </li>
                                        <li class="mui-table-view-cell mui-single" id="" title="4" style="color: #555;">
                                            <span class="xuanze_no">D</span> <span style="line-height: 2rem;">${item.an4}</span>
                                        </li>
                                    {@/if}
                                {@/each}
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
        <script src="<?php echo $root_path; ?>/assets/js/cookie.min.js"></script>
        <script src="<?php echo $root_path; ?>/assets/js/exam/test.js?=<?php echo $r; ?>"></script>
    </body>
</html>
