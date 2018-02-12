<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 评价管理模块
class Comment extends CI_Controller {

    static $limit = 10;
    static $page = 1;
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('mbase');
        $this->load->model('mcomment');
        $this->load->model('mschool');
        $this->load->model('muser');
    }

// 1.学员评价教练模块
    /**
     * 学员评价教练列表的展示
     *
     * @return  void
     */
    public function index()
    {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $data = ['school_id' => $school_id];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/comment/index', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * ajax加载学员评价教练列表
     *
     * @return  void
     */
    public function listajaxStuCommentCoach () 
    {
        $this->mbase->loginauth();
        if( ! $this->input->is_ajax_request()) {
            $data = ['code' => 100, 'msg' => '错误请求方式', 'data' => ''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }
        $param = [];
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $param['star'] = $this->input->post('star', true) ? trim($this->input->post('star', true)) : '';
        $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';
        $page = $this->input->post('p', true) ? intval($this->input->post('p', true)) : self::$page;
        $limit = $this->input->post('s', true) ? intval($this->input->post('s', true)) : self::$limit;
        $pageinfo = $this->mcomment->getStuCommentCoachPageNum($school_id, $param, $limit);
        
        $page = $page < $pageinfo['pagenum'] || $pageinfo['pagenum'] == 0 ? $page : $pageinfo['pagenum'];
        $start = ($page - 1) * $limit;
        $commentlist = $this->mcomment->getStuCommentCoachList($school_id, $param, $start, $limit);
        $list['p'] = $page;
        $list['list'] = $commentlist;
        $list['pagenum'] = $pageinfo['pagenum'];
        $list['count'] = $pageinfo['count'];
        $data = ['code' => 200, 'msg' => '获取成功', 'data' => $list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }

    /*编辑评论教练*/
    public function editStuCommentCoach () 
    {
        $this->mbase->loginauth();
        $id = $this->input->get('id') ? $this->input->get('id') : 0;
        $data = $this->mcomment->getStuCommentCoachInfo($id);
        if(!$data) show_404();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/comment/editStuCommentCoach', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /*ajax编辑学员评价教练*/
    public function editajaxStuCommentCoach () 
    {
        $this->mbase->loginauth();
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                    ->_display();
            exit;
        }
        $params = $this->input->post();
        $_data = [
            'id'=> isset($params['id']) ? $params['id'] : 0,
            'order_no'=> isset($params['order_no']) ? $params['order_no'] : '',
            'coach_star'=> isset($params['coach_star']) ? $params['coach_star'] : '',
            'coach_content'=> isset($params['coach_content']) ? $params['coach_content'] : '',
        ];
        $result = $this->mcomment->editStuCommentInfo($_data);
        if($result) {
            $data = ['code'=>200, 'msg'=>'编辑成功', 'data'=>['result'=>$result]];
        } else {
            $data = ['code'=>100, 'msg'=>'编辑失败', 'data'=>['result'=>$result]];
        }
        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                    ->_display();
        exit;
    }

    /*删除评论教练*/
    public function delStuCommentCoach () 
    {
        $this->mbase->loginauth();
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                    ->_display();
            exit;
        }
        $id = $this->input->post('id') ? intval($this->input->post('id')) : 0;
        if($id == '') {
            $data = ['code'=>400, 'msg'=>'参数错误', 'data'=>[]];
            $this->output->set_status_header(200)
                        ->set_content_type('application/json')
                        ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                        ->_display();
            exit;
        }
        $params = ['id'=> $id];
        $res = $this->mcomment->delInfo($this->mcomment->cc_tbl, $params);
        if($res) {
            $data = ['code'=>200, 'msg'=>'删除成功', 'data'=>[]];
        } else {
            $data = ['code'=>200, 'msg'=>'删除失败', 'data'=>[]];
        }
        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                    ->_display();
        exit;
    }


// 2.学员评价驾校模块
    /**
     * 学员评价驾校列表的展示
     *
     * @return  void
     */
    public function stuCommentSchool () 
    {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $data = ['school_id' => $school_id];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/comment/stuCommentSchool', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /**
     * 学员评价驾校列表分页ajax
     *
     * @return  void
     */
    public function listajaxStuCommentSchool() 
    {
        $this->mbase->loginauth();
        if( ! $this->input->is_ajax_request()) {
            $data = ['code' => 100, 'msg' => '错误请求方式', 'data' => ''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        $param = [];
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $param['star'] = $this->input->post('star', true) ? trim($this->input->post('star', true)) : '';
        $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';

        $page = $this->input->post('p', true) ? intval($this->input->post('p', true)) : self::$page;
        $limit = $this->input->post('s', true) ? intval($this->input->post('s', true)) : self::$limit;
        $pageinfo = $this->mcomment->getStuCommentSchoolPageNum($school_id, $param, $limit);

        $page = $page < $pageinfo['pagenum'] || $pageinfo['pagenum'] == 0 ? $page : $pageinfo['pagenum'];
        $start = ($page - 1) * $limit;
        $commentlist = $this->mcomment->getStuCommentSchoolList($school_id, $param, $start, $limit);

        $list['p'] = $page;
        $list['list'] = $commentlist;
        $list['pagenum'] = $pageinfo['pagenum'];
        $list['count'] = $pageinfo['count'];
        $data = ['code' => 200, 'msg' => '获取成功', 'data' => $list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }

    /**
     * 添加学员评价驾校
     *
     * @return  void
     * @author  wl
     * @date    Mar 17, 2017
     **/
    public function addStuCommentSchool () 
    {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $data = ['school_id' => $school_id];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/comment/addStuCommentSchool', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    public function addajaxStuCommentSchool () 
    {
        $this->mbase->loginauth();
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                ->_display();
            exit;
        }
        $param = [];
        $param['school_id'] = $this->input->post('school_id', true) ? intval($this->input->post('school_id', true)) : 0;
        $param['user_id'] = $this->input->post('user_id', true) ? trim($this->input->post('user_id', true)) : 0;
        $param['order_no'] = $this->input->post('order_no', true) ? trim($this->input->post('order_no', true)) : '';
        $param['type'] = 2;
        $param['school_star'] = $this->input->post('school_star', true) ? trim($this->input->post('school_star', true)) : '3';
        $param['school_content'] = $this->input->post('school_content', true) ? trim($this->input->post('school_content', true)) : '';
        $result = $this->mcomment->addStuCommentSchoolInfo($param);
        if($result) {
            $data = ['code'=>200, 'msg'=>'添加成功', 'data'=>['result'=>$result]];
        } else {
            $data = ['code'=>100, 'msg'=>'添加失败', 'data'=>['result'=>$result]];            
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }

    /*编辑学员评价驾校的相关信息*/
    public function editStuCommentSchool () 
    {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $id = $this->input->get('id') ? $this->input->get('id') : 0;
        $data = $this->mcomment->getStuCommentSchoolInfo($id);
        $data['sid'] = $school_id;
        if(!$data) show_404();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/comment/editStuCommentSchool', $data);
        $this->load->view(TEMPLATE.'/footer');

    }

    /*ajax编辑学员评价驾校*/
    public function editajaxStuCommentSchool () 
    {
        $this->mbase->loginauth();
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                ->_display();
            exit;
        }
        $param = [];
        $param['id'] = $this->input->post('id', true) ? intval($this->input->post('id', true)) : 0;
        $commentinfo = $this->mcomment->getStuCommentSchoolInfo($param['id']);
        $param['school_id'] = $this->input->post('school_id', true) ? intval($this->input->post('school_id', true)) : $commentinfo['l_school_id'];
        $param['user_id'] = $this->input->post('user_id', true) ? trim($this->input->post('user_id', true)) : $commentinfo['l_user_id'];
        $param['order_no'] = $this->input->post('order_no', true) ? trim($this->input->post('order_no', true)) : $commentinfo['order_no'];
        $param['type'] = 2;
        $param['school_star'] = $this->input->post('school_star', true) ? trim($this->input->post('school_star', true)) : '3';
        $param['school_content'] = $this->input->post('school_content', true) ? trim($this->input->post('school_content', true)) : $commentinfo['school_content'];
        $result = $this->mcomment->editStuCommentInfo($param);
        if($result) {
            $data = ['code'=>200, 'msg'=>'编辑成功', 'data'=>['result'=>$result]];
        } else {
            $data = ['code'=>100, 'msg'=>'编辑失败', 'data'=>['result'=>$result]];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }

    /*获取用户列表*/
    public function getUserList () 
    {   
        $this->mbase->loginauth();
        if( ! $this->input->is_ajax_request()) {
            $data = ['code' => 100, 'msg' => '错误请求方式', 'data' => ''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                ->_display();
            exit;
        }
        $school_id = $this->input->post('school_id', true) ? intval($this->input->post('school_id', true)) : 0;
        $userlist = $this->muser->getUserBySchoolId($school_id);
        $data = array('code' => 200, 'msg' => '获取成功', 'data' => $userlist);
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }

    /**
     * 获取用户订单号
     * @param   int  $user_id    用户ID
     * @return  void
     **/
    public function getUserOrderNo()
    {
        $this->mbase->loginauth();
        if( ! $this->input->is_ajax_request()) {
            $data = ['code' => 100, 'msg' => '错误请求方式', 'data' => ''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                ->_display();
            exit;
        }
        $user_id = $this->input->post('user_id', true) ? intval($this->input->post('user_id', true)) : 0;
        $order_list = $this->mcomment->getUserOrderNo($user_id);
        $data = array('code' => 200, 'msg' => '获取成功', 'data' => $order_list);
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }

    /*获取驾校列表*/
    public function getSchoolList () 
    {
        $items = $this->mschool->getSchoolList(0, 500);
        if($items) {
            $data = array('code' => 200, 'msg' => '获取成功', 'data' => $items);
        }else {
            $data = array('code' => 400, 'msg' => '获取失败', 'data' => array());
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }

    /**
     * 删除学员评价驾校
     *
     * @return
     * @author  wl
     * @date    Dec 01, 2017
     **/
    public function delStuCommentSchool () 
    {
        $this->mbase->loginauth();
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                ->_display();
            exit;
        }
        $id = $this->input->post('id') ? intval($this->input->post('id')) : 0;
        if($id == '') {
            $data = ['code'=>400, 'msg'=>'参数错误', 'data'=>[]];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                ->_display();
            exit;
        }
        $params = ['id'=> $id];
        $res = $this->mcomment->delInfo($this->mcomment->cc_tbl, $params);
        if($res) {
            $data = ['code'=>200, 'msg'=>'删除成功', 'data'=>[]];
        } else {
            $data = ['code'=>200, 'msg'=>'删除失败', 'data'=>[]];
        }
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }

// 3.教练评价学员
    /**
     * 教练评价学员列表展示
     *
     * @return  void
     * @author  wl
     * @date    June 09, 2017
     **/
    public function coaCommentStudent()
    {
        $this->mbase->loginauth();
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $data = ['school_id' => $school_id];
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/comment/coaCommentStudent', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    public function listajaxCoaCommentStudent () 
    {
        $this->mbase->loginauth();
        if( ! $this->input->is_ajax_request()) {
            $data = ['code' => 100, 'msg' => '错误请求方式', 'data' => ''];
            $this->output->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                ->_display();
            exit;
        }

        $param = [];
        $school_id = $this->mbase->getSchoolIdFromLoginauth($this->session->loginauth);
        $param['star'] = $this->input->post('star', true) ? trim($this->input->post('star', true)) : '';
        $param['keywords'] = $this->input->post('keywords', true) ? trim($this->input->post('keywords', true)) : '';

        $page = $this->input->post('p', true) ? intval($this->input->post('p', true)) : self::$page;
        $limit = $this->input->post('s', true) ? intval($this->input->post('s', true)) : self::$limit;

        $pageinfo = $this->mcomment->getCoaCommentStudentPageNum($school_id, $param, $limit);
        $page = $page < $pageinfo['pagenum'] || $pageinfo['pagenum'] == 0 ? $page : $pageinfo['pagenum'];
        $start = ($page - 1) * $limit;
        $commentlist = $this->mcomment->getCoaCommentStudentList($school_id, $param, $start, $limit);
        $list['p'] = $page;
        $list['list'] = $commentlist;
        $list['pagenum'] = $pageinfo['pagenum'];
        $list['count'] = $pageinfo['count'];
        $data = ['code' => 200, 'msg' => '获取成功', 'data' => $list];
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
            ->_display();
        exit;
    }

    /**
     * 删除教练评论学员
     *
     * @return  void
     * @author  wl
     * @date    June 09, 2017
     **/
    public function delCoaCommentStudent() 
    {
        $this->mbase->loginauth();
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                    ->_display();
            exit;
        }
        $id = $this->input->post('id') ? intval($this->input->post('id')) : 0;
        if($id == '') {
            $data = ['code'=>400, 'msg'=>'参数错误', 'data'=>[]];
            $this->output->set_status_header(200)
                        ->set_content_type('application/json')
                        ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                        ->_display();
            exit;
        }
        $params = ['id'=> $id];
        $res = $this->mcomment->delInfo($this->mcomment->sc_tbl, $params);
        if($res) {
            $data = ['code'=>200, 'msg'=>'删除成功', 'data'=>[]];
        } else {
            $data = ['code'=>200, 'msg'=>'删除失败', 'data'=>[]];
        }
        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                    ->_display();
        exit;
    }

    /*编辑评论学员*/
    public function editCoaCommentStudent () 
    {
        $this->mbase->loginauth();
        $id = $this->input->get('id') ? $this->input->get('id') : 0;
        $data = $this->mcomment->getCoaCommentStudentInfo($id);
        if(!$data) show_404();
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/comment/editCoaCommentStudent', $data);
        $this->load->view(TEMPLATE.'/footer');
    }

    /*ajax编辑学员评价学员*/
    public function editajaxCoaCommentStudent () 
    {
        $this->mbase->loginauth();
        if(!$this->input->is_ajax_request()) {
            $data = ['code'=>100, 'msg'=>'错误请求方式', 'data'=>''];
            $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                    ->_display();
            exit;
        }
        $params = $this->input->post();
        $_data = [
            'id'=> isset($params['id']) ? $params['id'] : 0,
            'order_no'=> isset($params['order_no']) ? $params['order_no'] : '',
            'star_num'=> isset($params['star_num']) ? $params['star_num'] : '',
            'content'=> isset($params['content']) ? $params['content'] : '',
        ];
        $result = $this->mcomment->editCoaCommentInfo($_data);
        if($result) {
            $data = ['code'=>200, 'msg'=>'编辑成功', 'data'=>['result'=>$result]];
        } else {
            $data = ['code'=>100, 'msg'=>'编辑失败', 'data'=>['result'=>$result]];
        }
        $this->output->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
                    ->_display();
        exit;
    }
}
