<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">
		<div class="gx-search">
            <el-form :inline="true" v-model="search" >
                <el-form-item label="关键词">
					<el-input v-model="search.keywords" placeholder="驾校名称 | 报名点"><el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" icon="search" @click="handleSearch">搜索</el-button>
                </el-form-item> 
            </el-form>
        </div>

		<div class="gx-iframe-content">
			<div class="gx-iframe-operation" style="margin-bottom: 20px; border-radius: 4px;">
				<!-- <el-button type="danger" style="margin-left:10px;"><i class="el-icon-delete"></i> 批量删除</el-button> -->
				<el-button type="success" style="margin-left:10px;" @click.active.prevent="handleAdd($event)" id="add" data-title="新增报名点"><i class="el-icon-plus"></i> 新增报名点</el-button>
				<el-button type="success" type="small" style="float:right; line-height: 38px;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
			</div>
			<el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
				<el-table-column fixed type="selection" width="55"></el-table-column> 
				<el-table-column prop="id" label="ID" sortable width="80"></el-table-column> 
				<el-table-column prop="order" label="排序" min-width="80" show-overflow-tooltip></el-table-column>
				<el-table-column prop="school_name" label="驾校名称" min-width="180" show-overflow-tooltip></el-table-column>
                <el-table-column prop="name" label="报名点" min-width="180" show-overflow-tooltip></el-table-column>
                <el-table-column prop="phone" label="报名点电话" min-width="180" show-overflow-tooltip></el-table-column>
                <el-table-column prop="imgurl" label="风采图" width="180">
					<template scope="scope">
                        <a href="#"  v-for="location_imgurl_0 in scope.row.location_imgurl" v-if="scope.row.location_imgurl['location_imgurl_0'] != '' " data-title="车辆图片" style="color: #12cf66" @click="showPic($event, scope.row.id, scope.$index, scope.row, location_imgurl_0)">预览；<a>
                        <span v-if="scope.row.location_imgurl == ''">--</span>
                    </template>
                </el-table-column> 
				<el-table-column prop="addtime" label="最近时间" min-width="180" sortable show-overflow-tooltip></el-table-column> 
				<el-table-column label="操作" fixed="right" width="150">
					<template scope="scope">
						<a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.id, scope.$index, list)"><i class="el-icon-delete"></i></a>
						<a title="编辑" data-title="编辑场地" style="margin-left:8px;" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
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
			list: [],
			multipleSelection: [],
			list_url: "<?php echo base_url('school/signplaceAjax'); ?>",
			del_url: "<?php echo base_url('school/delsignplace'); ?>",
			add_url: "<?php echo base_url('school/addsignplace'); ?>",
			edit_url: "<?php echo base_url('school/editsignplace'); ?>",
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
			var filters = {"p": this.currentPage, "keywords": this.search.keywords, 's': this.page_size};
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
					success: function(res) {
						vm.fullscreenLoading = false;			
						vm.refreshstatus = false;
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
				})
			},
			showPic: function(e, id, index, row, content){
                layer.open({
					title: e.currentTarget.getAttribute('data-title')
                    ,type: 2
                    ,skin: 'layui-layer-rim' //加上边框
                    ,area: ['100%', '100%'] //宽高
                    ,content: [content, 'no']
                    ,yes: function(){
						layer.closeAll();
					}
                });
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
						} else {
							vm.messageNotice('error', data.msg);			
                        }
					},
					error: function() {
						vm.fullscreenLoading = false;									
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				})
			},
			handleSizeChange: function (size) {
                this.page_size = size;
                var filters = {"p": this.currentPage, "keywords": this.search.keywords, 's': this.page_size};
                this.listAjax(filters);
            },
			handleSearch: function () {
                var filters = {"p": this.currentPage, "keywords": this.search.keywords, 's': this.page_size};
                vm.listAjax(filters);
            },
			handleCurrentChange: function(val) {
				this.refreshstatus = true;
                this.currentPage = val;
                // window.history.pushState(null, null, '?p='+val+'&keywords='+this.search.keywords);
                var filters = {"p": this.currentPage, "keywords": this.search.keywords, 's': this.page_size};
                vm.listAjax(filters);
			},
            filterTag: function(value, row) {
				return row.is_show == value;
			},
			handleRefresh: function() {
				this.refreshstatus = true;
				this.listAjax(this.currentPage);
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
				this.showLayer(e, '480px', 'lb', this.preview_url+'?id='+id);
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