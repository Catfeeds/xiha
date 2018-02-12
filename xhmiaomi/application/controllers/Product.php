<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 喵咪鼠标部分
class Product extends CI_Controller {

    static $limit = 10;
    static $page = 1;
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('mbase');
        $this->load->model('mproduct');
        $this->load->library('session');
        $this->config->load('upload');
        $this->load->library('upload');
        $this->load->database();

    }

    /**
     * 产品管理[页面展示]
     * @param
     * @return void
     **/
    public function index()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/product/index');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 获数据
     * @param 
     * @return void
     **/
    public function listAjax()
    {
        $param = [];
        $param['keywords'] = $this->input->post('keywords', true) ? trim((string)$this->input->post('keywords', true)) : '';

        $page = $this->input->post('p') ? (int)$this->input->post('p') : self::$page;
        $limit = $this->input->post('s') ? (int)$this->input->post('s') : self::$limit;

        $page_info = $this->mproduct->getPageNum($param, $limit);
        $pagenum = $page_info['pagenum'];
        $count = $page_info['count'];
        $page = $page < $pagenum || $pagenum == 0 ? $page : $pagenum;
        $start = ($page - self::$page) * $limit;

        $list = $this->mproduct->getProuctList($param, $start, $limit);
        $recordslist['p'] = $page;
        $recordslist['pagenum'] = $pagenum;
        $recordslist['count'] = $count;
        $recordslist['list'] = $list['list'];
        $data = [
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => $recordslist,
        ];

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;

    }

    /**
     * 新增记录[页面展示]
     * @param 
     * @return void
     **/
    public function add()
    {
        $this->mbase->loginauth();
        $role_id = $this->mbase->getRoleIdFromLoginauth($this->session->loginauth);
        $permission_check = $this->mbase->is_authorized('product/add', $role_id);
        if (true !== $permission_check) {
            $data = ['code' => 400, 'msg' => 'Permission Denied!'];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/product/add');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 新增记录操作[数据的增加]
     * @param 
     * @return void
     **/
    public function addAjax()
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
        $param = [];
        $param['client_name'] = $this->input->post('client_name') ? trim($this->input->post('client_name')) : '';
        $param['version'] = $this->input->post('version') ? trim($this->input->post('version')) : '';
        $param['version_code'] = $this->input->post('version_code') ? trim($this->input->post('version_code')) : '';
        $param['download_url'] = $this->input->post('download_url') ? trim($this->input->post('download_url')) : '';
        $param['update_log'] = $this->input->post('update_log') ? $this->input->post('update_log'): '';
        $param['os_type'] = 1;
        $param['client_type'] = 1;
        $param['addtime'] = time();
        $tblname = $this->mproduct->client_version_tbl;
        $result = $this->mproduct->add($param, $tblname);
        if ($result) {
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
     * 上传喵咪鼠标url
     * @param $id
     * @return void
     **/
    public function uploadAjax()
    {
        $data = $this->input->get('unique') ? intval($this->input->get('unique')) : 0;
        $type = $this->input->get('type') ? trim($this->input->get('type')) : '';
        $tblname = $this->mproduct->client_version_tbl;
        $condition = ['id' => $data];
        $handle = $this->mbase->handleAdminUpload($type, $data, $condition, 'download_url', $tblname);
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($handle, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

    /**
     * 新增记录[页面展示]
     * @param 
     * @return void
     **/
    public function edit()
    {
        $this->mbase->loginauth();
        $role_id = $this->mbase->getRoleIdFromLoginauth($this->session->loginauth);
        $permission_check = $this->mbase->is_authorized('product/edit', $role_id);
        if (true !== $permission_check) {
            $data = ['code' => 400, 'msg' => 'Permission Denied!', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $id = $this->input->get('id', true) ? intval($this->input->get('id', true)) : '';
        $data = $this->mproduct->getProductById($id);
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/product/edit', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 修改记录操作[数据的修改]
     * @param 
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
        $param = [];
        $param['id'] = $this->input->post('id') ? trim($this->input->post('id')) : '';
        $param['client_name'] = $this->input->post('client_name') ? trim($this->input->post('client_name')) : '';
        $param['version'] = $this->input->post('version') ? trim($this->input->post('version')) : '';
        $param['version_code'] = $this->input->post('version_code') ? trim($this->input->post('version_code')) : '';
        $param['update_log'] = $this->input->post('update_log') ? $this->input->post('update_log'): '';
        $param['download_url'] = $this->input->post('download_url') ? $this->input->post('download_url'): '';
        $param['os_type'] = 1;
        $param['client_type'] = 1;
        $param['updatetime'] = time();
        $field = 'id';
        $tblname = $this->mproduct->client_version_tbl;
        $result = $this->mproduct->edit($field,  $param['id'], $param, $tblname);
        if ($result) {
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
     * 删除产品记录
     * @param $id
     * @return void
     **/
    public function delAjax()
    {
        $this->mbase->loginauth();
        $role_id = $this->mbase->getRoleIdFromLoginauth($this->session->loginauth);
        $permission_check = $this->mbase->is_authorized('product/delajax', $role_id);
        if (true !== $permission_check) {
            $data = ['code' => 400, 'msg' => 'Permission Denied!', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
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
        $id = $this->input->post('id', true) ? $this->input->post('id', true) : '';
        $id_arr = explode(',', $id);
        $tblname = $this->mproduct->client_version_tbl;
        $result = $this->mproduct->del($id_arr, $tblname);
        if ($result) {
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














}
