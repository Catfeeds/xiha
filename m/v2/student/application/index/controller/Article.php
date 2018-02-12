<?php

//嘻哈文章管理控制器

namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\View;

class Article extends Controller
{
	protected $request;
	protected $params;
	protected $device;
	protected $lesson_id;
	protected $token;
	
	public function _initialize() {
		$this->request = Request::Instance();
		$this->params = $this->request->param();
		$this->device = $this->request->has('device') ? $this->params['device'] : '1'; // 1:web 2:ios 3:andriod
    	$this->lesson_id = $this->request->has('l') ? $this->params['l'] : 1;
		$this->token = $this->request->has('token', 'get') ? $this->request->get('token') : null;
		$this->assign('token', $this->token);
		$this->assign('r', time());
		$this->assign('lesson_id', $this->lesson_id);
		$this->assign('device', $this->device);
        $this->assign('title', '驾考圈');
	}
	
    // 首页
    public function index() {
    	$cate_id = $this->request->has('cid') ? $this->params['cid'] : 1;
    	$page = $this->request->has('page') ? $this->params['page'] : 1; // 分页
		$type = $this->request->has('type', 'get') ? $this->params['type'] : 1; // 区别是话题还是文章等 1：话题 2：文章
		$this->assign('cate_id', $cate_id);
		$this->assign('type', $type);
		$this->assign('page', $page);
		
		if($type == 1) {
			switch ($this->lesson_id) {
				case '1':
					$lesson_name = '科目一';
					break;
				case '2':
					$lesson_name = '科目二';
					break;
				case '3':
					$lesson_name = '科目三';
					break;
				case '4':
					$lesson_name = '科目四';
					break;
				case '5':
					$lesson_name = '晒驾照';
					break;
				default:
					$lesson_name = '科目一';
					break;
			}
			$this->assign('lesson_name', $lesson_name);
	    	return $this->fetch('article/questions/index');
		} else {
        	$this->assign('title', '《嘻哈号外》第一手驾培行业综合资讯，尽在嘻哈号外！');
	    	return $this->fetch('article/article/index');
						
		}
    }
	
//	//文章列表
//	public function list() {
//  	$cate_id = $this->request->has('cid') ? $this->params['cid'] : 1;
//  	$page = $this->request->has('page') ? $this->params['page'] : 1; // 分页
//		$type = $this->request->has('type', 'get') ? $this->params['type'] : 1; // 区别是话题还是文章等 1：话题 2：文章
//		$this->assign('cate_id', $cate_id);
//		$this->assign('type', $type);
//		$this->assign('page', $page);
//		return $this->fetch('article/article/article_list');
//	}
	
	//帖子详情
	public function detail() {
		$id = $this->request->has('id', 'get') ? $this->params['id'] : 0;
		$cate_id = $this->request->has('cid', 'get') ? $this->params['cid'] : 0;
		$type = $this->request->has('type', 'get') ? $this->params['type'] : 1; // 区别是话题还是文章等
		$this->assign('id', $id);
		$this->assign('cate_id', $cate_id);
		$this->assign('type', $type);
		if($type == 1) {
	        $this->assign('title', '驾考话题');
			return $this->fetch('article/questions/detail');
		} else {
			$this->assign('title', '文章详情');
			return $this->fetch('article/article/detail');
		}	
	}
	
}

?>