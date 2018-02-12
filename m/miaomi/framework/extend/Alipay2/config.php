<?php

/**

 * Copy Right IJH.CC

 * Each engineer has a duty to keep the code elegant

 * $Id: config.php 9343 2015-03-24 07:07:00Z youyi $

 */

return array(

	'code'=>'alipay',

	'name'=>'支付宝',

	'content'=>'支付宝网站(www.alipay.com) 是国内先进的网上支付平台。<a href="http://act.life.alipay.com/systembiz/shopnc" target="_blank" style="color:red; font-weight:bold;">立即在线申请</a>（<a href="http://club.alipay.com/read-htm-tid-10077293.html" style="color:red; font-weight:bold;" target="_blank">如何启用支付宝收款</a>）',

	'website'   => 'http://www.alipay.com',

	'version'   => '1.0',

	'currency'  => '人民币',

	'config'    => array(

        'service'  => array(

            'text'      => '接口类型',

            'desc'  => '1.5%费率用户请选“担保交易接口”',

            'type'      => 'select',

            'items'     => array(

                'trade_create_by_buyer'   => '标准双接口',

                'create_partner_trade_by_buyer'   => '担保交易接口',

                'create_direct_pay_by_user'   => '即时到帐交易接口',

            ),

        ),

        'seller_id'   => array(

            'text'  => '支付宝账户',

            'desc'  => '您必须拥有支付宝账户，能才通过支付宝收款，<a href="https://memberprod.alipay.com/account/reg/index.htm" style="color:red; font-weight:bold;" target="_blank">点此注册</a>我还未签约支付宝，<a href="https://b.alipay.com/order/serviceIndex.htm" style="color:red; font-weight:bold;" target="_blank">点此获取PID、Key</a>',

            'type'  => 'text',

        ),

        'partner'   => array(

            'text'  => '合作者身份(PID)',

       		'desc'	=>'<a href="https://b.alipay.com/order/serviceIndex.htm" id="pidKey" style="color:red; font-weight:bold;" target="_blank">我已签约支付宝，获取PID、Key</a>',

            'type'  => 'text',

        ),

        'key' => array(

            'text'  => '安全校验码(Key)',

            'desc'  => '',

            'type'  => 'password',

        ),

        // 'alipay_appid' => array(

        //     'text'  => '开放平台(APPID)',

        //     'desc'  => '',

        //     'type'  => 'text',

        // ),

        'alipay_rsa_private'   => array(

            'text'  => '合作者RSA私钥',

            'desc'  =>'合作者RSA私钥',

            'type'  => 'text',

        ),

        'alipay_rsa_public'   => array(

            'text'  => '支付宝RSA公钥',

            'desc'  =>'支付宝RSA公钥',

            'type'  => 'text',

        ),



    ),

);