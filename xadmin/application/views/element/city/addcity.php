<div id="app" v-cloak>
    <div class="iframe-content" style="margin-top: 20px">
    <!--表单部分-->
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
            <el-form-item prop="provinceid" label="省名称">
                    <el-select
                        v-model="ruleForm.provinceid"
                        filterable
                        remote
                        clearable
                        placeholder="请输入省名称"
                        :remote-method="remoteProvinceMethod"
                        :loading="loading">
                        <el-option
                        v-for="item in province_list"
                        :key="item.province"
                        :label="item.province"
                        :value="item.provinceid">
                        </el-option>
                    </el-select>
            </el-form-item>
            <el-form-item label="市名称" prop="city" required>
                <el-col :span="10">
                    <el-input v-model="ruleForm.city" placeholder="请输入市名称,如：合肥市" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="市区号" prop="cityid" required>
                <el-col :span="10">
                    <el-input v-model="ruleForm.cityid" placeholder="请输入市区号，如： 340100"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="是否热门" prop="is_hot">
                <el-switch
                    v-model="ruleForm.is_hot"
                    on-text=""
                    off-text="">
                </el-switch>
            </el-form-item>
            <el-form-item label="市首字母" prop="leter" required>
                <el-col :span="10">
                    <el-input v-model="ruleForm.leter" placeholder="请输入市首字母，如：H"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="市缩写" prop="acronym" required>
                <el-col :span="10">
                    <el-input v-model="ruleForm.acronym" placeholder="请输入市缩写，如：hf"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="市全拼" prop="spelling" required>
                <el-col :span="10">
                    <el-input v-model="ruleForm.spelling" placeholder="请输入市全拼，如：Hefei"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="submitForm('ruleForm')">立即创建</el-button>
                <el-button @click="resetForm('ruleForm')">重置</el-button>
            </el-form-item>
                
        </el-form>
    </div>
</div>

<script>
    var vm = new Vue({
        el: "#app",
        data: {
            loading: false,
            labelPosition: 'right',
            textarea: '',
            province_list: [],
            ruleForm: {
                provinceid: "",
                cityid: "",
                city: "",
                leter: "",
                acronym: "",
                spelling: "",
                is_hot: false
            },
            rules: {
                provinceid: [
                    { required: true, message: '请输入省名称', trigger: 'blur' }
                ],
                city: [
                    { required: true, message: '请输入市名称', trigger: 'blur' }
                ],
                cityid: [
                    { required: true, message: '请输入市区号', trigger: 'blur' }
                ],
                acronym: [
                    { required: true, message: '请输入地区区号', trigger: 'blur' }
                ],
                leter: [
                    { required: true, message: '请输入地区区号', trigger: 'blur' }
                ],
                spelling: [
                    { required: true, message: '请输入地区区号', trigger: 'blur' }
                ],
            },
            search_url: "<?php echo base_url('city/searchAjax')?>?name=province",
            add_url: "<?php echo base_url('city/addAjax')?>?name=city",

        },
        created: function() {

        },
        methods: {
            remoteProvinceMethod: function(query) {
                if (query !== '') {
                    this.loading = true;
                    $.ajax({
                        type: 'post',
                        url: this.search_url,
                        data: {key: query},
                        dataType: 'json',
                        success:function(data) {
                            vm.loading = false;
                            vm.province_list = data.data;
                        },
                        error: function() {
                            vm.messageNotice('warning', '搜索市出现网络错误');
                        }
                    });
                } 
            },
            addAjax: function(params) {
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
                            parent.vm.messageNotice('error', _.get(data, 'msg'));
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
                        vm.addAjax(vm.ruleForm);
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