<?php /* Smarty version 3.1.27, created on 2015-09-22 16:06:56
         compiled from "E:\web\admin\templates\member\edit.html" */ ?>
<?php
/*%%SmartyHeaderCode:1331456010c20b67174_97625850%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '41233aec306aaeea29d0f5d662b1897e4e9d6db4' => 
    array (
      0 => 'E:\\web\\admin\\templates\\member\\edit.html',
      1 => 1439052998,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1331456010c20b67174_97625850',
  'variables' => 
  array (
    'memberinfo' => 0,
    'provincelist' => 0,
    'value' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_56010c20c70bb4_02045067',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_56010c20c70bb4_02045067')) {
function content_56010c20c70bb4_02045067 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '1331456010c20b67174_97625850';
echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
?>

  <div class="tab-body">
    <br />
    <div class="tab-panel active" id="tab-set">
        <form method="post" class="form-x" style="width:80%" action="system.html">

            <div class="form-group">
                <div class="label"><label for="coach_name">用户名</label></div>
                <div class="field">
                     <input type="text" class="input" id="coach_name" name="coach_name" value="<?php echo $_smarty_tpl->tpl_vars['memberinfo']->value['s_username'];?>
" size="50" placeholder="请填写姓名" data-validate="required:请填写姓名" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="coach_phone">真实姓名</label></div>
                <div class="field">
                     <input type="text" class="input" id="coach_phone" name="coach_phone" value="<?php echo $_smarty_tpl->tpl_vars['memberinfo']->value['s_real_name'];?>
" size="50" placeholder="请填写手机号码" data-validate="required:请填写手机号码" />
                </div>
            </div>
            
            <div class="form-group">
                <div class="label"><label for="coach_phone">手机号码</label></div>
                <div class="field">
                     <input type="text" class="input" id="coach_phone" name="coach_phone" value="<?php echo $_smarty_tpl->tpl_vars['memberinfo']->value['s_phone'];?>
" size="50" placeholder="请填写手机号码" data-validate="required:请填写手机号码" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="license_img">头像</label></div>
                <div class="field">
                    <div class="imgdiv">
                        <img src="<?php echo $_smarty_tpl->tpl_vars['memberinfo']->value['s_imgurl'];?>
" id="imgShow" width="128" height="128" class="img-border radius-small" />
                    </div>

                    <a class="button input-file bg-green" href="javascript:void(0);">+ 浏览文件
                        <input size="100" type="file" name="license_img" value="" id="license_img" data-validate="regexp#.+.(jpg|jpeg|png|gif)$:只能上传jpg|gif|png格式文件" />
                        <input type="hidden" name="oldimg" value="<?php echo $_smarty_tpl->tpl_vars['memberinfo']->value['s_imgurl'];?>
">
                    </a>
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="i_wdid">地址</label></div>
                <div class="field">
                   <select class="input" id="province" style="width:20%; float:left" name="province"> 
                       <option value="">请选择省</option> 
                       <?php
$_from = $_smarty_tpl->tpl_vars['provincelist']->value;
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
                        <option value="<?php echo $_smarty_tpl->tpl_vars['value']->value['provinceid'];?>
"><?php echo $_smarty_tpl->tpl_vars['value']->value['province'];?>
</option>
                       <?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
                   </select>
                   <select class="input" id="city" style="width:20%; float:left" name="city"> 
                       <option value="">请选择市</option>
            
                   </select>
                   <select class="input" id="area" style="width:20%; float:left" name="area"> 
                       <option value="">请选择区域</option> 
                   </select>
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="s_address">详细地址</label></div>
                <div class="field">
                    <!-- <input type="text" class="address_start" value=""> -->
                    <input type="text" class="input" value="<?php echo $_smarty_tpl->tpl_vars['memberinfo']->value['address'];?>
" id="s_address" name="s_address" value="" size="50" placeholder="详细地址" />
                </div>
            </div>
            
            <div class="form-group">
                <div class="label"><label for="s_address">身份证</label></div>
                <div class="field">
                    <!-- <input type="text" class="address_start" value=""> -->
                    <input type="text" class="input" value="<?php echo $_smarty_tpl->tpl_vars['memberinfo']->value['identity_id'];?>
" id="s_address" name="s_address" value="" size="50" placeholder="详细地址" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label>性别</label></div>
                <div class="field">
                    <div class="button-group button-group-small radio">
                        <label class="button active">
                            <input name="is_online" value="1" checked="checked" type="radio">
                            <span class="icon icon-male"></span> 男</label>
                        <label class="button">
                            <input name="is_online" value="0" type="radio">
                            <span class="icon icon-female"></span> 女</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="s_yh_name">年龄</label></div>
                <div class="field">
                    <input type="text" class="input" id="s_yh_name"  value="<?php echo $_smarty_tpl->tpl_vars['memberinfo']->value['age'];?>
" name="s_yh_name" value="" size="50" placeholder="年龄" data-validate="required:请填写你年龄"/>
                </div>
            </div>

            <div class="form-button"><button class="button bg-main" type="submit">提交</button></div>
        </form>
    </div>

    <?php echo '<script'; ?>
>
    // 上传图片预览
        window.onload = function () { 
            new uploadPreview({ UpBtn: "license_img", DivShow: "imgdiv", ImgShow: "imgShow" });
        }

        // 城市联动
        $('#province').change(function() {
            var province_id = $(this).val();
            $("#city").load("index.php?action=school&op=getcity&province_id="+province_id);
        });

        $('#city').change(function() {
            var city_id = $(this).val();
            $('#area').load('index.php?action=school&op=getarea&city_id='+city_id);
            var city_html = $(this).find('option:selected').html();
            var province_html = $('#province').find('option:selected').html();
            $('#s_address').val(province_html+city_html);
        })

        $('#area').change(function() {
            var city_html = $('#city').find('option:selected').html();
            var province_html = $('#province').find('option:selected').html();
            var area_html = $(this).find('option:selected').html();
            $('#s_address').val(province_html+city_html+area_html);
        })
    <?php echo '</script'; ?>
>
<?php echo $_smarty_tpl->getSubTemplate ("library/footer.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
?>

<?php }
}
?>