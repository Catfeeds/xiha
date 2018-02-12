<?php /* Smarty version 3.1.27, created on 2015-09-22 13:50:22
         compiled from "E:\AppServ\www\service\admin\templates\admin\index.html" */ ?>
<?php
/*%%SmartyHeaderCode:320865600ec1e5928a1_69562880%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ec7cb72b23b181edd52cfdcc3f45475eed344bd1' => 
    array (
      0 => 'E:\\AppServ\\www\\service\\admin\\templates\\admin\\index.html',
      1 => 1442450661,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '320865600ec1e5928a1_69562880',
  'variables' => 
  array (
    'name' => 0,
    'manage_config' => 0,
    'key' => 0,
    'value' => 0,
    'v' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_5600ec1e6c7c04_64515762',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5600ec1e6c7c04_64515762')) {
function content_5600ec1e6c7c04_64515762 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '320865600ec1e5928a1_69562880';
echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
?>


<div class="lefter">
    <div class="logo">
        <a href="javascript:;" onclick="javascript:redirecturl('coach', 'index', this)">
            <!-- <img src="templates/assests/images/logo.jpg"/> -->
            嘻哈学车</a>
    </div>
</div>
<div class="righter nav-navicon" id="admin-nav">
    <div class="mainer">
        <div class="admin-navbar">
            <span class="float-right">
                <span>您好，<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
，欢迎您的光临。</span>
                <!-- <a class="button button-little bg-main" href="http://www.pintuer.com" target="_blank">个人信息</a> -->
                <a class="button button-little bg-yellow" href="index.php?action=admin&op=logout">注销登录</a>
            </span>
            <ul class="nav nav-inline admin-nav">
                <?php
$_from = $_smarty_tpl->tpl_vars['manage_config']->value;
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
                <li <?php if ($_smarty_tpl->tpl_vars['key']->value == 1) {?>class="active"<?php }?>>
                    <a href="javascript:;" onclick="javascript:redirecttopurl('<?php echo $_smarty_tpl->tpl_vars['value']->value['controll'];?>
', '<?php echo $_smarty_tpl->tpl_vars['value']->value['function'];?>
', this)" class="icon-home"> <?php echo $_smarty_tpl->tpl_vars['value']->value['bigcate_name'];?>
</a>
                    <ul>
                        <?php
$_from = $_smarty_tpl->tpl_vars['value']->value['seccate_name'];
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
                        <li><a href="javascript:;" onclick="javascript:redirecturl('<?php echo $_smarty_tpl->tpl_vars['v']->value['controll'];?>
','<?php echo $_smarty_tpl->tpl_vars['v']->value['function'];?>
', this)"><?php echo $_smarty_tpl->tpl_vars['v']->value['cate_name'];?>
</a></li>
                        <?php
$_smarty_tpl->tpl_vars['v'] = $foreach_v_Sav;
}
?>
                    </ul>
                </li>
                <?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>

               <!--  <li class="active">
                    <a href="javascript:;" onclick="javascript:redirecttopurl('coach', 'index', this)" class="icon-home"> 开始</a>
                    <ul>
                        <li><a href="javascript:;" onclick="javascript:redirecturl('coach','index', this)">教练列表</a></li>
                        <li><a href="javascript:;" onclick="javascript:redirecturl('order','index', this)">订单列表</a></li>
                        <li><a href="javascript:;" onclick="javascript:redirecturl('car','index', this)">车辆列表</a></li>
                        <li><a href="javascript:;" onclick="javascript:redirecturl('member','index', this)">会员列表</a></li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:;" onclick="javascript:redirecttopurl('coach', 'index', this)" class="icon-user"> 教练</a>
                    <ul>
                        <li><a href="javascript:;" onclick="javascript:redirecturl('coach', 'index', this)">教练列表</a></li>
                        <li><a href="javascript:;" onclick="javascript:redirecturl('coach', 'add', this)">教练添加</a></li>
                    </ul>
                </li>

                <li><a href="javascript:;" onclick="javascript:redirecttopurl('order', 'index', this)" class="icon-shopping-cart"> 订单</a>
                    <ul>
                        <li><a href="javascript:;" onclick="javascript:redirecturl('order', 'index', this)">订单列表</a></li>
                        <li><a href="javascript:;" onclick="javascript:redirecturl('order', 'pending', this)">已付款订单</a></li>
                        <li><a href="javascript:;" onclick="javascript:redirecturl('order', 'untreated', this)">未付款订单</a></li>
                        <li><a href="javascript:;" onclick="javascript:redirecturl('order', 'completed', this)">已完成订单</a></li>
                        <li><a href="javascript:;" onclick="javascript:redirecturl('order', 'cancel', this)">已取消订单</a></li>
                    </ul>
                </li>
                <li><a href="javascript:;" onclick="javascript:redirecttopurl('car', 'index', this)" class="icon-car"> 车辆</a>
                    <ul>
                        <li><a href="javascript:;" onclick="javascript:redirecturl('car', 'index', this)">车辆列表</a></li>
                        <li><a href="javascript:;" onclick="javascript:redirecturl('car', 'add', this)">车辆添加</a></li>
                    </ul>
                </li>
                <li><a href="javascript:;" onclick="javascript:redirecttopurl('member', 'index', this)" class="icon-user"> 会员</a>
                    <ul>
                        <li><a href="javascript:;" onclick="javascript:redirecturl('member', 'index', this)">会员列表</a></li>
                        <li><a href="javascript:;" onclick="javascript:redirecturl('member', 'add', this)">会员添加</a></li>
                    </ul> 
                </li>
                <li><a href="javascript:;" onclick="javascript:redirecttopurl('manager', 'index', this)" class="icon-user"> 管理员</a>
                    <ul>
                        <li><a href="javascript:;" onclick="javascript:redirecturl('manager', 'index', this)">管理列表</a></li>
                        <li><a href="javascript:;" onclick="javascript:redirecturl('manager', 'add', this)">管理添加</a></li>
                    </ul> 
                </li> -->
        <!--         <li><a href="#" class="icon-file"> 文件</a></li>
                <li><a href="#" class="icon-th-list"> 栏目</a></li> -->
            </ul>
        </div>
        <div class="admin-bread">
            <ul class="bread">
                <li><a href="javascript:;" class="icon-home nav_crumbs"> 开始</a></li>
                <li class="second_nav">后台首页</li>
            </ul>
        </div>
    </div>
</div>

<!-- <iframe name="right" id="leftMain" src="left.html" frameborder="false" scrolling="no" style="border:none; margin-bottom:0px;" width="100%" height="100%" allowtransparency="true"></iframe> -->
<!-- <div class="righter left_bar">
    <ul>
        <li class="active"><a href="javascript:redirecturl('info.html')">系统设置</a></li>
        <li><a href="javascript:redirecturl('list.html')">内容管理</a></li>
        <li><a href="#">订单管理</a></li>
        <li class=""><a href="#">会员管理</a></li>
        <li><a href="#">文件管理</a></li>
        <li><a href="#">栏目管理</a></li>
    </ul>
</div> -->


<!-- 右侧显示区域start -->
<div class="admin">
    <iframe name="right" id="rightMain" src="index.php?action=coach&op=index" frameborder="false" scrolling="auto" style="border:none; margin-bottom:0px;" width="130%" height="100%" allowtransparency="true"></iframe>
</div>
<!-- 右侧显示区域end -->

<?php echo $_smarty_tpl->getSubTemplate ("library/footer.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);

}
}
?>