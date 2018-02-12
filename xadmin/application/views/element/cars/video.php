<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">

        <!--search-->
        <div class="gx-search">
            <el-form :inline="true" v-model="search">
                <el-form-item label="开启否">
                    <el-select v-model="search.open" placeholder="--不限状态--">
                        <el-option v-for="item in open_option" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="科目">
                    <el-select v-model="search.course" placeholder="--不限科目--">
                        <el-option v-for="item in course_option" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="牌照类型">
                    <el-select v-model="search.cartype" placeholder="--不限类型--">
                        <el-option v-for="item in cartype_option" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="关键词">
                    <el-input v-model="search.keywords" placeholder="视频名称"></el-input>
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
                <el-button type="success" id="add" @click.active.prevent="handleAdd($event)" data-title="新增视频" style="margin-left: 10px;"><i class="el-icon-plus"> 新增视频</i></el-button>
                <el-button type="success" style="float: right; line-height: 38px;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
            </div>
            
            <el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange">
                <!-- <el-table-column type="selection" width="50"></el-table-column> -->
                <el-table-column type="expand">
                    <template scope="props">
                        <el-form label-position="left" inline class="demo-table-expand">
                            <el-form-item label="ID">
                                <span>{{ props.row.id }}</span>
                            </el-form-item>
                            <el-form-item label="视频名称">
                                <span>{{ props.row.title }}</span>
                            </el-form-item>
                            <el-form-item label="简短介绍">
                                <span>{{ props.row.skill_intro }}</span>
                            </el-form-item>
                            <el-form-item label="最近时间">
                                <span>{{ props.row.updatetime }}</span>
                            </el-form-item>
                            <el-form-item label="视频描述">
                                <span>{{ props.row.video_desc }}</span>
                            </el-form-item>
                        </el-form>
                    </template>
                </el-table-column>
				<el-table-column prop="id" label="ID" sortable width="80"></el-table-column> 
				<el-table-column prop="v_order" label="排序" width="100"></el-table-column> 
                <el-table-column prop="title" label="视频名称" min-width="130"></el-table-column> 
                <el-table-column prop="course_name" label="科目名称" min-width="130"></el-table-column> 
                <el-table-column prop="car_type_name" label="车牌类型" min-width="130"></el-table-column> 
                <el-table-column prop="pic_url" label="视频图片" min-width="130">
                    <template scope="prop">
                        <img :src="prop.row.pic_url" v-if="prop.row.pic_url != ''" style="width:100px; height:50px;cursor:pointer" @click="showPic($event, prop.row.id, prop.$index, prop.row, prop.row.pic_url)" data-title="视频图片">
                        <span v-else>--</span>
                    </template>
                </el-table-column> 
                <el-table-column prop="video_url" label="科目视频" min-width="130">
                    <template scope="prop">
                        <a href="#" v-if="prop.row.video_url != ''" style="width:100px; height:50px; cursor:pointer; color: yellowgreen" @click="showPic($event, prop.row.id, prop.$index, prop.row, prop.row.video_url)" data-title="科目视频">查看</a>
                        <span v-else>--</span>
                    </template>
                </el-table-column> 
				<el-table-column prop="skill_intro" label="技能介绍" min-width="180"></el-table-column> 
				<el-table-column prop="video_time" label="视频时间" min-width="150"></el-table-column> 
                <el-table-column prop="is_open" fixed="right" label="开启否" width="120">
					<template scope="scope">
				        <el-button size="small" type="success"   @click="handleOpen(scope.row.id, 0)" v-if="parseInt(scope.row.is_open) == 1">开启</el-button>
				        <el-button size="small" type="warning"  @click="handleOpen(scope.row.id, 1)" v-if="parseInt(scope.row.is_open) == 0">关闭</el-button>
			      	</template>
				</el-table-column>
				<el-table-column prop="addtime" label="添加时间" sortable min-width="150"></el-table-column> 
				<el-table-column prop="updatetime" label="最近时间" sortable min-width="150"></el-table-column> 
				<el-table-column label="操作" fixed="right" width="140">
					<template scope="scope">
						<a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.id, scope.$index, list)"><i class="el-icon-delete"></i></a>
						<a title="编辑" data-title="更新信息" style="margin-left:8px;" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
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
	var vm = new Vue({
		el: '#app',
		data: {
            refreshstatus: false,
            fullscreenLoading: false,
            list: [],
            multipleSelection: [],
            course_option: [
                {value: '', label: '--不限科目--'},
                {value: 'kemu2', label: '科目二'},
                {value: 'kemu3', label: '科目三'},
            ],
            cartype_option: [
                {value: '', label: '--不限类型--'},
                {value: 'car', label: '小车'},
                {value: 'bus', label: '客车'},
                {value: 'truck', label: '货车'},
                {value: 'moto', label: '摩托车'},
            ],
            open_option: [
                {value: '', label: "--不限状态--"},
                {value: 1, label: "开启"},
                {value: 0, label: "关闭"},
            ],
            list_url: "<?php echo base_url('cars/listAjax')?>?type=video",
            del_url: "<?php echo base_url('cars/delAjax');?>?type=video",
            add_url: "<?php echo base_url('cars/addvideo')?>",
            edit_url: "<?php echo base_url('cars/editvideo')?>",
            open_url: "<?php echo base_url('cars/handleOpen')?>",
            currentPage: 1,
            page_sizes: [10, 20, 30, 50, 100],
            page_size: 10,
            pagenum: 0,
            count: 0,
            search: {
                open: '',
                course: '',
                cartype: '',
                keywords: ''
            }
		},
        created: function() {
            var filters = {"p": this.currentPage, "open": this.search.open, "course": this.search.course, "ctype": this.search.cartype, "keywords": this.search.keywords, "s": this.page_size};
            this.listAjax(filters);
        },
		methods: {
            handleOpen: function(id, status) {
				$.ajax({
					type: 'post',
					url: this.open_url,
					data: {id:id, status:status},
					dataType:"json",
					success: function(data) {		
						if(data.code == 200) {
                            var filters = {"p": vm.currentPage, "open": vm.search.open, "course": vm.search.course, "ctype": vm.search.cartype, "keywords": vm.search.keywords, "s": vm.page_size};
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
            handleDel: function(id, index, rows) {
				this.$confirm('此操作将永久删除, 是否继续?', '提示', {
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
                    data: {id: id},
                    dataType: 'json',
                    success: function(data) {
                        is_ok = _.isObject(data) && _.has(data, 'code') && _.get(data, 'code') == 200
                        if (is_ok) {
                            var filters = {"p": vm.currentPage, "open": vm.search.open, "course": vm.search.course, "ctype": vm.search.cartype, "keywords": vm.search.keywords, "s": vm.page_size};
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
                window.history.pushState(null, null, '?p='+val+'&open='+this.search.open+'&course='+this.search.course+'&ctype='+this.search.cartype+'&keywords='+this.search.keywords);
                var filters = {"p": this.currentPage, "open": this.search.open, "course": this.search.course, "ctype": this.search.cartype, "keywords": this.search.keywords, 's': this.page_size};
                vm.listAjax(filters);
            },
            handleSizeChange: function (size) {
                this.page_size = size;
                var filters = {"p": this.currentPage, "open": this.search.open, "course": this.search.course, "ctype": this.search.cartype, "keywords": this.search.keywords, 's': this.page_size};
                this.listAjax(filters);
            },
            handleSearch: function () {
                // this.page_size = size;
                var filters = {"p": this.currentPage, "open": this.search.open, "course": this.search.course, "ctype": this.search.cartype, "keywords": this.search.keywords, 's': this.page_size};
                vm.listAjax(filters);
            },
            handleRefresh: function() {
				this.refreshstatus = true;
				this.listAjax();
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