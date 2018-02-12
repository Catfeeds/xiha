<?php
/**
 * 管理员模块
 * @author chenxi
 **/
namespace Admin\Model;
use Think\Model;
use Think\Page;

class ManagerModel extends BaseModel {
    private $_link = array(
    );
    public $tableName = 'admin'; 
// 1.管理员管理模块
    /**
     * 管理员列表的展示
     *
     * @return  void
     * @author  wl
     * @date    Nov 18, 2016
     **/
    public function getManagerList ($school_id) {
        if ($school_id == 0) {
            $count = $this->table(C('DB_PREFIX').'admin a')
                ->join(C('DB_PREFIX').'roles r ON r.l_role_id = a.role_id', 'LEFT')
                ->fetchSql(false)
                ->count();
            $Page = new Page($count, 10);
            $page = $this->getPage($count, 10);
            $managerlists = array();
            $managerlist = $this->table(C('DB_PREFIX').'admin a')
                ->join(C('DB_PREFIX').'roles r ON r.l_role_id = a.role_id', 'LEFT')
                ->field('a.id, a.name, a.password, a.role_permission_id, a.role_id, a.school_id, a.addtime, a.content, a.parent_id, a.is_close, r.s_rolename')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->order('id DESC')
                ->fetchSql(false)
                ->select();

        } else {
            $count = $this->table(C('DB_PREFIX').'admin a')
                ->join(C('DB_PREFIX').'roles r ON r.l_role_id = a.role_id', 'LEFT')
                ->where('school_id = :sid')
                ->bind(['sid' => $school_id])
                ->fetchSql(false)
                ->count();
            $Page = new Page($count, 10);
            $page = $this->getPage($count, 10);
            $managerlists = array();
            $managerlist = $this->table(C('DB_PREFIX').'admin a')
                ->join(C('DB_PREFIX').'roles r ON r.l_role_id = a.role_id', 'LEFT')
                ->where('school_id = :sid')
                ->bind(['sid' => $school_id])
                ->field('a.id, a.name, a.password, a.role_permission_id, a.role_id, a.school_id, a.addtime, a.content, a.parent_id, a.is_close, r.s_rolename')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->order('id DESC')
                ->fetchSql(false)
                ->select();
        }
        if (!empty($managerlist)) {
            foreach ($managerlist as $key => $value) {
                if ($value['addtime'] != 0) {
                    $managerlist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $managerlist[$key]['addtime'] = '';
                }
                $original_password = md5('xiha123456');
                if($original_password == $value['password']) {
                    $managerlist[$key]['is_change'] = 1;  // 未修改密码
                } else {
                    $managerlist[$key]['is_change'] = 2;  // 已修改密码
                }
            }
        }
        $managerlists = array('list' => $managerlist, 'count' => $count, 'page' => $page);
        return $managerlists;

    }

    /**
     * 管理员模块:根据管理员的展示名称来搜索管理员 
     *
     * @return  void
     * @author  wl
     * @update  Nov 18, 2016
     **/
    public function searchManager($param, $school_id){
        $map = array();
        if ($param['is_close'] != '') {
            $map['is_close'] = array('EQ', $param['is_close']);
        }

        if ($param['s_keyword'] != '') {
            $map['content'] = array('like', '%'.$param['s_keyword'].'%');
        }

        if ($school_id == 0) {
            $count = $this->table(C('DB_PREFIX').'admin a')
                ->join(C('DB_PREFIX').'roles r ON r.l_role_id = a.role_id', 'LEFT')
                ->where($map)
                ->fetchSql(false)
                ->count();
            $Page = new Page($count, 10, $param);
            $page = $this->getPage($count, 10, $param);
            $managerlists = array();
            $managerlist = $this->table(C('DB_PREFIX').'admin a')
                ->join(C('DB_PREFIX').'roles r ON r.l_role_id = a.role_id', 'LEFT')
                ->where($map)
                ->field('a.id, a.name, a.password, a.role_permission_id, a.role_id, a.school_id, a.addtime, a.content, a.parent_id, a.is_close, r.s_rolename')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->order('id DESC')
                ->fetchSql(false)
                ->select();
        } else {
            $count = $this->table(C('DB_PREFIX').'admin a')
                ->join(C('DB_PREFIX').'roles r ON r.l_role_id = a.role_id', 'LEFT')
                ->where($map)
                ->where('school_id = :sid')
                ->bind(['sid' => $school_id])
                ->fetchSql(false)
                ->count();
            $Page = new Page($count, 10, $param);
            $page = $this->getPage($count, 10, $param);
            $managerlists = array();
            $managerlist = $this->table(C('DB_PREFIX').'admin a')
                ->join(C('DB_PREFIX').'roles r ON r.l_role_id = a.role_id', 'LEFT')
                ->where($map)
                ->where('school_id = :sid')
                ->bind(['sid' => $school_id])
                ->field('a.id, a.name, a.password, a.role_permission_id, a.role_id, a.school_id, a.addtime, a.content, a.parent_id, a.is_close, r.s_rolename')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->order('id DESC')
                ->fetchSql(false)
                ->select();
        }
        if (!empty($managerlist)) {
            foreach ($managerlist as $key => $value) {
                if ($value['addtime'] != 0) {
                    $managerlist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $managerlist[$key]['addtime'] = '';
                }
                $original_password = md5('xiha123456');
                if($original_password == $value['password']) {
                    $managerlist[$key]['is_change'] = 1;  // 未修改密码
                } else {
                    $managerlist[$key]['is_change'] = 2;  // 已修改密码
                }
            }
        }
        $managerlists = array('list' => $managerlist, 'count' => $count, 'page' => $page);
        return $managerlists;
    }
    /**
     * 检查用户是否存在
     *
     * @return  void
     * @author  wl
     * @date    Nov 14, 2016
     **/
    public function checkLoginName ($name) {
        if (!trim((string)$name)) {
            return false;
        }
        $check_name = $this->table(C('DB_PREFIX').'admin')
            ->where('name = :nm')
            ->bind(['nm' => $name])
            ->find();
        if (!empty($check_name)) {
            return $check_name;
        } else {
            return array();
        }
    }

    /**
     * 通过带入的id获得相应的信息
     *
     * @return  void
     * @author  wl
     * @date    Sep 14, 2016
     **/
    public function getManagerListById ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $manager_list = $this->table(C('DB_PREFIX').'admin a')
            ->join(C('DB_PREFIX').'roles r ON r.l_role_id = a.role_id', 'LEFT')
            ->where(array('id' => $id))
            ->order('a.id DESC')
            ->find();
        if (!$manager_list) {
            return array();
        }
        return $manager_list;
    }

    /**
     * 通过带入的id获得相应的信息
     *
     * @return  void
     * @author  wl
     * @date    Sep 14, 2016
     **/
    public function getSchoolListBySchoolId ($school_id) {
        if (!is_numeric($school_id)) {
            return false;
        }
        $school_list = $this->table(C('DB_PREFIX').'school')
            ->where('l_school_id = :sid')
            ->bind(['sid' => $school_id])
            ->field('l_school_id, s_school_name')
            ->find();
        return $school_list;

    }

    /**
    * 管理员模块:获得管理员列表中的id
    * @author wl
    **/
    public function getManagerId($sid) {
        if($sid) {
            return false;
        }
        static $list = array();
        $Manager_id_arr = $this->table(C('DB_PREFIX').'admin')
            ->where('id IN ('.implode(',', $sid).')')
            ->field('id')
            ->fetchSql(false)
            ->select();
        $ids = array();
        if($Manager_id_arr) {
            foreach ($Manager_id_arr as $key => $value) {       
                $ids[] = $value['id'];      
            }
            $list[] = array_values($ids);
        }
        return $list;
    }
    /**
     * 管理员模块:设置管理员禁用与开启的状态
     * @author wl
     **/
    public function setManagerStatus($id,$status){
        if(!$id) {
            return false;
        } 
        $list = array();
        $sid = array($id);
        $mid = $this->getManagerId($id);
        if($mid){
            foreach($mid as $k=>$v){
                foreach($v as $key=>$value){
                    $sid[] = $value;  
                }
            }
        }
        $data =array('is_close'=>$status);
        $Model = M('admin');
        $result = $Model->where("id  IN (".implode(',', $sid).")")
            ->data($data) 
            ->fetchSql(false)
            ->save();
        $list['is_close'] = $result;
        $list['id'] = $sid;
        return $list;

    }

// 2.角色列表模块
    public function getRolesList($owner_id) {
        $count = $this->table(C('DB_PREFIX').'roles')->where('owner_id = :owner_id')->bind(['owner_id'=>$owner_id])->field('l_role_id')->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $list = array();
        $roles_list = $this->table(C('DB_PREFIX').'roles')
            ->where('owner_id = :owner_id')
            ->bind(['owner_id'=>$owner_id])
            ->field('l_role_id, s_rolename, s_description, owner_id, owner_type')
            ->limit($Page->firstRow.','.$Page->listRows)->order('l_role_id ASC')
            // ->cache(true, 60)
            ->fetchSql(false)
            ->select();
        $list = array('list'=>$roles_list, 'page'=>$page, 'count'=>$count);
        return $list;
    }


    /**
     * undocumented function
     *
     * @return void
     * @author cx
     **/
    public function insertRole() {

    }
    public function getRolesDetail($id) {
        $roles_detail = $this->table(C('DB_PREFIX').'roles as r')
            ->join(C('DB_PREFIX').'rolepermission as rp ON r.l_role_id = rp.l_role_id', 'LEFT')
            ->where('r.l_role_id = :id')
            ->bind(['id'=>$id])
            ->field('r.l_role_id, r.s_rolename, r.s_description, r.owner_id, r.owner_type, rp.module_id')
            ->fetchSql(false)
            ->find();
        $moduleids = array(0);
        if($roles_detail) {
            $moduleids = explode(',', $roles_detail['module_id']);
        }
        $menu_list = $this->getMenuList(1);
        $_data = array();
        foreach ($menu_list['list'] as $key => $value) {
            if(in_array($value['moduleid'], $moduleids)) {
                $menu_list['list'][$key]['is_checked'] = 1; // 选中
            } else {
                $menu_list['list'][$key]['is_checked'] = 2; // 未选中
            }
            if($value['m_type'] == 2) {
                $_data[] = $menu_list['list'][$key];
            }
        }
        if(!empty($menu_list['list'])) {
            foreach ($menu_list['list'] as $key => $value) {
                foreach ($_data as $k => $v) {
                    if($v['m_parentid'] == $value['moduleid']) {
                        $menu_list['list'][$key]['controller'][$k] = $v;
                    }
                }   
            }   
        }
        $roles_detail['menu_list'] = $menu_list;
        return $roles_detail;
    }

    public function deleteRole($id) {
        $res = $this->table(C('DB_PREFIX').'roles')
            ->join(C('DB_PREFIX').'rolepermission  ON  roles.l_role_id = rolepermission.l_role_id', 'INNER')
            ->where('l_role_id = :sid')
            ->bind(['sid'=>$id])
            ->delete();
        return $res;
    }
    /**
     * 管理员模块:获得角色权限中的l_rolepress_incode
     * @author wl
     **/
    public function getRolePressId($id){
        $rolepermission_list =$this->table(C('DB_PREFIX').'rolepermission')
            ->where('l_rolepress_incode = :sid')
            ->bind(['sid'=>$id])
            ->fetchSql(false)
            ->select();
        static $list ='';
        if($rolepermission_list){
            foreach($rolepermission_list as $key=>$value){
                $list =$value['l_rolepress_incode'] ;
            }
        }
        return $list;
    }
    /**
     * 获得管理员角色
     *
     * @return void
     * @author wl
     **/
    public function getRoles ($school_id) {
        $roles_list = $this->table(C('DB_PREFIX').'roles')
            ->where('owner_id=:school_id')
            ->bind(['school_id'=>$school_id])
            ->distinct(true)
            ->field('l_role_id,s_rolename,owner_id,owner_type')
            ->fetchSql(false)
            ->select();
        return $roles_list;                            
    }

// 3.菜单列表模块
    public function getMenuList($is_close, $parent_id = 0, $level=0, $html='—| ') {
        $count = 0;
        if($is_close) {
            $count = $this->table(C('DB_PREFIX').'menu')
                ->where('m_close = :is_close')
                ->bind(['is_close'=>$is_close])	
                ->field('moduleid')
                ->count();
            $Page = new Page($count, 20);
            $page = $this->getPage($count, 20);
            $menu_list = $this->table(C('DB_PREFIX').'menu')
                ->where('m_parentid = :parent_id AND m_close = :is_close')
                ->bind(['parent_id'=>$parent_id, 'is_close'=>$is_close])
                ->limit($Page->firstRow.','.$Page->listRows)
                ->order('i_order ASC')
                ->fetchSql(false)
                ->select();
        } else {
            $count = $this->table(C('DB_PREFIX').'menu')
                ->field('moduleid')
                ->count();
            $Page = new Page($count, 20);
            $page = $this->getPage($count, 20);
            $menu_list = $this->table(C('DB_PREFIX').'menu')
                ->where('m_parentid = :parent_id')
                ->bind(['parent_id'=>$parent_id])
                ->limit($Page->firstRow.','.$Page->listRows)
                ->order('i_order ASC')
                ->fetchSql(false)
                ->select();
        }
        $list = array();
        static $_list = array();
        static $_data = array();
        if($menu_list) {
            foreach ($menu_list as $key => $value) {
                if($value['m_parentid'] == $parent_id) {
                    $value['level'] = $level;
                    $value['html'] = str_repeat($html, $level);
                    $_list[] = $value;
                    if($value['m_type'] == 2) {
                        $_data[] = $value;
                    }
                    $this->getMenuList($is_close, $value['moduleid'], $level + 1);
                }
            }
        } else {

        }
        if(!empty($_list)) {
            foreach ($_list as $key => $value) {
                foreach ($_data as $k => $v) {
                    if($v['m_parentid'] == $value['moduleid']) {
                        $_list[$key]['controller'][$k] = $v;
                    } else {
                        if($value['m_parentid'] == 0) {
                            $_list[$key]['controller'] = array();
                        }
                    }
                }	
            }	
        }

        $list = array('list'=>$_list, 'page'=>$page, 'count'=>$count);
        return $list;
    }
    
    public function getMenuInfo($id) {
        $menu_info = $this->table(C('DB_PREFIX').'menu')->where('moduleid = :mid')->bind(['mid'=>$id])->field('moduleid, m_applicationid, m_parentid, m_pagecode, m_controller, m_cname, m_directory, m_imageurl, m_close, m_type, i_order')->fetchSql(false)->find();
        return $menu_info;
    }

    public function getAllMenuId($pid) {
        $menu_list = $this->table(C('DB_PREFIX').'menu')
            ->where('m_parentid = :pid')
            ->bind(['pid'=>$pid])
            ->field('moduleid')
            ->select();
        static $list = array();
        $list[] = $pid;
        if($menu_list) {
            foreach ($menu_list as $key => $value) {
                // $list[] = $value['moduleid'];
                $this->getAllMenuId($value['moduleid']);
            }
        }
        return $list;
    }

    /**
     * 更新菜单的排序
     *
     * @return  void
     * @author  wl
     * @date    Sep 24, 2016
     **/
    public function updateMenuOrder ($post) {
        if (empty($post)) {
            return 101;// 参数错误
        }
        if (isset($post['i_order'])) {
            if (!is_numeric($post['i_order'])) {
                return 102; // 参数类型不符合
            } else {
                $old_num = $this->table(C('DB_PREFIX').'menu')
                    ->where('moduleid = :mid')
                    ->bind(['mid' => $post['id']])
                    ->getField('i_order');
                if ($post['i_order'] == $old_num) {
                    return 105; // 未做任何修改
                } 
            }
        }
        $data['i_order'] = $post['i_order'];
        $menu = D('menu');
        if ($res = $menu->create($data)) {
            $result = $menu->where(array('moduleid' => $post['id']))
                ->fetchSql(false)
                ->save($res);
            if ($result) {
                return 200;
            } else {
                return 400;
            }
        }
    }

    /**
     * 获取所有上级moduleid
     *
     * @return void
     * @author cx
     **/
    public function getParentModuleId($moduleid) {
        if(empty($moduleid)) {
            return false;
        }
        static $list = array();
        $menu_id_arr = $this->table(C('DB_PREFIX').'menu')
            ->where('moduleid IN ('.implode(',', $moduleid).')')
            ->field('m_parentid, moduleid')
            ->fetchSql(false)
            ->select();
        $parentids = array(0);
        $moduleids = array(0);
        if($menu_id_arr) {
            foreach ($menu_id_arr as $key => $value) {
                $parentids[] = $value['m_parentid'];		
                $moduleids[] = $value['moduleid'];		
            }
            $list[] = array_values($moduleids);
            $this->getParentModuleId($parentids);

        }
        return $list;
    }

    /**
     * 获取所有包含当前id的下级moduleID
     *
     * @return void
     * @author cx
     **/
    public function getChildModuleId($parentid) {
        if(empty($parentid)) {
            return false;
        }
        static $list = array();
        $menu_id_arr = $this->table(C('DB_PREFIX').'menu')
            ->where('m_parentid IN ('.implode(',', $parentid).')')
            ->field('m_parentid, moduleid')
            ->fetchSql(false)
            ->select();
        $moduleids = array();
        if($menu_id_arr) {
            foreach ($menu_id_arr as $key => $value) {		
                $moduleids[] = $value['moduleid'];		
            }
            $list[] = array_values($moduleids);
            $this->getChildModuleId($moduleids);
        }
        return $list;
    }


    public function setMenuStatus($id, $status) {
        if(!$id) {
            return false;
        }
        $list = array();
        $module_ids = $id;
        $moduleids = $this->getChildModuleId($id);
        if($moduleids) {
            foreach ($moduleids as $key => $value) {
                foreach ($value as $k => $v) {
                    $module_ids[] = $v;
                }
            }
        }
        $data = array('m_close'=>$status);
        $Model = M('menu');
        $res = $Model->where("moduleid  IN (".implode(',', $module_ids).")")
            ->data($data)
            ->fetchSql(false)
            ->save();
        $list['res'] = $res;
        $list['moduleids'] = $module_ids;
        return $list;
    }

    /**
     * 获得驾校ID
     *
     * @return void
     * @author wl
     **/
    public function getSchoolList() {
        $school_list = $this->table(C('DB_PREFIX').'school')
            ->field('l_school_id,s_school_name')
            ->where(array('is_show' => 1))
            ->fetchSql(false)
            ->select();
        return $school_list;
    }

    /**
     * 根据驾校列表中的id来获得驾校的名称
     *
     * @return void
     * @author 
     **/
    public function getSchoolNameById($school_id){
        $school_list_name = $this->table(C('DB_PREFIX').'school')
            ->where('l_school_id = :sid')
            ->bind(['sid' => $school_id])
            ->field('s_school_name')
            ->fetchSql(false)
            ->find();
        return $school_list_name;

    }
    /*
     * 获取登陆者角色的对应菜单列表
     *
     * @author gaodacheng
     * @date 2016-09-11
     * */
    public function getMenuListByRoleId($role_id = 0) {
        $menu_list = array();
        $module_id_list = $this->table(C('DB_PREFIX').'rolepermission')
            ->where('l_role_id = :role_id')
            ->bind(array('role_id' => $role_id))
            ->field('module_id')
            ->find();
        if (!isset($module_id_list['module_id'])) {
            return array();
        }
        // 一级菜单
        $menu_list_first = $this->table(C('DB_PREFIX').'menu')
            ->where(array('moduleid' => array('IN', $module_id_list['module_id']), 'm_parentid' => 0))
            ->field(array('moduleid', 'm_cname', 'm_controller', 'm_parentid'))
            ->order('i_order ASC')
            ->fetchSql(false)
            ->select();
        if (is_array($menu_list_first) && count($menu_list_first) > 0) {
            foreach ($menu_list_first as $menu_first_index => $menu_first) {
                // 二级菜单
                $menu_list_second = $this->table(C('DB_PREFIX').'menu')
                    ->where(array('moduleid' => array('IN', $module_id_list['module_id']), 'm_parentid' => $menu_first['moduleid']))
                    ->field(array('moduleid', 'm_cname', 'm_controller', 'm_parentid'))
                    ->order('i_order ASC')
                    ->fetchSql(false)
                    ->select();
                if (is_array($menu_list_second) && count($menu_list_second) > 0) {
                    $menu_first['m_menu_url'] = U($menu_first['m_controller']);
                    foreach ($menu_list_second as $menu_second_index => $menu_second) {
                        // 三级菜单
                        $menu_list_third = $this->table(C('DB_PREFIX').'menu')
                            ->where(array('moduleid' => array('IN', $module_id_list['module_id']), 'm_parentid' => $menu_second['moduleid']))
                            ->field(array('moduleid', 'm_cname', 'm_controller', 'm_parentid'))
                            ->order('i_order ASC')
                            ->fetchSql(false)
                            ->select();
                        if (is_array($menu_list_third) && count($menu_list_third) > 0) {
                            $menu_list_second[$menu_second_index]['m_menu_url'] = U($menu_second['m_controller']);
                            $menu_list_second[$menu_second_index]['menu_list_third'] = $menu_list_third;
                        } else {
                            unset($menu_list_second[$menu_second_index]);
                        }
                    }
                }
                if (is_array($menu_list_second) && count($menu_list_second) > 0) {
                    $menu_list[] = array('menu_first' => $menu_first, 'menu_second_list' => array_values($menu_list_second));
                }
            }
        }
        return $menu_list;
    }

    /*
     * 按操作名找moduleid
     *
     * @author gaodacheng
     * @date 2016-09-19  
     *
     * */
    public function getModuleIdByController($controller_name = null) {
        if (is_null($controller_name) || (string)$controller_name == '') {
            return null;
        }
        $moduleid = $this->table(C('DB_PREFIX').'menu')
            ->where(array('m_controller' => $controller_name, 'm_type' => '2')) // m_type=1模块-2操作
            ->fetchSql(false)
            ->getField('moduleid');
        return intval($moduleid);
    }

    /*
     * 按角色名找module列表
     *
     * @author gaodacheng
     * @date 2016-09-19  
     *
     * */
    public function getPermissionModuleList($role_id = null) {
        if (is_null($role_id) || (int)$role_id <= 0) {
            return array();
        }
        $module_list = $this->table(C('DB_PREFIX').'rolepermission')
            ->where(array('l_role_id' => $role_id))
            ->fetchSql(false)
            ->getField('module_id');
        return array_filter(explode(',', $module_list));
    }
}
?>
