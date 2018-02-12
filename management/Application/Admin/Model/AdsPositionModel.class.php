<?php 
namespace Admin\Model;
use Think\Model;

/**
 * 广告管理模型类
 *
 * @return 
 * @author 
 **/
class AdsPositionModel extends BaseModel{

    /*
     * 获取广告位列表
     * @return array 
    */
    public function getAdsPositions() {
    	$ads_position = $this->select();
    	foreach ($ads_position as $key => $value) {
    		$ads_position[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
    	}
    	// var_dump($ads_position);
    	$count = count($ads_position);
    	$res = array($ads_position, $count);
    	return $res;
    }
	
    /**
     * 添加广告位
     *
     * @return 
     * @author sun
     **/
    public function addAdsPosition($condition) {
        $add = $this->add($condition);
        return $add;
    }

    /**
     * 编辑广告位
     *
     * @return 
     * @author sun
     **/
    public function editAdsPosition($condition) {
        $edit = $this->where(array('id'=>$condition['id']))->save($condition);
        return $edit;
    }

    /**
     * 判断数据库中是否存在传入的场景
     *
     * @return 
     * @author sun
     **/
    public function findScene($condition) {
        $find = $this->where(array('scene'=>$condition))->find();
        return $find;
    }

    /**
     * 根据id获取一条广告位信息
     *
     * @return 
     * @author sun
     **/
    public function findPosition($id) {
        $find = $this->where(array('id'=>$id))->find();
        return $find;
    }

    /**
     * 删除一条广告位信息
     *
     * @return 
     * @author sun
     **/
    public function delPosition($id) {
        $res = $this->where(array('id'=>$id))->delete();
        return $res;
    }
}

 ?>