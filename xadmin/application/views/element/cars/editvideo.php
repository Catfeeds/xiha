<div id="app" v-cloak>
    <div class="iframe-content" style="margin-top: 20px">
    <!--表单部分-->
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm" :label-position="labelPosition">
            <el-form-item label="科目" prop="course" required>
                <el-select v-model="ruleForm.course" placeholder="请选择科目">
                    <el-option value="kemu2" label="科目二"></el-option>
                    <el-option value="kemu3" label="科目三"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="车牌类型" prop="car_type" required>
                <el-select v-model="ruleForm.car_type" placeholder="请选择车牌类型">
                    <el-option value="car" label="小车"></el-option>
                    <el-option value="bus" label="客车"></el-option>
                    <el-option value="truck" label="货车"></el-option>
                    <el-option value="moto" label="摩托车"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="视频标题" prop="title" required>
                <el-col :span="11">
                    <el-input v-model="ruleForm.title" placeholder="如：侧方停车" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="技能介绍" prop="skill_intro" required>
                <el-col :span="11">
                    <el-input v-model="ruleForm.skill_intro" placeholder="如：一进一退方式将车停库区" ></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="排序" prop="order">
                <el-col :span="11">
                    <el-input v-model="ruleForm.order" placeholder="如：100"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="浏览人数" prop="views">
                <el-col :span="11">
                    <el-input v-model="ruleForm.views" placeholder="如：100"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="视频时间" prop="video_time">
                <el-col :span="11">
                    <el-input v-model="ruleForm.video_time" placeholder="如：120秒"></el-input>
                </el-col>
            </el-form-item>
            <el-form-item label="视频图片">
                <el-upload
                    :action="upload_picture"
                    list-type="picture-card"
                    :show-file-list="false"
                    :on-success="handlePic"
                    :class="[ruleForm.isNull ? errorClass : '']" 
                    v-model="ruleForm.picture">
                    <img v-if="ruleForm.video" :src="ruleForm.video" class="avatar">
                    <i v-else class="el-icon-plus avatar-uploader-icon" style="line-height: inherit !important;"></i>
                </el-upload>
            </el-form-item>
            <el-form-item label="科目视频">
                <el-upload
                    :action="upload_video"
                    class="upload-demo"
                    drag
                    :on-success="handleVideo"
                    :class="[ruleForm.isNull ? errorClass : '']" 
                    v-model="ruleForm.videourl">
                    <i class="el-icon-upload"></i>
                    <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
                    <div class="el-upload__tip" slot="tip">请上传科目视频</div>
                </el-upload>
            </el-form-item>
            <el-form-item label="开启否" prop="is_open">
                <el-switch
                    v-model="ruleForm.is_open"
                    on-text="开启"
                    off-text="关闭">
                </el-switch>
            </el-form-item>
            <el-form-item label="视频描述">
                <el-col :span="11">
                    <el-input type="textarea" v-model="ruleForm.video_desc" :autosize="{ minRows: 10, maxRows: 20}" placeholder="请输入视频描述"></el-input>
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
    var is_open = "<?php echo $is_open; ?>";
    var vm = new Vue({
        el: "#app",
        data: {
            labelPosition: 'right',
            textarea: '',
            errorClass: 'error',
            loading: false,
            ruleForm: {
                id: "<?php echo $id; ?>",
                title: "<?php echo $title; ?>",
                skill_intro: "<?php echo $skill_intro; ?>",
                car_type: "<?php echo $car_type; ?>",
                course: "<?php echo $course; ?>",
                order: "<?php echo $v_order; ?>",
                is_open: is_open = 1 ? true : false,
                views: "<?php echo $views; ?>",
                video_time: "<?php echo $video_time; ?>",
                video_desc: "<?php echo $video_desc; ?>",
                picture: "<?php echo $pic_url; ?>",
                video: "<?php echo $http_pic_url; ?>",
                videourl: "<?php echo $video_url; ?>",
            },
            rules: {
                title: [
                    { required: true, message: '请输入视频名称', trigger: 'blur' }
                ],
                course: [
                    { required: true, message: '请输入科目', trigger: 'blur' }
                ],
                car_type: [
                    { required: true, message: '请输入车牌类型', trigger: 'blur' }
                ],
                skill_intro: [
                    { required: true, message: '请输入技能介绍', trigger: 'blur' }
                ],
                video_time: [
                    { required: true, message: '请输入视频时间，单位：秒', trigger: '' }
                ],
            },
            add_url: "<?php echo base_url('cars/editAjax')?>?type=video",
            upload_picture: "<?php echo base_url('upload/handle?type=video') ?>",
            upload_video: "<?php echo base_url('upload/handle?type=video/download') ?>",

        },
        methods: {
            handlePic: function(response, file) {
                this.ruleForm[response.data.type] = URL.createObjectURL(file.raw);
                this.ruleForm.picture = response.data.url;
            },
            handleVideo: function(response, file) {
                this.ruleForm[response.data.type] = URL.createObjectURL(file.raw);
                this.ruleForm.videourl = response.data.url;
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
            messageNotice: function(type, msg) {
                this.$message({
                    type: type,
                    message: msg
                })
            }
        }

    });
</script>