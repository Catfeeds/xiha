<?php /* Smarty version 3.1.27, created on 2015-08-30 10:15:45
         compiled from "E:\AppServ\www\service\admin\templates\member\index.html" */ ?>
<?php
/*%%SmartyHeaderCode:64355e26751b33f28_06019307%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd6589e487e7b62533615f951e90b00650bad2141' => 
    array (
      0 => 'E:\\AppServ\\www\\service\\admin\\templates\\member\\index.html',
      1 => 1439109089,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '64355e26751b33f28_06019307',
  'variables' => 
  array (
    'member_list' => 0,
    'key' => 0,
    'value' => 0,
    'pagehtml' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_55e26751bd1581_07824377',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_55e26751bd1581_07824377')) {
function content_55e26751bd1581_07824377 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '64355e26751b33f28_06019307';
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
            <div class="panel-head"><strong>会员列表</strong></div>
            <div class="padding border-bottom">
                <input type="button" class="button button-small checkall" name="checkall" checkfor="member_id[]" value="全选" />
                <a href="index.php?action=member&op=add" class="button button-small border-green">添加会员</a>
                <input type="button" class="button button-small border-yellow" onclick="javascript:delall()" value="批量删除" />
            </div>
            <table class="table table-hover">
                <tr>
                    <th width="45">选择</th>
                    <th width="100">用户头像</th>
                    <th width="100">用户名</th>
                    <th width="120">真实姓名</th>
                    <th width="100">手机号码</th>
                    <th width="100">性别</th>
                    <th width="150">年龄</th>
                    <th width="150">身份证</th>
                    <th width="150">地址</th>
                    <th width="150">用户来源</th>
                    <th width="150">操作</th>
                </tr>
                <!-- 循环列表 -->

                <?php
$_from = $_smarty_tpl->tpl_vars['member_list']->value;
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
                <tr class="memberlist">
                    <td><input type="checkbox" name="member_id[]" id="checkbox_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" class="school_check" value="<?php echo $_smarty_tpl->tpl_vars['value']->value['l_user_id'];?>
" /></td>
                    <td><img src="<?php echo $_smarty_tpl->tpl_vars['value']->value['user_photo'];?>
" alt=""></td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['s_username'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['s_real_name'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['s_phone'];?>
</td>
                    <td><?php if ($_smarty_tpl->tpl_vars['value']->value['sex'] == 1) {?><span class="badge bg-green icon-male">男</span><?php } else { ?><span class="badge bg-red icon-female">女</span><?php }?></td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['age'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['identity_id'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['address'];?>
</td>
                    <td><?php if ($_smarty_tpl->tpl_vars['value']->value['i_from'] == 1) {?><span class="badge bg-main icon-android">安卓</span><?php } else { ?><span class="badge icon-apple">苹果</span><?php }?></td>
                    <td>
                        <a class="button border-blue button-little" href="index.php?action=member&op=edit&id=<?php echo $_smarty_tpl->tpl_vars['value']->value['l_user_id'];?>
"><span class="icon-edit text-blue"></span> 修改</a> 
                        <a class="button border-yellow button-little" href="javascript:;" onclick="javascript:delmember(<?php echo $_smarty_tpl->tpl_vars['value']->value['l_user_id'];?>
, this);">
                            <span class="icon-trash-o text-red"></span> 删除</a>
                    </td>
                </tr>
                <?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
            </table>

            <!-- 分页 -->
            <div class="panel-foot text-center">
                <?php echo $_smarty_tpl->tpl_vars['pagehtml']->value;?>

            </div>
        </div>
    </form>
    <?php echo '<script'; ?>
>
    // 删除驾校
    function delmember(id, obj) {
        if(confirm('你确定删除吗？')) {
            $.ajax({
                type:"POST",
                dataType:"JSON",
                url:"index.php?action=member&op=del",
                data:{"id":id},
                success:function(data) {
                    if(data.code == 1) {
                        $(obj).parents('.memberist').remove();
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
            alert('请选择需要删除的会员列表！');
            return false;
        }
        if(confirm('你确定删除吗？')) {
            var check_id = '';
            $(".table-hover input:checkbox").each(function(index) {
                check_id += $("#checkbox_"+index).val()+',';
                // alert($("#checkbox_"+index).val());
            });
            if(check_id.lastIndexOf(',') > 0) {
                check_id = check_id.substr(0, check_id.lastIndexOf(','));
            }
            alert(check_id);
            $.ajax({
                type:"POST",
                url:"index.php?action=member&op=delmore",
                data:{"check_id":check_id},
                dataType:"JSON",
                success:function(data) {
                    alert(data);
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