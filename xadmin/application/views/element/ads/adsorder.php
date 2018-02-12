<div id="app" v-cloak v-loading.fullscreen.lock="fullscreenLoading" element-loading-text="拼命加载中">
	<div class="iframe-content">

        <!--search-->
        <div class="gx-search">
            <el-form :inline="true" v-model="search">
                <el-form-item label="设备类型" >
                    <el-select v-model="search.device" placeholder="请选择类型">
                        <el-option v-for="item in device_options" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="支付方式" >
                    <el-select v-model="search.pay_type" placeholder="请选择方式">
                        <el-option v-for="item in pt_options" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </el-form-item>
                <!-- <el-form-item label="订单状态" >
                    <el-select v-model="search.order_status" placeholder="请选择订单状态">
                        <el-option v-for="item in order_options" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </el-form-item> -->
                <el-form-item label="关键词" >
                    <el-input v-model="search.keywords" placeholder="买家 | 订单号 | 交易号"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" icon="search" @click="handleSearch">搜索</el-button>
                </el-form-item>
            </el-form>
        </div>
        <!--end search-->

        <!--list-->
        <div class="gx-iframe-content">
            <!--add-->
            <div class="gx-iframe-operation" style="margin-bottom: 20px; border-radius: 4px;">
                 <el-button type="success" id="add" @click.active.prevent="handleAdd($event)" data-title="新增广告位" style="margin-left: 10px;"><i class="el-icon-plus"> 新增订单</i></el-button>
                <el-button type="success" style="float: right; line-height: 38px;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
            </div>

            <el-tabs v-model="activeName" @tab-click="handleClick">
                <el-tab-pane label="已付款" name="first">
                    <el-table :data="list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange">
                        <!-- <el-table-column type="selection" width="50"></el-table-column> -->
                        <el-table-column type="expand">
                            <template scope="props">
                                <el-form label-position="left" inline class="demo-table-expand">
                                    <el-form-item label="订单ID">
                                        <span>{{ props.row.id }}</span>
                                    </el-form-item>
                                    <el-form-item label="内容标题">
                                        <span>{{ props.row.ads_title }}</span>
                                    </el-form-item>
                                    <el-form-item label="原始价格">
                                        <span>￥{{ props.row.original_price }}元</span>
                                    </el-form-item>
                                    <el-form-item label="最终价格">
                                        <span>￥{{ props.row.final_price }}元</span>
                                    </el-form-item>
                                </el-form>
                            </template>
                        </el-table-column>
                        <el-table-column prop="id" label="ID" width="80" sortable show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="title" label="广告类型" width="150" show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="buyer_name" label="买家" min-width="120" show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="order_no" label="订单号" min-width="180" show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="unique_trade_no" label="唯一支付码" min-width="120" show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="resource_type" label="资源类型" width="130">
                            <template scope="scope">
                                <el-tag type="success" v-if="parseInt(scope.row.resource_type) == 1">图片</el-tag>
                                <el-tag type="primary" v-if="parseInt(scope.row.resource_type) == 2">视频</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column prop="device" label="设备类型" width="130">
                            <template scope="scope">
                                <el-tag type="success" v-if="parseInt(scope.row.device) == 1">苹果</el-tag>
                                <el-tag type="primary" v-if="parseInt(scope.row.device) == 2">安卓</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column label="图片" prop="resource_url" width="150">
                            <template scope="scope">
                                <!-- <img :src="scope.row.resource_url" v-if="scope.row.resource_url != ''" style="color: #12cf66; width: 100px;height: 60px;"> -->
                                <img :src="scope.row.resource_url" @click="showPic($event, scope.row.id, scope.$index, scope.row, scope.row.resource_url)" v-if="scope.row.resource_url != '' " data-title="图片预览" style="width: 120px; height: 60px;cursor: pointer">
                                <a href="#" v-if="scope.row.resource_url == ''">--<a>
                            </template>
                        </el-table-column> 
                        <el-table-column prop="pay_type" label="支付方式" width="130">
                            <template scope="scope">
                                <el-tag type="primary" v-if="parseInt(scope.row.pay_type) == 1">支付宝</el-tag>
                                <el-tag type="warning" v-if="parseInt(scope.row.pay_type) == 2">线下</el-tag>
                                <el-tag type="success" v-if="parseInt(scope.row.pay_type) == 3">微信</el-tag>
                                <el-tag type="danger" v-if="parseInt(scope.row.pay_type) == 4">银联</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column prop="is_promote" label="打折否" width="130">
                            <template scope="scope">
                                <el-button type="primary" @click="changePromote(scope.row.id, 0)" size="small" v-if="parseInt(scope.row.is_promote) == 1">是</el-button>
                                <el-button type="success" @click="changePromote(scope.row.id, 1)" size="small" v-if="parseInt(scope.row.is_promote) == 0">否</el-button>
                            </template>
                        </el-table-column>
                        <el-table-column prop="over_time" label="过期时间" sortable min-width="150" show-overflow-tooltip></el-table-column> 
                        <el-table-column label="操作" fixed="right" width="140">
                            <template scope="scope">
                                <a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.id, scope.$index, list)"><i class="el-icon-delete"></i></a>
                                <a title="编辑" data-title="更新订单" style="margin-left:8px;" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
                            </template>
                        </el-table-column>
                    </el-table>
                </el-tab-pane>
                <el-tab-pane label="未付款" name="second">
                    <el-table :data="unpaid_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange">
                        <!-- <el-table-column type="selection" width="50"></el-table-column> -->
                        <el-table-column type="expand">
                            <template scope="props">
                                <el-form label-position="left" inline class="demo-table-expand">
                                    <el-form-item label="订单ID">
                                        <span>{{ props.row.id }}</span>
                                    </el-form-item>
                                    <el-form-item label="内容标题">
                                        <span>{{ props.row.ads_title }}</span>
                                    </el-form-item>
                                    <el-form-item label="原始价格">
                                        <span>￥{{ props.row.original_price }}元</span>
                                    </el-form-item>
                                    <el-form-item label="最终价格">
                                        <span>￥{{ props.row.final_price }}元</span>
                                    </el-form-item>
                                </el-form>
                            </template>
                        </el-table-column>
                        <el-table-column prop="id" label="ID" width="80" sortable show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="title" label="广告类型" width="150" show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="buyer_name" label="买家" min-width="120" show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="order_no" label="订单号" min-width="180" show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="unique_trade_no" label="唯一支付码" min-width="120" show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="resource_type" label="资源类型" width="130">
                            <template scope="scope">
                                <el-tag type="success" v-if="parseInt(scope.row.resource_type) == 1">图片</el-tag>
                                <el-tag type="primary" v-if="parseInt(scope.row.resource_type) == 2">视频</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column prop="device" label="设备类型" width="130">
                            <template scope="scope">
                                <el-tag type="success" v-if="parseInt(scope.row.device) == 1">苹果</el-tag>
                                <el-tag type="primary" v-if="parseInt(scope.row.device) == 2">安卓</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column label="图片" prop="resource_url" width="150">
                            <template scope="scope">
                                <!-- <img :src="scope.row.resource_url" v-if="scope.row.resource_url != ''" style="color: #12cf66; width: 100px;height: 60px;"> -->
                                <img :src="scope.row.resource_url" @click="showPic($event, scope.row.id, scope.$index, scope.row, scope.row.resource_url)" v-if="scope.row.resource_url != '' " data-title="图片预览" style="width: 120px; height: 60px;cursor: pointer">
                                <a href="#" v-if="scope.row.resource_url == ''">--<a>
                            </template>
                        </el-table-column> 
                        <el-table-column prop="pay_type" label="支付方式" width="130">
                            <template scope="scope">
                                <el-tag type="primary" v-if="parseInt(scope.row.pay_type) == 1">支付宝</el-tag>
                                <el-tag type="warning" v-if="parseInt(scope.row.pay_type) == 2">线下</el-tag>
                                <el-tag type="success" v-if="parseInt(scope.row.pay_type) == 3">微信</el-tag>
                                <el-tag type="danger" v-if="parseInt(scope.row.pay_type) == 4">银联</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column prop="is_promote" label="打折否" width="130">
                            <template scope="scope">
                                <el-button type="success" @click="changePromote(scope.row.id, 1)" size="small" v-if="parseInt(scope.row.is_promote) == 0">否</el-button>
                                <el-button type="primary" @click="changePromote(scope.row.id, 0)" size="small" v-if="parseInt(scope.row.is_promote) == 1">是</el-button>
                            </template>
                        </el-table-column>
                        <el-table-column prop="over_time" label="过期时间" sortable min-width="150" show-overflow-tooltip></el-table-column> 
                        <el-table-column label="操作" fixed="right" width="140">
                            <template scope="scope">
                                <a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.id, scope.$index, list)"><i class="el-icon-delete"></i></a>
                                <a title="编辑" data-title="更新订单" style="margin-left:8px;" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
                            </template>
                        </el-table-column>
                    </el-table>
                </el-tab-pane>
                <el-tab-pane label="已取消" name="third">
                    <el-table :data="cancel_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange">
                        <!-- <el-table-column type="selection" width="50"></el-table-column> -->
                        <el-table-column type="expand">
                            <template scope="props">
                                <el-form label-position="left" inline class="demo-table-expand">
                                    <el-form-item label="订单ID">
                                        <span>{{ props.row.id }}</span>
                                    </el-form-item>
                                    <el-form-item label="内容标题">
                                        <span>{{ props.row.ads_title }}</span>
                                    </el-form-item>
                                    <el-form-item label="原始价格">
                                        <span>￥{{ props.row.original_price }}元</span>
                                    </el-form-item>
                                    <el-form-item label="最终价格">
                                        <span>￥{{ props.row.final_price }}元</span>
                                    </el-form-item>
                                </el-form>
                            </template>
                        </el-table-column>
                        <el-table-column prop="id" label="ID" width="80" sortable show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="title" label="广告类型" width="150" show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="buyer_name" label="买家" min-width="120" show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="order_no" label="订单号" min-width="180" show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="unique_trade_no" label="唯一支付码" min-width="120" show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="resource_type" label="资源类型" width="130">
                            <template scope="scope">
                                <el-tag type="success" v-if="parseInt(scope.row.resource_type) == 1">图片</el-tag>
                                <el-tag type="primary" v-if="parseInt(scope.row.resource_type) == 2">视频</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column prop="device" label="设备类型" width="130">
                            <template scope="scope">
                                <el-tag type="success" v-if="parseInt(scope.row.device) == 1">苹果</el-tag>
                                <el-tag type="primary" v-if="parseInt(scope.row.device) == 2">安卓</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column label="图片" prop="resource_url" width="150">
                            <template scope="scope">
                                <!-- <img :src="scope.row.resource_url" v-if="scope.row.resource_url != ''" style="color: #12cf66; width: 100px;height: 60px;"> -->
                                <img :src="scope.row.resource_url" @click="showPic($event, scope.row.id, scope.$index, scope.row, scope.row.resource_url)" v-if="scope.row.resource_url != '' " data-title="图片预览" style="width: 120px; height: 60px;cursor: pointer">
                                <a href="#" v-if="scope.row.resource_url == ''">--<a>
                            </template>
                        </el-table-column> 
                        <el-table-column prop="pay_type" label="支付方式" width="130">
                            <template scope="scope">
                                <el-tag type="primary" v-if="parseInt(scope.row.pay_type) == 1">支付宝</el-tag>
                                <el-tag type="warning" v-if="parseInt(scope.row.pay_type) == 2">线下</el-tag>
                                <el-tag type="success" v-if="parseInt(scope.row.pay_type) == 3">微信</el-tag>
                                <el-tag type="danger" v-if="parseInt(scope.row.pay_type) == 4">银联</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column prop="is_promote" label="打折否" width="130">
                            <template scope="scope">
                                <el-button type="success" @click="changePromote(scope.row.id, 1)" size="small" v-if="parseInt(scope.row.is_promote) == 0">否</el-button>
                                <el-button type="primary" @click="changePromote(scope.row.id, 0)" size="small" v-if="parseInt(scope.row.is_promote) == 1">是</el-button>
                            </template>
                        </el-table-column>
                        <el-table-column prop="over_time" label="过期时间" sortable min-width="150" show-overflow-tooltip></el-table-column> 
                        <el-table-column label="操作" fixed="right" width="140">
                            <template scope="scope">
                                <a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.id, scope.$index, list)"><i class="el-icon-delete"></i></a>
                                <a title="编辑" data-title="更新订单" style="margin-left:8px;" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
                            </template>
                        </el-table-column>
                    </el-table>
                </el-tab-pane>
                <el-tab-pane label="已退款" name="fourth">
                    <el-table :data="refund_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange">
                        <!-- <el-table-column type="selection" width="50"></el-table-column> -->
                        <el-table-column type="expand">
                            <template scope="props">
                                <el-form label-position="left" inline class="demo-table-expand">
                                    <el-form-item label="订单ID">
                                        <span>{{ props.row.id }}</span>
                                    </el-form-item>
                                    <el-form-item label="内容标题">
                                        <span>{{ props.row.ads_title }}</span>
                                    </el-form-item>
                                    <el-form-item label="原始价格">
                                        <span>￥{{ props.row.original_price }}元</span>
                                    </el-form-item>
                                    <el-form-item label="最终价格">
                                        <span>￥{{ props.row.final_price }}元</span>
                                    </el-form-item>
                                </el-form>
                            </template>
                        </el-table-column>
                        <el-table-column prop="id" label="ID" width="80" sortable show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="title" label="广告类型" width="150" show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="buyer_name" label="买家" min-width="120" show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="order_no" label="订单号" min-width="180" show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="unique_trade_no" label="唯一支付码" min-width="120" show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="resource_type" label="资源类型" width="130">
                            <template scope="scope">
                                <el-tag type="success" v-if="parseInt(scope.row.resource_type) == 1">图片</el-tag>
                                <el-tag type="primary" v-if="parseInt(scope.row.resource_type) == 2">视频</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column prop="device" label="设备类型" width="130">
                            <template scope="scope">
                                <el-tag type="success" v-if="parseInt(scope.row.device) == 1">苹果</el-tag>
                                <el-tag type="primary" v-if="parseInt(scope.row.device) == 2">安卓</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column label="图片" prop="resource_url" width="150">
                            <template scope="scope">
                                <!-- <img :src="scope.row.resource_url" v-if="scope.row.resource_url != ''" style="color: #12cf66; width: 100px;height: 60px;"> -->
                                <img :src="scope.row.resource_url" @click="showPic($event, scope.row.id, scope.$index, scope.row, scope.row.resource_url)" v-if="scope.row.resource_url != '' " data-title="图片预览" style="width: 120px; height: 60px;cursor: pointer">
                                <a href="#" v-if="scope.row.resource_url == ''">--<a>
                            </template>
                        </el-table-column> 
                        <el-table-column prop="pay_type" label="支付方式" width="130">
                            <template scope="scope">
                                <el-tag type="primary" v-if="parseInt(scope.row.pay_type) == 1">支付宝</el-tag>
                                <el-tag type="warning" v-if="parseInt(scope.row.pay_type) == 2">线下</el-tag>
                                <el-tag type="success" v-if="parseInt(scope.row.pay_type) == 3">微信</el-tag>
                                <el-tag type="danger" v-if="parseInt(scope.row.pay_type) == 4">银联</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column prop="is_promote" label="打折否" width="130">
                            <template scope="scope">
                                <el-button type="success" @click="changePromote(scope.row.id, 1)" size="small" v-if="parseInt(scope.row.is_promote) == 0">否</el-button>
                                <el-button type="primary" @click="changePromote(scope.row.id, 0)" size="small" v-if="parseInt(scope.row.is_promote) == 1">是</el-button>
                            </template>
                        </el-table-column>
                        <el-table-column prop="over_time" label="过期时间" sortable min-width="150" show-overflow-tooltip></el-table-column> 
                        <el-table-column label="操作" fixed="right" width="140">
                            <template scope="scope">
                                <a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.id, scope.$index, list)"><i class="el-icon-delete"></i></a>
                                <a title="编辑" data-title="更新订单" style="margin-left:8px;" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
                            </template>
                        </el-table-column>
                    </el-table>
                </el-tab-pane>
                <el-tab-pane label="已删除" name="fifth">
                    <el-table :data="deleted_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange">
                        <!-- <el-table-column type="selection" width="50"></el-table-column> -->
                        <el-table-column type="expand">
                            <template scope="props">
                                <el-form label-position="left" inline class="demo-table-expand">
                                    <el-form-item label="订单ID">
                                        <span>{{ props.row.id }}</span>
                                    </el-form-item>
                                    <el-form-item label="内容标题">
                                        <span>{{ props.row.ads_title }}</span>
                                    </el-form-item>
                                    <el-form-item label="原始价格">
                                        <span>￥{{ props.row.original_price }}元</span>
                                    </el-form-item>
                                    <el-form-item label="最终价格">
                                        <span>￥{{ props.row.final_price }}元</span>
                                    </el-form-item>
                                </el-form>
                            </template>
                        </el-table-column>
                        <el-table-column prop="id" label="ID" width="80" sortable show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="title" label="广告类型" width="150" show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="buyer_name" label="买家" min-width="120" show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="order_no" label="订单号" min-width="180" show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="unique_trade_no" label="唯一支付码" min-width="120" show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="resource_type" label="资源类型" width="130">
                            <template scope="scope">
                                <el-tag type="success" v-if="parseInt(scope.row.resource_type) == 1">图片</el-tag>
                                <el-tag type="primary" v-if="parseInt(scope.row.resource_type) == 2">视频</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column prop="device" label="设备类型" width="130">
                            <template scope="scope">
                                <el-tag type="success" v-if="parseInt(scope.row.device) == 1">苹果</el-tag>
                                <el-tag type="primary" v-if="parseInt(scope.row.device) == 2">安卓</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column label="图片" prop="resource_url" width="150">
                            <template scope="scope">
                                <!-- <img :src="scope.row.resource_url" v-if="scope.row.resource_url != ''" style="color: #12cf66; width: 100px;height: 60px;"> -->
                                <img :src="scope.row.resource_url" @click="showPic($event, scope.row.id, scope.$index, scope.row, scope.row.resource_url)" v-if="scope.row.resource_url != '' " data-title="图片预览" style="width: 120px; height: 60px;cursor: pointer">
                                <a href="#" v-if="scope.row.resource_url == ''">--<a>
                            </template>
                        </el-table-column> 
                        <el-table-column prop="pay_type" label="支付方式" width="130">
                            <template scope="scope">
                                <el-tag type="primary" v-if="parseInt(scope.row.pay_type) == 1">支付宝</el-tag>
                                <el-tag type="warning" v-if="parseInt(scope.row.pay_type) == 2">线下</el-tag>
                                <el-tag type="success" v-if="parseInt(scope.row.pay_type) == 3">微信</el-tag>
                                <el-tag type="danger" v-if="parseInt(scope.row.pay_type) == 4">银联</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column prop="is_promote" label="打折否" width="130">
                            <template scope="scope">
                                <el-button type="success" @click="changePromote(scope.row.id, 1)" size="small" v-if="parseInt(scope.row.is_promote) == 0">否</el-button>
                                <el-button type="primary" @click="changePromote(scope.row.id, 0)" size="small" v-if="parseInt(scope.row.is_promote) == 1">是</el-button>
                            </template>
                        </el-table-column>
                        <el-table-column prop="over_time" label="过期时间" sortable min-width="150" show-overflow-tooltip></el-table-column> 
                        <el-table-column label="操作" fixed="right" width="140">
                            <template scope="scope">
                                <a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.id, scope.$index, list)"><i class="el-icon-delete"></i></a>
                                <a title="编辑" data-title="更新订单" style="margin-left:8px;" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
                            </template>
                        </el-table-column>
                    </el-table>
                </el-tab-pane>
                <el-tab-pane label="退款中" name="sixth">
                    <el-table :data="refunding_list" border tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange">
                        <!-- <el-table-column type="selection" width="50"></el-table-column> -->
                        <el-table-column type="expand">
                            <template scope="props">
                                <el-form label-position="left" inline class="demo-table-expand">
                                    <el-form-item label="订单ID">
                                        <span>{{ props.row.id }}</span>
                                    </el-form-item>
                                    <el-form-item label="内容标题">
                                        <span>{{ props.row.ads_title }}</span>
                                    </el-form-item>
                                    <el-form-item label="原始价格">
                                        <span>￥{{ props.row.original_price }}元</span>
                                    </el-form-item>
                                    <el-form-item label="最终价格">
                                        <span>￥{{ props.row.final_price }}元</span>
                                    </el-form-item>
                                </el-form>
                            </template>
                        </el-table-column>
                        <el-table-column prop="id" label="ID" width="80" sortable show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="title" label="广告类型" width="150" show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="buyer_name" label="买家" min-width="120" show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="order_no" label="订单号" min-width="180" show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="unique_trade_no" label="唯一支付码" min-width="120" show-overflow-tooltip></el-table-column> 
                        <el-table-column prop="resource_type" label="资源类型" width="130">
                            <template scope="scope">
                                <el-tag type="success" v-if="parseInt(scope.row.resource_type) == 1">图片</el-tag>
                                <el-tag type="primary" v-if="parseInt(scope.row.resource_type) == 2">视频</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column prop="device" label="设备类型" width="130">
                            <template scope="scope">
                                <el-tag type="success" v-if="parseInt(scope.row.device) == 1">苹果</el-tag>
                                <el-tag type="primary" v-if="parseInt(scope.row.device) == 2">安卓</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column label="图片" prop="resource_url" width="150">
                            <template scope="scope">
                                <!-- <img :src="scope.row.resource_url" v-if="scope.row.resource_url != ''" style="color: #12cf66; width: 100px;height: 60px;"> -->
                                <img :src="scope.row.resource_url" @click="showPic($event, scope.row.id, scope.$index, scope.row, scope.row.resource_url)" v-if="scope.row.resource_url != '' " data-title="图片预览" style="width: 120px; height: 60px;cursor: pointer">
                                <a href="#" v-if="scope.row.resource_url == ''">--<a>
                            </template>
                        </el-table-column> 
                        <el-table-column prop="pay_type" label="支付方式" width="130">
                            <template scope="scope">
                                <el-tag type="primary" v-if="parseInt(scope.row.pay_type) == 1">支付宝</el-tag>
                                <el-tag type="warning" v-if="parseInt(scope.row.pay_type) == 2">线下</el-tag>
                                <el-tag type="success" v-if="parseInt(scope.row.pay_type) == 3">微信</el-tag>
                                <el-tag type="danger" v-if="parseInt(scope.row.pay_type) == 4">银联</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column prop="is_promote" label="打折否" width="130">
                            <template scope="scope">
                                <el-button type="success" @click="changePromote(scope.row.id, 1)" size="small" v-if="parseInt(scope.row.is_promote) == 0">否</el-button>
                                <el-button type="primary" @click="changePromote(scope.row.id, 0)" size="small" v-if="parseInt(scope.row.is_promote) == 1">是</el-button>
                            </template>
                        </el-table-column>
                        <el-table-column prop="over_time" label="过期时间" sortable min-width="150" show-overflow-tooltip></el-table-column> 
                        <el-table-column label="操作" fixed="right" width="140">
                            <template scope="scope">
                                <a title="删除" style="margin-left:8px;" @click="handleDel(scope.row.id, scope.$index, list)"><i class="el-icon-delete"></i></a>
                                <a title="编辑" data-title="更新订单" style="margin-left:8px;" @click="handleEdit($event, scope.row.id, scope.$index, scope.row)"><i class="el-icon-edit"></i></a>
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

        </div>
        <!--end list-->

	</div>
</div>
<script>
	var vm = new Vue({
		el: '#app',
		data: {
            refreshstatus: false,
            fullscreenLoading: true,
            list: [],
            unpaid_list: [],
            cancel_list: [],
            refund_list: [],
            deleted_list: [],
            refunding_list: [],
            multipleSelection: [],
            activeName: "first",
			order_status: "paid",
            device_options: [
                {value: '', label: "请选择设备类型"},
                {value: 1, label: "苹果"},
                {value: 2, label: "安卓"}
            ],
            pt_options: [
                {value: '', label: "请选择支付类型"},
                {value: 1, label: "支付宝"},
                {value: 2, label: "线下"},
                {value: 3, label: "微信"},
                {value: 4, label: "银联"}
            ],
            list_url: "<?php echo base_url('ads/listAjax')?>?type=order",
            add_url: "<?php echo base_url('ads/addadsorder')?>",
            edit_url: "<?php echo base_url('ads/editads')?>",
            del_url: "<?php echo base_url('ads/delAjax')?>?type=order",
            promote_url:  "<?php echo base_url('ads/handlePromote')?>",
            currentPage: 1,
            page_sizes: [10, 20, 30, 50, 100],
            page_size: 10,
            pagenum: 0,
            count: 0,
            search: {
                pay_type: '',
                device: '',
                keywords: ''
            }
		},
        created: function() {
            var filters = {"p": this.currentPage, "pt": this.search.pay_type, "os": this.order_status, "device": this.search.device, "keywords": this.search.keywords, "s": this.page_size};
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
						this.order_status = 'refund';
						break;
					case 'fifth':
						this.order_status = 'deleted';
						break;
					case 'sixth':
						this.order_status = 'refunding';
						break;
					default:
						this.order_status = 'paid';
						break;
				}
                
				window.history.pushState(null, null, '?os='+this.order_status+'&pt='+this.search.pay_type+'&act='+this.activeName+'&device='+this.search.device+'&keywords='+this.search.keywords+'&p='+ this.currentPage);

				if(this.list.length == 0 || this.unpaid_list.length == 0 ||  this.cancel_list.length == 0 ||  this.refunding_list.length == 0 ||  this.deleted_list.length == 0 || this.refund_list.length == 0) {
					filters = {"p": this.currentPage, "pt": this.search.pay_type, "os": this.order_status, "device": this.search.device, "keywords": this.search.keywords, "s": this.page_size};
                    this.listAjax(filters);
				}
			},
            changePromote: function (id, status) {
                $.ajax ({
                    type: 'post',
                    url: this.promote_url,
                    data: {id: id, status: status},
                    dataType: "json",
					success: function(data) {
                        is_ok = _.isObject(data) && _.has(data, 'code') && _.get(data, 'code') == 200;
                        if (is_ok) {
                            vm.listAjax(vm.currentPage);
                            vm.messageNotice('success', _.get(data, 'msg'));
                        } else {
                            vm.messageNotice('warning', _.get(data, 'msg'));
                        }
                    },
                    error: function (e) {
                        vm.messageNotice('error', '网络出现异常！');
                    }
                });
            },
            showPic: function(e, id, index, row, content){
                layer.open({
					title: e.currentTarget.getAttribute('data-title')
                    ,type: 2
                    ,skin: 'layui-layer-rim' //加上边框
                    ,area: ['100%', '100%'] //宽高
                    ,content: content
                    ,yes: function(){
						layer.closeAll();
					}
                });
            },
            handleSelectionChange: function(val) {
				this.multipleSelection = val;
			},
            listAjax: function (param) {
                $.ajax({
                    type: "post",
                    url: this.list_url,
                    data: param,
                    dataType: "json",
                    async: true,
                    success: function (res) {
                        vm.refreshstatus = false;
                        vm.fullscreenLoading = false;
                        isResOk = _.isObject(res) && _.has(res, 'code') && _.get(res, 'code') == 200;
                        if (isResOk) {
                            vm.list = _.get(res, 'data.list');
                            switch (param.os) {
								case 'paid':
									vm.list = _.get(res, 'data.list');
									break;
								case 'unpaid':
									vm.unpaid_list = _.get(res, 'data.list');
									break;
								case 'cancel':
									vm.cancel_list = _.get(res, 'data.list');
									break;
								case 'refund':
									vm.refund_list = _.get(res, 'data.list');
									break;
								case 'deleted':
									vm.deleted_list = _.get(res, 'data.list');
									break;
								case 'refunding':
									vm.refunding_list = _.get(res, 'data.list');
									break;
								default:
									vm.list = _.get(res, 'data.list');
									break;
                            }
                            // vm.list = _.get(res, 'data.list');
                            vm.pagenum = _.get(res, 'data.pagenum');
                            vm.count = _.get(res, 'data.count');
                            vm.currentPage = _.get(res, 'data.p');
                            // vm.messageNotice('success', _.get(res, 'msg'));
                        } else {
                            vm.messageNotice('success', _.get(res, 'msg'));
                        }
                    },
                    error: function (e) {
                        vm.messageNotice('error', '加载出错！');
                    } 
                });
            },
            handleDel: function(id, index, rows) {
				this.$confirm('此操作将永久删除, 是否继续?', '提示', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning',
					callback: function(action) {
						if(action == 'confirm') {
							vm.delAjax(id);
							// rows.splice(index, 1);
							vm.messageNotice('success', '删除成功!');
						} else {
							return false;
						}
					}
				});
			},
            delAjax: function(id) {
                $.ajax({
                    type: 'post',
                    url: this.del_url,
                    data: {id: id},
                    dataType: 'json',
                    success: function(data) {
                        is_ok = _.isObject(data) && _.has(data, 'code') && _.get(data, 'code') == 200
                        if (is_ok) {
                            var filters = {"p": vm.currentPage, "pt": vm.search.pay_type, "os": vm.order_status, "device": vm.search.device, "keywords": vm.search.keywords, 's': vm.page_size};
                            vm.listAjax(filters);
                        } else {
                            vm.messageNotice('warning', data.msg);
                        }
                    },
                    error: function(e) {
                        vm.messageNotice('error', '网络异常！');
                    }
                });
            },
            handleCurrentChange: function (val) {
                this.refreshstatus = true;
                this.currentPage = val;
                window.history.pushState(null, null, '?p='+val+'&pt='+this.search.pay_type+'&os='+this.order_status+'&device='+this.search.device+'&keywords='+this.search.keywords+'&s='+this.page_size);
                var filters = {"p": this.currentPage, "pt": vm.search.pay_type, "os": vm.order_status, "device": vm.search.device, "keywords": this.search.keywords, 's': this.page_size};
                vm.listAjax(filters);
            },
            handleSizeChange: function (size) {
                this.page_size = size;
                var filters = {"p": this.currentPage, "pt": vm.search.pay_type, "os": vm.order_status, "device": vm.search.device, "keywords": this.search.keywords, 's': this.page_size};
                this.listAjax(filters);
            },
            handleSearch: function () {
                var filters = {"p": this.currentPage, "pt": vm.search.pay_type, "os": vm.order_status, "device": vm.search.device, "keywords": this.search.keywords, 's': this.page_size};
                vm.listAjax(filters);
            },
            handleRefresh: function() {
				this.refreshstatus = true;
				this.listAjax({"os": this.order_status});
			},
            handleAdd: function(e) {
                this.showLayer(e, '60%', 'lb', this.add_url);
            },
            handleEdit: function(e, id, index, row) {
				this.showLayer(e, '60%', 'lb', this.edit_url+'?id='+id+'&type=order');
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