<?php $this->load->view('default/header'); ?>
<div id="app">
    <article class="page-container">
        <form class="form form-horizontal" id="form-article-add">
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>文章标题：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" v-bind:class="[form.title1.isNull ? errorClass : '']" v-model="form.title1.title" id="articletitle" name="articletitle" placeholder="请输入标题">
                    <label v-if="form.title1.isNull" id="" class="error" for="">{{ form.title1.notice }}</label>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">简略标题：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" v-bind:class="[form.title2.isNull ? errorClass : '']" v-model="form.title2.title" placeholder="请输入简略标题" id="articletitle2" name="articletitle2">
                    <label v-if="form.title2.isNull" id="" class="error" for="">{{ form.title2.notice }}</label>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>分类栏目：</label>
                <div class="formControls col-xs-8 col-sm-9"> <span class="select-box">
                    <select name="articlecolumn" class="select" v-bind:class="[form.select1.isNull ? errorClass : '']" v-model="form.select1.select">
                        <option value="">请选择栏目</option>
                        <option value="0">全部栏目</option>
                        <option value="1">新闻资讯</option>
                        <option value="11">├行业动态</option>
                        <option value="12">├行业资讯</option>
                        <option value="13">├行业新闻</option>
                    </select>
                    </span> </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>文章类型：</label>
                <div class="formControls col-xs-8 col-sm-9"> <span class="select-box">
                    <select name="articletype" class="select">
                        <option value="0">全部类型</option>
                        <option value="1">帮助说明</option>
                        <option value="2">新闻资讯</option>
                    </select>
                    </span> </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">文章摘要：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <textarea  v-bind:class="[form.textarea.isNull ? errorClass : '']" v-model="form.textarea.content" name="abstract" cols="" rows="" class="textarea"  placeholder="说点什么...最少输入10个字符" datatype="*10-100" dragonfly="true" nullmsg="备注不能为空！" onKeyUp="$.Huitextarealength(this,200)"></textarea>
                    <p class="textarea-numberbar"><em class="textarea-length">0</em>/200</p>
                    <label v-if="form.textarea.isNull" id="" class="error" for="">{{ form.textarea.notice }}</label>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">文章作者：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" value="0" placeholder="" id="author" name="author">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">文章来源：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" class="input-text" value="0" placeholder="" id="sources" name="sources">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">评论开始日期：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" id="commentdatemin" name="commentdatemin" class="input-text Wdate">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">评论结束日期：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <input type="text" id="commentdatemax" name="commentdatemax" class="input-text Wdate">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">缩略图：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <div class="uploader-thum-container">
                        <div id="fileList" class="uploader-list"></div>
                        <div id="filePicker">选择图片</div>
                        <button id="btn-star" class="btn btn-default btn-uploadstar radius ml-10">开始上传</button>
                    </div>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-2">文章内容：</label>
                <div class="formControls col-xs-8 col-sm-9">
                    <script id="editor" type="text/plain" style="width:100%;height:400px;"></script>
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
    var vmadd = new Vue({
        el: "#app",
        data: {
            form: {
                title1: {
                    title: '',
                    isNull: false,
                    notice: ''
                },
                title2: {
                    title: '',
                    isNull: false,
                    notice: ''
                },
                select1: {
                    select: '',
                    isNull: false,
                    notice: ''
                },
                textarea: {
                    content: '',
                    isNull: false,
                    notice: '',
                },
            },
            errorClass: 'error',
        },
        watch: {
            'form.title1.title': function() {
                this.form.title1.isNull = false;
            },
            'form.title2.title': function() {
                this.form.title2.isNull = false;
            },
            'form.select1.select': function() {
                this.form.select1.isNull = false;
            },
            'form.textarea.content': function() {
                this.form.textarea.isNull = false;
            }
        },
        methods: {
            // 数据验证
            validateform: function() {
                if(this.form.title1.title.trim() === '') {
                    this.form.title1.isNull = true;
                    this.form.title1.notice = '请输入文章标题';
                    return false;
                }
                if(this.form.title2.title.trim() === '') {
                    this.form.title2.isNull = true;
                    this.form.title2.notice = '请输入简略标题';
                    return false;
                }
                if(this.form.select1.select.trim() === '') {
                    this.form.select1.isNull = true;
                    return false;
                }
                if(this.form.textarea.content.trim() === '' || this.form.textarea.content.length < 10) {
                    this.form.textarea.isNull = true;
                    this.form.textarea.notice = '请最少输入10个字符';
                    return false;
                }
                parent.layer.closeAll();
                parent.layer.msg('提交成功', {
                icon: 1,
                    shade: 0.01,
                    offset: '0%',
                });
                console.log(this.form.title2.title)
            }
        }
    });
</script>
<?php $this->load->view('default/footer'); ?>
