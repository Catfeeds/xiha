var $ = mui, doc = document;

// 记录错误和正确的题目
function recordErrTrueQuestions(errq, trueq, qid, type, ctype, stype) {
	if(typeof errq != 'object' || typeof trueq != 'object') {
		return false;
	}
	switch(type) {
		case '1':
			//记录错题
			if(-1 == errq.indexOf(parseInt(qid))) {
				errq.push(qid);
			}
			if(-1 != trueq.indexOf(parseInt(qid))) {
				trueq.remove(qid);
			}
			break;
		case '2':
			//记录正确
			if(-1 == trueq.indexOf(parseInt(qid))) {
				trueq.push(qid);
			}
			if(-1 != errq.indexOf(parseInt(qid))) {
				errq.remove(qid);
			}
			break;
		default:
			return false;
			break;
	}
	localStorage.setItem('errq_'+ctype+'_'+stype, JSON.stringify(errq));
	localStorage.setItem('trueq_'+ctype+'_'+stype, JSON.stringify(trueq));
	return true;
}

// 根据题号获取题目
function getPractises(_question_ids, version, folder, aname) {
	var param = {
        'question_ids':_question_ids
    };
    var all_question_list = localStorage.getItem('all_question_list') ? JSON.parse(localStorage.getItem('all_question_list')) : [];
	var res = getPractiseByQid(all_question_list, _question_ids); // 从本地存储获取题目
	if(res.length != 0 && typeof res == 'object' && res != false) {
		var _question_list = {
			'question_list':res
		};
		localStorage.setItem('question_list', JSON.stringify(_question_list));
		return false;
	} else {
        $.ajax({
            type:"post",
            url:host+"/"+version+"/"+folder+"/"+aname,
            async:false,
            dataType:"json",
            data:param,
            success:function(data) {
                if(200 == data.code) {
                    var question_list = data.data;
                    localStorage.setItem('question_list', JSON.stringify(question_list));
                } else {
                	$.toast(data.data);
                	localStorage.setItem('question_list', '');
                	return false;
                }
        	},
            error:function() {
                localStorage.setItem('question_list', '');
                $.toast('网络错误，请检查网络');
                return false;
            }
        });
	}		
		
}

// 根据题号获取本地题目列表
function getPractiseByQid(q_list, qids) {
	if(typeof q_list != 'object' || q_list == null || q_list.length == 0) {
		return false;
	}
	var qids_obj = qids.split(',');
	var list = [];
	for(var i = 0; i < q_list['question_list'].length; i++) {
		for(var j = 0; j < qids_obj.length; j++) {
			if(q_list['question_list'][i]['id'] == qids_obj[j]) {
				list.push(q_list['question_list'][i]);
			}
		}
	}
	return list;
}

// 一键缓存所有题目
function getAllPractises(version, folder, aname, ctype, stype) {
	var param = {
        ctype: ctype,
        stype: stype
    };
    var all_question_list = '';
    $.ajax({
        type:'post',
        url:host+"/"+version+"/"+folder+"/"+aname,
        data:param,
        async:false,
        dataType:'json',
        success: function(data){
        	cacheall.innerText = '一键缓存';
        	cacheall.disabled = false;
            if (data.code == 200) {
            	all_question_list = JSON.stringify(data.data);
//              localStorage.setItem('all_question_list', JSON.stringify(data.data));
            } else {
            	$.toast(data.data);
//          	localStorage.setItem('all_question_list', '');
//          	return false;
            }
        },
        error: function(data){
        	$.toast('网路错误，请检查网络');
//      	localStorage.setItem('all_question_list', '');
//      	return false;
        }
    });
    try{
	    localStorage.setItem('all_question_list', all_question_list);
	    question_list = localStorage.getItem('all_question_list');
	    if (question_list != null) {
	        return JSON.parse(question_list);
	    }
    }catch(e){
    	$.toast('您可能处于无痕浏览，无法为您保存');
    	return false;
    }
    return false;
}

// 分页获取题目号
function getQuestionIdsByPage(page, limit, sequence_no_list) {
    if(typeof sequence_no_list != 'object' || sequence_no_list.length == 0) {
        return false;
    }
   	var p = page <= 0 ? 1 : page;
    var start = (p - 1) * limit;
    var list = [];
    for(var i = start; i < limit * p; i++) {
        list.push(sequence_no_list[i]['id']);
    }
    return list;
}
// 模板插入展示（包括收藏按钮的变化）
function template_render(colle, curr_id, question_list, dest_id, tmp_id) {
	var collection = doc.getElementById('collection');
	if(typeof question_list != 'object') {
		return false;
	}
	if(colle.indexOf(parseInt(curr_id)) != -1) {
		collection.style.color = '#ea8010';
		collection.querySelector('.iconfont').innerHTML = '&#xe635;';
	} else {
		collection.style.color = '#5c5c5c';
		collection.querySelector('.iconfont').innerHTML = '&#xe636;';
	}
    var tpl = doc.getElementById(tmp_id).innerHTML;
    var html = juicer(tpl, question_list);
    doc.getElementById(dest_id).innerHTML = html; 
}

// 单纯模板展示
function template_single_render(question_list, exam_choose_list, dest_id, tmp_id) {
	var all_answer_list = [];
	var chosen_list = [];
	var _chosen_list = [];
	var not_chosen_list = [];
	if(typeof question_list != 'object' || typeof exam_choose_list != 'object') {
		return false;
	}
	if(exam_choose_list.length > 0) {
		var answertrue_list = question_list['question_list'][0]['answertrue'].split('');
		var question_id = question_list['question_list'][0]['id'];
		var type = question_list['question_list'][0]['type'];
		switch(type) {
			case 1:
				all_answer_list = ["1", "2"];
				break;
			case 2:
			case 3:
				all_answer_list = ["1", "2", "3", "4"];
				break;
			default:
				all_answer_list = [];
				break;
		}
		$.each(exam_choose_list, function(e, v) {
			if(v.id == question_id) {
				_chosen_list = v.chosen;
				question_list['question_list'][0]['is_true'] = v.is_true;
			}
		});
		if(_chosen_list.length > 0) {
			$.each(_chosen_list, function(k, t) {
				if(-1 != answertrue_list.indexOf(t)) {
					chosen_list[k] = {'choose_id':t,'is_true':2}; 
				} else {
					chosen_list[k] = {'choose_id':t,'is_true':1}; 
				}					
			});
			$.each(all_answer_list, function(m, n) {
				if(-1 == _chosen_list.indexOf(n)) {
					if(-1 != answertrue_list.indexOf(n)) {
						not_chosen_list[m] = {'choose_id':n,'is_true':2};
					} else {
						not_chosen_list[m] = {'choose_id':n,'is_true':0};
					}
				}
			});
		}
		chosen_list = chosen_list.concat(not_chosen_list);
		chosen_list.sort(getSortFun('asc', 'choose_id'));
        if (chosen_list.length > 0) {
		    question_list['question_list'][0]['chosen'] = chosen_list;
        } else {
		    question_list['question_list'][0]['chosen'] = [{'choose_id':0, 'is_true':0}];
        }   
	} else {
		question_list['question_list'][0]['chosen'] = [{'choose_id':0, 'is_true':0}];
		question_list['question_list'][0]['is_true'] = 0;
	}
    var tpl = doc.getElementById(tmp_id).innerHTML;
    var html = juicer(tpl, question_list);
    doc.getElementById(dest_id).innerHTML = html; 
}

// 排序函数
function getSortFun(order, sortBy) {
  var ordAlpah = (order == 'asc') ? '>' : '<';
  var sortFun = new Function('a', 'b', 'return a.' + sortBy + ordAlpah + 'b.' + sortBy + '?1:-1');
  return sortFun;
}
// 拼接所有的题目ID
function getAllQuestionIds(sequence_no_list) {
	if(typeof sequence_no_list != 'object' || sequence_no_list.length == 0) {
        return false;
    }
	var list = [];
	for(var i = 0; i < sequence_no_list.length; i++) {
		list.push(sequence_no_list[i]['id']);
	}
	return list;
}

// 获取题号列表
function getQuestionId(question_ids, version, folder, aname, ctype, stype, localstorage_name){
    if (typeof ctype != 'string' || typeof stype != 'string') {
        return false;
    }
    //车型
    if(['C1', 'A1', 'A2', 'D'].indexOf(ctype) == -1){
        return false;
    }
    //科目
    if (['1', '4'].indexOf(stype) == -1) {
        return false;
    }
    if (question_ids != null) {
        return JSON.parse(question_ids);
    } else {
        var param = {
            ctype: ctype,
            stype: stype
        };
        $.ajax({
            type:'post',
        	url:host+"/"+version+"/"+folder+"/"+aname,
            data:param,
            async:false,
            dataType:'json',
            success: function(data){
                if (data.code == 200) {
                    localStorage.setItem(localstorage_name+'_'+ctype+'_'+stype, JSON.stringify(data.data.question_ids));
                } else {
                	return false;
                }
            },
            error: function(data){
            	$.toast('网络错误，请检查网络');
            	return false;
            }
        });
    }
    question_ids = localStorage.getItem(localstorage_name+'_'+ctype+'_'+stype);
    if (question_ids != null) {
        return JSON.parse(question_ids);
    }
    return false;
}

// 获取随机练习的题号
// 获取题号列表
function getRandomQuestionId(version, folder, aname, ctype, stype, localstorage_name){
    if (typeof ctype != 'string' || typeof stype != 'string') {
        return false;
    }
    //车型
    if(['C1', 'A1', 'A2', 'D'].indexOf(ctype) == -1){
        return false;
    }
    //科目
    if (['1', '4'].indexOf(stype) == -1) {
        return false;
    }
    var param = {
        ctype: ctype,
        stype: stype
    };
    $.ajax({
        type:'post',
    	url:host+"/"+version+"/"+folder+"/"+aname,
        data:param,
        async:false,
        dataType:'json',
        success: function(data){
            if (data.code == 200) {
                localStorage.setItem(localstorage_name+'_'+ctype+'_'+stype, JSON.stringify(data.data.question_ids));
            } else {
            	return false;
            }
        },
        error: function(data){
        	return false;
        }
    });

    question_ids = localStorage.getItem(localstorage_name+'_'+ctype+'_'+stype);
    if (question_ids != null) {
        return JSON.parse(question_ids);
    }
    return false;
}

// 删除数组中指定的值
Array.prototype.remove = function(val) {
	var index = this.indexOf(val);
	if (index > -1) {
		this.splice(index, 1);
	}
};

