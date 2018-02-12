<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Trainingcar extends CI_Controller {

    // 分页页码
    static $page = 1;

    // 分页大小
    static $limit = 10;

    protected $title = '训练车 - 计时系统';

    protected $theme = 'static/themes/default/';

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['form', 'url']);
        $this->load->library('form_validation');
        $this->load->model('mtrainingcar');
    }

    // 首页列表
    public function index()
    {
        $data['breadcrumb'] = ['首页', '驾培机构', '训练车'];
        $data['title'] = $this->title;
        $data['theme'] = $this->theme;
        $data['js_list'] = ['trainingcar/index'];

        $this->load->view('templates/head', $data);
        $this->load->view('templates/top', $data);
        $this->load->view('templates/menu', $data);
        $this->load->view('trainingcar/index', $data);
        $this->load->view('templates/foot', $data);
    }

    /**
     * 添加
     */
    public function add()
    {
        log_message('info', '访问 trainingcar/add');

        if ($this->input->method(TRUE) === 'POST')
        {
            if (empty($_POST))
            {
                $_POST = json_decode(file_get_contents('php://input'), TRUE);
                $fields = $this->mtrainingcar->getFields();
                $_POST = array_filter($_POST, function ($k) use ($fields) { return in_array($k, $fields); }, ARRAY_FILTER_USE_KEY);
            }
            $this->form_validation->set_rules('licnum', '', 'required', ['required' => '车牌号未填写']);
            $this->form_validation->set_rules('platecolor', '', 'required', ['required' => '车牌颜色未选择']);
            $this->form_validation->set_rules('perdritype', '', 'required', ['required' => '培训车型未选择']);
            $this->form_validation->set_rules('manufacture', '', 'required', ['required' => '生产厂家未填写']);
            $this->form_validation->set_rules('brand', '', 'required', ['required' => '品牌未填写']);
            if ($this->input->is_ajax_request())
            {
                log_message('info', '访问 training/add 通过ajax');
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
                    // leavedate && hiredate && fstdrilicdate
                    $this->load->helper('date');
                    if ($bad_date = $this->input->post('buydate'))
                    {
                        $_POST['buydate'] = nice_date($bad_date, 'Ymd');
                    }
                    $result = $this->mtrainingcar->add($_POST);
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
                log_message('info', '访问 training/add 通过cli');
                // 未实现
            }
            else
            {
                log_message('info', '访问 training/add 通过form表单');
            }
        }
        else
        {
            log_message('info', '访问trainingcar/add 渲染表单');

            $data['title'] = $this->title;
            $data['theme'] = $this->theme;
            $data['js_list'] = ['trainingcar/add'];

            $this->load->view('templates/head', $data);
            $this->load->view('templates/top', $data);
            // $this->load->view('templates/menu', $data);
            $this->load->view('trainingcar/add', $data);
            $this->load->view('templates/foot', $data);
        }
    }

    /**
     * 编辑
     */
    public function edit() {
        log_message('info', '访问trainingcar/edit');

        if ($this->input->method(TRUE) === 'POST')
        {
            if (empty($_POST))
            {
                $_POST = json_decode(file_get_contents('php://input'), TRUE);
                $fields = $this->mtrainingcar->getFields();
                $_POST = array_filter($_POST, function ($k) use ($fields) { return in_array($k, $fields); }, ARRAY_FILTER_USE_KEY);
            }
            $this->form_validation->set_rules('carid', '', 'required', ['required' => '车辆id未获取到']);
            $this->form_validation->set_rules('licnum', '', 'required', ['required' => '车牌号未填写']);
            $this->form_validation->set_rules('platecolor', '', 'required', ['required' => '车牌颜色未选择']);
            $this->form_validation->set_rules('perdritype', '', 'required', ['required' => '培训车型未选择']);
            $this->form_validation->set_rules('manufacture', '', 'required', ['required' => '生产厂家未填写']);
            $this->form_validation->set_rules('brand', '', 'required', ['required' => '品牌未填写']);
            if ($this->input->is_ajax_request())
            {
                log_message('info', '访问trainingcar/edit 通过ajax');
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
                    $id = (int) $this->input->post($this->mtrainingcar->getPrimaryKey(), TRUE);
                    if ($id <= 0)
                    {
                        log_message('error', 'id 违法');
                        $data = ['code' => 400, 'msg' => 'Fail', 'data' => new \stdClass()];
                    }
                    else
                    {
                        // 日期格式调整
                        // buydate
                        $this->load->helper('date');
                        if ($bad_date = $this->input->post('buydate'))
                        {
                            $_POST['buydate'] = nice_date($bad_date, 'Ymd');
                        }
                        $result = $this->mtrainingcar->update($_POST, $id);
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
                log_message('info', '访问trainingcar/edit 通过cli');
                // 未实现
            }
            else
            {
                log_message('info', '访问trainingcar/edit 通过form表单');
            }
        }
        else
        {
            log_message('info', '访问trainingcar/edit 渲染表单');

            $data['title'] = $this->title;
            $data['theme'] = $this->theme;
            $data['js_list'] = ['trainingcar/edit'];

            $this->load->view('templates/head', $data);
            $this->load->view('templates/top', $data);
            // $this->load->view('templates/menu', $data);
            $this->load->view('trainingcar/edit', $data);
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
        log_message('info', '访问 trainingcar/list');

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

            $list = $this->mtrainingcar->list($limit, $offset);
            $total = $this->mtrainingcar->total();

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
                log_message('error', '不允许的请求 trainingcar/list 非ajax');
            }
        }
        else
        {
            log_message('error', '不允许的请求 trainingcar/list 非GET');
        }
    }

    /**
     * 删除一条记录
     *
     * @param $id int 记录id
     */
    public function delete() {
        log_message('info', '访问 trainingcar/delete');

        if ($this->input->method(TRUE) === 'POST')
        {
            if (empty($_POST))
            {
                $_POST = json_decode(file_get_contents('php://input'), TRUE);
                $fields = $this->mtrainingcar->getFields();
                $_POST = array_filter($_POST, function ($k) use ($fields) { return in_array($k, $fields); }, ARRAY_FILTER_USE_KEY);
            }
            $this->form_validation->set_rules($this->mtrainingcar->getPrimaryKey(), 'carid', 'required', ['required' => '必须指定操作对象']);

            if ($this->input->is_ajax_request())
            {
                log_message('info', '访问 trainingcar/delete 通过ajax');
                if ($this->form_validation->run() === TRUE)
                {
                    $id = (int) $this->input->post($this->mtrainingcar->getPrimaryKey(), TRUE);
                    $result = $this->mtrainingcar->delete($id);
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
        log_message('info', '访问trainingcar/detail');

        if ($this->input->method(TRUE) === 'GET')
        {
            $id = (int) $this->input->get('id', TRUE);

            if ($this->input->is_ajax_request())
            {
                if ($id <= 0)
                {
                    $data = ['code' => 400, 'msg' => '获取详细数据失败', 'data' => new \stdClass];
                    log_message('error', '访问trainingcar/detail 未提供id参数');
                }
                else
                {
                    $result = $this->mtrainingcar->detail($id);
                    if ($result)
                    {
                        $this->load->model('mfiledata');
                        $this->load->helper('filedata');
                        // 车辆图片
                        if ((int) $result['photo'] > 0)
                        {
                            $file = $this->mfiledata->detail($result['photo']);
                            if ($file)
                            {
                                $vehimgurl = file_web_path($file['path'], 'upload');
                            }
                            else
                            {
                                $vehimgurl = '';
                            }
                        }
                        else
                        {
                            $vehimgurl = '';
                        }

                        // 日期格式化
                        // buydate
                        $this->load->helper('date');
                        if (isset($result['buydate']) && (string) $result['buydate'] !== '')
                        {
                            $bad_date = $result['buydate'];
                            $result['buydate'] = nice_date($bad_date, 'Y-m-d');
                        }
                        $result['vehimgurl'] = $vehimgurl;
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
