<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">
		<div class="gx-search">
			<el-form :inline="true" :model="search" class="demo-form-inline">
				<el-form-item label="按角色类别">
					<el-select v-model="search.type" placeholder="--不限类别--">
						<el-option v-for="item in type_options" :label="item.label" :value="item.value" v-if="school_id == ''"></el-option>
						<el-option v-for="item in type_option" :label="item.label" :value="item.value" v-if="school_id != ''"></el-option>
					</el-select>
				</el-form-item>
				<el-form-item label="关键词">
					<el-input v-model="search.keywords" placeholder="券名称、角色名称"></el-input>
				</el-form-item>
				<el-form-item>
					<el-button type="primary" icon="search" @click="handleSearch">搜索</el-button>
				</el-form-item>
			</el-form>
		</div>
		<div class="gx-iframe-content">
			<div class="gx-iframe-operation" style="margin-bottom: 20px; border-radius: 4px;">
				<!-- <el-button type="danger" style="margin-left:10px;"><i class="el-icon-delete"></i> 批量删除</el-button> -->
				<el-button type="success" style="margin-left: 10px;" @click.active.prevent="handleCouponAdd($event)" id="add" data-title="添加优惠券"><i class="el-icon-plus"></i> 添加优惠券</el-button>
				<el-button type="success" type="small" style="float:right; line-height: 38px;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
			</div>

			<el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
				<!-- <el-table-column type="selection" width="55"></el-table-column> -->
				<el-table-column type="expand" label="展开">
					<template scope="props">
						<el-form label-position="left" v-if="props.row" inline class="demo-table-expand">
							<el-form-item label="券名称：">
								<span>{{ props.row.coupon_name }}</span>
							</el-form-item>
							<el-form-item label="券值：">
								<span>￥{{ props.row.coupon_value }}元</span>
							</el-form-item>
							<el-form-item label="券总数：">
								<span>{{ props.row.coupon_total_num }}张</span>
							</el-form-item>
							<el-form-item label="被领数：">
								<span>{{ props.row.coupon_get_num }}张</span>
							</el-form-item>
							<el-form-item label="限领数：">
								<span>{{ props.row.coupon_limit_num }}张</span>
							</el-form-item>
							<el-form-item label="券种类：">
								<span v-if="props.row.coupon_category_id == 1">现金券</span>
								<span v-if="props.row.coupon_category_id == 2">折扣券</span>
							</el-form-item>
							<el-form-item label="发券者类型：">
								<span v-if="props.row.owner_type == 1">教练</span>
								<span v-if="props.row.owner_type == 2">驾校</span>
								<span v-if="props.row.owner_type == 3">嘻哈</span>
							</el-form-item>
							<el-form-item label="发券者：">
								<span>{{ props.row.owner_name }}</span>
							</el-form-item>
							<el-form-item label="添加时间：">
								<span>{{ props.row.addtime }}</span>
							</el-form-item>
							<el-form-item label="更新时间：">
								<span>{{ props.row.updatetime }}</span>
							</el-form-item>
							<el-form-item label="券描述：">
								<span>{{ props.row.coupon_desc }}</span>
							</el-form-item>
						</el-form>
					</template>
				</el-table-column>
				<el-table-column prop="id" label="ID" sortable width="80"></el-table-column>
				<el-table-column prop="coupon_name" label="券名称" show-overflow-tooltip min-width="150" show-overflow-tooltip></el-table-column>
				<el-table-column prop="owner_type" label="角色类别" show-overflow-tooltip min-width="100">
					<template scope="scope">
						<el-tag type="primary" v-if="parseInt(scope.row.owner_type) == 1">教练</el-tag> 
						<el-tag type="success" v-if="parseInt(scope.row.owner_type) == 2">驾校</el-tag> 
						<el-tag type="warning" v-if="parseInt(scope.row.owner_type) == 3">嘻哈</el-tag> 
					</template>
				</el-table-column>
				<el-table-column prop="owner_name" label="发券者" show-overflow-tooltip min-width="100"></el-table-column>
				<!-- <el-table-column prop="coupon_desc" label="券描述" show-overflow-tooltip width="200" show-overflow-tooltip></el-table-column> -->
				<el-table-column prop="coupon_total_num" label="总数 | 领取数 | 剩余数" show-overflow-tooltip min-width="200">
					<template scope="scope">
						<el-tag type="primary">{{ scope.row.coupon_total_num }}张</el-tag> |
						<el-tag type="warning">{{ scope.row.coupon_get_num }}张</el-tag> |
						<el-tag type="danger">{{ scope.row.coupon_surplus_num }}张</el-tag> 
					</template>
				</el-table-column>
				<el-table-column prop="coupon_value" label="券面值" show-overflow-tooltip min-width="140">
					<template scope="scope">
						<span>￥{{ scope.row.coupon_value }}元</span>
					</template>
				</el-table-column>
				<el-table-column prop="scene" label="适合场景" show-overflow-tooltip width="120">
					<template scope="scope">
						<el-tag type="success" v-if="parseInt(scope.row.scene) == 1">报名班制</el-tag>
						<el-tag type="warning" v-else>预约学车</el-tag>
					</template>
				</el-table-column>
				<el-table-column prop="coupon_limit_num" label="限领数" show-overflow-tooltip min-width="120">
					<template scope="scope">
						<span>{{ scope.row.coupon_limit_num }}张</span>
					</template>
				</el-table-column>
				<el-table-column prop="coupon_scope" label="使用范围" show-overflow-tooltip min-width="120">
					<template scope="scope">
						<el-tag type="primary" v-if="scope.row.coupon_scope == '0'">全国</el-tag> /
						<el-tag type="success" v-else-if="scope.row.coupon_scope == '1'">全省</el-tag> /
						<el-tag type="danger" v-else>全市 | {{ scope.row.city }}</el-tag>
					</template>
				</el-table-column>
				<el-table-column prop="address" label="使用区域" show-overflow-tooltip min-width="120"></el-table-column>
				<el-table-column prop="expiretime" label="到期时间" sortable show-overflow-tooltip width="180"></el-table-column>
				<el-table-column prop="is_open" fixed="right" label="开启否" width="100">
					<template scope="scope" >
						<el-button type="success" @click="handleOpen(scope.row.id, 2)" size="small" v-if="scope.row.is_open === '1'" close-transition>是</el-button>
						<el-button type="danger" @click="handleOpen(scope.row.id, 1)" size="small" v-else close-transition>否</el-button>
					</template>
				</el-table-column>
				<el-table-column prop="is_show" fixed="right" label="展示否" width="100">
					<template scope="scope" >
						<el-button type="success" @click="handleShow(scope.row.id, 0)" size="small" v-if="scope.row.is_show === '1'" close-transition>是</el-button>
						<el-button type="danger" @click="handleShow(scope.row.id, 1)" size="small" v-else close-transition>否</el-button>
					</template>
				</el-table-column>
				<el-table-column label="操作" fixed="right" width="140">
					<template scope="scope">
						<a title="删除" style="margin-left:18px; cursor: pointer;" @click="handleDel(scope.row.id, scope.$index, list)"><i class="el-icon-delete"></i></a>
						<a title="编辑" style="margin-left:18px; cursor: pointer;" data-title="编辑优惠券信息" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
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
			list: [],
			type_options: [
				{value: '', label: '--不限类别--'},
				{value: '1', label: '教练'},
				{value: '2', label: '驾校'},
				{value: '3', label: '嘻哈'}
			],
			type_option: [
				{value: '', label: '--不限类别--'},
				{value: '1', label: '教练'},
				{value: '2', label: '驾校'},
			],
			list_url: "<?php echo base_url('coupon/couponListAjax'); ?>",
			del_url: "<?php echo base_url('coupon/delAjax'); ?>",
			edit_url: "<?php echo base_url('coupon/edit'); ?>",
			add_url: "<?php echo base_url('coupon/add'); ?>",
			addcoupon_url: "<?php echo base_url('coupon/add'); ?>",
			preview_url: "<?php echo base_url('coupon/preview'); ?>",
			show_url: "<?php echo base_url('coupon/setShowAjax'); ?>",
			open_url: "<?php echo base_url('coupon/setOpenAjax'); ?>",
			currentPage: 1,
			page_sizes: [10, 20, 30, 50, 100],
			page_size: 10,
			pagenum: 0,
			count: 0,
			search: {
				type: '',
				keywords:'',
			},
		},
		created: function() {
			var filter = {"p": this.currentPage, "type": this.search.type, "keywords": this.search.keywords, "s": this.page_size};
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
							var filter = {"p": vm.currentPage, "type": vm.search.type, "keywords": vm.search.keywords, "s": vm.page_size};
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
					// rows.splice(index, 1);
					// this.messageNotice('success', '删除成功!');
				}).catch(() => {
					return false;
					// this.messageNotice('info', '已取消删除');
				});
			},
			handleCurrentChange: function(val) {
				this.currentPage = val;
				// window.history.pushState(null, null, '?p='+val+'&type='+this.search.type+'&kwords='+this.search.kwords+'&value='+this.search.value);
				var filter = {"p": this.currentPage, "type": this.search.type, "keywords": this.search.keywords, "s": this.page_size};
				this.listAjax(filter);
			},
			filterTag: function(value, row) {
				return row.is_open == value;
			},
			handleRefresh: function() {
				this.refreshstatus = true;
				var filter = {"p": this.currentPage, "s": this.page_size};
				vm.listAjax(filter);
			},
			handleSizeChange: function (size) {
				this.page_size = size;
				var filter = {"p": this.currentPage, "type": this.search.type, "keywords": this.search.keywords, "s": this.page_size};
				this.listAjax(filter);
			},
			handleSelectionChange: function(val) {
				this.multipleSelection = val;
			},
			handleAdd: function(e) {
				this.showLayer(e, '60%', 'rb', this.add_url);
			},
			handleCouponAdd: function(e) {
				this.showLayer(e, '60%', 'rb', this.addcoupon_url);
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
			handleShow: function(id, status) {
				$.ajax({
					type: 'post',
					url: this.show_url,
					data: {id:id, status:status},
					dataType:"json",
					success: function(data) {
						if(data.code == 200) {
							var filter = {"p":vm.currentPage,"type":vm.search.type,"kwords":vm.search.kwords,"value":vm.search.value};
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
			handleOpen: function(id, status) {
				$.ajax({
					type: 'post',
					url: this.open_url,
					data: {id:id, status:status},
					dataType:"json",
					success: function(data) {
						if(data.code == 200) {
							var filter = {"p":vm.currentPage,"type":vm.search.type,"kwords":vm.search.kwords,"value":vm.search.value};
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
				var filter = {"p":vm.currentPage,"type":vm.search.type,"kwords":vm.search.kwords,"value":vm.search.value};
				vm.listAjax(filter);
			}
		}
	})
</script>
