<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">

        <!--search-->
        <div class="gx-search">
            <el-form :inline="true" v-model="search" >
                <el-form-item label="用户类型">
                    <el-select v-model="search.usertype" placeholder="请选择用户类型">
                        <el-option v-for="item in utype_options" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="解决状态">
                    <el-select v-model="search.solved" placeholder="请选择状态">
                        <el-option v-for="item in solve_options" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="用户信息">
                    <el-input v-model="search.keywords" placeholder="姓名 | 手机号"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" icon="search" @click="handleSearch">搜索</el-button>
                </el-form-item>
            </el-form>
        </div>
        <!--end search-->

        <!--list-->
        <div class="gx-iframe-content">
            <div class="gx-iframe-operation" style="margin-bottom: 20px; border-radius: 4px;">
                <el-button type="success" style="float: right; line-height: 38px;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
            </div>
            
            <el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange">
                <el-table-column fixed type="selection" width="50"></el-table-column>
				<el-table-column prop="id" label="ID" sortable width="80"></el-table-column> 
				<el-table-column prop="name" label="姓名" width="150" show-overflow-tooltip></el-table-column> 
				<el-table-column prop="phone" label="手机号" width="150" show-overflow-tooltip></el-table-column> 
				<el-table-column prop="user_type" label="用户类型" width="130" :filters="utype" :filter-method="filterTag">
					<template scope="scope">
				        <el-tag type="success"  v-if="parseInt(scope.row.user_type) == 0">学员</el-tag>
				        <el-tag type="primary"  v-if="parseInt(scope.row.user_type) == 1">教练</el-tag>
			      	</template>
				</el-table-column>
				<el-table-column prop="content" label="内容" min-width="150" show-overflow-tooltip></el-table-column> 
                <el-table-column prop="is_solved" label="解决状态" width="130" :filters="solvedtype" :filter-method="filterTags">
					<template scope="scope">
				        <el-button type="success" @click="handleSolvedStatus(scope.row.id, 2)" size="small" v-if="parseInt(scope.row.is_solved) == 1">已解决</el-button>
				        <el-button type="warning"  @click="handleSolvedStatus(scope.row.id, 1)" size="small" v-if="parseInt(scope.row.is_solved) == 2">未解决</el-button>
			      	</template>
				</el-table-column>
                <el-table-column prop="addtime" label="提交时间" width="175" sortable></el-table-column>

				<el-table-column label="操作" fixed="right" width="100">
					<template scope="scope">
						<a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.id, scope.$index, list)"><i class="el-icon-delete"></i></a>
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
        <!--end list-->

	</div>
</div>
<script>
	var vm = new Vue({
		el: '#app',
		data: {
            refreshstatus: false,
            fullscreenLoading: true,
            list: [],
            multipleSelection: [],
            utype_options: [
                {value: '', label: "--不限类型--"},
                {value: 0, label: "学员"},
                {value: 1, label: "教练"},
            ],
            solve_options: [
                {value: '', label: "--不限状态--"},
                {value: 1, label: "已解决"},
                {value: 2, label: "未解决"},
            ],
            utype: [
                {text: '学员',value: 0},
                {text: '教练',value: 1},
            ],
            solvedtype: [
                {text: '已解决', value: 1},
                {text: '未解决', value: 2},
            ],
            list_url: "<?php echo base_url('app/feedbackAjax')?>",
            solve_url: "<?php echo base_url('app/handleSolvedStatus')?>",
            del_url: "<?php echo base_url('app/delFeedBackAjax')?>",
            currentPage: 1,
            page_sizes: [10, 20, 30, 50, 100],
            page_size: 10,
            pagenum: 0,
            count: 0,
            search: {
                usertype: '',
                solved: '',
                keywords: ''
            }

		},
        created: function() {
            var filters = {"p": this.currentPage, "usertype": this.search.usertype, "solved": this.search.solved, "keywords": this.search.keywords, 's': this.page_size};
            this.listAjax(filters);
        },
		methods: {
           
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
                            vm.currentPage = _.get(res, 'data.p');
                            vm.pagenum = _.get(res, 'data.pagenum');
                            vm.count = _.get(res, 'data.count');
                            vm.list = _.get(res, 'data.list');
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
            handleSolvedStatus: function (id, status) {
                $.ajax ({
                    type: 'post',
                    url: this.solve_url,
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
                            var filters = {"p": vm.currentPage, "usertype": vm.search.usertype, "solved": vm.search.solved, "keywords": vm.search.keywords, 's': vm.page_size};
                            vm.listAjax(filters);
                        }
                    },
                    error: function(e) {
                        vm.messageNotice('error', '网络异常！');
                    }
                });
            },
            filterTag: function (value, row) {
                return row.user_type == value;
            },
            filterTags: function (value, row) {
                return row.is_solved == value;
            },
            handleCurrentChange: function (val) {
                this.refreshstatus = false;
                this.currentPage = val;
                window.history.pushState(null, null, '?p='+val+'&usertype='+this.search.usertype+'&solved='+this.search.solved+'&keywords='+this.search.keywords);
                var filters = {"p": this.currentPage, "usertype": this.search.usertype, "solved": this.search.solved, "keywords": this.search.keywords, 's': this.page_size};
                vm.listAjax(filters);
            },
            handleSizeChange: function (size) {
                this.page_size = size;
                var filters = {"p": this.currentPage, "usertype": this.search.usertype, "solved": this.search.solved, "keywords": this.search.keywords, 's': this.page_size};
                this.listAjax(filters);
            },
            handleSearch: function () {
                var filters = {"p": this.currentPage, "usertype": this.search.usertype, "solved": this.search.solved, "keywords": this.search.keywords, 's': this.page_size};
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
            },

		}
	})
</script>