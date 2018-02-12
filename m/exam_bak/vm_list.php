<?php
    require_once '../include/config.php';
    $type = htmlspecialchars($_GET['t']);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
        <title></title>
        <link rel="stylesheet" href="../assets/css/mui.min.css" />
        <link rel="stylesheet" href="../assets/css/style.css" />
        <link rel="stylesheet" href="../assets/font/iconfont/iconfont.css" />
        <link rel="stylesheet" href="../assets/css/swiper.min.css" />
        <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
        <script src="../assets/js/cookie.min.js"></script>
        <script src="../assets/js/vue.min.js"></script>
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
            <h1 class="mui-title" id="">驾考题库</h1>
        </header>
        <div class="mui-content" id="question" style="background: #f5f5f5;" v-model="question">
            <!--<div class="mui-loading" style="background: #f5f5f5; padding-top:10px;">
                <div class="mui-spinner">
                </div>
                <p style="text-align: center; margin-top:5px;">正在加载中</p>
            </div>-->
             <ul class="mui-table-view" style="margin: 0px;">
                <li class="mui-table-view-cell" id="mui-li-click" title="" style="color: #333333;">
                    <span style="color: #888888;">({{ qid }}/{{ qtotal }})</span> 
                    <!--驾驶这种机动车上路行驶属于什么行为？-->
                    {{ current_question.question }}
                    <img src="http://m.ej400.com/content/DrivingImg/p_001.jpg" style="width: 100%;" alt="" />
                </li>
            </ul>
            <ul class="mui-table-view mui-ul-click">
                <li class="mui-table-view-cell" id="mui-li-click" title="" style="color: #333333;">
                    A.{{ current_question.an1 }}
                </li>
                <li class="mui-table-view-cell" id="mui-li-click" title="" style="color: #333333;">
                    B.{{ current_question.an2 }}
                </li>
                <li class="mui-table-view-cell" id="mui-li-click" title="" style="color: #333333;">
                    C.{{ current_question.an3 }}
                </li>
                <li class="mui-table-view-cell" id="mui-li-click" title="" style="color: #333333;">
                    D.{{ current_question.an4 }}
                </li>
            </ul>
        </div>
		
        <div style="text-align: center; margin-top:20px;">
            <button v-model="current_question" v-on:click="prevQuestion" class="mui-btn" id="prev"><span class="iconfont">&#xe60e;</span> 上一题</button>
            <button id="collection" class="mui-btn mui-btn"><span class="iconfont" style="color: #CF2D28;">&#xe625;</span> 收藏</button>
            <button v-model="current_question" v-on:click="nextQuestion" class="mui-btn" id="next">下一题 <span class="iconfont">&#xe60f;</span></button>
        </div>
        <script src="../assets/js/mui.min.js"></script>
        <script src="../assets/js/cookie.min.js"></script>
        
        <script>
            (function($, doc) {
                if(!Cookies.get('ctype') || !Cookies.get('ltype')) {
                    location.href="index.php";
                    return false;
                }
                var t = "<?php echo $type; ?>";
                getPractises(t);
                var questions = JSON.parse(localStorage.getItem('questions'));

                //做题记录
                var practicelist = localStorage.getItem('practicelist');
                if ( !practicelist ) {
                    //初始化
                    localStorage.setItem('practicelist', '..C1.1.1..C1.4.1..A1.1.1..A1.4.1..A2.1.1..A2.4.1..D.1.1..D.4.1');
                    var qid = 1; //从第1题开始
                } else {
                    current_practice_list = getOnePracticeList(practicelist, Cookies.get('ctype'), Cookies.get('ltype'));
                    var qid = current_practice_list[2];
                }
                    
                function getPractises(t) {
                    var url = '<?php echo HOST; ?>';
                    var param = {};
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
                        default:
                            url += '/get_emulation_practices.php';
                            break;
                    }
                    param.license = Cookies.get('ctype');
                    param.subject = Cookies.get('ltype');

                    $.ajax({
                        type:"post",
                        url:url,
                        async:true,
                        data:param,
                        dataType:'json',
                        beforeSend:function() {
                            
                        },
                        success:function(data) {
                            if(data.code == 200) {
                                localStorage.setItem('questions', JSON.stringify(data.data));
                            }
                        },
                        error:function() {
                            $.toast('网络错误，请检查网络');
                        }
                    });
                }

                // vue
                var vm = new Vue({
                    el: '#question',
                    data: {
                    	'current_question':questions[qid-1],
                    	'qtotal':questions.length,
                    	'qid':qid
                    },
                });

                // Next Question
                var vmnextQ = new Vue({
                    el: '#next',
                    methods: {
                        nextQuestion: function () {
                            if ( parseInt(qid) < questions.length )
                            {
								qid = parseInt(qid) + 1;
                                vm.current_question = questions[qid];
                                vm.qid = qid;
                            }
                        }
                    },
                    computed: {
                        qid: function () {
                            return qid
                        }
                    }
                });

                // Previous Question
                var vmprevQ = new Vue({
                    el: '#prev',
                    methods: {
                        prevQuestion: function () {
                            if ( parseInt(qid) > 1 )
                            {
                                qid = parseInt(qid) - 1;
                                vm.current_question = questions[qid];
                                vm.qid = qid;
                            }
                        }
                    },
                    computed: {
                        qid: function () {
                            return qid
                        }
                    }
                });
                
            })(mui, document);

            //根据牌照类型和科目几获取它的作题记录
            function getOnePracticeList (practicelist, ctype, stype)
            {
                var _list = practicelist.split('..');
                _list.shift();
                for ( var i = 0; i < _list.length; i++)
                {
                    var single_type_list = _list[i].split('.');
                    if ( single_type_list[0] == ctype && single_type_list[1] == stype )
                    {
                        return single_type_list;
                    }
                }
                return false;
            }
        </script>

        <script>
        </script>
    </body>
</html>
