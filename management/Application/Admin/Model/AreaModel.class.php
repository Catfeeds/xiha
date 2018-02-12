<?php
namespace Admin\Model;
use Think\Model;

class AreaModel extends BaseModel {
	/**
	 * 获取区域列表
	 *
	 * @return 
	 * @author sun
	 **/
	public function getAreaList($city_id) {
		$arealist = $this->where(array('fatherid'=>$city_id))->field('areaid, area')->select();
		return $arealist;
	}

	// 获取区域名称通过区域ID
	public function getAreaName ($area_id) {
		$area_name = $this->table(C('DB_PREFIX').'area')
			->where(array('areaid' => $area_id))
			->getField('area');
		return $area_name;
	}

}