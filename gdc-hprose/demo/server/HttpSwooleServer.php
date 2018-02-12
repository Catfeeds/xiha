<?php
require_once '../../hprose/Hprose.php';

use Hprose\Swoole\Server;

function hello($name) {
    return "Hello $name!";
}

$server = new Server("http://0.0.0.0:8086");
$server->addFunction('hello');
$server->start();
