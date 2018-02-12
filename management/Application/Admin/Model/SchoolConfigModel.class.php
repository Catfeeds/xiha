<?php
namespace Admin\Model;
use Think\Model;
use Think\Page;

class SchoolConfigModel extends BaseModel {
	
	/**
	 * 获取驾校时间配置列表
	 *
	 * @return 	void
	 * @author 	wl
	 * @date 	Sep 18, 2016
	 **/
	public function getSchoolSysconfig ($school_id) {
		$school_confs = array();
		if ($school_id == 0) {
			$count = $this->table(C('DB_PREFIX').'school_config sc')
				->join(C('DB_PREFIX').'school s ON s.l_school_id = sc.l_school_id', 'LEFT')
				->where(array('s.l_school_id' => array('gt', 0)))
				->count();
			$Page = new Page($count, 10);
			$page = $this->getPage($count, 10);
			$school_conf = $this->table(C('DB_PREFIX').'school_config sc')
				->join(C('DB_PREFIX').'school s ON s.l_school_id = sc.l_school_id', 'LEFT')
				->limit($Page->firstRow.','.$Page->listRows)
				->where(array('s.l_school_id' => array('gt', 0)))
				->field('sc.*, s.l_school_id, s.s_school_name')
				->order('sc.l_school_id DESC')
				->fetchSql(false)
				->select();
		} else{
			$count = $this->table(C('DB_PREFIX').'school_config sc')
				->join(C('DB_PREFIX').'school s ON s.l_school_id = sc.l_school_id', 'LEFT')
				->where('sc.l_school_id = :sid')
				->bind(['sid' => $school_id])
				->where(array('s.l_school_id' => array('gt', 0)))
				->count();
			$Page = new Page($count, 10);
			$page = $this->getPage($count, 10);
			$school_conf = $this->table(C('DB_PREFIX').'school_config sc')
				->join(C('DB_PREFIX').'school s ON s.l_school_id = sc.l_school_id', 'LEFT')
				->where(array('s.l_school_id' => array('gt', 0)))
				->where('sc.l_school_id = :sid')
				->bind(['sid' => $school_id])
				->limit($Page->firstRow.','.$Page->listRows)
				->field('sc.*, s.l_school_id, s.s_school_name')
				->order('sc.l_school_id DESC')
				->fetchSql(false)
				->select();
		}
		if (!$school_conf) {
			return array();
		}
		$school_confs = array('school_conf' => $school_conf, 'page' => $page, 'count' => $count);
		return $school_confs;
	}
	/**
	 * 搜索驾校时间配置列表
	 *
	 * @return 	void
	 * @author 	wl
	 * @date 	Sep 18, 2016
	 **/
	public function searchSchoolConfig ($param, $school_id) {
		$school_confs = array();
		$map = array();
		if ($param['s_keyword'] != '') {
			$map['s.s_school_name'] = array('like', '%'.$param['s_keyword'].'%');
		}

		// if ($param['is_automatic'] != 0) {
		// 	$map['is_automatic'] = array( 'EQ', $param['is_automatic']);
		// }

		if ($school_id == 0) {
			$count = $this->table(C('DB_PREFIX').'school_config sc')
				->join(C('DB_PREFIX').'school s ON s.l_school_id = sc.l_school_id', 'LEFT')
				->where(array('s.l_school_id' => array('gt', 0)))
				->where($map)
				->fetchSql(false)
				->count();
			$Page = new Page($count, 10, $param);
			$page = $this->getPage($count, 10, $param);
			$school_conf = $this->table(C('DB_PREFIX').'school_config sc')
				->join(C('DB_PREFIX').'school s ON s.l_school_id = sc.l_school_id', 'LEFT')
				->where(array('s.l_school_id' => array('gt', 0)))
				->where($map)
				->limit($Page->firstRow.','.$Page->listRows)
				->field('sc.*, s.l_school_id, s.s_school_name')
				->order('sc.l_school_id DESC')
				->fetchSql(false)
				->select();
		} else{
			$count = $this->table(C('DB_PREFIX').'school_config sc')
				->join(C('DB_PREFIX').'school s ON s.l_school_id = sc.l_school_id', 'LEFT')
				->where(array('s.l_school_id' => array('gt', 0)))
				->where($map)
				->where('sc.l_school_id = :sid')
				->bind(['sid' => $school_id])
				->count();
			$Page = new Page($count, 10, $param);
			$page = $this->getPage($count, 10, $param);
			$school_conf = $this->table(C('DB_PREFIX').'school_config sc')
				->join(C('DB_PREFIX').'school s ON s.l_school_id = sc.l_school_id', 'LEFT')
				->where(array('s.l_school_id' => array('gt', 0)))
				->where($map)
				->where('sc.l_school_id = :sid')
				->bind(['sid' => $school_id])
				->limit($Page->firstRow.','.$Page->listRows)
				->field('sc.*, s.l_school_id, s.s_school_name')
				->order('sc.l_school_id DESC')
				->fetchSql(false)
				->select();
		}
		if (!$school_conf) {
			return array();
		}
		$school_confs = array('school_conf' => $school_conf, 'page' => $page, 'count' => $count);
		return $school_confs;
	}

	/**
	 * 通过带过来的school_id获得相应的驾校配置信息
	 *
	 * @return 	void
	 * @author 	wl
	 * @date 	Sep 18, 2016
	 **/
	public function getSchoolSysconfigById ($id) {
		if (!is_numeric($id)) {
			return false;
		}
		$school_conf = $this->table(C('DB_PREFIX').'school_config sc')
			->join(C('DB_PREFIX').'school s ON s.l_school_id = sc.l_school_id', 'LEFT')
			->where('sc.l_school_id = :sid')
			->bind(['sid' => $id])
			->field('sc.*, s.s_school_name')
			->find();
		if ($school_conf) {
			$school_conf['s_time_list_arr'] = explode(',', $school_conf['s_time_list']);
		}
		$list = array();
		$list['school_conf'] = $school_conf;
		$coach_conf = $this->table(C('DB_PREFIX').'coach_time_config')
			->where(array('status' => 1))
			->select();
		if ($coach_conf) {
			foreach ($coach_conf as $key => $value) {
				// var_dump($value);exit;
				$start_minute = $value['start_minute'] == 0 ? '00' : $value['start_minute'];
				$end_minute = $value['end_minute'] == 0 ? '00' : $value['end_minute'];
				$coach_conf[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
				$coach_conf[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;
				if ($school_conf['s_time_list_arr']) {
					if (in_array($value['id'], $school_conf['s_time_list_arr'])) {
						$coach_conf[$key]['is_set'] = 1;
					} else {
						$coach_conf[$key]['is_set'] = 2;
					}
				} else {
					$coach_conf[$key]['is_set'] = 2;
				}
			}
		}
		$list['coach_conf'] = $coach_conf;
		return $list;
	}
	/**
	 * 删除单条驾校配置信息
	 *
	 * @return 	void
	 * @author 	wl
	 * @date 	Sep 18, 2016
	 **/
	public function delSchoolConfig ($id) {
		if (!is_numeric($id)) {
			return false;
		}
		$result = $this->table(C('DB_PREFIX').'school_config')
						->where('l_school_id = :sid')
						->bind(['sid' => $id])
						->fetchSql(true)
						->delete();
		return $result;
	}

	/**
	 * 获得驾校配置中信息
	 *
	 * @return 	void
	 * @author 	wl
	 * @date 	Sep 18, 2016
	 **/
	public function getSchoolConfigId () {
		$school_config_id = $this->table(C('DB_PREFIX').'school_config')
								->field('l_school_id')
								->fetchSql(false)
								->select();
		$school_config_ids = array();
		if ($school_config_id) {
			foreach ($school_config_id as $k => $v) {
				$school_config_ids[] = $v['l_school_id'];
			}
		}
		return $school_config_ids;
	}

	/**
	 * 设置驾校配置信息的自动状态
	 *
	 * @return 	void
	 * @author 	wl
	 * @date 	Sep 18, 2016
	 **/
	public function setSchoolConfStatus ($id,$status){
        if(!$id) {
            return false;
        } 
        $list = array();
        $data =array('is_automatic'=>$status);
        $result = M('school_config')->where(array('l_school_id' => $id))
					            ->data($data) 
					            ->fetchSql(false)
					            ->save();
        $list['is_automatic'] = $result;
        $list['id'] = $id;
        return $list;

    }

}
?>