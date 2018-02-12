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
    <el-button type="success" @click.active.prevent="handleAdd" id="add" data-title="学员"><i class="el-icon-plus"></i> 学员</el-button>
    <el-button type="success" style="float:right;" :loading="refreshstatus" @click="handleRefresh">刷新</el-button>
  </div>

  <!-- table -->
  <div class="main-content">
    <el-table v-loading="loading" element-loading-text="拼命加载中" :data="list" border style="width: 100%;">
      <el-table-column type="selection"></el-table-column>
      <el-table-column type="expand">
        <template scope="props">
          <el-form label-position="left">
            <el-form-item label="ID">
              <span>{{ props.row.stuid }}</span>
            </el-form-item>
            <el-form-item label="姓名">
              <span>{{ props.row.name }}</span>
            </el-form-item>
            <el-form-item label="性别">
              <span v-if="props.row.sex === '1'"     >男</span>
              <span v-else-if="props.row.sex === '2'">女</span>
              <span v-else>其他</span>
            </el-form-item>
            <el-form-item label="业务类型">
              <span v-if="props.row.busitype === '0'"     >初领</span>
              <span v-else-if="props.row.busitype === '1'">增领</span>
              <span v-else-if="props.row.busitype === '9'">其他</span>
              <span v-else>其他</span>
            </el-form-item>
            <el-form-item label="培训车型">
              <span>{{ props.row.traintype }}</span>
            </el-form-item>
            <el-form-item label="报名时间">
              <span>{{ props.row.applydate }}</span>
            </el-form-item>
            <el-form-item label="手机号">
              <span>{{ props.row.phone }}</span>
            </el-form-item>
            <el-form-item label="国籍">
              <span>{{ props.row.nationality }}</span>
            </el-form-item>
            <el-form-item label="地址">
              <span>{{ props.row.address }}</span>
            </el-form-item>
            <el-form-item label="证件类型">
              <span v-if="props.row.cardtype === '1'"     >身份证</span>
              <span v-else-if="props.row.cardtype === '2'">护照</span>
              <span v-else-if="props.row.cardtype === '3'">军官证</span>
              <span v-else-if="props.row.cardtype === '4'">其他</span>
              <span v-else>其他</span>
            </el-form-item>
            <el-form-item label="证件号码">
              <span>{{ props.row.idcard }}</span>
            </el-form-item>
            <el-form-item label="原始准驾车型">
              <span>{{ props.row.perdritype }}</span>
            </el-form-item>
            <el-form-item label="初次领证时间">
              <span>{{ props.row.fstdrilicdate }}</span>
            </el-form-item>
            <el-form-item label="驾驶证号">
              <span>{{ props.row.drilicnum }}</span>
            </el-form-item>
            <el-form-item label="学员统一编号">
              <span>{{ props.row.stunum }}</span>
            </el-form-item>
          </el-form>
        </template>
      </el-table-column>
      <el-table-column prop="stuid" label="ID"></el-table-column>
      <el-table-column prop="name" label="姓名"></el-table-column>
      <el-table-column prop="sex" label="性别">
        <template scope="scope">
          <template type="success" v-if="scope.row.sex === '1'">男</template>
          <template type="danger" v-else-if="scope.row.sex === '2'">女</template>
          <template type="warning" v-else>其他</template>
        </template>
      </el-table-column>
      <el-table-column prop="phone" label="手机号" min-width="120"></el-table-column>
      <el-table-column prop="traintype" label="培训车型"></el-table-column>
      <el-table-column prop="applydate" label="报名时间"></el-table-column>
      <el-table-column label="操作">
        <template scope="scope">
          <el-button size="small" type="text" data-title="编辑" @click="handleEdit(event, scope.row.stuid, scope.row)">编辑</el-button>
          <el-button size="small" type="text" style="color:#ff4949;" @click.native.prevent="handleDel(event, scope.row.stuid)">删除</el-button>
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
