<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">
		<div class="gx-search">
			<el-form :inline="true" :model="search" class="demo-form-inline">
				<el-form-item label="关键词"> 
					<el-input v-model="search.keywords" placeholder="券兑换码，优惠券名"></el-input>
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
				<el-table-column fixed type="selection" width="70"></el-table-column>
				<el-table-column prop="id" label="ID" sortable width="100"></el-table-column>
				<el-table-column prop="coupon_name" label="优惠券名" width="200" show-overflow-tooltip></el-table-column>
				<el-table-column prop="coupon_code" label="券兑换码" width="200" show-overflow-tooltip></el-table-column>
				<el-table-column prop="cate_name" label="券种类" width="200" show-overflow-tooltip></el-table-column>
				<el-table-column prop="coupon_value" label="券面值" width="150" show-overflow-tooltip>
					<template scope="scope">
					 	<span>￥{{ scope.row.coupon_value }}元</span>
					</template>
				</el-table-column>
				<el-table-column prop="is_used" label="使用状态" width="200" >
					<template scope="scope">
				        <el-tag type="danger" v-if="parseInt(scope.row.is_used) == 0">未使用</el-tag>
				        <el-tag type="success" v-if="parseInt(scope.row.is_used) == 1">已使用</el-tag>
			      	</template>
				</el-table-column>
				<el-table-column prop="addtime" label="添加时间" width="200" ></el-table-column>
				<el-table-column prop="updatetime" label="兑换时间" width="200" ></el-table-column>
				<el-table-column label="操作" fixed="right" width="140">
					<template scope="scope">
						<a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.id, scope.$index, list)"><i class="el-icon-delete"></i></a>
						<a title="编辑" data-title="编辑兑换码信息" style="margin-left:8px;" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
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
			list: [],
			multipleSelection: [],
			list_url: "<?php echo base_url('coupon/codeListAjax'); ?>",
			del_url: "<?php echo base_url('coupon/delCode'); ?>",
			edit_url: "<?php echo base_url('coupon/editCode'); ?>",
			currentPage: 1,
			page_sizes: [10, 20, 30, 50, 100],
			page_size: 10,
			pagenum: 0,
			count: 0,
			search: {
				keywords: '',
			},
		},
		created: function() {
			var filter = {"p": this.currentPage, "keywords": this.search.keywords, "s": this.page_size};
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
						vm.fullscreenLoading = false;
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
			delAjax: function(id) {
				$.ajax({
					type: 'post',
					url: this.del_url,
					data: {id:id},
					dataType:"json",
					success: function(data) {
						vm.fullscreenLoading = false;
						if(data.code == 200) {
							var filter = {"p": vm.currentPage, "keywords": vm.search.keywords, "s": vm.page_size};
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
			handleSearch: function () {
				var filter = {"p": this.currentPage, "keywords": this.search.keywords, "s": this.page_size};
				vm.listAjax(filter);
			},
			handleSizeChange: function (size) {
				this.page_size = size;
				var filter = {"p": this.currentPage, "keywords": this.search.keywords, "s": this.page_size};
				this.listAjax(filter);
			},
			handleSelectionChange: function(val) {
				this.multipleSelection = val;
			},
			handleEdit: function(e, id, index, row) {
				this.showLayer(e, '60%', 'lb', this.edit_url+'?id='+id);
			},
			handleDel: function(id, index, rows) {
				this.$confirm('此操作将永久删除该教练, 是否继续?', '提示', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning',
					callback: function(action) {
						if(action == 'confirm') {
							vm.delAjax(id);
							rows.splice(index, 1);
							vm.messageNotice('success', '删除成功!');
						} else {
							return false;
						}
					}
				});
			},
			handleCurrentChange: function(val) {
				this.refreshstatus = true;
				this.currentPage = val;
				// window.history.pushState(null, null, '?p='+val+'&kwords='+this.search.kwords+'&value='+this.search.value);
				var filter = {"p": this.currentPage, "keywords": this.search.keywords, "s": this.page_size};
				this.listAjax(filter);
			},
			handleRefresh: function() {
				this.refreshstatus = true;
				var filter = {"p": this.currentPage, "s": this.page_size};
				this.listAjax(filter);
			},
			messageNotice: function(type, msg) {
				this.$message({type: type,message: msg});
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
