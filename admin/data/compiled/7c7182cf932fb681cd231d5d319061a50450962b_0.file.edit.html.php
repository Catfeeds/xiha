<?php /* Smarty version 3.1.27, created on 2015-10-19 15:28:00
         compiled from "E:\web\admin\templates\car\edit.html" */ ?>
<?php
/*%%SmartyHeaderCode:618456249b80bb6a72_74728302%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7c7182cf932fb681cd231d5d319061a50450962b' => 
    array (
      0 => 'E:\\web\\admin\\templates\\car\\edit.html',
      1 => 1439052989,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '618456249b80bb6a72_74728302',
  'variables' => 
  array (
    'carinfo' => 0,
    'value' => 0,
    'id' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_56249b80c27f18_95449223',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_56249b80c27f18_95449223')) {
function content_56249b80c27f18_95449223 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '618456249b80bb6a72_74728302';
echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
?>

  <div class="tab-body">
    <br />
    <div class="tab-panel active" id="tab-set">
        <form method="post" class="form-x" style="width:80%" action="index.php?action=car&op=editoperate"  enctype="multipart/form-data">

            <div class="form-group">
                <div class="label"><label for="car_name">车辆名称</label></div>
                <div class="field">
                     <input type="text" class="input" id="car_name" value="<?php echo $_smarty_tpl->tpl_vars['carinfo']->value['name'];?>
" name="car_name" size="50" placeholder="请填写车辆名称" data-validate="required:请填写车辆名称" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="car_no">车牌号</label></div>
                <div class="field">
                     <input type="text" class="input" id="car_no" name="car_no" value="<?php echo $_smarty_tpl->tpl_vars['carinfo']->value['car_no'];?>
" size="50" placeholder="请填写车牌号" data-validate="required:请填写车牌号" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="car_type">车辆类型</label></div>
                <div class="field">
                    <select class="input" id="car_type" style="width:20%; float:left" name="car_type"> 
                        <option value="">请选择车辆类型</option> 
                        <option value="1">普通车型</option>
                        <option value="2">加强车型</option>
                        <option value="3">模拟车型</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="car_img">车辆图片</label></div>
                <div class="field img_field">
                    <?php if ($_smarty_tpl->tpl_vars['carinfo']->value['imgurl']) {?>
                    <?php
$_from = $_smarty_tpl->tpl_vars['carinfo']->value['imgurl'];
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
                    <img src="<?php echo $_smarty_tpl->tpl_vars['value']->value;?>
" width="50px" height="50px" alt="">
                    <input type="file" value="<?php echo $_smarty_tpl->tpl_vars['value']->value;?>
" name="car_img[]">
                    <?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
                    <?php } else { ?>
                    <img src="templates/assests/images/logo.jpg" width="50px" height="50px" alt="">
                    <input type="file" value="templates/assests/images/logo.jpg" class="button" name="car_img[]">
                    <?php }?>
                </div>
                <div class="label"></div>
                <div class="field" style="margin-top:10px;">
                    <span class="button bg-main icon-plus" style="cursor:pointer;" onclick="javascript:addimg();"> 添加图片</span>
                </div>
            </div>
            <input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" name="car_id">
            <div class="form-button"><button class="button bg-main" type="submit">提交</button></div>
        </form>
    </div>

    <?php echo '<script'; ?>
>
    // 上传图片预览
        // window.onload = function () { 
        //     new uploadPreview({ UpBtn: "license_img", DivShow: "imgdiv", ImgShow: "imgShow" });
        // }

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
        // 添加图片位
        function addimg() {

            var html = '<input type="file" value="" class="button" name="car_img[]">'; 
            $('.img_field').append(html); 
            }

    <?php echo '</script'; ?>
>
<?php echo $_smarty_tpl->getSubTemplate ("library/footer.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
?>

<?php }
}
?>