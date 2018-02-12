<?php

/**
 * Helper function
 */

/**
 * $api_url = 'http://api2.xihaxueche.com:8001/api2/public/v1/order/appoint/submit';
 *
 *
 *
 */
function sendHttpRequest($url, $params)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    //curl_setopt($ch, CURLOPT_SSLVERSION, 3); /* original */
    curl_setopt($ch, CURLOPT_SSLVERSION, 1); /* modified for local test, no need for server */
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-type:application/x-www-form-urlencoded;charset=UTF-8'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}


$server = new swoole_server("0.0.0.0", 6233, SWOOLE_BASE, SWOOLE_SOCK_TCP);

$server->set([
    'worker_num'            => 3,
    'task_worker_num'       => 3,
    'daemonize'             => false,
    'package_length_type'   => 'N',
    'log_file'              => 'swoole.6233.log',
]);

$server->on('Start', function($server) {
    echo '---------------------- split line ----------------------'."\n";
    echo date('Y-m-d H:i:s')." server: master_pid#{$server->master_pid} has started\n";
});

$server->on('Shutdown', function($server) {
    echo date('Y-m-d H:i:s')." server: master_pid#{$server->master_pid} has been shutdown\n";
});

$server->on('WorkerStart', function($server, $worker_id) {
    if ( $worker_id >= $server->setting['worker_num'] ) {
        // 任务进程
        echo date('Y-m-d H:i:s')." server: task_worker_pid#{$server->worker_pid} worker_id#{$worker_id} started\n";
    } else {
        // 普通进程
        if ( $worker_id == 0 ) {
            // #0 worker 进程作为监控
            echo date('Y-m-d H:i:s')." server: [observer]worker_pid#{$server->worker_pid} worker_id#{$worker_id} started\n";

            $server->tick( 1000, function() use ( $server, $worker_id ) {
                $redis = new \Redis();
                $redis->connect('127.0.0.1', 6379);
                $redis->auth('dalinux');
                $list_length = $redis->llen('coach_list');
                if ($list_length > 0) {
                    echo date('Y-m-d H:i:s').' 当前队列长度：'.$list_length."\n";
                }
            });
        } else {
            // 其它 worker 进程作为工作进程
            echo date('Y-m-d H:i:s')." server: [consumer]worker_pid#{$server->worker_pid} worker_id#{$worker_id} started\n";

            $server->tick( 100, function() use ( $server, $worker_id) {
                $redis = new \Redis();
                $redis->connect('127.0.0.1', 6379);
                $redis->auth('dalinux');
                $list_length = $redis->llen('coach_list');
                while ( $list_length > 0 ) {
                    $pdata = $redis->lpop('coach_list');
                    if ( $pdata ) {
                        $server->task($pdata);
                    }

                    $list_length = $redis->llen('coach_list');
                }
            });
        }
    }
});

$server->on('WorkerError', function($server, $worker_id) {
    echo date('Y-m-d H:i:s')." server: worker_pid#{$server->worker_pid} worker_id#{$worker_id} error happeded\n";
});

$server->on('WorkerStop', function($server, $worker_id) {
    if ( $worker_id >= $server->setting['worker_num'] ) {
        echo date('Y-m-d H:i:s')." server: task_worker_pid#{$server->worker_pid} worker_id#{$worker_id} stopped\n";
    } else {
        echo date('Y-m-d H:i:s')." server: worker_pid#{$server->worker_pid} worker_id#{$worker_id} stopped\n";
    }
});

$server->on('Task', function($server, $task_id, $src_worker_id, $data) {
    //echo date('Y-m-d H:i:s')." 投递任务成功 data#{$data} \n";

    $params = [
        'date' => '2017-1-19',
        'coach_id' => $data,
        'coach_phone' => '17355100855',
        'coach_name' => '高教练'.$data,
        'phone' => '17355100855',
        'identity_id' => '342222199305112039',
        'real_name' => '高大成',
        'money' => 130.00,
        'time_configs' => '[{"id":2,"is_coach_set":1}]',
        'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ4aWhheHVlY2hlX3Byb2R1Y3Rpb24iLCJhdWQiOiJ4aWhheHVlY2hlX3Byb2R1Y3Rpb24iLCJqdGkiOiJiYzA3MGE2OGM2YSIsImlhdCI6MTQ4NDIwMTg5NCwibmJmIjoxNDg0MjAxODk0LCJleHAiOjE0ODQ4MDY2OTQsInVzZXIiOiJleUpwZGlJNkltRkdNbG81ZDNaaWRGTk5SVEJaWkdsMllrdG9kM2M5UFNJc0luWmhiSFZsSWpvaVQyaHBUVkYzYkRjemVGcFBNVTlKUW1Gb1JGVjJSRVZWUm0xWldWQkpZWEJUUjJ4MGJHcFBWM1ZKVUZOQ2JXcFdOM1pVTnpOSk0wTmhURlpDV0doeWFtMTZPR3RyT1hScU1WaHlOSGRQV0dKMlpGTkVUMmxTTnpaNmFESXlVVUZpYVhseFJucG9YQzh6ZFVWWk0zRktkRGc0YVc4eFNEVm9ORFZxVW5ocGQzWmNMeUlzSW0xaFl5STZJbVExWkRka01qUTVaR00yT1RNd1ptRXpNRFUxTkRFeE1qVXpZV0ptWldKbE1tWXpZVFJpT0RaalpqSmpZbU5rWXpKaE1qTTVabVF5TnpkaE5EbGpNbVVpZlE9PSJ9.LIFlWwZlgceP3nf4ssMkDnBqQOVG1RBD-7EVtR6e_rQ',
    ];
    $api_url = 'http://api2.xihaxueche.com:8001/api2/public/v1/order/appoint/submit';
    $response = sendHttpRequest($api_url, $params);
    $server->finish( $data );
});

$server->on('Finish', function($server, $task_id, $data) {
    echo date('Y-m-d H:i:s').' '.explode(' ', microtime())[0]." 任务结束 data#{$data} \n";
});

$server->on('Connect', function($server, $fd, $from_id) {
    echo date('Y-m-d H:i:s')." 新的客户端连接，来自 fd#{$fd} from_id#{$from_id} \n";
});

$server->on('Receive', function($server, $fd, $from_id, $data) {
});


$server->on('Close', function($server, $fd, $from_id) {
    echo date('Y-m-d H:i:s')." 客户端断开连接，来自 fd#{$fd} from_id#{$from_id} \n";
});


function initCoachList() {
    $redis = new \Redis();
    $redis->connect('127.0.0.1', 6379);
    $redis->auth('dalinux');

    if ($redis->exists('coach_list')) {
        $redis->del('coach_list');
    }

    $coach_id = 11235;
    $coach_id = 1236 + 3;
    while ($coach_id >= 1236) {
        $redis->rpush('coach_list', 992);
        $coach_id--;
    }
}

// 在redis中建立一个coach_list队列存放10000个教练的信息
initCoachList();
$server->start();
