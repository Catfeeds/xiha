<?php 
namespace Admin\Controller;
use Think\Controller;
use Think\Page;
use Think\Log;
use Think\Crypt;

class SystemController extends BaseController {
    //构造函数，判断是否是登录状态
    public function _initialize() {
        parent::_initialize();
        if (!session('loginauth')) {
            $this->redirect('Public/login');
            exit();
        }
    }

// 6.短信管理
    /**
     * 展示短信列表
     *
     * @return  void
     * @author  wl
     * @date    Mar 09, 2017
     **/
    public function smsAdmin () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $smslist = D('System')->getSmsLists();
        $this->assign('count', $smslist['count']);
        $this->assign('page', $smslist['page']);
        $this->assign('smslist', $smslist['smslist']);
        $this->display('System/smsAdmin');
    }

    /**
     * 短信搜索
     *
     * @return  void
     * @author  wl
     * @date    Mar 09, 2017
     **/
    public function searchSmsList () {
        $param = I('param.');
        $is_read = trim((int)$param['is_read']);
        $msg_type = trim((int)$param['msg_type']);
        $user_type = trim((int)$param['user_type']);
        $search_info = trim((string)$param['search_info']);
        $s_keyword = trim((string)$param['s_keyword']);
        if ($is_read == '' && $msg_type == '' && $user_type == '' && $search_info == '' && $s_keyword == '') {
            $this->redirect('System/smsAdmin');
        } else {
            $smslist = D('System')->searchSmsLists($param);
            $this->assign('is_read', $is_read);
            $this->assign('msg_type', $msg_type);
            $this->assign('user_type', $user_type);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('search_info', $search_info);
            $this->assign('count', $smslist['count']);
            $this->assign('page', $smslist['page']);
            $this->assign('smslist', $smslist['smslist']);
            $this->display('System/smsAdmin');
        }
    }

    /**
     * 添加推送消息
     *
     * @return  void
     * @author  wl
     * @date    Mar 09, 2017
     **/
    public function addSms () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        if (IS_POST) {
            $post = I('post.');
            $member_type    = $post['member_type'];
            $user_phone     = $post['user_phone'];
            $beizhu         = $post['beizhu'];
            $from           = $post['s_from'];
            $content        = $post['content'];
            try {
                if ($member_type == 1) { // student
                    // 推送学员
                    $user_id = D('System')->getStudentInfoByPhone($user_phone);
                    if ($user_id == '') {
                        $this->error('该手机号与客户类型不符', U('System/addSms'));
                    }
                    $push_info = new \StdClass;
                    $push_info->product = 'student';
                    $push_info->target = $user_id;
                    $push_info->content = $content;
                    $push_info->type = 1;
                    $push_info->member_id = $user_id;
                    $push_info->member_type = 1;
                    $push_info->beizhu = $beizhu;
                    $push_info->from = $from;
                    $result = $this->redis->rpush('msg_send_queue', json_encode($push_info, JSON_UNESCAPED_UNICODE));
                } elseif ($member_type == 2) { // coach
                    // 推送教练
                    $user_id = D('System')->getCoachInfoByPhone($user_phone);
                    if ($user_id == '') {
                        $this->error('该手机号与客户类型不符', U('System/addSms'));
                    }
                    $push_info->product = 'coach';
                    $push_info->target = $user_phone;
                    $push_info->content = $content;
                    $push_info->type = 1;
                    $push_info->member_id = $user_id;
                    $push_info->member_type = 2;
                    $push_info->beizhu = $beizhu;
                    $push_info->from = $from;
                    $result = $this->redis->rpush('msg_send_queue', json_encode($push_info, JSON_UNESCAPED_UNICODE));
                }
                if ($result) {
                    action_log('add_sms', 'sms_sender', $result, $this->getLoginUserId());
                    $this->success('发送成功', U('System/smsAdmin'));
                } else {
                    action_log('add_sms', 'sms_sender', $result, $this->getLoginUserId());
                    $this->error('发送失败', U('System/smsAdmin'));
                }
            } catch (\Exception $e) {
                Log::write('写入异常');
                Log::write($e->getMessage());
            }
        } else {
            $this->display('System/addSms');
        }
    }


    /**
     * 删除对应的短信
     *
     * @return  void
     * @author  wl
     * @date    Mar 09, 2017
     **/
    public function delSms () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        if (IS_AJAX) {
            $param = I('param.');
            $id = $param['id'];
            $result = D('System')->delSms($id);
            if ($result) {
                action_log('del_sms', 'sms_sender', $id, $this->getLoginUserId());
                $data = array('code' =>200, 'msg' => '删除成功', 'data' => $result);
            } else {
                action_log('del_sms_error', 'sms_sender', $id, $this->getLoginUserId());
                $data = array('code' =>400, 'msg' => '删除失败', 'data' => '');
            }
        } else {
            action_log('del_sms_error', 'sms_sender', $id, $this->getLoginUserId());
            $data = array('code' =>400, 'msg' => '删除失败', 'data' => '');
        }
        $this->ajaxReturn($data, 'JSON');
    }

    //检测手机号是否已注册
    public function checkPhone() {
        if (IS_AJAX) {
            $phone = I('post.user_phone');
            // User表中检测手机号码是否已经注册
            $register = D('User')->isPhoneRegistered($phone);
            if ($register) { // 返回true说明该号码已注册
                $data = array('code'=>200, 'msg'=>'√，手机号可以使用','data'=>'');  
            } else {
                $data = array('code'=>400, 'msg'=>'亲，请先注册','data'=>'');
            }
        } else {
            $data = array('code'=>400, 'msg'=>'亲，请先注册','data'=>'');
        }
        $this->ajaxReturn($data, 'JSON');
    }

// 1.系统行为模块
    /**
     * 系统行为的列表展示
     *
     * @return  void
     * @author  wl
     * @date    Nov 15, 2016
     **/
    public function systemAction () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $systemactionlist = D('System')->getSystemAction();
        $this->assign('count',$systemactionlist['count']);
        $this->assign('page',$systemactionlist['page']);    
        $this->assign('systemActionList',$systemactionlist['systemActionList']);
        $this->display('System/systemAction');
    }

    /**
     * 系统行为的搜索
     *
     * @return  void
     * @author  wl
     * @date    Nov 15, 2016
     **/
    public function searchSystemAction () {
        $param = I('param.');
        $s_keyword = trim((string)$param['s_keyword']);
        $action_info = trim((string)$param['action_info']);
        if ($s_keyword == '' && $action_info == '') {
            $this->redirect('System/systemAction');
        } else {
            $systemactionlist = D('System')->searchSystemAction($param);
            $this->assign('s_keyword',$s_keyword);
            $this->assign('action_info',$action_info);
            $this->assign('count',$systemactionlist['count']);
            $this->assign('page',$systemactionlist['page']);    
            $this->assign('systemActionList',$systemactionlist['systemActionList']);
            $this->display('System/systemAction');
        }
    }

    /**
     * 添加系统行为
     *
     * @return  void
     * @author  wl
     * @date    Nov 15, 2016
     **/
    public function addSystemAction () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('System/systemAction'));
        }
        if (IS_POST) {
            $post = I('post.');
            $data['name'] = $post['name'] ? $post['name'] : '';
            $data['title'] = $post['title'] ? $post['title'] : '';
            $data['status'] = $post['status'] ? $post['status'] : 1;
            $data['rule'] = $post['rule'] ? $post['rule'] : '';
            $data['remark'] = $post['remark'] ? $post['remark'] : '';
            $data['log'] = $post['log'] ? $post['log'] : '';
            $data['type'] = 1;
            $data['add_time'] = time();
            if ($data['name'] == '' && $data['title'] == '' && $data['remark'] =='' && $data['log'] =='') {
                $this->redirect('System/systemAction');
            }
            $check_action = D('System')->checkActionInfo($data['name']);
            if (!empty($check_action)) {
                $this->error('改行为已经存在', U('System/systemAction'));
            }
            $action = D('action');
            if ($res = $action->create($data)) {
                $result = $action->add($res);
                if ($result) {
                    action_log('add_system_action', 'action', $result, $this->getLoginUserId());
                    $this->success('添加成功', U('System/systemAction'));
                } else {
                    action_log('add_system_action', 'action', $result, $this->getLoginUserId());
                    $this->error('添加失败', U('System/addSystemAction'));
                }
            } else {
                action_log('add_system_action', 'action', $result, $this->getLoginUserId());
                $this->error('添加失败', U('System/addSystemAction'));
            }   
        } else {
            $this->display('System/addSystemAction');
        }
    }

    /**
    * 编辑系统行为
    *
    * @return  void
    * @author  wl
    * @date    Nov 15, 2016
    **/
    public function editSystemAction () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('System/systemAction'));
        }
        $id = I('param.id');
        $systemActionList = D('System')->getActionInfoById($id);
        if (IS_POST) {
            $post = I('post.');
            $data['id'] = $post['id'];
            if ($systemActionList['name'] != '') {
                $data['name'] = $post['name'] == '' ? $systemActionList['name'] : $post['name'];
            } else {
                $data['name'] = $post['name'] == '' ? $post['name'] : '';
            }
            if ($systemActionList['title'] != '') {
                $data['title'] = $post['title'] == '' ? $systemActionList['title'] : $post['title'];
            } else {
                $data['title'] = $post['title'] == '' ? $post['title'] : '';
            }
            if ($systemActionList['rule'] != '') {
                $data['rule'] = $post['rule'] == '' ? $systemActionList['rule'] : $post['rule'];
            } else {
                $data['rule'] = $post['rule'] == '' ? $post['rule'] : '';
            }
            if ($systemActionList['remark'] != '') {
                $data['remark'] = $post['remark'] == '' ? $systemActionList['remark'] : $post['remark'];
            } else {
                $data['remark'] = $post['remark'] == '' ? $post['remark'] : '';
            }
            if ($systemActionList['log'] != '') {
                $data['log'] = $post['log'] == '' ? $systemActionList['log'] : $post['log'];
            } else {
                $data['log'] = $post['log'] == '' ? $systemActionList['log'] : $post['log'];
            }
            $data['status'] = $post['status'] == '' ? $systemActionList['status'] : $post['status'];
            $data['type'] = 1;
            $data['update_time'] = time();
            if ($data['name'] == '' && $data['title'] == '' && $data['remark'] =='' && $data['log'] =='') {
                $this->redirect('System/systemAction');
            }
            $action = D('action');
            if ($res = $action->create($data)) {
                $result = $action->where(array('id' => $id))->save($res);
                if ($result) {
                    action_log('edit_system_action', 'action', $result, $this->getLoginUserId(), '修改系统行为');
                    $this->success('修改成功', U('System/systemAction'));
                } else {
                    action_log('edit_system_action', 'action', $result, $this->getLoginUserId(), '修改系统行为失败');
                    $this->error('修改失败', U('System/editSystemAction'));
                }
            } else {
                action_log('edit_system_action', 'action', $result, $this->getLoginUserId());
                $this->error('修改失败', U('System/editSystemAction'));
            }   
        } else {
            $this->assign('systemactionlist', $systemActionList);
            $this->display('System/editSystemAction');
        }
    }

    /**
     * 设置系统行为的状态
     *
     * @return  void
     * @author  wl
     * @date    Nov 15, 2016
     **/
    public function setSystemActionStatus () {
        if(IS_AJAX) {
            $id = I('post.id');
            $status = I('post.status');
            $res = D('System')->setSystemActionStatus($id,$status);
            if($res['status']) {
                $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$id);
            } else {
                $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
            }
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');    
        }
        action_log('set_action_status', 'action', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 删除单条系统行为
     *
     * @return  void
     * @author  wl
     * @date    Nov 15, 2016
     **/
    public function delSystemAction () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('System/systemAction'));
        }
        if (IS_AJAX) {
            $id = I('post.id');
            $result = D('System')->delSystemAction($id);
            if ($result) {
                $data = array('code' => 200, 'msg' => '删除成功' , 'data' => $result);
            } else {
                $data = array('code' => 103, 'msg' => '删除失败', 'data' => '');
            }
        } else {
            $data = array('code' => 103, 'msg' => '删除失败', 'data' => '');
        }
        action_log('del_system_action', 'action', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 删除多条系统行为
     *
     * @return  void
     * @author  wl
     * @date    Nov 15, 2016
     **/
    public function delSystemActions () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('System/systemAction'));
        }
        if (IS_AJAX) {
            $id = I('post.check_id');
            $id_arr = explode(',', $id);
            $result = D('System')->delSystemActions($id_arr);
            if ($result) {
                $data = array('code' => 200, 'msg' => '删除成功' , 'data' => $result);
            } else {
                $data = array('code' => 103, 'msg' => '删除失败', 'data' => '');
            }
        } else {
            $data = array('code' => 103, 'msg' => '删除失败', 'data' => '');
        }
        action_log('del_system_actions', 'action', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 恢复多条系统行为
     *
     * @return  void
     * @author  wl
     * @date    Nov 15, 2016
     **/
    public function recoverSystemActions () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('System/systemAction'));
        }
        if (IS_AJAX) {
            $id = I('post.check_id');
            $id_arr = explode(',', $id);
            $result = D('System')->recoverSystemActions($id_arr);
            if ($result) {
                $data = array('code' => 200, 'msg' => '删除成功' , 'data' => $result);
            } else {
                $data = array('code' => 103, 'msg' => '删除失败', 'data' => '');
            }
        } else {
            $data = array('code' => 103, 'msg' => '删除失败', 'data' => '');
        }
        action_log('recover_system_actions', 'action', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

// 2.行为日志模块
    /**
     * 展示日志行为表
     *
     * @return  void
     * @author  wl
     * @date    Sep 13, 2016
     * @update  Nov 25, 2016
     **/
    public function index() { 
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $school_id = $this->getLoginauth();
        $rolelist = D('System')->getRoleList($role_id, $school_id);
        $action_infos = D('System')->getActionLogList($role_id, $school_id);
        $this->assign('rolelist',$rolelist);
        $this->assign('count',$action_infos['count']);
        $this->assign('page',$action_infos['page']);    
        $this->assign('action_infos',$action_infos['action_info']);
        $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
    }

    /**
     * 根据条件搜索日志记录
     *
     * @return  void
     * @author  wl
     * @date    Sep 13, 2016
     * @update  Nov 25, 2016
     **/
    public function searchActionLog () {
        $roleid = $this->getRoleId();
        $school_id = $this->getLoginauth();
        $rolelist = D('System')->getRoleList($roleid, $school_id);
        $param = I('param.');
        $role_id = trim((int)$param['role_id']);
        $s_keyword = trim((string)$param['s_keyword']);
        $search_info = trim((string)$param['search_info']);
        if ($s_keyword == '' && $role_id == '' && $search_info == '' ) {
            $this->redirect('System/index');
        } else {
            $action_infos = D('System')->searchActionLogList($param, $roleid, $school_id);
            $this->assign('role_id',$role_id);
            $this->assign('s_keyword',$s_keyword);
            $this->assign('search_info',$search_info);
            $this->assign('rolelist',$rolelist);
            $this->assign('count',$action_infos['count']);
            $this->assign('page',$action_infos['page']);    
            $this->assign('action_infos',$action_infos['action_info']);
            $this->display('System/index');
        }
    }

    /**
     * 设置日志行为的状态
     *
     * @return  void
     * @author  wl
     * @date    Nov 15, 2016
     **/
    public function setActionLogStatus () {
        if (IS_AJAX) {
            $id = I('post.id');
            $status = I('post.status');
            $res = D('System')->setActionLogStatus($id,$status);
            if($res['status']) {
                $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$id);
            } else {
                $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
            }
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');    
        }
        action_log('set_actionlog_status', 'action_log', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 删除单条日志记录
     *
     * @return  void
     * @author  wl
     * @date    Sep 13, 2016
     **/
    public function delActionLog () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('System/index'));
        }
        if (IS_AJAX) {
            $id = I('post.id');
            $result = D('System')->delActionLog($id);
            if ($result) {
                $data = array('code' => 200, 'msg' => '删除成功' , 'data' => $result);
            } else {
                $data = array('code' => 103, 'msg' => '删除失败', 'data' => '');
            }
        } else {
            $data = array('code' => 103, 'msg' => '删除失败', 'data' => '');
        }
        action_log('del_actionlog', 'action_log', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 删除多条日志记录
     *
     * @return  void
     * @author  wl
     * @date    Sep 13, 2016
     **/
    public function delActionLogs () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('System/index'));
        }
        if (IS_AJAX) {
            $id = I('post.check_id');
            $id_arr = explode(',', $id);
            $result = D('System')->delActionLogs($id_arr);
            if ($result) {
                $data = array('code' => 200, 'msg' => '删除成功' , 'data' => $result);
            } else {
                $data = array('code' => 103, 'msg' => '删除失败', 'data' => '');
            }
        } else {
            $data = array('code' => 103, 'msg' => '删除失败', 'data' => '');
        }
        action_log('del_actionlogs', 'action_log', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

// 3.系统标签模块
    /**
     * 系统标签列表的展示
     *
     * @return  void
     * @author  wl
     * @date    Oct 24, 2016
     **/
    public function tagConfigAdmin () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $tagconfiglist = D('System')->getTagConfigList();
        $this->assign('page', $tagconfiglist['page']);
        $this->assign('count', $tagconfiglist['count']);
        $this->assign('tagconfiglist', $tagconfiglist['tagconfiglist']);
        $this->display('System/tagConfigAdmin');
    }

    /**
     * 搜索标签
     *
     * @return  void
     * @author  wl
     * @date    Oct 24, 2016
     **/
    public function searchTagConfig () {
        $param = I('param.');
        $s_keyword = trim((string)$param['s_keyword']);
        $search_info = trim((string)$param['search_info']);
        $user_type = (int)$param['user_type'];
        if ($s_keyword == '' && $search_info == '' && $user_type == '') {
            $this->redirect('System/tagConfigAdmin');
        } else {
            $tagconfiglist = D('System')->searchTagConfig($param);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('user_type', $user_type);
            $this->assign('search_info', $search_info);
            $this->assign('page', $tagconfiglist['page']);
            $this->assign('count', $tagconfiglist['count']);
            $this->assign('tagconfiglist', $tagconfiglist['tagconfiglist']);
            $this->display('System/tagConfigAdmin');
        }
    }

    /**
     * 添加系统标签
     *
     * @return  void
     * @author  wl
     * @date    Oct 24, 2016
     **/
    public function addTagConfig () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('System/tagConfigAdmin'));
        }
        if (IS_POST) {
            $post = I('post.');
            $data['tag_name'] = $post['tag_name'] ? $post['tag_name'] : '';
            $data['tag_slug'] = $post['tag_slug'] ? $post['tag_slug'] : '';
            $data['order'] = $post['order'] ? $post['order'] : 0;
            $data['user_type'] = $post['user_type'] ? $post['user_type'] : 1;
            $data['addtime'] = time();
            $systemTagConfig = D('system_tag_config');
            if ($res = $systemTagConfig->create($data)) {
                $result = $systemTagConfig->fetchSql(false)->add($res);
                if ($result) {
                    action_log('add_tag_config', 'system_tag_config', $result, $this->getLoginUserId());
                    $this->success('添加成功', U('System/tagConfigAdmin'));
                } else {
                    action_log('add_tag_config', 'system_tag_config', $result, $this->getLoginUserId());
                    $this->error('添加失败', U('System/addTagConfig'));
                }
            } else {
                action_log('add_tag_config', 'system_tag_config', $result, $this->getLoginUserId());
                $this->error('添加失败', U('System/addTagConfig'));
            }

        } else {
            $this->display('System/addTagConfig');
        }
    }

    /**
     * 编辑系统标签
     *
     * @return  void
     * @author  wl
     * @date    Oct 24, 2016
     **/
    public function editTagConfig () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('System/tagConfigAdmin'));
        }
        $id = I('param.id');
        $tagconfiglist = D('System')->getTagConfigById($id);
        if (IS_POST) {
            $post = I('post.');
            $data['tag_name']   = $post['tag_name'] == '' ? $tagconfiglist['tag_name'] : $post['tag_name'];
            $data['tag_slug']   = $post['tag_slug'] == '' ? $tagconfiglist['tag_slug'] : $post['tag_slug'];
            $data['order']      = $post['order'] == '' ? $tagconfiglist['order'] : $post['order'];
            $data['user_type']  = $post['user_type'] == '' ? $tagconfiglist['user_type'] : $post['user_type'];
            $data['updatetime'] = time();
            $systemTagConfig = D('system_tag_config');
            if ($res = $systemTagConfig->create($data)) {
                $result = $systemTagConfig->where(array('id' => $id))->fetchSql(false)->save($res);
                if ($result) {
                    action_log('edit_tag_config', 'system_tag_config', $id, $this->getLoginUserId());
                    $this->success('编辑成功', U('System/tagConfigAdmin'));
                } else {
                    action_log('edit_tag_config', 'system_tag_config', $id, $this->getLoginUserId());
                    $this->error('编辑失败', U('System/editTagConfig'));
                }
            } else {
                action_log('edit_tag_config', 'system_tag_config', $id, $this->getLoginUserId());
                $this->error('编辑失败', U('System/editTagConfig'));
            }
        } else {
            $this->assign('tagconfiglist', $tagconfiglist);
            $this->display('System/editTagConfig');
        }
    }


    /**
     * 删除系统标签
     *
     * @return  void
     * @author  wl
     * @date    Oct 24, 2016
     **/
    public function delTagConfig () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('System/tagConfigAdmin'));
        }
        if (IS_AJAX) {
            $id = I('post.id');
            $res = D('System')->delTagConfig($id);
            if ($res) {
                $data = array('code' => 200,'msg'=> '删除成功', 'data' => $id);
            } else {
                $data = array('code' => 400, 'msg' => '删除失败', 'data' => '');
            }
        }
        action_log('del_tag_config', 'system_tag_config', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 设置系统标签排序状态
     *
     * @return  void
     * @author  wl
     * @date    Oct 24, 2016
     **/
    public function setTagConfigOrder () {
        if (!IS_AJAX || !IS_POST) {
            $msg = '参数错误';
            $data = '';
            $this->ajaxReturn(array('code' => 101, 'msg' => $msg, 'data' => $data), 'JSON');
        }

        $post = I('post.');
        $update_ok = D('System')->updateTagConfigOrder($post);
        if ($update_ok == 101 || $update_ok == 102 || $update_ok == 105) {
            $code = $update_ok;
            $msg = '参数错误';
            $data = '';

        } elseif ($update_ok == 200) {
            $code = $update_ok;
            $msg = '更新成功';
            $data = '';

        } elseif ($update_ok == 400) {
            $code = $update_ok;
            $msg = '更新失败';
            $data = '';

        }
        action_log('set_tag_order', 'system_tag_config', $post['id'], $this->getLoginUserId());
        $this->ajaxReturn(array('code' => $code, 'msg' => $msg, 'data' => $data), 'JSON');
    }

// 4.用户标签列表部分
    /**
     * 用户自定义标签列表的展示
     *
     * @return  void
     * @author  wl
     * @date    Oct 24, 2016
     **/
    public function userTagAdmin () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $usertaglist = D('System')->getUserTagList();
        $this->assign('page', $usertaglist['page']);
        $this->assign('count', $usertaglist['count']);
        $this->assign('usertaglist', $usertaglist['usertaglist']);
        $this->display('System/userTagAdmin');
    }

    /**
     * 搜索标签
     *
     * @return  void
     * @author  wl
     * @date    Oct 24, 2016
     **/
    public function searchUserTag () {
        $param = I('param.');
        $s_keyword = trim((string)$param['s_keyword']);
        $search_info = trim((string)$param['search_info']);
        $user_type = (int)$param['user_type'];
        $is_system = (int)$param['is_system'];
        if ($s_keyword == '' && $search_info == '' && $user_type == '' && $is_system == '') {
            $this->redirect('System/userTagAdmin');
        } else {
            $usertaglist = D('System')->searchUserTag($param);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('user_type', $user_type);
            $this->assign('is_system', $is_system);
            $this->assign('search_info', $search_info);
            $this->assign('page', $usertaglist['page']);
            $this->assign('count', $usertaglist['count']);
            $this->assign('usertaglist', $usertaglist['usertaglist']);
            $this->display('System/userTagAdmin');
        }
    }

    /**
     * 删除自定义标签
     *
     * @return  void
     * @author  wl
     * @date    Oct 24, 2016
     **/
    public function delUserTag () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('System/userTagAdmin'));
        }
        if (IS_AJAX) {
            $id = I('post.id');
            $res = D('System')->delUserTag($id);
            if ($res) {
                $data = array('code' => 200,'msg'=> '删除成功', 'data' => $id);
            } else {
                $data = array('code' => 400, 'msg' => '删除失败', 'data' => '');
            }
        }
        action_log('del_user_tag', 'user_tag', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 设置自定义标签排序状态
     *
     * @return  void
     * @author  wl
     * @date    Oct 24, 2016
     **/
    public function setUserTagOrder () {
        if (!IS_AJAX || !IS_POST) {
            $msg = '参数错误';
            $data = '';
            $this->ajaxReturn(array('code' => 101, 'msg' => $msg, 'data' => $data), 'JSON');
        }

        $post = I('post.');
        $update_ok = D('System')->updateUserTagOrder($post);
        if ($update_ok == 101 || $update_ok == 102 || $update_ok == 105) {
            $code = $update_ok;
            $msg = '参数错误';
            $data = '';

        } elseif ($update_ok == 200) {
            $code = $update_ok;
            $msg = '更新成功';
            $data = '';

        } elseif ($update_ok == 400) {
            $code = $update_ok;
            $msg = '更新失败';
            $data = '';

        }
        action_log('set_usertag_order', 'user_tag', $post['id'], $this->getLoginUserId());
        $this->ajaxReturn(array('code' => $code, 'msg' => $msg, 'data' => $data), 'JSON');
    }


// 5.用户账户配置管理

    /**
     * 用户账户配置管理的列表展示
     *
     * @return  void
     * @author  wl
     * @date    Oct 25, 2016
     **/
    public function payAccountAdmin () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $payaccountlist = D('System')->getPayAccountList();
        $this->assign('page', $payaccountlist['page']);
        $this->assign('count', $payaccountlist['count']);
        $this->assign('payaccountlist', $payaccountlist['payaccountlist']);
        $this->display('System/payAccountAdmin');
    }

    /**
     * 搜索账户配置信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 25, 2016
     **/
    public function searchPayAccount () {
        $param = I('param.');
        $s_keyword = trim((string)$param['s_keyword']);
        $search_info = trim((string)$param['search_info']);
        $is_open = (int)$param['is_open'];
        $is_bank = (int)$param['is_bank'];
        if ($s_keyword == '' && $search_info == '' && $is_open == '' && $is_bank == '') {
            $this->redirect('System/payAccountAdmin');
        } else {
            $payaccountlist = D('System')->searchPayAccount($param);
            $this->assign('is_open', $is_open);
            $this->assign('is_bank', $is_bank);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('search_info', $search_info);
            $this->assign('page', $payaccountlist['page']);
            $this->assign('count', $payaccountlist['count']);
            $this->assign('payaccountlist', $payaccountlist['payaccountlist']);
            $this->display('System/payAccountAdmin');
        }
    }

    /**
     * 添加用户账户配置
     *
     * @return  void
     * @author  wl
     * @date    Oct 25, 2016
     **/
    public function addPayAccount () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('System/payAccountAdmin'));
        }
        if (IS_POST) {
            $post = I('post.');
            $data['account_name'] = $post['account_name'] ? $post['account_name'] : '';
            $data['account_slug'] = $post['account_slug'] ? $post['account_slug'] : '';
            $data['is_open'] = $post['is_open'] ? $post['is_open'] : 1;
            $data['is_bank'] = $post['is_bank'] ? $post['is_bank'] : 1;
            $data['order'] = $post['order'] ? $post['order'] : 0;
            $data['account_description'] = $post['account_description'] ? $post['account_description'] : '';
            $data['addtime'] = time();
            if ($data['account_name'] == '' && $data['account_slug'] == '' && $data['account_description'] == '') {
                $this->error('请完善信息', U('System/payAccountAdmin'));
            }
            $validateinfo = D('System')->getPayAccountByName($data['account_name']);
            if ($validateinfo) {
                $this->error('此名称已经存在', U('System/addPayAccount'));
            }

            $pay_account_config = D('pay_account_config');
            if ($res = $pay_account_config->create($data)) {
                $result = $pay_account_config->fetchSql(false)->add($res);
                if ($result) {
                    action_log('add_pay_account', 'pay_account_config', $result, $this->getLoginUserId());
                    $this->success('添加成功', U('System/payAccountAdmin'));
                } else {
                    action_log('add_pay_account', 'pay_account_config', $result, $this->getLoginUserId());
                    $this->error('添加失败', U('System/addPayAccount'));
                }
            } else {
                action_log('add_pay_account', 'pay_account_config', $result, $this->getLoginUserId());
                $this->error('添加失败', U('System/addPayAccount'));
            }
        } else {
            $this->display('System/addPayAccount');
        }
    }

    /**
     * 编辑用户账户配置
     *
     * @return  void
     * @author  wl
     * @date    Oct 25, 2016
     **/
    public function editPayAccount () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('System/payAccountAdmin'));
        }
        $id = I('param.id');
        $payaccountlist = D('System')->getPayAccountById($id);
        if (IS_POST) {
            $post = I('post.');
            $data['account_name'] = $post['account_name'] == '' ? $payaccountlist['account_name'] : $post['account_name'];
            $data['account_slug'] = $post['account_slug'] == '' ? $payaccountlist['account_slug'] : $post['account_slug'];
            $data['is_open'] = $post['is_open'] == $payaccountlist['is_open'] ? $payaccountlist['is_open'] : $post['is_open'];
            $data['is_bank'] = $post['is_bank'] == $payaccountlist['is_bank'] ? $payaccountlist['is_bank'] : $post['is_bank'];
            $data['order'] = $post['order'] == '' ? $payaccountlist['order'] : $post['order'];
            $data['account_description'] = $post['account_description'] == '' ? $payaccountlist['account_description'] : $post['account_description'];
            $data['addtime'] = time();
            if ($data['account_name'] == '' && $data['account_slug'] == '' && $data['account_description'] == '') {
                $this->error('请完善信息', U('System/payAccountAdmin'));
            }

            $pay_account_config = D('pay_account_config');
            if ($res = $pay_account_config->create($data)) {
                $result = $pay_account_config->fetchSql(false)->where(array('id' => $id))->save($res);
                if ($result) {
                    action_log('edit_pay_account', 'pay_account_config', $id, $this->getLoginUserId());
                    $this->success('编辑成功', U('System/payAccountAdmin'));
                } else {
                    action_log('edit_pay_account', 'pay_account_config', $id, $this->getLoginUserId());
                    $this->error('编辑失败', U('System/editPayAccount'));
                }
            } else {
                action_log('edit_pay_account', 'pay_account_config', $id, $this->getLoginUserId());
                $this->error('编辑失败', U('System/editPayAccount'));
            }
        } else {
            $this->assign('payaccountlist', $payaccountlist);
            $this->display('System/editPayAccount');
        }
    }

    /**
     * 删除账户配置信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 25, 2016
     **/
    public function delPayAccount () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('System/payAccountAdmin'));
        }
        if (IS_AJAX) {
            $id = I('post.id');
            $res = D('System')->delPayAccount($id);
            if ($res) {
                $data = array('code' => 200,'msg'=> '删除成功', 'data' => $id);
            } else {
                $data = array('code' => 400, 'msg' => '删除失败', 'data' => '');
            }
        }
        action_log('del_pay_account', 'pay_account_config', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 设置是否开启的状态
     *
     * @return  void
     * @author  wl
     * @date    Oct 25, 2016
     **/
    public function setOpenStatus () {
        $id = I('post.id');
        $status = I('post.status');
        $list = D('System')->setOpenStatus($id, $status);
        if($list['res']) {
            $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$list['id']);
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
        }
        action_log('set_payaccount_status', 'pay_account_config', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');

    }

    /**
     * 设置用户账户配置排序状态
     *
     * @return  void
     * @author  wl
     * @date    Oct 25, 2016
     **/
    public function setPayAccountOrder () {
        if (!IS_AJAX || !IS_POST) {
            $msg = '参数错误';
            $data = '';
            $this->ajaxReturn(array('code' => 101, 'msg' => $msg, 'data' => $data), 'JSON');
        }

        $post = I('post.');
        $update_ok = D('System')->updatePayAccountOrder($post);
        if ($update_ok == 101 || $update_ok == 102 || $update_ok == 105) {
            $code = $update_ok;
            $msg = '参数错误';
            $data = '';

        } elseif ($update_ok == 200) {
            $code = $update_ok;
            $msg = '更新成功';
            $data = '';

        } elseif ($update_ok == 400) {
            $code = $update_ok;
            $msg = '更新失败';
            $data = '';

        }
        action_log('set_payaccount_order', 'pay_account_config', $id, $this->getLoginUserId());
        $this->ajaxReturn(array('code' => $code, 'msg' => $msg, 'data' => $data), 'JSON');
    }   


    /**
    * 获取驾校时间配置
    *
    * @return  void
    * @author  wl
    * @date    Sep 18, 2016
    **/
    public function schoolConfigAdmin () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $school_id = $this->getLoginauth();
        $school_sysconfig = D('schoolConfig')->getSchoolSysconfig($school_id);
        $this->assign('school_id', $school_id);
        $this->assign('page', $school_sysconfig['page']);
        $this->assign('count', $school_sysconfig['count']);
        $this->assign('school_sysconfig', $school_sysconfig['school_conf']);
        $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
    }
    
    /**
     * 根据条件搜索驾校配置信息
     *
     * @return  void
     * @author  wl
     * @date    Sep 18, 2016
     **/
    public function searchSchoolConfig () {
        $school_id = $this->getLoginauth();
        $param = I('param.');
        $s_keyword = trim((string)$param['s_keyword']);
        // $is_automatic = (int)$param['is_automatic'];
        if ($s_keyword == '') {
            $this->redirect('System/schoolConfigAdmin');
        } else {
            $school_sysconfig = D('SchoolConfig')->searchSchoolConfig($param, $school_id);
            $this->assign('school_id', $school_id);
            $this->assign('s_keyword', $s_keyword);
            // $this->assign('is_automatic', $is_automatic);
            $this->assign('count', $school_sysconfig['count']);
            $this->assign('page', $school_sysconfig['page']);
            $this->assign('school_sysconfig', $school_sysconfig['school_conf']);
            $this->display('System/schoolConfigAdmin');
        }
    }

    /**
     * 添加驾校的配置信息
     *
     * @return  void
     * @author  wl
     * @date    Sep 18, 2016
     **/
    public function addSchoolConfig () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('System/schoolConfigAdmin'));
        }
        $school_id = $this->getLoginauth();
        $school_list = D('Manager')->getSchoolList();
        $coach_config = D('CoachTimeConfig')->getCoachConfig();
        $school_config_ids = D('SchoolConfig')->getSchoolConfigId();
        if (IS_POST) {
            $post = I('post.');
            if ($school_id == 0) {
                $data['l_school_id'] = $post['school_id'];
            } else {
                $data['l_school_id'] = $school_id;
            }
            $data['s_time_list'] = isset($post['time_config_id']) ? implode(',', $post['time_config_id']) : '';
            // $data['free_time'] = $post['free_time'] != '' ? $post['free_time'] : 0;
            $data['cancel_in_advance'] = $post['cancel_in_advance'] != '' ? $post['cancel_in_advance'] : 2;
            $data['i_cancel_order_time'] = $post['i_cancel_order_time'] != '' ? $post['i_cancel_order_time'] : 2;
            $data['i_sum_appoint_time'] = $post['i_sum_appoint_time'] != '' ? $post['i_sum_appoint_time'] : 2;
            $data['is_automatic'] = $post['is_automatic'] != '' ? $post['is_automatic'] : 1;
            if ($data['l_school_id'] == '') {
                $this->error('请选择驾校', U('System/addschoolConfig'));
            }
            if (isset($school_config_ids[$data['l_school_id']])) {
                $this->error('您已经添加过了', U('System/schoolConfigAdmin'));
            }
            $school_config = D('school_config') ;
            if ($res = $school_config->create($data)) {
                $result = $school_config->add($res);
                if ($result) {
                    $this->success('添加成功', U('System/schoolConfigAdmin'));
                } else {
                    $this->error('添加失败', U('System/addSchoolConfig'));
                }
            } else {
                $this->error('添加失败', U('System/addSchoolConfig'));
            }
        } else {
            $this->assign('school_id', $school_id);
            $this->assign('coach_config', $coach_config);
            $this->assign('school_list', $school_list);
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
        }
    }

    /**
     * 删除单条驾校配置信息
     *
     * @return  void
     * @author  wl
     * @date    Sep 18, 2016
     **/
    public function delSchoolConfig () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('System/schoolConfigAdmin'));
        }
        if (IS_AJAX) {
            $id = I('post.id');
            $result = D('SchoolConfig')->delSchoolConfig($id);
            if ($result) {
                $data = array('code' => 200, 'msg' => '删除成功', 'data' => $id);
            } else {
                $data = array('code' => 103, 'msg' => '删除失败', 'data' => '');
            }
        } else {
            $data = array('code' => 103, 'msg' => '删除失败', 'data' => '');
        }
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 编辑驾校的配置信息
     *
     * @return  void
     * @author  wl
     * @date    Sep 18, 2016
     **/
    public function editSchoolConfig () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('System/schoolConfigAdmin'));
        }
        $school_id = $this->getLoginauth();
        $param = I('param.');
        $id = $param['id'];
        $school_sysconfig = D('schoolConfig')->getSchoolSysconfigById($id);
        $school_config_time = $school_sysconfig['school_conf'];
        $coach_config = $school_sysconfig['coach_conf'];
        if (IS_POST) {
            $post = I('post.'); 
            $school_sysconfig = D('schoolConfig')->getSchoolSysconfigById($post['school_id']);
            $school_config_arr = $school_sysconfig['school_conf'];
            $data['l_school_id'] = $post['school_id'] != '' ? $post['school_id'] : '';
            $data['s_time_list'] = isset($post['time_config_id']) ? implode(',', $post['time_config_id']) : $school_config_arr['s_time_list'];
            // $data['free_time'] = $post['free_time'] != '' ? $post['free_time'] : $school_config_arr['free_time'];
            $data['cancel_in_advance'] = $post['cancel_in_advance'] != '' ? $post['cancel_in_advance'] : $school_config_arr['cancel_in_advance'];
            $data['i_cancel_order_time'] = $post['i_cancel_order_time'] != '' ? $post['i_cancel_order_time'] : $school_config_arr['i_cancel_order_time'];
            $data['i_sum_appoint_time'] = $post['i_sum_appoint_time'] != '' ? $post['i_sum_appoint_time'] : $school_config_arr['i_sum_appoint_time'];
            $data['is_automatic'] = $post['is_automatic'] != '' ? $post['is_automatic'] : $school_config_arr['is_automatic'];
            $school_config = D('school_config');
            if ($res = $school_config -> create($data)) {
                $result = $school_config->where(array('l_school_id' => $post['school_id']))
                    ->fetchSql(false)
                    ->save();
                if ($result) {
                    $this->success('编辑成功', U('System/schoolConfigAdmin'));
                } else {
                    $this->error('您还未做任何修改', U('System/editSchoolConfig'));
                }
            } else {
                $this->error('您还未做任何修改', U('System/editSchoolConfig'));
            }
        } else {
            $this->assign('school_id', $school_id);
            $this->assign('coach_config', $coach_config);
            $this->assign('school_config', $school_config_time);
            $this->assign('school_sysconfig', $school_sysconfig);
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
        }
    }
    /**
     * 设置驾校配置信息的自动和手动
     *
     * @return  void
     * @author  wl
     * @date    Sep 18, 2016
     **/
    public function setSchoolConfStatus () {
        if(IS_AJAX) {
            $id = I('post.id');
            $status = I('post.status');
            $res = D('SchoolConfig')->setSchoolConfStatus($id,$status);
            if($res['is_automatic']) {
                $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$id);
            } else {
                $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
            }
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');    
        }
        $this->ajaxReturn($data, 'JSON');
    }


    /**
     * 教练系统设置
     *
     * @return  void
     * @author  wl
     * @date    Sep 18, 2016
     **/
    public function coachConfigAdmin () {
        $school_id = $this->getLoginauth();
        $coach_time_config = D('CoachTimeConfig')->getCoachConfigList();
        $this->assign('page', $coach_time_config['page']);
        $this->assign('count', $coach_time_config['count']);
        $this->assign('coach_config', $coach_time_config['coach_config']);
        $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
    }
    /**
     * 设置教练配置信息是否发布的状态设置
     *
     * @return  void
     * @author  wl
     * @date    Sep 19, 2016
     **/
    public function setPublishStatus () {
        if(IS_AJAX) {
            $id = I('post.id');
            $status = I('post.status');
            $res = D('CoachTimeConfig')->setPublishStatus($id,$status);
            if($res['is_publish']) {
                $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$id);
            } else {
                $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
            }
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');    
        }
        $this->ajaxReturn($data, 'JSON');
    }
    /**
     * 删除教练的配置信息
     *
     * @return  void
     * @author  wl
     * @date    Sep 19, 2016
     **/
    public function delCoachConfig () {
        if (IS_AJAX) {
            $id = I('post.id');
            $result = D('CoachTimeConfig')->delCoachConfig($id);
            if ($result) {
                $data = array('code' => 200, 'msg' => '删除成功', 'data' => $id);
            } else {
                $data = array('code' => 103, 'msg' => '删除失败', 'data' => '');
            }
        } else {
            $data = array('code' => 103, 'msg' => '删除失败', 'data' => '');
        }
        $this->ajaxReturn($data, 'JSON');
    }
    /**
     * 设置教练配置信息是否在线的状态设置
     *
     * @return  void
     * @author  wl
     * @date    Sep 19, 2016
     **/
    public function setOnlineStatus () {
        if(IS_AJAX) {
            $id = I('post.id');
            $status = I('post.status');
            $res = D('CoachTimeConfig')->setOnlineStatus($id,$status);
            if($res['is_online']) {
                $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$id);
            } else {
                $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
            }
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');    
        }
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 给教练添加系统配置信息
     *
     * @return  void
     * @author  wl
     * @date    Sep 19, 2016
     **/
    public function addCoachConfig () {
        $school_id = $this->getLoginauth();
        if (IS_POST) {
            $post = I('post.');
            $data['start_hour']   = $post['start_hour'] ? $post['start_hour'] : '';
            $data['start_minute'] = $post['start_minute'] ? '00' : $post['start_minute'];
            $data['end_hour']     = $post['end_hour'] ? $post['end_hour'] : '';
            $data['end_minute']   = $post['end_minute'] == 0 ? '00' : $post['end_minute'];
            $data['start_time']   = $post['start_hour'].':'.$post['start_minute'];
            $data['end_time']     = $post['end_hour'].':'.$post['end_minute'];
            $data['price']        = number_format($post['price'], 2) ? number_format($post['price'], 2) : '0.00';
            $data['max_user_num'] = $post['max_user_num'] ? $post['max_user_num'] : 1;
            $data['is_online']    = $post['is_online'] ? $post['is_online'] : 1;
            $data['is_publish']   = $post['is_publish'] ? $post['is_publish'] : '';
            $data['lesson_time']  = $post['lesson_time'] ? $post['lesson_time'] : '';
            $data['lesson_id']    = $post['lesson_id'] ? $post['lesson_id'] : 2;
            if ($data['lesson_id'] == 2) {
                $data['lesson_name'] = '科目二';
            } else if ($data['lesson_id'] == 3) {
                $data['lesson_name'] = '科目三';
            }
            $data['addtime']    = time();
            $data['year']       = date('Y', time());
            $data['month']      = date('m', time());
            $data['day']        = date('d', time());
            $data['timestamp']  = strtotime($data['year'].'-'.$data['month'].'-'.$data['day']);
            $coach_time_config = D('coach_time_config_new');
            if ($res = $coach_time_config->create($data)) {
                $result = $coach_time_config->add($res);
                if ($result) {
                    $this->success('添加成功',U('System/coachConfigAdmin'));
                } else {
                    $this->error('添加失败',U('System/addCoachConfig'));
                } 
            } else {
                $this->error('添加失败',U('System/addCoachConfig'));
            }

        } else {
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
        }
    }

    /**
     * 修改教练系统配置信息
     *
     * @return  void
     * @author  wl
     * @date    Sep 19, 2016
     **/
    public function editCoachConfig () {
        $id = I('param.id');
        $coach_config = D('CoachTimeConfig')->getCoachConfigById($id);
        if (IS_POST) {
            $post = I('post.');
            $data['id']           = $post['id'] == '' ? $coach_config['id'] : $post['id'];
            $data['start_hour']   = $post['start_hour'] == '' ? $coach_config['start_hour'] : $post['start_hour'];
            $data['start_minute'] = $post['start_minute'] == 0 ? '00' : $post['start_minute'];
            $data['end_hour']     = $post['end_hour'] == '' ? $coach_config['end_hour'] : $post['end_hour'];
            $data['end_minute']   = $post['end_minute'] == 0 ? '00' : $post['end_minute'] ;
            $data['start_time']   = $post['start_hour'].':'.$post['start_minute'];
            $data['end_time']     = $post['end_hour'].':'.$post['end_minute'];
            $data['price']        = number_format($post['price'], 2) ? number_format($post['price'], 2) : '0.00';
            $data['max_user_num'] = $post['max_user_num'] ? $post['max_user_num'] : 1;
            $data['is_online']    = $post['is_online'] ? $post['is_online'] : 1;
            $data['is_publish']   = $post['is_publish'] ? $post['is_publish'] : 1;
            $data['lesson_time']  = $post['lesson_time'] ? $post['lesson_time'] : '';
            $data['lesson_id']    = $post['lesson_id'] ? $post['lesson_id'] : 2;
            if ($data['lesson_id'] == 2) {
                $data['lesson_name'] = '科目二';
            } else if ($data['lesson_id'] == 3) {
                $data['lesson_name'] = '科目三';
            }
            $coach_time_config = D('coach_time_config_new');
            if ($res = $coach_time_config->create($data)) {
                $result = $coach_time_config->where(array('id' => $id))
                    ->save();
                if ($result) {
                    $this->success('修改成功', U('System/coachConfigAdmin'));
                } else {
                    $this->error('修改失败', U('System/editCoachConfig'));
                }
            } else {
                $this->error('修改失败', U('System/editCoachConfig'));
            }

        } else {
            $this->assign('coach_config', $coach_config);
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
        }
    }



}    
?>
