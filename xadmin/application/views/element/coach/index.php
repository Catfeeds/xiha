<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">
		<div class="gx-search">
			<el-form :inline="true" :model="search" class="demo-form-inline">
				<el-form-item label="平均星级">
					<el-select v-model="search.star" placeholder="--不限星级--">
						<el-option v-for="item in star_options" :label="item.label" :value="item.value"></el-option>
					</el-select>
				</el-form-item>
				<el-form-item label="认证状态">
					<el-select v-model="search.verify" placeholder="--不限状态--">
						<el-option v-for="item in verify_options" :label="item.label" :value="item.value"></el-option>
					</el-select>
				</el-form-item>
				<el-form-item label="关键词">
					<el-input style="width: 260px" v-model="search.keywords" v-if="school_id == ''" placeholder="ID，姓名，手机号，所属驾校" ></el-input>
					<el-input style="width: 260px" v-model="search.keywords" v-if="school_id != ''" placeholder="ID，姓名，手机号" ></el-input>
				</el-form-item>
				<el-form-item>
					<el-button type="primary" icon="search" @click="handleSearch">搜索</el-button>
				</el-form-item>
			</el-form>
		</div>
		<div class="gx-iframe-content">
			<div class="gx-iframe-operation" style="margin-bottom: 20px; border-radius: 4px;">
				<el-button type="success" style="margin-left: 10px;" @click.active.prevent="handleAdd($event)" id="add" data-title="添加教练"><i class="el-icon-plus"></i> 添加教练</el-button>
				<el-button type="success" type="small" style="float:right; line-height: 38px;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
			</div>

			<el-tabs v-model="activeName" @tab-click="handleClick" v-if="school_id == ''">
    			<el-tab-pane label="未删除" name="first">
					<el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
						<!-- <el-table-column fixed type="selection" width="50"></el-table-column> -->
						<el-table-column type="expand">
							<template scope="props">
								<el-form label-position="left" inline class="demo-table-expand">
									<el-form-item label="姓名：">
										<span>{{ props.row.s_coach_name }}</span>
									</el-form-item>
									<el-form-item label="联系方式：">
										<span>{{ props.row.s_coach_phone }}</span>
									</el-form-item>
									<el-form-item label="教练性别：">
										<el-tag type="success" v-if="props.row.s_coach_sex == '0'">男</el-tag>
										<el-tag type="danger" v-if="props.row.s_coach_sex == '1'">女</el-tag>
									</el-form-item>
									<el-form-item label="教龄：">
										<span>{{ props.row.s_teach_age }}年</span>
									</el-form-item>
									<el-form-item label="教练等级：">
										<el-tag type="warning" v-if="props.row.i_type == '0'">金牌教练</el-tag>
										<el-tag type="primary" v-if="props.row.i_type == '1'">普通教练</el-tag>
										<el-tag type="success" v-if="props.row.i_type == '2'">二级教练</el-tag>
										<el-tag type="success" v-if="props.row.i_type == '5'">二级教练，全国优秀教练员荣誉</el-tag>
										<el-tag type="danger" v-if="props.row.i_type == '3'">三级教练</el-tag>
										<el-tag type="gray" v-if="props.row.i_type == '4'">四级教练</el-tag>
									</el-form-item>
									<el-form-item label="教练车辆：">
										<span>{{ props.row.car_name }}</span>
									</el-form-item>
									<el-form-item label="科二通过率：">
										<span>{{ props.row.lesson2_pass_rate }}%</span>
									</el-form-item>
									<el-form-item label="科三通过率：">
										<span>{{ props.row.lesson3_pass_rate }}%</span>
									</el-form-item>
									<el-form-item label="注册时间：">
										<span>{{ props.row.addtime }}</span>
									</el-form-item>
									<el-form-item label="更新时间：">
										<span>{{ props.row.updatetime }}</span>
									</el-form-item>
									<el-form-item label="教练星级：">
										<el-div style="color:red;" type="danger" v-if="parseInt(props.row.i_coach_star) == 1" close-transition>★</el-div>
										<el-div style="color:red;" type="danger" v-else-if="parseInt(props.row.i_coach_star) == 2" close-transition>★★</el-div>
										<el-div style="color:red;" type="danger" v-else-if="parseInt(props.row.i_coach_star) == 3" close-transition>★★★</el-div>
										<el-div style="color:red;" type="danger" v-else-if="parseInt(props.row.i_coach_star) == 4" close-transition>★★★★</el-div>
										<el-div style="color:red;" type="danger" v-else close-transition>★★★★★</el-div>
									</el-form-item>
								</el-form>
							</template>
						</el-table-column>
						<el-table-column prop="l_coach_id" label="ID" sortable width="80"></el-table-column> 
						<!-- <el-table-column prop="i_order" label="排序" width="80"></el-table-column>  -->
						<el-table-column prop="s_coach_name" label="教练主信息" width="120" show-overflow-tooltip>
							<el-table-column prop="school_name" label="所属驾校" width="120" show-overflow-tooltip ></el-table-column>
							<el-table-column prop="s_coach_name" label="姓名" width="120" show-overflow-tooltip ></el-table-column>
							<el-table-column prop="s_coach_phone" label="手机号" width="150" show-overflow-tooltip ></el-table-column>
						</el-table-column>
						<el-table-column prop="coach_license" label="牌照 | 科目" width="" show-overflow-tooltip>
							<el-table-column prop="coach_lesson" label="课程" min-width="120" show-overflow-tooltip ></el-table-column>
							<el-table-column prop="coach_license" label="牌照" min-width="120" show-overflow-tooltip ></el-table-column>
						</el-table-column>
						<el-table-column prop="certification_status" label="状态" width="" show-overflow-tooltip>
							<el-table-column prop="certification_status" label="认证状态" width="120" >
								<template scope="scope">
									<el-tag type="danger" v-if="parseInt(scope.row.certification_status) == 1">未认证</el-tag>
									<el-tag type="blue" v-if="parseInt(scope.row.certification_status) == 2">认证中</el-tag>
									<el-tag type="success" v-if="parseInt(scope.row.certification_status) == 3">已认证</el-tag>
									<el-tag type="gray" v-if="parseInt(scope.row.certification_status) == 4">认证失败</el-tag>
								</template>
							</el-table-column>
							<el-table-column prop="is_elecoach" label="电子教练" width="125">
								<template scope="scope" >
									<el-button type="success" @click="handleCoachStatus(scope.row.l_coach_id, 'is_elecoach', 0)" size="small" v-if="parseInt(scope.row.is_elecoach) == 1" close-transition>支持</el-button>
									<el-button type="danger" @click="handleCoachStatus(scope.row.l_coach_id, 'is_elecoach', 1)" size="small" v-else close-transition>不支持</el-button>
								</template>
							</el-table-column>
							<el-table-column prop="must_bind" label="需绑定否？" width="125">
								<template scope="scope" >
									<el-tag type="primary" size="small" v-if="scope.row.must_bind == '0'" close-transition>未设置</el-tag>
									<el-tag type="success" size="small" v-if="scope.row.must_bind == '1'" close-transition>需绑定</el-tag>
									<el-tag type="danger" size="small" v-if="scope.row.must_bind == '2'" close-transition>不需绑</el-tag>
								</template>
							</el-table-column>
							<el-table-column prop="timetraining_supported" label="支持计时?" width="125">
								<template scope="scope" >
									<el-button type="success" @click="handleCoachStatus(scope.row.l_coach_id, 'timetraining_supported', 0)" size="small" v-if="parseInt(scope.row.timetraining_supported) == 1" close-transition>支持</el-button>
									<el-button type="danger" @click="handleCoachStatus(scope.row.l_coach_id, 'timetraining_supported', 1)" size="small" v-else close-transition>不支持</el-button>
								</template>
							</el-table-column>
							<el-table-column prop="order_receive_status" label="在线否" width="110">
								<template scope="scope" >
									<el-button type="success" @click="handleCoachStatus(scope.row.l_coach_id,   'order_receive_status', 0)" size="small" v-if="parseInt(scope.row.order_receive_status) == 1" close-transition>在线</el-button>
									<el-button type="danger" @click="handleCoachStatus(scope.row.l_coach_id, 'order_receive_status', 1)" size="small" v-else close-transition>下线</el-button>
								</template>
							</el-table-column>
							<el-table-column prop="is_hot" label="热门?" width="80">
								<template scope="scope">
									<el-button type="success" @click="handleCoachStatus(scope.row.l_coach_id, 'is_hot', 2)" size="small" v-if="parseInt(scope.row.is_hot) == 1" close-transition>否</el-button>
									<el-button type="danger" @click="handleCoachStatus(scope.row.l_coach_id, 'is_hot', 1)" size="small"  v-if="parseInt(scope.row.is_hot) == 2" close-transition>是</el-button>
								</template>
							</el-table-column>
							<el-table-column prop="coupon_supported" label="支持券?" width="125" >
								<template scope="scope" >
									<el-button type="success" @click="handleCoachStatus(scope.row.l_coach_id, 'coupon_supported', 0)" size="small" v-if="parseInt(scope.row.coupon_supported) == 1" close-transition>支持</el-button>
									<el-button type="danger" @click="handleCoachStatus(scope.row.l_coach_id, 'coupon_supported', 1)" size="small" v-else close-transition>不支持</el-button>
								</template>
							</el-table-column>
						</el-table-column>
						<el-table-column prop="addtime" label="时间">
							<el-table-column prop="addtime" label="添加时间" sortable min-width="180" show-overflow-tooltip ></el-table-column>
							<el-table-column prop="updatetime" label="更新时间" sortable min-width="180" show-overflow-tooltip ></el-table-column>
						</el-table-column>
						<el-table-column label="需绑定否" fixed="right" width="120">
							<template scope="scope">
								<el-dropdown trigger="click">
									<el-tag class="el-dropdown-link" size="small" style="cursor: pointer" type="success">设置<i class="el-icon-caret-bottom el-icon--right"></i></el-tag>
									<el-dropdown-menu slot="dropdown" style="margin-right: -20px">
										<a @click="hanleMustBind(scope.row.l_coach_id, 0)" v-if="scope.row.bind_status == 0">
											<el-dropdown-item >未设置</el-dropdown-item>
										</a>
										<a @click="hanleMustBind(scope.row.l_coach_id, 1)">
											<el-dropdown-item >需绑定</el-dropdown-item>
										</a>
										<a @click="hanleMustBind(scope.row.l_coach_id, 2)">
											<el-dropdown-item >不需绑</el-dropdown-item>
										</a>
									</el-dropdown-menu>
								</el-dropdown>
							</template>
						</el-table-column>
						<el-table-column label="操作" fixed="right" width="140">
							<template scope="scope">
								<a title="设置时间配置" style="margin-left:5px; cursor: pointer" @click="handleSetTime($event, scope.row.l_coach_id, scope.row.s_school_name_id, scope.$index, scope.row)"><i class="el-icon-setting" ></i></a>
								<!-- <a title="预览" style="margin-left:8px; cursor: pointer" @click="handlePreview($event, scope.row.l_coach_id, scope.$index, scope.row)"><i class="el-icon-view"></i></a> -->
								<a title="删除" style="margin-left:8px; cursor: pointer" @click="handleDel(scope.row.l_coach_id, scope.row.s_coach_phone, scope.row.user_id, scope.$index, list)"><i class="el-icon-delete"></i></a>
								<a title="编辑" data-title="编辑教练信息" style="margin-left:8px; cursor: pointer" @click="handleEdit($event, scope.row.l_coach_id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
							</template>
						</el-table-column>
					</el-table>
				</el-tab-pane>

				<el-tab-pane label="已删除" name="second">
					<el-table :data="del_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" >
						<!-- <el-table-column fixed type="selection" width="50"></el-table-column> -->
						<el-table-column type="expand">
						<template scope="props">
							<el-form label-position="left" inline class="demo-table-expand">
								<el-form-item label="姓名：">
									<span>{{ props.row.s_coach_name }}</span>
								</el-form-item>
								<el-form-item label="联系方式：">
									<span>{{ props.row.s_coach_phone }}</span>
								</el-form-item>
								<el-form-item label="教练性别：">
									<el-tag type="success" v-if="props.row.s_coach_sex == '0'">男</el-tag>
									<el-tag type="danger" v-if="props.row.s_coach_sex == '1'">女</el-tag>
								</el-form-item>
								<el-form-item label="教龄：">
									<span>{{ props.row.s_teach_age }}年</span>
								</el-form-item>
								<el-form-item label="教练等级：">
									<el-tag type="warning" v-if="props.row.i_type == '0'">金牌教练</el-tag>
									<el-tag type="primary" v-if="props.row.i_type == '1'">普通教练</el-tag>
									<el-tag type="success" v-if="props.row.i_type == '2'">二级教练</el-tag>
									<el-tag type="success" v-if="props.row.i_type == '5'">二级教练，全国优秀教练员荣誉</el-tag>
									<el-tag type="danger" v-if="props.row.i_type == '3'">三级教练</el-tag>
									<el-tag type="gray" v-if="props.row.i_type == '4'">四级教练</el-tag>
								</el-form-item>
								<el-form-item label="教练车辆：">
									<span>{{ props.row.car_name }}</span>
								</el-form-item>
								<el-form-item label="科二通过率：">
									<span>{{ props.row.lesson2_pass_rate }}%</span>
								</el-form-item>
								<el-form-item label="科三通过率：">
									<span>{{ props.row.lesson3_pass_rate }}%</span>
								</el-form-item>
								<el-form-item label="注册时间：">
									<span>{{ props.row.addtime }}</span>
								</el-form-item>
								<el-form-item label="更新时间：">
									<span>{{ props.row.updatetime }}</span>
								</el-form-item>
								<el-form-item label="教练星级：">
									<el-div style="color:red;" type="danger" v-if="parseInt(props.row.i_coach_star) == 1" close-transition>★</el-div>
									<el-div style="color:red;" type="danger" v-else-if="parseInt(props.row.i_coach_star) == 2" close-transition>★★</el-div>
									<el-div style="color:red;" type="danger" v-else-if="parseInt(props.row.i_coach_star) == 3" close-transition>★★★</el-div>
									<el-div style="color:red;" type="danger" v-else-if="parseInt(props.row.i_coach_star) == 4" close-transition>★★★★</el-div>
									<el-div style="color:red;" type="danger" v-else close-transition>★★★★★</el-div>
								</el-form-item>
							</el-form>
						</template>
					</el-table-column>
					<el-table-column prop="l_coach_id" label="ID" sortable width="80"></el-table-column> 
					<!-- <el-table-column prop="i_order" label="排序" width="80"></el-table-column>  -->
					<el-table-column prop="s_coach_name" label="教练主信息" width="120" show-overflow-tooltip>
						<el-table-column prop="school_name" label="所属驾校" width="120" show-overflow-tooltip ></el-table-column>
						<el-table-column prop="s_coach_name" label="姓名" width="120" show-overflow-tooltip ></el-table-column>
						<el-table-column prop="s_coach_phone" label="手机号" width="150" show-overflow-tooltip ></el-table-column>
					</el-table-column>
					<el-table-column prop="coach_license" label="牌照 | 科目" width="" show-overflow-tooltip>
						<el-table-column prop="coach_lesson" label="课程" min-width="120" show-overflow-tooltip ></el-table-column>
						<el-table-column prop="coach_license" label="牌照" min-width="120" show-overflow-tooltip ></el-table-column>
					</el-table-column>
					<el-table-column prop="certification_status" label="状态" width="" show-overflow-tooltip>
						<el-table-column prop="certification_status" label="认证状态" width="120" >
							<template scope="scope">
								<el-tag type="danger" v-if="parseInt(scope.row.certification_status) == 1">未认证</el-tag>
								<el-tag type="blue" v-if="parseInt(scope.row.certification_status) == 2">认证中</el-tag>
								<el-tag type="success" v-if="parseInt(scope.row.certification_status) == 3">已认证</el-tag>
								<el-tag type="gray" v-if="parseInt(scope.row.certification_status) == 4">认证失败</el-tag>
							</template>
						</el-table-column>
						<el-table-column prop="is_elecoach" label="电子教练" width="125">
							<template scope="scope" >
								<el-button type="success" @click="handleCoachStatus(scope.row.l_coach_id, 'is_elecoach', 0)" size="small" v-if="parseInt(scope.row.is_elecoach) == 1" close-transition>支持</el-button>
								<el-button type="danger" @click="handleCoachStatus(scope.row.l_coach_id, 'is_elecoach', 1)" size="small" v-else close-transition>不支持</el-button>
							</template>
						</el-table-column>
						<el-table-column prop="must_bind" label="需绑定否？" width="125">
							<template scope="scope" >
								<el-tag type="primary" size="small" v-if="scope.row.must_bind == '0'" close-transition>未设置</el-tag>
								<el-tag type="success" size="small" v-if="scope.row.must_bind == '1'" close-transition>需绑定</el-tag>
								<el-tag type="danger" size="small" v-if="scope.row.must_bind == '2'" close-transition>不需绑</el-tag>
							</template>
						</el-table-column>
						<el-table-column prop="timetraining_supported" label="支持计时?" width="125">
							<template scope="scope" >
								<el-button type="success" @click="handleCoachStatus(scope.row.l_coach_id, 'timetraining_supported', 0)" size="small" v-if="parseInt(scope.row.timetraining_supported) == 1" close-transition>支持</el-button>
								<el-button type="danger" @click="handleCoachStatus(scope.row.l_coach_id, 'timetraining_supported', 1)" size="small" v-else close-transition>不支持</el-button>
							</template>
						</el-table-column>
						<el-table-column prop="order_receive_status" label="在线否" width="110">
							<template scope="scope" >
								<el-button type="success" @click="handleCoachStatus(scope.row.l_coach_id,   'order_receive_status', 0)" size="small" v-if="parseInt(scope.row.order_receive_status) == 1" close-transition>在线</el-button>
								<el-button type="danger" @click="handleCoachStatus(scope.row.l_coach_id, 'order_receive_status', 1)" size="small" v-else close-transition>下线</el-button>
							</template>
						</el-table-column>
						<el-table-column prop="is_hot" label="热门?" width="80">
							<template scope="scope">
								<el-button type="success" @click="handleCoachStatus(scope.row.l_coach_id, 'is_hot', 2)" size="small" v-if="parseInt(scope.row.is_hot) == 1" close-transition>否</el-button>
								<el-button type="danger" @click="handleCoachStatus(scope.row.l_coach_id, 'is_hot', 1)" size="small"  v-if="parseInt(scope.row.is_hot) == 2" close-transition>是</el-button>
							</template>
						</el-table-column>
						<el-table-column prop="coupon_supported" label="支持券?" width="125" >
							<template scope="scope" >
								<el-button type="success" @click="handleCoachStatus(scope.row.l_coach_id, 'coupon_supported', 0)" size="small" v-if="parseInt(scope.row.coupon_supported) == 1" close-transition>支持</el-button>
								<el-button type="danger" @click="handleCoachStatus(scope.row.l_coach_id, 'coupon_supported', 1)" size="small" v-else close-transition>不支持</el-button>
							</template>
						</el-table-column>
					</el-table-column>
					<el-table-column prop="addtime" label="时间">
						<el-table-column prop="addtime" label="添加时间" sortable min-width="180" show-overflow-tooltip ></el-table-column>
						<el-table-column prop="updatetime" label="更新时间" sortable min-width="180" show-overflow-tooltip ></el-table-column>
					</el-table-column>
					<el-table-column label="需绑定否" fixed="right" width="120">
						<template scope="scope">
							<el-dropdown trigger="click">
								<el-tag class="el-dropdown-link" size="small" style="cursor: pointer" type="success">设置<i class="el-icon-caret-bottom el-icon--right"></i></el-tag>
								<el-dropdown-menu slot="dropdown" style="margin-right: -20px">
									<a @click="hanleMustBind(scope.row.l_coach_id, 0)" v-if="scope.row.bind_status == 0">
										<el-dropdown-item >未设置</el-dropdown-item>
									</a>
									<a @click="hanleMustBind(scope.row.l_coach_id, 1)">
										<el-dropdown-item >需绑定</el-dropdown-item>
									</a>
									<a @click="hanleMustBind(scope.row.l_coach_id, 2)">
										<el-dropdown-item >不需绑</el-dropdown-item>
									</a>
								</el-dropdown-menu>
							</el-dropdown>
						</template>
					</el-table-column>
					<el-table-column label="操作" fixed="right" width="140">
						<template scope="scope">
							<a title="设置时间配置" style="margin-left:5px; cursor: pointer" @click="handleSetTime($event, scope.row.l_coach_id, scope.row.s_school_name_id, scope.$index, scope.row)"><i class="el-icon-setting" ></i></a>
							<!-- <a title="预览" style="margin-left:8px; cursor: pointer" @click="handlePreview($event, scope.row.l_coach_id, scope.$index, scope.row)"><i class="el-icon-view"></i></a> -->
							<!-- <a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.l_coach_id, scope.row.s_coach_phone, scope.row.user_id, scope.$index, list)"><i class="el-icon-delete"></i></a> -->
							<a title="恢复" v-if="rid == 1" style="margin-left:8px; cursor: pointer" @click="handleRecover(scope.row.l_coach_id, scope.row.s_coach_phone, scope.row.user_id, scope.$index, list)"><i class="el-icon-plus"></i></a>
							<a title="编辑" data-title="编辑教练信息" style="margin-left:8px; cursor: pointer" @click="handleEdit($event, scope.row.l_coach_id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
						</template>
					</el-table-column>
				</el-tab-pane>
  			</el-tabs>

			<el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange" v-if="school_id != ''">
				<!-- <el-table-column fixed type="selection" width="50"></el-table-column> -->
				<el-table-column type="expand">
					<template scope="props">
						<el-form label-position="left" inline class="demo-table-expand">
							<el-form-item label="姓名：">
								<span>{{ props.row.s_coach_name }}</span>
							</el-form-item>
							<el-form-item label="联系方式：">
								<span>{{ props.row.s_coach_phone }}</span>
							</el-form-item>
							<el-form-item label="教练性别：">
								<el-tag type="success" v-if="props.row.s_coach_sex == '0'">男</el-tag>
								<el-tag type="danger" v-if="props.row.s_coach_sex == '1'">女</el-tag>
							</el-form-item>
							<el-form-item label="教龄：">
								<span>{{ props.row.s_teach_age }}年</span>
							</el-form-item>
							<el-form-item label="教练等级：">
								<el-tag type="warning" v-if="props.row.i_type == '0'">金牌教练</el-tag>
								<el-tag type="primary" v-if="props.row.i_type == '1'">普通教练</el-tag>
								<el-tag type="success" v-if="props.row.i_type == '2'">二级教练</el-tag>
								<el-tag type="success" v-if="props.row.i_type == '5'">二级教练，全国优秀教练员荣誉</el-tag>
								<el-tag type="danger" v-if="props.row.i_type == '3'">三级教练</el-tag>
								<el-tag type="gray" v-if="props.row.i_type == '4'">四级教练</el-tag>
							</el-form-item>
							<el-form-item label="教练车辆：">
								<span>{{ props.row.car_name }}</span>
							</el-form-item>
							<el-form-item label="科二通过率：">
								<span>{{ props.row.lesson2_pass_rate }}%</span>
							</el-form-item>
							<el-form-item label="科三通过率：">
								<span>{{ props.row.lesson3_pass_rate }}%</span>
							</el-form-item>
							<el-form-item label="注册时间：">
								<span>{{ props.row.addtime }}</span>
							</el-form-item>
							<el-form-item label="更新时间：">
								<span>{{ props.row.updatetime }}</span>
							</el-form-item>
							<el-form-item label="教练星级：">
								<el-div style="color:red;" type="danger" v-if="parseInt(props.row.i_coach_star) == 1" close-transition>★</el-div>
								<el-div style="color:red;" type="danger" v-else-if="parseInt(props.row.i_coach_star) == 2" close-transition>★★</el-div>
								<el-div style="color:red;" type="danger" v-else-if="parseInt(props.row.i_coach_star) == 3" close-transition>★★★</el-div>
								<el-div style="color:red;" type="danger" v-else-if="parseInt(props.row.i_coach_star) == 4" close-transition>★★★★</el-div>
								<el-div style="color:red;" type="danger" v-else close-transition>★★★★★</el-div>
							</el-form-item>
						</el-form>
					</template>
				</el-table-column>
				<el-table-column prop="l_coach_id" label="ID" sortable width="80"></el-table-column> 
				<!-- <el-table-column prop="i_order" label="排序" width="80"></el-table-column>  -->
				<el-table-column prop="s_coach_name" label="教练主信息" width="120" show-overflow-tooltip>
					<el-table-column prop="s_coach_name" label="姓名" width="120" show-overflow-tooltip ></el-table-column>
					<el-table-column prop="s_coach_phone" label="手机号" width="150" show-overflow-tooltip ></el-table-column>
				</el-table-column>
				<el-table-column prop="coach_license" label="牌照 | 科目" width="" show-overflow-tooltip>
					<el-table-column prop="coach_lesson" label="课程" min-width="120" show-overflow-tooltip ></el-table-column>
					<el-table-column prop="coach_license" label="牌照" min-width="120" show-overflow-tooltip ></el-table-column>
				</el-table-column>
				<el-table-column prop="certification_status" label="状态" width="" show-overflow-tooltip>
					<el-table-column prop="certification_status" label="认证状态" width="120" >
						<template scope="scope">
							<el-tag type="danger" v-if="parseInt(scope.row.certification_status) == 1">未认证</el-tag>
							<el-tag type="blue" v-if="parseInt(scope.row.certification_status) == 2">认证中</el-tag>
							<el-tag type="success" v-if="parseInt(scope.row.certification_status) == 3">已认证</el-tag>
							<el-tag type="gray" v-if="parseInt(scope.row.certification_status) == 4">认证失败</el-tag>
						</template>
					</el-table-column>
					<el-table-column prop="is_elecoach" label="电子教练" width="125">
						<template scope="scope" >
							<el-button type="success" @click="handleCoachStatus(scope.row.l_coach_id, 'is_elecoach', 0)" size="small" v-if="parseInt(scope.row.is_elecoach) == 1" close-transition>支持</el-button>
							<el-button type="danger" @click="handleCoachStatus(scope.row.l_coach_id, 'is_elecoach', 1)" size="small" v-else close-transition>不支持</el-button>
						</template>
					</el-table-column>
					<el-table-column prop="must_bind" label="需绑定否？" width="125">
						<template scope="scope" >
							<el-tag type="primary" size="small" v-if="scope.row.must_bind == '0'" close-transition>未设置</el-tag>
							<el-tag type="success" size="small" v-if="scope.row.must_bind == '1'" close-transition>需绑定</el-tag>
							<el-tag type="danger" size="small" v-if="scope.row.must_bind == '2'" close-transition>不需绑</el-tag>
						</template>
					</el-table-column>
					<el-table-column prop="timetraining_supported" label="支持计时?" width="125">
						<template scope="scope" >
							<el-button type="success" @click="handleCoachStatus(scope.row.l_coach_id, 'timetraining_supported', 0)" size="small" v-if="parseInt(scope.row.timetraining_supported) == 1" close-transition>支持</el-button>
							<el-button type="danger" @click="handleCoachStatus(scope.row.l_coach_id, 'timetraining_supported', 1)" size="small" v-else close-transition>不支持</el-button>
						</template>
					</el-table-column>
					<el-table-column prop="order_receive_status" label="在线否" width="110">
						<template scope="scope" >
							<el-button type="success" @click="handleCoachStatus(scope.row.l_coach_id,   'order_receive_status', 0)" size="small" v-if="parseInt(scope.row.order_receive_status) == 1" close-transition>在线</el-button>
							<el-button type="danger" @click="handleCoachStatus(scope.row.l_coach_id, 'order_receive_status', 1)" size="small" v-else close-transition>下线</el-button>
						</template>
					</el-table-column>
					<el-table-column prop="is_hot" label="热门?" width="80">
						<template scope="scope">
							<el-button type="success" @click="handleCoachStatus(scope.row.l_coach_id, 'is_hot', 2)" size="small" v-if="parseInt(scope.row.is_hot) == 1" close-transition>否</el-button>
							<el-button type="danger" @click="handleCoachStatus(scope.row.l_coach_id, 'is_hot', 1)" size="small"  v-if="parseInt(scope.row.is_hot) == 2" close-transition>是</el-button>
						</template>
					</el-table-column>
					<el-table-column prop="coupon_supported" label="支持券?" width="125" >
						<template scope="scope" >
							<el-button type="success" @click="handleCoachStatus(scope.row.l_coach_id, 'coupon_supported', 0)" size="small" v-if="parseInt(scope.row.coupon_supported) == 1" close-transition>支持</el-button>
							<el-button type="danger" @click="handleCoachStatus(scope.row.l_coach_id, 'coupon_supported', 1)" size="small" v-else close-transition>不支持</el-button>
						</template>
					</el-table-column>
				</el-table-column>
				<el-table-column prop="addtime" label="时间">
					<el-table-column prop="addtime" label="添加时间" sortable min-width="180" show-overflow-tooltip ></el-table-column>
					<el-table-column prop="updatetime" label="更新时间" sortable min-width="180" show-overflow-tooltip ></el-table-column>
				</el-table-column>
				<el-table-column label="需绑定否" fixed="right" width="120">
					<template scope="scope">
						<el-dropdown trigger="click">
							<el-tag class="el-dropdown-link" size="small" style="cursor: pointer" type="success">设置<i class="el-icon-caret-bottom el-icon--right"></i></el-tag>
							<el-dropdown-menu slot="dropdown" style="margin-right: -20px">
								<a @click="hanleMustBind(scope.row.l_coach_id, 0)" v-if="scope.row.bind_status == 0">
									<el-dropdown-item >未设置</el-dropdown-item>
								</a>
								<a @click="hanleMustBind(scope.row.l_coach_id, 1)">
									<el-dropdown-item >需绑定</el-dropdown-item>
								</a>
								<a @click="hanleMustBind(scope.row.l_coach_id, 2)">
									<el-dropdown-item >不需绑</el-dropdown-item>
								</a>
							</el-dropdown-menu>
						</el-dropdown>
					</template>
				</el-table-column>
				<el-table-column label="操作" fixed="right" width="140">
					<template scope="scope">
						<a title="设置时间配置" style="margin-left:5px;cursor: pointer" @click="handleSetTime($event, scope.row.l_coach_id, scope.$index, scope.row)"><i class="el-icon-setting" ></i></a>
						<!-- <a title="预览" style="margin-left:8px; cursor: pointer" @click="handlePreview($event, scope.row.l_coach_id, scope.$index, scope.row)"><i class="el-icon-view"></i></a> -->
						<a title="删除" style="margin-left:8px; cursor: pointer" @click="handleDel(scope.row.l_coach_id, scope.row.s_coach_phone, scope.row.user_id, scope.$index, list)"><i class="el-icon-delete"></i></a>
						<a title="编辑" data-title="编辑教练信息" style="margin-left:8px; cursor: pointer" @click="handleEdit($event, scope.row.l_coach_id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
					</template>
				</el-table-column>
			</el-table>
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
		</div>
	</div>
</div>
<script>
	var school_id = "<?php echo $school_id; ?>";
	var rid = "<?php echo $role_id; ?>";
	var vm = new Vue({
		el: '#app',
		data: {
			refreshstatus: false,
			fullscreenLoading: false,
			multipleSelection: [],
			list: [],
			del_list: [],
			star_options: [
				{value: '', label: '--不限星级--' }, 
				{value: '1.0', label: '★'}, 
				{value: '2.0', label: '★★'}, 
				{value: '3.0', label: '★★★'}, 
				{value: '4.0', label: '★★★★'}, 
				{value: '5.0', label: '★★★★★'}
			],
			verify_options: [
				{value: '', label: '--不限状态--' }, 
				{value: 1, label: '未认证' }, 
				{value: 2, label: '认证中'},
				{value: 3, label: '已认证' }, 
				{value: 4, label: '认证失败'}
			],
			list_url: "<?php echo base_url('coach/listAjax'); ?>",
			del_url: "<?php echo base_url('coach/delAjax'); ?>",
			recover_url: "<?php echo base_url('coach/recoverAjax'); ?>",
			edit_url: "<?php echo base_url('coach/edit'); ?>",
			editajax_url: "<?php echo base_url('coach/editAjax'); ?>",
			add_url: "<?php echo base_url('coach/add'); ?>",
			preview_url: "<?php echo base_url('coach/preview'); ?>",
			show_url: "<?php echo base_url('coach/show'); ?>",
			set_url: "<?php echo base_url('coach/setCoachStatus'); ?>",
			bind_url: "<?php echo base_url('coach/setMustBind'); ?>",
			setTimeConf_url: "<?php echo base_url('coach/setCoachTimeConf'); ?>",
			currentPage: 1,
			page_sizes: [10, 20, 30, 50, 100],
			page_size: 10,
			pagenum: 0,
			count: 0,
			activeName: 'first',
			status: 'undel',
			search: {
				star: '',
				verify: '',
				keywords: '',
			}
		},
		created: function() {
			var filter = {"p": this.currentPage, "status": this.status, "star": this.search.star, "verify": this.search.verify, "keywords": this.search.keywords, "s": this.page_size};
			this.listAjax(filter);
		},
		methods: {
			listAjax: function(param) {
				$.ajax({
					type: 'post',
					url: this.list_url,
					data: param,
					dataType:"json",
					async: true,
					success: function(ret) {
						vm.fullscreenLoading = false;			
						vm.refreshstatus = false;
						if(ret.code == 200) {
							vm.list = ret.data.list;
							if (param.status == 'undel') {
								vm.list = ret.data.list;
							} else {
								vm.del_list = ret.data.list;
							}
							vm.pagenum = ret.data.pagenum;
							vm.count = ret.data.count;
							vm.currentPage = ret.data.p;
						}
					},
					error: function() {
						vm.fullscreenLoading = false;
						vm.refreshstatus = false;
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				})
			},
			recoverAjax: function(param) {
				$.ajax({
					type: 'post',
					url: this.recover_url,
					data: param,
					dataType:"json",
					success: function(data) {
						vm.fullscreenLoading = false;			
						if(data.code == 200) {
							var filter = {"p": vm.currentPage, "status": vm.status, "star": vm.search.star, "verify": vm.search.verify, "keywords": vm.search.keywords, "s": vm.page_size};
							vm.listAjax(filter);
							vm.messageNotice('success', data.msg);			
						}
					},
					error: function() {
						vm.fullscreenLoading = false;									
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				})
			},
			delAjax: function(param) {
				$.ajax({
					type: 'post',
					url: this.del_url,
					data: param,
					dataType:"json",
					success: function(data) {
						vm.fullscreenLoading = false;			
						if(data.code == 200) {
							var filter = {"p": vm.currentPage, "status": vm.status, "star": vm.search.star, "verify": vm.search.verify, "keywords": vm.search.keywords, "s": vm.page_size};
							vm.listAjax(filter);
							vm.messageNotice('success', data.msg);			
						}
					},
					error: function() {
						vm.fullscreenLoading = false;									
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				})
			},
			hanleMustBind: function(id, status) {
				$.ajax({
					type: 'post',
					url: this.bind_url,
					data: {"id": id, "status": status},
					dataType:"json",
					success: function(data) {		
						if(data.code == 200) {
							var filter = {"p": vm.currentPage, "status": vm.status, "star": vm.search.star, "verify": vm.search.verify, "keywords": vm.search.keywords, "s": vm.page_size};
							vm.listAjax(filter);
						} else {
							vm.messageNotice('warning', data.msg);			
						}
					},
					error: function() {							
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				});
			},
			handleCoachStatus: function(id, field, status) {
				$.ajax({
					type: 'post',
					url: this.set_url,
					data: {"id": id, "field": field, "status": status},
					dataType:"json",
					success: function(data) {		
						if(data.code == 200) {
							var filter = {"p": vm.currentPage, "status": vm.status, "star": vm.search.star, "verify": vm.search.verify, "keywords": vm.search.keywords, "s": vm.page_size};
							vm.listAjax(filter);
						} else {
							vm.messageNotice('warning', data.msg);			
						}
					},
					error: function() {							
						vm.messageNotice('warning', '网络错误，请检查网络');
					}
				});
			},
			handleClick: function(tab, event) {
				switch (this.activeName) {
					case 'first':
						this.status = 'undel';
						break;
					case 'second':
						this.status = 'del';
						break;
					default:
						this.status = 'undel';
						break;
				}
				var filter = {"p": vm.currentPage, "status": vm.status, "star": vm.search.star, "verify": vm.search.verify, "keywords": vm.search.keywords, "s": vm.page_size};
				this.listAjax(filter);
			},
			handleSizeChange: function (size) {
				this.page_size = size;
				var filter = {"p": this.currentPage, "status": this.status, "star": this.search.star, "verify": this.search.verify, "keywords": this.search.keywords, "s": this.page_size};
				this.listAjax(filter);
			},
			handleCurrentChange: function(val) {
				this.refreshstatus = true;
				this.currentPage = val;
				// window.history.pushState(null, null, '?p='+val+'&status='+this.status+'&star='+this.search.star+'&verify='+this.search.verify+'&kwords='+this.search.kwords+'&value='+this.search.value);
				var filter = {"p": this.currentPage, "status": this.status, "star": this.search.star, "verify": this.search.verify, "keywords": this.search.keywords, "s": this.page_size};
				this.listAjax(filter);
			},
			handleRefresh: function() {
				this.refreshstatus = true;
				var filter = {"p": this.currentPage, "status": this.status, "s": this.page_size};
				this.listAjax(filter);
			},
			handleSearch: function () {
				var filter = {"p": this.currentPage, "status": this.status, "star": this.search.star, "verify": this.search.verify, "keywords": this.search.keywords, "s": this.page_size};
				this.listAjax(filter);
			},
			filterTag: function(value, row) {
				return row.is_show == value;
			},
			handleSetTime: function (e, id, school_id, index, row) {
				this.showLayer(e, '60%', 'lb', this.setTimeConf_url+'?id='+id+'&school_id='+school_id);
			},
			handleSelectionChange: function(val) {
				this.multipleSelection = val;
			},
			handleAdd: function(e) {
				this.showLayer(e, '60%', 'rb', this.add_url);
			},
			handleEdit: function(e, id, index, row) {
				this.showLayer(e, '60%', 'lb', this.edit_url+'?id='+id);
			},
			handlePreview: function(e, id, index, row) {
				this.showLayer(e, '60%', 'rb', this.preview_url+'?id='+id);
			},
			handleRecover: function(id, phone, user_id, index, rows) {
				this.$confirm('此操作可以恢复教练, 是否继续?', '提示', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning',
					callback: function(action) {
						if(action == 'confirm') {
							var filter = {'id': id, 'phone': phone, 'uid': user_id};
							vm.recoverAjax(filter);
							rows.splice(index, 1);
							vm.messageNotice('success', '恢复成功!');
						} else {
							return false;
						}
					}
				});
			},
			handleDel: function(id, phone, user_id, index, rows) {
				this.$confirm('此操作将永久删除该教练, 是否继续?', '提示', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning',
					callback: function(action) {
						if(action == 'confirm') {
							var filter = {'id': id, 'phone': phone, 'uid': user_id};
							vm.delAjax(filter);
							rows.splice(index, 1);
							vm.messageNotice('success', '删除成功!');
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
			},
			
		}
	})
</script>