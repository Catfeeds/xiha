<?php
require_once "../../hprose/Hprose.php";

use Hprose\Socket\Server;

function hello($name) {
    return "hello $name!";
}

$server = new Server("tcp://0.0.0.0:1314");
$server->addFunction('hello');
$server->start();
