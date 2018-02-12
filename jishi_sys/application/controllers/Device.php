<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Device extends CI_Controller {

    // 分页页码
    static $page = 1;

    // 分页大小
    static $limit = 10;

    protected $title = '计时终端设备 - 计时系统';

    protected $theme = 'static/themes/default/';

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['form', 'url']);
        $this->load->library('form_validation');
        $this->load->model('mdevice');
    }

    // 首页列表
    public function index()
    {
        $data['breadcrumb'] = ['首页', '计时终端', '终端信息'];
        $data['title'] = $this->title;
        $data['theme'] = $this->theme;
        $data['js_list'] = ['device/index'];
        $data['list'] = $this->mdevice->list(1, 10);

        $this->load->view('templates/head', $data);
        $this->load->view('templates/top', $data);
        $this->load->view('templates/menu', $data);
        $this->load->view('device/index', $data);
        $this->load->view('templates/foot', $data);
    }

    /**
     * 添加
     */
    public function add()
    {
        log_message('info', '访问device/add');

        if ($this->input->method(TRUE) === 'POST')
        {
            if (empty($_POST))
            {
                $_POST = json_decode(file_get_contents('php://input'), TRUE);
                $fields = $this->mdevice->getFields();
                $_POST = array_filter($_POST, function ($k) use ($fields) { return in_array($k, $fields); }, ARRAY_FILTER_USE_KEY);
            }
            $this->form_validation->set_rules('termtype', '', 'required', ['required' => '计时终端类型未选择']);
            $this->form_validation->set_rules('vendor', '', 'required', ['required' => '生产厂家未填写']);
            $this->form_validation->set_rules('model', '', 'required', ['required' => '终端型号未填写']);
            $this->form_validation->set_rules('imei', '', 'required', ['required' => '终端IMEI或MAC地址未填写']);
            $this->form_validation->set_rules('sn', '', 'required', ['required' => '终端出厂序列号未填写']);
            if ($this->input->is_ajax_request())
            {
                log_message('info', '访问device/add 通过ajax');
                if ($this->form_validation->run() === FALSE)
                {
                    $errors = $this->form_validation->error_array();
                    if (isset($errors[array_keys($errors)[0]]))
                    {
                        $data = ['code' => 400, 'msg' => $errors[array_keys($errors)[0]], 'data' => new \stdClass()];
                    }
                    else
                    {
                        $data = ['code' => 400, 'msg' => '表单未通过安全检测', 'data' => new \stdClass()];
                    }
                }
                else
                {
                    $result = $this->mdevice->add($_POST);
                    if ($result)
                    {
                        $data = ['code' => 200, 'msg' => 'OK', 'data' => ['id' => $result]];
                    }
                    else
                    {
                        $data = ['code' => 400, 'msg' => 'Fail', 'data' => new \stdClass()];
                    }
                }
                $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
                exit();
            }
            elseif ($this->input->is_cli_request())
            {
                log_message('info', '访问device/add 通过cli');
                // 未实现
            }
            else
            {
                log_message('info', '访问device/add 通过form表单');
            }
        }
        else
        {
            log_message('info', '访问device/add 渲染表单');

            $data['title'] = $this->title;
            $data['theme'] = $this->theme;
            $data['js_list'] = ['device/add'];

            $this->load->view('templates/head', $data);
            $this->load->view('templates/top', $data);
            // $this->load->view('templates/menu', $data);
            $this->load->view('device/add', $data);
            $this->load->view('templates/foot', $data);
        }
    }

    /**
     * 编辑
     */
    public function edit() {
        log_message('info', '访问device/edit');

        if ($this->input->method(TRUE) === 'POST')
        {
            if (empty($_POST))
            {
                $_POST = json_decode(file_get_contents('php://input'), TRUE);
                $fields = $this->mdevice->getFields();
                $_POST = array_filter($_POST, function ($k) use ($fields) { return in_array($k, $fields); }, ARRAY_FILTER_USE_KEY);
            }
            $this->form_validation->set_rules('termtype', '', 'required', ['required' => '计时终端类型未选择']);
            $this->form_validation->set_rules('vendor', '', 'required', ['required' => '生产厂家未填写']);
            $this->form_validation->set_rules('model', '', 'required', ['required' => '终端型号未填写']);
            $this->form_validation->set_rules('imei', '', 'required', ['required' => '终端IMEI或MAC地址未填写']);
            $this->form_validation->set_rules('sn', '', 'required', ['required' => '终端出厂序列号未填写']);
            if ($this->input->is_ajax_request())
            {
                log_message('info', '访问device/edit 通过ajax');
                if ($this->form_validation->run() === FALSE)
                {
                    $errors = $this->form_validation->error_array();
                    if (isset($errors[array_keys($errors)[0]]))
                    {
                        $data = ['code' => 400, 'msg' => $errors[array_keys($errors)[0]], 'data' => new \stdClass()];
                    }
                    else
                    {
                        $data = ['code' => 400, 'msg' => '表单未通过安全检测', 'data' => new \stdClass()];
                    }
                }
                else
                {
                    $id = (int) $this->input->post($this->mdevice->getPrimaryKey(), TRUE);
                    if ($id <= 0)
                    {
                        log_message('error', 'id 非法');
                        $data = ['code' => 400, 'msg' => 'Fail', 'data' => new \stdClass()];
                    }
                    else
                    {
                        $result = $this->mdevice->update($_POST, $id);
                        if ($result)
                        {
                            $data = ['code' => 200, 'msg' => 'OK', 'data' => new \stdClass];
                        }
                        else
                        {
                            $data = ['code' => 400, 'msg' => 'Fail', 'data' => new \stdClass()];
                        }
                    }
                }
                $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
                exit();
            }
            elseif ($this->input->is_cli_request())
            {
                log_message('info', '访问device/edit 通过cli');
                // 未实现
            }
            else
            {
                log_message('info', '访问device/edit 通过form表单');
            }
        }
        else
        {
            log_message('info', '访问device/edit 渲染表单');

            $data['title'] = $this->title;
            $data['theme'] = $this->theme;
            $data['js_list'] = ['device/edit'];

            $this->load->view('templates/head', $data);
            $this->load->view('templates/top', $data);
            // $this->load->view('templates/menu', $data);
            $this->load->view('device/edit', $data);
            $this->load->view('templates/foot', $data);
        }

    } /* function edit ends */

    /**
     * 获取列表
     *
     * @param $p int 页码
     * @param $s int 分页大小
     */
    public function list()
    {
        log_message('info', '访问 device/list');

        if ($this->input->method(TRUE) === 'GET')
        {
            $page = (int) $this->input->get('p', TRUE);
            if ($page <= 1)
            {
                $page = self::$page;
            }

            $limit = (int) $this->input->get('s', TRUE);
            if ($limit <= 1)
            {
                $limit = self::$limit;
            }
            $offset = $limit * ($page - 1);

            $list = $this->mdevice->list($limit, $offset);
            $total = $this->mdevice->total();

            $data = [
                'code' => 200,
                'msg'  => 'OK',
                'data' => [
                    'list' => $list,
                    'total' => $total,
                ],
            ];

            if ($this->input->is_ajax_request())
            {
                $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
                exit;
            }
            else
            {
                log_message('error', '不允许的请求 device/list 非ajax');
            }
        }
        else
        {
            log_message('error', '不允许的请求 device/list 非GET');
        }
    }

    /**
     * 删除一条记录
     *
     * @param $id int 记录id
     */
    public function delete() {
        log_message('info', '访问 device/delete');

        if ($this->input->method(TRUE) === 'POST')
        {
            if (empty($_POST))
            {
                $_POST = json_decode(file_get_contents('php://input'), TRUE);
                $fields = $this->mdevice->getFields();
                $_POST = array_filter($_POST, function ($k) use ($fields) { return in_array($k, $fields); }, ARRAY_FILTER_USE_KEY);
            }
            $this->form_validation->set_rules($this->mdevice->getPrimaryKey(), '', 'required', ['required' => '必须指定操作对象']);

            if ($this->input->is_ajax_request())
            {
                log_message('info', '访问 device/delete 通过ajax');
                if ($this->form_validation->run() === TRUE)
                {
                    $id = (int) $this->input->post($this->mdevice->getPrimaryKey(), TRUE);
                    $result = $this->mdevice->delete($id);
                    if ($result)
                    {
                        $data = ['code' => 200, 'msg' => '删除成功', 'data' => new \stdClass];
                    }
                    else
                    {
                        $data = ['code' => 400, 'msg' => '删除失败', 'data' => new \stdClass];
                    }
                }
                else
                {
                    $errors = $this->form_validation->error_array();
                    if (isset($errors[array_keys($errors)[0]]))
                    {
                        $data = ['code' => 400, 'msg' => $errors[array_keys($errors)[0]], 'data' => new \stdClass];
                    }
                    else
                    {
                        $data = ['code' => 400, 'msg' => '参数错误，删除失败', 'data' => new \stdClass];
                    }
                }
                $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
                exit;
            }
            else
            {
                log_message('error', '不允许的请求类型 only ajax allowed');
            }
        }
        else
        {
            log_message('error', '不允许的请求类型 only POST allowed');
        }
    }

    /**
     * 获取单条记录
     *
     * @param $id int
     */
    public function detail()
    {
        log_message('info', '访问device/detail');

        if ($this->input->method(TRUE) === 'GET')
        {
            $id = (int) $this->input->get('id', TRUE);

            if ($this->input->is_ajax_request())
            {
                if ($id <= 0)
                {
                    $data = ['code' => 400, 'msg' => '获取详细数据失败', 'data' => new \stdClass];
                    log_message('error', '访问device/detail 未提供id参数');
                }
                else
                {
                    $result = $this->mdevice->detail($id);
                    if ($result)
                    {
                        $data = ['code' => 200, 'msg' => '获取详情成功', 'data' => ['detail' => $result]];
                    }
                    else
                    {
                        $data = ['code' => 400, 'msg' => '获取详细数据失败', 'data' => new \stdClass];
                    }
                }
                $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
                exit;
            }
            else
            {
                log_message('error', '不允许的请求类型  非ajax');
            }
        }
        else
        {
            log_message('error', '不允许的请求类型 only GET allowed');
        }
    }

}
?>
