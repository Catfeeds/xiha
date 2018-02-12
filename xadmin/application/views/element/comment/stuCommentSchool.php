<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">
		<div class="gx-search">
			<el-form :inline="true" :model="search" class="demo-form-inline">
				<el-form-item>
					<el-select v-model="search.star_val" placeholder="按评价星级">
						<el-option v-for="item in star_options" :label="item.label" :value="item.value"></el-option>
					</el-select>   
				</el-form-item>
				<!-- <el-form-item>
					<el-select v-model="search.word_val" placeholder="按关键词" >
						<el-option v-for="item in word_options" :label="item.label" :value="item.value" ></el-option>
					</el-select>
				</el-form-item> -->
				<el-form-item>
					<el-input style="width: 300px" v-model="search.keywords" placeholder="订单号，驾校名称，学员昵称|姓名|手机号" v-if="school_id == ''"></el-input>
					<el-input style="width: 300px" v-if="school_id != '' " v-model="search.keywords" placeholder="订单号，学员昵称|姓名|手机号"></el-input>
				</el-form-item>	
				<el-form-item>
					<el-button type="primary" icon="search" @click="handleSearch">搜索</el-button>
				</el-form-item>
			</el-form>
		</div>

		<div class="gx-iframe-content">
			<div class="gx-iframe-operation" style="margin-bottom: 20px; border-radius: 4px;">
				<el-button type="success" style="margin-left: 10px;" @click.active.prevent="handleAdd" id="add" data-title="添加评价"><i class="el-icon-plus"></i> 添加评价</el-button>
				<el-button type="success" type="small" style="float:right; line-height: 38px;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
			</div>

			<el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
				<el-table-column fixed="left" type="selection" width="70"></el-table-column>
				<el-table-column prop="id" label="ID" sortable width="85"></el-table-column> 
				<el-table-column prop="school_name" label="驾校名称" width="160" v-if="school_id == ''" show-overflow-tooltip></el-table-column>
				<el-table-column prop="user_name" label="学员昵称|姓名" width="150" show-overflow-tooltip>
					<template scope="scope">
						<span>{{ scope.row.s_username }}</span> | 
						<span>{{ scope.row.s_real_name }}</span>
					</template>
				</el-table-column> 
				<el-table-column prop="s_phone" label="学员手机" width="150" show-overflow-tooltip></el-table-column>
				<el-table-column prop="order_no" label="订单号" width="180"show-overflow-tooltip></el-table-column>
				<el-table-column prop="" label="评价入口" width="160">
			      <template scope="scope">
			        <el-tag
			          size="small" type="success"
			          @click="handleEdit(scope.$index, scope.row)">报名驾校</el-tag>
			      </template>
			    </el-table-column>

				<el-table-column prop="school_star" label="驾校星级" width="145" show-overflow-tooltip>
					<template scope="scope" >
						<el-div style="color:red;" type="danger" v-if="parseInt(scope.row.school_star) == 1" close-transition>★</el-div>
						<el-div style="color:red;" type="danger" v-else-if="parseInt(scope.row.school_star) == 2" close-transition>★★</el-div>
						<el-div style="color:red;" type="danger" v-else-if="parseInt(scope.row.school_star) == 3" close-transition>★★★</el-div>
						<el-div style="color:red;" type="danger" v-else-if="parseInt(scope.row.school_star) == 4" close-transition>★★★★</el-div>
						<el-div style="color:red;" type="danger" v-else close-transition>★★★★★</el-div>
					</template>
				</el-table-column>
				<el-table-column prop="school_content" label="评价内容" width="150" show-overflow-tooltip></el-table-column>
				<el-table-column prop="addtime" label="评价时间" sortable width="180" show-overflow-tooltip></el-table-column>
				<el-table-column label="操作" fixed="right" width="120">
					<template scope="scope">
						<!-- <el-button size="small" type="text" data-title="编辑" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)">编辑</el-button>
						<el-button size="small" type="text" style="color:#ff4949;" @click.native.prevent="handleDel(scope.row.id, scope.$index, list)">删除</el-button> -->

						<a title="删除" style="margin-left:8px; cursor: pointer" @click="handleDel(scope.row.id, scope.$index, list)" ><i class="el-icon-delete"></i></a>
						<a title="编辑" data-title="更新评论" style="margin-left:8px;" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
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
	var school_id = "<?php echo $school_id; ?>";
	var vm = new Vue({
		el: '#app',
		data: {
			refreshstatus: false,
			fullscreenLoading: true,
			star_options: [{value: '', label: '不限星级' }, {value: '1.0', label: '★'}, {value: '2.0', label: '★★'}, {value: '3.0', label: '★★★'}, {value: '4.0', label: '★★★★'}, {value: '5.0', label: '★★★★★'}],
			word_options: [{value: '', label: '不限关键词' }, {value: 'order_no', label: '订单号'}, {value: 's_school_name', label: '驾校名称'}, {value: 's_username', label: '学员昵称'}, {value: 's_phone', label: '学员手机'}],
			list: [],
			multipleSelection: [],
			list_url: "<?php echo base_url('comment/listajaxStuCommentSchool'); ?>",
			del_url: "<?php echo base_url('comment/delStuCommentSchool'); ?>",
			edit_url: "<?php echo base_url('comment/editStuCommentSchool'); ?>",
			add_url: "<?php echo base_url('comment/addStuCommentSchool'); ?>",
			preview_url: "<?php echo base_url('comment/preStuCommentSchool'); ?>",
			show_url: "<?php echo base_url('comment/showStuCommentSchool'); ?>",
			search_url: "<?php echo base_url('comment/searchStuCommentSchool'); ?>",
			currentPage: 1,
            page_sizes: [10, 20, 30, 50, 100],
            page_size: 10,
            pagenum: 0,
            count: 0,
			search:{
				star_val: '',
				keywords: ''
			},
		},
		created: function() {
			var filters = {"p": this.currentPage, "star": this.search.star_val, "keywords": this.search.keywords, "s": this.page_size};
			this.listAjax(filters);
		},
		methods: {
			//	ajax加载列表
			listAjax: function(params) {
				$.ajax({
					type: 'post',
					url: this.list_url,
					data: params,
					dataType:"json",
					async: true,
					success: function(data) {
						// setTimeout(function() {
						vm.fullscreenLoading = false;			
						// }, 500);
						vm.refreshstatus = false;
						if(data.code == 200) {
							vm.list = data.data.list;
							vm.pagenum = data.data.pagenum;
							vm.count = data.data.count;
							vm.currentPage = params.p;
						}
					},
					error: function() {
						vm.fullscreenLoading = false;
						vm.refreshstatus = false;
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				})
			},
			//	ajax删除
			delAjax: function(id) {
				$.ajax({
					type: 'post',
					url: this.del_url,
					data: {id:id},
					dataType:"json",
					success: function(data) {
						vm.fullscreenLoading = false;			
						if(data.code == 200) {
							var filters = {"p":vm.currentPage, "star":vm.search.star_val, "keywords":vm.search.keywords, "s":vm.page_size};
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
			//	删除
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
			//	checkbox选中
			handleSelectionChange: function(val) {
				console.log(val);
				this.multipleSelection = val;
			},
			//	添加
			handleAdd: function(e) {
				this.showLayer(e, '60%', 'rb', this.add_url);
			},
			//	编辑
			handleEdit: function(e, id, index, row) {
				this.showLayer(e, '60%', 'lb', this.edit_url+'?id='+id);
			},
			//	预览
			handlePreview: function(e, id, index, row) {
				this.showLayer(e, '480px', 'rb', this.preview_url+'?id='+id);
			},
			//	翻页
			handleCurrentChange: function(val) {
				this.refreshstatus = true;
				this.currentPage = val;
				// window.history.pushState(null, null, '?p='+val+'&star='+this.search.star_val+'&keywords='+this.search.keywords+'&s='+this.page_size);
				this.listAjax({"p": val, "star": this.search.star_val, "keywords":this.search.keywords, "s": this.page_size});
			},
			handleSizeChange: function (size) {
                this.page_size = size;
				var filters = {"p": this.currentPage, "star": this.search.star_val, "keywords": this.search.keywords, 's': this.page_size};
                this.listAjax(filters);
            },
			//	搜索
			handleSearch: function() {
				this.listAjax({"p": this.currentPage, "star": this.search.star_val, "keywords": this.search.keywords, "s": this.page_size});
			},
			//	刷新
			handleRefresh: function() {
				this.refreshstatus = true;
				var filters = {"p": this.currentPage, "s": this.page_size};
				this.listAjax(filters);
			},
			//	消息框
			messageNotice: function(type, msg) {
				this.$message({type: type,message: msg});
			},
			//	弹出层
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