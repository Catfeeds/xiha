<?php
namespace Admin\Controller;
use Think\Controller;
class XihaController extends BaseController {
	//构造函数，判断是否登录
	public function _initialize() {
	if(!session('loginauth')) {
			$this->redirect('Public/login');
			exit();
	}
	}
	
    public function index(){
        $this->display();
    }
   
}