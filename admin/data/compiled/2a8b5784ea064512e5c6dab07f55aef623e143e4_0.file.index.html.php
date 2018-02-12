<?php /* Smarty version 3.1.27, created on 2015-10-05 15:18:57
         compiled from "E:\AppServ\www\service\admin\templates\shifts\index.html" */ ?>
<?php
/*%%SmartyHeaderCode:93075612246186c737_16663676%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2a8b5784ea064512e5c6dab07f55aef623e143e4' => 
    array (
      0 => 'E:\\AppServ\\www\\service\\admin\\templates\\shifts\\index.html',
      1 => 1442901615,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '93075612246186c737_16663676',
  'variables' => 
  array (
    'school_list' => 0,
    'key' => 0,
    'value' => 0,
    'pagehtml' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_56122461912620_98191818',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_56122461912620_98191818')) {
function content_56122461912620_98191818 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '93075612246186c737_16663676';
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
        	<div class="panel-head"><strong>驾校列表</strong></div>
            <div class="padding border-bottom">
                <input type="button" class="button button-small checkall" name="checkall" checkfor="school_id[]" value="全选" />
                <a href="index.php?action=school&op=add" class="button button-small border-green">添加驾校</a>
                <!-- <input type="button" class="button button-small border-green" value="添加文章" /> -->
                <input type="button" class="button button-small border-yellow" onclick="javascript:delall()" value="批量删除" />
                <!-- <input type="button" class="button button-small border-blue" value="回收站" /> -->
            </div>
            <table class="table table-hover">
            	<tr>
                    <th width="45">选择</th>
                    <th width="100">驾校名称</th>
                    <th width="120">法人代表</th>
                    <th width="100">法人手机</th>
                    <th width="150">组织结构代码</th>
                    <th width="150">驾校性质</th>
                    <th width="150">地址</th>
                    <th width="150">操作</th>
                </tr>
                <!-- 循环列表 -->

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
                <tr class="schoolist">
                    <td><input type="checkbox" name="school_id[]" id="checkbox_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" class="school_check" value="<?php echo $_smarty_tpl->tpl_vars['value']->value['school_id'];?>
" /></td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['school_name'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['frdb'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['frdb_mobile'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['zzjgdm'];?>
</td>
                    <?php if ($_smarty_tpl->tpl_vars['value']->value['dwxz'] == 1) {?>
                        <td><span class="badge bg-main">一类驾校</span></td>
                    <?php } elseif ($_smarty_tpl->tpl_vars['value']->value['dwxz'] == 2) {?>
                        <td><span class="badge bg-green">二类驾校</span></td>
                    <?php } elseif ($_smarty_tpl->tpl_vars['value']->value['dwxz'] == 3) {?>
                        <td><span class="badge bg-yellow">三类驾校</span></td>
                    <?php }?>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['address'];?>
</td>
                    <td>
                        <a class="button border-blue button-little" href="index.php?action=school&op=edit&id=<?php echo $_smarty_tpl->tpl_vars['value']->value['school_id'];?>
"><span class="icon-edit text-blue"></span> 修改</a> 
                        <a class="button border-yellow button-little" href="javascript:;" onclick="javascript:delschool(<?php echo $_smarty_tpl->tpl_vars['value']->value['school_id'];?>
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
    function delschool(id, obj) {
        if(confirm('你确定删除吗？')) {
            $.ajax({
                type:"POST",
                dataType:"JSON",
                url:"index.php?action=school&op=del",
                data:{"id":id},
                success:function(data) {
                    if(data.code == 1) {
                        $(obj).parents('.schoolist').remove();
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
            alert('请选择需要删除的驾校列表！');
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
                url:"index.php?action=school&op=delmore",
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