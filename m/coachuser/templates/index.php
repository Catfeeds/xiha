
<?php
    error_reporting(0);
    if(!isset($from) && $from != 'coachuser') {
        echo '404 NOT FOUND';
        exit();
    }
    require_once '../include/config.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="format-detection" content="telephone=no">
        <meta http-equiv="x-rim-auto-match" content="none">
        <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">
        <title>报名信息</title>
        <link rel="stylesheet" href="<?php echo HOST; ?>../m/assets/css/mui.min.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo HOST; ?>../m/assets/css/sweetalert.css">
        <link rel="stylesheet" href="<?php echo HOST; ?>../m/assets/css/style.css" />
        <link rel="stylesheet" href="<?php echo HOST; ?>../m/assets/font/iconfont/iconfont.css" />
        <script src="<?php echo HOST; ?>../m/assets/js/cookie.min.js"></script>
        <style type="text/css">
            .mui-table-view-cell:after {left:0px;}
            .mui-title {color:#555;}
            .mui-bar-nav {border:none;}
        </style>
    </head>
    
<body style="background: #f5f5f5;">
    <header class="mui-bar mui-bar-nav" style ="position: absolute;display: block;overflow-y: scroll;">
        <h1 class="mui-title" style="color:#666;right:0;left:0;">教练 <strong style="color:#333;"><?php echo $coach_name; ?></strong>邀请您填写个人信息</h1>
    </header>
    <!-- <div id="loginauth" class="mui-content" style="position: absolute;display: block;overflow-y: scroll;"> -->
    <div id="loginauth" class="mui-content" style="">
        <form id='login-form' class="mui-input-group" method="post" onSubmit="return formcheck();">
              <input id='year' name="year" value="<?php echo date('Y');?>" type="hidden" class="mui-input" placeholder="" style ="">
              <input id='month' name="month" value="<?php echo date('m');?>" type="hidden" class="mui-input" placeholder="" >
              <input id='day' name="day" value="<?php echo date('d');?>" type="hidden" class="mui-input" placeholder="" >
            <div class="mui-input-row">
              <label><span class="mui-badge mui-badge-danger mui-badge-inverted">*</span>姓名</label>
              <input id='user_name' name="user_name" type="text" class="mui-input" placeholder="请输入姓名" required>
            </div>
            <div class="mui-input-row">
              <label><span class="mui-badge mui-badge-danger mui-badge-inverted">*</span>手机号</label>
              <input id='user_phone' type="text" name="user_phone" class="mui-input" placeholder="请输入手机号" required>
            </div>
            <div class="mui-input-row">
              <label><span class="mui-badge mui-badge-danger mui-badge-inverted">*</span>科目</label>
              <select name="stage" id="stage">
                <option value="1">待定</option>
                <option value="2" selected>科目二</option>
                <option value="3">科目三</option>
                <option value="4">毕业</option>
              </select>
            </div>
            <div class="mui-input-row">
              <label><span class="mui-badge mui-badge-danger mui-badge-inverted">*</span>状态</label>
              <select name="status" id="status">
                <option value="1001" selected>休息中</option>
                <option value="1002">练车中</option>
                <option value="1003">考试中</option>
              </select>
            </div>
            <div class="mui-input-row">
              <label><span class="mui-badge mui-badge-danger mui-badge-inverted">*</span>身份证</label>
              <input id='identify' type="text" name="identify" class="mui-input" placeholder="请填写真实身份证号" required>
            </div> 
            <div class="" id="sendorder" style="margin:10px;">
                <input type="hidden" value="<?php echo $coach_id; ?>" name="coach_id" id="coach_id">
                <button type="button" id='submit_btn' style='padding:10px 0px;border:none; background: #00BD9C;' class='mui-btn mui-btn-block mui-btn-red' >提交</button>
            </div>
        </form>
        <p style="margin:10px;">
            注意事项：<br>
            1、请提交真实信息，教练确认后，完成学员学车登记；<br>
            2、嘻哈学车平台会保证学员信息安全，请放心填写；
        </p>
    <div class="mui-bar-footer" id="hiddentell" style="margin:0 auto;border:0px solid #ccc;height:50px;width:100%;position:relative; bottom:0px;display: block;">
        <input type="hidden" value="<?php echo $coach_id; ?>" name="coach_id" id="coach_id">
        <div style="width:50%;float:left;height:100%;">
            <a href="tel:<?php echo $coach_phone;?>" id='submitF' style='border-right:1px solid #eee;border-radius:0px;border-top:none;border-left:none;border-bottom:none; color: #00BD9C;background: white;' class='mui-btn mui-btn-block mui-btn-red mui-bar-footer' >
               <img src="../../assets/images/phone.png">
               拨打电话
            </a>
        </div>
        <div style="width:50%;float:left;height:100%;">
            <a href="sms:<?php echo $coach_phone;?>" id='submitS' style='border-left:0px solid #eee;border-radius:0px;border-top:none;border-right:none;border-bottom:none; color: #00BD9C;background: white;' class='mui-btn mui-btn-block mui-btn-red mui-bar-footer' >
                <img src="../../assets/images/message.png">
                发送短信
            </a>
            
        </div>
    </div>
    </div>
       
    <script src="<?php echo HOST; ?>../m/assets/js/mui.min.js"></script>
    <script src="<?php echo HOST; ?>../m/assets/js/sweetalert.min.js"></script>
    
    <script>
      
      (function($$){
         //提交添加学员信息
        var submit_btn = document.getElementById('submit_btn');
        submit_btn.addEventListener('tap', function(e) {
            var user_name = document.getElementById('user_name').value;
            var user_phone = document.getElementById('user_phone').value;
            var identify = document.getElementById('identify').value;
            var stage = document.getElementById('stage').value;
            var status = document.getElementById('status').value;
            var year = document.getElementById('year').value;
            var month = document.getElementById('month').value;
            var day = document.getElementById('day').value;
            var coach_id  = document.getElementById('coach_id').value;
//1)判断是否输入姓名
            if(!(user_name.trim()) ) {
                $$.toast('请填写姓名');
                return false;
            }
//2)判断是否输入手机号码          
            if(!(user_phone.trim())){
                $$.toast('请填写手机号');
                return false;
            }
//3)判断手机号码是否正确
            user_phone_length = user_phone.length;
            if(user_phone_length!=11){
               $$.toast('您输入的手机号格式有误！');
               return false;
            } else {
              var reg=/^1[3|4|5|7|8][0-9]\d{8}$/;
              var userphone = reg.test(user_phone);
              if(!userphone){
                $$.toast('您输入的手机号格式有误！');
                return false;   
            }
           }
//4)判断是否输入身份证号码
            if(!(identify.trim())){
                $$.toast('请输入身份证号码！');
                return false;
            }
//5)判断身份证号码格式输入是否真确
            var reg = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/; 
            var  identity = reg.test(identify);
            if(!(identity)){
                $$.toast('您输入的身份证号格式有误！');
                return false;
            }
            var users_json = [{
                'user_name':user_name.trim(), 
                'user_phone':user_phone.trim()
            }];
            users_json = JSON.stringify(users_json);
            var params = {
                'users_json':users_json,
                'identity_id':identify.trim(),
                'stage' : stage,
                'status':status,
                'year':year,
                'month':month,
                'day':day,
                'coach_id':coach_id
            };
            $$.ajax({
                type:"post",
                url:"<?php echo HOST; ?>v2/coachuser/add_users.php",
                async:true,
                data:params,
                dataType:"json",
                beforeSend: function () {
                      submit_btn.innerHTML = '正在提交';
                      submit_btn.setAttribute('disabled', true);
                    },
                success:function(data) {
                    if(data.code == 200) {
                     setTimeout(function(){
                        swal({
                        title: "",
                        text: "您确定要提交吗？",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "确定",
                        cancelButtonText: "取消",
                        closeOnConfirm: false
                        },
                        function(isConfirm){
                         if(isConfirm){
                            swal({
                            title:"", 
                            text:"提交成功", 
                            type:"success",
                         },
                         function(){
                          window.location.href="http://www.xihaxueche.com"; 
                         }); 
                        }else{
                         submit_btn.innerHTML = '提交';
                         submit_btn.removeAttribute('disabled');                            
                         }
                     })
                     },2000);

                    } else {
                        submit_btn.innerHTML = '提交';
                        submit_btn.removeAttribute('disabled');
                        $$.toast(data.data);
                    }
                        
                },
                error:function() {
                    submit_btn.innerHTML = '提交';
                    submit_btn.removeAttribute('disabled');
                    $$.toast('网络错误，请检查网络');
                }
            }); 
        });

        //二级联动
        var stage = document.getElementById('stage');
        var status = document.getElementById('status');
        var status_arr = [];
        stage.addEventListener('change', function(e) {
            switch(this.value) {
                case '1':
                    status_arr = {'1':'待定'};
                    break;
                case '2':
                case '3':
                    status_arr = {'1001':'休息中', '1002':'练车中', '1003':'考试中'};
                    break;

                case '4':
                    status_arr = {'4':'毕业'};
                    break;
            }
            var html = '';
            $$.each(status_arr, function(k,v) {
                html += '<option value="'+k+'">'+v+'</option>';
            });
            status.innerHTML = html;
        });


    })(mui);
 
        
    </script>
  </body>
  <footer>

    </footer>
  </html>     
