<link rel="stylesheet" href="<?php echo base_url('assets/css/question.min.css');?>" />
<div id="app" class="mui-fullscreen" v-cloak style=" background: #fff;">
     <div v-show="seen" @click="hideShade" class="hide-shade" style=""></div> 
    <header class="mui-bar mui-bar-nav">
        <a href="javascript:back(-1)" class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left"></a>
        <h1 class="mui-title">{{title}}</h1>
	    <button class="mui-btn mui-btn-blue mui-btn-link mui-pull-right" v-if="from == 1" @tap="handleSettings()">设置</button>
	    <button class="mui-btn mui-btn-blue mui-btn-link mui-pull-right" v-else-if="from == 2" @tap="handleTestTime()">暂停</button>
    </header>
    <div class="mui-content mui-fullscreen" style="background: #fff; overflow-y: scroll; margin-bottom: 110px;">
    	<div class="xiha-content">
    		<p style="line-height: 30px;">
    			<span class="xiha-ques-tag1" v-if="currentQuesInfo.option_type == 1">单选</span>
    			<span class="xiha-ques-tag2" v-else-if="currentQuesInfo.option_type == 2">多选</span>
    			<span class="xiha-ques-tag3" v-else-if="currentQuesInfo.option_type == 0">判断题</span>
    			<span style="font-size: 16px; color: #333;">{{currentQuesInfo.question}}</span>
    		</p>
        	<img v-if="currentQuesInfo.media_type == 1" style="width: 100%; height: 200px;" :src="currentQuesInfo.media_content" alt="" />
        	<video v-else-if="currentQuesInfo.media_type == 2" 
        		id="player" 
        		autobuffer 
        		style="width:100%; height: 200px;" 
				controls
        		autoplay=true 
        		preload="auto" 
        		:src="currentQuesInfo.media_content"
        		x-webkit-airplay="true" 
        		x5-video-player-type="h5" 
        		x5-video-player-fullscreen="true" 
        		webkit-playsinline="true" 
        		playsinline="true">
            </video>
    		<ul class="mui-table-view xiha-options">
    			<!--options-->
			    <li class="mui-table-view-cell mui-media" v-for="(item, index) in currentQuesInfo.options">
			    	<template v-if="currentQuesInfo.isChoose">
				        <a href="javascript:;" v-if="currentQuesInfo.option_type != 2">
				        	<template v-if="currentQuesInfo.chooseansSeen">
				        		<template v-if="item.tag == currentQuesInfo.chooseAns">
				        			<div class="mui-media-object mui-pull-left answer-cell iconfont right-cell" v-if="currentQuesInfo.chooseAns == currentQuesInfo.restore_answer">&#xe667;</div>
				        			<div class="mui-media-object mui-pull-left answer-cell iconfont wrong-cell" v-else>&#xe602;</div>
						            <div class="mui-media-body answer-content" v-if="currentQuesInfo.chooseAns == currentQuesInfo.restore_answer" style="color: #6EBBFF;">
					                	{{item.content}}
						            </div>
						            <div class="mui-media-body answer-content" v-else style="color: #E60A1B;">
					                	{{item.content}}
						            </div>
				        		</template>
				        		<template v-else>
				        			<div class="mui-media-object mui-pull-left answer-cell iconfont right-cell" v-if="item.tag == currentQuesInfo.restore_answer">&#xe667;</div>
				        			<div class="mui-media-object mui-pull-left answer-cell" v-else>{{item.tag}}</div>
						            <div class="mui-media-body answer-content" v-if="item.tag == currentQuesInfo.restore_answer" style="color: #6EBBFF;">
					                	{{item.content}}
						            </div>
						            <div class="mui-media-body answer-content" v-else>
					                	{{item.content}}
						            </div>
				        		</template>
				        	</template>
				        	<template v-else>
					        	<div class="mui-media-object mui-pull-left answer-cell" v-if="item.tag == currentQuesInfo.restore_answer">{{item.tag}}</div>
					        	<div class="mui-media-object mui-pull-left answer-cell" v-else>{{item.tag}}</div>
					            <div class="mui-media-body" style="text-align: left; padding-top: 3px;">
				                	{{item.content}}
					            </div>
				        	</template>
				        </a>
				        <a href="javascript:;" v-else>
				        	<template v-if="currentQuesInfo.answerList.indexOf(item.tag) >= 0">
				        		<template v-if="currentQuesInfo.chooseAns.split('').indexOf(item.tag) < 0">
				        			<div class="mui-media-object mui-pull-left answer-cell choose-cell">{{item.tag}}</div>
						            <div class="mui-media-body" style="text-align: left; padding-top: 3px; color: #6EBBFF;">
					                	{{item.content}}
						            </div>
				        		</template>
				        		<template v-else>
						        	<div class="mui-media-object mui-pull-left answer-cell iconfont right-cell">&#xe667;</div>
						            <div class="mui-media-body" style="text-align: left; padding-top: 3px; color: #6EBBFF;">
					                	{{item.content}}
						            </div>
				        		</template>
				        	</template>
				        	<template v-else>
				        		<template v-if="currentQuesInfo.chooseAns.split('').indexOf(item.tag) < 0">
				        			<div class="mui-media-object mui-pull-left answer-cell">{{item.tag}}</div>
						            <div class="mui-media-body" style="text-align: left; padding-top: 3px;">
					                	{{item.content}}
						            </div>
				        		</template>
				        		<template v-else>
				        			<div class="mui-media-object mui-pull-left answer-cell iconfont wrong-cell">&#xe602;</div>
						            <div class="mui-media-body" style="text-align: left; padding-top: 3px; color: #E60A1B;">
					                	{{item.content}}
						            </div>
				        		</template>
				        	</template>
				        </a>
			    	</template>
			    	<template v-else>
			    		<a href="javascript:;" v-if="currentQuesInfo.option_type != 2" @tap="chooseQuestion(item.tag)">
				        	<div class="mui-media-object mui-pull-left answer-cell">{{item.tag}}</div>
				            <div class="mui-media-body" style="text-align: left; padding-top: 3px;">
			                	{{item.content}}
				            </div>
				        </a>
				        <a href="javascript:;" v-else @tap="chooseMultiQuestion(index, item.tag)">
				        	<div v-if="item.isChoose" class="mui-media-object mui-pull-left answer-cell choose-cell">{{item.tag}}</div>
				        	<div v-else class="mui-media-object mui-pull-left answer-cell">{{item.tag}}</div>
				            <div class="mui-media-body" style="text-align: left; padding-top: 3px;">
			                	{{item.content}}
				            </div>
				        </a>
			    	</template>
			    </li>
			    <button v-if="buttonSeen && currentQuesInfo.option_type == 2" class="xiha-button" @tap="submitMultiQuestion()" style="width: 150px;">确定选择</button>
		    </ul>
    	</div>
    	<div class="xiha-content" v-if="ansSeen">
	    	<p style="text-align: center; font-size: 18px; border-top: 1px solid #EFEFEF; position: relative;">
	    		<span style="color: #6DBBFF; background: #fff; position: absolute; left: 50%; margin-left: -40px; top: -11px; display: block; height: 20px; width: 80px;">试题解析</span>
			</p>
			<p style="margin-top: 20px;">
				<span style="float: left; font-size: 16px; color: #333;">答案 {{currentQuesInfo.restore_answer}}</span>
				<span style="float: right;">
					<span style="font-size: 16px; color: #999;">难度</span> 
					<span v-for="n in 5" >
						<i v-if="n <= parseInt(currentQuesInfo.difficulty)" class="iconfont" style="color: #FFCC01;">&#xe70e;</i>
						<i v-else class="iconfont" style="color: #cdcdcd;">&#xe70e;</i>
					</span>
				</span>
			</p>
			<div class="clearfix"></div>
			<p style="font-size: 16px; margin-top: 10px; color: #333;" v-html="currentQuesInfo.explain"></p>
    	</div>
		
		<div class="xiha-content" v-if="commSeen">
	    	<p style="text-align: center; font-size: 18px; border-top: 1px solid #EFEFEF; position: relative;">
	    		<span style="color: #6DBBFF; background: #fff; position: absolute; left: 50%; margin-left: -40px; top: -11px; display: block; height: 20px; width: 80px;">试题点评</span>
			</p>
			
			<ul class="mui-table-view xiha-comment-table-view" v-if="commentList.length > 0">
			    <li class="mui-table-view-cell mui-media" style="" v-for="(item, index) in commentList">
			        <a style="white-space: initial;">
			            <img class="mui-media-object mui-pull-left" style="border-radius: 50%;" src="<?php echo base_url('assets/images/demo_bg.png'); ?>">
			            <div class="mui-media-body" style="text-align: left; line-height: 25px;">
			            	<span v-if="item.user_name">{{item.user_name}}</span>
			            	<span v-else>匿名</span>
		                	<div class='mui-ellipsis' style="color: #666; font-size: 16px; white-space: initial;">{{item.content}}</div>
			            </div>
			            <div class="mui-badge" style="background: none; color: #666; font-size: 16px;">
			            	<i class="iconfont" style="font-size: 1.2rem; color: #999;">&#xe600;</i> {{item.votes}}
			            	<!--<i class="iconfont" style="font-size: 1.2rem; color: #d81e06;">&#xe600;</i> {{item.votes}}-->
		            	</div>
			        </a>
			    </li>
		    </ul>
		    <p v-else style="height: 50px; text-align: center; margin-top: 50px;">
		    	暂无评价，点击抢沙发<br /><br />
		    	<button class="xiha-button" style="width: 130px; height: 38px;" @tap="submitComment()">快来点评吧</button>
	    	</p>
		    
    	</div>
    </div>
    <!--底部 -->
    <div class="xiha-footer">
    	<div class="xiha-nextprev">
    		<ul class="mui-table-view mui-row">
			    <li class="mui-table-view-cell mui-media mui-col-sm-6 mui-col-xs-6" style="border-bottom: 1px solid #EFEFEF;">
			        <a href="javascript:;" v-if="nextprevSeen" @tap="prevQuestion()">
			            <div class="mui-media-body" style="color: #05BFBA;">
		                	上一页
			            </div>
			        </a>
			        <a href="javascript:;" v-else>
			            <div class="mui-media-body" style="color: #05BFBA;">
		                	上一页
			            </div>
			        </a>
			    </li>
			    <li class="mui-table-view-cell mui-media mui-col-sm-6 mui-col-xs-6" style="border-bottom: 1px solid #EFEFEF;  border-left: 1px solid #EFEFEF;">
			        <a href="javascript:;" v-if="nextprevSeen" @tap="nextQuestion()">
			            <div class="mui-media-body" style="color: #55ABFF;">
		                	下一页
			            </div>
			        </a>
			        <a href="javascript:;" v-else>
			            <div class="mui-media-body" style="color: #55ABFF;">
		                	下一页
			            </div>
			        </a>
			    </li>
		    </ul>
    	</div>
    	<nav class="mui-bar mui-bar-tab">
			<div class="nav-bar">
				<div class="mui-tab-item" >
		        	<span class="iconfont" style="font-size: 16px; color: #F7AD7F;">&#xe72e;</span>
					<span class="mui-tab-label twelve_font">{{rigids.length}}</span>
				</div>
				<div class="mui-tab-item" >
		        	<span class="iconfont" style="font-size: 16px; color: #d81e06;">&#xe749;</span>
					<span class="mui-tab-label twelve_font">{{errids.length}}</span>
				</div>
				<div class="mui-tab-item" @tap="showQuesNo()">
		        	<span class="iconfont" style="font-size: 15px; color: #666;">&#xe63d;</span>
					<span class="mui-tab-label twelve_font"><span style="font-size: 14px;"><span style="color: #007AFF;">{{current_count}}</span>/{{count}}</span></span>
				</div>
				<div class="mui-tab-item" @click="handleCollect(currentQuesInfo.id)">
		        	<!--<span class="iconfont" style="font-size: 16px; color: #d81e06;">&#xe601;</span>-->
		        	<span class="iconfont" style="font-size: 16px; color: #999;">&#xe601;</span>
					<span class="mui-tab-label twelve_font">收藏</span>
				</div>
			</div>
		</nav>
		<!--评价-->
		<div style="position: fixed; right: 10px; bottom: 130px;">
			<img src="<?php echo base_url('assets/images/comment@2x.png'); ?>" style="width: 50px;" alt="" />
		</div>
    </div>
    <!--选择题号-->
	<div id="quesno-list" class="quesno-list">
		<ul class="mui-table-view mui-row" style="position: fixed; height: 44px; width: 100%; border-bottom: 1px solid #EFEFEF; background: #fff;">
		  	<li class="mui-table-view-cell mui-media mui-col-sm-3 mui-col-xs-3" style="padding: 11px 0px;">
		        <a href="javascript:;">
		            <div class="mui-media-body" style="">
		            	<span class="iconfont" style="font-size: 16px; color: #F7AD7F;">&#xe72e;</span>
						<span class="mui-tab-label twelve_font">{{rigids.length}}</span>
		            </div>
		        </a>
		    </li>
		  	<li class="mui-table-view-cell mui-media mui-col-sm-3 mui-col-xs-3" style="padding: 11px 0px;">
		        <a href="javascript:;">
		            <div class="mui-media-body" style="">
		            	<span class="iconfont" style="font-size: 16px; color: #d81e06;">&#xe749;</span>
						<span class="mui-tab-label twelve_font">{{errids.length}}</span>
		            </div>
		        </a>
		    </li>
		    <li class="mui-table-view-cell mui-media mui-col-sm-3 mui-col-xs-3" style="padding: 11px 0px;">
		        <a href="javascript:;">
		            <div class="mui-media-body" style="">
		        		<span class="iconfont" style="font-size: 15px; color: #666;">&#xe63d;</span>
						<span class="mui-tab-label twelve_font"><span style="font-size: 14px;"><span style="color: #007AFF;">{{current_count}}</span>/{{count}}</span></span>
		            </div>
		        </a>
		    </li>
		    <li class="mui-table-view-cell mui-media mui-col-sm-3 mui-col-xs-3" style="padding: 11px 0px;">
		        <a href="javascript:;" @tap="delAllRecord()">
		            <div class="mui-media-body" style="color: #999;">
						<span class="mui-tab-label twelve_font">清空记录</span>
		            </div>
		        </a>
		    </li>
	    </ul>
	    <!--no_list-->
	    <div class="mui-row xiha-quesno-row" style="margin-top: 44px; position: fixed; overflow-y: scroll; height: 310px; width: 100%;">
	        <div class="mui-col-sm-2 mui-col-xs-2" v-for="(item, index) in qidsnoList" @tap="showQuesInfo(index, item.id)">
	            <li class="mui-table-view-cell">
	            	<template v-if="item.isChoose">
	            		<template v-if="item.isActive">
	            			<div class="xiha-circle xiha-circle-right xiha-circle-active" v-if="item.isRight">{{index+1}}</div>
	            			<div class="xiha-circle xiha-circle-wrong xiha-circle-active" v-else>{{index+1}}</div>
	            		</template>
	            		<template v-else>
	            			<div class="xiha-circle xiha-circle-right" v-if="item.isRight">{{index+1}}</div>
	            			<div class="xiha-circle xiha-circle-wrong" v-else>{{index+1}}</div>
	            		</template>
	            	</template>
	            	<template v-else>
	            		<div class="xiha-circle xiha-circle-active" v-if="item.isActive">{{index+1}}</div>
	            		<div class="xiha-circle" v-else>{{index+1}}</div>
	            	</template>
	            </li>
	        </div>
	    </div>
    </div>
	<div id="loading" style=""></div>
</div>
<script type="text/javascript">
    var from = "<?php echo $from; ?>";
    var title = "<?php echo $title; ?>";
	var currentSubjectid = "<?php echo $subject_type;?>";
    var currentLicenseid = "<?php echo $license_type;?>";
	var quesids_url = "<?php echo $this->config->item('api_url').'student/exam/questionIds'; ?>";
    var queslist_url = "<?php echo $this->config->item('api_url').'student/exam/questions'; ?>";
    var colle_url = "<?php echo $this->config->item('api_url').'student/exam/collection'; ?>";
    var comment_url = "<?php echo $this->config->item('api_url').'student/exam/commentslist'; ?>"; 	
</script>
<script src="<?php echo base_url('assets/js/question.min.js'); ?>" type="text/javascript" charset="utf-8"></script>
