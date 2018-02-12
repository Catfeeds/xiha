<?php /* Smarty version 3.1.27, created on 2015-10-05 15:14:15
         compiled from "E:\AppServ\www\service\admin\templates\coach\add.html" */ ?>
<?php
/*%%SmartyHeaderCode:189856122347729869_11951778%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cfa3d530dd20f4bd221a8407dddccc74d474785c' => 
    array (
      0 => 'E:\\AppServ\\www\\service\\admin\\templates\\coach\\add.html',
      1 => 1439264953,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '189856122347729869_11951778',
  'variables' => 
  array (
    'school_list' => 0,
    'value' => 0,
    'lesson_config' => 0,
    'key' => 0,
    'lisence_config' => 0,
    'car_list' => 0,
    'provincelist' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_56122347a70ba3_07232289',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_56122347a70ba3_07232289')) {
function content_56122347a70ba3_07232289 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '189856122347729869_11951778';
echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
?>

  <div class="tab-body">
    <br />
    <div class="tab-panel active" id="tab-set">
        <form method="post" class="form-x" style="width:80%" action="index.php?action=coach&op=addoperate" enctype="multipart/form-data">

            <div class="form-group">
                <div class="label"><label for="s_coach_name">姓名</label></div>
                <div class="field">
                     <input type="text" class="input" id="s_coach_name" name="s_coach_name" size="50" placeholder="请填写姓名" data-validate="required:请填写姓名" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="s_coach_phone">手机号码</label></div>
                <div class="field">
                     <input type="text" class="input" id="s_coach_phone" name="s_coach_phone" size="50" placeholder="请填写手机号码" data-validate="required:请填写手机号码" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="license_img">头像</label></div>
                <div class="field">
                    <div class="imgdiv">
                        <img src="upload/coach/default_photo.jpg" id="imgShow" width="128" height="128" class="img-border radius-small" />
                    </div>

                    <a class="button input-file bg-green" href="javascript:void(0);">+ 浏览文件
                        <input size="100" type="file" name="license_img" value="" id="license_img" data-validate="regexp#.+.(jpg|jpeg|png|gif)$:只能上传jpg|gif|png格式文件" />
                        <input type="hidden" name="oldimg" value="upload/coach/default_photo.jpg">
                    </a>
                </div>
            </div>

            <div class="form-group">
              <div class="label"><label for="coach_school">所属驾校</label></div>
              <div class="field">
                  <select class="input" id="coach_school_car" style="width:20%; float:left" name="coach_school_car"> 
                      <option value="">请选择驾校</option> 
                      <?php if ($_smarty_tpl->tpl_vars['school_list']->value) {?>
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
                      <?php } else { ?>
                          <option value="">暂无驾校列表</option>
                      <?php }?>
                  </select>
          
              </div>
          </div>

            <div class="form-group">
                <div class="label"><label for="coach_lesson">培训课程</label></div>
                <div class="field">
                    <div class="button-group border-main checkbox"> 
                        <?php
$_from = $_smarty_tpl->tpl_vars['lesson_config']->value;
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
                        <?php if ($_smarty_tpl->tpl_vars['key']->value == 1) {?>
                        <label class="button active">
                            <input name="lesson_id[]" value="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" type="checkbox" checked="checked"><?php echo $_smarty_tpl->tpl_vars['value']->value;?>
</label> 
                        <?php } else { ?>
                            <label class="button"><input name="lesson_id[]" value="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" type="checkbox"><?php echo $_smarty_tpl->tpl_vars['value']->value;?>
</label>
                        <?php }?>
                        <?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
                    </div>
                    <span class="tips icon-exclamation-circle" style="color:#f60; cursor:pointer;" data-toggle="hover" data-place="right" title="培训课程可多选"></span>
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="coach_lesson">培训牌照</label></div>
                <div class="field">
                    <div class="button-group border-main checkbox"> 
                        <?php
$_from = $_smarty_tpl->tpl_vars['lisence_config']->value;
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
                        <?php if ($_smarty_tpl->tpl_vars['key']->value == 1) {?>
                        <label class="button active"><input name="lisence_id[]" value="1" type="checkbox" checked="checked"><?php echo $_smarty_tpl->tpl_vars['value']->value;?>
 </label>
                        <?php } else { ?>
                            <label class="button"><input name="lisence_id[]" value="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" type="checkbox"><?php echo $_smarty_tpl->tpl_vars['value']->value;?>
</label>
                        <?php }?>
                        <?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
                    </div>
                    <span class="tips icon-exclamation-circle" style="color:#f60; cursor:pointer;" data-toggle="hover" data-place="right" title="培训牌照可多选"></span>
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="coach_school_car">所属车辆</label></div>
                <div class="field">
                    <select class="input" id="coach_school_car" style="width:20%; float:left" name="coach_school_car"> 
                        <option value="">请选择车辆</option> 
                        <?php if ($_smarty_tpl->tpl_vars['car_list']->value) {?>
                            <?php
$_from = $_smarty_tpl->tpl_vars['car_list']->value;
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
                             <option value="<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['value']->value['name'];?>
 (<?php echo $_smarty_tpl->tpl_vars['value']->value['car_no'];?>
)</option>
                            <?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
                        <?php } else { ?>
                            <option value="">暂无车辆列表</option>
                        <?php }?>
                    </select>
                     <!-- <a href="index.php?action=car&op=add" class="button bg-yellow">添加车辆</a> -->
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
                    <input type="text" class="input" id="s_address" name="s_address" value="" size="50" placeholder="详细地址" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="coach_type">教练类型</label></div>
                <div class="field">
                    <select class="input" id="coach_type" style="width:20%; float:left" name="coach_type"> 
                        <option value="">请选择教练类型</option> 
                        <option value="1">普通教练</option>
                        <option value="0">金牌教练</option>
                    </select> 
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label>是否在线</label></div>
                <div class="field">
                    <div class="button-group button-group-small radio">
                        <label class="button active">
                            <input name="is_online" value="0" checked="checked" type="radio">
                            <span class="icon icon-check"></span> 在线</label>
                        <label class="button">
                            <input name="is_online" value="1" type="radio">
                            <span class="icon icon-times"></span> 不在线</label>
                    </div>
                </div>
            </div>
            
<!--             <div class="form-group">
                <div class="label"><label for="s_yh_name">收款银行名称</label></div>
                <div class="field">
                    <input type="text" class="input" id="s_yh_name" name="s_yh_name" value="" size="50" placeholder="收款银行名称" data-validate="required:请填写你收款银行名称"/>
                </div>
            </div>
            
            <div class="form-group">
                <div class="label"><label for="s_yh_zhanghao">收款银行账号</label></div>
                <div class="field">
                    <input type="text" class="input" id="s_yh_zhanghao" name="s_yh_zhanghao" value="" size="50" placeholder="收款银行账号" data-validate="required:请填写你收款银行账号"/>
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="s_yh_huming">银行账户户名</label></div>
                <div class="field">
                    <input type="text" class="input" id="s_yh_huming" name="s_yh_huming" value="" size="50" placeholder="银行账户户名" data-validate="required:请填写你银行账户户名" />
                </div>
            </div> -->

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