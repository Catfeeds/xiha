<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">
		<div class="gx-search">
			<el-form :inline="true" :model="search" class="demo-form-inline" v-if="school_id == ''">
				<el-form-item label="关键词">
					<el-input style="width: 260px" v-model="search.keywords"  placeholder="ID, 驾校名称" ></el-input>
				</el-form-item>
				<el-form-item>
					<el-button type="primary" icon="search" @click="handleSearch">搜索</el-button>
				</el-form-item>
			</el-form>
		</div>
		<div class="gx-iframe-content">
			<div class="gx-iframe-operation" style="margin-bottom: 20px; border-radius: 4px;">
				<!-- <el-button type="success" style="margin-left: 10px;" @click.active.prevent="handleAdd($event)" id="add" data-title="添加图片"><i class="el-icon-plus"></i> 添加图片</el-button> -->
				<el-button type="success" type="small" style="float:right; line-height: 38px;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
			</div>

			<el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
				<!-- <el-table-column fixed type="selection" width="50"></el-table-column> -->
				<el-table-column prop="l_school_id" label="ID" sortable width="80"></el-table-column> 
				<el-table-column prop="school_name" label="驾校名称" min-width="150" v-if="school_id == ''" show-overflow-tooltip> </el-table-column>
                <el-table-column prop="imgurl_one" label="轮播图（暂定最多五张）">
					<el-table-column prop="imgurl_one" label="图1" width="180">
                        <template scope="scope">
                            <el-col span="20">
                            <img :src="scope.row.http_imgurl_one" v-if="scope.row.http_imgurl_one != ''" data-title="驾校图片" style="width: 120px; height: 60px; cursor: pointer; border-radius: 5px;" @click="showPic($event, scope.row.l_school_id, scope.$index, scope.row, scope.row.http_imgurl_one)">
                            </el-col>
                            <i v-if="scope.row.imgurl_one != ''" fixed="right" style="color: red; cursor: pointer" class="el-icon-delete" @click="handleDel(scope.row.l_school_id, scope.row.imgurl_one, scope.$index, list)"></i>
                            <span v-if="scope.row.http_imgurl_one == ''">--</span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="imgurl_two" label="图2" width="180" >
                        <template scope="scope">
                            <el-col span="20">
                            <img :src="scope.row.http_imgurl_two" v-if="scope.row.http_imgurl_two != ''" data-title="驾校图片" style="width: 120px; height: 60px; cursor: pointer; border-radius: 5px;" @click="showPic($event, scope.row.l_school_id, scope.$index, scope.row, scope.row.http_imgurl_two)">
                            </el-col>
                            <i v-if="scope.row.imgurl_two != ''" fixed="right" style="color: red; cursor: pointer" class="el-icon-delete" @click="handleDel(scope.row.l_school_id, scope.row.imgurl_two, scope.$index, list)"></i>
                            <span v-if="scope.row.http_imgurl_two == ''">--</span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="imgurl_three" label="图3" width="180" >
                        <template scope="scope">
                            <el-col span="20">
                            <img :src="scope.row.http_imgurl_three" v-if="scope.row.http_imgurl_three != ''" data-title="驾校图片" style="width: 120px; height: 60px; cursor: pointer; border-radius: 5px;" @click="showPic($event, scope.row.l_school_id, scope.$index, scope.row, scope.row.http_imgurl_three)">
                            </el-col>
                            <i v-if="scope.row.imgurl_three != ''" fixed="right" style="color: red; cursor: pointer" class="el-icon-delete" @click="handleDel(scope.row.l_school_id, scope.row.imgurl_three, scope.$index, list)"></i>
                            <span v-if="scope.row.http_imgurl_three == ''">--</span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="imgurl_four" label="图4" width="180" >
                        <template scope="scope">
                            <el-col span="20">
                            <img :src="scope.row.http_imgurl_four" v-if="scope.row.http_imgurl_four != ''" data-title="驾校图片" style="width: 120px; height: 60px; cursor: pointer; border-radius: 5px;" @click="showPic($event, scope.row.l_school_id, scope.$index, scope.row, scope.row.http_imgurl_four)">
                            </el-col>
                            <i v-if="scope.row.imgurl_four != ''" fixed="right" style="color: red; cursor: pointer" class="el-icon-delete" @click="handleDel(scope.row.l_school_id, scope.row.imgurl_four, scope.$index, list)"></i>
                            <span v-if="scope.row.http_imgurl_four == ''">--</span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="imgurl_five" label="图5" width="180" >
                        <template scope="scope">
                            <el-col span="20">                     
                            <img :src="scope.row.http_imgurl_five" v-if="scope.row.http_imgurl_five != ''" data-title="驾校图片" style="width: 120px; height: 60px; cursor: pointer; border-radius: 5px;" @click="showPic($event, scope.row.l_school_id, scope.$index, scope.row, scope.row.http_imgurl_five)">
                            </el-col>
                            <i v-if="scope.row.imgurl_five != ''" fixed="right" style="color: red; cursor: pointer" class="el-icon-delete" @click="handleDel(scope.row.l_school_id, scope.row.imgurl_five, scope.$index, list)"></i>
                            <span v-if="scope.row.imgurl_five == ''">--</span>
                        </template>
                    </el-table-column>
				</el-table-column>
                <el-table-column prop="addtime" label="添加时间" sortable min-width="180" show-overflow-tooltip ></el-table-column>
				<el-table-column label="操作" fixed="right" width="120">
					<template scope="scope">
						<!-- <a title="预览" style="margin-left:8px; cursor: pointer" @click="handlePreview($event, scope.row.l_school_id, scope.$index, scope.row)"><i class="el-icon-view"></i></a> -->
						<!-- <a title="删除" style="margin-left:8px; cursor: pointer" @click="handleDel(scope.row.l_school_id, scope.row.s_coach_phone, scope.row.user_id, scope.$index, list)"><i class="el-icon-delete"></i></a> -->
						<a title="编辑" data-title="编辑驾校轮播图信息" style="margin-left:8px; cursor: pointer" @click="handleEdit($event, scope.row.l_school_id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
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
	var school_id = "<?php echo $school_id; ?>";
	var vm = new Vue({
		el: '#app',
		data: {
			refreshstatus: false,
			fullscreenLoading: false,
			multipleSelection: [],
			list: [],
			list_url: "<?php echo base_url('school/bannerlist'); ?>",
			edit_url: "<?php echo base_url('school/editBanner'); ?>",
			del_url: "<?php echo base_url('school/delBannerAjax'); ?>",
			add_url: "<?php echo base_url('school/add'); ?>",
			currentPage: 1,
			page_sizes: [10, 20, 30, 50, 100],
			page_size: 10,
			pagenum: 0,
			count: 0,
			search: {
				keywords: '',
			}
		},
		created: function() {
			var filter = {"p": this.currentPage, "keywords": this.search.keywords, "s": this.page_size};
			this.bannerlist(filter);
		},
		methods: {
			bannerlist: function(param) {
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
			delAjax: function(param) {
				$.ajax({
					type: 'post',
					url: this.del_url,
					data: param,
					dataType:"json",
					success: function(data) {
						vm.fullscreenLoading = false;			
						if(data.code == 200) {
							var filter = {"p": vm.currentPage, "keywords": vm.search.keywords, "s": vm.page_size};
							vm.bannerlist(filter);
							vm.messageNotice('success', data.msg);			
						}
					},
					error: function() {
						vm.fullscreenLoading = false;									
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				})
			},
            showPic: function(e, id, index, row, content){
                layer.open({
					title: e.currentTarget.getAttribute('data-title')
                    ,type: 2
                    ,skin: 'layui-layer-rim' //加上边框
                    ,area: ['100%', '100%'] //宽高
                    ,content: content
                    ,yes: function(){
						layer.closeAll();
					}
                });
            },
			handleSizeChange: function (size) {
				this.page_size = size;
				var filter = {"p": this.currentPage, "keywords": this.search.keywords, "s": this.page_size};
				this.bannerlist(filter);
			},
			handleCurrentChange: function(val) {
				this.refreshstatus = true;
				this.currentPage = val;
				// window.history.pushState(null, null, '?p='+val+'&status='+this.status+'&star='+this.search.star+'&verify='+this.search.verify+'&kwords='+this.search.kwords+'&value='+this.search.value);
				var filter = {"p": this.currentPage, "keywords": this.search.keywords, "s": this.page_size};
				this.bannerlist(filter);
			},
			handleRefresh: function() {
				this.refreshstatus = true;
				var filter = {"p": this.currentPage, "status": this.status, "s": this.page_size};
				this.bannerlist(filter);
			},
			handleSearch: function () {
				var filter = {"p": this.currentPage, "keywords": this.search.keywords, "s": this.page_size};
				this.bannerlist(filter);
			},
			filterTag: function(value, row) {
				return row.is_show == value;
			},
			handleSelectionChange: function(val) {
				this.multipleSelection = val;
			},
			handleAdd: function(e) {
				this.showLayer(e, '60%', 'rb', this.add_url);
			},
			handleEdit: function(e, id, index, row) {
				this.showLayer(e, '60%', 'lb', this.edit_url+'?id='+id);
			},
			handlePreview: function(e, id, index, row) {
				this.showLayer(e, '60%', 'rb', this.preview_url+'?id='+id);
			},
			handleDel: function(id, url, index, rows) {
				this.$confirm('此操作将永久删除此照片, 是否继续?', '提示', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning',
					callback: function(action) {
						if(action == 'confirm') {
							var filter = {'id': id, 'url': url,};
							vm.delAjax(filter);
							// rows.splice(index, 1);
							// vm.messageNotice('success', '删除成功!');
						} else {
							return false;
						}
					}
				});
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