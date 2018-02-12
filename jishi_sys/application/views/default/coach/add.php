<div id="app" v-cloak>
    <article class="page-container">
        <form class="form form-horizontal" id="form-article-add">
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>教练姓名：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" v-bind:class="[form.coachname.isNull ? errorClass : '']" v-model="form.coachname.title" id="name" name="name" placeholder="请输入教练姓名">
                    <label v-if="form.coachname.isNull" id="" v-text="form.coachname.notice" class="error" for=""></label>
                </div>
            </div>

            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>教练头像：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <el-upload
                      :action="upload_url"
                      list-type="picture-card"
                      :show-file-list="false"
                      :on-success="handleSuccess"
                      :class="[form.photo.isNull ? errorClass : '']" 
                      v-model="form.photo.title">
                      <img v-if="form.photo.file_url" :src="form.photo.file_url" class="avatar">
                      <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                    </el-upload>
                </div>
            </div>

            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>身份证号码：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" v-bind:class="[form.idcard.isNull ? errorClass : '']" v-model="form.idcard.title" id="idcard" name="idcard" placeholder="请输入身份证号码">
                    <label v-if="form.idcard.isNull" id="" v-text="form.idcard.notice" class="error" for=""></label>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>性别：</label>
                <div class="formControls col-xs-8 col-sm-9"> <span class="select-box">
                    <select name="articlecolumn" class="select" v-bind:class="[form.sex.isNull ? errorClass : '']" v-model="form.sex.select">
                        <option value="0">请选择性别</option>
                        <option value="1">男</option>
                        <option value="2">女</option>
                    </select>
                    </span> </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>手机号：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" v-bind:class="[form.mobile.isNull ? errorClass : '']" v-model="form.mobile.title" id="mobile" name="mobile" placeholder="请输入手机号">
                    <label v-if="form.mobile.isNull" id="" v-text="form.mobile.notice" class="error" for="mobile"></label>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">联系地址：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" id="address" name="address" v-model="form.address.title" placeholder="请输入联系地址">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>驾驶证号：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" v-bind:class="[form.drilicence.isNull ? errorClass : '']" v-model="form.drilicence.title" id="drilicence" name="drilicence" placeholder="请输入驾驶证号">
                    <label v-if="form.drilicence.isNull" id="" v-text="form.drilicence.notice" class="error" for=""></label>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>驾驶证初领日期：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text laydate-icon" @click="chooseDate($event)" style="height: 31px;" v-bind:class="[form.fstdrilicdate.isNull ? errorClass : '']" v-model="form.fstdrilicdate.title" id="fstdrilicdate" name="fstdrilicdate" placeholder="请选择时间">
                    <label v-if="form.fstdrilicdate.isNull" id="" v-text="form.fstdrilicdate.notice" class="error" for=""></label>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">职业资格证号：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" id="occupationno" v-model="form.occupationno.title"  name="occupationno" placeholder="请输入职业资格证号">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">职业资格等级：</label>
                <div class="formControls col-xs-8 col-sm-9"> <span class="select-box">
                    <select name="occupationlevel" class="select" v-model="form.occupationlevel.select">
                        <option value="">请选择等级</option>
                        <option value="1">一级</option>
                        <option value="2">二级</option>
                        <option value="3">三级</option>
                        <option value="4">四级</option>
                    </select>
                    </span> </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>准驾车型：</label>
                <div class="formControls col-xs-8 col-sm-9"> <span class="select-box">
                    <select name="dripermitted" class="select" v-bind:class="[form.dripermitted.isNull ? errorClass : '']" v-model="form.dripermitted.select">
                        <option value="">请选择车型</option>
                        <option value="A1">A1</option>
                        <option value="A2">A2</option>
                        <option value="A3">A3</option>
                        <option value="B1">B1</option>
                        <option value="B2">B2</option>
                        <option value="C1">C1</option>
                        <option value="C2">C2</option>
                        <option value="C3">C3</option>
                        <option value="C4">C4</option>
                        <option value="C5">C5</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        <option value="F">F</option>
                        <option value="M">M</option>
                        <option value="N">N</option>
                        <option value="P">P</option>
                    </select>
                    </span> </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>准教车型：</label>
                <div class="formControls col-xs-8 col-sm-9"> <span class="select-box">
                    <select name="teachpermitted" class="select" v-bind:class="[form.teachpermitted.isNull ? errorClass : '']" v-model="form.teachpermitted.select">
                        <option value="">请选择车型</option>
                        <option value="A1">A1</option>
                        <option value="A2">A2</option>
                        <option value="A3">A3</option>
                        <option value="B1">B1</option>
                        <option value="B2">B2</option>
                        <option value="C1">C1</option>
                        <option value="C2">C2</option>
                        <option value="C3">C3</option>
                        <option value="C4">C4</option>
                        <option value="C5">C5</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        <option value="F">F</option>
                        <option value="M">M</option>
                        <option value="N">N</option>
                        <option value="P">P</option>
                    </select>
                    </span> </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>供职状态：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <span class="select-box">
                        <select name="employstatus" class="select" v-bind:class="[form.employstatus.isNull ? errorClass : '']" v-model="form.employstatus.select">
                            <option value="">请选择供职状态</option>
                            <option value="0">在职</option>
                            <option value="1">离职</option>
                        </select>
                    </span>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>入职日期：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text laydate-icon" @click="chooseDate($event)" style="height: 31px;" v-bind:class="[form.hiredate.isNull ? errorClass : '']" v-model="form.hiredate.title" id="hiredate" name="hiredate" placeholder="请选择时间">
                    <label v-if="form.hiredate.isNull" id="" v-text="form.hiredate.notice" class="error" for=""></label>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">离职日期：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text laydate-icon" @click="chooseDate($event)" v-model="form.leavedate.title" style="height: 31px;" id="leavedate" name="leavedate" placeholder="请选择时间">
                </div>
            </div>
            <div class="row cl">
                <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                    <button class="btn btn-primary radius" type="button" v-on:click.stop="validateform"><i class="Hui-iconfont">&#xe632;</i> 保存并提交审核</button>
                    <button class="btn btn-default radius" type="reset">&nbsp;&nbsp;取消&nbsp;&nbsp;</button>
                </div>
            </div>

        </form>
    </article>

</div>

<script>
    var formEle = {
                coachname:      {title: '',isNull: false,notice: ''},
                idcard:         {title: '',isNull: false,notice: ''},
                sex:            {select: 0,isNull: false,notice: ''},
                mobile:         {title: '',isNull: false,notice: ''},
                address:        {title: ''},
                drilicence:     {title: '',isNull: false,notice: ''},
                fstdrilicdate:  {title: '',isNull: false,notice: ''},
                occupationno:   {title: '',isNull: false,notice: ''},
                occupationlevel:{select: '',isNull: false,notice: ''},
                dripermitted:   {select: '',isNull: false,notice: ''},
                teachpermitted: {select: '',isNull: false,notice: ''},
                employstatus:   {select: '',isNull: false,notice: ''},
                hiredate:       {title: '',isNull: false,notice: ''},
                leavedate:      {title: '',isNull: false,notice: ''},
                photo:          {title: '',isNull: false,file_url: ''},
            };
    var vm = new Vue({
        el: "#app",
        data: {
            form: formEle,
            errorClass: 'error',
            base_url: "<?php echo base_url('coach/addajax'); ?>",
            upload_url: "<?php echo base_url('upload/handle?type=coachimg') ?>",
        },
        watch: {
            'form.coachname.title':     function() {this.form.coachname.isNull = false;},
            'form.idcard.title':        function() {this.form.idcard.isNull = false;},
            'form.sex.select':          function() {this.form.sex.isNull = false;},
            'form.mobile.title':        function() {this.form.mobile.isNull = false;},
            'form.drilicence.title':    function() {this.form.drilicence.isNull = false;},
            'form.fstdrilicdate.title': function() {this.form.fstdrilicdate.isNull = false;},
            'form.dripermitted.select':  function() {this.form.dripermitted.isNull = false;},
            'form.teachpermitted.select':function() {this.form.teachpermitted.isNull = false;},
            'form.employstatus.select':  function() {this.form.employstatus.isNull = false;},
            'form.hiredate.title':      function() {this.form.hiredate.isNull = false;},
        },
        methods: {
            // 数据验证
            validateform: function() {
                if(this.form.coachname.title.trim() === '') {
                    this.form.coachname.isNull = true;
                    this.form.coachname.notice = '请输入教练姓名';
                    return false;
                }
                if(this.form.photo.title.trim() === '') {
                    this.form.coachname.isNull = true;
                    this.form.coachname.notice = '请上传教练头像';
                    return false;
                }
                if(this.form.idcard.title.trim() === '') {
                    this.form.idcard.isNull = true;
                    this.form.idcard.notice = '请输入身份证号码';
                    return false;
                }
                if(this.form.idcard.title.length !== 15 && this.form.idcard.title.length !== 18) {
                    this.form.idcard.isNull = true;
                    this.form.idcard.notice = '身份证号码格式不正确';
                    return false;
                }
                if(this.form.sex.select === 0) {
                    this.form.sex.isNull = true;
                    this.form.sex.notice = '请选择性别';
                    return false;
                }
                if(this.form.mobile.title.trim() === '') {
                    this.form.mobile.isNull = true;
                    this.form.mobile.notice = '请输入手机号';
                    return false;
                }
                if(this.form.drilicence.title.trim() === '') {
                    this.form.drilicence.isNull = true;
                    this.form.drilicence.notice = '请输入驾驶证号';
                    return false;
                }
                if(this.form.fstdrilicdate.title.trim() === '') {
                    this.form.fstdrilicdate.isNull = true;
                    this.form.fstdrilicdate.notice = '请选择驾驶证初领日期';
                    return false;
                }
                if(isNaN(this.form.fstdrilicdate.title)) {
                    this.form.fstdrilicdate.isNull = true;
                    this.form.fstdrilicdate.notice = '日期格式不正确';
                    return false;
                }
                if(this.form.dripermitted.select.trim() === '') {
                    this.form.dripermitted.isNull = true;
                    this.form.dripermitted.notice = '请选择准驾车型';
                    return false;
                }
                if(this.form.teachpermitted.select.trim() === '') {
                    this.form.teachpermitted.isNull = true;
                    this.form.teachpermitted.notice = '请选择准教车型';
                    return false;
                }
                if(this.form.employstatus.select.trim() === '') {
                    this.form.employstatus.isNull = true;
                    this.form.employstatus.notice = '请选择供职状态';
                    return false;
                }
                if(this.form.hiredate.title.trim() === '') {
                    this.form.hiredate.isNull = true;
                    this.form.hiredate.notice = '请选择入职日期';
                    return false;
                }
                var params = {
                    coachname: this.form.coachname.title,
                    idcard: this.form.idcard.title,
                    sex: this.form.sex.select,
                    address: this.form.address.title,
                    mobile: this.form.mobile.title,
                    drilicence: this.form.drilicence.title,
                    fstdrilicdate: this.form.fstdrilicdate.title,
                    occupationno: this.form.occupationno.title,
                    occupationlevel: this.form.occupationlevel.select,
                    dripermitted: this.form.dripermitted.select,
                    teachpermitted: this.form.teachpermitted.select,
                    employstatus: this.form.employstatus.select,
                    hiredate: this.form.hiredate.title,
                };
                this.addAjax(params);
            },
            chooseDate: function(e) {
                var id = e.currentTarget.id;
                laydate({
                    elem: '#'+e.currentTarget.id,
                    format: 'YYYYMMDD', // 分隔符可以任意定义，该例子表示只显示年月
                    choose: function(data) {
                        vm['form'][id]['title'] = data;
                    }
                });
                laydate.skin('yalan');
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
                            vm.showResult(1, data.msg);
                            parent.vm.showContent();
                        } else {
                            vm.showResult(2, data.msg);
                        }
					},
					error: function() {
                        vm.showResult(0, '网络错误，请检查网络');
					}
				});
            },
            showResult: function(icon_no, msg) {
                parent.layer.msg(msg, {
                    icon: icon_no,
                    shade: 0.01,
                    offset: '0%',
                });
            },
            handleSuccess: function(response, file, fileList) {
                this.form.photo.file_url = URL.createObjectURL(file.raw);
                this.form.photo.title = response.data.file_id;
                // this.form.photo.file_url = response.data.file_url;
                // console.log(response);
                // console.log(file);
                // console.log(fileList);
            }

        }
    });

</script>
