<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">
		<div class="gx-iframe-content">
			<!--搜索-->
			<div class="gx-search">
				<el-form :inline="true" :model="search" class="demo-form-inline">
					<el-form-item label="支付方式">
						<el-select v-model="search.pay_type" placeholder="--不限方式--">
						<el-option label="支付宝" value="1"></el-option>
						<el-option label="微信" value="3"></el-option>
						<el-option label="银联" value="4"></el-option>
						</el-select>
					</el-form-item>
					<el-form-item label="关键词">
						<el-input v-if="school_id == ''" style="width: 400px;" v-model="search.keywords" placeholder="订单ID|驾校名称|学员姓名|学员手机|订单号|交易号"></el-input>
						<el-input v-if="school_id != ''" style="width: 400px;" v-model="search.keywords" placeholder="订单ID|学员姓名|学员手机|订单号|交易号"></el-input>
					</el-form-item>
					<el-form-item>
						<el-button type="primary" @click="onSubmit">查询</el-button>
					</el-form-item>
				</el-form>
			</div>
			<div class="gx-iframe-operation" style="margin-bottom: 20px; border-radius: 4px;">
				<el-button type="success" style="margin-left:10px;" @click.active.prevent="handleAdd($event)" id="add" data-title="添加订单"><i class="el-icon-plus"></i> 添加订单</el-button>
				<el-button type="success" type="small" style="float:right; line-height: 38px;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
			</div>
			<el-tabs v-model="payActive" type="card" type="border-card" @tab-click="handlePayTypeClick">
				<el-tab-pane label="线上支付" name="first">
					<el-tabs v-model="activeName" @tab-click="handleClick">
						<el-tab-pane label="全部" name="zero">
							<el-table :data="all_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
								<!--<el-table-column type="selection"width="55"></el-table-column>-->
								<el-table-column type="expand" label="展开">
									<template scope="props">
										<el-form label-position="left" inline class="demo-table-expand">
											<el-form-item label="订单号：">
												<span>{{ props.row.so_order_no }}</span>
											</el-form-item>
											<el-form-item label="交易号：">
												<span>{{ props.row.s_zhifu_dm }}</span>
											</el-form-item>
											<el-form-item label="班制名称：" v-if="props.row.shifts_info">
												<span>{{ props.row.shifts_info.sh_title }}</span>
											</el-form-item>
											<el-form-item label="班制牌照：" v-if="props.row.shifts_info">
												<span>{{ props.row.shifts_info.sh_license_name }}</span>
											</el-form-item>
											<el-form-item label="支付时间：" >
												<span>{{ props.row.dt_zhifu_time }}</span>
											</el-form-item>
											<el-form-item label="下单时间：" >
												<span>{{ props.row.addtime }}</span>
											</el-form-item>
											<el-form-item label="用户身份证：" >
												<span>{{ props.row.so_user_identity_id }}</span>
											</el-form-item>
											<el-form-item label="用户报考证：" >
												<span>{{ props.row.so_licence }}</span>
											</el-form-item>
											<el-form-item label="优惠预约计时：" >
												<span style="color: red" v-if="props.row.free_study_hour == -1">不限</span>
												<span v-else>{{ props.row.free_study_hour }} 小时</span>
											</el-form-item>
											<el-form-item label="班制所属：" >
												<span v-if="props.row.coach_name != '--'">教练（{{ props.row.coach_name }}）</span>
												<span v-else>驾校（{{ props.row.school_name }}）</span>
											</el-form-item>
										</el-form>
									</template>
								</el-table-column>
								<el-table-column prop="id" label="ID" sortable width="80"></el-table-column> 
								<el-table-column prop="school_name" v-if="school_id == ''" label="驾校名称" width="150" show-overflow-tooltip></el-table-column> 
								<el-table-column prop="so_username" label="用户名 | 手机号" min-width="200" show-overflow-tooltip>
									<template scope="scope">
										<span>{{ scope.row.so_username }}</span> | 
										<el-tag type="danger" close-transition>{{ scope.row.so_phone }}</el-tag>
									</template>
								</el-table-column> 
								<el-table-column label="价格(元)" min-width="260">
									<el-table-column prop="lesson_name" label="原始价 | 最终价 | 实际支付" width="220" show-overflow-tooltip>
										<template scope="scope">
											<span style="color: #999">{{scope.row.so_original_price}}</span> | 
											<span style="color: #13ce66">{{ scope.row.so_final_price }}</span> | 
											<el-tag type="danger" v-if="scope.row.so_total_price == '0.00'" close-transition>{{scope.row.so_final_price}}</el-tag>
											<el-tag type="danger" v-else close-transition>{{scope.row.so_total_price}}</el-tag> 
										</template>
									</el-table-column>
									<el-table-column prop="coupon_name" label="优惠价" width="90" show-overflow-tooltip>
										<template scope="scope">
											<span>{{ scope.row.coupon_value }}</span>
										</template>
									</el-table-column>
								</el-table-column>
								<el-table-column prop="so_order_no" label="订单号" min-width="150" show-overflow-tooltip></el-table-column>
								<el-table-column prop="so_shifts_id" label="班制名称" min-width="220" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" close-transition v-if="scope.row.shifts_name">{{ scope.row.shifts_name }}</el-tag> | 
										{{ scope.row.shifts_license_name }}
									</template>
								</el-table-column>
								<el-table-column prop="so_order_status" label="订单状态" min-width="100" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" v-if="scope.row.so_order_status == '1'">已付款</el-tag>
										<el-tag type="warning" v-else-if="scope.row.so_order_status == '2'">退款中</el-tag>
										<el-tag type="danger" v-else-if="scope.row.so_order_status == '3'">已取消</el-tag>
										<el-tag type="warning" v-else-if="scope.row.so_order_status == '4'">未付款</el-tag>
										<el-tag type="warning" v-else-if="scope.row.so_order_status == '1007'">退款中</el-tag>
										<el-tag type="warning" v-else-if="scope.row.so_order_status == '101'">已删除</el-tag>
										<el-tag type="gray" v-else>未知</el-tag>
									</template>
								</el-table-column>
								<el-table-column prop="so_pay_type" label="支付方式" min-width="100" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" v-if="scope.row.so_pay_type == '1'">支付宝</el-tag>
										<el-tag type="gray" v-else-if="scope.row.so_pay_type == '2'">线下</el-tag>
										<el-tag type="success" v-else-if="scope.row.so_pay_type == '3'">微信</el-tag>
										<el-tag type="danger" v-else-if="scope.row.so_pay_type == '4'">银联</el-tag>
										<el-tag type="gray" v-else>未知</el-tag>
									</template>
								</el-table-column>
								<!-- <el-table-column prop="coach_name" label="教练名称" min-width="150" show-overflow-tooltip></el-table-column> -->
								<el-table-column prop="s_zhifu_dm" label="交易号" min-width="150" show-overflow-tooltip></el-table-column>
								<el-table-column prop="addtime" label="下单时间" min-width="150" show-overflow-tooltip></el-table-column>
								<el-table-column label="设置订单状态" fixed="right" width="150" style="margin: 0 auto">
									<template scope="scope">
										<el-dropdown trigger="click">
											<el-button class="el-dropdown-link" size="small" type="primary">设置<i class="el-icon-caret-bottom el-icon--right"></i></el-button>
											<el-dropdown-menu slot="dropdown" style="margin-right: -20px">
												<a @click="setOrderStatus(scope.row.id, 1)">
													<el-dropdown-item >已付款</el-dropdown-item>
												</a>
												<!-- <a @click="setOrderStatus(scope.row.id, 1011)">
													<el-dropdown-item >已完成</el-dropdown-item>
												</a> -->
												<a @click="setOrderStatus(scope.row.id, 3)">
													<el-dropdown-item >已取消</el-dropdown-item>
												</a>
												<a @click="setOrderStatus(scope.row.id, 4)">
													<el-dropdown-item >未付款</el-dropdown-item>
												</a>
												<a @click="setOrderStatus(scope.row.id, 2)">
													<el-dropdown-item >退款中</el-dropdown-item>
												</a>
												<a @click="setOrderStatus(scope.row.id, 1007)">
													<el-dropdown-item >已退款</el-dropdown-item>
												</a>
												<!-- <a @click="setOrderStatus(scope.row.id, 101)">
													<el-dropdown-item >已删除</el-dropdown-item>
												</a> -->
											</el-dropdown-menu>
										</el-dropdown>
									</template>
								</el-table-column>
									<el-table-column label="操作" fixed="right" width="150">
									<template scope="scope">
										<a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.id, scope.row.so_order_status)"><i class="el-icon-delete"></i></a>
										<a title="编辑" style="margin-left:8px;" data-title="编辑订单" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
									</template>
								</el-table-column>
							</el-table>
						</el-tab-pane>

						<el-tab-pane label="已付款" name="first">
							<el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
								<!--<el-table-column type="selection"width="55"></el-table-column>-->
								<el-table-column type="expand" label="展开">
									<template scope="props">
										<el-form label-position="left" inline class="demo-table-expand">
											<el-form-item label="订单号：">
												<span>{{ props.row.so_order_no }}</span>
											</el-form-item>
											<el-form-item label="交易号：">
												<span>{{ props.row.s_zhifu_dm }}</span>
											</el-form-item>
											<el-form-item label="班制名称：" v-if="props.row.shifts_info">
												<span>{{ props.row.shifts_info.sh_title }}</span>
											</el-form-item>
											<el-form-item label="班制牌照：" v-if="props.row.shifts_info">
												<span>{{ props.row.shifts_info.sh_license_name }}</span>
											</el-form-item>
											<el-form-item label="支付时间：" >
												<span>{{ props.row.dt_zhifu_time }}</span>
											</el-form-item>
											<el-form-item label="下单时间：" >
												<span>{{ props.row.addtime }}</span>
											</el-form-item>
											<el-form-item label="用户身份证：" >
												<span>{{ props.row.so_user_identity_id }}</span>
											</el-form-item>
											<el-form-item label="用户报考证：" >
												<span>{{ props.row.so_licence }}</span>
											</el-form-item>
											<el-form-item label="优惠预约计时：" >
												<span style="color: red" v-if="props.row.free_study_hour == -1">不限</span>
												<span v-else>{{ props.row.free_study_hour }} 小时</span>
											</el-form-item>
											<el-form-item label="班制所属：" >
												<span v-if="props.row.coach_name != '--'">教练（{{ props.row.coach_name }}）</span>
												<span v-else>驾校（{{ props.row.school_name }}）</span>
											</el-form-item>
										</el-form>
									</template>
								</el-table-column>
								<el-table-column prop="id" label="ID" sortable width="80"></el-table-column> 
								<el-table-column prop="school_name" v-if="school_id == ''" label="驾校名称" width="150" show-overflow-tooltip></el-table-column> 
								<el-table-column prop="so_username" label="用户名 | 手机号" min-width="200" show-overflow-tooltip>
									<template scope="scope">
										<span>{{ scope.row.so_username }}</span> | 
										<el-tag type="danger" close-transition>{{ scope.row.so_phone }}</el-tag>
									</template>
								</el-table-column> 
								<el-table-column label="价格(元)" min-width="260">
									<el-table-column prop="lesson_name" label="原始价 | 最终价 | 实际支付" width="220" show-overflow-tooltip>
										<template scope="scope">
											<span style="color: #999">{{scope.row.so_original_price}}</span> | 
											<span style="color: #13ce66">{{ scope.row.so_final_price }}</span> | 
											<el-tag type="danger" v-if="scope.row.so_total_price == '0.00'" close-transition>{{scope.row.so_final_price}}</el-tag>
											<el-tag type="danger" v-else close-transition>{{scope.row.so_total_price}}</el-tag> 
										</template>
									</el-table-column>
									<el-table-column prop="coupon_name" label="优惠价" width="90" show-overflow-tooltip>
										<template scope="scope">
											<span>{{ scope.row.coupon_value }}</span>
										</template>
									</el-table-column>
								</el-table-column>
								<el-table-column prop="so_order_no" label="订单号" min-width="150" show-overflow-tooltip></el-table-column>
								<el-table-column prop="so_shifts_id" label="班制名称" min-width="220" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" close-transition v-if="scope.row.shifts_name">{{ scope.row.shifts_name }}</el-tag> | 
										{{ scope.row.shifts_license_name }}
									</template>
								</el-table-column>
								<el-table-column prop="so_pay_type" label="支付方式" min-width="100" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" v-if="scope.row.so_pay_type == '1'">支付宝</el-tag>
										<el-tag type="gray" v-else-if="scope.row.so_pay_type == '2'">线下</el-tag>
										<el-tag type="success" v-else-if="scope.row.so_pay_type == '3'">微信</el-tag>
										<el-tag type="danger" v-else-if="scope.row.so_pay_type == '4'">银联</el-tag>
										<el-tag type="gray" v-else>未知</el-tag>
									</template>
								</el-table-column>
								<!-- <el-table-column prop="coach_name" label="教练名称" min-width="150" show-overflow-tooltip></el-table-column> -->
								<el-table-column prop="s_zhifu_dm" label="交易号" min-width="150" show-overflow-tooltip></el-table-column>
								<el-table-column prop="addtime" label="下单时间" min-width="150" show-overflow-tooltip></el-table-column>
								<el-table-column label="设置订单状态" fixed="right" width="150" style="margin: 0 auto">
									<template scope="scope">
										<el-dropdown trigger="click">
											<el-button class="el-dropdown-link" size="small" type="primary">设置<i class="el-icon-caret-bottom el-icon--right"></i></el-button>
											<el-dropdown-menu slot="dropdown" style="margin-right: -20px">
												<a @click="setOrderStatus(scope.row.id, 1)">
													<el-dropdown-item >已付款</el-dropdown-item>
												</a>
												<!-- <a @click="setOrderStatus(scope.row.id, 1011)">
													<el-dropdown-item >已完成</el-dropdown-item>
												</a> -->
												<a @click="setOrderStatus(scope.row.id, 3)">
													<el-dropdown-item >已取消</el-dropdown-item>
												</a>
												<a @click="setOrderStatus(scope.row.id, 4)">
													<el-dropdown-item >未付款</el-dropdown-item>
												</a>
												<a @click="setOrderStatus(scope.row.id, 2)">
													<el-dropdown-item >退款中</el-dropdown-item>
												</a>
												<a @click="setOrderStatus(scope.row.id, 1007)">
													<el-dropdown-item >已退款</el-dropdown-item>
												</a>
												<!-- <a @click="setOrderStatus(scope.row.id, 101)">
													<el-dropdown-item >已删除</el-dropdown-item>
												</a> -->
											</el-dropdown-menu>
										</el-dropdown>
									</template>
								</el-table-column>
									<el-table-column label="操作" fixed="right" width="150">
									<template scope="scope">
										<a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.id, scope.row.so_order_status)"><i class="el-icon-delete"></i></a>
										<a title="编辑" style="margin-left:8px;" data-title="编辑订单" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
									</template>
								</el-table-column>
							</el-table>
						</el-tab-pane>

						<el-tab-pane label="未付款" name="second">
							<el-table :data="unpaid_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
								<!--<el-table-column type="selection"width="55"></el-table-column>-->
								<el-table-column type="expand" label="展开">
									<template scope="props">
										<el-form label-position="left" inline class="demo-table-expand">
											<el-form-item label="订单号：">
												<span>{{ props.row.so_order_no }}</span>
											</el-form-item>
											<el-form-item label="交易号：">
												<span>{{ props.row.s_zhifu_dm }}</span>
											</el-form-item>
											<el-form-item label="班制名称：" v-if="props.row.shifts_info">
												<span>{{ props.row.shifts_info.sh_title }}</span>
											</el-form-item>
											<el-form-item label="班制牌照：" v-if="props.row.shifts_info">
												<span>{{ props.row.shifts_info.sh_license_name }}</span>
											</el-form-item>
											<el-form-item label="支付时间：" >
												<span>{{ props.row.dt_zhifu_time }}</span>
											</el-form-item>
											<el-form-item label="下单时间：" >
												<span>{{ props.row.addtime }}</span>
											</el-form-item>
											<el-form-item label="用户身份证：" >
												<span>{{ props.row.so_user_identity_id }}</span>
											</el-form-item>
											<el-form-item label="用户报考证：" >
												<span>{{ props.row.so_licence }}</span>
											</el-form-item>
											<el-form-item label="优惠预约计时：" >
												<span style="color: red" v-if="props.row.free_study_hour == -1">不限</span>
												<span v-else>{{ props.row.free_study_hour }} 小时</span>
											</el-form-item>
											<el-form-item label="班制所属：" >
												<span v-if="props.row.coach_name != '--'">教练（{{ props.row.coach_name }}）</span>
												<span v-else>驾校（{{ props.row.school_name }}）</span>
											</el-form-item>
										</el-form>
									</template>
								</el-table-column>
								<el-table-column prop="id" label="ID" sortable width="80"></el-table-column> 
								<el-table-column prop="school_name" v-if="school_id == ''" label="驾校名称" width="150" show-overflow-tooltip></el-table-column> 
								<el-table-column prop="so_username" label="用户名 | 手机号" min-width="200" show-overflow-tooltip>
									<template scope="scope">
										<span>{{ scope.row.so_username }}</span> | 
										<el-tag type="danger" close-transition>{{ scope.row.so_phone }}</el-tag>
									</template>
								</el-table-column> 
								<el-table-column label="价格(元)" min-width="260">
									<el-table-column prop="lesson_name" label="原始价 | 最终价 | 实际支付" width="220" show-overflow-tooltip>
										<template scope="scope">
											<span style="color: #999">{{scope.row.so_original_price}}</span> | 
											<span style="color: #13ce66">{{ scope.row.so_final_price }}</span> | 
											<el-tag type="danger" v-if="scope.row.so_total_price == '0.00'" close-transition>{{scope.row.so_final_price}}</el-tag>
											<el-tag type="danger" v-else close-transition>{{scope.row.so_total_price}}</el-tag> 
										</template>
									</el-table-column>
									<el-table-column prop="coupon_name" label="优惠价" width="90" show-overflow-tooltip>
										<template scope="scope">
											<span>{{ scope.row.coupon_value }}</span>
										</template>
									</el-table-column>
								</el-table-column>
								<el-table-column prop="so_order_no" label="订单号" min-width="150" show-overflow-tooltip></el-table-column>
								<el-table-column prop="so_shifts_id" label="班制名称" min-width="220" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" close-transition v-if="scope.row.shifts_name">{{ scope.row.shifts_name }}</el-tag> | 
										{{ scope.row.shifts_license_name }}
									</template>
								</el-table-column>
								<el-table-column prop="so_pay_type" label="支付方式" min-width="100" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" v-if="scope.row.so_pay_type == '1'">支付宝</el-tag>
										<el-tag type="gray" v-else-if="scope.row.so_pay_type == '2'">线下</el-tag>
										<el-tag type="success" v-else-if="scope.row.so_pay_type == '3'">微信</el-tag>
										<el-tag type="danger" v-else-if="scope.row.so_pay_type == '4'">银联</el-tag>
										<el-tag type="gray" v-else>未知</el-tag>
									</template>
								</el-table-column>
								<!-- <el-table-column prop="coach_name" label="教练名称" min-width="150" show-overflow-tooltip></el-table-column> -->
								<el-table-column prop="s_zhifu_dm" label="交易号" min-width="150" show-overflow-tooltip></el-table-column>
								<el-table-column prop="addtime" label="下单时间" min-width="150" show-overflow-tooltip></el-table-column>
								<el-table-column label="设置订单状态" fixed="right" width="150" style="margin: 0 auto">
									<template scope="scope">
										<el-dropdown trigger="click">
											<el-button class="el-dropdown-link" size="small" type="primary">设置<i class="el-icon-caret-bottom el-icon--right"></i></el-button>
											<el-dropdown-menu slot="dropdown" style="margin-right: -20px">
												<a @click="setOrderStatus(scope.row.id, 1)">
													<el-dropdown-item >已付款</el-dropdown-item>
												</a>
												<!-- <a @click="setOrderStatus(scope.row.id, 1011)">
													<el-dropdown-item >已完成</el-dropdown-item>
												</a> -->
												<a @click="setOrderStatus(scope.row.id, 3)">
													<el-dropdown-item >已取消</el-dropdown-item>
												</a>
												<a @click="setOrderStatus(scope.row.id, 4)">
													<el-dropdown-item >未付款</el-dropdown-item>
												</a>
												<a @click="setOrderStatus(scope.row.id, 2)">
													<el-dropdown-item >退款中</el-dropdown-item>
												</a>
												<a @click="setOrderStatus(scope.row.id, 1007)">
													<el-dropdown-item >已退款</el-dropdown-item>
												</a>
												<!-- <a @click="setOrderStatus(scope.row.id, 101)">
													<el-dropdown-item >已删除</el-dropdown-item>
												</a> -->
											</el-dropdown-menu>
										</el-dropdown>
									</template>
								</el-table-column>
								<el-table-column label="操作" fixed="right" width="150">
									<template scope="scope">
										<a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.id, scope.row.so_order_status)"><i class="el-icon-delete"></i></a>
										<a title="编辑" style="margin-left:8px;" data-title="编辑订单" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
									</template>
								</el-table-column>
							</el-table>
						</el-tab-pane>
						
						<el-tab-pane label="已取消" name="third">
							<el-table :data="cancel_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
								<!--<el-table-column type="selection"width="55"></el-table-column>-->
								<el-table-column type="expand" label="展开">
									<template scope="props">
										<el-form label-position="left" inline class="demo-table-expand">
											<el-form-item label="订单号：">
												<span>{{ props.row.so_order_no }}</span>
											</el-form-item>
											<el-form-item label="交易号：">
												<span>{{ props.row.s_zhifu_dm }}</span>
											</el-form-item>
											<el-form-item label="班制名称：" v-if="props.row.shifts_info">
												<span>{{ props.row.shifts_info.sh_title }}</span>
											</el-form-item>
											<el-form-item label="班制牌照：" v-if="props.row.shifts_info">
												<span>{{ props.row.shifts_info.sh_license_name }}</span>
											</el-form-item>
											<el-form-item label="支付时间：" >
												<span>{{ props.row.dt_zhifu_time }}</span>
											</el-form-item>
											<el-form-item label="下单时间：" >
												<span>{{ props.row.addtime }}</span>
											</el-form-item>
											<el-form-item label="用户身份证：" >
												<span>{{ props.row.so_user_identity_id }}</span>
											</el-form-item>
											<el-form-item label="用户报考证：" >
												<span>{{ props.row.so_licence }}</span>
											</el-form-item>
											<el-form-item label="优惠预约计时：" >
												<span style="color: red" v-if="props.row.free_study_hour == -1">不限</span>
												<span v-else>{{ props.row.free_study_hour }} 小时</span>
											</el-form-item>
											<el-form-item label="班制所属：" >
												<span v-if="props.row.coach_name != '--'">教练（{{ props.row.coach_name }}）</span>
												<span v-else>驾校（{{ props.row.school_name }}）</span>
											</el-form-item>
											<el-form-item label="取消时间：" >
												<span>{{ props.row.cancel_time }}</span>
											</el-form-item>
											<el-form-item label="取消者：" >
												<span v-if="props.row.cancel_type == 1">学员</span>
												<span v-if="props.row.cancel_type == 2">驾校</span>
											</el-form-item>
											<el-form-item label="取消原因：" >
												<span>{{ props.row.cancel_reason }}</span>
											</el-form-item>
										</el-form>
									</template>
								</el-table-column>
								<el-table-column prop="id" label="ID" sortable width="80"></el-table-column> 
								<el-table-column prop="school_name" v-if="school_id == ''" label="驾校名称" width="150" show-overflow-tooltip></el-table-column> 
								<el-table-column prop="so_username" label="用户名 | 手机号" min-width="200" show-overflow-tooltip>
									<template scope="scope">
										<span>{{ scope.row.so_username }}</span> | 
										<el-tag type="danger" close-transition>{{ scope.row.so_phone }}</el-tag>
									</template>
								</el-table-column> 
								<el-table-column label="价格(元)" min-width="260">
									<el-table-column prop="lesson_name" label="原始价 | 最终价 | 实际支付" width="220" show-overflow-tooltip>
										<template scope="scope">
											<span style="color: #999">{{scope.row.so_original_price}}</span> | 
											<span style="color: #13ce66">{{ scope.row.so_final_price }}</span> | 
											<el-tag type="danger" v-if="scope.row.so_total_price == '0.00'" close-transition>{{scope.row.so_final_price}}</el-tag>
											<el-tag type="danger" v-else close-transition>{{scope.row.so_total_price}}</el-tag> 
										</template>
									</el-table-column>
									<el-table-column prop="coupon_name" label="优惠价" width="90" show-overflow-tooltip>
										<template scope="scope">
											<span>{{ scope.row.coupon_value }}</span>
										</template>
									</el-table-column>
								</el-table-column>
								<el-table-column prop="so_order_no" label="订单号" min-width="150" show-overflow-tooltip></el-table-column>
								<el-table-column prop="so_shifts_id" label="班制名称" min-width="220" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" close-transition v-if="scope.row.shifts_name">{{ scope.row.shifts_name }}</el-tag> | 
										{{ scope.row.shifts_license_name }}
									</template>
								</el-table-column>
								<el-table-column prop="so_pay_type" label="支付方式" min-width="100" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" v-if="scope.row.so_pay_type == '1'">支付宝</el-tag>
										<el-tag type="gray" v-else-if="scope.row.so_pay_type == '2'">线下</el-tag>
										<el-tag type="success" v-else-if="scope.row.so_pay_type == '3'">微信</el-tag>
										<el-tag type="danger" v-else-if="scope.row.so_pay_type == '4'">银联</el-tag>
										<el-tag type="gray" v-else>未知</el-tag>
									</template>
								</el-table-column>
								<!-- <el-table-column prop="coach_name" label="教练名称" min-width="150" show-overflow-tooltip></el-table-column> -->
								<el-table-column prop="s_zhifu_dm" label="交易号" min-width="150" show-overflow-tooltip></el-table-column>
								<el-table-column prop="addtime" label="下单时间" min-width="150" show-overflow-tooltip></el-table-column>
								<el-table-column label="设置订单状态" fixed="right" width="150" style="margin: 0 auto">
									<template scope="scope">
										<el-dropdown trigger="click">
											<el-button class="el-dropdown-link" size="small" type="primary">设置<i class="el-icon-caret-bottom el-icon--right"></i></el-button>
											<el-dropdown-menu slot="dropdown" style="margin-right: -20px">
												<a @click="setOrderStatus(scope.row.id, 1)">
													<el-dropdown-item >已付款</el-dropdown-item>
												</a>
												<!-- <a @click="setOrderStatus(scope.row.id, 1011)">
													<el-dropdown-item >已完成</el-dropdown-item>
												</a> -->
												<a @click="setOrderStatus(scope.row.id, 3)">
													<el-dropdown-item >已取消</el-dropdown-item>
												</a>
												<a @click="setOrderStatus(scope.row.id, 4)">
													<el-dropdown-item >未付款</el-dropdown-item>
												</a>
												<a @click="setOrderStatus(scope.row.id, 2)">
													<el-dropdown-item >退款中</el-dropdown-item>
												</a>
												<a @click="setOrderStatus(scope.row.id, 1007)">
													<el-dropdown-item >已退款</el-dropdown-item>
												</a>
												<!-- <a @click="setOrderStatus(scope.row.id, 101)">
													<el-dropdown-item >已删除</el-dropdown-item>
												</a> -->
											</el-dropdown-menu>
										</el-dropdown>
									</template>
								</el-table-column>
								<el-table-column label="操作" fixed="right" width="150">
									<template scope="scope">
										<a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.id, scope.row.so_order_status)"><i class="el-icon-delete"></i></a>
										<a title="编辑" style="margin-left:8px;" data-title="编辑订单" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
									</template>
								</el-table-column>
							</el-table>
						</el-tab-pane>

						<el-tab-pane label="退款中" name="fourth">
							<el-table :data="refunding_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
								<!--<el-table-column type="selection"width="55"></el-table-column>-->
								<el-table-column type="expand" label="展开">
									<template scope="props">
										<el-form label-position="left" inline class="demo-table-expand">
											<el-form-item label="订单号：">
												<span>{{ props.row.so_order_no }}</span>
											</el-form-item>
											<el-form-item label="交易号：">
												<span>{{ props.row.s_zhifu_dm }}</span>
											</el-form-item>
											<el-form-item label="班制名称：" v-if="props.row.shifts_info">
												<span>{{ props.row.shifts_info.sh_title }}</span>
											</el-form-item>
											<el-form-item label="班制牌照：" v-if="props.row.shifts_info">
												<span>{{ props.row.shifts_info.sh_license_name }}</span>
											</el-form-item>
											<el-form-item label="支付时间：" >
												<span>{{ props.row.dt_zhifu_time }}</span>
											</el-form-item>
											<el-form-item label="下单时间：" >
												<span>{{ props.row.addtime }}</span>
											</el-form-item>
											<el-form-item label="用户身份证：" >
												<span>{{ props.row.so_user_identity_id }}</span>
											</el-form-item>
											<el-form-item label="用户报考证：" >
												<span>{{ props.row.so_licence }}</span>
											</el-form-item>
											<el-form-item label="优惠预约计时：" >
												<span style="color: red" v-if="props.row.free_study_hour == -1">不限</span>
												<span v-else>{{ props.row.free_study_hour }} 小时</span>
											</el-form-item>
											<el-form-item label="班制所属：" >
												<span v-if="props.row.coach_name != '--'">教练（{{ props.row.coach_name }}）</span>
												<span v-else>驾校（{{ props.row.school_name }}）</span>
											</el-form-item>
										</el-form>
									</template>
								</el-table-column>
								<el-table-column prop="id" label="ID" sortable width="80"></el-table-column> 
								<el-table-column prop="school_name" v-if="school_id == ''" label="驾校名称" width="150" show-overflow-tooltip></el-table-column> 
								<el-table-column prop="so_username" label="用户名 | 手机号" min-width="200" show-overflow-tooltip>
									<template scope="scope">
										<span>{{ scope.row.so_username }}</span> | 
										<el-tag type="danger" close-transition>{{ scope.row.so_phone }}</el-tag>
									</template>
								</el-table-column> 
								<el-table-column label="价格(元)" min-width="260">
									<el-table-column prop="lesson_name" label="原始价 | 最终价 | 实际支付" width="220" show-overflow-tooltip>
										<template scope="scope">
											<span style="color: #999">{{scope.row.so_original_price}}</span> | 
											<span style="color: #13ce66">{{ scope.row.so_final_price }}</span> | 
											<el-tag type="danger" v-if="scope.row.so_total_price == '0.00'" close-transition>{{scope.row.so_final_price}}</el-tag>
											<el-tag type="danger" v-else close-transition>{{scope.row.so_total_price}}</el-tag> 
										</template>
									</el-table-column>
									<el-table-column prop="coupon_name" label="优惠价" width="90" show-overflow-tooltip>
										<template scope="scope">
											<span>{{ scope.row.coupon_value }}</span>
										</template>
									</el-table-column>
								</el-table-column>
								<el-table-column prop="so_order_no" label="订单号" min-width="150" show-overflow-tooltip></el-table-column>
								<el-table-column prop="so_shifts_id" label="班制名称" min-width="220" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" close-transition v-if="scope.row.shifts_name">{{ scope.row.shifts_name }}</el-tag> | 
										{{ scope.row.shifts_license_name }}
									</template>
								</el-table-column>
								<el-table-column prop="so_pay_type" label="支付方式" min-width="100" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" v-if="scope.row.so_pay_type == '1'">支付宝</el-tag>
										<el-tag type="gray" v-else-if="scope.row.so_pay_type == '2'">线下</el-tag>
										<el-tag type="success" v-else-if="scope.row.so_pay_type == '3'">微信</el-tag>
										<el-tag type="danger" v-else-if="scope.row.so_pay_type == '4'">银联</el-tag>
										<el-tag type="gray" v-else>未知</el-tag>
									</template>
								</el-table-column>
								<!-- <el-table-column prop="coach_name" label="教练名称" min-width="150" show-overflow-tooltip></el-table-column> -->
								<el-table-column prop="s_zhifu_dm" label="交易号" min-width="150" show-overflow-tooltip></el-table-column>
								<el-table-column prop="addtime" label="下单时间" min-width="150" show-overflow-tooltip></el-table-column>
								<el-table-column label="设置订单状态" fixed="right" width="150" style="margin: 0 auto">
									<template scope="scope">
										<el-dropdown trigger="click">
											<el-button class="el-dropdown-link" size="small" type="primary">设置<i class="el-icon-caret-bottom el-icon--right"></i></el-button>
											<el-dropdown-menu slot="dropdown" style="margin-right: -20px">
												<a @click="setOrderStatus(scope.row.id, 1)">
													<el-dropdown-item >已付款</el-dropdown-item>
												</a>
												<!-- <a @click="setOrderStatus(scope.row.id, 1011)">
													<el-dropdown-item >已完成</el-dropdown-item>
												</a> -->
												<a @click="setOrderStatus(scope.row.id, 3)">
													<el-dropdown-item >已取消</el-dropdown-item>
												</a>
												<a @click="setOrderStatus(scope.row.id, 4)">
													<el-dropdown-item >未付款</el-dropdown-item>
												</a>
												<a @click="setOrderStatus(scope.row.id, 2)">
													<el-dropdown-item >退款中</el-dropdown-item>
												</a>
												<a @click="setOrderStatus(scope.row.id, 1007)">
													<el-dropdown-item >已退款</el-dropdown-item>
												</a>
												<!-- <a @click="setOrderStatus(scope.row.id, 101)">
													<el-dropdown-item >已删除</el-dropdown-item>
												</a> -->
											</el-dropdown-menu>
										</el-dropdown>
									</template>
								</el-table-column>
								<el-table-column label="操作" fixed="right" width="150">
									<template scope="scope">
										<a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.id, scope.row.so_order_status)"><i class="el-icon-delete"></i></a>
										<a title="编辑" style="margin-left:8px;" data-title="编辑订单" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
									</template>
								</el-table-column>
							</el-table>
						</el-tab-pane>

						<el-tab-pane label="已退款" name="seventh">
							<el-table :data="refunded_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
								<!--<el-table-column type="selection"width="55"></el-table-column>-->
								<el-table-column type="expand" label="展开">
									<template scope="props">
										<el-form label-position="left" inline class="demo-table-expand">
											<el-form-item label="订单号：">
												<span>{{ props.row.so_order_no }}</span>
											</el-form-item>
											<el-form-item label="交易号：">
												<span>{{ props.row.s_zhifu_dm }}</span>
											</el-form-item>
											<el-form-item label="班制名称：" v-if="props.row.shifts_info">
												<span>{{ props.row.shifts_info.sh_title }}</span>
											</el-form-item>
											<el-form-item label="班制牌照：" v-if="props.row.shifts_info">
												<span>{{ props.row.shifts_info.sh_license_name }}</span>
											</el-form-item>
											<el-form-item label="支付时间：" >
												<span>{{ props.row.dt_zhifu_time }}</span>
											</el-form-item>
											<el-form-item label="下单时间：" >
												<span>{{ props.row.addtime }}</span>
											</el-form-item>
											<el-form-item label="用户身份证：" >
												<span>{{ props.row.so_user_identity_id }}</span>
											</el-form-item>
											<el-form-item label="用户报考证：" >
												<span>{{ props.row.so_licence }}</span>
											</el-form-item>
											<el-form-item label="优惠预约计时：" >
												<span style="color: red" v-if="props.row.free_study_hour == -1">不限</span>
												<span v-else>{{ props.row.free_study_hour }} 小时</span>
											</el-form-item>
											<el-form-item label="班制所属：" >
												<span v-if="props.row.coach_name != '--'">教练（{{ props.row.coach_name }}）</span>
												<span v-else>驾校（{{ props.row.school_name }}）</span>
											</el-form-item>
										</el-form>
									</template>
								</el-table-column>
								<el-table-column prop="id" label="ID" sortable width="80"></el-table-column> 
								<el-table-column prop="school_name" v-if="school_id == ''" label="驾校名称" width="150" show-overflow-tooltip></el-table-column> 
								<el-table-column prop="so_username" label="用户名 | 手机号" min-width="200" show-overflow-tooltip>
									<template scope="scope">
										<span>{{ scope.row.so_username }}</span> | 
										<el-tag type="danger" close-transition>{{ scope.row.so_phone }}</el-tag>
									</template>
								</el-table-column> 
								<el-table-column label="价格(元)" min-width="260">
									<el-table-column prop="lesson_name" label="原始价 | 最终价 | 实际支付" width="220" show-overflow-tooltip>
										<template scope="scope">
											<span style="color: #999">{{scope.row.so_original_price}}</span> | 
											<span style="color: #13ce66">{{ scope.row.so_final_price }}</span> | 
											<el-tag type="danger" v-if="scope.row.so_total_price == '0.00'" close-transition>{{scope.row.so_final_price}}</el-tag>
											<el-tag type="danger" v-else close-transition>{{scope.row.so_total_price}}</el-tag> 
										</template>
									</el-table-column>
									<el-table-column prop="coupon_name" label="优惠价" width="90" show-overflow-tooltip>
										<template scope="scope">
											<span>{{ scope.row.coupon_value }}</span>
										</template>
									</el-table-column>
								</el-table-column>
								<el-table-column prop="so_order_no" label="订单号" min-width="150" show-overflow-tooltip></el-table-column>
								<el-table-column prop="so_shifts_id" label="班制名称" min-width="220" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" close-transition v-if="scope.row.shifts_name">{{ scope.row.shifts_name }}</el-tag> | 
										{{ scope.row.shifts_license_name }}
									</template>
								</el-table-column>
								<el-table-column prop="so_pay_type" label="支付方式" min-width="100" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" v-if="scope.row.so_pay_type == '1'">支付宝</el-tag>
										<el-tag type="gray" v-else-if="scope.row.so_pay_type == '2'">线下</el-tag>
										<el-tag type="success" v-else-if="scope.row.so_pay_type == '3'">微信</el-tag>
										<el-tag type="danger" v-else-if="scope.row.so_pay_type == '4'">银联</el-tag>
										<el-tag type="gray" v-else>未知</el-tag>
									</template>
								</el-table-column>
								<!-- <el-table-column prop="coach_name" label="教练名称" min-width="150" show-overflow-tooltip></el-table-column> -->
								<el-table-column prop="s_zhifu_dm" label="交易号" min-width="150" show-overflow-tooltip></el-table-column>
								<el-table-column prop="addtime" label="下单时间" min-width="150" show-overflow-tooltip></el-table-column>
								<el-table-column label="设置订单状态" fixed="right" width="150" style="margin: 0 auto">
									<template scope="scope">
										<el-dropdown trigger="click">
											<el-button class="el-dropdown-link" size="small" type="primary">设置<i class="el-icon-caret-bottom el-icon--right"></i></el-button>
											<el-dropdown-menu slot="dropdown" style="margin-right: -20px">
												<a @click="setOrderStatus(scope.row.id, 1)">
													<el-dropdown-item >已付款</el-dropdown-item>
												</a>
												<!-- <a @click="setOrderStatus(scope.row.id, 1011)">
													<el-dropdown-item >已完成</el-dropdown-item>
												</a> -->
												<a @click="setOrderStatus(scope.row.id, 3)">
													<el-dropdown-item >已取消</el-dropdown-item>
												</a>
												<a @click="setOrderStatus(scope.row.id, 4)">
													<el-dropdown-item >未付款</el-dropdown-item>
												</a>
												<a @click="setOrderStatus(scope.row.id, 2)">
													<el-dropdown-item >退款中</el-dropdown-item>
												</a>
												<a @click="setOrderStatus(scope.row.id, 1007)">
													<el-dropdown-item >已退款</el-dropdown-item>
												</a>
												<!-- <a @click="setOrderStatus(scope.row.id, 101)">
													<el-dropdown-item >已删除</el-dropdown-item>
												</a> -->
											</el-dropdown-menu>
										</el-dropdown>
									</template>
								</el-table-column>
								<el-table-column label="操作" fixed="right" width="150">
									<template scope="scope">
										<a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.id, scope.row.so_order_status)"><i class="el-icon-delete"></i></a>
										<a title="编辑" style="margin-left:8px;" data-title="编辑订单" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
									</template>
								</el-table-column>
							</el-table>
						</el-tab-pane>

						<el-tab-pane label="已删除" name="fifth" v-if="school_id == ''">
							<el-table :data="deleted_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
								<!--<el-table-column type="selection"width="55"></el-table-column>-->
								<el-table-column type="expand" label="展开">
									<template scope="props">
										<el-form label-position="left" inline class="demo-table-expand">
											<el-form-item label="订单号：">
												<span>{{ props.row.so_order_no }}</span>
											</el-form-item>
											<el-form-item label="交易号：">
												<span>{{ props.row.s_zhifu_dm }}</span>
											</el-form-item>
											<el-form-item label="班制名称：" v-if="props.row.shifts_info">
												<span>{{ props.row.shifts_info.sh_title }}</span>
											</el-form-item>
											<el-form-item label="班制牌照：" v-if="props.row.shifts_info">
												<span>{{ props.row.shifts_info.sh_license_name }}</span>
											</el-form-item>
											<el-form-item label="支付时间：" >
												<span>{{ props.row.dt_zhifu_time }}</span>
											</el-form-item>
											<el-form-item label="下单时间：" >
												<span>{{ props.row.addtime }}</span>
											</el-form-item>
											<el-form-item label="用户身份证：" >
												<span>{{ props.row.so_user_identity_id }}</span>
											</el-form-item>
											<el-form-item label="用户报考证：" >
												<span>{{ props.row.so_licence }}</span>
											</el-form-item>
											<el-form-item label="优惠预约计时：" >
												<span style="color: red" v-if="props.row.free_study_hour == -1">不限</span>
												<span v-else>{{ props.row.free_study_hour }} 小时</span>
											</el-form-item>
											<el-form-item label="班制所属：" >
												<span v-if="props.row.coach_name != '--'">教练（{{ props.row.coach_name }}）</span>
												<span v-else>驾校（{{ props.row.school_name }}）</span>
											</el-form-item>
										</el-form>
									</template>
								</el-table-column>
								<el-table-column prop="id" label="ID" sortable width="80"></el-table-column> 
								<el-table-column prop="school_name" v-if="school_id == ''" label="驾校名称" width="150" show-overflow-tooltip></el-table-column> 
								<el-table-column prop="so_username" label="用户名 | 手机号" min-width="200" show-overflow-tooltip>
									<template scope="scope">
										<span>{{ scope.row.so_username }}</span> | 
										<el-tag type="danger" close-transition>{{ scope.row.so_phone }}</el-tag>
									</template>
								</el-table-column> 
								<el-table-column label="价格(元)" min-width="260">
									<el-table-column prop="lesson_name" label="原始价 | 最终价 | 实际支付" width="220" show-overflow-tooltip>
										<template scope="scope">
											<span style="color: #999">{{scope.row.so_original_price}}</span> | 
											<span style="color: #13ce66">{{ scope.row.so_final_price }}</span> | 
											<el-tag type="danger" v-if="scope.row.so_total_price == '0.00'" close-transition>{{scope.row.so_final_price}}</el-tag>
											<el-tag type="danger" v-else close-transition>{{scope.row.so_total_price}}</el-tag> 
										</template>
									</el-table-column>
									<el-table-column prop="coupon_name" label="优惠价" width="90" show-overflow-tooltip>
										<template scope="scope">
											<span>{{ scope.row.coupon_value }}</span>
										</template>
									</el-table-column>
								</el-table-column>
								<el-table-column prop="so_order_no" label="订单号" min-width="150" show-overflow-tooltip></el-table-column>
								<el-table-column prop="so_shifts_id" label="班制名称" min-width="220" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" close-transition v-if="scope.row.shifts_name">{{ scope.row.shifts_name }}</el-tag> | 
										{{ scope.row.shifts_license_name }}
									</template>
								</el-table-column>
								<el-table-column prop="so_pay_type" label="支付方式" min-width="100" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" v-if="scope.row.so_pay_type == '1'">支付宝</el-tag>
										<el-tag type="gray" v-else-if="scope.row.so_pay_type == '2'">线下</el-tag>
										<el-tag type="success" v-else-if="scope.row.so_pay_type == '3'">微信</el-tag>
										<el-tag type="danger" v-else-if="scope.row.so_pay_type == '4'">银联</el-tag>
										<el-tag type="gray" v-else>未知</el-tag>
									</template>
								</el-table-column>
								<!-- <el-table-column prop="coach_name" label="教练名称" min-width="150" show-overflow-tooltip></el-table-column> -->
								<el-table-column prop="s_zhifu_dm" label="交易号" min-width="150" show-overflow-tooltip></el-table-column>
								<el-table-column prop="addtime" label="下单时间" min-width="150" show-overflow-tooltip></el-table-column>
								<el-table-column label="设置订单状态" fixed="right" width="150" style="margin: 0 auto">
									<template scope="scope">
										<el-dropdown trigger="click">
											<el-button class="el-dropdown-link" size="small" type="primary">设置<i class="el-icon-caret-bottom el-icon--right"></i></el-button>
											<el-dropdown-menu slot="dropdown" style="margin-right: -20px">
												<a @click="setOrderStatus(scope.row.id, 1)">
													<el-dropdown-item >已付款</el-dropdown-item>
												</a>
												<!-- <a @click="setOrderStatus(scope.row.id, 1011)">
													<el-dropdown-item >已完成</el-dropdown-item>
												</a> -->
												<a @click="setOrderStatus(scope.row.id, 3)">
													<el-dropdown-item >已取消</el-dropdown-item>
												</a>
												<a @click="setOrderStatus(scope.row.id, 4)">
													<el-dropdown-item >未付款</el-dropdown-item>
												</a>
												<a @click="setOrderStatus(scope.row.id, 2)">
													<el-dropdown-item >退款中</el-dropdown-item>
												</a>
												<a @click="setOrderStatus(scope.row.id, 1007)">
													<el-dropdown-item >已退款</el-dropdown-item>
												</a>
												<!-- <a @click="setOrderStatus(scope.row.id, 101)">
													<el-dropdown-item >已删除</el-dropdown-item>
												</a> -->
											</el-dropdown-menu>
										</el-dropdown>
									</template>
								</el-table-column>
								<el-table-column label="操作" fixed="right" width="150">
									<template scope="scope">
										<a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.id, scope.row.so_order_status)"><i class="el-icon-delete"></i></a>
										<a title="编辑" style="margin-left:8px;" data-title="编辑订单" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
									</template>
								</el-table-column>
							</el-table>
						</el-tab-pane>

						<el-tab-pane label="已完成" name="sixth">
							<el-table :data="completed_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
								<!--<el-table-column type="selection"width="55"></el-table-column>-->
								<el-table-column type="expand" label="展开">
									<template scope="props">
										<el-form label-position="left" inline class="demo-table-expand">
											<el-form-item label="订单号：">
												<span>{{ props.row.so_order_no }}</span>
											</el-form-item>
											<el-form-item label="交易号：">
												<span>{{ props.row.s_zhifu_dm }}</span>
											</el-form-item>
											<el-form-item label="班制名称：" v-if="props.row.shifts_info">
												<span>{{ props.row.shifts_info.sh_title }}</span>
											</el-form-item>
											<el-form-item label="班制牌照：" v-if="props.row.shifts_info">
												<span>{{ props.row.shifts_info.sh_license_name }}</span>
											</el-form-item>
											<el-form-item label="支付时间：" >
												<span>{{ props.row.dt_zhifu_time }}</span>
											</el-form-item>
											<el-form-item label="下单时间：" >
												<span>{{ props.row.addtime }}</span>
											</el-form-item>
											<el-form-item label="用户身份证：" >
												<span>{{ props.row.so_user_identity_id }}</span>
											</el-form-item>
											<el-form-item label="用户报考证：" >
												<span>{{ props.row.so_licence }}</span>
											</el-form-item>
											<el-form-item label="优惠预约计时：" >
												<span style="color: red" v-if="props.row.free_study_hour == -1">不限</span>
												<span v-else>{{ props.row.free_study_hour }} 小时</span>
											</el-form-item>
											<el-form-item label="班制所属：" >
												<span v-if="props.row.coach_name != '--'">教练（{{ props.row.coach_name }}）</span>
												<span v-else>驾校（{{ props.row.school_name }}）</span>
											</el-form-item>
										</el-form>
									</template>
								</el-table-column>
								<el-table-column prop="id" label="ID" sortable width="80"></el-table-column> 
								<el-table-column prop="school_name" v-if="school_id == ''" label="驾校名称" width="150" show-overflow-tooltip></el-table-column> 
								<el-table-column prop="so_username" label="用户名 | 手机号" min-width="200" show-overflow-tooltip>
									<template scope="scope">
										<span>{{ scope.row.so_username }}</span> | 
										<el-tag type="danger" close-transition>{{ scope.row.so_phone }}</el-tag>
									</template>
								</el-table-column> 
								<el-table-column label="价格(元)" min-width="260">
									<el-table-column prop="lesson_name" label="原始价 | 最终价 | 实际支付" width="220" show-overflow-tooltip>
										<template scope="scope">
											<span style="color: #999">{{scope.row.so_original_price}}</span> | 
											<span style="color: #13ce66">{{ scope.row.so_final_price }}</span> | 
											<el-tag type="danger" v-if="scope.row.so_total_price == '0.00'" close-transition>{{scope.row.so_final_price}}</el-tag>
											<el-tag type="danger" v-else close-transition>{{scope.row.so_total_price}}</el-tag> 
										</template>
									</el-table-column>
									<el-table-column prop="coupon_name" label="优惠价" width="90" show-overflow-tooltip>
										<template scope="scope">
											<span>{{ scope.row.coupon_value }}</span>
										</template>
									</el-table-column>
								</el-table-column>
								<el-table-column prop="so_order_no" label="订单号" min-width="150" show-overflow-tooltip></el-table-column>
								<el-table-column prop="so_shifts_id" label="班制名称" min-width="220" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" close-transition v-if="scope.row.shifts_name">{{ scope.row.shifts_name }}</el-tag> | 
										{{ scope.row.shifts_license_name }}
									</template>
								</el-table-column>
								<el-table-column prop="so_pay_type" label="支付方式" min-width="100" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" v-if="scope.row.so_pay_type == '1'">支付宝</el-tag>
										<el-tag type="gray" v-else-if="scope.row.so_pay_type == '2'">线下</el-tag>
										<el-tag type="success" v-else-if="scope.row.so_pay_type == '3'">微信</el-tag>
										<el-tag type="danger" v-else-if="scope.row.so_pay_type == '4'">银联</el-tag>
										<el-tag type="gray" v-else>未知</el-tag>
									</template>
								</el-table-column>
								<!-- <el-table-column prop="coach_name" label="教练名称" min-width="150" show-overflow-tooltip></el-table-column> -->
								<el-table-column prop="s_zhifu_dm" label="交易号" min-width="150" show-overflow-tooltip></el-table-column>
								<el-table-column prop="addtime" label="下单时间" min-width="150" show-overflow-tooltip></el-table-column>
								<el-table-column label="设置订单状态" fixed="right" width="150" style="margin: 0 auto">
									<template scope="scope">
										<el-dropdown trigger="click">
											<el-button class="el-dropdown-link" size="small" type="primary">设置<i class="el-icon-caret-bottom el-icon--right"></i></el-button>
											<el-dropdown-menu slot="dropdown" style="margin-right: -20px">
												<a @click="setOrderStatus(scope.row.id, 1)">
													<el-dropdown-item >已付款</el-dropdown-item>
												</a>
												<!-- <a @click="setOrderStatus(scope.row.id, 1011)">
													<el-dropdown-item >已完成</el-dropdown-item>
												</a> -->
												<a @click="setOrderStatus(scope.row.id, 3)">
													<el-dropdown-item >已取消</el-dropdown-item>
												</a>
												<a @click="setOrderStatus(scope.row.id, 4)">
													<el-dropdown-item >未付款</el-dropdown-item>
												</a>
												<a @click="setOrderStatus(scope.row.id, 2)">
													<el-dropdown-item >退款中</el-dropdown-item>
												</a>
												<a @click="setOrderStatus(scope.row.id, 1007)">
													<el-dropdown-item >已退款</el-dropdown-item>
												</a>
												<!-- <a @click="setOrderStatus(scope.row.id, 101)">
													<el-dropdown-item >已删除</el-dropdown-item>
												</a> -->
											</el-dropdown-menu>
										</el-dropdown>
									</template>
								</el-table-column>
								<el-table-column label="操作" fixed="right" width="150">
									<template scope="scope">
										<a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.id, scope.row.so_order_status)"><i class="el-icon-delete"></i></a>
										<a title="编辑" style="margin-left:8px;" data-title="编辑订单" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
									</template>
								</el-table-column>
							</el-table>
						</el-tab-pane>	
					</el-tabs>

					<!--page-->
					<div class="block" style="float: right; margin-top: 10px;">
						<el-pagination
							@size-change="handleSizeChange"
							@current-change="handleCurrentChange"
							:current-page="currentPage"
							:page-sizes="page_sizes"
							:page-size="page_size"
							layout="total, sizes, prev, pager, next, jumper"
							:total="count">
						</el-pagination>
					</div>
					<!--end page-->
				</el-tab-pane>
				<el-tab-pane label="线下支付" name="second"></el-tab-pane>
			</el-tabs>
		</div>
	</div>
</div>
<script>
	Vue.config.devtools = true;
	var school_id = "<?php echo $school_id;?>";
	var vm = new Vue({
		el: '#app',
		data: {
			refreshstatus: false,
			fullscreenLoading: true,
			list: [],
			all_list: [],
			unpaid_list: [],
			cancel_list: [],
			refunding_list: [],
			refunded_list: [],
			deleted_list: [],
			completed_list: [],
			multipleSelection: [],
			list_url: "<?php echo base_url('order/shiftsorderajax'); ?>",
			del_url: "<?php echo base_url('order/delShiftsOrderAjax'); ?>",
			edit_url: "<?php echo base_url('order/edit'); ?>",
			add_url: "<?php echo base_url('order/add'); ?>",
			set_url: "<?php echo base_url('order/setOrderAjax'); ?>?ot=online",
			currentPage: 1,
            page_sizes: [10, 20, 30, 50, 100],
            page_size: 10,
            pagenum: 0,
            count: 0,
			payActive: 'first',
			activeName: 'zero',
			order_status: 'all',
			online_type: "online",
			search: {
				pay_type: '',
				keywords: '',
			}
		},
		created: function() {
			var filters = {'p': this.currentPage, 'pt':this.search.pay_type, 'keywords':this.search.keywords, 'os': this.order_status, 'ot': this.online_type, 's': this.page_size};
			this.listAjax(filters);
		},
		methods: {
			setOrderStatus: function(id, status) {
				$.ajax({
					type: 'post',
					url: this.set_url,
					data: {'id': id, 'status': status},
					dataType:"json",
					async: true,
					success: function(data) {
						vm.fullscreenLoading = false;			
						vm.refreshstatus = false;
						if(data.code == 200) {
							var filters = {'p': vm.currentPage, 'ot': vm.online_type, 'os': vm.order_status, 's': vm.page_size};
							vm.listAjax(filters);
							vm.messageNotice('success', data.msg);
						} else {
							vm.messageNotice('warning', data.msg);
						}
					},
					error: function() {
						vm.fullscreenLoading = false;
						vm.refreshstatus = false;
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				});
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
							switch (param.os) {
								case 'all':
									vm.all_list = data.data.list;
									break;
								case 'paid':
									vm.list = data.data.list;
									break;
								case 'unpaid':
									vm.unpaid_list = data.data.list;
									break;
								case 'cancel':
									vm.cancel_list = data.data.list;
									break;
								case 'refunding':
									vm.refunding_list = data.data.list;
									break;
								case 'refunded':
									vm.refunded_list = data.data.list;
									break;
								case 'deleted':
									vm.deleted_list = data.data.list;
									break;
								case 'completed':
									vm.completed_list = data.data.list;
									break;
								default:
									vm.list = data.data.list;
									break;
							}
							vm.currentPage = data.data.p;
							vm.pagenum = data.data.pagenum;
							vm.count = data.data.count;
						}
					},
					error: function() {
						vm.fullscreenLoading = false;
						vm.refreshstatus = false;
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				});
			},
			delAjax: function(id) {
				$.ajax({
					type: 'post',
					url: this.del_url,
					data: {id:id},
					dataType:"json",
					success: function(data) {
						vm.fullscreenLoading = false;			
						if(data.code == 200) {
							var filters = {'p': vm.currentPage, 'os': vm.order_status, 'ot': vm.online_type, 's': vm.page_size};
							vm.listAjax(filters);
							vm.messageNotice('success', data.msg);			
						} 
					},
					error: function() {
						vm.fullscreenLoading = false;									
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				})
			},
			handlePayTypeClick: function() {
				if(this.payActive == 'second') {
					location.href="<?php echo base_url('order/shifts?ot=line'); ?>";
				}
			},
			handleClick: function(tab, event) {
				switch (this.activeName) {
					case 'zero':
						this.order_status = 'all';
						break;
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
					case 'seventh':
						this.order_status = 'refunded';
						break;
					default:
						this.order_status = 'all';
						break;
				}
				// window.history.pushState(null, null, '?ot='+this.otype+'&pt='+this.ptype+'&act='+this.activeName+'&p=1');
				// if(this.list.length == 0 || this.unpaid_list.length == 0 ||  this.cancel_list.length == 0 ||  this.refunding_list.length == 0 ||  this.deleted_list.length == 0 || this.completed_list.length == 0) {
					var filters = {'p': this.currentPage, 'pt':this.search.pay_type, 'keywords':this.search.keywords, 'os': this.order_status, 'ot': this.online_type, 's': this.page_size};
					this.listAjax(filters);
				// }
			},
			onSubmit: function() {
				var filters = {'p': this.currentPage, 'pt':this.search.pay_type, 'keywords':this.search.keywords, 'os': this.order_status, 'ot': this.online_type, 's': this.page_size};
				vm.listAjax(filters);
			},    
			handleSizeChange: function (size) {
                this.page_size = size;
				var filters = {'p': this.currentPage, 'pt':this.search.pay_type, 'keywords':this.search.keywords, 'os': this.order_status, 'ot': this.online_type, 's': this.page_size};
                this.listAjax(filters);
            },
			handleCurrentChange: function (val) {
                this.refreshstatus = true;
                this.currentPage = val;
                // window.history.pushState(null, null, '?p='+val+'&pt='+this.search.pay_type+'&os='+this.order_status+'&keywords='+this.search.keywords+'&ot='+this.online_type+'&s='+this.page_size);
				var filters = {'p': this.currentPage, 'pt':vm.search.pay_type, 'keywords':vm.search.keywords, 'os': vm.order_status, 'ot': vm.online_type, 's': this.page_size};
                vm.listAjax(filters);
            },
			handleSelectionChange: function(val) {
				this.multipleSelection = val;
			},
			handleRefresh: function() {
				this.refreshstatus = true;
				var filters = {'p': this.currentPage, 'os': this.order_status, 'ot': this.online_type, 's': this.page_size};
				this.listAjax(filters);
			},
			handleAdd: function(e) {
				this.showLayer(e, '60%', 'rb', this.add_url);
			},
			handleEdit: function(e, id, index, row) {
				this.showLayer(e, '80%', 'rb', this.edit_url+'?id='+id);
			},
			handleDel: function(id, index, rows) {
				this.$confirm('此操作将永久删除该文件, 是否继续?', '提示', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning',
					callback: function(action) {
						if(action == 'confirm') {
							vm.delAjax(id);
							// rows.splice(index, 1);
							// vm.messageNotice('success', '删除成功!');
						} else {
							return false;
						}
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
			}
			
		}
	})
</script>