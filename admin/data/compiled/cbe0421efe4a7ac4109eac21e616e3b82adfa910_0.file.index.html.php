<?php /* Smarty version 3.1.27, created on 2015-09-22 16:06:07
         compiled from "E:\web\admin\templates\car\index.html" */ ?>
<?php
/*%%SmartyHeaderCode:2275656010bef9f4bf7_91526145%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cbe0421efe4a7ac4109eac21e616e3b82adfa910' => 
    array (
      0 => 'E:\\web\\admin\\templates\\car\\index.html',
      1 => 1441033230,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2275656010bef9f4bf7_91526145',
  'variables' => 
  array (
    'carlist' => 0,
    'key' => 0,
    'value' => 0,
    'v' => 0,
    'pagehtml' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_56010befb2d438_18095792',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_56010befb2d438_18095792')) {
function content_56010befb2d438_18095792 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '2275656010bef9f4bf7_91526145';
echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
?>

	<form method="post">
        <div class="panel admin-panel">
        	<div class="panel-head"><strong>车辆列表</strong></div>
            <div class="padding border-bottom">
                <input type="button" class="button button-small checkall" name="checkall" checkfor="car_id[]" value="全选" />
                <a href="index.php?action=car&op=add" class="button button-small border-green">添加车辆</a>
                <!-- <input type="button" class="button button-small border-green" value="添加文章" /> -->
                <input type="button" class="button button-small border-yellow" onclick="javascript:delall()" value="批量删除" />
                <!-- <input type="button" class="button button-small border-blue" value="回收站" /> -->
            </div>
            <table class="table table-hover">
            	<tr>
                    <th width="100">选择</th>
                    <th width="200">名称</th>
                    <th width="200">图片</th>
                    <th width="220">车辆类型</th>
                    <th width="220">车牌号</th>
                    <th width="400">添加时间</th>
                    <th width="100">操作</th>
                </tr>
                <!-- 循环列表 -->

                <?php
$_from = $_smarty_tpl->tpl_vars['carlist']->value;
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
                <tr class="carlist">
                    <td><input type="checkbox" name="car_id[]" id="checkbox_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" class="school_check" value="<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" /></td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['name'];?>
</td>
                    <td>
                    <?php if ($_smarty_tpl->tpl_vars['value']->value['imgurl']) {?>
                    <?php
$_from = $_smarty_tpl->tpl_vars['value']->value['imgurl'];
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['v'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['v']->_loop = false;
$_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['v']->value) {
$_smarty_tpl->tpl_vars['v']->_loop = true;
$foreach_v_Sav = $_smarty_tpl->tpl_vars['v'];
?>
                        <a href="<?php echo $_smarty_tpl->tpl_vars['v']->value;?>
" target="_blank"><img width="50px" height="50px;" src="<?php echo $_smarty_tpl->tpl_vars['v']->value;?>
" alt=""></a>
                    <?php
$_smarty_tpl->tpl_vars['v'] = $foreach_v_Sav;
}
?>
                    <?php } else { ?>
                        <img src="templates/assests/images/logo.jpg" alt="">
                    <?php }?>
                    </td>
                    <td><?php if ($_smarty_tpl->tpl_vars['value']->value['car_type'] == 1) {?> <span class="tag bg-main">普通车型</span><?php } elseif ($_smarty_tpl->tpl_vars['value']->value['car_type'] == 2) {?><span class="tag bg-yellow">加强车型</span><?php } else { ?><span class="tag bg-green">模拟车型</span><?php }?></td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['car_no'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['addtime'];?>
</td>

                    <td>
                        <a class="button border-blue button-little" href="index.php?action=car&op=edit&id=<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
"><span class="icon-edit text-blue"></span> 修改</a> 
                        <a class="button border-yellow button-little" href="javascript:;" onclick="javascript:delcar(<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
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
    function delcar(id, obj) {
        if(window.confirm('你确定删除吗？')) {
            $.ajax({
                type:"POST",
                url:"index.php?action=car&op=del",
                // dataType:"JSON",
                data:{'id':id},
                success:function(data) {
                    if(data == 1) {
                        $(obj).parents('.carlist').remove();
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
            alert('请选择需要删除的教练信息！');
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
<?php echo $_smarty_tpl->getSubTemplate ("library/footer.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
?>

<?php }
}
?>