<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">
		<div class="gx-search">
            <el-form :inline="true" v-model="search" >
                <el-form-item label="驾校性质">
                    <el-select v-model="search.nature" placeholder="--不限性质--">
                        <el-option v-for="item in nature_options" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="品牌否">
                    <el-select v-model="search.brand" placeholder="--不限品牌--">
                        <el-option v-for="item in brand_options" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </el-form-item>
				<el-form-item label="热门否">
                    <el-select v-model="search.hot" placeholder="--不限状态--">
                        <el-option v-for="item in hot_options" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </el-form-item>
				<el-form-item label="上架否">
                    <el-select v-model="search.show" placeholder="--不限状态--">
                        <el-option v-for="item in show_options" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="关键词">
					<el-input v-model="search.keywords" placeholder="名称|法人|手机号|地址"><el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" icon="search" @click="handleSearch">搜索</el-button>
                </el-form-item> 
            </el-form>
        </div>

		<div class="gx-iframe-content">
			<div class="gx-iframe-operation" style="margin-bottom: 20px; border-radius: 4px;">
				<!-- <el-button type="danger" style="margin-left:10px;"><i class="el-icon-delete"></i> 批量删除</el-button> -->
				<el-button type="success" style="margin-left:10px;" @click.active.prevent="handleAdd($event)" id="add" data-title="添加驾校"><i class="el-icon-plus"></i> 添加驾校</el-button>
				<el-button type="success" type="small" style="float:right; line-height: 38px;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
			</div>

			<el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
				<!-- <el-table-column fixed type="selection" width="55"></el-table-column> -->
				<el-table-column type="expand">
					<template scope="props">
						<el-form label-position="left" inline class="demo-table-expand">
							<el-form-item label="驾校名称">
								<span>{{ props.row.s_school_name }}</span>
							</el-form-item>
							<el-form-item label="驾校固话">
								<span>{{ props.row.s_frdb_tel }}</span>
							</el-form-item>
							<el-form-item label="法人名称">
								<span>{{ props.row.s_frdb }}</span>
							</el-form-item>
							<el-form-item label="法人手机">
								<span>{{ props.row.s_frdb_mobile }}</span>
							</el-form-item>
							<el-form-item label="收费标准">
								<span>{{ props.row.dc_base_je }}</span>
							</el-form-item>
							<el-form-item label="组织机构码">
								<span>{{ props.row.s_zzjgdm }}</span>
							</el-form-item>
							<el-form-item label="详细地址">
								<span>{{ props.row.s_address }}</span>
							</el-form-item>
							<el-form-item label="银行账户">
								<span>{{ props.row.s_yh_huming }}</span>
							</el-form-item>
							<el-form-item label="银行名称">
								<span>{{ props.row.s_yh_name }}</span>
							</el-form-item>
							<el-form-item label="银行账号">
								<span>{{ props.row.s_yh_zhanghao }}</span>
							</el-form-item>
							<el-form-item label="新增时间">
								<span>{{ props.row.addtime }}</span>
							</el-form-item>
						</el-form>
					</template>
				</el-table-column>
				<el-table-column prop="l_school_id" label="ID" sortable width="80"></el-table-column> 
				<el-table-column prop="s_order" label="排序" width="100" show-overflow-tooltip></el-table-column> 
				<el-table-column prop="s_school_name" label="校名" width="180" show-overflow-tooltip></el-table-column> 
				<el-table-column prop="brand" label="品牌" width="100" show-overflow-tooltip>
					<template scope="scope" >
						<el-tag type="primary" v-if="scope.row.brand === '1'" close-transition>普通驾校</el-tag>
						<el-tag type="danger" v-else close-transition>品牌驾校</el-tag>
					</template>
				</el-table-column>
				<el-table-column prop="s_frdb" label="法人" width="120" show-overflow-tooltip></el-table-column>
				<el-table-column prop="s_frdb_mobile" label="法人手机" width="150" show-overflow-tooltip></el-table-column>
				<el-table-column prop="s_frdb_tel" label="驾校固话" width="150" show-overflow-tooltip></el-table-column>
				<el-table-column prop="i_dwxz" label="性质" width="100" show-overflow-tooltip>
					<template scope="scope" >
						<el-tag type="danger" v-if="scope.row.i_dwxz === '1'" close-transition>一类驾校</el-tag>
						<el-tag type="primary" v-else-if="scope.row.i_dwxz === '2'" close-transition>二类驾校</el-tag>
						<el-tag type="success" v-else close-transition>三类驾校</el-tag>
					</template>
				</el-table-column>
				<el-table-column prop="is_hot" label="热门否" width="120" :filters="[{ text: '是', value: '1' }, { text: '否', value: '2' }]" :filter-method="filterTag">				
					<template scope="scope" >
						<el-button type="success" @click="handleHotStatus(scope.row.l_school_id, 2)" size="small" v-if="scope.row.is_hot === '1'" close-transition>是</el-button>
						<el-button type="warning" @click="handleHotStatus(scope.row.l_school_id, 1)" size="small" v-else close-transition>否</el-button>
					</template>
				</el-table-column>
				<el-table-column prop="s_address" label="详细地址" width="400" show-overflow-tooltip></el-table-column>  
				<el-table-column prop="addtime" label="添加时间" width="180" sortable show-overflow-tooltip></el-table-column> 
				<el-table-column prop="is_show" fixed="right" label="是否下架" width="120" :filters="[{ text: '上架', value: '1' }, { text: '下架', value: '2' }]" :filter-method="filterTag">				
					<template scope="scope" >
						<el-button type="success" @click="handleShow(scope.row.l_school_id, 2)" size="small" v-if="scope.row.is_show === '1'" close-transition>上架</el-button>
						<el-button type="danger" @click="handleShow(scope.row.l_school_id, 1)" size="small" v-else close-transition>下架</el-button>
					</template>
				</el-table-column>
				<el-table-column label="操作" fixed="right" width="150">
					<template scope="scope">
						<!-- <el-button size="small" type="text" data-title="预览驾校" @click="handlePreview($event, scope.row.l_school_id, scope.$index, scope.row)">预览</el-button> -->
						<a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.l_school_id, scope.$index, list)"><i class="el-icon-delete"></i></a>
						<a title="编辑" data-title="编辑驾校" style="margin-left:8px;" @click="handleEdit($event, scope.row.l_school_id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
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
			nature_options: [
				{value: '', label: "--不限性质--"},
				{value: 1, label: "一类驾校"},
				{value: 2, label: "二类驾校"},
				{value: 3, label: "三类驾校"},
			],
			brand_options: [
				{value: '', label: "--不限品牌--"},
				{value: 1, label: "普通驾校"},
				{value: 2, label: "品牌驾校"},
			],
			show_options: [
				{value: '', label: "--不限状态--"},
				{value: 1, label: "上架"},
				{value: 2, label: "下架"},
			],
			hot_options: [
				{value: '', label: "--不限状态--"},
				{value: 1, label: "是"},
				{value: 2, label: "否"},
			],
			list_url: "<?php echo base_url('school/listajax'); ?>",
			del_url: "<?php echo base_url('school/delajax'); ?>",
			edit_url: "<?php echo base_url('school/edit'); ?>",
			add_url: "<?php echo base_url('school/add'); ?>",
			preview_url: "<?php echo base_url('school/preview'); ?>",
			show_url: "<?php echo base_url('school/show'); ?>",
			hot_url: "<?php echo base_url('school/handleHotStatus'); ?>",
			currentPage: 1,
            page_sizes: [10, 20, 30, 50, 100],
            page_size: 10,
            pagenum: 0,
            count: 0,
            search: {
                nature:'',
                hot: '',
                brand: '',
                show: '',
                keywords: ''
            }
		},
		created: function() {
			var filters = {"p": this.currentPage, "nature": this.search.nature, "hot": this.search.hot, "brand": this.search.brand, "show": this.search.show, "keywords": this.search.keywords, 's': this.page_size};
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
			handleDel: function(id, index, rows) {
				this.$confirm('此操作将永久删除该文件, 是否继续?', '提示', {
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
					data: {id:id},
					dataType:"json",
					success: function(data) {
						vm.fullscreenLoading = false;			
						if(data.code == 200) {
							vm.listAjax(vm.currentPage);
							vm.messageNotice('success', data.msg);			
						}
					},
					error: function() {
						vm.fullscreenLoading = false;									
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				})
			},
			handleHotStatus: function(id, status) {
				$.ajax({
					type: 'post',
					url: this.hot_url,
					data: {id:id, status:status},
					dataType:"json",
					success: function(data) {		
						if(data.code == 200) {
							vm.listAjax(vm.currentPage);
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
							vm.listAjax(vm.currentPage);
						} else {
							vm.messageNotice('warning', data.msg);			
						}
					},
					error: function() {							
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				});
			},
			handleSizeChange: function (size) {
                this.page_size = size;
                var filters = {"p": this.currentPage, "nature": this.search.nature, "hot": this.search.hot, "brand": this.search.brand, "show": this.search.show, "keywords": this.search.keywords, 's': this.page_size};
                this.listAjax(filters);
            },
			handleSearch: function () {
                var filters = {"p": this.currentPage, "nature": this.search.nature, "hot": this.search.hot, "brand": this.search.brand, "show": this.search.show, "keywords": this.search.keywords, 's': this.page_size};
                vm.listAjax(filters);
            },
			handleCurrentChange: function(val) {
				this.refreshstatus = true;
                this.currentPage = val;
                // window.history.pushState(null, null, '?p='+val+'&nature='+this.search.nature+'&hot='+this.search.hot+'&show='+this.search.show+'&brand='+this.search.brand+'&keywords='+this.search.keywords);
                var filters = {"p": this.currentPage, "nature": this.search.nature, "hot": this.search.hot, "brand": this.search.brand, "show": this.search.show, "keywords": this.search.keywords, 's': this.page_size};
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