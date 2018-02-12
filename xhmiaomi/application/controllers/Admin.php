<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 驾培机构
class Admin extends CI_Controller {

    static $limit = 10;
    static $page = 1;
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('mbase');
        $this->load->model('madmin');
        $this->load->helper('captcha');
        $this->load->library('session');
        // $this->mbase->loginauth();
    }

// 1.用户登录
    public function index() {
        $this->mbase->loginauth();
        $loginauth = $this->session->loginauth;
        $loginauth_arr = $loginauth ? explode('|', $loginauth) : [];
        $id = isset($loginauth_arr[0]) ? $loginauth_arr[0] : '';
        $admin_name = isset($loginauth_arr[1]) ? $loginauth_arr[1] : '';
        $role_permission_id = isset($loginauth_arr[2]) ? $loginauth_arr[2] : '';
        $role_id = isset($loginauth_arr[3]) ? $loginauth_arr[3] : '';
        $content = isset($loginauth_arr[4]) ? $loginauth_arr[4] : '';
        $owner_id = isset($loginauth_arr[5]) ? $loginauth_arr[5] : '';
        $data = ['admin_name'=>$admin_name, 'role_permission_id'=>$role_permission_id, 'role_id'=>$role_id, 'content'=>$content];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/menu', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 获取页面中的菜单
     * @param 
     * @return void
     **/
    public function showMenuAjax()
    {
        $this->mbase->loginauth();
        $loginauth = $this->session->loginauth;
        $loginauth_arr = $loginauth ? explode('|', $loginauth) : [];
        $id = isset($loginauth_arr[0]) ? $loginauth_arr[0] : '';
        $admin_name = isset($loginauth_arr[1]) ? $loginauth_arr[1] : '';
        $role_permission_id = isset($loginauth_arr[2]) ? $loginauth_arr[2] : '';
        $role_id = isset($loginauth_arr[3]) ? $loginauth_arr[3] : '';
        $menu_list = $this->madmin->getMenuByRoleId($role_id);
        $data = ['code' => 200, 'msg' => '获取成功', 'data' => $menu_list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    /**
     * 获取顶部菜单
     * @param 
     * @return void
     **/
    public function topMenuAjax()
    {
        $this->mbase->loginauth();
        $loginauth = $this->session->loginauth;
        $loginauth_arr = $loginauth ? explode('|', $loginauth) : [];
        $id = isset($loginauth_arr[0]) ? $loginauth_arr[0] : '';
        $admin_name = isset($loginauth_arr[1]) ? $loginauth_arr[1] : '';
        $role_permission_id = isset($loginauth_arr[2]) ? $loginauth_arr[2] : '';
        $role_id = isset($loginauth_arr[3]) ? $loginauth_arr[3] : '';
        $menu_list = $this->madmin->getTopMenuByRoleId($role_id);
        $data = ['code' => 200, 'msg' => '获取成功', 'data' => $menu_list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;

    }

    public function home() {
        $this->mbase->loginauth();
        $page = $this->input->get('p') ? intval($this->input->get('p')) : self::$page;
        $pape = $page == 0 ? self::$page : $page;
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/admin/index');
        $this->load->view(TEMPLATE.'/footer');
        
    }
    
    // 个人中心
    public function ucenter() {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/admin/ucenter');
    }

    // 退出登录
    public function logout() {
        $this->mbase->loginauth();
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $this->session->sess_destroy();
        $this->session->unset_userdata('loginauth');
        if(!$this->session->loginauth) {
            $data = ['code'=>200, 'msg'=>'退出成功', 'data'=>[]];
        } else {
            $data = ['code'=>100, 'msg'=>'退出失败', 'data'=>[]];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    // 登录
    public function login() {
        if($this->session->loginauth) {
            redirect(base_url('admin/index'));
            exit;
        }
        $cap = $this->captcha();
        $data = ['img_url'=>$cap['img_url']];
        $this->session->code = $cap['word'];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/admin/login', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    // 登录验证
    public function loginajax() {
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $params = $this->input->post();
        $name = isset($params['name']) ? $params['name'] : '';
        $pass = isset($params['pass']) ? $params['pass'] : '';
        $code = isset($params['code']) ? $params['code'] : '';

        // 验证验证码
        if($code !== $this->session->code) {
            $data = ['code'=>100, 'msg'=>'验证码错误', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        // 登录验证
        $wherecondition = ['name'=>$name, 'password'=>md5($pass), 'is_close'=>1 ];
        $admin_info = $this->madmin->getLoginInfo($wherecondition);
        if($admin_info) {
            $this->session->loginauth =  $admin_info['id'].'|'.$admin_info['name'].'|'.$admin_info['role_permission_id'].'|'.$admin_info['role_id'].'|'.$admin_info['content'].'|'.$admin_info['owner_id'];
            $data = ['code'=>200, 'msg'=>'登录成功', 'data'=>[]];
        } else {
            $data = ['code'=>100, 'msg'=>'用户名或者密码错误', 'data'=>[]];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    // 验证码
    public function captcha() {
        $vals = array(
            'word'      => rand(100000, 999999),
            'img_path'  => realpath(BASEPATH.'../upload').DIRECTORY_SEPARATOR.'captcha/',
            'img_url'   => base_url('upload/captcha'),
            // 'font_path' => realpath(BASEPATH.'../assets').DIRECTORY_SEPARATOR.'element/fonts/iconfont/iconfont.tff',
            'img_width' => '100',
            'img_height'    => 30,
            'expiration'    => 7200,
            'word_length'   => 8,
            'font_size' => 16,
            'img_id'    => 'Imageid',
            'pool'      => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',

            // White background and border, black text and red grid
            // 'colors'    => array(
            //     'background' => array(255, 255, 255),
            //     'border' => array(255, 255, 255),
            //     'text' => array(0, 0, 0),
            //     'grid' => array(255, 40, 40)
            // )
        );
        $cap = create_captcha($vals);
        $cap['img_url'] = $vals['img_url'].'/'.$cap['filename'];
        return $cap;
    }

    public function captchaajax() {
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $vals = array(
            'word'      => rand(100000, 999999),
            'img_path'  => realpath(BASEPATH.'../upload').DIRECTORY_SEPARATOR.'captcha/',
            'img_url'   => base_url('upload/captcha'),
            'font_path' => realpath(BASEPATH.'../assets').DIRECTORY_SEPARATOR.'element/fonts/iconfont/iconfont.tff',
            'img_width' => '100',
            'img_height'    => 30,
            'expiration'    => 7200,
            'word_length'   => 8,
            'font_size' => 16,
            'img_id'    => 'Imageid',
            'pool'      => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
        );
        $cap = create_captcha($vals);
        $cap['img_url'] = $vals['img_url'].'/'.$cap['filename'];
        $this->session->code = $cap['word'];
        $_data = ['img_url' => $cap['img_url']];
        $data = ['code'=>200, 'msg'=>'获取验证码成功', 'data'=>$_data];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

// 2.管理员管理
    // 获取管理员列表
    public function manage() {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/admin/manage');
        $this->load->view(TEMPLATE.'/footer');
    }
    
    // 管理员列表ajax
    public function managelistajax() {
        $this->mbase->loginauth();
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $param = [];
        $loginauth = $this->session->loginauth;
        $loginauth_arr = $loginauth ? explode('|', $loginauth) : [];
        $id = isset($loginauth_arr[0]) ? $loginauth_arr[0] : 0;
        $admin_name = isset($loginauth_arr[1]) ? $loginauth_arr[1] : '';
        $role_permission_id = isset($loginauth_arr[2]) ? $loginauth_arr[2] : 0;
        $role_id = isset($loginauth_arr[3]) ? $loginauth_arr[3] : 0;
        $content = isset($loginauth_arr[4]) ? $loginauth_arr[4] : '';
        $owner_id = isset($loginauth_arr[5]) ? $loginauth_arr[5] : 0;

        $param['is_close'] = $this->input->post('close') ? intval($this->input->post('close')) : '';
        $param['keywords'] = $this->input->post('keywords') ? trim($this->input->post('keywords')) : '';
        $page = $this->input->post('p') ? intval($this->input->post('p')) : self::$page;
        $limit = $this->input->post('s') ? intval($this->input->post('s')) : self::$limit;

        $pageinfo = $this->madmin->getManagePageNumByCondition($param, $owner_id, $limit);
        $page = $page < $pageinfo['pagenum'] || $pageinfo['pagenum'] == 0 ? $page : $pageinfo['pagenum'];        
        $start = ($page - 1) * $limit;
        
        $admin_info = $this->madmin->getManageList($param, $owner_id, $start, $limit);
        $managerlist['pagenum'] = $pageinfo['pagenum'];
        $managerlist['count'] = $pageinfo['count'];
        $managerlist['role_id'] = $role_id;
        $managerlist['list'] = $admin_info;
        $data = ['code'=>200, 'msg'=>'获取成功', 'data'=> $managerlist];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    // 添加管理员
    public function addmanage() {
        $this->mbase->loginauth();
        $role_id = $this->mbase->getRoleIdFromLoginauth($this->session->loginauth);
        $permission_check = $this->mbase->is_authorized("admin/addmanage", $role_id);
        if (true !== $permission_check) {
            $data = ['code' => 400, 'msg' => 'Permission Denied!'];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/admin/addmanage');
        $this->load->view(TEMPLATE.'/footer');
    }

    // 添加管理员
    public function addManageAjax() {
        $this->mbase->loginauth();
        $loginauth = $this->session->loginauth;
        $loginauth_arr = $loginauth ? explode('|', $loginauth) : [];
        $id = isset($loginauth_arr[0]) ? $loginauth_arr[0] : '';
        
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $params = $this->input->post();
        $role_info = isset($params['role_info']) ? trim($params['role_info']) : '';
        $role_id = explode('|', $role_info)[0];
        $owner_id = isset($loginauth_arr[5]) ? $loginauth_arr[5] : 0;
        $phone = isset($params['phone']) ? trim($params['phone']) : '';
        $role_permission_id = $this->madmin->getRolePermissionInfo(['l_role_id'=>$role_id], 'l_rolepress_incode');
        if(!$role_permission_id) {
            $data = ['code'=>100, 'msg'=>'当前角色没设置权限，请到角色管理设置', 'data'=>[]];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $insertdata = [
            'name'=> isset($params['name']) ? trim($params['name']) : '',
            'password'=> isset($params['password']) ? md5(trim($params['password'])) : md5('xiha123456'),
            'phone'=> $phone,
            'role_permission_id'=> $role_permission_id['l_rolepress_incode'],
            'addtime'=> time(),
            'role_id'=> $role_id,
            'owner_id'=> $owner_id,
            'parent_id'=> $id,
            'content'=> isset($params['content']) ? trim($params['content']) : '',
            'is_close'=> isset($params['is_close']) ? ($params['is_close'] ? 1 : 2) : 2,
        ];
        $wherecondition = "name = '{$insertdata['name']}'";
        $res = $this->madmin->insertManageInfo($insertdata, $wherecondition);
        if($res) {
            $data = ['code'=>200, 'msg'=>'添加成功', 'data'=>$res];
        } else {
            $data = ['code'=>100, 'msg'=>'管理员账号已存在', 'data'=>$res];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    /**
     * 修改管理员信息[页面展示]
     * @param
     * @return void
     **/
    public function editmanage()
    {
        $this->mbase->loginauth();
        $role_id = $this->mbase->getRoleIdFromLoginauth($this->session->loginauth);
        $permission_check = $this->mbase->is_authorized("admin/editmanage", $role_id);
        if (true !== $permission_check) {
            $data = ['code' => 400, 'msg' => 'Permission Denied!'];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $id = $this->input->get('id', true);
        $data = $this->madmin->getManageInfoById($id);
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/admin/editmanage', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 修改管理员信息[功能实现]
     * @param 
     * @return void
     **/
    public function editManageAjax()
    {
        $this->mbase->loginauth();
        $loginauth = $this->session->loginauth;
        $loginauth_arr = $loginauth ? explode('|', $loginauth) : [];
        $login_id = isset($loginauth_arr[0]) ? $loginauth_arr[0] : '';
        
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        $param = [];
        $param['id'] = $this->input->post('id', true) ? intval($this->input->post('id', true)) : '';
        $managerlist = $this->madmin->getManageInfoById($param['id']);
        $param['content'] = $this->input->post('content', true) ? trim($this->input->post('content', true)) : $managerlist['content'];
        $param['name'] = $this->input->post('name', true) ? trim($this->input->post('name', true)) : '';
        $param['phone'] = $this->input->post('phone', true) ? trim($this->input->post('phone', true)) : '';
        $param['is_close'] = $this->input->post('is_close', true) ? ($this->input->post('is_close', true) === 'false' ? 2 : 1) : $managerlist['is_close'];
        $param['password'] = $this->input->post('password', true) ? md5($this->input->post('password', true)) : $managerlist['password'];

        $role_info = $this->input->post('role_info', true) ? trim($this->input->post('role_info', true)) : '';
        $role_list = explode('|', $role_info);
        if ( count($role_list) > 1) {
            $param['role_id'] = $role_list[0];
        } else {
            $param['role_id'] = $managerlist['role_id'];
        }
        $param['parent_id'] = $login_id;
        $param['updatetime'] = time();
        $value = $param['id'];
        $field = 'id';
        $tblname = $this->madmin->admin_tablename;
        $result = $this->mbase->updateData($tblname, $field, $value, $param);
        if ($result) {
            $data = ['code'=>200, 'msg'=>'更新成功', 'data'=>$result];
        } else {
            $data = ['code'=>400, 'msg'=>'更新失败', 'data'=>$result];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
       
    }
    
    /**
     * 删除管理员
     * @param $id
     * @return void
     **/
    public function delajax()
    {
        $this->mbase->loginauth();
        $type_arr = ['manage', 'role', 'perssion', 'menu'];
        $type = $this->input->get('type', true) ? trim($this->input->get('type', true)) : '';
        if ( ! in_array($type, $type_arr)) {
            $data = ['code'=>101, 'msg'=>'类型不在规定范围类', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        $role_id = $this->mbase->getRoleIdFromLoginauth($this->session->loginauth);
        $permission_check = $this->mbase->is_authorized("admin/delajax?type=".$type, $role_id);
        if (true !== $permission_check) {
            $data = ['code' => 400, 'msg' => 'Permission Denied!'];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }


        $loginauth = $this->session->loginauth;
        $loginauth_arr = $loginauth ? explode('|', $loginauth) : [];
        $login_id = isset($loginauth_arr[0]) ? $loginauth_arr[0] : '';
        $id = $this->input->post('id', true) ? intval($this->input->post('id', true)) : '';
        $id_arr = explode(',', $id);
        if ( $type == 'manage' ) {
            $tblname = $this->madmin->admin_tablename;
            $field = "id";

        } elseif ($type == 'role') {
            $tblname = $this->madmin->roles_tablename;
            $field = "l_role_id";

        } elseif ($type == 'menu') {
            $tblname = $this->madmin->menu_tablename;
            $field = "moduleid";
        } 

        if ( $type == 'menu') {
            $moduleids = $this->madmin->getAllMenuIds($id);
            $result = $this->mbase->delData($tblname, $field, $moduleids);

        } else {
            $result = $this->mbase->delData($tblname, $field, $id_arr);
        }
        if ($result) {
            $data = ['code'=>200, 'msg'=>'删除成功', 'data'=>$result];
        } else {
            $data = ['code'=>400, 'msg'=>'删除失败', 'data'=>$result];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;

    }

// 3.角色管理
    /**
     * 角色管理列表[页面展示]
     * @param 
     * @return void
     **/
    public function roles() {
        $this->mbase->loginauth();
        $role_id = $this->mbase->getRoleIdFromLoginauth($this->session->loginauth);
        $permission_check = $this->mbase->is_authorized('admin/roles', $role_id);
        if (true !== $permission_check) {
            $data = ['code' => 400, 'msg' => 'Permission Denied!'];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/admin/roles');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 角色管理列表[数据获取]
     * @param 
     * @return void
     **/
    public function rolesAjax()
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
        $param = [];
        $page = $this->input->post('p', true) ? intval($this->input->post('p', true)) : self::$page;
        $limit = $this->input->post('s', true) ? intval($this->input->post('s', true)) : self::$limit;
        $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';
        
        $pageinfo = $this->madmin->getRolesPageNum($param, $limit);
        $page = $page < $pageinfo['pagenum'] || $pageinfo['pagenum'] == 0 ? $page : $pageinfo['pagenum'];        
        $start = ($page - 1) * $limit;

        $roles_info = $this->madmin->getRolesList($param, $start, $limit);
        $role_info['pagenum'] = $pageinfo['pagenum'];
        $role_info['count'] = $pageinfo['count'];
        $role_info['p'] = $page;
        $role_info['list'] = $roles_info;
        $data = ['code' => 200, 'msg' => '获取成功', 'data' => $role_info];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    /**
     * 添加角色[页面展示]
     * @param 
     * @return void
     **/
    public function addrole() {
        $this->mbase->loginauth(); 
        $role_id = $this->mbase->getRoleIdFromLoginauth($this->session->loginauth);
        $permission_check = $this->mbase->is_authorized("admin/addrole", $role_id);
        if (true !== $permission_check) {
            $data = ['code' => 400, 'msg' => 'Permission Denied!'];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }      
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/admin/addrole');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 获取菜单列表
     * @param  
     * @return void
     **/
    public function mListAjax()
    {
        $this->mbase->loginauth();
        $id = $this->input->post('id', true) ? intval($this->input->post('id', true)) : 0;
        $loginauth = $this->session->loginauth;
        $loginauth_arr = $loginauth ? explode('|', $loginauth) : [];
        $owner_id = isset($loginauth_arr[5]) ? $loginauth_arr[5] : 0;
        if ( ! $this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg' => '错误请求方式', 'data' => ''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        // $role_list = $this->madmin->getMenuList(1, $parent_id = 0, $level=0, $html='—| ', $limit = 100, $start = 0, $m_type = [1, 2]);
        $role_list = $this->madmin->getRoleMenuList(0, $id);
        if($role_list) {
            $data = ['code' => 200, 'msg' => '添加成功', 'data' => ['list' => $role_list]];
        } else {
            $data = ['code' => 100, 'msg' => '添加失败', 'data' => ['list' => $role_list]];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;

    }

    /**
     * 添加角色[功能实现]
     * @param 
     * @return void
     **/
    public function addroleajax() {
        $this->mbase->loginauth();
        $loginauth = $this->session->loginauth;
        $loginauth_arr = $loginauth ? explode('|', $loginauth) : [];
        $owner_id = isset($loginauth_arr[5]) ? $loginauth_arr[5] : 0;
        if ( ! $this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg' => '错误请求方式', 'data' => ''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $params = $this->input->post();
        
        $s_rolename = isset($params['s_rolename']) ? trim($params['s_rolename']) : '';
        $s_description = isset($params['s_description']) ? trim($params['s_description']) : '';
        $insertdata['s_role_name'] = $s_rolename;
        $insertdata['s_description'] = $s_description;
        $insertdata['owner_id'] = $owner_id;
        $insertdata['owner_type'] = 1;
        $insertdata['addtime'] = time();
        
        $module_ids = isset($params['moduleid']) ? $params['moduleid'] : [];
        if ( empty($module_ids) ) {
            $data = ['code' => 400, 'msg'=>'尚未选择权限', 'data' => new \stdClass];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        } 
        $moduleid_arr = [];
        foreach ($module_ids as $key => $value) {
            foreach ($value as $k => $v) {
                $moduleid_arr[] = $v;
            }
        }
        $moduleids = $this->madmin->getParentModuleId($moduleid_arr);
        $list = [];
        if ( ! empty($moduleids)) {
            foreach ($moduleids as $k => $v) {
                foreach ($v as $index => $moduleid) {
                    $list[] = $moduleid;
                }
            }
        }
        
        $wherecondition = ['s_role_name' => $s_rolename];
        $res = $this->madmin->_insert($this->madmin->roles_tablename, $insertdata, $wherecondition);
        if($res) {
            $rolepermission['l_role_id'] = $res;
            $rolepermission['module_id'] = implode(',', $list);
            $rolepermission['addtime'] = time();
            $wherecondition = ['l_role_id' => $res];
            $tblname = $this->madmin->permission_tablename;
            $res = $this->madmin->_insert($tblname, $rolepermission, $wherecondition);
            $data = ['code'=>200, 'msg'=>'添加成功', 'data'=>$res];
        } else {
            $data = ['code'=>100, 'msg'=>'添加失败', 'data'=>$res];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    // 编辑角色
    public function editrole() {
        $this->mbase->loginauth();
        $role_id = $this->mbase->getRoleIdFromLoginauth($this->session->loginauth);
        $permission_check = $this->mbase->is_authorized("admin/editrole", $role_id);
        if (true !== $permission_check) {
            $data = ['code' => 400, 'msg' => 'Permission Denied!'];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }    
        $id = $this->input->get('id') ? $this->input->get('id') : 0;
        $tblname = $this->madmin->roles_tablename;
        $data = $this->mbase->_fetchOne($tblname, 'l_role_id, s_role_name, s_description, owner_id, owner_type', ['l_role_id'=>$id]);
        if( ! $data) show_404();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/admin/editrole', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    // 编辑角色ajax
    public function editroleajax() {
        $this->mbase->loginauth();
        $loginauth = $this->session->loginauth;
        $loginauth_arr = $loginauth ? explode('|', $loginauth) : [];
        $owner_id = isset($loginauth_arr[5]) ? $loginauth_arr[5] : 0;
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $params = $this->input->post();
        $l_role_id = isset($params['l_role_id']) ? $params['l_role_id'] : 0;
        $s_rolename = isset($params['s_rolename']) ? $params['s_rolename'] : '';
        $s_description = isset($params['s_description']) ? $params['s_description'] : '';
        $update_data = [
            's_role_name'=> $s_rolename,
            's_description'=> $s_description,
            'updatetime'=> time(),
        ];

        $module_ids = isset($params['moduleid']) ? $params['moduleid'] : [];
        if ( empty($module_ids) ) {
            $tblname = $this->madmin->permission_tablename;
            $permissionlist = $this->mbase->_fetchOne($tblname, 'module_id', ['l_role_id'=>$l_role_id]);
            $list = explode(",", $permissionlist['module_id']);
        } else {
            $moduleid_arr = [];
            foreach ($module_ids as $key => $value) {
                foreach ($value as $k => $v) {
                    $moduleid_arr[] = $v;
                }
            }
            $moduleids = $this->madmin->getParentModuleId($moduleid_arr);
            $list = [];
            if ( ! empty($moduleids)) {
                foreach ($moduleids as $k => $v) {
                    foreach ($v as $index => $moduleid) {
                        $list[] = $moduleid;
                    }
                }
            }
        }
      
        $rolepermission['module_id'] = implode(',', $list);
        $rolepermission['addtime'] = time();
        $wherecondition = ['l_role_id' => $l_role_id];
        $res = $this->madmin->_insert($this->madmin->roles_tablename, $update_data, $wherecondition);
        $result = $this->madmin->_insert($this->madmin->permission_tablename, $rolepermission, $wherecondition);
        if($res) {
            $data = ['code'=>200, 'msg'=>'编辑成功', 'data'=>$res];
        } else {
            $data = ['code'=>100, 'msg'=>'编辑失败', 'data'=>$res];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

// 4.菜单管理
    /**
     * 菜单列表[页面展示]
     * @param
     * @return void
     **/
    public function menu() {
        $this->mbase->loginauth();
        $role_id = $this->mbase->getRoleIdFromLoginauth($this->session->loginauth);
        $permission_check = $this->mbase->is_authorized("admin/menu", $role_id);
        if (true !== $permission_check) {
            $data = ['code' => 400, 'msg' => 'Permission Denied!'];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }    
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/admin/menu');
        $this->load->view(TEMPLATE.'/footer');
    }
    
    public function menuListAjax() 
    {
        $this->mbase->loginauth();
        $loginauth = $this->session->loginauth;
        $loginauth_arr = $loginauth ? explode('|', $loginauth) : [];
        $owner_id = isset($loginauth_arr[5]) ? $loginauth_arr[5] : 0;
        $owner_type = $owner_id == 0 ? 3 : 1;        
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        $page = $this->input->post('p', true) ? intval( $this->input->post('p', true) ) : self::$page;
        $limit = $this->input->post('s', true) ? intval( $this->input->post('s', true) ) : 1;
        
        $pageinfo = $this->madmin->getMenuPageNum([1,2], 0, $limit);
        
        $page = $page < $pageinfo['pagenum'] || $pageinfo['pagenum'] == 0 ? $page : $pageinfo['pagenum'];
        $start = ($page - 1) * $limit;
        $menu_list = $this->madmin->getMenuList($is_close = [1, 2], $parent_id = 0, $level=0, $html='—| ', $limit, $start);
        $list['list'] = $menu_list;
        $list['pagenum'] = $pageinfo['pagenum'];
        $list['count'] = $pageinfo['count'];
        $list['p'] = $page;
        $data = [
            'code' =>200, 
            'msg' => '获取成功', 
            'data' => $list
        ];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }
    
    /**
     * 新增菜单[页面展示]
     * @param $data
     * @return void
     **/
    public function addmenu() {
        $this->mbase->loginauth();
        $role_id = $this->mbase->getRoleIdFromLoginauth($this->session->loginauth);
        $permission_check = $this->mbase->is_authorized("admin/addmenu", $role_id);
        if (true !== $permission_check) {
            $data = ['code' => 400, 'msg' => 'Permission Denied!'];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }   
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/admin/addmenu');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 新增菜单[功能实现]
     * @param $data
     * @return void
     **/
    public function addMenuAjax()
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
        $param = [];
        $param['m_cname'] = $this->input->post('menu_name', true) ? trim($this->input->post('menu_name', true)) : '';
        $param['m_description'] = $this->input->post('m_description', true) ? trim($this->input->post('m_description', true)) : '';
        $param['m_controller'] = $this->input->post('m_controller', true) ? trim($this->input->post('m_controller', true)) : '';
        $param['m_parentid'] = $this->input->post('m_parentid', true) ? intval($this->input->post('m_parentid', true)) : 0;
        $param['i_order'] = $this->input->post('i_order', true) ? intval($this->input->post('i_order', true)) : 50;
        $m_type_name = $this->input->post('m_type_name', true) ? trim($this->input->post('m_type_name', true)) : '';
        if ($param['m_parentid'] == 0) {
            $param['m_type'] = 1; // 模块
        } else {
            if ( $m_type_name == '模块') {
                $param['m_type'] = 1; // 模块
            } else {
                $param['m_type'] = 2; // 操作
            }
        }

        $param['m_close'] = $this->input->post('m_close', true) ? ($this->input->post('m_close', true) === "true" ? 1 : 2) : 1;
        $param['addtime'] = time();
        $param['m_pagecode'] = substr(microtime(), -6);
        $param['m_applicationid'] = 1;
        $tblname = $this->madmin->menu_tablename;
        $wherecondition = [
            'm_cname' => $param['m_cname'],
        ];
        $result = $this->madmin->_insertMenu($tblname, $param);
        if ($result) {
            $data = [
                'code' => 200,
                'msg' => '添加成功',
                'data' => $result,
            ];
        } else {
            $data = [
                'code' => 400,
                'msg' => '添加失败',
                'data' => '',
            ];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    /**
     * 菜单编辑
     * @param
     * @return void
     **/
    public function editmenu()
    {    
        $this->mbase->loginauth();
        $role_id = $this->mbase->getRoleIdFromLoginauth($this->session->loginauth);
        $permission_check = $this->mbase->is_authorized("admin/editmenu", $role_id);
        if (true !== $permission_check) {
            $data = ['code' => 400, 'msg' => 'Permission Denied!'];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }   
        $id = $this->input->get('id', true) ? intval($this->input->get('id', true)) : '';
        $data = $this->madmin->getMenuInfoById($id);
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/admin/editmenu', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 新增菜单[功能实现]
     * @param $data
     * @return void
     **/
    public function editMenuAjax()
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
        $param = [];
        $param['moduleid'] = $this->input->post('id', true) ? trim($this->input->post('id', true)) : '';
        $menulist = $this->madmin->getMenuInfoById($param['moduleid']);
        $param['m_cname'] = $this->input->post('menu_name', true) ? trim($this->input->post('menu_name', true)) : $menulist['m_cname'];
        $param['m_description'] = $this->input->post('m_description', true) ? trim($this->input->post('m_description', true)) : $menulist['m_description'];
        $param['m_controller'] = $this->input->post('m_controller', true) ? trim($this->input->post('m_controller', true)) : $menulist['m_controller'];
        $param['m_parentid'] = $this->input->post('m_parentid', true) ? intval($this->input->post('m_parentid', true)) : 0;
        $param['i_order'] = $this->input->post('i_order', true) ? intval($this->input->post('i_order', true)) : intval($menulist['i_order']);
        $m_type_name = $this->input->post('m_type_name', true) ? trim($this->input->post('m_type_name', true)) : '';
        if ($param['m_parentid'] == 0) {
            $param['m_type'] = 1; // 模块
        } else {
            if ( $m_type_name == '模块') {
                $param['m_type'] = 1; // 模块
            } else {
                $param['m_type'] = 2; // 操作
            }
        }

        $param['m_close'] = $this->input->post('m_close', true) ? ($this->input->post('m_close', true) === "true" ? 1 : 2) : 1;
        $param['updatetime'] = time();
        $param['m_pagecode'] = substr(microtime(), -6);
        $param['m_applicationid'] = 1;
        $tblname = $this->madmin->menu_tablename;
        $field = "moduleid";
        $value = $param['moduleid'];
        $result = $this->mbase->updateData($tblname, $field, $value, $param);
        if ($result) {
            $data = [
                'code' => 200,
                'msg' => '修改成功',
                'data' => $result,
            ];
        } else {
            $data = [
                'code' => 400,
                'msg' => '修改失败',
                'data' => '',
            ];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    /**
     * 获取菜单数据
     * @param 
     * @return void
     **/
    public function menuAjax()
    {
        $this->mbase->loginauth();
        $loginauth = $this->session->loginauth;
        $loginauth_arr = $loginauth ? explode('|', $loginauth) : [];
        $owner_id = isset($loginauth_arr[5]) ? $loginauth_arr[5] : 0;
        $owner_type = $owner_id == 0 ? 3 : 1;        
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        
        $menu_list = $this->madmin->getMenuList($is_close = 1, $parent_id = 0, $level=0, $html='—| ', $limit = 100, $start = 0, [1]);
        $list['list'] = $menu_list;
        $data = [
            'code' =>200, 
            'msg' => '获取成功', 
            'data' => $list
        ];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }


    /**
     * 设置菜单的开启状态
     * @param [$id, $status]
     * @return void
     **/
    public function handleCloseStatus()
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

        $id = $this->input->post('id', true) ? intval($this->input->post('id', true)) : '';
        $status = $this->input->post('status', true) ? intval($this->input->post('status', true)) : '';
        $moduleids = $this->madmin->getAllMenuIds($id);
        $result = $this->madmin->setCloseStatus($moduleids, $status);
        if ($result) {
            $data = ['code'=>200, 'msg'=>'设置成功', 'data'=>$result];
        } else {
            $data = ['code'=>400, 'msg'=>'获取失败', 'data'=>$result];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    /**
     * 设置菜单顶部展示的支持状态
     * @param [$id, $status]
     * @return void
     **/
    public function handleTopStatus()
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

        $id = $this->input->post('id', true) ? intval($this->input->post('id', true)) : '';
        $status = $this->input->post('status', true) ? intval($this->input->post('status', true)) : '';
        $result = $this->madmin->handleTopStatus($id, $status);
         if ($result) {
            $data = ['code'=>200, 'msg'=>'设置成功', 'data'=>$result];
        } else {
            $data = ['code'=>400, 'msg'=>'获取失败', 'data'=>$result];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;

    }

    public function editrolemenuajax() {
        $this->mbase->loginauth();
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $params = $this->input->post();
        $module_id_arr = [];
        $module_ids = $params['moduleid'] ? $params['moduleid'] : '';
        $module_id = [];
        if($module_ids) {
            foreach ($module_ids as $key => $value) {
                if(is_array($value)) {
                    foreach ($value as $k => $v) {
                        $module_id[] = $v;
                    }
                } else {
                    $module_id[] = $value;
                }
            }
        }
        // 根据module_id获取所以上级ID
        $module_ids = $this->madmin->getParentMenuModuleId($module_id);
        $l_role_id = $params['roleid'] ? $params['roleid'] : '';
        $insertData = ['l_role_id'=>$l_role_id, 'module_id'=>implode(',', $module_ids)];
        $wherecondition = ['l_role_id'=>$l_role_id];
        $res = $this->mbase->_insert($this->madmin->permission_tablename, $insertData, $wherecondition);
        if($res) {
            $data = ['code'=>200, 'msg'=>'编辑菜单成功', 'data'=>$res];
        } else {
            $data = ['code'=>100, 'msg'=>'编辑菜单失败', 'data'=>$res];
        }
        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
        exit;
    }

// 5.其他
    /**
     * 设置管理员关闭与开启的状态
     * @param
     * @return void
     **/
    public function show()
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

        $id = $this->input->post('id', true) ? intval($this->input->post('id', true)) : '';
        $status = $this->input->post('status', true) ? intval($this->input->post('status', true)) : '';
        $update_data = [
            'id' => $id,
            'is_close' => $status
        ];
        $field = 'id';
        $tblname = $this->madmin->admin_tablename;
        $result = $this->mbase->updateData($tblname, $field, $id, $update_data);

        if ($result) {
            $data = ['code'=>200, 'msg'=>'设置成功', 'data'=>$result];
        } else {
            $data = ['code'=>400, 'msg'=>'获取失败', 'data'=>$result];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    /**
     * 获取角色数据
     * @param 
     * @return void
     **/
    public function roleAjax()
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

        $loginauth = $this->session->loginauth;
        $loginauth_arr = $loginauth ? explode('|', $loginauth) : [];
        $role_id = isset($loginauth_arr[3]) ? $loginauth_arr[3] : 0;
        $owner_id = isset($loginauth_arr[5]) ? $loginauth_arr[5] : 0;
        $roles_info = $this->madmin->getRoleList($role_id, $owner_id);
        $data = ['code' => 200, 'msg' => '获取成功', 'data' => $roles_info];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    // 获取角色才到那权限
    public function showrolemenu() {
        $this->mbase->loginauth();
        $role_id = $this->input->get('id') ? intval($this->input->get('id')) : 0;
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/admin/showrolemenu', ['role_id'=>$role_id]);
        $this->load->view(TEMPLATE.'/footer');
    }
    
    // 获取角色菜单权限ajax
    public function showrolemenuajax() {
        $this->mbase->loginauth();
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
            exit;
        }
        $params = $this->input->post();
        $l_role_id = isset($params['id']) ? trim($params['id']) : 0;
        $wherecondition = ['l_role_id'=> $l_role_id];
        $menu_list = $this->madmin->getMenuListByCondition($wherecondition);
        $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$menu_list];
        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
        exit;
    }

}