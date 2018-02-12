<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mads extends CI_Model {

    public $ads_tbl = 'cs_ads';
    public $ads_info_tbl = 'cs_ads_info';
    public $ads_level_tbl = 'cs_ads_level';
    public $ads_order_tbl = 'cs_ads_order';
    public $ads_position_tbl = 'cs_ads_position';
    public $province_tbl = 'cs_province';
    public $city_tbl = 'cs_city';
    public $area_tbl = 'cs_area';
    public $school_tbl = 'cs_school';


    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * 获取广告管理的页码信息
     * @param $data
     * @return void
     **/
    public function getAdsPageNum($param, $type, $limit)
    {
        $map = [];
        $complex = [];
        if ( $type == 'position' ) { // 广告位的数量获取
            if ($param) {
                if ($param['keywords'] != '') {
                    $map['title'] = $param['keywords'];
                    $map['description'] = $param['keywords'];
                }
            }

            $count = $this->db
                ->from($this->ads_position_tbl)
                ->or_like($map)
                ->count_all_results();

        } elseif ( $type == 'level' ) {

            if ($param) {
                if ($param['keywords'] != '') {
                    $map['level_title'] = $param['keywords'];
                    $map['level_intro'] = $param['keywords'];
                }
            }

            $count = $this->db
                ->from($this->ads_level_tbl)
                ->or_like($map)
                ->count_all_results();
            
        } else if ( $type == 'ads' ) {

            if ($param) {

                if ( $param['ads_status'] != '' ) {
                    $map['ads.ads_status'] = $param['ads_status'];
                }

                // if ($param['keywords'] != '') {
                    $complex['ads.title'] = $param['keywords'];
                    $complex['ads.intro'] = $param['keywords'];
                // }
            }

             $count = $this->db
                ->from("{$this->ads_tbl} as ads")
                ->join("{$this->ads_position_tbl} as position", "position.scene = ads.scene_id", "LEFT")
                ->join("{$this->ads_level_tbl} as level", "level.id = ads.level_id", "LEFT")
                ->where($map)
                ->group_start()
                    ->or_like($complex)
                ->group_end()
                ->count_all_results();

        } else if ( $type == 'order' ) {

            if ($param) {
                if ( $param['order_status'] != '' ) {
                    $map['order.order_status'] = $param['order_status'];
                } 

                if ( $param['pay_type'] != '' ) {
                    $map['order.pay_type'] = $param['pay_type'];
                }
                if ( $param['device'] != '' ) {
                    $map['order.device'] = $param['device'];
                }

                // if ($param['keywords'] != '') {
                    $complex['order.buyer_name'] = $param['keywords'];
                    $complex['order.order_no'] = $param['keywords'];
                    $complex['order.unique_trade_no'] = $param['keywords'];
                // }
            }

            $count = $this->db
                ->from("{$this->ads_order_tbl} as order")
                ->join("{$this->ads_tbl} as ads", "ads.id = order.ads_id", "LEFT")
                ->where($map)
                ->group_start()
                    ->or_like($complex)
                ->group_end()
                ->count_all_results();
        }


        $pagenum = (int) ceil( $count / $limit);
        $page_info = [
            'pagenum' => $pagenum,
            'count' => $count
        ];

        return $page_info;

    }

    /**
     * 获取广告的信息
     * @param  [$param, $type, $start, $limit]
     * @return void
     **/
    public function getAdsList($param, $type, $start, $limit)
    {
        $map = [];
        $complex = [];
        if ( $type == 'position' ) { // 广告位的数据过去
            if ($param) {
                if ($param['keywords'] != '') {
                    $map['title'] = $param['keywords'];
                    $map['description'] = $param['keywords'];
                }
            }
            
            $list = $this->db 
                ->from($this->ads_position_tbl)
                ->or_like($map)
                ->limit($limit, $start)
                ->order_by('id', 'desc')
                ->get()
                ->result_array();
            if ( ! empty($list)) {
                foreach ($list as $key => $value) {
                    if ($value['addtime'] != '' AND $value['addtime'] != 0) {
                        $list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                    } else {
                        $list[$key]['addtime'] = '--';
                    }
                }
            }

        } else if ( $type == 'level' ) { // 广告等级的数据展示]

             if ($param) {
                if ($param['keywords'] != '') {
                    $map['level_title'] = $param['keywords'];
                    $map['level_intro'] = $param['keywords'];
                }
            }

            $list = $this->db 
                ->from($this->ads_level_tbl)
                ->or_like($map)
                ->limit($limit, $start)
                ->order_by('level_id', 'desc')
                ->order_by('id', 'desc')
                ->get()
                ->result_array();
            if ( ! empty($list)) {
                foreach ($list as $key => $value) {
                    if ($value['addtime'] != '' AND $value['addtime'] != 0) {
                        $list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                    } else {
                        $list[$key]['addtime'] = '--';
                    }
                }
            }

        } else if ( $type == 'ads' ) {

            if ($param) {

                if ( $param['ads_status'] != '' ) {
                    $map['ads.ads_status'] = $param['ads_status'];
                }

                // if ($param['keywords'] != '') {
                $complex['ads.title'] = $param['keywords'];
                $complex['ads.intro'] = $param['keywords'];
                // }
            }

            $list = $this->db 
                ->from("{$this->ads_tbl} as ads")
                ->join("{$this->ads_position_tbl} as position", "position.scene = ads.scene_id", "LEFT")
                ->join("{$this->ads_level_tbl} as level", "level.id = ads.level_id", "LEFT")
                ->join("{$this->ads_info_tbl} as info", "info.ads_id = ads.id", "LEFT")
                ->select(
                    'ads.*,
                     level.level_title,
                     position.title as ps_title,
                     position.scene,
                     info.resource_type,
                     info.device'
                )
                ->where($map)
                ->group_start()
                    ->or_like($complex)
                ->group_end()
                ->limit($limit, $start)
                ->order_by('ads.sort_order', 'desc')
                ->order_by('ads.id', 'desc')
                ->get()
                ->result_array();

            if ( ! empty($list)) {
                foreach ($list as $key => $value) {
                    $province_info = $this->getCityInfoByCondition($value['province_id'], "province");
                    if ( ! empty($province_info)) {
                        $list[$key]['province'] = $province_info['province'];
                    } else {
                        $list[$key]['province'] = '--';
                    }

                    $city_info = $this->getCityInfoByCondition($value['city_id'], "city");
                    if ( ! empty($city_info)) {
                        $list[$key]['city'] = $city_info['city'];
                    } else {
                        $list[$key]['city'] = '--';
                    }

                    $area_info = $this->getCityInfoByCondition($value['area_id'], "area");
                    if ( ! empty($area_info)) {
                        $list[$key]['area'] = $area_info['area'];
                    } else {
                        $list[$key]['area'] = '--';
                    }

                    if ($value['device'] == "1,2") {
                        $list[$key]['device'] = '3';
                    }

                    if ($value['addtime'] != '' AND $value['addtime'] != 0) {
                        $list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                    } else {
                        $list[$key]['addtime'] = '--';
                    }

                    if ($value['limit_time'] != '' AND $value['limit_time'] != 0) {
                        $list[$key]['limit_time'] = date('Y-m-d H:i:s', $value['limit_time']);
                    } else {
                        $list[$key]['limit_time'] = '--';
                    }

                }
            }

        } else if ( $type == 'order' ) {
            if ($param) {
                if ( $param['order_status'] != '' ) {
                    $map['order.order_status'] = $param['order_status'];
                } 
                
                if ( $param['pay_type'] != '' ) {
                    $map['order.pay_type'] = $param['pay_type'];
                }
                if ( $param['device'] != '' ) {
                    $map['order.device'] = $param['device'];
                }

                // if ($param['keywords'] != '') {
                    $complex['order.buyer_name'] = $param['keywords'];
                    $complex['order.order_no'] = $param['keywords'];
                    $complex['order.unique_trade_no'] = $param['keywords'];
                // }
            }

            $list = $this->db
                ->from("{$this->ads_order_tbl} as order")
                ->join("{$this->ads_tbl} as ads", "ads.id = order.ads_id", "LEFT")
                ->select(
                    'order.*,
                     ads.title'
                )
                ->where($map)
                ->group_start()
                    ->or_like($complex)
                ->group_end()
                ->limit($limit, $start)
                ->order_by('order.id', 'desc')
                ->get()
                ->result_array();
            
            if ( ! empty($list)) {
                foreach ( $list as $key => $value) {

                    if ($value['addtime'] != '' AND $value['addtime'] != 0) {
                        $list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                    } else {
                        $list[$key]['addtime'] = '--';
                    }

                    if ($value['over_time'] != '' AND $value['over_time'] != 0) {
                        $list[$key]['over_time'] = date('Y-m-d H:i:s', (int)$value['over_time']);
                    } else {
                        $list[$key]['over_time'] = '--';
                    }

                    $list[$key]['resource_url'] = $this->mbase->buildUrl($value['resource_url']);

                }

            }

        }

        $page_info = $this->getAdsPageNum($param, $type, $limit);
        $pagenum = $page_info['pagenum'];
        $count = $page_info['count'];

        $adslist = [
            'list' => $list,
            'pagenum' => $pagenum,
            'list' => $list
        ];

        return $adslist;

    }

    /**
     * 获取省市区信息的信息
     * @param $province_id | $city_id | $area_id , $name
     * @return $list
     **/
    public function getCityInfoByCondition($chanage, $name)
    {
        if ($name == "area") {
            $condition = [
                'area.areaid' => $chanage
            ];
            $result = $this->db
                ->from("{$this->area_tbl} as area")
                ->where($condition)
                ->select(
                    'area.id,
                    area.areaid,
                    area.area,
                    area.fatherid'
                )
                ->get()
                ->result_array();

        } elseif ($name == 'city') {
            $condition = [
                'city.cityid' => $chanage
            ];
            $result = $this->db
                ->from("{$this->city_tbl} as city")
                ->where($condition)
                ->select(
                    'city.id,
                     city.cityid,
                     city.city,
                     city.fatherid,
                     city.leter,
                     city.acronym,
                     city.spelling,
                     city.is_hot'
                )
                ->get()
                ->result_array();

        } elseif ($name == 'province') {
            $condition = [
                'province.provinceid' => $chanage
            ];
            $result = $this->db
                ->from("{$this->province_tbl} as province")
                ->where($condition)
                ->select(
                    'province.id,
                     province.provinceid,
                     province.province'
                )
                ->get()
                ->result_array();
        }

        $list = [];
        if ($result) {
            foreach ($result as $index => $value) {
                $list = $value;
            }
        }
        return $list;

    }


    /**
     * 新增数据
     * @param $data
     * @param $tblname
     * @return void
     **/
    public function add($data, $tblname)
    {
         $result = $this->db
            ->insert($tblname, $data);
        return $this->db->insert_id();
    }

    /**
     * 修改数据
     * @param $data
     * @param $tblname
     * @return void
     **/
    public function edit($field, $data, $tblname)
    {
        $condition = [
            $field => $data['id'],
        ];
        $result = $this->db
            ->from($tblname)
            ->where($condition)
            ->update($tblname, $data);
        return $result;
    }

    /**
     * 获取买家号码
     * @param $school_id
     * @return void
     **/
    public function getBuyerPhone($school_id)
    {
        $school_list = $this->db
            ->from($this->school_tbl)
            ->where('l_school_id', $school_id)
            ->select('s_frdb_tel, s_frdb_mobile')
            ->get()
            ->result_array();
        $list = [];
        if ( ! empty($school_list)) {
            foreach ($school_list as $key => $value) {
                if ($value['s_frdb_tel'] != '') {
                    $buyer_phone = $value['s_frdb_tel'];

                } else if ($value['s_frdb_mobile'] != '') {
                    $buyer_phone = $value['s_frdb_mobile'];

                }
            }
        }
        return $buyer_phone;
    }

    /**
     * 通过用户id获取用户名称
     * @param $buyer_id
     * @return void
     **/
    public function getBuyerName($buyer_id)
    {
        $list = $this->db
            ->from($this->school_tbl)
            ->where('l_school_id', $buyer_id)
            ->select('s_school_name as buyer_name')
            ->get()
            ->result_array();
        $buyer_name = '';
        if ( ! empty($list)) {
            foreach ( $list as $key => $value) {
                $buyer_name = $value['buyer_name'];
            }
        }

        return $buyer_name;

    }

    /**
     * 获取广告信息
     * @param
     * @return void
     **/   
    public function getAdsInfo()
    {
        $ads_list = $this->db
            ->select(
                'ads.id as ads_id,
                 ads.title as ads_title'
            )
            ->from("{$this->ads_tbl} as ads")
            ->get()
            ->result_array();

        return $ads_list;

    }


    /**
     * 获取广告位信息
     * @param 
     * @return void
     **/
    public function getSceneList()
    {
        $positionlist = $this->db
            ->from($this->ads_position_tbl)
            ->select(
                'scene,
                 title'
            )
            ->get()
            ->result_array();
        return $positionlist;
    }

    /**
     * 获取广告等级信息
     * @param 
     * @return void
     **/
    public function getAdsLevelList()
    {
        $levellist = $this->db
            ->from($this->ads_level_tbl)
            ->select(
                'level_id,
                 level_title'
            )
            ->get()
            ->result_array();
        return $levellist;
    }

    /**
     * 获取单条数据
     * @param  $id
     * @return void
     **/
    public function getAdsInfoById($id, $type)
    {
        $list = [];
        if ( $type == 'position') { // 获取单条的广告位

            $pos_list = $this->db 
                ->from($this->ads_position_tbl)
                ->where('id', $id)
                ->get()
                ->result_array();
            if ( ! empty($pos_list)) {
                foreach ($pos_list as $key => $value) {
                    $list = $value;
                }
            }

        } else if ( $type == 'level' ) { // 获取单条的广告等级

            $level_list = $this->db 
                ->from($this->ads_level_tbl)
                ->where('id', $id)
                ->get()
                ->result_array();
            if ( ! empty($level_list)) {
                foreach ($level_list as $key => $value) {
                    $list = $value;
                }
            }

        } else if ( $type == 'ads' ) { // 获取单条的广告招租

            $ads_list = $this->db 
                ->select(
                    'ads.*,
                     info.id as info_id,
                     info.device,
                     info.resource_type'
                )
                ->from("{$this->ads_tbl} as ads")
                ->join("{$this->ads_info_tbl} as info", "info.ads_id = ads.id", "LEFT")
                ->where('ads.id', $id)
                ->get()
                ->result_array();
            $list = [];
            if ( ! empty($ads_list)) {
                foreach ($ads_list as $key => $value) {
                    $province_info = $this->getCityInfoByCondition($value['province_id'], "province");
                    if ( ! empty($province_info)) {
                        $value['province'] = $province_info['province'];
                    } else {
                        $value['province'] = '';
                    }

                    $city_info = $this->getCityInfoByCondition($value['city_id'], "city");
                    if ( ! empty($city_info)) {
                        $value['city'] = $city_info['city'];
                    } else {
                        $value['city'] = '';
                    }

                    $area_info = $this->getCityInfoByCondition($value['area_id'], "area");
                    if ( ! empty($area_info)) {
                        $value['area'] = $area_info['area'];
                    } else {
                        $value['area'] = '';
                    }

                    if ($value['device'] == "1,2") {
                        $value['device'] = '3';
                    }

                    if ($value['limit_time'] != '' OR $value['limit_time'] != 0) {
                        $value['limit_time'] = date('Y-m-d H:i:s', $value['limit_time']);
                    }
                    $list = $value;
                }
            }
        } else if ( $type == 'order' ) {

            $ads_list = $this->db 
                ->select(
                    'order.*,
                     ads.title'
                )
                ->from("{$this->ads_order_tbl} as order")
                ->join("{$this->ads_tbl} as ads", "ads.id = order.ads_id", "LEFT")
                ->where('order.id', $id)
                ->get()
                ->result_array();
            if ( ! empty($ads_list)) {
                foreach ($ads_list as $key => $value) {
                    if ($value['over_time'] != '' OR $value['over_time'] != 0) {
                        $value['over_time'] = date('Y-m-d H:i:s', $value['over_time']);
                    }
                    $list = $value;
                }
            }

        }
        
        return $list;

    }

    /**
     * 删除数据
     * @param [$id, $field, $tblname]
     * 
     * @return void
     **/
    public function delAjax($id_arr, $field, $tblname)
    {
        $result = $this->db
            ->from($tblname)
            ->where_in($field, $id_arr)
            ->delete();

        return $result;

    }

    /**
     * 检查广告位场景是否存在
     * @param 
     * @return void
     **/
    public function checkPosition($scene)
    {
        $result = $this->db
            ->from($this->ads_position_tbl)
            ->where('scene', $scene)
            ->get()
            ->result_array();
        $list = [];
        if ( ! empty($result) ) {
            foreach ( $result as $key => $value) {
                $list = $value;
            }
        }

        return $list;

    }






}