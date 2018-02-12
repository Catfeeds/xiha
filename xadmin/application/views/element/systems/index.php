<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">

        <!--search-->
        <div class="gx-search">
            <el-form :inline="true" v-model="search">
                <el-form-item label="角色">
                    <el-select @change="handelChange" v-model="search.l_role_id" placeholder="请选择角色">
                        <el-option v-for="item in role_options" :label="item.s_rolename" :value="item.l_role_id"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="关键词">
                    <el-input v-model="search.keywords" placeholder="用户名 | 日志"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" icon="search" @click="handleSearch">搜索</el-button>
                </el-form-item>
            </el-form>
        </div>
        <!--end search-->

        <!--list-->
        <div class="gx-iframe-content">
            <!--add-->
            <div class="gx-iframe-operation" style="margin-bottom: 20px; border-radius: 4px;">
                <el-button type="success" style="float: right; line-height: 38px;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
            </div>
            
            <el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange">
                <el-table-column fixed type="selection" width="50"></el-table-column>
				<el-table-column prop="id" label="ID" sortable min-width="80" show-overflow-tooltip></el-table-column> 
				<el-table-column prop="title" label="日志" min-width="130" show-overflow-tooltip></el-table-column> 
				<el-table-column prop="content" label="执行者" width="150" show-overflow-tooltip></el-table-column> 
				<el-table-column prop="s_rolename" label="角色" min-width="150" show-overflow-tooltip></el-table-column> 
                <el-table-column prop="model" label="模型 | 模型ID" min-width="150" show-overflow-tooltip>
                    <template scope="scope">
                        <span>{{ scope.row.model }}</span> | 
                        <span style="color: red">{{ scope.row.record_id }}</span>
                    </template>
                </el-table-column> 
                <el-table-column prop="remark" label="备注" min-width="200" show-overflow-tooltip></el-table-column> 
                <el-table-column prop="create_time" label="创建时间" width="175" sortable></el-table-column>

				<el-table-column label="操作" fixed="right" width="140">
					<template scope="scope">
						<a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.id, scope.$index, list)"><i class="el-icon-delete"></i></a>
					</template>
				</el-table-column>
			</el-table>
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
        <!--end list-->

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
            role_options: "",
            role_url : "<?php echo base_url('systems/rolelistAjax')?>",
            list_url: "<?php echo base_url('systems/listAjax')?>?type=log",
            del_url: "<?php echo base_url('systems/delAjax')?>?type=log",
            currentPage: 1,
            page_sizes: [10, 20, 30, 50, 100],
            page_size: 10,
            pagenum: 0,
            count: 0,
            search: {
                l_role_id: '',
                keywords: ''
            }

		},
        created: function() {
            var filters = {"p": this.currentPage, "role": this.search.l_role_id, "keywords": this.search.keywords, "s": this.page_size};
            this.listAjax(filters);
            this.rolelistAjax();
        },
		methods: {
            handelChange: function (role_id) {
                vm.search.l_role_id = role_id;
                this.rolelistAjax();
            },
            handleSelectionChange: function(val) {
				this.multipleSelection = val;
			},
            rolelistAjax : function () {
                $.ajax({
                    type: "post",
                    url: this.role_url,
                    dataType: "json",
                    async: true,
                    success: function (res) {
                        isResOk = _.isObject(res) && _.has(res, 'code') && _.get(res, 'code') == 200;
                        if (isResOk) {
                            vm.role_options = _.get(res, 'data');
                        } 
                    },
                    error: function (e) {
                        vm.messageNotice('error', '加载出错！');
                    } 
                });
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
            handleDel: function(id, index, rows) {
				this.$confirm('此操作将永久删除, 是否继续?', '提示', {
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
            delAjax: function(id) {
                $.ajax({
                    type: 'post',
                    url: this.del_url,
                    data: {id: id},
                    dataType: 'json',
                    success: function(data) {
                        is_ok = _.isObject(data) && _.has(data, 'code') && _.get(data, 'code') == 200
                        if (is_ok) {
                            var filters = {"p": vm.currentPage, "role": vm.search.l_role_id, "keywords": vm.search.keywords, 's': vm.page_size};
                            vm.listAjax(filters);
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
                window.history.pushState(null, null, '?p='+val+'&role='+this.search.l_role_id+'&keywords='+this.search.keywords);
                var filters = {"p": this.currentPage, "role": this.search.l_role_id, "keywords": this.search.keywords, 's': this.page_size};
                vm.listAjax(filters);
            },
            handleSizeChange: function (size) {
                this.page_size = size;
                var filters = {"p": this.currentPage, "role": this.search.l_role_id, "keywords": this.search.keywords, 's': this.page_size};
                this.listAjax(filters);
            },
            handleSearch: function () {
                // this.page_size = size;
                var filters = {"p": this.currentPage, "role": this.search.l_role_id, "keywords": this.search.keywords, 's': this.page_size};
                vm.listAjax(filters);
            },
            handleRefresh: function() {
				this.refreshstatus = true;
				this.listAjax();
			},
            messageNotice: function(type, msg) {
                this.$message({
                    type: type,
                    message: msg
                });
            }
            
		}

	})
</script>