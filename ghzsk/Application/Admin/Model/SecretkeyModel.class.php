<?php
/**
* 车载注册模块
* @author  wl
* @date    August 16, 2016
**/
namespace Admin\Model;
use Think\Model;
use Think\Page;

class SecretkeyModel extends BaseModel {
   	private $_link = array(
	);
   	public $tableName = 'secretkey'; 
   	/**
   	 * 获取车载注册列表
   	 *
   	 * @return 	void
   	 * @author 	wl
   	 * @date 	August 15, 2016
   	 **/
	public function getSecretKeyList() {
			$count = $this->field('id')
						->count();
			$Page = new Page($count, 10);
			$page = $this->getPage($count, 10);
			$list = array();	
			$secretkey_list = $this->fetchSql(false)
								->order('id ')
								->limit($Page->firstRow.','.$Page->listRows)
								->select();
			if ($secretkey_list) {
        foreach ($secretkey_list as $key => $value) {
          if ($value['addtime'] != 0) {
            $secretkey_list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
          }
          if ($value['register_time'] != 0) {
            $secretkey_list[$key]['register_time'] = date('Y-m-d H:i:s', $value['register_time']);
          }
          if ($value['expire_time'] != 0) {
            $secretkey_list[$key]['expire_time'] = date('Y-m-d H:i:s', $value['expire_time']);
          }

          if ($value['system_type'] == 1) {
            $secretkey_list[$key]['system_type'] = '新系统';
          } else {
            $secretkey_list[$key]['system_type'] = '旧系统';
          }

          if ($value['register_type'] == 1) {
            $secretkey_list[$key]['register_type'] = '车载';
          } else if ($value['register_type'] == 2) {
            $secretkey_list[$key]['register_type'] = '调用中心';
          } else if ($value['register_type'] == 3) {
            $secretkey_list[$key]['register_type'] = '接口';
          } else {
            $secretkey_list[$key]['register_type'] = '管理软件';
          }
        }
      }
			$list = array('list'=>$secretkey_list, 'page'=>$page, 'count'=>$count);
			return $list;
	}

	/**
   	 * 搜索车载信息
   	 *
   	 * @return 	void
   	 * @author 	wl
   	 * @date 	  August 16, 2016
   	 **/
    public function searchSecretkey($param){
        $map = array();
        $nowts = time();
        if (isset($param['day_number']) && intval($param['day_number']) > 0 ) {
          $map['expire_time'] = array(array('gt', $nowts ), array('lt', intval($param['day_number']) * 24 * 3600 + $nowts), 'and');
        } else {
          $map['expire_time'] = array('elt', $nowts);
        }
        $map['school_name'] =  array('like', '%'.$param['school_name'].'%');
        $count = $this->where($map)->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $list = array();
        $secretkey_list = $this->where($map)
                              ->fetchSql(false)
                              ->order('id')
                              ->limit($Page->firstRow.','.$Page->listRows)
                              ->select();
        if ($secretkey_list) {
          foreach ($secretkey_list as $key => $value) {
            if ($value['addtime'] != 0) {
              $secretkey_list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
            }
            if ($value['register_time'] != 0) {
              $secretkey_list[$key]['register_time'] = date('Y-m-d H:i:s', $value['register_time']);
            }
            if ($value['expire_time'] != 0) {
              $secretkey_list[$key]['expire_time'] = date('Y-m-d H:i:s', $value['expire_time']);
            }

            if ($value['system_type'] == 1) {
              $secretkey_list[$key]['system_type'] = '新系统';
            } else {
              $secretkey_list[$key]['system_type'] = '旧系统';
            }

            if ($value['register_type'] == 1) {
              $secretkey_list[$key]['register_type'] = '车载';
            } else if ($value['register_type'] == 2) {
              $secretkey_list[$key]['register_type'] = '调用中心';
            } else if ($value['register_type'] == 3) {
              $secretkey_list[$key]['register_type'] = '接口';
            } else {
              $secretkey_list[$key]['register_type'] = '管理软件';
            }
        }
      }
      $list = array('list'=>$secretkey_list, 'page'=>$page, 'count'=>$count);
      return $list;        
  }

    /**
   * 根据带过来的id获得相应的值
   *
   * @return  void
   * @author  wl
   * @date    August 16, 2016
   **/
  public function getSecretkeyById ($id) {
    if (!is_numeric($id)) {
      return false;
    }
    $secretkey_list = $this->where('id = :pid')
                ->bind(['pid' => $id])
                ->fetchSql(false)
                ->find();
    $secretkey_list['addtime'] = date('Y-m-d H:i:s', $secretkey_list['addtime']);
    $secretkey_list['register_time'] = date('Y-m-d H:i:s', $secretkey_list['register_time']);
    $secretkey_list['expire_time'] = date('Y-m-d H:i:s', $secretkey_list['expire_time']);
    return $secretkey_list;
  }

    /**
     * 删除车载信息
     *
     * @return  void
     * @author  wl
     * @date    August 16, 2016
     **/
    public function delSecretkey ($id) {
      if (!is_numeric($id)) {
        return false;
      }
      $result = $this->where('id = :sid')
            ->bind(['sid' => $id])
            ->fetchSql(false)
            ->delete();
    return $result;
    }

}

?>