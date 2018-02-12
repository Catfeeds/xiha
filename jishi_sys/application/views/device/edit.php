<!-- main -->
<div style="width: 800px; margin-left: 10px;">
  <el-form :model="form" ref="form" :rules="rules" label-width="120px" :label-position="labelPosition">
    <el-tabs v-model="activeName">
      <el-tab-pane label="基本信息" name="first">
        <el-form-item label="终端类型" prop="termtype">
          <el-select v-model="form.termtype" placeholder="请选择">
          <el-option
           v-for="item in termtypeoptions"
           :label="item.label"
           :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="生产厂家" prop="vendor">
          <el-input v-model="form.vendor"></el-input>
        </el-form-item>
        <el-form-item label="终端型号" prop="model">
          <el-input v-model="form.model"></el-input>
        </el-form-item>
        <el-form-item label="终端IMEI号" prop="imei">
          <el-input v-model="form.imei"></el-input>
        </el-form-item>
        <el-form-item label="出厂序列号" prop="sn">
          <el-input v-model="form.sn"></el-input>
        </el-form-item>
      </el-tab-pane>

      <el-tab-pane label="更多信息" name="third">
        <el-form-item label="终端证书" prop="key">
          <el-input type="textarea" v-model="form.key"></el-input>
        </el-form-item>
        <el-form-item label="终端证书口令" prop="passwd">
          <el-input v-model="form.passwd"></el-input>
        </el-form-item>
      </el-tab-pane>
    </el-tabs>

    <el-form-item>
      <el-button type="success" icon="circle-check" @click="submit('form')">提交</el-button>
      <el-button :plain="true" @click="reset('form')">重置</el-button>
    </el-form-item>
  </el-form>
</div>
<script>
  var id = <?php echo isset($_GET['id']) ? $_GET['id'] : 0; ?>
</script>
