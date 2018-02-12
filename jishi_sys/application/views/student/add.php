<!-- main -->
<div style="width: 800px; margin-left: 10px;">
  <el-form :model="form" ref="form" :rules="rules" label-width="120px" :label-position="labelPosition">
    <el-tabs v-model="activeName">
      <el-tab-pane label="基本信息" name="first">
        <el-form-item label="姓名" prop="name">
          <el-input v-model="form.name"></el-input>
        </el-form-item>
        <el-form-item label="头像" prop="photo">
          <el-upload
          class="avatar-uploader"
          :action="upload_stuimg_url"
          :show-file-list="false"
          :on-success="handlestuimgSuccess"
          :before-upload="beforestuimgUpload">
          <img v-if="stuimgurl" :src="stuimgurl" class="avatar" />
          <i v-else class="el-icon-plus avatar-uploader-icon" ></i>
          </el-upload>
        </el-form-item>
        <el-form-item label="性别" prop="sex">
          <el-radio-group v-model="form.sex">
            <el-radio label="1">男士</el-radio>
            <el-radio label="2">女士</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="培训车型" prop="traintype">
          <el-select v-model="form.traintype" placeholder="请选择">
          <el-option
           v-for="item in drioptions"
           :label="item"
           :value="item">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="业务类型" prop="busitype">
          <el-select v-model="form.busitype" placeholder="请选择">
          <el-option
           v-for="item in busitypeoptions"
           :label="item.label"
           :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="证件类型" prop="cardtype">
          <el-select v-model="form.cardtype" placeholder="请选择">
          <el-option
           v-for="item in cardtypeoptions"
           :label="item.label"
           :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="证件号" prop="idcard">
          <el-input v-model="form.idcard"></el-input>
        </el-form-item>
        <el-form-item label="手机" prop="phone">
          <el-input v-model="form.phone"></el-input>
        </el-form-item>
        <el-form-item label="国籍" prop="nationality">
          <el-input v-model="form.nationality"></el-input>
        </el-form-item>
        <el-form-item label="报名时间" prop="applydate">
          <el-date-picker
            v-model="form.applydate"
            type="date"
            placeholder="请选择日期"
            :editable="true"
            :clearable="true">
          </el-date-picker>
        </el-form-item>
      </el-tab-pane>

      <el-tab-pane label="更多信息" name="second">
        <el-form-item label="联系地址" prop="address">
          <el-input v-model="form.address"></el-input>
        </el-form-item>
        <el-form-item label="指纹" prop="fingerprint">
          <el-upload
          class="avatar-uploader"
          :action="upload_stufp_url"
          :show-file-list="false"
          :on-success="handlestufpSuccess"
          :before-upload="beforestufpUpload">
          <img v-if="stufpurl" :src="stufpurl" class="avatar" />
          <i v-else class="el-icon-plus avatar-uploader-icon" ></i>
          </el-upload>
        </el-form-item>
        <el-form-item label="原始准驾车型" prop="perdritype">
          <el-select v-model="form.perdritype" placeholder="请选择">
          <el-option
           v-for="item in drioptions"
           :label="item"
           :value="item">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="驾驶证初领日期" prop="fstdrilicdate">
          <el-date-picker
            v-model="form.fstdrilicdate"
            type="date"
            placeholder="请选择日期"
            :editable="true"
            :clearable="true">
          </el-date-picker>
        </el-form-item>
        <el-form-item label="驾驶证号" prop="drilicnum">
          <el-input v-model="form.drilicnum"></el-input>
        </el-form-item>
      </el-tab-pane>
    </el-tabs>

    <el-form-item>
      <el-button type="success" icon="circle-check" @click="submit('form')">提交</el-button>
      <el-button :plain="true" @click="reset('form')">重置</el-button>
    </el-form-item>
  </el-form>
</div>
