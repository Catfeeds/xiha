<?php

//报名控制器

namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\View;

class Drive extends Controller
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
		$this->assign('r', time());
		$this->assign('device', $this->device);
        $this->assign('title', '驾考');
	}
	
    // 首页科目一，二，三，四，毕业
    public function index() {
    	$lesson_id = $this->request->has('l') ? $this->params['l'] : 1;
		$this->assign('lesson_id', $lesson_id);
		switch($lesson_id) {
			case "1":
		        return $this->fetch('drive/index');
				break;
			case "2":
				return $this->fetch('drive/lesson_two');
				break;
			case "3":
				return $this->fetch('drive/lesson_three');
				break;
			case "4":
				return $this->fetch('drive/lesson_four');
				break;
			case "5":
				return $this->fetch('drive/graduation');
				break;
			default:
				return $this->fetch('drive/index');
				break;
		}
    }
	
	public function lesson2skill() {
        $this->assign('title', '学车技巧');
		return $this->fetch('drive/lesson2skill');
	}
	
}

?>