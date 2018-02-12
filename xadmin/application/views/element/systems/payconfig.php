<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">

        <!--search-->
        <div class="gx-search">
            <el-form :inline="true" v-model="search">
                <el-form-item label="开启状态">
                    <el-select v-model="search.is_open" placeholder="--不限状态--">
                        <el-option label="请选择状态" value=""></el-option>
                        <el-option label="开启" value="1"></el-option>
                        <el-option label="未开启" value="2"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="银行账号否">
                    <el-select v-model="search.bank" placeholder="--不限是否--">
                        <el-option label="请选择是否状态" value=""></el-option>
                        <el-option label="是" value="1"></el-option>
                        <el-option label="否" value="2"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="支付方式">
                    <el-select v-model="search.pay_type" placeholder="--不限方式--">
                        <el-option label="请选择支付状态" value=""></el-option>
                        <el-option label="支付宝" value="1"></el-option>
                        <el-option label="线下" value="2"></el-option>
                        <el-option label="微信" value="3"></el-option>
                        <el-option label="银联" value="4"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="关键词">
                    <el-input v-model="search.keywords" placeholder="中文名 | 英文名"></el-input>
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
                <el-button type="success" id="add" @click.active.prevent="handleAdd($event)" data-title="新增配置" style="margin-left: 10px;"><i class="el-icon-plus"> 新增配置</i></el-button>
                <el-button type="success" style="float: right; line-height: 38px;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
            </div>
            
            <el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange">
                <el-table-column fixed type="selection" width="50"></el-table-column>
				<el-table-column prop="id" label="ID" sortable width="80"  show-overflow-tooltip></el-table-column> 
                <el-table-column prop="order" label="排序" min-width="100" show-overflow-tooltip></el-table-column> 
				<el-table-column prop="account_name" label="账户名" min-width="130" show-overflow-tooltip></el-table-column> 
				<el-table-column prop="account_slug" label="英文名" min-width="130" show-overflow-tooltip></el-table-column> 
                <el-table-column prop="pay_type" label="支付方式" width="140" :filters="paytypefilters" :filter-method="filterTag">
					<template scope="scope">
				        <el-tag type="success"  v-if="parseInt(scope.row.pay_type) == 1">支付宝</el-tag>
				        <el-tag type="primary"  v-if="parseInt(scope.row.pay_type) == 2">线下</el-tag>
				        <el-tag type="danger"  v-if="parseInt(scope.row.pay_type) == 3">微信</el-tag>
				        <el-tag type="warning"  v-if="parseInt(scope.row.pay_type) == 4">银联</el-tag>
				        <el-tag type="default"  v-if="parseInt(scope.row.pay_type) == 0">未知</el-tag>
			      	</template>
				</el-table-column>
                <el-table-column prop="is_bank" label="银行账户?" width="150" :filters="bankfilters" :filter-method="filterTags">
					<template scope="scope">
				        <el-tag type="success"  v-if="parseInt(scope.row.is_bank) == 1">是</el-tag>
				        <el-tag type="warning"  v-if="parseInt(scope.row.is_bank) == 2">否</el-tag>
			      	</template>
				</el-table-column>
				<el-table-column prop="account_description" label="说明" min-width="150" show-overflow-tooltip></el-table-column> 
                <el-table-column prop="is_open" label="开启?" width="140" :filters="openfilters" :filter-method="filter">
					<template scope="scope">
				        <el-button @click="handleOpenStatus(scope.row.id, 2)" size="small" type="success" v-if="parseInt(scope.row.is_open) == 1">是</el-button>
				        <el-button @click="handleOpenStatus(scope.row.id, 1)" size="small" type="warning" v-if="parseInt(scope.row.is_open) == 2">否</el-button>
			      	</template>
				</el-table-column>
                <el-table-column prop="addtime" label="新增时间" min-width="175" sortable></el-table-column>
				<el-table-column label="操作" fixed="right" width="140">
					<template scope="scope">
						<a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.id, scope.$index, list)"><i class="el-icon-delete"></i></a>
                        <a title="编辑" data-title="更新配置" style="margin-left:8px;" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
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
            fullscreenLoading: false,
            list: [],
            multipleSelection: [],
            paytypefilters: [ 
                {text: '支付宝', value: 1},
                {text: '线下', value: 2},
                {text: '微信', value: 3},
                {text: '银联', value: 4}
            ],
            bankfilters: [ 
                {text: '是', value: 1},
                {text: '否', value: 2},
            ],
            openfilters: [ 
                {text: '是', value: 1},
                {text: '否', value: 2},
            ],
            list_url: "<?php echo base_url('systems/listAjax')?>?type=pay",
            del_url: "<?php echo base_url('systems/delAjax')?>?type=pay",
            add_url: "<?php echo base_url('systems/addpay')?>",
            edit_url: "<?php echo base_url('systems/editpay')?>",
            open_url: "<?php echo base_url('systems/handleOpenStatus')?>",
            currentPage: 1,
            page_sizes: [10, 20, 30, 50, 100],
            page_size: 10,
            pagenum: 0,
            count: 0,
            search: {
                is_open: '',
                bank: '',
                pay_type: '',
                keywords: ''
            }

		},
        created: function() {
            var filters = {"p": this.currentPage, "open": this.search.is_open, "bank": this.search.bank, "pt": this.search.pay_type, "keywords": this.search.keywords, "s": this.page_size};
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
                            var filters = {"p": vm.currentPage, "open": this.search.is_open, "bank": this.search.bank, "pt": this.search.pay_type, "keywords": vm.search.keywords, 's': vm.page_size};
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
                window.history.pushState(null, null, '?p='+val+'&open='+this.search.is_open+'&bank='+this.search.bank+'&pt='+this.search.pay_type+'&keywords='+this.search.keywords);
                var filters = {"p": this.currentPage, "open": this.search.is_open, "bank": this.search.bank, "pt": this.search.pay_type, "keywords": this.search.keywords, 's': this.page_size};
                vm.listAjax(filters);
            },
            handleSizeChange: function (size) {
                this.page_size = size;
                var filters = {"p": this.currentPage, "open": this.search.is_open, "bank": this.search.bank, "pt": this.search.pay_type, "keywords": this.search.keywords, 's': this.page_size};
                this.listAjax(filters);
            },
            filterTag: function (value, row) {
                return row.pay_type == value;
            },
            filterTags: function (value, row) {
                return row.bank == value;
            },
            filter: function (value, row) {
                return row.is_open == value;
            },
            handleSearch: function () {
                var filters = {"p": this.currentPage, "open": this.search.is_open, "bank": this.search.bank, "pt": this.search.pay_type, "keywords": this.search.keywords, 's': this.page_size};
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
			}
		}

	})
</script>