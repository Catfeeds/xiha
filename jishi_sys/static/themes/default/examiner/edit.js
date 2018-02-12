var vm = new Vue({
    el: '#app',
    data: {
        labelPosition: 'left',
        activeName: 'basicinfo',
        occupationleveloptions: [{
                'label': '一级',
                'value': '1'
            },
            {
                'label': '二级',
                'value': '2'
            },
            {
                'label': '三级',
                'value': '3'
            },
            {
                'label': '四级',
                'value': '4'
            }
        ],
        drioptions: [
            'A1',
            'A2',
            'A3',
            'B1',
            'B2',
            'C1',
            'C2',
            'C3',
            'C4',
            'C5',
            'D',
            'E',
            'F',
            'M',
            'N',
            'P'
        ],
        form: {
            examid: '',
            name: '',
            sex: '',
            photo: '',
            examinerimgurl: '',
            idcard: '',
            mobile: '',
            examinerfpurl: '',
            address: '',
            dripermitted: '',
            teachpermitted: '',
            fstdrilicdate: '',
            drilicence: '',
            hiredate: '',
            employstatus: '0',
            leavedate: '',
            occupationlevel: '',
            occupationno: ''
        },
        cacheForm: {
            employhired: true,
            leavedate: ''
        },
        rules: {
            name: [
                { required: true, message: '姓名不能为空' }
            ],
            photo: [
                { required: true, message: '请选择一张头像吧' }
            ],
            sex: [
                { required: true, message: '性别还没选择哦' }
            ],
            idcard: [
                { required: true, message: '身份证还没填写哦，请仔细核对' }
            ],
            mobile: [
                { required: true, message: '手机是重要的联系方式，补充一下吧' }
            ],
            dripermitted: [
                { required: true, message: '他的准驾车型是什么呢' }
            ],
            teachpermitted: [
                { required: true, message: '他的准教车型是什么呢' }
            ],
            fstdrilicdate: [
                { required: true, message: '他第一次领证是什么日期呢' }
            ],
            drilicence: [
                { required: true, message: '驾驶证号不能为空' }
            ],
            hiredate: [
                { required: true, message: '他第一天上班是什么时候' }
            ],
            employstatus: [
                { required: true, message: '他还在职吗' }
            ]
        },
        list_url: site_url + 'examiner/index',
        add_url: site_url + 'examiner/add',
        edit_url: site_url + 'examiner/edit',
        detail_url: site_url + 'examiner/detail',
        upload_examinerimg_url: site_url + 'upload/handle?type=examinerimg',
        upload_examinerfp_url: site_url + 'upload/handle?type=examinerfp'
    },
    created: function() {
        this.ajaxDetail()
    },
    methods: {
        ajaxDetail: function() {
            var params = {
                id: id
            }
            var options = {
                method: 'GET',
                url: this.detail_url,
                params: params,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            };
            axios(options)
                .then(function(response) {
                    okResponse = _.has(response, 'data') && 'object' === typeof response.data && _.has(response.data, 'code') && _.has(response.data, 'data') && _.has(response.data, 'msg');
                    okResult = response.data.code === 200
                    result = response.data
                    if (okResponse) {
                        if (okResult) {
                            vm.$message({
                                type: 'success',
                                message: result.msg
                            })
                            vm.form = result.data.detail
                            vm.cacheForm.employhired = result.data.detail.employstatus === '0' ? true : false
                        } else {
                            vm.$message({
                                type: 'error',
                                message: result.msg
                            })
                        }
                    } else {
                        vm.$message({
                            type: 'error',
                            message: '获取数据异常，请稍后重试'
                        })
                    }
                })
                .catch(function(error) {
                    vm.$message({
                        type: 'error',
                        message: '网络异常，请稍后重试'
                    })
                })
        },
        ajaxEdit: function(params) {
            var options = {
                method: 'POST',
                url: this.edit_url,
                data: params,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            };
            axios(options)
                .then(function(response) {
                    okResponse = _.has(response, 'data') && 'object' === typeof response.data && _.has(response.data, 'code') && _.has(response.data, 'data') && _.has(response.data, 'msg');
                    okResult = response.data.code === 200
                    result = response.data
                    if (okResponse) {
                        if (okResult) {
                            if (typeof parent.vm.listAjax === 'undefined') {
                                vm.$message({
                                    type: 'success',
                                    message: result.msg
                                })
                                setTimeout(function() {
                                    window.location.href = vm.list_url
                                }, 500)
                            } else {
                                parent.layer.closeAll()
                                parent.vm.$message({
                                    type: 'success',
                                    message: result.msg
                                })
                                parent.vm.listAjax()
                            }
                        } else {
                            vm.$message({
                                type: 'error',
                                message: result.msg
                            })
                        }
                    } else {
                        vm.$message({
                            type: 'error',
                            message: '保存失败，请稍后重试'
                        })
                    }
                })
                .catch(function(error) {
                    vm.$message({
                        type: 'error',
                        message: '网络异常，请稍后重试'
                    })
                })
        },
        handleChangeemploystatus: function(e) {
            if (true === e) {
                vm.form.leavedate = ''
                vm.form.employstatus = '0'
            } else {
                vm.form.employstatus = '1'
                vm.form.leavedate = vm.cacheForm.leavedate
            }
        },
        handleChangeleavedate: function(e) {
            if ('undefined' === typeof e) {
                return
            }
            vm.cacheForm.leavedate = e
        },
        handleCoachimgSuccess: function(res, file) {
            // 获取返回的file_id
            if (_.has(res, 'code') && 200 === res.code) {
                this.form.photo = res.data.file_id;
                this.$message({
                    type: 'success',
                    message: '上传成功'
                })

                // 缩略图展示
                this.form.examinerimgurl = URL.createObjectURL(file.raw);
            } else {
                this.$message({
                    type: 'error',
                    message: '上传出错，请重新上传'
                })
            }
        },
        beforeCoachimgUpload: function(file) {
            const isJPG = file.type === 'image/jpeg';
            const isPNG = file.type === 'image/png';
            const isLt2M = file.size / 1024 / 1024 < 2;

            if (!isJPG && !isPNG) {
                this.$message.error('上传图片只能是 JPG|PNG 格式!');
            }
            if (!isLt2M) {
                this.$message.error('上传图片大小不能超过 2MB!');
            }
            return isJPG && isLt2M;
        },
        handleCoachfpSuccess: function(res, file) {
            // 获取返回的file_id
            if (_.has(res, 'code') && 200 === res.code) {
                this.form.fingerprint = res.data.file_id;
                this.$message({
                    type: 'success',
                    message: '图片上传成功'
                })

                // 缩略图展示
                this.form.examinerfpurl = URL.createObjectURL(file.raw);
            } else {
                this.$message({
                    type: 'error',
                    message: '上传出错，请重新上传'
                })
            }
        },
        beforeCoachfpUpload: function(file) {
            const isJPG = file.type === 'image/jpeg';
            const isPNG = file.type === 'image/png';
            const isLt2M = file.size / 1024 / 1024 < 2;

            if (!isJPG && !isPNG) {
                this.$message.error('上传图片只能是 JPG|PNG 格式!');
            }
            if (!isLt2M) {
                this.$message.error('上传图片大小不能超过 2MB!');
            }
            return isJPG && isLt2M;
        },
        reset: function(form) {
            this.$refs[form].resetFields()
            this.$message({ type: 'success', 'message': '已清空所有输入' })
        },
        submit: function(form) {
            this.$refs[form].validate((valid) => {
                if (valid) {
                    this.ajaxEdit(this.form)
                } else {
                    this.$message({
                        type: 'warning',
                        message: '请您补充填写红色标注的必填项'
                    })
                    return false
                }
            })
        }
    }
})
