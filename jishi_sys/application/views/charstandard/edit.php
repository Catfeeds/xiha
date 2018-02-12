<!-- main -->
<div style="width: 800px; margin-left: 10px;">
  <el-form :model="form" ref="form" :rules="rules" label-width="120px" :label-position="labelPosition">
    <el-tabs v-model="activeName">
      <el-tab-pane label="基本信息" name="first">
        <el-form-item label="收费编号" prop="seq" required>
          <el-input v-model="form.seq"></el-input>
        </el-form-item>
        <el-form-item label="培训车型" prop="vehicletype" required>
          <el-select v-model="cacheForm.vehicletype" multiple placeholder="请选择" @change="handleChangeVehicletype">
          <el-option
           v-for="item in drioptions"
           :label="item"
           :value="item">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="价格" prop="price" required>
          <el-input v-model="form.price"></el-input>
        </el-form-item>
        <el-form-item label="班型名称" prop="classcurr" required>
          <el-input v-model="form.classcurr"></el-input>
        </el-form-item>
        <el-form-item label="更新时间" prop="uptime" required>
          <el-date-picker
            v-model="form.uptime"
            type="date"
            placeholder="请选择日期"
            :editable="true"
            :clearable="true">
          </el-date-picker>
        </el-form-item>
      </el-tab-pane>

      <el-tab-pane label="更多信息" name="second">
        <el-form-item label="培训模式" prop="trainingmode">
          <el-select v-model="form.trainingmode" placeholder="请选择">
          <el-option
           v-for="item in trainingmodeoptions"
           :label="item.label"
           :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="培训部分" prop="subject">
          <el-select v-model="form.subject" placeholder="请选择">
          <el-option
           v-for="item in subjectoptions"
           :label="item.label"
           :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="培训时段" prop="trainingtime">
          <el-select v-model="form.trainingtime" placeholder="请选择">
          <el-option
           v-for="item in trainingtimeoptions"
           :label="item.label"
           :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="收费模式" prop="chargemode">
          <el-select v-model="form.chargemode" placeholder="请选择">
          <el-option
           v-for="item in chargemodeoptions"
           :label="item.label"
           :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="付费模式" prop="paymode">
          <el-select v-model="form.paymode" placeholder="请选择">
          <el-option
           v-for="item in paymodeoptions"
           :label="item.label"
           :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="服务内容" prop="service">
          <el-input type="textarea" v-model="form.service"></el-input>
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
