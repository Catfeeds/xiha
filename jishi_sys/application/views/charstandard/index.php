<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- main -->
<div class="main">
  <div class="breadcrumb">
    <el-breadcrumb separator='/'>
      <?php foreach ($breadcrumb as $item): ?>
      <el-breadcrumb-item><?php echo $item; ?></el-breadcrumb-item>
      <?php endforeach; ?>
    </el-breadcrumb>
  </div>
  <!-- button -->
  <div class="main-top">
    <el-button type="danger"><i class="el-icon-delete"></i> 批量删除</el-button>
    <el-button type="success" @click.active.prevent="handleAdd" id="add" data-title="收费标准"><i class="el-icon-plus"></i> 收费标准</el-button>
    <el-button type="success" style="float:right;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
  </div>

  <!-- table -->
  <div class="main-content">
    <el-table v-loading="loading" element-loading-text="拼命加载中" :data="list" border style="width: 100%;">
      <el-table-column type="selection"></el-table-column>
      <el-table-column type="expand">
        <template scope="props">
          <el-form label-position="left">
            <el-form-item label="收费编号">
              <span>{{ props.row.seq }}</span>
            </el-form-item>
            <el-form-item label="培训车型">
              <span>{{ props.row.vehicletype }}</span>
            </el-form-item>
            <el-form-item label="价格(元)">
              <span>{{ props.row.price }}</span>
            </el-form-item>
            <el-form-item label="班型名称">
              <span>{{ props.row.classcurr }}</span>
            </el-form-item>
            <el-form-item label="服务内容">
              <span>{{ props.row.service }}</span>
            </el-form-item>
            <el-form-item label="更新时间">
              <span>{{ props.row.uptime }}</span>
            </el-form-item>
            <el-form-item label="培训模式">
              <span v-if="props.row.trainingmode === '1'"     >定时培训</span>
              <span v-else-if="props.row.trainingmode === '2'">预约培训</span>
              <span v-else-if="props.row.trainingmode === '9'">其他</span>
              <span v-else>其他</span>
            </el-form-item>
            <el-form-item label="培训部分">
              <span v-if="props.row.subject === '1'"     >第一部分集中教学</span>
              <span v-else-if="props.row.subject === '2'">第一部分网络教学</span>
              <span v-else-if="props.row.subject === '3'">第四部分集中教学</span>
              <span v-else-if="props.row.subject === '4'">第四部分网络教学</span>
              <span v-else-if="props.row.subject === '5'">模拟器教学</span>
              <span v-else-if="props.row.subject === '6'">第二部分普通教学</span>
              <span v-else-if="props.row.subject === '7'">第二部分智能教学</span>
              <span v-else-if="props.row.subject === '8'">第三部分普通教学</span>
              <span v-else-if="props.row.subject === '9'">第三部分智能教学</span>
              <span v-else>其他</span>
              </el-form-item>
            <el-form-item label="培训时间">
              <span v-if="props.row.trainingtime === '1'"     >普通时段</span>
              <span v-else-if="props.row.trainingtime === '2'">高峰时段</span>
              <span v-else-if="props.row.trainingtime === '3'">节假日时段</span>
              <span v-else>其他</span>
            </el-form-item>
            <el-form-item label="收费模式">
              <span v-if="props.row.chargemode === '1'"     >一次性收费</span>
              <span v-else-if="props.row.chargemode === '2'">计时收费</span>
              <span v-else-if="props.row.chargemode === '9'">其他</span>
              <span v-else>其他</span>
            </el-form-item>
            <el-form-item label="付费模式">
              <span v-if="props.row.paymode === '1'"     >先学后付</span>
              <span v-else-if="props.row.paymode === '2'">先付后学</span>
              <span v-else-if="props.row.paymode === '9'">其他</span>
              <span v-else>其他</span>
            </el-form-item>
          </el-form>
        </template>
      </el-table-column>
      <el-table-column prop="seq" label="收费编号"></el-table-column>
      <el-table-column prop="vehicletype" label="培训车型"></el-table-column>
      <el-table-column prop="price" label="价格(元)"></el-table-column>
      <el-table-column prop="service" label="服务内容" min-width="180"></el-table-column>
      <el-table-column prop="uptime" label="更新时间"></el-table-column>
      <el-table-column label="操作">
        <template scope="scope">
          <el-button size="small" type="text" data-title="编辑" @click="handleEdit(event, scope.row.charstdid, scope.row)">编辑</el-button>
          <el-button size="small" type="text" style="color:#ff4949;" @click.native.prevent="handleDel(event, scope.row.charstdid)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>
  </div>

  <!-- pagination -->
  <div style="padding: 8px; margin: 10px 0; height: 36px; background: #EFF2F7;">
    <div style="float: right;">
      <el-pagination
      @size-change="handleSizeChange"
      @current-change="handleCurrentChange"
      :current-page="currentPage"
      :page-sizes="pageSizeOptions"
      :page-size="pageSize"
      layout="total, sizes, prev, pager, next, jumper"
      :total="totalNum">
      </el-pagination>
    </div>
  </div>
</div><!-- main ends -->
