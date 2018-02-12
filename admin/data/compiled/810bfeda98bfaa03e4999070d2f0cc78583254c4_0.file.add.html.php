<?php /* Smarty version 3.1.27, created on 2015-08-31 21:33:36
         compiled from "E:\AppServ\www\service\admin\templates\car\add.html" */ ?>
<?php
/*%%SmartyHeaderCode:322855e457b050efa5_85202960%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '810bfeda98bfaa03e4999070d2f0cc78583254c4' => 
    array (
      0 => 'E:\\AppServ\\www\\service\\admin\\templates\\car\\add.html',
      1 => 1439112480,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '322855e457b050efa5_85202960',
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_55e457b0562262_44520314',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_55e457b0562262_44520314')) {
function content_55e457b0562262_44520314 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '322855e457b050efa5_85202960';
echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
?>

  <div class="tab-body">
    <br />
    <div class="tab-panel active" id="tab-set">
        <form method="post" class="form-x" style="width:80%" action="index.php?action=car&op=addoperate"  enctype="multipart/form-data">

            <div class="form-group">
                <div class="label"><label for="car_name">车辆名称</label></div>
                <div class="field">
                     <input type="text" class="input" id="car_name" name="car_name" size="50" placeholder="请填写车辆名称" data-validate="required:请填写车辆名称" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="car_no">车牌号</label></div>
                <div class="field">
                     <input type="text" class="input" id="car_no" name="car_no" size="50" placeholder="请填写车牌号" data-validate="required:请填写车牌号" />
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
                    <input type="file" value="" class="button" name="car_img[]">
                </div>
                <div class="label"></div>
                <div class="field" style="margin-top:10px;">
                    <span class="button bg-main icon-plus" style="cursor:pointer; margin-top:10px;" onclick="javascript:addimg();"> 添加图片</span>
                </div>
                <!-- <span class="button bg-main icon-plus" style="margin-top:10px;" onclick="javascript:addimg();"> 添加图片</span> -->
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

        // 添加图片位
        function addimg() {

            var html = '<input type="file" value="" class="button" name="car_img[]">'; 
            $('.img_field').append(html); 
        }

    <?php echo '</script'; ?>
> 
<?php echo $_smarty_tpl->getSubTemplate ("library/footer.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);

}
}
?>