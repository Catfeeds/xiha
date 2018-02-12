<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">
		<div class="gx-search">
			<el-form :inline="true" :model="search" class="demo-form-inline">
				<!-- <el-form-item>
					<el-select v-model="status" placeholder="按删除状态" v-if="school_id ==''">
						<el-option label="--不限状态--" value=""></el-option>
						<el-option label="未删除" value="0"></el-option>
						<el-option label="已删除" value="2"></el-option>
					</el-select>
				</el-form-item> -->
				<el-form-item label="关键词">
					<el-input v-if="school_id == '' " style="width: 400px;" placeholder="ID，姓名，手机号，身份证号，所属驾校" v-model="search.keywords"></el-input>
					<el-input v-if="school_id != '' " style="width: 400px;" placeholder="ID，姓名，手机号，身份证号" v-model="search.keywords"></el-input>
				</el-form-item>
				<el-form-item>
					<el-button type="primary" icon="search" @click="handleSearch">搜索</el-button>
				</el-form-item>
			</el-form>
		</div>

		<div class="gx-iframe-content">
			<div class="gx-iframe-operation" style="margin-bottom: 20px; border-radius: 4px;">
				<el-button style="margin-left: 10px;" type="success" @click.active.prevent="handleAdd($event)" id="add" data-title="添加学员"><i class="el-icon-plus"></i> 添加学员</el-button>
				<el-button type="success" type="small" style="float:right; line-height: 38px;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
			</div>

			<el-tabs v-model="activeName" @tab-click="handleClick" v-if="school_id == ''">
				<el-tab-pane label="未删除" name="first" >
					<el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
						<!-- <el-table-column fixed type="selection" width="50"></el-table-column> -->
						<el-table-column type="expand" label="展开">
							<template scope="props">
								<el-form label-position="left" inline class="demo-table-expand">
									<el-form-item label="名称：">
										<span>{{ props.row.user_name }}</span>
									</el-form-item>
									<el-form-item label="交易号：">
										<span>{{ props.row.user_phone }}</span>
									</el-form-item>
									<el-form-item label="学习状态：">
										<span>{{ props.row.learncar_status }}</span>
									</el-form-item>
									<el-form-item label="报名科目：">
										<span>{{ props.row.lesson_name }}</span>
									</el-form-item>
									<el-form-item label="报名牌照：" >
										<span>{{ props.row.license_name }}</span>
									</el-form-item>
								</el-form>
							</template>
						</el-table-column>
						<el-table-column prop="l_user_id" label="ID" sortable width="90"></el-table-column>
						<el-table-column prop="school_name" v-if="school_id == '' " label="驾校" width="150" v-if="school_id == ''" show-overflow-tooltip></el-table-column>
						<el-table-column prop="user_name" label="姓名" width="140" show-overflow-tooltip></el-table-column>
						<el-table-column prop="user_phone" label="手机" width="163" show-overflow-tooltip></el-table-column>
						
						<el-table-column prop="sex" label="性别" width="100" >
							<template scope="scope">
								<el-tag type="primary" v-if="parseInt(scope.row.sex) == 1">男</el-tag>
								<el-tag type="success" v-if="parseInt(scope.row.sex) == 2">女</el-tag>
							</template>
						</el-table-column>
						<el-table-column prop="age" label="年龄" width="85" ></el-table-column>
						<el-table-column prop="identity_id" label="身份证号" width="250" show-overflow-tooltip></el-table-column>
						<el-table-column prop="stu_address" label="地址" width="180" show-overflow-tooltip></el-table-column>
						<el-table-column prop="addtime" label="注册时间" width="180" ></el-table-column>
						<el-table-column prop="updatetime" label="最近更新" width="180"></el-table-column>
						<el-table-column prop="status" fixed="right" label="删除状态" width="100" v-if="school_id == ''">
							<template scope="scope">
								<el-button size="small" type="success" @click="handleShow(scope.row.l_user_id, scope.row.user_phone, 2)" v-if="parseInt(scope.row.status) == 0">未删除</el-button>
								<el-button size="small" type="danger" @click="handleShow(scope.row.l_user_id, scope.row.user_phone, 0)" v-if="parseInt(scope.row.status) == 2">已删除</el-button>
							</template>
						</el-table-column>
						<el-table-column label="操作" fixed="right" width="100">
							<template scope="scope">
								<el-button size="small" type="text" data-title="编辑学员信息" @click="handleEdit($event, scope.row.l_user_id, scope.$index, scope.row)">编辑</el-button>
								<el-button size="small" type="text" style="color:#ff4949;" @click.native.prevent="handleDel(scope.row.l_user_id, scope.$index, list)">删除</el-button>
							</template>
						</el-table-column>
					</el-table>
				</el-tab-pane>

				<el-tab-pane label="已删除" name="second">
					<el-table :data="del_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
						<!-- <el-table-column fixed type="selection" width="50"></el-table-column> -->
						<el-table-column type="expand" label="展开">
							<template scope="props">
								<el-form label-position="left" inline class="demo-table-expand">
									<el-form-item label="名称：">
										<span>{{ props.row.user_name }}</span>
									</el-form-item>
									<el-form-item label="交易号：">
										<span>{{ props.row.user_phone }}</span>
									</el-form-item>
									<el-form-item label="学习状态：">
										<span>{{ props.row.learncar_status }}</span>
									</el-form-item>
									<el-form-item label="报名科目：">
										<span>{{ props.row.lesson_name }}</span>
									</el-form-item>
									<el-form-item label="报名牌照：" >
										<span>{{ props.row.license_name }}</span>
									</el-form-item>
								</el-form>
							</template>
						</el-table-column>
						<el-table-column prop="l_user_id" label="ID" sortable width="90"></el-table-column>
						<el-table-column prop="school_name" v-if="school_id == '' " label="驾校" width="150" v-if="school_id == ''" show-overflow-tooltip></el-table-column>
						<el-table-column prop="user_name" label="姓名" width="140" show-overflow-tooltip></el-table-column>
						<el-table-column prop="user_phone" label="手机" width="163" show-overflow-tooltip></el-table-column>
						
						<el-table-column prop="sex" label="性别" width="100" >
							<template scope="scope">
								<el-tag type="primary" v-if="parseInt(scope.row.sex) == 1">男</el-tag>
								<el-tag type="success" v-if="parseInt(scope.row.sex) == 2">女</el-tag>
							</template>
						</el-table-column>
						<el-table-column prop="age" label="年龄" width="85" ></el-table-column>
						<el-table-column prop="identity_id" label="身份证号" width="250" show-overflow-tooltip></el-table-column>
						<el-table-column prop="stu_address" label="地址" width="180" show-overflow-tooltip></el-table-column>
						<el-table-column prop="addtime" label="注册时间" width="180" ></el-table-column>
						<el-table-column prop="updatetime" label="最近更新" width="180"></el-table-column>
						<el-table-column prop="status" fixed="right" label="删除状态" width="100" v-if="school_id == ''">
							<template scope="scope">
								<el-button size="small" type="success" @click="handleShow(scope.row.l_user_id, scope.row.user_phone, 2)" v-if="parseInt(scope.row.status) == 0">未删除</el-button>
								<el-button size="small" type="danger" @click="handleShow(scope.row.l_user_id, scope.row.user_phone, 0)" v-if="parseInt(scope.row.status) == 2">已删除</el-button>
							</template>
						</el-table-column>
						<el-table-column label="操作" fixed="right" width="100">
							<template scope="scope">
								<el-button size="small" type="text" data-title="编辑学员信息" @click="handleEdit($event, scope.row.l_user_id, scope.$index, scope.row)">编辑</el-button>
								<el-button size="small" type="text" style="color:#ff4949;" @click.native.prevent="handleDel(scope.row.l_user_id, scope.$index, list)">删除</el-button>
							</template>
						</el-table-column>
					</el-table>
				</el-tab-pane>
			</el-tabs>

			<el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" v-if="school_id != ''">
				<!-- <el-table-column fixed type="selection" width="50"></el-table-column> -->
				<el-table-column type="expand" label="展开">
					<template scope="props">
						<el-form label-position="left" inline class="demo-table-expand">
							<el-form-item label="名称：">
								<span>{{ props.row.user_name }}</span>
							</el-form-item>
							<el-form-item label="交易号：">
								<span>{{ props.row.user_phone }}</span>
							</el-form-item>
							<el-form-item label="学习状态：">
								<span>{{ props.row.learncar_status }}</span>
							</el-form-item>
							<el-form-item label="报名科目：">
								<span>{{ props.row.lesson_name }}</span>
							</el-form-item>
							<el-form-item label="报名牌照：" >
								<span>{{ props.row.license_name }}</span>
							</el-form-item>
						</el-form>
					</template>
				</el-table-column>
				<el-table-column prop="l_user_id" label="ID" sortable width="90"></el-table-column>
				<el-table-column prop="school_name" v-if="school_id == '' " label="驾校" width="150" v-if="school_id == ''" show-overflow-tooltip></el-table-column>
				<el-table-column prop="user_name" label="姓名" width="140" show-overflow-tooltip></el-table-column>
				<el-table-column prop="user_phone" label="手机" width="163" show-overflow-tooltip></el-table-column>
				
				<el-table-column prop="sex" label="性别" width="100" >
					<template scope="scope">
						<el-tag type="primary" v-if="parseInt(scope.row.sex) == 1">男</el-tag>
						<el-tag type="success" v-if="parseInt(scope.row.sex) == 2">女</el-tag>
					</template>
				</el-table-column>
				<el-table-column prop="age" label="年龄" width="85" ></el-table-column>
				<el-table-column prop="identity_id" label="身份证号" width="250" show-overflow-tooltip></el-table-column>
				<el-table-column prop="stu_address" label="地址" width="180" show-overflow-tooltip></el-table-column>
				<el-table-column prop="addtime" label="注册时间" width="180" ></el-table-column>
				<el-table-column prop="updatetime" label="最近更新" width="180"></el-table-column>
				<el-table-column label="操作" fixed="right" width="100">
					<template scope="scope">
						<el-button size="small" type="text" data-title="编辑学员信息" @click="handleEdit($event, scope.row.l_user_id, scope.$index, scope.row)">编辑</el-button>
						<el-button size="small" type="text" style="color:#ff4949;" @click.native.prevent="handleDel(scope.row.l_user_id, scope.$index, list)">删除</el-button>
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
			del_list: [],
			multipleSelection: [],
			list_url: "<?php echo base_url('student/listAjax'); ?>",
			del_url: "<?php echo base_url('student/delAjax'); ?>",
			edit_url: "<?php echo base_url('student/edit'); ?>",
			editajax_url: "<?php echo base_url('student/editAjax'); ?>",
			add_url: "<?php echo base_url('student/add'); ?>",
			preview_url: "<?php echo base_url('student/preview'); ?>",
			show_url: "<?php echo base_url('student/handleShow'); ?>",
			currentPage: 1,
            page_sizes: [10, 20, 30, 50, 100],
            page_size: 10,
            pagenum: 0,
            count: 0,
			activeName: 'first',
			status: 'undel',
			search: {
				keywords: '',
			},
		},
		created: function() {
			var filters = {"p": this.currentPage, "status": this.status, "keywords": this.search.keywords, 's': this.page_size};
			this.listAjax(filters);
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
							if (param.status == 'undel') {
								vm.list = ret.data.list;
							} else {
								vm.del_list = ret.data.list;
							}
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
			handleClick: function(tab, event) {
				switch (this.activeName) {
					case 'first':
						this.status = 'undel';
						break;
					case 'second':
						this.status = 'del';
						break;
					default:
						this.status = 'undel';
						break;
				}
				var filters = {"p": vm.currentPage, "status": vm.status, "keywords":vm.search.keywords, 's': vm.page_size};
				this.listAjax(filters);
			},
			handleShow: function(id, phone, status) {
				$.ajax({
					type: 'post',
					url: this.show_url,
					data: {'id':id, 'phone': phone,'status':status},
					dataType:"json",
					success: function(data) {		
						if(data.code == 200) {
							var filters = {"p": vm.currentPage, "status": vm.status, "keywords":vm.search.keywords, 's': vm.page_size};
							vm.listAjax(filters);
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
							var filters = {"p": vm.currentPage, "status":vm.status, "keywords":vm.search.keywords, 's': vm.page_size}
							vm.listAjax(filters);
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
				this.$confirm('此操作将永久删除该文件, 是否继续?', '提示', {
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
			handleSizeChange: function (size) {
                this.page_size = size;
				var filters = {"p": this.currentPage, "status": this.status, "keywords":this.search.keywords, 's': this.page_size};
                this.listAjax(filters);
            },
			handleCurrentChange: function(val) {
				this.refreshstatus = true;
				this.currentPage = val;
				// window.history.pushState(null, null, '?p='+val+'&status='+this.status+'&type='+this.search.type+'&value='+this.search.value);
				var filters = {"p": this.currentPage, "status": this.status, "keywords": this.search.keywords, 's': this.page_size};
				this.listAjax(filters);
			},
			filterTag: function(value, row) {
				return row.is_show == value;
			},
			handleRefresh: function() {
				this.refreshstatus = true;
				var filters = {"p": this.currentPage, 's': this.page_size};
				this.listAjax(filters);
			},
			handleSearch: function () {
				var filters = {"p": this.currentPage, "status": this.status, "keywords": this.search.keywords, 's': this.page_size};
                vm.listAjax(filters);
			},
			handleSelectionChange: function(val) {
				this.multipleSelection = val;
			},
			handleAdd: function(e) {
				this.showLayer(e, '60%', 'lb', this.add_url);
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
