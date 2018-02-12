<?php
/**
 * 首页模型
 */
!defined('IN_FILE') && exit('Access Denied');

class mhome extends mbase {

	/**
	 * 获取分类下的品牌列表
	 * @param int $catid 分类ID
	 * @param int $limit 限制品牌展示数
	 * @param array $brandidarr 限定品牌id
	 * @param int $status 限制是否开启限制品牌展示数状态 0:不开启, 1:开启
	 * @return array 分类ID对应的品牌列表
	 */
	public function getCatBrandList($catid, $status=0, $limit=0, $brandidarr=array()) {
		$catid = intval($catid);
		$sql = "SELECT t.`brand_id` FROM `{$this->_dbtabpre}goods_cat` as c left join `{$this->_dbtabpre}type_brand` as t on t.`type_id`= c.`type_id`  where c.`cat_id` = {$catid} AND c.`disabled` = 'false'";
		$query = $this->_db->query($sql);
		$arr = array();
		$list = array();
		while($row = $this->_db->fetch_array($query)) {
			$arr[] = $row['brand_id'];
		}
		$brandid = implode(',',$arr);
		$brandidlimit = implode(',', $brandidarr);
		$sql = "SELECT `brand_id`, `brand_name`, `brand_url`, `brand_logo` FROM `{$this->_dbtabpre}brand` WHERE `brand_id`";
		!empty($brandidarr) ? $sql .= " IN ({$brandidlimit}) " : $sql .= " IN ({$brandid}) ";
		$sql .= " AND `disabled` = 'false'";
		$status == 0 ? $sql .= '' : $sql .= " LIMIT {$limit}";

		$query = $this->_db->query($sql);
		while($brands = $this->_db->fetch_array($query)) {
			$brand_logo = explode('|',$brands['brand_logo']);
			$list[$brands['brand_id']]['brand_id'] = $brands['brand_id'];
			$list[$brands['brand_id']]['brand_name'] = $brands['brand_name'];
			$list[$brands['brand_id']]['brand_url'] = $brands['brand_url'];
			$list[$brands['brand_id']]['brand_logo'] = $brand_logo[0];
		}
		return $list;
	}

	/**
	 * 获取一级分类下的所有二级分类
	 * @param int $parent_id 父级分类ID
	 * @param int $level 分类层级(一级分类为0)
	 * @return array 分类下二级分类   
	 */
	public function getCatelist($parent_id= 0, $level = 0) {
		$parent_id = intval($parent_id);
		$sql = "SELECT `cat_id`, `cat_name` FROM `{$this->_dbtabpre}goods_cat` WHERE `parent_id` = {$parent_id} AND `disabled` = 'false'";
		$query = $this->_db->query($sql);
		$list = array();
		while($row = $this->_db->fetch_array($query)) {
			$row['level'] = $level;
			if($level == 1) {
				$list[] = $row;
				break;
			}
			$row['subcategory'] = $this->getCateList($row['cat_id'], $level + 1);
			$list[] = $row;
		}
		return $list;
	}

}

?>