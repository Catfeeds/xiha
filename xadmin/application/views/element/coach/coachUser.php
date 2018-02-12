<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">
		<div class="gx-search">
			<el-form :inline="true" :model="search" class="demo-form-inline">
				<el-form-item>
					<el-select v-model="search.status" placeholder="按绑定状态">
						<el-option v-for="item in status_options" :label="item.label" :value="item.value"></el-option>
					</el-select>
				</el-form-item>
				<el-form-item label="关键词">
					<el-input style="width: 400px" v-model="search.keywords" v-if="school_id == ''" placeholder="学员信息，教练信息" ></el-input>
					<el-input style="width: 400px" v-model="search.keywords" v-if="school_id != ''" placeholder="学员信息，教练信息" ></el-input>
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
				<el-table-column fixed type="selection" width="50"></el-table-column>
				<el-table-column prop="id" label="ID" sortable width="80"></el-table-column> 
				<el-table-column prop="real_name" label="学员信息" show-overflow-tooltip>
					<el-table-column prop="real_name" label="真实姓名" min-width="150" show-overflow-tooltip></el-table-column>
					<el-table-column prop="user_name" label="昵称" min-width="150" show-overflow-tooltip></el-table-column>
					<el-table-column prop="user_phone" label="联系方式" min-width="150" show-overflow-tooltip></el-table-column>
				</el-table-column> 
				<el-table-column prop="real_name" label="教练信息" width="" show-overflow-tooltip>
					<el-table-column prop="school_name" label="所属驾校" min-width="150" v-if="school_id == ''" show-overflow-tooltip></el-table-column>
					<el-table-column prop="coach_name" label="姓名" min-width="150" show-overflow-tooltip></el-table-column>
					<el-table-column prop="coach_phone" label="联系方式" min-width="150" show-overflow-tooltip></el-table-column>
				</el-table-column> 
				<el-table-column prop="bind_status" label="绑定状态" min-width="160">
					<template scope="scope">
						<el-tag type="success" v-if="parseInt(scope.row.bind_status) == 1">已绑定</el-tag>
				        <el-tag type="warning" v-else-if="parseInt(scope.row.bind_status) == 2">解除绑定</el-tag>
				        <el-tag type="blue" v-else-if="parseInt(scope.row.bind_status) == 3">学员申请绑定教练</el-tag>
				        <el-tag type="danger" v-else-if="parseInt(scope.row.bind_status) == 4">教练申请绑定学员</el-tag>
				        <el-tag type="black" v-else-if="parseInt(scope.row.bind_status) == 5">学员申请解绑教练</el-tag>
				        <el-tag type="sliver" v-else-if="parseInt(scope.row.bind_status) == 6">教练申请解绑学员</el-tag>
				        <el-tag type="gray" v-else>未知状态</el-tag>
					</template>
				</el-table-column>
				<el-table-column prop="lesson_name" label="科目名称" width="120" ></el-table-column>
				<el-table-column prop="license_name" label="牌照名称" width="120" ></el-table-column>
				<el-table-column prop="addtime" label="创建时间" min-width="180" ></el-table-column>
				<el-table-column prop="updatetime" label="更新时间" min-width="180" ></el-table-column>
				<el-table-column label="操作" fixed="right" width="140">
					<template scope="scope">
						<el-dropdown trigger="click">
							<el-button class="el-dropdown-link" size="small" type="primary">设置<i class="el-icon-caret-bottom el-icon--right"></i></el-button>
							<el-dropdown-menu slot="dropdown" style="margin-right: -20px">
								<a @click="setCoachBindStatus(scope.row.id, 1)">
									<el-dropdown-item >已绑定</el-dropdown-item>
								</a>
								<a @click="setCoachBindStatus(scope.row.id, 2)">
									<el-dropdown-item >解除绑定</el-dropdown-item>
								</a>
								<a @click="setCoachBindStatus(scope.row.id, 3)">
									<el-dropdown-item >学员申请绑定教练</el-dropdown-item>
								</a>
								<a @click="setCoachBindStatus(scope.row.id, 4)">
									<el-dropdown-item >教练申请绑定学员</el-dropdown-item>
								</a>
								<a @click="setCoachBindStatus(scope.row.id, 5)">
									<el-dropdown-item >学员申请解绑教练</el-dropdown-item>
								</a>
								<a @click="setCoachBindStatus(scope.row.id, 6)">
									<el-dropdown-item >教练申请解绑学员</el-dropdown-item>
								</a>
							</el-dropdown-menu>
						</el-dropdown>
					</template>
				</el-table-column>
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
	var school_id = "<?php echo $school_id; ?>";
	var vm = new Vue({
		el: '#app',
		data: {
			refreshstatus: false,
			fullscreenLoading: true,
			list: [],
			multipleSelection: [],
			status_options: [
				{value: '', label: '不限绑定状态'},
				{value: '1', label: '已绑定'},
				{value: '2', label: '解除绑定'},
				{value: '3', label: '学员申请绑定教练'},
				{value: '4', label: '教练申请绑定学员'},
				{value: '5', label: '学员申请解绑教练'},
				{value: '6', label: '教练申请解绑学员'}
			],
			list_url: "<?php echo base_url('coach/coachUserAjax'); ?>",
			status_url: "<?php echo base_url('coach/setCoachBindStatus'); ?>",
			currentPage: 1,
			page_sizes: [10, 20, 30, 50, 100],
			page_size: 10,
			pagenum: 0,
			count: 0,
			search: {
				status: '',
				keywords: '',
			},
		},
		created: function() {
			var filter = {"p": this.currentPage, "status": this.search.status, "keywords": this.search.keywords,"s": this.page_size};
			this.listAjax(filter);
		},
		methods: {
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
							vm.currentPage = ret.data.p;
						}
					},
					error: function() {
						vm.fullscreenLoading = false;
						vm.refreshstatus = false;
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				})
			},
			setCoachBindStatus: function (id, status) {
				$.ajax({
					type: 'post',
					url: this.status_url,
					data: {id: id, status: status},
					dataType:"json",
					success: function(data) {		
						if(data.code == 200) {
							var filter = {"p": vm.currentPage, "status": vm.search.status, "keywords": vm.search.keywords,"s":vm.page_size};
							vm.listAjax(filter);		
							vm.messageNotice('success', data.msg);	
						} else {
							vm.messageNotice('warning', data.msg);			
						}
					},
					error: function() {							
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				});
			},
			delAjax: function(id) {
				$.ajax({
					type: 'post',
					url: this.del_url,
					data: {id:id},
					dataType:"json",
					success: function(data) {
						vm.fullscreenLoading = false;			
						if(data.code == 200) {
							var filter = {"p": vm.currentPage, "status": vm.search.status, "keywords": vm.search.keywords,"s": vm.page_size};
							vm.listAjax(filter);
							vm.messageNotice('success', data.msg);			
						}
					},
					error: function() {
						vm.fullscreenLoading = false;									
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				})
			},
			handleSizeChange: function (size) {
				this.page_size = size;
				var filter = {"p": this.currentPage, "status": this.search.status, "keywords": this.search.keywords,"s":this.page_size};
				this.listAjax(filter);
			},
			handleCurrentChange: function(val) {
				this.refreshstatus = true;
				this.currentPage = val;
				// window.history.pushState(null, null, '?p='+val+'&status='+this.search.status+'&star='+this.search.star+'&verify='+this.search.verify+'&kwords='+this.search.kwords+'&value='+this.search.value);
				var filter = {"p": this.currentPage, "status": this.search.status, "keywords": this.search.keywords,"s": this.page_size};
				this.listAjax(filter);
			},
			handleRefresh: function() {
				this.refreshstatus = true;
				var filter = {"p": this.currentPage, "s":this.page_size};
				this.listAjax(filter);
			},
			handleSearch: function () {
				var filter = {"p": this.currentPage, "status": this.search.status, "keywords": this.search.keywords,"s": this.page_size};
				vm.listAjax(filter);
			},
			handleDel: function(id, index, rows) {
				this.$confirm('此操作将永久删除该教练, 是否继续?', '提示', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning',
					callback: function(action) {
						if(action == 'confirm') {
							vm.delAjax(id);
							// rows.splice(index, 1);
							// vm.messageNotice('success', '删除成功!');
						} else {
							return false;
						}
					}
				});
			},
			handleSelectionChange: function(val) {
				this.multipleSelection = val;
			},
			handleAdd: function(e) {
				this.showLayer(e, '60%', 'rb', this.add_url);
			},
			handleEdit: function(e, id, index, row) {
				this.showLayer(e, '60%', 'lb', this.edit_url+'?id='+id);
			},
			handlePreview: function(e, id, index, row) {
				this.showLayer(e, '480px', 'rb', this.preview_url+'?id='+id);
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
			
		}
	})
</script>