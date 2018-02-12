<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;
use Think\Upload;

class AppController extends BaseController { 
    public $AppModel;
    //构造函数，判断登录状态
    public function _initialize() {
        if (!session('loginauth')) {
            $this->redirect('Public/login');
            exit();
        }
        $this->AppModel = D('App');      
    }

    /**
     * APP列表的展示
     *
     * @return  void
     * @author  wl
     * @date    August 11, 2016
     **/
    public function index () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $applists = $this->AppModel->getAppList();
        $this->assign('page', $applists['page']);
        $this->assign('count', $applists['count']);
        $this->assign('applist', $applists['applist']);
        $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
    }    

    /**
     * 根据条件搜索版本信息
     *
     * @return  void
     * @author  wl
     * @date    August 11, 2016
     **/
    public function searchAppInfo () {
        $param = I('param.');
        $s_keyword = trim((string)$param['s_keyword']);
        $os_type = trim((int)$param['os_type']);
        $app_client = trim((int)$param['app_client']);
        if ($s_keyword == '' && $os_type == '' && $app_client == '') {
            $this->redirect('App/index');
        }
        $applists = D('App')->searchAppInfo($param);
        $this->assign('os_type', $os_type);
        $this->assign('s_keyword', $s_keyword);
        $this->assign('app_client', $app_client);
        $this->assign('page', $applists['page']);
        $this->assign('count', $applists['count']);
        $this->assign('applist', $applists['applist']);
        $this->display('App/index');
    }

    /**
     * 根据传入的id删除相应的app信息
     *
     * @return  void
     * @author  wl
     * @date    August 11, 2016
     **/
    public function delApp() {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('App/index'));
        }
        if (IS_AJAX) {
            $aid = I('post.id');
            $result = D('App')->delApp($aid);
            if ($result) {
                $data = array('code' => 200, 'msg' => '删除成功', 'data' => $aid);
            } else {
                $data = array('code' => 103, 'msg' => '删除失败', 'data' => '');
            }
        } else {
            $data = array('code' => 103, 'msg' => '删除失败', 'data' => '');
        }
        action_log('del_app', 'app_version', $aid, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 设置app升级的强制状态
     *
     * @return  void
     * @author  wl
     * @date    Dec 28, 2016
     **/
    public function setForceStatus () {
        if (IS_AJAX) {
            $param = I('post.');
            $id = $param['id'];
            $status = $param['status'];
            $result = D('App')->setForceStatus($id, $status);
            if ($result['res']) {
                action_log('set_app_forcestatus', 'app_version', $id, $this->getLoginUserId());
                $data = array('code' => 200, 'msg' => '设置成功', 'data' => $result['id']);
            } else {
                action_log('set_app_forcestatus', 'app_version', $id, $this->getLoginUserId());
                $data = array('code' => 400, 'msg' => '设置失败', 'data' => '');
            } 
        } else {
            action_log('set_app_forcestatus', 'app_version', $id, $this->getLoginUserId());
            $data = array('code' => 400, 'msg' => '设置失败', 'data' => '');
        }
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 添加app信息
     *
     * @return  void
     * @author  wl
     * @date    August 11, 2016
     * @update  August 12, 2016
     **/
    public function addApp () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('App/index'));
        }
        if (IS_POST) {
            $app_type = array('1' => 'xihaStudent_', '2' => 'xihaTeacher_', '3' => 'xihaHeadmaster_');
            $post = I('post.');
            $data['app_name']       = $post['app_name'] ? $post['app_name'] : ''; 
            $data['version']        = $post['version'] ? $post['version'] : ''; 
            $data['version_code']   = $post['version_code'] ? $post['version_code'] : ''; 
            $data['is_force']       = $post['is_force'] ? $post['is_force'] : 0; 
            $data['os_type']        = $post['os_type'] ? $post['os_type'] : 1; 
            $data['app_client']     = $post['app_client'] ? $post['app_client'] : 1; 
            $data['app_update_log'] = $post['update_log'] ? $post['update_log'] : '';
            // $data['app_update_log'] = str_replace("\r" ,"", $data['app_update_log']); 
            // $data['app_update_log'] = str_replace("\n" ,"", $data['app_update_log']); 
            $data['app_update_log'] = str_replace(" ；" ,";", $data['app_update_log']); 
            $data['addtime']        = time();
            $data['force_least_updateversion'] = $post['least_updateversion'] ? $post['least_updateversion'] : 0; 

            if ($data['os_type'] == 3) { // pc
                $data['app_client'] = 4; // 猫咪鼠标
            }

            if (!empty($_FILES)) {
                if ($_FILES['download_url']['error'] == UPLOAD_ERR_OK) {
                    $download_url = $this->uploadSingleImg('download_url', 'xihaApp/', $app_type[$data['app_client']],'1000145728','../upload/', array('apk'));  
                    $data['app_download_url'] = $download_url['path'];
                } else {
                    $data['app_download_url'] = '';
                }
            }
            if ($data['app_name'] == '' && $data['version'] == '' && $data['version_code'] == '' && $data['app_update_log'] == '' && $data['app_update_log'] == '' && $data['app_download_url'] == '') {
                $this->error('请完善信息', U('App/addApp'));
            }
            $app_version = D('app_version');
            if ($res = $app_version->create($data)) {
                $result = $app_version->add($res);
                if ($result) {
                    action_log('add_app', 'app_version', $result, $this->getLoginUserId());
                    $this->success('添加成功', U('App/index'));
                } else {
                    action_log('add_app', 'app_version', $result, $this->getLoginUserId());
                    $this->error('添加失败', U('App/addApp'));
                }
            } else {
                action_log('add_app', 'app_version', $result, $this->getLoginUserId());
                $this->error('添加失败', U('App/addApp'));
            }

        } else {
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);          
        }
    }

    /**
     * 编辑App版本中的信息
     *
     * @return void
     * @author wl
     * @date   August 12, 2016
     * @update August 22, 2016
     **/
    public function editApp () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('App/index'));
        }
        $id = I('param.id');
        $applist = D('App')->getAppListById($id);
        $app_type = array('1' => 'xihaStudent_', '2' => 'xihaTeacher_', '3' => 'xihaHeadmaster_');
        if (IS_POST) {
            $post = I('post.');
            $data['app_name']       = $post['app_name'] == '' ? $applist['app_name'] : $post['app_name']; 
            $data['version']        = $post['version'] == '' ? $applist['version'] : $post['version']; 
            $data['version_code']   = $post['version_code'] == '' ? $applist['version_code'] : $post['version_code']; 
            $data['os_type']        = $post['os_type'] == '' ? $applist['os_type'] : $post['os_type']; 
            $data['app_client']     = $post['app_client'] == '' ? $applist['app_client'] : $post['app_client']; 
            $data['app_update_log'] = $post['update_log'] == '' ? $applist['app_update_log'] : $post['update_log']; 
            // $data['app_update_log'] = str_replace("\n" ,"", $data['app_update_log']); 
            // $data['app_update_log'] = str_replace("\r" ,"", $data['app_update_log']); 
            $data['app_update_log'] = str_replace(" ；" ,";", $data['app_update_log']); 
            $data['addtime']        = time();
            $data['is_force']       = $post['is_force'] == '' ? $applist['is_force'] : $post['is_force']; 
            $data['force_least_updateversion'] = $post['least_updateversion'] == '' ? $applist['force_least_updateversion'] : $post['least_updateversion']; 
            
            if ($data['os_type'] == 3) { // pc
                $data['app_client'] = 4; // 猫咪鼠标
            }

            if (!empty($_FILES)) {
                if ($_FILES['download_url']['error'] == 0) {
                    $download_url = $this->uploadSingleImg('download_url', 'xihaApp/', $app_type[$data['app_client']], '1000145728','../upload/', array('apk'));  
                    $data['app_download_url'] = $download_url['path'];
                } else {
                    $data['app_download_url'] = $applist['app_download_url'] != '' ? $applist['app_download_url'] : '';
                }
            }
            if ($data['app_name'] == '' && $data['version'] == '' && $data['version_code'] == '' && $data['app_update_log'] == '' && $data['app_update_log'] == '' && $data['app_download_url'] == '') {
                $this->error('请完善信息', U('App/editApp'));
            }
            $app_version = D('app_version');
            if ($res = $app_version->create($data)) {
                $result = $app_version->where(array('id' => $id))->fetchSql(false)->save($res);
                if ($result) {
                    action_log('edit_app', 'app_version', $id, $this->getLoginUserId());
                    $this->success('修改成功', U('App/index'));
                } else {
                    action_log('edit_app', 'app_version', $id, $this->getLoginUserId());
                    $this->error('未做任何修改', U('App/editApp'));
                }
            } else {
                action_log('edit_app', 'app_version', $id, $this->getLoginUserId());
                $this->error('未做任何修改', U('App/editApp'));
            }
        } else {
            $this->assign('applist', $applist);
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
        }
    }

// App反馈管理

    /**
     * App反馈管理的列表展示功能
     *
     * @return  void
     * @author  wl
     * @date    Oct 25, 2016
     **/
    public function appFeedBack () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('App/appFeedBack'));
        }
        $appfeedbacklist = D('App')->getAppfeedBacklist();
        $this->assign('page', $appfeedbacklist['page']);
        $this->assign('count', $appfeedbacklist['count']);
        $this->assign('appfeedbacklist', $appfeedbacklist['appfeedbacklist']);
        $this->display('App/appFeedBack');
    }

    /**
     * 根据条件搜索版本信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 25, 2016
     **/
    public function searchAppFeedBack () {
        $param = I('param.');
        $s_keyword = trim((string)$param['s_keyword']);
        $search_info = trim((string)$param['search_info']);
        if ($s_keyword == '' && $search_info == '') {
            $this->redirect('App/appFeedBack');
        } else {
            $appfeedbacklist = D('App')->searchAppFeedBack($param);
            $this->assign('s_keyword', $s_keyword);
            $this->assign('search_info', $search_info);
            $this->assign('page', $appfeedbacklist['page']);
            $this->assign('count', $appfeedbacklist['count']);
            $this->assign('appfeedbacklist', $appfeedbacklist['appfeedbacklist']);
            $this->display('App/appFeedBack');
        }
    }

    /**
     * 删除反馈
     *
     * @return  void
     * @author  wl
     * @date    Oct 25, 2016
     **/
    public function delAppFeedBack () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('App/appFeedBack'));
        }
        if (IS_AJAX) {
            $id = I('post.id');
            $result = D('App')->delAppFeedBack($id);
            if ($result) {
                $data = array('code' => 200, 'msg' => '删除成功', 'data' => $aid);
            } else {
                $data = array('code' => 103, 'msg' => '删除失败', 'data' => '');
            }
        } else {
            $data = array('code' => 103, 'msg' => '删除失败', 'data' => '');
        }
        action_log('del_qppfeedback', 'feedback', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }














}
?>
