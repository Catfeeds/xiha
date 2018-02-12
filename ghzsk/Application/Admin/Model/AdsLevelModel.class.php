<?php 
namespace Admin\Model;
use Think\Model;
use Think\Page;

/**
 * 广告等级模型类
 *
 * @author gaodcheng
 **/
class AdsLevelModel extends BaseModel{

    //自动完成配置
    protected $_auto = array(
        // addtime 添加时间戳
        array('addtime', 'time', 3, 'function'),
    );
    /*
     * 获取广告等级列表
    */
    public function getAllRecords() {
        $count = $this->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $ads_level = $this->limit($Page->firstRow . ',' . $Page->listRows)->select();
        p($ads_level);exit;
        $_data = array();
        foreach ( $ads_level as $k => $v ) {
            $_data[$k]['id'] = $v['id'];
            $_data[$k]['level_id'] = $v['level_id'];
            $_data[$k]['level_money'] = $v['level_money'];
            $_data[$k]['level_title'] = $v['level_title'];
            $_data[$k]['level_intro'] = $v['level_intro'];
            $_data[$k]['loop_time'] = $v['loop_time'];
            $_data[$k]['adddate'] = date('Y-m-d H:i:s', $v['addtime']);
        }
        return array('ads_level' => $_data, 'count' => $count, 'page' => $page);
    }

    /*
     * 更新广告等级
    **/
    public function updateOneRecordPartial( $p = array() ) {

        if ( empty($p) ) {
            /* 是否为空的参数 */
            return 101;
        } 

        if ( isset($p['level_id']) ) {
            /* level_id 不可以重复 */
            if ( !is_numeric($p['level_id']) ) {
                return 102; // 参数类型不符
            } else {
                $old_level_id = $this->where(array('id' => $p['id']))->getField('level_id');
                if ( $p['level_id'] === $old_level_id) {
                    return 107;
                }
                $is_exist = $this->where(array('level_id' => $p['level_id']))->getField('level_id');
                if ( $is_exist ) {
                    return 105;
                }
            }
        }

        $this->create($p);
        $update_ok = $this->save();
        if ( $update_ok ) {
            return 200;
        } else {
            return 400;
        }
    }

    /*
     * 根据主键id删除一条记录
     * @param integer $id  记录id
    **/
    public function delOneRecord( $id = null ) {
        return $this->delete($id);
    }

    /*
     * 添加一条新的记录
    **/
    public function addOneRecord($p) {
        $i_level = $p['ads_level'];
        if ( $this->where(array('ads_level' => $i_level))->getField('id') ) {
            // already exist
            return false;
        }
        $this->create($p);
        return $this->add();
    }

    public function getCount() {
        return $this->count();
    }

} /* class End */
