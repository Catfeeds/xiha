<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Log;
use Think\Page;
/**
 * 订单管理类--
 * 成员方法：
 *	1）index(报名驾校订单展示)
 *	2）appointLeaner(预约学车订单展示)
 *
 *
 **/
class OrderController extends BaseController {
    //构造函数，判断是否是登录状态
    public function _initialize() {
        parent::_initialize();
        if (!session('loginauth')) {
            $this->redirect('Public/login');
            exit();
        }

    }

// 1.报名驾校订单
    /**
     * 展示报名驾校订单
     *
     * @return 	void
     * @author 	sun/wl
     * @date	july 20, 2016
     * @update	july 21, 2016
     **/
    public function index() {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $school_id = $this->getLoginauth();
        $order_lists = D('Orders')->schoolOrdersList($school_id, ! in_array($role_id, [1, 8, 9]));
        $this->assign('school_id',$school_id);
        $this->assign('role_id',$role_id);
        $this->assign('count',$order_lists['count']);
        $this->assign('page',$order_lists['page']);
        $this->assign('school_orders_list',$order_lists['order_lists']);
        $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
    }


    /**
     * 搜索驾校订单
     *
     * @return void
     * @author wl
     * @date  july 21, 2016
     **/
    public function searchSchoolOrders () {
        $role_id = $this->getRoleId();
        $school_id = $this->getLoginauth();
        $param = I('param.');
        $search_type = trim((string)$param['search_type']);
        $s_keyword = trim((string)$param['s_keyword']);
        $pay_type = trim((int)$param['pay_type']);
        $pagecount = trim((int)$param['pagecount']);
        $elment = trim((string)$param['elment']);
        // $order_status = trim((int)$param['order_status']);
        if ($pagecount <= 0 ) {
            $pagecount = 10;
        } elseif ($pagecount > 200) {
            $pagecount = 200;
        }
        if ($elment == '搜索' || $elment == '设置') {
            if ( $s_keyword == '' && $search_type == '' && $pay_type  == '') {
                $this->redirect('Order/index');
            } else {
                $order_lists = D('Orders')->searchSchoolOrders($param, $school_id, $pagecount, ! in_array($role_id, [1, 8, 9]));
                $this->assign('school_id',$school_id);
                $this->assign('role_id',$role_id);
                $this->assign('pay_type', $pay_type);
                $this->assign('pagecount', $pagecount);
                $this->assign('order_status', $order_status);
                $this->assign('s_keyword', $s_keyword);
                $this->assign('search_type', $search_type);
                $this->assign('page', $order_lists['page']);
                $this->assign('count', $order_lists['count']);
                $this->assign('school_orders_list', $order_lists['order_lists']);
                $this->display('Order/index');
            }
        } elseif ($elment == 'Excel下载') {
            if ($param['begin_num'] < 0 || $param['end_num'] <= 0) {
                $param['begin_num'] = 1;
                $param['end_num'] = 2;
            }
            $num = intval(intval($param['end_num']) - intval($param['begin_num']) + 1);
            if ($num < 0 || $num > 9000) {
                $this->error('数据范围为1~9000');exit;
            }
            $orders_list = D('Export')->getDownloadSchoolOrders($param, $school_id);
            $title = '报名班制';
            $result = $this->DownloadExcel($orders_list, $title);
        }
    }

    /**
     * 根据支付方式获取订单状态
     *
     * @return  void
     * @author  wl
     * @date    Dec 30, 2016
     **/
    public function getOrderStatus () {
        if (IS_AJAX) {
            $post = I('post.');
            $pay_type = $post['pay_type'];
            $order_status_list = D('Orders')->getOrderStatusByPayType($pay_type);
            if ($order_status_list) {
                $data = array('code' => 200, 'msg' => '获取成功', 'data' => $order_status_list);
            } else {
                $data = array('code' => 400, 'msg' => '获取失败', 'data' => '' );
            }
        } else {
            $data = array('code' => 400, 'msg' => '获取失败', 'data' => '' );
        }
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * ajax获取班制信息（提交的school_id）
     *
     * @return  void
     * @author  wl
     * @date    Feb 06, 2016
     **/
    public function getSchoolShiftsBySchoolId () {
        if (IS_AJAX) {
            $post = I('post.');
            $school_id = $post['school_id'];
            $school_shifts = D('Orders')->getSchoolShifts($school_id);
            if (!empty($school_shifts)) {
                $data = array('code' => 200 , 'msg' => '获取成功' , 'data' => $school_shifts);
            } else {
                $data = array('code' => 400, 'msg' => '获取失败', 'data' => '');
            }
        } else {
            $data = array('code' => 400, 'msg' => '获取失败', 'data' => '' );
        }
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 添加报名驾校订单
     *
     * @return void
     * @author wl
     * @updatw july 12, 2016
     **/
    public function addSchoolOrder () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Order/index'));
        }
        $school_id = $this->getLoginauth();//获取驾校id
        $school_list = D('Manager')->getSchoolList();
        $school_shifts = D('Orders')->getSchoolShifts($school_id);
        $user_info = I('param.'); // get方法也可以获得所有
        if ($user_info) {
            $this->assign('user_info', $user_info);
        }
        if (IS_POST) {
            $post = I('post.');//模板post传递的添加订单内容
            if ( $school_id == 0) {
                $data['so_school_id'] = $post['school_id'] != '' ? $post['school_id'] : 0;
            } else {
                $data['so_school_id'] = $school_id;
            }
            $data['so_username']            = $post['so_username'] ? $post['so_username'] : '';
            $data['free_study_hour']        = $post['free_study_hour'] ? $post['free_study_hour'] : '';
            $data['so_phone']               = $post['so_phone'] ? $post['so_phone'] : '';
            $data['so_user_identity_id']    = $post['so_user_identity_id'] ? $post['so_user_identity_id'] : '';
            $data['so_original_price']      = abs($post['so_original_price']) ? abs($post['so_original_price']) : '';
            $data['so_final_price']         = abs($post['so_final_price']) ? abs($post['so_final_price']) : '';
            $data['so_total_price']         = abs($post['so_total_price']) ? abs($post['so_total_price']) : '';
            $data['so_pay_type']            = $post['so_pay_type'] ? $post['so_pay_type'] : '';
            $data['so_comment_status']      = 1;
            $data['addtime']                = time();
            if ($post['so_pay_type'] == 2) {
                $data['so_order_status'] = 3;
            } else {
                $data['so_order_status'] = $post['so_order_status'];
            }

            $data['so_order_no'] = $post['so_order_no'] ? $post['so_order_no'] : '';
            $data['so_licence'] = $post['so_licence'] ? $post['so_licence'] : '';
            $data['s_zhifu_dm'] = guid(false);
            $data['so_shifts_id'] = $post['so_shifts_id'];
            $user_info =  D('Orders')->getUserInfoByUserTab($data['so_phone']);
            if (empty($user_info)) {
                $this->error('该手机号还未注册', U('Student/addStudent'));
            }
            $user_id                        = $user_info['l_user_id'];
            $data['so_user_id']             = $user_id;
            $checkSchoolOrders = D('Orders')->checkSchoolOrders($data['so_phone']);
            if (! $checkSchoolOrders) {
                $this->error('此学员已报名！');
            }

            $SchoolOrders = D('school_orders');
            if($result = $SchoolOrders->create($data)) {
                $res = $SchoolOrders->add($result);
                if($res) {
                    $map = array();
                    $map['l_user_id'] = $data['so_user_id'];
                    $map['i_status'] = 0;
                    $map['i_user_type'] = 0;
                    $update_info['s_real_name'] = $data['so_username'];
                    $update_data['identity_id'] =  $data['so_user_identity_id'];
                    $update_data['school_id'] =  $data['so_school_id'];
                    $update_ok = D('user')->where($map)
                        ->data($update_info)
                        ->fetchSql(false)
                        ->save();
                    $update = D('users_info')
                        ->where(array('user_id' => $data['so_user_id']))
                        ->data($update_data)
                        ->fetchSql(false)
                        ->save();
                action_log('add_school_order', 'school_orders/user/users_info', $res, $this->getLoginUserId());
                $this->success('添加成功');
                } else {
                    action_log('add_school_order', 'school_orders/user/users_info', $res, $this->getLoginUserId());
                    $this->error('添加失败');
                }
            }

        } else {
            $this->assign('school_id', $school_id);
            $this->assign('school_shifts', $school_shifts);
            $this->assign('school_list', $school_list);
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);

        }
    }

    /**
     * 编辑报名驾校订单
     *
     * @return  void
     * @author  wl
     * @date    july 20, 2016
     * @update  August 10, 2016
     **/
    public function editSchoolOrder () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Order/index'));
        }
        $param = I('param.');
        $oid = $param['id'];
        $school_id = $param['school_id'];
        $school_list = D('Manager')->getSchoolList();
        $order_list = D('Orders')->getSchoolOrdersById($oid);
        $school_shifts = D('Orders')->getSchoolShifts($school_id);
        if (IS_POST) {
            $post = I('post.'); //raw input

            $sid = $post['id'];
            $data['id']                     = $post['id'] == '' ? $order_list['id'] : $post['id'];
            $data['so_school_id']           = $school_id;
            $data['free_study_hour']        = $post['free_study_hour'] == '' ? $order_list['free_study_hour'] : $post['free_study_hour'];
            $data['so_username']            = $post['so_username'] == '' ? $order_list['so_username'] : $post['so_username'];
            $data['so_phone']               = $post['so_phone'] == '' ? $order_list['so_phone'] : $post['so_phone'];
            $data['so_user_identity_id']    = $post['so_user_identity_id'] == '' ? $order_list['so_user_identity_id'] : $post['so_user_identity_id'];
            $data['so_original_price']      = abs($post['so_original_price']) == '' ? $order_list['so_original_price'] : abs($post['so_original_price']);
            $data['so_final_price']         = abs($post['so_final_price']) == '' ? $order_list['so_final_price'] : abs($post['so_final_price']);
            $data['so_total_price']         = abs($post['so_total_price']) == '' ? $order_list['so_total_price'] : abs($post['so_total_price']);
            $data['so_shifts_id']           = $post['so_shifts_id'] == '' ? $order_list['so_shifts_id'] : $post['so_shifts_id'];

            $data['so_pay_type']            = $post['so_pay_type'] != '' ? $post['so_pay_type'] : $order_list['so_pay_type'];
            if ( $post['so_pay_type'] == $order_list['so_pay_type']) {
                $data['so_order_status']            = $post['so_order_status'] != '' ? $post['so_order_status'] : $order_list['so_order_status'];
            } else {
                if ($post['so_pay_type'] == 2) { // 线下支付
                    if ($post['so_order_status'] == 1) { // 非线下支付改成线下支付的状态
                        $data['so_order_status'] = 3;
                    } elseif ($post['so_order_status'] == 2) {
                        $data['so_order_status'] = 4;
                    } elseif ($post['so_order_status'] == 3) {
                        $data['so_order_status'] = 2;
                    } elseif ($post['so_order_status'] == 4) {
                        $data['so_order_status'] = 1;
                    }
                } else {
                    if ($post['so_order_status'] == 1) { // 线下支付改成非线下支付的状态
                        $data['so_order_status'] = 4;
                    } elseif ($post['so_order_status'] == 2) {
                        $data['so_order_status'] = 3;
                    } elseif ($post['so_order_status'] == 3) {
                        $data['so_order_status'] = 1;
                    } elseif ($post['so_order_status'] == 4) {
                        $data['so_order_status'] = 2;
                    }
                }
            }
            $data['so_order_no']            = $post['so_order_no']  ? $post['so_order_no'] : '';
            $data['so_licence']             = $post['so_licence'] == '' ? $order_list['so_licence'] : $post['so_licence'];
            if ($order_list['s_zhifu_dm'] == '') {
                $data['s_zhifu_dm'] = guid(false);
            } else {
                $data['s_zhifu_dm'] = $order_list['s_zhifu_dm'];
            }
            $user_info =  D('Orders')->getUserInfoByUserTab($data['so_phone']);
            if (empty($user_info)) {
                $this->error('请先注册!', U('Student/addStudent'));
            }
            $user_id = $user_info['l_user_id'];
            $data['so_user_id'] = $user_id;
            $SchoolOrders = D('school_orders');
            if($result = $SchoolOrders->create($data)) {
                $res = $SchoolOrders->where(array('id' => $sid))->fetchSql(false)->save($result);
                if ($res) {
                    $map = array();
                    $map['l_user_id'] = $data['so_user_id'];
                    $map['i_status'] = 0;
                    $map['i_user_type'] = 0;
                    $update_info['s_real_name'] = $data['so_username'];
                    $update_data['identity_id'] =  $data['so_user_identity_id'];
                    $update_data['school_id'] =  $data['so_school_id'];
                    $update_ok = D('user')->where($map)
                        ->data($update_info)
                        ->fetchSql(false)
                        ->save();
                    $update = D('users_info')
                        ->where(array('user_id' => $data['so_user_id']))
                        ->data($update_data)
                        ->fetchSql(false)
                        ->save();
                    action_log('edit_school_order', 'school_orders/user/users_info', $sid, $this->getLoginUserId());
                    $this->success('修改成功', U('Order/index'));
                } else {
                    action_log('edit_school_order', 'school_orders/user/users_info', $sid, $this->getLoginUserId());
                    $this->error('未做任何修改', U('Order/editSchoolOrder'));
                }
            } else {
                action_log('edit_school_order', 'school_orders/user/users_info', $sid, $this->getLoginUserId());
                $this->error('未做任何修改', U('Order/editSchoolOrder'));
            }

        } else {
            $this->assign('school_shifts', $school_shifts);
            $this->assign('school_list', $school_list);
            $this->assign('order_list', $order_list); //将订单信息带到编辑模板中
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
        }
    }


    /**
     * 逻辑删除报名驾校订单
     *
     * @author wl
     * @date    july 20, 2016
     * @update  july 21, 2016
     **/
    public function delSchoolOrder () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Order/index'));
        }
        if(IS_AJAX) {
            $order_id = I('post.id');
            $school_orders = M('school_orders');
            $res = $school_orders->where(array('id' => $order_id))
                ->save(array('so_order_status'=>101)); // 101-删除状态
            if ($res) {
                $data = array('code' => 200, 'msg' => '删除成功！', 'data' => '');
            } else {
                $data = array('code' => 400, 'msg' => '删除失败！', 'data' => '');
            }
        } else {
            $data = array('code' => 400, 'msg' => '删除失败！', 'data' => '');
        }
        action_log('del_school_order', 'school_orders', $order_id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 批量删除报名班制的信息
     *
     * @return  void
     * @author  wl
     * @date    Mar 17, 2017
     **/
    public function delSchoolOrders () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $data = array('code' => 400, 'msg' => '您无权删除此订单', 'data' => '');
            $this->ajaxReturn($data, 'JSON');
        }
        if(IS_AJAX) {
            $order_id = I('post.check_id');
            $id_arr = explode(',', $order_id);
            $school_orders = M('school_orders');
            $res = $school_orders->where(array('id' => array('IN', $id_arr)))
                ->save(array('so_order_status'=>101)); // 101-删除状态
            if ($res) {
                $data = array('code' => 200, 'msg' => '删除成功！', 'data' => $res);
            } else {
                $data = array('code' => 400, 'msg' => '删除失败！', 'data' => '');
            }
        } else {
            $data = array('code' => 400, 'msg' => '删除失败！', 'data' => '');
        }
        foreach ($id_arr as $key => $value) {
            action_log('del_school_orders', 'school_orders', $value, $this->getLoginUserId());
        }
        $this->ajaxReturn($data, 'JSON');
    }


    /**
     * 设置报名驾校订单的状态
     *
     * @return  void
     * @author  wl
     * @date    Dec 21, 2016
     **/
    public function setSchoolOrderStatus () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Order/index'));
        }
        if (IS_AJAX) {
            $post = I('post.');
            $id = $post['id'];
            $order_status = $post['status'];
            $order_type = $post['order_type'];
            $school_id = $post['school_id'];
            $order_info = D('Orders')->getSchoolOrdersStatus($id);
            $old_type = $order_info['so_pay_type'];
            $old_status = $order_info['so_order_status'];
            $list = array($order_type, $order_status);
            if ($old_type == 2) {
                $old_status_name = array(
                    '1' => '未付款',
                    '2' => '已取消',
                    '3' => '已付款',
                    '4' => '退款中',
                    );
            } else {
                $old_status_name = array(
                    '1' => '已付款',
                    '2' => '退款中',
                    '3' => '已取消',
                    '4' => '未付款',
                    );
            }

            if ($order_type == 2) {
                $order_status_name = array(
                    '1' => '未付款',
                    '2' => '已取消',
                    '3' => '已付款',
                    '4' => '退款中',
                    );
            } else {
                $order_status_name = array(
                    '1' => '已付款',
                    '2' => '退款中',
                    '3' => '已取消',
                    '4' => '未付款',
                    );
            }

            $pay_method = array(
                '1' => '支付宝',
                '2' => '线下支付',
                '3' => '微信支付',
                '4' => '银联支付',
                );
            
            $result = D('Orders')->setSchoolOrderStatus($id, $order_status, $order_type, $school_id);
            $now_order_info = D('Orders')->getSchoolOrdersStatus($id);
            $now_order_type = $now_order_info['so_pay_type'];
            if ($result) {
                action_log('set_schoolorder_status', 'school_orders', $id, $this->getLoginUserId(), '将订单的状态由 '.$pay_method[$old_type].'/'.$old_status_name[$old_status].' 改成 '.$pay_method[$now_order_type].'/'.$order_status_name[$order_status]);
                $data = array('code' => 200 , 'msg' => '设置成功' , 'data' => $list);
            } else {
                action_log('set_schoolorder_status', 'school_orders', $id, $this->getLoginUserId(), '修改订单状态失败');
                $data = array('code' => 400, 'msg' => '设置失败', 'data' => 2);
            }
        } else {
            action_log('set_schoolorder_status', 'school_orders', $id, $this->getLoginUserId(), '修改订单状态失败');
            $data = array('code' => 400, 'msg' => '设置失败', 'data' => 2);
        }
        $this->ajaxReturn($data, 'JSON');

    }


// 2.预约学车的列表展示
    /**
     * 预约学车订单的列表展示
     *
     * @return  void
     * @author  wl
     * @update  july 22, 2016
     **/
    public function appointLearner () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $school_id = $this->getLoginauth();//获取当前驾校id
        $list = D('Orders')->getStudyOrdersLists($school_id, ! in_array($role_id, [1, 8, 9]));
        $this->assign('school_id',$school_id);
        $this->assign('count',$list['count']);
        $this->assign('page',$list['page']);
        $this->assign('study_orders', $list['study_orders']);
        $this->assign('total_time', $list['total_service_time']);
        $this->display('Order/appointLearner');
    }

    /**
     * 搜索预约学车订单
     *
     * @return  void
     * @author  wl
     * @date    july 22, 2016
     **/
    public function searchStudyOrders () {
        $school_id = $this->getLoginauth();
        $param = I('param.');
        $s_keyword = trim((string)$param['s_keyword']);
        $search_type = trim((string)$param['search_type']);
        $deal_type = $param['deal_type'];
        $i_status = $param['i_status'];
        $appoint_time = $param['appoint_time'];
        $role_id = $this->getRoleId();
        if ( $s_keyword == '' && $search_type == '' && $deal_type == '' && trim($i_status) == '' && $appoint_time == '') {
            $this->redirect('Order/appointLearner');
        } else {
            $study_orders_list = D('Orders')->searchStudyOrderList($param, $school_id, ! in_array($role_id, [1, 8, 9]));
            $this->assign('school_id', $school_id);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('search_type', $search_type);
            $this->assign('deal_type', $deal_type);
            $this->assign('i_status', $i_status);
            $this->assign('appoint_time', $appoint_time);
            $this->assign('count', $study_orders_list['count']);
            $this->assign('page', $study_orders_list['page']);
            $this->assign('total_time', $study_orders_list['total_service_time']);
            $this->assign('study_orders', $study_orders_list['study_orders']);
            $this->display('Order/appointLearner');
        }
    }

    /**
     * 逻辑删除预约学车订单
     *
     * @return  void
     * @author  wl
     * @date    july 22, 2016
     **/
    public function delAppointLearner () {
        if (IS_AJAX) {
            $role_id = $this->getRoleId();
            $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
            if (true !== $permission_check) {
                $data = array('code' => 102, 'msg' => 'Permission denied!', 'data' =>'');
            } else {
                $study_order_id = I('post.id');
                $res = M('study_orders')->where('l_study_order_id = :order_id')
                    ->bind(['order_id' => $study_order_id])
                    ->fetchSql(false)
                    ->save(array('i_status' => 101)); // 101-删除
                if ($res) {
                    $data = array('code' => 200, 'msg' => '删除订单成功！', 'data' =>$study_order_id);
                } else {
                    $data = array('code' => 400, 'msg' => '删除订单失败！', 'data' =>$study_order_id);
                }
            }
            action_log('del_study_orders', 'study_orders', $study_order_id, $this->getLoginUserId());
            $this->ajaxReturn($data);
        }
    }

    /**
     * 批量删除预约学车订单的信息
     *
     * @return  void
     * @author  wl
     * @date    Mar 18, 2017
     **/
    public function delAppointOrders () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $data = array('code' => 102, 'msg' => '您无权删除此订单', 'data' => '');
            $this->ajaxReturn($data, 'JSON');
        }
        if(IS_AJAX) {
            $order_id = I('post.id');
            $id_arr = explode(',', $order_id);
            $study_orders = M('study_orders');
            $res = $study_orders->where(array('l_study_order_id' => array('IN', $id_arr)))
                    ->save(array('i_status' => 101)); // 101-删除
            if ($res) {
                $data = array('code' => 200, 'msg' => '删除订单成功！', 'data' =>$order_id);
            } else {
                $data = array('code' => 400, 'msg' => '删除订单失败！', 'data' =>'');
            }
        } else {
            $data = array('code' => 400, 'msg' => '删除失败！', 'data' => '');
        }
        foreach ($id_arr as $key => $value) {
            action_log('del_appoint_orders', 'study_orders', $value, $this->getLoginUserId());
        }
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 设置预约学车订单状态
     *
     * @return  void
     * @author  wl
     * @date    Dec 21, 2016
     **/
    public function setStudyOrdersStatus () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Order/appointLearner'));
        }
        if (IS_AJAX) {
            $post = I('post.');
            $id = $post['id'];
            $now_time = time();
            $status = $post['status'];
            $study_orders_info = D('Orders')->getStudyOrdersStausById($id);
            $i_status = $study_orders_info['status'];
            $end_time = $study_orders_info['end_time'];
            $gap_time = $study_orders_info['gap_time'];
            $cancel_time = $study_orders_info['cancel_time'];
            $pay_type = $study_orders_info['pay_type'];
            $order_type_name = array(
                '0' => '其他方式',
                '1' => '支付宝支付',
                '2' => '线下支付',
                '3' => '微信支付',
                '4' => '银联支付',
                );
            $order_status_name = array(
                '1' => '已付款',
                '2' => '已完成',
                '3' => '已取消',
                '101' => '已删除',
                '1003' => '未付款',
                '1006' => '退款中',
                );
            if ($i_status == 1) { //已付款
                if ($status == 2) {
                    // 已付款的订单是否已经完成预约的时间
                    if ($now_time - $end_time < 0) {
                        $data = array('code' => 103 , 'msg' => '尚未完成预约的时间' , 'data' => ($now_time - $end_time));
                    } else {
                        $result = D('Orders')->setStudyOrdersStatus($id, $status);
                        $now_orders_info = D('Orders')->getStudyOrderStatus($id);
                        $now_pay_type = $now_orders_info['deal_type'];
                        $now_status = $now_orders_info['i_status'];
                        if ($result) {
                            action_log('set_studyorder_status', 'study_orders', $id, $this->getLoginUserId(), '将订单状态由 '.$order_type_name[$pay_type].'/'.$order_status_name[$i_status].' 改成 '.$order_type_name[$now_pay_type].'/'.$order_status_name[$now_status]);
                            $data = array('code' => 200 , 'msg' => '设置成功' , 'data' => $result);
                        } else {
                            action_log('set_studyorder_status', 'study_orders', $id, $this->getLoginUserId(), '修改订单状态失败');
                            $data = array('code' => 400, 'msg' => '设置失败', 'data' => 2);
                        }
                    }

                } else if ($status == 1) {
                    $data = array('code' => 104, 'msg' => '修改的状态不能为当前状态', 'data' => $status);

                } else if ($status == 1003 || $status == 1006) {
                    $data = array('code' => 103, 'msg' => '当前已付款', 'data' => $status);

                } else {

                    // add notification to student and coach if the order is canceled by administrator
                    if ($status == 3) { // cancel
                        $order_info = D('Orders')->getAppointOrderInfo(['order_id' => $id]);
                        if ($order_info) {
                            try {
                                $student_content = sprintf('详情: %s | %s | %s | %s %s | 订单号%s',$order_info['coach_name'], $order_info['license_name'], $order_info['lesson_name'], $order_info['appoint_date'], $order_info['appoint_time'], $order_info['order_no']);
                                // 推送学员
                                $push_info = new \StdClass;
                                $push_info->product = 'student';
                                $push_info->target = $order_info['user_id'];
                                $push_info->content = '【取消预约】'.$student_content;
                                $push_info->type = 2;
                                $push_info->member_id = $order_info['user_id'];
                                $push_info->member_type = 1;
                                $push_info->beizhu = '取消预约';
                                $push_info->from = '嘻哈学车';
                                $this->redis->rpush('msg_send_queue', json_encode($push_info, JSON_UNESCAPED_UNICODE));

                                $coach_content = sprintf('详情: %s | %s | %s | %s %s | 订单号%s',$order_info['user_name'], $order_info['license_name'], $order_info['lesson_name'], $order_info['appoint_date'], $order_info['appoint_time'], $order_info['order_no']);
                                // 推送学员
                                $push_info->product = 'coach';
                                $push_info->target = $order_info['coach_phone'];
                                $push_info->content = '【取消预约】'.$coach_content;
                                $push_info->type = 2;
                                $push_info->member_id = $order_info['coach_id'];
                                $push_info->member_type = 2;
                                $push_info->beizhu = '取消预约';
                                $push_info->from = '嘻哈学车';
                                $this->redis->rpush('msg_send_queue', json_encode($push_info, JSON_UNESCAPED_UNICODE));
                            } catch (\Exception $e) {
                                Log::write('异常发生了');
                                Log::write($e->getMessage());
                            }
                        } else {
                            $this->ajaxReturn(array('code' => '100', 'msg' => '订单不存在', 'data' => ''));
                        }
                    }

                    $result = D('Orders')->setStudyOrdersStatus($id, $status);
                    $now_orders_info = D('Orders')->getStudyOrderStatus($id);
                    $now_pay_type = $now_orders_info['deal_type'];
                    $now_status = $now_orders_info['i_status'];
                    if ($result) {
                        action_log('set_studyorder_status', 'study_orders', $id, $this->getLoginUserId(), '将订单状态由 '.$order_type_name[$pay_type].'/'.$order_status_name[$i_status].' 改成 '.$order_type_name[$now_pay_type].'/'.$order_status_name[$now_status]);
                        $data = array('code' => 200 , 'msg' => '设置成功' , 'data' => $result);
                    } else {
                        action_log('set_studyorder_status', 'study_orders', $id, $this->getLoginUserId(), '修改订单状态失败');
                        $data = array('code' => 400, 'msg' => '设置失败', 'data' => 2);
                    }
                }
                $this->ajaxReturn($data, 'JSON');

            } else if ($i_status == 2) { //已完成
                if ($status == 2) {
                    $data = array('code' => 104, 'msg' => '修改的状态不能为当前状态', 'data' => $status);
                } else {
                    $data = array('code' => 103, 'msg' => '不能对此订单做任何修改', 'data' => $status);

                }
                $this->ajaxReturn($data, 'JSON');

            } else if ($i_status == 3) { //已取消
                if ($status == 3) {
                    $data = array('code' => 104, 'msg' => '修改的状态不能为当前状态', 'data' => $status);

                } else if ($status == 1 || $status == 2 || $status == 1003 || $status == 1006 )  {
                    $data = array('code' => 103, 'msg' => '只可以删除此订单', 'data' => $status);

                } else {
                    $result = D('Orders')->setStudyOrdersStatus($id, $status);
                    $now_orders_info = D('Orders')->getStudyOrderStatus($id);
                    $now_pay_type = $now_orders_info['deal_type'];
                    $now_status = $now_orders_info['i_status'];
                    if ($result) {
                        action_log('set_studyorder_status', 'study_orders', $id, $this->getLoginUserId(), '将订单状态由 '.$order_type_name[$pay_type].'/'.$order_status_name[$i_status].' 改成 '.$order_type_name[$now_pay_type].'/'.$order_status_name[$now_status]);
                        $data = array('code' => 200 , 'msg' => '设置成功' , 'data' => $result);
                    } else {
                        action_log('set_studyorder_status', 'study_orders', $id, $this->getLoginUserId(), '修改订单状态失败');
                        $data = array('code' => 400, 'msg' => '设置失败', 'data' => 2);
                    }
                }
                $this->ajaxReturn($data, 'JSON');

            } else if ($i_status == 1003) { //未付款

                if ($status == 1006 || $status == 2) {
                    $data = array('code' => 103, 'msg' => '尚未付款', 'data' => $status);

                } else if ($status == 1003) {
                    $data = array('code' => 104, 'msg' => '修改的状态不能为当前状态', 'data' => $status);

                } else if ($status == 101) {
                    $data = array('code' => 103, 'msg' => '请先取消该订单', 'data' => $status);

                } else {

                    // add notification to student and coach if the order is canceled by administrator
                    if ($status == 3) { // cancel
                        $order_info = D('Orders')->getAppointOrderInfo(['order_id' => $id]);
                        if ($order_info) {
                            try {
                                $student_content = sprintf('详情: %s | %s | %s | %s %s | 订单号%s',$order_info['coach_name'], $order_info['license_name'], $order_info['lesson_name'], $order_info['appoint_date'], $order_info['appoint_time'], $order_info['order_no']);
                                // 推送学员
                                $push_info = new \StdClass;
                                $push_info->product = 'student';
                                $push_info->target = $order_info['user_id'];
                                $push_info->content = '【取消预约】'.$student_content;
                                $push_info->type = 2;
                                $push_info->member_id = $order_info['user_id'];
                                $push_info->member_type = 1;
                                $push_info->beizhu = '取消预约';
                                $push_info->from = '嘻哈学车';
                                $this->redis->rpush('msg_send_queue', json_encode($push_info, JSON_UNESCAPED_UNICODE));

                                $coach_content = sprintf('详情: %s | %s | %s | %s %s | 订单号%s',$order_info['user_name'], $order_info['license_name'], $order_info['lesson_name'], $order_info['appoint_date'], $order_info['appoint_time'], $order_info['order_no']);
                                // 推送学员
                                $push_info->product = 'coach';
                                $push_info->target = $order_info['coach_phone'];
                                $push_info->content = '【取消预约】'.$coach_content;
                                $push_info->type = 2;
                                $push_info->member_id = $order_info['coach_id'];
                                $push_info->member_type = 2;
                                $push_info->beizhu = '取消预约';
                                $push_info->from = '嘻哈学车';
                                $this->redis->rpush('msg_send_queue', json_encode($push_info, JSON_UNESCAPED_UNICODE));
                            } catch (\Exception $e) {
                                Log::write('异常发生了');
                                Log::write($e->getMessage());
                            }
                        } else {
                            $this->ajaxReturn(array('code' => '100', 'msg' => '订单不存在', 'data' => ''));
                        }
                    }

                    $result = D('Orders')->setStudyOrdersStatus($id, $status);
                    $now_orders_info = D('Orders')->getStudyOrderStatus($id);
                    $now_pay_type = $now_orders_info['deal_type'];
                    $now_status = $now_orders_info['i_status'];
                    if ($result) {
                        action_log('set_studyorder_status', 'study_orders', $id, $this->getLoginUserId(), '将订单状态由 '.$order_type_name[$pay_type].'/'.$order_status_name[$i_status].' 改成 '.$order_type_name[$now_pay_type].'/'.$order_status_name[$now_status]);
                        $data = array('code' => 200 , 'msg' => '设置成功' , 'data' => $result);
                    } else {
                        action_log('set_studyorder_status', 'study_orders', $id, $this->getLoginUserId(), '修改订单状态失败');
                        $data = array('code' => 400, 'msg' => '设置失败', 'data' => 2);
                    }
                }
                $this->ajaxReturn($data, 'JSON');

            } else if ($i_status == 1006) { //退款中

                if ($status == 1 || $status == 2 || $status == 1003) {
                    $data = array('code' => 103, 'msg' => '正在退款中', 'data' => $status);

                } else if ($status == 1006) {
                    $data = array('code' => 104, 'msg' => '修改的状态不能为当前状态', 'data' => $status);

                } else {
                    $result = D('Orders')->setStudyOrdersStatus($id, $status);
                    $now_orders_info = D('Orders')->getStudyOrderStatus($id);
                    $now_pay_type = $now_orders_info['deal_type'];
                    $now_status = $now_orders_info['i_status'];
                    if ($result) {
                        action_log('set_studyorder_status', 'study_orders', $id, $this->getLoginUserId(), '将订单状态由 '.$order_type_name[$pay_type].'/'.$order_status_name[$i_status].' 改成 '.$order_type_name[$now_pay_type].'/'.$order_status_name[$now_status]);
                        $data = array('code' => 200 , 'msg' => '设置成功' , 'data' => $result);
                    } else {
                        action_log('set_studyorder_status', 'study_orders', $id, $this->getLoginUserId(), '修改订单状态失败');
                        $data = array('code' => 400, 'msg' => '设置失败', 'data' => 2);
                    }
                }
                $this->ajaxReturn($data, 'JSON');

            } else if ($i_status == 101) { //已删除
                $data = array('code' => 104, 'msg' => '此订单已删除', 'data' => $status);
                $this->ajaxReturn($data, 'JSON');
            }

            $final_time = intval($gap_time - $cancel_time <= 0) ? 0 : intval($gap_time - $cancel_time);
            if ($final_time <= 0) {
                $data = array('code' => 102 , 'msg' => '您只有在下单时'.$cancel_time.'后取消' , 'data' => $gap_time);
                $this->ajaxReturn($data, 'JSON');
            }

            $result = D('Orders')->setStudyOrdersStatus($id, $status);
            $now_orders_info = D('Orders')->getStudyOrderStatus($id);
            $now_pay_type = $now_orders_info['deal_type'];
            $now_status = $now_orders_info['i_status'];
            if ($result) {
                action_log('set_studyorder_status', 'study_orders', $id, $this->getLoginUserId(), '将订单状态由 '.$order_type_name[$pay_type].'/'.$order_status_name[$i_status].' 改成 '.$order_type_name[$now_pay_type].'/'.$order_status_name[$now_status]);
                $data = array('code' => 200 , 'msg' => '设置成功' , 'data' => $result);
            } else {
                action_log('set_studyorder_status', 'study_orders', $id, $this->getLoginUserId(), '修改订单状态失败');
                $data = array('code' => 400, 'msg' => '设置失败', 'data' => 2);
            }

        } else {
            action_log('set_studyorder_status', 'study_orders', $id, $this->getLoginUserId(), '修改订单状态失败');
            $data = array('code' => 400, 'msg' => '设置失败', 'data' => 2);
        }
        $this->ajaxReturn($data, 'JSON');
    }


// 3.交易记录模块
    /**
     * 获取交易记录表
     *
     * @return  void
     * @author  wl
     * @date    Oct 26, 2016
     **/
    public function transRecords () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $transrecordlist = D('User')->getTransRecords();
        $this->assign('page', $transrecordlist['page']);
        $this->assign('count', $transrecordlist['count']);
        $this->assign('transrecordlist', $transrecordlist['transrecordslist']);
        $this->display('Order/transRecords');
    }

    /**
     * 获取交易记录表
     *
     * @return  void
     * @author  wl
     * @date    Oct 26, 2016
     **/
    public function searchTransRecords () {
        $param = I('param.');
        $s_keyword = trim((string)$param['s_keyword']);
        $transaction_pay_type = (int)$param['transaction_pay_type'];
        $transaction_status = (int)$param['transaction_status'];
        $search_info = trim((string)$param['search_info']);
        if ($s_keyword == '' && $transaction_status == '' && $transaction_pay_type == '' && $search_info == '') {
            $this->redirect('Order/transRecords');
        } else {
            $transrecordlist = D('User')->searchTransRecords($param);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('search_info', $search_info);
            $this->assign('transaction_status', $transaction_status);
            $this->assign('transaction_pay_type', $transaction_pay_type);
            $this->assign('page', $transrecordlist['page']);
            $this->assign('count', $transrecordlist['count']);
            $this->assign('transrecordlist', $transrecordlist['transrecordslist']);
            $this->display('Order/transRecords');
        }
    }

    /**
     * 删除交易记录信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 26, 2016
     **/
    public function delTransRecords () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Order/transRecords'));
        }
        if (IS_AJAX) {
            $id = I('param.id');
            $result = D('User')->delTransRecords($id);
            if ($result) {
                $data = array('code' => 200, 'msg' => '删除成功' ,'data' => $id);
            } else {
                $data = array('code' => 200, 'msg' => '删除失败' ,'data' => $id);
            }
        }
        action_log('del_train_records', 'transaction_records', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');

    }

    // 自动生成支付唯一识别码
    public function createdm () {
        $s_zhifu_dm = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $data = array('code'=>-1, 'data'=>$s_zhifu_dm);
        $this->ajaxReturn($data, 'JSON');
    }

    //自动生成订单号
    public function createno() {
        $s_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $data = array('code'=>-1, 'data'=>$s_order_no);
        $this->ajaxReturn($data, 'JSON');
    }

    //检测订单号重复
    public function checkno() {
        $so_order_no = I('post.so_order_no');
        $school_orders = M('school_orders');
        $res = $school_orders->where(array('so_order_no'=>$so_order_no))->find();
        if($res) {
            $data = array('code'=>400, 'msg'=>'有重复订单号,请重新生成', 'data'=>'');
        } else {
            $data = array('code'=>200, 'msg'=>'√，订单号可以使用', 'data'=>'');
        }
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 检测手机号重复
     *
     * @return void
     * @author sun/wl
     * @update july 21, 2016
     **/
    public function checkPhone() {
        $so_phone = I('post.so_phone');
        $school_orders = M('school_orders');
        $register = D('User')->isPhoneRegistered($so_phone);
        $map = array();
        $string = " ( so_pay_type = 2 AND so_order_status != 2) OR
            ( so_pay_type IN (1, 3, 4) AND so_order_status != 3) ";
        $map['_string'] = $string;
        $map['so_phone'] = array('EQ', $so_phone);
        $map['so_order_status'] = array('NEQ', 101);
        $res = $school_orders->where($map)->find();
        if ($register) {//返回true说明该号码已注册,可以添加订单
            if($res) {
                $data = array('code'=>400, 'msg'=>'×亲，该手机号已报名','data'=>'');
            } else {
                $data = array('code'=>200, 'msg'=>'√，手机号可以使用','data'=>'');
            }
        } else {
            $data = array('code'=>400, 'msg'=>'请先注册','data'=>'');
        }
        $this->ajaxReturn($data, 'JSON');
    }


    //检测身份证号重复
    public function checkIdentity() {
        $so_user_identity_id = I('post.so_user_identity_id');
        $school_orders = M('school_orders');
        $res = $school_orders->where(array('so_user_identity_id'=>$so_user_identity_id))->find();
        if($res) {
            $data = array('code'=>400, 'msg'=>'×亲，该身份证已注册','data'=>'');
        } else {
            $data = array('code'=>200, 'msg'=>'√ 可以使用','data'=>'');
        }
        $this->ajaxReturn($data, 'JSON');
    }

}
