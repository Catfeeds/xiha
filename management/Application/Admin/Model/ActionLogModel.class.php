<?php
namespace Admin\Model;
use Think\Model;
use Think\Page;

class ActionLogModel extends BaseModel {

// 系统行为的列表展示
	/**
     * 系统行为的列表展示
     *
     * @return  void
     * @author  wl
     * @date    Nov 15, 2016
     **/
    public function getSystemAction () {
        $count = $this->table(C('DB_PREFIX').'action')
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $systemActionLists = array();
        $systemActionList = $this->table(C('DB_PREFIX').'action')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('id DESC')
            ->fetchSql(false)
            ->select();
        if ($systemActionList) {
            foreach ($systemActionList as $key => $value) {
            	if ($value['add_time'] != 0) {
                    $systemActionList[$key]['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
                } else {
                    $systemActionList[$key]['add_time'] = '';
                }

                if ($value['update_time'] != 0) {
                    $systemActionList[$key]['update_time'] = date('Y-m-d H:i:s', $value['update_time']);
                } else {
                    $systemActionList[$key]['update_time'] = '';
                }

            }
        }
        $systemActionLists = array('systemActionList' => $systemActionList, 'count' => $count, 'page' => $page);
        return $systemActionLists;
    }

    /**
     * 系统行为的搜索
     *
     * @return  void
     * @author  wl
     * @date    Nov 15, 2016
     **/
    public function searchSystemAction ($param) {
    	$map = array();
        $complex = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
    	if ($param['action_info'] == '') {
            $complex['name'] = array('LIKE', $s_keyword);
            $complex['title'] = array('LIKE', $s_keyword);
    		$complex['_logic'] = 'OR';
    	} else {
            $complex[$param['action_info']] = array("LIKE", $s_keyword);
        }
        $map['_complex'] = $complex;
        $count = $this->table(C('DB_PREFIX').'action')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $systemActionLists = array();
        $systemActionList = $this->table(C('DB_PREFIX').'action')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->where($map)
            ->order('id DESC')
            ->fetchSql(false)
            ->select();
        if ($systemActionList) {
            foreach ($systemActionList as $key => $value) {
            	if ($value['add_time'] != 0) {
                    $systemActionList[$key]['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
                } else {
                    $systemActionList[$key]['add_time'] = '';
                }

                if ($value['update_time'] != 0) {
                    $systemActionList[$key]['update_time'] = date('Y-m-d H:i:s', $value['update_time']);
                } else {
                    $systemActionList[$key]['update_time'] = '';
                }

            }
        }
        $systemActionLists = array('systemActionList' => $systemActionList, 'count' => $count, 'page' => $page);
        return $systemActionLists;
    }

    /**
     * 通过行为唯一标识来检测是否存在
     *
     * @return 	void
     * @author 	wl
     * @date	Nov 15, 2016
     **/
    public function checkActionInfo ($name) {
    	if (!trim($name)) {
    		return false;
    	}
    	$check_info = $this->table(C('DB_PREFIX').'action')
    		->fetchSql(false)
    		->where(array('name' => $name))
    		->find();
		if (!empty($check_info)) {
			return $check_info;
		} else {
			array();
		}
    }
    
    /**
     * 通过系统行为id来获取系统行为信息
     *
     * @return 	void
     * @author 	wl
     * @date	Nov 15, 2016
     **/
    public function getActionInfoById ($id) {
    	if (!is_numeric($id)) {
    		return false;
    	}
    	$check_info = $this->table(C('DB_PREFIX').'action')
    		->fetchSql(false)
    		->where(array('id' => $id))
    		->find();
		if (!empty($check_info)) {
			return $check_info;
		} else {
			array();
		}
    }
    /**
	* 设置系统行为的状态
	*
	* @return  void
	* @author  wl
	* @date    Nov 15, 2016
	**/
	public function setSystemActionStatus ($id, $status) {
		if (!$id) {
			return false;
		}
		$list = array();
		$data = array('status' => $status);
		$result = M('action')->where('id = :cid')
			->bind(['cid' => $id])
			->fetchSql(false)
			->data($data)
			->save();
		$list['status']	= $result;
		$list['id']		= $id;
		return $list;
	}

	/**
	 * 删除单条系统行为
	 *
	 * @return 	void
	 * @author 	wl
	 * @date 	Nov 15, 2016
	 **/
	public function delSystemAction ($id) {
		if (!is_numeric($id)) {
			return false;
		}
		$result = $this->table(C('DB_PREFIX').'Action')
			->where(array('id' => $id))
			->fetchSql(false)
			->save(array('status' => 2));
			// ->delete();
		return $result;
	}

	/**
	 * 删除多条系统行为
	 *
	 * @return 	void
	 * @author 	wl
	 * @date 	Nov 15, 2016
	 **/
	public function delSystemActions ($id_arr) {
		if (empty($id_arr)) {
			return false;
		}
		$result = $this->table(C('DB_PREFIX').'Action')
			->where(array('id' => array('in', $id_arr)))
			->fetchSql(false)
			->save(array('status' => 2));
			// ->delete();
		return $result;
	}
	/**
	 * 恢复多条系统行为
	 *
	 * @return 	void
	 * @author 	wl
	 * @date 	Nov 15, 2016
	 **/
	public function recoverSystemActions ($id_arr) {
		if (empty($id_arr)) {
			return false;
		}
		$result = $this->table(C('DB_PREFIX').'Action')
			->where(array('id' => array('in', $id_arr)))
			->fetchSql(false)
			->save(array('status' => 1));
			// ->delete();
		return $result;
	}
	
// 行为日志模块
	/**
	 * 根据不同的登陆者id获取不同的列表
	 *
	 * @return 	void
	 * @author 	wl
     * @date    Sep 13, 2016
     * @update  Nov 21, 2016
	 * @update 	Nov 25, 2016
	 **/
    public function getActionLogList () {
            $count = $this->table(C('DB_PREFIX').'action_log al')
                ->join(C('DB_PREFIX').'action ac ON ac.id = al.action_id', 'LEFT')
                ->join(C('DB_PREFIX').'admin a ON a.id = al.user_id', 'LEFT')
                ->join(C('DB_PREFIX').'roles r ON r.l_role_id = a.role_id', 'LEFT')
                // ->where(array('al.status' => 1))
                ->count();
            $Page = new Page($count, 10);
            $page = $this->getPage($count, 10);
            $action_infos = array();
            $action_info = $this->table(C('DB_PREFIX').'action_log al')
                ->join(C('DB_PREFIX').'action ac ON ac.id = al.action_id', 'LEFT')
                ->join(C('DB_PREFIX').'admin a ON a.id = al.user_id', 'LEFT')
                ->join(C('DB_PREFIX').'roles r ON r.l_role_id = a.role_id', 'LEFT')
                // ->where(array('al.status' => 1))
                ->field('al.*, a.id as uid, a.content, ac.id as acid, ac.title, ac.name, r.l_role_id, r.s_rolename')
                ->order('al.id DESC')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();
            if ($action_info) {
                foreach ($action_info as $key => $value) {
                    if ($value['create_time'] != 0) {
                        $action_info[$key]['create_time'] = date('Y-m-d H:i:s', $value['create_time']);
                    } else {
                        $action_info[$key]['create_time'] = '';
                    }
                }
            }
            $action_infos = array('action_info' => $action_info, 'page' => $page, 'count' => $count);
            return $action_infos;
    }

    /**
     * 获取角色类别role表中
     *
     * @return  void
     * @author  wl
     * @date    Nov 25, 2016
     **/
    public function getRoleList () {
        $rolelist = $this->table(C('DB_PREFIX').'roles r')
            ->fetchSql(false)
            ->field('l_role_id, s_rolename')
            ->select();
        if ($rolelist) {
            return $rolelist;
        } else {
            return array();
        }
    }

	/**
	 * 根据搜索登陆者的相关日志
	 *
	 * @return 	void
	 * @author 	wl
     * @date    Sep 13, 2016
     * @update  Nov 21, 2016
     * @update  Nov 25, 2016
	 **/
	public function searchActionLogList ($param) {
        $map = array();
        $complex = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_info'] == '') {
            $complex['al.id'] = array('EQ', $param['s_keyword']);
            $complex['a.content'] = array('like', $s_keyword);
            $complex['ac.title'] = array('like', $s_keyword);
            $complex['_logic'] = 'OR';
        } else {
            if ($param['search_info'] == 'id') {
                $param['search_info'] = 'al.id';
                $complex[$param['search_info']] = array('eq', $param['s_keyword']);
            }
            if ($param['search_info'] == 'content') {
                $param['search_info'] = 'a.content';
                $complex[$param['search_info']] = array('like', $param['s_keyword']);
            }
            if ($param['search_info'] == 'title') {
                $param['search_info'] = 'ac.title';
                $complex[$param['search_info']] = array('like', $param['s_keyword']);
            }
        }
        $map['_complex'] = $complex;
        if ($param['role_id'] != '') {
            $map['r.l_role_id'] = array('eq', $param['role_id']);
        }
        $count = $this->table(C('DB_PREFIX').'action_log al')
            ->join(C('DB_PREFIX').'action ac ON ac.id = al.action_id', 'LEFT')
            ->join(C('DB_PREFIX').'admin a ON a.id = al.user_id', 'LEFT')
            ->join(C('DB_PREFIX').'roles r ON r.l_role_id = a.role_id', 'LEFT')
            // ->where(array('al.status' => 1))
            ->fetchSql(false)
            ->where($map)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $action_infos = array();
        $action_info = $this->table(C('DB_PREFIX').'action_log al')
            ->join(C('DB_PREFIX').'action ac ON ac.id = al.action_id', 'LEFT')
            ->join(C('DB_PREFIX').'admin a ON a.id = al.user_id', 'LEFT')
            ->join(C('DB_PREFIX').'roles r ON r.l_role_id = a.role_id', 'LEFT')
            // ->where(array('al.status' => 1))
            ->where($map)
            ->field('al.*, a.id as uid, a.content, ac.id as acid, ac.title, ac.name, r.l_role_id, r.s_rolename')
            ->order('al.id DESC')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        if ($action_info) {
            foreach ($action_info as $key => $value) {
                if ($value['create_time'] != 0) {
                    $action_info[$key]['create_time'] = date('Y-m-d H:i:s', $value['create_time']);
                } else {
                    $action_info[$key]['create_time'] = '';
                }
            }
        }
        $action_infos = array('action_info' => $action_info, 'page' => $page, 'count' => $count);
        return $action_infos;
       
	}
	/**
	* 设置行为日志的状态
	*
	* @return  void
	* @author  wl
	* @date    Sep 19, 2016
	**/
	public function setActionLogStatus ($id, $status) {
		if (!$id) {
			return false;
		}
		$list = array();
		$data = array('status' => $status);
		$result = M('action_log')->where('id = :cid')
			->bind(['cid' => $id])
			->fetchSql(false)
			->data($data)
			->save();
		$list['status']	= $result;
		$list['id']			= $id;
		return $list;
	}
	/**
	 * 删除单条日志记录
	 *
	 * @return 	void
	 * @author 	wl
	 * @date 	Sep 13, 2016
	 **/
	public function delActionLog ($id) {
		if (!is_numeric($id)) {
			return false;
		}
		$result = $this->where(array('id' => $id))
						->fetchSql(false)
						->save(array('status' => 2));
						// ->delete();
		return $result;
	}

	/**
	 * 删除多条日志记录
	 *
	 * @return 	void
	 * @author 	wl
	 * @date 	Sep 13, 2016
	 **/
	public function delActionLogs ($id_arr) {
		if (empty($id_arr)) {
			return false;
		}
		$result = $this->where(array('id' => array('in', $id_arr)))
						->fetchSql(false)
						->save(array('status' => 2));
						// ->delete();
		return $result;
	}























	
}
?>