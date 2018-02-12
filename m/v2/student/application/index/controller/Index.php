<?php

//首页控制器
	
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\View;
//use app\index\model\Coach as CoachModel;

class Index extends Controller
{
	protected $request;
	protected $params;
	protected $device;
	protected $token;
	
	public function _initialize() {
		$this->request = Request::Instance();
		$this->params = $this->request->param();
		$this->device = $this->request->has('device') ? $this->params['device'] : '1'; // 1:web 2:ios 3:andriod
		$this->token = $this->request->has('token', 'get') ? $this->request->get('token') : null;
		$this->assign('token', $this->token);
		$this->assign('device', $this->device);
		$this->assign('r', time());
        $this->assign('title', '嘻哈学车');
	}
	
    // 首页
    public function index() {
        return $this->fetch('index/index');
    }

    // 报名流程
    public function signupflow() {
        $this->assign('title', '报名须知');
        return $this->fetch('index/signupflow');
    }

    // 学车流程
    public function learncarflow() {
        $this->assign('title', '学车流程');
        return $this->fetch('index/learncarflow');
    }

    // 常见问题
    public function issue() {
        $this->assign('title', '常见问题');
        return $this->fetch('index/issue');
    }

	// navbar 导航
	public function navbar() {
        return $this->fetch('index/navbar');
	}
	
	// 分享sharebar
	public function sharebar() {
        return $this->fetch('index/sharebar');
	}
	
	// 选择城市
	public function citylist() {
        $this->assign('title', '城市列表');
		return $this->fetch('index/citylist');
	}
	
	// 嘻哈学车优惠券
	public function xhtickets() {
        $this->assign('title', '嘻哈券领取');
		return $this->fetch('index/xhtickets');
	}

}
