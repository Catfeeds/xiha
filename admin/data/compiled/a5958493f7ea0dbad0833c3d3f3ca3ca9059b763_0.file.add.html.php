<?php /* Smarty version 3.1.27, created on 2015-08-30 10:16:27
         compiled from "E:\AppServ\www\service\admin\templates\manager\add.html" */ ?>
<?php
/*%%SmartyHeaderCode:522655e2677b051f45_58460043%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a5958493f7ea0dbad0833c3d3f3ca3ca9059b763' => 
    array (
      0 => 'E:\\AppServ\\www\\service\\admin\\templates\\manager\\add.html',
      1 => 1439103189,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '522655e2677b051f45_58460043',
  'variables' => 
  array (
    'permission_list' => 0,
    'key' => 0,
    'value' => 0,
    'k' => 0,
    'v' => 0,
    'school_list' => 0,
    'managerinfo' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_55e2677b10f5e3_06969304',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_55e2677b10f5e3_06969304')) {
function content_55e2677b10f5e3_06969304 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '522655e2677b051f45_58460043';
echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
?>

  <div class="tab-body" width="30%">
    <br />
    <div class="tab-panel active" id="tab-set">
        <form method="post" class="form-x float-left" style="width:35%" action="index.php?action=manager&op=addoperate" onSubmit="return passwordcheck();">

            <div class="form-group">
                <div class="label"><label for="manage_name">管理员姓名</label></div>
                <div class="field">
                     <input type="text" class="input" id="manage_name" name="manage_name" value="" size="50" placeholder="请填写姓名" data-validate="required:请填写姓名" /><a href="javascript:;" class="button bg-yellow" onclick="javascript:checknamerepeat()">检测重复</a>
                     <span id="tip_name" class="text-dot"></span>
                </div>
            </div>

<!--             <div class="form-group">
                <div class="label"><label for="manage_name">权限设置</label></div>
                <div class="field">

                   <div class="tab"> 
                        <div class="tab-head"> 
                            <ul class="tab-nav">
                                <?php
$_from = $_smarty_tpl->tpl_vars['permission_list']->value;
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
                                <li <?php if ($_smarty_tpl->tpl_vars['key']->value == 0) {?>class="active"<?php }?>><a href="#tab-start-<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['value']->value['role_name'];?>
</a></li> 
                                <?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
                            </ul> 
                        </div> 
                        <div class="tab-body">
                            <?php
$_from = $_smarty_tpl->tpl_vars['permission_list']->value;
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
                                <div <?php if ($_smarty_tpl->tpl_vars['key']->value == 0) {?>class="tab-panel active"<?php } else { ?>class="tab-panel"<?php }?> id="tab-start-<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
">
                                    <div class="button-group border-main checkbox"> 
                                        <?php
$_from = $_smarty_tpl->tpl_vars['value']->value['permission_list'];
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['v'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['v']->_loop = false;
$_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
foreach ($_from as $_smarty_tpl->tpl_vars['k']->value => $_smarty_tpl->tpl_vars['v']->value) {
$_smarty_tpl->tpl_vars['v']->_loop = true;
$foreach_v_Sav = $_smarty_tpl->tpl_vars['v'];
?>
                                        <label class="button">
                                        <input name="pintuer" value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" type="checkbox" checked="checked"><?php echo $_smarty_tpl->tpl_vars['v']->value['bigcate_name'];?>
</label> 
                                        <?php
$_smarty_tpl->tpl_vars['v'] = $foreach_v_Sav;
}
?>
                                    </div>
                                </div> 
                            <?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
                        </div> 
                    </div>
                </div>
            </div> -->
            
             <div class="form-group">
                <div class="label"><label for="role_id">管理角色</label></div>
                <div class="field">
                     <select class="input" id="role_id" style="width:30%; float:left" name="role_id"> 
                         <option value="">请选择角色</option> 
                         <?php
$_from = $_smarty_tpl->tpl_vars['permission_list']->value;
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
                         <option value="<?php echo $_smarty_tpl->tpl_vars['value']->value['l_rolepress_id'];?>
"><?php echo $_smarty_tpl->tpl_vars['value']->value['role_name'];?>
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
"><?php echo $_smarty_tpl->tpl_vars['value']->value['school_name'];?>
</option> 
                         <?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
                     </select>
                </div>
            </div>
            
             <div class="form-group">
                <div class="label"><label for="manage_content">备注</label></div>
                <div class="field">
                     <input type="text" class="input" id="manage_content" name="manage_content" value="<?php echo $_smarty_tpl->tpl_vars['managerinfo']->value['content'];?>
" size="50" placeholder="请填写备注:例如某某驾校" data-validate="required:请填写备注" />
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

           <!--  <div class="form-group">
                <div class="label"><label for="license_img">头像</label></div>
                <div class="field">
                    <div class="imgdiv">
                        <img src="" id="imgShow" width="128" height="128" class="img-border radius-small" />
                    </div>

                    <a class="button input-file bg-green" href="javascript:void(0);">+ 浏览文件
                        <input size="100" type="file" name="license_img" value="" id="license_img" data-validate="regexp#.+.(jpg|jpeg|png|gif)$:只能上传jpg|gif|png格式文件" />
                        <input type="hidden" name="oldimg" value="">
                    </a>
                </div>
            </div> -->
            <div class="form-group">
                <div class="label"><label for="school_location">设置坐标</label></div>
                <div class="field">
                    <!-- <iframe src="index.php?action=manager&op=map" frameborder="0" width="100%" height="100%" ></iframe> -->
                    <input type="text" class="input" id="school_location_x" name="school_location_x" value="" size="50" placeholder="请填写经度" data-validate="required:请填写经度" />
                    <input type="text" class="input" id="school_location_y" name="school_location_y" value="" size="50" placeholder="请填写纬度" data-validate="required:请填写纬度" />
                </div>
            </div>
            <div class="form-button"><button class="button bg-main" type="submit">提交</button></div>
        </form>

        <div class="float-left" style=" margin-left:20px; width:60%; height:100%">
            <iframe src="index.php?action=manager&op=map" frameborder="0" width="100%" height="600px" ></iframe>
        </div>        
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