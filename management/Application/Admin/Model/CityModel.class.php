<?php
namespace Admin\Model;
use Think\Model;
use Think\Page;
class CityModel extends BaseModel {
	/**
	 * 获取城市列表
	 *
	 * @return 
	 * @author sun
	 **/
	public function getCityList($province_id) {
		$citylist = $this->where(array('fatherid'=>$province_id))->field('cityid, city')->select();
		return $citylist;
	}

	// 获取城市名称通过城市ID
	public function getCityName ($city_id) {
		$city_name = $this->table(C('DB_PREFIX').'city')
			->where(array('cityid' => $city_id))
			->getField('city');
		return $city_name;
	}

	/**
	 * 获取城市列表的相关信息
	 *
	 * @return 	void
	 * @author 	wl
	 * @date	Jan 03, 2017
	 **/
	public function getCityLists () {
		$citylists = array();
		$count = $this->table(C('DB_PREFIX').'city city')
			->join(C('DB_PREFIX').'province province ON province.provinceid = city.fatherid', 'LEFT')
			->join(C('DB_PREFIX').'area area ON area.fatherid = city.cityid', 'LEFT')
			->where(array('area.id' => array('gt', 0)))
			->fetchSql(false)
			->count();
		$Page = new Page($count, 10);
		$page = $this->getPage($count, 10);
		$citylist = $this->table(C('DB_PREFIX').'city city')
			->field(
				'city.*, 
				 province.provinceid as provinceid, 
				 province.province as province, 
				 area.areaid as areaid, 
				 area.area as area, 
				 area.id as area_id'
			)
			->join(C('DB_PREFIX').'province province ON province.provinceid = city.fatherid', 'LEFT')
			->join(C('DB_PREFIX').'area area ON area.fatherid = city.cityid', 'LEFT')
			->where(array('area.id' => array('gt', 0)))
			->order('is_hot ASC, area.id DESC')
			->limit($Page->firstRow.','.$Page->listRows)
			->fetchSql(false)
			->select();
		$citylists = array('citylist' => $citylist, 'count' => $count, 'page' => $page);
		return $citylists;
	}

	/**
	 * 城市列表中的搜索功能
	 *
	 * @return 	void
	 * @author 	wl
	 * @date	Jan 03, 2017
	 **/
	public function searchCityLists ($param) {
		$map = array();
		$complex = array();
		$s_keyword = '%'.$param['s_keyword'].'%';
		if ($param['search_info'] == '') {
			$complex['area.id'] = array('LIKE', $s_keyword);
			$complex['city.id'] = array('LIKE', $s_keyword);
			$complex['province'] = array('LIKE', $s_keyword);
			$complex['city'] = array('LIKE', $s_keyword);
			$complex['area'] = array('LIKE', $s_keyword);
			$complex['_logic'] = 'OR';

		} else {
			if ($param['search_info'] == 'id') {
				$param['search_info'] = 'city.id';
				$complex[$param['search_info']] = array('eq', $s_keyword);
			}
			if ($param['search_info'] == 'area_id') {
				$param['search_info'] = 'area.id';
				$complex[$param['search_info']] = array('eq', $s_keyword);
			}
			$complex[$param['search_info']] = array('LIKE', $s_keyword);
		}
		$map['area.id'] = array('gt', 0);
		$map['_complex'] = $complex;
		$citylists = array();
		$count = $this->table(C('DB_PREFIX').'city city')
			->join(C('DB_PREFIX').'province province ON province.provinceid = city.fatherid', 'LEFT')
			->join(C('DB_PREFIX').'area area ON area.fatherid = city.cityid', 'LEFT')
			->where($map)
			->fetchSql(false)
			->count();
		$Page = new Page($count, 10, $param);
		$page = $this->getPage($count, 10, $param);
		$citylist = $this->table(C('DB_PREFIX').'city city')
			->field(
				'city.*, 
				 province.provinceid as provinceid, 
				 province.province as province, 
				 area.areaid as areaid, 
				 area.area as area, 
				 area.id as area_id'
			)
			->join(C('DB_PREFIX').'province province ON province.provinceid = city.fatherid', 'LEFT')
			->join(C('DB_PREFIX').'area area ON area.fatherid = city.cityid', 'LEFT')
			->where($map)
			->order('is_hot ASC, area.id DESC')
			->limit($Page->firstRow.','.$Page->listRows)
			->fetchSql(false)
			->select();
		$citylists = array('citylist' => $citylist, 'count' => $count, 'page' => $page);
		return $citylists;
	}

	/**
	 * 设置城市的热门状态
	 *
	 * @return 	void
	 * @author 	wl
	 * @date	Jan 03, 2016
	 **/
	public function setHotCity ($id, $status) {
		if (!is_numeric($id) && !is_numeric($status)) {
			return false;
		}
		$list = array();
		$data = array('is_hot' => $status);
		$result = M('city')->where(array('id' => $id))
			->data($data)
			->save();
		$list['id'] = $id;
		$list['res'] = $result;
		return $list;
	}

	/**
	 * 检查地区有无重复
	 *
	 * @return 	void
	 * @author 	wl
	 * @date	Jan 03, 2016
	 **/
	public function checkArea ($provinceid, $cityid, $areaid) {
		$arealist = $this->table(C('DB_PREFIX').'city c')
			->join(C('DB_PREFIX').'province p ON p.provinceid = c.fatherid', 'LEFT')
			->join(C('DB_PREFIX').'area a ON a.fatherid = c.cityid', 'LEFT')
			->where(array(
					'p.provinceid' => $provinceid,
					'c.cityid' => $cityid,
					'a.areaid' => $areaid,
				))
			->find();
		if (!empty($arealist)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 检查省份是否存在
	 *
	 * @return 	void
	 * @author 	wl
	 * @date	Jan 03, 2016
	 **/
	public function checkProvince ($provinceid) {
		$provincelist = $this->table(C('DB_PREFIX').'province')
			->where(array('provinceid' => $provinceid))
			->find();
		if (!empty($provincelist)) {
			return $provincelist;
		} else {
			return array();
		}
	}

	/**
	 * 检查省份是否存在
	 *
	 * @return 	void
	 * @author 	wl
	 * @date	Jan 03, 2016
	 **/
	public function checkCity ($cityid) {
		$citylist = $this->table(C('DB_PREFIX').'city')
			->where(array('cityid' => $cityid))
			->find();
		if (!empty($citylist)) {
			return $citylist;
		} else {
			return array();
		}
	}

	/**
	 * 根据id获取城市的信息
	 *
	 * @return 	void
	 * @author 	wl
	 * @date	Jan 03, 2016
	 **/
	public function getCityListById ($area_id, $city_id) {
		if (!is_numeric($area_id) && !is_numeric($city_id)) {
			return false;
		}
		$citylist = $this->table(C('DB_PREFIX').'city c')
			->join(C('DB_PREFIX').'province p ON p.provinceid = c.fatherid', 'LEFT')
			->join(C('DB_PREFIX').'area a ON a.fatherid = c.cityid', 'LEFT')
			->where(array(
					'c.id' => $city_id,
					'a.id' => $area_id,
				))
			->field('c.*, p.province, p.provinceid, p.id pid, a.area, a.areaid, a.id aid')
			->find();
		if (!empty($citylist)) {
			return $citylist;
		} else {
			return array();
		}
	}

	/**
	 * 删除城市
	 *
	 * @return 	void
	 * @author 	wl
	 * @date	Jan 03, 2017
	 **/
	public function delCity ($id) {
		if (!is_numeric($id)) {
			return false;
		}
		$result = M('area')
			->where(array('id' => $id))
			->delete();
		return $result;
	}






}