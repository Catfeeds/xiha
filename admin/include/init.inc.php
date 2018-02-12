<?php

/**
 * 初始化包含文件
 */

error_reporting(E_ALL);
define('IN_FILE', true);
define('MOBILE_ROOT', substr(dirname(__FILE__), 0, -7));
define('WEB_ROOT', substr(MOBILE_ROOT, 0, -7));
session_start();
$sid = session_id();

require_once MOBILE_ROOT.'./include/config.php';
require_once MOBILE_ROOT.'./include/global.func.php';
require_once MOBILE_ROOT.'./include/upload.class.php';


// 非搜索引擎且非移动设备访问自动跳转至ZBSD主站(本地调试则不跳转)
/*
define('IS_ROBOT', checkrobot());
require_once MOBILE_ROOT.'./libs/mobiledetect.class.php';
$mobile_detect = new Mobile_Detect;
define('IS_MOBILE', $mobile_detect->isMobile());
unset($mobile_detect);
if(!IS_ROBOT && !IS_MOBILE && $_SERVER['SERVER_ADDR'] != '127.0.0.1') {
	iheader('Location: http://www.zbsd.com/');
}
*/

// MySQL 数据库操作类实例
require_once MOBILE_ROOT.'./include/mysql.class.php';
$charsetmap = array('gbk' => 'gbk', 'gb2312' => 'gbk', 'utf8' => 'utf8','utf-8' => 'utf8');
$db_charset = $charsetmap[strtolower(DB_CHARSET)];
$db = new mysql();
$db->connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PREFIX, $db_charset);
//unset(DB   _HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PREFIX, $db_charset, $charsetmap);

// Smarty 模板引擎类实例
require_once MOBILE_ROOT.'./libs/smarty-3.1.27/Smarty.class.php';
$smarty = new Smarty();
$smarty->left_delimiter = '<!--{';
$smarty->right_delimiter = '}-->';
$smarty->setTemplateDir(MOBILE_ROOT.'./templates/');
$smarty->setCompileDir(MOBILE_ROOT.'./data/compiled/');
$smarty->setCacheDir(MOBILE_ROOT.'./data/cache/');
$smarty->setConfigDir(MOBILE_ROOT.'./data/config/');

// 自动加载模型
if(function_exists('spl_autoload_register')) {
	spl_autoload_register('loadmodel');
} else {
	function __autoload($classname) {
		loadclass($classname);
	}
}

// 全局变量定义
$page_title = '主题名品网手机站';    // 页面标题
$timestamp = time();    // UNIX时间戳
$onlineip = getip();    // 用户IP

// 手机站COOKIE设置
$cookie_pre 	= 'xh_';    // COOKIE前缀
$cookie_path 	= '/';     // COOKIE作用域
$cookie_domain 	= '';    // COOKIE作用域名

// 判断用户是否登录

// if(!isset($_SESSION['loginauth'])) {
// 	echo "<script>location.href='index.php?action=admin&op=login';exit;</script>";
// }

?>