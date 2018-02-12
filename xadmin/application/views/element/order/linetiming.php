<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">
		<div class="gx-iframe-content">
			<el-tabs v-model="payActive" type="card" type="border-card" @tab-click="handlePayTypeClick">
				<el-tab-pane label="线上支付" name="first"></el-tab-pane>
				<el-tab-pane label="线下支付" name="second">
					
					<!--搜索-->
					<div class="gx-search">
						<el-form :inline="true" :model="formSearch" class="demo-form-inline">
							<el-form-item label="学员号码">
								<el-input v-model="formSearch.user" placeholder="请输入学员号码"></el-input>
							</el-form-item>
							<el-form-item label="支付方式">
								<el-select v-model="formSearch.region" placeholder="请选择支付方式">
								<el-option label="支付宝" value="1"></el-option>
								<el-option label="微信" value="3"></el-option>
								<el-option label="银联" value="4"></el-option>
								</el-select>
							</el-form-item><el-form-item>
								<el-button type="primary" @click="onSubmit">查询</el-button>
							</el-form-item>
						</el-form>
					</div>

					<el-tabs v-model="activeName" @tab-click="handleClick">
						<el-tab-pane label="已付款" name="first">
							
							<el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
								<!--<el-table-column type="selection"width="55"></el-table-column>-->
									<el-table-column type="expand" label="展开">
										<template scope="props">
											<el-form label-position="left" v-if="props.row.school_info" inline class="demo-table-expand">
												<el-form-item label="所报驾校：">
													<span>{{ props.row.school_info.s_school_name }}</span>
												</el-form-item>
												<el-form-item label="驾校地址：">
													<span>{{ props.row.school_info.s_address }}</span>
												</el-form-item>
											</el-form>
											<el-form label-position="left" v-if="props.row.user_info" inline class="demo-table-expand">
												<el-form-item label="学员性别：">
													<el-tag type="primary" v-if="props.row.user_info.sex === '1'" close-transition>男</el-tag>
													<el-tag type="danger" v-else-if="props.row.user_info.sex === '2'" close-transition>女</el-tag>
													<el-tag type="success" v-else close-transition>未知</el-tag>
												</el-form-item>
												<el-form-item label="学员年龄：">
													<span>{{ props.row.user_info.age }}</span>
												</el-form-item>
												<el-form-item label="身份证：">
													<span>{{ props.row.user_info.identity_id }}</span>
												</el-form-item>
												<el-form-item label="学员地址：">
													<span>{{ props.row.user_info.address }}</span>
												</el-form-item>
											</el-form>
											
										</template>
									</el-table-column>
								<el-table-column prop="id" label="ID" sortable width="80"></el-table-column> 
								<el-table-column prop="so_username" label="用户名 / 手机号" width="200" show-overflow-tooltip>
									<template scope="scope">
										<span>{{ scope.row.so_username }}</span> / 
										<el-tag type="danger" close-transition>{{ scope.row.so_phone }}</el-tag>
									</template>
								</el-table-column> 
								<el-table-column prop="so_shifts_id" label="班制名称" width="250" show-overflow-tooltip>
									<template scope="scope">
											<el-tag type="primary" close-transition v-if="scope.row.shift_info">{{ scope.row.shift_info.sh_title }}</el-tag> / 
										{{ scope.row.so_licence }}
									</template>
								</el-table-column>

								<el-table-column label="价格">
									<el-table-column prop="lesson_name" label="原始价 / 最终价 / 实际支付" width="220" show-overflow-tooltip>
										<template scope="scope">
											<span style="color: #999">{{scope.row.so_original_price}}</span> / 
											<span style="color: #13ce66">{{ scope.row.so_final_price }}</span> / 
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

								<el-table-column prop="so_pay_type" label="支付方式" width="100" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" v-if="scope.row.so_pay_type == '1'">支付宝</el-tag>
										<el-tag type="gray" v-else-if="scope.row.so_pay_type == '2'">线下</el-tag>
										<el-tag type="success" v-else-if="scope.row.so_pay_type == '3'">微信</el-tag>
										<el-tag type="danger" v-else-if="scope.row.so_pay_type == '4'">银联</el-tag>
										<el-tag type="gray" v-else>未知</el-tag>
									</template>
								</el-table-column>

								<el-table-column prop="addtime" label="下单时间" show-overflow-tooltip></el-table-column>
								<el-table-column label="操作" fixed="right" width="100">
									<template scope="scope">
										<el-button size="small" type="text" data-title="预览驾校" @click="handlePreview($event, scope.row.l_user_id, scope.$index, scope.row)">操作</el-button>
									</template>
								</el-table-column>
							</el-table>

						</el-tab-pane>

						<el-tab-pane label="未付款" name="second">
							<el-table :data="unpaid_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
								<!--<el-table-column type="selection"width="55"></el-table-column>-->
									<el-table-column type="expand" label="展开">
										<template scope="props">
											<el-form label-position="left" v-if="props.row.school_info" inline class="demo-table-expand">
												<el-form-item label="所报驾校：">
													<span>{{ props.row.school_info.s_school_name }}</span>
												</el-form-item>
												<el-form-item label="驾校地址：">
													<span>{{ props.row.school_info.s_address }}</span>
												</el-form-item>
											</el-form>
											<el-form label-position="left" v-if="props.row.user_info" inline class="demo-table-expand">
												<el-form-item label="学员性别：">
													<el-tag type="primary" v-if="props.row.user_info.sex === '1'" close-transition>男</el-tag>
													<el-tag type="danger" v-else-if="props.row.user_info.sex === '2'" close-transition>女</el-tag>
													<el-tag type="success" v-else close-transition>未知</el-tag>
												</el-form-item>
												<el-form-item label="学员年龄：">
													<span>{{ props.row.user_info.age }}</span>
												</el-form-item>
												<el-form-item label="身份证：">
													<span>{{ props.row.user_info.identity_id }}</span>
												</el-form-item>
												<el-form-item label="学员地址：">
													<span>{{ props.row.user_info.address }}</span>
												</el-form-item>
											</el-form>
											
										</template>
									</el-table-column>
								<el-table-column prop="id" label="ID" sortable width="80"></el-table-column> 
								<el-table-column prop="so_username" label="用户名 / 手机号" width="200" show-overflow-tooltip>
									<template scope="scope">
										<span>{{ scope.row.so_username }}</span> / 
										<el-tag type="danger" close-transition>{{ scope.row.so_phone }}</el-tag>
									</template>
								</el-table-column> 
								<el-table-column prop="so_shifts_id" label="班制名称" width="250" show-overflow-tooltip>
									<template scope="scope">
											<el-tag type="primary" close-transition v-if="scope.row.shift_info">{{ scope.row.shift_info.sh_title }}</el-tag> / 
										{{ scope.row.so_licence }}
									</template>
								</el-table-column>

								<el-table-column label="价格">
									<el-table-column prop="lesson_name" label="原始价 / 最终价 / 实际支付" width="220" show-overflow-tooltip>
										<template scope="scope">
											<span style="color: #999">{{scope.row.so_original_price}}</span> / 
											<span style="color: #13ce66">{{ scope.row.so_final_price }}</span> / 
											<el-tag type="danger" v-if="scope.row.so_final_price == 0" close-transition>{{scope.row.so_total_price}}</el-tag>
											<el-tag type="danger" v-else close-transition>{{scope.row.so_final_price}}</el-tag> 
										</template>
									</el-table-column>
									<el-table-column prop="coupon_name" label="优惠价" width="90" show-overflow-tooltip>
										<template scope="scope">
											<span>{{ scope.row.coupon_value }}</span>
										</template>
									</el-table-column>
								</el-table-column>

								<el-table-column prop="so_pay_type" label="支付方式" width="100" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" v-if="scope.row.so_pay_type == '1'">支付宝</el-tag>
										<el-tag type="gray" v-else-if="scope.row.so_pay_type == '2'">线下</el-tag>
										<el-tag type="success" v-else-if="scope.row.so_pay_type == '3'">微信</el-tag>
										<el-tag type="danger" v-else-if="scope.row.so_pay_type == '4'">银联</el-tag>
										<el-tag type="gray" v-else>未知</el-tag>
									</template>
								</el-table-column>

								<el-table-column prop="addtime" label="下单时间" show-overflow-tooltip></el-table-column>
								<el-table-column label="操作" fixed="right" width="100">
									<template scope="scope">
										<el-button size="small" type="text" data-title="预览驾校" @click="handlePreview($event, scope.row.l_user_id, scope.$index, scope.row)">操作</el-button>
									</template>
								</el-table-column>
							</el-table>
						</el-tab-pane>
						
						<el-tab-pane label="已取消" name="third">
							<el-table :data="cancel_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
								<!--<el-table-column type="selection"width="55"></el-table-column>-->
									<el-table-column type="expand" label="展开">
										<template scope="props">
											<el-form label-position="left" v-if="props.row.school_info" inline class="demo-table-expand">
												<el-form-item label="所报驾校：">
													<span>{{ props.row.school_info.s_school_name }}</span>
												</el-form-item>
												<el-form-item label="驾校地址：">
													<span>{{ props.row.school_info.s_address }}</span>
												</el-form-item>
											</el-form>
											<el-form label-position="left" v-if="props.row.user_info" inline class="demo-table-expand">
												<el-form-item label="学员性别：">
													<el-tag type="primary" v-if="props.row.user_info.sex === '1'" close-transition>男</el-tag>
													<el-tag type="danger" v-else-if="props.row.user_info.sex === '2'" close-transition>女</el-tag>
													<el-tag type="success" v-else close-transition>未知</el-tag>
												</el-form-item>
												<el-form-item label="学员年龄：">
													<span>{{ props.row.user_info.age }}</span>
												</el-form-item>
												<el-form-item label="身份证：">
													<span>{{ props.row.user_info.identity_id }}</span>
												</el-form-item>
												<el-form-item label="学员地址：">
													<span>{{ props.row.user_info.address }}</span>
												</el-form-item>
											</el-form>
											
										</template>
									</el-table-column>
								<el-table-column prop="id" label="ID" sortable width="80"></el-table-column> 
								<el-table-column prop="so_username" label="用户名 / 手机号" width="200" show-overflow-tooltip>
									<template scope="scope">
										<span>{{ scope.row.so_username }}</span> / 
										<el-tag type="danger" close-transition>{{ scope.row.so_phone }}</el-tag>
									</template>
								</el-table-column> 
								<el-table-column prop="so_shifts_id" label="班制名称" width="250" show-overflow-tooltip>
									<template scope="scope">
											<el-tag type="primary" close-transition v-if="scope.row.shift_info">{{ scope.row.shift_info.sh_title }}</el-tag> / 
										{{ scope.row.so_licence }}
									</template>
								</el-table-column>

								<el-table-column label="价格">
									<el-table-column prop="lesson_name" label="原始价 / 最终价 / 实际支付" width="220" show-overflow-tooltip>
										<template scope="scope">
											<span style="color: #999">{{scope.row.so_original_price}}</span> / 
											<span style="color: #13ce66">{{ scope.row.so_final_price }}</span> / 
											<el-tag type="danger" v-if="scope.row.so_final_price == 0" close-transition>{{scope.row.so_total_price}}</el-tag>
											<el-tag type="danger" v-else close-transition>{{scope.row.so_final_price}}</el-tag> 
										</template>
									</el-table-column>
									<el-table-column prop="coupon_name" label="优惠价" width="90" show-overflow-tooltip>
										<template scope="scope">
											<span>{{ scope.row.coupon_value }}</span>
										</template>
									</el-table-column>
								</el-table-column>

								<el-table-column prop="so_pay_type" label="支付方式" width="100" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" v-if="scope.row.so_pay_type == '1'">支付宝</el-tag>
										<el-tag type="gray" v-else-if="scope.row.so_pay_type == '2'">线下</el-tag>
										<el-tag type="success" v-else-if="scope.row.so_pay_type == '3'">微信</el-tag>
										<el-tag type="danger" v-else-if="scope.row.so_pay_type == '4'">银联</el-tag>
										<el-tag type="gray" v-else>未知</el-tag>
									</template>
								</el-table-column>

								<el-table-column prop="addtime" label="下单时间" show-overflow-tooltip></el-table-column>
								<el-table-column label="操作" fixed="right" width="100">
									<template scope="scope">
										<el-button size="small" type="text" data-title="预览驾校" @click="handlePreview($event, scope.row.l_user_id, scope.$index, scope.row)">操作</el-button>
									</template>
								</el-table-column>
							</el-table>
						</el-tab-pane>

						<el-tab-pane label="退款中" name="fourth">
							<el-table :data="refunding_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
								<!--<el-table-column type="selection"width="55"></el-table-column>-->
									<el-table-column type="expand" label="展开">
										<template scope="props">
											<el-form label-position="left" v-if="props.row.school_info" inline class="demo-table-expand">
												<el-form-item label="所报驾校：">
													<span>{{ props.row.school_info.s_school_name }}</span>
												</el-form-item>
												<el-form-item label="驾校地址：">
													<span>{{ props.row.school_info.s_address }}</span>
												</el-form-item>
											</el-form>
											<el-form label-position="left" v-if="props.row.user_info" inline class="demo-table-expand">
												<el-form-item label="学员性别：">
													<el-tag type="primary" v-if="props.row.user_info.sex === '1'" close-transition>男</el-tag>
													<el-tag type="danger" v-else-if="props.row.user_info.sex === '2'" close-transition>女</el-tag>
													<el-tag type="success" v-else close-transition>未知</el-tag>
												</el-form-item>
												<el-form-item label="学员年龄：">
													<span>{{ props.row.user_info.age }}</span>
												</el-form-item>
												<el-form-item label="身份证：">
													<span>{{ props.row.user_info.identity_id }}</span>
												</el-form-item>
												<el-form-item label="学员地址：">
													<span>{{ props.row.user_info.address }}</span>
												</el-form-item>
											</el-form>
											
										</template>
									</el-table-column>
								<el-table-column prop="id" label="ID" sortable width="80"></el-table-column> 
								<el-table-column prop="so_username" label="用户名 / 手机号" width="200" show-overflow-tooltip>
									<template scope="scope">
										<span>{{ scope.row.so_username }}</span> / 
										<el-tag type="danger" close-transition>{{ scope.row.so_phone }}</el-tag>
									</template>
								</el-table-column> 
								<el-table-column prop="so_shifts_id" label="班制名称" width="250" show-overflow-tooltip>
									<template scope="scope">
											<el-tag type="primary" close-transition v-if="scope.row.shift_info">{{ scope.row.shift_info.sh_title }}</el-tag> / 
										{{ scope.row.so_licence }}
									</template>
								</el-table-column>

								<el-table-column label="价格">
									<el-table-column prop="lesson_name" label="原始价 / 最终价 / 实际支付" width="220" show-overflow-tooltip>
										<template scope="scope">
											<span style="color: #999">{{scope.row.so_original_price}}</span> / 
											<span style="color: #13ce66">{{ scope.row.so_final_price }}</span> / 
											<el-tag type="danger" v-if="scope.row.so_final_price == 0" close-transition>{{scope.row.so_total_price}}</el-tag>
											<el-tag type="danger" v-else close-transition>{{scope.row.so_final_price}}</el-tag> 
										</template>
									</el-table-column>
									<el-table-column prop="coupon_name" label="优惠价" width="90" show-overflow-tooltip>
										<template scope="scope">
											<span>{{ scope.row.coupon_value }}</span>
										</template>
									</el-table-column>
								</el-table-column>

								<el-table-column prop="so_pay_type" label="支付方式" width="100" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" v-if="scope.row.so_pay_type == '1'">支付宝</el-tag>
										<el-tag type="gray" v-else-if="scope.row.so_pay_type == '2'">线下</el-tag>
										<el-tag type="success" v-else-if="scope.row.so_pay_type == '3'">微信</el-tag>
										<el-tag type="danger" v-else-if="scope.row.so_pay_type == '4'">银联</el-tag>
										<el-tag type="gray" v-else>未知</el-tag>
									</template>
								</el-table-column>

								<el-table-column prop="addtime" label="下单时间" show-overflow-tooltip></el-table-column>
								<el-table-column label="操作" fixed="right" width="100">
									<template scope="scope">
										<el-button size="small" type="text" data-title="预览驾校" @click="handlePreview($event, scope.row.l_user_id, scope.$index, scope.row)">操作</el-button>
									</template>
								</el-table-column>
							</el-table>
						</el-tab-pane>

						<el-tab-pane label="已删除" name="fifth">
							<el-table :data="deleted_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
								<!--<el-table-column type="selection"width="55"></el-table-column>-->
									<el-table-column type="expand" label="展开">
										<template scope="props">
											<el-form label-position="left" v-if="props.row.school_info" inline class="demo-table-expand">
												<el-form-item label="所报驾校：">
													<span>{{ props.row.school_info.s_school_name }}</span>
												</el-form-item>
												<el-form-item label="驾校地址：">
													<span>{{ props.row.school_info.s_address }}</span>
												</el-form-item>
											</el-form>
											<el-form label-position="left" v-if="props.row.user_info" inline class="demo-table-expand">
												<el-form-item label="学员性别：">
													<el-tag type="primary" v-if="props.row.user_info.sex === '1'" close-transition>男</el-tag>
													<el-tag type="danger" v-else-if="props.row.user_info.sex === '2'" close-transition>女</el-tag>
													<el-tag type="success" v-else close-transition>未知</el-tag>
												</el-form-item>
												<el-form-item label="学员年龄：">
													<span>{{ props.row.user_info.age }}</span>
												</el-form-item>
												<el-form-item label="身份证：">
													<span>{{ props.row.user_info.identity_id }}</span>
												</el-form-item>
												<el-form-item label="学员地址：">
													<span>{{ props.row.user_info.address }}</span>
												</el-form-item>
											</el-form>
											
										</template>
									</el-table-column>
								<el-table-column prop="id" label="ID" sortable width="80"></el-table-column> 
								<el-table-column prop="so_username" label="用户名 / 手机号" width="200" show-overflow-tooltip>
									<template scope="scope">
										<span>{{ scope.row.so_username }}</span> / 
										<el-tag type="danger" close-transition>{{ scope.row.so_phone }}</el-tag>
									</template>
								</el-table-column> 
								<el-table-column prop="so_shifts_id" label="班制名称" width="250" show-overflow-tooltip>
									<template scope="scope">
											<el-tag type="primary" close-transition v-if="scope.row.shift_info">{{ scope.row.shift_info.sh_title }}</el-tag> / 
										{{ scope.row.so_licence }}
									</template>
								</el-table-column>

								<el-table-column label="价格">
									<el-table-column prop="lesson_name" label="原始价 / 最终价 / 实际支付" width="220" show-overflow-tooltip>
										<template scope="scope">
											<span style="color: #999">{{scope.row.so_original_price}}</span> / 
											<span style="color: #13ce66">{{ scope.row.so_final_price }}</span> / 
											<el-tag type="danger" v-if="scope.row.so_final_price == 0" close-transition>{{scope.row.so_total_price}}</el-tag>
											<el-tag type="danger" v-else close-transition>{{scope.row.so_final_price}}</el-tag> 
										</template>
									</el-table-column>
									<el-table-column prop="coupon_name" label="优惠价" width="90" show-overflow-tooltip>
										<template scope="scope">
											<span>{{ scope.row.coupon_value }}</span>
										</template>
									</el-table-column>
								</el-table-column>

								<el-table-column prop="so_pay_type" label="支付方式" width="100" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" v-if="scope.row.so_pay_type == '1'">支付宝</el-tag>
										<el-tag type="gray" v-else-if="scope.row.so_pay_type == '2'">线下</el-tag>
										<el-tag type="success" v-else-if="scope.row.so_pay_type == '3'">微信</el-tag>
										<el-tag type="danger" v-else-if="scope.row.so_pay_type == '4'">银联</el-tag>
										<el-tag type="gray" v-else>未知</el-tag>
									</template>
								</el-table-column>

								<el-table-column prop="addtime" label="下单时间" show-overflow-tooltip></el-table-column>
								<el-table-column label="操作" fixed="right" width="100">
									<template scope="scope">
										<el-button size="small" type="text" data-title="预览驾校" @click="handlePreview($event, scope.row.l_user_id, scope.$index, scope.row)">操作</el-button>
									</template>
								</el-table-column>
							</el-table>
						</el-tab-pane>

						<el-tab-pane label="已完成" name="sixth">
							<el-table :data="completed_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
								<!--<el-table-column type="selection"width="55"></el-table-column>-->
									<el-table-column type="expand" label="展开">
										<template scope="props">
											<el-form label-position="left" v-if="props.row.school_info" inline class="demo-table-expand">
												<el-form-item label="所报驾校：">
													<span>{{ props.row.school_info.s_school_name }}</span>
												</el-form-item>
												<el-form-item label="驾校地址：">
													<span>{{ props.row.school_info.s_address }}</span>
												</el-form-item>
											</el-form>
											<el-form label-position="left" v-if="props.row.user_info" inline class="demo-table-expand">
												<el-form-item label="学员性别：">
													<el-tag type="primary" v-if="props.row.user_info.sex === '1'" close-transition>男</el-tag>
													<el-tag type="danger" v-else-if="props.row.user_info.sex === '2'" close-transition>女</el-tag>
													<el-tag type="success" v-else close-transition>未知</el-tag>
												</el-form-item>
												<el-form-item label="学员年龄：">
													<span>{{ props.row.user_info.age }}</span>
												</el-form-item>
												<el-form-item label="身份证：">
													<span>{{ props.row.user_info.identity_id }}</span>
												</el-form-item>
												<el-form-item label="学员地址：">
													<span>{{ props.row.user_info.address }}</span>
												</el-form-item>
											</el-form>
											
										</template>
									</el-table-column>
								<el-table-column prop="id" label="ID" sortable width="80"></el-table-column> 
								<el-table-column prop="so_username" label="用户名 / 手机号" width="200" show-overflow-tooltip>
									<template scope="scope">
										<span>{{ scope.row.so_username }}</span> / 
										<el-tag type="danger" close-transition>{{ scope.row.so_phone }}</el-tag>
									</template>
								</el-table-column> 
								<el-table-column prop="so_shifts_id" label="班制名称" width="250" show-overflow-tooltip>
									<template scope="scope">
											<el-tag type="primary" close-transition v-if="scope.row.shift_info">{{ scope.row.shift_info.sh_title }}</el-tag> / 
										{{ scope.row.so_licence }}
									</template>
								</el-table-column>

								<el-table-column label="价格">
									<el-table-column prop="lesson_name" label="原始价 / 最终价 / 实际支付" width="220" show-overflow-tooltip>
										<template scope="scope">
											<span style="color: #999">{{scope.row.so_original_price}}</span> / 
											<span style="color: #13ce66">{{ scope.row.so_final_price }}</span> / 
											<el-tag type="danger" v-if="scope.row.so_final_price == 0" close-transition>{{scope.row.so_total_price}}</el-tag>
											<el-tag type="danger" v-else close-transition>{{scope.row.so_final_price}}</el-tag> 
										</template>
									</el-table-column>
									<el-table-column prop="coupon_name" label="优惠价" width="90" show-overflow-tooltip>
										<template scope="scope">
											<span>{{ scope.row.coupon_value }}</span>
										</template>
									</el-table-column>
								</el-table-column>

								<el-table-column prop="so_pay_type" label="支付方式" width="100" show-overflow-tooltip>
									<template scope="scope">
										<el-tag type="primary" v-if="scope.row.so_pay_type == '1'">支付宝</el-tag>
										<el-tag type="gray" v-else-if="scope.row.so_pay_type == '2'">线下</el-tag>
										<el-tag type="success" v-else-if="scope.row.so_pay_type == '3'">微信</el-tag>
										<el-tag type="danger" v-else-if="scope.row.so_pay_type == '4'">银联</el-tag>
										<el-tag type="gray" v-else>未知</el-tag>
									</template>
								</el-table-column>

								<el-table-column prop="addtime" label="下单时间" show-overflow-tooltip></el-table-column>
								<el-table-column label="操作" fixed="right" width="100">
									<template scope="scope">
										<el-button size="small" type="text" data-title="预览驾校" @click="handlePreview($event, scope.row.l_user_id, scope.$index, scope.row)">操作</el-button>
									</template>
								</el-table-column>
							</el-table>
						</el-tab-pane>
						
					</el-tabs>

					<!--page-->
					<div class="block" style="float: right; margin-top: 10px;">
						<el-pagination
						@current-change="handleCurrentChange"
						:current-page="currentPage"
						layout="total, prev, pager, next, jumper"
						:total="count">
						</el-pagination>
					</div>
				</el-tab-pane>
			</el-tabs>
		</div>
	</div>
</div>
<script>
	Vue.config.devtools = true;
	var vm = new Vue({
		el: '#app',
		data: {
			refreshstatus: false,
			fullscreenLoading: true,
			list: [],
			unpaid_list: [],
			cancel_list: [],
			refunding_list: [],
			deleted_list: [],
			completed_list: [],
			multipleSelection: [],
			list_url: "<?php echo base_url('order/timingorderajax'); ?>",
			del_url: "<?php echo base_url('user/delajax'); ?>",
			edit_url: "<?php echo base_url('user/edit'); ?>",
			add_url: "<?php echo base_url('user/add'); ?>",
			preview_url: "<?php echo base_url('user/preview'); ?>",
			show_url: "<?php echo base_url('user/show'); ?>",
			currentPage: parseInt("<?php echo $p; ?>"),
			pagenum: "<?php echo $pagenum; ?>",
			count: "<?php echo $count; ?>",
			payActive: 'second',
			activeName: "<?php echo $act; ?>",
			ptype: "<?php echo $pt; ?>",
			otype: "<?php echo $ot; ?>",
			formSearch: {
				user: '',
				region: ''
			}

		},
		created: function() {
			this.listAjax(this.currentPage, this.ptype, this.otype);
		},
		methods: {
			onSubmit: function() {
				console.log('submit!');
			},
			handleSelectionChange: function(val) {
				this.multipleSelection = val;
			},
			handlePayTypeClick: function() {
				if(this.payActive == 'first') {
					location.href="<?php echo base_url('order/shifts?ot=online'); ?>";
				}
			},
			handleClick: function(tab, event) {
				switch (this.activeName) {
					case 'first':
						this.ptype = 'paid';
						break;
					case 'second':
						this.ptype = 'unpaid';
						break;
					case 'third':
						this.ptype = 'cancel';
						break;
					case 'fourth':
						this.ptype = 'refunding';
						break;
					case 'fifth':
						this.ptype = 'deleted';
						break;
					case 'sixth':
						this.ptype = 'completed';
						break;
					default:
						this.ptype = 'paid';
						break;
				}
				window.history.pushState(null, null, '?ot='+this.otype+'&pt='+this.ptype+'&act='+this.activeName+'&p=1');
				this.currentPage = 1;
				if(this.list.length == 0 || this.unpaid_list.length == 0 ||  this.cancel_list.length == 0 ||  this.refunding_list.length == 0 ||  this.deleted_list.length == 0 || this.completed_list.length == 0) {
					this.listAjax(this.currentPage, this.ptype, this.otype);
				}
			},
			handleAdd: function(e) {
				this.showLayer(e, '60%', 'rb', this.add_url);
			},
			handleEdit: function(e, id, index, row) {
				this.showLayer(e, '60%', 'lb', this.edit_url+'?id='+id);
			},
			handlePreview: function(e, id, index, row) {
				this.showLayer(e, '480px', 'rb', this.preview_url+'?id='+id);
			},
			handleDel: function(id, index, rows) {
				this.$confirm('此操作将永久删除该文件, 是否继续?', '提示', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning',
					callback: function(action) {
						if(action == 'confirm') {
							vm.delAjax(id);
							rows.splice(index, 1);
							vm.messageNotice('success', '删除成功!');
						} else {
							return false;
						}
					}
				});
				// this.$confirm('此操作将永久删除该文件, 是否继续?', '提示', {
				// 	confirmButtonText: '确定',
				// 	cancelButtonText: '取消',
				// 	type: 'warning'
				// }).then(() => {
				// 	this.delAjax(id);
				// 	rows.splice(index, 1);
				// 	this.messageNotice('success', '删除成功!');
				// }).catch(() => {
				// 	return false;
				// });
			},
			handleCurrentChange: function(val) {
				// console.log("当前页:"+val);
				console.log(this.activeName);
				window.history.pushState(null, null, '?ot='+this.otype+'&pt='+this.ptype+'&act='+this.activeName+'&p='+val);
				this.listAjax(val, this.ptype, this.otype);
			},
			filterTag: function(value, row) {
				return row.i_status == value;
			},
			handleRefresh: function() {
				this.refreshstatus = true;
				this.listAjax(this.currentPage, this.ptype, this.otype);
			},
			listAjax: function(page, ptype, otype) {
				$.ajax({
					type: 'post',
					url: this.list_url+'?pt='+ptype+'&ot='+otype,
					data: {p: page},
					dataType:"json",
					async: true,
					success: function(data) {
						// setTimeout(function() {
						vm.fullscreenLoading = false;			
						// }, 500);
						vm.refreshstatus = false;
						if(data.code == 200) {
							switch (ptype) {
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
								default:
									vm.list = data.data.list;
									break;
							}
							vm.pagenum = data.data.pagenum;
							vm.count = data.data.count;
							vm.currentPage = page;
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
							vm.listAjax(vm.currentPage);
							vm.messageNotice('success', data.msg);			
						}
					},
					error: function() {
						vm.fullscreenLoading = false;									
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				})
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
			handleShow: function(id, status) {
				$.ajax({
					type: 'post',
					url: this.show_url,
					data: {id:id, status:status},
					dataType:"json",
					success: function(data) {		
						if(data.code == 200) {
							vm.listAjax(vm.currentPage);
						} else {
							vm.messageNotice('warning', data.msg);			
						}
					},
					error: function() {							
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				});
			}
			
		}
	})
</script>