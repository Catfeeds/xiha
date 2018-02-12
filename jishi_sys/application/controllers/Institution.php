<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 驾培机构
class Institution extends CI_Controller {

    static $limit = 10;
    static $page = 1;
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('city');
    }

    public function index()
    {
        $this->load->view('default/header');
        $this->load->view('default/menu');
        $this->load->view('default/footer');
    }
    
    public function list() {
        $page = $this->input->get('p') ? intval($this->input->get('p')) : self::$page;
        $pageinfo = $this->city->getCityPageNum(self::$limit);        
    
        $this->load->view('default/header');      
        $this->load->view('default/list', array('p'=>$page, 'pagenum'=>$pageinfo['pn'], 'count'=>$pageinfo['count']));
        $this->load->view('default/footer');     
    }

    // 列表ajax
    public function listajax()
    {
        $page = $this->input->post('p') ? intval($this->input->post('p')) : self::$page;
        $start = ($page - self::$page) * self::$limit;
        $cityinfo = $this->city->getCityList($start, self::$limit);
        $pageinfo = $this->city->getCityPageNum(self::$limit);
        $cityinfo['pagenum'] = $pageinfo['pn'];      
        $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$cityinfo];
        // sleep(1);
        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
        exit;
    }

    public function add() {
        $this->load->view('default/header');
        $this->load->view('default/add');
        $this->load->view('default/footer');
    }
}
