<div id="app" v-cloak>
    <div class="iframe-content">
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="120px" class="demo-ruleForm" :label-position="labelPosition">
        <el-tabs v-model="timeActive" type="card" type="border-card" @tab-click="handlePayTypeClick">
            <el-tab-pane label="时间模板" name="first">
                <el-form-item label="上午科目" prop="s_am_subject" required>
                    <el-select v-model="ruleForm.s_am_subject" placeholder="请选择科目">
                        <el-option v-for="item in lesson_options" :label="item.lesson_name" :value="item.lesson_id"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="下午科目" prop="s_pm_subject" required>
                    <el-select v-model="ruleForm.s_pm_subject" placeholder="请选择科目">
                        <el-option v-for="item in lesson_options" :label="item.lesson_name" :value="item.lesson_id"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="上午时间设置" prop="am_time_list">
                    <el-table ref="multipleTable"  v-model="ruleForm.am_time_list" :data="list.am_time_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChangeAm1">
                        <el-table-column type="selection" width="50"></el-table-column>
                        <el-table-column prop="id" label="ID" sortable width="80" ></el-table-column>
                        <el-table-column prop="final_start_time"  label="开始时间" min-width="208"></el-table-column>
                        <el-table-column prop="final_end_time" label="结束时间" min-width="208"></el-table-column>
                        <el-table-column prop="status" label="是否可被预约" min-width="208">
                            <template scope="scope">
                                <el-tag type="success" v-if="parseInt(scope.row.status) == 1">可以</el-tag>
                            </template>
                        </el-table-column>
                    </el-table>
                </el-form-item>

                <el-form-item label="下午时间设置" prop="s_pm_time_list">
                    <el-table ref="multipleTable"  v-model="ruleForm.s_pm_time_list" :data="list.pm_time_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChangePm1">
                        <el-table-column type="selection" width="50"></el-table-column>
                        <el-table-column prop="id" label="ID" sortable width="80"></el-table-column>
                        <el-table-column prop="final_start_time"  label="开始时间" min-width="208"></el-table-column>
                        <el-table-column prop="final_end_time" label="结束时间" min-width="208"></el-table-column>
                        <el-table-column prop="status" label="是否可被预约" min-width="208">
                            <template scope="scope">
                                <el-tag type="success" v-if="parseInt(scope.row.status) == 1">可以</el-tag>
                            </template>
                        </el-table-column>
                    </el-table>
                </el-form-item>
            </el-tab-pane>
            <el-tab-pane label="时间配置" name="second"></el-tab-pane>
        </el-tabs>

                <!-- <el-tab-pane label="时间配置" name="second">
                	<el-form-item label="日期列表" prop="currentdate" required>
                        <div class="block">
                            <el-date-picker v-model="ruleForm.currentdate" type="date" placeholder="选择日期"></el-date-picker>
                        </div>
					</el-form-item>
					<el-form-item label="上午时间设置" prop="s_am_time_list2" required >
                    	<el-table v-model="ruleForm.s_am_time_list2" :data="amtime_list2" border tooltip-effect="dark" style="width: 100%">
							<el-table-column fixed type="selection" width="55"></el-table-column>
							<el-table-column prop="id" label="ID" sortable width="55"></el-table-column>
							<el-table-column prop="final_start_time"  label="开始时间" width="101"></el-table-column>
							<el-table-column prop="final_end_time" label="结束时间" width="101"></el-table-column>
							<el-table-column prop="license_no" label="牌照" width="104"></el-table-column>
							<el-table-column prop="subjects" label="科目" width="104"></el-table-column>
							<el-table-column prop="price" label="单价" width="104"></el-table-column>
							<el-table-column prop="status" label="是否可被预约" width="104">
								<template scope="scope">
									<el-button type="success" v-if="parseInt(scope.row.status) == 1">可以</el-button>
						      	</template>
							</el-table-column>
							<el-table-column prop="status" label="预约状态" width="104">
								<template scope="scope">
									<el-button type="success" v-if="parseInt(scope.row.status) == 1">可以</el-button>
						      	</template>
							</el-table-column>
						</el-table>
                    </el-form-item>

                    <el-form-item label="下午时间设置" prop="s_pm_time_list2" required >
                    	<el-table v-model="ruleForm.s_pm_time_list2" :data="pmtime_list2" border tooltip-effect="dark" style="width: 100%">
							<el-table-column fixed type="selection" width="55"></el-table-column>
							<el-table-column prop="id" label="ID" sortable width="55"></el-table-column>
							<el-table-column prop="final_start_time"  label="开始时间" width="101"></el-table-column>
							<el-table-column prop="final_end_time" label="结束时间" width="101"></el-table-column>
							<el-table-column prop="license_no" label="牌照" width="104"></el-table-column>
							<el-table-column prop="subjects" label="科目" width="104"></el-table-column>
							<el-table-column prop="price" label="单价" width="104"></el-table-column>
							<el-table-column prop="status" label="是否可被预约" width="104">
								<template scope="scope">
									<el-button type="success" v-if="parseInt(scope.row.status) == 1">可以</el-button>
						      	</template>
							</el-table-column>
							<el-table-column prop="status" label="预约状态" width="104">
								<template scope="scope">
									<el-button type="success" v-if="parseInt(scope.row.status) == 1">可以</el-button>
						      	</template>
							</el-table-column>
						</el-table>
                    </el-form-item>
                </el-tab-pane> -->
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
            lesson_options: '',
            timeActive: "first",
            os_type: "time",
            checked: true,
            list: [],
            am_list: [],
            pm_list: [],
            am_time_selectd_list: [],
            pm_time_selectd_list: [],
            amtime_list2: [],
            pmtime_list2: [],
            coach_time_list_am:[],
            coach_time_list_pm:[],
            date_list: [],
            coach_id: "<?php echo $coach_id;?>",
            school_id: "<?php echo $school_id;?>",
            ruleForm: {
            	coach_id: "<?php echo $coach_id;?>",
                s_am_subject: "<?php echo $s_am_subject;?>",
                s_pm_subject: "<?php echo $s_pm_subject;?>",
                s_am_time_list: "",
                s_pm_time_list: "",
                // s_am_time_list2: "",
                // s_pm_time_list2: "",
                // currentdate:"",
            },
            rules:{
                s_am_subject: [
                    { required: true, message: '请选择上午科目'}
                ],
                s_pm_subject: [
                    { required: true, message: '请选择下午科目'}
                ],
            },
            list_url: "<?php echo base_url('coach/setCoachTimeConfAjax')?>",
            lesson_url: "<?php echo base_url('coach/getLessonInfo')?>",
            timelist_url: "<?php echo base_url('coach/getTimeList')?>",
        },
        created: function() {
            var filter = {"id": this.coach_id, "school_id": this.school_id, "ot": this.os_type};
            this.getTimeList(filter);
            this.getLessonInfo();
        },
        methods: {
            handlePayTypeClick: function() {
				if(this.timeActive == 'second') {
					location.href="<?php echo base_url('coach/setCoachTimeConf?ot=current'); ?>"+"&id="+this.coach_id+"&school_id="+this.school_id;
				}
			},
            getTimeList: function(param) {
				$.ajax({
					type: 'post',
					url: this.timelist_url,
					data: param,
					dataType:"json",
					async: true,
					success: function(res) {
						// if(res.code == 200) {
                            vm.list = res.data.list;
                            // vm.am_list = res.data.amtime_list1;
							// vm.pm_list = res.data.pmtime_list1;
							// vm.amtime_list2 = res.data.amtime_list2;
							// vm.pmtime_list2 = res.data.pmtime_list2;
							// vm.coach_time_list_am = res.data.coach_time_list_am;
							// vm.coach_time_list_pm = res.data.coach_time_list_pm;
							// vm.date_list = res.data.date_list;
						// }
					},
					error: function() {
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				})
			},
            editAjax: function(params) {
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
                var am1Str = '';
                for (index in val) {
                    am1Str += val[index]['id'] + ',';
                }
                vm.ruleForm.s_am_time_list = am1Str;
			},
            handleSelectionChangePm1: function(val) {
                var pm1Str = '';
                for (index in val) {
                    pm1Str += val[index]['id'] + ',';
                }
                vm.ruleForm.s_pm_time_list = pm1Str;
			},
            messageNotice: function(type, msg) {
				this.$message({
					type: type,
					message: msg
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
    	}
    });

</script>
