(function($, doc) {
    var param = {
        'ctype': ctype,
        'stype': stype
    }
    var limit = 1;
    var qid = 1;
    var page = 1;
    page = page == 0 ? 1 : page;
    page = isNaN(page) ? 1 : page;
    var errq = localStorage.getItem('errq_') ? JSON.parse(localStorage.getItem('errq_')) : [];
    var trueq = localStorage.getItem('trueq_') ? JSON.parse(localStorage.getItem('trueq_')) : [];
    var colle = localStorage.getItem('colle') ? JSON.parse(localStorage.getItem('colle')) : [];
    var choose_list = [];
    var is_start = 1;
    var exam_choose_list = [];
    Cookies.set('exam_choose_list', '[]');
    localStorage.setItem('errq__', '[]');
    localStorage.setItem('trueq__', '[]');
    // 获取题号
    var version = 'v2';
    var folder = 'exam';
    var aname = 'exam.php';
    var localstorage_name = 'test_id';
    var question_ids = getRandomQuestionId(version, folder, aname, ctype, stype, localstorage_name);

    if (question_ids == false || question_ids.length == 0) {
        $.toast('网络错误，请检查网络');
        location.href = root_path + "exam/index-" + sid + ".html";
        return false;
    }
    localStorage.setItem('qtotal', question_ids.length);

    // 获取分页的题目号
    var _question_list = getQuestionIdsByPage(page, limit, question_ids);
    var _question_ids = _question_list.join(',');
    var question_list = localStorage.getItem('question_list') ? JSON.parse(localStorage.getItem('question_list')) : [];

    //初始化
    localStorage.setItem('temporary_page', page);
    getPractises(_question_ids, 'v2', 'exam', 'question_list.php');
    question_list = JSON.parse(localStorage.getItem('question_list'));
    question_list.page = page;
    question_list.qtotal = question_ids.length;
    doc.getElementById('qtotal').innerHTML = question_ids.length;
    doc.getElementById('qid').innerHTML = page;
    var colle = localStorage.getItem('colle') ? JSON.parse(localStorage.getItem('colle')) : [];
    var curr_id = question_list['question_list'][0]['id'];
    var _exam_choose_list = Cookies.get('exam_choose_list') ? JSON.parse(Cookies.get('exam_choose_list')) : [];
    template_single_render(question_list, _exam_choose_list, 'question', 'question_temp');
    startTimer();

    //下一页
    var handover = doc.getElementById('handover');
    var next = doc.getElementById('next');
    var next_func = function() {
        choose_list.length = 0;
        var xuanze_no_list = [];
        doc.getElementById('showexplain').style.display = 'none';
        var page = localStorage.getItem('temporary_page');
        //record single question
        var question_list = localStorage.getItem('question_list') ? JSON.parse(localStorage.getItem('question_list')) : [];
        var answertrue = question_list['question_list'][0]['answertrue'];
        var type = question_list['question_list'][0]['type'];
        var qid = question_list['question_list'][0]['id'];
        if (type == 1 || type == 2) {
            $.each(document.querySelectorAll('.xuanze_no'), function(e) {
                if (this.getAttribute('data-id') == 2) {
                    xuanze_no_list.push(this.parentNode.getAttribute('title'));
                }
            });
            if (xuanze_no_list.length != 0) {
                var xuanze_no_str = xuanze_no_list.join('');
                if (answertrue == xuanze_no_str) {
                    exam_choose_list.push({
                        'id': qid,
                        'chosen': xuanze_no_list,
                        'is_true': 2
                    });
                    recordErrTrueQuestions(errq, trueq, qid, '2', '', ''); //记录正确
                } else {
                    exam_choose_list.push({
                        'id': qid,
                        'chosen': xuanze_no_list,
                        'is_true': 1
                    });
                    recordErrTrueQuestions(errq, trueq, qid, '1', '', ''); //记录错题
                }

                Cookies.set('exam_choose_list', JSON.stringify(exam_choose_list));
            }
        }
        page++;
        if (page > question_ids.length) {
            this.style.color = '#999';
            return false;
        }
        localStorage.setItem('temporary_page', page);
        var _question_list = getQuestionIdsByPage(page, limit, question_ids);
        var _question_ids = _question_list.join(',');
        var question_list = localStorage.getItem('question_list') ? JSON.parse(localStorage.getItem('question_list')) : [];
        getPractises(_question_ids, 'v2', 'exam', 'question_list.php');
        question_list = JSON.parse(localStorage.getItem('question_list'));
        question_list.page = page;
        question_list.qtotal = question_ids.length;
        doc.getElementById('qtotal').innerHTML = question_ids.length;
        doc.getElementById('qid').innerHTML = page;
        var colle = localStorage.getItem('colle') ? JSON.parse(localStorage.getItem('colle')) : [];
        var curr_id = question_list['question_list'][0]['id'];
        var _exam_choose_list = Cookies.get('exam_choose_list') ? JSON.parse(Cookies.get('exam_choose_list')) : [];
        template_single_render(question_list, _exam_choose_list, 'question', 'question_temp');
    }
    next.addEventListener('tap', next_func, false);

    //交卷
    handover.addEventListener('tap', function() {
        var _errq = localStorage.getItem('errq__') ? JSON.parse(localStorage.getItem('errq__')) : [];
        var _trueq = localStorage.getItem('trueq__') ? JSON.parse(localStorage.getItem('trueq__')) : [];
        var answer_len = _errq.length + _trueq.length;
        var version = 'v2';
        var folder = 'exam';
        var aname = 'add_exam_record.php';
        var score = 0;
        var xuanze_no_list = [];
        var question_list = localStorage.getItem('question_list') ? JSON.parse(localStorage.getItem('question_list')) : [];
        var answertrue = question_list['question_list'][0]['answertrue'];
        var type = question_list['question_list'][0]['type'];
        var qid = question_list['question_list'][0]['id'];
        $.each(document.querySelectorAll('.xuanze_no'), function(e) {
            if (this.getAttribute('data-id') == 2) {
                xuanze_no_list.push(this.parentNode.getAttribute('title'));
            }
        });
        if (xuanze_no_list.length != 0) {
            var xuanze_no_str = xuanze_no_list.join('');
            if (answertrue == xuanze_no_str) {
                exam_choose_list.push({
                    'id': qid,
                    'chosen': xuanze_no_list,
                    'is_true': 2
                });
                recordErrTrueQuestions(errq, trueq, qid, '2', '', ''); //记录正确
            } else {
                exam_choose_list.push({
                    'id': qid,
                    'chosen': xuanze_no_list,
                    'is_true': 1
                });
                recordErrTrueQuestions(errq, trueq, qid, '1', '', ''); //记录错题
            }
            Cookies.set('exam_choose_list', JSON.stringify(exam_choose_list));
        }
        var question_list = localStorage.getItem('question_list') ? JSON.parse(localStorage.getItem('question_list')) : [];
        var _exam_choose_list = Cookies.get('exam_choose_list') ? JSON.parse(Cookies.get('exam_choose_list')) : [];
        template_single_render(question_list, _exam_choose_list, 'question', 'question_temp');
        _trueq = localStorage.getItem('trueq__') ? JSON.parse(localStorage.getItem('trueq__')) : [];

        if (stype == 1) {
            score = _trueq.length;
        } else {
            score = _trueq.length * 2;
        }
        if (score == 0) {
            var index = layer.open({
                content: '<p style="color:#333; line-height:30px; font-size:1rem;">你当前分数是 <span style="color:#18b4ed;">0</span>，不可交卷！</p>',
                shadeClose: false,
                shade: 1,
                btn: ['<a style="color:#18b4ed;">开始做题</a>'],
                end: function(index) {
                    time_button.innerHTML = '<span class="mui-icon iconfont">&#xe63f;</span><span class="mui-tab-label">暂停</span>';
                    next.addEventListener('tap', next_func, false);
                    next.removeAttribute('disabled');
                    questions_no_list.addEventListener('tap', question_list_func, false);
                    questions_no_list.removeAttribute('disabled');
                    clearTimeout(i);
                }
            });
            return false;
        }
        var content_html = '<p style="color:#333; line-height:30px; font-size:1rem;">你还剩时间 <span style="color:#18b4ed; font-weight:bold;">' + exam_times.innerHTML + '</span>，已作答 <span style="color:#18b4ed; font-weight:bold;">' + answer_len + '</span> 题，最终得分 <span style="color:#18b4ed; font-weight:bold;">' + score + '</span> 分，<br/>确定交卷吗？</p>';
        clearTimeout(i);
        //询问框
        var index = layer.open({
            content: content_html,
            btn: ['<a style="color:#18b4ed;">交卷</a>', '<a style="color:#18b4ed;">继续答题</a>'],
            shadeClose: false,
            yes: function(index) {
                var terrq = localStorage.getItem('errq__') ? JSON.parse(localStorage.getItem('errq__')) : [];
                var params = {
                    'user_id': uid,
                    'error_exam_id': terrq.join(','),
                    'score': score,
                    'license': ctype,
                    'subject': stype,
                    'total_time': total - time,
                    'school_id': sid,
                    'os': os,
                };

                // ajax提交分数
                handoverAjax(version, folder, aname, params);
                // shareAjax(version, folder, aname, params, score, ctype, stype);
                layer.close(index);
            },
            no: function(index) {
                startTimer();
            }
        });
    });


    // 答案选择
    var choose_answer = function() {
        var exam_btn = doc.getElementById('exam-btn');
        var question_list = localStorage.getItem('question_list') ? JSON.parse(localStorage.getItem('question_list')) : [];
        var answer_id = this.getAttribute('title');

        if (typeof question_list != 'object' || question_list == '' || question_list == false || question_list['question_list'].length == 0) {
            location.reload();
            return false;
        }
        var iconfont_check = this.parentNode.querySelector('.iconfont') ? 1 : 2;
        if (1 == iconfont_check) {
            return false;
        }
        var ans = question_list['question_list'][0]['answertrue'];
        var type = question_list['question_list'][0]['type'];
        var title = this.getAttribute('title');

        var flag = this.querySelector('.xuanze_no').getAttribute('data-id') ? this.querySelector('.xuanze_no').getAttribute('data-id') : 1;
        var xuanze_no_list = [];

        //单选和判断
        if (type == 1 || type == 2) {
            if (1 == flag) {
                this.querySelector('.xuanze_no').className += ' xuanze_checked';
                this.querySelector('.xuanze_no').setAttribute('data-id', '2');
            }
            cancelSiblings(sibling(this));
            //多选
        } else {
            if (1 == flag) {
                this.querySelector('.xuanze_no').className += ' xuanze_checked';
                this.querySelector('.xuanze_no').setAttribute('data-id', '2');
            } else {
                this.querySelector('.xuanze_no').className = 'xuanze_no';
                this.querySelector('.xuanze_no').setAttribute('data-id', '1');
            }
            $.each(this.parentNode.querySelectorAll('.xuanze_no'), function(e) {
                if (this.getAttribute('data-id') == 2) {
                    xuanze_no_list.push(this.parentNode.getAttribute('title'));
                }
            });
            if (exam_btn) {
                if (xuanze_no_list.length > 1) {
                    exam_btn.style.backgroundColor = '#18b4ed';
                    exam_btn.disabled = false;
                } else {
                    exam_btn.style.backgroundColor = '#999';
                    exam_btn.disabled = true;
                }
            } else {
                return false;
            }
            //多选选择答案
            exam_btn.addEventListener('tap', function(e) {
                if (exam_btn) {
                    //this.parentNode.removeChild(exam_btn);
                    this.style.display = 'none';
                }
                var _xuanze_no_list = [];
                var question_list = localStorage.getItem('question_list') ? JSON.parse(localStorage.getItem('question_list')) : '';
                if (typeof question_list != 'object' || question_list == '' || question_list == false || question_list['question_list'].length == 0) {
                    location.reload();
                    return false;
                }
                var answertrue = question_list['question_list'][0]['answertrue'];
                var qid = question_list['question_list'][0]['id'];

                $.each(document.querySelectorAll('.xuanze_no'), function(e) {
                    if (this.getAttribute('data-id') == 2) {
                        _xuanze_no_list.push(this.parentNode.getAttribute('title'));
                    }
                });
                if (_xuanze_no_list.length == 1) {
                    layer.open({
                        content: '这是一道多选题，请至少选择2个答案！',
                        time: 2
                    });
                    return false;
                }
                var xuanze_no_str = _xuanze_no_list.join('');
                if (answertrue == xuanze_no_str) {
                    exam_choose_list.push({
                        'id': qid,
                        'chosen': _xuanze_no_list,
                        'is_true': 2
                    });
                    recordErrTrueQuestions(errq, trueq, qid, '2', '', ''); //记录正确
                } else {
                    exam_choose_list.push({
                        'id': qid,
                        'chosen': _xuanze_no_list,
                        'is_true': 1
                    });
                    recordErrTrueQuestions(errq, trueq, qid, '1', '', ''); //记录错题
                }
                Cookies.set('exam_choose_list', JSON.stringify(exam_choose_list));
            });
        }
    };

    // 点击弹出题目列表
    var questions_no_list = doc.getElementById('questions_no_list');
    var question_list_func = function() {
        var question_list = localStorage.getItem('question_list') ? JSON.parse(localStorage.getItem('question_list')) : [];
        var test_id_list = localStorage.getItem('test_id_' + ctype + '_' + stype) ? JSON.parse(localStorage.getItem('test_id_' + ctype + '_' + stype)) : [];
        var errq_list = localStorage.getItem('errq__') ? JSON.parse(localStorage.getItem('errq__')) : [];
        var trueq_list = localStorage.getItem('trueq__') ? JSON.parse(localStorage.getItem('trueq__')) : [];
        var qtotal = localStorage.getItem('qtotal') ? parseInt(localStorage.getItem('qtotal')) : 0;
        var curr_qid = question_list['question_list'][0]['id'];
        var html = "<div style='width:100%; text-align:center; margin:0px; padding:0px;' id='choosequestion'>";

        $.each(test_id_list, function(k, v) {
            if (curr_qid == v.id) {
                test_id_list[k]['status'] = '2';
            } else {
                test_id_list[k]['status'] = '1';
            }
            if (-1 != errq_list.indexOf(parseInt(v.id))) {
                test_id_list[k]['status'] = '4';
            }
            if (-1 != trueq_list.indexOf(parseInt(v.id))) {
                test_id_list[k]['status'] = '3';
            }
        });
        $.each(test_id_list, function(k, v) {
            switch (v.status) {
                case '1':
                    html += '<a data-id="' + v.id + '" class="choosequestion xuanze_init" style="">' + (k + 1) + '</a>';
                    break;
                case '2':
                    html += '<a data-id="' + v.id + '" class="choosequestion xuanze_choose">' + (k + 1) + '</a>';
                    break;
                case '3':
                    html += '<a data-id="' + v.id + '" class="choosequestion xuanze_right">' + (k + 1) + '</a>';
                    break;
                case '4':
                    html += '<a data-id="' + v.id + '" class="choosequestion xuanze_wrong">' + (k + 1) + '</a>';
                    break;
                default:
                    html += '<a data-id="' + v.id + '" class="choosequestion xuanze_init" style="">' + (k + 1) + '</a>';
                    break;
            }
        });
        var title = '<span class="dadui" style="color:#555;">正确：<b id="dadui_num" style="color:#18b4ed;">' + trueq_list.length + '</b> </span>&nbsp;&nbsp;<span class="dacuo">错误：<b id="dacuo_num" style="color:#f25e5e;">' + errq_list.length + '</b></span>&nbsp;&nbsp;<span class="weida" style="color:#555;">待答：<b id="weida_num" style="color:#999">' + (qtotal - errq_list.length - trueq_list.length) + '</b></span>';
        html += '</div>';
        layer.open({
            type: 1,
            content: html,
            anim: 0.2,
            title: title,
            style: 'position:fixed; bottom:0; left:0; width:100%; height:400px; overflow-y:scroll; border:none;'
        });

        //选择题目
        $('#choosequestion').on('tap', '.choosequestion', function(e) {
            choose_list.length = 0;
            //record single question
            var xuanze_no_list = [];
            var question_list = localStorage.getItem('question_list') ? JSON.parse(localStorage.getItem('question_list')) : [];
            var answertrue = question_list['question_list'][0]['answertrue'];
            var type = question_list['question_list'][0]['type'];
            var qid = question_list['question_list'][0]['id'];
            if (type == 1 || type == 2) {
                $.each(document.querySelectorAll('.xuanze_no'), function(e) {
                    if (this.getAttribute('data-id') == 2) {
                        xuanze_no_list.push(this.parentNode.getAttribute('title'));
                    }
                });
                if (xuanze_no_list.length != 0) {
                    var xuanze_no_str = xuanze_no_list.join('');
                    if (answertrue == xuanze_no_str) {
                        exam_choose_list.push({
                            'id': qid,
                            'chosen': xuanze_no_list,
                            'is_true': 2
                        });
                        recordErrTrueQuestions(errq, trueq, qid, '2', '', ''); //记录正确
                    } else {
                        exam_choose_list.push({
                            'id': qid,
                            'chosen': xuanze_no_list,
                            'is_true': 1
                        });
                        recordErrTrueQuestions(errq, trueq, qid, '1', '', ''); //记录错题
                    }
                    Cookies.set('exam_choose_list', JSON.stringify(exam_choose_list));
                }
            }
            var data_id = this.getAttribute('data-id');
            var page = this.innerText;
            layer.closeAll();
            localStorage.setItem('temporary_page', page);
            getPractises(this.getAttribute('data-id'), 'v2', 'exam', 'question_list.php');
            var question_list = JSON.parse(localStorage.getItem('question_list'));
            question_list.page = page;
            question_list.qtotal = question_ids.length;
            doc.getElementById('qtotal').innerHTML = question_ids.length;
            doc.getElementById('qid').innerHTML = page;
            var colle = localStorage.getItem('colle') ? JSON.parse(localStorage.getItem('colle')) : [];
            var curr_id = question_list['question_list'][0]['id'];
            var _exam_choose_list = Cookies.get('exam_choose_list') ? JSON.parse(Cookies.get('exam_choose_list')) : [];
            template_single_render(question_list, _exam_choose_list, 'question', 'question_temp');
        })
    };
    questions_no_list.addEventListener('tap', question_list_func, false);

    $('#question').on('tap', '.mui-ul-click .mui-table-view-cell', choose_answer);

    // 开始暂停
    var time_button = doc.getElementById('time_button');
    time_button.addEventListener('tap', function() {
        //is_start 标记当前的开始与否的状态
        // 1 表示成功启动
        if (is_start == 1) {
            is_start = 0;
        } else {
            is_start = 1;
        }
        if (is_start) {
            time_button.innerHTML = '<span class="mui-icon iconfont">&#xe63f;</span><span class="mui-tab-label">暂停</span>';
            next.addEventListener('tap', next_func, false);
            next.removeAttribute('disabled');
            questions_no_list.addEventListener('tap', question_list_func, false);
            questions_no_list.removeAttribute('disabled');
            startTimer();
        } else {
            var index = layer.open({
                content: '<p style="color:#333; line-height:30px; font-size:1rem;">你当前时间是 <span style="color:#18b4ed;">' + exam_times.innerHTML + '</span>，正在暂停考试，请注意时间！</p>',
                shadeClose: false,
                shade: 1,
                btn: ['<a style="color:#18b4ed;">开始做题</a>'],
                end: function(index) {
                    time_button.innerHTML = '<span class="mui-icon iconfont">&#xe63f;</span><span class="mui-tab-label">暂停</span>';
                    next.addEventListener('tap', next_func, false);
                    next.removeAttribute('disabled');
                    questions_no_list.addEventListener('tap', question_list_func, false);
                    questions_no_list.removeAttribute('disabled');
                    startTimer();
                    is_start = 1;
                }
            });
            time_button.innerHTML = '<span class="mui-icon iconfont">&#xe63e;</span><span class="mui-tab-label">开始</span>';
            next.removeEventListener('tap', next_func, false);
            next.setAttribute('disabled', true);
            questions_no_list.removeEventListener('tap', question_list_func, false);
            questions_no_list.setAttribute('disabled', true);
            clearTimeout(i);
        }
    });

    //找到所有不包括自己的兄弟节点
    function sibling(elem) {
        var r = [];
        var n = elem.parentNode.firstChild;
        for (; n; n = n.nextSibling) {
            if (n.nodeType === 1 && n !== elem) {
                r.push(n);
            }
        }

        return r;
    }
    //取消选中其它兄弟节点
    function cancelSiblings(obj) {
        for (var i = 0; i < obj.length; i++) {
            obj[i].querySelector('.xuanze_no').className = 'xuanze_no';
            obj[i].querySelector('.xuanze_no').setAttribute('data-id', '1');
        }
    }

})(mui, document);

// 交卷后分享
function shareAjax(version, folder, aname, params, score, ctype, stype) {
    mui.ajax({
        type: "post",
        url: host + "/" + version + "/" + folder + "/" + aname,
        data: params,
        async: false,
        dataType: "json",
        success: function(data) {
            if (data.code == 200) {
                location.href = root_path + 'exam/share-' + sid + '-' + ctype + '-' + stype + '-' + score + '-' + os + '.html';
            } else {
                mui.toast(data.data);
                location.reload();
                return false;
            }
        },
        error: function() {
            mui.toast('网络不给力，请重试');
            location.href = root_path + 'exam/index-' + sid + '-' + os + '.html';
            return false;
        }
    });
}

// 交卷ajax
function handoverAjax(version, folder, aname, params) {
    mui.ajax({
        type: "post",
        url: host + "/" + version + "/" + folder + "/" + aname,
        data: params,
        async: false,
        dataType: "json",
        success: function(data) {
            if (data.code == 200) {
                var content_html = '<p style="color:#333; line-height:30px; font-size:1rem;">成绩保存成功，还有勇气挑战更高分吗？</p>';
                var onemore = layer.open({
                    content: content_html,
                    btn: ['<a style="color:#18b4ed;">好的，来吧</a>', '<a style="color:#18b4ed;">今天不了</a>'],
                    yes: function(onemore) {
                        layer.close(onemore);
                        location.reload();
                    },
                    no: function(onemore) {
                        layer.close(onemore);
                        if (os == 'web') {
                            location.href = root_path + 'exam/index-' + sid + '-' + os + '.html';
                        } else {
                            history.back(-2);
                        }

                    }
                });
            } else {
                mui.toast(data.data);
                location.reload();
                return false;
            }
        },
        error: function() {
            mui.toast('网络不给力，请重试');
            location.href = root_path + 'exam/index-' + sid + '-' + os + '.html';
            return false;
        }
    });
}
//var total = 2700; // 45 min
var total = 0;
if (stype == 1) {
    total = 2700; //秒数 45 min
    //  total = 10; //秒数 45 min
} else if (stype == 4) {
    total = 1800; // 秒数 30 min
}
var time = total;
var i;

function startTimer() {
    var min = 0,
        sec = 0;
    if (time > 0) {
        min = Math.floor(time / 60);
        sec = Math.floor(time) - (min * 60);
    }
    if (min <= 9) {
        min = '0' + min;
    }
    if (sec <= 9) {
        sec = '0' + sec;
    }
    rest_time = min + ':' + sec;
    exam_times.innerHTML = rest_time;
    time = time - 1;
    if (time == 0) {
        var score = localStorage.getItem('score');
        var terrq = localStorage.getItem('terrq');
        var width = (document.body.clientWidth - 200) / 2;
        var height = (document.body.scrollHeight - 300) / 2;
        //      var html = '<div style="margin:0px auto; width:100%;"><div style="position:absolute; top:'+height+'px; left:'+width+'px; width:200px; text-align:center;background:#fff; height:200px; border-radius:10px; opacity:0.8; padding-top:50px;"><span style="color:red; font-size:2rem; margin-bottom:60px; display:inline-block;">    分数：'+score+'分</span><br /><button class="mui-btn mui-btn-green" style="padding:5px 20px;" onclick="location.reload();">重考</button>&nbsp;&nbsp;<button onclick="location.href=\'errorq.php\'" class="mui-btn mui-btn-red" style="padding:5px 20px;">错题</button></div><img src="../assets/images/back.png" width="100%" height="'+document.body.scrollHeight+'px" /></div>';
        //      var pageii = layer.open({
        //          type: 1,
        //          content: html,
        //          style: 'position:fixed; left:0; top:0; width:100%; height:100%; border:none; '
        //      });
        // score
        var version = 'v2';
        var score = 0;
        var folder = 'exam';
        var aname = 'add_exam_record.php';
        var terrq = localStorage.getItem('errq__') ? JSON.parse(localStorage.getItem('errq__')) : [];
        var _trueq = localStorage.getItem('trueq__') ? JSON.parse(localStorage.getItem('trueq__')) : [];
        if (stype == 1) {
            score = _trueq.length;
        } else {
            score = _trueq.length * 2;
        }
        if (score == 0) {
            var index = layer.open({
                content: '<p style="color:#333; line-height:30px; font-size:1rem;">你当前分数是 <span style="color:#18b4ed;">0</span>，不可交卷！</p>',
                shadeClose: false,
                shade: 1,
                btn: ['<a style="color:#18b4ed;">重新做题</a>'],
                end: function(index) {
                    location.reload();
                }
            });
            return false;
        }
        var params = {
            'user_id': uid,
            'error_exam_id': terrq.join(','),
            'score': score,
            'license': ctype,
            'subject': stype,
            'total_time': total - time,
            'school_id': sid,
            'os': os,
        };
        handoverAjax(version, folder, aname, params);
        return false;
    }
    i = setTimeout("startTimer()", 1000);
}