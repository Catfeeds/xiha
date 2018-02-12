<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">

        <!--search-->
        <div class="gx-search">
            <el-form :inline="true" v-model="search">
                <el-form-item label="车辆类型">
                    <el-select v-model="search.cartype" placeholder="请选择车辆类型">
                        <el-option v-for="item in cartype_options" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="关键词">
                    <el-input v-if="school_id =='' " v-model="search.keywords" placeholder="驾校名称 | 车名称 | 车牌号"></el-input>
                    <el-input v-if="school_id !='' "  v-model="search.keywords" placeholder="车名称 | 车牌号"></el-input>
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
                <el-button type="success" id="add" @click.active.prevent="handleAdd($event)" data-title="新增车辆" style="margin-left: 10px;"><i class="el-icon-plus"> 新增车辆</i></el-button>
                <el-button type="success" style="float: right; line-height: 38px;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
            </div>
            
            <el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange">
                <el-table-column type="selection" width="50"></el-table-column>
				<el-table-column prop="id" label="ID" sortable width="80"></el-table-column> 
                <el-table-column prop="school_name" v-if="school_id =='' " label="驾校名称" min-width="130"></el-table-column> 
                <el-table-column prop="name" label="车名称" min-width="130"></el-table-column> 
                <el-table-column prop="car_no_name" label="车品牌" min-width="130"></el-table-column> 
                <el-table-column prop="car_no" label="车牌号" min-width="130"></el-table-column> 
                <el-table-column label="车辆图片" prop="car_imgurl" min-width="150">
                    <template scope="scope">
                        <a href="#"  v-for="car_imgurl_0 in scope.row.car_imgurl" v-if="scope.row.car_imgurl['car_imgurl_0'] != '' " data-title="车辆图片" style="color: #12cf66" @click="showPic($event, scope.row.id, scope.$index, scope.row, car_imgurl_0)">预览；<a>
                        <span v-if="scope.row.car_imgurl == ''">--</span>
                    </template>
                </el-table-column> 
                <el-table-column prop="car_type" label="车辆类型" min-width="130" :filters="ctype" :filter-method="filterTag">
					<template scope="scope">
				        <el-tag type="default"  v-if="parseInt(scope.row.car_type) == 0">未知车型</el-tag>
				        <el-tag type="success"  v-if="parseInt(scope.row.car_type) == 1">普通车型</el-tag>
				        <el-tag type="primary"  v-if="parseInt(scope.row.car_type) == 2">加强车型</el-tag>
				        <el-tag type="warning"  v-if="parseInt(scope.row.car_type) == 3">模拟车型</el-tag>
			      	</template>
				</el-table-column>
				<el-table-column prop="addtime" label="最近时间" sortable min-width="150"></el-table-column> 
				<el-table-column label="操作" fixed="right" width="140">
					<template scope="scope">
						<a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.id, scope.$index, list)"><i class="el-icon-delete"></i></a>
						<a title="编辑" data-title="更新车辆" style="margin-left:8px;" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
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
    var school_id = "<?php echo $school_id; ?>";
	var vm = new Vue({
		el: '#app',
		data: {
            refreshstatus: false,
            fullscreenLoading: false,
            list: [],
            multipleSelection: [],
            cartype_options: [
                {value: '', label: "请选择车型"},
                {value: 1, label: "普通车型"},
                {value: 2, label: "加强车型"},
                {value: 3, label: "模拟车型"},
            ],
            ctype: [
                {text: "普通车型", value: 1},
                {text: "加强车型", value: 2},
                {text: "模拟车型", value: 3}
            ],
            list_url: "<?php echo base_url('cars/listAjax')?>?type=coachcar",
            del_url: "<?php echo base_url('cars/delAjax');?>?type=coachcar",
            add_url: "<?php echo base_url('cars/add')?>",
            edit_url: "<?php echo base_url('cars/edit')?>",
            currentPage: 1,
            page_sizes: [10, 20, 30, 50, 100],
            page_size: 10,
            pagenum: 0,
            count: 0,
            search: {
                cartype: '',
                keywords: ''
            }
		},
        created: function() {
            var filters = {"p": this.currentPage, "ctype": this.search.cartype, "keywords": this.search.keywords, "s": this.page_size};
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
                            var filters = {"p": vm.currentPage, "keywords": vm.search.keywords, "s": vm.page_size};
                            vm.listAjax(filters);
                        }
                    },
                    error: function(e) {
                        vm.messageNotice('error', '网络异常！');
                    }
                });
            },
            filterTag: function (value, row) {
                return row.car_type == value;
            },
            handleCurrentChange: function (val) {
                this.refreshstatus = true;
                this.currentPage = val;
                window.history.pushState(null, null, '?p='+val+'&ctype='+this.search.cartype+'&keywords='+this.search.keywords);
                var filters = {"p": this.currentPage, "ctype": this.search.cartype, "keywords": this.search.keywords, 's': this.page_size};
                vm.listAjax(filters);
            },
            handleSizeChange: function (size) {
                this.page_size = size;
                var filters = {"p": this.currentPage, "ctype": this.search.cartype, "keywords": this.search.keywords, 's': this.page_size};
                this.listAjax(filters);
            },
            handleSearch: function () {
                // this.page_size = size;
                var filters = {"p": this.currentPage, "ctype": this.search.cartype, "keywords": this.search.keywords, 's': this.page_size};
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