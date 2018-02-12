<!-- menu -->
<nav>
<el-menu :default-active="active_menu" mode="horizontal" @select="handleMenuSelect">
  <el-submenu index="schooladmin">
    <template slot="title">驾培机构</template>
    <el-menu-item index="coach">教练员</el-menu-item>
    <el-menu-item index="trainingcar">训练车</el-menu-item>
    <el-menu-item index="securityguard">安全员</el-menu-item>
    <el-menu-item index="examiner">考核员</el-menu-item>
    <el-menu-item index="charstandard">收费标准</el-menu-item>
  </el-submenu>
  <el-submenu index="studentlearn">
    <template slot="title">学员培训</template>
    <el-menu-item index="student">学员信息</el-menu-item>
  </el-submenu>
  <el-submenu index="timetrainingdevice">
    <template slot="title">计时终端</template>
    <el-menu-item index="device">终端信息</el-menu-item>
  </el-submenu>
</el-menu>
</nav>
