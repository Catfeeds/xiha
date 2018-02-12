<?php
    require_once '../include/config.php';
    $type = htmlspecialchars($_GET['t']);
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
        <!--<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>-->
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
            <h1 class="mui-title" id="title"></h1>
            <span class="mui-btn mui-btn-link mui-pull-right" style="margin-left: 10px;">
                <span id="chooseexam">选题</span>
            </span>
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
        <script src="../assets/js/template.js"></script>
        <script src="../assets/js/layer/layer.js"></script>
        <script type="text/html" id="question_temp">
            <div style="padding:0px 10px;">
                <p style="font-size:1rem; color: #555; line-height: 1.5em;">
                    <span style="color: #888888;">
                        (<em style="color: #CF2D28;">{{ page }}</em>/{{ qtotal }}) 
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
            </div>
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
                
                localStorage.removeItem('qtotal');
                localStorage.removeItem('questions');
                localStorage.removeItem('practicelist');
                
                if(!Cookies.get('ctype') || !Cookies.get('ltype') || !Cookies.get('cid')) {
                    location.href="index.html?id=<?php echo $sid; ?>";
                    return false;
                }        
                
                //做题记录
                var t = "<?php echo $type; ?>";
                if(t == 2) {
                    localStorage.removeItem('practicelist');        
                    var _p = localStorage.getItem('practicelist');
                    setOnePracticePage(_p, Cookies.get('ctype'), Cookies.get('ltype'), 1, t);
                }
                var title = '';
                switch(t) {
                    case '1': title = '顺序练习';break;
                    case '2': title = '随机练习';break;
                    case '3': title = '顺序练习';break;
                    case '4': title = '顺序练习';break;
                    case '5': title = '章节练习';break;
                    case '6': title = '我的收藏';break;
                }
                doc.getElementById('title').innerHTML = title;
                var ctype = Cookies.get('ctype');
                var ltype = Cookies.get('ltype');
                if ( !localStorage.getItem('practicelist') ) {
                    //初始化
                    var init = [
                        ['C1', 1, 1, t],
                        ['C1', 4, 1, t],
                        ['A1', 1, 1, t],
                        ['A1', 4, 1, t],
                        ['A2', 1, 1, t],
                        ['A2', 4, 1, t],
                        ['D', 1, 1, t],
                        ['D', 4, 1, t]
                    ];
                    localStorage.setItem('practicelist', JSON.stringify(init));
                } else {
                    
                }
                var practicelist = localStorage.getItem('practicelist');
                var _type = setOnePracticeType(practicelist, ctype, ltype, t);
                localStorage.setItem('practicelist', JSON.stringify(_type));
                practicelist = localStorage.getItem('practicelist');
                var cur_practicelist = getOnePracticeList(practicelist, ctype, ltype);
                if(!cur_practicelist) {
                    location.href = "index.php";
                    return false;
                }
                var qid = cur_practicelist[2]; //从第1题开始
                //获取第一数据
                if(t == 6 && !localStorage.getItem('colle')) {
                    alert('暂无收藏，快点收藏点吧！！！');
                    location.href="default.php";
                    return false;
                }
                //获取第一个题目
                if(t == 1) {
                    var ques = getPractises(t, qid);
                } else {
                    var ques = getPractises(t, 1);
                }
                    
                var html = template('question_temp', ques);
                doc.getElementById('question').innerHTML = html;
                var cids = localStorage.getItem('colle');
                var r = getCurrentQCollection(ques.id, cids);
                if(r) {
                    doc.getElementById('collection').className += ' mui-btn-red';
                    doc.getElementById('collection').querySelector('span').style.color = '#fff';
                } else {
                    doc.getElementById('collection').className = 'mui-btn';
                    doc.getElementById('collection').querySelector('span').style.color = '#CF2D28';
                }
                if(qid != 1 && t == 1) {
                    layer.open({
                        content: '上次练习到第'+qid+'题，是否继续？',
                        btn: ['继续','否'],
                        shadeClose: false,
                        yes: function(){
                            layer.closeAll();
                        }, no: function(){
                            var _res_ = setOnePracticePage(practicelist, ctype, ltype, 1, t);
                            localStorage.setItem('practicelist', JSON.stringify(_res_));
                            location.reload();
                            return false;
                        }
                    });
                }

                var prev = doc.getElementById('prev');
                var next = doc.getElementById('next');
                var questions = JSON.parse(localStorage.getItem('questions'));
                var c = 1;
                //下一页
                next.addEventListener('tap', function(e) {
                    var questions = JSON.parse(localStorage.getItem('questions'));
                    var qtotal = localStorage.getItem('qtotal');
                    doc.getElementById('showexplain').style.display = 'none';
                    var _practicelist = localStorage.getItem('practicelist');
                    var cids = !localStorage.getItem('colle') ? '' : localStorage.getItem('colle');
                    var current_practice_list = getOnePracticeList(_practicelist, ctype, ltype);
                    var page = current_practice_list[2];
                    var _p = parseInt(page) + 1;
                    if(_p > parseInt(qtotal)) {
                        _p = 1;
                    }
                    localStorage.setItem('rp', _p);
                    questions[_p - 1]['page'] = _p;
                    questions[_p - 1]['qtotal'] = localStorage.getItem('qtotal');
                    var r = getCurrentQCollection(questions[_p - 1].id, cids);
                    if(r) {
                        doc.getElementById('collection').className = 'mui-btn mui-btn-red';
                        doc.getElementById('collection').querySelector('span').style.color = '#fff';
                    } else {
                        doc.getElementById('collection').className = 'mui-btn';
                        doc.getElementById('collection').querySelector('span').style.color = '#CF2D28';
                    }
                    var html = template('question_temp', questions[_p - 1]);
                    doc.getElementById('question').innerHTML = html;
                    if(questions[_p - 1]['mediatype'] == 2) { 
                        var video = doc.getElementById('player');
                        video.addEventListener('click', function() {
                            video.play();
                        }, false);
                    }
                    var _res = setOnePracticePage(_practicelist, ctype, ltype, _p, t);
                    localStorage.setItem('practicelist', JSON.stringify(_res));
                    c = 1;
                });
                
                //上一页
                prev.addEventListener('tap', function(e) {
                    var questions = JSON.parse(localStorage.getItem('questions'));
                    var qtotal = localStorage.getItem('qtotal');
                    doc.getElementById('showexplain').style.display = 'none';
                    var _practicelist = localStorage.getItem('practicelist');
                    var current_practice_list = getOnePracticeList(_practicelist, ctype, ltype);
                    var page = current_practice_list[2];
                    var _p = parseInt(page) - 1;
                    if(_p > parseInt(qtotal)) {
                        _p = 1; 
                    }
                    if(_p < 1) {
                        return false;
                    }
                    localStorage.setItem('rp', _p);
                    questions[_p - 1]['page'] = _p;
                    questions[_p - 1]['qtotal'] = localStorage.getItem('qtotal');
                    var _r = getCurrentQCollection(questions[_p - 1].id, cids);
                    if(_r) {
                        doc.getElementById('collection').className = 'mui-btn mui-btn-red';
                        doc.getElementById('collection').querySelector('span').style.color = '#fff';
                    } else {
                        doc.getElementById('collection').className = 'mui-btn';
                        doc.getElementById('collection').querySelector('span').style.color = '#CF2D28';
                    }
                    var html = template('question_temp', questions[_p - 1]);
                    doc.getElementById('question').innerHTML = html;
                    if(questions[_p - 1]['mediatype'] == 2) { 
                        var video = doc.getElementById('player');
                        video.addEventListener('click', function() {
                            video.play();
                        }, false);
                    }
                    var _res = setOnePracticePage(_practicelist, ctype, ltype, _p, t);
                    localStorage.setItem('practicelist', JSON.stringify(_res));
                    c = 1;
                });
                
                //点击单选题或者判断答案
                localStorage.setItem('rp', 1);
                $('#question').on('tap', '.mui-ul-click .mui-single', function() {
                    var _practicelist = localStorage.getItem('practicelist');
                    var qtotal = localStorage.getItem('qtotal');
                    var questions = JSON.parse(localStorage.getItem('questions'));
                    var type = "<?php echo $type; ?>";

                    if(type == 2) {
                        var _p = parseInt(localStorage.getItem('rp'));  
                    } else {
                        var current_practice_list = getOnePracticeList(_practicelist, ctype, ltype);
                        var page = current_practice_list[2];
                        var _p = parseInt(page);
                    }
                        
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
                    if(title == _question.answertrue) {
                        var title = this.getAttribute('title');
                        this.style.background = '#dff0d8';
                        this.style.color = '#3c763d';
                        doc.getElementById('showexplain').style.display = 'block';
                        doc.getElementById('explain').innerHTML = '<span style="font-weight:bold;">回答正确！</span><br />&nbsp;&nbsp;&nbsp;&nbsp;'+_question.explain;
                        doc.getElementById('explain').style.background = '#dff0d8';
                        doc.getElementById('explain').style.color = '#3c763d';
                    } else {
                        this.style.background = '#f2dede';
                        this.style.color = '#a94442';
                        doc.getElementById('showexplain').style.display = 'block';
                        doc.getElementById('explain').innerHTML = '<span style="font-weight:bold;">正确答案：'+ans+"</span><br />&nbsp;&nbsp;&nbsp;&nbsp;"+_question.explain;
                        doc.getElementById('explain').style.background = '#f2dede';
                        doc.getElementById('explain').style.color = '#a94442';
                        //记录错题
                        var errq = !localStorage.getItem('errq') ? '' : localStorage.getItem('errq');
                        localStorage.setItem('errq', errq + _question.id + ',');
                    }
                    c++;
                });
                //点击多选题答案
                var flag = [];
                var user_answer = '';
                $('#question').on('tap', '.mui-ul-click .mui-multi', function() {
                    var questions = JSON.parse(localStorage.getItem('questions'));
                    var _practicelist = localStorage.getItem('practicelist');
                    var type = "<?php echo $type; ?>";

                    if(type == 2) {
                        var _p = parseInt(localStorage.getItem('rp'));  
                    } else {
                        var current_practice_list = getOnePracticeList(_practicelist, ctype, ltype);
                        var page = current_practice_list[2];
                        var _p = parseInt(page);
                    }
                    //当前练习题目
                    var _question = questions[_p - 1];
                    //title为用户选择的答案id,例如:1/2/3/4,一次点击，产生一个数字
                    var title = this.getAttribute('title');
                    var temp_answer = user_answer.split('');
                    temp_answer.push(title);
                    temp_answer = dax_unique_array_value(temp_answer);
                    temp_answer = temp_answer.sort().toString().replaceAll(',', '');
                    user_answer = temp_answer;
                    var ans = '';
                    var _a = _question.answertrue.split("");
                    var title_list = doc.querySelectorAll('.mui-ul-click .mui-multi');
                    
                    //得到ans='ABCD'字样,如果答案是1234
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
                        this.style.background = '#dff0d8';
                        this.style.color = '#3c763d';
                        if ( _question.answertrue == user_answer ) {
                            doc.getElementById('explain').innerHTML = '<span style="font-weight:bold;">回答正确！</span><br />&nbsp;&nbsp;&nbsp;&nbsp;'+_question.explain;
                            doc.getElementById('showexplain').style.display = 'block';
                            doc.getElementById('explain').style.background = '#dff0d8';
                            doc.getElementById('explain').style.color = '#3c763d';
                        }
                        return false;
                    } else {
                        this.style.background = '#f2dede';
                        this.style.color = '#a94442';
                        doc.getElementById('showexplain').style.display = 'block';
                        doc.getElementById('explain').innerHTML = '<span style="font-weight:bold;">正确答案：'+ans+"</span><br />&nbsp;&nbsp;&nbsp;&nbsp;"+_question.explain;
                        doc.getElementById('explain').style.background = '#f2dede';
                        doc.getElementById('explain').style.color = '#a94442';
                        //记录错题
                        var errq = !localStorage.getItem('errq') ? '' : localStorage.getItem('errq');
                        localStorage.setItem('errq', errq + _question.id + ',');
                        return false
                    }
                });
                //点击收藏
                var collection = doc.getElementById('collection');
                collection.addEventListener('tap', function(e) {
                    var questions = JSON.parse(localStorage.getItem('questions'));
                    var qtotal = localStorage.getItem('qtotal');

                    var colle = !localStorage.getItem('colle') ? '' : localStorage.getItem('colle');
                    var _practicelist = localStorage.getItem('practicelist');
                    var current_practice_list = getOnePracticeList(_practicelist, ctype, ltype);
                    var page = current_practice_list[2];
                    var _p = parseInt(page);
                    if(_p > parseInt(qtotal)) {
                        _p = 1;
                    }
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
//                              $.toast('取消收藏失败');
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
                function getPractises(t, qid) {
                    var url = "<?php echo HOST; ?>";
                    // var url = "";
                    var param = {};
                    var q = '';
                    switch(t) {
                        case '1':
                            param.number = 0;
                            url += '/get_all_practices.php';
                            break;
                        case '2':
                            param.rqid = 1;
                            url += '/get_random_practices.php';
                            break;
                        case '3':
                            location.href = "chapters.php";
                            break;
                        case '4':
                            url += '/get_emulation_practices.php';
                            break;
                        case '5':
                            param.cid = Cookies.get('cid');
                            param.number = 0;
                            url += '/get_chapter_practices.php';
                            break;
                        case '6':
                            param.qid_list = !localStorage.getItem('colle') ? '' : localStorage.getItem('colle');
                            url += '/get_questions_by_qid.php';
                            break;
                        default:
                            url += '/get_emulation_practices.php';
                            break;
                    }
                    param.license = Cookies.get('ctype');
                    param.subject = Cookies.get('ltype');

                    $.ajax({
                        type:"post",
                        url:url,
                        async:false,
                        data:param,
                        dataType:'json',
                        success:function(data) {
                            if(data.code == 200) {
                                data.data[qid-1].page = qid;
                                data.data[qid-1].qtotal = data.data.length;
                                q = data.data[qid-1];
                                localStorage.setItem('qtotal', data.data.length);
                                localStorage.setItem('questions', JSON.stringify(data.data));
                                
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
                    return q;
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
                function setOnePracticePage(practicelist, ctype, stype, page, t) {
                    var _list = JSON.parse(practicelist);
                    if(_list) {
                        for ( var i = 0; i < _list.length; i++)
                        {
                            if ( _list[i][0] == ctype && _list[i][1] == stype )
                            {
                                _list[i][2] = page;
                                _list[i][3] = !t ? 1 : t;
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
//                  _list.shift();
                    for (var i = 0; i < _list.length; i++) {
                        if(qid == _list[i]) {
                            return true;
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

                function dax_unique_array_value( ar ) {
                    var ret = [];
                    for (var i = ar.length-1; i >= 0; i--) {
                        if ( ret.indexOf(ar[i]) == -1 ) {
                            ret.push(ar[i]);
                        }
                    }
                    return ret;
                }
                
                // 选题
                var chooseexam = doc.getElementById('chooseexam');
                chooseexam.addEventListener('tap', function(e) {
                    var qtotal = localStorage.getItem('qtotal');
                    var html = "<div style='width:100%;' id='choosequestion'>";
                    for(var i=1; i<= parseInt(qtotal); i++) {
                        html += '<a data-id="'+i+'" class="choosequestion" style="border:1px solid #ccc; width:50px; height:50px; margin:5px; display:inline-block; border-radius:50%; text-align:center; line-height:50px;">'+i+'</a>';
                    }
                    html += '</div>';
                    layer.open({
                        type: 1,
                        content: html,
                        anim: 0,
                        style: 'position:fixed; bottom:0; left:0; width:100%; height:400px; overflow-y:scroll; padding:10px 0; border:none;'
                    });
                    
                    //选择题目
                    $('#choosequestion').on('tap', '.choosequestion', function(e) {
                        var explain = document.getElementById('showexplain');
                        showexplain.style.display = 'none';
                        c = 1;
                        var data_id = this.getAttribute('data-id');
                        var practiselist = localStorage.getItem('practiselist');
                        var ctype = Cookies.get('ctype');
                        var ltype = Cookies.get('ltype');
                        var _res = setOnePracticePage(practicelist, ctype, ltype, data_id, 1);
                        localStorage.setItem('practicelist', JSON.stringify(_res));
                        var questions = JSON.parse(localStorage.getItem('questions'));
                        var _p = data_id;
                        localStorage.setItem('rp', _p);
                        questions[_p - 1]['page'] = _p;
                        questions[_p - 1]['qtotal'] = localStorage.getItem('qtotal');
                        var html = template('question_temp', questions[_p - 1]);
                        doc.getElementById('question').innerHTML = html;
                        layer.closeAll();
                    });
                });
                
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
