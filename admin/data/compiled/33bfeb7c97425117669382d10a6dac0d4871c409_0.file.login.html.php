<?php /* Smarty version 3.1.27, created on 2015-08-30 10:15:28
         compiled from "E:\AppServ\www\service\admin\templates\admin\login.html" */ ?>
<?php
/*%%SmartyHeaderCode:1157555e2674035bfd6_31939974%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '33bfeb7c97425117669382d10a6dac0d4871c409' => 
    array (
      0 => 'E:\\AppServ\\www\\service\\admin\\templates\\admin\\login.html',
      1 => 1439087859,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1157555e2674035bfd6_31939974',
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_55e267403b0e67_93073135',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_55e267403b0e67_93073135')) {
function content_55e267403b0e67_93073135 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '1157555e2674035bfd6_31939974';
echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
?>


<div class="container" style="width:100%; background-image:url('templates/assests/images/bg_login_03.jpg');">
    <div class="line">
        <div class="xs6 xm4 xs3-move xm4-move">
            <br /><br />
            <div class="media media-y">
                <a href="javascript:;" style="font-size:25px; color:#fff; font-weight:bold;">嘻哈后台管理系统</a>
                <!-- <br> <span style="color:#fff; font-weight:bold;">beta v1.0</span> -->
            </div>
            <br /><br />
            <form action="index.php?action=admin&op=logincheck" method="post">
            <div class="panel" style="background:#fff !important; margin:0px auto; width:350px; border:none;">
                <div class="panel-head" style="background:none; border:none;"></div>
                <div class="panel-body" style="padding:30px;">
                    <div class="form-group">
                        <div class="field field-icon-right input_placeholder">
                            <input type="text" style="border:none; background:#7E99AE; color:#fff;" class="input" name="username" placeholder="登录账号" data-validate="required:请填写账号" />
                            <span class="icon icon-user" style="color:#fff;"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="field field-icon-right input_placeholder">
                            <input type="password" style="border:none; background:#7E99AE; color:#fff;" class="input" name="password" placeholder="登录密码" data-validate="required:请填写密码" />
                            <span class="icon icon-key" style="color:#fff;"></span>
                        </div>
                    </div>
       <!--              <div class="form-group">
                        <div class="field">
                            <input type="text" class="input" name="passcode" placeholder="填写右侧的验证码" data-validate="required:请填写右侧的验证码" />
                            <img src="images/passcode.jpg" width="80" height="32" class="passcode" />
                        </div>
                    </div> -->
                </div>
                <div class="panel-foot text-center" style="background:none; border:none;"><button class="button button-block bg-main text-big">立即登录后台</button></div>
            </div>
            </form>

        </div>
    </div>
</div>
<?php echo '<script'; ?>
>
    // 获取当前屏幕的高度
    $(function() {
        var height = $(window).height();
        $('.container').css('height', height); 
    })
<?php echo '</script'; ?>
>     
<?php echo $_smarty_tpl->getSubTemplate ("library/footer.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);

}
}
?>