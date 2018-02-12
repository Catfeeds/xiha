<?php
/**
* 管理员模块
* @author chenxi
**/
namespace Admin\Model;
use Think\Model;
use Think\Page;

class GhzskModel extends BaseModel {
   	private $_link = array(
	);
   	public $tableName = 'post'; 
   	/**
   	 * 获取知识库列表
   	 *
   	 * @return 	void
   	 * @author 	wl
   	 * @date 	August 15, 2016
   	 **/
	public function getAuthorList ($user_id) {
			$count = $this->field('id')
						->count();
			$Page = new Page($count, 10);
			$page = $this->getPage($count, 10);
			$list = array();	
			// var_dump($count);exit;
			$author_list = $this->alias('p')
								->join(C('DB_PREFIX').'category c ON c.id = p.cate_id', 'LEFT')
								->field('p.id,p.*,c.cate_name,c.id cid')
								->order('p.id')
								->limit($Page->firstRow.','.$Page->listRows)
								->fetchSql(false)
								->select();
			if ($author_list) {
				foreach ($author_list as $key => $value) {
					if ($value['modified'] != 0) {
						$author_list[$key]['modified'] = date('Y-m-d H:i:s', $value['modified']);
					}
					$author_list[$key]['created'] = date('Y-m-d H:i:s', $value['created']);
					if ($value['system_type'] == 1) {
						$author_list[$key]['system_type'] = '旧系统'; 
					} else {
						$author_list[$key]['system_type'] = '新系统'; 
					}
					if (!empty($value['attachment']) && isset($value['attachment'])) {
						$author_list[$key]['attachment'] = C('HTTP_HOST').$value['attachment'];
					} else {
						$author_list[$key]['attachment'] = '';
					}

				}
			}
			$list = array('list'=>$author_list, 'page'=>$page, 'count'=>$count);
			// var_dump($list);exit;
			return $list;
	}
	/**
	 * 添加搜索功能/搜索知识主题
	 *
	 * @return 	void
	 * @author 	wl
	 * @date	August 15, 2016
	 * @update	August 18, 2016
	 **/
	public function searchZsk ($param) {
		$map = array();
		if (isset($param['search_type']) && $param['search_type'] != null &&  $param['search_cate'] == null) {
			$map = array($param['search_type'] => array('like', '%'.$param['s_keyword'].'%'));
		} else if (!isset($param['search_type']) && $param['search_type'] == null && !isset($param['search_cate']) && $param['search_cate'] == null) {
			$map['cate_id'] = array('eq', $param['search_cate']);
		} else {
			$map = array($param['search_type'] => array('like', '%'.$param['s_keyword'].'%'), 'cate_id' => $param['search_cate']);
		}

      	$count = $this->where($map)->fetchSql(false)->count();
      	// var_dump($count);exit;
		$Page = new Page($count, 10, $param);
		$page = $this->getPage($count, 10, $param);
		$list = array();
		$author_list = $this->alias('p')
							->join(C('DB_PREFIX').'category c ON c.id = p.cate_id', 'LEFT')
							->where($map)
							->field('p.id,p.*,c.cate_name, c.id cid')
							->order('p.id')
							->limit($Page->firstRow.','.$Page->listRows)
							->fetchSql(false)
							->select();

		if ($author_list) {
			foreach ($author_list as $key => $value) {
				$author_list[$key]['modified'] = date('Y-m-d H:i:s', $value['modified']);
				$author_list[$key]['created'] = date('Y-m-d H:i:s', $value['created']);
				if ($value['system_type'] == 1) {
					$author_list[$key]['system_type'] = '旧系统'; 
				} else {
					$author_list[$key]['system_type'] = '新系统'; 
				}
			}
		}
		$list = array('list'=>$author_list, 'page'=>$page, 'count'=>$count);
      	// var_dump($list);exit;
		return $list;  
	}
	/**
	 * 根据带过来的id获得相应的值
	 *
	 * @return 	void
	 * @author 	wl
	 * @date 	August 15, 2016
	 **/
	public function getZskById ($id) {
		if (!is_numeric($id)) {
			return false;
		}
		$author_list = M('post')->where('id = :pid')
								->bind(['pid' => $id])
								->fetchSql(false)
								->find();
		return $author_list;
	}


	/**
     * 删除用户
     *
     * @return 	void
     * @author 	wl
     * @date 	August 15, 2016
     **/
    public function delZsk ($id) {
    	if (!is_numeric($id)) {
    		return false;
    	}
    	$result = M('post')->where('id = :gid')
						->bind(['gid' => $id])
						->fetchSql(false)
						->delete($id);
		return $result;
    }

    /**
     * 获得category数据表中的问题
     *
     * @return 	void
     * @author 	wl
     * @date 	August 16, 2016; 
     **/
    public function getCategoryList () {
    	$categoryList = M('category')->fetchSql(false)
    						->select(); 
		// var_dump($categoryList);exit;
		return $categoryList;

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