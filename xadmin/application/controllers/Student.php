<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 学员管理模块
class Student extends CI_Controller {

    static $limit = 10;
    static $page = 1;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('mbase');
        $this->load->model('mstudent');
        $this->load->model('muser');
        $this->load->model('mcoach');
        $this->load->model('morder');
        $this->load->model('mcity');
        $this->load->helper(['form', 'url']);
        $this->load->library('form_validation');
    }

    /*学员列表*/
    public function index()
    {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $data = ['school_id' => $school_id];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/student/index', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 获取学员信息[获取数据]
     * 
     * @return  void
     **/
    public function listAjax()
    {
        $this->mbase->loginauth();
        if( ! $this->input->is_ajax_request()) {
            $data = ['code' => 100, 'msg' => '错误请求方式', 'data' => ''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                ->_display();
            exit;
        }
        $param = [];
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $page = $this->input->post('p', true) ? intval($this->input->post('p', true)) : self::$page;
        $limit = $this->input->post('s', true) ? intval($this->input->post('s', true)) : self::$limit;
        $status = $this->input->post('status', true) ? trim($this->input->post('status', true)) : 'undel';
        $status_value = $status == 'undel' ? 1 : 2;
        $param['status'] = $status_value;
        $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';
        $pageinfo = $this->mstudent->getStudentPageNum($school_id, $param, $limit);
        $page = $page < $pageinfo['pagenum'] || $pageinfo['pagenum'] == 0 ? $page : $pageinfo['pagenum'];
        $start = ($page - 1) * $limit;

        $studentList = $this->mstudent->getStudentListByCondition($school_id, $param, $start, $limit);
        $list['p'] = $page;
        $list['pagenum'] = $pageinfo['pagenum'];
        $list['count'] = $pageinfo['count'];
        $list['list'] = $studentList;
        $data = ['code' => 200, 'msg' => '获取成功', 'data' => $list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }

    /**
     * 设置学员的在线状态
     * @param  
     * @return void
     **/
    public function handleShow()
    {
        $this->mbase->loginauth();
        if( ! $this->input->is_ajax_request()) {
            $data = ['code' => 100, 'msg' => '错误请求方式', 'data' => ''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                ->_display();
            exit;
        }
        $id = $this->input->post('id', true) ? intval($this->input->post('id', true)) : '';
        $status = $this->input->post('status', true) ? intval($this->input->post('status', true)) : 0;
        $phone = $this->input->post('phone', true) ? trim($this->input->post('phone', true)) : 0;
        if ($status == 0) {
            $user_info = $this->morder->getUserInfoByphone($phone);
            if ( ! empty($user_info)) {
                $data = ['code' => 400, 'msg' => '此用户已存在', 'data' => ''];
                $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                    ->_display();
                exit;
            } 
        }
        $param = [
            'l_user_id' => $id,
            'i_status' => $status
        ];
        $tablename = $this->mstudent->user_tbl;
        $result = $this->mbase->updateData($tablename, 'l_user_id', $id, $param);
        if ($result) {
            $data = ['code' => 200, 'msg' => '设置成功', 'data' => $result];
        } else {
            $data = ['code' => 400, 'msg' => '设置失败', 'data' => $result];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }
    
    /**
     * 添加学员
     * 
     * @return  void 
     **/
    public function add()
    {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $data = ['school_id' => $school_id];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/student/add', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    // 添加学员ajax提交表单
    public function addAjax()
    {
        $this->mbase->loginauth();
        if( ! $this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                ->_display();
            exit;
        }
       
        $this->form_validation->set_rules('s_real_name', '', 'required', ['required' => '请填写真实姓名']);
        $this->form_validation->set_rules('s_phone', '', 'required', ['required' => '请填写手机号码']);
        $this->form_validation->set_rules('age', '', 'required', ['required' => '请填写年龄']);
        $this->form_validation->set_rules('identity_id', '', 'required', ['required' => '请填写身份证']);
        $this->form_validation->set_rules('license_id', '', 'required', ['required' => '请选择牌照']);
        $this->form_validation->set_rules('lesson_id', '', 'required', ['required' => '请选择科目']);
        $this->form_validation->set_rules('province_id', '', 'required', ['required' => '请选择省份']);
        $this->form_validation->set_rules('city_id', '', 'required', ['required' => '请选择城市']);
        $this->form_validation->set_rules('area_id', '', 'required', ['required' => '请选择区域']);
        $this->form_validation->set_rules('address', '', 'required', ['required' => '请填写详细地址']);
        $this->form_validation->set_rules('sex', '', 'required', ['required' => '请选择性别']);

        if ($this->form_validation->run() === FALSE) {
            $errors = $this->form_validation->error_array();
            if (isset($errors[array_keys($errors)[0]])){
                $data = ['code' => 400, 'msg' => $errors[array_keys($errors)[0]], 'data' => new \stdClass()];
            }
            else{
                $data = ['code' => 400, 'msg' => '表单未通过安全检测', 'data' => new \stdClass()];
            }
        }else {
            $user = $userinfo = $post = array();
            $params = $this->input->post(NULL, TRUE);
            $user_info = $this->morder->getUserInfoByPhone($params['s_phone']);
            $user = array(
                's_real_name'   => $params['s_real_name'],
                's_username'    => $params['s_real_name'],
                's_phone'       => $params['s_phone'],
                'i_user_type'   => 0,
                'i_status'      => 0,
                's_password'    => md5('123456'),
                'coach_id'      => 0,
                'is_signup'     => $params['school_id'],
                'i_from'        => 2, // 0: ios; 1: android; 2: line
            );

            $license_name = $this->morder->getLicenseName($params['license_id']);
            $lesson_name = $this->mstudent->getLessonName($params['lesson_id']);
            $userinfo = array(
                'age'               => $params['age'],
                'sex'               => $params['sex'],
                'identity_id'       => $params['identity_id'],
                'license_num'       => $params['license_num'],
                'lesson_id'         => $params['lesson_id'],
                'lesson_name'       => $lesson_name,
                'license_id'        => $params['license_id'],
                'license_name'      => $license_name,
                'exam_license_name' => $license_name,
                'province_id'       => $params['province_id'],
                'city_id'           => $params['city_id'],
                'area_id'           => $params['area_id'],
                'address'           => $params['address'],
                'school_id'         => $params['school_id'],
                'learncar_status'   => '科目一学习中',
                'user_photo'        => $params['user_photo'],
            );
            
            if ( ! empty($user_info)) {
                $user['l_user_id'] = $user_info['l_user_id'];
                $result = $this->muser->editUser($user, $userinfo);
            } else {
                $result = $this->muser->addUser($user, $userinfo);
            }

            $action = 'add_student';
            $intro = "添加学员";
            $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
            $tblname = $this->muser->user_tablename;
            $name = substr($tblname, 3, strlen($tblname));

            if($result) {
                if ( ! empty($user_info)) {
                    $this->mlog->action_log($action, $name, $user_info['l_user_id'], $login_id, $intro);
                } else {
                    $this->mlog->action_log($action, $name, $result, $login_id, $intro);
                }
                $data = ['code'=>200, 'msg'=>'添加成功', 'data'=>['result'=>$result]];
            } else {
                $data = ['code'=>100, 'msg'=>'添加失败', 'data'=>['result'=>$result]];
            }
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }
   
    /**  
     * 编辑学员
     *
     * @return  void
     **/
    public  function edit()
    {
        $this->mbase->loginauth();
        $id = $this->input->get('id') ? $this->input->get('id') : 0;
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $data = $this->muser->getUserInfo($id);
        $data['sid'] = $school_id;
        if(!$data) show_404();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/student/edit', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    //  编辑学员ajax提交
    public function editAjax()
    {
        $this->mbase->loginauth();
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $this->form_validation->set_rules('s_username', '', 'required', ['required' => '请填写昵称']);
        $this->form_validation->set_rules('s_real_name', '', 'required', ['required' => '请填写真实姓名']);
        $this->form_validation->set_rules('s_phone', '', 'required', ['required' => '请填写手机号码']);
        $this->form_validation->set_rules('age', '', 'required', ['required' => '请填写年龄']);
        $this->form_validation->set_rules('identity_id', '', 'required', ['required' => '请填写身份证']);
        $this->form_validation->set_rules('license_id', '', 'required', ['required' => '请选择牌照']);
        $this->form_validation->set_rules('lesson_id', '', 'required', ['required' => '请选择科目']);
        $this->form_validation->set_rules('province_id', '', 'required', ['required' => '请选择省份']);
        $this->form_validation->set_rules('city_id', '', 'required', ['required' => '请选择城市']);
        $this->form_validation->set_rules('area_id', '', 'required', ['required' => '请选择区域']);
        $this->form_validation->set_rules('address', '', 'required', ['required' => '请填写详细地址']);
        $this->form_validation->set_rules('sex', '', 'required', ['required' => '请选择性别']);
        if ($this->form_validation->run() === FALSE) {
            $errors = $this->form_validation->error_array();
            if (isset($errors[array_keys($errors)[0]])){
                $data = ['code' => 400, 'msg' => $errors[array_keys($errors)[0]], 'data' => new \stdClass()];
            }
            else{
                $data = ['code' => 400, 'msg' => '表单未通过安全检测', 'data' => new \stdClass()];
            }
        }else {
            $params = $this->input->post(NULL, TRUE);
            $user = array(
                'l_user_id'     => $params['l_user_id'],
                's_real_name'   => $params['s_real_name'],
                's_username'    => $params['s_real_name'],
                's_phone'       => $params['s_phone'],
                'i_user_type'   => 0,
                'i_status'      => 0,
                'coach_id'      => 0,
                'is_signup'     => $params['school_id'],
                'i_from'        => $params['i_from'], // 0: ios; 1: android; 2: line
            );

            $license_name = $this->morder->getLicenseName($params['license_id']);
            $lesson_name = $this->mstudent->getLessonName($params['lesson_id']);
            $userinfo = array(
                'user_id'           => $params['l_user_id'],
                'age'               => $params['age'],
                'sex'               => $params['sex'],
                'identity_id'       => $params['identity_id'],
                'license_num'       => $params['license_num'],
                'lesson_id'         => $params['lesson_id'],
                'lesson_name'       => $lesson_name,
                'license_id'        => $params['license_id'],
                'license_name'      => $license_name,
                'exam_license_name' => $license_name,
                'province_id'       => $params['province_id'],
                'city_id'           => $params['city_id'],
                'area_id'           => $params['area_id'],
                'address'           => $params['address'],
                'school_id'         => $params['school_id'],
                'learncar_status'   => $params['learncar_status'],
                'user_photo'        => $params['user_photo'],
            );
            $result = $this->muser->editUser($user, $userinfo);
            if($result) {
                $data = ['code'=>200, 'msg'=>'编辑成功', 'data'=>['result'=>$result]];
            } else {
                $data = ['code'=>100, 'msg'=>'编辑失败', 'data'=>['result'=>$result]];
            }
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    //  删除学员
    public function delAjax ()
    {
        $this->mbase->loginauth();
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $id = $this->input->post('id') ? intval($this->input->post('id')) : 0;
        if($id == '') {
            $data = ['code'=>400, 'msg'=>'参数错误', 'data'=>[]];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $params = ['l_user_id'=> $id];
        $res = $this->muser->delInfo($this->muser->user_tablename, $this->muser->userinfo_tablename, $params);
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

    public function getLicenseInfo()
    {
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                ->_display();
            exit;
        }
        $items = $this->mcoach->getLicenseInfo();
        if($items) {
            $data = array('code'=>200,'msg'=>'获取成功','data'=>$items);
        }else {
            $data = array('code'=>200,'msg'=>'获取成功','data'=>array());
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }

    public function getLessonInfo()
    {
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                ->_display();
            exit;
        }
        $items = $this->mcoach->getLessonInfo();
        if($items) {
            $data = array('code'=>200,'msg'=>'获取成功','data'=>$items);
        }else {
            $data = array('code'=>200,'msg'=>'获取成功','data'=>array());
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }

    //  检测身份证是否已注册
    public function checkIdentity()
    {
        $this->mbase->loginauth();
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $identity_id = $this->input->post('identity_id') ? trim($this->input->post('identity_id')) : 0;
        $checkIdentity = $this->mbase->checkIdentityFormat($identity_id);
        if ($checkIdentity == false) {
            $data = array('code' => 400, 'msg'=>'身份证格式错误', 'data' => '');
            if ( 0 == $identity_id) {
                $data = array('code' => 400, 'msg'=>'请填写身份证', 'data' => '');
            }
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        // UserInfo表中检测身份证是否已经注册
        $register = $this->muser->isIdentityRegistered($identity_id);
        if ($register) {//返回true说明该身份证已注册
            $data = array('code'=>400, 'msg'=>'亲，该身份证已注册','data'=>'');
        } else {
            $data = array('code'=>200, 'msg'=>'√，身份证可以使用','data'=>'');
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    //  检测手机号是否已注册
    public function checkPhone()
    {
        $this->mbase->loginauth();
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        $s_phone = $this->input->post('s_phone') ? trim($this->input->post('s_phone')) : 0;
        $checkphone = $this->mbase->checkPhoneFormat($s_phone);
        if ( $checkphone == false) {
            $data = ['code' => 400, 'msg' => '手机号格式错误', 'data' => ''];
            if ($s_phone == '') {
                $data = ['code' => 400, 'msg' => '请填写手机号', 'data' => ''];
            }
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        // User表中检测手机号码是否已经注册
        $register = $this->muser->isPhoneRegistered($s_phone);
        if ($register) {//返回true说明该号码已注册
            $data = array('code'=>400, 'msg'=>'亲，该手机号已注册','data'=>'');
        } else {
            $data = array('code'=>200, 'msg'=>'√，手机号可以使用','data'=>'');
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

// 2.模拟成绩
    /**
     * 模拟成绩列表
     * 
     * @return void
     **/
    public function examRecords()
    {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $data = ['school_id' => $school_id];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/student/records', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    //  模拟成绩ajax加载
    public function recordsAjax()
    {
        $this->mbase->loginauth();
        if( ! $this->input->is_ajax_request()) {
            $data = ['code' => 100, 'msg' => '错误请求方式', 'data' => ''];
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
        $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';
        $pageinfo = $this->mstudent->getRecordsPageNum($school_id, $param, $limit);
        $page = $page < $pageinfo['pagenum'] || $pageinfo['pagenum'] == 0 ? $page : $pageinfo['pagenum'];
        $start = ($page - 1) * $limit;
        $recordslist = $this->mstudent->getExamRecordsList($school_id, $param, $start, $limit);

        $records['p'] = $page;
        $records['list'] = $recordslist;
        $records['pagenum'] = $pageinfo['pagenum'];
        $records['count'] = $pageinfo['count'];
        $data = ['code' => 200, 'msg' => '获取成功', 'data' => $records];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }
}
