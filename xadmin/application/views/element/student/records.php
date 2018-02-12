<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">
		<div class="gx-search">
			<el-form :inline="true" :model="search" class="demo-form-inline">
				<!-- <el-form-item>
					<el-select v-model="search.type">
						<el-option label="--不限关键词--" value=""></el-option>
						<el-option label="用户名" value="s_real_name"></el-option>
						<el-option label="手机号" value="s_phone"></el-option>
						<el-option label="身份证号" value="identity_id"></el-option>
					</el-select>
				</el-form-item>	 -->
				<el-form-item>
					<el-input v-if="school_id == '' " style="width: 300px; " v-model="search.keywords" placeholder="所属驾校，用户名，手机号，身份证号"></el-input>
					<el-input v-if="school_id != '' " style="width: 300px; " v-model="search.keywords" placeholder="用户名，手机号，身份证号"></el-input>
				</el-form-item>	
				<el-form-item>
					<el-button type="primary" icon="search" @click="handleSearch">搜索</el-button>
				</el-form-item>	
			</el-form>
		</div>

		<div class="gx-iframe-content">
			<div class="gx-iframe-operation" style="margin-bottom: 20px; border-radius: 4px;">
				<el-button type="success" type="small" style="float:right; line-height: 38px;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
			</div>

			<el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
				<el-table-column type="selection" width="55"></el-table-column>
				<el-table-column prop="id" label="ID" sortable width="80"></el-table-column> 
				<el-table-column prop="school_name" v-if="school_id == '' " label="所属驾校" width="150" v-if="school_id ==''"></el-table-column> 
				<el-table-column prop="realname" label="用户名" width="150"></el-table-column>
				<el-table-column prop="phone_num" label="手机号" width="150"></el-table-column>
				<el-table-column prop="identify_id" label="身份证" width="200"></el-table-column>
				<el-table-column prop="os" label="信息来源" width="120">
					<template scope="scope">
						<el-tag type="primary" v-if="scope.row.os == 'web'">web</el-tag>
				        <el-tag type="danger" v-else-if="scope.row.os == 'ios'">ios</el-tag>
				        <el-tag type="success" v-else-if="scope.row.os == 'android'">android</el-tag>
				        <el-tag type="primary" v-else>web</el-tag>
			      	</template>
				</el-table-column>
				<el-table-column prop="ctype" label="牌照" width="120">
					<template scope="scope">
						<span>{{ scope.row.ctype }}</span> / 
						<span style="color: red">{{ scope.row.car_type }}</span>
					</template>
				</el-table-column>

				<el-table-column prop="stype_name" label="考试科目" width="110">
					<template scope="scope">
						<span>{{ scope.row.stype_name }}</span> /  
						<span style="color: red">{{ scope.row.course_name }}</span>
					</template>
				</el-table-column>

				<el-table-column prop="exam_total_time" label="考试用时" width="120"></el-table-column>
				<el-table-column prop="addtime" label="交卷时间" width="180"></el-table-column>
				<el-table-column prop="score" label="考试成绩(分)" width="120"></el-table-column>
			</el-table>

			<!--page-->
            <div class="block" style="float: right; margin-top: 10px;">
				<el-pagination
					@size-change="handleSizeChange"
					@current-change="handleCurrentChange"
					:current-page="currentPage"
					:page-sizes="page_sizes"
					:page-size="page_size"
					layout="total, sizes, prev, pager, next, jumper"
					:total="count">
				</el-pagination>
			</div>
			<!--end page-->
		</div>
	</div>
</div>
<script>
	Vue.config.devtools = true;
	var school_id = "<?php echo $school_id?>";
	var vm = new Vue({
		el: '#app',
		data: {
			refreshstatus: false,
			fullscreenLoading: true,
			list: [],
			multipleSelection: [],
			list_url: "<?php echo base_url('student/recordsAjax'); ?>",
			preview_url: "<?php echo base_url('user/preview'); ?>",
			show_url: "<?php echo base_url('user/show'); ?>",
			currentPage: 1,
            page_sizes: [10, 20, 30, 50, 100],
            page_size: 10,
            pagenum: 0,
            count: 0,
			search: {
				keywords: ''
			},
		},
		created: function() {
			var filters = {"p": this.currentPage, "keywords": this.search.keywords, 's': this.page_size}
			this.listAjax(filters);
		},
		methods: {
			handleSearch: function () {
				var filters = {"p": this.currentPage, "keywords": this.search.keywords, 's': this.page_size};
				this.listAjax(filters);
			},
			handleSelectionChange: function(val) {
				this.multipleSelection = val;
			},
			handlePreview: function(e, id, index, row) {
				this.showLayer(e, '480px', 'rb', this.preview_url+'?id='+id);
			},
			handleSizeChange: function (size) {
                this.page_size = size;
				var filters = {"p": this.currentPage, "keywords": this.search.keywords, 's': this.page_size};
                this.listAjax(filters);
            },
			handleCurrentChange: function(val) {
				this.refreshstatus = true;
				this.currentPage = val;
				// window.history.pushState(null, null, '?p='+val+'&keywords='+this.search.keywords+'&s='+this.page_size);
				var filters = {"p": this.currentPage, "keywords": this.search.keywords, 's': this.page_size};
				this.listAjax(filters);
			},
			handleRefresh: function() {
				this.refreshstatus = true;
				var filters = {"p": this.currentPage, 's': this.page_size};
				this.listAjax(filters);
			},
			listAjax: function(param) {
				$.ajax({
					type: 'post',
					url: this.list_url,
					data: param,
					dataType:"json",
					async: true,
					success: function(ret) {
						// setTimeout(function() {
						vm.fullscreenLoading = false;			
						// }, 500);
						vm.refreshstatus = false;
						if(ret.code == 200) {
							vm.list = ret.data.list;
							vm.pagenum = ret.data.pagenum;
							vm.count = ret.data.count;
							vm.currentPage = param.p;
						}
					},
					error: function() {
						vm.fullscreenLoading = false;
						vm.refreshstatus = false;
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				})
			},
			messageNotice: function(type, msg) {
				this.$message({
					type: type,
					message: msg
				});
			},
			showLayer: function(e, width, offset, content) {
				layer.closeAll();
				layer.open({
					title: e.currentTarget.getAttribute('data-title')
					,offset: offset //具体配置参考：offset参数项
					,anim: -1
					,type: 2
					,area: [width ,'100%']
					,content: content
					,shade: 0.4 //不显示遮罩
					,shadeClose: false //不显示遮罩
					,maxmin: true
					,move: false
					,yes: function(){
						layer.closeAll();
					}
				});
			},
			handleShow: function(id, status) {
				$.ajax({
					type: 'post',
					url: this.show_url,
					data: {id:id, status:status},
					dataType:"json",
					success: function(data) {		
						if(data.code == 200) {
							var filters = {"p": this.currentPage, "keywords": this.search.keywords, 's': this.page_size};
							vm.listAjax(filters);
						} else {
							vm.messageNotice('warning', data.msg);			
						}
					},
					error: function() {							
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				});
			},
			
		}
	})
</script>