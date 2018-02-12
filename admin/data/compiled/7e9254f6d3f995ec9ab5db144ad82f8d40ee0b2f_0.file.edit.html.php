<?php /* Smarty version 3.1.27, created on 2015-08-28 23:35:55
         compiled from "E:\AppServ\www\service\admin\templates\coach\edit.html" */ ?>
<?php
/*%%SmartyHeaderCode:2777755e07fdbb49925_97011027%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7e9254f6d3f995ec9ab5db144ad82f8d40ee0b2f' => 
    array (
      0 => 'E:\\AppServ\\www\\service\\admin\\templates\\coach\\edit.html',
      1 => 1440776145,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2777755e07fdbb49925_97011027',
  'variables' => 
  array (
    'coachdetail' => 0,
    'lesson_config' => 0,
    'key' => 0,
    'value' => 0,
    'lisence_config' => 0,
    'school_list' => 0,
    'car_list' => 0,
    'provincelist' => 0,
    'cityinfo' => 0,
    'areainfo' => 0,
    'coach_time_config' => 0,
    'k' => 0,
    'v' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_55e07fdbd13b60_00603185',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_55e07fdbd13b60_00603185')) {
function content_55e07fdbd13b60_00603185 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '2777755e07fdbb49925_97011027';
echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
?>

  <div class="tab-body">
    <br />
    <div class="tab-panel active" id="tab-set">
        <form method="post" class="form-x" style="width:80%" action="index.php?action=coach&op=editoperate" enctype="multipart/form-data">

            <div class="form-group">
                <div class="label"><label for="s_coach_name">姓名</label></div>
                <div class="field">
                     <input type="text" class="input" id="s_coach_name" name="s_coach_name" size="50" value="<?php echo $_smarty_tpl->tpl_vars['coachdetail']->value['s_coach_name'];?>
" placeholder="请填写姓名" data-validate="required:请填写姓名" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="s_coach_phone">手机号码</label></div>
                <div class="field">
                     <input type="text" class="input" id="s_coach_phone" name="s_coach_phone" size="50" value="<?php echo $_smarty_tpl->tpl_vars['coachdetail']->value['s_coach_phone'];?>
" placeholder="请填写手机号码" data-validate="required:请填写手机号码" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="license_img">头像</label></div>
                <div class="field">
                    <div class="imgdiv">
                        <img src="<?php echo $_smarty_tpl->tpl_vars['coachdetail']->value['s_coach_imgurl'];?>
" id="imgShow" width="128" height="128" class="img-border radius-small" />
                    </div>

                    <a class="button input-file bg-green" href="javascript:;">+ 浏览文件
                        <input size="100" type="file" name="license_img" value="" id="license_img" data-validate="regexp#.+.(jpg|jpeg|png|gif)$:只能上传jpg|gif|png格式文件" />
                        <input type="hidden" name="oldimg" value="<?php echo $_smarty_tpl->tpl_vars['coachdetail']->value['s_coach_imgurl'];?>
">
                    </a>
                </div>
            </div>

<!--             <div class="form-group">
                <div class="label"><label for="coach_school">所属驾校</label></div>
                <div class="field">
                    
                </div>
            </div> -->

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
" <?php if ($_smarty_tpl->tpl_vars['coachdetail']->value['s_coach_lesson_id'] == $_smarty_tpl->tpl_vars['key']->value) {?>checked="checked"<?php }?>
                                type="checkbox"><?php echo $_smarty_tpl->tpl_vars['value']->value;?>
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
                <div class="label"><label for="coach_school">所属驾校</label></div>
                <div class="field">

                    <select class="input" id="coach_school_id" style="width:20%; float:left" name="coach_school_id"> 
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
" <?php if ($_smarty_tpl->tpl_vars['coachdetail']->value['s_school_name_id'] == $_smarty_tpl->tpl_vars['value']->value['school_id']) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['value']->value['school_name'];?>
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
" <?php if ($_smarty_tpl->tpl_vars['coachdetail']->value['s_coach_car_id'] == $_smarty_tpl->tpl_vars['value']->value['id']) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['value']->value['name'];?>
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
" <?php if ($_smarty_tpl->tpl_vars['coachdetail']->value['province_id'] == $_smarty_tpl->tpl_vars['value']->value['provinceid']) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['value']->value['province'];?>
</option>
                       <?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
                   </select>
                   <select class="input" id="city" style="width:20%; float:left" name="city"> 
                       <option value="">请选择市</option>
                       <?php if ($_smarty_tpl->tpl_vars['coachdetail']->value['city_id']) {?>
                       <option value="<?php echo $_smarty_tpl->tpl_vars['cityinfo']->value['cityid'];?>
" <?php if ($_smarty_tpl->tpl_vars['coachdetail']->value['city_id'] == $_smarty_tpl->tpl_vars['cityinfo']->value['cityid']) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['cityinfo']->value['city'];?>
</option>
                       <?php }?>
            
                   </select>
                   <select class="input" id="area" style="width:20%; float:left" name="area"> 
                       <option value="">请选择区域</option>
                       <?php if ($_smarty_tpl->tpl_vars['coachdetail']->value['area_id']) {?>
                       <option value="<?php echo $_smarty_tpl->tpl_vars['areainfo']->value['areaid'];?>
" <?php if ($_smarty_tpl->tpl_vars['coachdetail']->value['area_id'] == $_smarty_tpl->tpl_vars['areainfo']->value['areaid']) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['areainfo']->value['area'];?>
</option>
                       <?php }?>
                   </select>
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="s_address">详细地址</label></div>
                <div class="field">
                    <!-- <input type="text" class="address_start" value=""> -->
                    <input type="text" class="input" id="s_address" name="s_address" value="<?php echo $_smarty_tpl->tpl_vars['coachdetail']->value['s_coach_address'];?>
" size="50" placeholder="详细地址" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="coach_type">教练类型</label></div>
                <div class="field">
                    <select class="input" id="coach_type" style="width:20%; float:left" name="coach_type"> 
                        <option value="">请选择教练类型</option> 
                        <option value="1" <?php if ($_smarty_tpl->tpl_vars['coachdetail']->value['i_type'] == 1) {?>selected<?php }?>>普通教练</option>
                        <option value="0" <?php if ($_smarty_tpl->tpl_vars['coachdetail']->value['i_type'] == 0) {?>selected<?php }?>>金牌教练</option>
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

            <!-- 预约时间的配置 -->
            <div class="form-group">
                <div class="label"><label for="appoint_time">时间配置</label></div>
                <div class="field">
                    <div class="tab"> 
                        <div class="tab-head border-main float-left"> 
                             <ul class="tab-nav date_config"> 
                                <!-- 时间7天日期显示 -->

                                <?php
$_from = $_smarty_tpl->tpl_vars['coach_time_config']->value['date'];
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
                                <li><a href="#tab-time-<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" title="<?php echo $_smarty_tpl->tpl_vars['value']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['value']->value;?>
</a></li>
                                <?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
                            </ul>
                        </div>
                        <div class="float-left" style="margin-left:10px;">
                            <button class="button bg-main  button-little" style="" onclick="javascript:delpretime();" type="button">删除上一天时间</button>
                            <button class="button bg-yellow  button-little" onclick="javascript:delalltime();" type="button">删除全部时间</button>
                        </div>
                        <div class="clearfix"></div>

                        <!-- 每一天的时间配置 -->
                        <div class="tab-body time_config">
                            <?php
$_from = $_smarty_tpl->tpl_vars['coach_time_config']->value['date'];
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
                            <div class="tab-panel <?php if ($_smarty_tpl->tpl_vars['key']->value == 0) {?>active<?php }?>" id="tab-time-<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
"> 
                                <table class="table" >
                                    <tr>
                                        <th width="50">
                                            <input type="button" class="button button-small checkall" name="checkall" checkfor="time_config_id_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
[]" value="全选" /></th>
                                        <th width="100">开始时间</th>
                                        <th width="100">结束时间</th>
                                        <th width="100">牌照</th>
                                        <th width="100">科目</th>
                                        <th width="100">单价</th>
                                        <th width="150"><button class="button bg-main" onclick="javascript:gettimeconfig();" type="button">保存<?php echo $_smarty_tpl->tpl_vars['value']->value;?>
数据</button></th>
                                    </tr> 
                                    <?php
$_from = $_smarty_tpl->tpl_vars['coach_time_config']->value['time'];
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
                                        <tr>
                                            <td><input type="checkbox" name="time_config_id_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
[]" id="checkbox_<?php echo $_smarty_tpl->tpl_vars['k']->value+1;?>
" value="<?php echo $_smarty_tpl->tpl_vars['v']->value['id'];?>
" /></td>
                                            <td><span><?php echo $_smarty_tpl->tpl_vars['v']->value['start_time'];?>
:00</span></td>
                                            <td><?php echo $_smarty_tpl->tpl_vars['v']->value['end_time'];?>
:00</td>
                                            <td><input type="text" class="input lisence_no_<?php echo $_smarty_tpl->tpl_vars['k']->value+1;?>
" style="text-align:center" size="1" value="<?php echo $_smarty_tpl->tpl_vars['v']->value['license_no'];?>
" name="lisence_no"></td>
                                            <td><input type="text" class="input subjects_<?php echo $_smarty_tpl->tpl_vars['k']->value+1;?>
" style="text-align:center" size="1" value="<?php echo $_smarty_tpl->tpl_vars['v']->value['subjects'];?>
" name="subjects"></td>
                                            <td><input type="text" class="input single_price_<?php echo $_smarty_tpl->tpl_vars['k']->value+1;?>
" style="text-align:center" size="1" value="<?php echo $_smarty_tpl->tpl_vars['v']->value['price'];?>
" name="single_price"></td>
                                            <td><input type="hidden" value=""></td>
                                        </tr>
                                    <?php
$_smarty_tpl->tpl_vars['v'] = $foreach_v_Sav;
}
?>
                                </table> 
                             </div>
                            <?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
                        </div> 
                    </div>
                    

                </div>
            </div>

<!--             <div class="form-group">
    <div class="label"><label for="s_yh_name">收款银行名称</label></div>
    <div class="field">
        <input type="text" class="input" id="s_yh_name" name="s_yh_name" value="{$coachdetail.s_yh_name}" size="50" placeholder="收款银行名称" data-validate="required:请填写你收款银行名称"/>
    </div>
</div>

<div class="form-group">
    <div class="label"><label for="s_yh_zhanghao">收款银行账号</label></div>
    <div class="field">
        <input type="text" class="input" id="s_yh_zhanghao" name="s_yh_zhanghao" value="{$coachdetail.s_yh_zhanghao}" size="50" placeholder="收款银行账号" data-validate="required:请填写你收款银行账号"/>
    </div>
</div>

<div class="form-group">
    <div class="label"><label for="s_yh_huming">银行账户户名</label></div>
    <div class="field">
        <input type="text" class="input" id="s_yh_huming" name="s_yh_huming" value="{$coachdetail.s_yh_huming}" size="50" placeholder="银行账户户名" data-validate="required:请填写你银行账户户名" />
    </div>
</div> -->
            <input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['coachdetail']->value['l_coach_id'];?>
" name="l_coach_id">
            <input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['coachdetail']->value['s_school_name_id'];?>
" name="school_id">
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

        // 选择时间配置
        function gettimeconfig() {
            if($('.time_config .active').find('input:checked').val() == undefined) {
                alert('请选择需要保存的教练时间段信息！');
                return false;
            }

            if(window.confirm('你确定保存吗？')) {

                var time_money_config = new Array();
                var lisence_no_config = new Array();
                var subjects_config = new Array();

                $(".time_config .active input:checkbox").each(function(index) {

                   var checkid = $(this).parents('table').find('#checkbox_'+(index+1)+":checked").val();

                    if($("#checkbox_"+(index+1)+":checked").val() != undefined) {
                        time_money_config[checkid] = $("#checkbox_"+(index+1)+":checked").parents('tr').find('.single_price_'+(index+1)).val();

                        lisence_no_config[checkid] = $("#checkbox_"+(index+1)+":checked").parents('tr').find('.lisence_no_'+(index+1)).val();

                        subjects_config[checkid] = $("#checkbox_"+(index+1)+":checked").parents('tr').find('.subjects_'+(index+1)).val();
                    }
                });
                // 获取checkbox值
                // if(time_config.lastIndexOf(',') > 0) {
                //     time_config = time_config.substr(0, time_config.lastIndexOf(','));
                // }
                // alert(subjects_config);

                // alert(time_money_config);

                // return false;
                // 获取日期
                var date_config = $('.date_config .active').find('a').attr('title');
                $.ajax({
                    type:"POST",
                    url:"index.php?action=coach&op=savetime",
                    dataType:'JSON',
                    data:{"time_money_config":time_money_config,'date_config':date_config,'lisence_no':lisence_no_config,'subjects':subjects_config,'coach_id':<?php echo $_smarty_tpl->tpl_vars['coachdetail']->value['l_coach_id'];?>
},
                    dataType:"JSON",
                    success:function(data) {
                        if(data.code == 1) {
                            alert('保存成功！');
                        } else if(data.code == 0) {
                            alert('保存失败！');
                        }
                    }
                })
            } else {
                return false;
            }
        }

        // 删除前一天的时间配置
        function delpretime() {
            if(window.confirm('你确定删除吗？')) {
                $.ajax({
                    type:"POST",
                    url:"index.php?action=coach&op=delpretime",
                    dataType:"JSON",
                    data:{'coach_id':<?php echo $_smarty_tpl->tpl_vars['coachdetail']->value['l_coach_id'];?>
},
                    success:function(data) {
                        if(data.code == 1) {
                            alert('删除成功！');
                        } else {
                            alert('删除失败！');
                        }
                    }
                });
            } else {
                return false;
            }   
        }

        // 删除所有时间数据
        function delalltime() {
            if(window.confirm('你确定删除吗？')) {
                $.ajax({
                    type:"POST",
                    url:"index.php?action=coach&op=delalltime",
                    dataType:"JSON",
                    data:{'coach_id':<?php echo $_smarty_tpl->tpl_vars['coachdetail']->value['l_coach_id'];?>
},
                    success:function(data) {
                        if(data.code == 1) {
                            alert('删除成功！');
                        } else {
                            alert('删除失败！');
                        }
                    }
                });
            } else {
                return false;
            }
        }
    <?php echo '</script'; ?>
>
<?php echo $_smarty_tpl->getSubTemplate ("library/footer.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
?>

<?php }
}
?>