<?php
    // 首页
defined('BASEPATH') OR exit('No direct script access allowed');

class Madmin extends CI_Model {

    public $admin_tablename = 'cs_admin';
    public $roles_tablename = 'cs_roles';
    public $menu_tablename = 'cs_menus';
    public $permission_tablename = 'cs_rolepermission';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('mbase');
    }

// 1.登录
    // 获取登录者信息
    public function getLoginInfo($wherecondition) {
    	$query = $this->db->get_where($this->admin_tablename, $wherecondition);
    	return $query->row_array();
    }

    // 获取登录者信息
    public function getRoleInfo($wherecondition, $select='') {
        if($select) {
            $this->db->select($select);
        }
        $query = $this->db->get_where($this->roles_tablename, $wherecondition);
        return $query->row_array();
    }
    
    // 获取登录者信息
    public function getRolePermissionInfo($wherecondition, $select='') {
        if($select) {
            $this->db->select($select);
        }
        $query = $this->db->get_where($this->permission_tablename, $wherecondition);
        return $query->row_array();
    }
    
    // 获取角色列表
    public function getRolesList($wherecondition, $start='', $limit='') {
        $query = $this->db->order_by('l_role_id', 'DESC')->get_where($this->roles_tablename, $wherecondition, $limit, $start);
        return ['list'=>$query->result()];
    }

    /**
     * 获取展示的相关信息
     * @param  $role_id
     * @return void
     **/
    public function getMenuByRoleId($role_id)
    {
        $map = [];
        $menu_list = [];
        $module_id = $this->db
            ->select('module_id')
            ->from($this->permission_tablename)
            ->where('l_role_id', $role_id)
            ->get()
            ->result_array();
        $module_ids = [];
        if ( ! empty($module_id)) {
            foreach ($module_id as $key => $value) {
                $module_ids = explode(',', $value['module_id']);
            }
        }

        // 一级菜单
        $menu_first_list = $this->db
            ->from($this->menu_tablename)
            ->select('moduleid, m_applicationid, m_parentid, m_controller, m_cname, m_imageurl')
            ->where('m_parentid', 0)
            ->where('m_close', 1)
            ->where_in('moduleid', $module_ids)
            ->order_by('i_order', 'asc')
            ->get()
            ->result_array();
        $iconclass = '';
        if ( ! empty($menu_first_list) && count($menu_first_list) > 0) {
            foreach ($menu_first_list as $menu_first_index => $menu_first) {
                // 二级菜单 
                $map['m_parentid'] = $menu_first['moduleid'];
                $map['m_close'] = 1;
                $menu_second_list = $this->db
                    ->from($this->menu_tablename)
                    ->select('moduleid, m_applicationid, m_parentid, m_controller, m_cname')
                    ->where($map)
                    ->where_in('moduleid', $module_ids)
                    ->get()
                    ->result_array();

                if ( ! empty($menu_second_list) && count($menu_second_list) > 0) {
                    foreach ( $menu_second_list as $menu_second_index => $menu_second) {
                        // 三级菜单
                        $menu_third_list = $this->db->from($this->menu_tablename)
                            ->select('moduleid, m_parentid, m_controller, m_cname')
                            ->where_in('moduleid', $module_ids)
                            ->where('m_parentid', $menu_second['moduleid'])
                            ->get()
                            ->result_array();
                        if ( ! empty($menu_third_list) && count($menu_third_list) > 0) {
                            $menu_list[$menu_first_index]['index'] = $menu_first['moduleid'];
                            $menu_list[$menu_first_index]['mname'] = $menu_first['m_cname'];
                            switch ($menu_first['m_cname']) {
                                case "驾校管理" : $iconclass = "icon-xuexiao"; break;
                                case "教练管理" : $iconclass = "icon-jiaolian"; break;
                                case "学员管理" : $iconclass = "icon-xueyuanguanli"; break;
                                case "订单管理" : $iconclass = "icon-dingdan"; break;
                                case "评价管理" : $iconclass = "icon-pingjia"; break;
                                case "优惠券管理" : $iconclass = "icon-youhuiquan"; break;
                                case "车辆管理" : $iconclass = "icon-cheliang"; break;
                                case "广告管理" : $iconclass = "icon-jinlingyingcaiwangtubiao86"; break;
                                case "App管理" : $iconclass = "icon-app"; break;
                                case "区域管理" : $iconclass = "icon-quyu"; break;
                                case "权限管理" : $iconclass = "icon-quanxian"; break;
                                case "系统管理" : $iconclass = "icon-xitong"; break;
                                default: $iconclass = $menu_first['m_imageurl']; break;
                            }
                            $menu_list[$menu_first_index]['iconclass'] = $iconclass;
                            $menu_list[$menu_first_index]['secondmenu'][$menu_second_index]['moduleid'] = $menu_second['moduleid'];
                            $menu_list[$menu_first_index]['secondmenu'][$menu_second_index]['mname'] = $menu_second['m_cname'];
                            $menu_list[$menu_first_index]['secondmenu'][$menu_second_index]['url'] = base_url($menu_second['m_controller']);
                        } else {
                            unset($menu_second_list[$menu_second_index]);
                        }// end menu_third_list

                    }
                }// end menu_second_list
                
            }
        }// end menu_first_list

        return $menu_list;

    }

    /**
     * 获取顶部菜单根据role_id
     * @param $role_id
     * @return void
     **/
    public function getTopMenuByRoleId($role_id)
    {
        $menu_list = [];
        $secondmenu = [];
        $permissionlist = $this->db
            ->select('module_id')
            ->from($this->permission_tablename)
            ->where('l_role_id', $role_id)
            ->get()->result_array();
        $module_ids = [];
        if ( ! empty($permissionlist)) {
            foreach ($permissionlist as $key => $value) {
                $module_ids = explode(',', $value['module_id']);
            }
        }

        // 一级菜单
        $menu_list_first = $this->db->from($this->menu_tablename)
            ->select('moduleid, m_parentid, m_controller, m_cname')
            ->where_in('moduleid', $module_ids)
            ->where('m_parentid', '0')
            ->where('m_close', 1)
            ->where('is_top', 1)
            ->order_by('i_order', 'asc')
            ->get()
            ->result_array();
        if ( ! empty($menu_list_first)) {
            foreach ($menu_list_first as $menu_first_index => $menu_first) {
                if ($menu_first['m_cname'] == "权限管理") {
                    $menu_list[$menu_first_index]['iconclass'] = "icon-gerenzhongxin";

                } elseif ($menu_first['m_cname'] == "产品管理") {
                    $menu_list[$menu_first_index]['iconclass'] = "icon-app";
                }

                $menu_list[$menu_first_index]['index'] = $menu_first['moduleid'];
                $menu_list[$menu_first_index]['mname'] = $menu_first['m_cname'];
                // 二级
                $menu_list_second = $this->db->from($this->menu_tablename)
                    ->select('moduleid, m_parentid, m_controller, m_cname')
                    ->where_in('moduleid', $module_ids)
                    ->where('m_parentid', $menu_first['moduleid'])
                    ->where('m_close', 1)
                    ->get()
                    ->result_array();
                 
                if ( ! empty($menu_list_second)) {
                    foreach ($menu_list_second as $menu_second_index => $menu_second) {
                        // 三级
                        $menu_list_third = $this->db->from($this->menu_tablename)
                            ->select('moduleid, m_parentid, m_controller, m_cname, m_directory')
                            ->where_in('moduleid', $module_ids)
                            ->where('m_parentid', $menu_second['moduleid'])
                            ->where('m_close', 1)
                            ->where('is_top', 1)
                            ->get()
                            ->result_array();
                        if ( ! empty($menu_list_third)) {
                            foreach ($menu_list_third as $menu_third_index => $menu_third) {
                                
                                $menu_list[$menu_first_index]['secondmenu'][$menu_third['moduleid']]['index'] = $menu_third['moduleid'];
                                $menu_list[$menu_first_index]['secondmenu'][$menu_third['moduleid']]['mname'] = $menu_third['m_directory'];
                                $menu_list[$menu_first_index]['secondmenu'][$menu_third['moduleid']]['url'] = base_url($menu_third['m_controller']);
                            }
                        }
                    }
                }
            }
        }
        return $menu_list;
    }
    
    /**
     * 通过控制器名称获取moduleid
     * @param $controller_name
     * @return void
     **/
    public function getModuleIdByController($controller_name)
    {
        $list = '';
        $controller_info = $this->db 
            ->select('moduleid')
            ->from($this->menu_tablename)
            ->where('m_controller', $controller_name)
            ->get()
            ->result_array();
        if ( ! empty($controller_info)) {
            foreach ($controller_info as $key => $value) {
                $list = (int)$value['moduleid'];
            }
        }
        return $list;
    }

    /**
     * 检查当前role_id是否设置权限
     * @param $role_id
     * @return void
     **/
    public function getPermissionModuleList($role_id)
    {
        $list = [];
        $permission_list = $this->db 
            ->select('module_id')
            ->from($this->permission_tablename)
            ->where('l_role_id', $role_id)
            ->get()
            ->result_array();
        if ( ! empty($permission_list)) {
            foreach ($permission_list as $key => $value) {
                $list = explode(',', $value['module_id']);
            }
        }
        return $list;

    }


// 2.管理员管理
    /**
     * 获取管理员列表中相关信息
     * @param 
     * @return void
     **/
    public function getManagePageNum($param, $school_id, $limit)
    {
        $map = [];
        $complex = [];
        if ($param) {
            if ($param['close'] != '') {
                $map['is_close'] = $param['close'];
            }

            $complex['name'] = $param['keywords'];
            $complex['phone'] = $param['keywords'];
            $complex['content'] = $param['keywords'];
        }

        if ($school_id != 0) {
            $map['school_id'] = $school_id;
        }

        $count = $this->db 
            ->from($this->admin_tablename)
            ->where($map)
            ->group_start()
                ->or_like($complex)
            ->group_end()
            ->count_all_results();

        $pagenum = (int) ceil( $count / $limit);
        $data = [
            'pagenum' => $pagenum,
            'count' => $count,
        ];

        return $data;

    }

    /**
     * 获取管理员信息
     * @param 
     * @return void
     **/
    public function getManageList($param, $school_id, $start, $limit)
    {
        $map = [];
        $complex = [];
        if ($param) {
            if ($param['close'] != '') {
                $map['is_close'] = $param['close'];
            }

            $complex['name'] = $param['keywords'];
            $complex['phone'] = $param['keywords'];
            $complex['content'] = $param['keywords'];
        }

        if ($school_id != 0) {
            $map['school_id'] = $school_id;
        }

        $manage_list = $this->db
            ->select(
                'admin.*,
                 roles.s_rolename'
            ) 
            ->from("{$this->admin_tablename} as admin")
            ->join("{$this->roles_tablename} as roles", "admin.role_id=roles.l_role_id", "LEFTs")
            ->where($map)
            ->group_start()
                ->or_like($complex)
            ->group_end()
            ->order_by('id', 'desc')
            ->limit($limit, $start)
            ->get()
            ->result_array();
        $original_password = md5("xiha123456");
        if ( ! empty($manage_list)) {
            foreach ($manage_list as $key => $value) {
                if ($value['addtime'] != "" AND $value['addtime'] != 0) {
                    $manage_list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $manage_list[$key]['addtime'] = '--';
                }

                if ($value['password'] != $original_password) {
                    $manage_list[$key]['is_change'] = '1'; // 密码已修改
                } else {
                    $manage_list[$key]['is_change'] = '2'; // 密码未修改
                }

                if ($value['phone'] == '') {
                    $manage_list[$key]['phone'] = '--';
                } 

            }
        }

        return $manage_list;

    }

    /**
     * 获取管理员单条信息
     * @param id
     * @return void
     **/
    public function getManageInfoById($id)
    {
        $list = $this->db 
            ->select(
                'admin.*,
                 roles.s_rolename,
                 roles.l_role_id'
            )
            ->from("{$this->admin_tablename} as admin")
            ->join("{$this->roles_tablename} as roles", "admin.role_id = roles.l_role_id", "LEFT")
            ->where('id', $id)
            ->get()
            ->result_array();
        $managelist = [];
        if ( ! empty($list)) {
            foreach ($list as $key => $value) {
                $managelist = $value;
            }
        }

        return $managelist;
    }

// 3.角色列表
    /**
     * 获取角色页码信息
     * @param
     * @return void
     **/
    public function getRolesPageNum($param, $school_id, $limit)
    {
        $map = [];
        $complex = [];
        if ($param) {
            $complex['s_rolename'] = $param['keywords'];
            $complex['s_description'] = $param['keywords'];
        }

        if ($school_id != 0) {
            $map['owner_id'] = $school_id;
        } 

        $count = $this->db 
            ->from($this->roles_tablename)
            ->where($map)
            ->group_start()
                ->or_like($complex)
            ->group_end()
            ->count_all_results();
        $page_info = [
            'pagenum' => (int) ceil( $count / $limit ),
            'count' => $count
        ];
        return $page_info;
    }

    /**
     * 获取角色列表
     * @param $param, $start, $limit
     * @return void
     **/
    public function getRolesListByCondition($param, $school_id, $start, $limit)
    {
        $map = [];
        $complex = [];
        if ($param) {
            $complex['s_rolename'] = $param['keywords'];
            $complex['s_description'] = $param['keywords'];
        }

        if ($school_id != 0) {
            $map['owner_id'] = $school_id;
        } 

        $role_list = $this->db 
            ->from($this->roles_tablename)
            ->where($map)
            ->group_start()
                ->or_like($complex)
            ->group_end()
            ->limit($limit, $start)
            ->order_by('l_role_id', 'asc')
            ->get()
            ->result_array();
        return $role_list;
    }

    /**
     * 获取菜单列表的展示[角色列表的新增和编辑]
     * @param
     * @return void
     **/
    public function getRoleMenuList($parent_id = 0, $id=0, $map = [])
    {
        $map = [];
        $complex = [];
        if ($id != 0) {
            $permission_arr = $this->getPermissionInfo($id);
        }
        $map['m_parentid'] = $parent_id;
        $map['m_close'] = 1;
        $menulist = $this->db
            ->select('moduleid, m_cname')
            ->from($this->menu_tablename) 
            ->where($map)
            ->order_by('i_order', 'asc')
            ->get()
            ->result_array();
        static $_list = [];
        static $menu = [];
        static $children = [];
        $mod = [];
        if ( ! empty($menulist)) {
            foreach ($menulist as $key => $value) {
                $complex['m_parentid'] = (int)$value['moduleid'];
                $complex['m_close'] = 1;
                $complex['m_type'] = 1;
                $menu = $this->db
                    ->select('moduleid, m_cname')
                    ->from($this->menu_tablename) 
                    ->where($complex)
                    ->order_by('i_order', 'asc')
                    ->get()
                    ->result_array();
                if ( ! empty($menu)) {
                    $menulist[$key]['children'] = $menu;
                    foreach ($menu as $k => $v) {
                        $menulist[$key]['children'][$k]['checkAll'] = false;
                        $menulist[$key]['children'][$k]['isIndeterminate'] = false;
                        $complex['m_parentid'] = (int)$v['moduleid'];
                        $complex['m_close'] = 1;
                        $complex['m_type'] = 2;

                        $menulist[$key]['children'][$k]['checkedMenu'] = [];
                        if ( ! empty($permission_arr)) {
                            $list = $this->db
                                ->select('moduleid')
                                ->from($this->menu_tablename) 
                                ->where($complex)
                                ->where_in('moduleid', $permission_arr)
                                ->order_by('i_order', 'asc')
                                ->get()
                                ->result_array();
                            if ( ! empty($list)) {
                                foreach ($list as $m => $d) {
                                    $mod[] = $d['moduleid'];
                                }
                                $menulist[$key]['children'][$k]['isIndeterminate'] = true;
                                $menulist[$key]['children'][$k]['checkedMenu'] = $mod;
                            }
                        } 

                        $_list = $this->db
                            ->select('moduleid, m_parentid, m_cname, m_type')
                            ->from($this->menu_tablename) 
                            ->where($complex)
                            ->order_by('i_order', 'asc')
                            ->get()
                            ->result_array();
                        if ( ! empty($_list)) {
                            $menulist[$key]['children'][$k]['menu_list'] = $_list;
                        }
                        
                    }
                }
            }
        }
        return $menulist;
    }

    /**
     * 通过l_role_id获取权限表中的信息
     * @param l_role_id
     * @return void
     **/
    public function getPermissionInfo($l_role_id)
    {
        $permission = $this->db 
            ->select('module_id')
            ->from($this->permission_tablename)
            ->where('l_role_id', $l_role_id)
            ->get()
            ->result_array();
        $list = [];
        if ( ! empty($permission)) {
            foreach ($permission as $key => $value) {
                $list = explode(',', $value['module_id']);
            }
        }
        return $list;
    }

    /**
     * 获取所有上级moduleid
     * @param moduleid
     * @return void
     **/
    public function getParentModuleId($moduleid) {
        if(empty($moduleid)) {
            return false;
        }
        static $list = array();
        $menu_id_arr = $this->db 
            ->select('moduleid, m_parentid')
            ->from($this->menu_tablename)
            ->where_in('moduleid', $moduleid)
            ->get()
            ->result_array();
        $parentids = [];
        $moduleids = [];
        if ( ! empty($menu_id_arr)) {
            foreach ($menu_id_arr as $key => $value) {
                $parentids[] = $value['m_parentid'];
                $moduleids[] = $value['moduleid'];

            }
            $list[] = array_values(array_filter($moduleids));
            $this->getParentModuleId($parentids);
        }
        return $list;
    }

    /**
     * 新增角色
     * @param
     * @return void
     **/
    public function _insert($tablename, $data, $wherecondition) {
        $query = $this->db->get_where($tablename, $wherecondition);
        if($query->row_array()) {
            $res = $this->db->update($tablename, $data, $wherecondition);
            return $res;
        }
        $query = $this->db->insert($tablename, $data);
        return $this->db->insert_id();
    }


// 4.菜单
    /**
     * 获取菜单的页码信息
     * @param
     * @return void
     **/
    public function getMenuPageNum($is_close = [1, 2], $parent_id=0, $limit)
    {
        $map = [];
        if ( ! is_array($is_close)) {
            $map['m_parentid'] = $parent_id;
            $map['m_close'] = $is_close;
            $count = $this->db
                ->from($this->menu_tablename) 
                ->where($map)
                ->count_all_results();
        } else {
            $map['m_parentid'] = $parent_id;
            $count = $this->db
                ->from($this->menu_tablename) 
                ->where($map)
                ->where_in('m_close', $is_close)
                ->count_all_results();
        }

        $pageinfo = [
            'pagenum' => (int)ceil( $count / $limit ),
            'count'   => $count
        ];
        return $pageinfo;
    }

     /**
     * 获取菜单列表
     * @param
     * @return void
     **/
    public function getMenuList($is_close = [1, 2], $parent_id = 0, $level=0, $html='—| ', $limit = 100, $start = 0, $m_type = [1, 2])
    {  
        $map = [];
        if ( ! is_array($is_close)) {
            $map['m_parentid'] = $parent_id;
            $map['m_close'] = $is_close;
            $menu_list = $this->db
                ->from($this->menu_tablename) 
                ->where($map)
                ->where_in('m_type', $m_type)
                ->order_by('i_order', 'asc')
                ->limit($limit, $start)
                ->get()
                ->result_array();
            
        } else {
            $map['m_parentid'] = $parent_id;
            $menu_list = $this->db
                ->from($this->menu_tablename) 
                ->where($map)
                ->where_in('m_close', $is_close)
                ->where_in('m_type', $m_type)
                ->order_by('i_order', 'asc')
                ->limit($limit, $start)
                ->get()
                ->result_array();
        }
        
        $list = [];
        static $_list = [];
        static $_data = [];
        if ( ! empty($menu_list)) {
            foreach ($menu_list as $key => $value) {
                if ($value['m_parentid'] == $parent_id) {
                    $value['level'] = $level;
                    $value['html'] = str_repeat($html, $level);
                    $_list[] = $value;
                    if ($value['m_type'] == 2) {
                        $_data[] = $value;
                    }
                    $this->getMenuList($is_close, $value['moduleid'], $level + 1, $html='—| ', $limit = 100, $start = 0, $m_type);
                }

            }
        }
        if(!empty($_list)) {
            foreach ($_list as $key => $value) {
                $_list[$key]['name'] = $value['html'].$value['m_cname'];
                foreach ($_data as $k => $v) {
                    if($v['m_parentid'] == $value['moduleid']) {
                        $_list[$key]['controller'][$k] = $v;
                    } else {
                        if($value['m_parentid'] == 0) {
                            $_list[$key]['controller'] = [];
                        }
                    }
                }	
            }	
        }
        
        return $_list;

    }

    /**
     * 新增菜单
     * @param
     * @return void
     **/
    public function _insertMenu($tablename, $data) {
        $query = $this->db->insert($tablename, $data);
        return $this->db->insert_id();
    }

    /**
     * 获取单条菜单信息
     * @param 
     * @return void
     **/
    public function getMenuInfoById($id)
    {
        $menu_list = $this->db 
            ->from($this->menu_tablename)
            ->where('moduleid', $id)
            ->get()
            ->result_array();
        $list = [];
        if ( ! empty($menu_list)) {
            foreach ($menu_list as $key => $value) {
                $list = $value;
            }
        }
        return $list;
    }

   
    /**
     * 获取当前父级下的所有子集
     * @param $parent_id
     * @return void
     **/
    public function getAllMenuIds($parent_id)
    {
        $module_ids = $this->db 
            ->from($this->menu_tablename)
            ->where('m_parentid', $parent_id)
            ->select('moduleid')
            ->get()
            ->result_array();
        static $_list = [];
        $_list[] = $parent_id;
        if ( ! empty($module_ids)) {
            foreach ($module_ids as $key => $value) {
                $this->getAllMenuIds($value['moduleid']);
            }
        }
        return $_list;
    }

    // 获取条件下的数据页码和总数（通用）
    public function getManagePageNumByCondition($tablename, $school_id, $wherecondition, $limit) {
        if($school_id == 0) {
            $count = $this->db->count_all_results($tablename);
        } else {
            $count = $this->db->where($wherecondition)->count_all_results($tablename);
        }
        return ['pn'=>(int) ceil($count / $limit), 'count'=>$count];
    }

    // 获取所有分类
    public function getAllMenuList() {
        // 获取一级分类
        $leve1_menu_list = $this->getLevelMenuList();

        // 获取二级分类
        foreach ($leve1_menu_list as $key => $value) {
            $value->children = $this->getLevelMenuList($value->moduleid);
            if($value->children) {
                foreach ($value->children as $k => $v) {
                    // 获取操作
                    $value->children[$k]->menu_list = $this->getLevelMenuList($v->moduleid, 1, 2);
                    $value->children[$k]->checkAll = false;
                    $value->children[$k]->checkedMenu = [];
                    $value->children[$k]->isIndeterminate = false;
                }
            } else {
                    $value->children->menu_list = [];     
                    $value->children->checkAll = false;
                    $value->children->checkedMenu = [];
                    $value->children->isIndeterminate = false;           
            }
        }
        return $leve1_menu_list;
    }

    // 获取各级分类 m_type： 1:模块 2:操作 
    public function getLevelMenuList($parent_id=0, $is_close=1, $type=1) {
        $query = $this->db->select('moduleid, m_cname, m_parentid, m_type')
            ->where(['m_parentid'=>$parent_id, 'm_close'=>$is_close, 'm_type'=>$type])
            ->order_by('i_order', 'ASC')->get($this->menu_tablename);
        return $query->result();
    }

    // 根据操作ID获取所有上级ID
    public function getParentMenuModuleId($module_ids) {
        static $m_parentid = [];
        $query = $this->db->select('m_parentid')->where_in('moduleid', $module_ids)->get($this->menu_tablename);
        if($query->result()) {
            foreach ($query->result() as $key => $value) {
                $m_parentid[] = $value->m_parentid;
            }
            if(!in_array(0, $m_parentid)) {
                $this->getParentMenuModuleId(array_unique($m_parentid));
            }
            foreach (array_unique($m_parentid) as $key => $value) {
                if($value == 0) {
                    unset($m_parentid[$key]);
                }
            }
            return array_merge($module_ids, array_unique($m_parentid));
        }
    }

    // 新建管理员
    public function insertManageInfo($data, $wherecondition) {
        $query = $this->db->where($wherecondition)->get($this->admin_tablename);
        if($query->row_array()) {
            return false;
        }
        $query = $this->db->insert($this->admin_tablename, $data);
        return $this->db->insert_id();  
    }

    // 根据moduleID获取菜单数据
    public function getMenuListByCondition($wherecondition) {
        $query = $this->db->select('module_id')->where($wherecondition)->get($this->permission_tablename);
        $module_ids = $query->row_array();
        if($module_ids) {
            $module_ids_arr = explode(',', $module_ids['module_id']);
            $query = $this->db->select('moduleid, m_cname, m_parentid, m_type')->where_in('moduleid', $module_ids_arr)->get($this->menu_tablename);
            $menu_list = $this->sortMenuList($query->result());
            return ['list'=> $menu_list];
        } else {
            return ['list'=>[]];
        }
    }

    /**
     * 设置关闭的状态
     * @param 
     * @return void
     **/
    public function setCloseStatus($ids, $status)
    {
        $data = ['m_close' => $status];
        $result = $this->db 
            ->where_in('moduleid', $ids)
            ->update($this->menu_tablename, $data);
        return $result;
    }

    /**
     * 设置顶部展示状态
     * @param 
     * @return void
     **/
    public function showTopStatus($id, $status)
    {
        $data = ['is_top' => $status];
        $result = $this->db 
            ->where('moduleid', $id)
            ->update($this->menu_tablename, $data);
        return $result;
    }

    // 排序菜单数组
    public function sortMenuList($obj) {
        static $_pcate_list = [];
        static $_cate_list = [];
        static $_menu_list = [];
        static $_list = [];
        foreach ($obj as $key => $value) {
            if($value->m_parentid == 0) {
                $_pcate_list[$key] = $value;
            } elseif($value->m_parentid != 0) {
                if($value->m_type == 1) {
                    $_cate_list[$key] = $value;
                } else {
                    $_menu_list[$key] = $value;
                }            
            } else {
                continue;
            }
        }
        if(!empty($_pcate_list) && !empty($_cate_list) && !empty($_menu_list)) {
            foreach ($_pcate_list as $key => $value) {
                $_list[$key] = $value;
                foreach ($_cate_list as $k => $val) {
                    if($val->m_parentid == $value->moduleid) {
                        $_list[$key]->children[$k] = $val;
                        foreach ($_menu_list as $index => $item) {
                            if($item->m_parentid == $val->moduleid) {
                                $_list[$key]->children[$k]->menu_list[] = $item;
                            }
                        }
                    }
                }
            }
        }
        $_list = array_values($_list);
        foreach ($_list as $key => $value) {
            $_list[$key]->children = array_values($value->children);
        }
        return $_list;
    }

    /**
     * 获取角色列表
     * @param   role_id
     * @param   school_id
     * @return  rolelist
     **/
    public function getRoleList($role_id, $school_id)
    {
        if ($school_id == NULL) 
        {
            $school_id = 0;
        }

        $map = [];
        if ($role_id != 1 AND $school_id == 0) 
        {
            $map['roles.l_role_id'] = $role_id;
        } 
        else if ($role_id != 1 AND $school_id != 0) 
        {
            $map['admin.school_id'] = $school_id;
            $map['roles.l_role_id'] = $role_id;
        }

        if ( empty ($map)) 
        {
            $rolelist = $this->db
                ->select(
                    'l_role_id,
                    s_rolename'
                )
                ->from($this->roles_tablename)
                ->get()
                ->result_array();

        } 
        else 
        {
            $rolelist = $this->db
                ->select(
                    'l_role_id,
                    s_rolename'
                )
                ->from("{$this->roles_tablename} as roles")
                ->join("{$this->admin_tablename} as admin", "roles.l_role_id = admin.role_id ", "LEFT")
                ->where($map)
                ->distinct('l_role_id')
                ->get()
                ->result_array();

        }
        
        return $rolelist;

    }



}