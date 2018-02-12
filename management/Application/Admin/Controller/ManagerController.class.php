<?php  
namespace Admin\Controller;
use Think\Controller;
use Think\Page;

/**
 * 管理员模块
 * @author chenxi
 **/
class ManagerController extends BaseController {
    public $ManagerModel;
    public function _initialize() {
        if (!session('loginauth')) {
            $this->redirect('Public/login');
        }
        $this->ManagerModel = D('Manager');
    }
// 1.管理员管理模块
    /**
     * 管理员列表的展示
     *
     * @return  void
     * @author  wl
     * @date    Nov 18, 2016
     **/
    public function index () {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $school_id = $this->getLoginauth();
        $manager_list = $this->ManagerModel->getManagerList($school_id);
        $this->assign('manager_list', $manager_list['list']);
        $this->assign('page', $manager_list['page']);
        $this->assign('count', $manager_list['count']);
        $this->assign('owner_id', $owner_id);
        $this->assign('school_id', $school_id);
        $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
    }

    /**
     * 管理员模块:搜索驾校的名称
     *
     * @author wl
     * @update Nov 18, 2016
     **/
    public function searchSchoolName(){
        $param = I('param.');
        $school_id = $this->getLoginauth();
        $s_keyword = trim((string)$param['s_keyword']);
        $is_close = (int)$param['is_close'];
        if(trim($s_keyword) == '' && $is_close == ''){
            $this->redirect('Manager/index');
        } else {
            $manager_list = $this->ManagerModel->searchManager($param, $school_id);
            $this->assign('param', $param);
            $this->assign('page', $manager_list['page']);
            $this->assign('count', $manager_list['count']);
            $this->assign('manager_list', $manager_list['list']);
            $this->display('Manager/index');
        }
    }

    /**
     * 管理员模块:添加驾校管理员及驾校之下的各种人员
     * @author wl
     **/
    public function addManager() {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Manager/index'));
        }
        $school_id = $this->getLoginauth();
        $role_id = $this->getRoleId();
        $owner_id = $this->getLoginUserId();
        $school_list = $this->ManagerModel->getSchoolList();
        $roles_list = $this->ManagerModel->getRoles($school_id);
        if(IS_POST) {
            $post =I('post.');
            $data['addtime']            = time();
            $data['name']               = $post['name'] ? $post['name'] : '';
            $data['password']           = md5('xiha123456');
            $data['parent_id']          = $this->getLoginUserId();
            $data['role_id']            = $post['role_id'] ? $post['role_id'] : 0;
            $data['role_permission_id'] = $post['role_id'] ? $post['role_id'] : 0;
            $data['school_id']          = $post['school_id'] == '' ? $school_id : $post['school_id'];
            $data['is_close']           = $post['is_close'] ? $post['is_close'] : 1;
            if ($post['school_id'] == "") {
                $school_name = $this->ManagerModel->getSchoolNameById($school_id);
            } else {
                $school_name = $this->ManagerModel->getSchoolNameById($post['school_id']);
            }
            $data['content']            = $post['content'] == '' ? $school_name['s_school_name'] : $post['content'];
            if($data['content'] == '' && $data['name'] == ''){
                $this->error('请完善所填信息',U('Manager/addManager'));
            }
            $check_name = D('Manager')->checkLoginName($data['name']);
            if (!empty($check_name)) {
                $this->error('该账号已存在');
            }
            $manager = D('admin');
            if($result = $manager->create($data)){
                $res = $manager->fetchSql(false)->add($result);
                if($res){
                    action_log('add_manager', 'admin', $res, $this->getLoginUserId());
                    $this->success('添加成功',U('Manager/index'));
                } else {
                    action_log('add_manager', 'admin', $res, $this->getLoginUserId());
                    $this->error('添加失败',U('addManager'));
                }
            }

        } else {
            $this->assign('role_id', $role_id);
            $this->assign('school_id', $school_id);
            $this->assign('owner_id', $owner_id);
            $this->assign('school_list', $school_list);
            $this->assign('roles_list', $roles_list);
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
        }
    }

    /**
     * 管理员模块:删除管理
     * @author wl
     **/
    public function delManager(){
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Manager/index'));
        }
        if(IS_AJAX){
            $sid =I('post.id');
            $Model =M('admin');
            $res = $Model->where(array('id'=>$sid))->delete();
            if($res){
                $data=array('code'=>200,'msg'=>"删除成功",'data'=>'');
            } else {
                $data=array('code'=>101,'msg'=>"删除失败",'data'=>'');
            }
        } else {
            $data=array('code'=>102,'msg'=>"删除失败",'data'=>'');
        }
        action_log('del_manager', 'admin', $sid, $this->getLoginUserId());
        $this->ajaxReturn($data,'JSON');
    }

    /**
     * 管理员模块:编辑管理
     * @author wl
     **/
    public function editManager(){
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Manager/index'));
        }
        $param = I('param.');
        $sid = $param['school_id'];
        $id = $param['id'];
        if(!$id){
            $this->error('参数出错了',U('Manager/index'));
        }
        if ($sid == 0) {
            $school_list['l_school_id'] = 0;
            $school_list['s_school_name'] = '嘻哈平台';
        } else {
            $school_list = $this->ManagerModel->getSchoolListBySchoolId($sid);
        }
        $school_id = $this->getLoginauth();
        $role_id = $this->getRoleId();
        $manager_list = $this->ManagerModel->getManagerListById($id);
        $roles_list = $this->ManagerModel->getRoles($school_id);
        if(IS_POST){
            $post = I('post.');
            $aid = $post['id'];
            $manager_list = $this->ManagerModel->getManagerListById($aid);
            $data['name']               = $post['name'] == '' ? $manager_list['name'] : $post['name'];
            $data['password']           = $post['password'] == '' ? $manager_list['password'] : md5($post['password']);
            $data['parent_id']          = $this->getLoginUserId();
            $data['role_id']            = $post['role_id'] == '' ? $manager_list['role_id'] : $post['role_id'];
            $data['role_permission_id'] = $post['role_id'] == '' ? $manager_list['role_id'] : $post['role_id'];
            $data['school_id']          = $post['school_id'] == '' ? $school_id : $manager_list['school_id'];
            $data['is_close']           = $post['is_close'] == '' ? $manager_list['is_close'] : $post['is_close'];
            if ($post['school_id'] == "") {
                $school_name = $this->ManagerModel->getSchoolNameById($school_id);
            } else {
                $school_name = $this->ManagerModel->getSchoolNameById($post['school_id']);
            }
            $data['content']            = $post['content'] == '' ? $manager['s_school_name'] : $post['content'];
            if($data['content'] == '' && $data['name'] == ''){
                $this->error('请完善所填信息',U('Manager/addManager'));
                exit;
            }
            $Model = M('admin');
            if($ui=$Model->create($data)){
                $res = $Model->where(array('id' => $id))->save($ui);
                if($res){
                    action_log('edit_manager', 'admin', $id, $this->getLoginUserId());
                    $this->success('数据修改成功！',U('Manager/index'));
                } else {
                    action_log('edit_manager', 'admin', $id, $this->getLoginUserId());
                    $this->error('数据修改失败！',U('Manager/editManager'));
                }
            } 
        } else {
            $this->assign('role_id', $role_id);
            $this->assign('school_id', $school_id);
            $this->assign('owner_id', $owner_id);
            $this->assign('school_list', $school_list);
            $this->assign('roles_list', $roles_list);
            $this->assign('manager_list', $manager_list);
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
        }
    } 

// 2.角色列表模块
    public function roleList() {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $school_id = $this->getLoginauth();
        $roles_list = $this->ManagerModel->getRolesList($school_id);
        $this->assign('roles_list', $roles_list['list']);
        $this->assign('page', $roles_list['page']);
        $this->assign('count', $roles_list['count']);
        $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
    }

    public function addRoles() {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Manager/roleList'));
        }
        $roles_list = $this->ManagerModel->getMenuList(1);
        if(IS_POST) {
            $post = I('post.');
            $list = array(0);
            $data = array();
            $rolepermission = array();
            $moduleids = $this->ManagerModel->getParentModuleId($post['moduleid']);
            foreach ($moduleids as $key => $value) {
                foreach ($value as $k => $v) {
                    $list[] = $v;
                }
            }
            $list = array_values(array_filter($list));
            $data['s_rolename'] = $post['role_name'];
            $data['s_description'] = $post['role_description'];
            $data['owner_id'] = $this->getLoginauth(); //school_id
            $data['owner_type'] = $this->getRoleId();
            $Roles = M('roles');
            $Permiss = M('rolepermission');
            if($rd=$Roles->create($data)) {
                $res = $Roles->add();
                if($res) {
                    $rolepermission['l_role_id'] = $res;
                    $rolepermission['module_id'] =  implode(',', $list);
                    if($Permiss->create($rolepermission)) {
                        $r = $Permiss->add();
                        if($r) {
                            $this->success('添加角色成功', U('Manager/roleList'));
                        } else {
                            $this->error('添加角色失败', U('Manager/addRoles'));
                        }
                    } else {
                        $this->error('添加角色失败', U('Manager/addRoles'));
                    }
                } else {
                    $this->error('添加角色失败', U('Manager/addRoles'));
                }
            } else {
                $this->error('添加角色失败', U('Manager/addRoles'));
            }

        } else {
            $this->assign('roles_list', $roles_list);
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
        }
    }

    /**
     * 编辑角色列表
     *
     * @return void
     * @author wl进行相关改进
     * @update Sep 11, 2016
     **/
    public function editRoles() {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Manager/roleList'));
        }
        $id = I('param.id');
        if(!$id) {
            $this->error('参数错误', U('Manager/rolesList'));
        }
        $roles_detail = $this->ManagerModel->getRolesDetail($id);
        $roles_list = $this->ManagerModel->getMenuList(1);
        if(IS_POST) {
            $post = I('post.');
            $list = array(0);
            $data = array();
            $rolepermission = array();
            $moduleids = $this->ManagerModel->getParentModuleId($post['moduleid']);
            foreach ($moduleids[0] as $key => $value) {
                $list[] = $value;
            }
            $list = array_values(array_filter($list));
            $data['l_role_id'] = $post['l_role_id'];
            $data['s_rolename'] = $post['role_name'];
            $data['s_description'] = $post['role_description'];
            $data['owner_id'] = $this->getLoginauth(); //school_id
            $data['owner_type'] = $this->getRoleId();

            $Roles = M('roles');
            $Permiss = M('rolepermission');
            $rolepermission_id=$this->ManagerModel->getRolePressId($id);

            if($rd = $Roles->create($data)) {
                $res = $Roles->save($rd);
            } 
            $rolepermission['l_rolepress_incode']=$rolepermission_id;
            $rolepermission['l_role_id'] = $data['l_role_id'];
            $rolepermission['module_id'] =  implode(',', $list);
            if($rs = $Permiss->create($rolepermission)) {
                $r = $Permiss->save($rs);
            }
            if ($res || $r) {
                $this->success('修改成功');
            } else {
                $this->success('保存成功');
            }
        } else {
            $this->assign('roles_list', $roles_list);
            $this->assign('roles_detail', $roles_detail);
            $this->assign('rolepermission_id', $rolepermission_id);
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
        }
    }

    /**
     * 管理员模块中：删除角色
     *
     * @return void
     * @author wl
     **/
    public function delRoles() {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Manager/roleList'));
        }
        if(IS_AJAX){
            $id = I('post.id');
            if(!$id) {
                $data = array('code'=>101, 'msg'=>'删除失败', 'data'=>'');
                $this->ajaxReturn($data, 'JSON');
            }
            $res = D('roles')->relation('rolepermission')->delete($id);
            if($res) {
                $data = array('code'=>200, 'msg'=>'删除成功', 'data'=>'');
            } else {
                $data = array('code'=>102, 'msg'=>'删除失败', 'data'=>'');    
            }

        } else {
            $data = array('code'=>102, 'msg'=>'删除失败', 'data'=>'');    
        }
        $this->ajaxReturn($data, 'JSON');
    }

// 3.菜单列表模块
    public function menuList() {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Admin/Index/welcome'));
        }
        $menu_list = $this->ManagerModel->getMenuList();
        $this->assign('menu_list', $menu_list['list']);
        $this->assign('page', $menu_list['page']);
        $this->assign('count', $menu_list['count']);
        $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
    }
    
    /**
     * 双击表格进行排序
     *
     * @return  void
     * @author  wl
     * @date    Sep 24, 2016
     **/
    public function changeMenuOrder () {
        if (!IS_POST || !IS_AJAX) {
            $msg = "参数错误";
            $data = '';
            $this->ajaxReturn(array('code' => 101, 'msg' => $msg, 'data' => $data), "JSON");
        }
        $post = I('post.');
        $update_ok = D('Manager')->updateMenuOrder($post);
        if ($update_ok == 101 || $update_ok == 102 ) {
            $code = $update_ok;
            $msg = '参数错误';
            $data = '';
        } elseif ($update_ok == 105) {
            $code = $update_ok;
            $msg = '未做任何修改';
            $data = '';

        } elseif ($update_ok == 200) {
            $code = $update_ok;
            $msg = '修改成功';
            $data = '';

        } elseif ($update_ok == 400) {
            $code = $update_ok;
            $msg = '修改失败';
            $data = '';
        }
        action_log('change_menu_order', 'menu', $post['id'], $this->getLoginUserId());
        $this->ajaxReturn(array('code' => $code, 'msg' => $msg, 'data' => $data), "JSON");
    }

    /**
     * 给菜单列表进行排序
     *
     * @return  void
     * @author  wl
     * @date    Sep 20, 2016
     **/
    public function orderMenu () {
        if (IS_AJAX) {
            $post = I('post.');
            $mid = $post['mid'];
            $data['i_order'] = $post['i_order'] ? $post['i_order']: 1;
            $menu = M('menu');
            if ($res = $menu->create($data)) {
                $result = $menu->where(array('moduleid' => $mid))->fetchSql(false)->save($res);
                if ($result) {
                    $data = array('code' => 200, 'msg' => '修改成功', 'data' => $data['i_order']);
                } else {
                    $data = array('code' => 103, 'msg' => '修改失败', 'data' => '');
                }
            } else {
                $data = array('code' => 103, 'msg' => '修改失败', 'data' => '');
            }
        }
        action_log('change_menu_order', 'menu', $mid, $this->getLoginUserId());
        $this->ajaxReturn($data, "JSON");
    }

    public function addMenu() {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Manager/menuList'));
        }
        $menu_list = $this->ManagerModel->getMenuList();
        if(IS_POST) {
            $data = array();
            $data['m_applicationid'] = 1;
            $data['m_parentid'] = I('post.parentid');
            $data['m_cname'] = I('post.menu_name');
            $data['m_directory'] = I('post.menu_directory');
            $data['m_close'] = I('post.m_close');
            $data['m_type'] = I('post.m_type');
            $data['m_controller'] = I('post.menu_controller');
            $data['m_pagecode'] = substr(microtime(), -6);
            if($data['m_parentid'] == '' && $data['m_cname'] == '' && $data['m_directory'] == '' && $data['m_close'] == '' && $data['m_pagecode'] == '') {
                $this->error('请完善所填信息', U('Manager/addMenu'));
            }
            $Model = M('menu');
            $image_info = $this->uploadSingleImg('menu_logo', 'menu/', 'menu_');
            if(!$image_info) {
                $this->error('图片上传失败', U('Manager/addMenu'));
            }
            $data['m_imageurl'] = isset($image_info['path']) ? $image_info['path'] : '';
            if($Model->create($data)) {
                $res = $Model->add();
                if($res) {
                    action_log('add_menu', 'menu', $res, $this->getLoginUserId());
                    $this->success('添加成功', U('Manager/menuList'));
                } else {
                    action_log('add_menu', 'menu', $res, $this->getLoginUserId());
                    $this->error('添加失败', U('Manager/addMenu'));
                }
            } else {
                action_log('add_menu', 'menu', $res, $this->getLoginUserId());
                $this->error('添加失败', U('Manager/addMenu'));
            }
        } else {
            $this->assign('menu_list', $menu_list['list']);
            $this->assign('page', $menu_list['page']);
            $this->assign('count', $menu_list['count']);
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
        }
    }


    public function editMenu() {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Manager/menuList'));
        }
        $module_id = I('param.id');
        if(IS_POST) {
            $data = array();
            $data['m_applicationid'] = 1;
            $data['m_parentid'] = I('post.parentid');
            $data['m_cname'] = I('post.menu_name');
            $data['m_directory'] = I('post.menu_directory');
            $data['m_close'] = I('post.m_close');
            $data['i_order'] = I('post.i_order');
            $data['m_type'] = I('post.m_type');
            $data['m_controller'] = I('post.menu_controller');
            if($data['m_parentid'] == '' && $data['m_cname'] == '' && $data['m_directory'] == '' && $data['m_close'] == '') {
                $this->error('请完善所填信息', U('Manager/editMenu'));
            }
            $Model = M('menu');
            if($_FILES['menu_logo']['error'] == 0) {
                $image_info = $this->uploadSingleImg('menu_logo', 'menu/', 'menu_');
                if(!$image_info) {
                    $this->error('图片上传失败', U('Manager/editMenu').'?id='.$module_id);
                }
                $data['m_imageurl'] = $image_info['path'];                
            } else {
                $data['m_imageurl'] = I('post.menu_img');
            }

            $res = $Model->where("moduleid = $module_id")->data($data)->fetchSql(false)->save();
            if($res) {
                action_log('edit_menu', 'menu', $module_id, $this->getLoginUserId());
                $this->success('编辑成功', U('Manager/menuList'));
            } else {
                action_log('edit_menu', 'menu', $module_id, $this->getLoginUserId());
                $this->error('未做任何修改', U('Manager/editMenu').'?id='.$module_id);
            }

        } else {
            $menu_list = $this->ManagerModel->getMenuList();
            $menu_info = $this->ManagerModel->getMenuInfo($module_id);
            $this->assign('menu_list', $menu_list['list']);
            $this->assign('menu_info', $menu_info);
            $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
        }
    }

    public function delMenu() {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Manager/menuList'));
        }
        if(IS_POST) {
            $id = I('post.id');
            $moduleids = $this->ManagerModel->getAllMenuId($id);
            $Model = M('menu');
            if($moduleids) {
                $res = $Model->where('moduleid IN('.implode(',', $moduleids).')')->delete();
            } else {
                $data = array('code'=>103, 'msg'=>"删除失败", 'data'=>'');
            }

            if($res) {
                $data = array('code'=>200, 'msg'=>"删除成功", 'data'=>'');
            } else {
                $data = array('code'=>101, 'msg'=>"删除失败", 'data'=>'');
            }
        } else {
            $data = array('code'=>102, 'msg'=>"删除失败", 'data'=>'');        
        }
        action_log('del_menu', 'menu', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }


    public function schoolList() {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.ACTION_NAME, $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!');
        }
        $school_list = $this->ManagerModel->getSchoolList();
        $this->display(CONTROLLER_NAME.'/'.__FUNCTION__);
    }

    public function setMenuStatus() {
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.'editMenu', $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Manager/menuList'));
        }
        $id = I('post.id');
        $status = I('post.status');
        $list = $this->ManagerModel->setMenuStatus(array($id), $status);
        if($list['res']) {
            $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$list['moduleids']);
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
        }
        action_log('set_menu_status', 'menu', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

    /**
     * 管理员模块:设置管理员禁用与开启的状态
     * @author wl
     **/
    public function setManagerStatus(){
        $role_id = $this->getRoleId();
        $permission_check = $this->is_authorized(CONTROLLER_NAME.'/'.'editManager', $role_id);
        if (true !== $permission_check) {
            $this->error('Permission Denied!', U('Manager/index'));
        }
        if(IS_AJAX) {
            $id = I('post.id');
            $status = I('post.status');
            $res = $this->ManagerModel->setManagerStatus($id,$status);
            if($res['is_close']) {
                $data = array('code'=>200, 'msg'=>"设置成功", 'data'=>$list['moduleids']);
            } else {
                $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');
            }
        } else {
            $data = array('code'=>102, 'msg'=>"设置失败", 'data'=>'');    
        }
        action_log('set_manager_status', 'admin', $id, $this->getLoginUserId());
        $this->ajaxReturn($data, 'JSON');
    }

}

?>
