<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">
		<div class="gx-iframe-content">
            <el-card class="box-card" style="box-shadow: none;">
                <div slot="header" class="clearfix">
                    <span style="line-height: 10px;">订单信息</span>
                </div>
                <div class="text item">
                    <el-form label-position="left" inline class="demo-table-expand">
                        <el-form-item label="订单号：">
                            <span>{{ list.order_no }}</span>
                        </el-form-item>
                        <el-form-item label="订单状态：">
                            <el-tag type="primary" v-if="list.pay_type == '1'">支付宝</el-tag>
                            <el-tag type="gray" v-else-if="list.pay_type == '2'">线下</el-tag>
                            <el-tag type="success" v-else-if="list.pay_type == '3'">微信</el-tag>
                            <el-tag type="danger" v-else-if="list.pay_type == '4'">银联</el-tag>
                            <el-tag type="gray" v-else>未知</el-tag> /
                            <el-tag type="success" v-if="list.order_status == '1'">已支付</el-tag>
                            <el-tag type="warning" v-else-if="list.order_status == '2'">退款中</el-tag>
                            <el-tag type="danger" v-else-if="list.order_status == '3'">已取消</el-tag>
                            <el-tag type="warning" v-else-if="list.order_status == '4'">未付款</el-tag>
                            <el-tag type="danger" v-else-if="list.order_status == '101'">已删除</el-tag>
                            <el-tag type="gray" v-else>未知</el-tag>
                        </el-form-item>
                        <el-form-item label="用户信息：">
                            <span>{{ list.user_name }}</span> / <el-tag type="danger">{{ list.user_phone }}</el-tag>
                        </el-form-item>
                        <el-form-item label="所属驾校：">
                            <span>{{ user_info.school_name }}</span>
                        </el-form-item>
                        <el-form-item label="预约时间：" style="color: red">
                            <span>{{ list.appoint_time_date }}</span><br/>
                            <span >{{ list.appoint_time }}</span><br/>
                        </el-form-item>
                        <el-form-item label="预约时长：">
                            <span>{{ list.service_time }} 小时</span>
                        </el-form-item>

                        <el-form-item label="牌照：">
                            <span>{{ list.license_name }}</span>
                        </el-form-item>
                        <el-form-item label="科目：">
                            <span>{{ list.lesson_name }}</span>
                        </el-form-item>
                        <el-form-item label="下单时间：">
                            <span>{{ list.dt_order_time }}</span>
                        </el-form-item>
                        <el-form-item label="支付时间：">
                            <span v-if="list.order_status == '1' || list.order_status == '2'"><el-tag type="success" >{{ list.zhifu_time }}</el-tag></span>
                            <span v-else>--</span>
                        </el-form-item>
                        <el-form-item label="支付金额：">
                            <span>{{ list.price }} 元</span>
                        </el-form-item>
                        <el-form-item label="取消者：" v-if="list.order_status == '3'">
                            <span v-if="list.cancel_type == '0'"><el-tag type="success" >学员</el-tag></span>
                            <span v-if="list.cancel_type == '1'"><el-tag type="success" >教练</el-tag></span>
                            <span v-if="list.cancel_type == '2'"><el-tag type="success" >驾校</el-tag></span>
                            <span v-else>--</span>
                        </el-form-item>
                        <el-form-item label="取消时间：" v-if="list.order_status == '3'">
                            <span>{{ list.cancel_reason }}</span>
                        </el-form-item>
                        <el-form-item label="取消原因：" v-if="list.order_status == '3'">
                            <span>{{ list.cancel_time }}</span>
                        </el-form-item>
                    </el-form>
                </div>
            </el-card>
            <el-card class="box-card" style="margin-top: 20px; box-shadow: none;">
                <div slot="header" class="clearfix">
                    <span style="line-height: 10px;">教练信息</span>
                </div>
                <div class="text item">
                    <el-form label-position="left" inline class="demo-table-expand">
                        <el-form-item label="教练名称：">
                            <span>{{ list.coach_name }}</span>
                        </el-form-item>
                        <el-form-item label="联系电话：">
                            <span>{{ list.coach_phone }}</span>
                        </el-form-item>
                        <el-form-item label="教龄：">
                            <span>{{ coach_info.age }}</span>
                        </el-form-item>
                        <el-form-item label="性别：">
                            <el-tag type="danger" v-if="coach_info.sex == 1">女</el-tag>
                            <el-tag type="success" v-if="coach_info.sex == 0">男</el-tag>
                        </el-form-item>
                        <el-form-item label="所属驾校：">
                            <span>{{ coach_info.school_name }}</span>
                        </el-form-item>
                        <el-form-item label="教练住址：">
                            <span>{{ coach_info.coach_address }}</span>
                        </el-form-item>
                        <el-form-item label="所教牌照：">
                            <span>{{ coach_info.license_name }}</span>
                        </el-form-item>
                        <el-form-item label="所教科目：">
                            <span>{{ coach_info.lesson_name }}</span>
                        </el-form-item>
                        <el-form-item label="科二通过率：">
                            <span>{{ coach_info.lesson2_pass_rate }}%</span>
                        </el-form-item>
                        <el-form-item label="科三通过率：">
                            <span>{{ coach_info.lesson3_pass_rate }}%</span>
                        </el-form-item>
                        <el-form-item label="教练星级：">
                            <el-rate style="margin-top: 5px;"
                                v-model="coach_info.coach_star"
                                disabled
                                text-color="#ff9900"
                                text-template="{value}">
                            </el-rate>
                        </el-form-item>
                    </el-form>
                </div>
            </el-card>
            <el-card class="box-card" style="margin-top: 20px; box-shadow: none;">
                <div slot="header" class="clearfix">
                    <span style="line-height: 10px;">学员信息</span>
                </div>
                <div class="text item">
                    <el-form label-position="left" inline class="demo-table-expand">
                        <el-form-item label="姓名：">
                            <span>{{ list.user_name }}</span>
                        </el-form-item>
                        <el-form-item label="联系电话：">
                            <span>{{ list.user_phone }}</span>
                        </el-form-item>
                        <el-form-item label="身份证：">
                            <span>{{ user_info.identity_id }}</span>
                        </el-form-item>
                        <el-form-item label="年龄：">
                            <span>{{ user_info.age }}</span>
                        </el-form-item>
                        <el-form-item label="性别：">
                            <el-tag type="success" v-if="user_info.sex == 1">男</el-tag>
                            <el-tag type="danger" v-if="user_info.sex == 2">女</el-tag>
                        </el-form-item>
                        <el-form-item label="所属驾校：">
                            <span>{{ user_info.school_name }}</span>
                        </el-form-item>
                        <el-form-item label="所学科目：">
                            <span>{{ user_info.lesson_name }}</span>
                        </el-form-item>
                        <el-form-item label="所学牌照：">
                            <span>{{ user_info.license_name }}</span>
                        </el-form-item>
                        <el-form-item label="考证次数：">
                            <span>{{ user_info.license_num }} 次</span>
                        </el-form-item>
                        <el-form-item label="住址：">
                            <span>{{ user_info.address }}</span>
                        </el-form-item>
                    </el-form>
                </div>
            </el-card>

		</div>
	</div>
</div>
<script>
	var school_id = "<?php echo $school_id; ?>";
	var id = "<?php echo $id; ?>";
	var vm = new Vue({
		el: '#app',
		data: {
			refreshstatus: false,
			fullscreenLoading: true,
			dialogTableVisible: false,
			list: [],
            user_info: [],
            coach_info: [],
			multipleSelection: [],
            keywords: '',
            deal_type: 1,
            status: 1,
            currentDate: new Date(),
			list_url: "<?php echo base_url('order/previewAjax'); ?>",
			activeName: "first",
		},
		created: function() {
			var filters = {'id': id};
			this.listAjax(filters);
		},
		methods: {
			handleClick: function(tab, event) {
				switch (this.activeName) {
					case 'first':
						this.order_status = 'paid';
						break;
					case 'second':
						this.order_status = 'unpaid';
						break;
					case 'third':
						this.order_status = 'cancel';
						break;
					case 'fourth':
						this.order_status = 'refunding';
						break;
					case 'fifth':
						this.order_status = 'deleted';
						break;
					case 'sixth':
						this.order_status = 'completed';
						break;
					default:
						this.order_status = 'paid';
						break;
				}
			    var filters = {'id': id};
				this.listAjax(filters);
			},
			listAjax: function(param) {
				$.ajax({
					type: 'post',
					url: this.list_url,
					data: param,
					dataType:"json",
					async: true,
					success: function(data) {
						vm.fullscreenLoading = false;			
						vm.refreshstatus = false;
						if(data.code == 200) {
                            vm.list = data.data;
                            vm.user_info = data.data.user_info;
                            vm.coach_info = data.data.coach_info;
                        }
					},
					error: function() {
						vm.fullscreenLoading = false;
						vm.refreshstatus = false;
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				});
			},
			
			messageNotice: function(type, msg) {
				this.$message({
					type: type,
					message: msg
				});
			},
			showLayer: function(e, width, offset, content) {
				layer.closeAll();
				layer.open({
					title: e.currentTarget.getAttribute('data-title')
					,offset: offset //具体配置参考：offset参数项
					,anim: -1
					,type: 2
					,area: [width ,'100%']
					,content: content
					,shade: 0.4 //不显示遮罩
					,shadeClose: false //不显示遮罩
					,maxmin: true
					,move: false
					,yes: function(){
						layer.closeAll();
					}
				});
			},
			
		}
	})
</script>