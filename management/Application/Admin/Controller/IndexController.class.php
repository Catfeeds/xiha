<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;

class IndexController extends BaseController {
    //构造函数
    public function _initialize() {    
        if(!session('loginauth')) {       //如果不存在用户登录session
            $this->redirect('Public/login');  //没有权限，重定向至登录页面
            exit();
        }
    }

    public function index() {
        $role_id = $this->getRoleId();
        $this->assign('role_id', $role_id);
        $this->assign('user_id', $this->getLoginUserId());
        $this->assign('login_name', $this->getLoginName());
        //echo $this->getLoginName();
        $menu_list = D('Manager')->getMenuListByRoleId($role_id);
        $this->assign('menu_list', $menu_list);
        $this->display('Index/index');
    }

    /**
     * 获取ajax天数数据
     *
     * @return void
     * @author 
     **/
    public function getAjaxDayData() {
        $month = I('post.m');     //Ajax传递的月份
        $year = I('post.y');      //Ajax传递的年份
        $res = $this->getMonthDayAction($year, $month);     //使用getMonthDayAction()方法获取月份的天数信息
        if($res) {
            $data = array('code'=>'200', 'msg'=>'获取成功', 'data'=>array('baseinfo'=>$res));   //如果获取成功则返回 200响应码  ，提示信息，和数据
        } else {
            $data = array('code'=>'-1', 'msg'=>'获取失败', 'data'=>array());          //如果不成功则返回 -1  ，提示信息，和空数据
        }
        echo json_encode($data);  //转换成json格式传递
    }

    public function getMonthDayAction($year='', $month) {

        $list = array();
        if($year == '') {
            $year = date('Y', time());  //如果$year是空的，则用date()获取现在的年份
        }
        $t0 = date('t', strtotime($year.'-'.$month.'-1'));      // 一个月一共有几天
        $t1 = mktime(0,0,0,$month,1,$year);        // 创建当月开始时间 
        $t2 = mktime(23,59,59,$month,$t0,$year);       // 创建当月结束时间
        $list['month'] = $month;
        $list['start_dateformat'] = $t1;
        $list['end_dateformat'] = $t2;
        $list['date'] = $year.'-'.$month.'-1';
        $list['monthday'] = $t0;

        return $list;
    }
}
