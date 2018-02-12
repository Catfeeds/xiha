<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 1.app上线记录
/**
 * App的上线记录、用户的反馈信息
 *
 * @category app
 * @package app
 * @author wl
 * @return void
 **/

class App extends CI_Controller {

    static $limit = 10;
    static $page = 1;
    protected $_permitted_type = [
        'xihaApp',
    ];
    public function __construct() 
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('mbase');
        $this->load->model('mapp');
        // $this->config->load('upload');
        // $this->load->library('upload');
        $this->load->database();
    }

    /**
     * App上线记录
     * 
     * @return void
     **/
    public function index() 
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/app/index');
        $this->load->view(TEMPLATE.'/footer');
    }
    
    /**
     * app上线记录ajax列表
     *
     * @return void
     **/
    public function listAjax() 
    {
        $param = [];
        $param['ostype'] = $this->input->post('ostype', true) ? $this->input->post('ostype', true) : '';
        $param['apptype'] = $this->input->post('apptype', true) ? $this->input->post('apptype', true) : '';
        $param['force'] = $this->input->post('force', true) ? $this->input->post('force', true) : '';
        $param['keywords'] = trim((string)$this->input->post('keywords', true)) ? trim((string)$this->input->post('keywords', true)) : '';

        $page = $this->input->post('p') ? (int)$this->input->post('p') : self::$page;
        $limit = $this->input->post('s') ? (int)$this->input->post('s') : self::$limit;

        $page_info = $this->mapp->getAppPageNum($param, $limit);
        $pagenum = $page_info['pagenum'];
        $count = $page_info['count'];

        $page = $page < $pagenum || $pagenum == 0 ? $page : $pagenum;
        $start = ($page - self::$page) * $limit;

        $applist = $this->mapp->getAppRecordsList($param, $start, $limit);
        $apprecordslist['p'] = $page;
        $apprecordslist['pagenum'] = $pagenum;
        $apprecordslist['count'] = $count;
        $apprecordslist['list'] = $applist['list'];
        $data = [
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => $apprecordslist,
        ];

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;

    }

    /**
     * 新增app上线记录[页面展示]
     * @param form
     *
     * @return void
     **/
    public function add()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/app/add');
        $this->load->view(TEMPLATE.'/footer');
    }

    
    /**
     * 新增app上线记录[功能实现]
     * @param element
     *
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

        $params = $this->input->post();
        $_data = [
            'app_name' => $params['app_name'] != '' ? trim($params['app_name']) : '',
            'version' => $params['version'] != '' ? trim($params['version']) : 0,
            'version_code' => $params['version_code'] != '' ? intval($params['version_code']) : 0,
            'force_least_updateversion' => $params['force_least_updateversion'],
            'os_type' => $params['os_type'] != '' ? $params['os_type'] : 2,
            'app_client' => $params['app_client'] != '' ? $params['app_client'] : 2,
            'is_force' => isset($params['is_force']) ? ($params['is_force'] === 'true' ? 1 : 0) : 0,
            'app_update_log' => $params['app_update_log'] ? trim($params['app_update_log']) : '',
            'addtime' => time(),
            'app_download_url' => $params['app_download_url'] ? trim($params['app_download_url']) : ''
        ];

        $action = 'add_app';
        $intro = "添加新版本app";
        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $tblname = $this->mapp->app_version_tbl;
        $name = substr($tblname, 3, strlen($tblname));
        
        $result = $this->mapp->addAppOnlineRecords($_data);
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
     * 更新app上线记录[页面展示]
     *
     * @return void
     **/
    public function edit()
    {
        $this->mbase->loginauth();
        $role_id = $this->mbase->getRoleIdFromLoginauth($this->session->loginauth);
        $permission_check = $this->mbase->is_authorized('admin/editmenu', $role_id);
        if (true !== $permission_check) {
            $data = 'Permission Denied!';
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output($data)
                ->_display();
            exit;
        }
        $id = $this->input->get('id') ? $this->input->get('id') : 0;
        $data = $this->mapp->getAppListById($id);
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/app/edit', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 更新app上线记录[功能实现]
     * @param data
     *
     * @return void
     **/
    public function editAjax()
    {
        $this->mbase->loginauth();
        $role_id = $this->mbase->getRoleIdFromLoginauth($this->session->loginauth);
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

        $params = $this->input->post();
        $app_list = $this->mapp->getAppListById($params['id']);
        $_data = [
            'id'                => $params['id'] ? intval($params['id']) : $app_list['id'],
            'app_name'          => $params['app_name'] ? trim($params['app_name']) : $app_list['app_name'],
            'version'           => $params['version'] ? trim($params['version']) : $app_list['version'],
            'version'           => $params['version'] ? trim($params['version']) : $app_list['version'],
            'version_code'      => $params['version_code'] ? intval($params['version_code']) : $app_list['version_code'],
            'os_type'           => $params['os_type'] ? intval($params['os_type']) : $app_list['os_type'],
            'app_client'        => $params['app_client'] ? intval($params['app_client']) : $app_list['app_client'],
            'app_update_log'    => $params['app_update_log'] ? trim($params['app_update_log']) : $app_list['app_update_log'],
            'is_force'          => isset($params['is_force']) ? ($params['is_force'] === 'true' ? 1 : 0) : 0,
            'addtime'           => time(),
            'force_least_updateversion' => $params['force_least_updateversion'] ? trim($params['force_least_updateversion']) : $app_list['force_least_updateversion'],
            'app_download_url'    => $params['app_download_url'] ? trim($params['app_download_url']) : $app_list['app_download_url'],
            
        ];

        $action = 'add_app';
        $intro = "修改app上线记录的信息[ID: ".$params['id']."]";
        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $tblname = $this->mapp->app_version_tbl;
        $name = substr($tblname, 3, strlen($tblname));
        $tblname = $this->mapp->app_version_tbl;

        $result = $this->mapp->updateData($_data, $tblname);
        if ($result) {
            $this->mlog->action_log($action, $name, $params['id'], $login_id, $intro);
            $data = [
                'code'  => 200,
                'msg'   => '更新成功',
                'data'  => ['success' => $result]
            ];
        } else {
            $data = [
                'code'  => 400,
                'msg'   => '更新失败',
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
     * 删除对应行
     *
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

        $id = $this->input->post();
        $app_id = $id['id'];
        $action = 'del_app';
        $intro = "删除app上线记录[ID: ".$app_id.']';
        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $tblname = $this->mapp->app_version_tbl;
        $name = substr($tblname, 3, strlen($tblname));
        $tblname = $this->mapp->app_version_tbl;

        $result = $this->mapp->delAppRecords($id);
        if ($result) {
            $this->mlog->action_log($action, $name, $app_id, $login_id, $intro);
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
     * 设置是否强制升级的状态
     * 
     * @return void
     **/
    public function handleForceStatus() 
    {
        $this->mbase->loginauth();
        if ( ! $this->input->is_ajax_request()) 
        {
            $data = [
                'code'  => 400,
                'msg'   => '请求出错',
                'data'  => ''
            ];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        $id = $this->input->post('id') ? (int)$this->input->post('id') : 0;
        $status = $this->input->post('status') ? (int)$this->input->post('status') : 0;
        $updata_data = [
            'id' => $id,
            'is_force' => $status
        ];

        $action = 'set_app_force';
        if ($status == 1) {
            $intro = '设置app强制升级的状态为强制';
        } else {
            $intro = '设置app强制升级的状态为不强制';
        }

        $tblname = $this->mapp->app_version_tbl;
        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $name = substr($tblname, 3, strlen($tblname));
        $res = $this->mlog->action_log($action, $name, $id, $login_id, $intro);
        $updata_ok = $this->mapp->updateData($updata_data, $tblname);
        if ($updata_ok) {
            $data = [
                'code'  => 200,
                'msg'   => '设置成功',
                'data'  => ['success' => $updata_ok]
            ];
        } else {
            $data = [
                'code'  => 400,
                'msg'   => '设置失败',
                'data'  => ['default' => $updata_ok]
            ];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }

// 2、app问题反馈

    /**
     * 问题反馈列表[页面展示]
     * @param  
     *
     * @return void
     **/
    public function appfeedback ()
    {
        $this->mbase->loginauth();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/app/appfeedback');
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 问题反馈列表[数据获取]
     * @param  data
     * @return void
     **/
    public function feedbackAjax()
    {
        $param = [];
        $param['utype'] = $this->input->post('usertype', true) != '' ? $this->input->post('usertype', true): '';
        $param['solved'] = $this->input->post('solved', true) ? $this->input->post('solved', true) : '';
        $param['keywords'] = trim((string)$this->input->post('keywords', true)) ? trim((string)$this->input->post('keywords', true)) : '';

        $page = $this->input->post('p') ? (int)$this->input->post('p') : self::$page;
        $limit = $this->input->post('s') ? (int)$this->input->post('s') : self::$limit;
        
        $page_info = $this->mapp->getFeedBackNum($param, $limit);
        $pagenum = $page_info['pagenum'];
        $count = $page_info['count'];
        $page = $page < $pagenum || $pagenum == 0 ? $page : $pagenum;
        $start = ($page - self::$page) * $limit;

        $feedbacklist = $this->mapp->getFeedBackList($param, $start, $limit);
        $feedback_list['p'] = $page;
        $feedback_list['pagenum'] = $pagenum;
        $feedback_list['count'] = $count;
        $feedback_list['list'] = $feedbacklist['list'];
        $data = [
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => $feedback_list,
        ];

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;

    }

    /**
     * 处理解决的状态
     * @param   $status
     * @return void
     **/
    public function handleSolvedStatus()
    {
        $this->mbase->loginauth();
        if ( ! $this->input->is_ajax_request())
        {
            $data = [
                'code'  => 400,
                'msg'   => '请求出错',
                'data'  => ''
            ];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        $id = $this->input->post('id') ? (int)$this->input->post('id') : 0;
        $status = $this->input->post('status') ? (int)$this->input->post('status') : 0;
        $updata_data = [
            'id' => $id,
            'is_solved' => $status
        ];

        $tblname = $this->mapp->feedback_tbl;
        $action = 'set_feedback_solved';
        $name = substr($tblname, 3, strlen($tblname));
        if ($status == 1) {
            $intro = "设置app反馈解决状态为已解决";
        } else {
            $intro = "设置app反馈解决状态为未解决";
        }

        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $updata_ok = $this->mapp->updateData($updata_data, $tblname);

        if ($updata_ok) {
            $this->mlog->action_log($action, $name, $id, $login_id, $intro);
            $data = [
                'code'  => 200,
                'msg'   => '设置成功',
                'data'  => ['success' => $updata_ok]
            ];
        } else {
            $data = [
                'code'  => 400,
                'msg'   => '设置失败',
                'data'  => ['default' => $updata_ok]
            ];
        }

        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;

    }

    /**
     * 删除反馈列表
     * @param   id
     * @return
     **/
    public function delFeedBackAjax()
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

        $id = $this->input->post();
        // $update_data = [
        //     'id' => $id['id'],
        //     'is_solved' => 2
        // ];

        $tblname = $this->mapp->feedback_tbl;
        $name = substr($tblname, 3, strlen($tblname));
        $action = 'del_qppfeedback';
        $login_id = $this->mlog->getLoginUserId($this->session->loginauth);
        $intro = "删除app反馈[ID: ".$id['id']."]";
        // $result = $this->mapp->updateData($update_data, $tblname);
        $result = $this->mapp->delFeedBack($id);
        if ($result) {
            $this->mlog->action_log($action, $name, $id['id'], $login_id, $intro);
            $data = [
                'code'  => 200,
                'msg'   => '删除成功',
                'data'  => ['success' => $result]
            ];
        } else {
            $data = [
                'code'  => 400,
                'msg'   => '状态为已解决',
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
     * 上传app文件
     * @param   id  对应的ID
     * 
     * @return void
     **/
    public function appUpload() 
    {
        $params = $this->input->get('unique') ? intval($this->input->get('unique')) : null;
        $type = $this->input->get('type') ? trim($this->input->get('type')) : '';
        $tblname = $this->mapp->app_version_tbl;
        $condition = ['id' => $params];
        $handle = $this->mbase->handleAdminUpload($type, $params, $condition, 'app_download_url', $tblname);
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($handle, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit;
    }




}
