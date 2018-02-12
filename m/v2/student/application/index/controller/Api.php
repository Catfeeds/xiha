<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Db;
use think\View;

class Api extends Controller
{
	protected $request;
	protected $params;
    protected $key = '0f3c5b5bff12a5c8adbba5c17652eaf3';

	public function _initialize() {
		$this->request = Request::Instance();
		$this->params = $this->request->param();
		$this->device = $this->request->has('device') ? $this->params['device'] : '1'; // 1:web 2:ios 3:andriod
		$this->token = $this->request->has('token', 'get') ? $this->request->get('token') : null;
		$this->assign('token', $this->token);
		$this->assign('r', time());
		$this->assign('device', $this->device);
        $this->assign('title', '嘻哈学车');
	}

    // 首页
    public function index() {
        return $this->fetch('index/test');
    }

    public function couponcode() {
        $data = $this->params;
        $signature = self::sign($data, $this->key);
        $data['sign'] = $signature;
        $query_string = http_build_query($data);
        $result = file_get_contents('http://api2.xihaxueche.com:8001/api2/public/v1/ucenter/smscode/student/coupon'.'?'.$query_string);
        // $result = self::send('http://api2.xihaxueche.com:8001/api2/public/v1/ucenter/smscode/student/coupon');
        return json(json_decode($result, true));
    }

    public static function sign($data, $key)
    {
        unset($data['sign']);

        ksort($data);

        $query = urldecode(http_build_query($data));
        $query .= "&key={$key}";

        return strtoupper(md5($query));
    }

    /**
     * @param string $host - $host of socket server
     * @param string $message - 发送的消息
     * @param string $address - 地址
     * @return bool
     */
    protected static function send($url, $message = '')
    {
        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $headers = [
            "Content-Type: application/json;charset=UTF-8",
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); //设置header
        return curl_exec($ch);
    }

}

?>
