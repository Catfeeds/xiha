<?php
$serv = new swoole_websocket_server("0.0.0.0", 5511);

$serv->on('Start', function($server) {
    echo "swoole started\n";
});

$serv->on('Open', function($server, $req) {
    echo "connection open: ".$req->fd;
});

$serv->on('Message', function($server, $frame) {
    echo "message: ".$frame->data;
    $server->tick( 5000, function () use ($server, $frame) {
        echo date('Y-m-d H:i:s')."\n";
        $server->push($frame->fd, json_encode(["t" => date('Y-m-d H:i:s')]));
    });
});

$serv->on('Close', function($server, $fd) {
    echo "connection close: ".$fd."\n";
});

$serv->start();
