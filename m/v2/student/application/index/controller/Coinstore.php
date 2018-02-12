<?php

//金币商城控制器

namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\View;

class Coinstore extends Controller
{
	protected $request;
	protected $params;
	protected $device;
	
	public function _initialize() {
		$this->request = Request::Instance();
		$this->params = $this->request->param();
		$this->device = $this->request->has('device') ? $this->params['device'] : '1'; // 1:web 2:ios 3:andriod
		$this->assign('r', time());
		$this->assign('device', $this->device);
        $this->assign('title', '金币商城');
	}
	
    // 首页
    public function index() {
        return $this->fetch('coinstore/index');
    }
	
	//赚金币
	public function earncoin() {
        $this->assign('title', '赚金币');
		$token = $this->request->has('token', 'cookie') ? $this->request->cookie('token') : null;
		if(trim($token) != null) {
			$this->assign('token', $token);
			return $this->fetch('coinstore/earncoin');
		} else {
			$this->redirect('./ucenter/login', ['redirect_url' => 'coinstore', 'device' => $this->device]);
		}
	}
	
	//兑换记录
	public function earnrecords() {
		$this->assign('title', '兑换记录');
		return $this->fetch('coinstore/earnrecords');
	}
	
	//金币规则
	public function rule() {
		$this->assign('title', '金币规则');
		return $this->fetch('coinstore/rule');
	}
	
	//商品详情
	public function goodsdetail() {
		$this->assign('title', '商品详情');
		$goods_id = $this->request->has('id', 'get') ? trim($this->params['id']) : 0;
		$this->assign('goods_id', $goods_id);
		return $this->fetch('coinstore/goodsdetail');
	}
	
	
}

?>