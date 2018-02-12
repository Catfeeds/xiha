<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 驾培机构
class Coach extends CI_Controller {

    static $limit = 10;
    static $page = 1;
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('mcoach');
        $this->load->model('mbase');
        $this->load->model('mcars');
        $this->load->model('mcoachtimeconfig');
        $this->load->helper(['form', 'url']);
        $this->load->library('form_validation');
    }

// 1.教练列表
    /**
     * 教练列表[页面展示]
     *  
     * @return  void
     **/
    public function index()
    {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $role_id = $this->mbase->getRoleIdFromLoginauth($this->session->loginauth);
        $data = ['school_id' => $school_id, 'role_id' => $role_id];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/coach/index', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 教练列表[数据获取]
     *  
     * @return  void
     **/
    public function listAjax()
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

        $status = $this->input->post('status', true) ? trim($this->input->post('status', true)) : 'undel';
        $param['status'] = $status === 'undel' ? 0 : 2;
        $param['star'] = $this->input->post('star', true) ? trim($this->input->post('star')) : '';
        $param['verify'] = $this->input->post('verify', true) ? trim($this->input->post('verify')) : '';
        $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords')) : '';
        $pageinfo = $this->mcoach->getCoachPageNum($school_id, $param, $limit);
        $page = $page < $pageinfo['pagenum'] || $pageinfo['pagenum'] == 0 ? $page : $pageinfo['pagenum'];
        $start = ($page - 1) * $limit;
        
        $coachlist = $this->mcoach->getCoachList($school_id, $param, $start, $limit);
        $list['p'] = $page;
        $list['count'] = $pageinfo['count'];
        $list['pagenum'] = $pageinfo['pagenum'];
        $list['list'] = $coachlist;
        $data = ['code' => 200, 'msg' => '获取成功', 'data' => $list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    /**
     * 设置教练各种状态
     * 
     * @return void
     **/
    public function setCoachStatus () {
        $this->mbase->loginauth();
        if( ! $this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $field_arr = ['coupon_supported', 'timetraining_supported', 'order_receive_status', 'is_hot', 'is_elecoach'];
        $field = $this->input->post('field', true) ? trim($this->input->post('field', true)) : '';

        if ( ! in_array($field, $field_arr)) {
            $data = ['code' => 100, 'msg' => '状态不在规定范围内', 'data' => ''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

       
        $id = $this->input->post('id', true) ? intval($this->input->post('id', true)) : 0;
        $status = $this->input->post('status', true);
        $condition = ['l_coach_id' => $id];
        $data = [$field => $status];
        $tblname = $this->mcoach->coach_tbl;
        $result = $this->mcoach->setStatus($condition, $data, $tblname);

        if ($result) {
            $data = ['code' => 200, 'msg' => '设置成功', 'data' => $result];
            $field_text_arr = ['coupon_supported' => '支持券', 'timetraining_supported' => '支持计时', 'order_receive_status' => '在线', 'is_hot' => '热门', 'is_elecoach' => '电子教练支持'];
            $coach_action_arr = ['coupon_supported' => 'set_coupon_support_status', 'timetraining_supported' => 'set_training_support_status', 'order_receive_status' => 'set_coach_status', 'is_hot' => 'set_coach_hotstatus', 'is_elecoach' => 'set_coach_elecoach'];
            $action = $coach_action_arr[$field];
            $intro = "设置教练".$field_text_arr[$field]."状态";
            $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
            $name = substr($tblname, 3, strlen($tblname));
            $this->mlog->action_log($action, $name, $id, $login_id, $intro);
        } else {
            $data = ['code' => 400, 'msg' => '设置失败', 'data' => $result];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    /**
     * 设置教练是否必须绑定的状态
     *
     * @return void
     **/
    public function setMustBind()
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
     
        $id = $this->input->post('id', true) ? intval($this->input->post('id', true)) : 0;
        $status = $this->input->post('status', true);
        $tblname = $this->mcoach->coach_tbl;
        $condition = ['l_coach_id' => $id];

        $param = ['must_bind' => $status];
        $result = $this->mcoach->setStatus($condition, $param, $tblname);
        if ($result) {
            $old_status = $this->mcoach->getCoachStatus($condition, 'must_bind', $tblname);
            $status_arr = ['0' => '未设置', '1' => '需绑定', '2' => '不需绑定'];
            $intro = "是否需绑定教练的状态由".$status_arr[$old_status]."被设置成".$status_arr[$status];
            $action = 'set_coach_bindstatus';
            $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
            $name = substr($tblname, 3, strlen($tblname));
            $this->mlog->action_log($action, $name, $id, $login_id, $intro);
            $data = array('code' => 200, 'msg' => '设置成功', 'data' => $result);
        } else {
            $data = array('code' => 200, 'msg' => '保存成功', 'data' => $result);
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    /**
     * 添加教练[页面展示]
     *
     * @return  void
     **/
    public function add() {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $data = ['school_id' => $school_id];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/coach/add', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 编辑教练信息[页面展示]
     *
     * @return  void
     **/
    public function edit() {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $id = $this->input->get('id') ? $this->input->get('id') : 0;
        $data = $this->mcoach->getCoachInfo($id);
        $data['school_id'] = $school_id;
        if( ! $data) show_404();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/coach/edit', $data);
        $this->load->view(TEMPLATE.'/footer');
    }
    
    /**
     * 添加教练[功能实现]
     *
     * @return  void
     **/
    public function addAjax()
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
        $param['s_coach_phone'] = $this->input->post('s_coach_phone', true) ? trim($this->input->post('s_coach_phone', true)) : '';
        $checkCoachPhone = $this->mcoach->checkCoachPhone($param['s_coach_phone']);
        if ( ! empty($checkCoachPhone)) {
            $data = ['code' => 400, 'msg' => '此手机号用户已存在', 'data' => ''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT))
                ->_display();
            exit;
        }
        $sid = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $school_id = $this->input->post('school_id', true) ? intval($this->input->post('school_id', true)) : $sid;
        $param['s_school_name_id'] = $school_id;
        $param['s_coach_name'] = $this->input->post('s_coach_name', true) ? trim($this->input->post('s_coach_name', true)) : '';
        $param['s_teach_age'] = $this->input->post('s_teach_age', true) ? trim($this->input->post('s_teach_age', true)) : '0';
        $param['i_coach_star'] = $this->input->post('i_coach_star', true) ? trim($this->input->post('i_coach_star', true)) : '3';
        $param['average_license_time'] = $this->input->post('average_license_time', true) ? trim($this->input->post('average_license_time', true)) : '30';
        $param['lesson2_pass_rate'] = $this->input->post('lesson2_pass_rate', true) ? trim($this->input->post('lesson2_pass_rate', true)) : '80';
        $param['lesson3_pass_rate'] = $this->input->post('lesson3_pass_rate', true) ? trim($this->input->post('lesson3_pass_rate', true)) : '80';
        $param['s_coach_sex'] = $this->input->post('s_coach_sex', true) ? trim($this->input->post('s_coach_sex', true)) : '1';
        $param['s_coach_imgurl'] = $this->input->post('s_coach_imgurl', true) ? trim($this->input->post('s_coach_imgurl', true)) : '';
        $param['s_coach_car_id'] = $this->input->post('s_coach_car_id', true) ? trim($this->input->post('s_coach_car_id', true)) : '';
        $param['province_id'] = $this->input->post('province_id', true) ? intval($this->input->post('province_id', true)) : 0;
        $param['city_id'] = $this->input->post('city_id', true) ? intval($this->input->post('city_id', true)) : 0;
        $param['area_id'] = $this->input->post('area_id', true) ? intval($this->input->post('area_id', true)) : 0;
        $param['s_coach_address'] = $this->input->post('s_coach_address', true) ? trim($this->input->post('s_coach_address', true)) : 0;
        $param['i_type'] = $this->input->post('i_type', true) ? intval($this->input->post('i_type', true)) : 0;
        $param['is_hot'] = $this->input->post('is_hot', true) ? ($this->input->post('is_hot', true) === "true" ? 1 : 2) : 1;
        $param['order_receive_status'] = $this->input->post('order_receive_status', true) ? ($this->input->post('order_receive_status', true) === "true" ? 1 : 0) : 1;
        $param['is_elecoach'] = $this->input->post('is_elecoach', true) ? ($this->input->post('is_elecoach', true) === "true" ? 1 : 0) : 1;
        $param['coupon_supported'] = $this->input->post('coupon_supported', true) ? ($this->input->post('coupon_supported', true) === "true" ? 1 : 0) : 1;
        $param['must_bind'] = $this->input->post('must_bind', true) ? ($this->input->post('must_bind', true) === "true" ? 1 : 2) : 1;
        $param['timetraining_supported'] = $this->input->post('timetraining_supported', true) ? ($this->input->post('timetraining_supported', true) === "true" ? 1 : 0) : 0;
        
        $coach_lesson_arr = $this->input->post('s_coach_lesson_id', true) ? $this->input->post('s_coach_lesson_id', true) : ['科目一'];
        $coach_license_arr = $this->input->post('s_coach_lisence_id', true) ? $this->input->post('s_coach_lisence_id', true): ['C1'];
        $lesson_id_arr = [];
        $lesson_info = $this->mcoach->getLessonInfo();
        if ( ! empty($lesson_info)) {
            foreach ($lesson_info as $index => $lesson) {
                if ( in_array($lesson['lesson_name'], $coach_lesson_arr)) {
                    $lesson_id_arr[] = $lesson['lesson_id']; 
                } 
            }
        } else {
            $lesson_id_arr = ['1'];
        }
        $param['s_coach_lesson_id'] = implode(',', $lesson_id_arr);

        $license_id_arr = [];
        $license_info = $this->mcoach->getLicenseInfo();
        if ( ! empty($license_info)) {
            foreach ($license_info as $index => $license) {
                if ( in_array($license['license_name'], $coach_license_arr)) {
                    $license_id_arr[] = $license['license_id']; 
                } 
            }
        } else {
            $license_id_arr = ['1'];
        }
        $param['s_coach_lisence_id'] = implode(',', $license_id_arr);
        $param['addtime'] = time();
        $result = $this->mcoach->addCoachInfo($param);
        if($result) {
            $user_param = [
                'i_user_type' => 1,
                'i_status' => 0,
                's_username' => $param['s_coach_name'],
                's_real_name' => $param['s_coach_name'],
                'coach_id' => $result,
                's_password' => md5('123456'),
                's_phone' => $param['s_coach_phone'],
                'addtime' => time()
            ];
            $res = $this->mcoach->addUser($user_param);
            if ($res) {
                $coach_data = ['l_coach_id' => $result, 'user_id' => $res, 'updatetime' => time()];
                $re = $this->mcoach->editCoachInfo($coach_data);
            }
            $data = ['code' => 200, 'msg' => '添加成功', 'data' => ['result'=>$result]];
        } else {
            $data = ['code' => 400, 'msg' => '添加失败', 'data' => ['result'=>$result]];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    /**
     * 编辑教练[功能实现]
     *
     * @return  void
     **/
    public function editAjax()
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
        $l_coach_id = $this->input->post('l_coach_id', true) ? trim($this->input->post('l_coach_id', true)) : 0;
        $param['l_coach_id'] = $l_coach_id;
        $coach_info = $this->mcoach->getCoachInfo($l_coach_id);
        $param['s_coach_name'] = $this->input->post('s_coach_name', true) ? trim($this->input->post('s_coach_name', true)) : $coach_info['s_coach_name'];
        $s_coach_phone = $this->input->post('s_coach_phone', true) ? trim($this->input->post('s_coach_phone', true)) : '';
        if ($s_coach_phone != $coach_info['s_coach_phone']) { // 更新用户表信息信息
            $user_data = [
                'i_user_type' => 1,
                'i_status' => 0,
                's_phone' => $s_coach_phone,
                's_username' => $param['s_coach_name'],
                's_real_name' => $param['s_coach_name']
            ];
            $update_user_ok = $this->mcoach->updateUser($user_data, $coach_info['s_coach_phone']);
        }
        $param['s_coach_phone'] = $s_coach_phone;
        // $checkCoachPhone = $this->mcoach->checkCoachPhone($param['s_coach_phone']);
        // if ( count($checkCoachPhone) > 1) {
        //     $data = ['code' => 400, 'msg' => '此手机号用户已存在', 'data' => ''];
        //     $this->output->set_status_header(200)
        //         ->set_content_type('application/json')
        //         ->set_output(json_encode($data, JSON_PRETTY_PRINT))
        //         ->_display();
        //     exit;
        // }
        $sid = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $school_id = $this->input->post('school_id', true) ? intval($this->input->post('school_id', true)) : $sid;
        $param['s_school_name_id'] = $school_id;
        
        $param['s_teach_age'] = $this->input->post('s_teach_age', true) ? trim($this->input->post('s_teach_age', true)) : $coach_info['s_teach_age'];
        $param['i_coach_star'] = $this->input->post('i_coach_star', true) ? trim($this->input->post('i_coach_star', true)) : $coach_info['i_coach_star'];
        $param['average_license_time'] = $this->input->post('average_license_time', true) ? trim($this->input->post('average_license_time', true)) : $coach_info['average_license_time'];
        $param['lesson2_pass_rate'] = $this->input->post('lesson2_pass_rate', true) ? trim($this->input->post('lesson2_pass_rate', true)) : $coach_info['lesson2_pass_rate'];
        $param['lesson3_pass_rate'] = $this->input->post('lesson3_pass_rate', true) ? trim($this->input->post('lesson3_pass_rate', true)) : $coach_info['lesson3_pass_rate'];
        $param['s_coach_sex'] = $this->input->post('s_coach_sex', true) ? trim($this->input->post('s_coach_sex', true)) : $coach_info['s_coach_sex'];
        $param['s_coach_imgurl'] = $this->input->post('s_coach_imgurl', true) ? trim($this->input->post('s_coach_imgurl', true)) : $coach_info['s_coach_imgurl'];
        $param['s_coach_car_id'] = $this->input->post('s_coach_car_id', true) ? trim($this->input->post('s_coach_car_id', true)) : $coach_info['s_coach_car_id'];
        $param['province_id'] = $this->input->post('province_id', true) ? intval($this->input->post('province_id', true)) : $coach_info['province_id'];
        $param['city_id'] = $this->input->post('city_id', true) ? intval($this->input->post('city_id', true)) : $coach_info['city_id'];
        $param['area_id'] = $this->input->post('area_id', true) ? intval($this->input->post('area_id', true)) : $coach_info['area_id'];
        $param['s_coach_address'] = $this->input->post('s_coach_address', true) ? trim($this->input->post('s_coach_address', true)) : $coach_info['s_coach_address'];
        $param['i_type'] = $this->input->post('i_type', true) ? intval($this->input->post('i_type', true)) : $coach_info['i_type'];
        $param['is_hot'] = $this->input->post('is_hot', true) ? ($this->input->post('is_hot', true) === "true" ? 1 : 2) : 1;
        $param['order_receive_status'] = $this->input->post('order_receive_status', true) ? ($this->input->post('order_receive_status', true) === "true" ? 1 : 0) : 1;
        $param['is_elecoach'] = $this->input->post('is_elecoach', true) ? ($this->input->post('is_elecoach', true) === "true" ? 1 : 0) : 1;
        $param['coupon_supported'] = $this->input->post('coupon_supported', true) ? ($this->input->post('coupon_supported', true) === "true" ? 1 : 0) : 1;
        $param['must_bind'] = $this->input->post('must_bind', true) ? ($this->input->post('must_bind', true) === "true" ? 1 : 2) : 1;
        $param['timetraining_supported'] = $this->input->post('timetraining_supported', true) ? ($this->input->post('timetraining_supported', true) === "true" ? 1 : 0) : 0;
        
        $coach_lesson_arr = $this->input->post('s_coach_lesson_id', true) ? $this->input->post('s_coach_lesson_id', true) : ['科目一'];
        $coach_license_arr = $this->input->post('s_coach_lisence_id', true) ? $this->input->post('s_coach_lisence_id', true): ['C1'];
        $lesson_id_arr = [];
        $lesson_info = $this->mcoach->getLessonInfo();
        if ( ! empty($lesson_info)) {
            foreach ($lesson_info as $index => $lesson) {
                if ( in_array($lesson['lesson_name'], $coach_lesson_arr)) {
                    $lesson_id_arr[] = $lesson['lesson_id']; 
                } 
            }
        } else {
            $lesson_id_arr = ['1'];
        }
        $param['s_coach_lesson_id'] = implode(',', $lesson_id_arr);

        $license_id_arr = [];
        $license_info = $this->mcoach->getLicenseInfo();
        if ( ! empty($license_info)) {
            foreach ($license_info as $index => $license) {
                if ( in_array($license['license_name'], $coach_license_arr)) {
                    $license_id_arr[] = $license['license_id']; 
                } 
            }
        } else {
            $license_id_arr = ['1'];
        }
        $param['s_coach_lisence_id'] = implode(',', $license_id_arr);
        $param['updatetime'] = time();
        $result = $this->mcoach->editCoachInfo($param);
        if($result) {
            $data = ['code' => 200, 'msg' => '编辑成功', 'data' => ['result'=>$result]];
        } else {
            $data = ['code' => 400, 'msg' => '编辑失败', 'data' => ['result'=>$result]];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    /**
     * 检查手机号的格式
     * @param   string  $coach_phone    教练手机
     *
     * @return  void
     **/
    public function checkPhone()
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

        $coach_phone = $this->input->post('coach_phone', true) ? trim($this->input->post('coach_phone', true)) : '';
        $data = ['code' => 200, 'msg' => '此手机可用', 'data' => ''];
        if ($coach_phone === '') {
            $data = ['code' => 400, 'msg' => '手机号不能为空', 'data' => ''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        } 

        if( ! preg_match("/^1(3[0-9]|4[579]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/",trim($coach_phone)) ) {
            $data = ['code' => 400, 'msg' => '手机格式错误', 'data' => ''];
        } 
        
        $checkCoachPhone = $this->mcoach->checkCoachPhone($coach_phone);
        if ( ! empty($checkCoachPhone)) {
            $data = ['code' => 400, 'msg' => '此手机号用户已存在', 'data' => ''];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    public function schoolList()
    {
        $items = $data = array();
        if($items = $this->mcoach->getSchoolList()) {
            $data = array('code'=>200,'msg'=>'获取成功','data'=>$items);
        }else {
            $data = array('code'=>200,'msg'=>'获取失败','data'=>array());
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    /**
     * 获取驾校下的车辆信息
     * @param   int     $school_id  驾校ID
     * @return  void
     **/
    public function carsList()
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
        $id = $this->input->post('school_id', TRUE) ? intval($this->input->post('school_id', true)) : 0;
        if ($id == 0) {
            $cars_list = [];
        } else {
            $cars_list = $this->mcars->getCarsByIds($id);
        }
        $data = ['code' => 200, 'msg' => '获取成功', 'data' => $cars_list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    /**
     * 获取牌照信息
     *
     * @return  void
     **/
    public function getLicenseInfo()
    {
        $data = array();
        if($items = $this->mcoach->getLicenseInfo()) {
            $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$items];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    /**
     * 获取科目信息
     *
     * @return  void
     **/
    public function getLessonInfo()
    {
        $data = array();
        if($items = $this->mcoach->productLesson()) {
            $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$items];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    /**
     * 获取教练类型信息
     *
     * @return  void
     **/
     public function iType()
     {
         $data = array();
         if($items = $this->mcoach->iType()) {
             $data = array('code'=>200,'msg'=>'获取成功','data'=>$items);
         }
         $this->output->set_status_header(200)
             ->set_content_type('application/json')
             ->set_output(json_encode($data, JSON_PRETTY_PRINT))
             ->_display();
         exit;
     }

    public function licenseAndLesson()
    {
        $id = $this->input->post('id') ? $this->input->post('id') : 0;
        $data = array();
        if($items = $this->mcoach->getCoachInfo($id)) {
            $data = array('code'=>200,'msg'=>'获取成功','data'=>$items);
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    /**
     * 删除教练
     *
     * @return  void
     **/
    public function delAjax() {
        $this->mbase->loginauth();
        if( ! $this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        $id = $this->input->post('id', true) ? intval($this->input->post('id', true)) : '';
        $user_id = $this->input->post('uid', true) ? intval($this->input->post('uid', true)) : '';
        $user_phone = $this->input->post('phone', true) ? trim($this->input->post('phone', true)) : '';
        if($id == '') {
            $data = ['code'=>400, 'msg'=>'参数错误', 'data'=>[]];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT))
                ->_display();
            exit;
        }

        $param = [
            'i_status' => 2,
            'i_user_type' => 1,
            's_phone' => $user_phone,
            'updatetime' => time()
        ];
        $res = $this->mcoach->updateUser($param, $user_phone);
        // $params = ['l_coach_id' => $id];
        // $res = $this->mcoach->delCoachInfo($params);
        if($res) {
            $data = ['code' => 200, 'msg' => '删除成功', 'data' => $res];
        } else {
            $data = ['code' => 200, 'msg' => '删除失败', 'data' => ''];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    /**
     * 恢复教练
     *
     * @return  void
     **/
    public function recoverAjax() {
        $this->mbase->loginauth();
        if( ! $this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        $id = $this->input->post('id', true) ? intval($this->input->post('id', true)) : '';
        $user_id = $this->input->post('uid', true) ? intval($this->input->post('uid', true)) : '';
        $user_phone = $this->input->post('phone', true) ? trim($this->input->post('phone', true)) : '';
        if($id == '') {
            $data = ['code'=>400, 'msg'=>'参数错误', 'data'=>[]];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT))
                ->_display();
            exit;
        }

        $param = [
            'i_status' => 0,
            'i_user_type' => 1,
            's_phone' => $user_phone,
            'updatetime' => time()
        ];
        $condition = ['i_status' => 2, 'i_user_type' => 1, 's_phone' => $user_phone];
        $res = $this->mcoach->updateUser($param, $user_phone, $condition);
        // $params = ['l_coach_id' => $id];
        // $res = $this->mcoach->delCoachInfo($params);
        if($res) {
            $data = ['code' => 200, 'msg' => '删除成功', 'data' => $res];
        } else {
            $data = ['code' => 200, 'msg' => '删除失败', 'data' => ''];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }
    

    // 根据关键词搜索驾校列表
    public function search() {
        $key = $this->input->post('key') ? trim($this->input->post('key')) : '';
        $result = $this->mcoach->getSearchCoachList($key);
        if($result) {
            $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$result];
        } else {
            $data = ['code'=>400, 'msg'=>'获取失败', 'data'=>$result];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    // 获取驾校详情 ajax
    public function coachinfoajax() {
        $id = $this->input->post('id') ? intval($this->input->post('id')) : 0;
        $coach_info = $this->mcoach->getCoachParamsInfo('s_coach_name, l_coach_id', $id);
        $data = ['code'=>200, 'msg'=>'获取教练详情成功', 'data'=>$coach_info];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    /**
     * 教练时间配置
     * 
     * @return  void
     **/
    public function setCoachTimeConf()
    {
        $this->mbase->loginauth();
        $coach_id = $this->input->get('id', true) ? intval($this->input->get('id', true)) : 0;
        $school_id = $this->input->get('school_id', true) ? intval($this->input->get('school_id', true)) : 0;
        $os_type = $this->input->get('ot', true) ? trim($this->input->get('ot', true)) : 'time';
        // $time_list = $this->mcoachtimeconfig->getCoachAmPmConfig($coach_id);
        // $coach_time_config = $this->mcoachtimeconfig->getCoachTimeConfig($school_id, $coach_id);
        $current_date = date('Y-m-d', time());
        $detail = $this->mcoach->getCoachInfo($coach_id);
        $this->load->view(TEMPLATE.'/header');
        if ($os_type == "time") {
            $data = [
                'coach_id'      => $coach_id,
                'school_id'     => $school_id,
                's_am_subject'  => $detail['s_am_subject'],
                's_pm_subject'  => $detail['s_pm_subject'],
            ];
            $this->load->view(TEMPLATE.'/coach/setCoachTimeConf', $data);

        } elseif ($os_type == "current") {
            $data = [
                'coach_id'      => $coach_id,
                'school_id'     => $school_id,
                'current_date'  => $current_date,
            ];
            $this->load->view(TEMPLATE.'/coach/coachCurrentTime', $data);
        }
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 获取教练的时间配置列表
     * 
     * @return  void
     **/
    public function getTimeList()
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
        $coach_id = $this->input->post('id') ? (int)$this->input->post('id') : 0;
        $school_id = $this->input->post('school_id') ? intval($this->input->post('school_id')) : 0;
        $time_list = $this->mcoachtimeconfig->getCoachAmPmConfig($coach_id);

        $detail = $this->mcoach->getCoachInfo($coach_id);
        $list = ['list'  => $time_list,];
        $data = ['code' => 200, 'msg' => '获取成功', 'data' => $list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    /**
     * 获取教练的当前配置时间
     *
     * @return  void
     **/
    public function getCurrentTimeConf()
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
        $coach_id = $this->input->post('id') ? (int)$this->input->post('id') : 0;
        $school_id = $this->input->post('school_id') ? intval($this->input->post('school_id')) : 0;
        $coach_time_config = $this->mcoachtimeconfig->getCoachTimeConfig($school_id, $coach_id);
        $date_list = $coach_time_config['date_time'];
        $detail = $this->mcoach->getCoachInfo($coach_id);
        $coach_date_time = $this->mcoach->getCoachDateTimeConfig($coach_id);
        $list = [
            'date_list'     => $coach_time_config['date_time'],
            'amtime_list2'  => $coach_time_config['am_list'],
            'pmtime_list2'  => $coach_time_config['pm_list'],
            'coach_date_time'=> $coach_date_time,
        ];
        $data = ['code' => 200, 'msg' => '获取成功', 'data' => $list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    /**
     * 设置教练时间模板
     * 
     * @return void
     **/
    public function setCoachTimeConfAjax()
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
        $param['l_coach_id'] = $this->input->post('coach_id', true) ? trim($this->input->post('coach_id', true)) : 0;
        $param['s_am_subject'] = $this->input->post('s_am_subject', true) ? trim($this->input->post('s_am_subject', true)) : '2';
        $param['s_pm_subject'] = $this->input->post('s_pm_subject', true) ? trim($this->input->post('s_pm_subject', true)) : '2';
        $param['s_am_time_list'] = $this->input->post('s_am_time_list', true) ? trim($this->input->post('s_am_time_list', true), ',') : '';
        $param['s_pm_time_list'] = $this->input->post('s_pm_time_list', true) ? trim($this->input->post('s_pm_time_list', true), ',') : '';
        $result = $this->mcoach->editCoachInfo($param);
        if ($result) {
            $data = array('code' => 200, 'msg' => '设置成功', 'data' => $result);
        } else {
            $data = array('code' => 200, 'msg' => '保存成功', 'data' => $result);
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    /**
     * 设置教练时间配置
     * 
     * @return void
     **/
    public function setCoachCurrentTime()
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
        $time_list = [];
        $param['coach_id'] = $this->input->post('coach_id', true) ? trim($this->input->post('coach_id', true)) : 0;
        $_time = $this->input->post('currentdate', true) ? trim($this->input->post('currentdate', true), ',') : time();
        $current_time = explode('-', $_time);
        if (count($current_time) > 1) {
            $param['current_time'] = strtotime($_time);
            $param['year'] = $current_time[0];
            $param['month'] = $current_time[1];
            $param['day'] = $current_time[2];
        } else {
            $param['current_time'] = $current_time[0];
            $param['year'] = date("Y", $current_time[0]);
            $param['month'] = date("m", $current_time[0]);
            $param['day'] = date("d", $current_time[0]);
        }
        $s_am_time_list = $this->input->post('s_am_time_list', true) ? $this->input->post('s_am_time_list', true) : [];
        $s_pm_time_list = $this->input->post('s_pm_time_list', true) ? $this->input->post('s_pm_time_list', true) : [];

        $time_config_id = [];
        $time_config_money_id = [];
        $time_lisence_config_id = [];
        $time_lesson_config_id = [];
        $time_list = array_merge($s_am_time_list, $s_pm_time_list);
        if ( ! empty($time_list)) {
            foreach ($time_list as $index => $time) {
                $time_config_id_arr[] = $time['id'];
                $time_config_money_id_arr[$time['id']] = $time['price'];
                $time_lisence_config_id_arr[$time['id']] = $time['license_no'];
                $time_lesson_config_id_arr[$time['id']] = $time['subjects'];
            }
            $param['time_config_id'] = implode(',', $time_config_id_arr);
            $param['time_config_money_id'] = json_encode($time_config_money_id_arr);
            $param['time_lisence_config_id'] = json_encode($time_lisence_config_id_arr);
            $param['time_lesson_config_id'] = $this->mbase->JSON($time_lesson_config_id_arr);
        } else {
            $param['time_config_id'] = '';
            $param['time_config_money_id'] = '';
            $param['time_lisence_config_id'] = '';
            $param['time_lesson_config_id'] = '';
        }
        $result = $this->mcoachtimeconfig->setCoachTimeConfig($param);
        if ($result['data']) {
            $data = array('code' => 200, 'msg' => '设置成功', 'data' => $result['data']);
        } else {
            $data = array('code' => 200, 'msg' => '设置成功', 'data' => $result['data']);
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    public function getCoachSignUpInfo()
    {
        $coach_id = intval($this->input->post('coach_id'));
        $coachsignupinfo = $this->mcoach->getCoachSignUpInfo($coach_id);
        $coachsignuplist = $this->mcoach->getCoachSignUpList($coach_id);
        $data = [
            'code'=> 200,
            'msg' => '获取成功',
            'data' => [
                'coach_signup'=> $coachsignupinfo,
                'coach_signup_list'=> $coachsignuplist,
            ]
        ];
        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT))
                    ->_display();
        exit;
    }

    public function preview()
    {
        $coach_id = $this->input->get('id') ? (int)$this->input->get('id') : 0;
        $school_id = $this->input->get('school_id') ? intval($this->input->get('school_id')) : 0;
        $time = date('Y-m-d', time());
        $detail = $this->mcoach->getCoachInfo($coach_id);
        $detail['time']= date('Y-m-d', time());
        $detail['coach_date_time'] = $this->mcoach->getCoachDateTimeConfig($coach_id);
        $detail['comment_chart1'] = $this->mcoach->getStudyCommentInfo($coach_id);
        $detail['comment_chart2'] = $this->mcoach->getShiftsCommentInfo($coach_id, 0);
        $data = array('detail'=>$detail);
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/coach/preview', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

// 2.绑定列表
    /**
     * 学员绑定教练列表[页面展示]
     *
     * @return  void
     **/
    public function coachUser()
    {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $data = ['school_id' => $school_id];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/coach/coachUser', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 学员绑定教练列表[功能实现]
     *
     * @return  void
     **/
    public function coachUserAjax()
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
        
        $param['status'] = $this->input->post('status', true) ? trim($this->input->post('status')) : '';
        $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords')) : '';
        $pageinfo = $this->mcoach->getcoachUserPageNum($school_id, $param, $limit);
        $page = $page < $pageinfo['pagenum'] || $pageinfo['pagenum'] == 0 ? $page : $pageinfo['pagenum'];
        $start = ($page - 1) * $limit;
        
        $coachuserlist = $this->mcoach->getCoachUserList($school_id, $param, $start, $limit);
        $list['p'] = $page;
        $list['count'] = $pageinfo['count'];
        $list['pagenum'] = $pageinfo['pagenum'];
        $list['list'] = $coachuserlist;
        $data = ['code' => 200, 'msg' => '获取成功', 'data' => $list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    /**
     * 设置学员与教练的绑定状态
     *
     * @reuturn void
     **/
    public function setCoachBindStatus()
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
     
        $id = $this->input->post('id', true) ? intval($this->input->post('id', true)) : 0;
        $status = $this->input->post('status', true) ? intval($this->input->post('status', true)) : 0;

        $tblname = $this->mcoach->coach_user_rela_tbl;
        $condition = ['id' => $id];
        $old_status = $this->mcoach->getCoachStatus($condition, 'bind_status', $tblname);

        $status_arr = ['1' => '已绑定', '2' => '解除绑定', '3'=> '学员申请绑定教练', '4' => '教练申请绑定学员', '5' => '学员申请解绑教练', '6' => '教练申请解绑学'];
        $intro = "教练与学员的绑定状态由".$status_arr[$old_status]."设置成".$status_arr[$status];
        $action = 'set_coach_bindstatus';
        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $name = substr($tblname, 3, strlen($tblname));
        
        $condition = ['id' => $id];
        $param = ['bind_status' => $status];
        $result = $this->mcoach->setStatus($condition, $param, $tblname);
        if ($result) {
            $this->mlog->action_log($action, $name, $id, $login_id, $intro);
            $data = array('code' => 200, 'msg' => '设置成功', 'data' => $result);
        } else {
            $data = array('code' => 200, 'msg' => '保存成功', 'data' => $result);
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

// 3.教练认证列表
    /**
     * 教练认证列表[页面展示]
     *
     * @return  void
     **/
    public function coachVerify () {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $data = ['school_id' => $school_id];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/coach/coachVerify', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 教练认证列表[功能实现]
     *
     * @return  void
     **/
    public function coachVerifyAjax()
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

        $param['status'] = $this->input->post('status', true) ? trim($this->input->post('status')) : '';
        $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords')) : '';
        $pageinfo = $this->mcoach->getcoachVerifyPageNum($school_id, $param, $limit);
        $page = $page < $pageinfo['pagenum'] || $pageinfo['pagenum'] == 0 ? $page : $pageinfo['pagenum'];
        $start = ($page - 1) * $limit;

        $verlist = $this->mcoach->getCoachVerifyList($school_id, $param, $start, $limit);
        $list['p'] = $page;
        $list['count'] = $pageinfo['count'];
        $list['pagenum'] = $pageinfo['pagenum'];
        $list['list'] = $verlist;
        $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    /**
     * 设置教练认证状态
     *
     * @return  void
     **/
    public function setCoachVerifyStatus()
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
        $id = $this->input->post('id', true) ? intval($this->input->post('id', true)) : 0;
        $status = $this->input->post('status', true) ? intval($this->input->post('status', true)) : 0;

        $tblname = $this->mcoach->coach_tbl;
        $condition = ['l_coach_id' => $id];
        $old_status = $this->mcoach->getCoachStatus($condition, 'certification_status', $tblname);

        $status_arr = ['1' => '未认证', '2' => '认证中', '3'=> '已认证', '4' => '认证失败'];
        $intro = "教练的认证状态由".$status_arr[$old_status]."设置成".$status_arr[$status];
        $action = 'set_coach_certification';
        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $name = substr($tblname, 3, strlen($tblname));
        
        $condition = ['l_coach_id' => $id];
        $param = ['certification_status' => $status];
        $result = $this->mcoach->setStatus($condition, $param, $tblname);
        if ($result) {
            $this->mlog->action_log($action, $name, $id, $login_id, $intro);
            $data = array('code' => 200, 'msg' => '设置成功', 'data' => $result);
        } else {
            $data = array('code' => 200, 'msg' => '保存成功', 'data' => $result);
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }


}
?>
