<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 驾培机构
class School extends CI_Controller {

    static $limit = 10;
    static $page = 1;
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('mbase');
        $this->load->model('mschool');
        $this->load->model('mcity');
        $this->load->model('mads');
        $this->load->model('mtags');
        $this->load->model('mcoupon');
        $this->load->library('sms');
    }

// 1.驾校列表
    // 首页列表
    public function index()
    {   
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/school/index');
        $this->load->view(TEMPLATE.'/footer');
    }

    // 添加
    public function add() {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/school/add');
        $this->load->view(TEMPLATE.'/footer');
    }
    
    // 编辑
    public function edit() {
        $this->mbase->loginauth();
        $id = $this->input->get('id') ? $this->input->get('id') : 0;
        $data = $this->mschool->getSchoolInfo($id);
        if(!$data) show_404();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/school/edit', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    // 列表分页ajax
    public function listajax()
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
        $page = $this->input->post('p') ? intval($this->input->post('p')) : self::$page;
        $limit = $this->input->post('s') ? intval($this->input->post('s')) : self::$limit;

        $param = [];
        $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';
        $param['nature'] = $this->input->post('nature', true) ? intval($this->input->post('nature', true)) : '';
        $param['hot'] = $this->input->post('hot', true) ? intval($this->input->post('hot', true)) : '';
        $param['brand'] = $this->input->post('brand', true) ? intval($this->input->post('brand', true)) : '';
        $param['show'] = $this->input->post('show', true) ? intval($this->input->post('show', true)) : '';
        $pageinfo = $this->mschool->getSchoolPageNum($param, $limit);

        $page = $page < $pageinfo['pagenum'] || $pageinfo['pagenum'] == 0 ? $page : $pageinfo['pagenum']; 
        $start = ($page - 1) * $limit;
        $schoolinfo = $this->mschool->getSchoolListByCondition($param, $start, $limit);
        $list['pagenum'] = $pageinfo['pagenum'];
        $list['count'] = $pageinfo['count'];
        $list['p'] = $page;
        $list['list'] = $schoolinfo;
        $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    // 添加驾校ajax
    public function addajax()
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
        $params = $this->input->post();
        $_data = [
            's_school_name'=> isset($params['s_school_name']) ? $params['s_school_name'] : '',
            's_frdb'=> isset($params['s_frdb']) ? $params['s_frdb'] : '',
            's_thumb'=> isset($params['s_thumb']) ? $params['s_thumb'] : '',
            's_frdb_mobile'=> isset($params['s_frdb_mobile']) ? $params['s_frdb_mobile'] : '',
            's_frdb_tel'=> isset($params['s_frdb_tel']) ? $params['s_frdb_tel'] : '',
            's_zzjgdm'=> isset($params['s_zzjgdm']) ? $params['s_zzjgdm'] : '',
            'dc_base_je'=> isset($params['dc_base_je']) ? $params['dc_base_je'] : '',
            'dc_bili'=> isset($params['dc_bili']) ? $params['dc_bili'] : '',
            's_yh_name'=> isset($params['s_yh_name']) ? $params['s_yh_name'] : '',
            's_yh_zhanghao'=> isset($params['s_yh_zhanghao']) ? $params['s_yh_zhanghao'] : '',
            's_yh_huming'=> isset($params['s_yh_huming']) ? $params['s_yh_huming'] : '',
            'i_dwxz'=> isset($params['i_dwxz']) ? $params['i_dwxz'] : '',
            'brand'=> isset($params['brand']) ? $params['brand'] : '',
            's_location_x'=> isset($params['s_location_x']) ? $params['s_location_x'] : '',
            's_location_y'=> isset($params['s_location_y']) ? $params['s_location_y'] : '',
            'province_id'=> isset($params['province_id']) ? $params['province_id'] : '',
            'city_id'=> isset($params['city_id']) ? $params['city_id'] : '',
            'area_id'=> isset($params['area_id']) ? $params['area_id'] : '',
            's_address'=> isset($params['s_address']) ? $params['s_address'] : '',
            's_yyzz'=> isset($params['s_yyzz']) ? $params['s_yyzz'] : '',
            'is_show'=> isset($params['is_show']) ? ($params['is_show'] ? 2 : 1) : 2,
            's_imgurl'=> '',
            'addtime'=> time(),
            'shifts_intro'=> '',
            'i_wdid'=> '',
            's_shuoming'=> '',
            'dc_time_je'=> time(),
            'cash_pass'=> isset($params['cash_pass']) ? md5($params['cash_pass']) : '',
        ];
        if(strlen($params['cash_pass']) != 6) {
            $data = ['code'=>100, 'msg'=>'提现密码为6位', 'data'=>['result'=>[]]];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $result = $this->mschool->addschoolInfo($_data);
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
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $params = $this->input->post();
        $_data = [
            'l_school_id'=> isset($params['l_school_id']) ? $params['l_school_id'] : 0,
            's_school_name'=> isset($params['s_school_name']) ? $params['s_school_name'] : '',
            's_frdb'=> isset($params['s_frdb']) ? $params['s_frdb'] : '',
            's_thumb'=> isset($params['s_thumb']) ? $params['s_thumb'] : '',
            's_frdb_mobile'=> isset($params['s_frdb_mobile']) ? $params['s_frdb_mobile'] : '',
            's_frdb_tel'=> isset($params['s_frdb_tel']) ? $params['s_frdb_tel'] : '',
            's_zzjgdm'=> isset($params['s_zzjgdm']) ? $params['s_zzjgdm'] : '',
            'dc_base_je'=> isset($params['dc_base_je']) ? $params['dc_base_je'] : '',
            'dc_bili'=> isset($params['dc_bili']) ? $params['dc_bili'] : '',
            's_yh_name'=> isset($params['s_yh_name']) ? $params['s_yh_name'] : '',
            's_yh_zhanghao'=> isset($params['s_yh_zhanghao']) ? $params['s_yh_zhanghao'] : '',
            's_yh_huming'=> isset($params['s_yh_huming']) ? $params['s_yh_huming'] : '',
            'i_dwxz'=> isset($params['i_dwxz']) ? $params['i_dwxz'] : '',
            'brand'=> isset($params['brand']) ? $params['brand'] : '',
            's_location_x'=> isset($params['s_location_x']) ? $params['s_location_x'] : '',
            's_location_y'=> isset($params['s_location_y']) ? $params['s_location_y'] : '',
            'province_id'=> isset($params['province_id']) ? $params['province_id'] : '',
            'city_id'=> isset($params['city_id']) ? $params['city_id'] : '',
            'area_id'=> isset($params['area_id']) ? $params['area_id'] : '',
            's_address'=> isset($params['s_address']) ? $params['s_address'] : '',
            's_yyzz'=> isset($params['s_yyzz']) ? $params['s_yyzz'] : '',
            'is_show'=> isset($params['is_show']) ? ($params['is_show'] === 'true' ? 1 : 2) : 2,
            's_imgurl'=> '',
            'addtime'=> time(),
            'shifts_intro'=> '',
            'i_wdid'=> '',
            's_shuoming'=> '',
            'dc_time_je'=> time(),
            'cash_pass'=> isset($params['cash_pass']) ? (strlen($params['cash_pass']) == 6 ? md5($params['cash_pass']) : $params['old_cash_pass']) : '',
        ];

        $result = $this->mschool->editschoolInfo($_data);
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
        $params = ['l_school_id'=> $id];
        $res = $this->mschool->delInfo($this->mschool->school_tablename, $params);
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

// 5.轮播图管理
    /**
     * 轮播图管理[页面展示]
     *
     * @return  void
     **/
    public function banner()
    {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $data = ['school_id' => $school_id];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/school/banner', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 轮播图管理[数据获取]
     *
     * @return  void
     **/
    public function bannerlist()
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
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $page = $this->input->post('p') ? intval($this->input->post('p')) : self::$page;
        $limit = $this->input->post('s') ? intval($this->input->post('s')) : self::$limit;
        $param = [];
        $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';
        $pageinfo = $this->mschool->getSchoolbannerPageNum($school_id, $param, $limit);
        $page = $page < $pageinfo['pagenum'] || $pageinfo['pagenum'] == 0 ? $page : $pageinfo['pagenum']; 
        $start = ($page - 1) * $limit;
        $schoolinfo = $this->mschool->getSchoolBanner($school_id, $param, $start, $limit);
        $list['pagenum'] = $pageinfo['pagenum'];
        $list['count'] = $pageinfo['count'];
        $list['p'] = $page;
        $list['list'] = $schoolinfo;
        $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    /**
     * 编辑图片[页面展示]
     * 
     * @return  void
     **/
     public function editBanner()
     {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $id = $this->input->get('id', true) ? intval($this->input->get('id', true)) : 0;
        $data = $this->mschool->getBannerInfo($id);
        $data['school_id'] = $school_id;
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/school/editBanner', $data);
        $this->load->view(TEMPLATE.'/footer');
     }

    /**
     * 编辑图片[功能实现]
     * 
     * @return  void
     **/
    public function editBannerAjax()
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
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $param['l_school_id'] = $this->input->post('id', true) ? intval($this->input->post('id', true)) : $school_id;
        $banner_list = $this->mschool->getBannerInfo($param['l_school_id']);
        $imgurl_one = $this->input->post('imgurl_one', true) ? trim($this->input->post('imgurl_one', true)) : '';
        $imgurl_two = $this->input->post('imgurl_two', true) ? trim($this->input->post('imgurl_two', true)) : '';
        $imgurl_three = $this->input->post('imgurl_three', true) ? trim($this->input->post('imgurl_three', true)) : '';
        $imgurl_four = $this->input->post('imgurl_four', true) ? trim($this->input->post('imgurl_four', true)) : '';
        $imgurl_five = $this->input->post('imgurl_five', true) ? trim($this->input->post('imgurl_five', true)) : '';
        $imgurl_arr = [$imgurl_one, $imgurl_two, $imgurl_three, $imgurl_four, $imgurl_five];
        $s_imgurl = [];
        foreach ($imgurl_arr as $index => $imgurl) {
            if ($imgurl != '') {
                $s_imgurl[] = $imgurl;
            }
        }
        if ( empty($s_imgurl)) {
            $s_imgurl = $banner_list['s_imgurl'];
        }
        $param['s_imgurl'] = json_encode($s_imgurl, JSON_UNESCAPED_SLASHES);
        $result = $this->mschool->editSchoolInfo($param);
        if ($result) {
            $data = ['code' => 200, 'msg' => '编辑成功', 'data' => $result];
        } else {
            $data = ['code' => 400, 'msg' => '编辑失败', 'data' => ''];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    /**
     * 删除轮播图
     *
     * @return  void
     **/
    public function delBannerAjax()
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
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $l_school_id = $this->input->post('id', true) ? intval($this->input->post('id', true)) : $school_id;
        $url = $this->input->post('url', true) ? trim($this->input->post('url', true)) : '';
        $result = $this->mschool->delBanner($l_school_id, $url);
        if ($result) {
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

// 4.报名点管理
    /**
     * 报名点管理[页面展示]
     * @param 
     * @return void
     **/
    public function signplace()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/school/signplace');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 获取报名点数据[数据实现]
     * @param
     * @return void
     **/
    public function signplaceAjax()
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
        $school_id = $this->mbase->getSchoolidFromLoginauth($this->session->loginauth);
        $page = $this->input->post('p') ? intval($this->input->post('p')) : self::$page;
        $limit = $this->input->post('s') ? intval($this->input->post('s')) : self::$limit;

        $param = [];
        $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';
        
        $pageinfo = $this->mschool->getSignplacePageNum($school_id, $param, $limit);
        $page = $page < $pageinfo['pagenum'] || $pageinfo['pagenum'] == 0 ? $page : $pageinfo['pagenum']; 
        $start = ($page - 1) * $limit;
        
        $signplacelist = $this->mschool->getSignplaceList($school_id, $param, $start, $limit);

        $list['pagenum'] = $pageinfo['pagenum'];
        $list['count'] = $pageinfo['count'];
        $list['p'] = $page;
        $list['list'] = $signplacelist;
        $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    /**
     * 报名点管理[添加页面]
     * @param 
     * @return void
     **/    
    public function addsignplace() {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolidFromLoginauth($this->session->loginauth);
        $data = ['school_id' => $school_id];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/school/addsignplace', $data);
        $this->load->view(TEMPLATE.'/footer');
    }
    
    /**
     * 报名点管理[编辑页面]
     * @param 
     * @return void
     **/    
    public function editsignplace() {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolidFromLoginauth($this->session->loginauth);
        $id = $this->input->get('id') ? $this->input->get('id') : 0;
        $data = $this->mschool->getSignPlaceInfo($id, $school_id);
        if(!$data) show_404();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/school/editsignplace', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 新增报名点
     * @param 
     * @return void
     **/
    public function addSignplaceAjax()
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
        $school_id = $this->mbase->getSchoolidFromLoginauth($this->session->loginauth);
        $param = [];
        $param['tl_school_id'] = $this->input->post('school_id', true) ? $this->input->post('school_id', true) : $school_id;
        $param['tl_train_address'] = $this->input->post('name', true) ? $this->input->post('name', true) : '';
        $param['tl_location_x'] = $this->input->post('location_x', true) ? $this->input->post('location_x', true) : '';
        $param['tl_location_y'] = $this->input->post('location_y', true) ? $this->input->post('location_y', true) : '';
        $param['order'] = $this->input->post('order', true) ? $this->input->post('order', true) : '';
        $param['tl_phone'] = $this->input->post('phone', true) ? $this->input->post('phone', true) : '';
        
        $imgurl_one = $this->input->post('imgurl_one', true) ? trim($this->input->post('imgurl_one', true)) : '';
        $imgurl_two = $this->input->post('imgurl_two', true) ? trim($this->input->post('imgurl_two', true)) : '';
        $imgurl_three = $this->input->post('imgurl_three', true) ? trim($this->input->post('imgurl_three', true)) : '';
        $imgurl_four = $this->input->post('imgurl_four', true) ? trim($this->input->post('imgurl_four', true)) : '';
        $imgurl_five = $this->input->post('imgurl_five', true) ? trim($this->input->post('imgurl_five', true)) : '';

        $imgurl = [$imgurl_one, $imgurl_two, $imgurl_three, $imgurl_four, $imgurl_five];
        $img = [];
        foreach ($imgurl as $url) {
            if ($url != '') {
                $img[] = $url;
            }
        }
        $img = json_encode($img, JSON_UNESCAPED_SLASHES);
        $param['tl_imgurl'] = $img;
        $param['addtime'] = time();

        $action = 'add_train_location';
        $intro = "添加新的驾校报名点";
        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $tblname = $this->mschool->train_tablename;
        $name = substr($tblname, 3, strlen($tblname));
       
        $result = $this->mschool->addSchoolMessage($tblname, $param);
        if($result) {
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

    /**
     * 修改报名点
     * @param 
     * @return void
     **/
    public function editSignplaceAjax()
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
        $school_id = $this->mbase->getSchoolidFromLoginauth($this->session->loginauth);
        $param = [];
        $param['id'] = $this->input->post('id', true) ? $this->input->post('id', true) : '';
        $signplacelist = $this->mschool->getSignPlaceInfo($param['id'], $school_id);

        $param['tl_school_id'] = $this->input->post('school_id', true) ? $this->input->post('school_id', true) : $signplacelist['tl_school_id'];
        $param['tl_train_address'] = $this->input->post('name', true) ? $this->input->post('name', true) : $signplacelist['tl_train_address'];
        $param['tl_location_x'] = $this->input->post('location_x', true) ? $this->input->post('location_x', true) : $signplacelist['tl_location_x'];
        $param['tl_location_y'] = $this->input->post('location_y', true) ? $this->input->post('location_y', true) : $signplacelist['tl_location_y'];
        $param['order'] = $this->input->post('order', true) ? $this->input->post('order', true) : $signplacelist['order'];
        $param['tl_phone'] = $this->input->post('phone', true) ? $this->input->post('phone', true) : $signplacelist['tl_phone'];

        $imgurl_one = $this->input->post('imgurl_one', true) ? trim($this->input->post('imgurl_one', true)) : $signplacelist['imgurl_one'];
        $imgurl_two = $this->input->post('imgurl_two', true) ? trim($this->input->post('imgurl_two', true)) : $signplacelist['imgurl_two'];
        $imgurl_three = $this->input->post('imgurl_three', true) ? trim($this->input->post('imgurl_three', true)) : $signplacelist['imgurl_three'];
        $imgurl_four = $this->input->post('imgurl_four', true) ? trim($this->input->post('imgurl_four', true)) : $signplacelist['imgurl_four'];
        $imgurl_five = $this->input->post('imgurl_five', true) ? trim($this->input->post('imgurl_five', true)) : $signplacelist['imgurl_five'];
        $imgurl = [$imgurl_one, $imgurl_two, $imgurl_three, $imgurl_four, $imgurl_five];
        $img = [];
        foreach ($imgurl as $url) {
            if ($url != '') {
                $img[] = $url;
            }
        }

        $img = json_encode($imgurl);
        $param['tl_imgurl'] = $img;
        $param['addtime'] = time();

        $action = 'add_train_location';
        $intro = "添加新的驾校报名点";
        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $tblname = $this->mschool->train_tablename;
        $name = substr($tblname, 3, strlen($tblname));
       
        $result = $this->mbase->updateData($tblname, 'id', $param['id'], $param);
        if($result) {
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


    // 删除报名点
    public function delsignplace() {
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
        $params = ['id'=> $id];
        $res = $this->mschool->delInfo($this->mschool->train_tablename, $params);
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

// 3.驾校场地管理
    /**
     * 场地管理[列表页面展示]
     * @param 
     * @return void
     **/    
    public function place()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/school/place');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 场地管理[添加页面]
     * @param 
     * @return void
     **/    
    public function addplace() {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/school/addplace');
        $this->load->view(TEMPLATE.'/footer');
    }
    
    /**
     * 场地管理[编辑页面]
     * @param 
     * @return void
     **/    
    public function editplace() {
        $this->mbase->loginauth();
        $id = $this->input->get('id') ? $this->input->get('id') : 0;
        $data = $this->mschool->getSchoolInfo($id);
        if(!$data) show_404();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/school/editplace', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 获取场地数据[功能实现]
     * @param 
     * @return void
     */
    public function placeListAjax()
    {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolidFromLoginauth($this->session->loginauth);
        $page = $this->input->post('p') ? intval($this->input->post('p')) : self::$page;
        $limit = $this->input->post('s') ? intval($this->input->post('s')) : self::$limit;

        $param = [];
        $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';
        $param['open'] = $this->input->post('open', true) ? intval($this->input->post('open', true)) : '';
        $pageinfo = $this->mschool->getSitePageNum($school_id, $param, $limit);
        $page = $page < $pageinfo['pagenum'] || $pageinfo['pagenum'] == 0 ? $page : $pageinfo['pagenum']; 
        $start = ($page - 1) * $limit;

        $sitelist = $this->mschool->getSiteList($school_id, $param, $start, $limit);
        $list['pagenum'] = $pageinfo['pagenum'];
        $list['count'] = $pageinfo['count'];
        $list['p'] = $page;
        $list['list'] = $sitelist;
        $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    // 新增场地
    public function addsite()
    {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolidFromLoginauth($this->session->loginauth);
        $data = ['school_id' => $school_id];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/school/addsite', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 新增场地
     * @param 
     * @return void
     **/
    public function addSiteAjax()
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
        $school_id = $this->mbase->getSchoolidFromLoginauth($this->session->loginauth);
        $param = [];
        $province = '';
        $city = '';
        $area = '';
        $province_id = $this->input->post('province_id', true) ? $this->input->post('province_id', true) : 0;
        $city_id = $this->input->post('city_id', true) ? $this->input->post('city_id', true) : 0;
        $area_id = $this->input->post('area_id', true) ? $this->input->post('area_id', true) : 0;
        $provinceinfo = $this->mads->getCityInfoByCondition($province_id, 'province');
        if ( ! empty($provinceinfo)) {
            $province = $provinceinfo['province'];
        }

        $cityinfo = $this->mads->getCityInfoByCondition($city_id, 'city');
        if ( ! empty($cityinfo)) {
            $city = $cityinfo['city'];
        }

        $areainfo = $this->mads->getCityInfoByCondition($area_id, 'area');
        if ( ! empty($areainfo)) {
            $area = $areainfo['area'];
        }
        $address = $this->input->post('s_address', true) ? trim($this->input->post('s_address', true)) : '';
        $param['province_id'] = $province_id;
        $param['city_id'] = $city_id;
        $param['area_id'] = $area_id;
        $param['address'] = $province.$city.$area.$address;

        $param['school_id'] = $this->input->post('school_id', true) ? $this->input->post('school_id', true) : $school_id;
        $param['site_name'] = $this->input->post('name', true) ? $this->input->post('name', true) : '';
        $param['site_status'] = $this->input->post('open_status', true) ? ($this->input->post('open_status', true) === 'false' ? 2 : 1) : 2;
        
        $param['site_desc'] = $this->input->post('site_desc', true) ? trim($this->input->post('site_desc', true)) : '';
        $param['point_text_url1'] = $this->input->post('point_url_one', true) ? trim($this->input->post('point_url_one', true)) : '';
        $param['point_text_url2'] = $this->input->post('point_url_two', true) ? trim($this->input->post('point_url_two', true)) : '';
        $param['model_resource_url'] = $this->input->post('resource_url', true) ? trim($this->input->post('resource_url', true)) : '';
        $param['imgurl'] = $this->input->post('site_imgurl', true) ? trim($this->input->post('site_imgurl', true)) : '';
        $param['add_time'] = time();

        $action = 'add_schoolsite';
        $intro = "添加新的驾校场地";
        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $tblname = $this->mschool->site_tablename;
        $name = substr($tblname, 3, strlen($tblname));
       
        $result = $this->mschool->addSchoolMessage($tblname, $param);
        if($result) {
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

    // 编辑场地[页面展示]
    public function editsite()
    {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolidFromLoginauth($this->session->loginauth);
        $id = $this->input->get('id', true) ? intval($this->input->get('id', true)) : '';
        $data = $this->mschool->getSiteInfo($id, $school_id);
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/school/editsite', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 修改场地信息[功能实现]
     * @param 
     * @return void
     **/
    public function editSiteAjax()
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
        $school_id = $this->mbase->getSchoolidFromLoginauth($this->session->loginauth);
        $param = [];
        $id = $this->input->post('id', true) ? trim($this->input->post('id', true)) : '';
        $param['id'] = $id;
        $siteinfo = $this->mschool->getSiteInfo($id, $school_id);
        $province_id = $this->input->post('province_id', true) ? $this->input->post('province_id', true) : 0;
        $city_id = $this->input->post('city_id', true) ? $this->input->post('city_id', true) : 0;
        $area_id = $this->input->post('area_id', true) ? $this->input->post('area_id', true) : 0;
        if (intval($province_id) != 0 ) {
            $param['province_id'] = $province_id;
        } else {
            $param['province_id'] = $siteinfo['province_id'];
        }

        if (intval($city_id) != 0 ) {
            $param['city_id'] = $city_id;
        } else {
            $param['city_id'] = $siteinfo['city_id'];
        }

        if (intval($area_id) != 0 ) {
            $param['area_id'] = $area_id;
        } else {
            $param['area_id'] = $siteinfo['area_id'];
        }

        $param['address'] = $this->input->post('s_address', true) ? trim($this->input->post('s_address', true)) : $siteinfo['address'];

        $param['school_id'] = $this->input->post('school_id', true) ? $this->input->post('school_id', true) : $siteinfo['school_id'];
        $param['site_name'] = $this->input->post('name', true) ? $this->input->post('name', true) : $siteinfo['site_name'];
        $param['site_status'] = $this->input->post('open_status', true) ? ($this->input->post('open_status', true) === 'false' ? 2 : 1) : 2;
        
        $param['site_desc'] = $this->input->post('site_desc', true) ? trim($this->input->post('site_desc', true)) : $siteinfo['site_desc'];
        $param['point_text_url1'] = $this->input->post('point_url_one', true) ? trim($this->input->post('point_url_one', true)) : $siteinfo['point_text_url1'];
        $param['point_text_url2'] = $this->input->post('point_url_two', true) ? trim($this->input->post('point_url_two', true)) : $siteinfo['point_text_url2'];
        $param['model_resource_url'] = $this->input->post('resource_url', true) ? trim($this->input->post('resource_url', true)) : $siteinfo['model_resource_url'];
        $param['imgurl'] = $this->input->post('site_imgurl', true) ? trim($this->input->post('site_imgurl', true)) : $siteinfo['imgurl'];
        $param['add_time'] = time();

        $action = 'edit_schoolsite';
        $intro = "修改驾校场地信息";
        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $tblname = $this->mschool->site_tablename;
        $name = substr($tblname, 3, strlen($tblname));
       
        $result = $this->mbase->updateData($tblname, 'id', $id, $param);
        if($result) {
            $this->mlog->action_log($action, $name, $id, $login_id, $intro);
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

    /**
     * 删除场地
     * @param $id
     * @return void
     **/
    public function delSite()
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
        $id_arr = array($id);
        $action = 'del_schoolsite';
        $intro = "删除驾校场地";
        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $tblname = $this->mschool->site_tablename;
        $name = substr($tblname, 3, strlen($tblname));

        $res = $this->mbase->delData($tblname, 'id', $id_arr);
        if($res) {
            $this->mlog->action_log($action, $name, $id, $login_id, $intro);
            $data = ['code'=>200, 'msg'=>'删除成功', 'data'=>$res];
        } else {
            $data = ['code'=>200, 'msg'=>'删除失败', 'data'=>$res];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    /**
     * 设置驾校场地的开启状态
     * @param $id, $status
     * @return void
     **/
    public function handleOpenStatus()
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
        $status = $this->input->post('status') ? intval($this->input->post('status')) : 0;
        $data = [
            'id' => $id,
            'site_status' => $status
        ];
        $res = $this->mschool->editSiteInfo($data);
        $action = 'set_site_status';
        if ($status == 1) {
            $intro = "设置场地的开启状态为开启";
        } else {
            $intro = "设置场地的开启状态为关闭";
        }
        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $tblname = $this->mschool->site_tablename;
        $name = substr($tblname, 3, strlen($tblname));
        if($res) {
            $this->mlog->action_log($action, $name, $res, $login_id, $intro);
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


    // 获取省份
    public function provinceajax() {
        $this->mbase->loginauth();
        if(!$this->input->is_ajax_request()) {
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
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        
        $province_id = $this->input->post('pid', true) ? intval($this->input->post('pid', true)) : 0;
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
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $city_id = $this->input->post('cid', true) ? intval($this->input->post('cid', true)) : 0;
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
        $this->load->view(TEMPLATE.'/school/preview');
        $this->load->view(TEMPLATE.'/footer');
    }

    // 上架下架ajax
    public function show() {
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
        $status = $this->input->post('status') ? intval($this->input->post('status')) : 0;
        $data = [
            'l_school_id' => $id,
            'is_show' => $status
        ];
        $res = $this->mschool->editSchoolInfo($data);
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

    /**
     * 设置驾校的热门与不热门
     * @param $id, $status
     * @return void
     **/
    public function handleHotStatus()
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
        $status = $this->input->post('status') ? intval($this->input->post('status')) : 0;
        $data = [
            'l_school_id' => $id,
            'is_hot' => $status
        ];
        $res = $this->mschool->editSchoolInfo($data);
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

// 2.班制列表
    // 班制
    public function shifts() {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $data = ['school_id' => $school_id];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/school/shifts', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    // 班制ajax
    public function shiftsajax() {
        $this->mbase->loginauth();
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $param = [];
        $type = $this->input->post('type', true) ? trim($this->input->post('type', true)) : 'school';
        $param['promote'] = $this->input->post('promote', true) ? intval($this->input->post('promote', true)) : '';
        $param['package'] = $this->input->post('package', true) ? intval($this->input->post('package', true)) : '';
        $param['del'] = $this->input->post('del', true) ? intval($this->input->post('del', true)) : '';
        $param['keywords'] = $this->input->post('keywords', true) ? $this->input->post('keywords', true) : '';

        $page = $this->input->post('p') ? intval($this->input->post('p')) : self::$page;
        $limit = $this->input->post('s') ? intval($this->input->post('s')) : self::$limit;

        $pageinfo = $this->mschool->getShiftsPageNum($school_id, $param, $limit, $type);

        $page = $page < $pageinfo['pagenum'] || $pageinfo['pagenum'] == 0 ? $page : $pageinfo['pagenum'];        
        $start = ($page - 1) * $limit;
        $shifts_list = $this->mschool->getShftsLists($school_id, $param, $start, $limit, $type);
        $list['pagenum'] = $pageinfo['pagenum'];
        $list['count'] = $pageinfo['count'];
        $list['p'] = $page;
        $list['list'] = $shifts_list;
        $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }
    // 活动班制获取
    public function hotshiftsajax() {
        $this->mbase->loginauth();
        $page = $this->input->post('p') ? intval($this->input->post('p')) : self::$page;
        $wherecondition = [
            'is_package'=> 1
        ];
        $pageinfo = $this->mbase->getPageNumByCondition($this->mschool->shifts_tablename, $wherecondition, self::$limit);
        $page = $page < $pageinfo['pn'] || $pageinfo['pn'] == 0 ? $page : $pageinfo['pn'];        
        $start = ($page - self::$page) * self::$limit;
        $shifts_list = $this->mschool->getHotShftsList($wherecondition, $start, self::$limit);
        $shifts_list['pagenum'] = $pageinfo['pn'];
        $shifts_list['count'] = $pageinfo['count'];
        $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$shifts_list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    // 添加
    public function addshifts() {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $data = ['school_id' => $school_id];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/school/addshifts', $data);
        $this->load->view(TEMPLATE.'/footer');
    }
    
    // 编辑
    public function editshifts() {
        $this->mbase->loginauth();
        $id = $this->input->get('id') ? $this->input->get('id') : 0;
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $data = $this->mschool->getShiftsInfo($id, $school_id);
        if(!$data) show_404();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/school/editshifts', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    // 删除班制
    public function delshiftsajax() {
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
        $params = ['id'=> $id];
        $res = $this->mschool->delInfo($this->mschool->shifts_tablename, $params);
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

    // 设置班制促销的状态
    public function handlePromoteStatus() {
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
        $status = $this->input->post('status') ? intval($this->input->post('status')) : 0;
        $data = [
            'id' => $id,
            'is_promote' => $status
        ];
        $res = $this->mschool->editSchoolShiftsInfo($data);
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

    // 学车套餐的设置
    public function changePackage() {
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
        $status = $this->input->post('status') ? intval($this->input->post('status')) : 0;
        $data = [
            'id' => $id,
            'is_package' => $status
        ];
        $res = $this->mschool->editSchoolShiftsInfo($data);
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

    // 上下架班制
    public function showshifts() {
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
        $status = $this->input->post('status') ? intval($this->input->post('status')) : 0;
        $data = [
            'id' => $id,
            'deleted' => $status
        ];
        $res = $this->mschool->editSchoolShiftsInfo($data);
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

    // 搜索当前驾校下的教练
    public function searchCoachAjax()
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
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $key = $this->input->post('key') ? trim($this->input->post('key')) : '';
        $result = $this->mbase->searchCoachList($school_id, ['s_coach_name'=>$key], 20);
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

    // 根据关键词搜索驾校列表
    public function search() {
        $this->mbase->loginauth();
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $where = ['is_show' => 1];
        $key = $this->input->post('key') ? trim($this->input->post('key')) : '';
        $result = $this->mbase->getSearchList('l_school_id, s_school_name', $where, ['s_school_name'=>$key], $this->mschool->school_tablename, 20);
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

    // 添加班制
    public function addshiftsajax()
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
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $sh_school_id = $this->input->post('sh_school_id', true) ? $this->input->post('sh_school_id', true) : '';
        $coach_id = $this->input->post('coach_id', true) ? $this->input->post('coach_id', true) : '';

        if($school_id != 0) {
            $param['sh_school_id'] = $school_id;
        }

        if ($coach_id == '') {
            $param['sh_school_id'] = $sh_school_id;
            $param['coach_id'] = NULL;
        } else {
            $param['coach_id'] = $coach_id;
            $school_list = $this->mschool->getSchoolInfoByCoachId($coach_id);
            if ( ! empty($school_list)) {
                $param['sh_school_id'] = $school_list['l_school_id'];
            } else {
                $param['sh_school_id'] = '';
            }
        }

        $param['sh_title'] = $this->input->post('sh_title', true) ? trim($this->input->post('sh_title', true)) : '';
        $param['sh_money'] = $this->input->post('sh_money', true) ? trim($this->input->post('sh_money', true)) : '';
        $param['sh_original_money'] = $this->input->post('sh_original_money', true) ? trim($this->input->post('sh_original_money', true)) : '';
        $param['sh_type'] = $this->input->post('sh_type', true) ? intval($this->input->post('sh_type', true)) : 2;
        $param['sh_description_2'] = $this->input->post('sh_description_2', true) ? trim($this->input->post('sh_description_2', true)) : '';
        $param['sh_info'] = $this->input->post('sh_info', true) ? trim($this->input->post('sh_info', true)) : '';
        $param['sh_description_1'] = $this->input->post('sh_description_1', true) ? trim($this->input->post('sh_description_1', true)) : '';
        $param['is_package'] = $this->input->post('is_package', true) ? ($this->input->post('is_package', true) === 'true' ? 1 : 2) : 2;


        $sh_license = $this->input->post('sh_license', true) ? trim($this->input->post('sh_license', true)) : '';
        if ($sh_license != '') {
            $param['sh_license_id'] = explode('|', $sh_license)[0];
            $param['sh_license_name'] = explode('|', $sh_license)[1];
        } else {
            $param['sh_license_id'] = $this->input->post('sh_license_id', true) ? intval($this->input->post('sh_license_id', true)) : '';
            $param['sh_license_name'] = $this->input->post('sh_license_name', true) ? trim($this->input->post('sh_license_name', true)) : '';
        }
        
        $sh_tag_info = $this->input->post('sh_tag_info', true) ? trim($this->input->post('sh_tag_info', true)) : '';
        if ($sh_tag_info != '') {
            $param['sh_tag_id'] = explode('|', $sh_tag_info)[0];
            $param['sh_tag'] = explode('|', $sh_tag_info)[1];
        } else {
            $param['sh_tag'] = $this->input->post('sh_tag', true) ? trim($this->input->post('sh_tag', true)) : '';
            $param['sh_tag_id'] = $this->input->post('sh_tag_id', true) ? intval($this->input->post('sh_tag_id', true)) : '';
        }

        $param['is_promote'] = $this->input->post('is_promote', true) ? ($this->input->post('is_promote', true) === 'true' ? 1: 2) : 2;
        $param['coupon_id'] = $this->input->post('coupon_id', true) ? intval($this->input->post('coupon_id', true)) : '';
        $param['deleted'] = $this->input->post('deleted', true) ? ($this->input->post('deleted', true) === 'true' ? 1: 2) : 2;
        $param['order'] = $this->input->post('order', true) ? intval($this->input->post('order', true)) : 50;
        $param['sh_imgurl'] = $this->input->post('sh_imgurl', true) ? trim($this->input->post('sh_imgurl', true)) : '';
        $param['addtime'] = time();
       
        $result = $this->mschool->addSchoolShiftsInfo($param);
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

    // 编辑班制
    public function editshiftsajax()
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
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $id = $this->input->post('id', true) ? $this->input->post('id', true) : '';
        $shifts_list = $this->mschool->getShiftsInfo($id, $school_id);

        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $sh_school_id = $this->input->post('sh_school_id', true) ? $this->input->post('sh_school_id', true) : $shifts_list['sh_school_id'];
        $coach_id = $this->input->post('coach_id', true) ? $this->input->post('coach_id', true) : $shifts_list['coach_id'];
        
        $param['id'] = $id;
        if($school_id != 0) {
            $param['sh_school_id'] = $school_id;
        }

        if ($coach_id == '') {
            $param['sh_school_id'] = $sh_school_id;
            $param['coach_id'] = NULL;
        } else {
            $param['coach_id'] = $coach_id;
            $school_list = $this->mschool->getSchoolInfoByCoachId($coach_id);
            if ( ! empty($school_list)) {
                $param['sh_school_id'] = $school_list['l_school_id'];
            } else {
                $param['sh_school_id'] = '';
            }
        }

        $param['sh_title'] = $this->input->post('sh_title', true) ? trim($this->input->post('sh_title', true)) : $shifts_list['sh_title'];
        $param['sh_money'] = $this->input->post('sh_money', true) ? trim($this->input->post('sh_money', true)) : $shifts_list['sh_money'];
        $param['sh_original_money'] = $this->input->post('sh_original_money', true) ? trim($this->input->post('sh_original_money', true)) : $shifts_list['sh_original_money'];
        $param['sh_type'] = $this->input->post('sh_type', true) ? intval($this->input->post('sh_type', true)) : 2;
        $param['sh_description_2'] = $this->input->post('sh_description_2', true) ? trim($this->input->post('sh_description_2', true)) : '';
        $param['sh_info'] = $this->input->post('sh_info', true) ? trim($this->input->post('sh_info', true)) : '';
        $param['sh_description_1'] = $this->input->post('sh_description_1', true) ? trim($this->input->post('sh_description_1', true)) : '';
        $param['is_package'] = $this->input->post('is_package', true) ? ($this->input->post('is_package', true) === 'true' ? 1 : 2) : 2;

        $sh_license = $this->input->post('sh_license', true) ? trim($this->input->post('sh_license', true)) : '';
        if ($sh_license != '') {
            $param['sh_license_id'] = explode('|', $sh_license)[0];
            $param['sh_license_name'] = explode('|', $sh_license)[1];
        } else {
            $param['sh_license_id'] = $this->input->post('sh_license_id', true) ? intval($this->input->post('sh_license_id', true)) : $shifts_list['sh_license_id'];
            $param['sh_license_name'] = $this->input->post('sh_license_name', true) ? trim($this->input->post('sh_license_name', true)) : $shifts_list['sh_license_name'];
        }
        
        $sh_tag_info = $this->input->post('sh_tag_info', true) ? trim($this->input->post('sh_tag_info', true)) : '';
        if ($sh_tag_info != '') {
            $param['sh_tag_id'] = explode('|', $sh_tag_info)[0];
            $param['sh_tag'] = explode('|', $sh_tag_info)[1];
        } else {
            $param['sh_tag'] = $this->input->post('sh_tag', true) ? trim($this->input->post('sh_tag', true)) : $shifts_list['sh_tag'];
            $param['sh_tag_id'] = $this->input->post('sh_tag_id', true) ? intval($this->input->post('sh_tag_id', true)) : $shifts_list['sh_tag_id'];
        }

        $param['is_promote'] = $this->input->post('is_promote', true) ? ($this->input->post('is_promote', true) === 'true' ? 1: 2) : 2;
        $param['coupon_id'] = $this->input->post('coupon_id', true) ? intval($this->input->post('coupon_id', true)) : '';
        $param['deleted'] = $this->input->post('deleted', true) ? ($this->input->post('deleted', true) === 'true' ? 1: 2) : 2;
        $param['order'] = $this->input->post('order', true) ? intval($this->input->post('order', true)) : $shifts_list['order'];
        $param['sh_imgurl'] = $this->input->post('sh_imgurl', true) ? trim($this->input->post('sh_imgurl', true)) : $shifts_list['sh_imgurl'];
        $param['updatetime'] = time();
        $result = $this->mschool->editSchoolShiftsInfo($param);
        if($result) {
            $data = ['code'=>200, 'msg'=>'修改成功', 'data'=>['result'=>$result]];
        } else {
            $data = ['code'=>100, 'msg'=>'修改失败', 'data'=>['result'=>$result]];            
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
        if(!$this->input->is_ajax_request()) {
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
            'user_type'=> isset($params['user_type']) ? $params['user_type'] : 2,
            'order'=>50,
            'addtime'=>time(),
            'updatetime'=>time(),
        );
        $result = $this->mtags->addTag($this->mtags->systags_tablename, $_data);
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

    public function systagsajax() {
        $this->mbase->loginauth();
        if(!$this->input->is_ajax_request()) {
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
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
            exit;
        }
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
        if(!$this->input->is_ajax_request()) {
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
        $data = ['code'=>200, 'msg'=>'获取优惠券列表成功', 'data'=>$coupon_list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    // 获取驾校详情 ajax
    public function schoolinfoajax() {
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
        $school_info = $this->mschool->getSchoolParamsInfo('s_school_name, l_school_id', $id);
        $data = ['code'=>200, 'msg'=>'获取驾校详情成功', 'data'=>$school_info];
        $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
        exit;
    }

    // 添加银行账号
    public function addaccount() {
        $this->mbase->loginauth();
        $this->session->unset_userdata('addaccount_step');
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/school/addaccount');
        $this->load->view(TEMPLATE.'/footer');
    }

    // 获取驾校银行账号列表
    public function accountlistajax() {
        $this->mbase->loginauth();
        $loginauth = $this->session->loginauth;
        $loginauth_arr = $loginauth ? explode('|', $loginauth) : [];
        $school_id = isset($loginauth_arr[5]) ? $loginauth_arr[5] : 0;

        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $wherecondition = [
            'school_id'=>$school_id,
        ];
        $coupon_list = $this->mschool->getSchoolAccountList($wherecondition);
        $data = ['code'=>200, 'msg'=>'获取银行账户列表成功', 'data'=>$coupon_list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    } 

    public function validatepassajax() {
        $this->mbase->loginauth();
        $loginauth = $this->session->loginauth;
        $loginauth_arr = $loginauth ? explode('|', $loginauth) : [];
        $school_id = isset($loginauth_arr[5]) ? $loginauth_arr[5] : 0;

        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        
        $params = $this->input->post();
        $wherecondition = [
            'cash_pass'=>isset($params['pass']) ? md5($params['pass']) : '',
            'l_school_id'=>$school_id,
        ];
        $result = $this->mbase->getInfoByCondition($this->mschool->school_tablename, $wherecondition);
        if($result) {
            $this->session->addaccount_step = 2;
            $data = ['code'=>200, 'msg'=>'密码验证成功', 'data'=>[]];
        } else {
            $data = ['code'=>100, 'msg'=>'密码验证失败，请查看密码是否设置', 'data'=>[]];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    public function validatebankajax() {
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
        $card_no = isset($params['cardNo']) ? $params['cardNo'] : '';
        // https://ccdcapi.alipay.com/validateAndCacheCardInfo.json?_input_charset=utf-8&cardNo=6217001630032500818&cardBinCheck=true   3401040160000114588
        // $post_data = [
        //     '_input_charset'=> 'utf-8',
        //     'cardNo'=> isset($params['cardNo']) ? $params['cardNo'] : '',
        //     'cardBinCheck'=> 'true'
        // ];
        $account_info = file_get_contents('https://ccdcapi.alipay.com/validateAndCacheCardInfo.json?_input_charset=utf-8&cardNo='.$card_no.'&cardBinCheck=true');
        $result = json_decode($account_info, true);
        if($result['validated']) {
            $this->session->addaccount_step = 3;            
            $wherecondition = ['bank_no'=>$result['bank'], 'card_type'=>$result['cardType']];
            $bank_info = $this->mbase->getBankConfigByCondition($wherecondition);
            $data = ['code'=>200, 'msg'=>'银行卡号验证成功', 'data'=>$bank_info];
        } else {
            $data = ['code'=>100, 'msg'=>'银行卡号验证失败，请手动填写卡类型', 'data'=>$account_info];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    // 获取配置银行列表
    public function banklistajax() {
        $this->mbase->loginauth();
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $bank_list = $this->mbase->getBankConfigList();
        if($result['validated']) {
            $data = ['code'=>200, 'msg'=>'获取银行列表成功', 'data'=>$bank_list];
        } else {
            $data = ['code'=>100, 'msg'=>'获取银行列表失败', 'data'=>[]];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    // 获取验证码
    public function getcodeajax() {
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
        $phone = isset($params['phone']) ? trim($params['phone']) : '';      
        $rand = rand(100000, 999999);
        $content = "尊敬的客户，欢迎您使用嘻哈学车！您的验证码是".$rand."，请在3分钟内输入。【嘻哈学车】";
        $result = $this->sms->sendAll($phone, $content);
        if($result['stat'] == '100') {
            // 将code存入数据库
            $insertdata = ['s_phone'=>$phone, 's_code'=>$rand, 'addtime'=>time()];
            $res = $this->mbase->insertVelidate($insertdata);
            $data = ['code'=>200, 'msg'=>'获取验证码成功，请查看手机短信', 'data'=>$result];
        } else {
            $data = ['code'=>100, 'msg'=>'获取验证码失败，请稍后再试', 'data'=>[]];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }
    
    // 添加银行账户
    public function addaccountajax() {
        $this->mbase->loginauth();
        $loginauth = $this->session->loginauth;
        $loginauth_arr = $loginauth ? explode('|', $loginauth) : [];
        $school_id = isset($loginauth_arr[5]) ? $loginauth_arr[5] : 0;

        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $params = $this->input->post();
        $account_user_name = isset($params['account_user_name']) ? trim($params['account_user_name']) : '';
        $account_no = isset($params['account_no']) ? trim($params['account_no']) : '';
        $bank_name = isset($params['bank_name']) ? trim($params['bank_name']) : '';
        $account_type = isset($params['account_type']) ? trim($params['account_type']) : '';
        $account_phone = isset($params['account_phone']) ? trim($params['account_phone']) : '';
        $validate_code = isset($params['validate_code']) ? trim($params['validate_code']) : '';
        $account_identifyid = isset($params['account_identifyid']) ? trim($params['account_identifyid']) : '';
        $insertdata = ['school_id'=>$school_id, 'account_user_name'=>$account_user_name, 'bank_name'=>$bank_name, 'account_no'=>$account_no, 'account_phone'=>$account_phone, 'account_identifyid'=>$account_identifyid, 'is_default'=>1, 'addtime'=>time()];
        $wherecondition = ['account_no'=>$account_no];
        // 验证验证码是否正确
        $_wherecondition = ['s_phone'=>$account_phone, 's_code'=>$validate_code];
        $res = $this->mbase->velidatePhone($_wherecondition);
        if(!$res) {
            $data = ['code'=>100, 'msg'=>'验证码有误', 'data'=>[]];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $result = $this->mbase->_insert($this->mschool->saccount_tablename, $insertdata, $wherecondition);
        if($result) {
            $data = ['code'=>200, 'msg'=>'添加或编辑银行账户成功', 'data'=>$result];
        } else {
            $data = ['code'=>100, 'msg'=>'添加或编辑银行账户失败', 'data'=>[]];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }
    
}
?>