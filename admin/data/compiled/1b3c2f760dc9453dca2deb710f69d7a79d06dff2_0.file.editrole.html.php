<?php /* Smarty version 3.1.27, created on 2015-10-08 10:51:41
         compiled from "E:\web\admin\templates\manager\editrole.html" */ ?>
<?php
/*%%SmartyHeaderCode:18065615da3dbb4566_09196780%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1b3c2f760dc9453dca2deb710f69d7a79d06dff2' => 
    array (
      0 => 'E:\\web\\admin\\templates\\manager\\editrole.html',
      1 => 1439052996,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18065615da3dbb4566_09196780',
  'variables' => 
  array (
    'roleinfo' => 0,
    'manage_config' => 0,
    'key' => 0,
    'value' => 0,
    'id' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_5615da3dbf1464_01162077',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5615da3dbf1464_01162077')) {
function content_5615da3dbf1464_01162077 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '18065615da3dbb4566_09196780';
echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
?>

  <div class="tab-body">
    <br />
    <div class="tab-panel active" id="tab-set">
        <form method="post" class="form-x" style="width:80%" action="index.php?action=manager&op=editroleoperate">

            <div class="form-group">
                <div class="label"><label for="manage_role_name">管理角色名称</label></div>
                <div class="field">
                     <input type="text" class="input" id="manage_role_name" name="manage_role_name" value="<?php echo $_smarty_tpl->tpl_vars['roleinfo']->value['s_rolename'];?>
" size="50" placeholder="请填写管理角色名称" data-validate="required:请填写管理角色名称" />
                </div>
            </div>
    
            <div class="form-group">
                <div class="label"><label for="manage_role_content">权限描述</label></div>
                <div class="field">
                     <input type="text" class="input" id="manage_role_content" name="manage_role_content" value="<?php echo $_smarty_tpl->tpl_vars['roleinfo']->value['s_description'];?>
" size="50" placeholder="请填写管理权限描述" data-validate="required:请填写管理权限描述" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="manage_name">权限设置</label></div>
                <!-- <div class="field"> -->
                    <div class="button-group border-main checkbox"> 
                        <?php
$_from = $_smarty_tpl->tpl_vars['manage_config']->value;
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
                        <label class="button"><input name="permission_id[]" value="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" type="checkbox"><?php echo $_smarty_tpl->tpl_vars['value']->value['bigcate_name'];?>
</label> 
                        <?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
                    </div><span class="tips icon-exclamation-circle" style="color:#f60; cursor:pointer;" data-toggle="hover" data-place="right" title="这个是设置管理员可查看的上面的模块"></span>
                <!-- </div> -->
<!--                 <div class="button-group border-main checkbox"> 
                    <label class="button active"><input name="pintuer" value="1" type="checkbox" checked="checked">开始</label> 
                    <label class="button"><input name="pintuer" value="2" type="checkbox">CSS</label> 
                    <label class="button"><input name="pintuer" value="3" type="checkbox">元件</label> 
                    <label class="button"><input name="pintuer" value="4" type="checkbox">JS组件</label> 
                    <label class="button"><input name="pintuer" value="5" type="checkbox">模块</label> 
                </div> -->
            </div>
            <input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" name="id">
            <div class="form-button"><button class="button bg-main" type="submit">提交</button></div>
        </form>
    </div>

    <?php echo '<script'; ?>
>


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