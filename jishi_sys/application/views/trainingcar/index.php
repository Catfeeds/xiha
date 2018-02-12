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
    <el-button type="success" @click.active.prevent="handleAdd" id="add" data-title="训练车"><i class="el-icon-plus"></i> 训练车</el-button>
    <el-button type="success" style="float:right;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
  </div>

  <!-- table -->
  <div class="main-content">
    <el-table v-loading="loading" element-loading-text="拼命加载中" :data="list" border style="width: 100%;">
      <el-table-column type="selection" min-width="50"></el-table-column>
      <el-table-column type="expand">
        <template scope="props">
          <el-form label-position="left">
            <el-form-item label="ID">
              <span>{{ props.row.carid }}</span>
            </el-form-item>
            <el-form-item label="车牌号">
              <span>{{ props.row.licnum }}</span>
            </el-form-item>
            <el-form-item label="培训车型">
              <span>{{ props.row.perdritype }}</span>
            </el-form-item>
            <el-form-item label="车牌颜色">
              <span v-if="props.row.platecolor === '1'"     >蓝色</span>
              <span v-else-if="props.row.platecolor === '2'">黄色</span>
              <span v-else-if="props.row.platecolor === '3'">黑色</span>
              <span v-else-if="props.row.platecolor === '4'">白色</span>
              <span v-else-if="props.row.platecolor === '5'">绿色</span>
              <span v-else-if="props.row.platecolor === '9'">其他颜色</span>
              <span v-else>其他颜色</span>
            </el-form-item>
            <el-form-item label="购买日期">
              <span>{{ props.row.buydate }}</span>
            </el-form-item>
            <el-form-item label="厂家">
              <span>{{ props.row.manufacture }}</span>
            </el-form-item>
            <el-form-item label="品牌">
              <span>{{ props.row.brand }}</span>
            </el-form-item>
            <el-form-item label="型号">
              <span>{{ props.row.model }}</span>
            </el-form-item>
            <el-form-item label="车架号">
              <span>{{ props.row.franum }}</span>
            </el-form-item>
            <el-form-item label="发动机号">
              <span>{{ props.row.engnum }}</span>
            </el-form-item>
            <el-form-item label="车辆统一编号">
              <span>{{ props.row.carnum }}</span>
            </el-form-item>
          </el-form>
        </template>
      </el-table-column>
      <el-table-column prop="carid" label="ID"></el-table-column>
      <el-table-column prop="licnum" label="车牌号"></el-table-column>
      <el-table-column prop="perdritype" label="培训车型"></el-table-column>
      <el-table-column prop="platecolor" label="车牌颜色">
        <template scope="scope">
          <template v-if="scope.row.platecolor === '1'">蓝色</template>
          <template v-else-if="scope.row.platecolor === '2'">黄色</template>
          <template v-else-if="scope.row.platecolor === '3'">黑色</template>
          <template v-else-if="scope.row.platecolor === '4'">白色</template>
          <template v-else-if="scope.row.platecolor === '5'">绿色</template>
          <template v-else-if="scope.row.platecolor === '9'">其他</template>
          <template v-else>其他</template>
        </template>
      </el-table-column>
      <el-table-column prop="buydate" label="购买日期"></el-table-column>
      <el-table-column label="操作">
        <template scope="scope">
          <el-button size="small" type="text" data-title="编辑" @click="handleEdit(event, scope.row.carid, scope.row)">编辑</el-button>
          <el-button size="small" type="text" style="color:#ff4949;" @click.native.prevent="handleDel(event, scope.row.carid)">删除</el-button>
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
