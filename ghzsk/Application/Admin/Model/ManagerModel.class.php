<?php
/**
* 管理员模块
* @author wl
**/
namespace Admin\Model;
use Think\Model;
use Think\Page;

class ManagerModel extends BaseModel {
   	private $_link = array(
	);
   	public $tableName = 'user'; 
   	/**
   	 * 获取用户列表
   	 *
   	 * @return 	void
   	 * @author 	wl
   	 * @date 	August 15, 2016
   	 **/
	public function getUserList() {
			$count = $this->field('id')
						->count();
			$Page = new Page($count, 10);
			$page = $this->getPage($count, 10);
			$list = array();	
			$user_list = $this->fetchSql(false)
								->order('id ')
								->limit($Page->firstRow.','.$Page->listRows)
								->select();
			if ($user_list) {
				foreach ($user_list as $key => $value) {
					$user_list[$key]['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
					if ($value['user_password'] == md5('ghgd2016')) {
						$user_list[$key]['is_change'] = 1; //未修改密码
					} else {
						$user_list[$key]['is_change'] = 2; //已修改密码
					}
				}
			}
			$list = array('list'=>$user_list, 'page'=>$page, 'count'=>$count);
			return $list;
	}

	/**
   	 * 搜索用户
   	 *
   	 * @return 	void
   	 * @author 	wl
   	 * @date 	August 15, 2016
   	 **/
    public function searchMember($param){
      $map = array('user_name'=> array('like', '%'.$param['s_keyword'].'%'));
      $count = $this->where($map)->count();
      $Page = new Page($count, 10, $param);
      $page = $this->getPage($count, 10, $param);
      $list = array();
      $user_list = $this->where($map)
  					   ->limit($Page->firstRow.','.$Page->listRows)
  					   ->order('id') 
					   ->fetchSql(false)
  					   ->select();
      	if ($user_list) {
				foreach ($user_list as $key => $value) {
					$user_list[$key]['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
					if ($value['user_password'] == md5('ghgd2016')) {
						$user_list[$key]['is_change'] = 1; //未修改密码
					} else {
						$user_list[$key]['is_change'] = 2; //已修改密码
					}
				}
			}
		$list = array('list'=>$user_list, 'page'=>$page, 'count'=>$count);
		return $list;         
    }

    /**
   * 根据带过来的id获得相应的值
   *
   * @return  void
   * @author  wl
   * @date  August 15, 2016
   **/
  public function getMemberById ($id) {
    if (!is_numeric($id)) {
      return false;
    }
    $member_list = $this->where('id = :pid')
                ->bind(['pid' => $id])
                ->fetchSql(false)
                ->find();
    return $member_list;
  }

    /**
     * 删除用户
     *
     * @return  void
     * @author  wl
     * @date  August 15, 2016
     **/
    public function delMember ($id) {
      if (!is_numeric($id)) {
        return false;
      }
      $result = $this->where('id = :gid')
            ->bind(['gid' => $id])
            ->fetchSql(false)
            ->delete();
    return $result;
    }


    /**
     * 获得用户的权限列表
     *
     * @return 	void
     * @author 	wl
     * @date 	August 15, 2016; 
     **/
    public function getGroupList ($group_id) {
    	$count = $this->table(C('DB_PREFIX').'group')
    					// ->where(array('id' => $group_id))
    					->count();
		$Page = new Page($count, 10);
		$page = $this->getPage($count, 10);
		$list = array();
    	$group_list = $this->table(C('DB_PREFIX').'group')
    						// ->where(array('id' => $group_id))
    						->select();
		if (!$group_list) {
			return $list = array();
		}
		$list = array('list' => $group_list, 'page' => $page, 'count' => $count);
		return $list;
    }


}

?>