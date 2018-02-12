<?php

function classLoader($class)
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
	if($path == 'AlipayTradePagePayRequest' || $path == 'AlipayTradeQueryRequest') {
		$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'aop' . DIRECTORY_SEPARATOR . 'request' . DIRECTORY_SEPARATOR . $path . '.php';
	}else if($path == 'AopClient') {
		$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'aop' . DIRECTORY_SEPARATOR . $path . '.php';
	}else {
		$file = $path;
	}
    if (file_exists($file)) {
        require_once $file;
    }
}
spl_autoload_register('classLoader');