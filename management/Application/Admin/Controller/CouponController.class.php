<?php 
namespace Admin\Controller;
use Think\Controller;

/**
 * 优惠卷管理模块
 * @author wl
 */
class CouponController extends BaseController {

	//构造函数，判断是否登录
	public function _initialize() {
	    if(!session('loginauth')) {
			$this->redirect('Public/login');
			exit();
	    }
	}
// 优惠券兑换码管理列表
    /**
     * 券兑换码列表
     *
     * @return  void
     * @author  wl
     * @date    Mar 14, 2017
     **/
    public function couponCodeAdmin () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $couponcodelist = D('Coupon')->getCouponCodeList();
        $this->assign('count', $couponcodelist['count']);
        $this->assign('page', $couponcodelist['page']);
        $this->assign('couponcodelist', $couponcodelist['couponcodelist']);
        $this->display('Coupon/couponCodeAdmin');
    }

    /**
     * 搜索兑换码
     *
     * @return  void
     * @author  wl
     * @date    Mar 15, 2017
     **/
    public function searchCouponCode () {
        $param = I('param.');
        $s_keyword = trim((string)$param['s_keyword']);
        $search_info = trim((string)$param['search_info']);
        if ($s_keyword == '' && $search_info == '') {
            $this->redirect('Coupon/couponCodeAdmin');
        } else {    
            $couponcodelist = D('Coupon')->searchCouponCode($param);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('search_info', $search_info);
            $this->assign('count', $couponcodelist['count']);
            $this->assign('page', $couponcodelist['page']);
            $this->assign('couponcodelist', $couponcodelist['couponcodelist']);
            $this->display('Coupon/couponCodeAdmin');
        }
    }

    /**
     * 修改券兑换码中的相关信息
     *
     * @return  void
     * @author  wl
     * @date    Mar 15, 2017
     **/
    public function editCouponCode () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $param = I('param.');
        $id = $param['id'];
        $couponlist = D('Coupon')->getCouponName();
        $couponcodelist = D('Coupon')->getCouponCodeById($id);
        if (IS_POST) {
            $post = I('post.');
            $data['id'] = $post['id'];
            $data['coupon_id'] = $post['coupon_id'] != '' ? $post['coupon_id'] : $couponcodelist['coupon_id'];
            $data['coupon_code'] = $post['coupon_code'] != '' ? $post['coupon_code'] : $couponcodelist['coupon_code'];
            if ($post['coupon_code'] != $couponcodelist['coupon_code']) {
                $data['is_used'] = 0;
            } else {
                $data['is_used'] = $couponcodelist['is_used'];
            }
            $coupon = D('coupon');
            $coupon_code = D('coupon_code');
            if ($r = $coupon_code->create($data)) {
                $re = $coupon_code->where(array('id' => $data['id']))->fetchSql(false)->save($r);
                if ($re) {
                    action_log('edit_coupon_code', 'coupon_code', $data['id'], $this->getLoginUserId());
                    $this->success('修改成功', u('Coupon/couponCodeAdmin'));
                } else {
                    action_log('edit_coupon_code_error', 'coupon_code', $data['id'], $this->getLoginUserId());
                    $this->success('尚未修改', u('Coupon/editCouponCode'));
                }
            } else {
                action_log('edit_coupon_code_error', 'coupon_code', $data['id'], $this->getLoginUserId());
                $this->success('尚未修改', u('Coupon/editCouponCode'));
            }
        } else {
            $this->assign('couponlist', $couponlist);
            $this->assign('couponcodelist', $couponcodelist);
            $this->display('Coupon/editCouponCode');            
        }
    }

    /**
     * 删除优惠码
     *
     * @return  void
     * @author  wl
     * @date    Mar 15, 2017
     **/
    public function delCouponCode () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        if (IS_AJAX) {
            $param = I('param.');
            $id = $param['id'];
            $result = D('Coupon')->delCouponCode($id);
            if ($result) {
                action_log('del_coupon_code', 'coupon_code', $result, $this->getLoginUserId());
                $data = array('code' => 200, 'msg' => '删除成功', 'data' => $id);
            } else {
                action_log('del_coupon_code_error', 'coupon_code', $result, $this->getLoginUserId());
                $data = array('code' => 400, 'msg' => '删除失败', 'data' => '');
            }
        } else {
            action_log('del_coupon_code_error', 'coupon_code', $result, $this->getLoginUserId());
            $data = array('code' => 400, 'msg' => '删除失败', 'data' => '');
        }

    }

    /**
     * 券的种类列表展示
     *
     * @return  void
     * @author  wl
     * @date    Nov 11, 2016
     **/
    public function couponCategory () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $couponCategoryList = D('Coupon')->getCouponCateList();
        $this->assign('page', $couponCategoryList['page']);
        $this->assign('count', $couponCategoryList['count']);
        $this->assign('couponcatelist', $couponCategoryList['couponcatelist']);
        $this->display('Coupon/couponCategory');
    }
    /**
     * 搜索券的种类列表
     *
     * @return  void
     * @author  wl
     * @date    Nov 11, 2016
     **/
    public function searchCouponCategory () {
        $param = I('param.');
        $s_keyword = trim((string)$param['s_keyword']);
        if ( $s_keyword == '' ) {
            $this->redirect('Coupon/couponCategory');
        } else {
            $couponCategoryList = D('Coupon')->searchCouponCategory($param);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('page', $couponCategoryList['page']);
            $this->assign('count', $couponCategoryList['count']);
            $this->assign('couponcatelist', $couponCategoryList['couponcatelist']);
            $this->display('Coupon/couponCategory');
        }
    }
    /**
     * 添加券的种类
     *
     * @return  void
     * @author  wl
     * @date    Nov 11, 2016
     **/
    public function addCouponCategory () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Coupon/couponCategory'));
        }
        if (IS_POST) {
            $post = I('post.');
            $data['cate_name']      = $post['cate_name'] ? $post['cate_name'] : '';
            $data['cate_desc']      = $post['cate_desc'] ? $post['cate_desc'] : '';
            $data['coupon_rule']    = $post['coupon_rule'] ? $post['coupon_rule'] : '';
            $data['addtime']        = time();
            if ($data['cate_name'] == '') {
                $this->error('请完善信息', U('Coupon/addCouponCategory'));
            }
            $check_cate_name = D('Coupon')->checkCouponCateName($data['cate_name']);
            if (!empty($check_cate_name)) {
                $this->error('该优惠券种类已存在');
            }
            $coupon_category = D('coupon_category');
            if ($res = $coupon_category->create($data)) {
                $result = $coupon_category->add($res);
                if ($result) {
                    action_log('add_coupon_category', 'coupon_category', $result, $this->getLoginUserId());
                    $this->success('添加成功', U('Coupon/couponCategory'));
                } else {
                    action_log('add_coupon_category', 'coupon_category', $result, $this->getLoginUserId());
                    $this->error('添加失败', U('Coupon/addCouponCategory'));
                }
            } else {
                action_log('add_coupon_category', 'coupon_category', $result, $this->getLoginUserId());
                $this->error('添加失败', U('Coupon/addCouponCategory'));
            }
        } else {
            $this->display('Coupon/addCouponCategory');
        }
    }

    /**
     * 编辑券的种类
     *
     * @return  void
     * @author  wl
     * @date    Nov 11, 2016
     **/
    public function editCouponCategory () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Coupon/couponCategory'));
        }
        $id = I('param.id');
        $couponcatelist = D('Coupon')->getCouponCateById($id);
        if (IS_POST) {
            $post = I('post.');
            $data['id']             = $post['id'];
            $data['cate_name']      = $post['cate_name'] == '' ? $couponcatelist['cate_name'] : $post['cate_name'];
            $data['cate_desc']      = $post['cate_desc'] == '' ? $couponcatelist['cate_desc'] : $post['cate_desc'];
            $data['coupon_rule']    = $post['coupon_rule'] == '' ? $couponcatelist['coupon_rule'] : $post['coupon_rule'];
            if ($data['cate_name'] == '') {
                $this->error('请完善信息', U('Coupon/addCouponCategory'));
            }
            $coupon_category = D('coupon_category');
            if ($res = $coupon_category->create($data)) {
                $result = $coupon_category->where(array('id' => $id))->save($res);
                if ($result) {
                    action_log('edit_coupon_category', 'coupon_category', $id, $this->getLoginUserId());
                    $this->success('修改成功', U('Coupon/couponCategory'));
                } else {
                    action_log('edit_coupon_category', 'coupon_category', $id, $this->getLoginUserId());
                    $this->error('未做任何修改', U('Coupon/editCouponCategory'));
                }
            } else {
                action_log('edit_coupon_category', 'coupon_category', $id, $this->getLoginUserId());
                $this->error('未做任何修改', U('Coupon/editCouponCategory'));
            }
        } else {
            $this->assign('couponcatelist', $couponcatelist);
            $this->display('Coupon/editCouponCategory');
        }
    }


    /**
     * 单条删除优惠券种类及其相关信息
     *
     * @return  void
     * @author  wl
     * @date    Nov 11, 2016
     **/
    public function delCouponCategory () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Coupon/couponCategory'));
        }
        if(IS_AJAX) {
            $cid = I('post.id');
            $res = D('Coupon')->delCouponCategory($cid); 
            if ($res) {
                $data = array('code' => 200, 'msg' => '删除成功！', 'data' => '');
            } else {
                $data = array('code' => 400, 'msg' => '删除失败！', 'data' => '');
            }                   
        } else {
            $data = array('code' => 400, 'msg' => '删除失败！', 'data' => '');
        }
        action_log('del_coupon_category', 'coupon_category', $cid, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }
    /**
     * 优惠卷列表展示
     *
     * @return  void
     * @author  wl
     * @date    Oct 11, 2016
     **/
    public function index () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $school_id = $this->getLoginauth();
        $couponList = D('Coupon')->getCouponList($school_id);
        $this->assign('school_id', $school_id);
        $this->assign('page', $couponList['page']);
        $this->assign('count', $couponList['count']);
        $this->assign('couponlist', $couponList['couponlist']);
        $this->display('Coupon/index');
    }
    /**
     * 搜索优惠券信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 11, 2016
     **/
    public function searchCoupon () {
        $school_id = $this->getLoginauth();
        $param = I('param.');
        $search_info = trim((string)$param['search_info']);
        $owner_type = trim((int)$param['owner_type']);
        $coupon_category = trim((int)$param['coupon_category']);
        $s_keyword = trim((string)$param['s_keyword']);
        if ( $s_keyword == '' && $search_info == '' && $coupon_category  == '' && $owner_type  == '') {
            $this->redirect('Coupon/index');
        } else {
            $couponList = D('Coupon')->searchCoupon($param, $school_id);
            $this->assign('school_id', $school_id);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('owner_type', $owner_type);
            $this->assign('search_info', $search_info);
            $this->assign('coupon_category', $coupon_category);
            $this->assign('page', $couponList['page']);
            $this->assign('count', $couponList['count']);
            $this->assign('couponlist', $couponList['couponlist']);
            $this->display('Coupon/index');
        }
    }

    /**
     * 设置优惠券的开启状态
     *
     * @return  void
     * @author  wl
     * @date    Nov 11, 2016
     **/
    public function setOpenStatus () {
        if(IS_AJAX) {
            $id = I('post.id');
            $status = I('post.status');
            $res = D('Coupon')->setOpenStatus($id,$status);
            if($res['is_open']) {
                $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$id);
            } else {
                $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
            }
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');    
        }
        action_log('set_coupon_status', 'coupon', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 设置优惠券的展示状态
     *
     * @return  void
     * @author  wl
     * @date    Mar 15, 2016
     **/
    public function setShowStatus () {
        if(IS_AJAX) {
            $id = I('post.id');
            $status = I('post.status');
            $res = D('Coupon')->setShowStatus($id,$status);
            if($res['is_show']) {
                $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$id);
            } else {
                $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
            }
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');    
        }
        action_log('set_coupon_status', 'coupon', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }


    /**
     * 通过owner_type获得角色类别的信息(嘻哈管理员登录)
     *
     * @return  void
     * @author  wl
     * @date    Oct 12, 2016 
     **/
    public function getManagerOwnerName () {
        $school_id = $this->getLoginauth();
        $type = I('post.type');
        $rolelist = D('Coupon')->getCouponOwnerInfo($type, $school_id);
        if (is_array($rolelist)) {
            $data = array('code' => 200, 'data' => $rolelist);
        } else {
            $data = array('code' => 200, 'data' => array());
        }
        exit(json_encode($data));
    }
    /**
     * 通过owner_type获得角色类别的信息(驾校登录登录)
     *
     * @return  void
     * @author  wl
     * @date    Oct 12, 2016 
     **/
    public function getSchoolOwnerName () {
        $school_id = $this->getLoginauth();
        $owner_type = I('post.owner_type');
        $rolelist = D('Coupon')->getCouponOwnerInfo($owner_type, $school_id);
        if (is_array($rolelist)) {
            $data = array('code' => 200, 'data' => $rolelist);
        } else {
            $data = array('code' => 200, 'data' => array());
        }
        exit(json_encode($data));
    }

   /**
     * 添加优惠卷
     *
     * @return  void
     * @author  wl
     * @date    Oct 11, 2016
     **/
    public function addCoupon () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Coupon/index'));
        }
        $school_id = $this->getLoginauth();
        if (IS_POST) {
            $post = I('post.');
            $data['owner_type']         = $post['owner_type'] ? $post['owner_type'] : '' ;
            $data['coupon_name']        = $post['coupon_name'] ? $post['coupon_name'] : '' ;
            $data['coupon_desc']        = $post['coupon_desc'] ? $post['coupon_desc'] : '' ;
            $data['owner_id']           = $post['owner_id'];
            $data['scene']              = $post['scene'] ? $post['scene'] : 1;
            $data['coupon_category_id'] = $post['coupon_category'] ? $post['coupon_category'] : 1;
            $data['coupon_total_num']   = abs(trim($post['coupon_total_num'])) ? abs(trim($post['coupon_total_num'])) : 50;
            $data['coupon_value']       = abs(trim($post['coupon_value'])) ? abs(trim($post['coupon_value'])) : '';
            $data['coupon_limit_num']   = abs(trim($post['coupon_limit_num'])) ? abs(trim($post['coupon_limit_num'])) : 10;
            $data['coupon_get_num']     = 0;
            $data['order']              = abs(trim($post['order'])) ? abs(trim($post['order'])) : 0;
            // $data['coupon_code']        = $post['coupon_code'] ? $post['coupon_code'] : '';
            $data['addtime']            = strtotime($post['addtime']) ? strtotime($post['addtime']) : '';
            $data['expiretime']         = strtotime($post['expiretime']) ? strtotime($post['expiretime']) : '';
            $data['province_id']        = $post['province'] ? $post['province'] : 0;
            $data['coupon_scope']       = $post['coupon_scope'] ? $post['coupon_scope'] : 0;
            $data['city_id']            = $post['city'] ? $post['city'] : 0;
            $data['area_id']            = $post['area'] ? $post['area'] : 0;
            $data['is_open']            = $post['is_open'] ? $post['is_open'] : 2; // 1：开启，2：未开启；默认2
            $data['is_show']            = $post['is_show'] ? $post['is_show'] : 0; // 1：展示，0：未展示；默认0
            if ($post['owner_type'] && $post['owner_id']) {
                $owner_type = $post['owner_type'];
                $owner_id   = $post['owner_id'];
                $owner_name = D('Coupon')->getOwnerNameByOwnerId($owner_type, $owner_id, $school_id);
                $data['owner_name'] = $owner_name['owner_name'];
            }
            // var_dump($data);echo '<hr>';
            $checkCouponTime = D('Coupon')->checkCouponTime($data['owner_type'], $data['owner_id'], $data['coupon_name']);
            if ($checkCouponTime === true) {
                $this->error('您还有优惠券未过期', U('Coupon/addCoupon'));
            }

            if ($data['expiretime'] <= $data['addtime']) {
                $this->error('添加时间大于过期时间', U('Coupon/addCoupon'));
            }

            // $coupon_code = D('Coupon')->couponCodeUnqiue($data['coupon_code']);
            // if (!empty($coupon_code)) {
            //     $this->error('此优惠码已经存在', U('Coupon/addCoupon'));
            // }

            if ($post['owner_type'] == '' && $post['owner_id'] == '') {
                $this->error('请完善信息', U('Coupon/addCoupon'));
            }

            $code_arr = explode(',', $post['coupon_code']);
            $coupon = D('coupon');
            $coupon_code = D('coupon_code');
            if ($res = $coupon->create($data)) {
                $result = $coupon->fetchSql(false)->add($res);
                foreach ($code_arr as $key => $value) {
                    $data_info['is_used'] = 0;
                    $data_info['addtime'] = time();
                    $data_info['coupon_id'] = $result;
                    $data_info['coupon_code'] = $value;
                    if ($re = $coupon_code->create($data_info)) {
                        $code_result = $coupon_code->add($re);
                    }
                }
                if ($result || $code_result) {
                    action_log('add_coupon', 'coupon/coupon_code', $result, $this->getLoginUserId());
                    $this->success('添加成功', U('Coupon/index'));
                } else {
                    action_log('add_coupon', 'coupon/coupon_code', $result, $this->getLoginUserId());
                    $this->error('添加失败', U('Coupon/addCoupon'));
                }
            } else {
                action_log('add_coupon', 'coupon/coupon_code', $result, $this->getLoginUserId());
                $this->error('添加失败', U('Coupon/addCoupon'));
            }

        } else {
            $coupon_category_name = D('Coupon')->getCouponCateName();
            $province_list = D('Province')->getProvinceList();
            $this->assign('province_list', $province_list);
            $this->assign('coupon_cate_name', $coupon_category_name);
            $this->assign('school_id', $school_id);
            $this->display('Coupon/addCoupon');
        }
    }


   /**
     * 修改优惠卷
     *
     * @return  void
     * @author  wl
     * @date    Oct 13, 2016
     **/
    public function editCoupon () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Coupon/index'));
        }
        $school_id = $this->getLoginauth();
        $id = I('param.id');
        $couponlist = D('Coupon')->getCouponListById($id);
        if (IS_POST) {
            $post = I('post.');
            $data['id'] = $post['id']; 
            $data['coupon_name']        = $post['coupon_name'] ? $post['coupon_name'] : '' ;
            $data['coupon_desc']        = $post['coupon_desc'] ? $post['coupon_desc'] : '' ;
            if ($couponlist['scene'] != '') {
                $data['scene'] = $post['scene'] == '' ? $couponlist['scene'] : $post['scene'];
            } else {
                $data['scene'] = $post['scene'] == '' ? 1 : $post['scene'];
            }
            $data['owner_type']         = $post['owner_type']; 
            $data['owner_id']           = $post['owner_id']; 
            $data['owner_name']         = $post['owner_name']; 
            $data['coupon_category_id'] = $post['coupon_category'] == '' ? $couponlist['coupon_category'] : $post['coupon_category']; 
            $data['coupon_total_num']   = abs($post['coupon_total_num']) == '' ? abs($couponlist['coupon_total_num']) : abs($post['coupon_total_num']); 
            $data['coupon_value']       = abs($post['coupon_value']) == '' ? abs($couponlist['coupon_value']) : abs($post['coupon_value']); 
            $data['coupon_limit_num']   = abs($post['coupon_limit_num']) == '' ? abs($couponlist['coupon_limit_num']) : abs($post['coupon_limit_num']); 
            $data['order']              = abs($post['order']) == '' ? abs($couponlist['order']) : abs($post['order']); 
            // $data['coupon_code']        = $post['coupon_code'] == '' ? $couponlist['coupon_code'] : $post['coupon_code']; 
            $data['addtime']            = strtotime($post['addtime']) == '' ? $couponlist['addtime'] : strtotime($post['addtime']); 
            $data['expiretime']         = strtotime($post['expiretime']) == '' ? $couponlist['expiretime'] : strtotime($post['expiretime']); 
            if (couponlist['coupon_scope'] != '') {
                $data['coupon_scope'] = $post['coupon_scope'] == '' ? $couponlist['coupon_scope'] : $post['coupon_scope'];
            } else {
                $data['coupon_scope'] = $post['coupon_scope'] == '' ? 0 : $post['coupon_scope'];
            }
            $data['province_id']        = $post['province'] == '' ? $couponlist['province_id'] : $post['province']; 
            $data['city_id']            = $post['city'] == '' ? $couponlist['city_id'] : $post['city']; 
            $data['area_id']            = $post['area'] == '' ? $couponlist['area_id'] : $post['area']; 
            $data['is_open']            = $post['is_open'] ? $post['is_open'] : 2; // 1：开启，2：未开启；默认2
            $data['is_show']            = $post['is_show'] ? $post['is_show'] : 0; // 1：展示，0：未展示；默认0
            $data['coupon_get_num']     = $couponlist['coupon_get_num'] == '' ? 0 : $couponlist['coupon_get_num'];
            $data['updatetime']         = time();
            if ($data['expiretime'] <= $data['addtime']) {
                $this->error('添加时间大于过期时间', U('Coupon/addCoupon'));
            }
            $coupon = D('coupon');
            $coupon_code = D('coupon_code');
            if ($post['coupon_code'] != '') {
                $code_arr = array();
                $code_arr = explode(',', $post['coupon_code']);
                foreach ($code_arr as $key => $value) {
                    $data_info['is_used'] = 0;
                    $data_info['addtime'] = time();
                    $data_info['coupon_id'] = $data['id'];
                    $data_info['coupon_code'] = $value;
                    if ($re = $coupon_code->create($data_info)) {
                        $code_result = $coupon_code->add($re);
                    }
                }
            }
            if ($res = $coupon->create($data)) {
                $result = $coupon->where(array('id' => $data['id']))->fetchSql(false)->save($res);
                if ($result) {
                    action_log('edit_coupon', 'coupon', $id, $this->getLoginUserId());
                    $this->success('修改成功', U('Coupon/index'));
                } else {
                    action_log('edit_coupon', 'coupon', $id, $this->getLoginUserId());
                    $this->error('未做任何修改', U('Coupon/editCoupon'));
                }
            } else {
                action_log('edit_coupon', 'coupon', $id, $this->getLoginUserId());
                $this->error('未做任何修改', U('Coupon/editCoupon'));
            }
        } else {
            $coupon_category_name = D('Coupon')->getCouponCateName();
            $province_list = D('Province')->getProvinceList();
            $this->assign('province_list', $province_list);
            $this->assign('school_id', $school_id);
            $this->assign('couponlist', $couponlist);
            $this->assign('coupon_cate_name', $coupon_category_name);
            $this->display('Coupon/editCoupon');
        }
    }

    /**
     * 删除优惠券
     *
     * @return  void
     * @author  wl
     * @date    Oct 11, 2016
     **/
    public function delCoupon () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Coupon/index'));
        }
        if(IS_AJAX) {
            $cid = I('post.id');
            $coupon = M('coupon');
            $res = $coupon->where(array('id' => $cid))
                ->fetchSql(false)
                ->delete(); 
            if ($res) {
                $data = array('code' => 200, 'msg' => '删除成功！', 'data' => '');
            } else {
                $data = array('code' => 400, 'msg' => '删除失败！', 'data' => '');
            }                   
        } else {
            $data = array('code' => 400, 'msg' => '删除失败！', 'data' => '');
        }
        action_log('del_coupon', 'coupon', $cid, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');

    }

    /**
    * 设置优惠券的排序状态
    *
    * @return  void
    * @author  wl
    * @date    Oct 11, 2016
    **/
    public function setCouponOrder () {
        if (!IS_AJAX || !IS_POST) {
            $msg = '参数错误';
            $data = '';
            $this->ajaxReturn(array('code' => 101, 'msg' => $msg, 'data' => $data), 'JSON');
        }

        $post = I('post.');
        $update_ok = D('Coupon')->updateCouponOrder($post);
        if ($update_ok == 101 || $update_ok == 102 || $update_ok == 105) {
            $code = $update_ok;
            $msg = '参数错误';
            $data = '';
            
        } elseif ($update_ok == 200) {
            $code = $update_ok;
            $msg = '更新成功';
            $data = '';

        } elseif ($update_ok == 400) {
            $code = $update_ok;
            $msg = '更新失败';
            $data = '';

        }
        action_log('set_coupon_order', 'coupon', $post['id'], $this->getLoginUserId());
        $this->ajaxReturn(array('code' => $code, 'msg' => $msg, 'data' => $data), 'JSON');
    }

    /**
     * 学车领取券表的展示
     *
     * @return  void
     * @author  wl
     * @date    Oct 13, 2016
     **/
    public function userCoupon () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $school_id = $this->getLoginauth();
        $userCouponList = D('Coupon')->getUserCouponList($school_id);
        $this->assign('role_id', $role_id);
        $this->assign('school_id', $school_id);
        $this->assign('page', $userCouponList['page']);
        $this->assign('count', $userCouponList['count']);
        $this->assign('usercouponlist', $userCouponList['usercouponlist']);
        $this->display('Coupon/userCoupon');
    }

    /**
     * 搜索领取学车券
     *
     * @return  void
     * @author  wl
     * @date    Oct 13, 2016
     **/
    public function searchUserCoupon () {
        $role_id = $this->getRoleId();
        $school_id = $this->getLoginauth();
        $param = I('param.');
        $coupon_type = trim((int)$param['coupon_type']);
        $coupon_status = trim((int)$param['coupon_status']);
        $search_info = trim((string)$param['search_info']);
        $s_keyword = trim((string)$param['s_keyword']);
        if ( $s_keyword == '' && $search_info == '' && $coupon_type  == '' && $coupon_status  == '') {
            $this->redirect('Coupon/userCoupon');
        } else {
            $userCouponList = D('Coupon')->searchUserCoupon($param, $school_id);
            $this->assign('role_id', $role_id);
            $this->assign('school_id', $school_id);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('coupon_type', $coupon_type);
            $this->assign('search_info', $search_info);
            $this->assign('coupon_status', $coupon_status);
            $this->assign('page', $userCouponList['page']);
            $this->assign('count', $userCouponList['count']);
            $this->assign('usercouponlist', $userCouponList['usercouponlist']);
            $this->display('Coupon/userCoupon');
        }
    }

    /**
     * 删除学车券
     *
     * @return  void
     * @author  wl
     * @date    Oct 13, 2016
     **/
    public function delUserCoupon () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Coupon/userCoupon'));
        }
        if(IS_AJAX) {
            $cid = I('post.id');
            $coupon = M('user_coupon');
            $res = $coupon->where(array('id' => $cid))
                ->fetchSql(false)
                ->save(array('coupon_status' => 4)); 
            if ($res) {
                $data = array('code' => 200, 'msg' => '删除成功！', 'data' => '');
            } else {
                $data = array('code' => 400, 'msg' => '删除失败！', 'data' => '');
            }                   
        } else {
            $data = array('code' => 400, 'msg' => '删除失败！', 'data' => '');
        }
        action_log('del_user_coupon', 'user_coupon', $cid, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }


    /**
     * 获取城市
     *
     * @return 
     * @author sun
     **/
    public function getCity() {
        $province_id = I('param.province_id');
        $city_list = D('City')->getCityList($province_id);
        // $html = "<option value=''>请选择市</option>";
        $html = "";
        foreach ($city_list as $key => $value) {
            $html .= "<option value='".$value['cityid']."'>".$value['city']."</option>";
        }
        echo $html;
    }

    /**
     * 获取区域
     *
     * @return 
     * @author sun
     **/
    public function getArea() {
        $city_id = I('param.city_id');
        // $this->ajaxReturn($city_id, 'JSON');
        $area_list = D('Area')->getAreaList($city_id);
        // $html = "<option value=''>请选择区域</option>";
        $html = "";
        foreach ($area_list as $key => $value) {
            $html .= "<option value='".$value['areaid']."'>".$value['area']."</option>";
        }
        echo $html;
    }

    // 生成单条兑换码
    public function createSignalCode () {
        $code = guid(false);
        $coupon_code = substr($code, -6, 6);
        $data = array('code' => -1, 'data' => $coupon_code);
        $this->ajaxReturn($data, 'JSON');
    }

    // 生成多条兑换码
    public function createCode () {
        if (IS_AJAX) {
            $post = I('post.');
            $code_num = $post['code_num'];
            $i = 0;
            $code_arr = array();
            for (;$i < $code_num; $i++) {
                $code = guid(false);
                $code_arr[$i] = substr($code, -6, 6);
            }
            if (!empty($code_arr)) {
                $coupon_code = implode(',', $code_arr);
            } else {
                $coupon_code = '';
            }
            $data = array('code'=>-1, 'data'=>$coupon_code);
        }
        $this->ajaxReturn($data, 'JSON');
    }


}

