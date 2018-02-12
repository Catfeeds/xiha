<?php /* Smarty version 3.1.27, created on 2015-08-30 10:16:21
         compiled from "E:\AppServ\www\service\admin\templates\manager\index.html" */ ?>
<?php
/*%%SmartyHeaderCode:641255e26775a4cae3_85066638%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'eaba1b683664295235de96b01e2e9de41b33f371' => 
    array (
      0 => 'E:\\AppServ\\www\\service\\admin\\templates\\manager\\index.html',
      1 => 1439111746,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '641255e26775a4cae3_85066638',
  'variables' => 
  array (
    'manage_list' => 0,
    'key' => 0,
    'value' => 0,
    'pagehtml' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_55e26775ae46d3_72374439',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_55e26775ae46d3_72374439')) {
function content_55e26775ae46d3_72374439 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '641255e26775a4cae3_85066638';
echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
?>

    <form method="post">
        <div class="panel admin-panel">
            <div class="panel-head"><strong>管理员列表</strong></div>
            <div class="padding border-bottom">
                <input type="button" class="button button-small checkall" name="checkall" checkfor="manager_id[]" value="全选" />
                <a href="index.php?action=manager&op=add" class="button button-small border-green">添加管理员</a>
                <input type="button" class="button button-small border-yellow" onclick="javascript:delall()" value="批量删除" />
            </div>
            <table class="table table-hover">
                <tr>
                   <th width="45">选择</th>
                   <th width="100">用户名</th>
                   <th width="100">管理角色</th>
                   <th width="100">操作</th>

               </tr>
               <!-- 循环列表 -->

               <?php
$_from = $_smarty_tpl->tpl_vars['manage_list']->value;
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
               <tr class="managerlist">
                   <td><input type="checkbox" name="manager_id[]" id="checkbox_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" class="manages_check" value="<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" /></td>
                   <td><?php echo $_smarty_tpl->tpl_vars['value']->value['name'];?>
</td>
                   <td><?php echo $_smarty_tpl->tpl_vars['value']->value['role_permission'];?>
</td>
                   <td>
                      <a class="button border-blue button-little" href="index.php?action=manager&op=edit&id=<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
"><span class="icon-edit text-blue"></span> 修改</a>
                      <?php if ($_smarty_tpl->tpl_vars['value']->value['name'] != 'admin') {?>
                        <a class="button border-yellow button-little" href="javascript:;" onclick="javascript:delmanager(<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
, this);">
                           <span class="icon-trash-o text-red"></span> 删除</a>
                      <?php }?>
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
    function delmanager(id, obj) {
        if(confirm('你确定删除吗？')) {
            $.ajax({
                type:"POST",
                dataType:"JSON",
                url:"index.php?action=manager&op=del",
                data:{"id":id},
                success:function(data) {
                    if(data.code == 1) {
                        $(obj).parents('.managerlist').remove();
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
            alert('请选择需要删除的管理员列表！');
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
                url:"index.php?action=manager&op=delmore",
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