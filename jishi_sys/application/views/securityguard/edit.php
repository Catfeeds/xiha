<!-- main -->
<div style="width: 800px; margin-left: 10px;">
  <el-form :model="form" ref="form" :rules="rules" label-width="120px" :label-position="labelPosition">
    <el-tabs v-model="activeName">
      <el-tab-pane label="基本信息" name="basicinfo">
        <el-form-item label="姓名" prop="name" required>
          <el-input v-model="form.name"></el-input>
        </el-form-item>
        <el-form-item label="头像" prop="photo" required>
          <el-upload
          class="avatar-uploader"
          :action="upload_securityguardimg_url"
          :show-file-list="false"
          :on-success="handleSecurityguardimgSuccess"
          :before-upload="beforeSecurityguardimgUpload">
          <img v-if="form.securityguardimgurl" :src="form.securityguardimgurl" class="avatar" />
          <i v-else class="el-icon-plus avatar-uploader-icon" ></i>
          </el-upload>
        </el-form-item>
        <el-form-item label="性别" prop="sex" required>
          <el-radio-group v-model="form.sex">
            <el-radio label="1">男士</el-radio>
            <el-radio label="2">女士</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="身份证" prop="idcard" required>
          <el-input v-model="form.idcard"></el-input>
        </el-form-item>
        <el-form-item label="手机" prop="mobile" required>
          <el-input v-model="form.mobile"></el-input>
        </el-form-item>
      </el-tab-pane>

      <el-tab-pane label="职业信息" name="careerinfo">
        <el-form-item label="准驾车型" prop="dripermitted" required>
          <el-select v-model="form.dripermitted" placeholder="请选择">
          <el-option
           v-for="item in drioptions"
           :label="item"
           :value="item">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="准教车型" prop="teachpermitted" required>
          <el-select v-model="form.teachpermitted" placeholder="请选择">
          <el-option
           v-for="item in drioptions"
           :label="item"
           :value="item">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="初次领证" prop="fstdrilicdate" required>
          <el-date-picker
            v-model="form.fstdrilicdate"
            type="date"
            placeholder="请选择日期"
            :editable="true"
            :clearable="true">
          </el-date-picker>
        </el-form-item>
        <el-form-item label="驾驶证号" prop="drilicence" required>
          <el-input v-model="form.drilicence"></el-input>
        </el-form-item>
        <el-form-item label="入职时间" prop="hiredate" required>
          <el-date-picker
            v-model="form.hiredate"
            type="date"
            placeholder="请选择日期"
            :editable="true"
            :clearable="true">
          </el-date-picker>
        </el-form-item>
        <el-form-item label="供职状态" prop="employstatus" required>
          <el-switch
            v-model="cacheForm.employhired"
            on-text="在职"
            off-text="离职"
            @change="handleChangeemploystatus">
          </el-switch>
        </el-form-item>
        <el-form-item label="离职时间" prop="leavedate" v-show="! cacheForm.employhired">
          <el-date-picker
            v-model="form.leavedate"
            type="date"
            placeholder="请选择日期"
            :editable="true"
            :clearable="true"
            @change="handleChangeleavedate">
          </el-date-picker>
        </el-form-item>
      </el-tab-pane>

      <el-tab-pane label="其他信息" name="otherinfo">
        <el-form-item label="联系地址" prop="address">
          <el-input v-model="form.address"></el-input>
        </el-form-item>
        <el-form-item label="指纹" prop="fingerprint">
          <el-upload
          class="avatar-uploader"
          :action="upload_securityguardfp_url"
          :show-file-list="false"
          :on-success="handleSecurityguardfpSuccess"
          :before-upload="beforeSecurityguardfpUpload">
          <img v-if="form.securityguardfpurl" :src="form.securityguardfpurl" class="avatar" />
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
<script>
  var id = <?php echo isset($_GET['id']) ? $_GET['id'] : 0; ?>
</script>
