<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">
		<div class="gx-search">
            <el-form :inline="true" v-model="search" >
                <el-form-item label="上架状态">
					<el-select v-model="search.deleted" placeholder="--不限状态--">
						<el-option v-for="item in deleted_options" :label="item.label" :value="item.value"></el-option>
					</el-select>
				</el-form-item>
				<el-form-item label="套餐否">
					<el-select v-model="search.package" placeholder="--不限状态--">
						<el-option v-for="item in package_options" :label="item.label" :value="item.value"></el-option>
					</el-select>
				</el-form-item>
				<el-form-item label="推荐否">
					<el-select v-model="search.promote" placeholder="--不限状态--">
						<el-option v-for="item in promote_options" :label="item.label" :value="item.value"></el-option>
					</el-select>
                </el-form-item>
                <el-form-item label="关键词">
					<el-input v-model="search.keywords" placeholder="驾校名称|班制名称|教练名称"><el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" icon="search" @click="handleSearch">搜索</el-button>
                </el-form-item> 
            </el-form>
        </div>

		<div class="gx-iframe-content">
			<div class="gx-iframe-operation" style="margin-bottom: 20px; border-radius: 4px;">
				<!-- <el-button type="danger" style="margin-left:10px;"><i class="el-icon-delete"></i> 批量删除</el-button> -->
				<el-button type="success" style="margin-left:10px;" @click.active.prevent="handleAdd($event)" id="add" data-title="添加班制"><i class="el-icon-plus"></i> 添加班制</el-button>
				<el-button type="success" type="small" style="float:right; line-height: 38px;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
			</div>

			<!--table start-->
			<el-tabs v-model="activeName" @tab-click="handleClick">
				<el-tab-pane label="驾校班制" name="first">
					<el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
						<!-- <el-table-column fixed type="selection" width="55"></el-table-column> -->
						<el-table-column type="expand">
							<template scope="props">
								<el-form label-position="left" inline class="demo-table-expand">
									<el-form-item label="驾校名称">
										<span>{{ props.row.school_name }}</span>
									</el-form-item>
									<el-form-item label="班制名称">
										<span>{{ props.row.sh_title }}</span>
									</el-form-item>
									<el-form-item label="牌照名称">
										<span>{{ props.row.sh_license_name }}</span>
									</el-form-item>
									<el-form-item label="标签一">
										<span>{{ props.row.sh_tag }}</span>
									</el-form-item>
									<el-form-item label="标签二">
										<span>{{ props.row.sh_description_1 }}</span>
									</el-form-item>
									<el-form-item label="新增时间">
										<span>{{ props.row.addtime }}</span>
									</el-form-item>
								</el-form>
							</template>
						</el-table-column>
						<el-table-column prop="id" label="ID" sortable width="80"></el-table-column> 
						<el-table-column prop="sh_title" label="班制名称" width="180"></el-table-column> 
						<el-table-column prop="sh_type" label="班制类型" width="100" show-overflow-tooltip>
							<template scope="scope">
								<el-tag type="danger" v-if="scope.row.sh_type === '1'" close-transition>计时班</el-tag>
								<el-tag type="primary" v-else close-transition>非计时班</el-tag>
							</template>
						</el-table-column>
						<el-table-column prop="school_name" v-if="school_id == ''" label="所属驾校" width="150" show-overflow-tooltip></el-table-column>
						 <!-- <el-table-column prop="coach_name" label="所属教练" width="120" show-overflow-tooltip></el-table-column>  -->
						<el-table-column prop="sh_original_money" label="原价 | 最终价" width="160" show-overflow-tooltip>
							<template scope="scope">
								<span style="color: #888">{{scope.row.sh_original_money}}</span> |
								<span style="color: #ff4949">{{scope.row.sh_money}}</span>
							</template>
						</el-table-column>
						<el-table-column prop="sh_license_name" label="牌照" width="70" show-overflow-tooltip></el-table-column>
						<el-table-column prop="is_package" label="是否套餐" width="100" show-overflow-tooltip>
							<template scope="scope">
								<el-button type="success" @click="handlePackage(scope.row.id, 2)" size="small" v-if="scope.row.is_package === '1'" close-transition>是</el-button>
								<el-button type="warning" @click="handlePackage(scope.row.id, 1)" size="small" v-else close-transition>否</el-button>
							</template>
						</el-table-column>
						<el-table-column prop="is_promote" label="是否促销" width="100" show-overflow-tooltip>
							<template scope="scope">
								<el-button type="success" @click="handlePromoteStatus(scope.row.id, 2)" size="small" v-if="scope.row.is_promote === '1'" close-transition>是</el-button>
								<el-button type="warning" @click="handlePromoteStatus(scope.row.id, 1)" size="small" v-else close-transition>否</el-button>
							</template>
						</el-table-column>
						<el-table-column prop="sh_imgurl" label="班制图片" width="150">
							<template scope="scope">
								<img :src="scope.row.sh_imgurl" @click="showPic($event, scope.row.id, scope.$index, scope.row, scope.row.sh_imgurl)" v-if="scope.row.sh_imgurl != '' " data-title="班制图片" style="width: 120px; height: 60px;cursor: pointer">
								<!-- <img :src="prop.row.sh_imgurl" v-if="prop.row.sh_imgurl != ''" style="width:100px; height:50px;"> -->
								<span v-else>--</span>
							</template>
						</el-table-column>
						<el-table-column prop="coupon_name" label="优惠券" width="150"></el-table-column>
						 <el-table-column prop="addtime" label="新增时间" width="165" sortable show-overflow-tooltip></el-table-column> 
						<el-table-column prop="deleted" fixed="right" label="是否下架" width="120" :filters="[{ text: '上架', value: '1' }, { text: '下架', value: '2' }]" :filter-method="filterTag">				
							<template scope="scope" >
								<el-button type="success" @click="handleShow(scope.row.id, 2)" size="small" v-if="scope.row.deleted === '1'" close-transition>上架</el-button>
								<el-button type="danger" @click="handleShow(scope.row.id, 1)" size="small" v-else close-transition>下架</el-button>
							</template>
						</el-table-column>
						<el-table-column label="操作" fixed="right" width="150">
							<template scope="scope">
								<a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.id, scope.$index, list)"><i class="el-icon-delete"></i></a>
								<a title="编辑" data-title="编辑班制" style="margin-left:8px;" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
							</template>
						</el-table-column>
					</el-table>
					<!--table end-->
				</el-tab-pane>
				<el-tab-pane label="教练班制" name="second">
					<el-table :data="coach_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
						<!-- <el-table-column fixed type="selection" width="55"></el-table-column> -->
						<el-table-column type="expand">
							<template scope="props">
								<el-form label-position="left" inline class="demo-table-expand">
									<el-form-item label="驾校名称">
										<span>{{ props.row.school_name }}</span>
									</el-form-item>
									<el-form-item label="班制名称">
										<span>{{ props.row.sh_title }}</span>
									</el-form-item>
									<el-form-item label="牌照名称">
										<span>{{ props.row.sh_license_name }}</span>
									</el-form-item>
									<el-form-item label="标签一">
										<span>{{ props.row.sh_tag }}</span>
									</el-form-item>
									<el-form-item label="标签二">
										<span>{{ props.row.sh_description_1 }}</span>
									</el-form-item>
									<el-form-item label="新增时间">
										<span>{{ props.row.addtime }}</span>
									</el-form-item>
								</el-form>
							</template>
						</el-table-column>
						<el-table-column prop="id" label="ID" sortable width="80"></el-table-column> 
						<el-table-column prop="sh_title" label="班制名称" min-width="180"></el-table-column> 
						<el-table-column prop="sh_type" label="班制类型" width="120" show-overflow-tooltip>
							<template scope="scope">
								<el-tag type="danger" v-if="scope.row.sh_type === '1'" close-transition>计时班</el-tag>
								<el-tag type="primary" v-else close-transition>非计时班</el-tag>
							</template>
						</el-table-column>
						<el-table-column prop="coach_name" label="所属教练" min-width="150" show-overflow-tooltip></el-table-column>
						 <el-table-column prop="school_name" v-if="school_id == ''" label="所属驾校" min-width="150" show-overflow-tooltip></el-table-column> 
						<el-table-column prop="sh_original_money" label="原价 | 最终价" width="160" show-overflow-tooltip>
							<template scope="scope">
								<span style="color: #888">{{scope.row.sh_original_money}}</span> |
								<span style="color: #ff4949">{{scope.row.sh_money}}</span>
							</template>
						</el-table-column>
						<el-table-column prop="sh_license_name" label="牌照" width="70" show-overflow-tooltip></el-table-column>
						<el-table-column prop="is_package" label="是否套餐" width="100" show-overflow-tooltip>
							<template scope="scope">
								<el-button type="success" @click="handlePackage(scope.row.id, 2)" size="small" v-if="scope.row.is_package === '1'" close-transition>是</el-button>
								<el-button type="warning" @click="handlePackage(scope.row.id, 1)" size="small" v-else close-transition>否</el-button>
							</template>
						</el-table-column>
						<el-table-column prop="is_promote" label="是否促销" width="100" show-overflow-tooltip>
							<template scope="scope">
								<el-button type="success" @click="handlePromoteStatus(scope.row.id, 2)" size="small" v-if="scope.row.is_promote === '1'" close-transition>是</el-button>
								<el-button type="warning" @click="handlePromoteStatus(scope.row.id, 1)" size="small" v-else close-transition>否</el-button>
							</template>
						</el-table-column>
						<el-table-column prop="sh_imgurl" label="班制图片" width="150">
							<template scope="scope">
								<img :src="scope.row.sh_imgurl" @click="showPic($event, scope.row.id, scope.$index, scope.row, scope.row.sh_imgurl)" v-if="scope.row.sh_imgurl != '' " data-title="班制图片" style="width: 120px; height: 60px;cursor: pointer">
								<span v-else>--</span>
							</template>
						</el-table-column>
						 <el-table-column prop="coupon_name" label="优惠券" width="150"> 
							<!-- <template scope="scope">
								<el-popover v-if="scope.row.coupon_info" v-if="scope.row.coupon_info != ''" trigger="hover" placement="top" width="500">
									<el-form label-position="right" label-width="120px" class="demo-table-expand">
										<el-form-item style="margin-bottom:0px;" label="名称：">
											<span>{{ scope.row.coupon_info.coupon_name }}</span>
										</el-form-item>
										<el-form-item style="margin-bottom:0px;" label="总数 | 领取数：">
											<span>{{ scope.row.coupon_info.coupon_total_num }} | {{ scope.row.coupon_info.coupon_get_num }}</span>
										</el-form-item>
										<el-form-item style="margin-bottom:0px;" label="是否开启：">
											<span style="color: #13ce66;" v-if=" scope.row.coupon_info.is_open == 1">已开启</span>
											<span style="color: #ff4949;" v-else>未开启</span>
										</el-form-item>
										<el-form-item style="margin-bottom:0px;" label="是否展示：">
											<span style="color: #13ce66;" v-if=" scope.row.coupon_info.is_show == 1">已展示</span>
											<span style="color: #ff4949;" v-else>未展示</span>
										</el-form-item>
									</el-form>
									<div slot="reference" class="name-wrapper">
										<el-tag type="danger" v-if="scope.row.coupon_info">{{ scope.row.coupon_info.coupon_name }}</el-tag>
									</div>
								</el-popover> 
							</template> -->
						</el-table-column> 
						
						 <el-table-column prop="addtime" label="添加时间" width="165" sortable show-overflow-tooltip></el-table-column> 
						<el-table-column prop="deleted" fixed="right" label="是否下架" width="120" :filters="[{ text: '上架', value: '1' }, { text: '下架', value: '2' }]" :filter-method="filterTag">				
							<template scope="scope" >
								<el-button type="success" @click="handleShow(scope.row.id, 2)" size="small" v-if="scope.row.deleted === '1'" close-transition>上架</el-button>
								<el-button type="danger" @click="handleShow(scope.row.id, 1)" size="small" v-else close-transition>下架</el-button>
							</template>
						</el-table-column>
						<el-table-column label="操作" fixed="right" width="150">
							<template scope="scope">
								<a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.id, scope.$index, list)"><i class="el-icon-delete"></i></a>
								<a title="编辑" data-title="编辑班制" style="margin-left:8px;" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
							</template>
								<!-- <el-button size="small" type="text" data-title="预览班制" @click="handlePreview($event, scope.row.id, scope.$index, scope.row)">预览</el-button>
								<el-button size="small" type="text" data-title="编辑班制" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)">编辑</el-button>
								<el-button size="small" type="text" style="color:#ff4949;" @click.native.prevent="handleDel(scope.row.id, scope.$index, coach_list)">删除</el-button> -->
						</el-table-column>
					</el-table>
					<!--table end-->
				</el-tab-pane>

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
	Vue.config.devtools = true;
	var vm = new Vue({
		el: '#app',
		data: {
			refreshstatus: false,
			fullscreenLoading: true,
			activeName: "first",
			deleted_options: [
				{value: '', label: '--不限状态--' }, 
				{value: '1', label: '上架' }, 
				{value: '2', label: '下架'}
			],
			package_options: [
				{value: '', label: '--不限状态--' }, 
				{value: '1', label: '是' }, 
				{value: '2', label: '否'}
			],
			promote_options: [
				{value: '', label: '--不限状态--' }, 
				{value: '1', label: '是' }, 
				{value: '2', label: '否'}
			],
			list: [],
			type: 'school',
			coach_list: [],
			multipleSelection: [],
			list_url: "<?php echo base_url('school/shiftsajax'); ?>",
			del_url: "<?php echo base_url('school/delshiftsajax'); ?>",
			package_url: "<?php echo base_url('school/changePackage'); ?>",
			promote_url: "<?php echo base_url('school/handlePromoteStatus'); ?>",
			edit_url: "<?php echo base_url('school/editshifts'); ?>",
			add_url: "<?php echo base_url('school/addshifts'); ?>",
			preview_url: "<?php echo base_url('school/preview'); ?>",
			show_url: "<?php echo base_url('school/showshifts'); ?>",
			couponstatus_url: "<?php echo base_url('coupon/statuschangeajax'); ?>",
			is_open: true,
			is_show: true,
			currentPage: 1,
            page_sizes: [10, 20, 30, 50, 100],
            page_size: 10,
            pagenum: 0,
            count: 0,
			search: {
				promote: '',
				package: '',
				deleted: '',
                keywords: ''
			}
		},
		created: function() {
			var filters = {"p": this.currentPage, "type": this.type, "promote": this.search.promote, "package": this.search.package, "del": this.search.deleted, "keywords": this.search.keywords, 's': this.page_size};
			this.listAjax(filters);
		},
		methods: {
			handleClick: function(tab, event) {
				switch (this.activeName) {
					case 'first':
						this.type = 'school';
						break;
					case 'second':
						this.type = 'coach';
						break;
					default:
						this.type = 'school';
						break;
				}
				// window.history.pushState(null, null, '?promote='+this.search.promote+'&package='+this.search.package+'&del='+this.search.deleted+'&s='+this.page_size+'&keywords='+this.search.keywords+'&p='+ this.currentPage);

				// if(this.list.length == 0 || this.coach_list.length == 0 ) {
					var filters = {"p": this.currentPage, "type": this.type, "promote": this.search.promote, "package": this.search.package, "del": this.search.deleted, "keywords": this.search.keywords, 's': this.page_size};
                    this.listAjax(filters);
				// }
			},
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
                            // vm.list = _.get(res, 'data.list');
							switch (param.type) {
								case 'school':
									vm.list = _.get(res, 'data.list');
									break;
								case 'coach':
									vm.coach_list = _.get(res, 'data.list');
									break;
								default:
									vm.list = _.get(res, 'data.list');
									break;
							}
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
							var filters = {"p": vm.currentPage, "type": vm.type, "promote": vm.search.promote, "package": vm.search.package, "del": vm.search.deleted, "keywords": vm.search.keywords, 's': vm.page_size};
							vm.listAjax(filters);
							vm.messageNotice('success', data.msg);			
						}
					},
					error: function() {
						vm.fullscreenLoading = false;									
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				})
			},
			handlePromoteStatus: function(id, status) {
				$.ajax({
					type: 'post',
					url: this.promote_url,
					data: {id:id, status:status},
					dataType:"json",
					success: function(data) {		
						if(data.code == 200) {
							var filters = {"p": this.currentPage, "type": this.type, "promote": this.search.promote, "package": this.search.package, "del": this.search.deleted, "keywords": this.search.keywords, 's': this.page_size};
							vm.listAjax(filters);
						} else {
							vm.messageNotice('warning', data.msg);			
						}
					},
					error: function() {							
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				});
			},
			handlePackage: function(id, status) {
				$.ajax({
					type: 'post',
					url: this.package_url,
					data: {id:id, status:status},
					dataType:"json",
					success: function(data) {		
						if(data.code == 200) {
							var filters = {"p": vm.currentPage, "type": vm.type, "promote": vm.search.promote, "package": vm.search.package, "del": vm.search.deleted, "keywords": vm.search.keywords, 's': this.page_size};
							vm.listAjax(filters);
						} else {
							vm.messageNotice('warning', data.msg);			
						}
					},
					error: function() {							
						vm.messageNotice('warning', '网络错误，请检查网络');
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
							var filters = {"p": vm.currentPage, "type": vm.type, "promote": vm.search.promote, "package": vm.search.package, "del": vm.search.deleted, "keywords": vm.search.keywords, 's': this.page_size};
							vm.listAjax(filters);
						} else {
							vm.messageNotice('warning', data.msg);			
						}
					},
					error: function() {							
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				});
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
			// handlePreview: function(e, id, index, row) {
			// 	this.showLayer(e, '480px', 'lb', this.preview_url+'?id='+id);
			// },
			handleSizeChange: function (size) {
                this.page_size = size;
				var filters = {"p": this.currentPage, "type": this.type, "promote": this.search.promote, "package": this.search.package, "del": this.search.deleted, "keywords": this.search.keywords, 's': this.page_size};
                this.listAjax(filters);
            },
			handleCurrentChange: function(val) {
				this.refreshstatus = true;
                this.currentPage = val;
				var filters = {"p": this.currentPage, "type": this.type, "promote": this.search.promote, "package": this.search.package, "del": this.search.deleted, "keywords": this.search.keywords, 's': this.page_size};
                vm.listAjax(filters);
			},
			handleSearch: function () {
				var filters = {"p": this.currentPage, "type": this.type, "promote": this.search.promote, "package": this.search.package, "del": this.search.deleted, "keywords": this.search.keywords, 's': this.page_size};
                vm.listAjax(filters);
            },
			filterTag: function(value, row) {
				return row.deleted == value;
			},
			handleRefresh: function() {
				this.refreshstatus = true;
				this.listAjax(this.currentPage);
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