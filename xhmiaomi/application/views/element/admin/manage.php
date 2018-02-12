<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">
		<div class="gx-search">
			<el-form :inline="true" v-model="search" >
                <el-form-item label="开放状态">
					<el-select v-model="search.is_close" placeholder="请选择状态">
						<el-option v-for="item in options" :label="item.label" :value="item.value"></el-option>
					</el-select>
				</el-form-item>
				<el-form-item label="关键词">
                    <el-input v-model="search.keywords" placeholder="账号 | 手机号 | 用户名称"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" icon="search" @click="handleSearch">搜索</el-button>
                </el-form-item>
            </el-form>
		</div>

		<div class="gx-iframe-content">
			<div class="gx-iframe-operation" style="margin-bottom: 20px; border-radius: 4px;">
				<el-button type="success" style="margin-left:10px;" @click.active.prevent="handleAdd($event)" id="add" data-title="新增管理员"><i class="el-icon-plus"></i> 新增管理员</el-button>
				<el-button type="success" type="small" style="float:right; line-height: 38px;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
			</div>

			<el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
				<el-table-column type="selection" width="55"></el-table-column>
				<el-table-column prop="id" label="ID" sortable width="80"></el-table-column> 
				<el-table-column prop="content" label="展示名称" show-overflow-tooltip></el-table-column> 
				<el-table-column prop="name" label="登录账号" show-overflow-tooltip></el-table-column>
				<el-table-column prop="phone" label="手机号" show-overflow-tooltip></el-table-column>
				<el-table-column prop="password" label="登录密码" show-overflow-tooltip>
					<template scope="scope">
						<el-tag type="primary" v-if="scope.row.is_change == 1">已修改</el-tag>
						<el-tag type="danger" v-else>初始密码</el-tag>
					</template>	
				</el-table-column>
				<el-table-column prop="s_role_name" label="角色名称" show-overflow-tooltip></el-table-column>
				<el-table-column prop="addtime" label="创建时间" show-overflow-tooltip></el-table-column>
				<el-table-column prop="updatetime" label="最近时间" show-overflow-tooltip></el-table-column>
				<el-table-column prop="is_close" label="是否开放" show-overflow-tooltip>
					<template scope="scope" >
						<el-button type="success" @click="handleShow(scope.row.id, 2)" size="small" v-if="scope.row.is_close == '1'" close-transition>开放</el-button>
						<el-button type="danger" @click="handleShow(scope.row.id, 1)" size="small" v-else close-transition>关闭</el-button>
					</template>
				</el-table-column>
				<el-table-column label="操作" fixed="right">
					<template scope="scope">
						<el-button size="small" v-if="scope.row.id != '1'" type="text" style="color:#ff4949;" @click.native.prevent="handleDel(scope.row.id, scope.$index, list)">删除</el-button>
						<el-button size="small" type="text" data-title="更新信息" @click.native.prevent="handleEdit($event, scope.row.id, scope.$index, scope.row)">编辑</el-button>
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
			options: [
				{value: '1', label: '开放' }, 
				{value: '2', label: '关闭'}
			],
			list: [],
			multipleSelection: [],
			list_url: "<?php echo base_url('admin/managelistajax'); ?>",
			del_url: "<?php echo base_url('admin/delajax'); ?>?type=manage",
			add_url: "<?php echo base_url('admin/addmanage'); ?>",
			edit_url: "<?php echo base_url('admin/editmanage'); ?>",
			show_url: "<?php echo base_url('admin/show'); ?>",
			currentPage: 1,
            page_sizes: [10, 20, 30, 50, 100],
            page_size: 10,
            pagenum: 0,
            count: 0,
            search: {
				is_close: '',
                keywords: '',
            }
		},		
		created: function() {
            var filters = {"p": this.currentPage, "close": this.search.is_close, "keywords": this.search.keywords, "s": this.page_size};
            this.listAjax(filters);
        },
		methods: {
			handleSelectionChange: function(val) {
				this.multipleSelection = val;
			},
			handleAdd: function(e) {
				this.showLayer(e, '60%', 'rb', this.add_url);
			},
            handleSelectionChange: function(val) {
				this.multipleSelection = val;
			},
            listAjax: function (param) {
                $.ajax({
                    type: "post",
                    url: this.list_url,
                    data: param,
                    dataType: "json",
                    async: true,
                    success: function (res) {
                        vm.refreshstatus = false;
                        vm.fullscreenLoading = false;
                        isResOk = _.isObject(res) && _.has(res, 'code') && _.get(res, 'code') == 200;
                        if (isResOk) {
                            vm.list = _.get(res, 'data.list');
                            vm.pagenum = _.get(res, 'data.pagenum');
                            vm.count = _.get(res, 'data.count');
                            vm.currentPage = _.get(res, 'data.p');
                            // vm.messageNotice('success', _.get(res, 'msg'));
                        } else {
                            vm.messageNotice('success', _.get(res, 'msg'));
                        }
                    },
                    error: function (e) {
                        vm.messageNotice('error', '加载出错！');
                    } 
                });
            },
            handleForceStatus: function (id, status) {
                $.ajax ({
                    type: 'post',
                    url: this.force_url,
                    data: {id: id, status: status},
                    dataType: "json",
					success: function(data) {
                        is_ok = _.isObject(data) && _.has(data, 'code') && _.get(data, 'code') == 200;
                        if (is_ok) {
                            vm.listAjax(vm.currentPage);
                            vm.messageNotice('success', _.get(data, 'msg'));
                        } else {
                            vm.messageNotice('warning', _.get(data, 'msg'));
                        }
                    },
                    error: function (e) {
                        vm.messageNotice('error', '网络出现异常！');
                    }
                });
                
            },
            handleDel: function(id, index, rows) {
				this.$confirm('此操作将永久删除, 是否继续?', '提示', {
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
            delAjax: function(id) {
                $.ajax({
                    type: 'post',
                    url: this.del_url,
                    data: {id: id},
                    dataType: 'json',
                    success: function(data) {
                        is_ok = _.isObject(data) && _.has(data, 'code') && _.get(data, 'code') == 200
                        if (is_ok) {
                            var filters = {"p": vm.currentPage, "close": vm.search.is_close, "keywords": vm.search.keywords, 's': vm.page_size};
                            vm.listAjax(filters);
							vm.messageNotice('success', _.get(data, 'msg'));
                        } else {
                            vm.messageNotice('warning', _.get(data, 'msg'));
                        }
                    },
                    error: function(e) {
                        vm.messageNotice('error', '网络异常！');
                    }
                });
            },
            filterTag: function (value, row) {
                return row.os_type == value;
            },
            filterTags: function (value, row) {
                return row.app_client == value;
            },
            handleCurrentChange: function (val) {
                this.refreshstatus = true;
                this.currentPage = val;
                window.history.pushState(null, null, '?p='+val+'close'+this.search.is_close+'&keywords='+this.search.keywords);
                var filters = {"p": this.currentPage, "close": this.search.is_close, "keywords": this.search.keywords, 's': this.page_size};
                vm.listAjax(filters);
            },
            handleSizeChange: function (size) {
                this.page_size = size;
                var filters = {"p": this.currentPage, "close": this.search.is_close, "keywords": this.search.keywords, 's': this.page_size};
                this.listAjax(filters);
            },
            handleSearch: function () {
                // this.page_size = size;
                var filters = {"p": this.currentPage, "close": this.search.is_close, "keywords": this.search.keywords, 's': this.page_size};
                vm.listAjax(filters);
            },
            handleRefresh: function() {
				this.refreshstatus = true;
				this.listAjax();
			},
            handleAdd: function(e) {
                this.showLayer(e, '60%', 'lb', this.add_url);
            },
            handleEdit: function(e, id, index, row) {
				this.showLayer(e, '60%', 'lb', this.edit_url+'?id='+id);
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