var vm = new Vue({
    el: '#app',
    data: {
        labelPosition: 'left',
        activeName: 'first',
        termtypeoptions: [
            {label: '车载计程计时终端', value: '1'},
            {label: '课堂教学计时终端', value: '2'},
            {label: '模拟训练计时终端', value: '3'}
        ],
        form: {
            termtype: '',
            vendor: '',
            model: '',
            imei: '',
            sn: '',
            key: '',
            passwd: ''
        },
        rules: {
            termtype: [
                {required: true, message: '终端类型未选择'}
            ],
            vendor: [
                {required: true, message: '生产厂家未填写'}
            ],
            model: [
                {required: true, message: '终端型号未填写'}
            ],
            imei: [
                {required: true, message: '终端IMEI号未填写'}
            ],
            sn: [
                {required: true, message: '出厂序列号未填写'}
            ]
        },
        list_url: site_url+'device/index',
        add_url: site_url+'device/add'
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
        reset: function (form) {
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
