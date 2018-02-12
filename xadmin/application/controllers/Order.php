<?php
/**
 * PHP version 7.0
 *
 * 订单类，包含预约计时和报名班制
 *
 * @category Order
 * @package  Order
 * @author   chenxi <chenxi@xihaxueche.com>
 * @license  Private http://xihaxueche.com
 * @version  SVN: $r0000$ in development
 * @link     http://xihaxueche.com
 * @return   void
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 订单类，包含预约计时和报名班制
 *
 * @category Order
 * @package  Order
 * @author   chenxi <chenxi@xihaxueche.com>
 * @license  Private http://xihaxueche.com
 * @link     http://xihaxueche.com
 * @return   void
 */
class Order extends CI_Controller
{

    static $limit = 10;
    static $page = 1;

    /**
     * 构造
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['form', 'url', 'common']);
        $this->load->model('mbase');
        $this->load->model('morder');
        $this->load->model('mbalance');
        $this->load->model('mcity');
        $this->load->model('mtags');
        $this->load->model('mcoupon');
    }

    /**
     * 提现
     *
     * @return void
     */
    public function index()
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
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/order/index', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 报名班制订单列表
     *
     * @return void
     */
    public function shifts()
    {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $online_type = $this->input->get('ot') ? $this->input->get('ot') : 'online'; // 线上 or 线下
        $data = ['school_id' => $school_id, 'online_type' => $online_type];
        $this->load->view(TEMPLATE.'/header');
        if ($online_type == 'line') { //线下支付
            $this->load->view(TEMPLATE.'/order/lineshifts', $data);
        } else {
            $this->load->view(TEMPLATE.'/order/shifts', $data);
        }
        $this->load->view(TEMPLATE.'/footer');
    }

    
    // 报名班制列表ajax
    public function shiftsorderajax()
    {
        $this->mbase->loginauth();
        if (!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $param = [];
        $page = $this->input->post('p', true) ? intval($this->input->post('p', true)) : self::$page;
        $limit = $this->input->post('s', true) ? intval($this->input->post('s', true)) : self::$limit;
        $os = $this->input->post('os', true) ? trim($this->input->post('os', true)) : 'all';
        $ot = $this->input->post('ot', true) ? trim($this->input->post('ot', true)) : 'online';
        
        $online_arr = ['all' => 200, 'paid' => 1, 'refunding' => 2, 'cancel' => 3, 'unpaid' => 4, 'refunded' => 1007, 'deleted' => 101, 'completed' => 1011 ];
        $line_arr = ['all' => 200, 'paid' => 3, 'refunding' => 4, 'cancel' => 2, 'unpaid' => 1, 'refunded' => 1007, 'deleted' => 101, 'completed' => 1011 ];
        if ( $ot == 'online') { // 线上
            $order_status = $online_arr[$os];
        } else { // 线下
            $order_status = $line_arr[$os];
        }
        
        $param['order_status'] = $order_status;
        $param['pay_type'] = $this->input->post('pt', true) ? trim($this->input->post('pt', true)) : '';
        $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';
        $pageinfo = $this->morder->getShiftsOrderPageNum($school_id, $param, $limit, $ot);
        $page = $page < $pageinfo['pagenum'] || $pageinfo['pagenum'] == 0 ? $page : $pageinfo['pagenum'];
        $start = ($page - 1) * $limit;

        $orderinfo = $this->morder->getSchoolOrderList($school_id, $param, $start, $limit, $ot);
        $orderlist['p'] = $page;
        $orderlist['count'] = $pageinfo['count'];
        $orderlist['pagenum'] = $pageinfo['pagenum'];
        $orderlist['list'] = $orderinfo;
        $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$orderlist];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    /**
     * 设置报名班制的订单状态
     * @param   
     * @return void
     **/
    public function setOrderAjax()
    {
        $this->mbase->loginauth();
        if (!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $online_type = $this->input->get('ot', true) ? trim($this->input->get('ot', true)) : "";
        if ( ! in_array($online_type, ['online', 'line'])) {
            $data = ['code' => 400, 'msg' => '支付状态不在规定范围内', 'data' => ''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        $id = $this->input->post('id', true) ? intval($this->input->post('id', true)) : 0;
        $status = $this->input->post('status', true) ? intval($this->input->post('status', true)) : 0;
        $old_status = $this->morder->getSchoolOrderStatus($id);

        $data = ['id' => $id, 'so_order_status' => $status];
        $action = 'set_schoolorder_status';
        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $tblname = $this->morder->shiftsorder_tablename;
        $name = substr($tblname, 3, strlen($tblname));
        if ($online_type == 'online') { // 线上
            $order_status_arr = [
                '1'     => '已付款',
                '2'     => '退款中',
                '3'     => '已取消',
                '4'     => '未付款',
                '1011'  => '已完成',
                '1007'  => '已退款',
                '101'   => '已删除'
            ];
        } else { // 线下
            $order_status_arr = [
                '1'     => '未付款',
                '2'     => '已取消',
                '3'     => '已付款',
                '4'     => '退款中',
                '1011'  => '已完成',
                '1007'  => '已退款',
                '101'   => '已删除'
            ];
        }

        $intro = "将订单的状态由".$order_status_arr[$old_status]."设置成".$order_status_arr[$status];

        $result = $this->mbase->updateData($tblname, 'id', $id, $data);
        if ($result) {
            $this->mlog->action_log($action, $name, $id, $login_id, $intro);
            $data = ['code' => 200, 'msg' => '设置成功', 'data' => $result];
        } else {
            $data = ['code' => 400, 'msg' => '设置失败', 'data' => ''];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    /**
     * 添加报名班制
     *
     * @return void
     */
     public function add()
     {
         $this->mbase->loginauth();
         $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
         $data = ['school_id' => $school_id];
         $this->load->view(TEMPLATE.'/header');
         $this->load->view(TEMPLATE.'/order/add', $data);
         $this->load->view(TEMPLATE.'/footer');
     }
 
     /**
      * 编辑报名班制
      *
      * @return void
      */
     public function edit()
     {
         $this->mbase->loginauth();
         $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
         $id = $this->input->get('id') ? $this->input->get('id') : 0;
         $data = $this->morder->getShiftOrderInfoById($id);
         $data['sid'] = $school_id;
         if (!$data) {
             show_404();
         }
         $this->load->view(TEMPLATE.'/header');
         $this->load->view(TEMPLATE.'/order/edit', $data);
         $this->load->view(TEMPLATE.'/footer');
     }

     /**
      * 获取驾校的班制信息
      * @param      int     $school_id      驾校ID
      * @return     void
      **/
    public function schoolShiftsAjax()
    {
        $this->mbase->loginauth();
        if (!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        $list = [];
        $school_id = $this->input->post('school_id', true) ? intval($this->input->post('school_id', true)) : 0;
        if ($school_id > 0) {
            $wherecondition = ['sh_school_id' => $school_id, 'deleted' => 1];
            $select = 'id, sh_school_id, sh_title';
            $list = $this->mschool->getShiftsListByCondition($select, $wherecondition);
        } 
        $data = ['code'=>200, 'msg' => '获取成功', 'data' => $list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    // 添加报名班制ajax
    public function addajax()
    {
        $this->mbase->loginauth();
        if (!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $order_data = [];
        $user_data = [];
        $userinfo_data = [];
        $so_school_id = $this->input->post('so_school_id', true) ? intval($this->input->post('so_school_id', true)) : 0;
        $user_name = $this->input->post('user_name', true) ? trim($this->input->post('user_name', true)) : '';
        $user_phone = $this->input->post('user_phone', true) ? trim($this->input->post('user_phone', true)) : '';
        $identity_id = $this->input->post('identity_id', true) ? trim($this->input->post('identity_id', true)) : '';
        $license_id = $this->input->post('license', true) ? intval($this->input->post('license', true)) : '';
        $license_name = $this->morder->getLicenseName($license_id);
        $so_shifts_id = $this->input->post('so_shifts_id', true) ? intval($this->input->post('so_shifts_id', true)) : 0;
        $free_study_hour = $this->input->post('free_study_hour', true) ? trim($this->input->post('free_study_hour', true)) : '-1';
        $original_price = $this->input->post('original_price', true) ? intval($this->input->post('original_price', true)) : 3000;
        $final_price = $this->input->post('final_price', true) ? intval($this->input->post('final_price', true)) : 3000;
        $total_price = $this->input->post('total_price', true) ? intval($this->input->post('total_price', true)) : 3000;
        $order_no = $this->input->post('order_no', true) ? trim($this->input->post('order_no', true)) : '';
        $pay_type = $this->input->post('pay_type', true) ? trim($this->input->post('pay_type', true)) : '';
        $order_status = $this->input->post('order_status', true) ? trim($this->input->post('order_status', true)) : '';
        $so_pay_type = $pay_type == '线下' ? 2 : 2;
        $so_order_status = $order_status == '已付款' ? 3 : 3;
        $zhifu_dm = guid(false);
        // 检测同一用户订单是否存在
        $order_info = $this->morder->checkOrder($user_phone); 
        if ( ! empty($order_info)) {
            $data = ['code' => 100, 'msg' => '此用户已报过名', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $user_data = [
            's_username' => $user_name,
            's_password' => md5('123456'),
            'i_user_type' => 0,
            'i_status' => 0,
            's_real_name' => $user_name,
            's_phone' => $user_phone,
            'is_first' => 0,
            'is_signup' => $so_school_id,
            's_imgurl' => '',
            'content' => '',
            'addtime' => time(),
            'updatetime' => time(),
        ];
        $user_tablename = $this->morder->user_tablename;
        $where = ['i_status' => 0, 'i_user_type' => 0, 's_phone' => $user_phone];
        $new_user = $this->mbase->_insert($user_tablename, $user_data, $where);
        $user_id = 0;
        if ($new_user) {
            $user_info = $this->morder->getUserInfoByphone($user_phone);
            $user_id = $user_info['l_user_id'];
            $userinfo_data = [
                'user_id' => $user_id,
                'sex' => 1,
                'age' => 18,
                'identity_id' => $identity_id,
                'license_num' => 0,
                'school_id' => $so_school_id,
                'lesson_id' => 1,
                'lesson_name' => '科目一',
                'license_id' => $license_id,
                'license_name' => $license_name,
                'exam_license_name' => $license_name,
                'learncar_status' => "科目一学习中",
                'addtime' => time(),
                'updatetime' => time()
            ];    
            $userinfo_tablename = $this->morder->userinfo_tablename;
            $wherecondition = ['user_id' => $user_id];
            $res = $this->mbase->_insert($userinfo_tablename, $userinfo_data, $wherecondition);
        }
       
        $order_data = [
            'so_school_id' => $so_school_id,
            'so_final_price' => $final_price,
            'so_original_price' => $original_price,
            'so_total_price' => $total_price,
            'so_shifts_id' => $so_shifts_id,
            'so_pay_type' => $so_pay_type,
            'so_order_status' => $so_order_status,
            'so_comment_status' => 1    ,
            'so_order_no' => $order_no,
            's_zhifu_dm' => $zhifu_dm,
            'dt_zhifu_time' => strtotime(time()),
            'so_user_id' => $user_id,
            'so_coach_id' => 0,
            'so_phone' => $user_phone,
            'so_user_identity_id' => $identity_id,
            'so_licence' => $license_name,
            'so_username' => $user_name,
            'free_study_hour' => $free_study_hour,
            'addtime'=> time(),
        ];

        $tablename = $this->morder->shiftsorder_tablename;
        $action = 'add_school_order';
        $intro = "添加新的报名班制订单";
        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $name = substr($tblname, 3, strlen($tblname));

        $result = $this->morder->addOrder($tablename, $order_data);
        if ($result) {
            $this->mlog->action_log($action, $name, $result, $login_id, $intro);
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

    // 编辑报名班制[功能实现]
    public function editajax()
    {
        $this->mbase->loginauth();
        if (!$this->input->is_ajax_request()) {
            $data = ['code' => 100, 'msg' => '错误请求方式', 'data' => ''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
       
        $order_data = [];
        $user_data = [];
        $userinfo_data = [];
        $id = $this->input->post('id', true) ? intval($this->input->post('id', true)) : 0;
        $shiftsOrderList = $this->morder->getShiftOrderInfoById($id);
      
        $user_id = $this->input->post('user_id', true) ? intval($this->input->post('user_id', true)) : 0;
        $so_school_id = $this->input->post('so_school_id', true) ? intval($this->input->post('so_school_id', true)) : 0;
        $user_name = $this->input->post('user_name', true) ? trim($this->input->post('user_name', true)) : '';
        $user_phone = $this->input->post('user_phone', true) ? trim($this->input->post('user_phone', true)) : '';
        $identity_id = $this->input->post('identity_id', true) ? trim($this->input->post('identity_id', true)) : '';
        $license_id = $this->input->post('license', true) ? $this->input->post('license', true) : '';
        if (intval($license_id) == 0) {
            $license_name = $license_id;
        } else {
            $license_name = $this->morder->getLicenseName($license_id);
        }

        $so_shifts_id = $this->input->post('so_shifts_id', true) ? intval($this->input->post('so_shifts_id', true)) : 0;
        $free_study_hour = $this->input->post('free_study_hour', true) ? trim($this->input->post('free_study_hour', true)) : '-1';
        $original_price = $this->input->post('original_price', true) ? intval($this->input->post('original_price', true)) : 3000;
        $final_price = $this->input->post('final_price', true) ? intval($this->input->post('final_price', true)) : 3000;
        $total_price = $this->input->post('total_price', true) ? intval($this->input->post('total_price', true)) : 3000;
        $order_no = $this->input->post('order_no', true) ? trim($this->input->post('order_no', true)) : '';
        $zhifu_dm = $shiftsOrderList ? $shiftsOrderList['s_zhifu_dm'] : guid(false);

        $pay_type = $this->input->post('pay_type', true) ? trim($this->input->post('pay_type', true)) : '';
        $order_status = $this->input->post('order_status', true) ? trim($this->input->post('order_status', true)) : '';

        $pay_type_arr = ['支付宝' => '1', '线下' => '2', '微信' => '3', '银联' => '4'];
        $so_pay_type = $pay_type_arr[$pay_type];
        $order_status_arr = [];
        if ( in_array($so_pay_type, [1, 3, 4])) {
            $order_status_arr = ['已付款' => '1', '退款中' => '2', '已取消' => '3', '未付款' => '4', '已完成' => '1011', '已退款' => '1007', '已删除' => '101'];
        } else {
            $order_status_arr = ['未付款' => '1', '已取消' => '2', '已付款' => '3', '退款中' => '4', '已完成' => '1011','已退款' => '1007', '已删除' => '101'];
        }
        $so_order_status = $order_status_arr[$order_status];

        if ($user_name != $shiftsOrderList['so_username'] 
            OR $user_phone != $shiftsOrderList['so_phone']) 
        {
            $user_data = ['l_user_id' => $user_id, 's_username' => $user_name, 's_real_name' => $user_name, 's_phone' => $user_phone,];
            $user_tablename = $this->morder->user_tablename;
            $update_user = $this->mbase->updateData($user_tablename, 'l_user_id', $user_id, $user_data);
        }

        if ($identity_id != $shiftsOrderList['so_user_identity_id']) {
            $userinfo_data = ['user_id' => $user_id, 'identity_id' => $identity_id, 'updatetime' => time()];  
            $userinfo_tablename = $this->morder->userinfo_tablename;
            $update_userinfo = $this->mbase->updateData($userinfo_tablename, 'user_id', $user_id, $userinfo_data);
        }
       
        $order_data = [
            'so_school_id' => $so_school_id,
            'so_final_price' => $final_price,
            'so_original_price' => $original_price,
            'so_total_price' => $total_price,
            'so_shifts_id' => $so_shifts_id,
            'so_pay_type' => $so_pay_type,
            'so_order_status' => $so_order_status,
            'so_comment_status' => 1    ,
            'so_order_no' => $order_no,
            's_zhifu_dm' => $zhifu_dm,
            'dt_zhifu_time' => strtotime(time()),
            'so_user_id' => $user_id,
            'so_coach_id' => 0,
            'so_phone' => $user_phone,
            'so_user_identity_id' => $identity_id,
            'so_licence' => $license_name,
            'so_username' => $user_name,
            'free_study_hour' => $free_study_hour,
            'addtime'=> time(),
        ];

        $tablename = $this->morder->shiftsorder_tablename;
        $action = 'edit_school_order';
        $intro = "编辑报名班制订单";
        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $name = substr($tablename, 3, strlen($tablename));

        $result = $this->mbase->updateData($tablename, 'id', $id, $order_data);
        if ($result) {
            $this->mlog->action_log($action, $name, $id, $login_id, $intro);
            $data = ['code' => 200, 'msg' => '修改成功', 'data' => ['result'=>$result]];
        } else {
            $data = ['code' => 100, 'msg' => '修改失败', 'data' => ['result'=>$result]];
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
        if (!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $id = $this->input->post('id') ? intval($this->input->post('id')) : 0;
        if ($id == '') {
            $data = ['code'=>400, 'msg'=>'参数错误', 'data'=>[]];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $wherecondition = ['l_order_id'=> $id];
        $res = $this->morder->delInfo($this->morder->shiftsorder_tablename, $this->morder->orderinfo_tablename, $wherecondition);
        if ($res) {
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
        if (!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
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
        if (!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
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
        if (!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
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
        $id = $this->input->get('id') ? $this->input->get('id') : 0;

        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/order/preview');
        $this->load->view(TEMPLATE.'/footer');
    }

    // 上架下架
    public function show() {
        $this->mbase->loginauth();
        if (!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $id = $this->input->post('id') ? intval($this->input->post('id')) : 0;
        $status = $this->input->post('status') ? intval($this->input->post('status')) : 0;
        $wherecondition = [
            'l_order_id' => $id,
        ];
        $_data = [
            'i_status' => $status
        ];
        $res = $this->morder->editOrderStatus($_data, $wherecondition);
        if ($res) {
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

    // 添加
    public function addshifts() {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/order/addshifts');
        $this->load->view(TEMPLATE.'/footer');
    }

    // 编辑
    public function editshifts() {
        $this->mbase->loginauth();
        $id = $this->input->get('id') ? $this->input->get('id') : 0;
        $data = $this->morder->getordershiftsInfo($id);
        // var_dump($data);
        if (!$data) show_404();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/order/editshifts', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    // 删除班制
    public function delshiftsajax() {
        $this->mbase->loginauth();
        if (!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $id = $this->input->post('id') ? intval($this->input->post('id')) : 0;
        if ($id == '') {
            $data = ['code'=>400, 'msg'=>'参数错误', 'data'=>[]];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $params = ['id'=> $id];
        $res = $this->morder->delInfo($this->morder->shiftsorder_tablename, $params);
        if ($res) {
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

    // 根据条件获取班制列表
    public function shiftsajax() {
        $this->mbase->loginauth();
        if (!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $params = $this->input->post();
        $owner_id = $params['owner_id'] ? intval($params['owner_id']) : 0;
        $owner_type = $params['owner_type'] ? intval($params['owner_type']) : 1;

        if ($owner_type == 1) { //驾校
            $wherecondition = ['sh_school_id'=>$owner_id, 'deleted'=>1];
        } else {
            $wherecondition = ['coach_id'=>$owner_id, 'deleted'=>1];
        }
        $select = 'id, sh_school_id, sh_title';
        $list = $this->mschool->getShiftsListByCondition($select, $wherecondition);
        $data = ['code'=>200, 'msg'=>'获取班制列表成功', 'data'=>$list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    // 上下架班制
    public function showshifts() {
        $this->mbase->loginauth();
        if (!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $id = $this->input->post('id') ? intval($this->input->post('id')) : 0;
        $status = $this->input->post('status') ? intval($this->input->post('status')) : 0;
        $data = [
            'id' => $id,
            'deleted' => $status
        ];
        $res = $this->morder->editorderShiftsInfo($data);
        if ($res) {
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
        if (!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $key = $this->input->post('key') ? trim($this->input->post('key')) : '';
        $result = $this->morder->getSearchorderList($key);
        if ($result) {
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

    // 添加班制
    public function addshiftsajax()
    {
        $this->mbase->loginauth();
        if (!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $params = $this->input->post();
        $_data = [
            'sh_order_id'=> isset($params['sh_order_id']) ? $params['sh_order_id'] : 0,
            'coach_id'=> isset($params['coach_id']) ? $params['coach_id'] : 0,
            'sh_title'=> isset($params['sh_title']) ? $params['sh_title'] : '',
            'sh_money'=> isset($params['sh_money']) ? $params['sh_money'] : 0,
            'sh_original_money'=> isset($params['sh_original_money']) ? $params['sh_original_money'] : 0,
            'sh_type'=> isset($params['sh_type']) ? $params['sh_type'] : 2,
            'sh_description_2'=> isset($params['sh_description_2']) ? $params['sh_description_2'] : '',
            'sh_info'=> isset($params['sh_info']) ? $params['sh_info'] : '',
            'is_package'=> isset($params['is_package']) ? ($params['is_package'] === 'true' ? 1 : 2) : 2,
            'sh_description_1'=> isset($params['sh_description_1']) ? $params['sh_description_1'] : '',
            'sh_license_name'=> isset($params['sh_license_name']) ? explode('|', $params['sh_license'])[1] : '',
            'sh_license_id'=> isset($params['sh_license_id']) ? explode('|', $params['sh_license'])[0] : 0,
            'sh_tag'=> isset($params['sh_tag']) ? explode('|', $params['sh_tag_info'])[1] : '',
            'sh_tag_id'=> isset($params['sh_tag_id']) ? explode('|', $params['sh_tag_info'])[0] : 0,
            'is_promote'=> isset($params['is_promote']) ? ($params['is_promote'] === 'true' ? 1 : 2) : 2,
            'coupon_id'=> isset($params['coupon_id']) ? $params['coupon_id'] : 0,
            'order'=> 50,
            'updatetime'=> time(),
            'deleted'=> isset($params['deleted']) ? ($params['deleted'] === 'true' ? 1 : 2) : 2,
            'addtime'=> time(),
        ];
        $result = $this->morder->addorderShiftsInfo($_data);
        if ($result) {
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

    // 编辑班制
    public function editshiftsajax()
    {
        $this->mbase->loginauth();
        if (!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $params = $this->input->post();
        $_data = [
            'id'=> isset($params['id']) ? $params['id'] : 0,
            'sh_order_id'=> isset($params['sh_order_id']) ? $params['sh_order_id'] : 0,
            'coach_id'=> isset($params['coach_id']) ? $params['coach_id'] : 0,
            'sh_title'=> isset($params['sh_title']) ? $params['sh_title'] : '',
            'sh_money'=> isset($params['sh_money']) ? $params['sh_money'] : 0,
            'sh_original_money'=> isset($params['sh_original_money']) ? $params['sh_original_money'] : 0,
            'sh_type'=> isset($params['sh_type']) ? $params['sh_type'] : 2,
            'sh_description_2'=> isset($params['sh_description_2']) ? $params['sh_description_2'] : '',
            'sh_info'=> isset($params['sh_info']) ? $params['sh_info'] : '',
            'is_package'=> isset($params['is_package']) ? ($params['is_package'] === 'true' ? 1 : 2) : 2,
            'sh_description_1'=> isset($params['sh_description_1']) ? $params['sh_description_1'] : '',
            'sh_license_name'=> isset($params['sh_license_name']) ? explode('|', $params['sh_license'])[1] : '',
            'sh_license_id'=> isset($params['sh_license_id']) ? explode('|', $params['sh_license'])[0] : 0,
            'sh_tag'=> isset($params['sh_tag']) ? explode('|', $params['sh_tag_info'])[1] : '',
            'sh_tag_id'=> isset($params['sh_tag_id']) ? explode('|', $params['sh_tag_info'])[0] : 0,
            'is_promote'=> isset($params['is_promote']) ? ($params['is_promote'] === 'true' ? 1 : 2) : 2,
            'coupon_id'=> isset($params['coupon_id']) ? $params['coupon_id'] : 0,
            'updatetime'=> time(),
            'deleted'=> isset($params['deleted']) ? ($params['deleted'] === 'true' ? 1 : 2) : 2,
        ];
        $result = $this->morder->editorderShiftsInfo($_data);
        if ($result) {
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

    // 添加标签
    public function addsystagajax() {
        $this->mbase->loginauth();
        if (!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $params = $this->input->post();
        $_data = array(
            'tag_name'=> isset($params['tag_name']) ? $params['tag_name'] : '',
            'tag_slug'=> isset($params['tag_slug']) ? $params['tag_slug'] : '',
            'order_type'=> isset($params['order_type']) ? $params['order_type'] : 2,
            'order'=>50,
            'addtime'=>time(),
            'updatetime'=>time(),
        );
        $result = $this->mtags->addTag($this->mtags->systags_tablename, $_data);
        if ($result) {
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

    public function systagsajax() {
        $this->mbase->loginauth();
        if (!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $tags_list = $this->mtags->getSysTagListByType(2);
        $data = ['code'=>200, 'msg'=>'获取标签列表成功', 'data'=>$tags_list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    // 获取牌照列表
    public function liceconfigajax() {
        $this->mbase->loginauth();
        if (!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $license_config_list = $this->morder->getLicenseConfigList();
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
        if (!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
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
        if (!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $pass = $this->input->post('pass') ? trim($this->input->post('pass')) : 'xiha123456';
        $order_id = $this->input->post('uid') ? trim($this->input->post('uid')) : 0;
        $wherecondition = [
            'l_order_id'=> $order_id
        ];
        $res = $this->morder->changeorderPass($wherecondition, ['s_password'=>md5($pass)]);

        if ($res) {
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

    /**
     * 删除报名班制订单
     * @param   int     $id     订单ID
     * @return  void
     **/
     public function delShiftsOrderAjax()
     {
         $this->mbase->loginauth();
         if (!$this->input->is_ajax_request()) {
             $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
             $this->output->set_status_header(200)
                 ->set_content_type('application/json')
                 ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                 ->_display();
             exit;
         }
 
         $id = $this->input->post('id', true) ? intval($this->input->post('id', true)) : 0;
        //  $status = $this->input->post('status', true) ? intval($this->input->post('status', true)) : 0;
         $data = ['id' => $id, 'so_order_status' => 101];
         $tblname = $this->morder->shiftsorder_tablename;
         $action = 'del_school_orders';
         $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
         $name = substr($tblname, 3, strlen($tblname));
         $intro = "删除报名班制订单( ID：".$id." )";
 
         $result = $this->mbase->updateData($tblname, 'id', $id, $data);
         if ($result) {
            $this->mlog->action_log($action, $name, $id, $login_id, $intro);
             $data = ['code' => 200, 'msg' => '删除成功', 'data' => $result];
         } else {
             $data = ['code' => 400, 'msg' => '删除失败', 'data' => ''];
         }
 
         $this->output->set_status_header(200)
             ->set_content_type('application/json')
             ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
             ->_display();
         exit;
     }
 

// 2.预约计时
    // 预约计时
    public function timing() {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolidFromLoginauth($this->session->loginauth);
        $data = ['school_id' => $school_id];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/order/timing', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    // 预约计时
    public function timingorderajax() {
        $this->mbase->loginauth();
        if (!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $role_id = (int)$this->mbase->getRoleIdFromLoginauth($this->session->loginauth);
        $school_id = (int)$this->mbase->getSchoolidFromLoginauth($this->session->loginauth);
        $order_status = $this->input->post('os', true) ? trim($this->input->post('os', true)) : 'all';
        switch ($order_status) {
            case 'all': $order = 200; break;
            case 'paid': $order = 1; break;
            case 'completed': $order = 2; break;
            case 'cancel': $order = 3; break;
            case 'unpaid': $order = 1003; break;
            case 'refunding': $order = 1006; break;
            case 'refunded': $order = 1007; break;
            case 'deleted': $order = 101; break;
        }
        $param['order_status'] = $order;
        $param['deal_type'] = $this->input->post('pt', true) ? intval($this->input->post('pt', true)) : '';
        $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';
        $page = $this->input->post('p', true) ? intval($this->input->post('p', true)) : self::$page;
        $limit = $this->input->post('s', true) ? intval($this->input->post('s', true)) : self::$limit;
        $pageinfo = $this->morder->getTimingOrderPageNum($school_id, $param, $limit, ! in_array($role_id, [1, 8, 9]));
       
        $page = $page < $pageinfo['pagenum'] || $pageinfo['pagenum'] == 0 ? $page : $pageinfo['pagenum'];
        $start = ($page - 1) * $limit;
        $orderinfo = $this->morder->getTimingOrderList($school_id, $param, $start, $limit, ! in_array($role_id, [1, 8, 9]));

        $orderlist['p'] = $page;
        $orderlist['list'] = $orderinfo['list'];
        $orderlist['count'] = $pageinfo['count'];
        $orderlist['pagenum'] = $pageinfo['pagenum'];
        $orderlist['total_service_time'] = $orderinfo['total_service_time'];
        $data = ['code'=>200, 'msg'=>'获取成功', 'data' => $orderlist];

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    /**
     * 预约订单预览[页面展示]
     * @param   
     * @return  void
     **/
    public function timingorderpreview()
    {
        $this->mbase->loginauth();
        $id = $this->input->get('id', true) ? intval($this->input->get('id', true)) : 0;
        $school_id = $this->mbase->getSchoolidFromLoginauth($this->session->loginauth);
        $data = ['id' => $id, 'school_id' => $school_id];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/order/timingorderpreview', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 预约订单预览[获取数据]
     * @param   int     $id     订单ID
     * @return  void
     **/
    public function previewAjax()
    {
        $this->mbase->loginauth();
        if (!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        $id = $this->input->post('id', true) ? intval($this->input->post('id', true)) : 0;
        $orderinfo = $this->morder->getStudyOrderInfo($id);
        $data = ['code' => 200, 'msg' => '获取成功', 'data' => $orderinfo];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    /**
     * 设置订单的状态
     * @param   int     $id     订单ID
     * @param   int     $status 订单状态
     * @return  void
     **/
    public function setOrderStatusAjax()
    {
        $this->mbase->loginauth();
        if (!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        $id = $this->input->post('id', true) ? intval($this->input->post('id', true)) : 0;
        $status = $this->input->post('status', true) ? intval($this->input->post('status', true)) : 0;
        $old_status = $this->morder->getOrderStatus($id);
        $data = ['l_study_order_id' => $id, 'i_status' => $status];
        $action = 'set_studyorder_status';
        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $tblname = $this->morder->timingorder_tablename;
        $name = substr($tblname, 3, strlen($tblname));
        $order_status_arr = [
            '1'     => '已付款',
            '2'     => '已完成',
            '3'     => '已取消',
            '1003'  => '未付款',
            '1006'  => '退款中',
            '1007'  => '已退款',
            '101'   => '已删除'
        ];
        $intro = "将订单的状态由".$order_status_arr[$old_status]."设置成".$order_status_arr[$status];

        $result = $this->mbase->updateData($tblname, 'l_study_order_id', $id, $data);
        if ($result) {
            $this->mlog->action_log($action, $name, $id, $login_id, $intro);
            $data = ['code' => 200, 'msg' => '设置成功', 'data' => $result];
        } else {
            $data = ['code' => 400, 'msg' => '设置失败', 'data' => ''];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    /**
     * 删除预约计时订单
     * @param   int     $id     订单ID
     * @return  void
     **/
    public function deltimingajax()
    {
        $this->mbase->loginauth();
        if (!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        $id = $this->input->post('id', true) ? intval($this->input->post('id', true)) : 0;
        $status = $this->input->post('status', true) ? intval($this->input->post('status', true)) : 0;
        $data = ['l_study_order_id' => $id, 'i_status' => 101];
        $tblname = $this->morder->timingorder_tablename;
        $action = 'del_appoint_orders';
        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $name = substr($tblname, 3, strlen($tblname));
        $intro = "删除预约计时订单( ID：".$id." )";
        $result = $this->mbase->updateData($tblname, 'l_study_order_id', $id, $data);

        if ($result) {
            $this->mlog->action_log($action, $name, $id, $login_id, $intro);
            $data = ['code' => 200, 'msg' => '删除成功', 'data' => $result];
        } else {
            $data = ['code' => 400, 'msg' => '删除失败', 'data' => ''];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }


// 3.提现申请
    // 提现申请
    public function withdrawajax() {
        $this->mbase->loginauth();
        $loginauth = $this->session->loginauth;
        $loginauth_arr = explode('|', $loginauth);
        $school_id = $loginauth_arr[5];

        if ( ! $this->input->is_ajax_request() OR $this->input->method(true) !== 'POST') {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data))
                ->_display();
            exit;
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('money', '', 'required', ['required' => '提现金额为未填写']);
        $this->form_validation->set_rules('password', '', 'required', ['required' => '提现密码未填写']);
        $this->form_validation->set_rules('account_no', '', 'required', ['required' => '银行账户未选择']);
        $this->form_validation->set_rules('bank_name', '', 'required', ['required' => '银行名称未填写']);
        $this->form_validation->set_rules('account_user_name', '', 'required', ['required' => '提款人未填写']);
        $this->form_validation->set_rules('account_phone', '', 'required', ['required' => '预留手机号未填写']);
        $this->form_validation->set_rules('account_identifyid', '', 'required', ['required' => '身份证号未填写']);
        $this->form_validation->set_rules('account_address', '', 'required', ['required' => '银行开户地址']);

        if ($this->form_validation->run() !== false) {
            // 通过表单验证的后续处理
            $school_info = $this->mschool->getSchoolInfo($school_id);
            $isPasswordCorrect = $school_info['cash_pass'] === md5($this->input->post('password', true));
            if ($isPasswordCorrect) {
                $money = $this->input->post('money', true);
                if (is_numeric($money)) {
                    // 写入提现申请表当中
                    $insert_data = [
                        'utype' 			=> 'school',
                        'withdraw_type' 	=> 'bank',
                        'created'           => 1,
                        'created_at'        => time(),
                        'uid' 				=> intval($school_id),
                        'money' 			=> (float)number_format($money, 2, '.', ''),
                        'withdraw_account' 	=> $this->input->post('account_no'),
                        'bank_name' 		=> $this->input->post('bank_name'),
                        'beizhu' 			=> $this->input->post('beizhu'),
                        'identifyid' 		=> $this->input->post('account_identifyid'),
                        'name' 				=> $this->input->post('account_user_name'),
                        'phone' 			=> $this->input->post('account_phone'),
                    ];
                    $where_condition = $insert_data;
                    $res = $this->mbase->_insert($this->morder->withdraw_tablename, $insert_data, $where_condition);
                    if ($res) {
                        $data = ['code' => 200, 'msg' => '提交成功', 'data' => new \stdClass];
                    } else {
                        $data = ['code' => 400, 'msg' => '提交出错', 'data' => new \stdClass];
                    }
                    $data['debug'] = $insert_data;
                } else {
                    $data = ['code' => 400, 'msg' => '提现金额格式不正确', 'data' => new \stdClass()];
                }
            } else {
                // 支付密码不正确
                $data = ['code' => 400, 'msg' => '支付密码不正确', 'data' => new \stdClass()];
            }
        } else {
            $errors = $this->form_validation->error_array();
            if (isset($errors[array_keys($errors)[0]])) {
                $data = ['code' => 400, 'msg' => $errors[array_keys($errors)[0]], 'data' => new \stdClass()];
            } else {
                $data = ['code' => 400, 'msg' => '提交的信息不完善', 'data' => new \stdClass()];
            }
        }

        // 返回结果
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data))
            ->_display();
        exit;
    }


}
?>
