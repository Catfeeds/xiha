<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">
		<div class="gx-iframe-content">
			<!-- <el-tabs v-model="payActive" type="card" type="border-card" @tab-click="handlePayTypeClick"> -->
				<!-- <el-tab-pane label="线上支付" name="first"> -->

					<!--搜索-->
					<div class="gx-search">
						<el-form :inline="true" :model="search" class="demo-form-inline">
							<el-form-item label="支付方式">
								<el-select v-model="search.deal_type" placeholder="--不限方式--">
								<el-option label="--不限方式--" value=""></el-option>
								<el-option label="支付宝" value="1"></el-option>
								<el-option label="线下" value="2"></el-option>
								<el-option label="微信" value="3"></el-option>
								<el-option label="银联" value="4"></el-option>
								</el-select>
							</el-form-item>
							<el-form-item label="关键词">
								<el-input v-if="school_id == ''" style="width:300px;" v-model="search.keywords" placeholder="ID|姓名|手机|订单号|交易号|驾校名称"></el-input>
								<el-input v-if="school_id != ''" style="width:300px;" v-model="search.keywords" placeholder="ID|姓名|手机|订单号|交易号"></el-input>
							</el-form-item>
							<el-form-item>
								<el-button type="primary" @click="onSubmit">查询</el-button>
							</el-form-item>
						</el-form>
					</div>
					<div class="gx-iframe-operation" style="margin-bottom: 20px; border-radius: 4px;">
						<span style="padding-left: 10px; font-weight:bold;font-size: 14px;">已完成：{{ total_service_time }} 学时</span>
						<el-button type="success" type="small" style="float:right; line-height: 38px;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
					</div>
					<el-tabs v-model="activeName" @tab-click="handleClick">
					<el-tab-pane label="全部" name="zero">
							<el-table :data="all_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
								<!--<el-table-column type="selection" width="55"></el-table-column>-->
								<el-table-column type="expand" label="展开">
									<template scope="props">
										<el-form label-position="left"  inline class="demo-table-expand">
											<el-form-item label="学员姓名：">
												<span>{{ props.row.s_user_name }}</span>
											</el-form-item>
											<el-form-item label="学员手机：">
												<span>{{ props.row.s_user_phone }}</span>
											</el-form-item>
											<el-form-item label="教练姓名：">
												<span>{{ props.row.s_coach_name }}</span>
											</el-form-item>
											<el-form-item label="教练手机：">
												<span>{{ props.row.s_coach_phone }}</span>
											</el-form-item>
											<el-form-item label="预约科目：">
												<span>{{ props.row.s_lesson_name }}</span>
											</el-form-item>
											<el-form-item label="预约牌照：">
												<span>{{ props.row.s_lisence_name }}</span>
											</el-form-item>
											<el-form-item label="下单时间：">
												<span>{{ props.row.dt_order_time }}</span>
											</el-form-item>
											<el-form-item label="支付时间：">
												<span>{{ props.row.dt_zhifu_time }}</span>
											</el-form-item>
											<el-form-item label="所报驾校：">
												<span>{{ props.row.s_school_name }}</span>
											</el-form-item>
											<el-form-item label="驾校地址：">
												<span>{{ props.row.s_address }}</span>
											</el-form-item>
											<el-form-item label="预约计时：">
												<span>{{ props.row.i_service_time }}小时</span>
											</el-form-item>
										</el-form>
									</template>
								</el-table-column>
								<el-table-column prop="l_study_order_id" label="ID" sortable width="80"></el-table-column> 
								<el-table-column prop="s_school_name" v-if="school_id == ''" label="驾校名称" min-width="160" show-overflow-tooltip></el-table-column> 
								<el-table-column prop="s_user_name" label="学员姓名" min-width="125" show-overflow-tooltip></el-table-column> 
								<el-table-column prop="s_user_phone" label="学员手机" min-width="125" show-overflow-tooltip></el-table-column> 
								<el-table-column prop="s_coach_name" label="教练姓名" min-width="125" show-overflow-tooltip></el-table-column>
								<el-table-column prop="s_coach_phone" label="教练手机" min-width="125" show-overflow-tooltip></el-table-column>
								<el-table-column prop="appoint_time_list" label="预约时间" min-width="205">
									<template scope="scope">
										<span>{{ scope.row.appoint_time_date }}</span> <br/>
										<span style="color:red">{{ scope.row.appoint_time }}</span>  
									</template>
								</el-table-column>
								<el-table-column prop="dc_money" label="支付金额(元)" min-width="130" show-overflow-tooltip></el-table-column>
								<el-table-column prop="i_service_time" label="预约时长" min-width="125" show-overflow-tooltip>
									<template scope="scope">
										<span>{{ scope.row.i_service_time }} 学时</span> <br/>
									</template>
								</el-table-column>
								<el-table-column prop="s_order_no" label="订单号" min-width="180" show-overflow-tooltip></el-table-column>
								<el-table-column prop="s_zhifu_dm" label="交易号" min-width="180" show-overflow-tooltip></el-table-column>
								<el-table-column prop="i_status" label="订单状态" width="100" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" v-if="scope.row.i_status == '1'">已付款</el-tag>
										<el-tag type="success" v-else-if="scope.row.i_status == '2'">已完成</el-tag>
										<el-tag type="danger" v-else-if="scope.row.i_status == '3'">已取消</el-tag>
										<el-tag type="warning" v-else-if="scope.row.i_status == '1003'">未付款</el-tag>
										<el-tag type="warning" v-else-if="scope.row.i_status == '1006'">退款中</el-tag>
										<el-tag type="danger" v-else-if="scope.row.i_status == '1007'">已退款</el-tag>
										<el-tag type="danger" v-else-if="scope.row.i_status == '101'">已删除</el-tag>
										<el-tag type="gray" v-else>未知</el-tag>
									</template>
								</el-table-column>
								<el-table-column prop="deal_type" label="支付方式" width="100" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" v-if="scope.row.deal_type == '1'">支付宝</el-tag>
										<el-tag type="warning" v-else-if="scope.row.deal_type == '2'">线下</el-tag>
										<el-tag type="success" v-else-if="scope.row.deal_type == '3'">微信</el-tag>
										<el-tag type="danger" v-else-if="scope.row.deal_type == '4'">银联</el-tag>
										<el-tag type="gray" v-else>未知</el-tag>
									</template>
								</el-table-column>

								<el-table-column prop="dt_order_time" label="下单时间" min-width="180" show-overflow-tooltip></el-table-column>
								<el-table-column label="设置订单状态" fixed="right" width="150" style="margin: 0 auto">
									<template scope="scope">
										<el-dropdown trigger="click">
											<el-button class="el-dropdown-link" size="small" type="primary">设置<i class="el-icon-caret-bottom el-icon--right"></i></el-button>
											<el-dropdown-menu slot="dropdown" style="margin-right: -20px">
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1)">
													<el-dropdown-item >已付款</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 2)">
													<el-dropdown-item >已完成</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 3)">
													<el-dropdown-item >已取消</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1003)">
													<el-dropdown-item >未付款</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1006)">
													<el-dropdown-item >退款中</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1007)">
													<el-dropdown-item >已退款</el-dropdown-item>
												</a>
												<!-- <a @click="handleOrderStatus(scope.row.l_study_order_id, 101)">
													<el-dropdown-item >已删除</el-dropdown-item>
												</a> -->
											</el-dropdown-menu>
										</el-dropdown>
									</template>
								</el-table-column>
								<el-table-column label="操作" fixed="right" width="140">
									<template scope="scope">
										<a title="查看" data-title="查看订单" @click="handlePreview($event, scope.row.l_study_order_id, scope.$index, scope.row)"><i class="el-icon-view"></i></a>
										<a title="删除" style="margin-left:8px;" @click="handleDel (scope.row.l_study_order_id, scope.row.i_status)"><i class="el-icon-delete"></i></a>
									</template>
								</el-table-column>
							</el-table>
						</el-tab-pane>

						<el-tab-pane label="已付款" name="first">
							<el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
								<!--<el-table-column type="selection" width="55"></el-table-column>-->
								<el-table-column type="expand" label="展开">
									<template scope="props">
										<el-form label-position="left"  inline class="demo-table-expand">
											<el-form-item label="学员姓名：">
												<span>{{ props.row.s_user_name }}</span>
											</el-form-item>
											<el-form-item label="学员手机：">
												<span>{{ props.row.s_user_phone }}</span>
											</el-form-item>
											<el-form-item label="教练姓名：">
												<span>{{ props.row.s_coach_name }}</span>
											</el-form-item>
											<el-form-item label="教练手机：">
												<span>{{ props.row.s_coach_phone }}</span>
											</el-form-item>
											<el-form-item label="预约科目：">
												<span>{{ props.row.s_lesson_name }}</span>
											</el-form-item>
											<el-form-item label="预约牌照：">
												<span>{{ props.row.s_lisence_name }}</span>
											</el-form-item>
											<el-form-item label="下单时间：">
												<span>{{ props.row.dt_order_time }}</span>
											</el-form-item>
											<el-form-item label="支付时间：">
												<span>{{ props.row.dt_zhifu_time }}</span>
											</el-form-item>
											<el-form-item label="所报驾校：">
												<span>{{ props.row.s_school_name }}</span>
											</el-form-item>
											<el-form-item label="驾校地址：">
												<span>{{ props.row.s_address }}</span>
											</el-form-item>
											<el-form-item label="预约计时：">
												<span>{{ props.row.i_service_time }}小时</span>
											</el-form-item>
										</el-form>
									</template>
								</el-table-column>
								<el-table-column prop="l_study_order_id" label="ID" sortable width="80"></el-table-column> 
								<el-table-column prop="s_school_name" v-if="school_id == ''" label="驾校名称" min-width="160" show-overflow-tooltip></el-table-column>  
								<el-table-column prop="s_user_name" label="学员姓名" min-width="125" show-overflow-tooltip></el-table-column> 
								<el-table-column prop="s_user_phone" label="学员手机" min-width="125" show-overflow-tooltip></el-table-column> 
								<el-table-column prop="s_coach_name" label="教练姓名" min-width="125" show-overflow-tooltip></el-table-column>
								<el-table-column prop="s_coach_phone" label="教练手机" min-width="125" show-overflow-tooltip></el-table-column>
								<el-table-column prop="appoint_time_list" label="预约时间" min-width="205">
									<template scope="scope">
										<span>{{ scope.row.appoint_time_date }}</span> <br/>
										<span style="color:red">{{ scope.row.appoint_time }}</span>  
									</template>
								</el-table-column>
								<el-table-column prop="dc_money" label="支付金额(元)" min-width="130" show-overflow-tooltip></el-table-column>
								<el-table-column prop="i_service_time" label="预约时长" min-width="125" show-overflow-tooltip>
									<template scope="scope">
										<span>{{ scope.row.i_service_time }} 学时</span> <br/>
									</template>
								</el-table-column>
								<el-table-column prop="s_order_no" label="订单号" min-width="180" show-overflow-tooltip></el-table-column>
								<el-table-column prop="s_zhifu_dm" label="交易号" min-width="180" show-overflow-tooltip></el-table-column>
								<el-table-column prop="deal_type" label="支付方式" width="100" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" v-if="scope.row.deal_type == '1'">支付宝</el-tag>
										<el-tag type="warning" v-else-if="scope.row.deal_type == '2'">线下</el-tag>
										<el-tag type="success" v-else-if="scope.row.deal_type == '3'">微信</el-tag>
										<el-tag type="danger" v-else-if="scope.row.deal_type == '4'">银联</el-tag>
										<el-tag type="gray" v-else>未知</el-tag>
									</template>
								</el-table-column>

								<el-table-column prop="dt_order_time" label="下单时间" min-width="180" show-overflow-tooltip></el-table-column>
								<el-table-column label="设置订单状态" fixed="right" width="150" style="margin: 0 auto">
									<template scope="scope">
										<el-dropdown trigger="click">
											<el-button class="el-dropdown-link" size="small" type="primary">设置<i class="el-icon-caret-bottom el-icon--right"></i></el-button>
											<el-dropdown-menu slot="dropdown" style="margin-right: -20px">
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1)">
													<el-dropdown-item >已付款</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 2)">
													<el-dropdown-item >已完成</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 3)">
													<el-dropdown-item >已取消</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1003)">
													<el-dropdown-item >未付款</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1006)">
													<el-dropdown-item >退款中</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1007)">
													<el-dropdown-item >已退款</el-dropdown-item>
												</a>
												<!-- <a @click="handleOrderStatus(scope.row.l_study_order_id, 101)">
													<el-dropdown-item >已删除</el-dropdown-item>
												</a> -->
											</el-dropdown-menu>
										</el-dropdown>
									</template>
								</el-table-column>
								<el-table-column label="操作" fixed="right" width="140">
									<template scope="scope">
										<a title="查看" data-title="查看订单" @click="handlePreview($event, scope.row.l_study_order_id, scope.$index, scope.row)"><i class="el-icon-view"></i></a>
										<a title="删除" style="margin-left:8px;" @click="handleDel (scope.row.l_study_order_id, scope.row.i_status)"><i class="el-icon-delete"></i></a>
									</template>
								</el-table-column>
							</el-table>
						</el-tab-pane>

						<el-tab-pane label="未付款" name="second">
							<el-table :data="unpaid_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
								<!--<el-table-column type="selection" width="55"></el-table-column>-->
								<el-table-column type="expand" label="展开">
									<template scope="props">
										<el-form label-position="left"  inline class="demo-table-expand">
											<el-form-item label="学员姓名：">
												<span>{{ props.row.s_user_name }}</span>
											</el-form-item>
											<el-form-item label="学员手机：">
												<span>{{ props.row.s_user_phone }}</span>
											</el-form-item>
											<el-form-item label="教练姓名：">
												<span>{{ props.row.s_coach_name }}</span>
											</el-form-item>
											<el-form-item label="教练手机：">
												<span>{{ props.row.s_coach_phone }}</span>
											</el-form-item>
											<el-form-item label="预约科目：">
												<span>{{ props.row.s_lesson_name }}</span>
											</el-form-item>
											<el-form-item label="预约牌照：">
												<span>{{ props.row.s_lisence_name }}</span>
											</el-form-item>
											<el-form-item label="下单时间：">
												<span>{{ props.row.dt_order_time }}</span>
											</el-form-item>
											<el-form-item label="支付时间：">
												<span>{{ props.row.dt_zhifu_time }}</span>
											</el-form-item>
											<el-form-item label="所报驾校：">
												<span>{{ props.row.s_school_name }}</span>
											</el-form-item>
											<el-form-item label="驾校地址：">
												<span>{{ props.row.s_address }}</span>
											</el-form-item>
											<el-form-item label="预约计时：">
												<span>{{ props.row.i_service_time }}小时</span>
											</el-form-item>
										</el-form>
									</template>
								</el-table-column>
								<el-table-column prop="l_study_order_id" label="ID" sortable width="80"></el-table-column> 
								<el-table-column prop="s_school_name" v-if="school_id == ''" label="驾校名称" min-width="160" show-overflow-tooltip></el-table-column>  
								<el-table-column prop="s_user_name" label="学员姓名" min-width="125" show-overflow-tooltip></el-table-column> 
								<el-table-column prop="s_user_phone" label="学员手机" min-width="125" show-overflow-tooltip></el-table-column> 
								<el-table-column prop="s_coach_name" label="教练姓名" min-width="125" show-overflow-tooltip></el-table-column>
								<el-table-column prop="s_coach_phone" label="教练手机" min-width="125" show-overflow-tooltip></el-table-column>
								
								<el-table-column prop="appoint_time_list" label="预约时间" min-width="205">
									<template scope="scope">
										<span>{{ scope.row.appoint_time_date }}</span> <br/>
										<span style="color:red">{{ scope.row.appoint_time }}</span>  
									</template>
								</el-table-column>
								<el-table-column prop="dc_money" label="支付金额(元)" min-width="130" show-overflow-tooltip></el-table-column>
								<el-table-column prop="i_service_time" label="预约时长" min-width="125" show-overflow-tooltip>
									<template scope="scope">
										<span>{{ scope.row.i_service_time }} 学时</span> <br/>
									</template>
								</el-table-column>
								<el-table-column prop="s_order_no" label="订单号" min-width="180" show-overflow-tooltip></el-table-column>
								<el-table-column prop="s_zhifu_dm" label="交易号" min-width="180" show-overflow-tooltip></el-table-column>
								<el-table-column prop="deal_type" label="支付方式" width="100" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" v-if="scope.row.deal_type == '1'">支付宝</el-tag>
										<el-tag type="warning" v-else-if="scope.row.deal_type == '2'">线下</el-tag>
										<el-tag type="success" v-else-if="scope.row.deal_type == '3'">微信</el-tag>
										<el-tag type="danger" v-else-if="scope.row.deal_type == '4'">银联</el-tag>
										<el-tag type="gray" v-else>未知</el-tag>
									</template>
								</el-table-column>

								<el-table-column prop="dt_order_time" label="下单时间" min-width="180" show-overflow-tooltip></el-table-column>
								<el-table-column label="设置订单状态" fixed="right" width="150" style="margin: 0 auto">
									<template scope="scope">
										<el-dropdown trigger="click">
											<el-button class="el-dropdown-link" size="small" type="primary">设置<i class="el-icon-caret-bottom el-icon--right"></i></el-button>
											<el-dropdown-menu slot="dropdown" style="margin-right: -20px">
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1)">
													<el-dropdown-item >已付款</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 2)">
													<el-dropdown-item >已完成</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 3)">
													<el-dropdown-item >已取消</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1003)">
													<el-dropdown-item >未付款</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1006)">
													<el-dropdown-item >退款中</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1007)">
													<el-dropdown-item >已退款</el-dropdown-item>
												</a>
												<!-- <a @click="handleOrderStatus(scope.row.l_study_order_id, 101)">
													<el-dropdown-item >已删除</el-dropdown-item>
												</a> -->
											</el-dropdown-menu>
										</el-dropdown>
									</template>
								</el-table-column>
								<el-table-column label="操作" fixed="right" width="140">
									<template scope="scope">
										<a title="查看" data-title="查看订单" @click="handlePreview($event, scope.row.l_study_order_id, scope.$index, scope.row)"><i class="el-icon-view"></i></a>
										<a title="删除" style="margin-left:8px;" @click="handleDel (scope.row.l_study_order_id, scope.row.i_status)"><i class="el-icon-delete"></i></a>
									</template>
								</el-table-column>
							</el-table>
						</el-tab-pane>
						
						<el-tab-pane label="已取消" name="third">
							<el-table :data="cancel_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
								<!--<el-table-column type="selection" width="55"></el-table-column>-->
								<el-table-column type="expand" label="展开">
									<template scope="props">
										<el-form label-position="left"  inline class="demo-table-expand">
											<el-form-item label="学员姓名：">
												<span>{{ props.row.s_user_name }}</span>
											</el-form-item>
											<el-form-item label="学员手机：">
												<span>{{ props.row.s_user_phone }}</span>
											</el-form-item>
											<el-form-item label="教练姓名：">
												<span>{{ props.row.s_coach_name }}</span>
											</el-form-item>
											<el-form-item label="教练手机：">
												<span>{{ props.row.s_coach_phone }}</span>
											</el-form-item>
											<el-form-item label="预约科目：">
												<span>{{ props.row.s_lesson_name }}</span>
											</el-form-item>
											<el-form-item label="预约牌照：">
												<span>{{ props.row.s_lisence_name }}</span>
											</el-form-item>
											<el-form-item label="下单时间：">
												<span>{{ props.row.dt_order_time }}</span>
											</el-form-item>
											<el-form-item label="支付时间：">
												<span>{{ props.row.dt_zhifu_time }}</span>
											</el-form-item>
											<el-form-item label="取消类型：">
												<span style="color: red">{{ props.row.cancel_type }}</span>
											</el-form-item>
											<el-form-item label="取消原因：">
												<span style="color: red">{{ props.row.cancel_reason }}</span>
											</el-form-item>
											<el-form-item label="取消时间：">
												<span style="color: red">{{ props.row.cancel_time }}</span>
											</el-form-item>
											<el-form-item label="备注：">
												<span style="color: red">{{ props.row.s_beizhu }}</span>
											</el-form-item>
											
											<el-form-item label="所报驾校：">
												<span>{{ props.row.s_school_name }}</span>
											</el-form-item>
											<el-form-item label="驾校地址：">
												<span>{{ props.row.s_address }}</span>
											</el-form-item>
											<el-form-item label="预约计时：">
												<span>{{ props.row.i_service_time }}小时</span>
											</el-form-item>
										</el-form>
									</template>
								</el-table-column>
								<el-table-column prop="l_study_order_id" label="ID" sortable width="80"></el-table-column> 
								<el-table-column prop="s_school_name" v-if="school_id == ''" label="驾校名称" min-width="160" show-overflow-tooltip></el-table-column>  
								<el-table-column prop="s_user_name" label="学员姓名" min-width="125" show-overflow-tooltip></el-table-column> 
								<el-table-column prop="s_user_phone" label="学员手机" min-width="125" show-overflow-tooltip></el-table-column> 
								<el-table-column prop="s_coach_name" label="教练姓名" min-width="125" show-overflow-tooltip></el-table-column>
								<el-table-column prop="s_coach_phone" label="教练手机" min-width="125" show-overflow-tooltip></el-table-column>
								<el-table-column prop="appoint_time_list" label="预约时间" min-width="205">
									<template scope="scope">
										<span>{{ scope.row.appoint_time_date }}</span> <br/>
										<span style="color:red">{{ scope.row.appoint_time }}</span>  
									</template>
								</el-table-column>
								<el-table-column prop="dc_money" label="支付金额(元)" min-width="130" show-overflow-tooltip></el-table-column>
								<el-table-column prop="i_service_time" label="预约时长" min-width="125" show-overflow-tooltip>
									<template scope="scope">
										<span>{{ scope.row.i_service_time }} 学时</span> <br/>
									</template>
								</el-table-column>
								<el-table-column prop="s_order_no" label="订单号" min-width="180" show-overflow-tooltip></el-table-column>
								<el-table-column prop="s_zhifu_dm" label="交易号" min-width="180" show-overflow-tooltip></el-table-column>
								<el-table-column prop="deal_type" label="支付方式" width="100" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" v-if="scope.row.deal_type == '1'">支付宝</el-tag>
										<el-tag type="warning" v-else-if="scope.row.deal_type == '2'">线下</el-tag>
										<el-tag type="success" v-else-if="scope.row.deal_type == '3'">微信</el-tag>
										<el-tag type="danger" v-else-if="scope.row.deal_type == '4'">银联</el-tag>
										<el-tag type="gray" v-else>未知</el-tag>
									</template>
								</el-table-column>

								<el-table-column prop="dt_order_time" label="下单时间" min-width="180" show-overflow-tooltip></el-table-column>
								<el-table-column label="设置订单状态" fixed="right" width="150" style="margin: 0 auto">
									<template scope="scope">
										<el-dropdown trigger="click">
											<el-button class="el-dropdown-link" size="small" type="primary">设置<i class="el-icon-caret-bottom el-icon--right"></i></el-button>
											<el-dropdown-menu slot="dropdown" style="margin-right: -20px">
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1)">
													<el-dropdown-item >已付款</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 2)">
													<el-dropdown-item >已完成</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 3)">
													<el-dropdown-item >已取消</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1003)">
													<el-dropdown-item >未付款</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1006)">
													<el-dropdown-item >退款中</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1007)">
													<el-dropdown-item >已退款</el-dropdown-item>
												</a>
												<!-- <a @click="handleOrderStatus(scope.row.l_study_order_id, 101)">
													<el-dropdown-item >已删除</el-dropdown-item>
												</a> -->
											</el-dropdown-menu>
										</el-dropdown>
									</template>
								</el-table-column>
								<el-table-column label="操作" fixed="right" width="140">
									<template scope="scope">
										<a title="查看" data-title="查看订单" @click="handlePreview($event, scope.row.l_study_order_id, scope.$index, scope.row)"><i class="el-icon-view"></i></a>
										<a title="删除" style="margin-left:8px;" @click="handleDel (scope.row.l_study_order_id, scope.row.i_status)"><i class="el-icon-delete"></i></a>
									</template>
								</el-table-column>
							</el-table>
						</el-tab-pane>

						<el-tab-pane label="退款中" name="fourth">
							<el-table :data="refunding_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
								<!--<el-table-column type="selection" width="55"></el-table-column>-->
								<el-table-column type="expand" label="展开">
									<template scope="props">
										<el-form label-position="left"  inline class="demo-table-expand">
											<el-form-item label="学员姓名：">
												<span>{{ props.row.s_user_name }}</span>
											</el-form-item>
											<el-form-item label="学员手机：">
												<span>{{ props.row.s_user_phone }}</span>
											</el-form-item>
											<el-form-item label="教练姓名：">
												<span>{{ props.row.s_coach_name }}</span>
											</el-form-item>
											<el-form-item label="教练手机：">
												<span>{{ props.row.s_coach_phone }}</span>
											</el-form-item>
											<el-form-item label="预约科目：">
												<span>{{ props.row.s_lesson_name }}</span>
											</el-form-item>
											<el-form-item label="预约牌照：">
												<span>{{ props.row.s_lisence_name }}</span>
											</el-form-item>
											<el-form-item label="下单时间：">
												<span>{{ props.row.dt_order_time }}</span>
											</el-form-item>
											<el-form-item label="支付时间：">
												<span>{{ props.row.dt_zhifu_time }}</span>
											</el-form-item>
											<el-form-item label="所报驾校：">
												<span>{{ props.row.s_school_name }}</span>
											</el-form-item>
											<el-form-item label="驾校地址：">
												<span>{{ props.row.s_address }}</span>
											</el-form-item>
											<el-form-item label="预约计时：">
												<span>{{ props.row.i_service_time }}小时</span>
											</el-form-item>
										</el-form>
									</template>
								</el-table-column>
								<el-table-column prop="l_study_order_id" label="ID" sortable width="80"></el-table-column> 
								<el-table-column prop="s_school_name" v-if="school_id == ''" label="驾校名称" min-width="160" show-overflow-tooltip></el-table-column>  
								<el-table-column prop="s_user_name" label="学员姓名" min-width="125" show-overflow-tooltip></el-table-column> 
								<el-table-column prop="s_user_phone" label="学员手机" min-width="125" show-overflow-tooltip></el-table-column> 
								<el-table-column prop="s_coach_name" label="教练姓名" min-width="125" show-overflow-tooltip></el-table-column>
								<el-table-column prop="s_coach_phone" label="教练手机" min-width="125" show-overflow-tooltip></el-table-column>
								<el-table-column prop="appoint_time_list" label="预约时间" min-width="205">
									<template scope="scope">
										<span>{{ scope.row.appoint_time_date }}</span> <br/>
										<span style="color:red">{{ scope.row.appoint_time }}</span>  
									</template>
								</el-table-column>
								<el-table-column prop="dc_money" label="支付金额(元)" min-width="130" show-overflow-tooltip></el-table-column>
								<el-table-column prop="i_service_time" label="预约时长" min-width="125" show-overflow-tooltip>
									<template scope="scope">
										<span>{{ scope.row.i_service_time }} 学时</span> <br/>
									</template>
								</el-table-column>
								<el-table-column prop="s_order_no" label="订单号" min-width="180" show-overflow-tooltip></el-table-column>
								<el-table-column prop="s_zhifu_dm" label="交易号" min-width="180" show-overflow-tooltip></el-table-column>
								
								<el-table-column prop="deal_type" label="支付方式" width="100" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" v-if="scope.row.deal_type == '1'">支付宝</el-tag>
										<el-tag type="warning" v-else-if="scope.row.deal_type == '2'">线下</el-tag>
										<el-tag type="success" v-else-if="scope.row.deal_type == '3'">微信</el-tag>
										<el-tag type="danger" v-else-if="scope.row.deal_type == '4'">银联</el-tag>
										<el-tag type="gray" v-else>未知</el-tag>
									</template>
								</el-table-column>
								<el-table-column prop="dt_order_time" label="下单时间" min-width="180" show-overflow-tooltip></el-table-column>
								<el-table-column label="设置订单状态" fixed="right" width="150" style="margin: 0 auto">
									<template scope="scope">
										<el-dropdown trigger="click">
											<el-button class="el-dropdown-link" size="small" type="primary">设置<i class="el-icon-caret-bottom el-icon--right"></i></el-button>
											<el-dropdown-menu slot="dropdown" style="margin-right: -20px">
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1)">
													<el-dropdown-item >已付款</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 2)">
													<el-dropdown-item >已完成</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 3)">
													<el-dropdown-item >已取消</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1003)">
													<el-dropdown-item >未付款</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1006)">
													<el-dropdown-item >退款中</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1007)">
													<el-dropdown-item >已退款</el-dropdown-item>
												</a>
												<!-- <a @click="handleOrderStatus(scope.row.l_study_order_id, 101)">
													<el-dropdown-item >已删除</el-dropdown-item>
												</a> -->
											</el-dropdown-menu>
										</el-dropdown>
									</template>
								</el-table-column>
								<el-table-column label="操作" fixed="right" width="140">
									<template scope="scope">
										<a title="查看" data-title="查看订单" @click="handlePreview($event, scope.row.l_study_order_id, scope.$index, scope.row)"><i class="el-icon-view"></i></a>
										<a title="删除" style="margin-left:8px;" @click="handleDel (scope.row.l_study_order_id, scope.row.i_status)"><i class="el-icon-delete"></i></a>
									</template>
								</el-table-column>
							</el-table>
						</el-tab-pane>

						<el-tab-pane label="已退款" name="seventh">
							<el-table :data="refunded_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
								<!--<el-table-column type="selection" width="55"></el-table-column>-->
								<el-table-column type="expand" label="展开">
									<template scope="props">
										<el-form label-position="left"  inline class="demo-table-expand">
											<el-form-item label="学员姓名：">
												<span>{{ props.row.s_user_name }}</span>
											</el-form-item>
											<el-form-item label="学员手机：">
												<span>{{ props.row.s_user_phone }}</span>
											</el-form-item>
											<el-form-item label="教练姓名：">
												<span>{{ props.row.s_coach_name }}</span>
											</el-form-item>
											<el-form-item label="教练手机：">
												<span>{{ props.row.s_coach_phone }}</span>
											</el-form-item>
											<el-form-item label="预约科目：">
												<span>{{ props.row.s_lesson_name }}</span>
											</el-form-item>
											<el-form-item label="预约牌照：">
												<span>{{ props.row.s_lisence_name }}</span>
											</el-form-item>
											<el-form-item label="下单时间：">
												<span>{{ props.row.dt_order_time }}</span>
											</el-form-item>
											<el-form-item label="支付时间：">
												<span>{{ props.row.dt_zhifu_time }}</span>
											</el-form-item>
											<el-form-item label="所报驾校：">
												<span>{{ props.row.s_school_name }}</span>
											</el-form-item>
											<el-form-item label="驾校地址：">
												<span>{{ props.row.s_address }}</span>
											</el-form-item>
											<el-form-item label="预约计时：">
												<span>{{ props.row.i_service_time }}小时</span>
											</el-form-item>
										</el-form>
									</template>
								</el-table-column>
								<el-table-column prop="l_study_order_id" label="ID" sortable width="80"></el-table-column> 
								<el-table-column prop="s_school_name" v-if="school_id == ''" label="驾校名称" min-width="160" show-overflow-tooltip></el-table-column>  
								<el-table-column prop="s_user_name" label="学员姓名" min-width="125" show-overflow-tooltip></el-table-column> 
								<el-table-column prop="s_user_phone" label="学员手机" min-width="125" show-overflow-tooltip></el-table-column> 
								<el-table-column prop="s_coach_name" label="教练姓名" min-width="125" show-overflow-tooltip></el-table-column>
								<el-table-column prop="s_coach_phone" label="教练手机" min-width="125" show-overflow-tooltip></el-table-column>
								<el-table-column prop="appoint_time_list" label="预约时间" min-width="205">
									<template scope="scope">
										<span>{{ scope.row.appoint_time_date }}</span> <br/>
										<span style="color:red">{{ scope.row.appoint_time }}</span>  
									</template>
								</el-table-column>
								<el-table-column prop="dc_money" label="支付金额(元)" min-width="130" show-overflow-tooltip></el-table-column>
								<el-table-column prop="i_service_time" label="预约时长" min-width="125" show-overflow-tooltip>
									<template scope="scope">
										<span>{{ scope.row.i_service_time }} 学时</span> <br/>
									</template>
								</el-table-column>
								<el-table-column prop="s_order_no" label="订单号" min-width="180" show-overflow-tooltip></el-table-column>
								<el-table-column prop="s_zhifu_dm" label="交易号" min-width="180" show-overflow-tooltip></el-table-column>
								<el-table-column prop="deal_type" label="支付方式" width="100" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" v-if="scope.row.deal_type == '1'">支付宝</el-tag>
										<el-tag type="warning" v-else-if="scope.row.deal_type == '2'">线下</el-tag>
										<el-tag type="success" v-else-if="scope.row.deal_type == '3'">微信</el-tag>
										<el-tag type="danger" v-else-if="scope.row.deal_type == '4'">银联</el-tag>
										<el-tag type="gray" v-else>未知</el-tag>
									</template>
								</el-table-column>

								<el-table-column prop="dt_order_time" label="下单时间" min-width="180" show-overflow-tooltip></el-table-column>
								<el-table-column label="设置订单状态" fixed="right" width="150" style="margin: 0 auto">
									<template scope="scope">
										<el-dropdown trigger="click">
											<el-button class="el-dropdown-link" size="small" type="primary">设置<i class="el-icon-caret-bottom el-icon--right"></i></el-button>
											<el-dropdown-menu slot="dropdown" style="margin-right: -20px">
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1)">
													<el-dropdown-item >已付款</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 2)">
													<el-dropdown-item >已完成</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 3)">
													<el-dropdown-item >已取消</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1003)">
													<el-dropdown-item >未付款</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1006)">
													<el-dropdown-item >退款中</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1007)">
													<el-dropdown-item >已退款</el-dropdown-item>
												</a>
												<!-- <a @click="handleOrderStatus(scope.row.l_study_order_id, 101)">
													<el-dropdown-item >已删除</el-dropdown-item>
												</a> -->
											</el-dropdown-menu>
										</el-dropdown>
									</template>
								</el-table-column>
								<el-table-column label="操作" fixed="right" width="140">
									<template scope="scope">
										<a title="查看" data-title="查看订单" @click="handlePreview($event, scope.row.l_study_order_id, scope.$index, scope.row)"><i class="el-icon-view"></i></a>
										<a title="删除" style="margin-left:8px;" @click="handleDel (scope.row.l_study_order_id, scope.row.i_status)"><i class="el-icon-delete"></i></a>
									</template>
								</el-table-column>
							</el-table>
						</el-tab-pane>

						<el-tab-pane label="已删除" name="fifth" v-if="school_id == ''">
							<el-table :data="deleted_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
								<!--<el-table-column type="selection" width="55"></el-table-column>-->
								<el-table-column type="expand" label="展开">
									<template scope="props">
										<el-form label-position="left"  inline class="demo-table-expand">
											<el-form-item label="学员姓名：">
												<span>{{ props.row.s_user_name }}</span>
											</el-form-item>
											<el-form-item label="学员手机：">
												<span>{{ props.row.s_user_phone }}</span>
											</el-form-item>
											<el-form-item label="教练姓名：">
												<span>{{ props.row.s_coach_name }}</span>
											</el-form-item>
											<el-form-item label="教练手机：">
												<span>{{ props.row.s_coach_phone }}</span>
											</el-form-item>
											<el-form-item label="预约科目：">
												<span>{{ props.row.s_lesson_name }}</span>
											</el-form-item>
											<el-form-item label="预约牌照：">
												<span>{{ props.row.s_lisence_name }}</span>
											</el-form-item>
											<el-form-item label="下单时间：">
												<span>{{ props.row.dt_order_time }}</span>
											</el-form-item>
											<el-form-item label="支付时间：">
												<span>{{ props.row.dt_zhifu_time }}</span>
											</el-form-item>
											<el-form-item label="所报驾校：">
												<span>{{ props.row.s_school_name }}</span>
											</el-form-item>
											<el-form-item label="驾校地址：">
												<span>{{ props.row.s_address }}</span>
											</el-form-item>
											<el-form-item label="预约计时：">
												<span>{{ props.row.i_service_time }}小时</span>
											</el-form-item>
										</el-form>
									</template>
								</el-table-column>
								<el-table-column prop="l_study_order_id" label="ID" sortable width="80"></el-table-column> 
								<el-table-column prop="s_school_name" v-if="school_id == ''" label="驾校名称" min-width="160" show-overflow-tooltip></el-table-column>  
								<el-table-column prop="s_user_name" label="学员姓名" min-width="125" show-overflow-tooltip></el-table-column> 
								<el-table-column prop="s_user_phone" label="学员手机" min-width="125" show-overflow-tooltip></el-table-column> 
								<el-table-column prop="s_coach_name" label="教练姓名" min-width="125" show-overflow-tooltip></el-table-column>
								<el-table-column prop="s_coach_phone" label="教练手机" min-width="125" show-overflow-tooltip></el-table-column>
								
								<el-table-column prop="appoint_time_list" label="预约时间" min-width="205">
									<template scope="scope">
										<span>{{ scope.row.appoint_time_date }}</span> <br/>
										<span style="color:red">{{ scope.row.appoint_time }}</span>  
									</template>
								</el-table-column>
								<el-table-column prop="dc_money" label="支付金额(元)" min-width="130" show-overflow-tooltip></el-table-column>
								<el-table-column prop="i_service_time" label="预约时长" min-width="125" show-overflow-tooltip>
									<template scope="scope">
										<span>{{ scope.row.i_service_time }} 学时</span> <br/>
									</template>
								</el-table-column>
								<el-table-column prop="s_order_no" label="订单号" min-width="180" show-overflow-tooltip></el-table-column>
								<el-table-column prop="s_zhifu_dm" label="交易号" min-width="180" show-overflow-tooltip></el-table-column>
								<el-table-column prop="deal_type" label="支付方式" width="100" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" v-if="scope.row.deal_type == '1'">支付宝</el-tag>
										<el-tag type="warning" v-else-if="scope.row.deal_type == '2'">线下</el-tag>
										<el-tag type="success" v-else-if="scope.row.deal_type == '3'">微信</el-tag>
										<el-tag type="danger" v-else-if="scope.row.deal_type == '4'">银联</el-tag>
										<el-tag type="gray" v-else>未知</el-tag>
									</template>
								</el-table-column>

								<el-table-column prop="dt_order_time" label="下单时间" min-width="180" show-overflow-tooltip></el-table-column>
								<el-table-column label="设置订单状态" fixed="right" width="150" style="margin: 0 auto">
									<template scope="scope">
										<el-dropdown trigger="click">
											<el-button class="el-dropdown-link" size="small" type="primary">设置<i class="el-icon-caret-bottom el-icon--right"></i></el-button>
											<el-dropdown-menu slot="dropdown" style="margin-right: -20px">
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1)">
													<el-dropdown-item >已付款</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 2)">
													<el-dropdown-item >已完成</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 3)">
													<el-dropdown-item >已取消</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1003)">
													<el-dropdown-item >未付款</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1006)">
													<el-dropdown-item >退款中</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1007)">
													<el-dropdown-item >已退款</el-dropdown-item>
												</a>
												<!-- <a @click="handleOrderStatus(scope.row.l_study_order_id, 101)">
													<el-dropdown-item >已删除</el-dropdown-item>
												</a> -->
											</el-dropdown-menu>
										</el-dropdown>
									</template>
								</el-table-column>
								<el-table-column label="操作" fixed="right" width="140">
									<template scope="scope">
										<a title="查看" data-title="查看订单" @click="handlePreview($event, scope.row.l_study_order_id, scope.$index, scope.row)"><i class="el-icon-view"></i></a>
										<a title="删除" style="margin-left:8px;" @click="handleDel (scope.row.l_study_order_id, scope.row.i_status)"><i class="el-icon-delete"></i></a>
									</template>
								</el-table-column>
							</el-table>
						</el-tab-pane>

						<el-tab-pane label="已完成" name="sixth">
							<el-table :data="completed_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
								<!--<el-table-column type="selection" width="55"></el-table-column>-->
								<el-table-column type="expand" label="展开">
									<template scope="props">
										<el-form label-position="left"  inline class="demo-table-expand">
											<el-form-item label="学员姓名：">
												<span>{{ props.row.s_user_name }}</span>
											</el-form-item>
											<el-form-item label="学员手机：">
												<span>{{ props.row.s_user_phone }}</span>
											</el-form-item>
											<el-form-item label="教练姓名：">
												<span>{{ props.row.s_coach_name }}</span>
											</el-form-item>
											<el-form-item label="教练手机：">
												<span>{{ props.row.s_coach_phone }}</span>
											</el-form-item>
											<el-form-item label="预约科目：">
												<span>{{ props.row.s_lesson_name }}</span>
											</el-form-item>
											<el-form-item label="预约牌照：">
												<span>{{ props.row.s_lisence_name }}</span>
											</el-form-item>
											<el-form-item label="下单时间：">
												<span>{{ props.row.dt_order_time }}</span>
											</el-form-item>
											<el-form-item label="支付时间：">
												<span>{{ props.row.dt_zhifu_time }}</span>
											</el-form-item>
											<el-form-item label="所报驾校：">
												<span>{{ props.row.s_school_name }}</span>
											</el-form-item>
											<el-form-item label="驾校地址：">
												<span>{{ props.row.s_address }}</span>
											</el-form-item>
											<el-form-item label="预约计时：">
												<span>{{ props.row.i_service_time }}小时</span>
											</el-form-item>
										</el-form>
									</template>
								</el-table-column>
								<el-table-column prop="l_study_order_id" label="ID" sortable width="80"></el-table-column> 
								<el-table-column prop="s_school_name" v-if="school_id == ''" label="驾校名称" min-width="160" show-overflow-tooltip></el-table-column>  
								<el-table-column prop="s_user_name" label="学员姓名" min-width="125" show-overflow-tooltip></el-table-column> 
								<el-table-column prop="s_user_phone" label="学员手机" min-width="125" show-overflow-tooltip></el-table-column> 
								<el-table-column prop="s_coach_name" label="教练姓名" min-width="125" show-overflow-tooltip></el-table-column>
								<el-table-column prop="s_coach_phone" label="教练手机" min-width="125" show-overflow-tooltip></el-table-column>
								<el-table-column prop="appoint_time_list" label="预约时间" min-width="205">
									<template scope="scope">
										<span>{{ scope.row.appoint_time_date }}</span> <br/>
										<span style="color:red">{{ scope.row.appoint_time }}</span>  
									</template>
								</el-table-column>
								<el-table-column prop="dc_money" label="支付金额(元)" min-width="130" show-overflow-tooltip></el-table-column>
								<el-table-column prop="i_service_time" label="预约时长" min-width="125" show-overflow-tooltip>
									<template scope="scope">
										<span>{{ scope.row.i_service_time }} 学时</span> <br/>
									</template>
								</el-table-column>
								<el-table-column prop="s_order_no" label="订单号" min-width="180" show-overflow-tooltip></el-table-column>
								<el-table-column prop="s_zhifu_dm" label="交易号" min-width="180" show-overflow-tooltip></el-table-column>
								<el-table-column prop="deal_type" label="支付方式" width="100" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" v-if="scope.row.deal_type == '1'">支付宝</el-tag>
										<el-tag type="warning" v-else-if="scope.row.deal_type == '2'">线下</el-tag>
										<el-tag type="success" v-else-if="scope.row.deal_type == '3'">微信</el-tag>
										<el-tag type="danger" v-else-if="scope.row.deal_type == '4'">银联</el-tag>
										<el-tag type="gray" v-else>未知</el-tag>
									</template>
								</el-table-column>

								<el-table-column prop="dt_order_time" label="下单时间" min-width="180" show-overflow-tooltip></el-table-column>
								<el-table-column label="设置订单状态" fixed="right" width="150" style="margin: 0 auto">
									<template scope="scope">
										<el-dropdown trigger="click">
											<el-button class="el-dropdown-link" size="small" type="primary">设置<i class="el-icon-caret-bottom el-icon--right"></i></el-button>
											<el-dropdown-menu slot="dropdown" style="margin-right: -20px">
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1)">
													<el-dropdown-item >已付款</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 2)">
													<el-dropdown-item >已完成</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 3)">
													<el-dropdown-item >已取消</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1003)">
													<el-dropdown-item >未付款</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1006)">
													<el-dropdown-item >退款中</el-dropdown-item>
												</a>
												<a @click="handleOrderStatus(scope.row.l_study_order_id, 1007)">
													<el-dropdown-item >已退款</el-dropdown-item>
												</a>
												<!-- <a @click="handleOrderStatus(scope.row.l_study_order_id, 101)">
													<el-dropdown-item >已删除</el-dropdown-item>
												</a> -->
											</el-dropdown-menu>
										</el-dropdown>
									</template>
								</el-table-column>
								<el-table-column label="操作" fixed="right" width="140">
									<template scope="scope">
										<a title="查看" data-title="查看订单" @click="handlePreview($event, scope.row.l_study_order_id, scope.$index, scope.row)"><i class="el-icon-view"></i></a>
										<a title="删除" style="margin-left:8px;" @click="handleDel (scope.row.l_study_order_id, scope.row.i_status)"><i class="el-icon-delete"></i></a>
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
			</el-tabs>
		</div>
	</div>
</div>
<script>
	var school_id = "<?php echo $school_id; ?>";
	var vm = new Vue({
		el: '#app',
		data: {
			refreshstatus: false,
			fullscreenLoading: true,
			dialogTableVisible: false,
			ceshi: '',
			list: [],
			all_list: [],
			unpaid_list: [],
			cancel_list: [],
			refunding_list: [],
			deleted_list: [],
			completed_list: [],
			refunded_list: [],
			multipleSelection: [],
			list_url: "<?php echo base_url('order/timingorderajax'); ?>",
			del_url: "<?php echo base_url('order/deltimingajax'); ?>",
			preview_url: "<?php echo base_url('order/timingorderpreview'); ?>",
			add_url: "<?php echo base_url('order/addtiming'); ?>",
			line_url: "<?php echo base_url('order/timing?ot=line'); ?>", 
			set_url: "<?php echo base_url('order/setOrderStatusAjax'); ?>",
			currentPage: 1,
            page_sizes: [10, 20, 30, 50, 100],
            page_size: 10,
            pagenum: 0,
            count: 0,
			total_service_time: 0,
			payActive: "first",
			activeName: "zero",
			order_status: 'all',
			search: {
				deal_type: "",
				keywords: ''
			}

		},
		created: function() {
			var filters = {'p': this.currentPage, 'pt':this.search.deal_type, 'os': this.order_status,'keywords':this.search.keywords, 's': this.page_size};
			this.listAjax(filters);
		},
		methods: {
			handleOrderStatus: function(id, status) {
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
							var filters = {'p': vm.currentPage, 'os': vm.order_status, 's': vm.page_size};
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
				// window.history.pushState(null, null, '?p='+ this.currentPage+'&pt='+this.search.deal_type+'&os='+this.order_status+'&s='+this.page_size);
				var filters = {'p': this.currentPage, 'pt':this.search.deal_type, 'os': this.order_status,'keywords':this.search.keywords, 's': this.page_size};
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
								case 'deleted':
									vm.deleted_list = data.data.list;
									break;
								case 'completed':
									vm.completed_list = data.data.list;
									break;
								case 'refunded':
									vm.refunded_list = data.data.list;
									break;
								default:
									vm.all_list = data.data.list;
									break;
							}
							vm.total_service_time = data.data.total_service_time;
							vm.currentPage = data.data.p;
							vm.count = data.data.count;
							vm.pagenum = data.data.pagenum;
						}
					},
					error: function() {
						vm.fullscreenLoading = false;
						vm.refreshstatus = false;
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				});
			},
			delAjax: function(id, status) {
				$.ajax({
					type: 'post',
					url: this.del_url,
					data: {id:id, status: status},
					dataType:"json",
					success: function(data) {
						vm.fullscreenLoading = false;			
						if(data.code == 200) {
							var filters = {'p': vm.currentPage, 'os': vm.order_status, 's': vm.page_size};
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
			onSubmit: function() {
				var filters = {'p': this.currentPage, 'pt':this.search.deal_type, 'os': this.order_status,'keywords':this.search.keywords, 's': this.page_size};
				vm.listAjax(filters);
			},               
			handleSelectionChange: function () {
				var filters = {'p': this.currentPage, 'pt':this.search.deal_type, 'os': this.order_status,'keywords':this.search.keywords, 's': this.page_size};
                vm.listAjax(filters);
            },
			// handlePayTypeClick: function() {
			// 	console.log(this.payActive)
			// 	if(this.payActive == 'second') {
			// 		location.href=this.line_url;
			// 	}
			// },
			handleSizeChange: function (size) {
                this.page_size = size;
				var filters = {'p': this.currentPage, 'pt':vm.search.deal_type, 'os': vm.order_status,'keywords':vm.search.keywords, 's': this.page_size};
                this.listAjax(filters);
            },
			handleDel: function(id, status) {
				this.$confirm('此操作将永久删除该文件, 是否继续?', '提示', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning',
					callback: function(action) {
						if(action == 'confirm') {
							vm.delAjax(id, status);
							// rows.splice(index, 1);
							// vm.messageNotice('success', '删除成功!');
						} else {
							return false;
						}
					}
				});
			},
			handleCurrentChange: function (val) {
                this.refreshstatus = true;
                this.currentPage = val;
                window.history.pushState(null, null, '?p='+val+'&pt='+this.search.deal_type+'&os='+this.order_status+'&keywords='+this.search.keywords+'&s='+this.page_size);
                var filters = {"p": this.currentPage, 'pt':vm.search.deal_type, 'os': vm.order_status, "keywords": this.search.keywords, 's': this.page_size};
                vm.listAjax(filters);
            },
			handlePreview: function(e, id, index, row) {
				this.showLayer(e, '60%', 'lb', this.preview_url+'?id='+id);
			},
			filterTag: function(value, row) {
				return row.i_status == value;
			},
			handleRefresh: function() {
				this.refreshstatus = true;
                var filters = {"p": this.currentPage, 'os': vm.order_status, "keywords": this.search.keywords, 's': this.page_size};
				this.listAjax(filters);
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