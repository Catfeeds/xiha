<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">
		<!--<div class="gx-breadcrumb gx-line">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
				<el-breadcrumb-item>学员管理</el-breadcrumb-item>
				<el-breadcrumb-item>学员列表</el-breadcrumb-item>
			</el-breadcrumb>
		</div>-->
		<div class="gx-search">
			<table style="width: 100%;">
				<tr>
					<td>                              
						<el-select v-model="value" placeholder="请选择性别">
							<el-option v-for="item in options" :label="item.label" :value="item.value"></el-option>
						</el-select>
					</td>
					<td>
						<el-input placeholder="请输入电话" v-model="input">
							<template slot="prepend">电话：</template>
						</el-input>
					</td>
					<td>
						<el-input placeholder="请输入手机号" v-model="input">
							<template slot="prepend">手机号：</template>
						</el-input>
					</td>
					<td>
						<el-input placeholder="请输入身份证号" v-model="input">
							<template slot="prepend">身份证号：</template>
						</el-input>
					</td>
				</tr>
				<tr style="text-align: center;">
					<td colspan="4" style="padding-top: 10px;"><el-button type="primary" icon="search">搜索</el-button></td>
				</tr>
			</table>
		</div>

		<div class="gx-iframe-content">
			<div class="gx-iframe-operation" style="margin-bottom: 20px; border-radius: 4px;">
				<el-button type="danger" style="margin-left:10px;"><i class="el-icon-delete"></i> 批量删除</el-button>
				<el-button type="success" @click.active.prevent="handleAdd($event)" id="add" data-title="添加学员"><i class="el-icon-plus"></i> 添加学员</el-button>
				<el-button type="warning" @click.active.prevent="handleCouponAdd($event)" id="add" data-title="添加优惠券"><i class="el-icon-plus"></i> 添加优惠券</el-button>
				<el-button type="success" type="small" style="float:right; line-height: 38px;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
			</div>

			<el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
				<el-table-column type="selection"width="55"></el-table-column>
					<el-table-column type="expand" label="展开">
						<template scope="props">
							<el-form label-position="left" v-if="props.row.user_info" inline class="demo-table-expand">
								<el-form-item label="是否第一次登录：">
									<el-tag type="danger" v-if="props.row.is_first == '1'">是</el-tag>
									<el-tag type="primary" v-else>否</el-tag>
								</el-form-item>
								<el-form-item label="年龄：">
									<span>{{ props.row.user_info.age }}</span>
								</el-form-item>
								<el-form-item label="身份证号：">
									<span>{{ props.row.user_info.identity_id }}</span>
								</el-form-item>
								<el-form-item label="地址：">
									<span>{{ props.row.user_info.address }}</span>
								</el-form-item>
								<el-form-item label="做题类型：">
									<span>{{ props.row.user_info.exam_license_name }}</span>
								</el-form-item>
								<el-form-item label="报名驾校：">
									<span>{{ props.row.user_info.school_id }}</span>
								</el-form-item>
							</el-form>
							<el-form v-else>
								<el-form-item>暂无详细信息</el-form-item>
							</el-form>
						</template>
					</el-table-column>
				<el-table-column prop="l_user_id" label="ID" sortable width="80"></el-table-column> 
				<el-table-column prop="s_username" label="昵称" show-overflow-tooltip></el-table-column> 
				<el-table-column prop="s_real_name" label="真实姓名" show-overflow-tooltip></el-table-column>
				<el-table-column prop="user_info.sex" label="性别" show-overflow-tooltip>
					<template scope="scope">
						<div v-if="scope.row.user_info">
							<el-tag type="primary" v-if="scope.row.user_info.sex === '1'" close-transition>男</el-tag>
							<el-tag type="danger" v-else-if="scope.row.user_info.sex === '2'" close-transition>女</el-tag>
							<el-tag type="success" v-else close-transition>未知</el-tag>
						</div>
					</template>
				</el-table-column>
				<el-table-column prop="s_phone" label="手机" show-overflow-tooltip></el-table-column>
				<el-table-column prop="lesson_name" label="牌照 / 科目" show-overflow-tooltip>
					<template scope="scope">
						<div v-if="scope.row.user_info">
							<el-tag type="danger">{{ scope.row.user_info.license_name }} / {{ scope.row.user_info.lesson_name }}</el-tag>
						</div>
					</template>
				</el-table-column>
				<el-table-column prop="addtime" label="添加时间" sortable show-overflow-tooltip></el-table-column>
				<el-table-column prop="i_status" fixed="right" label="是否下架" :filters="[{ text: '在线', value: '0' }, { text: '下架', value: '2' }]" :filter-method="filterTag">				
					<template scope="scope" >
						<el-button type="success" @click="handleShow(scope.row.l_user_id, 2)" size="small" v-if="scope.row.i_status === '0'" close-transition>在线</el-button>
						<el-button type="danger" @click="handleShow(scope.row.l_user_id, 0)" size="small" v-else close-transition>拉黑</el-button>
					</template>
				</el-table-column>
				<el-table-column label="操作" fixed="right">
					<template scope="scope">
						<el-button size="small" type="text" data-title="预览学员" @click="handlePreview($event, scope.row.l_user_id, scope.$index, scope.row)">预览</el-button>
						<el-button size="small" type="text" data-title="编辑学员" @click="handleEdit($event, scope.row.l_user_id, scope.$index, scope.row)">编辑</el-button>
						<el-button size="small" type="text" style="color:#ff4949;" @click.native.prevent="handleDel(scope.row.l_user_id, scope.$index, list)">删除</el-button>
					</template>
				</el-table-column>
			</el-table>

			<!--page-->
			<div class="block" style="float: right; margin-top: 10px;">
				<el-pagination
				@current-change="handleCurrentChange"
				:current-page="currentPage"
				layout="total, prev, pager, next, jumper"
				:total="count">
				</el-pagination>
			</div>

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
			input: '',
			value: '',
			options: [{value: '1', label: '男' }, {value: '2', label: '女'}],
			list: [],
			multipleSelection: [],
			list_url: "<?php echo base_url('user/listajax'); ?>",
			del_url: "<?php echo base_url('user/delajax'); ?>",
			edit_url: "<?php echo base_url('user/edit'); ?>",
			add_url: "<?php echo base_url('user/add'); ?>",
			addcoupon_url: "<?php echo base_url('coupon/add?f=user'); ?>",
			preview_url: "<?php echo base_url('user/preview'); ?>",
			show_url: "<?php echo base_url('user/show'); ?>",
			currentPage: parseInt("<?php echo $p; ?>"),
			pagenum: "<?php echo $pagenum; ?>",
			count: "<?php echo $count; ?>",
		},
		created: function() {
			this.listAjax(this.currentPage);
		},
		methods: {
			handleSelectionChange: function(val) {
				this.multipleSelection = val;
			},
			handleAdd: function(e) {
				this.showLayer(e, '60%', 'rb', this.add_url);
			},
			handleCouponAdd: function(e) {
				console.log(this.multipleSelection)
				if(this.multipleSelection.length == 0) {
					this.messageNotice('warning', '请选择至少一个学员');
					return false;
				}
				this.showLayer(e, '60%', 'rb', this.addcoupon_url);
			},
			handleEdit: function(e, id, index, row) {
				this.showLayer(e, '60%', 'lb', this.edit_url+'?id='+id);
			},
			handlePreview: function(e, id, index, row) {
				this.showLayer(e, '480px', 'rb', this.preview_url+'?id='+id);
			},
			handleDel: function(id, index, rows) {
				this.$confirm('此操作将永久删除该文件, 是否继续?', '提示', {
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
				// this.$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
				// 	confirmButtonText: '确定',
				// 	cancelButtonText: '取消',
				// 	type: 'warning'
				// }).then(() => {
				// 	this.delAjax(id);
				// 	rows.splice(index, 1);
				// 	this.messageNotice('success', '删除成功!');
				// }).catch(() => {
				// 	return false;
				// 	// this.messageNotice('info', '已取消删除');
				// });
			},
			handleCurrentChange: function(val) {
				// console.log("当前页:"+val);
				window.history.pushState(null, null, '?p='+val);
				this.listAjax(val);
			},
			filterTag: function(value, row) {
				return row.i_status == value;
			},
			handleRefresh: function() {
				this.refreshstatus = true;
				// console.log(this.currentPage);
				this.listAjax(this.currentPage);
			},
			listAjax: function(page) {
				$.ajax({
					type: 'post',
					url: this.list_url,
					data: {p: page},
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
							vm.currentPage = page;
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
							vm.listAjax(vm.currentPage);
							vm.messageNotice('success', data.msg);			
						}
					},
					error: function() {
						vm.fullscreenLoading = false;									
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
							vm.listAjax(vm.currentPage);
						} else {
							vm.messageNotice('warning', data.msg);			
						}
					},
					error: function() {							
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				});
			}
			
		}
	})
</script>