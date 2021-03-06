var loading = document.getElementById('loading');
var qidskey = '';
var qids = '';
var vm = new Vue({
    el: '#app',
    data: {
    	show: true,
    	seen: false,
        title: title,
        from: from,
       	currentSubjectid: currentSubjectid,
        currentLicenseid: currentLicenseid,
        quesids_url: quesids_url,
        queslist_url: queslist_url,
        colle_url: colle_url,
        comment_url: comment_url,
        ansSeen: false,
        commSeen: false,
        currentChoose: 0,
        currentAns: 0,
        qidList:[],
        qidsnoList: [],
        list: [],
        count: 0,
        current_count: 1,
        currentQuesInfo: [],
        page: 1,
        limit: 25,
        rigids: localStorage.getItem('errids') ? localStorage.getItem('errids').split(',') : [],
        errids: localStorage.getItem('errids') ? localStorage.getItem('errids').split(',') : [],
        token: localStorage.getItem('token') ? localStorage.getItem('token') : '',
        nextprevSeen: true,
        buttonSeen: false,
        commentList: []
    },
    created: function() {
 		/*qidskey = this.currentSubjectid+'-currqindex-from'+this.from;
 		var currentquespage = this.currentSubjectid+'-currqpage-from'+this.from;
		var currentquesid = this.currentSubjectid+'-currqid-from'+this.from;
		var quesids = this.currentSubjectid+'-qids-from'+this.from;
    	this.current_count = localStorage.getItem(qidskey) ? localStorage.getItem(qidskey) : 1;
    	this.qidList = localStorage.getItem(quesids) ? localStorage.getItem(quesids).split(',') : [];
    	this.page = localStorage.getItem(currentquespage) ? localStorage.getItem(currentquespage) : 1;
    	var currqid = localStorage.getItem(currentquesid) ? localStorage.getItem(currentquesid) : this.currentQuesInfo.id;*/
    	this.quesidsAjax();
    },
    methods: {
    	quesidsAjax: function() {
    		mui.ajax(this.quesids_url, {
                 data:{
                 	car_type: this.currentLicenseid,
                 	course: this.currentSubjectid,
                 },
                 dataType:'json',
                 type:'get',
                 timeout:10000,
                 beforeSend: function() {
                     var spinner = new Spinner(opts).spin(loading);
                 },
                 success:function(data){
                 	 loading = document.getElementById('loading');
					 loading.style.display = "none";
                     if(data.code == 200) {
                     	vm.qidList = data.data.list;
                     	vm.qidList.forEach(function(val, key) {
                     		if(vm.current_count == key + 1) {
                     			vm.qidsnoList.push({id: val, isChoose: false, isRight: false, isActive: true});
                     		} else {
                     			vm.qidsnoList.push({id: val, isChoose: false, isRight: false, isActive: false});
                     		}
                     	});
                 		qidskey = vm.currentSubjectid+'-qids-from'+vm.from;
             			localStorage.setItem(qidskey, data.data.list);
             			vm.count = data.data.list.length;
             			qids = vm.qidList.slice(0, vm.limit);
			 			vm.queslistAjax(qids.join(','), vm.limit);
                     } else {
                         mui.toast(data.msg)
                     }
                 },
                 error:function(xhr,type,errorThrown){
                 	 var loading = document.getElementById('loading');
                     loading.parentNode.removeChild(loading);
                     mui.toast('网络错误，请检查网络');
                     return false;
                 }
         	});
    	},
    	queslistAjax: function(qids, limit) {
			var loading = document.getElementById('loading');
		 	loading.style.display = "block";
		 	this.nextprevSeen = false;
    		mui.ajax(this.queslist_url, {
                 data:{
                 	question_ids: qids,
                 	limit: limit,
                 },
                 dataType: 'json',
                 type: 'post',
                 timeout: 10000,
                 async: true,
                 beforeSend: function() {
                     var spinner = new Spinner(opts).spin(loading);
                 },
                 success:function(data){
                     vm.nextprevSeen = true;
                     if(data.code == 200) {
                     	data.data.list.forEach(function(val, key) {
                     		val.chooseAns = ''; /*选择的答案*/
                     		val.chooseansSeen = false; /*选择的答案显示*/
                     		val.isChoose = false; /*是否选择*/
                     		val.options.forEach(function(v, k) {
                     			v.isChoose = false;
                     		});
                     		val.answerList = val.restore_answer.split('');
                     		vm.list.push(val);
                     	});
                     	if(data.data.list.length > 0) {
                     		vm.currentQuesInfo = data.data.list[0];
 							var currentquesid = vm.currentSubjectid+'-currqid-from'+vm.from;
            				localStorage.setItem(currentquesid, vm.currentQuesInfo.id);
                     	} else {
                     		mui.alert('暂无题库列表~.~');
                     	}
                     } else {
                         mui.toast(data.msg)
                     }
                     loading = document.getElementById('loading');
					 loading.style.display = "none";
                 },
                 error:function(xhr,type,errorThrown){
                     vm.nextprevSeen = true;
                     loading = document.getElementById('loading');
					 loading.style.display = "none";
                     mui.toast('网络错误，请检查网络');
                     return false;
                 }
         	});
    	},
    	commentAjax: function() {
    		mui.ajax(this.comment_url, {
             	data:{
                 	question_id: vm.currentQuesInfo.id,
                 },
                 dataType: 'json',
                 type: 'get',
                 timeout: 10000,
                 async: true,
                 success:function(data){
                 	vm.commentList = data.data.data;
                 },
                 error:function(xhr,type,errorThrown){
                     mui.toast('网络错误，请检查网络');
                     return false;
                 }
         	});
    	},
        handleCollect: function(qid) {
        	if(this.token == '') {
        		mui.toast('请登录');
        		return false;
        	}
        	mui.ajax(this.colle_url, {
                 data:{
                 	questions_id: qid,
                 	chapter_id: this.currentQuesInfo.chapter_id,
                 	token: this.token,
                 },
                 dataType: 'json',
                 type: 'post',
                 timeout: 10000,
                 async: false,
                 success:function(data){
                 	mui.toast(data.msg);
                 },
                 error:function(xhr,type,errorThrown){
                     mui.toast('网络错误，请检查网络');
                     return false;
                 }
         	});
        },
        nextQuestion: function() {
        	if(this.current_count < this.qidList.length) {
            	this.qidsnoList.forEach(function(val, key) {
            		val.isActive = false;
            	});
            	this.qidsnoList[this.current_count].isActive = true;
        		this.current_count = this.current_count + 1;
        		this.currentQuesInfo = this.list[this.current_count - 1];
        		if(this.list[this.current_count - 1].isChoose) {
        			this.ansSeen = true;
	            	this.commSeen = true;
            	} else {
					this.ansSeen = false;
	            	this.commSeen = false;
            	}
            	this.buttonSeen = false;
        		if((this.page) * (this.limit) - this.current_count  == 1) {
         			qids = this.qidList.slice((this.limit) * (this.page), (this.limit) * (this.page + 1));
		 			this.queslistAjax(qids.join(','), this.limit);
         			this.page++;
        		}
        		/*存储当前count*/
         		var currentqueskey = this.currentSubjectid+'-currqindex-from'+this.from;
         		var currentquespage = this.currentSubjectid+'-currqpage-from'+this.from;
     			var currentquesid = this.currentSubjectid+'-currqid-from'+this.from;
            	localStorage.setItem(currentqueskey, this.current_count);
            	localStorage.setItem(currentquespage, this.page);
            	localStorage.setItem(currentquesid, this.currentQuesInfo.id);
        	} else {
        		mui.toast('已是最后一题');
        		return false;
        	}
        },
        prevQuestion: function() {
        	if(this.current_count > 1) {
        		this.qidsnoList.forEach(function(val, key) {
            		val.isActive = false;
            	});
            	this.qidsnoList[this.current_count - 2].isActive = true;
        		this.current_count = this.current_count - 1;
        		this.currentQuesInfo = this.list[this.current_count - 1];
            	if(this.list[this.current_count - 1].isChoose) {
					this.ansSeen = true;
	            	this.commSeen = true;
            	} else {
					this.ansSeen = false;
	            	this.commSeen = false;
            	}
            	this.buttonSeen = false;
            	/*存储当前count*/
         		var currentqueskey = this.currentSubjectid+'-currqindex-from'+this.from;
         		var currentquespage = this.currentSubjectid+'-currqpage-from'+this.from;
     			var currentquesid = this.currentSubjectid+'-currqid-from'+this.from;
            	localStorage.setItem(currentqueskey, this.current_count);
            	localStorage.setItem(currentquespage, this.page);
            	localStorage.setItem(currentquesid, this.currentQuesInfo.id);
        	} else {
        		return false;
        	}
        },
        chooseQuestion: function(tag) {
        	var flag = false;
        	this.list[this.current_count - 1].chooseAns = tag;
        	this.list[this.current_count - 1].chooseansSeen = true;
        	this.list[this.current_count - 1].isChoose = true;
        	this.qidsnoList[this.current_count - 1].isChoose = true;
        	this.currentQuesInfo = this.list[this.current_count - 1];
        	if(tag == this.currentQuesInfo.restore_answer) {
        		this.rigids.push(this.currentQuesInfo.id);
        		localStorage.setItem('rigids', this.rigids);
        		setTimeout(function(){
        			mui.toast('下一题');
        			vm.showQuesInfo(vm.current_count, vm.currentQuesInfo.id);
        		}, 200);
        		flag = true;
        	} else {
        		this.errids.push(this.currentQuesInfo.id);
        		localStorage.setItem('errids', this.errids);
        		this.ansSeen = true;
        		this.commSeen = true;
        	}
        	this.qidsnoList[this.current_count - 1].isRight = flag;
        	this.commentAjax();
        },
        handleTestTime: function() {
        	
        },
        handleSettings: function() {
        	
        },
        showQuesNo: function() {
        	this.seen = true;
        	Velocity(document.getElementById("quesno-list"), {
				bottom: '0px',
			}, {
			    duration: 0
			});
        },
        hideQuesNo: function() {
        	this.seen = false;
			Velocity(document.getElementById("quesno-list"), {
				bottom: '-360px',
			}, {
			    duration: 0
			});            	
        },
        hideShade: function() {
        	this.seen = false;
        	this.hideQuesNo();
        },
        showQuesInfo: function(index, id) {
        	var flag = 1;
			this.ansSeen = false;
          	this.commSeen = false;
        	this.buttonSeen = false;
        	this.current_count = parseInt(index) + 1;
        	this.hideQuesNo();
    		if(this.current_count > this.list.length) {
    			/*点击的当前index不存在已经获取的题库列表中*/
    			flag = 2;
    		}
        	if(flag == 2) {
        		/*获取当前选择的id之前的不存在于list中的所有id以及分页所得的最后id之间对应的题库列表*/
        		var last_index = parseInt((index+1) / (this.limit)) + 1;
        		qids = this.qidList.slice(this.list.length, last_index * (this.limit));            		
        		this.page = last_index;
        		if(qids.length == 0) {
        			mui.toast('题号获取错误，请刷新');
        			return false;
        		} else {
        			this.queslistAjax(qids.join(','), qids.length);
        		}
        	} else {
         		this.currentQuesInfo = this.list[index];
		      	if(this.list[index].isChoose) {
					this.ansSeen = true;
		          	this.commSeen = true;
		      	} else {
					this.ansSeen = false;
		          	this.commSeen = false;
		      	}
        	}
        	this.qidsnoList.forEach(function(val, key) {
        		val.isActive = false;
        	});
        	this.qidsnoList[index].isActive = true;
        	/*存储当前count*/
     		var currentqueskey = this.currentSubjectid+'-currqindex-from'+this.from;
     		var currentquespage = this.currentSubjectid+'-currqpage-from'+this.from;
     		var currentquesid = this.currentSubjectid+'-currqid-from'+this.from;
        	localStorage.setItem(currentqueskey, this.current_count);
        	localStorage.setItem(currentquespage, this.page);
        	localStorage.setItem(currentquesid, id);
        	
        },
        delAllRecord: function() {
        	mui.confirm('确定清空顺序练习的做题记录吗？', '', ['确定', '取消'], function(e) {
        		if(e.index == 0) {
        			vm.rigids = [];
        			vm.errids = [];
        			vm.hideQuesNo();
        			localStorage.removeItem('errids');
        			localStorage.removeItem('rigids');
        		}
        	}, 'div');
        },
        chooseMultiQuestion: function(index, tag) {
        	var flag= 1;
        	if(this.currentQuesInfo.options[index].isChoose) {
        		this.currentQuesInfo.options[index].isChoose = false;
        	} else {
        		this.currentQuesInfo.options[index].isChoose = true;
        	}
        	this.currentQuesInfo.options.forEach(function(val, key) {
        		if(val.isChoose) {
        			flag = 2;
        		}
        	});
        	if(flag == 1) {
        		this.buttonSeen = false;
        	} else {
        		this.buttonSeen = true;
        	}
        },
        submitMultiQuestion: function() {
        	this.buttonSeen = false;
        	var answer_list = this.currentQuesInfo.restore_answer.split('');
        	var chooseAnsList = [];
        	var flag = false;
        	this.currentQuesInfo.options.forEach(function(val, key) {
        		if(val.isChoose) {
        			chooseAnsList[key] = val.tag;
        		}
        	});
        	var tag = chooseAnsList.join('');
        	this.chooseQuestion(tag);
        },
        submitComment: function() {
        	
        },
    }
});