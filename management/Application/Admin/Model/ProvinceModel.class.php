<?php
namespace Admin\Model;
use Think\Model;

class ProvinceModel extends BaseModel {
	/**
	 * 获取省份列表
	 *
	 * @return 
	 * @author sun
	 **/
	public function getProvinceList() {
		$provincelist = $this->field('province, provinceid')->select();
		return $provincelist;
	}

	// 获取省份名称通过省份ID
	public function getProvinceName ($province_id) {
		$province_name = $this->table(C('DB_PREFIX').'province')
			->where(array('provinceid' => $province_id))
			->getField('province');
		return $province_name;
	}
}