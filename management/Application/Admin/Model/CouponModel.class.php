<?php 
namespace Admin\Model;
use Think\Model;
use Think\Page;

/**
 * 优惠卷管理模型
 *
 * @author wl
 **/
class CouponModel extends BaseModel{

// 4.券兑换码管理
    /**
     * 券兑换码列表
     *
     * @return  void
     * @author  wl
     * @date    Mar 14, 2017
     **/
    public function getCouponCodeList () {
        $couponcodelists = array();
        $count = $this->table(C('DB_PREFIX').'coupon_code code')
            ->join(C('DB_PREFIX').'coupon coupon ON coupon.id = code.coupon_id', 'LEFT')
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $couponcodelist = $this->table(C('DB_PREFIX').'coupon_code code')
            ->field(
                'code.id as id,
                 code.coupon_id as code_coupon_id,
                 code.coupon_code as coupon_code,
                 code.is_used as is_used,
                 code.addtime as addtime,
                 code.updatetime as updatetime,
                 coupon.id as coupon_id,
                 coupon.coupon_name as coupon_name,
                 coupon.coupon_value as coupon_value,
                 coupon.coupon_category_id as coupon_category'
            )
            ->join(C('DB_PREFIX').'coupon coupon ON coupon.id = code.coupon_id', 'LEFT')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('code.id desc')
            ->select();
        if (!empty($couponcodelist)) {
            foreach ($couponcodelist as $key => $value) {
                if ($value['addtime'] != 0) {
                    $couponcodelist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $couponcodelist[$key]['addtime'] = '--';
                }

                if ($value['updatetime'] != '') {
                    if ($value['updatetime'] != 0) {
                        $couponcodelist[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                    } else {
                        $couponcodelist[$key]['updatetime'] = '--';
                    }
                } else {
                    $couponcodelist[$key]['updatetime'] = '--';
                }

                if ($value['coupon_name'] == '') {
                    $couponcodelist[$key]['coupon_name'] = '--';
                }

                if ($value['coupon_category'] != '') {
                    if ($value['coupon_category'] == 1) {
                        $couponcodelist[$key]['coupon_category_name'] = '现金券';
                    } elseif ($value['coupon_category'] == 2) {
                        $couponcodelist[$key]['coupon_category_name'] = '打折券';
                    }
                } else {
                    $couponcodelist[$key]['coupon_category'] = '1';
                }

                if ($value['coupon_value'] == '') {
                    $couponcodelist[$key]['coupon_value'] = '--';
                }

            }
        }
        $couponcodelists = array('couponcodelist' => $couponcodelist, 'count' => $count, 'page' => $page);
        return $couponcodelists;
    }

    /**
     * 搜索券兑换码列表
     *
     * @return  void
     * @author  wl
     * @date    Mar 15, 2017
     **/
    public function searchCouponCode ($param) {
        $map = array();
        $complex = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_info'] == '') {
            $complex['code.coupon_code'] = array('LIKE', $s_keyword);
            $complex['coupon.coupon_name'] = array('LIKE', $s_keyword);
            $complex['_logic'] = 'OR';
        } else {
            if ($param['search_info'] == 'coupon_code') {
                $param['search_info'] = 'code.coupon_code';
            }
            $complex[$param['search_info']] = array('LIKE', $s_keyword);
        }
        $map['_complex'] = $complex;

        $couponcodelists = array();
        $count = $this->table(C('DB_PREFIX').'coupon_code code')
            ->join(C('DB_PREFIX').'coupon coupon ON coupon.id = code.coupon_id', 'LEFT')
            ->where($map)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $couponcodelist = $this->table(C('DB_PREFIX').'coupon_code code')
            ->field(
                'code.id as id,
                 code.coupon_id as code_coupon_id,
                 code.coupon_code as coupon_code,
                 code.is_used as is_used,
                 code.addtime as addtime,
                 code.updatetime as updatetime,
                 coupon.id as coupon_id,
                 coupon.coupon_name as coupon_name,
                 coupon.coupon_value as coupon_value,
                 coupon.coupon_category_id as coupon_category'
            )
            ->join(C('DB_PREFIX').'coupon coupon ON coupon.id = code.coupon_id', 'LEFT')
            ->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('code.id desc')
            ->select();
        if (!empty($couponcodelist)) {
            foreach ($couponcodelist as $key => $value) {
                if ($value['addtime'] != 0) {
                    $couponcodelist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $couponcodelist[$key]['addtime'] = '--';
                }

                if ($value['updatetime'] != '') {
                    if ($value['updatetime'] != 0) {
                        $couponcodelist[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                    } else {
                        $couponcodelist[$key]['updatetime'] = '--';
                    }
                } else {
                    $couponcodelist[$key]['updatetime'] = '--';
                }

                if ($value['coupon_name'] == '') {
                    $couponcodelist[$key]['coupon_name'] = '--';
                }

                if ($value['coupon_category'] != '') {
                    if ($value['coupon_category'] == 1) {
                        $couponcodelist[$key]['coupon_category_name'] = '现金券';
                    } elseif ($value['coupon_category'] == 2) {
                        $couponcodelist[$key]['coupon_category_name'] = '打折券';
                    }
                } else {
                    $couponcodelist[$key]['coupon_category'] = '1';
                }

                if ($value['coupon_value'] == '') {
                    $couponcodelist[$key]['coupon_value'] = '--';
                }

            }
        }
        $couponcodelists = array('couponcodelist' => $couponcodelist, 'count' => $count, 'page' => $page);
        return $couponcodelists;
    }

    /**
     * 删除对应的优惠券兑换码
     *
     * @return  void
     * @author  wl
     * @date    Mar 15, 2017
     **/
    public function delCouponCode ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $where = array('id' => $id);
        $result = M('Coupon_code')
            ->where($where)
            ->fetchSql(false)
            ->delete();
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 获取优惠券相关信息
     *
     * @return  void
     * @author  wl
     * @date    Mar 15, 2017
     **/
    public function getCouponName () {
        $nowtime = time();
        $couponlist = $this->table(C('DB_PREFIX').'coupon coupon')
            ->field(
                'coupon.id as coupon_id,
                 coupon.coupon_name as coupon_name'
            )
            ->where(array('expiretime' => array('gt', $nowtime)))
            ->select();
        if (!empty($couponlist)) {
            return $couponlist;
        } else {
            return array();
        }
    }

    /**
     * 获取券兑换码根据ID
     *
     * @return  void
     * @author  wl
     * @date    Mar 15, 2017
     **/
    public function getCouponCodeById ($id) {
        if (!is_numeric($id)) {
            return false;
        }

        $where = array('code.id' => $id);
        $couponcodelist = $this->table(C('DB_PREFIX').'coupon_code code')
            ->field(
                'code.id as id,
                 code.coupon_id as coupon_id,
                 code.is_used as is_used,
                 code.coupon_code as coupon_code,
                 code.addtime as addtime,
                 coupon.coupon_name as coupon_name'
            )
            ->join(C('DB_PREFIX').'coupon coupon ON coupon.id = code.coupon_id')
            ->where($where)
            ->find();
        if (!empty($couponcodelist)) {
            return $couponcodelist;
        } else {
            array();
        }
    }

// 1.券的种类列表
    /**
     * 获取券的种类信息
     *
     * @return  void
     * @author  wl
     * @date    Nov 11, 2016
     **/
    public function getCouponCateList () {
        $count = $this->table(C('DB_PREFIX').'coupon_category')
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $couponcatelists = array();
        $couponcatelist = $this->table(C('DB_PREFIX').'coupon_category')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('id DESC')
            ->fetchSql(false)
            ->select();
        if (!empty($couponcatelist)) {
            foreach ($couponcatelist as $key => $value) {
                if ($value['addtime'] != 0) {
                    $couponcatelist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $couponcatelist[$key]['addtime'] = '--';
                }

                if ($value['cate_desc'] =='') {
                    $couponcatelist[$key]['cate_desc'] = '--';
                }

                if ($value['coupon_rule'] =='') {
                    $couponcatelist[$key]['coupon_rule'] = '--';
                }

                if ($value['cate_name'] == '') {
                    $couponcatelist[$key]['cate_name'] = '--';
                }

            }
        }
        $couponcatelists = array('couponcatelist' => $couponcatelist, 'count' => $count, 'page' => $page);  
        return $couponcatelists;
    }

    /**
     * 搜索券的种类信息
     *
     * @return  void
     * @author  wl
     * @date    Nov 11, 2016
     **/
    public function searchCouponCategory ($param) {
        $map = array();
        if ($param['s_keyword'] != '') {
            $map['cate_name'] = array('like', '%'.$param['s_keyword'].'%');
        }
        $count = $this->table(C('DB_PREFIX').'coupon_category')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $couponcatelists = array();
        $couponcatelist = $this->table(C('DB_PREFIX').'coupon_category')
            ->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('id DESC')
            ->fetchSql(false)
            ->select();
        if (!empty($couponcatelist)) {
            foreach ($couponcatelist as $key => $value) {
                if ($value['addtime'] != 0) {
                    $couponcatelist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $couponcatelist[$key]['addtime'] = '';
                }

                if ($value['cate_desc'] =='') {
                    $couponcatelist[$key]['cate_desc'] = '--';
                }

                if ($value['coupon_rule'] =='') {
                    $couponcatelist[$key]['coupon_rule'] = '--';
                }

                if ($value['cate_name'] == '') {
                    $couponcatelist[$key]['cate_name'] = '--';
                }

            }
        }
        $couponcatelists = array('couponcatelist' => $couponcatelist, 'count' => $count, 'page' => $page);  
        return $couponcatelists;
    }

    /**
     * 获取单条优惠券种类的相关信息
     *
     * @return  void
     * @author  wl
     * @date    Nov 11, 2016
     **/
    public function getCouponCateById ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $couponcatelist = $this->table(C('DB_PREFIX').'coupon_category')
            ->where('id = :cid')
            ->bind(['cid' => $id])
            ->fetchSql(false)
            ->find();  
        if ($couponcatelist) {
            return $couponcatelist;
        } else {
            return array();
        }
    }
    /**
     * 根据优惠券种类的名称判断该优惠券种类是否存在
     *
     * @return  void
     * @author  wl
     * @date    Nov 11, 2016
     **/
    public function checkCouponCateName ($cate_name) {
        if (trim((string)$cate_name) == '') {
            return false;
        }
        $couponcatelist = $this->table(C('DB_PREFIX').'coupon_category')
            ->where(array('cate_name' => $cate_name))
            ->fetchSql(false)
            ->find();
        if ($couponcatelist) {
            return $couponcatelist;
        } else {
            return array();
        }
    }   

    /**
     * 删除优惠券种类及其信息（单条删除）
     *
     * @return  void
     * @author  wl
     * @date    Nov 11, 2016
     **/
    public function delCouponCategory ($id) {
        if (!is_numeric($id)) {
            return false;
        }

        $result = M('coupon_category')
            ->where(array('id' => $id))
            ->fetchSql(false)
            ->delete();
        return $result;
    }


    /**
     * 获取优惠卷管理列表
     *
     * @return  void
     * @author  wl
     * @date    Oct 11, 2016
     **/
    public function getCouponList ($school_id) {
        $couponlists = array();
        if ($school_id == 0) {
            $count = $this->table(C('DB_PREFIX').'coupon c')
                ->fetchSql(false)
                ->count();
            $Page = new Page($count, 10);
            $page = $this->getPage($count, 10);
            $couponlist = $this->table(C('DB_PREFIX').'coupon c')
                ->join(C('DB_PREFIX').'coupon_category cc ON cc.id = c.coupon_category_id', 'LEFT')
                ->join(C('DB_PREFIX').'province p ON p.provinceid = c.province_id', 'LEFT')
                ->join(C('DB_PREFIX').'city ct ON ct.cityid = c.city_id', 'LEFT')
                ->join(C('DB_PREFIX').'area a ON a.areaid = c.area_id', 'LEFT')
                ->field('c.*, cc.id as cate_id, cc.cate_name, cc.cate_desc, cc.coupon_rule, provinceid, province, cityid, city, areaid, area')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->order('c.order ASC, c.id DESC')
                ->fetchSql(false)
                ->select();
        } else {
            $owner_ids = array();
            $owner_id = $this->table(C('DB_PREFIX').'coach')
                ->where(array('s_school_name_id' => $school_id))
                ->field('l_coach_id')
                ->select();
            if ($owner_id) {
                foreach ($owner_id as $key => $value) {
                    $owner_ids[$key] = $value['l_coach_id'];
                }
            }
            array_unshift($owner_ids, $school_id);
            $count = $this->table(C('DB_PREFIX').'coupon c')
                ->where(array('c.owner_id' => array('in', $owner_ids)))
                ->fetchSql(false)
                ->count();
            $Page = new Page($count, 10);
            $page = $this->getPage($count, 10);
            $couponlist = $this->table(C('DB_PREFIX').'coupon c')
                ->join(C('DB_PREFIX').'coupon_category cc ON cc.id = c.coupon_category_id', 'LEFT')
                ->join(C('DB_PREFIX').'province p ON p.provinceid = c.province_id', 'LEFT')
                ->join(C('DB_PREFIX').'city ct ON ct.cityid = c.city_id', 'LEFT')
                ->join(C('DB_PREFIX').'area a ON a.areaid = c.area_id', 'LEFT')
                ->where(array('c.owner_id' => array('in', $owner_ids)))
                ->field('c.*, cc.id as cate_id, cc.cate_name, cc.cate_desc, cc.coupon_rule, provinceid, province, cityid, city, areaid, area')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->order('c.order ASC, c.id DESC')
                ->fetchSql(false)
                ->select();
        }
        if (!empty($couponlist)) {
            foreach ($couponlist as $key => $value) {
                // 剩余券的数量
                $surplus = intval(intval($value['coupon_total_num']) - intval($value['coupon_get_num']));
                if ($surplus >= 0) {
                    $couponlist[$key]['coupon_surplus_num'] = $surplus;
                } else {
                    $couponlist[$key]['coupon_surplus_num'] = 0;
                }

                if ($value['addtime'] != 0) {
                    $couponlist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $couponlist[$key]['addtime'] = '--';
                }

                if ($value['expiretime'] != 0) {
                    $couponlist[$key]['expiretime'] = date('Y-m-d H:i:s', $value['expiretime']);
                } else {
                    $couponlist[$key]['expiretime'] = '--';
                }

                if ($value['updatetime'] != 0) {
                    $couponlist[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $couponlist[$key]['updatetime'] = '--';
                }

                if ($value['coupon_scope'] == 0) {
                    $couponlist[$key]['address'] = '全国';

                } elseif ($value['coupon_scope'] == 1) {
                    $couponlist[$key]['address'] = $value['province'];

                } elseif ($value['coupon_scope'] == 2) {
                    $couponlist[$key]['address'] = $value['province'].$value['city'];

                } elseif ($value['coupon_scope'] == 3) {
                    $couponlist[$key]['address'] = $value['province'].$value['city'].$value['area'];
                }

                if ($value['coupon_code'] == '') {
                    $couponlist[$key]['coupon_code'] = '--';
                }

                if ($value['coupon_desc'] == '') {
                    $couponlist[$key]['coupon_desc'] = '--';
                }

            }
        }
        $couponlists = array('couponlist' => $couponlist, 'page' => $page, 'count' => $count);
        return $couponlists;
    }

    /**
     * 搜索优惠券的相关信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 11, 2016
     **/
    public function searchCoupon ($param, $school_id) {
        $map = array();
        $complex = array();
        $couponlists = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ( $param['search_info'] == '' ) {
            $complex['coupon_name'] = array('like', $s_keyword);
            // $complex['coupon_code'] = array('like', $s_keyword);
            $complex['owner_name'] = array('like', $s_keyword);
            $complex['_logic'] = 'OR';
        } else {
            $complex[$param['search_info']] = array('like', $s_keyword) ;
        }
        $map['_complex'] = $complex;

        if ($param['owner_type'] != 0) {
            $map['owner_type'] = array('EQ', $param['owner_type']);
        }

        if ($school_id == 0) {
            $count = $this->table(C('DB_PREFIX').'coupon c')
                ->where($map)
                ->fetchSql(false)
                ->count();
            $Page = new Page($count, 10, $param);
            $page = $this->getPage($count, 10, $param);
            $couponlist = $this->table(C('DB_PREFIX').'coupon c')
                ->join(C('DB_PREFIX').'coupon_category cc ON cc.id = c.coupon_category_id', 'LEFT')
                ->join(C('DB_PREFIX').'province p ON p.provinceid = c.province_id', 'LEFT')
                ->join(C('DB_PREFIX').'city ct ON ct.cityid = c.city_id', 'LEFT')
                ->join(C('DB_PREFIX').'area a ON a.areaid = c.area_id', 'LEFT')
                ->where($map)
                ->field('c.*, cc.id as cate_id, cc.cate_name, cc.cate_desc, cc.coupon_rule, provinceid, province, cityid, city, areaid, area')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->order('c.order ASC, c.id DESC')
                ->fetchSql(false)
                ->select();
        } else {
            $owner_ids = array();
            $owner_id = $this->table(C('DB_PREFIX').'coach')
                ->where(array('s_school_name_id' => $school_id))
                ->field('l_coach_id')
                ->select();
            if ($owner_id) {
                foreach ($owner_id as $key => $value) {
                    $owner_ids[$key] = $value['l_coach_id'];
                }
            }
            array_unshift($owner_ids, $school_id);
            $count = $this->table(C('DB_PREFIX').'coupon c')
                ->where(array('c.owner_id' => array('in', $owner_ids)))
                ->where($map)
                ->fetchSql(false)
                ->count();
            $Page = new Page($count, 10, $param);
            $page = $this->getPage($count, 10, $param);
            $couponlist = $this->table(C('DB_PREFIX').'coupon c')
                ->join(C('DB_PREFIX').'coupon_category cc ON cc.id = c.coupon_category_id', 'LEFT')
                ->join(C('DB_PREFIX').'province p ON p.provinceid = c.province_id', 'LEFT')
                ->join(C('DB_PREFIX').'city ct ON ct.cityid = c.city_id', 'LEFT')
                ->join(C('DB_PREFIX').'area a ON a.areaid = c.area_id', 'LEFT')
                ->where($map)
                ->where(array('c.owner_id' => array('in', $owner_ids)))
                ->field('c.*, cc.id as cate_id, cc.cate_name, cc.cate_desc, cc.coupon_rule, provinceid, province, cityid, city, areaid, area')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->order('c.order ASC, c.id DESC')
                ->fetchSql(false)
                ->select();
        }
        if (!empty($couponlist)) {
            foreach ($couponlist as $key => $value) {
                // 剩余券的数量
                $surplus = intval(intval($value['coupon_total_num']) - intval($value['coupon_get_num']));
                if ($surplus >= 0) {
                    $couponlist[$key]['coupon_surplus_num'] = $surplus;
                } else {
                    $couponlist[$key]['coupon_surplus_num'] = 0;
                }
                
                if ($value['addtime'] != 0) {
                    $couponlist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $couponlist[$key]['addtime'] = '--';
                }

                if ($value['expiretime'] != 0) {
                    $couponlist[$key]['expiretime'] = date('Y-m-d H:i:s', $value['expiretime']);
                } else {
                    $couponlist[$key]['expiretime'] = '--';
                }

                if ($value['updatetime'] != 0) {
                    $couponlist[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $couponlist[$key]['updatetime'] = '--';
                }

                if ($value['coupon_scope'] == 0) {
                    $couponlist[$key]['address'] = '全国';

                } elseif ($value['coupon_scope'] == 1) {
                    $couponlist[$key]['address'] = $value['province'];

                } elseif ($value['coupon_scope'] == 2) {
                    $couponlist[$key]['address'] = $value['province'].$value['city'];

                } elseif ($value['coupon_scope'] == 3) {
                    $couponlist[$key]['address'] = $value['province'].$value['city'].$value['area'];
                }

                if ($value['coupon_code'] == '') {
                    $couponlist[$key]['coupon_code'] = '--';
                }

                if ($value['coupon_desc'] == '') {
                    $couponlist[$key]['coupon_desc'] = '--';
                }

            }
        }
        $couponlists = array('couponlist' => $couponlist, 'page' => $page, 'count' => $count);
        return $couponlists;
    }

    /**
     * 检车教练添加的优惠券过时否
     *
     * @return  void
     * @author  wl
     * @date    Jan 03, 2016
     **/
    public function checkCouponTime ($owner_type, $owner_id, $owner_name) {
        if (!is_numeric($owner_type) && !$owner_id && $owner_name) {
            return false;
        }
        $time = time();
        $map = array();
        $map = array(
                'owner_type' => $owner_type,
                'owner_id' => $owner_id,
                'owner_name' => $owner_name,
                'expiretime' => array('gt', $time)
            );
        $result = $this->table(C('DB_PREFIX').'coupon')
            ->where($map)
            ->fetchSql(false)
            ->find();
        if ($result) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * 获取单条信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 12, 2016
     **/
    public function getCouponListById ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $couponlist = $this->table(C('DB_PREFIX').'coupon c')
            ->join(C('DB_PREFIX').'coupon_category cc ON cc.id = c.coupon_category_id', 'LEFT')
            ->join(C('DB_PREFIX').'province p ON p.provinceid = c.province_id', 'LEFT')
            ->join(C('DB_PREFIX').'city ct ON ct.cityid = c.city_id', 'LEFT')
            ->join(C('DB_PREFIX').'area a ON a.areaid = c.area_id', 'LEFT')
            ->where('c.id = :cid')
            ->bind(['cid' => $id])
            ->field('c.*, cc.id as cate_id, cc.cate_name, cc.cate_desc, cc.coupon_rule, provinceid, province, cityid, city, areaid, area')
            ->fetchSql(false)
            ->find();
        if ($couponlist) {
            if ($couponlist['addtime'] != 0) {
                $couponlist['addtime'] = date('Y-m-d H:i:s', $couponlist['addtime']);
            } else {
                $couponlist['addtime'] = '';
            }

            if ($couponlist['expiretime'] != 0) {
                $couponlist['expiretime'] = date('Y-m-d H:i:s', $couponlist['expiretime']);
            } else {
                $couponlist['expiretime'] = '';
            }

            if ($couponlist['updatetime'] != 0) {
                $couponlist['updatetime'] = date('Y-m-d H:i:s', $couponlist['updatetime']);
            } else {
                $couponlist['updatetime'] = '';
            }

            if ($couponlist['owner_type'] == 1) {
                    $couponlist['owner_type_name'] = '教练';

            } elseif ($couponlist['owner_type'] == 2) {
                $couponlist['owner_type_name'] = '驾校';

            } elseif ($couponlist['owner_type'] == 3) {
                $couponlist['owner_type_name'] = '嘻哈平台';
            }
        }
        return $couponlist;
    }

    /**
     * 优惠券种类名称
     *
     * @return  void
     * @author  wl
     * @date    Nov 14, 2016
     **/
    public function getCouponCateName () {
        $coupon_cate_name = $this->table(C('DB_PREFIX').'coupon_category')
            ->field('id, cate_name')
            ->fetchSql(false)
            ->select();
        if (!empty($coupon_cate_name)) {
            return $coupon_cate_name;
        } else {
            return array();
        }

    }

    /**
    * 设置券的在线状态
    *
    * @return  void
    * @author  wl
    * @date    Sep 19, 2016
    **/
    public function setOpenStatus ($id, $status) {
        if (!$id) {
            return false;
        }
        $list = array();
        $data = array('is_open' => $status);
        $result = M('coupon')->where('id = :cid')
            ->bind(['cid' => $id])
            ->fetchSql(false)
            ->data($data)
            ->save();
        $list['is_open']  = $result;
        $list['id'] = $id;
        return $list;
    }

    /**
     * 设置优惠券的展示状态
     *
     * @return  void
     * @author  wl
     * @date    Mar 15, 2016
     **/
    public function setShowStatus ($id, $status) {
        if (!$id) {
            return false;
        }
        $list = array();
        $data = array('is_show' => $status);
        $result = M('coupon')->where('id = :cid')
            ->bind(['cid' => $id])
            ->fetchSql(false)
            ->data($data)
            ->save();
        $list['is_show']  = $result;
        $list['id'] = $id;
        return $list;
    }

    /**
     * update coupon order
     *
     * @return  void
     * @author  wl
     * @date    Oct 11, 2016
     **/
    public function updateCouponOrder ($post) {
        if (empty($post)) {
            return 101; // 参数错误
        }
        if (isset($post['order'])) {
            if (!is_numeric($post['order'])) {
                return 102; // 参数类型不符合
            } else {
                $old_num = $this->table(C('DB_PREFIX').'coupon')
                    ->where('id = :cid')
                    ->bind(['cid' => $post['id']])
                    ->getField('order');
                if ($post['order'] === $old_num) {
                    return 105; // 未做任何修改
                }
            }
        }
        $data['order'] = $post['order'];
        $coupon = D('coupon');
        if ($res = $coupon->create($data)) {
            $result = $coupon->where(array('id' => $post['id']))->save($res);
            if ($result) {
                return 200;
            } else {
                return 400;
            }
        }

    }

    /**
     * 根据角色类别获取驾校、教练、和嘻哈后台的id和名称
     *
     * @return  void
     * @author  wl
     * @date    Oct 12, 2016
     **/
    public function getCouponOwnerInfo ($owner_type, $school_id) {
        if (!is_numeric($owner_type)) {
            return false; 
        }
        if ($school_id == 0) {
            if ($owner_type == 1) {
                $where = array(
                    's.is_show'                 => 1, // 1:展示 2：不展示
                    'c.coupon_supported'        => 1, // 券的支持状态 1:支持 0:不支持
                    'c.order_receive_status'    => 1, // 1:在线 2:不在线
                    'user.i_user_type'          => 1, // 0:student 1:coach
                    'user.i_status'             => 0, // 0:在线 1:不在线
                );

                $getCoachList = $this->table(C('DB_PREFIX').'coach c')
                    ->join(C('DB_PREFIX').'school s ON s.l_school_id = c.s_school_name_id', 'LEFT')
                    ->join(C('DB_PREFIX').'user user ON user.l_user_id = c.user_id', 'LEFT')
                    ->field('l_coach_id, s_coach_name, s_school_name, l_school_id')
                    ->where($where)
                    ->fetchSql(false)
                    ->select();
                if ($getCoachList) {
                    foreach ($getCoachList as $key => $value) {
                        if ($value['s_school_name'] == '') {
                            $getCoachList[$key]['s_school_name'] = '暂无';
                        } else {
                            $getCoachList[$key]['s_school_name'] = $value['s_school_name'];
                        }
                    }
                    return $getCoachList;
                } else {
                    return array();
                }
            } elseif ($owner_type == 2) {
                $where = array(
                    'is_show'           => 1, // 1:展示 2:不展示
                    'support_coupon'    => 1, // 1:支持 2:不支持
                );

                $getSchoolList = $this->table(C('DB_PREFIX').'school')
                    ->field('l_school_id, s_school_name')
                    ->where($where)
                    ->fetchSql(false)
                    ->select();
                if ($getSchoolList) {
                    return $getSchoolList;
                } else {
                    return array();
                }
            } elseif ($owner_type == 3) {
                $getManagerList = array();
                $getManagerList['manager_id'] = 0;
                $getManagerList['manager_name'] = '嘻哈后台';
                return $getManagerList;
            }
        } else {
            if ($owner_type == 1) {
                $where = array(
                    'coupon_supported'      => 1, 
                    'order_receive_status'  => 1, // 1:在线 2:不在线
                    'user.i_user_type'      => 1, // 0:student 1:coach
                    'user.i_status'         => 0, // 0:用户存在 1:用户已被删除
                );
                $getCoachList = $this->table(C('DB_PREFIX').'coach')
                    ->join(C('DB_PREFIX').'user user ON user.l_user_id = coach.user_id', 'LEFT')
                    ->field('l_coach_id, s_coach_name')
                    ->where($where)
                    ->fetchSql(false)
                    ->select();
                if ($getCoachList) {
                    return $getCoachList;
                } else {
                    return array();
                }
            } elseif ($owner_type == 2) {
                $getSchoolList = $this->table(C('DB_PREFIX').'school')
                    ->field('l_school_id, s_school_name')
                    ->where(array('l_school_id' => $school_id))
                    ->fetchSql(false)
                    ->find();
                if ($getSchoolList) {
                    return $getSchoolList;
                } else {
                    return array();
                }
            }
        }
    }
    /**
     * 根据owner_id获取单一驾校、教练、和嘻哈后台名称
     *
     * @return  void
     * @author  wl
     * @date    Oct 12, 2016
     **/
    public function getOwnerNameByOwnerId ($owner_type, $owner_id, $school_id) {
        if (!is_numeric($owner_id) && !is_numeric($owner_type)) {
            return false;
        }
        if ($school_id == 0) {
            if ($owner_type == 1) {
                $getCoachList = $this->table(C('DB_PREFIX').'coach c')
                    ->field('l_coach_id as owner_id, s_coach_name as owner_name')
                    ->where(array('l_coach_id' => $owner_id))
                    ->fetchSql(false)
                    ->find();
                
                return $getCoachList;
            } else if ($owner_type == 2) {
                $getSchoolList = $this->table(C('DB_PREFIX').'school')
                    ->field('l_school_id as owner_id, s_school_name as owner_name')
                    ->where(array('l_school_id' => $owner_id))
                    ->fetchSql(false)
                    ->find();
                return $getSchoolList;
            } else if ($owner_type == 3) {
                $getManagerList = array();
                $getManagerList['owner_id'] = 0;
                $getManagerList['owner_name'] = '嘻哈平台';
                return $getManagerList;
            }
        } else {
            if ($owner_type == 1) {
                $getCoachList = $this->table(C('DB_PREFIX').'coach')
                    ->where(array('l_coach_id' => $owner_id))
                    ->field('l_coach_id as owner_id, s_coach_name as owner_name')
                    ->fetchSql(false)
                    ->find();
                return $getCoachList;
            } elseif ($owner_type == 2) {
                $getSchoolList = $this->table(C('DB_PREFIX').'school')
                    ->where(array('l_school_id' => $school_id))
                    ->field('l_school_id as owner_id, s_school_name as owner_name')
                    ->fetchSql(false)
                    ->find();
                return $getSchoolList;
            }
        }
    }

    /**
     * 兑换码唯一是确定的
     *
     * @return  void
     * @author  wl
     * @date    Oct 31, 2016
     **/
    public function couponCodeUnqiue ($code) {
        if (!$code) {
            return false;
        }
        $code = $this->table(C('DB_PREFIX').'coupon')
            ->where(array('coupon_code' => $code))
            ->fetchSql(false)
            ->find();
        return $code;
    } 

    
    /**
     * 获取学车领取券表
     *
     * @return  void
     * @author  wl
     * @date    Oct 13, 2016
     **/
    public function getUserCouponList ($school_id) {
        if (!is_numeric($school_id)) {
            return false;
        }

        $usercouponlists = array();
        $map['coupon_status'] = array('neq', 4);
        if ($school_id != 0) {
            $map['coupon_status'] = array('neq', 4);
            $map['coupon_sender_owner_type'] = array('in', array('1', '2'));
        }
        $count = $this->table(C('DB_PREFIX').'user_coupon u')
            ->join(C('DB_PREFIX').'province p ON p.provinceid = u.province_id', 'LEFT')
            ->join(C('DB_PREFIX').'city c ON c.cityid = u.city_id', 'LEFT')
            ->join(C('DB_PREFIX').'area a ON a.areaid = u.area_id', 'LEFT')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $usercouponlist = $this->table(C('DB_PREFIX').'user_coupon u')
            ->join(C('DB_PREFIX').'province p ON p.provinceid = u.province_id', 'LEFT')
            ->join(C('DB_PREFIX').'city c ON c.cityid = u.city_id', 'LEFT')
            ->join(C('DB_PREFIX').'area a ON a.areaid = u.area_id', 'LEFT')
            ->where($map)
            ->field('u.*, p.provinceid, p.province, c.cityid, c.city, a.areaid, a.area')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('u.id DESC, coupon_status ASC')
            ->fetchSql(false)
            ->select();
        if (!empty($usercouponlist)) {
            foreach ($usercouponlist as $key => $value) {
                if ($value['coupon_sender_owner_type'] != '' && $value['coupon_sender_owner_id'] != '') {
                    $owner_type = $value['coupon_sender_owner_type'];
                    $owner_id = $value['coupon_sender_owner_id'];
                    $sender_owner_name = $this->getOwnerNameByOwnerId($owner_type, $owner_id, $school_id);
                    if (!empty($sender_owner_name)) {
                        $usercouponlist[$key]['owner_name'] = $sender_owner_name['owner_name'];
                    } else {
                        $usercouponlist[$key]['owner_name'] = '--';
                    }
                }
                if ($value['addtime'] != 0) {
                    $usercouponlist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $usercouponlist[$key]['addtime'] = '--';
                }

                if ($value['expiretime'] != 0) {
                    $usercouponlist[$key]['expiretime'] = date('Y-m-d H:i:s', $value['expiretime']);
                } else {
                    $usercouponlist[$key]['expiretime'] = '--';
                }

                if ($value['expiretime'] - time() <= 0) {
                    if ($value['coupon_status'] == 1) {
                        $usercouponlist[$key]['coupon_status'] = 3;
                    } 
                }

                if ($value['coupon_desc'] == '') {
                    $usercouponlist[$key]['coupon_desc'] = '--';
                }

                if ($value['coupon_scope'] == 0) {
                    $usercouponlist[$key]['address'] = '全国';

                } elseif ($value['coupon_scope'] == 1) {
                    $usercouponlist[$key]['address'] = $value['province'];

                } elseif ($value['coupon_scope'] == 2) {
                    $usercouponlist[$key]['address'] = $value['province'].$value['city'];

                } elseif ($value['coupon_scope'] == 3) {
                    $usercouponlist[$key]['address'] = $value['province'].$value['city'].$value['area'];
                }

                if ($value['province_id'] == 0) {
                    $usercouponlist[$key]['province'] = '';
                }
                if ($value['city_id'] == 0) {
                    $usercouponlist[$key]['city'] = '';
                }
                if ($value['area_id'] == 0) {
                    $usercouponlist[$key]['area'] = '';
                }

                if ($value['coupon_desc'] == '') {
                    $usercouponlist[$key]['coupon_desc'] = '--';
                }
            }
        }
        $usercouponlists = array('usercouponlist' => $usercouponlist, 'page' => $page, 'count' => $count);
        return $usercouponlists;
    }

    /**
     * 搜索学车券信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 13, 2016
     **/
    public function searchUserCoupon ($param, $school_id) {
        $map = array();
        $complex = array();
        $usercouponlists = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_info'] == '') {
            $complex['coupon_name'] = array('like', $s_keyword);
            $complex['user_name'] = array('like', $s_keyword);
            $complex['user_phone'] = array('like', $s_keyword);
            // $complex['coupon_code'] = array('like', $s_keyword);
            $complex['_logic'] = 'OR';
        } else {
            $complex[$param['search_info']] = array('like', $s_keyword);
        }
        $map['_complex'] = $complex;

        if ($param['coupon_status'] != 0) {
            $map['coupon_status'] = array('EQ', $param['coupon_status']);
        } else {
            $map['coupon_status'] = array('NEQ', 4);
        }

        if ($param['coupon_type'] != 0) {
            $map['coupon_type'] = array('EQ', $param['coupon_type']);
        }  

        if ($school_id != 0) {
            $map['coupon_sender_owner_type'] = array('in', array('1', '2'));
        }

        $count = $this->table(C('DB_PREFIX').'user_coupon u')
            ->join(C('DB_PREFIX').'province p ON p.provinceid = u.province_id', 'LEFT')
            ->join(C('DB_PREFIX').'city c ON c.cityid = u.city_id', 'LEFT')
            ->join(C('DB_PREFIX').'area a ON a.areaid = u.area_id', 'LEFT')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $usercouponlist = $this->table(C('DB_PREFIX').'user_coupon u')
            ->join(C('DB_PREFIX').'province p ON p.provinceid = u.province_id', 'LEFT')
            ->join(C('DB_PREFIX').'city c ON c.cityid = u.city_id', 'LEFT')
            ->join(C('DB_PREFIX').'area a ON a.areaid = u.area_id', 'LEFT')
            ->where($map)
            ->field('u.*, p.provinceid, p.province, c.cityid, c.city, a.areaid, a.area')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('u.id DESC, coupon_status ASC')
            ->fetchSql(false)
            ->select();
        if (!empty($usercouponlist)) {
            foreach ($usercouponlist as $key => $value) {
                if ($value['coupon_sender_owner_type'] != '' && $value['coupon_sender_owner_id'] != '') {
                    $owner_type = $value['coupon_sender_owner_type'];
                    $owner_id = $value['coupon_sender_owner_id'];
                    $sender_owner_name = $this->getOwnerNameByOwnerId($owner_type, $owner_id, $school_id);
                    if (!empty($sender_owner_name)) {
                        $usercouponlist[$key]['owner_name'] = $sender_owner_name['owner_name'];
                    } else {
                        $usercouponlist[$key]['owner_name'] = '--';
                    }
                }
                if ($value['addtime'] != 0) {
                    $usercouponlist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $usercouponlist[$key]['addtime'] = '';
                }

                if ($value['expiretime'] != 0) {
                    $usercouponlist[$key]['expiretime'] = date('Y-m-d H:i:s', $value['expiretime']);
                } else {
                    $usercouponlist[$key]['expiretime'] = '';
                }

                if ($value['expiretime'] - time() <= 0) {
                    if ($value['coupon_status'] == 1) {
                        $usercouponlist[$key]['coupon_status'] = 3;
                    } 
                }

                if ($value['coupon_desc'] == '') {
                    $usercouponlist[$key]['coupon_desc'] = '--';
                }

                if ($value['scope'] == 0) {
                    $usercouponlist[$key]['address'] = '全国';

                } elseif ($value['scope'] == 1) {
                    $usercouponlist[$key]['address'] = $value['province'];

                } elseif ($value['scope'] == 2) {
                    $usercouponlist[$key]['address'] = $value['province'].$value['city'];

                } elseif ($value['scope'] == 3) {
                    $usercouponlist[$key]['address'] = $value['province'].$value['city'].$value['area'];
                }


                if ($value['province_id'] == 0) {
                    $usercouponlist[$key]['province'] = '';
                }
                if ($value['city_id'] == 0) {
                    $usercouponlist[$key]['city'] = '';
                }
                if ($value['area_id'] == 0) {
                    $usercouponlist[$key]['area'] = '';
                }
            }
        }
        $usercouponlists = array('usercouponlist' => $usercouponlist, 'page' => $page, 'count' => $count);
        return $usercouponlists;
    }




} /* class End */
