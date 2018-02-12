<div id="app" v-cloak>
    <div class="iframe-content" style="margin-top: 20px;">
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
        
            <el-tabs v-model="activeName" type="card" @tab-click="handleClick">
                <el-tab-pane label="基本信息" name="first">

                    <el-form-item label="班制名称" prop="sh_title" required>
                        <el-input v-model="ruleForm.sh_title" placeholder="请输入班制名称不得超过10个字"></el-input>
                    </el-form-item>
                    <el-form-item label="价格" required>
                        <el-col :span="11">
                        <el-form-item prop="sh_original_money" label="原价">
                            <el-input v-model="ruleForm.sh_original_money" placeholder="请输入原价如4000"></el-input>
                        </el-form-item>
                        </el-col>
                        <el-col class="line" :span="1" style="text-align:center;">-</el-col>
                        <el-col :span="11">
                        <el-form-item prop="sh_money" label="最终价">
                            <el-input v-model="ruleForm.sh_money" placeholder="请输入最终价如3500"></el-input>
                        </el-form-item>
                        </el-col>
                    </el-form-item>
                    <el-form-item label="班制图片" prop="sh_imgurl" required>
                        <el-upload
                            :action="shifts_imgurl"
                            list-type="picture-card"
                            :show-file-list="false"
                            :on-success="handleImgurlSuccess"
                            :class="[ruleForm.isNull ? errorClass : '']" 
                            v-model="ruleForm.sh_imgurl">
                            <img v-if="ruleForm.schoolshifts" :src="ruleForm.schoolshifts" class="avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon" style="line-height: inherit !important;"></i>
                        </el-upload>
                    </el-form-item>
                    <el-form-item label="班制类型" prop="sh_type" required>
                        <el-select v-model="ruleForm.sh_type" placeholder="请选择班制类型">
                            <el-option label="计时班" value="1"></el-option>
                            <el-option label="非计时班" value="2"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="班制标签" prop="sh_tag_info" required>
                        <el-select v-model="ruleForm.sh_tag_info" clearable placeholder="请选择自定义班制标签">
                            <el-option v-for="item in tags_list" :label="item.tag_name" :value="item.id+'|'+item.tag_name"></el-option>
                        </el-select>
                        <el-button type="danger" @click="dialogFormVisible = true">添加标签</el-button>

                    </el-form-item>
                    <el-form-item label="牌照" prop="sh_license" required>
                        <el-select v-model="ruleForm.sh_license" clearable placeholder="请选择牌照">
                            <el-option v-for="item in license_list" :label="item.license_name" :value="item.license_id+'|'+item.license_name"></el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item v-if="school_id == ''">
                        <el-col style="width:50px;">
                            <el-form-item prop="iscoach">
                                <el-switch on-text="" off-text="" v-model="ruleForm.iscoach" @change="isCoach"></el-switch>
                            </el-form-item>
                        </el-col>
                        <el-col :span="15">
                            <span style="color: #FF4949;">切换到所属驾校或所属教练</span>
                        </el-col>
                    </el-form-item>
                    <el-form-item :label="ruleForm.iscoach ? coachlabel : schoollabel" v-if="school_id == '' " >
                        <el-form-item prop="sh_school_id" v-if="!ruleForm.iscoach">
                            <el-select
                                v-model="ruleForm.sh_school_id"
                                filterable
                                remote
                                clearable
                                placeholder="请输入驾校关键词如嘻哈"
                                :remote-method="remoteSchoolMethod"
                                @change="couponlistAjax"
                                :loading="loading">
                                <el-option
                                v-for="item in school_list"
                                :key="item.s_school_name"
                                :label="item.s_school_name"
                                :value="item.l_school_id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item prop="coach_id" v-else>
                            <el-select
                                v-model="ruleForm.coach_id"
                                filterable
                                remote
                                clearable
                                placeholder="请输入教练姓名如嘻哈"
                                :remote-method="remoteCoachMethod"
                                @change="couponlistAjax"
                                :loading="loading">
                                <el-option
                                v-for="item in coach_list"
                                :key="item.name"
                                :label="item.name"
                                :value="item.l_coach_id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-form-item>
                    <el-form-item prop="coach_id" label="教练名称" v-if="school_id != ''">
                        <el-select
                            v-model="ruleForm.coach_id"
                            filterable
                            remote
                            clearable
                            placeholder="请输入教练姓名如张教练"
                            :remote-method="remoteSchoolsCoachMethod"
                            @change="couponlistAjax"
                            :loading="loading">
                            <el-option
                            v-for="item in coach_list"
                            :key="item.s_coach_name"
                            :label="item.s_coach_name"
                            :value="item.l_coach_id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="排序" prop="order" required>
                        <el-input v-model="ruleForm.order" placeholder="请输入排序如：50"></el-input>
                    </el-form-item>
                    <el-form-item label="班制标签二" prop="sh_description_1" required>
                        <el-input v-model="ruleForm.sh_description_1" placeholder="请输入班制标签如：最快2个月拿证 不得超过10个字"></el-input>
                    </el-form-item>
                   
                    <el-form-item label="是否展示" prop="deleted">
                        <el-switch on-text="" off-text="" v-model="ruleForm.deleted"></el-switch>
                    </el-form-item>
                </el-tab-pane>
                
                <el-tab-pane label="活动配置" name="second">
                    <el-form-item label="是否促销" prop="is_promote">
                        <el-switch on-text="" off-text="" v-model="ruleForm.is_promote"></el-switch>
                    </el-form-item>
                    <el-form-item label="是否套餐" prop="is_package">
                        <el-switch on-text="" off-text="" v-model="ruleForm.is_package"></el-switch>
                    </el-form-item>
                    <el-form-item label="优惠券" prop="coupon_id">
                        <el-form-item prop="iscoupon">
                            <el-switch on-text="" off-text="" v-model="ruleForm.iscoupon"></el-switch>
                        </el-form-item>
                        <template v-if="ruleForm.iscoupon">
                            <el-select v-model="ruleForm.coupon_id" placeholder="请选择优惠券">
                                <el-option v-for="item in coupon_list" :label="item.coupon_name" :value="item.id"></el-option>
                            </el-select>
                        </template>
                    </el-form-item>
                </el-tab-pane>

                <el-tab-pane label="班制介绍" name="third" style="margin-bottom: 20px;">
                    <el-form-item label="套餐描述" prop="sh_info">
                        <vue-html5-editor :content="ruleForm.sh_info" :height="500" :show-module-name="showModuleName" @change="updateData" ref="editor"></vue-html5-editor>
                    </el-form-item>
                    <el-form-item label="班制说明2" prop="sh_description_2">
                        <vue-html5-editor :content="ruleForm.sh_description_2" :height="500" :show-module-name="showModuleName" @change="description" ref="editor"></vue-html5-editor>
                        <!-- <el-input type="textarea" v-model="ruleForm.sh_description_2" :autosize="{ minRows: 2, maxRows: 4}" placeholder="请输入班制简短说明 不得超过20字"></el-input> -->
                    </el-form-item>
                </el-tab-pane>
                
            </el-tabs>  

            <el-form-item>
                <el-button type="primary" @click="submitForm('ruleForm')">立即创建</el-button>
                <el-button @click="resetForm('ruleForm')">重置</el-button>
            </el-form-item>
        </el-form>

        <el-dialog title="添加自定义标签" v-model="dialogFormVisible">
            <el-form :model="tagForm" :rules="tagRules" ref="tagForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
                <el-form-item label="标签中文名" prop="tag_name" :label-width="formLabelWidth" required>
                    <el-input v-model="tagForm.tag_name" placeholder="请输入中文标签 如：拿证快 不超过8个字"></el-input>
                </el-form-item>
                <el-form-item label="标签英文名" prop="tag_slug" :label-width="formLabelWidth" required>
                    <el-input v-model="tagForm.tag_slug" placeholder="请输入英文标签 如：nazhengkuai"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="resetForm('tagForm')">取 消</el-button>
                <el-button type="primary" @click="addSysTag('tagForm')">确 定</el-button>
            </div>
        </el-dialog>

    </div>

</div>

<script>
    var school_id = "<?php echo $school_id;?>";
    Vue.use(VueHtml5Editor, {
        showModuleName: true,
        image: {
            sizeLimit: 512 * 1024,
            compress: true,
            width: 500,
            height: 500,
            quality: 80
        }
    });
    var vm = new Vue({
        el: "#app",
        data: {
            labelPosition: 'right',
            textarea: '',
            ruleForm: { 
                sh_school_id: school_id,
                coach_id: '',
                sh_title: '',
                sh_money: '',
                sh_original_money: '',
                sh_type: '',
                sh_description_1: '',
                sh_license: '',
                sh_license_id: '',
                sh_license_name: '',
                sh_tag_info: '',
                sh_tag: '',
                sh_tag_id: '',
                is_promote: false,
                coupon_id: '',
                sh_description_2: '',
                sh_info: '',
                order: 50,
                is_package: false,
                deleted: false,
                iscoach: false,
                iscoupon: false,
                sh_imgurl: '',
                schoolshifts: '',
            },
            rules: {
                sh_school_id: [
                    { required: true, message: '请选择驾校', trigger: 'blur' }
                ],
                // coach_id: [
                //     { required: true, message: '请选择教练', trigger: 'blur' }
                // ],
                sh_title: [
                    { required: true, message: '请输入班制名称', trigger: 'blur' }
                ],
                sh_original_money: [
                    { required: true, message: '请输入原价', trigger: 'blur' }
                ],
                sh_money: [
                    { required: true, message: '请输入最终价', trigger: 'blur' }
                ],
                sh_type: [
                    { required: true, message: '请选择班制类型', trigger: 'change' }
                ],
                sh_license: [
                    { required: true, message: '请选择牌照', trigger: 'change' }
                ],
                sh_tag_info: [
                    { required: true, message: '请选择班制标签', trigger: 'change' }
                ],
                // sh_description_1: [
                //     { required: true, message: '请输入班制说明一', trigger: 'blur' }
                // ],
                // sh_description_2: [
                //     { required: true, message: '请输入班制说明二', trigger: 'blur' }
                // ],
                // sh_info: [
                //     { required: true, message: '请输入班制介绍', trigger: 'blur' }
                // ],
                
            },
            tagForm: {
                tag_name: '',
                tag_slug: '',
            },
            tagRules: {
                tag_name: [
                    { required: true, message: '请输入中文标签', trigger: 'blur' }
                ],
                tag_slug: [
                    { required: true, message: '请输入英文标签', trigger: 'blur' }
                ],
            },
            dialogFormVisible: false,
            formLabelWidth: '120px',
            showModuleName: false,
            place: {
                province_list: [],
                city_list: [],
                area_list: []
            },
            base_url: "<?php echo base_url('school/addshiftsajax'); ?>",
            ssearch_url: "<?php echo base_url('school/search')?>",
            // csearch_url: "<?php echo base_url('coach/search')?>",
            scsearch_url: "<?php echo base_url('school/searchCoachAjax')?>",
            systags_url: "<?php echo base_url('school/systagsajax')?>",
            addsystags_url: "<?php echo base_url('school/addsystagajax')?>",
            liceconfig_url: "<?php echo base_url('school/liceconfigajax')?>",
            couponlist_url: "<?php echo base_url('school/couponajax')?>",
            shifts_imgurl: "<?php echo base_url('upload/handle?type=schoolshifts') ?>",
            activeName: 'first',
            school_list: [],
            coach_list: [],
            tags_list: [],
            loading: false,
            license_list: [],
            coupon_list: [],
            coachlabel: '所属教练',
            schoollabel: '所属驾校',
        },
        created: function() {
            this.sysTagsAjax();
            this.liceConfigAjax();
            // this.couponlistAjax();
        },
        methods: {
            handleImgurlSuccess: function(response, file) {
                this.ruleForm[response.data.type] = URL.createObjectURL(file.raw);
                this.ruleForm.sh_imgurl = response.data.url;
            },
            isCoach: function() {
                this.ruleForm.coupon_id = 0;
                this.coupon_list = [];
                if(this.ruleForm.iscoach) {
                    this.ruleForm.sh_school_id = '';
                } else {
                    this.ruleForm.coach_id = '';
                }
            },
            handleClick: function(tab, event) {
                // console.log(tab, event);
            },
            addAjax: function(params) {
                $.ajax({
					type: 'post',
					url: this.base_url,
					data: params,
					dataType:"json",
					success: function(data) {
                        if(data.code == 200) {
                            parent.layer.closeAll();
                            parent.vm.messageNotice('success', data.msg);
                            parent.vm.listAjax();
                        } else {
                            parent.vm.messageNotice('warning', data.msg);
                        }
					},
					error: function() {
                        vm.messageNotice('warning', '网络错误，请检查网络');
					}
				});
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
                        vm.addAjax(vm.ruleForm);
                    } else {
                        return false;
                    }
                });
            },
            resetForm: function(formName) {
                this.dialogFormVisible = false;
                this.$refs[formName].resetFields();
            },
            remoteSchoolsCoachMethod: function(query){
                if (query !== '') {
                    this.loading = true;
                    $.ajax({
                        type: 'post',
                        url: this.scsearch_url,
                        data: {key: query},
                        dataType: 'json',
                        success:function(data) {
                            vm.loading = false;
                            vm.coach_list = data.data;
                        },
                        error: function() {
                            vm.messageNotice('warning', '搜索教练出现网络错误');
                        }
                    });
                    this.couponlistAjax();
                } else {
                    this.coach_list = [];
                }
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
                    this.couponlistAjax();
                } else {
                    this.school_list = [];
                }
            },
            remoteCoachMethod: function(query) {
                if (query !== '') {
                    this.loading = true;
                    $.ajax({
                        type: 'post',
                        url: this.scsearch_url,
                        data: {key: query},
                        dataType: 'json',
                        success:function(data) {
                            vm.loading = false;
                            vm.coach_list = data.data;
                        },
                        error: function() {
                            vm.messageNotice('warning', '搜索教练出现网络错误');
                        }
                    });
                    this.couponlistAjax();
                } else {
                    this.coach_list = [];
                }
            },
            updateData: function (data) {
                this.ruleForm.sh_info = data
            },
            description: function (data) {
                this.ruleForm.sh_description_2 = data
            },
            addSysTag: function(formName) {
                this.$refs[formName].validate(function(valid) {
                    if (valid) {
                        $.ajax({
                            type: 'post',
                            url: vm.addsystags_url,
                            data: vm.tagForm,
                            dataType: 'json',
                            success:function(data) {
                                vm.dialogFormVisible = false;
                                if(data.code == 200) {
                                    vm.messageNotice('success', data.msg);
                                    var param = {
                                        'id': data.result,
                                        'tag_name': vm.tagForm.tag_name,
                                    };
                                    vm.tags_list.unshift(param);
                                } else {
                                    vm.messageNotice('warning', data.msg);
                                }
                            },
                            error: function() {
                                vm.messageNotice('warning', '网络错误，请检查网络');
                            }
                        });
                    } else {
                        return false;
                    }
                });
            },
            sysTagsAjax: function() {
                $.ajax({
                    type: 'post',
                    url: this.systags_url,
                    dataType: 'json',
                    success:function(data) {
                        vm.tags_list = data.data.list;
                    },
                    error: function() {
                        vm.messageNotice('warning', '获取班制标签错误');
                    }
                });
            },
            liceConfigAjax: function() {
                $.ajax({
                    type: 'post',
                    url: this.liceconfig_url,
                    dataType: 'json',
                    success:function(data) {
                        vm.license_list = data.data.list;
                    },
                    error: function() {
                        vm.messageNotice('warning', '获取牌照列表错误');
                    }
                });
            },
            couponlistAjax: function() {
                var owner_type = this.ruleForm.coach_id ? 1 : 2;
                var owner_id = this.ruleForm.coach_id ? this.ruleForm.coach_id : this.ruleForm.sh_shool_id;
                $.ajax({
                    type: 'post',
                    url: this.couponlist_url,
                    data: {'owner_type': owner_type, 'owner_id': owner_id},
                    dataType: 'json',
                    success:function(data) {
                        vm.coupon_list = data.data.list;
                    },
                    error: function() {
                        vm.messageNotice('warning', '获取优惠券列表错误');
                    }
                });
            }
        }
    });

</script>
