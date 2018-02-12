<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">
		<div class="gx-search">
			<el-form :inline="true" :model="search" class="demo-form-inline">
				<el-form-item>
					<el-input v-model="search.value" placeholder="按种类名称"></el-input>
				</el-form-item>
				<el-form-item>
					<el-button type="primary" icon="search" @click="handleSearch">搜索</el-button>
				</el-form-item>
			</el-form>
		</div>
		<div class="gx-iframe-content">
			<div class="gx-iframe-operation" style="margin-bottom: 20px; border-radius: 4px;">
				<!-- <el-button type="danger" style="margin-left:10px;"><i class="el-icon-delete"></i> 批量删除</el-button> -->
				<el-button type="success" style="margin-left: 10px;" @click.active.prevent="handleCateAdd($event)" id="add" data-title="添加券种类"><i class="el-icon-plus"></i> 添加券种类</el-button>
				<el-button type="success" type="small" style="float:right; line-height: 38px;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
			</div>

			<el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
				<el-table-column type="selection" width="100"></el-table-column>
				<el-table-column prop="id" label="ID" sortable width="101"></el-table-column>
				<el-table-column prop="cate_name" label="种类名称" width="301"></el-table-column>
				<el-table-column prop="cate_desc" label="种类描述"  width="301"></el-table-column>
				<el-table-column prop="coupon_rule" label="券的规则"  width="301"></el-table-column>
				<el-table-column prop="addtime" label="添加时间" sortable  width="359"></el-table-column>
				<el-table-column label="操作" fixed="right" width="180">
					<template scope="scope">
						<!-- <a title="预览" style="margin-left:18px;" @click="handlePreview($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-view"></i></a> -->
						<a title="删除" style="margin-left:18px;" @click="handleDel(scope.row.id, scope.$index, list)"><i class="el-icon-delete"></i></a>
						<a title="编辑" data-title="编辑券种类信息" style="margin-left:18px;" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
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
	var vm = new Vue({
		el: '#app',
		data: {
			refreshstatus: false,
			fullscreenLoading: true,
			type_options: [
				{value: '', label: '不限类别'},
				{value: '1', label: '教练'},
				{value: '2', label: '驾校'},
				{value: '3', label: '嘻哈'}
			],
			kwords_options: [
				{value: '', label: '不限关键词'},
				{value: 'coupon_name', label: '券名称'},
				{value: 'coupon_code', label: '兑换码'},
				{value: 'owner_name', label: '角色名称'}
			],
			list: [],
			multipleSelection: [],
			list_url: "<?php echo base_url('coupon/cateAjax'); ?>",
			del_url: "<?php echo base_url('coupon/delCate'); ?>",
			edit_url: "<?php echo base_url('coupon/editCate'); ?>",
			addcate_url: "<?php echo base_url('coupon/addCate'); ?>",
			currentPage: 1,
			page_sizes: [10, 20, 30, 50, 100],
			page_size: 10,
			pagenum: 0,
			count: 0,
			search: {
				value:'',
			},
		},
		created: function() {
			var filter = {"p":this.currentPage, "value":this.search.value, 's': this.page_size};
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
					success: function(data) {
						// setTimeout(function() {
						vm.fullscreenLoading = false;
						// }, 500);
						vm.refreshstatus = false;
						if(data.code == 200) {
							vm.list = data.data.list;
							vm.pagenum = data.data.pagenum;
							vm.count = data.data.count;
							vm.currentPage = param.page;
						}
					},
					error: function() {
						vm.fullscreenLoading = false;
						vm.refreshstatus = false;
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				})
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
							var filter = {"p":vm.currentPage,"type":vm.search.type,"kwords":vm.search.kwords,"value":vm.search.value, 's': vm.page_size};
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
			handleSelectionChange: function(val) {
				this.multipleSelection = val;
			},
			handleAdd: function(e) {
				this.showLayer(e, '60%', 'rb', this.add_url);
			},
			handleCateAdd: function(e) {
				this.showLayer(e, '60%', 'rb', this.addcate_url);
			},
			handleEdit: function(e, id, index, row) {
				this.showLayer(e, '60%', 'lb', this.edit_url+'?id='+id);
			},
			handlePreview: function(e, id, index, row) {
				this.showLayer(e, '480px', 'rb', this.preview_url+'?id='+id);
			},
			handleDel: function(id, index, rows) {
				this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning'
				}).then(() => {
					this.delAjax(id);
					rows.splice(index, 1);
					this.messageNotice('success', '删除成功!');
				}).catch(() => {
					return false;
					// this.messageNotice('info', '已取消删除');
				});
			},
			handleCurrentChange: function(val) {
				// console.log("当前页:"+val);
				this.currentPage = val;
				window.history.pushState(null, null, '?p='+val+'&value='+this.search.value);
				var filter = {"p":this.currentPage, "value":this.search.value, 's': this.page_size};
				this.listAjax(filter);
			},
			handleSizeChange: function (size) {
				this.page_size = size;
				var filter = {"p":this.currentPage, "value":this.search.value, 's': this.page_size};
				this.listAjax(filter);
			},
			handleRefresh: function() {
				this.refreshstatus = true;
				// console.log(this.currentPage);
				var filter = {"p":this.currentPage, 's': $this.page_size};
				this.listAjax(filter);
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
							var filter = {"p":vm.currentPage,"type":vm.search.type,"kwords":vm.search.kwords,"value":vm.search.value, 's': vm.page_size};
							vm.listAjax(filter);
						} else {
							vm.messageNotice('warning', data.msg);
						}
					},
					error: function() {
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				});
			},
			handleSearch: function() {
				var filter = {"p":this.currentPage, "value":this.search.value, 's': this.page_size};
				this.listAjax(filter);
			}
		}
	})
</script>
