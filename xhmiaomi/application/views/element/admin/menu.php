<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">
		<!-- <div class="gx-search">
			<el-form :inline="true" v-model="search" >
				<el-form-item label="关键词">
                    <el-input v-model="search.keywords" placeholder="名称 | 描述"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" icon="search" @click="handleSearch">搜索</el-button>
                </el-form-item>
            </el-form>
		</div> -->

		<div class="gx-iframe-content">
			<div class="gx-iframe-operation" style="margin-bottom: 20px; border-radius: 4px;">
				<el-button type="success" style="margin-left:10px;" @click.active.prevent="handleAdd($event)" id="add" data-title="新增菜单"><i class="el-icon-plus"></i> 新增菜单</el-button>
				<el-button type="success" type="small" style="float:right; line-height: 38px;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
			</div>

			<el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
				<el-table-column type="selection" width="55"></el-table-column>
				<el-table-column prop="moduleid" label="ID" sortable width="80"></el-table-column> 
				<el-table-column prop="i_order" label="排序" width="100" show-overflow-tooltip></el-table-column> 
				<el-table-column prop="name" label="名称" min-width="150" show-overflow-tooltip></el-table-column>
				<el-table-column prop="m_parentid" label="父级ID" min-width="120" show-overflow-tooltip></el-table-column>
				<el-table-column prop="m_description" label="说明" min-width="180" show-overflow-tooltip></el-table-column>
				<el-table-column prop="m_pagecode" label="唯一码" min-width="140" show-overflow-tooltip></el-table-column>
				<el-table-column prop="m_controller" label="控制器" min-width="180" show-overflow-tooltip></el-table-column>
				<el-table-column prop="m_type" label="菜单类型" min-width="130" show-overflow-tooltip>
					<template scope="scope">
						<el-tag type="primary" v-if="parseInt(scope.row.m_type) == 1">模块</el-tag>
						<el-tag type="warning" v-if="parseInt(scope.row.m_type) == 2">操作</el-tag>
					</template>
				</el-table-column>
                <el-table-column prop="is_top" label="顶部展示" min-width="130" show-overflow-tooltip>
					<template scope="scope">
						<el-button size="small" @click="handleTopStatus(scope.row.moduleid, 2)" type="success" v-if="parseInt(scope.row.is_top) == 1">支持</el-button>
						<el-button size="small" @click="handleTopStatus(scope.row.moduleid, 1)" type="warning" v-if="parseInt(scope.row.is_top) == 2">不支持</el-button>
					</template>
                </el-table-column>
                <el-table-column prop="m_close" label="开启状态" min-width="130" show-overflow-tooltip>
					<template scope="scope">
						<el-button size="small" @click="handleCloseStatus(scope.row.moduleid, 2)" type="success" v-if="parseInt(scope.row.m_close) == 1">开启</el-button>
						<el-button size="small" @click="handleCloseStatus(scope.row.moduleid, 1)" type="warning" v-if="parseInt(scope.row.m_close) == 2">关闭</el-button>
					</template>
                </el-table-column>
				<el-table-column prop="addtime" label="创建时间" width="160" show-overflow-tooltip></el-table-column>
				<el-table-column prop="updatetime" label="最近时间" width="160" show-overflow-tooltip></el-table-column>
				<el-table-column label="操作" fixed="right" width="145">
					<template scope="scope">
						<el-button size="small" style="color: #333" type="text" style="color:#ff4949;" @click.native.prevent="handleDel(scope.row.moduleid, scope.$index, list)"><i class="el-icon-delete"></i></el-button>
						<el-button size="small" style="color: #333" type="text" data-title="更新信息" @click.native.prevent="handleEdit($event, scope.row.moduleid, scope.$index, scope.row)"><i class="el-icon-edit"></i></el-button>
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
			fullscreenLoading: false,
			list: [],
			multipleSelection: [],
			list_url: "<?php echo base_url('admin/menuListAjax'); ?>",
			del_url: "<?php echo base_url('admin/delajax'); ?>?type=menu",
			add_url: "<?php echo base_url('admin/addmenu'); ?>",
			edit_url: "<?php echo base_url('admin/editmenu'); ?>",
			close_url: "<?php echo base_url('admin/handleCloseStatus'); ?>",
			top_url: "<?php echo base_url('admin/handleTopStatus'); ?>",
			currentPage: 1,
            page_sizes: [1, 10, 20, 30, 50, 100],
            page_size: 1,
            pagenum: 0,
            count: 0,
		},		
		created: function() {
            var filters = {"p": this.currentPage, "s": this.page_size};
            this.listAjax(filters);
        },
		methods: {
            handleTopStatus: function(id, status) {
				$.ajax ({
                    type: 'post',
                    url: this.top_url,
                    data: {id: id, status: status},
                    dataType: "json",
					success: function(data) {
                        is_ok = _.isObject(data) && _.has(data, 'code') && _.get(data, 'code') == 200;
                        if (is_ok) {
                            var filters = {"p": vm.currentPage, 's': vm.page_size};
                            vm.listAjax(filters);
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
			handleCloseStatus: function(id, status) {
				$.ajax ({
                    type: 'post',
                    url: this.close_url,
                    data: {id: id, status: status},
                    dataType: "json",
					success: function(data) {
                        is_ok = _.isObject(data) && _.has(data, 'code') && _.get(data, 'code') == 200;
                        if (is_ok) {
                            var filters = {"p": vm.currentPage, 's': vm.page_size};
                            vm.listAjax(filters);
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
                            var filters = {"p": vm.currentPage, 's': vm.page_size};
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
            handleCurrentChange: function (val) {
                this.refreshstatus = true;
                this.currentPage = val;
                window.history.pushState(null, null, '?p='+val);
                var filters = {"p": this.currentPage, 's': this.page_size};
                vm.listAjax(filters);
            },
            handleSizeChange: function (size) {
                this.page_size = size;
                var filters = {"p": this.currentPage, 's': this.page_size};
                this.listAjax(filters);
            },
            handleSearch: function () {
                // this.page_size = size;
                var filters = {"p": this.currentPage, 's': this.page_size};
                vm.listAjax(filters);
            },
            handleRefresh: function() {
				this.refreshstatus = true;
                filters = {'s': this.page_size};
				this.listAjax(filters);
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
			
		}
	})
</script>