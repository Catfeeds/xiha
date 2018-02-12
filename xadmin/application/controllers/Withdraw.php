<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Withdraw extends CI_Controller {

    static $limit = 10;
    static $page = 1;

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('url');

        $this->load->model('mbase');
        $this->load->model('mwithdraw');
    }

    public function index()
    {
        $this->load->view(TEMPLATE . '/header');
        $this->load->view(TEMPLATE . '/withdraw/index');
        $this->load->view(TEMPLATE . '/footer');
    }

    public function request()
    {
        $this->mbase->loginauth();
        $loginauth = $this->session->loginauth;
        $loginauth_arr = $loginauth ? explode('|', $loginauth) : [];
        $content = isset($loginauth_arr[4]) ? $loginauth_arr[4] : '';
        $admin_name = isset($loginauth_arr[1]) ? $loginauth_arr[1] : '';
        $school_id = isset($loginauth_arr[5]) ? $loginauth_arr[5] : 0;
        if ($school_id == 0) {
            echo 'no school no money';
            exit();
        }
        // 获取当前可提现余额
        $total_price = $this->mbalance
            ->getBalanceByUtypeAndUid('school', $school_id);
        $data = [
            'content'     => $content,
            'admin_name'  => $admin_name,
            'total_price' => $total_price
        ];
        $this->load->view(TEMPLATE . '/header');
        $this->load->view(TEMPLATE . '/withdraw/request', $data);
        $this->load->view(TEMPLATE . '/footer');
    }

    public function my()
    {
        $this->mbase->loginauth();

        $this->load->view(TEMPLATE . '/header');
        $this->load->view(TEMPLATE . '/withdraw/my');
        $this->load->view(TEMPLATE . '/footer');
    }

    /**
     * 我的提现个人中心-获取提现列表ajax
     */
    public function myAjax()
    {
        $this->mbase->loginauth();
        $p = $this->input->post('p') ? intval($this->input->post('p')) : self::$page;
        $s = $this->input->post('s') ? intval($this->input->post('s')) : self::$limit;

        $school_id = $this->mbase->getSchoolidFromLoginauth($this->session->loginauth);
        if ($school_id > 0) {
            // get withdraw list
            $start = ($p - 1) * $s;
            $_result = $this->mwithdraw->myRequestList(['uid' => $school_id, 'utype' => 'school'], $start, $s);
            $page_info = ['pagenum' => $p, 'count' => $_result['count'], 'list' => $_result['items']];
        } else {
            // return empty list
            $page_info = ['pagenum' => 0, 'count' => 0, 'list' => []];
        }

        $data = ['code' => 200, 'msg' => '获取成功', 'data' => $page_info];

        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                    ->_display();
        exit;
    }

    /**
     * 提现个人中心的统计
     */
    public function mySuminfoAjax()
    {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolidFromLoginauth($this->session->loginauth);

        $money_field = 'money';
        $all_condition = ['utype' => 'school', 'uid' => $school_id];
        $done_condition = ['completed' => 1, 'utype' => 'school', 'uid' => $school_id];
        $suminfo_all_count = $this->mwithdraw->count($all_condition);
        $suminfo_all_money = $this->mwithdraw->sum($money_field, $all_condition);
        $suminfo_done_count = $this->mwithdraw->count($done_condition);
        $suminfo_done_money = $this->mwithdraw->sum($money_field, $done_condition);

        $suminfo = [
            'all' => [
                'num' => $suminfo_all_count,
                'money' => $suminfo_all_money,
            ],
            'done' => [
                'num' => $suminfo_done_count,
                'money' => $suminfo_done_money,
            ],
        ];
        $data = ['code' => 200, 'msg' => '获取成功', 'data' => ['suminfo' => $suminfo]];

        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                    ->_display();
        exit;
    }

    public function admin()
    {
        $this->load->view(TEMPLATE . '/header');
        $this->load->view(TEMPLATE . '/withdraw/admin');
        $this->load->view(TEMPLATE . '/footer');
    }

    /**
     * 管理员审核提现请求-获取提现列表ajax
     */
    public function adminAjax()
    {
        $this->mbase->loginauth();
        $p = $this->input->post('p') ? intval($this->input->post('p')) : self::$page;
        $s = $this->input->post('s') ? intval($this->input->post('s')) : self::$limit;
        $process_status = $this->input->post('process_status', true) ? $this->input->post('process_status') : '';
        $where = [];
        if (! empty($process_status) && in_array($process_status, ['created', 'reviewed', 'transferred', 'completed'])) {
            $where[$process_status] = 1;
        }

        $school_id = $this->mbase->getSchoolidFromLoginauth($this->session->loginauth);
        log_message('debug', 'school_id:'.$school_id);
        if ($school_id == 0) {
            // get withdraw list
            $start = ($p - 1) * $s;
            $_result = $this->mwithdraw->myRequestList($where, $start, $s);
            $page_info = ['pagenum' => $p, 'count' => $_result['count'], 'list' => $_result['items']];
        } else {
            // return empty list
            $page_info = ['pagenum' => 0, 'count' => 0, 'list' => []];
        }

        $data = ['code' => 200, 'msg' => '获取成功', 'data' => $page_info];

        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                    ->_display();
        exit;
    }
}
