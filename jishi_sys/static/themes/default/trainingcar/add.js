var vm = new Vue({
    el: '#app',
    data: {
        labelPosition: 'left',
        activeName: 'first',
        colorOptions: [{
                'label': '蓝色',
                'value': '1'
            },
            {
                'label': '黄色',
                'value': '2'
            },
            {
                'label': '黑色',
                'value': '3'
            },
            {
                'label': '白色',
                'value': '4'
            },
            {
                'label': '绿色',
                'value': '5'
            },
            {
                'label': '其他',
                'value': '9'
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
            licnum: '',
            platecolor: '',
            perdritype: '',
            photo: '',
            vehimgurl: '',
            buydate: '',
            manufacture: '',
            brand: '',
            model: '',
            franum: '',
            engnum: ''
        },
        cacheForm: {},
        rules: {
            licnum: [{ required: true, message: '请填写车牌号，如皖A12345' }],
            platecolor: [{ required: true, message: '请选择车牌颜色，如蓝色' }],
            perdritype: [{ required: true, message: '请选择培训车型，如C1' }],
            manufacture: [{ required: true, message: '请填写生产厂家，如上汽大众' }],
            brand: [{ required: true, message: '请填写车辆品牌' }]
        },
        vehimgurl: '',
        list_url: site_url + 'trainingcar/index',
        add_url: site_url + 'trainingcar/add',
        upload_vehimg_url: site_url + 'upload/handle?type=vehimg'
    },
    methods: {
        ajaxAdd: function(params) {
            var options = {
                method: 'POST',
                url: this.add_url,
                data: params,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
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
        handleVehimgSuccess: function(res, file) {
            // 获取返回的file_id
            if (_.has(res, 'code') && 200 === res.code) {
                this.form.photo = res.data.file_id;
                this.$message({
                    type: 'success',
                    message: '上传成功'
                })

                // 缩略图展示
                this.vehimgurl = URL.createObjectURL(file.raw);
            } else {
                this.$message({
                    type: 'error',
                    message: '上传出错，请重新上传'
                    message: res.msg
                })
            }
        },
        beforeVehimgUpload: function(file) {
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
            this.vehimgurl = ''
            this.$refs[form].resetFields()
            this.$message({
                type: 'success',
                message: '已清空所有输入'
            })
        },
        submit: function(form) {
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
