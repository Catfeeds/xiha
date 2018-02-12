<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
define('API_URL', 'http://api2.xihaxueche.com:8001/api2/public/');
define('SITE_URL', 'http://m.xihaxueche.com:8001/v2/student/public/');
// define('API_URL', 'http://36.33.24.119:50001/php/api2/dist/public/');
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
