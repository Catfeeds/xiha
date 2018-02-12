<?php

/**
 * 手机网站单点入口
 */

require_once './include/init.inc.php';

// 动作定义及获取当前动作/操作
$actions = array('admin','config','school','learncar','coach','car','member','manager','shifts','signup','ads');
$action = isset($_GET['action']) ? trim($_GET['action']) : 'admin';
$action = in_array($action, $actions) ? $action : 'admin';
$smarty->assign('action', $action);
$op = isset($_GET['op']) ? trim($_GET['op']) : 'index';
$smarty->assign('op', $op);
$inajax = isset($_GET['inajax']) ? true : false;

include MOBILE_ROOT.'./action/'.$action.'.inc.php';
?>  