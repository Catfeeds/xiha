<?php
	require_once '../include/config.php';
	// require_once '../include/crypt.php';
  
	// define('SECRET', 'xhxueche');
	// $crypt = new Xcrypt(SECRET, 'cbc', 'off');
	// if(isset($_SESSION['loginauth'])) {
	// 	$loginauth = $crypt->decrypt($_SESSION['loginauth']);
	// 	$loginauth_arr = explode('\t', $loginauth);
	// 	$uid = $loginauth_arr[0];
	// } else {
	// 	header('location:login.php');
	// 	exit();
	// }
	
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

    <script>
    	var _hmt = _hmt || [];
    	(function() {
    	  var hm = document.createElement("script");
    	  hm.src = "//hm.baidu.com/hm.js?ec52a986344c1bab363e1ae39c0dd626";
    	  var s = document.getElementsByTagName("script")[0]; 
    	  s.parentNode.insertBefore(hm, s);
    	})();
    </script>
    
  </head>
  
  <body style="background: #f5f5f5;">
    <header class="mui-bar mui-bar-nav">
      <a class="mui-action-back mui-icon mui-icon-left iconfont" style="color: #00BD9C; font-size:1.3rem;">&#xe608;</a>
      <h1 class="mui-title" id="">模拟考试</h1>
      <span class="mui-btn mui-btn-link mui-pull-right">
      <i class="iconfont">&#xe62e;</i> 
      <span id="exam_times"></span>
      </span>
    </header>
    <div class="mui-content" id="question" style="background: #f5f5f5;">
      <div id="question" >
				<div class="mui-loading" style="background: #f5f5f5; padding-top:10px;">
					<div class="mui-spinner">
					</div>
					<p style="text-align: center; margin-top:5px;">正在加载中</p>
				</div>
			</div>
    </div>
    
    <div style="text-align: center; margin-top:20px; margin-bottom: 20px;">
      <!--<button class="mui-btn" id="prev"><span class="iconfont">&#xe60e;</span> 上一题</button>-->
      <button id="handover" class="mui-btn mui-btn-success"> 交卷 <span class="iconfont">&#xe62a;</span></button>
      <button id="start_exam" class="mui-btn mui-btn-warning"> 暂停 <span class="iconfont">&#xe628;</span></button>
      <button class="mui-btn" id="next">下一题 <span class="iconfont">&#xe60f;</span></button>
    </div>
    <script src="../assets/js/mui.min.js"></script>
    <script src="../assets/js/cookie.min.js"></script>
    <script src="../assets/js/template.js"></script>
    <script src="../assets/js/layer/layer.js"></script>
    <script type="text/html" id="question_tmpl">
      <ul class="mui-table-view" style="margin: 0px auto;">
    		<li class="mui-table-view-cell" id="mui-li-click" title="" style="color: #333333;">
					<p style="font-size:1rem; color: #555; line-height: 1.5em;">
						<span style="color: #888888;">
							(<em style="color: #CF2D28;">{{ page }}</em>/{{ qtotal }}) 
							{{if type == 1}}
							<strong style="color:red;">(判)</strong>
							{{else if type == 2}}
							<strong style="color:red;">(单)</strong>
							{{else}}
							<strong style="color:red;">(多)</strong>
							{{/if}}
						</span> 
						{{question}}
					</p>
					{{if mediatype == 1}}
					  <div style="width: 100%;height:50%;margin:0 auto;line-height:50%;text-align:center;">
						<img src="{{ imageurl }}" style="width: 80%;height:20%;margin:0 auto;text-align:center;" alt="" />
					  </div>
					{{else if mediatype == 2}}
						<div class="flowplayer">
							<video id="player" autobuffer style="width:100%; height: 200px;" controls autoplay preload="auto" webkit-playsinline>
							  <source src="{{ imageurl }}" type="video/mp4">
							  <object data="{{ imageurl }}" style="width:100%">
							    <embed src="{{ imageurl }}" style="width:100%">
							  </object> 
							</video>
						</div>					

					{{/if}}
				</li>
			</ul>
			<ul class="mui-table-view mui-ul-click" style="">
				{{if type == 1}}
					<li class="mui-table-view-cell mui-single" id="" title="1" style="color: #555;">
	        			A.正确
					</li>
					<li class="mui-table-view-cell mui-single" id="" title="2" style="color: #555;">
	        			B.错误
					</li>
				{{else if type == 2}}
	        		<li class="mui-table-view-cell mui-single" id="" title="1" style="color: #555;">
	        			A.{{an1}}
					</li>
					<li class="mui-table-view-cell mui-single" id="" title="2" style="color: #555;">
	        			B.{{an2}}
					</li>
					<li class="mui-table-view-cell mui-single" id="" title="3" style="color: #555;">
	        			C.{{an3}}
					</li>
					<li class="mui-table-view-cell mui-single" id="" title="4" style="color: #555;">
	        			D.{{an4}}
					</li>
				{{else}}
					<li class="mui-table-view-cell mui-multi" id="" title="1" style="color: #555;">
	        			A.{{an1}}
					</li>
					<li class="mui-table-view-cell mui-multi" id="" title="2" style="color: #555;">
	        			B.{{an2}}
					</li>
					<li class="mui-table-view-cell mui-multi" id="" title="3" style="color: #555;">
	        			C.{{an3}}
					</li>
					<li class="mui-table-view-cell mui-multi" id="" title="4" style="color: #555;">
	        			D.{{an4}}
					</li>
				{{/if}}
			</ul>
      
    </script>
    
    <script>
			

      (function($, doc) {
        if( !Cookies.get('ctype') || !Cookies.get('ltype')) {
          location.href="index.php?id=<?php echo $sid; ?>";
          return false;
        }
        var loginauth = localStorage.getItem('loginauth');
        if(!loginauth) {
        	location.href = 'login.php';
        	return false;
        }
        localStorage.setItem('terrq', '');
				if ( !localStorage.getItem('practicelist') ) {
	        //初始化
	        var init = [
	        	['C1', 1, 1, 1],
	        	['C1', 4, 1, 1],
	        	['A1', 1, 1, 1],
	        	['A1', 4, 1, 1],
	        	['A2', 1, 1, 1],
	        	['A2', 4, 1, 1],
	        	['D', 1, 1, 1],
	        	['D', 4, 1, 1]
	        ];
	        localStorage.setItem('practicelist', JSON.stringify(init));
        }
        
        //模拟考试100
        var practicelist = localStorage.getItem('practicelist');
        var ctype = Cookies.get('ctype');
        var ltype = Cookies.get('ltype');
        var _res_ = setOnePracticePage(practicelist, ctype, ltype, 1);
				localStorage.setItem('practicelist', JSON.stringify(_res_));
        practicelist = localStorage.getItem('practicelist');
        var _type = setOnePracticeType(practicelist, ctype, ltype, 5);
				localStorage.setItem('practicelist', JSON.stringify(_type));
        practicelist = localStorage.getItem('practicelist');
				localStorage.setItem('score', 0);
        var cur_practicelist = getOnePracticeList(practicelist, ctype, ltype);
        if(!cur_practicelist) {
        	location.href = "index.php";
        	return false;
        }
        var qid = cur_practicelist[2]; //从第1题开始
        getPractices(qid);
        //开始计时
        startTimer();

				var next = doc.getElementById('next');
				var questions = JSON.parse(localStorage.getItem('questions'));
				localStorage.setItem('canswer', '');

				var c = 1;
				//下一页
				var next_fun = function(e) {
    				var _practicelist = localStorage.getItem('practicelist');
    				var questions = JSON.parse(localStorage.getItem('questions'));
					var current_practice_list = getOnePracticeList(_practicelist, ctype, ltype);
    				var page = current_practice_list[2];
    				var qtotal = localStorage.getItem('qtotal');
					var canswer = localStorage.getItem('canswer');
					var _p = parseInt(page) + 1;
					var answertrue = questions[_p - 2]['answertrue'];
					if(_p > qtotal) {
						if(answertrue == canswer) {
							if(ltype == 4) {
								localStorage.setItem('score', parseInt(localStorage.getItem('score')) + 2);
							} else {
								localStorage.setItem('score', parseInt(localStorage.getItem('score')) + 1);
							}	
						} else {
							var terrq = !localStorage.getItem('terrq') ? ',' : localStorage.getItem('terrq');
							if(terrq.indexOf(questions[qtotal - 1].id) <= 0) {
								localStorage.setItem('terrq', terrq + questions[qtotal - 1].id + ',');
							}
						}
							
						localStorage.setItem('canswer', '');
						return false;
					}

					questions[_p - 1]['page'] = _p;
					questions[_p - 1]['qtotal'] = qtotal;
					if(canswer == answertrue) {
						if(ltype == 4) {
							localStorage.setItem('score', parseInt(localStorage.getItem('score')) + 2);
						} else {
							localStorage.setItem('score', parseInt(localStorage.getItem('score')) + 1);
						}
					} else {
						var terrq = !localStorage.getItem('terrq') ? ',' : localStorage.getItem('terrq');
						if(terrq.indexOf(questions[_p - 2].id) <= 0) {
							localStorage.setItem('terrq', terrq + questions[_p - 2].id + ',');
						}
					}
					var html = template('question_tmpl', questions[_p - 1]);
					doc.getElementById('question').innerHTML = html;
					if(questions[_p - 1]['mediatype'] == 2) { 
						var video = doc.getElementById('player');
						video.addEventListener('click', function() {
							video.play();
						}, false);
					}
					var _res = setOnePracticePage(_practicelist, ctype, ltype, _p);
					localStorage.setItem('practicelist', JSON.stringify(_res));
					c = 1;
					localStorage.setItem('canswer', '');
				}
				next.addEventListener('tap', next_fun, false);

				// 选择
				$('#question').on('tap', '.mui-ul-click .mui-table-view-cell', function() {
					var questions = JSON.parse(localStorage.getItem('questions'));
					var _practicelist = localStorage.getItem('practicelist');
					var current_practice_list = getOnePracticeList(_practicelist, ctype, ltype);
        			var page = current_practice_list[2];
					var _p = parseInt(page);
					var _question = questions[_p - 1];
					var title = this.getAttribute('title');
					var ans = _question.answertrue;
					var type = _question.type;
					if(type == 1 || type == 2) {
						if(this.style.background == 'rgb(223, 240, 216)') {
							this.style.background = '#fff';
							this.style.color = '#555';
						}
						cancelSiblings(sibling(this));
					}

					if(this.style.background == 'rgb(223, 240, 216)') {
						this.style.background = '#fff';
						this.style.color = '#555';
						var canswer = localStorage.getItem('canswer');
						var answer_split = canswer.split("");
						var res = answer_split.splice(answer_split.indexOf(title), 1);
						var answer = answer_split.sort().toString();
						localStorage.setItem('canswer', answer.replaceAll(',', ''));
					
					} else {
						this.style.background = '#dff0d8';
						this.style.color = '#3c763d';
						if(type == 1 || type == 2) {
							localStorage.setItem('canswer', this.getAttribute('title'));
						} else {
							var canswer = localStorage.getItem('canswer')+this.getAttribute('title');
							var answer_split = canswer.split("");
							var answer = answer_split.sort().toString();
							localStorage.setItem('canswer', answer.replaceAll(',', ''));
						}	
					}

				})
				//timer(intDiff);
        var is_start = 1;
        //开始/暂停按纽
        var start_exam = doc.getElementById('start_exam');
        //存放时间倒计时的地方
        var exam_times = doc.getElementById('exam_times');
        start_exam.addEventListener('tap', function(e) {
            //is_start 标记当前的开始与否的状态
            // 1 表示成功启动
            if ( is_start == 1 ) 
            {
                is_start = 0;
            }
            else
            {
                is_start = 1;
            }

            if ( is_start)
            {
                start_exam.innerHTML = " 暂停 <span class='iconfont'>&#xe628;</span>";
                next.addEventListener('tap', next_fun, false);
                next.removeAttribute('disabled');
                startTimer();
            }
            else
            {
                start_exam.innerHTML = " 开始 <span class='iconfont'>&#xe629;</span>";
                next.removeEventListener('tap', next_fun, false);
                next.setAttribute('disabled', true);
                clearTimeout(i);
            }
        })

				//交卷
				var handover = doc.getElementById('handover');
				handover.addEventListener('tap', function(e) {
					// window.clearTimeout('timer('+intDiff+')');

					var practicelist = localStorage.getItem('practicelist');
			        var cur_practicelist = getOnePracticeList(practicelist, ctype, ltype);
			        var qid = cur_practicelist[2];
			        start_exam.innerHTML = " 开始 <span class='iconfont'>&#xe629;</span>";
	                next.addEventListener('tap', next_fun, false);
	                next.setAttribute('disabled', true);
                    clearTimeout(i);
					layer.open({
				    content: '您还有'+(100-parseInt(qid))+'道题没做，确定继续吗？',
				    btn: ['继续','交卷'],
				    shadeClose: false,
				    yes: function(){
				    	layer.closeAll();
				    	start_exam.innerHTML = " 暂停 <span class='iconfont'>&#xe628;</span>";
		                next.addEventListener('tap', next_fun, false);
		                next.removeAttribute('disabled');
		                startTimer();
				    	return false;
				    }, no: function(){
				    	var score = localStorage.getItem('score');
			        	var terrq = localStorage.getItem('terrq');
				    	var width = (document.body.clientWidth - 250) / 2;
				    	var height = (document.body.scrollHeight - 300) / 2;

			            if(score == 0) {
			            	start_exam.innerHTML = " 暂停 <span class='iconfont'>&#xe628;</span>";
			                next.addEventListener('tap', next_fun, false);
			                next.removeAttribute('disabled');
		                	startTimer();
			            	$.toast('你当前分数为0');
			            	return false;
			            }
			            var canswer = localStorage.getItem('canswer');
	      			  	var qtotal = localStorage.getItem('qtotal');
	      			  	var questions = JSON.parse(localStorage.getItem('questions'));
	      			  	var answertrue = questions[qtotal - 1]['answertrue'];
	
	      			  	if(canswer) {
	      			  		if(answertrue == canswer) {
		      			  		if(ltype == 4) {
									localStorage.setItem('score', parseInt(localStorage.getItem('score')) + 2);
		      			  		} else {
									localStorage.setItem('score', parseInt(localStorage.getItem('score')) + 1);
		      			  		}
	      			  		} else {
								var terrq = !localStorage.getItem('terrq') ? ',' : localStorage.getItem('terrq');
								if(terrq.indexOf(questions[qtotal - 1].id) <= 0) {
								localStorage.setItem('terrq', terrq + questions[qtotal - 1].id + ',');
								}
							}
	      			  	}
	      			  	score = localStorage.getItem('score');
				    	var html = '<div style="margin:0px auto; width:100%;"><div style="padding:5px 10px; position:absolute; top:'+height+'px; left:'+width+'px; width:250px; text-align:center;background:#fff; border-radius:10px; opacity:0.8; padding-top:5px;"><span style="color:red; font-size:1rem; margin-bottom:60px;">真实姓名<input type="text" name="real_name" id="real_name" /></span><br /><span style="color:red; font-size:1rem; margin-bottom:60px;">身份证<input type="text" name="identify_id" id="identify_id" /></span><br /><span style="color:red; font-size:1rem; margin-bottom:60px;">手机号<input type="text" name="user_phone" id="user_phone" /></span><br /><button id="score_submit" class="mui-btn mui-btn-green" style="padding:5px 20px;">提交</button>&nbsp;&nbsp;<button type="reset" class="mui-btn mui-btn-red" style="padding:5px 20px;">取消</button></div><img src="../assets/images/back.png" width="100%" height="'+document.body.scrollHeight+'px" /></div>';
				    	var pageii = layer.open({
			                type: 1,
			                content: html,
			                style: 'position:fixed; left:0; top:0; width:100%; height:100%; border:none; '
			            });

				    	var score_submit = doc.getElementById('score_submit');
						score_submit.addEventListener('tap', function(e) {
							var real_name = doc.getElementById('real_name').value;
							var user_phone = doc.getElementById('user_phone').value;
							var identify_id = doc.getElementById('identify_id').value;
							if(real_name.trim() == '' || user_phone.trim() == '' || identify_id.trim() == '') {
								$.alert('请填写完整信息');
								return false;
							}
							var loginauth = localStorage.getItem('loginauth');

							var uid = 0;
				            if(loginauth) {
				            	uid = JSON.parse(loginauth).l_user_id; 
		      			  	}
      			  	
		      			  	var terrq = !localStorage.getItem('terrq') ? 0 : localStorage.getItem('terrq');
				            var params = {
				            	'user_id':uid,
				            	'error_exam_id':terrq,
				            	'score':score,
				            	'license':Cookies.get('ctype'),
				            	'subject':Cookies.get('ltype'),
				            	'total_time': total - time,
				            	'school_id': "<?php echo $sid; ?>",
				            	'real_name': real_name,
				            	'user_phone': user_phone,
				            	'identify_id': identify_id
				            };
				            localStorage.setItem('scoreparam', JSON.stringify(params));
				            saveExamRecord();
				            if(!localStorage.getItem('scoreparam')) {
				            	setInterval('saveExamRecord()', 1000);
				            }
				            	
						})
				    	// var html = '<div style="margin:0px auto; width:100%;"><div style="position:absolute; top:'+height+'px; left:'+width+'px; width:200px; text-align:center;background:#fff; height:200px; border-radius:10px; opacity:0.8; padding-top:50px;"><span style="color:red; font-size:2rem; margin-bottom:60px; display:inline-block;">	分数：'+score+'分</span><br /><button class="mui-btn mui-btn-green" style="padding:5px 20px;" onclick="location.reload();">重考</button>&nbsp;&nbsp;<button onclick="location.href=\'terrorq.php\'" class="mui-btn mui-btn-red" style="padding:5px 20px;">错题</button></div><img src="../assets/images/back.png" width="100%" height="'+document.body.scrollHeight+'px" /></div>';
				    	// var pageii = layer.open({
			      //           type: 1,
			      //           content: html,
			      //           style: 'position:fixed; left:0; top:0; width:100%; height:100%; border:none; '
			      //       });
			            // score
			            	
//			    		var _res_ = setOnePracticePage(practicelist, ctype, ltype, 1);
//							localStorage.setItem('practicelist', JSON.stringify(_res_));
//							location.reload();
//			       	return false;
				    	}
					});
				});
				
				
        function getPractices(qid) {
          var url = "<?php echo HOST; ?>"+ "/get_emulation_practices.php";
          var param = {
            license: Cookies.get('ctype'),
            subject: Cookies.get('ltype')
          };
          $.ajax({
            type:"post",
            url:url,
            async:true,
            data:param,
            dataType:'json',
            success:function(data) {
              if(data.code == 200) {
              	data.data[qid-1].page = qid;
				data.data[qid-1].qtotal = data.data.length;
				q = data.data[qid-1];
                localStorage.setItem('qtotal', data.data.length);
                localStorage.setItem('questions', JSON.stringify(data.data));
                var html = template('question_tmpl', data.data[qid-1]);
                doc.getElementById('question').innerHTML = html;
				if(q.mediatype == 2) { 
					var video = doc.getElementById('player');
					video.addEventListener('click', function() {
						video.play();
					}, false);
				}	
             } else {
             	$.toast(data.data);
				return false;
             }
            },
            error:function() {
              $.toast('网络错误，请检查网络');
            }
          });
        }
        // getPractices end
        
        //根据牌照类型和科目几获取它的作题记录
        function getOnePracticeList (practicelist, ctype, stype)
        {
            var _list = JSON.parse(practicelist);
            if(_list) {
                for ( var i = 0; i < _list.length; i++)
                {
                    var single_type_list = _list[i];
                    if ( single_type_list[0] == ctype && single_type_list[1] == stype )
                    {
                        return single_type_list;
                    }
                }
            } else {
                return false;
            }	                	
            	
        }
        //存储分页数
        function setOnePracticePage(practicelist, ctype, stype, page) {
            var _list = JSON.parse(practicelist);
            if(_list) {
	            for ( var i = 0; i < _list.length; i++)
	            {
	                if ( _list[i][0] == ctype && _list[i][1] == stype )
	                {
	                	_list[i][2] = page;
	                }
	            }
            }
            return _list;
        }
        //存储类型
        function setOnePracticeType(practicelist, ctype, stype, t) {
            var _list = JSON.parse(practicelist);
            if(_list) {
                for ( var i = 0; i < _list.length; i++)
                {
                    if ( _list[i][0] == ctype && _list[i][1] == stype )
                    {
                    	_list[i][3] = !t ? 1 : t;
                    }
                }
            }
            return _list;
        }
        function contains(obj, a) { 
					var i = a.length; 
					while (i--) { 
						if (a[i] === obj) { 
							return true; 
						} 
					} 
					return false; 
				}
      })(mui, document);
      //找到所有不包括自己的兄弟节点
            function sibling( elem ) {
                var r = [];
                var n = elem.parentNode.firstChild;
                for ( ; n; n = n.nextSibling ) {
                    if ( n.nodeType === 1 && n !== elem ) {
                        r.push( n );
                    }
                }

                return r;
            }
            //取消选中其它兄弟节点
            function cancelSiblings(obj) {
                for (var i = 0; i < obj.length; i++) {
                	obj[i].style.background = '#fff';
                	obj[i].style.color = '#555';
                }
            }
            
		      	function saveExamRecord() {
					var params = localStorage.getItem('scoreparam');
			    	var score = localStorage.getItem('score');
		        	var terrq = localStorage.getItem('terrq');
			    	var width = (document.body.clientWidth - 200) / 2;
			    	var height = (document.body.scrollHeight - 300) / 2;
					var score_submit = document.getElementById('score_submit');
					
					if(params) {
						mui.ajax({
			            	type:"post",
			            	url:"<?php echo HOST; ?>/add_exam_record.php",
			            	data:JSON.parse(params),
			            	async:true,
            	            beforeSend:function() {
            	            	var loading = '<div class="mui-loading" style="padding-top:10px; color:#fff"><div class="mui-spinner"></div><p style="text-align: center; margin-top:5px; color:#555;">正在保存分数...</p></div>';
            			    	layer.open({
            					    content: loading,
            					    style: 'background-color:#fff; color:#fff; border:none;',
            					    time:2,
            					});
            					score_submit.innerHTML = '正在提交中';
            	            },
			            	dataType:"json",
			            	success:function(data) {
			            		layer.closeAll();
            					score_submit.innerHTML = '提交';
			            		var html = '<div style="margin:0px auto; width:100%;"><div style="position:absolute; top:'+height+'px; left:'+width+'px; width:200px; text-align:center;background:#fff; height:200px; border-radius:10px; opacity:0.8; padding-top:50px;"><span style="color:red; font-size:2rem; margin-bottom:60px; display:inline-block;">	分数：'+score+'分</span><br /><button class="mui-btn mui-btn-green" style="padding:5px 20px;" onclick="location.reload();">重考</button>&nbsp;&nbsp;<button onclick="location.href=\'terrorq.php\'" class="mui-btn mui-btn-red" style="padding:5px 20px;">错题</button></div><img src="../assets/images/back.png" width="100%" height="'+document.body.scrollHeight+'px" /></div>';
						    	var pageii = layer.open({
					                type: 1,
					                content: html,
					                style: 'position:fixed; left:0; top:0; width:100%; height:100%; border:none; '
					            });
			            		if(data.code == 200) {
		            				localStorage.setItem('scoreparam', '');
			            		} else {
		            				localStorage.setItem('scoreparam', '');
			            			mui.toast('当前考试人数太多，考试成绩可能没有保存');
			            			return false;
			            		}
			            	},
			            	error:function() {
            					score_submit.innerHTML = '提交';
			            		mui.toast('当前考试人数太多，考试成绩可能没有保存');
			            	}
			            })
					} else {
						return false;
					}
				}
                var total = 2700; // 45 min
                var time = total;
                var i;
	            function startTimer() {
	                var min = 0,
	                    sec = 0;
	                if ( time > 0 ) 
	                {
	                    min = Math.floor( time / 60);
	                    sec = Math.floor( time ) - ( min * 60 );
	                }
	                if ( min <= 9 )
	                {
	                    min = '0' + min;
	                }
	                if ( sec <= 9 )
	                {
	                    sec = '0' + sec;
	                }
	                rest_time = min + ':' + sec;
	                exam_times.innerHTML = rest_time;
	                time = time - 1;
			        if( time == 0 ) {
				        var score = localStorage.getItem('score');
				        var terrq = localStorage.getItem('terrq');
		    	        var width = (document.body.clientWidth - 200) / 2;
		    	        var height = (document.body.scrollHeight - 300) / 2;
		    	        var html = '<div style="margin:0px auto; width:100%;"><div style="position:absolute; top:'+height+'px; left:'+width+'px; width:200px; text-align:center;background:#fff; height:200px; border-radius:10px; opacity:0.8; padding-top:50px;"><span style="color:red; font-size:2rem; margin-bottom:60px; display:inline-block;">	分数：'+score+'分</span><br /><button class="mui-btn mui-btn-green" style="padding:5px 20px;" onclick="location.reload();">重考</button>&nbsp;&nbsp;<button onclick="location.href=\'errorq.php\'" class="mui-btn mui-btn-red" style="padding:5px 20px;">错题</button></div><img src="../assets/images/back.png" width="100%" height="'+document.body.scrollHeight+'px" /></div>';
		    	        var pageii = layer.open({
	                        type: 1,
	                        content: html,
	                        style: 'position:fixed; left:0; top:0; width:100%; height:100%; border:none; '
	                    });
		            	// score
		           		var loginauth = localStorage.getItem('loginauth');
						var uid = 0;
						if(!uid) {
							location.href="login.php";
							return false;
						}
			            if(loginauth) {
			            	uid = JSON.parse(loginauth).l_user_id; 
	      			  	}
		            	
			            var params = {
			            	'user_id':uid,
			            	'error_exam_id': terrq,
			            	'score':score,
			            	'license':Cookies.get('ctype'),
			            	'subject':Cookies.get('ltype'),
			            	'total_time': total - time,
			            	'school_id': "<?php echo $sid; ?>"

			            };
		            $.ajax({
		            	type:"post",
		            	url:"<?php echo HOST; ?>/add_exam_record.php",
		            	data:params,
		            	async:false,
		            	dataType:"json",
		            	success:function(data) {
	//			            			alert(data.data);
		            		if(data.code == 200) {
		            		}
		            	},
		            	error:function() {

		            	}
		            })
				        return false;
			        }
	                i = setTimeout("startTimer()", 1000);
	            }
      
			//替换所有要替换的文字
			String.prototype.replaceAll = function(str1, str2) {
			    var str = this;
			    var result = str.replace(eval("/" + str1 + "/gi"), str2);
			    return result;
			}
    </script>
	<?php require_once 'cs.php';echo '<img src="'._cnzzTrackPageView(1259547373).'" width="0" height="0"/>';?>


  </body>
</html>
