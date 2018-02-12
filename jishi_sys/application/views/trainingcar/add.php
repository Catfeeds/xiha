<!-- main -->
<div style="width: 800px; margin-left: 10px;">
  <el-form :model="form" ref="form" :rules="rules" label-width="100px" :label-position="labelPosition">
    <el-tabs v-model="activeName">
      <el-tab-pane label="基本信息" name="first">
        <el-form-item label="车辆牌号" prop="licnum" required>
          <el-input v-model="form.licnum" placeholder="如：皖A88888"></el-input>
        </el-form-item>
        <el-form-item label="车牌颜色" prop="platecolor" required>
          <el-select v-model="form.platecolor" placeholder="请选择">
          <el-option
           v-for="item in colorOptions"
           :label="item.label"
           :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="培训车型" prop="perdritype" required>
          <el-select v-model="form.perdritype" placeholder="请选择">
          <el-option
           v-for="item in drioptions"
           :label="item"
           :value="item">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="生产厂家" prop="manufacture" required>
          <el-input v-model="form.manufacture" placeholder="如：上汽大众"></el-input>
        </el-form-item>
        <el-form-item label="车辆品牌" prop="brand" required>
          <el-input v-model="form.brand" placeholder="如：桑塔那"></el-input>
        </el-form-item>
      </el-tab-pane>

      <el-tab-pane label="更多信息" name="second">
        <el-form-item label="车辆型号" prop="model">
          <el-input v-model="form.model" placeholder="如：桑塔那2000"></el-input>
        </el-form-item>
        <el-form-item label="车架号" prop="franum">
          <el-input v-model="form.franum"></el-input>
        </el-form-item>
        <el-form-item label="发动机号" prop="engnum">
          <el-input v-model="form.engnum"></el-input>
        </el-form-item>
        <el-form-item label="购买日期" prop="buydate">
          <el-date-picker
            v-model="form.buydate"
            type="date"
            placeholder="请选择日期"
            :editable="true"
            :clearable="true">
          </el-date-picker>
        </el-form-item>
        <el-form-item label="附件" prop="photo">
          <el-upload
          class="avatar-uploader"
          :action="upload_vehimg_url"
          :show-file-list="false"
          :on-success="handleVehimgSuccess"
          :before-upload="beforeVehimgUpload">
          <img v-if="vehimgurl" :src="vehimgurl" class="avatar" />
          <i v-else class="el-icon-plus avatar-uploader-icon" ></i>
          </el-upload>
        </el-form-item>
      </el-tab-pane>
    </el-tabs>

    <el-form-item>
      <el-button type="success" icon="circle-check" @click="submit('form')">提交</el-button>
      <el-button :plain="true" @click="reset('form')">重置</el-button>
    </el-form-item>
  </el-form>
</div>