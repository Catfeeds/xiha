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
    <el-button type="success" @click.active.prevent="handleAdd" id="add" data-title="计时终端"><i class="el-icon-plus"></i> 计时终端</el-button>
    <el-button type="success" style="float:right;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
  </div>

  <!-- table -->
  <div class="main-content">
    <el-table v-loading="loading" element-loading-text="拼命加载中" :data="list" border style="width: 100%;">
      <el-table-column type="selection" min-width="40"></el-table-column>
      <el-table-column type="expand">
        <template scope="props">
          <el-form label-position="left">
            <el-form-item label="ID">
              <span>{{ props.row.devid }}</span>
            </el-form-item>
            <el-form-item label="终端厂家">
              <span>{{ props.row.vendor }}</span>
            </el-form-item>
            <el-form-item label="终端型号">
              <span>{{ props.row.model }}</span>
            </el-form-item>
            <el-form-item label="终端类型">
              <span>{{ props.row.termtype }}</span>
            </el-form-item>
            <el-form-item label="终端IMEI或MAC地址">
              <span>{{ props.row.imei }}</span>
            </el-form-item>
            <el-form-item label="出厂序列号">
              <span>{{ props.row.sn }}</span>
            </el-form-item>
            <el-form-item label="终端设备统一编号">
              <span>{{ props.row.devnum }}</span>
            </el-form-item>
          </el-form>
        </template>
      </el-table-column>
      <el-table-column prop="devid" label="ID" min-width="60"></el-table-column>
      <el-table-column prop="vendor" label="生产厂家" min-width="120"></el-table-column>
      <el-table-column prop="model" label="终端型号" min-width="120"></el-table-column>
      <el-table-column prop="termtype" label="终端类型" width="160">
        <template scope="scope">
          <template v-if="scope.row.termtype === '1'"     >车载计程计时终端</template>
          <template v-else-if="scope.row.termtype === '2'">课堂教学计时终端</template>
          <template v-else-if="scope.row.termtype === '3'">模拟训练计时终端</template>
          <template v-else>其他</template>
        </template>
      </el-table-column>
      <el-table-column label="操作" width="120">
        <template scope="scope">
          <el-button size="small" type="text" data-title="编辑" @click="handleEdit(event, scope.row.devid, scope.row)">编辑</el-button>
          <el-button size="small" type="text" style="color:#ff4949;" @click.native.prevent="handleDel(event, scope.row.devid)">删除</el-button>
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
