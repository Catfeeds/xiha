<?php /* Smarty version 3.1.27, created on 2015-08-31 23:02:19
         compiled from "E:\AppServ\www\service\admin\templates\order\add.html" */ ?>
<?php
/*%%SmartyHeaderCode:699855e46c7b3f0f11_63420163%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '454f673c854ea21a02941c4dd0a6c037c2bd310e' => 
    array (
      0 => 'E:\\AppServ\\www\\service\\admin\\templates\\order\\add.html',
      1 => 1439054582,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '699855e46c7b3f0f11_63420163',
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_55e46c7b44a400_41791864',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_55e46c7b44a400_41791864')) {
function content_55e46c7b44a400_41791864 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '699855e46c7b3f0f11_63420163';
echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
?>

  <div class="tab-body">
    <br />
    <div class="tab-panel active" id="tab-set">
        <form method="post" class="form-x" style="width:80%" action="index.php?action=order&op=addcheck" enctype="multipart/form-data">
            <div class="form-group">
                <div class="label"><label for="order_no">订单号</label></div>
                <div class="field">
                    <input type="text" class="input" id="order_no" name="order_no" size="20" value="" placeholder="请填写订单号" data-validate="required:请填写订单号" />
                </div>
            </div>
                
            <div class="form-group">
                <div class="label"><label for="dt_order_time">订单时间</label></div>
                <div class="field">
                    <input type="text" class="input" id="dt_order_time" name="dt_order_time" size="20" value="" placeholder="请填写订单时间" data-validate="required:请填写订单时间" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="s_order_content">订单科目</label></div>
                <div class="field">
                    <input type="text" class="input" id="s_order_content" name="s_order_content" size="20" value="" placeholder="请填写驾校名称" data-validate="required:请填写驾校的名称" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="s_order_money">订单单价</label></div>
                <div class="field">
                    <input type="text" class="input" id="s_order_money" name="s_order_money" size="20" value="" placeholder="请填写订单单价" data-validate="required:请填写订单单价" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="s_lisence_type">订单详情</label></div>
                <div class="field">
                    <input type="text" class="input" id="s_lisence_type" name="s_lisence_type" size="20" value="" placeholder="请填写订单详情" data-validate="required:请填写订单详情" />
                </div>
            </div>
            
            <div class="form-group">
                <div class="label"><label for="s_lisence_type">驾照类型</label></div>
                <div class="field">
                    <input type="text" class="input" id="s_lisence_type" name="s_lisence_type" size="20" value="" placeholder="驾照类型" data-validate="required:请填写驾照类型" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="s_user_name">学员姓名</label></div>
                <div class="field">
                    <input type="text" class="input" id="s_user_name" name="s_user_name" size="20" value="" placeholder="学员姓名" data-validate="required:请填写学员姓名" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="s_user_phone">学员手机号</label></div>
                <div class="field">
                    <input type="text" class="input" id="s_user_phone" name="s_user_phone" size="20" value="" placeholder="学员手机号" data-validate="required:请填写学员手机号" />
                </div>
            </div>
            
            <div class="form-group">
                <div class="label"><label for="s_coach_name">教练姓名</label></div>
                <div class="field">
                    <input type="text" class="input" id="s_coach_name" name="s_coach_name" size="20" value="" placeholder="教练姓名" data-validate="required:请填写教练姓名" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="s_coach_phone">教练手机号</label></div>
                <div class="field">
                    <input type="text" class="input" id="s_coach_phone" name="s_coach_phone" size="20" value="" placeholder="教练手机号" data-validate="required:请填写教练手机号" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="s_address">训练场地</label></div>
                <div class="field">
                    <input type="text" class="input" id="s_address" name="s_address" size="20" value="" placeholder="训练场地" data-validate="required:请填写训练场地" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="dc_money">训练费用</label></div>
                <div class="field">
                    <input type="text" class="input" id="dc_money" name="dc_money" size="20" value="" placeholder="训练费用" data-validate="required:请填写训练费用" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="i_service_time">预约时长</label></div>
                <div class="field">
                    <input type="text" class="input" id="i_service_time" name="i_service_time" size="20" value="" placeholder="预约时长" data-validate="required:请填写预约时长" />
                </div>
            </div>

            <div class="form-group">
                <div class="label"><label for="deal_type">支付形式</label></div>
                <div class="field">
                    <select class="input" id="deal_type" style="width:20%; float:left" name="deal_type"> 
                        <option value="">请选择支付形式</option> 
                         <option value="1">线上支付</option>
                         <option value="2">线下支付</option>
                    </select>
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