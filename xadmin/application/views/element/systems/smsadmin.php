<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">

        <!--search-->
        <div class="gx-search">
            <el-form :inline="true" v-model="search">
                <el-form-item label="用户类型">
                    <el-select v-model="search.member_type" placeholder="--不限类型--">
                        <el-option v-for="item in mtype_options" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="是否已读">
                    <el-select v-model="search.is_read" placeholder="--不限状态--">
                        <el-option v-for="item in read_options" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="消息类型">
                    <el-select v-model="search.i_yw_type" placeholder="--不限类型--">
                        <el-option v-for="item in stype_options" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="关键词">
                    <el-input v-model="search.keywords" placeholder="标题 | 用户名 | 用户手机"></el-input>
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
                <el-button type="success" id="add" @click.active.prevent="handleAdd($event)" data-title="发送" style="margin-left: 10px;"><i class="el-icon-plus"> 发送</i></el-button>
                <el-button type="success" style="float: right; line-height: 38px;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
            </div>
            
            <el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange">
                <el-table-column fixed type="selection" width="50"></el-table-column>
				<el-table-column prop="id" label="ID" sortable width="80"  show-overflow-tooltip></el-table-column> 
                <el-table-column prop="dt_sender" label="发送时间" min-width="175" show-overflow-tooltip></el-table-column> 
				<el-table-column prop="s_beizhu" label="标题" min-width="150" show-overflow-tooltip></el-table-column> 
				<el-table-column prop="user_name" label="用户姓名" min-width="150" show-overflow-tooltip></el-table-column> 
				<el-table-column prop="user_phone" label="用户手机" min-width="150" show-overflow-tooltip></el-table-column> 
                <el-table-column prop="member_type" label="用户类型" width="120">
					<template scope="scope">
				        <el-tag type="success"  v-if="parseInt(scope.row.member_type) == 1">学员</el-tag>
				        <el-tag type="primary"  v-if="parseInt(scope.row.member_type) == 2">教练</el-tag>
			      	</template>
				</el-table-column>
				<el-table-column prop="s_content" label="内容" min-width="180" show-overflow-tooltip></el-table-column> 
                <el-table-column prop="i_yw_type" label="通知类型" width="150" :filters="stypefilters" :filter-method="filterTags">
					<template scope="scope">
				        <el-tag type="success"  v-if="parseInt(scope.row.i_yw_type) == 1">系统消息</el-tag>
				        <el-tag type="primary"  v-if="parseInt(scope.row.i_yw_type) == 2">订单消息</el-tag>
			      	</template>
				</el-table-column>
				<el-table-column prop="s_from" label="来源" min-width="130" show-overflow-tooltip></el-table-column> 
                <el-table-column prop="is_read" label="已读？" width="150" :filters="readfilters" :filter-method="filter">
					<template scope="scope">
				        <el-tag type="success"  v-if="parseInt(scope.row.is_read) == 1">已读</el-tag>
				        <el-tag type="primary"  v-if="parseInt(scope.row.is_read) == 2">未读</el-tag>
			      	</template>
				</el-table-column>
                <el-table-column prop="addtime" label="新增时间" min-width="175" sortable></el-table-column>
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
            mtype_options: [
                {value: '', label: "请选择用户类型"},
                {value: 1, label: "学员"},
                {value: 2, label: "教练"}
            ],
            read_options: [
                {value: '', label: "请选择是否已读"},
                {value: 1, label: "已读"},
                {value: 2, label: "未读"}
            ],
            stype_options: [
                {value: '', label: "请选择消息类型"},
                {value: 1, label: "系统消息"},
                {value: 2, label: "订单消息"}
            ],
            stypefilters: [ 
                {text: '系统消息', value: 1},
                {text: '订单消息', value: 2},
            ],
            readfilters: [ 
                {text: '已读', value: 1},
                {text: '未读', value: 2},
            ],
            list_url: "<?php echo base_url('systems/listAjax')?>?type=message",
            del_url: "<?php echo base_url('systems/delAjax')?>?type=message",
            add_url: "<?php echo base_url('systems/addsms')?>",
            currentPage: 1,
            page_sizes: [10, 20, 30, 50, 100],
            page_size: 10,
            pagenum: 0,
            count: 0,
            search: {
                member_type: 1,
                i_yw_type: '',
                is_read: '',
                keywords: ''
            }

		},
        created: function() {
            var filters = {"p": this.currentPage, "mt": this.search.member_type, "st": this.search.i_yw_type, "read": this.search.is_read, "keywords": this.search.keywords, "s": this.page_size};
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
            handleOpenStatus: function (id, status) {
                $.ajax ({
                    type: 'post',
                    url: this.open_url,
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
                            var filters = {"p": vm.currentPage, "mt": vm.search.member_type, "st": vm.search.i_yw_type, "read": vm.search.is_read, "keywords": vm.search.keywords, 's': vm.page_size};
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
                window.history.pushState(null, null, '?p='+val+'&mt='+this.search.member_type+'&st='+this.search.i_yw_type+'&read='+this.search.is_read+'&keywords='+this.search.keywords);
                var filters = {"p": this.currentPage, "mt": this.search.member_type, "st": this.search.i_yw_type, "read": this.search.is_read, "keywords": this.search.keywords, 's': this.page_size};
                vm.listAjax(filters);
            },
            handleSizeChange: function (size) {
                this.page_size = size;
                var filters = {"p": this.currentPage, "mt": this.search.member_type, "st": this.search.i_yw_type, "read": this.search.is_read, "keywords": this.search.keywords, 's': this.page_size};
                this.listAjax(filters);
            },
            filterTags: function (value, row) {
                return row.i_yw_type == value;
            },
            filter: function (value, row) {
                return row.is_read == value;
            },
            handleSearch: function () {
                var filters = {"p": this.currentPage, "mt": this.search.member_type, "st": this.search.i_yw_type, "read": this.search.is_read, "keywords": this.search.keywords, 's': this.page_size};
                vm.listAjax(filters);
            },
            handleRefresh: function() {
				this.refreshstatus = true;
				this.listAjax();
			},
            handleAdd: function(e) {
                this.showLayer(e, '60%', 'lb', this.add_url);
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
			}
		}

	})
</script>