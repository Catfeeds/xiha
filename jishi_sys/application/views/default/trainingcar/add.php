<div id="app" v-cloak>
    <article class="page-container">
        <form class="form form-horizontal" id="form-article-add">
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">车架号：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" v-model="form.franum.value" id="franum" name="franum" placeholder="请输入车架号">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">发动机号：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" v-model="form.engnum.value" id="engnum" name="engnum" placeholder="请输入发动机号">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>车牌号：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" v-bind:class="[form.licnum.isNull ? errorClass : '']" v-model="form.licnum.value" id="licnum" name="licnum" placeholder="请输入车牌号">
                    <label v-if="form.licnum.isNull" id="" v-text="form.licnum.notice" class="error" for=""></label>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>车牌颜色：</label>
                <div class="formControls col-xs-8 col-sm-9"> <span class="select-box">
                    <select name="platecolor" class="select" v-bind:class="[form.platecolor.isNull ? errorClass : '']" v-model="form.platecolor.value">
                        <option value="0">请选择颜色</option>
                        <option value="1">蓝色</option>
                        <option value="2">黄色</option>
                        <option value="3">黑色</option>
                        <option value="4">白色</option>
                        <option value="5">绿色</option>
                        <option value="9">其他</option>
                    </select>
                    </span> </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>生产厂家：</label>
                <div class="formControls col-xs-8 col-sm-9"> 
                    <input type="text" class="input-text" v-bind:class="[form.manufacture.isNull ? errorClass : '']" v-model="form.manufacture.value" id="manufacture" name="manufacture" placeholder="请输入生产厂家">
                    <label v-if="form.manufacture.isNull" id="" v-text="form.manufacture.notice" class="error" for="manufacture"></label>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>车辆品牌：</label>
                <div class="formControls col-xs-8 col-sm-9"> 
                    <input type="text" class="input-text" id="brand" name="brand" v-model="form.brand.value" placeholder="请输入车辆品牌">
                    <label v-if="form.brand.isNull" id="" v-text="form.brand.notice" class="error" for="brand"></label>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">车辆型号：</label>
                <div class="formControls col-xs-8 col-sm-9"> 
                    <input type="text" class="input-text" id="model" name="model" v-model="form.model.value" placeholder="请输入车辆型号">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>培训车型：</label>
                <div class="formControls col-xs-8 col-sm-9"> <span class="select-box">
                    <select name="perdritype" class="select" v-bind:class="[form.perdritype.isNull ? errorClass : '']" v-model="form.perdritype.value">
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
                <label class="form-label col-xs-4 col-sm-2">购买日期：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text laydate-icon" @click="chooseDate($event)" style="height: 31px;" v-model="form.buydate.value" id="buydate" name="buydate" placeholder="请选择时间">                
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
                franum:         {value: '',isNull: false,notice: ''},
                engnum:         {value: '',isNull: false,notice: ''},
                licnum:         {value: '',isNull: false,notice: ''},
                platecolor:     {value: 0,isNull: false,notice: ''},
                manufacture:    {value: '',isNull: false,notice: ''},
                brand:          {value: '',isNull: false,notice: ''},
                model:          {value: '',isNull: false,notice: ''},
                perdritype:     {value: '',isNull: false,notice: ''},
                buydate:        {value: '',isNull: false,notice: ''},
            };
    var vm = new Vue({
        el: "#app",
        data: {
            form: formEle,
            errorClass: 'error',
            base_url: "<?php echo base_url('trainingcar/addajax'); ?>",
        },
        watch: {
            'form.franum.value':        function() {this.form.franum.isNull = false;},
            'form.engnum.value':        function() {this.form.engnum.isNull = false;},
            'form.licnum.value':        function() {this.form.licnum.isNull = false;},
            'form.platecolor.value':    function() {this.form.platecolor.isNull = false;},
            'form.manufacture.value':   function() {this.form.manufacture.isNull = false;},
            'form.brand.value':         function() {this.form.brand.isNull = false;},
            'form.model.value':         function() {this.form.model.isNull = false;},
            'form.perdritype.value':    function() {this.form.perdritype.isNull = false;},
            'form.buydate.value':       function() {this.form.buydate.isNull = false;},
        },
        methods: {
            // 数据验证
            validateform: function() {
                if(this.form.licnum.value.trim() === '') {
                    this.form.licnum.isNull = true;
                    this.form.licnum.notice = '请输入车辆牌号';
                    return false;
                }
                if(this.form.platecolor.value === 0) {
                    this.form.platecolor.isNull = true;
                    this.form.platecolor.notice = '请选择车辆颜色';
                    return false;
                }
                if(this.form.manufacture.value.trim() === '') {
                    this.form.manufacture.isNull = true;
                    this.form.manufacture.notice = '请输入生产厂家';
                    return false;
                }
                if(this.form.brand.value.trim() === '') {
                    this.form.brand.isNull = true;
                    this.form.brand.notice = '请输入车辆品牌';
                    return false;
                }
                if(this.form.perdritype.value.trim() === '') {
                    this.form.perdritype.isNull = true;
                    this.form.perdritype.notice = '请选择培训车型';
                    return false;
                }
                var params = {
                    franum: this.form.franum.value,
                    engnum: this.form.engnum.value,      
                    licnum: this.form.licnum.value,
                    platecolor: this.form.platecolor.value,      
                    manufacture: this.form.manufacture.value,  
                    brand: this.form.brand.value,
                    perdritype: this.form.perdritype.value,  
                    model: this.form.model.value,
                    buydate: this.form.buydate.value,
                };
                this.addAjax(params);
            },
            chooseDate: function(e) {
                var id = e.currentTarget.id;
                laydate({
                    elem: '#'+e.currentTarget.id,
                    format: 'YYYYMMDD', // 分隔符可以任意定义，该例子表示只显示年月
                    choose: function(data) {
                        vm['form'][id]['value'] = data;
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

        }
    });
    
</script>
