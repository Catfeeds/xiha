<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 系统管理[1.系统日志, 2.推送通知, 3.标签管理, 4.支付配置, 5.系统行为, 6.驾校设置, 7.用户标签]
 *
 * @category system
 * @package system
 * @author wl
 * @return void
 **/

class Systems extends CI_Controller {

    static $limit = 10;
    static $page = 1;
    public function __construct() 
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('mbase');
        $this->load->model('madmin');
        $this->load->model('msystems');
        $this->load->config('redis');
        $this->load->library('redis');
        $this->load->database();

    }

    
    /**
     * 系统日志[页面展示]
     * @param 
     * @return void
     **/
    public function index()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/systems/index');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 系统日志[页面展示]
     * @param 
     * @return void
     **/
    public function systemtag()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/systems/systemtag');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 支付配置[页面展示]
     * @param 
     * @return void
     **/
    public function payconfig()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/systems/payconfig');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 通知列表[页面展示]
     * @param 
     * @return void
     **/
    public function smsadmin()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/systems/smsadmin');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 系统行为[页面展示]
     * @param 
     * @return void
     **/
    public function systemaction()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/systems/systemaction');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 驾校时间设置[页面展示]
     * @param 
     * @return void
     **/
    public function schoolconfig()
    {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $data = ['school_id' => $school_id];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/systems/schoolconfig', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 用户标签[页面展示]
     * @param 
     * @return void
     **/
    public function usertag()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/systems/usertag');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 所有页面数据的获取
     * @param $school_id
     * @param $role_id
     * @return void
     **/
    public function listAjax()
    {   
        $type_arr = ['log', 'action', 'tag', 'utag', 'pay', 'message', 'sconf']; 
        $type = $this->input->get('type') ? trim($this->input->get('type')) : '';
        if ( ! in_array($type, $type_arr)) {
            $data = [
                'code'  => 400,
                'msg'   => $type.'类型不在规定范围内',
                'data'  => new \stdClass
            ];

            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT))
                ->_display();
            exit;
        }

        $role_id = $this->mbase->getRoleIdFromLoginauth($this->session->loginauth);
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $param = [];
        
        if ($type == 'log') {
            $param['role'] = $this->input->post('role', true) ? (int)$this->input->post('role', true) : '';
            $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';

        } elseif ( $type == 'action') {
            $param['status'] = $this->input->post('st', true) ? (int)$this->input->post('st', true) : '';
            $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';

        } elseif ( $type == 'tag') {

            $param['user_type'] = $this->input->post('utype', true) ? (int)$this->input->post('utype', true) : '';
            $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';

        } elseif ( $type == 'utag') {

            $param['user_type'] = $this->input->post('utype', true) ? (int)$this->input->post('utype', true) : '';
            $param['is_system'] = $this->input->post('sy', true) ? (int)$this->input->post('sy', true) : '';
            $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';

        } elseif ( $type == 'pay') {

            $param['is_open'] = $this->input->post('open', true) ? (int)$this->input->post('open', true) : '';
            $param['is_bank'] = $this->input->post('bank', true) ? (int)$this->input->post('bank', true) : '';
            $param['pay_type'] = $this->input->post('pt', true) ? (int)$this->input->post('pt', true) : '';
            $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';

        } elseif ( $type == 'message') {

            $param['member_type'] = $this->input->post('mt', true) ? (int)$this->input->post('mt', true) : '';
            $param['i_yw_type'] = $this->input->post('st', true) ? (int)$this->input->post('st', true) : '';
            $param['is_read'] = $this->input->post('read', true) ? (int)$this->input->post('read', true) : '';
            $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';

        } elseif ( $type == 'sconf') {
            
            $param['is_auto'] = $this->input->post('at', true) ? (int)$this->input->post('at', true) : '';
            $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';

        }      

        $page = $this->input->post('p', true) ? (int)$this->input->post('p', true) : self::$page;
        $limit = $this->input->post('s', true) ? (int)$this->input->post('s', true) : self::$limit;
        $page_info = $this->msystems->getSystemsPageNum($param, $role_id, $school_id, $limit, $type);
        $pagenum = $page_info['pagenum'];
        $count = $page_info['count'];
        $start = ($page - 1) * $limit;
        $page = $page < $pagenum || $pagenum == 0 ? $page : $pagenum;
        $list = $this->msystems->getSystemsList($param, $role_id, $school_id, $start, $limit, $type);
        $log_list['p'] = $page;
        $log_list['pagenum'] = $pagenum;
        $log_list['count'] = $count;
        $log_list['list'] = $list['list'];
        
        $data = [
            'code' => 200,
            'msg' => '获取成功',
            'data' => $log_list
        ];

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;

    }


    /**
     * 新增系统标签[页面展示]
     * @param
     * @return void
     **/
    public function add() 
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/systems/add');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 新增日志行为标签[页面展示]
     * @param
     * @return void
     **/
    public function addaction() 
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/systems/addaction');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 新增支付配置[页面展示]
     * @param
     * @return void
     **/
    public function addpay() 
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/systems/addpay');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 新增系统通知[页面展示]
     * @param
     * @return void
     **/
    public function addsms() 
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/systems/addsms');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 新增驾校设置[页面展示]
     * @param
     * @return void
     **/
    public function addschoolconfig() 
    {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $data = ['school_id' => $school_id];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/systems/addschoolconfig', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 新增数据[功能实现]
     * @param data
     * @return void
     **/
    public function addAjax()
    {
        if ( ! $this->input->is_ajax_request()) {
            $data = [
                'code'  => 400,
                'msg'   => '请求方式错误',
                'data'  => new \stdClass
            ];

            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT))
                ->_display();
            exit;
        }

        $type_arr = ['log', 'action', 'tag', 'utag', 'pay', 'message', 'sconf']; 
        $type = $this->input->get('type') ? trim($this->input->get('type')) : '';
        if ( ! in_array($type, $type_arr)) {
            $data = [
                'code'  => 400,
                'msg'   => $type.'类型不在规定范围内',
                'data'  => new \stdClass
            ];

            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT))
                ->_display();
            exit;
        }

        $param = [];
        if ( $type == 'tag') { // 新增系统标签
            $param['tag_name'] = $this->input->post('tag_name', true) ? trim($this->input->post('tag_name', true)) : '';
            $param['tag_slug'] = $this->input->post('tag_slug', true) ? trim($this->input->post('tag_slug', true)) : '';
            $param['user_type'] = $this->input->post('user_type', true) ? intval($this->input->post('user_type', true)) : '';
            $param['order'] = $this->input->post('order', true) ? intval($this->input->post('order', true)) : '';
            $param['addtime'] = time();
            $tblname = $this->msystems->tag_config_tbl;
            $action = 'add_tag_config';
            $intro = "添加新的系统标签";
            
        } elseif ( $type == 'action') {
            $param['name'] = $this->input->post('name', true) ? trim($this->input->post('name', true)) : '';
            $param['title'] = $this->input->post('title', true) ? trim($this->input->post('title', true)) : '';
            $param['remark'] = $this->input->post('remark', true) ? trim($this->input->post('remark', true)) : '';
            $param['rule'] = $this->input->post('rule', true) ? trim($this->input->post('rule', true)) : '';
            $param['log'] = $this->input->post('log', true) ? trim($this->input->post('log', true)) : '';
            $param['status'] = $this->input->post('status', true) ? ($this->input->post('status', true) === 'true' ? 1 : 2) : 2;
            $param['type'] = 1;
            $param['add_time'] = time();
            $tblname = $this->msystems->action_tbl;
            $action = 'add_system_action';
            $intro = "添加新的系统行为配置";

        } elseif ( $type == 'pay') {
            $param['account_name'] = $this->input->post('account_name', true) ? trim($this->input->post('account_name', true)) : '';
            $param['account_slug'] = $this->input->post('account_slug', true) ? trim($this->input->post('account_slug', true)) : '';
            $param['account_description'] = $this->input->post('description', true) ? trim($this->input->post('description', true)) : '';
            $param['pay_scope'] = $this->input->post('pay_scope', true) ? intval($this->input->post('pay_scope', true)) : 3;
            $param['pay_type'] = $this->input->post('pay_type', true) ? intval($this->input->post('pay_type', true)) : '';
            $param['order'] = $this->input->post('order', true) ? intval($this->input->post('order', true)) : '';
            $param['is_open'] = $this->input->post('is_open', true) ? ($this->input->post('is_open', true) === 'true' ? 1 : 2) : 2;
            $param['is_bank'] = $this->input->post('is_bank', true) ? ($this->input->post('is_bank', true) === 'true' ? 1 : 2) : 2;
            $param['addtime'] = time();
            $tblname = $this->msystems->pay_config_tbl;
            $action = 'add_pay_account';
            $intro = '添加新的支付方式' ;
            
        } elseif ($type == 'message') {

            $action = 'add_sms';
            $intro = "发送系统通知";
            $s_beizhu = $this->input->post('s_beizhu', true) ? trim($this->input->post('s_beizhu', true)) : '';
            $content = $this->input->post('s_content', true) ? trim($this->input->post('s_content', true)) : '';
            $member_type = $this->input->post('is_coach', true) ? ($this->input->post('is_coach', true) === 'false' ? 1 : 2) : 1;
            $user_id = $this->input->post('user_id', true);
            $coach_id = $this->input->post('coach_id', true);
            $from = $this->input->post('s_from', true) ? trim($this->input->post('s_from', true)) : '';
            if ($member_type == 1) {
                $push_info = new \StdClass;
                $push_info->product = 'student';
                $push_info->target = $user_id;
                $push_info->content = $content;
                $push_info->type = 1;
                $push_info->member_id = $user_id;
                $push_info->member_type = 1;
                $push_info->beizhu = $s_beizhu;
                $push_info->from = $from;
            } else {
                $coach_info = $this->msystems->getCoachInfoById($coach_id);
                if ( ! empty($coach_info)) {
                    $user_phone = $coach_info['s_coach_phone'];
                } else {
                    $data = [
                        'code' => 400,
                        'msg' => '当前用户不存在',
                        'data' => new \stdClass
                    ];
                    $this->output->set_status_header(200)
                        ->set_content_type('application/json')
                        ->set_output(json_encode($data, JSON_PRETTY_PRINT))
                        ->_display();
                    exit;
                }
                $push_info = new \StdClass;
                $push_info->product = 'coach';
                $push_info->target = $user_phone;
                $push_info->content = $content;
                $push_info->type = 1;
                $push_info->member_id = $coach_id;
                $push_info->member_type = 2;
                $push_info->beizhu = $s_beizhu;
                $push_info->from = $from;
            }

        } elseif ( $type == 'sconf') {

            $param['l_school_id'] = $this->input->post('school_id', true) ? intval($this->input->post('school_id', true)) : '';
            $param['i_cancel_order_time'] = $this->input->post('cancel_order_time', true) ? intval($this->input->post('cancel_order_time', true)) : '';
            $param['i_sum_appoint_time'] = $this->input->post('sum_appoint_time', true) ? intval($this->input->post('sum_appoint_time', true)) : '';

            $time_list = $this->input->post('time_list', true) ? trim($this->input->post('time_list', true)) : '';
            $time_list = substr($time_list, 0, (strlen($time_list) - 1));
            $param['s_time_list'] = $time_list;
            $param['cancel_in_advance'] = $this->input->post('cancel_in_advance', true) ? intval($this->input->post('cancel_in_advance', true)) : '';
            $param['is_automatic'] = $this->input->post('is_auto', true) ? ($this->input->post('is_auto', true) === "false" ? 2 : 1) : 2;

            $tblname = $this->msystems->school_config_tbl;
            $action = 'add_school_config';
            $intro = '添加新的驾校时间配置' ;
            // check reptition
            $checkinfo = $this->msystems->checkSchoolConfig($param['l_school_id']);
            if ( ! empty ($checkinfo) ) {
                $data = [
                    'code' => 102,
                    'msg' => $checkinfo['school_name'].'已添加过',
                    'data' => $checkinfo['school_name']
                ];

                $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT))
                    ->_display();
                exit;
            }
        }

        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);

        if ( $type == 'message') {
            $result = $this->redis->rpush('msg_send_queue', json_encode($push_info, JSON_UNESCAPED_UNICODE));
            $tblname = $this->msystems->sms_tbl;
            $name = substr($tblname, 3, strlen($tblname));

        } else {
            $result = $this->msystems->add($param, $tblname);
            $name = substr($tblname, 3, strlen($tblname));
        }

        if ($type == 'sconf') {
            $this->mlog->action_log($action, $name, $param['l_school_id'], $login_id, $intro);
            $data = [
                'code' => 200,
                'msg' => '新增成功',
                'data' => $result
            ];
        }

        if ($result) {
            $this->mlog->action_log($action, $name, $result, $login_id, $intro);
            $data = [
                'code' => 200,
                'msg' => '新增成功',
                'data' => $result
            ];
        } else {
            $data = [
                'code' => 400,
                'msg' => '新增失败',
                'data' => $result
            ];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    /**
     * 修改标签[页面展示]
     * @param
     * @return viod
     */
    public function edit()
    {
        $this->mbase->loginauth();
        $id = $this->input->get('id', true) ? (int)$this->input->get('id', true) : '';
        $data = $this->msystems->getTagListById($id);
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/systems/edit', $data);
        $this->load->view(TEMPLATE.'/footer');

    }

    /**
     * 修改系统行为[页面展示]
     * @param
     * @return viod
     */
    public function editaction()
    {
        $this->mbase->loginauth();
        $id = $this->input->get('id', true) ? (int)$this->input->get('id', true) : '';
        $data = $this->msystems->getActionListById($id);
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/systems/editaction', $data);
        $this->load->view(TEMPLATE.'/footer');

    }

    /**
     * 修改标签[页面展示]
     * @param
     * @return viod
     */
    public function editpay()
    {
        $this->mbase->loginauth();
        $id = $this->input->get('id', true) ? (int)$this->input->get('id', true) : '';
        $data = $this->msystems->getPayConfigById($id);
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/systems/editpay', $data);
        $this->load->view(TEMPLATE.'/footer');

    }

    
    /**
     * 修改驾校配置[页面展示]
     * @param
     * @return viod
     */
    public function editschoolconfig()
    {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $id = $this->input->get('id', true) ? (int)$this->input->get('id', true) : '';
        $data = $this->msystems->getSchoolConfigById($id);
        $data['school_id'] = $school_id;
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/systems/editschoolconfig', $data);
        $this->load->view(TEMPLATE.'/footer');

    }

     /**
     * 修改数据[功能实现]
     * @param data
     * @return viod
     */
    public function editAjax()
    {
        if ( ! $this->input->is_ajax_request()) {
            $data = [
                'code'  => 400,
                'msg'   => '请求方式错误',
                'data'  => new \stdClass
            ];

            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT))
                ->_display();
            exit;
        }

        $type_arr = ['log', 'action', 'tag', 'utag', 'pay', 'message', 'sconf']; 
        $type = $this->input->get('type') ? trim($this->input->get('type')) : '';
        if ( ! in_array($type, $type_arr)) {
            $data = [
                'code'  => 400,
                'msg'   => $type.'类型不在规定范围内',
                'data'  => new \stdClass
            ];

            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT))
                ->_display();
            exit;
        }

        $param = [];
        if ( $type == 'tag') { // 修改系统标签

            $param['id'] = $this->input->post('id', true) ? intval($this->input->post('id', true)) : '';
            $taglist = $this->msystems->getTagListById($param['id']);
            $param['tag_name'] = $this->input->post('tag_name', true) ? trim($this->input->post('tag_name', true)) : $taglist['tag_name'];
            $param['tag_slug'] = $this->input->post('tag_slug', true) ? trim($this->input->post('tag_slug', true)) : $taglist['tag_slug'];
            $param['user_type'] = $this->input->post('user_type', true) ? intval($this->input->post('user_type', true)) : $taglist['user_type'];
            $param['order'] = $this->input->post('order', true) ? intval($this->input->post('order', true)) : $taglist['order'];
            $param['updatetime'] = time();
            $tblname = $this->msystems->tag_config_tbl;
            $field = 'id';
            $action = 'edit_tag_config';
            $intro = "修改系统标签[ID: ".$param['id']."]";

        } elseif ($type == 'action') {
            $param['id'] = $this->input->post('id', true) ? intval($this->input->post('id', true)) : '';
            $actionlist = $this->msystems->getActionListById($param['id']);
            $param['name'] = $this->input->post('name', true) ? trim($this->input->post('name', true)) : $actionlist['name'];
            $param['title'] = $this->input->post('title', true) ? trim($this->input->post('title', true)) : $actionlist['title'];
            $param['remark'] = $this->input->post('remark', true) ? trim($this->input->post('remark', true)) : $actionlist['remark'];
            $param['rule'] = $this->input->post('rule', true) ? trim($this->input->post('rule', true)) : '';
            $param['log'] = $this->input->post('log', true) ? trim($this->input->post('log', true)) : $actionlist['log'];
            $param['status'] = $this->input->post('status', true) ? ($this->input->post('status', true) === 'true' ? 1 : 2) : 2;
            $param['type'] = 1;
            $param['update_time'] = time();
            $tblname = $this->msystems->action_tbl;
            $field = 'id';
            $action = 'edit_system_action';
            $intro = "修改系统行为[ID: ".$param['id']."]";

        } elseif ($type == 'pay') {

            $param['id'] = $this->input->post('id', true) ? intval($this->input->post('id', true)) : '';
            $payconfiglist = $this->msystems->getPayConfigById($param['id']);
            $param['account_name'] = $this->input->post('account_name', true) ? trim($this->input->post('account_name', true)) : $payconfiglist['account_name'];
            $param['account_slug'] = $this->input->post('account_slug', true) ? trim($this->input->post('account_slug', true)) : $payconfiglist['account_slug'];
            $param['account_description'] = $this->input->post('description', true) ? trim($this->input->post('description', true)) : '';
            $param['pay_scope'] = $this->input->post('pay_scope', true) ? intval($this->input->post('pay_scope', true)) : 3;
            $param['pay_type'] = $this->input->post('pay_type', true) ? intval($this->input->post('pay_type', true)) : '';
            $param['order'] = $this->input->post('order', true) ? intval($this->input->post('order', true)) : '';
            $param['is_open'] = $this->input->post('is_open', true) ? ($this->input->post('is_open', true) === 'true' ? 1 : 2) : 2;
            $param['is_bank'] = $this->input->post('is_bank', true) ? ($this->input->post('is_bank', true) === 'true' ? 1 : 2) : 2;
            $param['addtime'] = time();
            $tblname = $this->msystems->pay_config_tbl;
            $field = 'id';
            $action = 'edit_pay_account';
            $intro = "修改支付配置信息[ID: ".$param['id']."]";

        } elseif ( $type == "sconf") {
            $param['l_school_id'] = $this->input->post('school_id', true) ? intval($this->input->post('school_id', true)) : '';
            $school_config_list = $this->msystems->getSchoolConfigById($param['l_school_id']);
            $param['i_cancel_order_time'] = $this->input->post('cancel_order_time', true) ? intval($this->input->post('cancel_order_time', true)) : $school_config_list['i_cancel_order_time'];
            $param['i_sum_appoint_time'] = $this->input->post('sum_appoint_time', true) ? intval($this->input->post('sum_appoint_time', true)) : $school_config_list['i_sum_appoint_time'];

            $time_list = $this->input->post('time_list', true) ? trim($this->input->post('time_list', true)) : '';
            $time_list = substr($time_list, 0, (strlen($time_list) - 1));
            $param['s_time_list'] = $time_list;
            $param['cancel_in_advance'] = $this->input->post('cancel_in_advance', true) ? intval($this->input->post('cancel_in_advance', true)) : $school_config_list['cancel_in_advance'];
            $param['is_automatic'] = $this->input->post('is_auto', true) ? ($this->input->post('is_auto', true) === "false" ? 2 : 1) : 2;
            $field = 'l_school_id';
            $tblname = $this->msystems->school_config_tbl;
            $action = 'edit_school_config';
            $intro = '修改驾校的时间配置' ;
        }

        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $result = $this->msystems->edit($field, $param, $tblname);
        $name = substr($tblname, 3, strlen($tblname));

        if ($result) {
            if ($type == 'sconf') {
                $res = $this->mlog->action_log($action, $name, $param['l_school_id'], $login_id, $intro);
            } else {
                $this->mlog->action_log($action, $name, $param['id'], $login_id, $intro);
            }
            $data = [
                'code' => 200,
                'msg' => '编辑成功',
                'data' => $result
            ];
        } else {
            $data = [
                'code' => 400,
                'msg' => '编辑失败',
                'data' => $result
            ];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;

    }

    /**
     * 删除数据 
     * @param $id
     * @return void
     **/
    public function delAjax()
    {
        $this->mbase->loginauth();
        if ( ! $this->input->is_ajax_request()) {
            $data = [
                'code'  => 400,
                'msg'   => '请求方式错误',
                'data'  => new \stdClass
            ];

            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT))
                ->_display();
            exit;
        }

        $type_arr = ['log', 'action', 'tag', 'utag', 'pay', 'message', 'sconf']; 
        $type = $this->input->get('type') ? trim($this->input->get('type')) : '';
        if ( ! in_array($type, $type_arr)) {
            $data = [
                'code'  => 400,
                'msg'   => $type.'类型不在规定范围内',
                'data'  => new \stdClass
            ];

            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT))
                ->_display();
            exit;
        } 

        if ( $type == 'log' ) { // 系统日志的删除

            $id = $this->input->post('id') ? $this->input->post('id') : '';
            $id_arr = explode(',', $id);
            $tblname = $this->msystems->action_log_tbl;
            $field = 'id';

        } elseif ( $type == 'action' ) { // 日志行为的删除

            $id = $this->input->post('id') ? $this->input->post('id') : '';
            $id_arr = explode(',', $id);
            $tblname = $this->msystems->action_tbl;
            $field = 'id';
            $action = 'del_tag_config';
            $intro = "删除日志行为[ID: ".$id."]";

        } elseif ( $type == 'tag' ) { // 标签管理的删除

            $id = $this->input->post('id') ? $this->input->post('id') : '';
            $id_arr = explode(',', $id);
            $tblname = $this->msystems->tag_config_tbl;
            $field = 'id';
            $action = 'del_tag_config';
            $intro = "删除系统标签[ID: ".$id."]";
        
        } elseif ( $type == 'utag' ) { // 删除用户标签

            $id = $this->input->post('id') ? $this->input->post('id') : '';
            $id_arr = explode(',', $id);
            $tblname = $this->msystems->user_tag_tbl;
            $field = 'id';
            $action = 'del_user_tag';
            $intro = "删除用户标签[ID: ".$id."]";

        } elseif ( $type == 'pay' ) { // 支付配置的删除

            $id = $this->input->post('id') ? $this->input->post('id') : '';
            $id_arr = explode(',', $id);
            $tblname = $this->msystems->pay_config_tbl;
            $field = 'id';
            $action = 'del_pay_account';
            $intro = "删除支付配置[ID: ".$id."]";

        } elseif ( $type == 'message' ) { // 信息的删除

            $id = $this->input->post('id') ? $this->input->post('id') : '';
            $param = [
                'id' => $id,
                'is_read' => 101,
            ];
            $tblname = $this->msystems->sms_tbl;
            $field = 'id';
            $action = 'del_sms';
            $intro = "删除通知信息[ID: ".$id."]";

        } elseif ( $type == 'sconf') {

            $id = $this->input->post('id') ? $this->input->post('id') : '';
            $id_arr = explode(',', $id);
            $tblname = $this->msystems->school_config_tbl;
            $field = 'l_school_id';
            $action = 'del_school_config';
            $intro = "删除驾校设置[ID: ".$id."]";

        }
        
        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $name = substr($tblname, 3, strlen($tblname));
        if ($type == 'message') {
            $result = $this->msystems->edit($field, $param, $tblname);
        } else {
            $result = $this->msystems->del($field, $id_arr, $tblname);
        }
        if ($result) {
            if ($type != 'log') {
                $this->mlog->action_log($action, $name, $id, $login_id, $intro);
            }
            $data = [
                'code' => 200,
                'msg' => '删除成功',
                'data' => $result
            ];
        } else {
            $data = [
                'code' => 400,
                'msg' => '删除失败',
                'data' => $result
            ];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;

    }

     /**
     * 获取搜索信息
     * @param $id
     * 
     * @return void
     **/
    public function searchAjax()
    {
        if ( ! $this->input->is_ajax_request()) {
            $data = [
                'code'  => 400,
                'msg'   => '请求方式错误',
                'data'  => new \stdClass
            ];

            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT))
                ->_display();
            exit;
        }

        $type_arr = ['stu', 'coach', 'school']; 
        $type = $this->input->get('type') ? trim($this->input->get('type')) : '';
        if ( ! in_array($type, $type_arr)) {
            $data = [
                'code'  => 400,
                'msg'   => $type.'类型不在规定范围内',
                'data'  => new \stdClass
            ];

            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT))
                ->_display();
            exit;
        } 
        
        $key = $this->input->post('key', true) ? $this->input->post('key', true) : '';

        if ( $type == 'school') {
            $tblname = $this->msystems->school_tbl;
            $condition = ['is_show' => 1];
            $select = ['l_school_id', 's_school_name'];
            $like = ['s_school_name' => $key];
            $result = $this->mbase->getSearchList($select, $condition, $like, $tblname, 15);
            
        } else {
            $result = $this->msystems->search($key, $type, 20);

        }
        if ($result) {
            $data = [
                'code'  => 200,
                'msg'   => '获取成功',
                'data'  => $result
            ];
        } else {
            $data = [
                'code'  => 400,
                'msg'   => '获取失败',
                'data'  => $result
            ];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;

    }

    /**
     * 获取时间配置数据
     * @param 
     * @return coach_time_config_list
     **/
    public function timelistAjax()
    {
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        if ($school_id == '') {
            $school_id = $this->input->get('sid', true) ? $this->input->get('sid', true) : '';
        }
        $time_config_list = $this->msystems->getCoachTimeConfigList($school_id);
        if ( ! empty($time_config_list)) 
        {
            $data = [
                'code'  => 200,
                'msg'   => '获取成功',
                'data'  => ['list' => $time_config_list],
            ];
        } else {
            $data = [
                'code'  => 400,
                'msg'   => '获取失败',
                'data'  => ['list' => $time_config_list],
            ];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    /**
     * 获取用户的角色列表
     * @param
     * @return $rolelist
     **/
    public function rolelistAjax()
    {
        $role_id = $this->mbase->getRoleIdFromLoginauth($this->session->loginauth);
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $role_list = $this->madmin->getRoleList($role_id, $school_id);
        if ( ! empty($role_list)) 
        {
            $data = [
                'code'  => 200,
                'msg'   => '获取成功',
                'data'  => $role_list,
            ];
        } else {
            $data = [
                'code'  => 400,
                'msg'   => '获取失败',
                'data'  => $role_list,
            ];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;

    }

    /**
     * 设置的开启状态
     * @param $id
     * @param $status
     * @return void
     **/
    public function handleOpenStatus()
    {
        $this->mbase->loginauth();
        if ( ! $this->input->is_ajax_request()) {
            $data = [
                'code'  => 400,
                'msg'   => '请求方式错误',
                'data'  => new \stdClass
            ];

            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT))
                ->_display();
            exit;
        }

        $param = [];
        $id = $this->input->post('id', true) ? $this->input->post('id', true) : '';
        $status = $this->input->post('status', true) ? $this->input->post('status', true) : '';
        $param = [
            'id' => $id,
            'is_open' => $status
        ];
        $field = 'id';
        $tblname = $this->msystems->pay_config_tbl;

        $action = 'set_payaccount_status';
        $name = substr($tblname, 3, strlen($tblname));
        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);

        if ($status == 1) {
            $intro = "设置日志行为状态为开启";
        } else {
            $intro = "设置日志行为状态为关闭";
        }

        $update_ok = $this->msystems->edit($field, $param, $tblname);

        if ($update_ok) {
            $this->mlog->action_log($action, $name, $id, $login_id, $intro);
            $data = [
                'code'  => 200,
                'msg'   => '设置成功',
                'data'  => ['success' => $update_ok]
            ];
        } else {
            $data = [
                'code'  => 400,
                'msg'   => '设置失败',
                'data'  => ['defult' => $update_ok]
            ];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    /**
     * 设置日志行为的开启状态
     * @param $id
     * @param $status
     * @return void
     **/
    public function handleStatus()
    {
        $this->mbase->loginauth();
        if ( ! $this->input->is_ajax_request()) {
            $data = [
                'code'  => 400,
                'msg'   => '请求方式错误',
                'data'  => new \stdClass
            ];

            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT))
                ->_display();
            exit;
        }

        $param = [];
        $id = $this->input->post('id', true) ? $this->input->post('id', true) : '';
        $status = $this->input->post('status', true) ? $this->input->post('status', true) : '';
        $param = [
            'id' => $id,
            'status' => $status
        ];
        $field = 'id';
        $tblname = $this->msystems->action_tbl;
        $action = 'set_action_status';
        $name = substr($tblname, 3, strlen($tblname));
        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);

        if ($status == 1) {
            $intro = "设置日志行为状态为开启";
        } else {
            $intro = "设置日志行为状态为关闭";
        }

        $update_ok = $this->msystems->edit($field, $param, $tblname);
        if ($update_ok) {
            $this->mlog->action_log($action, $name, $id, $login_id, $intro);
            $data = [
                'code'  => 200,
                'msg'   => '设置成功',
                'data'  => ['success' => $update_ok]
            ];
        } else {
            $data = [
                'code'  => 400,
                'msg'   => '设置失败',
                'data'  => ['defult' => $update_ok]
            ];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;

    }
   
    /**
     * 设置驾校设置的开启状态
     * @param $id
     * @param $status
     * @return void
     **/
    public function handleAutoStatus()
    {
        $this->mbase->loginauth();
        if ( ! $this->input->is_ajax_request()) {
            $data = [
                'code'  => 400,
                'msg'   => '请求方式错误',
                'data'  => new \stdClass
            ];

            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT))
                ->_display();
            exit;
        }

        $param = [];
        $id = $this->input->post('id', true) ? $this->input->post('id', true) : '';
        $status = $this->input->post('status', true) ? $this->input->post('status', true) : '';
        $param = [
            'l_school_id' => $id,
            'is_automatic' => $status
        ];
        $field = 'l_school_id';
        $tblname = $this->msystems->school_config_tbl;
        $action = '设置驾校设置中的自动状态';
        $name = substr($tblname, 3, strlen($tblname));
        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);

        if ($status == 1) {
            $intro = "设置驾校设置中的自动状态为自动";
        } else {
            $intro = "设置驾校设置中的自动状态为手动";
        }

        $update_ok = $this->msystems->edit($field, $param, $tblname);
        if ($update_ok) {
            $this->mlog->action_log($action, $name, $id, $login_id, $intro);
            $data = [
                'code'  => 200,
                'msg'   => '设置成功',
                'data'  => ['success' => $update_ok]
            ];
        } else {
            $data = [
                'code'  => 400,
                'msg'   => '设置失败',
                'data'  => ['defult' => $update_ok]
            ];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;

    }
   


}