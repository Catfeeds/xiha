<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">
		<div class="gx-search">
			<el-form :inline="true" :model="search" class="demo-form-inline">
				<el-form-item label="按领券方式">
					<el-select v-model="search.coupon_type" placeholder="按领券方式">
						<el-option v-for="item in coupon_type_options" :label="item.label" :value="item.value"></el-option>
					</el-select>
				</el-form-item>
				<el-form-item label="按使用状态">
					<el-select v-model="search.coupon_status" placeholder="按使用状态">
						<el-option v-for="item in coupon_status_options" :label="item.label" :value="item.value"></el-option>
					</el-select>
				</el-form-item>
				<!-- <el-form-item label="按关键词">
					<el-select v-model="search.kwords" placeholder="按关键词">
						<el-option v-for="item in kwords_options" :label="item.label" :value="item.value"></el-option>
					</el-select>
				</el-form-item> -->
				<el-form-item>
					<el-input v-model="search.keywords" placeholder="领券者，券名称，手机号"></el-input>
				</el-form-item>
				<el-form-item>
					<el-button type="primary" icon="search" @click="handleSearch">搜索</el-button>
				</el-form-item>
			</el-form>
		</div>
		<div class="gx-iframe-content">
			<el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
				<el-table-column type="selection" width="45"></el-table-column>
				<el-table-column prop="id" label="ID" sortable width="80"></el-table-column>
				<el-table-column prop="user_name" label="领券者" width="100" show-overflow-tooltip></el-table-column>
				<el-table-column prop="user_phone" label="手机号"  width="140" show-overflow-tooltip></el-table-column>
				<el-table-column prop="coupon_name" label="券名称"  width="135" show-overflow-tooltip></el-table-column>
				<el-table-column prop="coupon_sender_owner_type" label="发放者类型"  width="130">
					<template scope="scope">
						<el-tag type="primary" v-if="parseInt(scope.row.coupon_sender_owner_type) == 1">教练</el-tag>
						<el-tag type="success" v-if="parseInt(scope.row.coupon_sender_owner_type) == 2">驾校</el-tag>
						<el-tag type="danger" v-if="parseInt(scope.row.coupon_sender_owner_type) == 3">嘻哈</el-tag>
					</template>
				</el-table-column>
				<el-table-column prop="owner_name" label="发放者"  width="155" show-overflow-tooltip></el-table-column>
				<el-table-column prop="coupon_scope" label="使用范围" width="120" show-overflow-tooltip>
					<template scope="scope">
						<el-tag type="primary" v-if="scope.row.coupon_scope == '0'">全国</el-tag> /
						<el-tag type="success" v-else-if="scope.row.coupon_scope == '1'">全省 {{ scope.row.province }}</el-tag> /
						<el-tag type="danger" v-else>全市 | {{ scope.row.city }}</el-tag>
					</template>
				</el-table-column>
				<el-table-column prop="address" label="使用地区"  width="120" show-overflow-tooltip></el-table-column>
				<el-table-column prop="coupon_type" label="领券方式"  width="110" show-overflow-tooltip>
					<template scope="scope">
						<el-tag type="primary" v-if="scope.row.coupon_type == '1'">自己领取</el-tag>
						<el-tag type="success" v-if="scope.row.coupon_type == '2'">系统推送</el-tag>
					</template>
				</el-table-column>
				<el-table-column prop="coupon_status" label="使用状态"  width="110" show-overflow-tooltip>
					<template scope="scope">
						<el-tag type="primary" v-if="parseInt(scope.row.coupon_status) == 1">未使用</el-tag>
						<el-tag type="success" v-if="parseInt(scope.row.coupon_status) == 2">已使用</el-tag>
						<el-tag type="danger" v-if="parseInt(scope.row.coupon_status) == 3">已过期</el-tag>
						<el-tag type="grey" v-if="parseInt(scope.row.coupon_status) == 4">已删除</el-tag>
					</template>
				</el-table-column>
				<el-table-column prop="addtime" label="领券时间"  width="180"></el-table-column>
				<el-table-column prop="expiretime" label="过期时间" sortable  width="180"></el-table-column>
				<el-table-column label="操作" fixed="right" width="120">
					<template scope="scope">
						<a title="删除" style="margin-left:18px;" @click="handleDel(scope.row.id, scope.$index, list)"><i class="el-icon-delete"></i></a>
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
			multipleSelection: [],
			coupon_type_options: [
				{value: '', label: '--不限方式--'},
				{value: '1', label: '自己领取'},
				{value: '2', label: '系统推送'}
			],
			coupon_status_options:[
				{value:'', label: '--不限状态--'},
				{value: '1', label: '未使用'},
				{value: '2', label: '已使用'},
				{value: '3', label: '已过期'},
				// {value: '4', label: '已删除'}
			],
			list: [],
			list_url: "<?php echo base_url('coupon/userCouponAjax'); ?>",
			del_url: "<?php echo base_url('coupon/delUserCoupon'); ?>",
			currentPage: 1,
			page_sizes: [10, 20, 30, 50, 100],
			page_size: 10,
			pagenum: 0,
			count: 0,
			search: {
				coupon_type: '',
				coupon_status: '',
				keywords:'',
			},
		},
		created: function() {
			var filter = {"p": this.currentPage, "type": this.search.coupon_type, "status": this.search.coupon_status, "keywords": this.search.keywords, 's': this.page_size};
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
						vm.fullscreenLoading = false;
						vm.refreshstatus = false;
						if(data.code == 200) {
							vm.list = data.data.list;
							vm.pagenum = data.data.pagenum;
							vm.count = data.data.count;
							vm.currentPage = data.data.p;
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
							var filter = {"p": vm.currentPage, "type": vm.search.coupon_type, "status": vm.search.coupon_status, "keywords": vm.search.keywords, 's': vm.page_size};
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
			handleSizeChange: function (size) {
				this.page_size = size;
				var filter = {"p": this.currentPage, "type": this.search.coupon_type, "status": this.search.coupon_status, "keywords": this.search.keywords, 's': this.page_size};
				this.listAjax(filter);
			},
			handleSelectionChange: function(val) {
				this.multipleSelection = val;
			},
			handleCurrentChange: function(val) {
				// console.log("当前页:"+val);
				this.currentPage = val;
				// window.history.pushState(null, null, '?p='+val+'&coupon_status='+this.search.coupon_status+'&coupon_type='+this.search.coupon_type+'&kwords='+this.search.kwords+'&value='+this.search.value);
				var filter = {"p": this.currentPage, "type": this.search.coupon_type, "status": this.search.coupon_status, "keywords": this.search.keywords, 's': this.page_size};
				this.listAjax(filter);
			},
			handleRefresh: function() {
				this.refreshstatus = true;
				// console.log(this.currentPage);
				var filter = {"p": this.currentPage, 's': this.page_size};
				vm.listAjax(filter);
			},
			handleSearch: function() {
				var filter = {"p": this.currentPage, "type": this.search.coupon_type, "status": this.search.coupon_status, "keywords": this.search.keywords, 's': this.page_size};
				vm.listAjax(filter);
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
