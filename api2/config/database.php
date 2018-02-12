<?php

return [
    // default connection
    'default' => 'mysql',
    'fetch'   => PDO::FETCH_OBJ, // 查询结果输出为对象 stdClass

    'connections' => [
        'mysql' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST'),
            'database'  => env('DB_DATABASE'),
            'username'  => env('DB_USERNAME'),
            'password'  => env('DB_PASSWORD'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => env('DB_PREFIX'),
        ],
    ]
];
