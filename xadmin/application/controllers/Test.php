<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 驾培机构
class Test extends CI_Controller {

    static $limit = 10;
    static $page = 1;
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('mcoach');
    }

    public function index()
    {
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/menu');
        $this->load->view(TEMPLATE.'/footer');
    }

    // 添加
    public function add() {
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/coach/add');
        $this->load->view(TEMPLATE.'/footer');
    }
    
    // 添加
    public function edit() {
        $id = $this->input->get('id') ? $this->input->get('id') : 0;
        $data = $this->mcoach->getCoachInfo($id);
        if(!$data) show_404();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/coach/edit', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    // 列表分页ajax
    public function listajax()
    {
        $page = $this->input->post('p') ? intval($this->input->post('p')) : self::$page;
        $pageinfo = $this->mcoach->getCoachPageNum(self::$limit);
        $page = $page < $pageinfo['pn'] || $pageinfo['pn'] == 0 ? $page : $pageinfo['pn'];        
        $start = ($page - self::$page) * self::$limit;
        $coachinfo = $this->mcoach->getCoachList($start, self::$limit);
        $coachinfo['pagenum'] = $pageinfo['pn'];
        $coachinfo['count'] = $pageinfo['count'];
        $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$coachinfo];
        // sleep(1);
        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
        exit;
    }

    // 添加ajax
    public function addajax()
    {
        $params = $this->input->post();
        $_data = [
            'name'=> isset($params['coachname']) ? $params['coachname'] : '',
            'idcard'=> isset($params['idcard']) ? $params['idcard'] : '',
            'sex'=> isset($params['sex']) ? $params['sex'] : '',
            'address'=> isset($params['address']) ? $params['address'] : '',
            'mobile'=> isset($params['mobile']) ? $params['mobile'] : '',
            'drilicence'=> isset($params['drilicence']) ? $params['drilicence'] : '',
            'fstdrilicdate'=> isset($params['fstdrilicdate']) ? $params['fstdrilicdate'] : '',
            'occupationno'=> isset($params['occupationno']) ? $params['occupationno'] : '',
            'occupationlevel'=> isset($params['occupationlevel']) ? $params['occupationlevel'] : '',
            'dripermitted'=> isset($params['dripermitted']) ? $params['dripermitted'] : '',
            'teachpermitted'=> isset($params['teachpermitted']) ? $params['teachpermitted'] : '',
            'employstatus'=> isset($params['employstatus']) ? $params['employstatus'] : '',
            'hiredate'=> isset($params['hiredate']) ? $params['hiredate'] : '',
            'photo'=> isset($params['photo']) ? $params['photo'] : '',
            'inscode'=> '',
            'coachnum'=> '',
        ];
        $result = $this->mcoach->addCoachInfo($_data);
        if($result) {
            $data = ['code'=>200, 'msg'=>'添加成功', 'data'=>['result'=>$result]];
        } else {
            $data = ['code'=>100, 'msg'=>'添加失败', 'data'=>['result'=>$result]];            
        }
        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
        exit;
    }

    // 编辑ajax
    public function editajax()
    {
        $params = $this->input->post();
        $_data = [
            'coachid'=> isset($params['coachid']) ? $params['coachid'] : 0,
            'name'=> isset($params['coachname']) ? $params['coachname'] : '',
            'idcard'=> isset($params['idcard']) ? $params['idcard'] : '',
            'sex'=> isset($params['sex']) ? $params['sex'] : '',
            'address'=> isset($params['address']) ? $params['address'] : '',
            'mobile'=> isset($params['mobile']) ? $params['mobile'] : '',
            'drilicence'=> isset($params['drilicence']) ? $params['drilicence'] : '',
            'fstdrilicdate'=> isset($params['fstdrilicdate']) ? $params['fstdrilicdate'] : '',
            'occupationno'=> isset($params['occupationno']) ? $params['occupationno'] : '',
            'occupationlevel'=> isset($params['occupationlevel']) ? $params['occupationlevel'] : '',
            'dripermitted'=> isset($params['dripermitted']) ? $params['dripermitted'] : '',
            'teachpermitted'=> isset($params['teachpermitted']) ? $params['teachpermitted'] : '',
            'employstatus'=> isset($params['employstatus']) ? $params['employstatus'] : '',
            'hiredate'=> isset($params['hiredate']) ? $params['hiredate'] : '',
            'photo'=> isset($params['photo']) ? $params['photo'] : '',
            'inscode'=> '',
            'coachnum'=> '',
        ];
        $result = $this->mcoach->editCoachInfo($_data);
        if($result) {
            $data = ['code'=>200, 'msg'=>'编辑成功', 'data'=>['result'=>$result]];
        } else {
            $data = ['code'=>100, 'msg'=>'编辑失败', 'data'=>['result'=>$result]];            
        }
        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
        exit;
    }

    // 删除ajax
    public function delajax() {
        $id = $this->input->post('id') ? intval($this->input->post('id')) : '';
        if($id == '') {
            $data = ['code'=>400, 'msg'=>'参数错误', 'data'=>[]];
            $this->output->set_status_header(200)
                        ->set_content_type('application/json')
                        ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                        ->_display();
            exit;
        }
        $params = ['coachid'=> $id];
        $res = $this->mcoach->delCoachInfo($params);
        if($res) {
            $data = ['code'=>200, 'msg'=>'删除成功', 'data'=>[]];
        } else {
            $data = ['code'=>200, 'msg'=>'删除失败', 'data'=>[]];
        }
        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->_display();
        exit;
    }

}
?>