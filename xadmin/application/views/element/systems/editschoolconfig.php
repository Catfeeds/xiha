<div id="app" v-cloak>
    <div class="iframe-content" style="margin-top: 20px">

    <!--表单部分-->
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="200px" class="demo-ruleForm" :label-position="labelPosition">
            <el-form-item label="所属驾校" prop="school_id" v-if="school_id == ''">
            <el-col :span="7">
                <el-input placeholder="" v-model="ruleForm.school_name" :disabled="true"></el-input>
            </el-col>
            </el-form-item>
            <!--<el-form-item label="所属驾校" required>
                <el-form-item prop="school_id" >
                    <el-select
                        v-model="ruleForm.school_id"
                        filterable
                        remote
                        clearable
                        placeholder="请输入驾校的名称"
                        :remote-method="remoteSchoolMethod"
                        :loading="loading">
                        <el-option
                        v-for="item in school_list"
                        :key="item.s_school_name"
                        :label="item.s_school_name"
                        :value="item.l_school_id">
                        </el-option>
                    </el-select>
                </el-form-item>
            </el-form-item>-->
            <el-form-item label="当天最多可取消订单" prop="cancel_order_time" required>
                <el-select v-model="ruleForm.cancel_order_time" placeholder="请">
                    <el-option label="1次" value="1"></el-option>
                    <el-option label="2次" value="2"></el-option>
                    <el-option label="3次" value="3"></el-option>
                    <el-option label="4次" value="4"></el-option>
                    <el-option label="8次" value="8"></el-option>
                    <el-option label="24次" value="24"></el-option>
                </el-select>
                <!--<el-col :span="10">
                    <el-input v-model="ruleForm.cancel_order_time" placeholder="请输入当天取消订单的次数，如：2" ></el-input>
                </el-col>-->
            </el-form-item>
            <el-form-item label="一天最多可预约" prop="sum_appoint_time" required>
                <el-select v-model="ruleForm.sum_appoint_time" placeholder="">
                    <el-option label="1小时" value="1"></el-option>
                    <el-option label="2小时" value="2"></el-option>
                    <el-option label="3小时" value="3"></el-option>
                    <el-option label="4小时" value="4"></el-option>
                    <el-option label="8小时" value="8"></el-option>
                </el-select>
                <!--<el-col :span="10">
                    <el-input v-model="ruleForm.cancel_order_time" placeholder="请输入当天最多可预约次数，如：2" ></el-input>
                </el-col>-->
            </el-form-item>
            <el-form-item label="取消订单需提前" prop="cancel_in_advance" required>
                <el-select v-model="ruleForm.cancel_in_advance" placeholder="">
                    <el-option label="1小时" value="1"></el-option>
                    <el-option label="2小时" value="2"></el-option>
                    <el-option label="4小时" value="4"></el-option>
                    <el-option label="8小时" value="8"></el-option>
                    <el-option label="12小时" value="12"></el-option>
                    <el-option label="1天" value="24"></el-option>
                    <el-option label="2天" value="48"></el-option>
                </el-select>
                <!--<el-col :span="10">
                    <el-input v-model="ruleForm.cancel_in_advance" placeholder="请输入取消订单需提前时间，如：2天" ></el-input>
                </el-col>-->
            </el-form-item>
            <el-form-item label="是否自动">
                <el-col style="width:50px;">
                    <el-form-item prop="is_auto">
                        <el-switch on-text="" off-text="" v-model="ruleForm.is_auto"></el-switch>
                    </el-form-item>
                </el-col>
            </el-form-item>
            <el-form-item label="时间设置" prop="time_list" required>
                <el-table :data="list" ref="singleTable" border tooltip-effect="dark" style="width: 60%" @selection-change="handleSelectionChange">
                    <el-table-column fixed type="selection" width="50"></el-table-column>
                    <!--<el-table-column prop="id" label="ID" min-width="80" show-overflow-tooltip></el-table-column> -->
                    <el-table-column prop="start" label="开始时间" min-width="150" show-overflow-tooltip></el-table-column> 
                    <el-table-column prop="end" label="结束时间" min-width="150" show-overflow-tooltip></el-table-column> 
                </el-table>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="submitForm('ruleForm')">立即创建</el-button>
                <el-button @click="resetForm('ruleForm')">重置</el-button>
            </el-form-item>
        </el-form>
    </div>
</div>

<script>
    var school_id = "<?php echo $school_id; ?>";
    var is_auto = "<?php echo $is_auto;?>";
    var vm = new Vue({
        el: "#app",
        data: {
            labelPosition: 'right',
            loading: false,
            school_list: [],
            list: [],
            ruleForm: {
                school_id: "<?php echo $l_school_id; ?>",
                school_name: "<?php echo $school_name; ?>",
                cancel_order_time: "<?php echo $cancel_order_time; ?>",
                sum_appoint_time: "<?php echo $sum_appoint_time; ?>", 
                is_auto: is_auto == 1 ? true : false,
                cancel_in_advance: "<?php echo $cancel_in_advance; ?>",
                time_list: "<?php echo $time_list; ?>",
            },
            rules: {
                school_id: [
                    { required: true, message: '请输入驾校名称', trigger: 'blur' }
                ],
                cancel_order_time: [
                    { required: true, message: '请输入当天取消订单的次数', trigger: 'change' }
                ],
                sum_appoint_time: [
                    { required: true, message: '请输入当天最多可预约的次数', trigger: 'change' }
                ],
                time_list: [
                    { required: true, message: '请选择时间段', trigger: 'blur' }
                ],
                cancel_in_advance: [
                    { required: true, message: '请输入提前多少小时取消', trigger: 'change' }
                ],
            },
            add_url: "<?php echo base_url('systems/editAjax')?>?type=sconf",
            ssearch_url: "<?php echo base_url('systems/searchAjax')?>?type=school",
            time_url: "<?php echo base_url('systems/timelistAjax')?>?sid="+"<?php echo $l_school_id?>",
        },
        created: function() {
            this.timelistAjax();
        },
        methods: {
            handleSelectionChange: function(val) {
                var time_list = '';
                for (index in val) {
                    time_list += val[index]['id'] + ',';
                }
                this.ruleForm.time_list = time_list;
                console.log(val);
			},
            remoteSchoolMethod: function(query) {
                if (query !== '') {
                    this.loading = true;
                    $.ajax({
                        type: 'post',
                        url: this.ssearch_url,
                        data: {key: query},
                        dataType: 'json',
                        success:function(data) {
                            vm.loading = false;
                            vm.school_list = data.data.list;
                        },
                        error: function() {
                            vm.messageNotice('warning', '搜索驾校出现网络错误');
                        }
                    });
                } 
            },
            timelistAjax: function() {
                $.ajax({
                    url: this.time_url,
                    dataType: 'json',
                    success:function(data) {
                        vm.loading = false;
                        vm.list = data.data.list;
                    },
                    error: function() {
                        vm.messageNotice('warning', '搜索驾校出现网络错误');
                    }
                });
            },
            editAjax: function(params) {
                $.ajax({
                    type: 'post',
                    url: this.add_url,
                    data: params,
                    dataType: 'json',
                    success: function(data) {
                        is_ok = _.isObject(data) && _.has(data, 'code') && _.get(data, 'code') == 200
                        if (is_ok) {
                            parent.layer.closeAll();
                            parent.vm.messageNotice('success', _.get(data, 'msg'));
                            parent.vm.listAjax(parent.vm.currentPage);
                        } else {
                            parent.vm.messageNotice('success', _.get(data, 'msg'));
                        }
                    },
                    error: function(e) {
                        vm.messageNotice('error', '网络出错！');
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
            resetForm: function(formName) {
                this.$refs[formName].resetFields();
            },
            messageNotice: function(type, msg) {
                this.$message({
                    type: type,
                    message: msg
                })
            }

        }

    });
</script>