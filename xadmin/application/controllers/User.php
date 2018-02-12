<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 用户类
class User extends CI_Controller {

    static $limit = 10;
    static $page = 1;
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('mbase');
        $this->load->model('muser');
        $this->load->model('mcity');
        $this->load->model('mtags');
        $this->load->model('mcoupon');
    }

    // 首页列表
    public function index()
    {   
        $this->mbase->loginauth();
        $page = $this->input->get('p') ? intval($this->input->get('p')) : self::$page;
        $pape = $page == 0 ? self::$page : $page;
        $wherecondition = ['i_user_type'=>0];
        $pageinfo = $this->mbase->getPageNumByCondition($this->muser->user_tablename, $wherecondition, self::$limit);
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/user/index', array('p'=>$page, 'pagenum'=>$pageinfo['pn'], 'count'=>$pageinfo['count']));
        $this->load->view(TEMPLATE.'/footer');
    }

    // 添加
    public function add() {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/user/add');
        $this->load->view(TEMPLATE.'/footer');
    }
    
    // 添加
    public function edit() {
        $this->mbase->loginauth();
        $id = $this->input->get('id') ? $this->input->get('id') : 0;
        $user_info = $this->muser->getUserInfoByCondition($this->muser->user_tablename, ['l_user_id'=>$id]);
        $users_info = [];
        if($user_info) {
            $users_info = $this->muser->getUserInfoByCondition($this->muser->userinfo_tablename, ['user_id'=>$user_info['l_user_id']]);
        }
        $data = $users_info ? array_merge($user_info, $users_info) : $user_info;
        // if(!$data) show_404();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/user/edit', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    // 列表分页ajax
    public function listajax()
    {
        $this->mbase->loginauth();
        $page = $this->input->post('p') ? intval($this->input->post('p')) : self::$page;
        $wherecondition = ['i_user_type'=>0];
        $pageinfo = $this->mbase->getPageNumByCondition($this->muser->user_tablename, $wherecondition, self::$limit);
        $page = $page < $pageinfo['pn'] || $pageinfo['pn'] == 0 ? $page : $pageinfo['pn'];        
        $start = ($page - self::$page) * self::$limit;
        $wherecondition = ['i_user_type'=>0];
        $userinfo = $this->muser->getUserList($wherecondition, $start, self::$limit);
        $userinfo['pagenum'] = $pageinfo['pn'];
        $userinfo['count'] = $pageinfo['count'];
        $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$userinfo];
        // sleep(1);
        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
        exit;
    }

    // 添加ajax
    public function addajax()
    {
        $this->mbase->loginauth();
        $params = $this->input->post();
        $user_data = [
            's_username'=> isset($params['s_username']) ? $params['s_username'] : '',
            's_password'=> isset($params['s_password']) ? md5($params['s_password']) : '',
            'i_user_type'=> 0,
            'i_status'=> 0,
            's_real_name'=> isset($params['s_real_name']) ? $params['s_real_name'] : '',
            'l_yw_incode'=> 0,
            'i_from'=> 0,
            's_phone'=> isset($params['s_phone']) ? $params['s_phone'] : '',
            'is_first'=> 0,
            's_imgurl'=> '',
            'content'=> '',
            'coach_id'=> 0,
            'addtime'=> time(),
            'updatetime'=> time(),
        ];
        $users_info_data = [
            'user_id'=> isset($params['user_id']) ? $params['user_id'] : 0,
            'x'=> 0,
            'y'=> 0,
            'sex'=> isset($params['sex']) ? $params['sex'] : 0,
            'age'=> isset($params['age']) ? $params['age'] : 16,
            'identity_id'=> isset($params['identity_id']) ? $params['identity_id'] : '',
            'address'=> isset($params['address']) ? $params['address'] : '',
            'user_photo'=> isset($params['user_photo']) ? $params['user_photo'] : '',
            'license_num'=> isset($params['license_num']) ? $params['license_num'] : 0,
            'school_id'=> isset($params['school_id']) ? $params['school_id'] : 0,
            'lesson_id'=> isset($params['lesson_info']) ? (explode('|', $params['lesson_info'])[0] ? explode('|', $params['lesson_info'])[0] : 0) : 0,
            'lesson_name'=> isset($params['lesson_info']) ? (explode('|', $params['lesson_info'])[1] ? explode('|', $params['lesson_info'])[1] : '') : '',
            'license_id'=> isset($params['license_info']) ? (explode('|', $params['license_info'])[0] ? explode('|', $params['license_info'])[0] : 0) : 0,
            'license_name'=> isset($params['license_info']) ? (explode('|', $params['license_info'])[1] ? explode('|', $params['license_info'])[1] : '') : '',
            'exam_license_name'=> isset($params['license_info']) ? (explode('|', $params['license_info'])[1] ? explode('|', $params['license_info'])[1] : '') : '',
            'balance'=> isset($params['balance']) ? $params['balance'] : 0,
            'is_paypass_activated'=> isset($params['is_paypass_activated']) ? ($params['is_paypass_activated'] === 'true' ? 1 : 2) : 2,
            'wallet_access_pass'=> isset($params['wallet_access_pass']) ? $params['wallet_access_pass'] : '',
            'pay_pass'=> isset($params['pay_pass']) ? $params['pay_pass'] : '',
            'xiha_coin'=> isset($params['xiha_coin']) ? $params['xiha_coin'] : 0,
            'signin_num'=> isset($params['signin_num']) ? $params['signin_num'] : 0,
            'signin_lasttime'=> isset($params['signin_lasttime']) ? $params['signin_lasttime'] : 0,
            'province_id'=> isset($params['province_id']) ? $params['province_id'] : '',
            'city_id'=> isset($params['city_id']) ? $params['city_id'] : '',
            'area_id'=> isset($params['area_id']) ? $params['area_id'] : '',
            'photo_id'=> rand(1, 16),
            'learncar_status'=> isset($params['learncar_status']) ? $params['learncar_status'] : '',
        ];

        // 判断手机号是否重复
        $wherecondition = [
            's_phone' => $user_data['s_phone']
        ];
        $user_info = $this->muser->getUserInfoByCondition($this->muser->user_tablename, $wherecondition);
        if($user_info) {
            $data = ['code'=>100, 'msg'=>'用户已存在', 'data'=>''];
            $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
            exit;
        }
        $result = $this->muser->addUser($user_data, $users_info_data);
        if($result) {
            $data = ['code'=>200, 'msg'=>'添加成功', 'data'=>['result'=>$result]];
        } else {
            $data = ['code'=>100, 'msg'=>'添加失败', 'data'=>['result'=>$result]];            
        }
        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
        exit;
    }

    // 编辑ajax
    public function editajax()
    {
        $this->mbase->loginauth();
        $params = $this->input->post();
        $user_data = [
            'l_user_id'=> isset($params['l_user_id']) ? $params['l_user_id'] : 0,
            's_username'=> isset($params['s_username']) ? $params['s_username'] : '',
            'i_user_type'=> 0,
            'i_status'=> 0,
            's_real_name'=> isset($params['s_real_name']) ? $params['s_real_name'] : '',
            'l_yw_incode'=> 0,
            'i_from'=> 0,
            's_phone'=> isset($params['s_phone']) ? $params['s_phone'] : '',
            'is_first'=> 0,
            's_imgurl'=> '',
            'content'=> '',
            'coach_id'=> 0,
            'addtime'=> time(),
            'updatetime'=> time(),
        ];
        $users_info_data = [
            'x'=> 0,
            'y'=> 0,
            'sex'=> isset($params['sex']) ? $params['sex'] : 0,
            'age'=> isset($params['age']) ? $params['age'] : 16,
            'identity_id'=> isset($params['identity_id']) ? $params['identity_id'] : '',
            'address'=> isset($params['address']) ? $params['address'] : '',
            'user_photo'=> isset($params['user_photo']) ? $params['user_photo'] : '',
            'license_num'=> isset($params['license_num']) ? $params['license_num'] : 0,
            'school_id'=> isset($params['school_id']) ? $params['school_id'] : 0,
            'lesson_id'=> isset($params['lesson_info']) ? (explode('|', $params['lesson_info'])[0] ? explode('|', $params['lesson_info'])[0] : 0) : 0,
            'lesson_name'=> isset($params['lesson_info']) ? (explode('|', $params['lesson_info'])[1] ? explode('|', $params['lesson_info'])[1] : '') : '',
            'license_id'=> isset($params['license_info']) ? (explode('|', $params['license_info'])[0] ? explode('|', $params['license_info'])[0] : 0) : 0,
            'license_name'=> isset($params['license_info']) ? (explode('|', $params['license_info'])[1] ? explode('|', $params['license_info'])[1] : '') : '',
            'exam_license_name'=> isset($params['license_info']) ? (explode('|', $params['license_info'])[1] ? explode('|', $params['license_info'])[1] : '') : '',
            'balance'=> isset($params['balance']) ? $params['balance'] : 0,
            'is_paypass_activated'=> isset($params['is_paypass_activated']) ? ($params['is_paypass_activated'] === 'true' ? 1 : 2) : 2,
            'wallet_access_pass'=> isset($params['wallet_access_pass']) ? $params['wallet_access_pass'] : '',
            'pay_pass'=> isset($params['pay_pass']) ? $params['pay_pass'] : '',
            'xiha_coin'=> isset($params['xiha_coin']) ? $params['xiha_coin'] : 0,
            'signin_num'=> isset($params['signin_num']) ? $params['signin_num'] : 0,
            'signin_lasttime'=> isset($params['signin_lasttime']) ? $params['signin_lasttime'] : 0,
            'province_id'=> isset($params['province_id']) ? $params['province_id'] : '',
            'city_id'=> isset($params['city_id']) ? $params['city_id'] : '',
            'area_id'=> isset($params['area_id']) ? $params['area_id'] : '',
            'photo_id'=> rand(1, 16),
            'learncar_status'=> isset($params['learncar_status']) ? $params['learncar_status'] : '',
        ];
        // 判断手机号是否重复
        $wherecondition = [
            's_phone' => $user_data['s_phone'],
            'l_user_id !=' => $user_data['l_user_id'],
            'i_user_type' => 0
        ];
        $user_info = $this->muser->getUserInfoByCondition($this->muser->user_tablename, $wherecondition);
        if($user_info) {
            $data = ['code'=>100, 'msg'=>'用户已存在', 'data'=>''];
            $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
            exit;
        }
        $result = $this->muser->editUser($user_data, $users_info_data);
        if($result) {
            $data = ['code'=>200, 'msg'=>'编辑成功', 'data'=>['result'=>$result]];
        } else {
            $data = ['code'=>100, 'msg'=>'编辑失败', 'data'=>['result'=>$result]];            
        }
        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
        exit;
    }

    // 删除ajax
    public function delajax() {
        $this->mbase->loginauth();
        $id = $this->input->post('id') ? intval($this->input->post('id')) : 0;
        if($id == '') {
            $data = ['code'=>400, 'msg'=>'参数错误', 'data'=>[]];
            $this->output->set_status_header(200)
                        ->set_content_type('application/json')
                        ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                        ->_display();
            exit;
        }
        $wherecondition = ['l_user_id'=> $id];
        $res = $this->muser->delInfo($this->muser->user_tablename, $this->muser->userinfo_tablename, $wherecondition);
        if($res) {
            $data = ['code'=>200, 'msg'=>'删除成功', 'data'=>[]];
        } else {
            $data = ['code'=>200, 'msg'=>'删除失败', 'data'=>[]];
        }
        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
        exit;
    }

    // 获取省份
    public function provinceajax() {
        $this->mbase->loginauth();
        $province_list = $this->mcity->getProvinceList();
        $data = ['code'=>200, 'msg'=>'获取省份列表成功', 'data'=>$province_list];
        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
        exit;
    }

    // 获取根据省份ID获取城市列表
    public function cityajax() {
        $this->mbase->loginauth();
        $province_id = $this->input->post('pid') ? intval($this->input->post('pid')) : 0;
        $province_list = $this->mcity->getCityListByProvinceId($province_id);
        $data = ['code'=>200, 'msg'=>'获取城市列表成功', 'data'=>$province_list];
        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
        exit;
    }

    // 获取根据城市ID获取区域列表
    public function areaajax() {
        $this->mbase->loginauth();
        $city_id = $this->input->post('cid') ? intval($this->input->post('cid')) : 0;
        $city_list = $this->mcity->getAreaListByCityId($city_id);
        $data = ['code'=>200, 'msg'=>'获取区域列表成功', 'data'=>$city_list];
        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
        exit;
    }

    // 驾校预览
    public function preview() {
        $this->mbase->loginauth();
        $id = $this->input->get('id') ? $this->input->get('id') : 0;
        
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/user/preview');
        $this->load->view(TEMPLATE.'/footer');
    }

    // 上架下架
    public function show() {
        $this->mbase->loginauth();
        $id = $this->input->post('id') ? intval($this->input->post('id')) : 0;
        $status = $this->input->post('status') ? intval($this->input->post('status')) : 0;
        $wherecondition = [
            'l_user_id' => $id,
        ];
        $_data = [
            'i_status' => $status
        ];
        $res = $this->muser->editUserStatus($_data, $wherecondition);
        if($res) {
            $data = ['code'=>200, 'msg'=>'设置成功', 'data'=>['result'=>$res]];
        } else {
            $data = ['code'=>400, 'msg'=>'设置失败', 'data'=>['result'=>$res]];
        }
        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
        exit;
    }

    // 根据关键词搜索驾校列表
    public function search() {
        $this->mbase->loginauth();
        $key = $this->input->post('key') ? trim($this->input->post('key')) : '';
        $result = $this->mbase->getSearchList('l_user_id, s_user_name', ['s_username'=>$key], $this->muser->user_tablename, 20);
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

    // 获取牌照列表
    public function liceconfigajax() {
        $this->mbase->loginauth();
        $license_config_list = $this->mbase->getLicenseConfigList();
        $data = ['code'=>200, 'msg'=>'获取牌照列表成功', 'data'=>$license_config_list];
        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
        exit;
    }

    // 根据券所有者属性获取优惠券列表
    public function couponajax() {
        $this->mbase->loginauth();
        $params = $this->input->post();
        $wherecondition = [
            'owner_type'=>isset($params['owner_type']) ? $params['owner_type'] : 0,
            'owner_id'=>isset($params['owner_id']) ? $params['owner_id'] : 0,
        ];
        $coupon_list = $this->mcoupon->getCouponListByCondition($this->mcoupon->coupon_tablename, $wherecondition);
        $data = ['code'=>200, 'msg'=>'获取牌照列表成功', 'data'=>$coupon_list];
        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
        exit;
    }

    // 重置密码
    public function resetpassajax() {
        $this->mbase->loginauth();
        $pass = $this->input->post('pass') ? trim($this->input->post('pass')) : 'xiha123456';
        $user_id = $this->input->post('uid') ? trim($this->input->post('uid')) : 0;
        $wherecondition = [
            'l_user_id'=> $user_id
        ];
        $res = $this->muser->changeUserPass($wherecondition, ['s_password'=>md5($pass)]);

        if($res) {
            $data = ['code'=>200, 'msg'=>'重置密码成功', 'data'=>['result'=>$res]];
        } else {
            $data = ['code'=>100, 'msg'=>'重置密码失败', 'data'=>['result'=>$res]];
        }
        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
        exit;
    }

}
?>