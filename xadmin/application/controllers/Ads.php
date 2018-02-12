<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 广告管理[1.广告位管理, 2.广告等级, 3.广告招租, 4.广告订单]
 *
 * @category ads
 * @package ads
 * @author wl
 * @return void
 **/

class Ads extends CI_Controller {

    static $limit = 10;
    static $page = 1;
    public function __construct() 
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('mbase');
        $this->load->model('mads');
        $this->load->helper(['form', 'url', 'common']);
        $this->load->database();
    }

    /**
     * 广告招租[页面展示]
     * @param 
     * @return void
     **/
    public function index()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE . '/header');
        $this->load->view(TEMPLATE . '/ads/index');
        $this->load->view(TEMPLATE . '/footer');
    }

    /**
     * 广告位管理[页面展示]
     * @param 
     * @return void
     **/
    public function adsposition()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE . '/header');
        $this->load->view(TEMPLATE . '/ads/adsposition');
        $this->load->view(TEMPLATE . '/footer');
    }

    /**
     * 广告等级管理[页面展示]
     * @param 
     * @return void
     **/
    public function adslevel()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE . '/header');
        $this->load->view(TEMPLATE . '/ads/adslevel');
        $this->load->view(TEMPLATE . '/footer');
    }

    /**
     * 广告订单[页面展示]
     * @param 
     * @return void
     **/
    public function adsorder()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE . '/header');
        $this->load->view(TEMPLATE . '/ads/adsorder');
        $this->load->view(TEMPLATE . '/footer');
    }

    /**
     * 获取广告管理信息[获取数据]
     * @param param
     * @param type
     * @return void
     **/
    public function listAjax()
    {
        $type_arr = ['ads', 'position', 'level', 'order', 'info']; 
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
        if ( $type == 'position' ) {

            $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';

        } else if ( $type == 'level' ) {

            $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';

        } else if ( $type == 'ads' ) {

            $param['ads_status'] = $this->input->post('as', true) ? intval($this->input->post('as', true)) : '';
            $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';

        } else if ( $type == 'order' ) {
            $order = 1002;
            $order_status = $this->input->post('os', true) ? $this->input->post('os', true) : '';
            switch ($order_status) {
                case 'paid': $order = 1002; break;
                case 'unpaid': $order = 1003; break;
                case 'cancel': $order = 1005; break;
                case 'refund': $order = 1007; break;
                case 'deleted': $order = 1010; break;
                case 'refunding': $order = 1006; break;
            }
            $param['order_status'] = $order;
            $param['pay_type'] = $this->input->post('pt', true) ? intval($this->input->post('pt', true)) : '';
            $param['device'] = $this->input->post('device', true) ? intval($this->input->post('device', true)) : '';
            $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';

        }
        $page = $this->input->post('p', true) ? intval($this->input->post('p', true)) : self::$page;
        $limit = $this->input->post('s', true) ? intval($this->input->post('s', true)) : self::$limit;

        $pageinfo = $this->mads->getAdsPageNum($param, $type, $limit);
        $pagenum = $pageinfo['pagenum'];
        $count = $pageinfo['count'];
        $page = $page < $pagenum || $pagenum == 0 ? $page : $pagenum;
        $start = ($page - 1) * $limit;
        $list = $this->mads->getAdsList($param, $type, $start, $limit);
        $ads_list['p'] = $page;
        $ads_list['pagenum'] = $pagenum;
        $ads_list['count'] = $count;
        $ads_list['list'] = $list['list'];
        
        $data = [
            'code' => 200,
            'msg' => '获取成功',
            'data' => $ads_list
        ];

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;

    }

    /**
     * 添加广告[页面展示]
     * @param  
     * @return void
     **/
    public function addads()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/ads/addads');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 添加广告位[页面展示]
     * @param  
     * @return void
     **/
    public function addadspos()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/ads/addadspos');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 添加广告位[页面展示]
     * @param  
     * @return void
     **/
    public function addadslevel()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/ads/addadslevel');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 添加广告订单[页面展示]
     * @param  
     * @return void
     **/
    public function addadsorder()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/ads/addadsorder');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 新增广告管理中的新数据
     * @param $post
     *
     * @return void
     **/
    public function addAjax()
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

        $type_arr = ['ads', 'position', 'level', 'order', 'info']; 
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
        if ( $type == 'position' ) {

            $param['title'] = $this->input->post('title', true) ? trim($this->input->post('title', true)) : '';
            $param['description'] = $this->input->post('description', true) ? trim($this->input->post('description', true)) : '';
            $param['scene'] = $this->input->post('scene', true) ? trim($this->input->post('scene', true)) : '';
            $param['addtime'] = time();
            $tblname = $this->mads->ads_position_tbl;
            $action = 'add_ads_position';
            $intro = "添加新的广告寻访位置";

            $check_pos = $this->mads->checkPosition($param['scene']);
            if ( ! empty($check_pos)) {
                $data = [
                    'code' => 102,
                    'msg' => $check_pos['scene'].'场景已添加过',
                    'data' => $check_pos['scene']
                ];

                $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT))
                    ->_display();
                exit;
            }

        } elseif ( $type == 'level' ) {

            $param['level_id'] = $this->input->post('level_id', true) ? intval($this->input->post('level_id', true)) : '';
            $param['level_title'] = $this->input->post('level_title', true) ? trim($this->input->post('level_title', true)) : '';
            $param['loop_time'] = $this->input->post('loop_time', true) ? intval($this->input->post('loop_time', true)) : '';
            $param['level_intro'] = $this->input->post('level_intro', true) ? trim($this->input->post('level_intro', true)) : '';
            $param['level_money'] = $this->input->post('level_money', true) ? $this->input->post('level_money', true) : '';
            $param['addtime'] = time();
            $tblname = $this->mads->ads_level_tbl;
            $action = 'add_ads_level';
            $intro = "添加新的广告等级";

        } elseif ( $type == 'ads' ) {

            $publish_id = $this->mbase->getSchoolidFromLoginauth($this->session->loginauth);
            if ($publish_id == NULL OR $publish_id == '') {
                $publish_id = 0;
            }
            $param['publisher_id'] = $publish_id;
            $param['publisher_type'] = 1;
            $param['level_id'] = $this->input->post('level_id', true) ? intval($this->input->post('level_id', true)) : '';
            $param['scene_id'] = $this->input->post('scene_id', true) ? intval($this->input->post('scene_id', true)) : '';
            $param['limit_time'] = $this->input->post('limit_time', true) ? intval($this->input->post('limit_time', true)) : '';
            $param['ads_status'] = $this->input->post('ads_status', true) ? intval($this->input->post('ads_status', true)) : '';
            $param['title'] = $this->input->post('title', true) ? trim($this->input->post('title', true)) : '';
            $param['intro'] = $this->input->post('intro', true) ? trim($this->input->post('intro', true)) : '';
            $param['limit_num'] = $this->input->post('limit_num', true) ? intval($this->input->post('limit_num', true)) : '';
            $param['sort_order'] = $this->input->post('sort_order', true) ? intval($this->input->post('sort_order', true)) : '';
            $param['province_id'] = $this->input->post('province_id', true) ? intval($this->input->post('province_id', true)) : '';
            $param['city_id'] = $this->input->post('city_id', true) ? intval($this->input->post('city_id', true)) : '';
            $param['area_id'] = $this->input->post('area_id', true) ? intval($this->input->post('area_id', true)) : '';
            $param['addtime'] = time();
            
            $params['device'] = $this->input->post('device', true) ? intval($this->input->post('device', true)) : '';
            if ($params['device'] == 3) {
                $params['device'] = '1,2';
            }
            $params['resource_type'] = $this->input->post('resource_type', true) ? intval($this->input->post('resource_type', true)) : '';

            $tblname = $this->mads->ads_tbl;
            $action = 'add_adsmanage';
            $intro = "添加新的广告";

        } else if ( $type == 'order' ) {

            $school_id = $this->input->post('school_id', true) ? intval($this->input->post('school_id', true)) : '';
            $param['buyer_id'] = $school_id;
            $buyer_name = $this->mads->getBuyerName($school_id);
            $param['buyer_name'] = $buyer_name;
            $param['buyer_phone'] = $this->input->post('buyer_phone', true) ? $this->input->post('buyer_phone', true) : '';
            $param['buyer_type'] = 2; // school
            $param['ads_id'] = $this->input->post('ads_id', true) ? intval($this->input->post('ads_id', true)) : '';
            $param['ads_title'] = $this->input->post('title', true) ? trim($this->input->post('title', true)) : '';
            $param['over_time'] = $this->input->post('over_time', true) ? $this->input->post('over_time', true) : '';
            $param['loop_time'] = $this->input->post('loop_time', true) ? intval($this->input->post('loop_time', true)) : '';
            $param['original_price'] = $this->input->post('original_price', true) ? round($this->input->post('original_price', true)) : '';
            $param['final_price'] = $this->input->post('final_price', true) ? round($this->input->post('final_price', true)) : '';
            $param['is_promote'] = $this->input->post('is_promote', true) ? ($this->input->post('is_promote', true) === 'false' ? 0 : 1) : 0;
            $param['ads_url'] = $this->input->post('ads_url', true) ? trim($this->input->post('ads_url', true)) : '';
            $param['order_status'] = $this->input->post('order_status', true) ? intval($this->input->post('order_status', true)) : '';
            $param['pay_type'] = $this->input->post('pay_type', true) ? intval($this->input->post('pay_type', true)) : '';
            $param['device'] = $this->input->post('device', true) ? intval($this->input->post('device', true)) : '';
            $param['order_no'] = $this->input->post('order_no', true) ?$this->input->post('order_no', true) : '';
            $param['resource_type'] = $this->input->post('resource_type', true) ? (int)$this->input->post('resource_type', true) : '';
            $param['resource_url'] = $this->input->post('resource_url', true) ? $this->input->post('resource_url', true) : '';
            $param['unique_trade_no'] = guid(false);
            $param['addtime'] = time();
            $tblname = $this->mads->ads_order_tbl;
            $action = 'add_ads_order';
            $intro = "添加新的广告订单";
        }

        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $name = substr($tblname, 3, strlen($tblname));        
        $result = $this->mads->add($param, $tblname);

        if ($result) {
            $this->mlog->action_log($action, $name, $result, $login_id, $intro);
            if ($type == 'ads') {
                $params['ads_id'] = $result;
                $params['addtime'] = time();
                $tblname = $this->mads->ads_info_tbl;
                $res = $this->mads->add($params, $tblname);
            }
            $data = [
                'code' => 200,
                'msg' => '添加成功',
                'data' => $result
            ];

        } else {

            $data = [
                'code' => 400,
                'msg' => '添加失败',
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
     * 修改广告位[页面展示]
     * @param  
     * @return void
     **/
    public function editads()
    {
        $this->mbase->loginauth();
        $id = $this->input->get('id') ? intval($this->input->get('id')) : '';
        $type_arr = ['ads', 'position', 'level', 'order', 'info']; 
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

        $this->load->view(TEMPLATE.'/header');
        if ( $type == 'position' ) {
            $data = $this->mads->getAdsInfoById($id, 'position');
            $this->load->view(TEMPLATE.'/ads/editadspos', $data);

        } elseif ( $type == 'level') {
            $data = $this->mads->getAdsInfoById($id, 'level');
            $this->load->view(TEMPLATE.'/ads/editadslevel', $data);

        } elseif ( $type == 'ads' ) {
            $data = $this->mads->getAdsInfoById($id, 'ads');
            $this->load->view(TEMPLATE.'/ads/editads', $data);

        } elseif ( $type == 'order' ) {
            $data = $this->mads->getAdsInfoById($id, 'order');
            $this->load->view(TEMPLATE.'/ads/editadsorder', $data);
        }

        $this->load->view(TEMPLATE.'/footer');

    }

    /**
     * 修改广告管理[页面展示]
     * @param  
     * @return void
     **/
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

        $type_arr = ['ads', 'position', 'level', 'order', 'info']; 
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
        if ( $type == 'position' ) {
            $param['id'] = $this->input->post('id', true) ? intval($this->input->post('id', true)) : '';
            $positionlist = $this->mads->getAdsInfoById($param['id'], 'position');
            $param['title'] = $this->input->post('title', true) ? trim($this->input->post('title', true)) : $positionlist['title'];
            $param['description'] = $this->input->post('description', true) ? trim($this->input->post('description', true)) : $positionlist['description'];
            $param['scene'] = $this->input->post('scene', true) ? intval($this->input->post('scene', true)) : $positionlist['description'];
            $param['addtime'] = time();
            $field = 'id';
            $tblname = $this->mads->ads_position_tbl;
            $action = 'edit_ads_position';
            $intro = "修改广告寻访位置";
            
        } elseif ( $type == 'level' ) {

            $param['id'] = $this->input->post('id', true) ? intval($this->input->post('id', true)) : '';
            $adslevellist = $this->mads->getAdsInfoById($param['id'], 'level');
            $param['level_id'] = $this->input->post('level_id', true) ? intval($this->input->post('level_id', true)) : $adslevellist['level_id'];
            $param['level_title'] = $this->input->post('level_title', true) ? trim($this->input->post('level_title', true)) : $adslevellist['level_title'];
            $param['loop_time'] = $this->input->post('loop_time', true) ? intval($this->input->post('loop_time', true)) : $adslevellist['loop_time'];
            $param['level_intro'] = $this->input->post('level_intro', true) ? trim($this->input->post('level_intro', true)) : $adslevellist['level_intro'];
            $param['level_money'] = $this->input->post('level_money', true) ? $this->input->post('level_money', true) : $adslevellist['level_money'];
            $param['addtime'] = time();
            $tblname = $this->mads->ads_level_tbl;
            $action = 'update_ads_level';
            $intro = "修改广告等级";
            $field = "id";

        } else if ( $type == 'ads' ) {

            $param['id'] = $this->input->post('id', true) ? intval($this->input->post('id', true)) : '';
            $adslist = $this->mads->getAdsInfoById($param['id'], 'ads');
            $publish_id = $this->mbase->getSchoolidFromLoginauth($this->session->loginauth);
            if ($publish_id == NULL OR $publish_id == '') {
                $publish_id = 0;
            }
            $param['publisher_id'] = $publish_id;
            $param['publisher_type'] = 1;
            $param['level_id'] = $this->input->post('level_id', true) ? intval($this->input->post('level_id', true)) : $adslist['level_id'];
            $param['scene_id'] = $this->input->post('scene_id', true) ? intval($this->input->post('scene_id', true)) : $adslist['scene_id'];
            $param['ads_status'] = $this->input->post('ads_status', true) ? intval($this->input->post('ads_status', true)) : $adslist['ads_status'];
            $param['title'] = $this->input->post('title', true) ? trim($this->input->post('title', true)) : $adslist['title'];
            $param['intro'] = $this->input->post('intro', true) ? trim($this->input->post('intro', true)) : $adslist['intro'];
            $param['limit_num'] = $this->input->post('limit_num', true) ? intval($this->input->post('limit_num', true)) : $adslist['limit_num'];
            $param['sort_order'] = $this->input->post('sort_order', true) ? intval($this->input->post('sort_order', true)) : 1;

            $limit_time = $this->input->post('limit_time', true);
            $limit_time_length = strlen($limit_time);
            if ($limit_time_length > 10) {
                $param['limit_time'] = strtotime($limit_time);
            } else {
                $param['limit_time'] = intval($this->input->post('limit_time', true));
            }
            $param['limit_time'] = $this->input->post('limit_time', true) ? intval($this->input->post('limit_time', true)) : $adslist['limit_time'];
            
            if ( (int)$this->input->post('province', true) === 0 ) {
                $param['province_id'] = (int)$this->input->post('province_id', true);
            } else {
                $param['province_id'] = (int)$this->input->post('province', true);
            }

            if ( $this->input->post('city_id', true) === ''  ) {
                $param['city_id'] = (int)$this->input->post('city', true);
            } else {
                $param['city_id'] = (int)$this->input->post('city_id', true);
            }

            if ( $this->input->post('area_id', true) === ''  ) {
                $param['area_id'] = (int)$this->input->post('area', true);
            } else {
                $param['area_id'] = (int)$this->input->post('area_id', true);
            }

            $params['device'] = $this->input->post('device', true) ? intval($this->input->post('device', true)) : '';
            $info_id = $this->input->post('info_id', true) ? intval($this->input->post('info_id', true)) : '';
            if ($params['device'] == 3) {
                $params['device'] = '1,2';
            }
            $params['resource_type'] = $this->input->post('resource_type', true) ? intval($this->input->post('resource_type', true)) : '';
            

            $field = 'id';
            $tblname = $this->mads->ads_tbl;
            $action = 'edit_adsmanage';
            $intro = "修改广告";

        } else if ( $type == 'order' ) {

            $param['id'] = $this->input->post('id', true) ? intval($this->input->post('id', true)) : '';
            $adsorderlist = $this->mads->getAdsInfoById($param['id'], 'order');
            $param['buyer_id'] = $this->input->post('buyer_id', true) ? (int)$this->input->post('buyer_id', true) : $adsorderlist['buyer_id'];
            $param['buyer_name'] = $this->input->post('buyer_name', true) ? trim($this->input->post('buyer_name', true)) : $adsorderlist['buyer_name'];
            $param['buyer_phone'] = $this->input->post('buyer_phone', true) ? $this->input->post('buyer_phone', true) : $adsorderlist['buyer_phone'];
            $param['buyer_type'] = 2; // school
            $param['ads_id'] = $this->input->post('ads_id', true) ? intval($this->input->post('ads_id', true)) : $adsorderlist['ads_id'];
            $param['ads_title'] = $this->input->post('title', true) ? trim($this->input->post('title', true)) : $adsorderlist['ads_title'];
            // 过期时间的判断
            $over_time = $this->input->post('over_time', true);
            $over_time_arr = explode('-', $over_time);
            $time_count = count($over_time_arr);
            if ( $time_count > 1) {
                $param['over_time'] = strtotime($over_time);
            } else {
                $param['over_time'] = $over_time;
            }

            $param['loop_time'] = $this->input->post('loop_time', true) ? intval($this->input->post('loop_time', true)) : '';
            $param['original_price'] = $this->input->post('original_price', true) ? round($this->input->post('original_price', true)) : '';
            $param['final_price'] = $this->input->post('final_price', true) ? round($this->input->post('final_price', true)) : '';
            $param['is_promote'] = $this->input->post('is_promote', true) ? ($this->input->post('is_promote', true) === 'false' ? 0 : 1) : 0;
            $param['ads_url'] = $this->input->post('ads_url', true) ? trim($this->input->post('ads_url', true)) : '';
            $param['order_status'] = $this->input->post('order_status', true) ? intval($this->input->post('order_status', true)) : '';
            $param['pay_type'] = $this->input->post('pay_type', true) ? intval($this->input->post('pay_type', true)) : '';
            $param['device'] = $this->input->post('device', true) ? intval($this->input->post('device', true)) : '';
            $param['order_no'] = $this->input->post('order_no', true) ? $this->input->post('order_no', true) : '';
            $param['resource_type'] = $this->input->post('resource_type', true) ? (int)$this->input->post('resource_type', true) : '';
            $param['resource_url'] = $this->input->post('resource_url', true) ? $this->input->post('resource_url', true) : $adsorderlist['resource_url'];
            $param['unique_trade_no'] = guid(false);
            $tblname = $this->mads->ads_order_tbl;
            $action = 'edit_ads_order';
            $intro = "修改广告订单";
            $field = 'id';
            
        }

        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $name = substr($tblname, 3, strlen($tblname));        
        $result = $this->mads->edit($field, $param, $tblname);
        
        if ($result) {
            $this->mlog->action_log($action, $name, $param['id'], $login_id, $intro);
            if ($type == 'ads') {
                $field = 'id';
                $params['id'] = $info_id;
                $params['ads_id'] = $param['id'];
                $params['addtime'] = time();
                $tblname = $this->mads->ads_info_tbl;
                $res = $this->mads->edit($field, $params, $tblname);
            }
            $data = [
                'code' => 200,
                'msg' => '修改成功',
                'data' => $result
            ];

        } else {

            $data = [
                'code' => 400,
                'msg' => '修改失败',
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
     * 上传文件
     * @param   id  对应的ID
     * 
     * @return void
     **/
    public function adsUpload() 
    {
        $params = $this->input->get('unique') ? intval($this->input->get('unique')) : 0;
        $type = $this->input->get('tp') ? trim($this->input->get('tp')) : '';
        $tblname = $this->mads->ads_order_tbl;
        $condition = ['id' => $params];
        $handle = $this->mbase->handleAdminUpload($type, $params, $condition, 'resource_url', $tblname);
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($handle, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
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

        $type_arr = ['ads', 'position', 'level', 'order', 'info']; 
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
        
        $id = $this->input->post('id', true) ? intval($this->input->post('id', true)) : '';
        $id_arr = explode(',', $id);

        if ( $type == 'position' ) { // 删除广告位

            $tblname = $this->mads->ads_position_tbl;
            $field = 'id';
            $action = 'del_ads_position';
            $intro = "删除广告位[ID: ".$id."]";

        } else if ( $type == 'level' ) { // 删除广告等级

            $tblname = $this->mads->ads_level_tbl;
            $field = 'id';
            $action = 'del_ads_level';
            $intro = "删除广告等级[ID: ".$id."]";

        } else if ( $type == 'ads' ) {

            $tblname = $this->mads->ads_tbl;
            $field = 'id';
            $action = 'del_adsmanage';
            $intro = "删除广告[ID: ".$id."]";

        } else if ( $type == 'order' ) {

            $ads_order_list = $this->mads->getAdsInfoById($id, 'order');
            $order_status = $ads_order_list['order_status'];

            if ( $order_status == '1010' ) {
                    $data = [
                    'code' => 400,
                    'msg' => '该订单已删除',
                    'data' => $order_status
                ];

                $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT))
                    ->_display();
                exit;

            }

            $tblname = $this->mads->ads_order_tbl;
            $field = 'id';
            $data = [
                'id' => $id,
                'order_status' => 1010,
            ];
            $action = 'del_ads_order';
            $intro = "删除订单[ID: ".$id."]";

        }

        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $name = substr($tblname, 3, strlen($tblname)); 
            
        if ( $type == 'order' ) {
            $result = $this->mads->edit($field, $data, $tblname);

        } else {
            $result = $this->mads->delAjax($id_arr, $field, $tblname);

        }

        if ($result) {

            $this->mlog->action_log($action, $name, $id, $login_id, $intro);
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
     * 设置广告订单
     * @param $id
     * @return void
     **/
    public function handlePromote()
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

        $id = $this->input->post('id', true) ? intval($this->input->post('id', true)) : '';
        $status = $this->input->post('status', true) ? intval($this->input->post('status', true)) : '';
        $update_data = [
            'id' => $id,
            'is_promote' => $status
        ];

        if ( $status == 0) {
            $intro = "设置广告订单折扣状态为不打折";
        } else {
            $intro = "设置广告订单折扣状态为打折";
        }
        $field = "id";
        $tblname = $this->mads->ads_order_tbl;
        $action = "set_adsorder_promote";
        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $name = substr($tblname, 3, strlen($tblname)); 
        $result = $this->mads->edit($field, $update_data, $tblname);
        if ($result) {

            $this->mlog->action_log($action, $name, $id, $login_id, $intro);
            $data = [
                'code' => 200,
                'msg' => '设置成功',
                'data' => $result
            ];

        } else {

            $data = [
                'code' => 400,
                'msg' => '设置失败',
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
     * 获取广告位置信息
     * @param 
     * @return void
     **/
    public function sceneListAjax()
    {
        $this->mbase->loginauth();
        $position_list = $this->mads->getSceneList();
        if ($position_list) {
            $data = [
                'code' => 200,
                'msg' => '获取成功',
                'data' => $position_list
            ];

        } else {

            $data = [
                'code' => 400,
                'msg' => '获取失败',
                'data' => $position_list
            ];

        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;

    }

    /**
     * 获取广告等级信息
     * @param 
     * @return void
     **/
    public function levelListAjax()
    {
        $this->mbase->loginauth();
        $position_list = $this->mads->getAdsLevelList();
        if ($position_list) {
            $data = [
                'code' => 200,
                'msg' => '获取成功',
                'data' => $position_list
            ];

        } else {

            $data = [
                'code' => 400,
                'msg' => '获取失败',
                'data' => $position_list
            ];

        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;

    }

    /**
     * 自动生成订单号
     * @param 
     * @return void
     **/
    public function orderCreateAjax() 
    {
        $order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $data = [
            'code' => 200,
            'msg' => '获取成功',
            'data' => $order_no
        ];

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;

    }

    /**
     * 获取驾校的号码
     * @param $school_id
     * @return void
     **/
    public function getPhoneAjax()
    {
        $school_id = $this->input->post('school_id', true) ? intval($this->input->post('school_id', true)) : '';
        $school_phone = $this->mads->getBuyerPhone($school_id);
        $data = [
            'code' => 200,
            'msg' => '获取成功',
            'data' => $school_phone
        ];

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;

    }

    /**
     * 获取广告订单信息
     * @param 
     * @return void
     **/
    public function adsListAjax()
    {
        $ads_list = $this->mads->getAdsInfo();
        if ( ! empty($ads_list)) {
            $data = [
                'code'  => 200,
                'msg'   => '获取成功',
                'data'  => $ads_list
            ];

        } else {
            $data = [
                'code'  => 400,
                'msg'   => '获取失败',
                'data'  => new \stdClass
            ];
        }

         $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;

    }



}