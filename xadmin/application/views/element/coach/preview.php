    <script src="<?php echo base_url('assets/element/js/echarts.min.js'); ?>"></script>
<style>
  .el-row {
    margin-bottom: 20px;
    &:last-child {
      margin-bottom: 0;
    }
  }
  .el-col {
    border-radius: 4px;
  }
  .bg-purple-dark {
    background: #99a9bf;
  }
  .bg-purple {
    background: #d3dce6;
  }
  .bg-purple-light {
    background: #e5e9f2;
  }
  .grid-content {
    border-radius: 4px;
    height: 36px; line-height: 36px;
    margin-left:5px;
  }
  .row-bg {
    padding: 10px 0;
    background-color: #f9fafc;
  }
</style>
<div id="app" v-cloak>
    <div class="iframe-content">
        <el-form :model="ruleForm" ref="ruleForm" label-width="120px" class="demo-ruleForm" :label-position="labelPosition">
        	<el-tabs >
		        <el-tag ><?php echo $detail['s_coach_name']; ?></el-tag>
				<el-tag  type="gray"><?php echo $detail['s_school_name']; ?></el-tag>
			</el-tabs>
			<el-tabs v-model="activeName" >
				<!-- <el-tab-pane label="主页" name="first">主页</el-tab-pane> -->
				<el-tab-pane label="资料" name="second">
                    <el-row>
                      <el-col :span="12">
                          <div class="grid-content bg-purple">姓名：<?php echo $detail['s_coach_name']; ?></div>
                      </el-col>
                      <el-col :span="12">
                          <div class="grid-content bg-purple-light">性别：<?php if($detail['s_coach_sex']==1){echo '男';}else {echo '女';} ?></div>
                      </el-col>
                    </el-row>
                    <el-row>
                      <el-col :span="12"><div class="grid-content bg-purple">教龄：<?php echo $detail['s_teach_age']; ?></div></el-col>
                      <el-col :span="12"><div class="grid-content bg-purple-light">星级：<?php echo $detail['star_label']; ?></div></el-col>
                    </el-row>
                    <el-row>
                      <el-col :span="12"><div class="grid-content bg-purple">级别：<?php echo $detail['level']; ?></div></el-col>
                      <el-col :span="12"><div class="grid-content bg-purple-light">地址：<?php echo $detail['s_coach_address']; ?></div></el-col>
                    </el-row>
                    <el-row>
                      <el-col :span="12"><div class="grid-content bg-purple">所属驾校：<?php echo $detail['s_school_name']; ?></div></el-col>
                      <el-col :span="12"><div class="grid-content bg-purple-light">认证状态：<?php echo $detail['verify']; ?></div></el-col>
                    </el-row>
                    <el-row>
                      <el-col :span="12"><div class="grid-content bg-purple">教练等级：<?php echo $detail['level']; ?></div></el-col>
                      <el-col :span="12"><div class="grid-content bg-purple-light">车辆牌照：<?php echo $detail['name']; ?></div></el-col>
                    </el-row>
                    <el-row>
                      <el-col :span="24"><div class="grid-content bg-purple">自我介绍：<?php if($detail['s_coach_content']){echo $detail['s_coach_content'];}else{echo '暂未填写';} ?></div></el-col>
                    </el-row>
                    <el-row>
                      <el-col :span="12"><div class="grid-content bg-purple">手机号：<?php echo $detail['s_coach_phone']; ?></div></el-col>
                      <el-col :span="12"><div class="grid-content bg-purple-light">二维码：<?php echo $detail['s_coach_qrcode']; ?></div></el-col>
                    </el-row>
                    <el-row>
                      <el-col :span="12"><div class="grid-content bg-purple">培训科目：<?php echo $detail['lesson_name']; ?></div></el-col>
                      <el-col :span="12"><div class="grid-content bg-purple-light">培训牌照：<?php echo $detail['license_name']; ?></div></el-col>
                    </el-row>
                    <el-row>
                      <el-col :span="12"><div class="grid-content bg-purple">计时培训支持：<?php echo $detail['timetraining_supported_value']; ?></div></el-col>
                      <el-col :span="12"><div class="grid-content bg-purple-light">计时培训最少价格：<?php echo $detail['timetraining_min_price']; ?> 元/小时</div></el-col>
                    </el-row>
                    <el-row>
                      <el-col :span="12"><div class="grid-content bg-purple">银行卡号：<?php if(!$detail['s_yh_zhanghao']){echo '暂未填写';}else{echo $detail['s_yh_zhanghao'];} ?></div></el-col>
                      <el-col :span="12"><div class="grid-content bg-purple-light">所属银行：<?php if(!$detail['s_yh_name']){echo '暂未填写';}else{echo $detail['s_yh_name'];} ?></div></el-col>
                    </el-row>
                </el-tab-pane>
				<!-- <el-tab-pane label="预约情况" name="third">
                    <el-form-item label="上午时间设置" prop="date_time" required >
                    	<el-table ref="multipleTable" v-model="ruleForm.coach_date_time" :data="timelist" border tooltip-effect="dark" style="width: 100%"  >
							<el-table-column fixed type="selection" width="50"></el-table-column>
							<el-table-column prop="id" label="ID" sortable width="80"></el-table-column>
							<el-table-column prop="final_start_time"  label="开始时间" width="208"></el-table-column>
							<el-table-column prop="final_end_time" label="结束时间" width="208"></el-table-column>
							<el-table-column prop="status" label="是否可被预约" width="208">
								<template scope="scope">
									<el-button type="success" v-if="parseInt(scope.row.status) == 1">可以</el-button>
						      	</template>
							</el-table-column>
						</el-table>
                    </el-form-item>
				</el-tab-pane> -->
				<el-tab-pane label="报名情况" name="fourth">
                    <el-form-item label="班制信息" prop="date_time" required >
                    	<el-table ref="multipleTable"  :data="coach_singnupinfo" border tooltip-effect="dark" style="width: 60%"  >
							<el-table-column prop="shifts_title"  label="班制名称" width="250"></el-table-column>
							<el-table-column prop="count" label="报名人数" width="250"></el-table-column>
						</el-table>
                    </el-form-item>
				</el-tab-pane>
				<el-tab-pane label="评价情况" name="fifth">
                    <div id="signup_comment_info" style="border: 1px solid #ddd; height: 600px; width: 400px; float:left; margin: 35px;"></div>
                    <div id="appoint_comment_info" style="border: 1px solid #ddd; height: 600px; width: 400px; float:left; margin: 35px;"></div>
				</el-tab-pane>
			</el-tabs>
		</el-form>
    </div>
</div>
<script>
	Vue.config.devtools = true;
    var vm = new Vue({
        el: "#app",
        data: {
            labelPosition: 'right',
            ruleForm: {
                s_coach_name:"<?php echo $detail['s_coach_name']?>",
            },
            timelist:'',
            coach_singnupinfo: '',
            base_url: "<?php echo base_url('coach/setCoachTimeConf')?>",
            time_url: "<?php echo base_url('coach/getTimeList')?>",
            coach_singnupinfo_url: "<?php echo base_url('coach/getCoachSignUpInfo')?>",
            activeName: 'second',
        },
        created: function() {
            this.gettimelist();
            this.getCoachSignUpInfo("<?php echo $detail['l_coach_id']?>");
        },
        methods: {
        	handleSelectionChange: function(val) {
				this.multipleSelection = val;
			},
            messageNotice: function(type, msg) {
				this.$message({
					type: type,
					message: msg
				});
			},
            submitForm: function(formName) {
                this.$refs[formName].validate(function(valid) {
                    if (valid) {
                        vm.editAjax(vm.ruleForm);
                    } else {
                        return false;
                    }
                });
            },
            gettimelist: function (param) {
            	$.ajax({
                    type: 'post',
                    url: this.time_url,
                    data: param,
                    dataType: 'json',
                    success:function(ret) {
                    	vm.timelist = ret.coach_date_time;
                    },
                    error: function() {
                        vm.messageNotice('warning', '获取时间列表错误');
                    }
                });
            },
            getCoachSignUpInfo: function (coach_id) {
                $.ajax({
                    type: 'post',
                    url: this.coach_singnupinfo_url,
                    data: {"coach_id":coach_id},
                    dataType: 'json',
                    success:function(ret) {
                        console.log(ret.data);return;
                    	vm.coach_singnupinfo = ret.data.coach_signup_list;
                    },
                    error: function() {
                        vm.messageNotice('warning', '获取报名情况错误');
                    }
                });
            }
    	}
    });

    var signup_comment_info = echarts.init(document.getElementById('signup_comment_info'));
    signup_comment_info.setOption({
        toolbox: {
            show: true,
            orient: 'vertical',
            left: 'right',
            top: 'center',
            feature: {
                dataView: {readOnly: false},
                restore: {},
                saveAsImage: {}
            }
        },
        title: {
            text: '报名班制评价情况',
            subtext: '嘻哈学车统计',
            sublink: 'http://www.xihaxueche.com',
            left: 'center'
        },
        tooltip : {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        series : [
            {
                name: '评价来源',
                type: 'pie',
                radius: '55%',
                center: ['50%', '50%'],
                data:[
                    {value:"<?php echo $detail['comment_chart1']['star_good_count'];?>", name:'好评'},
                    {value:"<?php echo $detail['comment_chart1']['star_bad_count'];?>", name:'差评'},
                    {value:"<?php echo $detail['comment_chart1']['star_mid_count'];?>", name:'中评'},
                ].sort(function (a, b) { return a.value - b.value}),

            }
        ]
    });
    var appoint_comment_info = echarts.init(document.getElementById('appoint_comment_info'));
    appoint_comment_info.setOption({
        toolbox: {
            show: true,
            orient: 'vertical',
            left: 'right',
            top: 'center',
            feature: {
                dataView: {readOnly: false},
                restore: {},
                saveAsImage: {}
            }
        },
        title: {
            text: '预约计时评价情况',
            subtext: '嘻哈学车统计',
            sublink: 'http://www.xihaxueche.com',
            left: 'center'
        },
        tooltip : {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        series : [
            {
                name: '评价来源',
                type: 'pie',
                radius: '55%',
                center: ['50%', '50%'],
                data:[
                    {value:"<?php echo $detail['comment_chart2']['star_good_count'];?>", name:'好评'},
                    {value:"<?php echo $detail['comment_chart2']['star_bad_count'];?>", name:'差评'},
                    {value:"<?php echo $detail['comment_chart2']['star_mid_count'];?>", name:'中评'},
                ].sort(function (a, b) { return a.value - b.value}),

            }
        ]
    });
</script>
