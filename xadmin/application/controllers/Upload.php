<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Upload extends CI_Controller {

    /**
     * 文件上传
     *
     * 使用方法：
     * POST http://example.com/upload/handle?type={type}&{type}=_FILE
     *
     */

    /**
     * 允许上传的字段类型
     *
     * @var array
     */
    protected $_permitted_type = [
        'stuimg',
        'stufp',
        'coachimg',
        'examinerimg',
        'examinerfp',
        'securityguardimg',
        'securityguradfp',
        'vehimg',
        'outletsimg',
        'occupationimg',
        'voiceprintimg',
        'epdfimg',
        'onlineimg',
        'classroomimg',
        'simulation',
        'video',
        'schoolthumb',
        'schoollicence',
        'userthumb',
        'xihaApp',
        'adsOrder',
        'cars',
        'sitePointOne',
        'sitePointTwo',
        'siteResoure',
        'siteimgurl',
        'schooltrainone',
        'schooltraintwo',
        'schooltrainthree',
        'schooltrainfour',
        'schooltrainfive',
        'schoolshifts',
        'coachCarsOne',
        'coachCarsTwo',
        'coachCarsThree',
        'coachCarsThree',
        'video/download',
        'schoolBannerOne',
        'schoolBannerTwo',
        'schoolBannerThree',
        'schoolBannerFour',
        'schoolBannerFive',
    ];

    public function __construct()
    {
        parent::__construct();

        // 加载upload配置
        $this->load->config('upload');

        $config['upload_path']          = $this->config->item('upload_path');
        $config['allowed_types']        = '*';
        $config['max_size']             = 102400; // 100MB
        $config['max_width']            = 3840;
        $config['max_height']           = 2160;
        $config['min_width']            = 100;
        $config['min_height']           = 100;
        $config['encrypt_name']         = TRUE;

        // 加载upload类
        $this->load->helper('url');
        $this->load->library('upload', $config);
        $this->load->model('mbase');
        // 加载数据库类
        $this->load->database();
    }

    public function index()
    {
        $this->load->view('welcome_message');
    }

    /**
     * 处理上传
     */
    public function handle()
    {
        // 必须指定上传的文件类型 type
        if ( ! empty($type = $this->input->get_post('type', TRUE)))
        {
            if ( ! in_array($type, $this->_permitted_type))
            {
                log_message('error', 'Type not allowed');
                $this->output->set_content_type('application/json')
                             ->set_output(json_encode( ['code' => 400, 'msg' => 'Type not allowed', 'data' => new \stdClass] ))
                             ->_display();
                exit();
            }
        }
        else
        {
            log_message('error', 'Type needs to be set');
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['code' => 400, 'msg' => 'Type needs to be set', 'data' => new \stdClass]))
                         ->_display();
            exit();
        }

        if (FALSE !== $this->config->item('enable_sub_dir') && '' !== $this->config->item('sub_dir_format'))
        {
            $_sub_dir = sprintf($this->config->item('sub_dir_format'), $type, date('Ymd'));
            $_upload_path = $this->config->item('upload_path').$_sub_dir;

            if ($this->_mkdir($_upload_path))
            {
                $this->upload->set_upload_path($_upload_path);
            }
            else
            {
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode(['code' => 400, 'msg' => 'sub dir create error', 'data' => new \stdClass]))
                    ->_display();
                exit();
            }
        }
        if ( ! $this->upload->do_upload('file'))
        {
            $error = array('error' => $this->upload->display_errors('', '')); // just msg , no 'html tag', default is '<p>'
            log_message('error', json_encode($error));

            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['code' => 400, 'msg' => 'upload error', 'data' => $error]))
                         ->_display();
            exit();
        }
        else
        {
            // 获取upload_path后面的相对路径，入库
            $_relative_path = substr($this->upload->data('full_path'), strlen($this->config->item('upload_path')));
            $file_id = $this->_save_path($type, $_relative_path);
            if ((int)$file_id <= 0)
            {
                log_message('error', 'path save to database error');
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode(['code' => 400, 'msg' => 'save error', 'data' => new \stdClass]))
                    ->_display();
                exit();
            }

            $url = $this->mbase->buildUrl('upload/'.$_relative_path);
            // base_url('upload/'.$_relative_path)
            $data = [
                'code' => 200, 
                'msg' => 'upload ok', 
                'data' => [
                    'file_id' => $file_id, 
                    'type'=>$type, 
                    'url'=>'upload/'.$_relative_path, 
                    'file_url' => $url
                ]
            ];

            $this->output->set_content_type('application/json')
                ->set_output(json_encode($data))
                ->_display();
            exit();
        }
    }

    /**
     * 创建目录
     */
    protected function _mkdir($path = '')
    {
        if ('' === $path)
        {
            log_message('error', 'path is empty');
            return FALSE;
        }

        if (file_exists($path))
        {
            if (is_dir($path))
            {
                return TRUE;
            }
            else
            {
                log_message('error', 'The path is file not a dir:'.$path);
                return FALSE;
            }
        }
        else
        {
            if (mkdir($path, 0777, TRUE)) // 递归创建目录
            {
                if (is_really_writable($path))
                {
                    return TRUE;
                }
                else
                {
                    log_message('error', 'dir is not writable');
                    return fALSE;
                }
            }
            else
            {
                log_message('error', 'dir create error');
                return FALSE;
            }
        }
    }

    /**
     * 保存文件路径到数据库，返回 file_id
     */
    protected function _save_path($field = NULL, $path = '')
    {
        if (is_null($field) OR '' === $path OR ! in_array((string)$field, $this->_permitted_type))
        {
            return NULL;
        }

        // 写入数据库
        $_data = [
            'filetype'  => $field,
            'path'      => $path,
        ];

        $this->db->insert('cs_filedata', $_data);
        return (int)$this->db->insert_id();
    }


}
