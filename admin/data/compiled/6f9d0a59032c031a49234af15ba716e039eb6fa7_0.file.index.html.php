<?php /* Smarty version 3.1.27, created on 2015-08-30 10:15:44
         compiled from "E:\AppServ\www\service\admin\templates\order\index.html" */ ?>
<?php
/*%%SmartyHeaderCode:2023455e267505c8ee4_01211338%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6f9d0a59032c031a49234af15ba716e039eb6fa7' => 
    array (
      0 => 'E:\\AppServ\\www\\service\\admin\\templates\\order\\index.html',
      1 => 1439207103,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2023455e267505c8ee4_01211338',
  'variables' => 
  array (
    'orderlist' => 0,
    'key' => 0,
    'value' => 0,
    'v' => 0,
    'pagehtml' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_55e26750685204_70249713',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_55e26750685204_70249713')) {
function content_55e26750685204_70249713 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '2023455e267505c8ee4_01211338';
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="renderer" content="webkit">
    <title>后台管理-后台管理</title>
    <link rel="stylesheet" href="templates/assests/css/pintuer.css">
    <link rel="stylesheet" href="templates/assests/css/admin.css">
    <?php echo '<script'; ?>
 src="templates/assests/js/jquery.js"><?php echo '</script'; ?>
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
</head>

<body>
	<form method="post">
        <div class="panel admin-panel">
        	<div class="panel-head"><strong>订单列表</strong></div>
            <div class="padding border-bottom">
                <input type="button" class="button button-small checkall" name="checkall" checkfor="order_id[]" value="全选" />
                <a href="index.php?action=order&op=add" class="button button-small border-green">添加订单</a>
                <!-- <input type="button" class="button button-small border-green" value="添加文章" /> -->
                <input type="button" class="button button-small border-yellow" onclick="delall()" value="批量删除" />
                <!-- <input type="button" class="button button-small border-blue" value="回收站" /> -->
            </div>
            <table class="table table-hover">
            	<tr>
                    <th width="65">选择</th>
                    <th width="110">订单号</th>
                    <th width="100">订单时间</th>
                    <th width="150">订单科目</th>
                    <!-- <th width="150">订单单价</th>
                    <th width="150">订单详情</th>
                    <th width="150">驾照类型</th>-->
                    <th width="140">学员姓名</th> 
                    <th width="130">学员手机号</th>
                    <th width="150">教练姓名</th>
                    <th width="140">教练手机号</th>
                    <th width="150">训练场地</th>
                    <!-- <th width="130">训练费用</th> -->
                    <th width="140">预约时长</th>
                    <th width="100">订单状态</th>
                    <th width="80">支付形式</th>
                    <th width="160">操作</th>

                </tr>
                <!-- 循环列表 -->
                <?php
$_from = $_smarty_tpl->tpl_vars['orderlist']->value;
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
                <tr class="orderlist">
                    <td><input type="checkbox" name="order_id[]" id="checkbox_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['value']->value['l_study_order_id'];?>
" /></td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['s_order_no'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['dt_order_time'];?>
</td>
                    <td>
                        <?php
$_from = $_smarty_tpl->tpl_vars['value']->value['lesson_name'];
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
                            <?php echo $_smarty_tpl->tpl_vars['v']->value;?>

                        <?php
$_smarty_tpl->tpl_vars['v'] = $foreach_v_Sav;
}
?>
                    </td>
                    <!-- <td>{$value['s_order_money']}</td>
                    <td>{$value['s_order_time']} </td>
                    <td>{$value['s_lisence_type']}</td>-->
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['s_user_name'];?>
</td> 
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['s_user_phone'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['s_coach_name'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['s_coach_phone'];?>
</td>
                    <!-- <td>{$value['s_address']}</td> -->
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['dc_money'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['i_service_time'];?>
</td>

                    <?php if ($_smarty_tpl->tpl_vars['value']->value['i_status'] == 1) {?>
                        <td><span class="badge bg-yellow icon-bell-o">待完成</span></td>
                    <?php } elseif ($_smarty_tpl->tpl_vars['value']->value['i_status'] == 2) {?>
                        <td><span class="badge bg-green icon-check">已完成</span></td>
                    <?php } elseif ($_smarty_tpl->tpl_vars['value']->value['i_status'] == 3) {?>
                        <td><span class="badge bg-red icon-times">已取消</span></td>
                    <?php }?>

                    <?php if ($_smarty_tpl->tpl_vars['value']->value['deal_type'] == 1) {?>
                        <td><span class="badge bg-blue-light icon-arrow-up">线上支付</span></td>
                    <?php } else { ?>
                        <td><span class="badge bg-yellow-light icon-arrow-down">线下支付</span></td>
                    <?php }?>
                    <td>
                        <a class="button border-blue button-little" href="index.php?action=order&op=edit&id=<?php echo $_smarty_tpl->tpl_vars['value']->value['l_study_order_id'];?>
"><span class="icon-edit text-blue"></span> 修改</a> 
                        <a class="button border-yellow button-little" href="javascript:;" onclick="javascript:delschool(<?php echo $_smarty_tpl->tpl_vars['value']->value['l_study_order_id'];?>
, this);">
                            <span class="icon-trash-o text-red"></span> 删除</a>
                    </td>
                </tr>
                <?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
            </table>
            <div class="panel-foot text-center">
                <?php echo $_smarty_tpl->tpl_vars['pagehtml']->value;?>

            </div>
        </div>
    </form>
    <?php echo '<script'; ?>
>
    // 删除驾校
    function delschool(id, obj) {
        if(confirm('你确定删除吗？')) {
            $.ajax({
                type:"POST",
                dataType:"JSON",
                url:"index.php?action=order&op=del",
                data:{"id":id},
                success:function(data) {
                    if(data.code == 1) {
                        $(obj).parents('.orderlist').remove();
                    }
                }
            })
        } else {
            return false;
        }
    }

    // 批量删除
    function delall() {
        if($('.table-hover').find('input:checked').val() == undefined) {
            alert('请选择需要删除的订单列表！');
            return false;
        }
        if(confirm('你确定删除吗？')) {
            var check_id = '';
            $(".table-hover input:checkbox").each(function(index) {
                if($("#checkbox_"+index+':checked').val() != undefined) {
                    check_id += $("#checkbox_"+index+':checked').val()+',';
                    
                }
                // alert($("#checkbox_"+index).val());
            });
            if(check_id.lastIndexOf(',') > 0) {
                check_id = check_id.substr(0, check_id.lastIndexOf(','));
            }
            // alert(check_id);
            $.ajax({
                type:"POST",
                url:"index.php?action=order&op=delmore",
                data:{"check_id":check_id},
                dataType:"JSON",
                success:function(data) {
                    if(data.code == 1) {
                        location.href="index.php?action=order&op=index";
                    }
                }
            })
        } else {
            return false;
        }
    }
    <?php echo '</script'; ?>
>
</body>
</html><?php }
}
?>