<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],
    ':action' 	=> 'index/Index/:action',
    'm/index' 	=> 'index/Wap/index',
    'm/aboutus' => 'index/Wap/aboutus',
    'm/join' 	=> 'index/Wap/join',
    'm/contact' => 'index/Wap/contact',
    'm/coach'	=> 'index/Wap/coach',
    'm/student' => 'index/Wap/student',
    'm/ecoach'	=> 'index/Wap/ecoach',
    'm/recruit'	=> 'index/Wap/recruit',
    
];
