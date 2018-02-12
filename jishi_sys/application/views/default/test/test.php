<?php $this->load->view('default/header'); ?>
<div id="app">
    <ul>
        <li v-for="item in citylist">{{ item.id }} | {{ item.cityid }} | {{ item.city }} | {{ item.fatcherid }} | {{ item.leter }} | {{ item.is_hot }}</li>
    </ul>

    <div id="pagination" class="page"></div>
</div>

<script>
    var vm = new Vue({
        el:"#app",
        data: {
            notice: '',
            seen: true,
            citylist: [],
            base_url: "<?php echo base_url('home/testajax'); ?>",
            page: "<?php echo $p; ?>",
            pagenum: "<?php echo $pagenum; ?>",
        },
        methods: {
            showlist: function(page) {
                layer.msg('加载中', {
					icon: 16,
					shade: 0.01,
					offset: '0%',
				});
                $.ajax({
					type: 'post',
					url: this.base_url,
					data:{p: page},
					dataType:"json",
					success: function(data) {
						layer.closeAll();
                        if(data.code == 200) {
                            vm.citylist = data.data.list;
                            if(data.data.list.length === 0 ) {
                                vm.notice = '暂无列表信息';
                                vm.seen = false;
                            } else {
                                vm.seen = true;
                            }
                        } else {

                        }
					},
					error: function() {
						layer.closeAll();
						vm.seen = false;
						vm.notice = '网络错误！找不到结果。';
					}
				});
                // axios.get(this.base_url, {
				// 	params: {
				// 		p: page
				// 	}
				// }).then(function(response) {
				// 	layer.closeAll();
				// 	vm.list = response.data.list;
				// 	if(response.data.list.length === 0 ) {
				// 		vm.notice = '暂无列表信息';
				// 		vm.seen = false;
				// 	} else {
				// 		vm.seen = true;
				// 	}
				// })
				// .catch(function(error) {
				// 	layer.closeAll();
				// 	vm.seen = false;
				// 	vm.notice = '网络错误！找不到结果。'+error;
				// });
            }
        }
    });
    laypage({
		cont: document.getElementById('pagination'), //容器。值支持id名、原生dom对象，jquery对象,
		pages: vm.pagenum, //总页数
		skip: true, //是否开启跳页
		skin: '#dd514c',
		groups: 7, //连续显示分页数
		curr: vm.page,
		jump: function(obj) {
			window.history.pushState(null, null, '?p='+obj.curr);
			vm.page = obj.curr;
			vm.showlist(obj.curr);
		}
	});

    
</script>
<?php $this->load->view('default/footer'); ?>
