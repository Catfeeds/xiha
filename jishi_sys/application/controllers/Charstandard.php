<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Charstandard extends CI_Controller {

    // 分页页码
    static $page = 1;

    // 分页大小
    static $limit = 10;

    protected $title = '收费标准 - 计时系统';

    protected $theme = 'static/themes/default/';

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['form', 'url']);
        $this->load->library('form_validation');
        $this->load->model('mcharstandard');
    }

    // 首页列表
    public function index()
    {
        $data['breadcrumb'] = ['首页', '驾培机构', '收费标准'];
        $data['title'] = $this->title;
        $data['theme'] = $this->theme;
        $data['js_list'] = ['charstandard/index'];
        $data['list'] = $this->mcharstandard->list(1, 10);

        $this->load->view('templates/head', $data);
        $this->load->view('templates/top', $data);
        $this->load->view('templates/menu', $data);
        $this->load->view('charstandard/index', $data);
        $this->load->view('templates/foot', $data);
    }

    /**
     * 添加
     */
    public function add()
    {
        log_message('info', '访问charstandard/add');

        if ($this->input->method(TRUE) === 'POST')
        {
            if (empty($_POST))
            {
                $_POST = json_decode(file_get_contents('php://input'), TRUE);
                $fields = $this->mcharstandard->getFields();
                $_POST = array_filter($_POST, function ($k) use ($fields) { return in_array($k, $fields); }, ARRAY_FILTER_USE_KEY);
            }
            $this->form_validation->set_rules('seq', '', 'required', ['required' => '收费编号未填写']);
            $this->form_validation->set_rules('vehicletype', '', 'required', ['required' => '培训类型未选择，可以多选']);
            $this->form_validation->set_rules('price', '', 'required', ['required' => '价格未填写，计时模式为单价，一次性收费为总额']);
            $this->form_validation->set_rules('classcurr', '', 'required', ['required' => '班型名称未填写']);
            $this->form_validation->set_rules('uptime', '', 'required', ['required' => '更新时间未设置']);
            if ($this->input->is_ajax_request())
            {
                log_message('info', '访问charstandard/add 通过ajax');
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
                    // 日期格式调整
                    // uptime
                    $this->load->helper('date');
                    if ($bad_date = $this->input->post('uptime'))
                    {
                        $_POST['uptime'] = nice_date($bad_date, 'Ymd');
                    }
                    $result = $this->mcharstandard->add($_POST);
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
                log_message('info', '访问charstandard/add 通过cli');
                // 未实现
            }
            else
            {
                log_message('info', '访问charstandard/add 通过form表单');
            }
        }
        else
        {
            log_message('info', '访问charstandard/add 渲染表单');

            $data['title'] = $this->title;
            $data['theme'] = $this->theme;
            $data['js_list'] = ['charstandard/add'];

            $this->load->view('templates/head', $data);
            $this->load->view('templates/top', $data);
            // $this->load->view('templates/menu', $data);
            $this->load->view('charstandard/add', $data);
            $this->load->view('templates/foot', $data);
        }
    }

    /**
     * 编辑
     */
    public function edit() {
        log_message('info', '访问charstandard/edit');

        if ($this->input->method(TRUE) === 'POST')
        {
            if (empty($_POST))
            {
                $_POST = json_decode(file_get_contents('php://input'), TRUE);
                $fields = $this->mcharstandard->getFields();
                $_POST = array_filter($_POST, function ($k) use ($fields) { return in_array($k, $fields); }, ARRAY_FILTER_USE_KEY);
            }
            $this->form_validation->set_rules('seq', '', 'required', ['required' => '收费编号未填写']);
            $this->form_validation->set_rules('vehicletype', '', 'required', ['required' => '培训类型未选择，可以多选']);
            $this->form_validation->set_rules('price', '', 'required', ['required' => '价格未填写，计时模式为单价，一次性收费为总额']);
            $this->form_validation->set_rules('classcurr', '', 'required', ['required' => '班型名称未填写']);
            $this->form_validation->set_rules('uptime', '', 'required', ['required' => '更新时间未设置']);
            if ($this->input->is_ajax_request())
            {
                log_message('info', '访问charstandard/edit 通过ajax');
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
                    $id = (int) $this->input->post($this->mcharstandard->getPrimaryKey(), TRUE);
                    if ($id <= 0)
                    {
                        log_message('error', 'id 非法');
                        $data = ['code' => 400, 'msg' => 'Fail', 'data' => new \stdClass()];
                    }
                    else
                    {
                        // 日期格式调整
                        // uptime
                        $this->load->helper('date');
                        if ($bad_date = $this->input->post('uptime'))
                        {
                            $_POST['uptime'] = nice_date($bad_date, 'Ymd');
                        }
                        $result = $this->mcharstandard->update($_POST, $id);
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
                log_message('info', '访问charstandard/edit 通过cli');
                // 未实现
            }
            else
            {
                log_message('info', '访问charstandard/edit 通过form表单');
            }
        }
        else
        {
            log_message('info', '访问charstandard/edit 渲染表单');

            $data['title'] = $this->title;
            $data['theme'] = $this->theme;
            $data['js_list'] = ['charstandard/edit'];

            $this->load->view('templates/head', $data);
            $this->load->view('templates/top', $data);
            // $this->load->view('templates/menu', $data);
            $this->load->view('charstandard/edit', $data);
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
        log_message('info', '访问 charstandard/list');

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

            $list = $this->mcharstandard->list($limit, $offset);
            $total = $this->mcharstandard->total();

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
                log_message('error', '不允许的请求 charstandard/list 非ajax');
            }
        }
        else
        {
            log_message('error', '不允许的请求 charstandard/list 非GET');
        }
    }

    /**
     * 删除一条记录
     *
     * @param $id int 记录id
     */
    public function delete() {
        log_message('info', '访问 charstandard/delete');

        if ($this->input->method(TRUE) === 'POST')
        {
            if (empty($_POST))
            {
                $_POST = json_decode(file_get_contents('php://input'), TRUE);
                $fields = $this->mcharstandard->getFields();
                $_POST = array_filter($_POST, function ($k) use ($fields) { return in_array($k, $fields); }, ARRAY_FILTER_USE_KEY);
            }
            $this->form_validation->set_rules($this->mcharstandard->getPrimaryKey(), '', 'required', ['required' => '必须指定操作对象']);

            if ($this->input->is_ajax_request())
            {
                log_message('info', '访问 charstandard/delete 通过ajax');
                if ($this->form_validation->run() === TRUE)
                {
                    $id = (int) $this->input->post($this->mcharstandard->getPrimaryKey(), TRUE);
                    $result = $this->mcharstandard->delete($id);
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
        log_message('info', '访问charstandard/detail');

        if ($this->input->method(TRUE) === 'GET')
        {
            $id = (int) $this->input->get('id', TRUE);

            if ($this->input->is_ajax_request())
            {
                if ($id <= 0)
                {
                    $data = ['code' => 400, 'msg' => '获取详细数据失败', 'data' => new \stdClass];
                    log_message('error', '访问charstandard/detail 未提供id参数');
                }
                else
                {
                    $result = $this->mcharstandard->detail($id);
                    if ($result)
                    {
                        // 日期格式化
                        // uptime
                        $this->load->helper('date');
                        if (isset($result['uptime']) && (string) $result['uptime'] !== '')
                        {
                            $bad_date = $result['uptime'];
                            $result['uptime'] = nice_date($bad_date, 'Y-m-d');
                        }
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
