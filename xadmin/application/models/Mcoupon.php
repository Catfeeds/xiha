<?php
    // 首页
defined('BASEPATH') OR exit('No direct script access allowed');

class Mcoupon extends CI_Model {

    public $coupon_tablename = 'cs_coupon';
    public $coupon_cate_tablename = 'cs_coupon_category';
    public $coupon_code_tablename = 'cs_coupon_code';
    public $coupon_userd_tablename = 'cs_user_coupon';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('mcity');
        $this->coupon_tbl = $this->db->dbprefix('coupon');
        $this->coupon_cate_tbl = $this->db->dbprefix('coupon_category');
        $this->coupon_code_tbl = $this->db->dbprefix('coupon_code');
        $this->user_coupon_tbl = $this->db->dbprefix('user_coupon');
        $this->province_tbl = $this->db->dbprefix('province');
        $this->city_tbl = $this->db->dbprefix('city');
        $this->area_tbl = $this->db->dbprefix('area');
        $this->school_tbl = $this->db->dbprefix('school');
        $this->user_tbl = $this->db->dbprefix('user');
        $this->coach_tbl = $this->db->dbprefix('coach');
    }

// 1.优惠券列表
    /**
     * 获取优惠券列表页码信息
     * @param   int     $school_id  驾校ID
     * @param   array   $param      条件
     * @param   int     $limit      限定数
     * @return  $pageinfo
     **/
    public function getCouponPageNum($school_id, $param, $limit)
    {
        $map = [];
        $complex = [];
        if ($param) {
            if ($param['type']) {
                $map['owner_type'] = $param['type'];
            }
            $keywords = $param['keywords'];
            if ($keywords != '') {
                $complex = [
                    'coupon.coupon_name' => $keywords,
                    'coupon.owner_name' => $keywords,
                ];
            }
        }
        
        $all_ids = [];
        if ($school_id != 0) {
            $all_ids = $this->getSchoolCoachById($school_id);
        }

        $query = $this->db
            ->from("{$this->coupon_tbl} as coupon")
            ->where($map);

        if ($school_id != 0) {
            $query = $query
                ->where_in('owner_type', [1, 2])
                ->where_in('owner_id', $all_ids);
        }
        if ( ! empty($complex)) {
            $query = $query
                ->group_start()
                    ->or_like($complex)
                ->group_end();
        }

        $count = $query->count_all_results();
        return $pageinfo = [
            'count' => $count,
            'pagenum' => ceil ( $count / $limit),
        ];        
    }

    /**
     * 获取优惠券列表页码信息
     * @param   int     $school_id  驾校ID
     * @param   array   $param      条件
     * @param   int     $limit      限定数
     * @return  $pageinfo
     **/
    public function getCouponList($school_id, $param, $start, $limit)
    {
        $map = [];
        $complex = [];
        if ($param) {
            if ($param['type']) {
                $map['owner_type'] = $param['type'];
            }
            $keywords = $param['keywords'];
            if ($keywords != '') {
                $complex = [
                    'coupon.coupon_name' => $keywords,
                    'coupon.owner_name' => $keywords,
                ];
            }
        }
        
        $all_ids = [];
        if ($school_id != 0) {
            $all_ids = $this->getSchoolCoachById($school_id);
        }

        $query = $this->db
            ->select(
                'coupon.*,
                 province.province,
                 city.city,
                 area.area'
            )
            ->from("{$this->coupon_tbl} as coupon")
            ->join("{$this->province_tbl} as province", 'province.provinceid=coupon.province_id', 'left')
            ->join("{$this->city_tbl} as city", 'city.cityid=coupon.city_id', 'left')
            ->join("{$this->area_tbl} as area", 'area.areaid=coupon.area_id', 'left')
            ->where($map);

        if ($school_id != 0) {
            $query = $query
                ->where_in('owner_type', [1, 2])
                ->where_in('owner_id', $all_ids);
        }
        if ( ! empty($complex)) {
            $query = $query
                ->group_start()
                    ->or_like($complex)
                ->group_end();
        }

        $list = $query
            ->limit($limit, $start)
            ->order_by('id', 'desc')
            ->get()
            ->result_array();
        if ( ! empty($list)) {
            foreach ($list as $index => $coupon) {
                // 剩余券的数量
                $surplus = intval(intval($coupon['coupon_total_num']) - intval($coupon['coupon_get_num']));
                if ($surplus >= 0) {
                    $list[$index]['coupon_surplus_num'] = $surplus;
                } else {
                    $list[$index]['coupon_surplus_num'] = 0;
                }

                if ($coupon['addtime'] != '') {
                    $list[$index]['addtime'] = date('Y-m-d H:i:s', $coupon['addtime']);
                } else {
                    $list[$index]['addtime'] = '--';
                }

                if ($coupon['expiretime'] != 0) {
                    $list[$index]['expiretime'] = date('Y-m-d H:i:s', $coupon['expiretime']);
                } else {
                    $list[$index]['expiretime'] = '--';
                }

                if ($coupon['updatetime'] != 0) {
                    $list[$index]['updatetime'] = date('Y-m-d H:i:s', $coupon['updatetime']);
                } else {
                    $list[$index]['updatetime'] = '--';
                }

                if ($coupon['coupon_scope'] == 0) {
                    $list[$index]['address'] = '全国';

                } elseif ($coupon['coupon_scope'] == 1) {
                    $list[$index]['address'] = $coupon['province'];

                } elseif ($coupon['coupon_scope'] == 2) {
                    $list[$index]['address'] = $coupon['province'].$coupon['city'];

                } elseif ($coupon['coupon_scope'] == 3) {
                    $list[$index]['address'] = $coupon['province'].$coupon['city'].$coupon['area'];

                } else {
                    $list[$index]['address'] = '--';
                }

                if ($coupon['coupon_code'] == '') {
                    $list[$index]['coupon_code'] = '--';
                }

                if ($coupon['coupon_desc'] == '') {
                    $list[$index]['coupon_desc'] = '--';
                }
            }
        }
        return $list;
    }
   
    
    public function create($data)
    {
        $data['addtime'] = isset($data['addtime']) ? $data['addtime'] : time();
        $query = $this->db->insert($this->coupon_tbl, $data);
        return $this->db->insert_id();
    }

    // 获取数据页码和总数（通用）
    public function getPageNumA($param, $limit)
    {
        $map = "c.id!='0'";
        $filter_1 = $filter_2 = '';
        if($param) {
            if($param['type']) {
                $filter_1 = " AND c.owner_type='".(int)$param['type']."'";
            }
            if($param['kwords'] == 'coupon_name') {
                $filter_2 = " AND c.coupon_name LIKE '%" .$param['value']. "%'";
            }else if($param['kwords'] == 'coupon_code') {
                $filter_2 = " AND c.coupon_code LIKE '%" .$param['value']. "%'";
            }else if($param['kwords'] == 'owner_name') {
                $filter_2 = " AND c.owner_name LIKE '%" .$param['value']. "%'";
            }
        }
        $map .= $filter_1 . $filter_2;
        $count = $this->db->from("{$this->coupon_tbl} as c")->where($map)->count_all_results();
        return ['pn'=>(int) ceil($count / $limit), 'count'=>$count];
    }

    // 获取优惠券列表
    public function getAllCouponList($param, $start='', $limit='')
    {
        $map = "c.id!='0'";
        $filter_1 = $filter_2 = '';
        if($param) {
            if($param['type']) {
                $filter_1 = " AND c.owner_type='".(int)$param['type']."'";
            }
            if($param['kwords'] == 'coupon_name') {
                $filter_2 = " AND c.coupon_name LIKE '%" .$param['value']. "%'";
            }else if($param['kwords'] == 'coupon_code') {
                $filter_2 = " AND c.coupon_code LIKE '%" .$param['value']. "%'";
            }else if($param['kwords'] == 'owner_name') {
                $filter_2 = " AND c.owner_name LIKE '%" .$param['value']. "%'";
            }
        }
        $map .= $filter_1 . $filter_2;
        $count = $this->db->from("{$this->coupon_tbl} as c")->where($map)->count_all_results();
        $items = $this->db->select('c.*, cc.id as cate_id, cc.cate_name, cc.cate_desc, cc.coupon_rule, provinceid, province, cityid, city, areaid, area')
                ->from("{$this->coupon_tbl} as c")
                ->join("{$this->coupon_cate_tbl} as cc", 'cc.id=c.coupon_category_id', 'left')
                ->join("{$this->province_tbl} as p", 'p.provinceid=c.province_id', 'left')
                ->join("{$this->city_tbl} as ct", 'ct.cityid=c.city_id', 'left')
                ->join("{$this->area_tbl} as a", 'a.areaid=c.area_id', 'left')
                ->where($map)->order_by('c.id', 'DESC')
                ->limit($limit, $start)->get()->result_array();
        $page = (int) ceil($count / $limit);
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                // 剩余券的数量
                $surplus = intval(intval($value['coupon_total_num']) - intval($value['coupon_get_num']));
                if ($surplus >= 0) {
                    $items[$key]['coupon_surplus_num'] = $surplus;
                } else {
                    $items[$key]['coupon_surplus_num'] = 0;
                }

                if ($value['addtime'] != 0) {
                    $items[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $items[$key]['addtime'] = '--';
                }

                if ($value['expiretime'] != 0) {
                    $items[$key]['expiretime'] = date('Y-m-d H:i:s', $value['expiretime']);
                } else {
                    $items[$key]['expiretime'] = '--';
                }

                if ($value['updatetime'] != 0) {
                    $items[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $items[$key]['updatetime'] = '--';
                }

                if ($value['coupon_scope'] == 0) {
                    $items[$key]['address'] = '全国';

                } elseif ($value['coupon_scope'] == 1) {
                    $items[$key]['address'] = $value['province'];

                } elseif ($value['coupon_scope'] == 2) {
                    $items[$key]['address'] = $value['province'].$value['city'];

                } elseif ($value['coupon_scope'] == 3) {
                    $items[$key]['address'] = $value['province'].$value['city'].$value['area'];
                }

                if ($value['coupon_code'] == '') {
                    $items[$key]['coupon_code'] = '--';
                }

                if ($value['coupon_desc'] == '') {
                    $items[$key]['coupon_desc'] = '--';
                }
                if($value['coupon_value']) {
                    $items[$key]['coupon_value'] = '￥'.$value['coupon_value'].'元';
                }
                if($value['coupon_limit_num']) {
                    $items[$key]['coupon_limit_num'] = $value['coupon_limit_num'] . '张';
                }
            }
        }
        return array('items' => $items, 'pn' => $page, 'count' => $count);
    }

// 2.券优惠码列表
    /**
     * 获取券优惠码列表的页码
     * @param   array   $param  条件
     * @param   int     $limit  限定条数
     * @return  void
     **/
    public function getCouponCodePageNum($param, $limit)
    {
        $map = [];
        if ($param) {
            $keywords = $param['keywords'];
            if ($keywords != '') {
                $map['coupon.coupon_name'] = $keywords;
                $map['code.coupon_code'] = $keywords;
            }
        }
        $count = $this->db 
            ->from("{$this->coupon_code_tbl} as code")
            ->join("{$this->coupon_tbl} as coupon", 'coupon.id=code.coupon_id', 'left')
            ->or_like($map)
            ->count_all_results();
        return $pageinfo = ['count' => $count, 'pagenum' => ceil ( $count / $limit )];
    }

    /**
     * 获取券优惠码列表
     * @param   array   $param  条件
     * @param   int     $limit  限定条数
     * @return  void
     **/
    public function getCouponCodeList($param, $start, $limit)
    {
        $map = [];
        if ($param) {
            $keywords = $param['keywords'];
            if ($keywords != '') {
                $map['coupon.coupon_name'] = $keywords;
                $map['code.coupon_code'] = $keywords;
            }
        }
        $list = $this->db 
            ->select(
                'code.*,
                 coupon.coupon_name,
                 coupon.coupon_value,
                 coupon.coupon_category_id as cate_id'
            )
            ->from("{$this->coupon_code_tbl} as code")
            ->join("{$this->coupon_tbl} as coupon", 'coupon.id=code.coupon_id', 'left')
            ->or_like($map)
            ->limit($limit, $start)
            ->order_by('id', 'desc')
            ->get()
            ->result_array();
       
        if ( ! empty($list)) {
            foreach ($list as $key => $code) {

                if ($code['addtime'] != 0 AND $code['addtime'] != '') {
                    $list[$key]['addtime'] = date('Y-m-d H:i:s', $code['addtime']);
                } else {
                    $list[$key]['addtime'] = '--';
                }

                if ($code['updatetime'] != '' AND $code['updatetime'] != 0) {
                    $list[$key]['updatetime'] = date('Y-m-d H:i:s', $code['updatetime']);
                } else {
                    $list[$key]['updatetime'] = '--';
                }

                if ($code['coupon_name'] == '') {
                    $list[$key]['coupon_name'] = '--';
                }

                if ($code['cate_id'] != '') {
                    if ($code['cate_id'] == 1) {
                        $list[$key]['cate_name'] = '现金券';

                    } elseif ($code['cate_id'] == 2) {
                        $list[$key]['cate_name'] = '打折券';

                    }
                } else {
                    $list[$key]['cate_name'] = '现金券';
                }

                if ($code['coupon_value'] == '') {
                    $list[$key]['coupon_value'] = '--';
                }
            }
        }
        return $list;
    }

// 3.券种类列表
    public function getPageNumC($param, $limit)
    {
        $map = "id!='0'";
        if($param) {
            if($param['value']) {
                $map .= " AND cate_name LIKE '%" .$param['value']. "%'";
            }
        }
        $count = $this->db->from("{$this->coupon_cate_tbl} as coupon_category")->where($map)->count_all_results();
        return ['pn'=>(int) ceil($count / $limit), 'count'=>$count];
    }

    public function getCouponCateList($param, $start, $limit)
    {
        $map = "id!='0'";
        if($param) {
            if($param['value']) {
                $map .= " AND cate_name LIKE '%" .$param['value']. "%'";
            }
        }
        $count = $this->db->from("{$this->coupon_cate_tbl} as coupon_category")->where($map)->count_all_results();
        $page = (int) ceil($count / $limit);
        $items = $this->db->from("{$this->coupon_cate_tbl}")
            ->where($map)
            ->order_by('id', 'DESC')
            ->limit($limit, $start)->get()->result_array();
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                if ($value['addtime'] != 0) {
                    $items[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $items[$key]['addtime'] = '--';
                }
                if ($value['cate_desc'] =='') {
                    $items[$key]['cate_desc'] = '--';
                }
                if ($value['coupon_rule'] =='') {
                    $items[$key]['coupon_rule'] = '--';
                }
                if ($value['cate_name'] == '') {
                    $items[$key]['cate_name'] = '--';
                }
            }
        }
        return array('items' => $items, 'pn' => $page, 'count' => $count);
    }

// 4.用户领券列表
    /**
     * 获取用户领券列表的页码信息
     * @param   int     $school_id  驾校ID
     * @param   array   $param      条件
     * @param   int     $limit      限定条数
     * @return  $pageinfo
     **/
    public function getUserCouponPageNum($school_id, $param, $limit)
    {
        $map = [];
        $complex = [];
        if ($param) {
            if ($param['type'] != '') {
                $map['coupon_type'] = $param['type']; // 1: 自己领取 2：系统推送
            }

            if ( $param['status'] != '') {
                $map['coupon_status'] = $param['status']; // 1: 未使用 2：已使用 3：已过期 4：已删除
            }

            $keywords = $param['keywords'];
            if ($keywords) {
                $complex['user_name'] = $keywords;
                $complex['user_phone'] = $keywords;
                $complex['coupon_name'] = $keywords;
            }
        }

        $all_ids = [];
        if ($school_id != 0) {
            $all_ids = $this->getSchoolCoachById($school_id);
        }
        $query = $this->db
            ->from("{$this->user_coupon_tbl} as coupon")
            ->join("{$this->province_tbl} as province", 'province.provinceid=coupon.province_id', 'left')
            ->join("{$this->city_tbl} as city", 'city.cityid=coupon.city_id', 'left')
            ->join("{$this->area_tbl} as area", 'area.areaid=coupon.area_id', 'left')
            ->where($map)
            ->where_in('coupon_status', [1, 2, 3]);

        if ($school_id != 0) {
            $query = $query
                ->where_in('coupon_sender_owner_type', [1, 2])
                ->where_in('coupon_sender_owner_id', $all_ids);
        }

        if ( ! empty($complex)) {
            $query = $query
                ->group_start()
                    ->or_like($complex)
                ->group_end();
        }
        
        $count = $query->count_all_results();
        return $pageinfo = [
            'count' => $count,
            'pagenum' => ceil ( $count / $limit ),
        ];
    }

    /**
     * 获取用户领券列表的页码信息
     * @param   int     $school_id  驾校ID
     * @param   array   $param      条件
     * @param   int     $start      开始数
     * @param   int     $limit      限定条数
     * @return  $pageinfo
     **/
    public function getUserCouponList($school_id, $param, $start, $limit)
    {
        $map = [];
        $complex = [];
        if ($param) {
            if ($param['type'] != '') {
                $map['coupon_type'] = $param['type']; // 1: 自己领取 2：系统推送
            }

            if ( $param['status'] != '') {
                $map['coupon_status'] = $param['status']; // 1: 未使用 2：已使用 3：已过期 4：已删除
            }

            $keywords = $param['keywords'];
            if ($keywords) {
                $complex['user_name'] = $keywords;
                $complex['user_phone'] = $keywords;
                $complex['coupon_name'] = $keywords;
            }
        }

        $all_ids = [];
        if ($school_id != 0) {
            $all_ids = $this->getSchoolCoachById($school_id);
        }

        $query = $this->db
            ->select(
                'coupon.*,
                 province.province,
                 city.city,
                 area.area'
            )
            ->from("{$this->user_coupon_tbl} as coupon")
            ->join("{$this->province_tbl} as province", 'province.provinceid=coupon.province_id', 'left')
            ->join("{$this->city_tbl} as city", 'city.cityid=coupon.city_id', 'left')
            ->join("{$this->area_tbl} as area", 'area.areaid=coupon.area_id', 'left')
            ->where($map)
            ->where_in('coupon_status', [1, 2, 3]);

        if ($school_id != 0) {
            $query = $query
                ->where_in('coupon_sender_owner_type', [1, 2])
                ->where_in('coupon_sender_owner_id', $all_ids);
        }

        if ( ! empty($complex)) {
            $query = $query
                ->group_start()
                    ->or_like($complex)
                ->group_end();
        }
        $list = $query
            ->limit($limit, $start)
            ->order_by('id', 'desc')
            ->get()
            ->result_array();
        if ( ! empty($list)) {
            foreach ($list as $key => $coupon) {
                if ($coupon['coupon_sender_owner_type'] != '' && $coupon['coupon_sender_owner_id'] != '') {
                    $owner_type = $coupon['coupon_sender_owner_type'];
                    $owner_id = $coupon['coupon_sender_owner_id'];
                    $sender_owner_name = $this->getOwnerNameByOwnerId($owner_type, $owner_id);
                    if (!empty($sender_owner_name)) {
                        $list[$key]['owner_name'] = $sender_owner_name['owner_name'];
                    } else {
                        $list[$key]['owner_name'] = '--';
                    }
                }
                if ($coupon['addtime'] != 0) {
                    $list[$key]['addtime'] = date('Y-m-d H:i:s', $coupon['addtime']);
                } else {
                    $list[$key]['addtime'] = '--';
                }

                if ($coupon['expiretime'] != 0) {
                    $list[$key]['expiretime'] = date('Y-m-d H:i:s', (int)$coupon['expiretime']);
                } else {
                    $list[$key]['expiretime'] = '--';
                }

                if ($coupon['expiretime'] - time() <= 0) {
                    if ($coupon['coupon_status'] == 1) {
                        $list[$key]['coupon_status'] = 3;
                    }
                }

                if ($coupon['coupon_desc'] == '') {
                    $list[$key]['coupon_desc'] = '--';
                }

                if ($coupon['coupon_scope'] == 0) {
                    $list[$key]['address'] = '全国';

                } elseif ($coupon['coupon_scope'] == 1) {
                    $list[$key]['address'] = $coupon['province'];

                } elseif ($coupon['coupon_scope'] == 2) {
                    $list[$key]['address'] = $coupon['province'].$coupon['city'];

                } elseif ($coupon['coupon_scope'] == 3) {
                    $list[$key]['address'] = $coupon['province'].$coupon['city'].$coupon['area'];
                }

                if ($coupon['province_id'] == 0) {
                    $list[$key]['province'] = '';
                }
                if ($coupon['city_id'] == 0) {
                    $list[$key]['city'] = '';
                }
                if ($coupon['area_id'] == 0) {
                    $list[$key]['area'] = '';
                }

                if ($coupon['coupon_desc'] == '') {
                    $list[$key]['coupon_desc'] = '--';
                }
            }
        }
        return $list;
    }

    // 获取驾校下的教练ID
    public function getSchoolCoachById($school_id)
    {
        $map = ['coach.s_school_name_id' => $school_id];
        $coach_ids = $this->db
            ->select('coach.l_coach_id')
            ->from("{$this->coach_tbl} as coach")
            ->where($map)
            ->get()
            ->result_array();
        $list = [];
        if ( ! empty($coach_ids)) {
            foreach ($coach_ids as $index => $coach) {
                $list[] = $coach['l_coach_id'];
            }
        }
        array_unshift($list, $school_id);
        return $list;
    }

    // 删除
    public function delInfo($data)
    {
        $res = $this->db->delete($this->coupon_tbl, $data);
        return $res;
    }


    // 根据权所有者和角色类别获取券列表
    public function getCouponListByCondition($tablename, $wherecondition) {
        $query = $this->db->where($wherecondition)->get($tablename);
        return ['list'=>$query->result()];
    }

    // 获取优惠券信息包含用户领取的券信息等
    public function getCouponInfoById($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $map = array('code.id'=>$id);
        $detail = $this->db->select('code.*,coupon.*')
            ->from("{$this->coupon_code_tbl} as code")
            ->join("{$this->coupon_tbl} as coupon",'coupon.id=code.coupon_id','left')
            ->where($map)->limit(1)
            ->get()->row_array();
        if($detail['owner_type']==1) {
            $detail['owner_label'] = '教练';
        }else if($detail['owner_type']==2) {
            $detail['owner_label'] = '驾校';
        }else if($detail['owner_type']==3) {
            $detail['owner_label'] = '嘻哈平台';
        }
        return $detail;
    }

    public function getCouponListById($id)
    {
        if (!is_numeric($id)) {
            return false;
        }
        $couponlist = $this->db->select(
            'c.*,
            cc.id as cate_id,
            cc.cate_name,
            cc.cate_desc,
            cc.coupon_rule,
            p.provinceid,
            p.province,
            ct.cityid,
            ct.city,
            a.areaid,
            a.area'
            )
            ->from("{$this->coupon_tbl} as c")
            ->join("{$this->coupon_cate_tbl} as cc", 'cc.id=c.coupon_category_id', 'left')
            ->join("{$this->province_tbl} as p", 'p.provinceid=c.province_id', 'left')
            ->join("{$this->city_tbl} as ct", 'ct.cityid=c.city_id', 'left')
            ->join("{$this->area_tbl} as a", 'a.areaid=c.area_id', 'left')
            ->where(array('c.id'=>$id))
            ->get()->row_array();
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
     * 获取搜索的教练信息
     * @param   int     $school_id  驾校ID
     * @param   string  $key        关键词
     * @param   int     $limit      限定条数
     * @return  void
     **/
    public function searchCoachList($school_id, $key, $limit)
    {
        $map = [];
        if ( $school_id != 0) {
            $map['school.l_school_id'] = $school_id;
        }
        $list = $this->db 
            ->select(
                'coach.l_coach_id as owner_id,
                 coach.s_coach_name ,
                 school.s_school_name as school_name'
            )
            ->from("{$this->coach_tbl} as coach")
            ->join("{$this->school_tbl} as school", "school.l_school_id=coach.s_school_name_id", "LEFT")
            ->where($map)
            ->like('s_coach_name', $key)
            ->get()
            ->result_object();
        if ( ! empty($list)) {
            foreach ($list as $key => $value) {
                if ($school_id == 0) {
                    if (empty($value->school_name)) {
                        $list[$key]->owner_name = $value->s_coach_name;
                    } else {
                        $list[$key]->owner_name = $value->s_coach_name."(".$value->school_name.")";
                    }
                } else {
                    $list[$key]->owner_name = $value->s_coach_name;  
                }
            }
        }
        return ["list" => $list];
    }

    // 获取自定义字段的优惠券信息
    public function getCouponParamsInfo($select, $id) {
        $query = $this->db->select($select)->get_where($this->coupon_tablename, ['id'=>$id]);
        return ['list'=>$query->result()];
    }

    // 改变优惠券开启状态或者是否显示状态
    public function editCouponStatus($wherecondition, $data) {
        return $this->db->where($wherecondition)->update($this->coupon_tablename, $data);
    }

    // 根据角色类别查询角色名称列表
    public function getCouponOwnerInfo($owner_type)
    {
        if (!is_numeric($owner_type)) {
            return false;
        }
        $items = array();
        if ($owner_type == 1) {
            $where = array(
                'coupon_supported'      => 1,
                'order_receive_status'  => 1, // 1:在线 2:不在线
                'user.i_user_type'      => 1, // 0:student 1:coach
                'user.i_status'         => 0, // 0:用户存在 1:用户已被删除
            );
            $getCoachList = $this->db->select('l_coach_id, s_coach_name')
                ->from("{$this->coach_tbl} as coach")
                ->join("{$this->user_tbl} as user", 'user.l_user_id=coach.user_id', 'left')
                ->where($where)
                ->get()->result_array();
            if($getCoachList) {
                $items = $getCoachList;
                foreach($items as $k=>$v) {
                    $items[$k]['value'] = $v['l_coach_id'];
                    $items[$k]['label'] = $v['s_coach_name'];
                }
            }
        } elseif ($owner_type == 2) {
            $getSchoolList = $this->db->select('l_school_id, s_school_name')
                ->from("{$this->school_tbl}")
                ->where("l_school_id!='0' AND is_show='1'")
                ->get()->result_array();
            if ($getSchoolList) {
                $items = $getSchoolList;
                foreach($items as $k=>$v) {
                    $items[$k]['value'] = $v['l_school_id'];
                    $items[$k]['label'] = $v['s_school_name'];
                }
            }
        } else if($owner_type == 3) {
            $items = array(array('value'=>'xiha','label'=>'嘻哈平台'));
        }
        return $items;
    }

    //  根据owner_id获取单一驾校、教练、和嘻哈后台名称
    public function getOwnerNameByOwnerId($owner_type, $owner_id)
    {
        if (!is_numeric($owner_id) && !is_numeric($owner_type)) {
            return false;
        }
        $items = array();
        if ($owner_type == 1) {
            $getCoachList = $this->db->select('l_coach_id as owner_id, s_coach_name as owner_name')
                ->from("{$this->coach_tbl} as c")
                ->where(array('l_coach_id'=>$owner_id))
                ->get()->row_array();
            $detail = $getCoachList;
        } else if ($owner_type == 2) {
            $getSchoolList = $this->db->select('l_school_id as owner_id, s_school_name as owner_name')
                ->from("{$this->school_tbl}")
                ->where(array('l_school_id'=>$owner_id))
                ->get()->row_array();
            $detail = $getSchoolList;
        } else if ($owner_type == 3) {
            $getManagerList = array();
            $getManagerList['owner_id'] = 0;
            $getManagerList['owner_name'] = '嘻哈平台';
            $detail = $getManagerList;
        }
        return $detail;
    }

    /*检车教练添加的优惠券过时否*/
    public function checkCouponTime ($owner_type, $owner_id, $owner_name) {
        if (!is_numeric($owner_type) && !$owner_id && $owner_name) {
            return false;
        }
        $time = time();
        $map = "owner_type='{$owner_type}' AND owner_id='{$owner_id}' AND owner_name='{$owner_name}' AND expiretime>='{$time}'";
        $result = $this->db->from("{$this->coupon_tbl}")->where($map)->get()->row_array();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /*获取优惠券相关信息*/
    public function getCouponName () {
        $nowtime = time();
        $couponlist = $this->db->select('coupon.id as coupon_id,coupon.coupon_name as coupon_name')
            ->from("{$this->coupon_tbl} as coupon")
            ->where("expiretime>='{$nowtime}'")->get()->result_array();
        if (!empty($couponlist)) {
            return $couponlist;
        } else {
            return array();
        }
    }

    /*获取券兑换码根据ID**/
    public function getCouponCodeById ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $where = array('code.id' => $id);
        $couponcodelist = $this->db->select(
            'code.id as id,
             code.coupon_id as coupon_id,
             code.is_used as is_used,
             code.coupon_code as coupon_code,
             code.addtime as addtime,
             coupon.coupon_name as coupon_name'
             )
             ->from("{$this->coupon_code_tbl} as code")
             ->join("{$this->coupon_tbl} as coupon", 'coupon.id=code.coupon_id', 'left')
             ->where($where)->get()->row_array();
        if (!empty($couponcodelist)) {
            return $couponcodelist;
        } else {
            array();
        }
    }

    //  添加优惠券
    public function addCouponCate($data)
    {
        $data['addtime'] = isset($data['addtime']) ? $data['addtime'] : time();
        $query = $this->db->insert($this->coupon_cate_tbl, $data);
        return $this->db->insert_id();
    }

    public function cateDetail($id)
    {
        if($id != (int)$id) {
            return false;
        }
        $detail = $this->db->from("{$this->coupon_cate_tbl}")->where(array('id'=>$id))->get()->row_array();
        return $detail;
    }

    public function editCouponCate($id, $data)
    {
        if($id != (int)$id) {
            return false;
        }
        $query = $this->db->update("{$this->coupon_cate_tbl}", $data, array('id'=>$id));
        return $query;
    }

    public function delCate($id)
    {
        if($id != (int)$id) {
            return false;
        }
        $query = $this->db->delete($this->coupon_cate_tbl, array('id'=>$id));
        return $query;
    }

    public function userCouponDetail($id)
    {
        if($id != (int)$id) {
            return false;
        }
        $detail = $this->db->from("{$this->user_coupon_tbl}")->where(array('id'=>$id))->get()->row_array();
        return $detail;
    }

    public function delUserCoupon($id)
    {
        if($id != (int)$id) {
            return false;
        }
        $query = $this->db->delete($this->user_coupon_tbl, array('id'=>$id));
        return $query;
    }
}
?>
