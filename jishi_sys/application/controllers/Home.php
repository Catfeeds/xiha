<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 首页
class Home extends CI_Controller {

    static $limit = 10;
    static $page = 1;
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('pagination');
        $this->load->model('city');
    }

    // 首页
    public function index()
    {   
        $this->load->view('default/index');
    }

    // 测试
    public function test() 
    {
        $page = $this->input->get('p') ? intval($this->input->get('p')) : self::$page;
        $pageinfo = $this->city->getCityPageNum(self::$limit);
        $this->load->view('default/test', array('p'=>$page, 'pagenum'=>$pageinfo['pn'], 'count'=>$pageinfo['count']));
    }

    // 测试ajax
    public function testajax()
    {
        $page = $this->input->post('p') ? intval($this->input->post('p')) : self::$page;
        $start = ($page - self::$page) * self::$limit;
        $cityinfo = $this->city->getCityList($start, self::$limit);
        $pageinfo = $this->city->getCityPageNum(self::$limit);
        $cityinfo['pagenum'] = $pageinfo['pn'];      
        $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$cityinfo];
        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
        exit;
    }

    // 列表
    public function hlist()
    {   
        $page = $this->input->get('p') ? intval($this->input->get('p')) : self::$page;
        $pageinfo = $this->city->getCityPageNum(self::$limit);        
        $this->load->view('default/list', array('p'=>$page, 'pagenum'=>$pageinfo['pn'], 'count'=>$pageinfo['count']));
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

    // 添加
    public function add()
    {
        $this->load->view('default/add');
    }

    protected function page($url='', $total, $per_page, $uri_segment, $page)
    {
        $config['base_url'] = base_url($url);
        $config['total_rows'] = $total;
        $config['per_page'] = $per_page;
        $config['uri_segment'] = $uri_segment;
        $config['use_page_numbers'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['first_link'] = '首页';
        $config['last_link'] = '末页';
        $config['query_string_segment'] = 'p';
        $config['cur_tag_open'] = '<a class="current" data-ci-pagination-page="'.$page.'">';
        $config['cur_tag_close'] = '</a>';
        return $this->pagination->initialize($config);
    }

}
