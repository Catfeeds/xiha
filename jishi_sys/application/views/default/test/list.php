<div id="app">
	<nav class="breadcrumb">
		<i class="Hui-iconfont">&#xe67f;</i> 首页 
		<span class="c-gray en">&gt;</span> 资讯管理 
		<span class="c-gray en">&gt;</span> 资讯列表 
		<a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" v-on:click="showlist(page)" _href="javascript:location.replace(location.href);" title="刷新" >
			<i class="Hui-iconfont">&#xe68f;</i>
		</a>
	</nav>
	<div class="page-container">
		<div class="text-c">
		<span class="select-box inline">
			<select name="" class="select">
				<option value="0">全部分类</option>
				<option value="1">分类一</option>
				<option value="2">分类二</option>
			</select>
			</span> 日期范围：
			<input type="text" id="logmin" class="input-text Wdate" style="width:120px;">  
			-
			<input type="text" id="logmax" class="input-text Wdate" style="width:120px;">
			<input type="text" name="" id="" placeholder=" 资讯名称" style="width:250px" class="input-text" >
			<button name="" id="" class="btn btn-success" type="submit"><i class="Hui-iconfont">&#xe665;</i> 搜资讯</button>
		</div>
		<div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l"><a href="javascript:;" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a> 
		<a class="btn btn-primary radius" data-title="添加资讯" data-href="article-add.html" id="add" @click="addinfo" href="javascript:;"><i class="Hui-iconfont">&#xe600;</i> 添加资讯</a></span> 
		<span class="r">共{{ pagenum }}页 共有数据：<strong>{{ count }}</strong> 条</span> </div>
		<div class="mt-20">
			<table class="table table-border table-bordered table-bg table-hover table-sort">
				<thead>
					<tr class="text-c">
						<th width="25"><input type="checkbox" name="" value=""></th>
						<th width="80">ID</th>
						<th>标题</th>
						<th width="80">分类</th>
						<th width="80">来源</th>
						<th width="120">更新时间</th>
						<th width="75">浏览次数</th>
						<th width="60">发布状态</th>
						<th width="120">操作</th>
					</tr>
				</thead>
				<tbody>
					<template  v-if="seen">
						<tr class="text-c" v-for="item in list">
							<td><input type="checkbox" value="" name=""></td>
							<td>{{ item.id }}</td>
							<td class="text-l">
								<u style="cursor:pointer" v-bind:id="['cityid'+item.id]" class="text-primary" @click="showinfo(item.id)" @mouseover="hoverinfo(item.id)" @mouseleave="closeinfo(item.id)" title="查看">{{ item.city }}</u>
							</td>
							<td>{{ item.cityid }}</td>
							<td>{{ item.acronym }}</td>
							<td>{{ item.fatherid }}</td>
							<td>{{ item.leter }}</td>
							<td class="td-status">
								<span v-if="item.is_hot == 1" class="label label-success radius">热门</span>
								<span v-else class="label label-error radius">正常</span>
							</td>
							<td class="f-14 td-manage">
								<template v-if="item.is_hot == 1">
									<a style="text-decoration:none" href="javascript:;" title="审核">审核</a> 
									<a style="text-decoration:none" class="ml-5" @click="editinfo(item.id)" href="javascript:;" title="编辑"><i class="Hui-iconfont">&#xe6df;</i></a>
									<a style="text-decoration:none" class="ml-5" href="javascript:;" title="删除"><i class="Hui-iconfont">&#xe6e2;</i></a>
								</template>
								<template v-else>
									<a style="text-decoration:none" href="javascript:;" title="下架"><i class="Hui-iconfont">&#xe6de;</i></a> 
									<a style="text-decoration:none" class="ml-5" href="javascript:;" title="编辑"><i class="Hui-iconfont">&#xe6df;</i></a>
									<a style="text-decoration:none" class="ml-5" href="javascript:;" title="删除"><i class="Hui-iconfont">&#xe6e2;</i></a>
								</template>
							</td>
						</tr>
					</template>
					<template v-else>
						<tr class="text-c">
							<td colspan="9">
								{{ notice }}
							</td>
						</tr>
					</template>
				</tbody>
			</table>
		</div>

		<div id="pagination" class="page">
			<!--分页-->
		</div>
	</div>
</div>

<script>
	var vm = new Vue({
		el:"#app",
		data: {
			notice: '',
			seen: true,
			list: [],
			base_url: "<?php echo base_url('home/listajax'); ?>",
			page: "<?php echo $p; ?>",
			pagenum: "<?php echo $pagenum; ?>",
			count: "<?php echo $count; ?>",
		},
		methods: {
			showlist: function(page) {
				layer.msg('加载中', {
					icon: 16,
					shade: 0.01,
					offset: '0%',
					time:100000
				});
				$.ajax({
					type: 'post',
					url: this.base_url,
					data:{p: page},
					dataType:"json",
					success: function(data) {
						layer.closeAll();
                        if(data.code == 200) {
                            vm.list = data.data.list;
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
			},
			showinfo: function(id) {
				layer.closeAll();
				layer.open({
					type: 2,
					area: ['700px', '530px'],
					fixed: false, //不固定
					maxmin: true,
					content: 'test'
				});
			},
			hoverinfo: function(id) {
				var index = layer.tips('我是另外一个tips，只不过我长得跟之前那位稍有些不一样。', '#cityid'+id, {
					tips: [1, '#5a98de'],
					time:0,
					area: '500px'
				});
			},
			closeinfo: function(id) {
				var index = layer.tips();
				layer.close(index); 
			},
			addinfo: function() {
				// var addindex = layer.open({
				// 	type: 2,
				// 	title: document.getElementById('add').getAttribute('data-title'),
				// 	content: 'add'
				// });
				// layer.full(addindex);
				layer.closeAll();
				layer.open({
					title: document.getElementById('add').getAttribute('data-title')
					,offset: 'rb' //具体配置参考：offset参数项
					,anim: -1
					,type: 2
					,area: ['60%','100%']
					,content: 'add'
					,btn: '关闭'
					,btnAlign: 'c' //按钮居中
					,shade: 0.4 //不显示遮罩
					,shadeClose: true //不显示遮罩
					,maxmin: true
					,move: false
					,yes: function(){
						layer.closeAll();
					}
				});
			},
			editinfo: function(id) {
				layer.closeAll();
				layer.open({
					title: document.getElementById('add').getAttribute('data-title')
					,offset: 'lb' //具体配置参考：offset参数项
					,anim: -1
					,type: 2
					,area: ['60%','100%']
					,content: 'add'
					,btn: '关闭'
					,btnAlign: 'c' //按钮居中
					,shade: 0.4 //不显示遮罩
					,shadeClose: true //不显示遮罩
					,maxmin: true
					,move: false
					,yes: function(){
						layer.closeAll();
					}
				});
			}
		}
	});
	// 分页
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