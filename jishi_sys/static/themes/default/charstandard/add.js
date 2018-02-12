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
        trainingmodeoptions: [
            {label: '定时培训', value: '1'},
            {label: '预约培训', value: '2'},
            {label: '其他', value: '9'}
        ],
        subjectoptions: [
            {label: '第一部分集中教学', value: '1'},
            {label: '第一部分网络教学', value: '2'},
            {label: '第四部分集中教学', value: '3'},
            {label: '第四部分网络教学', value: '4'},
            {label: '模拟器教学',       value: '5'},
            {label: '第二部分普通教学', value: '6'},
            {label: '第二部分智能教学', value: '7'},
            {label: '第三部分普通教学', value: '8'},
            {label: '第三部分智能教学', value: '9'}
        ],
        trainingtimeoptions: [
            {label: '普通时段', value: '1'},
            {label: '高峰时段', value: '2'},
            {label: '节假日时段', value: '3'}
        ],
        chargemodeoptions: [
            {label: '一次性收费', value: '1'},
            {label: '计时收费', value: '2'},
            {label: '其他', value: '9'}
        ],
        paymodeoptions: [
            {label: '先学后付', value: '1'},
            {label: '先付后学', value: '2'},
            {label: '其他', value: '9'}
        ],
        form: {
            // 必填
            seq: '',
            vehicletype: '',
            _vehicletype: '',
            price: '',
            classcurr: '',
            uptime: '',
            // 选填
            trainingmode: '',
            subject: '',
            trainingtime: '',
            chargemode: '',
            paymode: '',
            service: ''
        },
        rules: {
            vehicletype: [
                {required: true, message: '培训类型未选择，可以多选'}
            ],
            price: [
                {required: true, message: '价格未填写，计时培训为单价，一次性收费为总额'}
            ],
            classcurr: [
                {required: true, message: '班型名称未填写'}
            ],
            uptime: [
                {required: true, message: '更新时间未设置'}
            ]
        },
        list_url: site_url+'charstandard/index',
        add_url: site_url+'charstandard/add',
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
        },
        handleChangeVehicletype: function (e) {
            this.form.vehicletype = _.join(e, ',')
        }
    }
})
