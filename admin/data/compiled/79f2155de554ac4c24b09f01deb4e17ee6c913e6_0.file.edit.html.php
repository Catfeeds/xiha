<?php /* Smarty version 3.1.27, created on 2015-09-14 21:54:18
         compiled from "E:\AppServ\www\service\admin\templates\school\edit.html" */ ?>
<?php
/*%%SmartyHeaderCode:642555f6d18a3f7eb4_74184965%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '79f2155de554ac4c24b09f01deb4e17ee6c913e6' => 
    array (
      0 => 'E:\\AppServ\\www\\service\\admin\\templates\\school\\edit.html',
      1 => 1439053000,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '642555f6d18a3f7eb4_74184965',
  'variables' => 
  array (
    'id' => 0,
    'schooldetail' => 0,
    'provincelist' => 0,
    'value' => 0,
    'cityinfo' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_55f6d18a780fb7_07975367',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_55f6d18a780fb7_07975367')) {
function content_55f6d18a780fb7_07975367 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '642555f6d18a3f7eb4_74184965';
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="renderer" content="webkit">
    <title>拼图后台管理-后台管理</title>
    <link rel="stylesheet" href="templates/assests/css/pintuer.css">
    <link rel="stylesheet" href="templates/assests/css/admin.css">
    <?php echo '<script'; ?>
 src="templates/assests/js/jquery.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="templates/assests/js/uploadPreview.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="templates/assests/js/pintuer.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="templates/assests/js/respond.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="templates/assests/js/admin.js"><?php echo '</script'; ?>
>
    <link type="image/x-icon" href="http://www.pintuer.com/favicon.ico" rel="shortcut icon" />
    <link href="http://www.pintuer.com/favicon.ico" rel="bookmark icon" />
    <style>
        html { overflow-x:hidden; }
    </style>
</head>

<body>
  <div class="tab-body">
    <br />
    <div class="tab-panel active" id="tab-set">
        <form method="post" class="form-x" style="width:80%" action="index.php?action=school&op=editcheck&id=<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" enctype="multipart/form-data">
            <div class="form-group">
                <div class="label"><label for="school_name">驾校名称</label></div>
                <div class="field">
                    <input type="text" class="input" id="school_name" name="school_name" size="20" value="<?php echo $_smarty_tpl->tpl_vars['schooldetail']->value['s_school_name'];?>
" placeholder="驾校名称" data-validate="required:请填写驾校的名称" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="legal_person">法人代表</label></div>
                <div class="field">
                    <input type="text" class="input" id="legal_person" name="legal_person" size="50" value="<?php echo $_smarty_tpl->tpl_vars['schooldetail']->value['s_frdb'];?>
" placeholder="法人代表" data-validate="required:请填写法人代表" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="legal_person_phone">法人手机号码</label></div>
                <div class="field">
                    <input type="text" class="input" id="legal_person_phone" name="legal_person_phone" size="50" value="<?php echo $_smarty_tpl->tpl_vars['schooldetail']->value['s_frdb_mobile'];?>
" placeholder="法人手机号码" data-validate="required:请填写法人手机号码" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="legal_person_tel">法人固定号码</label></div>
                <div class="field">
                    <input type="text" class="input" id="legal_person_tel" name="legal_person_tel" value="<?php echo $_smarty_tpl->tpl_vars['schooldetail']->value['s_frdb_tel'];?>
" size="50" placeholder="法人固定号码" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="license_img">营业执照</label></div>
                <div class="field">
                    <div class="imgdiv">
                        <img src="<?php echo $_smarty_tpl->tpl_vars['schooldetail']->value['s_yyzz'];?>
" id="imgShow" width="128" height="128" class="img-border radius-small" />
                    </div>

                    <a class="button input-file bg-green" href="javascript:void(0);">+ 浏览文件
                        <input size="100" type="file" name="license_img" value="<?php echo $_smarty_tpl->tpl_vars['schooldetail']->value['s_yyzz'];?>
" id="license_img" data-validate="regexp#.+.(jpg|jpeg|png|gif)$:只能上传jpg|gif|png格式文件" />
                        <input type="hidden" name="oldimg" value="<?php echo $_smarty_tpl->tpl_vars['schooldetail']->value['s_yyzz'];?>
">
                    </a>
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="s_zzjgdm">组织结构代码</label></div>
                <div class="field">
                    <input type="text" class="input" id="s_zzjgdm" name="s_zzjgdm" value="<?php echo $_smarty_tpl->tpl_vars['schooldetail']->value['s_zzjgdm'];?>
" size="50" placeholder="法人固定号码" />
                </div>
            </div>
            
            <div class="form-group">
                <div class="label"><label for="school_character">驾校性质</label></div>
                <select class="input" style="width:30%" name="school_character"> 
                    <option value="1" <?php if ($_smarty_tpl->tpl_vars['schooldetail']->value['i_dwxz'] == 1) {?>selected<?php }?> >一类驾校</option> 
                    <option value="2" <?php if ($_smarty_tpl->tpl_vars['schooldetail']->value['i_dwxz'] == 2) {?>selected<?php }?> >二类驾校</option> 
                    <option value="3" <?php if ($_smarty_tpl->tpl_vars['schooldetail']->value['i_dwxz'] == 3) {?>selected<?php }?> >三类驾校</option> 
                </select>

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
" <?php if ($_smarty_tpl->tpl_vars['schooldetail']->value['province_id'] == $_smarty_tpl->tpl_vars['value']->value['provinceid']) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['value']->value['province'];?>
</option>
                       <?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
                   </select>
                   <select class="input" id="city" style="width:20%; float:left" name="city"> 
                       <option value="">请选择市</option>
                       <?php if ($_smarty_tpl->tpl_vars['schooldetail']->value['city_id']) {?>
                       <option value="<?php echo $_smarty_tpl->tpl_vars['cityinfo']->value['cityid'];?>
" <?php if ($_smarty_tpl->tpl_vars['schooldetail']->value['city_id'] == $_smarty_tpl->tpl_vars['cityinfo']->value['cityid']) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['cityinfo']->value['city'];?>
</option>
                       <?php }?>
    
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
                    <input type="text" class="input" id="s_address" name="s_address" value="<?php echo $_smarty_tpl->tpl_vars['schooldetail']->value['s_address'];?>
" size="50" placeholder="法人固定号码" />
                </div>
            </div>
            
            <div class="form-group">
                <div class="label"><label for="dc_base_je">收费标准</label></div>
                <div class="field">
                    <input type="text" class="input" id="dc_base_je" name="dc_base_je" value="<?php echo $_smarty_tpl->tpl_vars['schooldetail']->value['dc_base_je'];?>
" size="50" placeholder="法人固定号码" />
                </div>
            </div>
            
            <div class="form-group">
                <div class="label"><label for="dc_bili">上浮最高比例</label></div>
                <div class="field">
                    <input type="text" class="input" id="dc_bili" name="dc_bili" value="<?php echo $_smarty_tpl->tpl_vars['schooldetail']->value['dc_bili'];?>
" size="50" placeholder="法人固定号码" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="s_yh_name">收款银行名称</label></div>
                <div class="field">
                    <input type="text" class="input" id="s_yh_name" name="s_yh_name" value="<?php echo $_smarty_tpl->tpl_vars['schooldetail']->value['s_yh_name'];?>
" size="50" placeholder="收款银行名称" data-validate="required:请填写你收款银行名称"/>
                </div>
            </div>
    
            <div class="form-group">
                <div class="label"><label for="s_yh_zhanghao">收款银行账号</label></div>
                <div class="field">
                    <input type="text" class="input" id="s_yh_zhanghao" name="s_yh_zhanghao" value="<?php echo $_smarty_tpl->tpl_vars['schooldetail']->value['s_yh_zhanghao'];?>
" size="50" placeholder="收款银行账号" data-validate="required:请填写你收款银行账号"/>
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="s_yh_huming">银行账户户名</label></div>
                <div class="field">
                    <input type="text" class="input" id="s_yh_huming" name="s_yh_huming" value="<?php echo $_smarty_tpl->tpl_vars['schooldetail']->value['s_yh_huming'];?>
" size="50" placeholder="银行账户户名" data-validate="required:请填写你银行账户户名" />
                </div>
            </div>
    
            <div class="form-group">
                <div class="label"><label for="s_shuoming">驾校说明</label></div>
                <div class="field">
                    <textarea class="input" rows="5" cols="50" name="s_shuoming" placeholder="请填写驾校说明" ><?php echo $_smarty_tpl->tpl_vars['schooldetail']->value['s_shuoming'];?>
</textarea>
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
</body>
</html>
<?php }
}
?>