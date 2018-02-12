var vm = new Vue({
    el: '#app',
    data: {
        list_url: site_url + 'trainingcar/list',
        del_url: site_url + 'trainingcar/delete',
        add_url: site_url + 'trainingcar/add',
        edit_url: site_url + 'trainingcar/edit',
        refreshstatus: false,
        totalNum: 0,
        currentPage: 1,
        pageSize: 10,
        pageSizeOptions: [10, 25, 50, 100],
        active_menu: 'trainingcar',
        list: []
    },
    created: function() {
        this.listAjax()
    },
    methods: {
        listAjax: function() {
            this.loading = true
            var params = {
                p: this.currentPage,
                s: this.pageSize
            }
            var options = {
                method: 'GET',
                url: this.list_url,
                params: params,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }
            axios(options)
                .then(function(response) {
                    vm.loading = false
                    okResponse = typeof response == 'object' && _.has(response, 'data') && typeof response.data == 'object' && _.has(response.data, 'code') && _.has(response.data, 'msg') && _.has(response.data, 'data')
                    result = response.data
                    if (okResponse) {
                        if (200 === result.code) {
                            vm.$message({
                                type: 'success',
                                message: '刷新成功'
                            })
                            if (_.has(result.data, 'list')) {
                                vm.list = result.data.list
                            }
                            if (_.has(result.data, 'total')) {
                                vm.totalNum = result.data.total
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
                            message: '网络异常，稍后重试'
                        })
                    }
                })
                .catch(function(error) {
                    vm.loading = false
                    vm.$message({
                        type: 'error',
                        message: '网络异常，稍后重试'
                    })
                })
        },
        handleAdd: function(e) {
            this.showLayer(e, '100%', 'rb', this.add_url)
        },
        handleEdit: function(e, id) {
            this.showLayer(e, '100%', 'rb', this.edit_url + '?id=' + id)
        },
        handleDel: function(e, id) {
            this.$confirm('你确定要删除吗？', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                this.delAjax(id)
            }).catch(() => {
                this.$message({
                    type: 'info',
                    message: '您取消了操作',
                    duration: 700
                })
            })
        },
        delAjax: function(id) {
            var params = {
                carid: id
            }
            var options = {
                method: 'POST',
                url: this.del_url,
                data: params,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }
            axios(options)
                .then(function(response) {
                    okResponse = typeof response == 'object' && _.has(response, 'data') && typeof response.data == 'object' && _.has(response.data, 'code') && _.has(response.data, 'msg') && _.has(response.data, 'data')
                    result = response.data
                    if (okResponse) {
                        if (200 === result.code) {
                            vm.$message({
                                type: 'success',
                                message: '删除成功'
                            })
                            vm.listAjax()
                        } else {
                            vm.$message({
                                type: 'error',
                                message: result.msg
                            })
                        }
                    } else {
                        vm.$message({
                            type: 'error',
                            message: '网络异常，稍后重试'
                        })
                    }
                })
                .catch(function(error) {
                    vm.$message({
                        type: 'error',
                        message: '网络异常，稍后重试'
                    })
                })
        },
        handleRefresh: function(e) {
            vm.listAjax()
        },
        handleSizeChange: function(e) {
            vm.pageSize = e
            vm.listAjax()
        },
        handleCurrentChange: function(e) {
            vm.currentPage = e
            vm.listAjax()
        },
        showLayer: function(e, width, offset, content) {
            layer.closeAll();
            var index = layer.open({
                title: e.currentTarget.getAttribute('data-title'),
                offset: offset,
                anim: -1,
                type: 2,
                area: [width, '100%'],
                content: content,
                shade: 0.4,
                shadeClose: false,
                maxmin: true,
                move: false,
                yes: function() {
                    layer.closeAll();
                }
            });
        },
        handleMenuSelect: function (index, path) {
            window.location.href = site_url+index
        }
    }
})
