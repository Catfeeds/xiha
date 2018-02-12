var vm = new Vue({
    el: '#app',
    data: {
        labelPosition: 'left',
        activeName: 'first',
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
        cardtypeoptions: [
            {label: '身份证', value: '1'},
            {label: '护照', value: '2'},
            {label: '军官证', value: '3'},
            {label: '其他', value: '4'},
        ],
        busitypeoptions: [
            {label: '初领', value: '0'},
            {label: '增领', value: '1'},
            {label: '其他', value: '9'},
        ],
        form: {
            stuid: '',
            inscode: '',
            cardtype: '',
            idcard: '',
            nationality: '',
            name: '',
            sex: '',
            address: '',
            photo: '',
            phone: '',
            fingerprint: '',
            traintype: '',
            busitype: '',
            applydate: '',
            perdritype: '',
            drilicnum: '',
            fstdrilicdate: ''
        },
        rules: {
            name: [
                {required: true, message: '姓名不能为空'}
            ],
            photo: [
                {required: true, message: '请选择一张头像吧'}
            ],
            sex: [
                {required: true, message: '性别未选择'}
            ],
            traintype: [
                {required: true, message: '培训车型未选择'}
            ],
            busitype: [
                {required: true, message: '业务类型未选择'}
            ],
            cardtype: [
                {required: true, message: '证件类型未选择'}
            ],
            idcard: [
                {required: true, message: '证件号还没填写哦，请仔细核对'}
            ],
            phone: [
                {required: true, message: '手机是重要的联系方式，补充一下吧'}
            ],
            nationality: [
                {required: true, message: '请填写国籍信息'}
            ],
            applydate: [
                {required: true, message: '请填写报名时间'}
            ]
        },
        stuimgurl: '',
        stufpurl: '',
        list_url: site_url+'student/index',
        add_url: site_url+'student/add',
        upload_stuimg_url: site_url+'upload/handle?type=stuimg',
        upload_stufp_url: site_url+'upload/handle?type=stufp'
    },
    methods: {
        ajaxAdd: function (params) {
            var options = {
                method: 'POST',
                url: this.add_url,
                data: params,
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            };
            axios(options)
            .then(function (response) {
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
                            setTimeout(function () {
                                window.location.href=vm.list_url
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
            .catch(function (error) {
                console.log('error happen')
                console.log(error)
                vm.$message({
                    type: 'error',
                    message: '网络异常，请稍后重试'
                })
            })
        },
        handleChangeemploystatus: function (e) {
            if (true === e) {
                vm.form.leavedate = ''
                vm.form.employstatus = '0'
            } else {
                vm.form.employstatus = '1'
                vm.form.leavedate = vm.cacheForm.leavedate
            }
        },
        handleChangeleavedate: function (e) {
            if ('undefined' === typeof e) {
                return;
            }
            vm.cacheForm.leavedate = e
        },
        handlestuimgSuccess: function (res, file) {
            // 获取返回的file_id
            if (_.has(res, 'code') && 200 === res.code) {
                this.form.photo = res.data.file_id;
                this.$message({
                    type: 'success',
                    message: '上传成功'
                })

                // 缩略图展示
                this.stuimgurl = URL.createObjectURL(file.raw);
            } else {
                this.$message({
                    type: 'error',
                    message: res.msg
                })
            }
        },
        beforestuimgUpload: function (file) {
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
        handlestufpSuccess: function (res, file) {
            // 获取返回的file_id
            if (_.has(res, 'code') && 200 === res.code) {
                this.form.fingerprint = res.data.file_id;
                this.$message({
                    type: 'success',
                    message: '图片上传成功'
                })

                // 缩略图展示
                this.stufpurl = URL.createObjectURL(file.raw);
            } else {
                this.$message({
                    type: 'error',
                    message: res.msg
                })
            }
        },
        beforestufpUpload: function (file) {
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
        reset: function (form) {
            this.stuimgurl = ''
            this.stufpurl = ''
            this.$refs[form].resetFields()
            this.$message({type: 'success', 'message': '已清空所有输入'})
        },
        submit: function (form) {
            this.$refs[form].validate((valid) => {
                if (valid) {
                    this.ajaxAdd(this.form)
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
