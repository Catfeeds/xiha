<div id="app" v-cloak>
    <div class="iframe-content" style="margin-top: 20px">
    <!--表单部分-->
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
            <el-form-item prop="cityid" label="市名称">
                    <el-select
                        v-model="ruleForm.city"
                        filterable
                        remote
                        clearable
                        placeholder="请输入市名称"
                        :remote-method="remoteCityMethod"
                        :loading="loading">
                        <el-option
                        v-for="item in city_list"
                        :key="item.city"
                        :label="item.city"
                        :value="item.cityid">
                        </el-option>
                    </el-select>
            </el-form-item>
            <el-form-item label="地区名称" prop="area" required>
                <el-col :span="10">
                    <el-input v-model="ruleForm.area" placeholder="请输入区名称,如：肥西县" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="区号" prop="areaid" required>
                <el-col :span="10">
                    <el-input v-model="ruleForm.areaid" placeholder="请输入区号，如：340123"></el-input>
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
            city_list: [],
            ruleForm: {
                id: "<?php echo $id; ?>",
                city : "<?php echo $city; ?>",
                cityid: "<?php echo $cityid; ?>",
                area: "<?php echo $area?>",
                areaid: "<?php echo $areaid?>",
            },
            rules: {
                cityid: [
                    { required: true, message: '请输入市名称', trigger: 'blur' }
                ],
                area: [
                    { required: true, message: '请输入地区名称', trigger: 'blur' }
                ],
                areaid: [
                    { required: true, message: '请输入地区区号', trigger: 'blur' }
                ],
            },
            search_url: "<?php echo base_url('city/searchAjax')?>?name=city",
            edit_url: "<?php echo base_url('city/editAjax')?>?name=area",

        },
        created: function() {

        },
        methods: {
            remoteCityMethod: function(query) {
                console.log(query);
                if (query !== '') {
                    this.loading = true;
                    $.ajax({
                        type: 'post',
                        url: this.search_url,
                        data: {key: query},
                        dataType: 'json',
                        success:function(data) {
                            vm.loading = false;
                            vm.city_list = data.data;
                        },
                        error: function() {
                            vm.messageNotice('warning', '搜索市出现网络错误');
                        }
                    });
                   
                } 
            },
            editAjax: function(params) {
                $.ajax({
                    type: 'post',
                    url: this.edit_url,
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