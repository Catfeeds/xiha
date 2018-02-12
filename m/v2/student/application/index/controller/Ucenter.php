<?php

//报名控制器

namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\View;

class Ucenter extends Controller
{
	protected $request;
	protected $params;
	protected $header;
	protected $device;
	protected $token;
	
	public function _initialize() {
		$this->request = Request::Instance();
		$this->params = $this->request->param();
		$this->header = $this->request->header();
		$this->device = $this->request->has('device') ? $this->params['device'] : '1'; // 1:web 2:ios 3:andriod
		$this->token = $this->request->has('token', 'get') ? $this->request->get('token') : null;
		$this->assign('device', $this->device);
		$this->assign('token', $this->token);
		$this->assign('r', time());
        $this->assign('title', '我的');
	}
	
    // 首页
    public function index() {
        return $this->fetch('ucenter/index');
    }
	// 个人信息
	public function info() {
        $this->assign('title', '个人资料');
		if(isset($this->token) && trim($this->token) != '') {
			return $this->fetch('ucenter/info');
		} else {
			$this->redirect('./ucenter/login', ['redirect_url' => urlencode(url('/ucenter/info')), 'device' => $this->device]);
		}
	}
	
	//学车券
	public function tickets() {
        $this->assign('title', '我的学车券');
		if(isset($this->token) && trim($this->token) != '') {
			return $this->fetch('ucenter/tickets');
		} else {
			$this->redirect('./ucenter/login', ['redirect_url' => urlencode(url('/ucenter/tickets')), 'device' => $this->device]);
		}
	}
	
	// 登录
	public function login() {
        $this->assign('title', '登录');
		$redirect_url = $this->request->has('redirect_url', 'get') ? $this->params['redirect_url'] : 'index';
		$this->assign('redirect_url', $redirect_url);
		if(isset($this->token) && trim($this->token) != '') {
			$this->redirect('./ucenter', ['token' => $this->token, 'device' => $this->device]);
		} else {
			return $this->fetch('ucenter/login');
		}
	}
	// 注册
	public function register() {
        $this->assign('title', '注册');
		if(isset($this->token) && trim($this->token) != '') {
			$this->redirect('./ucenter', ['token' => $this->token, 'device' => $this->device]);
		} else {
			return $this->fetch('ucenter/register');
		}
	}
	
	//设置中心
	public function settings() {
	    $this->assign('title', '设置中心');
		return $this->fetch('ucenter/settings');
	}
	
	//修改密码
	public function changepass() {
	    $this->assign('title', '修改密码');
		if(isset($this->token) && trim($this->token) != '') {
			return $this->fetch('ucenter/changepass');
		} else {
			$this->redirect('./ucenter/login', ['redirect_url' => urlencode(url('/ucenter/changepass')), 'device' => $this->device]);
		}
	}
	
	//我的消息
	public function mymessage() {
	 	$this->assign('title', '我的消息');
		if(isset($this->token) && trim($this->token) != '') {
			return $this->fetch('ucenter/mymessage');
		} else {
			$this->redirect('./ucenter/login', ['redirect_url' => urlencode(url('/ucenter/mymessage')), 'device' => $this->device]);
		}
	}
	
	//我的钱包
	public function mywallet() {
	 	$this->assign('title', '我的钱包');
		if(isset($this->token) && trim($this->token) != '') {
			return $this->fetch('ucenter/mywallet');
		} else {
			$this->redirect('./ucenter/login', ['redirect_url' => urlencode(url('/ucenter/mywallet')), 'device' => $this->device]);
		}
	}
	
	//电子教练
	public function eleccoach() {
	 	$this->assign('title', '电子教练');
		if(isset($this->token) && trim($this->token) != '') {
			return $this->fetch('ucenter/eleccoach');
		} else {
			$this->redirect('./ucenter/login', ['redirect_url' => urlencode(url('/ucenter/eleccoach')), 'device' => $this->device]);
		}
	}
	
	//忘记密码
	public function forgetpass() {
		$this->assign('title', '忘记密码');
		if(isset($this->token) && trim($this->token) != '') {
			$this->redirect('./ucenter', ['redirect_url' => $this->token, 'device' => $this->device]);
		} else {
			return $this->fetch('ucenter/forgetpass');
		}
	}
	
	//用户协议
	public function protocol() {
		$this->assign('title', '用户协议');
		return $this->fetch('ucenter/protocol');
	}
	
	//我的题库
	public function myexams() {
		$this->assign('title', '我的题库');
		if(isset($this->token) && trim($this->token) != '') {
			return $this->fetch('ucenter/myexams');
		} else {
			$this->redirect('./ucenter/login', ['redirect_url' => urlencode(url('/ucenter/myexams')), 'device' => $this->device]);
		}
	}
	
	//我的教练
	public function mycoach() {
		$this->assign('title', '我的题库');
		if(isset($this->token) && trim($this->token) != '') {
			return $this->fetch('ucenter/myexams');
		} else {
			$this->redirect('./ucenter/login', ['redirect_url' => urlencode(url('/ucenter/mycoach')), 'device' => $this->device]);
		}
	}
}

?>