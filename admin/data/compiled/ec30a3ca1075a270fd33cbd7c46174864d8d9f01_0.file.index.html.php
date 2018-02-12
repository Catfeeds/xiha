<?php /* Smarty version 3.1.27, created on 2015-08-31 23:02:07
         compiled from "E:\AppServ\www\service\admin\templates\coach\index.html" */ ?>
<?php
/*%%SmartyHeaderCode:2494555e46c6f289c79_38752127%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ec30a3ca1075a270fd33cbd7c46174864d8d9f01' => 
    array (
      0 => 'E:\\AppServ\\www\\service\\admin\\templates\\coach\\index.html',
      1 => 1441033231,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2494555e46c6f289c79_38752127',
  'variables' => 
  array (
    'coach_list' => 0,
    'key' => 0,
    'value' => 0,
    'pagehtml' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_55e46c6f35a8a0_42811046',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_55e46c6f35a8a0_42811046')) {
function content_55e46c6f35a8a0_42811046 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '2494555e46c6f289c79_38752127';
echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
?>


	<form method="post">
        <div class="panel admin-panel">
        	<div class="panel-head"><strong>教练列表</strong></div>
            <div class="padding border-bottom">
                <input type="button" class="button button-small checkall" name="checkall" checkfor="id" value="全选" />
                <a href="index.php?action=coach&op=add" class="button button-small border-green">添加教练</a>
                <input type="button" class="button button-small border-yellow" onclick="delallcoach()" value="批量删除" />
                <!-- <input type="button" class="button button-small border-blue" value="回收站" /> -->
            </div>
            <table class="table table-hover">
            	<tr>
                    <th width="50">选择</th>
                    <th width="50">名称</th>
                    <th width="100">手机号码</th>
                    <th width="100">所属驾校</th>
                    <th width="150">培训课程</th>
                    <th width="80">培训牌照</th>
                    <th width="80">所属车</th>
                    <th width="100">教练星级</th>
                    <th width="80">好评数</th>
                    <th width="100">服务次数</th>
                    <th width="100">通过人数</th>
                    <th width="150">地址</th>
                    <th width="100">类型</th>
                    <th width="80">在线</th>
                    <th width="120">注册时间</th>
                    <th width="150">操作</th>
                </tr>
                <!-- 循环列表 -->

                <?php
$_from = $_smarty_tpl->tpl_vars['coach_list']->value;
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
                <tr>
                    <td><input type="checkbox" id="checkbox_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" name="id" value="<?php echo $_smarty_tpl->tpl_vars['value']->value['coach_id'];?>
" /></td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['coach_name'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['coach_phone'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['school_name'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['coach_lesson'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['coach_lisence'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['coach_car_name'];?>
</td>
                    <td>
                        <?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['loop'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['name'] = 'loop';
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['value']->value['coach_star']) ? count($_loop) : max(0, (int) $_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['loop']['total']);
?>
                        <span class="icon-star text-small text-red"></span>
                        <?php endfor; endif; ?>
                    </td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['good_coach_star'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['service_count'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['success_count'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['coach_address'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['type'];?>
</td>
                    <td>
                        <a href="javascript:;" onclick="javascript:setonlinestatus(<?php echo $_smarty_tpl->tpl_vars['value']->value['coach_id'];?>
,this)">
                            <?php if ($_smarty_tpl->tpl_vars['value']->value['is_online'] == 0) {?>
                            <span class="badge bg-green"><?php echo $_smarty_tpl->tpl_vars['value']->value['online_status'];?>
</span>
                            <?php } else { ?>
                            <span class="badge bg-gray"><?php echo $_smarty_tpl->tpl_vars['value']->value['online_status'];?>
</span></td>
                            <?php }?>
                        </a>
                    </td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['addtime'];?>
</td>
                    <td>
                        <!-- <a data-toggle="click" onclick = "javascript:showcoach({$value.coach_id})" data-target="#mydialog-{$value.coach_id}" data-mask="1" data-width="50%" class="button border-blue button-little dialogs" href="javascript:;">查看</a>  -->
                        <a onclick = "javascript:showcoach(<?php echo $_smarty_tpl->tpl_vars['value']->value['coach_id'];?>
)" class="button border-blue button-little" href="javascript:;">查看</a> 
                        <a class="button border-blue button-little" href="index.php?action=coach&op=edit&id=<?php echo $_smarty_tpl->tpl_vars['value']->value['coach_id'];?>
">修改</a> 
                        <a class="button border-yellow button-little" href="#" onclick="delcoach(<?php echo $_smarty_tpl->tpl_vars['value']->value['coach_id'];?>
,this)">删除</a>
                    </td>
                </tr>
                <!-- 弹出框 -->
                <?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
            </table>
            <div id="showdialog">
    
            </div>

            <!-- 分页 -->
            <div class="panel-foot text-center">
                <?php echo $_smarty_tpl->tpl_vars['pagehtml']->value;?>

            </div>
        </div>
    </form>
    <?php echo '<script'; ?>
>
        // 设置在线不在线状态
        function setonlinestatus(id, obj) {
            $.ajax({
                type:"POST",
                url:"index.php?action=coach&op=online",
                dataType:"JSON",
                data:{'id':id},
                success:function(data) {
                    if(data.code == 1) {
                        if($(obj).find('span').hasClass('bg-green')) {
                            $(obj).find('span').removeClass('bg-green');
                            $(obj).find('span').addClass('bg-gray');
                            $(obj).find('span').html('不在线');
                        } else {
                            $(obj).find('span').addClass('bg-green');
                            $(obj).find('span').removeClass('bg-gray');
                            $(obj).find('span').html('在线');
                        }
                            
                    }
                }
            })
        }

        // 删除教练
        function delcoach(id, obj) {
            if(window.confirm('你确定删除吗？')) {
                $.ajax({
                    type:"POST",
                    url:"index.php?action=coach&op=delcoach",
                    dataType:"JSON",
                    data:{'id':id},
                    success:function(data) {
                        if(data.code == 1) {
                            $(obj).parents('tr').remove();
                        }
                    }
                })
                
            } else {
                return false;
            }
        }

        // 批量删除教练
        // 批量删除
        function delallcoach() {
            if($('.table-hover').find('input:checked').val() == undefined) {
                alert('请选择需要删除的教练信息！');
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
                    url:"index.php?action=coach&op=delmore",
                    data:{"check_id":check_id},
                    dataType:"JSON",
                    success:function(data) {
                        if(data.code == 1) {
                            location.href="index.php?action=coach&op=index";
                        }
                        // alert(data.code);
                    }
                })
            } else {
                return false;
            }
        }

        // 展示教练信息
        function showcoach(id) {
            //iframe层-父子操作
            layer.open({
                type: 2,
                title:'教练详细信息',
                area: ['70%', '70%'],
                fix: false, //不固定
                maxmin: true,
                shadeClose: true,
                content: 'index.php?action=coach&op=showiframe&id='+id
            });

            // $.ajax({
            //     type:"POST",
            //     url:"index.php?action=coach&op=show",
            //     data:{id:id},
            //     dataType:"JSON",
            //     async:false,
            //     success:function(data) {
            //         // alert(data);

            //         if(data.code == 1) {

            //             var html = '<div id="mydialog-'+id+'"><div class="dialog"><div class="dialog-head"> <span class="close rotate-hover"></span> <strong>'+data.data.s_coach_name+' id:'+id+'</strong> </div> <div class="dialog-body"> <div class="collapse"> <div class="panel active"> <div class="panel-head"><h4>...</h4></div> <div class="panel-body">...</div> </div> <div class="panel"> <div class="panel-head"><h4>...</h4></div> <div class="panel-body">...</div> </div> <div class="panel"> <div class="panel-head"><h4>...</h4></div> <div class="panel-body">...</div> </div> </div> </div> <div class="dialog-foot"> <button class="button dialog-close">取消</button> <button class="button bg-green">确认</button></div></div></div>';
            //             $('#showdialog').html(html);
            //         }
            //     },
            //     error:function(data) {
            //         alert(data)
            //     }
            // })
        }
    <?php echo '</script'; ?>
>
<?php echo $_smarty_tpl->getSubTemplate ("library/footer.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
?>

<?php }
}
?>