<?php
/**
 * 网店配置模板
 *
 * 版本 $Id: config.sample.php 80547 2013-08-15 16:48:00Z dong $
 * 配置参数讨论专贴 http://www.shopex.cn/bbs/thread-61957-1-1.html
 */

// ** 数据库配置 ** //
define('DB_USER', 'root');  # 数据库用户名
define('DB_PASSWORD', ''); # 数据库密码
define('DB_NAME', 'xihaxueche');    # 数据库名

# 数据库服务器 -- 99% 的情况下您不需要修改此参数
define('DB_HOST', 'localhost');
//define('DB_PCONNECT',1); #是否启用数据库持续连接？
//define('SHOP_DEVELOPER', true);
define('STORE_KEY', '316875f5024459400c6bfd5949f0527a'); #密钥
define('DB_PREFIX', 'cs_');
define('LANG', '');

#启用触发器日志: home/logs/trigger.php
//define ('TRIGGER_LOG',true);
//define ('DISABLE_TRIGGER',true); #禁用触发器

define('STAGE','release');
define('ERROR_	REPORTING',E_ALL ^ E_NOTICE ^ E_WARNING);
define('SHOPADMIN_PATH', 'shopadmin');
define('GD_VCODE',false); #使用GD验证码
/* 以下为调优参数 */
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');
define('DEBUG_JS',false);
define('DEBUG_CSS',false);
define('BASE_DIR', realpath(dirname(__FILE__).'/../'));
define('CORE_DIR', BASE_DIR.'/core');
define('CORE_INCLUDE_DIR', CORE_DIR.'/include_v5');

define('HTTP_HOST', 'http://localhost/php/sadmin/');
// define('HTTP_HOST', 'http://180.153.52.71:8001/service/sadmin/');
// define('HTTP_HOST', 'http://60.173.247.68:8081/sadmin/');

// 教练端推送配置
define('COACH_APPKEYS', '3ebbbf7c2e811171a6e5c836');
define('COACH_SECRET', 'b4272f005b740f30d49a6758');

// 学员端推送配置
define('STUDENTS_APPKEYS', 'c1b9d554f52b5668cba58c75');
define('STUDENTS_SECRET', '68ce861810390d1e88112310');

//安全模式启用后将禁用插件 no:modifiers
//define('SAFE_MODE',false);

#您可以更改这个目录的位置来获得更高的安全性
define('HOME_DIR', BASE_DIR.'/home'); 
define('PLUGIN_DIR', BASE_DIR.'/plugins');
define('THEME_DIR', BASE_DIR.'/themes');
define('MEDIA_DIR', BASE_DIR.'/images');
define('PUBLIC_DIR', BASE_DIR.'/public');  #同一主机共享文件
define('CERT_DIR', BASE_DIR.'/cert');
define('DEFAULT_LOCAL','mainland');
define('SECACHE_SIZE','150M'); #缓存大小,最大不能超过1G
//define('TEMPLATE_MODE','database');
define("MAIL_LOG",false);
define('DEFAULT_INDEX','');
define('SERVER_TIMEZONE','8'); #服务器时区
//define('APP_ROOT_PHP','index.php'); #iis 5

@ini_set('memory_limit','356M');
define('WITHOUT_GZIP',false);

define('WITHOUT_CACHE',true);

// 设置时区
date_default_timezone_set('Asia/Chongqing');

#前台禁ip
//define('BLACKLIST','10.0.0.0/24 192.168.0.1/24');

#数据库集群.
//define('DB_SLAVE_NAME',DB_NAME);
//define('DB_SLAVE_USER',DB_USER);
//define('DB_SLAVE_PASSWORD',DB_PASSWORD);
//define('DB_SLAVE_HOST',DB_HOST);

#支持泛解的时候才可以用这个, 仅支持fs_storager
/*
 * define('HOST_MIRRORS',
 * 'http://img0.example.com,
 * http://img2.example.com,
 * http://img2.example.com');
 */

#使用ftp存放图片文件
//define('WITH_STORAGER','ftp_storager');

#确定服务器支持htaccess文件时，可以打开下面两个参数获得加速。
//define ('GZIP_CSS',true);
//define ('GZIP_JS',true);

#可以选择缓存方式apc 或者 memcached
define('CACHE_METHOD','secache');
//define('CACHE_METHOD','apc');
//======================================
//define('CACHE_METHOD','memcached');
//======================================
#使用单个文件存放，稳定，但无法控制文件大小
//define('CACHE_METHOD','dir'); 


/* 日志 */
//define('LOG_LEVEL',E_ERROR);

#按日期分目录，每个ip一个日志文件。扩展名是php防止下载。
//define('LOG_FILE',HOME_DIR.'/logs/{date}/{ip}.php');

#log文件头部放上exit()保证无法下载。
//define('LOG_HEAD_TEXT','<'.'?php exit()?'.'>');  
//define('LOG_FORMAT',"{gmt}\t{request}\t{code}");

//======================================
define('WITH_MEMCACHE',false);
define('MEMCACHED_SERVER','127.0.0.1:11211');
//======================================

#禁止运行安装
//define('DISABLE_SYS_CALL',1);

#使用数据库存放改动过的模板
//define('THEME_STORAGE','db');


#使用变动商品图片名
//define('IMAGE_CHANGE',true);

#加载二次开发目录
define('CUSTOM_CORE_DIR', BASE_DIR.'/pfadmin_core');

#手机站加密密钥
define('AUTH_KEY', '8p23dVfspabfSdJ9Q13z34cf0J1PbTfm3X2z3u3Ajfp1j025raOck0E1ibgfX3l2');

// 配置驾照类型 
$lisence_config = array(
	'1' => 'C1',
	'2' => 'C2',
	'3' => 'C5',
	'4' => 'A1',
	'5' => 'A2',
	'6' => 'B1',
	'7' => 'B2',
	'8' => 'D',
	'9' => 'E',
	'10' => 'F'
);

// 配置科目类型
$lesson_config = array(
	// '1' => '科目一',
	'2' => '科目二',
	'3' => '科目三',
	// '4' => '科目四'
);

// 配置管理员权限
$manage_config = array(
	'1' => array(
			'bigcate_name' => '开始',
			'controll' => 'school',
			'function' => 'index',
			'style' => 'icon-home',
			'content' => '&#xe6b8;',	
			'seccate_name' => array(
				'1' => array(
					'cate_name' => '教练管理',
					'controll' => 'coach',
					'function' => 'index',
					'style' => 'icon-child',
					'content' => '&#xe736;'		

				),
				'2' => array(
					'cate_name' => '车辆管理',
					'controll' => 'car',
					'function' => 'index',
					'style' => 'icon-male',
					'content' => '&#xe65d;'		
				),
				'3' => array(
					'cate_name' => '学员管理',
					'controll' => 'member',
					'function' => 'index',
					'style' => 'icon-car',
					'content' => '&#xe6b7;'

				),
				'4' => array(
					'cate_name' => '驾校管理',
					'controll' => 'school',
					'function' => 'edit',
					'style' => 'icon-car',
					'content' => '&#xe6bb;'
				),
				'5' => array(
					'cate_name' => '评价管理',
					'controll' => 'comment',
					'function' => 'index',
					'style' => 'icon-car',
					'content' => '&#xe752;'
				),
				'6' => array(
					'cate_name' => '系统设置',
					'controll' => 'sysconfig',
					'function' => 'school',
					'style' => 'icon-car',
					'content' => '&#xe68a;'
				)
			)
		),
	'2' => array(
			'bigcate_name' => '教练',
			'controll' => 'coach',
			'function' => 'index',
			'style' => 'icon-child',
			'content' => '&#xe736;',		
			'seccate_name' => array(
				'1' => array(
					'cate_name' => '教练列表',
					'controll' => 'coach',
					'function' => 'index',
					'style' => 'icon-child',
					'content' => '&#xe736;'

				),
				// '2' => array(
				// 	'cate_name' => '时间配置',
				// 	'controll' => 'coach',
				// 	'function' => 'timeconfig',
				// 	'style' => 'icon-plus-square-o',
				// 	'content' => '&#xe6ca;'

				// ),
			)
		),
	'3' => array(
			'bigcate_name' => '车辆',
			'controll' => 'car',
			'function' => 'index',
			'style' => 'icon-car',
			'content' => '&#xe65d;',
			'seccate_name' => array(
				'1' => array(
					'cate_name' => '车辆列表',
					'controll' => 'car',
					'function' => 'index',
					'style' => 'icon-car',
					'content' => '&#xe65d;'

				),
				'2' => array(
					'cate_name' => '记录列表',
					'controll' => 'car',
					'function' => 'record',
					'style' => 'icon-plus-square-o',
					'content' => '&#xe6fb;'
				),
				'3' => array(
					'cate_name' => '车辆添加',
					'controll' => 'car',
					'function' => 'add',
					'style' => 'icon-plus-square-o',
					'content' => '&#xe767;'
				),
				'4' => array(
					'cate_name' => '记录添加',
					'controll' => 'car',
					'function' => 'recordadd',
					'style' => 'icon-plus-square-o',
					'content' => '&#xe752;'
				)
				
			)
		),
	'4' => array(
			'bigcate_name' => '学员',
			'controll' => 'member',
			'function' => 'index',
			'style' => 'icon-user',
			'content' => '&#xe6b7;',		
			'seccate_name' => array(
				'1' => array(
					'cate_name' => '报名订单管理',
					'controll' => 'signup',
					'function' => 'index',
					'style' => 'icon-dropbox',
					'content' => '&#xe66c;'

				),
				'2' => array(
					'cate_name' => '学车订单管理',
					'controll' => 'learncar',
					'function' => 'index',
					'style' => 'icon-foursquare',
					'content' => '&#xe6fb;'
				),
				'3' => array(
					'cate_name' => '学员档案管理',
					'controll' => 'member',
					'function' => 'index',
					'style' => 'icon-file-text-o',
					'content' => '&#xe6bd;'
				),
				'4' => array(
					'cate_name' => '档案回收站',
					'controll' => 'member',
					'style' => 'icon-file-text-o',
					'content' => '&#xe73b;',
					'function' => 'collection'
				),
				'5' => array(
					'cate_name' => '在线模拟',
					'controll' => 'member',
					'function' => 'examrecord',
					'style' => 'icon-child',
					'content' => '&#xe752;'
				),


			)
		),
	'5' => array(
			'bigcate_name' => '驾校',
			'controll' => 'school',
			'function' => 'index',
			'style' => 'icon-child',
			'content' => '&#xe6bb;',			
			'seccate_name' => array(
				'1' => array(
					'cate_name' => '编辑驾校',
					'controll' => 'school',
					'function' => 'edit',
					'style' => 'icon-child',
					'content' => '&#xe68a;'			
				),
				'2' => array(
					'cate_name' => '轮播图管理',
					'controll' => 'school',
					'function' => 'banner',
					'style' => 'icon-child',
					'content' => '&#xe734;'			

				),
				'3' => array(
					'cate_name' => '班制管理',
					'controll' => 'school',
					'function' => 'shifts',
					'style' => 'icon-child',
					'content' => '&#xe6cc;'			

				),
				'4' => array(
					'cate_name' => '报名点管理',
					'controll' => 'school',
					'function' => 'address',
					'style' => 'icon-child',
					'content' => '&#xe651;'
				),
				// '5' => array(
				// 	'cate_name' => '时间配置',
				// 	'controll' => 'school',
				// 	'function' => 'showtime',
				// 	'style' => 'icon-child',
				// 	'content' => '&#xe651;'
				// ),

			)
		),
	 '6' => array(
			'bigcate_name' => '评价',
			'controll' => 'comment',
			'function' => 'index',
			'style' => 'icon-child',
			'content' => '&#xe752;',		
			'seccate_name' => array(
				'1' => array(
					'cate_name' => '学员评价教练',
					'controll' => 'comment',
					'function' => 'index',
					'style' => 'icon-child',
					'content' => '&#xe6b7;',

				),
				'2' => array(
					'cate_name' => '学员评价驾校',
					'controll' => 'comment',
					'function' => 'scomment',
					'style' => 'icon-child',
					'content' => '&#xe6b8;',

				),
				'3' => array(
					'cate_name' => '教练评价学员',
					'controll' => 'comment',
					'function' => 'stucomment',
					'style' => 'icon-plus-square-o',
					'content' => '&#xe6bd;'
				),
			)
		),
	  '7' => array(
	 		'bigcate_name' => '消息',
	 		'controll' => 'message',
	 		'function' => 'index',
	 		'style' => 'icon-child',
	 		'content' => '&#xe752;',		
	 		'seccate_name' => array(
	 			'1' => array(
	 				'cate_name' => '学员消息',
	 				'controll' => 'message',
	 				'function' => 'index',
	 				'style' => 'icon-child',
	 				'content' => '&#xe6b7;',

	 			),
	 			'2' => array(
	 				'cate_name' => '教练消息',
	 				'controll' => 'message',
	 				'function' => 'scomment',
	 				'style' => 'icon-child',
	 				'content' => '&#xe6b8;',

	 			),
	 		)
	 	)
);
