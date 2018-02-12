<?php

//报名控制器

namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\View;

class Test extends Controller
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
        $this->assign('title', '嘻哈学车');
	}
	
    // 首页
    public function index() {
        return $this->fetch('index/test');
    }
	
}

?>