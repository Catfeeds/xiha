<div id="app" v-cloak>
    <div class="iframe-content">
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="120px" class="demo-ruleForm" :label-position="labelPosition">
        <el-tabs v-model="timeActive" type="card" type="border-card" @tab-click="handlePayTypeClick">
                <el-tab-pane label="时间模板" name="first"></el-tab-pane>
                <el-tab-pane label="时间配置" name="second">
                	<el-form-item label="日期列表" prop="currentdate" required>
                        <div class="block">
                            <el-date-picker v-model="ruleForm.currentdate" type="date" placeholder="选择日期时间">
                            </el-date-picker>
                        </div>
                        <!-- <div class="block">
                            <el-date-picker v-model="ruleForm.currentdate" type="date" placeholder="选择日期"></el-date-picker>
                        </div> -->
					</el-form-item>
					<el-form-item label="上午时间设置" prop="s_am_time_list" required >
                    	<el-table ref="multipleTable" v-model="ruleForm.s_am_time_list" :data="amtime_list2" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChangeAm1">
							<el-table-column type="selection" width="55"></el-table-column>
							<el-table-column prop="id" label="ID" sortable width="55"></el-table-column>
							<el-table-column prop="final_start_time"  label="开始时间" min-width="101"></el-table-column>
							<el-table-column prop="final_end_time" label="结束时间" min-width="101"></el-table-column>
							<el-table-column prop="license_no" label="牌照" min-width="104">
                                <template scope="scope">
                                    <el-select v-model="scope.row.license_no" filterable placeholder="请选择">
                                        <el-option label="C1" value="C1"></el-option>
                                        <el-option label="C2" value="C2"></el-option>
                                    </el-select>
                                </template>
                            </el-table-column>
							<el-table-column prop="subjects" label="科目" min-width="104">
                                <template scope="scope">
                                    <el-select v-model="scope.row.subjects" filterable placeholder="请选择">
                                        <el-option label="科目二" value="科目二"></el-option>
                                        <el-option label="科目三" value="科目三"></el-option>
                                    </el-select>
                                </template>
                            </el-table-column>
							<el-table-column prop="price" label="单价" min-width="104">
                                <template scope="scope">
                                    <el-input align="center" v-model="scope.row.price" >{{ scope.row.price }}</el-input>
                                </template>
                            </el-table-column>
							<el-table-column prop="status" label="是否可被预约" width="130">
                                <template scope="scope">
                                    <el-tag type="success" v-if="parseInt(scope.row.status) == 1">可以</el-tag>
                                    <el-tag type="warning" v-if="parseInt(scope.row.status) == 2">不可以</el-tag>
                                </template>
                            </el-table-column>
                            <el-table-column prop="status" label="预约状态" width="120">
                                <template scope="scope">
                                    <el-tag type="success" v-if="parseInt(scope.row.status) == 1">可以</el-tag>
                                    <el-tag type="warning" v-if="parseInt(scope.row.status) == 2">不可以</el-tag>
                                </template>
                            </el-table-column>
						</el-table>
                    </el-form-item>
                    <el-form-item label="下午时间设置" prop="s_pm_time_list" required >
                    	<el-table ref="multipleTable" v-model="ruleForm.s_pm_time_list" :data="pmtime_list2" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChangePm1">
							<el-table-column  type="selection" width="55"></el-table-column>
							<el-table-column prop="id" label="ID" sortable width="55"></el-table-column>
							<el-table-column prop="final_start_time"  label="开始时间" min-width="101"></el-table-column>
							<el-table-column prop="final_end_time" label="结束时间" min-width="101"></el-table-column>
							<el-table-column prop="license_no" label="牌照" min-width="104">
                                <template scope="scope">
                                    <el-select v-model="scope.row.license_no" filterable placeholder="请选择">
                                        <el-option label="C1" value="C1"></el-option>
                                        <el-option label="C2" value="C2"></el-option>
                                    </el-select>
                                </template>
                            </el-table-column>
							<el-table-column prop="subjects" label="科目" min-width="104">
                                <template scope="scope">
                                    <el-select v-model="scope.row.subjects" filterable placeholder="请选择">
                                        <el-option label="科目二" value="科目二"></el-option>
                                        <el-option label="科目三" value="科目三"></el-option>
                                    </el-select>
                                </template>
                            </el-table-column>
                            <el-table-column prop="price" label="单价" min-width="104">
                                <template scope="scope">
                                    <el-input align="center" v-model="scope.row.price" >{{ scope.row.price }}</el-input>
                                </template>
                            </el-table-column>
							<el-table-column prop="status" label="是否可被预约" width="130">
								<template scope="scope">
									<el-tag type="success" v-if="parseInt(scope.row.status) == 1">可以</el-tag>
									<el-tag type="warning" v-if="parseInt(scope.row.status) == 2">不可以</el-tag>
						      	</template>
							</el-table-column>
							<el-table-column prop="status" label="预约状态" width="120">
								<template scope="scope">
									<el-tag type="success" v-if="parseInt(scope.row.status) == 1">可以</el-tag>
									<el-tag type="warning" v-if="parseInt(scope.row.status) == 2">不可以</el-tag>
						      	</template>
							</el-table-column>
						</el-table>
                    </el-form-item>
                </el-tab-pane>
            </el-tabs>
            <el-form-item>
                <el-button type="primary" @click="submitForm('ruleForm')">编辑保存</el-button>
            </el-form-item>
        </el-form>
    </div>
</div>

<script>
	Vue.config.devtools = true;
    var vm = new Vue({
        el: "#app",
        data: {
            labelPosition: 'right',
            timeActive: "second",
            os_type: "current",
            amtime_list2: [],
            pmtime_list2: [],
            coach_time_list_am:[],
            coach_time_list_pm:[],
            date_list: [],
            coach_id: "<?php echo $coach_id;?>",
            school_id: "<?php echo $school_id;?>",
            ruleForm: {
            	coach_id: "<?php echo $coach_id;?>",
            	school_id: "<?php echo $school_id;?>",
                currentdate:"<?php echo $current_date;?>",
                s_am_time_list: "",
                s_pm_time_list: "",
            },
            rules:{
                currentdate: [
                    { required: true, message: '日期不能为空'}
                ],
            },
            list_url: "<?php echo base_url('coach/setCoachCurrentTime')?>",
            timelist_url: "<?php echo base_url('coach/getCurrentTimeConf')?>",
            lesson_url: "<?php echo base_url('coach/getLessonInfo')?>",
        },
        created: function() {
            var filter = {"id": this.coach_id, "school_id": this.school_id, "ot": this.os_type, 'current_time': this.ruleForm.currentdate};
            this.getCurrentTimeConf(filter);
        },
        methods: {
            handlePayTypeClick: function() {
				if(this.timeActive == 'first') {
					location.href="<?php echo base_url('coach/setCoachTimeConf?ot=time'); ?>"+"&id="+this.coach_id+"&school_id="+this.school_id;
				}
			},
            getCurrentTimeConf: function(param) {
				$.ajax({
					type: 'post',
					url: this.timelist_url,
					data: param,
					dataType:"json",
					async: true,
					success: function(res) {
						if(res.code == 200) {
							vm.date_list = res.data.date_list;
							vm.amtime_list2 = res.data.amtime_list2;
							vm.pmtime_list2 = res.data.pmtime_list2;
						}
					},
					error: function() {
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				})
			},
            getLessonInfo: function() {
                $.ajax({
                    type: 'post',
                    url: this.lesson_url,
                    dataType: 'json',
                    success:function(ret) {
                    	vm.lesson_options = ret.data;
                    },
                    error: function() {
                        vm.messageNotice('warning', '获取科目列表错误');
                    }
                });
            },
            handleSelectionChange: function(val) {
                this.multipleSelection = val;
            },
        	handleSelectionChangeAm1: function(val) {
                vm.ruleForm.s_am_time_list = val;
			},
            handleSelectionChangePm1: function(val) {
                vm.ruleForm.s_pm_time_list = val;
			},
            editAjax: function(params) {
                if (isNaN(parseInt(params.currentdate))) {
                    params.currentdate = parseInt(params.currentdate.getTime()/1000);
                }  
                $.ajax({
					type: 'post',
					url: this.list_url,
					data: params,
					dataType:"json",
					success: function(data) {
                        if(data.code == 200) {
                            parent.layer.closeAll();
                            parent.vm.messageNotice('success', data.msg);
                            // parent.vm.listAjax(parent.vm.currentPage);
                        } else {
                            parent.vm.messageNotice('warning', data.msg);
                        }
					},
					error: function() {
                        vm.messageNotice('warning', '网络错误，请检查网络');
					}
				});
            },
            submitForm: function(formName) {
                this.$refs[formName].validate(function(valid) {
                    if (valid) {
                        vm.editAjax(vm.ruleForm);
                    } else {
                        return false;
                    }
                });
            },
            messageNotice: function(type, msg) {
				this.$message({
					type: type,
					message: msg
				});
			},
    	}
    });

</script>
