<?php
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
			#showexplain .mui-table-view:before {height: 0;}
			.mui-bar .mui-btn {top:0px; border:none;}
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
			<h1 class="mui-title" id="">我的错题</h1>
		</header>
		<div class="mui-content" style="background: #f5f5f5;">
			<div id="question" >
				<div class="mui-loading" style="background: #f5f5f5; padding-top:10px;">
					<div class="mui-spinner">
					</div>
					<p style="text-align: center; margin-top:5px;">正在加载中</p>
				</div>
			</div>
			<div id="showexplain" style="background: #f5f5f5; margin-top: 10px; display: none; height: auto;">
				<ul class="mui-table-view" style="margin: 0px;">
	            	<li class="mui-table-view-cell" title="" style="color: #333333;" id="explain">
	            	</li>
	        	</ul>
			</div>
		</div>
		<nav class="mui-bar mui-bar-tab" style="position: inherit; bottom: 0px; margin-top:10px; margin-bottom:10px; box-shadow: none;">
			<a class="mui-tab-item mui-active" href="javascript:;">
				<button class="mui-btn" style="padding:10px 30px" id="prev"><span class="iconfont">&#xe60e;</span> 上一题</button>
			</a>
			<a class="mui-tab-item" href="javascript:;">
				<button id="collection" style="padding:10px 30px" class="mui-btn"><span class="iconfont" style="color: #CF2D28;">&#xe625;</span> 收藏</button>
			</a>
			<a class="mui-tab-item" href="javascript:;">
				<button class="mui-btn" style="padding:10px 30px" id="next">下一题 <span class="iconfont">&#xe60f;</span></button>
			</a>
		</nav>
		<!--<div style="text-align: center; margin-bottom:20px; position: fixed; width: 100%; bottom: 0px; ">
			<button class="mui-btn" id="prev"><span class="iconfont">&#xe60e;</span> 上一题</button>
			<button id="collection" class="mui-btn"><span class="iconfont" style="color: #CF2D28;">&#xe625;</span> 收藏</button>
			<button class="mui-btn" id="next">下一题 <span class="iconfont">&#xe60f;</span></button>
		</div>-->
		<script src="../assets/js/mui.min.js"></script>
		<script src="../assets/js/cookie.min.js"></script>
		<script src="../assets/js/template.js"></script>
		<script src="../assets/js/layer/layer.js"></script>
		<script type="text/html" id="question_temp">
			
			<ul class="mui-table-view" style="margin: 0px;">
            	<li class="mui-table-view-cell" id="mui-li-click" title="" style="color: #333333;">
					<p style="font-size:1rem; color: #555; line-height: 1.5em;">
						<span style="color: #888888;">
							(<em style="color: #CF2D28;">{{ page }}</em>/{{ errtotal }}) 
							{{if type == 1}}
							(判)
							{{else if type == 2}}
							(单)
							{{else}}
							(多)
							{{/if}}
						</span> 
						{{question}}
					</p>

					{{if mediatype == 1}}
						<img src="{{ imageurl }}" style="width: 100%;" alt="" />
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
				//有没有错题
                var errq = localStorage.getItem('errq');
                if ( !errq ) {
                    $.alert('您真棒，还没有错题!回首页，再继续修炼!');
                    location.href = "index.php?id=<?php echo $sid; ?>";
                    return false;
                }
                //根据errq存的id取出错误的题目
                var errlist = JSON.parse(localStorage.getItem('questions'));
				var ctype = Cookies.get('ctype');
				var ltype = Cookies.get('ltype');
				if ( !localStorage.getItem('practicelist') ) {
                    //初始化
	                var init = [
	                	['C1', 1, 1, 5],
	                	['C1', 4, 1, 5],
	                	['A1', 1, 1, 5],
	                	['A1', 4, 1, 5],
	                	['A2', 1, 1, 5],
	                	['A2', 4, 1, 5],
	                	['D', 1, 1, 5],
	                	['D', 4, 1, 5]
	                ];
                    localStorage.setItem('practicelist', JSON.stringify(init));
                }
                var practicelist = localStorage.getItem('practicelist');
                var _type = setOnePracticeType(practicelist, ctype, ltype, 5);
		    	var _res_ = setOnePracticePage(practicelist, ctype, ltype, 1);
                localStorage.setItem('practicelist', JSON.stringify(_res_));
                practicelist = localStorage.getItem('practicelist');
		        var cur_practicelist = getOnePracticeList(practicelist, ctype, ltype);
		        if(!cur_practicelist) {
		        	location.href = "index.php";
		        	return false;
		        }
		        var qid = cur_practicelist[2]; //从第1题开始
//		        console.log(qid);
                getWrongPractises(errq, qid);
                
				//获取第一数据
                ques = errlist[0];
				var cids = localStorage.getItem('colle');
				var r = getCurrentQCollection(ques.id, cids);
				if(r) {
					doc.getElementById('collection').className += ' mui-btn-red';
					doc.getElementById('collection').querySelector('span').style.color = '#fff';
				}

				var prev = doc.getElementById('prev');
				var next = doc.getElementById('next');
				var questions = JSON.parse(localStorage.getItem('questions'));
				var c = 1;
				//下一页
				next.addEventListener('tap', function(e) {
        			var _practicelist = localStorage.getItem('practicelist');
					var cids = !localStorage.getItem('errq') ? '' : localStorage.getItem('errq');
					var current_practice_list = getOnePracticeList(_practicelist, ctype, ltype);
        			var page = current_practice_list[2];
					var _p = parseInt(page) + 1;
					if(_p > localStorage.getItem('errtotal')) {
						return false;
					}
					questions[_p - 1]['page'] = _p;
					questions[_p - 1]['errtotal'] = localStorage.getItem('errtotal');
					
					var html = template('question_temp', questions[_p - 1]);
					doc.getElementById('question').innerHTML = html;
					var _res = setOnePracticePage(_practicelist, ctype, ltype, _p);
					localStorage.setItem('practicelist', JSON.stringify(_res));
					c = 1;
				});
				
				//上一页
				prev.addEventListener('tap', function(e) {
					var _practicelist = localStorage.getItem('practicelist');
					var current_practice_list = getOnePracticeList(_practicelist, ctype, ltype);
        			var page = current_practice_list[2];
					var _p = parseInt(page) - 1;
					if(_p < 1) {
						return false;
					}
					questions[_p - 1]['page'] = _p;
					questions[_p - 1]['errtotal'] = localStorage.getItem('errtotal');
					
					var html = template('question_temp', questions[_p - 1]);
					doc.getElementById('question').innerHTML = html;
					var _res = setOnePracticePage(_practicelist, ctype, ltype, _p);
					localStorage.setItem('practicelist', JSON.stringify(_res));
					c = 1;
				});
        		//点击单选题或者判断答案
				$('#question').on('tap', '.mui-ul-click .mui-single', function() {
					var _practicelist = localStorage.getItem('practicelist');
					var current_practice_list = getOnePracticeList(_practicelist, ctype, ltype);
					var errq = !localStorage.getItem('errq') ? '' : localStorage.getItem('errq');
					var errtotal = !localStorage.getItem('errtotal') ? '' : localStorage.getItem('errtotal');
                    var page = current_practice_list[2];
					var _p = parseInt(page);
					var _question = questions[_p - 1];
					var title = this.getAttribute('title');
					var ans = '';
					switch(_question.answertrue) {
						case '1': ans = 'A'; break;
						case '2': ans = 'B'; break;
						case '3': ans = 'C'; break;
						case '4': ans = 'D'; break;
					}
					if(c != 1) {
						return false;
					}
					//正确
					if(title == _question.answertrue) {
						var title = this.getAttribute('title');
						this.style.background = '#dff0d8';
						this.style.color = '#3c763d';
						localStorage.setItem('errq', errq.replaceAll(_question.id + ",", ""));
						localStorage.setItem('errtotal', parseInt(errtotal) - 1);
						_question.errtotal = parseInt(errtotal) - 1;
						_question.page = _p-1;
						doc.getElementById('showexplain').style.display = 'block';
						doc.getElementById('explain').innerHTML = '<span style="font-weight:bold;">回答正确！</span><br />&nbsp;&nbsp;&nbsp;&nbsp;'+_question.explain;
						doc.getElementById('explain').style.background = '#dff0d8';
						doc.getElementById('explain').style.color = '#3c763d';
						// var html = template('question_temp', _question);
						// doc.getElementById('question').innerHTML = html;
						
					} else {
						this.style.background = '#f2dede';
						this.style.color = '#a94442';
						doc.getElementById('showexplain').style.display = 'block';
						doc.getElementById('explain').innerHTML = '<span style="font-weight:bold;">正确答案：'+ans+"</span><br />&nbsp;&nbsp;&nbsp;&nbsp;"+_question.explain;
						doc.getElementById('explain').style.background = '#f2dede';
						doc.getElementById('explain').style.color = '#a94442';
						
						//记录错题
//						var errq = !localStorage.getItem('errq') ? '' : localStorage.getItem('errq');
//						localStorage.setItem('errq', errq + _question.id + ',');
					}
					c++;
				});
				//点击多选题答案
				var flag = [];
				$('#question').on('tap', '.mui-ul-click .mui-multi', function() {
					var _practicelist = localStorage.getItem('practicelist');
					var errq = !localStorage.getItem('errq') ? '' : localStorage.getItem('errq');
					var current_practice_list = getOnePracticeList(_practicelist, ctype, ltype);
                    var page = current_practice_list[2];
					var _p = parseInt(page);
					var _question = questions[_p - 1];
					var title = this.getAttribute('title');
					var ans = '';
					var _a = _question.answertrue.split("");
					var title_list = doc.querySelectorAll('.mui-ul-click .mui-multi');
					
					for (var i = 0; i < _a.length; i++) {
						switch(_a[i]) {
							case '1': ans += 'A'; break;
							case '2': ans += 'B'; break;
							case '3': ans += 'C'; break;
							case '4': ans += 'D'; break;
						}
					}
					var res = contains(title, _a);
					var _a_len = _a.length;
					if(res) {
						flag.push(title);
					}
					if(res) {
						//全对
						if(flag.length == _a_len) {
							doc.getElementById('explain').innerHTML = '<span style="font-weight:bold;">回答正确！</span><br />&nbsp;&nbsp;&nbsp;&nbsp;'+_question.explain;
							doc.getElementById('showexplain').style.display = 'block';
							doc.getElementById('explain').style.background = '#dff0d8';
							doc.getElementById('explain').style.color = '#3c763d';
							localStorage.setItem('errq', errq.replaceAll(_question.id + ",", ""));
						}
						this.style.background = '#dff0d8';
						this.style.color = '#3c763d';
						return false;
						
					} else {
						this.style.background = '#f2dede';
						this.style.color = '#a94442';
						doc.getElementById('showexplain').style.display = 'block';
						doc.getElementById('explain').innerHTML = '<span style="font-weight:bold;">正确答案：'+ans+"</span><br />&nbsp;&nbsp;&nbsp;&nbsp;"+_question.explain;
						doc.getElementById('explain').style.background = '#f2dede';
						doc.getElementById('explain').style.color = '#a94442';
						//记录错题
//						var errq = !localStorage.getItem('errq') ? '' : localStorage.getItem('errq');
//						localStorage.setItem('errq', errq + _question.id + ',');
//						return false
					}
				});
				//点击收藏
				var collection = doc.getElementById('collection');
				collection.addEventListener('tap', function(e) {
					var colle = !localStorage.getItem('colle') ? '' : localStorage.getItem('colle');
					var _practicelist = localStorage.getItem('practicelist');
					var current_practice_list = getOnePracticeList(_practicelist, ctype, ltype);
                    var page = current_practice_list[2];
					var _p = parseInt(page);
					var _question = questions[_p - 1];
						
					if(colle) {
						var res = getCurrentQCollection(_question.id, colle);
						if(res) {
							//存在取消
							if(colle.replaceAll(_question.id + ",", "")) {
								this.className = 'mui-btn';
								this.querySelector('span').style.color = '#CF2D28';
								localStorage.setItem('colle', colle.replaceAll(_question.id + ",", ""));
							} else {
								this.className = 'mui-btn';
								this.querySelector('span').style.color = '#CF2D28';
								localStorage.setItem('colle', colle.replaceAll(_question.id + ",", ""));
//								$.toast('取消收藏失败');
								return false;
							}
						} else {
							this.className += ' mui-btn-red';
							this.querySelector('span').style.color = '#fff';
							localStorage.setItem('colle', colle + _question.id + ',');
						}
					} else {
						this.className += ' mui-btn-red';
						this.querySelector('span').style.color = '#fff';
						localStorage.setItem('colle', colle + _question.id + ',');
					}
					colle = localStorage.getItem('colle');
					var res = getCurrentQCollection(_question.id, colle);
				});
                function getWrongPractises(qid_list, qid) {
                    var url  = "<?php echo HOST; ?>/get_questions_by_qid.php";
                    var param = {
                        'qid_list': qid_list
                    };
					$.ajax({
						type:"post",
						url:url,
						async:false,
						data:param,
						dataType:'json',
						success:function(data) {
							if(data.code == 200) {
								data.data[0].page = 1;
								data.data[0].errtotal = data.data.length;
								q = data.data[qid-1];
            					localStorage.setItem('errtotal', data.data.length);
								localStorage.setItem('questions', JSON.stringify(data.data));
								var html = template('question_temp', data.data[0]);
								doc.getElementById('question').innerHTML = html;
								if(q.mediatype == 2) { 
									var video = doc.getElementById('player');
									if(video) {
										video.addEventListener('click', function() {
											video.play();
										}, false);
									}
										
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
	                for ( var i = 0; i < _list.length; i++)
	                {
	                    if ( _list[i][0] == ctype && _list[i][1] == stype )
	                    {
	                    	_list[i][2] = page;
	                    }
	                }
	                return _list;
	            }
	            //存储类型
	            function setOnePracticeType(practicelist, ctype, stype, t) {
	                var _list = JSON.parse(practicelist);
	                for ( var i = 0; i < _list.length; i++)
	                {
	                    if ( _list[i][0] == ctype && _list[i][1] == stype )
	                    {
	                    	_list[i][3] = !t ? 1 : t;
	                    }
	                }
	                return _list;
	            }
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
	            
	            //获取当前问题有没有收藏
	            function getCurrentQCollection(qid, cids) {
	            	if(cids) {
		            	var _cids = cids.lastIndexOf(',');
		            	if(_cids > 0) {
		            		cids = cids.substr(',', _cids);
		            	}
	            	} else {
	            		return false;
	            	}
					var _list = cids.split(',');
//              	_list.shift();
                	for (var i = 0; i < _list.length; i++) {
                		if(qid == _list[i]) {
                			return qid;
                		}
                	}
                	return false;
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
			
			//替换所有要替换的文字
			String.prototype.replaceAll = function(str1, str2) {
			    var str = this;
			    var result = str.replace(eval("/" + str1 + "/gi"), str2);
			    return result;
			}
		</script>
	</body>
</html>
