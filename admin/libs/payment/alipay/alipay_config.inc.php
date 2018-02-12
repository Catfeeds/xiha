<?php

/**
 * 支付宝手机网站支付配置文件
 */

!defined('IN_FILE') && exit('Access Denied');

$alipay_config['partner'] = '2088311075866685';
$alipay_config['key'] = 'xtk28flho6y2c3n2f39mci2nv0ab09f9';
$alipay_config['sign_type'] = 'MD5';
$alipay_config['input_charset'] = 'utf-8';
$alipay_config['cacert'] = MOBILE_ROOT.'./libs/payment/alipay/alipay_cacert.pem';
$alipay_config['transport'] = 'http';

?>