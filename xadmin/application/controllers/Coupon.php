<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 优惠券类
class Coupon extends CI_Controller {

    static $limit = 10;
    static $page = 1;
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('mbase');
        $this->load->model('mschool');
        $this->load->model('mcity');
        $this->load->model('mtags');
        $this->load->model('mcoupon');
        $this->load->model('mcouponcode');
        $this->load->helper(['form', 'url', 'common']);
        $this->load->library('form_validation');
    }

    // 首页列表
    public function index()
    {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $data = ['school_id' => $school_id];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/coupon/index', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    // 获取全部优惠券列表
    public function couponListAjax()
    {
        $this->mbase->loginauth();
        if( ! $this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $param = [];
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $page = $this->input->post('p', true) ? intval($this->input->post('p', true)) : self::$page;
        $limit = $this->input->post('s', true) ? intval($this->input->post('s', true)) : self::$limit;

        $param['type'] = $this->input->post('type', true) ? trim($this->input->post('type', true)) : '';
        $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';
        $pageinfo = $this->mcoupon->getCouponPageNum($school_id, $param, $limit);
        $page = $page < $pageinfo['pagenum'] || $pageinfo['pagenum'] == 0 ? $page : $pageinfo['pagenum'];
        $start = ($page - 1) * $limit;
        $couponlist = $this->mcoupon->getCouponList($school_id, $param, $start, $limit);
        $list['p'] = $page;
        $list['count'] = $pageinfo['count'];
        $list['pagenum'] = $pageinfo['pagenum'];
        $list['list'] = $couponlist;
        $data = ['code' => 200, 'msg' => '获取成功', 'data' => $list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }

    /**
     * 添加优惠券[页面展示]
     *
     * @return  void
     **/
    public function add()
    {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $data = ['school_id' => $school_id];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/coupon/add', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 添加优惠券[功能实现]
     * 
     * @return  void
     **/
    public function addCouponAjax()
    {
        $this->mbase->loginauth();
        if( ! $this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $param = [];
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $param['coupon_name'] = $this->input->post('coupon_name', true) ? trim($this->input->post('coupon_name', true)) : '';
        $param['owner_id'] = $this->input->post('owner_id', true) ? intval($this->input->post('owner_id', true)) : '';
        $param['owner_type'] = $this->input->post('owner_type', true) ? intval($this->input->post('owner_type', true)) : '';
        $param['scene'] = $this->input->post('scene', true) ? intval($this->input->post('scene', true)) : 1; // 默认报名班制
        $param['coupon_category_id'] = $this->input->post('coupon_category_id', true) ? intval($this->input->post('coupon_category_id', true)) : 1; // 默认现金券
        $param['coupon_value'] = $this->input->post('coupon_value', true) ? trim($this->input->post('coupon_value', true)) : ''; 
        $param['coupon_total_num'] = $this->input->post('code_num', true) ? intval($this->input->post('code_num', true)) : 1; 
        $param['coupon_limit_num'] = $this->input->post('coupon_limit_num', true) ? intval($this->input->post('coupon_limit_num', true)) : 1; 
        $param['expiretime'] = $this->input->post('expiretime', true) ? trim($this->input->post('expiretime', true)) : ''; 
        $param['is_open'] = $this->input->post('is_open', true) ? intval($this->input->post('is_open', true)) : 1; 
        $param['is_show'] = $this->input->post('is_show', true) ? intval($this->input->post('is_show', true)) : 1; 
        $param['order'] = $this->input->post('order', true) ? intval($this->input->post('order', true)) : 50; 
        $param['coupon_scope'] = $this->input->post('coupon_scope', true) ? trim($this->input->post('coupon_scope', true)) : '0'; 
        $param['coupon_desc'] = $this->input->post('coupon_desc', true) ? trim($this->input->post('coupon_desc', true)) : ''; 
        $param['province_id'] = $this->input->post('province_id', true) ? intval($this->input->post('province_id', true)) : 0; 
        $param['city_id'] = $this->input->post('city_id', true) ? intval($this->input->post('city_id', true)) : 0; 
        $param['area_id'] = $this->input->post('area_id', true) ? intval($this->input->post('area_id', true)) : 0; 
        $param['coupon_get_num'] = 0;
        $coupon_code_list = $this->input->post('coupon_code_list', true) ? trim($this->input->post('coupon_code_list', true)) : ''; 
        
        if ($param['owner_type'] && $param['owner_id']) {
            $owner_type = $param['owner_type'];
            $owner_id   = $param['owner_id'];
            $owner_name = $this->mcoupon->getOwnerNameByOwnerId($owner_type, $owner_id);
            $param['owner_name'] = $owner_name['owner_name'];
        }

        $checkCouponTime = $this->mcoupon->checkCouponTime($param['owner_type'], $param['owner_id'], $param['coupon_name']);
        if ($checkCouponTime === true) {
            $data = ['code' => 400, 'msg' => '您还有优惠券未过期', 'data' => ''];
        }
        
        $code_results = 0;
        $code_arr = explode(',', $coupon_code_list);
        $result = $this->mcoupon->create($param);
        if ($result) {
            if($code_arr) {
                foreach ($code_arr as $key => $value) {
                    $data_info['is_used'] = 0;
                    $data_info['addtime'] = time();
                    $data_info['coupon_id'] = $result;
                    $data_info['coupon_code'] = $value;
                    $code_result = $this->mcouponcode->create($data_info);
                    if($code_result) {
                        $code_results ++;
                    }
                }
            }
            if ($result || $code_result) {
                $data = ['code'=>200, 'msg'=>'添加成功', 'data'=>[]];
            } else {
                $data = ['code'=>400, 'msg'=>'添加失败', 'data'=>[]];
            }
        } else {
            $data = ['code'=>400, 'msg'=>'添加失败', 'data'=>[]];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }
    
    /**
     * 编辑优惠券页面[页面展示]
     *
     * @return  void
     **/
     public function edit()
     {
         $this->mbase->loginauth();
         $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
         $id = $this->input->get('id') ? $this->input->get('id') : 0;
         $data = $this->mcoupon->getCouponListById($id);
         // echo "<pre>";
         // var_dump($data);exit;
         $data['sid'] = $school_id;
         if( ! $data) show_404();
         $this->load->view(TEMPLATE.'/header');
         $this->load->view(TEMPLATE.'/coupon/edit', $data);
         $this->load->view(TEMPLATE.'/footer');
     }
 
     /**
      * 编辑优惠券页面[功能实现]
      *
      * @return  void
      **/
     public function editCouponAjax()
     {
        $this->mbase->loginauth();
        if( ! $this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        $param = [];
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $param['id'] = $this->input->post('id', true) ? trim($this->input->post('id', true)) : 0;
        $coupon_list = $this->mcoupon->getCouponListById($param['id']);
        
        $param['coupon_name'] = $this->input->post('coupon_name', true) ? trim($this->input->post('coupon_name', true)) : $coupon_list['coupon_name'];
        $param['owner_id'] = $this->input->post('owner_id', true) ? intval($this->input->post('owner_id', true)) : $coupon_list['owner_id'];
        $param['owner_type'] = $this->input->post('owner_type', true) ? intval($this->input->post('owner_type', true)) : $coupon_list['owner_type'];
        $param['scene'] = $this->input->post('scene', true) ? intval($this->input->post('scene', true)) : $coupon_list['scene']; // 默认报名班制
        $param['coupon_category_id'] = $this->input->post('coupon_category_id', true) ? intval($this->input->post('coupon_category_id', true)) : $coupon_list['coupon_category_id']; // 默认现金券
        $param['coupon_value'] = $this->input->post('coupon_value', true) ? trim($this->input->post('coupon_value', true)) : $coupon_list['coupon_value']; 
        $param['coupon_total_num'] = $this->input->post('code_num', true) ? intval($this->input->post('code_num', true)) : $coupon_list['code_num']; 
        $param['coupon_limit_num'] = $this->input->post('coupon_limit_num', true) ? intval($this->input->post('coupon_limit_num', true)) : $coupon_list['coupon_limit_num']; 
        $expiretime = $this->input->post('expiretime', true) ? trim($this->input->post('expiretime', true)) : 0; 
        if (strlen($expiretime) <= 14) {
            $param['expiretime'] = $expiretime;
        } else {
            $param['expiretime'] = strtotime($expiretime);
        }
        $param['is_open'] = $this->input->post('is_open', true) ? intval($this->input->post('is_open', true)) : 1; 
        $param['is_show'] = $this->input->post('is_show', true) ? intval($this->input->post('is_show', true)) : 1; 
        $param['order'] = $this->input->post('order', true) ? intval($this->input->post('order', true)) : 50; 
        $param['coupon_scope'] = $this->input->post('coupon_scope', true) ? trim($this->input->post('coupon_scope', true)) : $coupon_list['coupon_scope']; 
        $param['coupon_desc'] = $this->input->post('coupon_desc', true) ? trim($this->input->post('coupon_desc', true)) : $coupon_list['coupon_desc']; 
        $param['province_id'] = $this->input->post('province_id', true) ? intval($this->input->post('province_id', true)) : $coupon_list['province_id']; 
        $param['city_id'] = $this->input->post('city_id', true) ? intval($this->input->post('city_id', true)) : $coupon_list['province_id']; 
        $param['area_id'] = $this->input->post('area_id', true) ? intval($this->input->post('area_id', true)) : $coupon_list['province_id']; 
        $param['coupon_get_num'] = $coupon_list['coupon_get_num'];
        $coupon_code_list = $this->input->post('coupon_code_list', true) ? trim($this->input->post('coupon_code_list', true)) : '';
        
        $param['owner_name'] = $this->input->post('owner_name', true) ? trim($this->input->post('owner_name', true)) : $coupon_list['owner_name'];
        if ($param['expiretime'] <= time()) {
            $json = ['code' => 200, 'msg' => '添加时间大于过期时间', 'data' => ''];
        }
        $code_arr = explode(',', $coupon_code_list);
        if ($coupon_code_list != '') {
            $code_arr = array();
            $code_arr = explode(',', $coupon_code_list);
            foreach ($code_arr as $key => $value) {
                $data_info['is_used'] = 0;
                $data_info['addtime'] = time();
                $data_info['coupon_id'] = $param['id'];
                $data_info['coupon_code'] = $value;
                $res = $this->mcouponcode->create($data_info);
            }
        }
        $result = $this->mbase->_insert($this->mcoupon->coupon_tbl, $param, ['id' => $param['id']]);
        if ($result) {
            $data = ['code' => 200, 'msg' => '修改成功', 'data' => $result];
        } else {
            $data = ['code' => 400, 'msg' => '添加失败', 'data' => $result];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
     }

    /**
     * 获取发券者的信息
     * 
     * @return  void
     **/
    public function getOwnerAjax()
    {
        $this->mbase->loginauth();
        if( ! $this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        
        $type = $this->input->post('type', true) ? intval($this->input->post('type', true)) : '';
        $key = $this->input->post('key') ? trim($this->input->post('key')) : '';
        $school_id = $this->input->post('school_id') ? trim($this->input->post('school_id')) : '';
        if ( $type == 1) { // 教练
            $result = $this->mcoupon->searchCoachList($school_id, $key, 20);
        } elseif ( $type == 2) { // 驾校
            $where = ['is_show' => 1];
            if ( $school_id != 0) {
                $where = ['l_school_id' => $school_id, 'is_show' => 1];
            }
            $result = $this->mbase->getSearchList('l_school_id as owner_id, s_school_name as owner_name', $where, ['s_school_name'=>$key], $this->mschool->school_tablename, 20);

        } else {
            $result = ["list" => ['owner_id' => 0, 'owner_name' => '嘻哈平台'],];
        }

        if($result) {
            $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$result];
        } else {
            $data = ['code'=>400, 'msg'=>'获取失败', 'data'=>$result];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    //  设置优惠券展示与否
    public function setShowAjax()
    {
        $this->mbase->loginauth();
        if( ! $this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $params = $this->input->post();
        $where = array('id'=>$params['id']);
        $_data = array('is_show'=>$params['status']);
        $res = $this->mcoupon->editCouponStatus($where, $_data);
        if($res) {
            $data = ['code'=>200, 'msg'=>'设置成功', 'data'=>['result'=>$res]];
        } else {
            $data = ['code'=>100, 'msg'=>'设置失败', 'data'=>['result'=>$res]];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }

    //  设置优惠券开启与否
    public function setOpenAjax()
    {
        $this->mbase->loginauth();
        if( ! $this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $params = $this->input->post();
        $where = array('id'=>$params['id']);
        $_data = array('is_open'=>$params['status']);
        $res = $this->mcoupon->editCouponStatus($where, $_data);
        if($res) {
            $data = ['code'=>200, 'msg'=>'设置成功', 'data'=>['result'=>$res]];
        } else {
            $data = ['code'=>100, 'msg'=>'设置失败', 'data'=>['result'=>$res]];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }

    //  删除优惠券
    public function delAjax()
    {
        $this->mbase->loginauth();
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                ->_display();
            exit;
        }
        $id = $this->input->post('id') ? intval($this->input->post('id')) : 0;
        if($id == '') {
            $data = ['code'=>400, 'msg'=>'参数错误', 'data'=>[]];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                ->_display();
            exit;
        }
        $params = ['id'=> $id];
        $res = $this->mcoupon->delInfo($params);
        if($res) {
            $data = ['code'=>200, 'msg'=>'删除成功', 'data'=>[]];
        } else {
            $data = ['code'=>200, 'msg'=>'删除失败', 'data'=>[]];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }

    // 获取券信息
    public function couponInfoAjax()
    {
        $id = $this->input->post('id') ? intval($this->input->post('id')) : 0;
        $coupon_info = $this->mcoupon->getCouponParamsInfo('id, coupon_name', $id);
        $data = ['code'=>200, 'msg'=>'获取优惠券详情成功', 'data'=>$coupon_info];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }

    //  根据角色类别获取角色列表
    public function getManagerOwnerName()
    {
        $type = intval($this->input->post('type'));
        $rolelist = $this->mcoupon->getCouponOwnerInfo($type);
        if (is_array($rolelist)) {
            $data = array('code' => 200,'msg'=>'获取成功', 'data' => $rolelist);
        } else {
            $data = array('code' => 200, 'msg'=>'获取失败', 'data' => array());
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }

// 2.券兑换码列表
    /**
     * 获取券兑换码列表[页面展示]
     * 
     * @return  void
     **/
    public function code()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/coupon/code');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 获取券兑换码列表[数据获取]
     * @param   array   $param  条件
     * @param   int     $limit  限定数目
     * @return  void
     **/
    public function codeListAjax()
    {
        $this->mbase->loginauth();
        if( ! $this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        $param = [];
        $page = $this->input->post('p', true) ? intval($this->input->post('p', true)) : self::$page;
        $limit = $this->input->post('s', true) ? intval($this->input->post('s', true)) : self::$limit;

        $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';
        $pageinfo = $this->mcoupon->getCouponCodePageNum($param, $limit);
        $page = $page < $pageinfo['pagenum'] || $pageinfo['pagenum'] == 0 ? $page : $pageinfo['pagenum'];
        $start = ($page - 1) * $limit;
        $usercouponlist = $this->mcoupon->getCouponCodeList($param, $start, $limit);
        $list['p'] = $page;
        $list['count'] = $pageinfo['count'];
        $list['pagenum'] = $pageinfo['pagenum'];
        $list['list'] = $usercouponlist;
        $data = ['code' => 200, 'msg' => '获取成功', 'data' => $list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }
    
    /**
     * 修改券兑换码[页面展示]
     *
     * @return  void
     **/
    public  function editCode()
    {
        $this->mbase->loginauth();
        $id = $this->input->get('id') ? $this->input->get('id') : 0;
        $data = $this->mcoupon->getCouponCodeById($id);
        if( ! $data) show_404();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/coupon/editCode', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 修改券兑换码[功能实现]
     * @param   array   $param
     *
     * @return  void
     **/
    public function editCodeAjax()
    {
        $this->mbase->loginauth();
        if( ! $this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        $param = [];
        $param['id'] = $this->input->post('id', true) ? intval($this->input->post('id', true)) : '';
        $param['coupon_code'] = $this->input->post('coupon_code', true) ? trim($this->input->post('coupon_code', true)) : '';
        $result = $this->mcouponcode->update($param['id'], $param);
        if ($result) {
            $data = ['code' => 200, 'msg' => '修改成功', 'data' => $result];
        } else {
            $data = ['code' => 400, 'msg' => '修改失败', 'data' => $result];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    // 生成多条兑换码
    public function createCode () {
        $this->mbase->loginauth();
        if( ! $this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $code_num = $this->input->post('code_num', true) ? intval($this->input->post('code_num', true)) : 1; 
        $i = 0;
        $code_arr = [];
        for (;$i < $code_num; $i++) {
            $code = guid(false);
            $code_arr[$i] = substr($code, -6, 6);
        }

        if ( ! empty($code_arr)) {
            $coupon_code = implode(',', $code_arr);
        } else {
            $coupon_code = '';
        }

        if( ! $coupon_code) {
            $data = array('code'=>200, 'msg'=>'生成失败', 'data'=>'');
        }else {
            $data = array('code'=>200, 'msg'=>'生成成功', 'data'=>$coupon_code);
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }

    // 生成单条兑换码
    public function createSignalCode () {
        $code = guid(false);
        $coupon_code = substr($code, -6, 6);
        if($coupon_code) {
            $data = array('code'=>200, 'msg'=>'生成成功', 'data'=>$coupon_code);
        }else {
            $data = array('code'=>200, 'msg'=>'生成失败', 'data'=>'');
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }

    //  删除兑换券
    public function delCode()
    {
        $this->mbase->loginauth();
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                ->_display();
            exit;
        }
        $id = $this->input->post('id') ? intval($this->input->post('id')) : 0;
        if($id == '') {
            $data = ['code'=>400, 'msg'=>'参数错误', 'data'=>[]];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                ->_display();
            exit;
        }
        $params = ['id'=> $id];
        $res = $this->mcouponcode->delInfo($params);
        if($res) {
            $data = ['code'=>200, 'msg'=>'删除成功', 'data'=>[]];
        } else {
            $data = ['code'=>200, 'msg'=>'删除失败', 'data'=>[]];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }

    //  获取优惠券相关信息
    public function getCouponName()
    {
        $name = $this->mcoupon->getCouponName();
        if($name) {
            $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$name];
        }else {
            $data = ['code'=>200, 'msg'=>'获取失败', 'data'=>array()];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }

// 3.券种类列表
    //  优惠券种类列表
    public function cate()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/coupon/cate');
        $this->load->view(TEMPLATE.'/footer');
    }

    //  ajax加载优惠券种类列表
    public function cateAjax()
    {
        $this->mbase->loginauth();
        if( ! $this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $param = array();
        $param['value'] = $this->input->post('value',true);
        $page = $this->input->post('p') ? intval($this->input->post('p')) : self::$page;
        $limit = $this->input->post('s') ? intval($this->input->post('s')) : self::$limit;

        $pageinfo = $this->mcoupon->getPageNumC($param, $limit);
        $page = $page < $pageinfo['pn'] || $pageinfo['pn'] == 0 ? $page : $pageinfo['pn'];
        $start = ($page - 1) * $limit;
        $items = $this->mcoupon->getCouponCateList($param, $start, $limit);
        $list['p'] = $page;
        $list['pagenum'] = $pageinfo['pn'];
        $list['count'] = $pageinfo['count'];
        $list['list'] = $items['items'];
        $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }

    //  添加优惠券种类
    public function addCate()
    {
        if($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('cate_name', '', 'required', ['required' => '请填写券种类的名称']);
            $this->form_validation->set_rules('cate_desc', '', 'required', ['required' => '请填写券的种类描述']);
            $this->form_validation->set_rules('coupon_rule', '', 'required', ['required' => '请填写券的规则描述']);
            if ($this->form_validation->run() === FALSE) {
                $errors = $this->form_validation->error_array();
                if (isset($errors[array_keys($errors)[0]])){
                    $json = ['code' => 400, 'msg' => $errors[array_keys($errors)[0]], 'data' => new \stdClass()];
                }
                else{
                    $json = ['code' => 400, 'msg' => '表单未通过安全检测', 'data' => new \stdClass()];
                }
            }else {
                $data = $this->input->post(NULL,TRUE);
                if($id = $this->mcoupon->addCouponCate($data)){
                    $json = array('code'=>200,'msg'=>'添加成功','data'=>$id);
                }
            }
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($json, JSON_UNESCAPED_UNICODE))
                ->_display();
            exit;
        }else {
            $this->load->view(TEMPLATE.'/header');
            $this->load->view(TEMPLATE.'/coupon/addCate');
            $this->load->view(TEMPLATE.'/footer');
        }
    }

    //  编辑优惠券种类
    public function editCate()
    {
        if($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('cate_name', '', 'required', ['required' => '请填写券种类的名称']);
            $this->form_validation->set_rules('cate_desc', '', 'required', ['required' => '请填写券的种类描述']);
            $this->form_validation->set_rules('coupon_rule', '', 'required', ['required' => '请填写券的规则描述']);
            if ($this->form_validation->run() === FALSE) {
                $errors = $this->form_validation->error_array();
                if (isset($errors[array_keys($errors)[0]])){
                    $json = ['code' => 400, 'msg' => $errors[array_keys($errors)[0]], 'data' => new \stdClass()];
                }
                else{
                    $json = ['code' => 400, 'msg' => '表单未通过安全检测', 'data' => new \stdClass()];
                }
            }else {
                if(!$data = $this->input->post(NULL,TRUE)){
                    $json = array('code'=>400,'msg'=>'参数错误','data'=>'');
                }else if(!$data['id']) {
                    $json = array('code'=>400,'msg'=>'未指定ID','data'=>'');
                }else if(!$detail = $this->mcoupon->cateDetail($data['id'])){
                    $json = array('code'=>400,'msg'=>'要编辑的内容不存在','data'=>'');
                }else if($res = $this->mcoupon->editCouponCate($data['id'],$data)){
                    $json = array('code'=>200,'msg'=>'编辑成功','data'=>$res);
                }else {
                    $json = array('code'=>200,'msg'=>'编辑失败','data'=>'');
                }
            }
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($json, JSON_UNESCAPED_UNICODE))
                ->_display();
            exit;
        }else {
            $id = $this->input->get('id') ? $this->input->get('id') : 0;
            $detail = $this->mcoupon->cateDetail($id);
            $this->load->view(TEMPLATE.'/header');
            $this->load->view(TEMPLATE.'/coupon/editCate', $detail);
            $this->load->view(TEMPLATE.'/footer');
        }
    }

    //  删除优惠券种类
    public function delCate()
    {
        if(!$this->input->is_ajax_request()) {
            $json = array('code'=>400,'msg'=>'错误请求方式','data'=>'');
        }else if(!$id = (int)$this->input->post('id')){
            $json = array('code'=>400,'msg'=>'参数错误','data'=>'');
        }else if(!$detail = $this->mcoupon->cateDetail($id)) {
            $json = array('code'=>400,'msg'=>'要删除的内容不存在');
        }else if($rlt = $this->mcoupon->delCate($id)) {
            $json = array('code'=>200,'msg'=>'删除成功','data'=>'');
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($json, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }

// 4.用户领券记录表
    /**
     * 用户领取记录列表[页面展示]
     *
     * @return  void
     **/
    public function userCoupon()
    {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $data = ['school_id' => $school_id];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/coupon/userCoupon', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    //  用户领取记录ajax请求
    public function userCouponAjax()
    {
        $this->mbase->loginauth();
        if( ! $this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $param = [];
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $page = $this->input->post('p', true) ? intval($this->input->post('p', true)) : self::$page;
        $limit = $this->input->post('s', true) ? intval($this->input->post('s', true)) : self::$limit;
        $param['type'] = $this->input->post('type', true) ? trim($this->input->post('type', true)) : '';
        $param['status'] = $this->input->post('status', true) ? intval($this->input->post('status', true)) : '';
        $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';
        $pageinfo = $this->mcoupon->getUserCouponPageNum($school_id, $param, $limit);
        
        $page = $page < $pageinfo['pagenum'] || $pageinfo['pagenum'] == 0 ? $page : $pageinfo['pagenum'];
        $start = ($page - 1) * $limit;
        $usercouponlist = $this->mcoupon->getUserCouponList($school_id, $param, $start, $limit);
        $list['p'] = $page;
        $list['count'] = $pageinfo['count'];
        $list['pagenum'] = $pageinfo['pagenum'];
        $list['list'] = $usercouponlist;
        $data = ['code' => 200, 'msg' => '获取成功', 'data' => $list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }

    //  删除用户领券记录
    public function delUserCoupon()
    {
        if(!$this->input->is_ajax_request()) {
            $json = array('code'=>400, 'msg'=>'错误请求方式', 'data'=>'');
        }else if(!$id = (int)$this->input->post('id')) {
            $json = array('code'=>400, 'msg'=>'未指定ID', 'data'=>'');
        }else if(!$detail = $this->mcoupon->userCouponDetail($id)) {
            $json = array('code'=>400, 'msg'=>'要删除的内容不存在', 'data'=>'');
        }else if($rlt = $this->mcoupon->delUserCoupon($id)) {
            $json = array('code'=>200, 'msg'=>'删除成功', 'data'=>$rlt);
        }
        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($json, JSON_UNESCAPED_UNICODE))
                    ->_display();
        exit;
    }



}
