<template>
  <div id="app">
    <div class="nav-top-bar">
      <p class="nav-top-logo">
        <span>element-ui管理系统</span>
        <el-dropdown>
          <span class="el-dropdown-link" style="color: #fff; margin-left: 20px; cursor: pointer;">
            快捷入口<i class="el-icon-arrow-down el-icon--right"></i>
          </span>
          <el-dropdown-menu slot="dropdown">
            <el-dropdown-item>资讯</el-dropdown-item>
            <el-dropdown-item>图片</el-dropdown-item>
            <el-dropdown-item>产品</el-dropdown-item>
            <el-dropdown-item>用户</el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </p>
    </div>
    <div class="nav-left-bar">
      <el-row class="tac">
        <el-col :span="8" :style="navObjectClass">
          <el-menu default-active="2" class="el-menu-vertical-demo" @open="handleOpen" @close="handleClose" >
            <el-submenu index="1">
              <template slot="title"><i class="el-icon-message"></i>资讯管理</template>
              <el-menu-item-group>
                <template slot="title">资讯管理</template>
                <el-menu-item index="1-1">选项1</el-menu-item>
                <el-menu-item index="1-2">选项2</el-menu-item>
              </el-menu-item-group>
              <el-menu-item-group title="分组2">
                <el-menu-item index="1-3">选项3</el-menu-item>
              </el-menu-item-group>
              <el-submenu index="1-4">
                <template slot="title">选项4</template>
                <el-menu-item index="1-4-1">选项1</el-menu-item>
              </el-submenu>
            </el-submenu>

            <el-submenu index="4">
              <template slot="title"><i class="el-icon-message"></i>培训机构</template>
              <el-menu-item index="4-1">培训机构管理</el-menu-item>
              <el-menu-item index="4-2">教练员管理</el-menu-item>
              <el-menu-item index="4-3">教练车管理</el-menu-item>
              <el-menu-item index="4-4">教练区域管理</el-menu-item>
              <el-menu-item index="4-5">考核员管理</el-menu-item>
              <el-menu-item index="4-6">安全员管理</el-menu-item>
              <el-menu-item index="4-7">培训时段管理</el-menu-item>
            </el-submenu>

            <el-menu-item index="2"><i class="el-icon-menu"></i>学员管理</el-menu-item>
            <el-menu-item index="3"><i class="el-icon-setting"></i>教练管理</el-menu-item>
          </el-menu>
        </el-col>
      
      </el-row>
    </div>
    <div id="iframe_box" class="Hui-article">
      <div class="show_iframe">
        <div style="display:none" class="loading"></div>
        <iframe scrolling="yes" frameborder="0" src="http://www.baidu.com"></iframe>
      </div>
	  </div>

    <!--<div class="nav-right-content">
      <el-table :data="tableData" style="width: 100%">
        <el-table-column prop="date" label="日期" width="180"></el-table-column>
        <el-table-column prop="name" label="姓名" width="180"></el-table-column>
        <el-table-column prop="address" label="地址"></el-table-column>
      </el-table>
    </div>-->
  </div>
</template>

<script>
export default {
  data () {
    return {
      navObjectClass: {
        'width': '160px'
      },
      activeName: 'first',
      tableData: [{
        date: '2016-05-03',
        name: '王小虎',
        address: '上海市普陀区金沙江路 1518 弄'
      }, {
        date: '2016-05-02',
        name: '王小虎',
        address: '上海市普陀区金沙江路 1518 弄'
      }, {
        date: '2016-05-04',
        name: '王小虎',
        address: '上海市普陀区金沙江路 1518 弄'
      }],
      loading: true
    }
  },
  methods: {
    handleOpen(key, KeyPath) {
      console.log(key, KeyPath);
    },
    handleClose(key, KeyPath) {
      console.log(key, KeyPath);
    },
    handleClick(tab, event) {
      console.log(tab, event);
    }
  }
}
</script>

<style>
body {
  font-family: Helvetica, sans-serif;
}
.nav-top-logo {margin: 0px; color: #fff; line-height: 50px; margin: 0px 20px;}
.nav-top-bar {height: 50px; width: 100%; background: #1D8CE0}
.nav-left-bar { 
    background: #eef1f6; 
    position: absolute;
    top: 50px;
    bottom: 0;
    left: 0;
    width: 160px;
    z-index: 99;
    overflow: auto;
}
.nav-right-content {float:left; width: 100%}
.el-dropdown-menu {top: 45px !important; left: 160px !important;}
.el-dropdown-menu__item {text-align: center}
.Hui-article {    
    position: absolute;
    top: 50px;
    bottom: 0;
    left: 160px;
    right: 0;
    overflow: auto;
    z-index: 1;
}
.show_iframe {
    position: absolute;
    top: 0;
    right: 0;
    left: 0;
    bottom: 0;
}
.show_iframe iframe {
    position: absolute;
    bottom: 0;
    height: 100%;
    width: 100%;
}
</style>
