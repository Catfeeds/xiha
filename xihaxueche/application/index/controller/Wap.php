<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
use app\index\model\Coach as CoachModel;

class Wap extends Controller
{   
    public function _initialize() {
        $this->assign('title', '嘻哈学车-官网');
    }
    // 首页
    public function index() {
        return $this->fetch('wap/index');
    }
	
	// 教练端
	public function coach() {
		$this->assign('title', '嘻哈学车|教练端');
        return $this->fetch('wap/products_coach');
	}
	
	// 学员端
	public function student() {
		$this->assign('title', '嘻哈学车|学员端');
        return $this->fetch('wap/products_students');
	}

    // 关于我们
    public function aboutus() {
        $this->assign('title', '嘻哈学车|关于我们');
        return $this->fetch('wap/aboutus');
    }

    // 加入我们
    public function join() {
        $this->assign('title', '嘻哈学车|加入我们');
        return $this->fetch('wap/join');
    }

    // 联系我们
    public function contact() {
        $this->assign('title', '嘻哈学车|联系我们');
        return $this->fetch('wap/contact');
    }
	
	//教练员招募
	public function	recruit(Request $request) {
		$token = $request->has('token', 'get') ? $request->get('token') : '';
		$type = $request->has('type', 'get') ? $request->get('type') : 'index';
        $this->assign('title', '嘻哈学车|教练员招募');
		if($type == 'index' || $token == '') {
			return $this->fetch('wap/recruit');
		} else {
			return $this->fetch('wap/recruit_info');
		}
			
	}

    // 电子教练
    public function ecoach() {
        $this->assign('title', '嘻哈学车|电子教练');
        return $this->fetch('wap/products_ecoach');
    }


}
