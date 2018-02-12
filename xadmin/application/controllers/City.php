<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 区域管理
 *
 * @category city
 * @package city
 * @author wl
 * @return void
 **/

class City extends CI_Controller {

    static $limit = 10;
    static $page = 1;
    
    public function __construct() 
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('mbase');
        $this->load->model('mcity');
        $this->load->database();

    }

// 1.省市区列表
    /**
     * 省市区列表[页面展示]
     * @return void
     **/
    public function index() 
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/city/index');
        $this->load->view(TEMPLATE.'/footer');
    }
    
    /**
     * 省市区列表[获取数据]
     * @param data
     * @return void
     **/
    public function listAjax() 
    {
        $param = [];
        $param['is_hot'] = $this->input->post('hot', true) ? $this->input->post('hot', true) : '';
        $param['keywords'] = trim((string)$this->input->post('keywords', true)) ? trim((string)$this->input->post('keywords', true)) : '';
        $page = $this->input->post('p') ? (int)$this->input->post('p') : self::$page;
        $limit = $this->input->post('s') ? (int)$this->input->post('s') : self::$limit;

        $page_info = $this->mcity->getAreaPageNum($param, $limit);
        $pagenum = $page_info['pagenum'];
        $count = $page_info['count'];

        $page = $page < $pagenum || $pagenum == 0 ? $page : $pagenum;
        $start = ($page - self::$page) * $limit;

        $arealist = $this->mcity->getAreaList($param, $start, $limit);
        $area_list['p'] = $page;
        $area_list['pagenum'] = $pagenum;
        $area_list['count'] = $count;
        $area_list['list'] = $arealist['list'];
        $data = [
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => $area_list,
        ];

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;

    }

    /**
     * 设置热门城市
     * @param id
     * @param status
     * @return void
     **/
    public function handleHotStatus()
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
        $post = $this->input->post();
        $id = $this->input->post('id', true) ? $this->input->post('id', true) : '';
        $status = $this->input->post('status', true) ? $this->input->post('status', true) : '';
        $param = [
            'id' => $id,
            'is_hot' => $status
        ];

        $tblname = $this->mcity->city_tbname;
        $name = substr($tblname, 3, strlen($tblname));
        $action = 'set_hot_city';
        if ($status == 1) {
            $intro = "设置城市的热门状态为热门[ID: ".$id."]";
        } else {
            $intro = "设置城市的热门状态为不热门[ID: ".$id."]";
        }
        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);

        $update_ok = $this->mcity->updateData($param, $tblname);
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
     * 删除数据
     * @param $post
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

        $id = $this->input->post('id', true) ? $this->input->post('id', true): '';
        $data = ['id' => $id];
        
        $name = $this->input->get('name') ? (string)$this->input->get('name') : '';
        if ($name == 'area') {
            $tblname = $this->mcity->area_tbname;
            $name = substr($tblname, 3, strlen($tblname));
            $action = 'del_city';
            $intro = "删除ID为".$id."的城市";

        } else {
            $tblname = $this->mcity->city_tbname;
            $name = substr($tblname, 3, strlen($tblname));
            $action = 'del_hot_city';
            $intro = "删除ID为".$id."的热门城市";

        }

        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $result = $this->mcity->del($data, $tblname);
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
                'data'  => ['defult' => $result]
            ];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    /**
     * 新增地区[页面展示]
     * @param 
     * @return void
     **/
    public function add()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/city/add');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 新增地区[页面逻辑]
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

        $name = $this->input->get('name') ? trim($this->input->get('name')) : '';

        if ( $name == '') {
            $data = [
                'code'  => 400,
                'msg'   => '缺少参数',
                'data'  => new \stdClass
            ];

            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT))
                ->_display();
            exit;
        }
        
        if ( $name == "area") {

            $fatherid = $this->input->post('cityid', true) ? intval($this->input->post('cityid', true)) : 0;
            $area = $this->input->post('area', true) ? trim($this->input->post('area', true)) : '';
            $areaid = $this->input->post('areaid', true) ? intval($this->input->post('areaid', true)) : 0;
            $param = [
                'areaid' => $areaid,
                'area' => $area,
                'fatherid' => $fatherid
            ];

            $tblname = $this->mcity->area_tbname;
            // check city
            $checkArea = $this->mcity->check($param, $tblname);
            if ( ! empty($checkArea)) {
                $data = ['code' => 400, 'msg' => '该地区已存在', 'data' => $checkArea];
                $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT))
                    ->_display();
                exit;
            }

            $name = substr($tblname, 3, strlen($tblname));
            $action = 'add_city';
            $intro = "添加新的地区";

        } elseif ($name == "city") {
            
            $fatherid = $this->input->post('provinceid', true) ? intval($this->input->post('provinceid', true)) : 0;
            $city = $this->input->post('city', true) ? trim($this->input->post('city', true)) : '';
            $cityid = $this->input->post('cityid', true) ? intval($this->input->post('cityid', true)) : 0;
            $leter = $this->input->post('leter', true) ? trim($this->input->post('leter', true)) : 0;
            $acronym = $this->input->post('acronym', true) ? trim($this->input->post('acronym', true)) : 0;
            $spelling = $this->input->post('spelling', true) ? trim($this->input->post('spelling', true)) : 0;
            $is_hot = $this->input->post('is_hot', true) ? ($this->input->post('is_hot', true) === 'true' ? 1 : 2) : 2;

            $param = [
                'cityid' => $cityid,
                'city' => $city,
                'fatherid' => $fatherid,
                'leter' => $leter,
                'acronym' => $acronym,
                'spelling' => $spelling,
                'is_hot' => $is_hot,
            ];
            $tblname = $this->mcity->city_tbname;
            $name = substr($tblname, 3, strlen($tblname));
            $action = 'add_hot_city';
            $intro = "添加新的热门城市";
        }

        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $result = $this->mcity->add($param, $tblname);
        if($result) {
            $this->mlog->action_log($action, $name, $result, $login_id, $intro);
            $data = ['code'=>200, 'msg'=>'添加成功', 'data'=>['result'=>$result]];
        } else {
            $data = ['code'=>400, 'msg'=>'添加失败', 'data'=>['result'=>$result]];            
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;

    }

    /**
     * 编辑地区[页面展示]
     * @param 
     * @return void
     **/
    public function edit()
    {
        $this->mbase->loginauth();
        $id = $this->input->get('id') ? $this->input->get('id') : 0;
        $data = $this->mcity->getPcaById($id);
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/city/edit', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 编辑地区[页面逻辑]
     * @param data
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

        $name = $this->input->get("name") ? trim($this->input->get("name")) : "";

        if ($name == "") {
            $data = [
                'code'  => 400,
                'msg'   => '缺少参数',
                'data'  => new \stdClass
            ];

            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT))
                ->_display();
            exit;
        }

        if ($name == "area") {
            $id = $this->input->post('id', true) ? intval($this->input->post('id', true)) : 0;
            $area_list = $this->mcity->getPcaById($id);
            $area = $this->input->post('area', true) ? trim($this->input->post('area', true)) : $area_list['area'];
            $areaid = $this->input->post('areaid', true) ? intval($this->input->post('areaid', true)) : $area_list['areaid'];
            $fatherid = $this->input->post('city', true) ? intval($this->input->post('city', true)) : $area_list['city'];
            if ($fatherid === 0) {
                $fatherid = $area_list['fatherid'];
            } 

            $param = [
                'id' => $id,
                'areaid' => $areaid,
                'area' => $area,
                'fatherid' => $fatherid
            ];

            $tblname = $this->mcity->area_tbname;
            $name = substr($tblname, 3, strlen($tblname));
            $action = 'edit_city';
            $intro = "修改ID为".$id."的地区信息";

        } elseif ($name == "city") {

            $id = $this->input->post('id', true) ? intval($this->input->post('id', true)) : 0;
            $city_list = $this->mcity->getPcaById($id, 'city');

            $city = $this->input->post('city', true) ? trim($this->input->post('city', true)) : $city_list['city'];
            $cityid = $this->input->post('cityid', true) ? intval($this->input->post('cityid', true)) : $city_list['cityid'];
            $leter = $this->input->post('leter', true) ? trim($this->input->post('leter', true)) : $city_list['leter'];
            $acronym = $this->input->post('acronym', true) ? trim($this->input->post('acronym', true)) : $city_list['acronym'];
            $spelling = $this->input->post('spelling', true) ? trim($this->input->post('spelling', true)) : $city_list['spelling'];
            $is_hot = $this->input->post('is_hot', true) ? ($this->input->post('is_hot', true) === 'true' ? 1 : 2) : 2;

            $provinceid = $this->input->post('provinceid', true) ? intval($this->input->post('provinceid', true)) : 0;
            $province = $this->input->post('province', true) ? intval($this->input->post('province', true)) : 0;
            if ($provinceid != $province) {
                $provinceid = $province;
            } 
            $param = [
                'id' => $id,
                'cityid' => $cityid,
                'city' => $city,
                'fatherid' => $provinceid,
                'leter' => $leter,
                'acronym' => $acronym,
                'spelling' => $spelling,
                'is_hot' => $is_hot,
            ];
            
            $tblname = $this->mcity->city_tbname;
            $name = substr($tblname, 3, strlen($tblname));
            $action = 'edit_hot_city';
            $intro = "修改ID为".$id."的城市信息";
        }

        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $update_ok = $this->mcity->updateData($param, $tblname);

        if($update_ok) {
            $this->mlog->action_log($action, $name, $id, $login_id, $intro);
            $data = ['code'=>200, 'msg'=>'修改成功', 'data'=>['result'=>$update_ok]];
        } else {
            $data = ['code'=>400, 'msg'=>'修改失败', 'data'=>['result'=>$update_ok]];            
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    /**
     * 搜索城市 | 省份
     * @param query
     * @return void
     **/
    public function searchAjax()
    {
        $name = $this->input->get('name') ? (string)$this->input->get('name') : "";

        if ( $name == "") {
            $data = [
                'code'  => 400,
                'msg'   => '缺少参数',
                'data'  => new \stdClass
            ];

            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT))
                ->_display();
            exit;
        }
        $post = $this->input->post();
        $key = $post['key'] ? trim($post['key']) : '';
        $list = $this->mcity->searchCityInfo($key, $name);
        
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

// 2、热门城市
    /**
     * 热门城市[页面展示]
     * @return void
     **/
    public function hotcity()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/city/hotcity');
        $this->load->view(TEMPLATE.'/footer');
    }
    
    /**
     * 热门城市[数据获取]
     * @param data
     * @return void
     **/
    public function listCityAjax()
    {
        $param = [];
        $param['is_hot'] = $this->input->post('hot', true) ? $this->input->post('hot', true) : '';
        $param['keywords'] = trim((string)$this->input->post('keywords', true)) ? trim((string)$this->input->post('keywords', true)) : '';
        $page = $this->input->post('p') ? (int)$this->input->post('p') : self::$page;
        $limit = $this->input->post('s') ? (int)$this->input->post('s') : self::$limit;

        $page_info = $this->mcity->getCityPageNum($param, $limit);
        $pagenum = $page_info['pagenum'];
        $count = $page_info['count'];
        $page = $page < $pagenum || $pagenum == 0 ? $page : $pagenum;
        $start = ($page - self::$page) * $limit;

        $citylist = $this->mcity->getCityList($param, $start, $limit);

        $city_list['p'] = $page;
        $city_list['pagenum'] = $pagenum;
        $city_list['count'] = $count;
        $city_list['list'] = $citylist['list'];
        $data = [
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => $city_list,
        ];

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }

    /**
     * 新增城市[页面展示]
     * @param
     * @return void
     **/
    public function addCity()
    {
      $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/city/addcity');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 编辑城市[页面展示]
     * @param
     * @return void
     **/
    public function editCity()
    {
        $this->mbase->loginauth();
        $id = $this->input->get('id') ? $this->input->get('id') : 0;
        $data = $this->mcity->getPcaById($id, 'city');
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/city/editcity', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    


}
