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
    <el-button type="success" @click.active.prevent="handleAdd" id="add" data-title="教练员"><i class="el-icon-plus"></i> 教练员</el-button>
    <el-button type="success" style="float:right;" v-loading="refreshstatus" @click="handleRefresh">刷新</el-button>
  </div>

  <!-- table -->
  <div class="main-content">
    <el-table v-loading="loading" element-loading-text="拼命加载中" :data="list" border style="width: 100%;">
      <el-table-column type="selection" min-width="40"></el-table-column>
      <el-table-column type="expand">
        <template scope="props">
          <el-form label-position="left">
            <el-form-item label="ID">
              <span>{{ props.row.coachid }}</span>
            </el-form-item>
            <el-form-item label="性名">
              <span>{{ props.row.name }}</span>
            </el-form-item>
            <el-form-item label="性别">
              <span v-if="props.row.sex === '1'">男</span>
              <span v-else-if="props.row.sex === '2'">女</span>
              <span v-else>其他</span>
            </el-form-item>
            <el-form-item label="地址">
              <span>{{ props.row.address }}</span>
            </el-form-item>
            <el-form-item label="手机">
              <span>{{ props.row.mobile }}</span>
            </el-form-item>
            <el-form-item label="身份证号">
              <span>{{ props.row.idcard }}</span>
            </el-form-item>
            <el-form-item label="准驾车型">
              <span>{{ props.row.dripermitted }}</span>
            </el-form-item>
            <el-form-item label="准教车型">
              <span>{{ props.row.teachpermitted }}</span>
            </el-form-item>
            <el-form-item label="初次领证时间">
              <span>{{ props.row.fstdrilicdate }}</span>
            </el-form-item>
            <el-form-item label="驾驶证编号">
              <span>{{ props.row.drilicence }}</span>
            </el-form-item>
            <el-form-item label="职业资格等级">
              <span v-if="props.row.occupationlevel === '1'"     >一级</span>
              <span v-else-if="props.row.occupationlevel === '2'">二级</span>
              <span v-else-if="props.row.occupationlevel === '3'">三级</span>
              <span v-else-if="props.row.occupationlevel === '4'">四级</span>
              <span v-else>无</span>
            </el-form-item>
            <el-form-item label="职业资格证号">
              <span>{{ props.row.occupationno }}</span>
            </el-form-item>
            <el-form-item label="入职日期">
              <span>{{ props.row.hiredate }}</span>
            </el-form-item>
            <el-form-item label="供职状态">
              <span v-if="props.row.employstatus === '0'"     >在职</span>
              <span v-else-if="props.row.employstatus === '1'">离职</span>
              <span v-else>未知</span>
            </el-form-item>
            <el-form-item label="离职日期">
              <span>{{ props.row.leavedate }}</span>
            </el-form-item>
            <el-form-item label="教练统一编号">
              <span>{{ props.row.coachnum }}</span>
            </el-form-item>
          </el-form>
        </template>
      </el-table-column>
      <el-table-column prop="coachid" label="ID"></el-table-column>
      <el-table-column prop="name" label="姓名"></el-table-column>
      <el-table-column prop="sex" label="性别">
        <template scope="scope">
          <el-tag type="success" v-if="scope.row.sex === '1'">男</el-tag>
          <el-tag type="danger" v-else-if="scope.row.sex === '2'">女</el-tag>
          <el-tag type="warning" v-else>其他</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="address" label="地址"></el-table-column>
      <el-table-column prop="mobile" label="手机号"></el-table-column>
      <el-table-column label="操作">
        <template scope="scope">
          <el-button size="small" type="text" data-title="编辑" @click="handleEdit(event, scope.row.coachid, scope.row)">编辑</el-button>
          <el-button size="small" type="text" style="color:#ff4949;" @click.native.prevent="handleDel(event, scope.row.coachid)">删除</el-button>
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
