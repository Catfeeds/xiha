<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">

        <!--search-->
        <div class="gx-search">
            <el-form :inline="true" v-model="search" >
                <el-form-item label="用户信息">
                    <el-input v-model="search.keywords" placeholder="名称 | 更新日志"></el-input>
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
                <el-button type="success" id="add" @click.active.prevent="handleAdd($event)" data-title="新增记录" style="margin-left: 10px;"><i class="el-icon-plus"> 新增记录</i></el-button>
                <el-button type="success" style="float: right; line-height: 38px;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
            </div>
            
            <el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange">
                <el-table-column fixed type="selection" width="50"></el-table-column>
				<el-table-column prop="id" label="ID" sortable width="80"  show-overflow-tooltip></el-table-column> 
				<el-table-column prop="client_name" label="名称" width="150"></el-table-column> 
				<el-table-column prop="os_type" label="系统类型" width="130" >
					<template scope="scope">
				        <el-tag type="danger"  v-if="parseInt(scope.row.os_type) == 1">windows</el-tag>
				        <el-tag type="success"  v-if="parseInt(scope.row.os_type) == 2">android</el-tag>
				        <el-tag type="primary"  v-if="parseInt(scope.row.os_type) == 3">ios</el-tag>
			      	</template>
				</el-table-column>
                <el-table-column prop="client_type" label="客户端类型" width="130" >
					<template scope="scope">
				        <el-tag type="success" v-if="parseInt(scope.row.client_type) == 1">喵咪鼠标</el-tag>
				        <el-tag type="primary" v-if="parseInt(scope.row.client_type) == 2">学员端</el-tag>
				        <el-tag type="warning" v-if="parseInt(scope.row.client_type) == 3">教练端</el-tag>
				        <el-tag type="danger" v-if="parseInt(scope.row.client_type) == 4">校长端</el-tag>
			      	</template>
				</el-table-column>
				<el-table-column label="下载" prop="download_url" width="130">
                    <template scope="scope">
                        <a :href="scope.row.download_url" v-if="scope.row.download_url != ''" style="color: #12cf66"><img src="<?php echo base_url('assets/element/images/download.png')?>" style="width: 22px;padding-top: 5px;"><a>
                        <a href="#" v-if="scope.row.download_url == ''">--<a>
                    </template>
                </el-table-column> 
				<el-table-column prop="version" label="版本号" min-width="150"></el-table-column> 
				<el-table-column prop="version_code" label="版本代号" min-width="130"></el-table-column> 
                <el-table-column prop="addtime" label="发布时间" width="175" sortable></el-table-column>
                <el-table-column prop="updatetime" label="最近时间" width="175" sortable></el-table-column>

				<el-table-column label="操作" fixed="right" width="140">
					<template scope="scope">
						<a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.id, scope.$index, list)"><i class="el-icon-delete"></i></a>
						<a title="编辑" data-title="更新记录" style="margin-left:8px;" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
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
            ostypefilters: [
                {text: 'windows', value: 1},
                {text: 'android',value: 2},
                {text: 'ios',value: 3},
            ],
            apptypefilters: [
                {text: '喵咪鼠标', value: 1},
                {text: '学员端', value: 2},
                {text: '教练端', value: 3},
                {text: '校长端', value: 4},
            ],
            list_url: "<?php echo base_url('product/listAjax')?>",
            add_url: "<?php echo base_url('product/add')?>",
            edit_url: "<?php echo base_url('product/edit')?>",
            del_url: "<?php echo base_url('product/delAjax')?>",
            currentPage: 1,
            page_sizes: [10, 20, 30, 50, 100],
            page_size: 10,
            pagenum: 0,
            count: 0,
            search: {
                keywords: ''
            }

		},
        created: function() {
            var filters = {"p": this.currentPage, "keywords": this.search.keywords, "s": this.page_size};
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
                            var filters = {"p": vm.currentPage, "keywords": vm.search.keywords, 's': vm.page_size};
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
                window.history.pushState(null, null, '?p='+val+'&keywords='+this.search.keywords);
                var filters = {"p": this.currentPage, "keywords": this.search.keywords, 's': this.page_size};
                vm.listAjax(filters);
            },
            handleSizeChange: function (size) {
                this.page_size = size;
                var filters = {"p": this.currentPage, "keywords": this.search.keywords, 's': this.page_size};
                this.listAjax(filters);
            },
            handleSearch: function () {
                // this.page_size = size;
                var filters = {"p": this.currentPage, "keywords": this.search.keywords, 's': this.page_size};
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