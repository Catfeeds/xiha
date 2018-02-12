<?php /* Smarty version 3.1.27, created on 2016-06-28 18:35:19
         compiled from "D:\wlwork\php\admin\templates\school\index.html" */ ?>
<?php
/*%%SmartyHeaderCode:19563577252e72099d7_61581884%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c61e7d2416a4c4ceb54cb1ac9d40ac76a0ee0f9c' => 
    array (
      0 => 'D:\\wlwork\\php\\admin\\templates\\school\\index.html',
      1 => 1464060764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19563577252e72099d7_61581884',
  'variables' => 
  array (
    'op' => 0,
    'keywords' => 0,
    'school_list' => 0,
    'key' => 0,
    'value' => 0,
    'pagehtml' => 0,
    'action' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_577252e7317293_38820481',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_577252e7317293_38820481')) {
function content_577252e7317293_38820481 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '19563577252e72099d7_61581884';
echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
?>


	<form method="post" action="index.php?action=school&op=searchheadmaster" onSubmit="return searchcheck();">
        <div class="panel admin-panel">
        	<div class="panel-head"><strong>驾校列表</strong></div>
            <div class="padding border-bottom">
                <input type="button" class="button button-small checkall" name="checkall" checkfor="school_id[]" value="全选" />
                <a href="index.php?action=school&op=add" class="button button-small border-green">添加驾校</a>
                <input type="button" class="button button-small border-yellow" onclick="javascript:delall()" value="批量删除" />

                <select class="input input-auto" name="search_condition" id="search_condition">
                    <option value="1" >驾校名称</option>
                    <option value="2" >城市名称</option>
                    <option value="3" >省份名称</option>
                </select>

                <input type="text" class="input input-auto" size="20" name="keyword" value="<?php if ($_smarty_tpl->tpl_vars['op']->value == 'search') {
echo $_smarty_tpl->tpl_vars['keywords']->value;
}?>" placeholder="请输入查询条件" id="keywords">
                <input type="hidden" value="" name="order_id">
                <input type="submit" id="headmaster-search" class="button border-blue" value="搜索" />

            </div>
            <table class="table table-hover">
            	<tr>
                    <th width="45">选择</th>
                    <th width="45">ID</th>
                    <th width="100">名称</th>
                    <th width="100">品牌标识</th>
                    <th width="120">法人代表</th>
                    <th width="100">法人手机</th>
                    <th width="150">组织结构代码</th>
                    <th width="150">驾校性质</th>
                    <th width="150">地址</th>
                    <th width="150">是否展示<span class="tips icon-exclamation-circle" title="鼠标点击驾校下面展示不展示按钮，可改变在app的展示状态" data-place="right" data-toggle="hover" style="color:#f60; cursor:pointer;"></span></th>
                    <th width="150">操作</th>
                </tr>
                <!-- 循环列表 -->
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
                <tr class="schoolist">
                    <td><input type="checkbox" name="school_id[]" id="checkbox_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" class="school_check" value="<?php echo $_smarty_tpl->tpl_vars['value']->value['school_id'];?>
" /></td>
                    <td><?php echo $_smarty_tpl->tpl_vars['value']->value['school_id'];?>
</td>
                    <td><a href="javascript:;" onclick="javascript:setschoolbrand(<?php echo $_smarty_tpl->tpl_vars['value']->value['school_id'];?>
,this)">
                            <?php if ($_smarty_tpl->tpl_vars['value']->value['brand'] == 2) {?>
                            <span class="badge bg-red">品牌驾校</span>
                            <?php } else { ?>
                            <span class="badge bg-green">普通驾校</span></td>
                            <?php }?>
                        </a>
                    </td>
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
                        <a href="javascript:;" onclick="javascript:setonlinestatus(<?php echo $_smarty_tpl->tpl_vars['value']->value['school_id'];?>
,this)">
                            <?php if ($_smarty_tpl->tpl_vars['value']->value['is_show'] == 1) {?>
                            <span class="badge bg-green">展示</span>
                            <?php } else { ?>
                            <span class="badge bg-gray">不展示</span>
                            <?php }?>
                        </a>
                    </td>

                    <td> 
                       
                        <a class="button border-blue button-little" href="index.php?action=school&op=edit&id=<?php echo $_smarty_tpl->tpl_vars['value']->value['school_id'];?>
">
                            <span class="icon-edit text-blue"></span> 修改</a> 
                        <a class="button border-yellow button-little" href="javascript:;" onclick="javascript:delschool(<?php echo $_smarty_tpl->tpl_vars['value']->value['school_id'];?>
, this);">
                            <span class="icon-trash-o text-red"></span> 删除</a>
                    </td>
                </tr>
                <?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
                <?php } else { ?>
                <tr><td colspan=5>暂无驾校列表</td></tr>
                 
                <?php }?>
            </table>

            <!-- 分页 -->
            <div class="panel-foot text-center">
                <?php echo $_smarty_tpl->tpl_vars['pagehtml']->value;?>

            </div>
        </div>
    </form>
    <?php echo '<script'; ?>
>

    // 跳转页面
    $('#skipping').click(function() {
        $(this).html('跳转中...');
        var selectpage = $('#selectpage').find('option:selected').val();
        location.href="index.php?action=<?php echo $_smarty_tpl->tpl_vars['action']->value;?>
&op=<?php echo $_smarty_tpl->tpl_vars['op']->value;?>
&page="+selectpage;
    })
    
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

    // 设置在线不在线状态
    function setonlinestatus(id, obj) {
        $.ajax({
            type:"POST",
            url:"index.php?action=school&op=online",
            // dataType:"JSON",
            data:{'id':id},
            success:function(data) {
                if(data == 1) {
                    if($(obj).find('span').hasClass('bg-green')) {
                        $(obj).find('span').removeClass('bg-green');
                        $(obj).find('span').addClass('bg-gray');
                        $(obj).find('span').html('不展示');
                    } else {
                        $(obj).find('span').addClass('bg-green');
                        $(obj).find('span').removeClass('bg-gray');
                        $(obj).find('span').html('展示');
                    }
                        
                }
            }
        })
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

     //设置品牌驾校
     function setschoolbrand(id, obj) {
            $.ajax({
                type:"POST",
                url:"index.php?action=school&op=brand",
                // dataType:"JSON",
                data:{'id':id},
                success:function(data) {
                    if(data == 1) {
                        if($(obj).find('span').hasClass('bg-red')) {
                            $(obj).find('span').removeClass('bg-red');
                            $(obj).find('span').addClass('bg-green');
                            $(obj).find('span').html('普通驾校');
                        } else {
                            $(obj).find('span').addClass('bg-red');
                            $(obj).find('span').removeClass('bg-green');
                            $(obj).find('span').html('品牌驾校');
                        }
                            
                    }
                }
            })
        }

        // 搜索检测
        function searchcheck() {
          var keywords = $('#keywords').val();
          var type = $('#search_condition').find('option:selected').val();
          if(keywords.trim() == '' || type.trim() == '') {
            layer.msg('请输入搜索内容',{icon:0, offset:['0px','40%'], time:1500});
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