<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">

        <!--search-->
        <div class="gx-search">
            <el-form :inline="true" v-model="search" >
                <el-form-item label="系统类型">
                    <el-select v-model="search.ostype" placeholder="系统类型">
                        <el-option v-for="item in ostype_options" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="客户端类型">
                    <el-select v-model="search.apptype" placeholder="客户端类型">
                        <el-option v-for="item in apptype_options" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="强制升级状态">
                    <el-select v-model="search.force" placeholder="强制升级状态">
                        <el-option v-for="item in force_options" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="用户信息">
                    <el-input v-model="search.keywords" placeholder="请输入app名称"><el-input>
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
				<el-table-column prop="app_name" label="App名称" width="150"></el-table-column> 
				<el-table-column prop="os_type" label="系统类型" width="130" :filters="ostypefilters" :filter-method="filterTag">
					<template scope="scope">
				        <el-tag type="success"  v-if="parseInt(scope.row.os_type) == 1">android</el-tag>
				        <el-tag type="primary"  v-if="parseInt(scope.row.os_type) == 2">ios</el-tag>
				        <el-tag type="danger"  v-if="parseInt(scope.row.os_type) == 3">windows</el-tag>
			      	</template>
				</el-table-column>
                <el-table-column prop="app_client" label="客户端类型" width="130" :filters="apptypefilters" :filter-method="filterTags">
					<template scope="scope">
				        <el-tag type="success" v-if="parseInt(scope.row.app_client) == 1">学员端</el-tag>
				        <el-tag type="primary" v-if="parseInt(scope.row.app_client) == 2">教练端</el-tag>
				        <el-tag type="warning" v-if="parseInt(scope.row.app_client) == 3">校长端</el-tag>
				        <el-tag type="danger" v-if="parseInt(scope.row.app_client) == 4">喵咪鼠标</el-tag>
			      	</template>
				</el-table-column>
				<el-table-column label="下载" prop="app_download_url" width="130">
                    <template scope="scope">
                        <a :href="scope.row.app_download_url" v-if="scope.row.app_download_url != ''" style="color: #12cf66"><img src="<?php echo base_url('assets/element/images/download.png')?>" style="width: 22px;padding-top: 5px;"><a>
                        <a href="#" v-if="scope.row.app_download_url == ''">--<a>
                    </template>
                </el-table-column> 
				<el-table-column prop="version" label="版本号" min-width="150"></el-table-column> 
				<el-table-column prop="version_code" label="版本代号" min-width="130"></el-table-column> 
				<el-table-column prop="force_least_updateversion" label="最低限制版本" width="150"></el-table-column> 
                <el-table-column prop="is_force" label="强制升级？" min-width="130" >
					<template scope="scope">
				        <el-button type="info" @click="handleForceStatus(scope.row.id, 1)" v-if="parseInt(scope.row.is_force) == 0" size="small">不强制</el-button>
				        <el-button type="warning" @click="handleForceStatus(scope.row.id, 0)" v-if="parseInt(scope.row.is_force) == 1" size="small">强制</el-button>
			      	</template>
				</el-table-column>
                <el-table-column prop="addtime" label="发布时间" width="175" sortable></el-table-column>

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
            fullscreenLoading: true,
            list: [],
            multipleSelection: [],
            ostype_options: [
                {value: '', label: "--不限类型--"}, 
                {value: 1, label: "android"}, 
                {value: 2, label: "ios"}, 
                {value: 3, label: "windows"}
            ],
            apptype_options: [
                {value: '', label: "--不限类型--"}, 
                {value: 1, label: "学员端"}, 
                {value: 2, label: "教练端"}, 
                {value: 3, label: "校长端"}, 
                {value: 4, label: "喵咪鼠标"}
            ],
            force_options: [
                {value: '', label: "--不限状态--"}, 
                {value: 0, label: "不强制"}, 
                {value: 1, label: "强制"}
            ],
            ostypefilters: [
                {text: 'android',value: 1},
                {text: 'ios',value: 2},
                {text: 'windows', value: 3}
            ],
            apptypefilters: [
                {text: '学员端', value: 1},
                {text: '教练端', value: 2},
                {text: '校长端', value: 3},
                {text: '喵咪鼠标', value: 4}
            ],
            list_url: "<?php echo base_url('app/listAjax')?>",
            force_url: "<?php echo base_url('app/handleForceStatus')?>",
            add_url: "<?php echo base_url('app/add')?>",
            edit_url: "<?php echo base_url('app/edit')?>",
            del_url: "<?php echo base_url('app/delAjax')?>",
            currentPage: 1,
            page_sizes: [10, 20, 30, 50, 100],
            page_size: 10,
            pagenum: 0,
            count: 0,
            search: {
                ostype:'',
                apptype: '',
                force: '',
                keywords: ''
            }

		},
        created: function() {
            var filters = {"p": this.currentPage, "ostype": this.search.ostype, "apptype": this.search.apptype, "force": this.search.force, "keywords": this.search.keywords, "s": this.page_size};
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
                            var filters = {"p": vm.currentPage, "ostype": vm.search.ostype, "apptype": vm.search.apptype, "force": vm.search.force, "keywords": vm.search.keywords, 's': vm.page_size};
                            vm.listAjax(filters);
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
                window.history.pushState(null, null, '?p='+val+'&ostype='+this.search.ostype+'&apptype='+this.search.apptype+'&force='+this.search.force+'&keywords='+this.search.keywords);
                var filters = {"p": this.currentPage, "ostype": this.search.ostype, "apptype": this.search.apptype, "force": this.search.force, "keywords": this.search.keywords, 's': this.page_size};
                vm.listAjax(filters);
            },
            handleSizeChange: function (size) {
                this.page_size = size;
                var filters = {"p": this.currentPage, "ostype": this.search.ostype, "apptype": this.search.apptype, "force": this.search.force, "keywords": this.search.keywords, 's': this.page_size};
                this.listAjax(filters);
            },
            handleSearch: function () {
                // this.page_size = size;
                var filters = {"p": this.currentPage, "ostype": this.search.ostype, "apptype": this.search.apptype, "force": this.search.force, "keywords": this.search.keywords, 's': this.page_size};
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