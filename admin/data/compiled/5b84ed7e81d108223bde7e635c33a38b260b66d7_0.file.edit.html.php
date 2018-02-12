<?php /* Smarty version 3.1.27, created on 2015-10-17 12:53:15
         compiled from "E:\web\admin\templates\manager\edit.html" */ ?>
<?php
/*%%SmartyHeaderCode:273805621d43b95f8d5_27635986%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5b84ed7e81d108223bde7e635c33a38b260b66d7' => 
    array (
      0 => 'E:\\web\\admin\\templates\\manager\\edit.html',
      1 => 1445057252,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '273805621d43b95f8d5_27635986',
  'variables' => 
  array (
    'managerinfo' => 0,
    'role_list' => 0,
    'value' => 0,
    'school_list' => 0,
    'id' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_5621d43ba13400_22037562',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5621d43ba13400_22037562')) {
function content_5621d43ba13400_22037562 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '273805621d43b95f8d5_27635986';
echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
?>

  <div class="tab-body">
    <br />
    <div class="tab-panel active" id="tab-set">
        <form method="post" class="form-x" style="width:50%" action="index.php?action=manager&op=editoperate" onSubmit="return passwordcheck();">

            <div class="form-group">
                <div class="label"><label for="manage_name">管理员</label></div>
                <div class="field">
                     <input type="text" class="input" id="manage_name" name="manage_name" value="<?php echo $_smarty_tpl->tpl_vars['managerinfo']->value['name'];?>
" size="50" placeholder="请填写姓名" data-validate="required:请填写姓名" /><a href="javascript:;" class="button bg-yellow" onclick="javascript:checknamerepeat()">检测重复</a>
                     <span id="tip_name" class="text-dot"></span>
                </div>
            </div>
            
             <div class="form-group">
                <div class="label"><label for="role_id">管理角色</label></div>
                <div class="field">
                     <select class="input" id="role_id" style="width:30%; float:left" name="role_id"> 
                         <option value="">请选择角色</option> 
                         <?php
$_from = $_smarty_tpl->tpl_vars['role_list']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['value']->_loop = false;
$_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->_loop = true;
$foreach_value_Sav = $_smarty_tpl->tpl_vars['value'];
?>
                         <option value="<?php echo $_smarty_tpl->tpl_vars['value']->value['l_role_id'];?>
"<?php if ($_smarty_tpl->tpl_vars['value']->value['l_role_id'] == $_smarty_tpl->tpl_vars['managerinfo']->value['role_permission_id']) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['value']->value['s_rolename'];?>
</option> 
                         <?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
                     </select>
                </div>
            </div>
        
             <div class="form-group">
                <div class="label"><label for="school_id">所属驾校</label></div>
                <div class="field">
                     <select class="input" id="school_id" style="width:30%; float:left" name="school_id"> 
                         <option value="0">请选择驾校</option> 
                         <?php
$_from = $_smarty_tpl->tpl_vars['school_list']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['value']->_loop = false;
$_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->_loop = true;
$foreach_value_Sav = $_smarty_tpl->tpl_vars['value'];
?>
                         <option value="<?php echo $_smarty_tpl->tpl_vars['value']->value['school_id'];?>
" <?php if ($_smarty_tpl->tpl_vars['managerinfo']->value['school_id'] == $_smarty_tpl->tpl_vars['value']->value['school_id']) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['value']->value['school_name'];?>
</option> 
                         <?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
                     </select>
                </div>
            </div>

             <div class="form-group">
                <div class="label"><label for="manage_content">登录名</label></div>
                <div class="field">
                     <input type="text" class="input" id="manage_content" name="manage_content" value="<?php echo $_smarty_tpl->tpl_vars['managerinfo']->value['content'];?>
" size="50" placeholder="请填写备注:例如某某驾校" data-validate="required:请填写登录名 例如：某某驾校" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="manage_password">密码</label></div>
                <div class="field">
                     <input type="password" class="input" id="manage_password" name="manage_password" value="" size="50" placeholder="请填写密码" data-validate="required:请填写密码" />
                </div>
            </div>
            
            <div class="form-group">
                <div class="label"><label for="manage_repeat_password">重复密码</label></div>
                <div class="field">
                     <input type="password" class="input" id="manage_repeat_password" name="manage_repeat_password" value="" size="50" placeholder="请填写重复密码" data-validate="required:请填写重复密码" />
                </div>
            </div>
            <input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" name="id">
            <div class="form-button"><button class="button bg-main" type="submit">提交</button></div>
        </form>
    </div>

    <?php echo '<script'; ?>
>

    // 验证密码是否一致
    function passwordcheck() {
        var manage_password = $('#manage_password').val()
        var manage_repeat_password = $('#manage_repeat_password').val();
        if(manage_password != manage_repeat_password) {
            alert('密码不一致！');
            return false;
        }
    }

    // 检测用户名是否重复
    function checknamerepeat() {
        var manage_name = $('#manage_name').val();
        if(manage_name == '') {
            $('#tip_name').html('请填写姓名以便检查');
            return false;
        }
        $.ajax({
            type:"POST",
            url:"index.php?action=manager&op=usercheck",
            data:{'name':manage_name},
            dataType:"JSON",
            success:function(data) {
                if(data.code == 1) {
                    $('#tip_name').html('这个名称不可使用');
                } else {
                    $('#tip_name').html('这个名称可以使用');
                }
            }
        })
    }

    // // 上传图片预览
    //     window.onload = function () { 
    //         new uploadPreview({ UpBtn: "license_img", DivShow: "imgdiv", ImgShow: "imgShow" });
    //     }

    //     // 城市联动
    //     $('#province').change(function() {
    //         var province_id = $(this).val();
    //         $("#city").load("index.php?action=school&op=getcity&province_id="+province_id);
    //     });

    //     $('#city').change(function() {
    //         var city_id = $(this).val();
    //         $('#area').load('index.php?action=school&op=getarea&city_id='+city_id);
    //         var city_html = $(this).find('option:selected').html();
    //         var province_html = $('#province').find('option:selected').html();
    //         $('#s_address').val(province_html+city_html);
    //     })

    //     $('#area').change(function() {
    //         var city_html = $('#city').find('option:selected').html();
    //         var province_html = $('#province').find('option:selected').html();
    //         var area_html = $(this).find('option:selected').html();
    //         $('#s_address').val(province_html+city_html+area_html);
    //     })
    <?php echo '</script'; ?>
>
<?php echo $_smarty_tpl->getSubTemplate ("library/footer.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
?>

<?php }
}
?>