<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * coach cars | cars category
 *
 * @category cars
 * @package cars
 * @author wl
 * @return void
 **/

class Cars extends CI_Controller {

    static $limit = 10;
    static $page = 1;

    public function __construct() 
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('mbase');
        $this->load->model('mcars');
        $this->config->load('upload');
        $this->load->library('upload');
        $this->load->database();

    }

// 车型列表 | 教练车辆列表
    /**
     * 教练车辆[页面展示]
     * @param
     * @return void
     **/
     public function index()
    {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $data = ['school_id' => $school_id];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/cars/index', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 车型列表[页面展示]
     * @param
     * @return void
     **/
    public function carcate()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/cars/carcate');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 学车视频管理
     * @param
     * @return viod
     **/
    public function video()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/cars/video');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 车型 | 教练车辆列表[数据获取]
     * @param data
     * @return void
     **/
    public function listAjax()
    {
        $param = [];
        $type_arr = ['category', 'coachcar', 'video'];
        $type = $this->input->get('type') ? trim($this->input->get('type')) : '';

        if ( '' == $type 
            OR ! in_array($type, $type_arr)) 
        {
            $data = [
                'code'  => 400,
                'msg'   => '参数类型错误',
                'data'  => new \stdClass
            ];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        $school_id = $this->mbase->getSchoolidFromLoginauth($this->session->loginauth);
        $page = $this->input->post('p') ? (int) $this->input->post('p') : self::$page;
        $limit = $this->input->post('s') ? (int) $this->input->post('s') : self::$limit;

        if ( $type == 'category') {
            $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';

        } elseif ($type == 'coachcar') {
            $param['ctype'] = $this->input->post('ctype', true) ? intval($this->input->post('ctype', true)) : '';
            $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';

        } elseif ($type == 'video') {
            $param['course'] = $this->input->post('course', true) ? trim($this->input->post('course', true)) : '';
            $param['ctype'] = $this->input->post('ctype', true) ? trim($this->input->post('ctype', true)) : '';
            $param['open'] = $this->input->post('open', true);
            $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';

        }

        $page_info = $this->mcars->getCarsPageNum($school_id, $param, $limit, $type);
        $pagenum = $page_info['pagenum'];
        $count = $page_info['count'];
        
        $page = $page < $pagenum || $pagenum == 0 ? $page : $pagenum;
        $start = ($page - self::$page) * $limit;
        
        $cars_info = $this->mcars->getCarsInfo($school_id, $param, $start, $limit, $type);
        
        $list = [
            'p' => $page,
            'pagenum' => $pagenum,
            'count' => $count,
            'list' => $cars_info['list'],
        ];

        $data = [
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => $list,
        ];

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    /**
     * 新增车型[页面展示]
     * @return void
     **/
    public function addCate()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/cars/addcate');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 新增教练车辆[页面展示]
     * @return void
     **/
    public function add()
    {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolidFromLoginauth($this->session->loginauth);
        $data = ['school_id' => $school_id];        
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/cars/add', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 新增学车视频[页面展示]
     * @return void
     **/
     public function addvideo()
     {
         $this->mbase->loginauth();
         $this->load->view(TEMPLATE.'/header');
         $this->load->view(TEMPLATE.'/cars/addvideo');
         $this->load->view(TEMPLATE.'/footer');
     }

    /**
     * 新增教练车辆[功能实现]
     * @param $data
     * @param $type
     * @return void
     **/
    public function addAjax()
    {
        if ( ! $this->input->is_ajax_request()) {
            $data = [
                'code'  => 400,
                'msg'   => '请求方式错误！',
                'data'  => ''
            ];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        $type_arr = ['category', 'coachcar', 'video'];
        $type = $this->input->get('type') ? trim($this->input->get('type')) : '';

        if ( '' == $type 
            OR ! in_array($type, $type_arr)) 
        {
            $data = [
                'code'  => 400,
                'msg'   => '参数类型错误',
                'data'  => new \stdClass
            ];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        if ( $type == 'category') {

            $brand = $this->input->post('brand', true) ? trim($this->input->post('brand', true)) : '';
            $subtype = $this->input->post('subtype', true) ? trim($this->input->post('subtype', true)) : '';
            $point_text_url = $this->input->post('point_text_url', true) ? trim($this->input->post('point_text_url', true)) : '';
            $name = trim($brand.$subtype);

            $data = [
                'name' => $name,
                'brand' => $brand,
                'subtype' => $subtype,
                'point_text_url' => $point_text_url,
                'addtime' => time()
            ];
            $tblname = $this->mcars->cate_tbl;
            $name = substr($tblname, 3, strlen($tblname));
            $action = 'add_cars_category';
            $intro = "添加新的车型";
            

        } elseif ( $type == 'coachcar' ) {
            $cars_imgurl = [];
            $imgurl_one = $this->input->post('imgurl_one', true) ? trim($this->input->post('imgurl_one', true)) : '';
            $imgurl_two = $this->input->post('imgurl_two', true) ? trim($this->input->post('imgurl_two', true)) : '';
            $imgurl_three = $this->input->post('imgurl_three', true) ? trim($this->input->post('imgurl_three', true)) : '';
            $imgurl = [$imgurl_one, $imgurl_two, $imgurl_three];
            foreach ($imgurl as $url) {
                if ($url != '') {
                    $cars_imgurl[] = $url;
                }
            }
            $cars_imgurl = json_encode($cars_imgurl, JSON_UNESCAPED_SLASHES);
            $data = [
                'school_id' => $this->input->post('school_id', true) ? intval($this->input->post('school_id', true)) : '',
                'name' => $this->input->post('name', true) ? trim($this->input->post('name', true)) : '',
                'car_no' => $this->input->post('car_no', true) ? trim($this->input->post('car_no', true)) : '',
                'car_type' => $this->input->post('car_type', true) ? intval($this->input->post('car_type', true)) : '',
                'car_cate_id' => $this->input->post('car_cate_id', true) ? intval($this->input->post('car_cate_id', true)) : '',
                'imgurl' => $cars_imgurl,
                'original_imgurl' => $cars_imgurl,
                'addtime' => time()
            ];
            $tblname = $this->mcars->cars_tbl;
            $name = substr($tblname, 3, strlen($tblname));
            $action = 'add_car';
            $intro = "添加新的教练车辆";

        } elseif ( $type == 'video' ) {
            $title = $this->input->post('title', true) ? trim($this->input->post('title', true)) : '';
            $car_type = $this->input->post('car_type', true) ? trim($this->input->post('car_type', true)) : '';
            $course = $this->input->post('course', true) ? trim($this->input->post('course', true)) : '';
            $data = [
                'title' => $title,
                'skill_intro' => $this->input->post('skill_intro', true) ? trim($this->input->post('skill_intro', true)) : '',
                'car_type' => $this->input->post('car_type', true) ? trim($this->input->post('car_type', true)) : '',
                'course' => $this->input->post('course', true) ? trim($this->input->post('course', true)) : '',
                'v_order' => $this->input->post('order', true) ? intval($this->input->post('order', true)) : 50,
                'is_open' => $this->input->post('is_open', true) ? ($this->input->post('is_open', true) === "true" ? 1 : 0) : 1,
                'views' => $this->input->post('views', true) ? intval($this->input->post('views', true)) : 0,
                'video_time' => $this->input->post('video_time', true) ? intval($this->input->post('video_time', true)) : 0,
                'video_desc' => $this->input->post('video_desc', true) ? trim($this->input->post('video_desc', true)) : '',
                'pic_url' => $this->input->post('picture', true) ? trim($this->input->post('picture', true)) : '',
                'video_url' => $this->input->post('videourl', true) ? trim($this->input->post('videourl', true)) : '',
                'addtime' => time(),
                'updatetime' => 0,
            ];
            
            $tblname = $this->mcars->video_tbl;
            $name = substr($tblname, 3, strlen($tblname));
            $action = 'add_video';
            $intro = "添加新的科目视频";

        }

        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        if ($type == 'video') {
            $videoinfo = $this->mcars->getVideoByCondition($title, $car_type, $course);
            if ( ! empty($videoinfo)) {
                $id = $videoinfo['id'];
                $data['id'] = $id;
                $data['updatetime'] = time();
                $result = $this->mcars->editData($data, $tblname);
            } else {
                $result = $this->mcars->add($data, $tblname);
            }
        } else {
            $result = $this->mcars->add($data, $tblname);
        }

        if ($result) {
            $this->mlog->action_log($action, $name, $result, $login_id, $intro);
            $data = [
                'code'  => 200,
                'msg'   => '新增成功',
                'data'  => ['success' => $result]
            ];
        } else {
            $data = [
                'code'  => 400,
                'msg'   => '新增失败',
                'data'  => ['default' => $result]
            ];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    /**
     * 编辑教练车辆[页面展示]
     * @return void
     **/
    public function edit()
    {
        $this->mbase->loginauth();
        $id = $this->input->get('id') ? intval($this->input->get('id')) : '';
        $school_id = $this->mbase->getSchoolidFromLoginauth($this->session->loginauth);
        $data = $this->mcars->getCarById($id);
        $data['l_school_id'] = $school_id;
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/cars/edit', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 编辑车型[页面展示]
     * @return void
     **/
    public function editCate()
    {
        $this->mbase->loginauth();
        $id = $this->input->get('id') ? intval($this->input->get('id')) : '';
        $data = $this->mcars->getCarCate($id);
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/cars/editcate', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    
    /**
     * 编辑教练车辆[页面展示]
     * @return void
     **/
    public function editvideo()
    {
        $this->mbase->loginauth();
        $id = $this->input->get('id') ? intval($this->input->get('id')) : '';
        $data = $this->mcars->getVideoInfoById($id);
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/cars/editvideo', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 编辑教练车辆[功能实现]
     * @param $data
     * @param $type
     * @return void
     **/
    public function editAjax()
    {
        $this->mbase->loginauth();
        if ( ! $this->input->is_ajax_request()) {
            $data = [
                'code'  => 400,
                'msg'   => '请求方式错误！',
                'data'  => ''
            ];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        $type_arr = ['category', 'coachcar', 'video'];
        $type = $this->input->get('type') ? trim($this->input->get('type')) : '';

        if ( '' == $type 
            OR ! in_array($type, $type_arr)) 
        {
            $data = [
                'code'  => 400,
                'msg'   => '参数类型错误',
                'data'  => new \stdClass
            ];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        if ( $type == 'category') {

            $id = $this->input->post('id', true) ? trim($this->input->post('id', true)) : 0;
            $cars_info = $this->mcars->getCarCate($id);
            $brand = $this->input->post('brand', true) ? trim($this->input->post('brand', true)) : $cars_info['brand'];
            $subtype = $this->input->post('subtype', true) ? trim($this->input->post('subtype', true)) : $cars_info['subtype'];
            $point_text_url = $this->input->post('point_text_url', true) ? trim($this->input->post('point_text_url', true)) : $cars_info['point_text_url'];
            $name = trim($brand.$subtype);

            $data = [
                'id' => $id,
                'name' => $name,
                'brand' => $brand,
                'subtype' => $subtype,
                'point_text_url' => $point_text_url,
                'addtime' => time()
            ];

            $tblname = $this->mcars->cate_tbl;
            $name = substr($tblname, 3, strlen($tblname));
            $action = 'edit_cars_category';
            $intro = "修改ID为".$id."的车型";

        } elseif ( $type == "coachcar" ) {
            
            $id = $this->input->post('id') ? intval($this->input->post('id')) : 0;
            $cars_info = $this->mcars->getCarById($id);
            $imgurl_one = $this->input->post('imgurl_one', true) ? trim($this->input->post('imgurl_one', true)) : $cars_info['imgurl_one'];
            $imgurl_two = $this->input->post('imgurl_two', true) ? trim($this->input->post('imgurl_two', true)) : $cars_info['imgurl_two'];
            $imgurl_three = $this->input->post('imgurl_three', true) ? trim($this->input->post('imgurl_three', true)) : $cars_info['imgurl_three'];

            $cars_imgurl = [];
            $imgurl = [$imgurl_one, $imgurl_two, $imgurl_three];
            foreach ($imgurl as $url) {
                if ($url != '') {
                    $cars_imgurl[] = $url;
                }
            }
            $cars_imgurl = json_encode($cars_imgurl, JSON_UNESCAPED_SLASHES);
        
            $sid = $this->input->post('school_id') ? intval($this->input->post('school_id')) : 0;
            $school_name = $this->input->post('school_name') ? intval($this->input->post('school_name')) : 0;
            $cate_id = $this->input->post('car_cate_id') ? intval($this->input->post('car_cate_id')) : 0;
            $cate_name = $this->input->post('cate_name') ? intval($this->input->post('cate_name')) : 0;
            $name = $this->input->post('name') ? trim($this->input->post('name')) : $cars_info['name'];
            $car_type = $this->input->post('car_type') ? intval($this->input->post('car_type')) : 1;
            $car_no = $this->input->post('car_no') ? trim($this->input->post('car_no')) : $cars_info['car_no'];
            if ($school_name == 0) {
                $cars_school_id = $sid;
            } else {
                $cars_school_id = $school_name;
            }

            if ($cate_name == 0) {
                $car_cate_id = $cate_id;
            } else {
                $car_cate_id = $cate_name;
            }
            $data = [
                'id' => $id,
                'name' => $name,
                'school_id' => $cars_school_id,
                'car_cate_id' => $car_cate_id,
                'car_no' => $car_no,
                'car_type' => $car_type,
                'imgurl' => $cars_imgurl,
                'original_imgurl' => $cars_imgurl,
                'car_type' => $car_type,
                'addtime' => time()
            ];
            $tblname = $this->mcars->cars_tbl;
            $name = substr($tblname, 3, strlen($tblname));
            $action = 'edit_car';
            $intro = "修改ID为".$id."的车辆信息";

        } elseif ( $type == "video" ) {
            $id = $this->input->post('id', true) ? intval($this->input->post('id', true)) : '';
            $video_info = $this->mcars->getVideoInfoById($id);

            $title = $this->input->post('title', true) ? trim($this->input->post('title', true)) : $video_info['title'];
            $skill_intro = $this->input->post('skill_intro', true) ? trim($this->input->post('skill_intro', true)) : $video_info['skill_intro'];
            $car_type = $this->input->post('car_type', true) ? trim($this->input->post('car_type', true)) : $video_info['car_type'];
            $course = $this->input->post('course', true) ? trim($this->input->post('course', true)) : $video_info['course'];
            $v_order = $this->input->post('order', true) ? intval($this->input->post('order', true)) : 50;
            $is_open = $this->input->post('is_open', true) ? ($this->input->post('is_open', true) === "true" ? 1 : 0) : 1;
            $views = $this->input->post('views', true) ? intval($this->input->post('views', true)) : 0;
            $video_time = $this->input->post('video_time', true) ? intval($this->input->post('video_time', true)) : 0;
            $video_desc = $this->input->post('video_desc', true) ? trim($this->input->post('video_desc', true)) : '';
            $pic_url = $this->input->post('picture', true) ? trim($this->input->post('picture', true)) : '';
            $video_url = $this->input->post('videourl', true) ? trim($this->input->post('videourl', true)) : '';

            $data = [
                'id' => $id,
                'title' => $title,
                'skill_intro' => $skill_intro,
                'car_type' => $car_type,
                'course' => $course,
                'v_order' => $v_order,
                'is_open' => $is_open,
                'views' => $views,
                'video_time' => $video_time,
                'video_desc' => $video_desc,
                'pic_url' => $pic_url,
                'video_url' => $video_url,
                'updatetime' => time(),
            ];
            
            $tblname = $this->mcars->video_tbl;
            $name = substr($tblname, 3, strlen($tblname));
            $action = 'edit_video';
            $intro = "修改科目视频的信息";

        }

        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $result = $this->mcars->editData($data, $tblname);

        if ($result) {
            $this->mlog->action_log($action, $name, $id, $login_id, $intro);
            $data = [
                'code'  => 200,
                'msg'   => '修改成功',
                'data'  => ['success' => $result]
            ];
        } else {
            $data = [
                'code'  => 400,
                'msg'   => '修改失败',
                'data'  => ['default' => $result]
            ];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;

    }

    /**
     * 删除数据
     * @param type
     * @param id
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
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        $type_arr = ['category', 'coachcar', 'video'];
        $type = $this->input->get('type') ? trim($this->input->get('type')) : '';
        
        if ( '' == $type 
            OR ! in_array($type, $type_arr)) 
        {
            $data = [
                'code'  => 400,
                'msg'   => '参数类型错误',
                'data'  => new \stdClass
            ];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        $id = $this->input->post('id') ? (int)$this->input->post('id') : '';

        if ( $type == 'category' ) {
            $tblname = $this->mcars->cate_tbl;
            $name = substr($tblname, 3, strlen($tblname));
            $action = 'del_cars_category';
            $intro = "删除ID为".$id."的车型信息";

        } elseif ( $type == "coachcar" ) {
            $tblname = $this->mcars->cars_tbl;
            $name = substr($tblname, 3, strlen($tblname));
            $action = 'del_car';
            $intro = "删除ID为".$id."的车辆";
            
        } elseif ( $type == "video" ) {
            $tblname = $this->mcars->video_tbl;
            $name = substr($tblname, 3, strlen($tblname));
            $action = 'del_video';
            $intro = "删除ID为".$id."的科目";
            
        }

        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $result = $this->mcars->del($id, $tblname);

        if ($result) {
            $this->mlog->action_log($action, $name, $id, $login_id, $intro);
            $data = [
                'code'  => 200,
                'msg'   => '删除成功',
                'data'  => ['success' => $result]
            ];
        } else {
            $data = [
                'code'  => 400,
                'msg'   => '删除失败',
                'data'  => ['default' => $result]
            ];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;

    }

    /**
     * 获取车种类信息
     * @param
     * @return void
     **/
    public function searchAjax()
    {
        
        $key = $this->input->post('key') ? trim($this->input->post('key')) : '';
        $list = $this->mcars->searchCarCateList($key);

        if ($list) {
            $data = [
                'code'  => 200,
                'msg'   => '获取成功',
                'data'  => $list
            ];
        } else {
            $data = [
                'code'  => 400,
                'msg'   => '获取失败',
                'data'  => $list
            ];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;

    } 

    /**
     * 设置学车视频的开启状态
     * @param
     * @return void
     **/
    public function handleOpen()
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
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        $id = $this->input->post('id', true) ? intval($this->input->post('id', true)) : '';
        $status = $this->input->post('status', true);
        $data = ['id' => $id, 'is_open' => $status];

        $tblname = $this->mcars->video_tbl;
        $name = substr($tblname, 3, strlen($tblname));
        $action = 'set_video_open';
        if ($status == 1) {
            $intro = "将ID为".$id."的视频状态设置成开启";
        } else {
            $intro = "将ID为".$id."的视频状态设置成关闭";
        }
        
        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $result = $this->mcars->editData($data, $tblname);

        if ($result) {
            $this->mlog->action_log($action, $name, $id, $login_id, $intro);
            $data = ['code' => 200, 'msg' => '设置成功', 'data' => $result];
        } else {
            $data = ['code' => 400, 'msg' => '设置失败', 'data' => $result];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

  

}

