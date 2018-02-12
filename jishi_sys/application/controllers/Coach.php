<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Coach extends CI_Controller {

    // 分页页码
    static $page = 1;

    // 分页大小
    static $limit = 10;

    protected $title = '教练员 - 计时系统';

    protected $theme = 'static/themes/default/';

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['form', 'url']);
        $this->load->library('form_validation');
        $this->load->model('mcoach');
    }

    // 首页列表
    public function index()
    {
        $data['breadcrumb'] = ['首页', '驾培机构', '教练员'];
        $data['title'] = $this->title;
        $data['theme'] = $this->theme;
        $data['js_list'] = ['coach/index'];
        $data['list'] = $this->mcoach->list(1, 10);

        $this->load->view('templates/head', $data);
        $this->load->view('templates/top', $data);
        $this->load->view('templates/menu', $data);
        $this->load->view('coach/index', $data);
        $this->load->view('templates/foot', $data);
    }

    /**
     * 添加
     */
    public function add()
    {
        log_message('info', '访问coach/add');

        if ($this->input->method(TRUE) === 'POST')
        {
            if (empty($_POST))
            {
                $_POST = json_decode(file_get_contents('php://input'), TRUE);
                $fields = $this->mcoach->getFields();
                $_POST = array_filter($_POST, function ($k) use ($fields) { return in_array($k, $fields); }, ARRAY_FILTER_USE_KEY);
            }
            $this->form_validation->set_rules('name', '', 'required', ['required' => '姓名未填写']);
            $this->form_validation->set_rules('sex', '', 'required', ['required' => '性别未选择']);
            $this->form_validation->set_rules('idcard', '', 'required|exact_length[18]', ['required' => '身份证号未填写', 'exact_length' => '身份证必须是18位']);
            $this->form_validation->set_rules('mobile', '', 'required|exact_length[11]', ['required' => '手机联系方式未填写', 'exact_length' => '手机号必须是11位']);
            $this->form_validation->set_rules('photo', '', 'required', ['required' => '头像未上传']);
            $this->form_validation->set_rules('drilicence', '', 'required', ['required' => '驾驶证号未填写']);
            $this->form_validation->set_rules('fstdrilicdate', '', 'required', ['required' => '驾驶证初领日期未填写']);
            $this->form_validation->set_rules('dripermitted', '', 'required', ['required' => '准驾车型未填写']);
            $this->form_validation->set_rules('teachpermitted', '', 'required', ['required' => '准教车型未填写']);
            $this->form_validation->set_rules('employstatus', '', 'required', ['required' => '供职状态未指定']);
            $this->form_validation->set_rules('hiredate', '', 'required', ['required' => '入职日期未填写']);
            if ($this->input->is_ajax_request())
            {
                log_message('info', '访问coach/add 通过ajax');
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
                    // extra phone check
                    $mobile_pattern = '/^1(3[0-9]|4[579]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/';
                    preg_match_all($mobile_pattern, $this->input->post('mobile', TRUE), $matches);
                    if (empty($matches))
                    {
                        $data = ['code' => 400, 'msg' => '手机号格式不正确', 'data' => new \stdClass];
                        goto ret;
                    }

                    // 日期格式调整
                    // leavedate && hiredate && fstdrilicdate
                    $this->load->helper('date');
                    if ($bad_date = $this->input->post('fstdrilicdate'))
                    {
                        $_POST['fstdrilicdate'] = nice_date($bad_date, 'Ymd');
                    }
                    if ($bad_date = $this->input->post('hiredate'))
                    {
                        $_POST['hiredate'] = nice_date($bad_date, 'Ymd');
                    }
                    if ($bad_date = $this->input->post('leavedate'))
                    {
                        $_POST['leavedate'] = nice_date($bad_date, 'Ymd');
                    }
                    $result = $this->mcoach->add($_POST);
                    if ($result)
                    {
                        $data = ['code' => 200, 'msg' => 'OK', 'data' => ['id' => $result]];
                    }
                    else
                    {
                        $data = ['code' => 400, 'msg' => 'Fail', 'data' => new \stdClass()];
                    }
                }
                ret:
                $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
                exit();
            }
            elseif ($this->input->is_cli_request())
            {
                log_message('info', '访问coach/add 通过cli');
                // 未实现
            }
            else
            {
                log_message('info', '访问coach/add 通过form表单');
            }
        }
        else
        {
            log_message('info', '访问coach/add 渲染表单');

            $data['title'] = $this->title;
            $data['theme'] = $this->theme;
            $data['js_list'] = ['coach/add'];

            $this->load->view('templates/head', $data);
            $this->load->view('templates/top', $data);
            // $this->load->view('templates/menu', $data);
            $this->load->view('coach/add', $data);
            $this->load->view('templates/foot', $data);
        }
    }

    /**
     * 编辑
     */
    public function edit() {
        log_message('info', '访问coach/edit');

        if ($this->input->method(TRUE) === 'POST')
        {
            if (empty($_POST))
            {
                $_POST = json_decode(file_get_contents('php://input'), TRUE);
                $fields = $this->mcoach->getFields();
                $_POST = array_filter($_POST, function ($k) use ($fields) { return in_array($k, $fields); }, ARRAY_FILTER_USE_KEY);
            }
            $this->form_validation->set_rules('coachid', '', 'required', ['required' => '教练id未获取到']);
            $this->form_validation->set_rules('name', '', 'required', ['required' => '姓名未填写']);
            $this->form_validation->set_rules('sex', '', 'required', ['required' => '性别未选择']);
            $this->form_validation->set_rules('idcard', '', 'required|exact_length[18]', ['required' => '身份证号未填写', 'exact_length' => '身份证必须是18位']);
            $this->form_validation->set_rules('mobile', '', 'required|exact_length[11]', ['required' => '手机联系方式未填写', 'exact_length' => '手机号必须是11位']);
            $this->form_validation->set_rules('photo', '', 'required', ['required' => '头像未上传']);
            $this->form_validation->set_rules('drilicence', '', 'required', ['required' => '驾驶证号未填写']);
            $this->form_validation->set_rules('fstdrilicdate', '', 'required', ['required' => '驾驶证初领日期未填写']);
            $this->form_validation->set_rules('dripermitted', '', 'required', ['required' => '准驾车型未填写']);
            $this->form_validation->set_rules('teachpermitted', '', 'required', ['required' => '准教车型未填写']);
            $this->form_validation->set_rules('employstatus', '', 'required', ['required' => '供职状态未指定']);
            $this->form_validation->set_rules('hiredate', '', 'required', ['required' => '入职日期未填写']);
            if ($this->input->is_ajax_request())
            {
                log_message('info', '访问coach/edit 通过ajax');
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
                    // extra phone check
                    $mobile_pattern = '/^1(3[0-9]|4[579]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/';
                    preg_match_all($mobile_pattern, $this->input->post('mobile', TRUE), $matches);
                    if (empty($matches[0]))
                    {
                        $data = ['code' => 400, 'msg' => '手机号格式不正确', 'data' => new \stdClass];
                        goto ret;
                    }

                    $id = (int) $this->input->post($this->mcoach->getPrimaryKey(), TRUE);
                    if ($id <= 0)
                    {
                        log_message('error', 'id 非法');
                        $data = ['code' => 400, 'msg' => 'Fail', 'data' => new \stdClass()];
                    }
                    else
                    {
                        // 如果在职，则将离职日期置空
                        if ($this->input->post('employstatus') == '0' && ! isset($_POST['leavedate']))
                        {
                            $_POST['leavedate'] = '';
                        }
                        // 日期格式调整
                        // leavedate && hiredate && fstdrilicdate
                        $this->load->helper('date');
                        if ($bad_date = $this->input->post('fstdrilicdate'))
                        {
                            $_POST['fstdrilicdate'] = nice_date($bad_date, 'Ymd');
                        }
                        if ($bad_date = $this->input->post('hiredate'))
                        {
                            $_POST['hiredate'] = nice_date($bad_date, 'Ymd');
                        }
                        if ($bad_date = $this->input->post('leavedate'))
                        {
                            $_POST['leavedate'] = nice_date($bad_date, 'Ymd');
                        }
                        $result = $this->mcoach->update($_POST, $id);
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
                ret:
                $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
                exit();
            }
            elseif ($this->input->is_cli_request())
            {
                log_message('info', '访问coach/edit 通过cli');
                // 未实现
            }
            else
            {
                log_message('info', '访问coach/edit 通过form表单');
            }
        }
        else
        {
            log_message('info', '访问coach/edit 渲染表单');

            $data['title'] = $this->title;
            $data['theme'] = $this->theme;
            $data['js_list'] = ['coach/edit'];

            $this->load->view('templates/head', $data);
            $this->load->view('templates/top', $data);
            // $this->load->view('templates/menu', $data);
            $this->load->view('coach/edit', $data);
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
        log_message('info', '访问 coach/list');

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

            $list = $this->mcoach->list($limit, $offset);
            $total = $this->mcoach->total();

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
                log_message('error', '不允许的请求 coach/list 非ajax');
            }
        }
        else
        {
            log_message('error', '不允许的请求 coach/list 非GET');
        }
    }

    /**
     * 删除一条记录
     *
     * @param $id int 记录id
     */
    public function delete() {
        log_message('info', '访问 coach/delete');

        if ($this->input->method(TRUE) === 'POST')
        {
            if (empty($_POST))
            {
                $_POST = json_decode(file_get_contents('php://input'), TRUE);
                $fields = $this->mcoach->getFields();
                $_POST = array_filter($_POST, function ($k) use ($fields) { return in_array($k, $fields); }, ARRAY_FILTER_USE_KEY);
            }
            $this->form_validation->set_rules($this->mcoach->getPrimaryKey(), 'coachid', 'required', ['required' => '必须指定操作对象']);

            if ($this->input->is_ajax_request())
            {
                log_message('info', '访问 coach/delete 通过ajax');
                if ($this->form_validation->run() === TRUE)
                {
                    $id = (int) $this->input->post($this->mcoach->getPrimaryKey(), TRUE);
                    $result = $this->mcoach->delete($id);
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
        log_message('info', '访问coach/detail');

        if ($this->input->method(TRUE) === 'GET')
        {
            $id = (int) $this->input->get('id', TRUE);

            if ($this->input->is_ajax_request())
            {
                if ($id <= 0)
                {
                    $data = ['code' => 400, 'msg' => '获取详细数据失败', 'data' => new \stdClass];
                    log_message('error', '访问coach/detail 未提供id参数');
                }
                else
                {
                    $result = $this->mcoach->detail($id);
                    if ($result)
                    {
                        $this->load->model('mfiledata');
                        $this->load->helper('filedata');
                        // 头像
                        if ((int) $result['photo'] > 0)
                        {
                            $file = $this->mfiledata->detail($result['photo']);
                            if ($file)
                            {
                                $coachimgurl = file_web_path($file['path'], 'upload');
                            }
                            else
                            {
                                $coachimgurl = '';
                            }
                        }
                        else
                        {
                            $coachimgurl = '';
                        }
                        // 指纹
                        if ((int) $result['fingerprint'] > 0)
                        {
                            $file = $this->mfiledata->detail($result['fingerprint']);
                            if ($file)
                            {
                                $coachfpurl = file_web_path($file['path'], 'upload');
                            }
                            else
                            {
                                $coachfpurl = '';
                            }
                        }
                        else
                        {
                            $coachfpurl = '';
                        }

                        // 日期格式化
                        // fstdrilicdate && hiredate && leavedate
                        $this->load->helper('date');
                        if (isset($result['fstdrilicdate']) && (string) $result['fstdrilicdate'] !== '')
                        {
                            $bad_date = $result['fstdrilicdate'];
                            $result['fstdrilicdate'] = nice_date($bad_date, 'Y-m-d');
                        }
                        if (isset($result['hiredate']) && (string) $result['hiredate'] !== '')
                        {
                            $bad_date = $result['hiredate'];
                            $result['hiredate'] = nice_date($bad_date, 'Y-m-d');
                        }
                        if (isset($result['leavedate']) && (string) $result['leavedate'] !== '')
                        {
                            $bad_date = $result['leavedate'];
                            $result['leavedate'] = nice_date($bad_date, 'Y-m-d');
                        }
                        $result['coachimgurl'] = $coachimgurl;
                        $result['coachfpurl'] = $coachfpurl;
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
