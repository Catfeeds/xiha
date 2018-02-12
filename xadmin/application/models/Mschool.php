<?php
    // 首页
defined('BASEPATH') OR exit('No direct script access allowed');

class Mschool extends CI_Model {

    public $school_tablename = 'cs_school';
    public $shifts_tablename = 'cs_school_shifts';
    public $train_tablename = 'cs_school_train_location';
    public $liceconfig_tablename = 'cs_license_config';
    public $saccount_tablename = 'cs_school_account';
    public $site_tablename = 'cs_site';
    public $coach_tablename = 'cs_coach';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('mcoach');
        $this->load->model('mcoupon');
    }

// 1.驾校列表
    /**
     * 获取驾校页数
     * @param  $param, $limit
     * @return void
     **/
    public function getSchoolPageNum($param, $limit)
    {
        $map = [];
        $complex = [];
        if ($param) {
            if ($param['nature'] != '') {
                $map['i_dwxz'] = $param['nature'];
            }

            if ($param['hot'] != '') {
                $map['is_hot'] = $param['hot'];
            }

            if ($param['show'] != '') {
                $map['is_show'] = $param['show'];
            }

            if ($param['brand'] != '') {
                $map['brand'] = $param['brand'];
            }

            if ($param['nature'] != '') {
                $map['i_dwxz'] = $param['nature'];
            }

            $complex['s_school_name'] = $param['keywords'];
            $complex['s_frdb'] = $param['keywords'];
            $complex['s_frdb_mobile'] = $param['keywords'];
            $complex['s_address'] = $param['keywords'];
        }

        $count = $this->db->from($this->school_tablename)
            ->where($map)
            ->group_start()
                ->or_like($complex)
            ->group_end()
            ->count_all_results();

        $pageinfo = [
            'pagenum' => (int) ceil( $count / $limit),
            'count' => $count
        ];

        return $pageinfo;

    }

    /**
     * 获取驾校列表
     * @param $param | $limit | $start
     * @return void
     **/
    public function getSchoolListByCondition($param, $start, $limit)
    {
        $map = [];
        $complex = [];
        if ($param) {
            if ($param['nature'] != '') {
                $map['i_dwxz'] = $param['nature'];
            }

            if ($param['hot'] != '') {
                $map['is_hot'] = $param['hot'];
            }

            if ($param['show'] != '') {
                $map['is_show'] = $param['show'];
            }

            if ($param['brand'] != '') {
                $map['brand'] = $param['brand'];
            }

            if ($param['nature'] != '') {
                $map['i_dwxz'] = $param['nature'];
            }

            $complex['s_school_name'] = $param['keywords'];
            $complex['s_frdb'] = $param['keywords'];
            $complex['s_frdb_mobile'] = $param['keywords'];
            $complex['s_address'] = $param['keywords'];
        }

        $school_list = $this->db->from($this->school_tablename)
            ->where($map)
            ->group_start()
                ->or_like($complex)
            ->group_end()
            ->limit($limit, $start)
            ->order_by('s_order', 'asc')
            ->order_by('l_school_id', 'desc')
            ->get()
            ->result_array();
        if ( ! empty($school_list)) {
            foreach ($school_list as $key => $value) {
                if ($value['addtime'] != '') {
                    $school_list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $school_list[$key]['addtime'] = '--';
                }

                if ($value['s_school_name'] == '') {
                    $school_list[$key]['s_school_name'] = '--';
                }

                if ($value['s_frdb_mobile'] == '') {
                    $school_list[$key]['s_frdb_mobile'] = '--';
                }

                if ($value['s_frdb'] == '') {
                    $school_list[$key]['s_frdb'] = '--';
                }

                if ($value['s_frdb_tel'] == '') {
                    $school_list[$key]['s_frdb_tel'] = '--';
                }

                if ($value['s_zzjgdm'] == '') {
                    $school_list[$key]['s_zzjgdm'] = '--';
                }

                if ($value['s_yh_name'] == '') {
                    $school_list[$key]['s_yh_name'] = '--';
                }

                if ($value['s_yh_huming'] == '') {
                    $school_list[$key]['s_yh_huming'] = '--';
                }

                if ($value['s_yh_zhanghao'] == '') {
                    $school_list[$key]['s_yh_zhanghao'] = '--';
                }

                if ($value['s_address'] == '') {
                    $school_list[$key]['s_address'] = '--';
                }

            }
        }

        return $school_list;

    }

    // 获取教练所在的驾校信息
    public function getSchoolInfoByCoachId($coach_id)
    {
        $school_list = $this->db
            ->select(
                'school.l_school_id,
                 school.s_school_name,
                 coach.l_coach_id,
                 coach.s_coach_name'
            )
            ->from("{$this->school_tablename} as school")
            ->join("{$this->coach_tablename} as coach", "coach.s_school_name_id=school.l_school_id", "LEFT")
            ->where('l_coach_id', $coach_id)
            ->get()
            ->result_array();
        if ( ! empty($school_list)) {
            foreach ($school_list as $key => $value) {
                $list = $value;
            }
        }
        return $list;
    }


    public function getSchoolList($start, $limit)
    {
        $query = $this->db->order_by('l_school_id', 'DESC')->get($this->school_tablename, $limit, $start);
        foreach($query->result() as $key => $value) {
            $query->result()[$key]->addtime = date('Y-m-d H:i:s', $value->addtime);
        }
        return ['list'=>$query->result()];
    }

    public function addSchoolInfo($data) {
        $this->db->insert($this->school_tablename, $data);
        return $this->db->insert_id();
    }

    public function editSchoolInfo($data) {
        return $this->db->where('l_school_id', $data['l_school_id'])->update($this->school_tablename, $data);
    }

    public function delInfo($tbname, $data) {
        return $this->db->delete($tbname, $data);
    }

    public function getSchoolInfo($id) {
        $query = $this->db->get_where($this->school_tablename, ['l_school_id'=>$id]);
        $school_list = $query->row_array();
        if ( ! empty($school_list)) {
            $school_list['schoollicence'] = $this->mbase->buildUrl($school_list['s_yyzz']);
            $school_list['schoolthumb'] = $this->mbase->buildUrl($school_list['s_thumb']);
        }
        return $school_list;
    }

    // 获取自定义字段的驾校数据
    public function getSchoolParamsInfo($select, $id, $filters = false) {
        $query = $this->db->select($select)->get_where($this->school_tablename, ['l_school_id'=>$id]);
        if ($filters) {
            return $query->row_array();
        } else {
            return ['list'=>$query->result()];
        }
    }

// 2.班制管理
    /**
     * 获取班制页码信息
     * @param $school_id -> 驾校ID, $param->搜索条件, $limit->每页的个数限制, $type->教练还是驾校
     * @return void
     **/
    public function getShiftsPageNum($school_id, $param, $limit, $type)
    {
        $map = [];
        $complex = [];
        $where = [];
        if ($param) {
            if ($param['promote'] != '') {
                $map['is_promote'] = $param['promote'];
            }

            if ($param['package'] != '') {
                $map['is_package'] = $param['package'];
            }

            if ($param['del'] != '') {
                $map['deleted'] = $param['del'];
            }
            
            if ($param['promote'] != '') {
                $map['is_promote'] = $param['promote'];
            }

            if ($type == "school") {
                $complex['school.s_school_name'] = $param['keywords'];
                $complex['shifts.sh_title'] = $param['keywords'];
            } else {
                $complex['school.s_school_name'] = $param['keywords'];
                $complex['coach.s_coach_name'] = $param['keywords'];
                $complex['shifts.sh_title'] = $param['keywords'];
            }

        }

        if ($school_id != 0) {
            $map['shifts.sh_school_id'] = $school_id;
        }
       
        if ($type == "school") {
            $count = $this->db
                ->from("{$this->shifts_tablename} as shifts")
                ->join("{$this->school_tablename} as school", "school.l_school_id=shifts.sh_school_id", "LEFT")
                ->where($map)
                ->group_start()
                    ->or_like($complex)
                ->group_end()
                ->group_start()
                    ->or_where('coach_id', NULL)
                    ->or_where('coach_id', 0)
                    ->or_where('coach_id', '')
                ->group_end()
                ->count_all_results();
        } elseif ($type == 'coach') {
            $where = ['', 'NULL', '0'];
            $count = $this->db
                ->from("{$this->shifts_tablename} as shifts")
                ->join("{$this->school_tablename} as school", "school.l_school_id=shifts.sh_school_id", "LEFT")
                ->join("{$this->coach_tablename} as coach", "coach.l_coach_id=shifts.coach_id", "LEFT")
                ->where($map)
                ->where_not_in('coach_id', $where)
                ->group_start()
                    ->or_like($complex)
                ->group_end()
                ->count_all_results();
        }

        $page_info = [
            'pagenum' => (int) ceil($count / $limit),
            'count' => $count
        ];
        return $page_info;
    }

   /**
     * 获取班制页码信息
     * @param $school_id 驾校ID, 
     * @param $param 搜索条件,
     * @param $start 后一页开始数,
     * @param $limit 每页的个数限制
     * @return void
     **/
    public function getShftsLists($school_id, $param, $start, $limit, $type)
    {
        $map = [];
        $complex = [];
        $where = [];
        if ($param) {
            if ($param['promote'] != '') {
                $map['is_promote'] = $param['promote'];
            }

            if ($param['package'] != '') {
                $map['is_package'] = $param['package'];
            }

            if ($param['del'] != '') {
                $map['deleted'] = $param['del'];
            }
            
            if ($param['promote'] != '') {
                $map['is_promote'] = $param['promote'];
            }

            if ($type == "school") {
                $complex['school.s_school_name'] = $param['keywords'];
                $complex['shifts.sh_title'] = $param['keywords'];
            } else {
                $complex['school.s_school_name'] = $param['keywords'];
                $complex['coach.s_coach_name'] = $param['keywords'];
                $complex['shifts.sh_title'] = $param['keywords'];
            }

        }

        if ($school_id != 0) {
            $map['shifts.sh_school_id'] = $school_id;
        }
        
        if ( $type == 'school') {
            $shiftslist = $this->db
                ->select(
                    'shifts.*,
                     school.s_school_name as school_name,
                     school.l_school_id as school_id'
                )
                ->from("{$this->shifts_tablename} as shifts")
                ->join("{$this->school_tablename} as school", "school.l_school_id=shifts.sh_school_id", "LEFT")
                ->where($map)
                ->group_start()
                    ->or_where('coach_id', NULL)
                    ->or_where('coach_id', 0)
                    ->or_where('coach_id', '')
                ->group_end()
                ->group_start()
                    ->or_like($complex)
                ->group_end()
                ->limit($limit, $start)
                ->order_by('shifts.order', 'desc')
                ->order_by('shifts.id', 'desc')
                ->get()
                ->result_array();
        } else {
            $where = ['', 'NULL', '0'];
            $shiftslist = $this->db
                ->select(
                    'shifts.*,
                     school.s_school_name as school_name,
                     school.l_school_id as school_id,
                     coach.s_coach_name as coach_name,'
                )
                ->from("{$this->shifts_tablename} as shifts")
                ->join("{$this->school_tablename} as school", "school.l_school_id=shifts.sh_school_id", "LEFT")
                ->join("{$this->coach_tablename} as coach", "coach.l_coach_id=shifts.coach_id", "LEFT")
                ->where($map)
                ->where_not_in('coach_id', $where)
                ->group_start()
                    ->or_like($complex)
                ->group_end()
                ->limit($limit, $start)
                ->order_by('shifts.order', 'desc')
                ->order_by('shifts.id', 'desc')
                ->get()
                ->result_array();
        }
        if ( ! empty($shiftslist) ) {
            foreach ($shiftslist as $key => $value) {
                if ($value['addtime'] != '') {
                    $shiftslist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $shiftslist[$key]['addtime'] = '--';
                }
                if ($value['updatetime'] != '') {
                    $shiftslist[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $shiftslist[$key]['updatetime'] = '--';
                }

                if ($value['school_name'] == '') {
                    $shiftslist[$key]['school_name'] = '--';
                }

                if ($value['sh_title'] == '') {
                    $shiftslist[$key]['sh_title'] = '--';
                }

                if ($value['sh_license_name'] == '') {
                    $shiftslist[$key]['sh_license_name'] = '--';
                }

                if ($value['sh_original_money'] == '') {
                    $shiftslist[$key]['sh_original_money'] = '--';
                }

                if ($value['sh_money'] == '') {
                    $shiftslist[$key]['sh_money'] = '--';
                }

                $shiftslist[$key]['sh_imgurl'] = $this->mbase->buildUrl($value['sh_imgurl']);

                $coupon_info = $this->mcoupon->getCouponInfoById($this->mcoupon->coupon_tablename, ['id'=>$value['coupon_id']]);
                if ( ! empty($counpon_info)) {
                    $shiftslist[$key]['coupon_info'] = $coupon_info;
                    if ( $coupon_info['name'] != '') {
                        $shiftslist[$key]['coupon_name'] = $coupon_info['coupon_name'];
                    } else {
                        $shiftslist[$key]['coupon_name'] = '--';
                    }
                } else {
                    $shiftslist[$key]['coupon_name'] = '--';
                    $shiftslist[$key]['coupon_info'] = '';
                }

                if ($type == 'coach') {
                    if ($value['coach_name'] == '') {
                        $shiftslist[$key]['coach_name'] = '--';
                    }
                }
                // if ($value['coach_id'] != 0 OR $value['coach_id'] !='' OR $value['coach_id'] != NULL) {
                //     $coach_info = $this->getCoachInfo($value['coach_id']);
                //     if (!empty($coach_info)) {
                //         $shiftslist[$key]['coach_name'] = $coach_info['s_coach_name'];
                //     } else {
                //         $shiftslist[$key]['coach_name'] = '--';
                        
                //     }
                // } else {
                //     $shiftslist[$key]['coach_name'] = '--';
                // }
            }
        }

        return $shiftslist;

    }
    
    /**
     * 获取教练信息
     * @param $coach_id
     * @return void
     **/
    public function getCoachInfo($coach_id)
    {
        $where = ['l_coach_id' => $coach_id];
        $query = $this->db->get_where($this->coach_tablename, $where);
        return $query->row_array();
    }

    // 获取班制详情
    public function getShiftsInfo($id, $school_id)
    {
        $shifts_list = $this->db
            ->select(
                'shifts.*,
                 coach.l_coach_id,
                 coach.s_coach_name,
                 school.l_school_id,
                 school.s_school_name'
            )
            ->from("{$this->shifts_tablename} as shifts")
            ->join("{$this->school_tablename} as school", "school.l_school_id=shifts.sh_school_id", "LEFT")
            ->join("{$this->coach_tablename} as coach", "coach.l_coach_id=shifts.coach_id", "LEFT")
            ->where('shifts.id', $id)
            ->get()
            ->result_array();
        $list = [];
        if ( ! empty($shifts_list)) {
            foreach ($shifts_list as $key => $value) {
                $list = $value;
                $list['sid'] = $school_id;
                $list['imgurl'] = $this->mbase->buildUrl($value['sh_imgurl']);
            }
        }
        return $list;
    }

    // 获取班制列表
    public function getShftsList($start='', $limit='') {
        $query = $this->db->order_by('id', 'DESC')->get($this->shifts_tablename, $limit, $start);
        foreach($query->result() as $key => $value) {
            $query->result()[$key]->addtime = date('Y-m-d H:i:s', $value->addtime);
            $query->result()[$key]->school_info = $this->getSchoolInfo($value->sh_school_id);
            $query->result()[$key]->coach_info = $this->mcoach->getCoachInfo($value->coach_id);
            $query->result()[$key]->coupon_info = $this->mcoupon->getCouponInfoById($this->mcoupon->coupon_tablename, ['id'=>$value->coupon_id]);
        }
        return ['list'=>$query->result()];
    }

    // 根据条件获取班制列表
    public function getShiftsListByCondition($select, $wherecondition) {
        $query = $this->db->select($select)->order_by('id', 'DESC')->get_where($this->shifts_tablename, $wherecondition);
        return ['list'=>$query->result()];
    }

    // 获取班制详情
    public function getShiftInfo($select, $wherecondition) {
        $query = $this->db->select($select)->get_where($this->shifts_tablename, $wherecondition);
        return $query->row_array();
    }

    // 获取活动班制列表
    public function getHotShftsList($wherecondition, $start='', $limit='') {
        $query = $this->db->where($wherecondition)->order_by('id', 'DESC')->get($this->shifts_tablename, $limit, $start);
        foreach($query->result() as $key => $value) {
            $query->result()[$key]->addtime = date('Y-m-d H:i:s', $value->addtime);
            $query->result()[$key]->school_info = $this->getSchoolInfo($value->sh_school_id);
            $query->result()[$key]->coach_info = $this->mcoach->getCoachInfo($value->coach_id);
            $query->result()[$key]->coupon_info = $this->mcoupon->getCouponInfoById($this->mcoupon->coupon_tablename, ['id'=>$value->coupon_id]);
        }
        return ['list'=>$query->result()];
    }

    public function getSchoolShiftsInfo($id) {
        $query = $this->db->get_where($this->shifts_tablename, ['id'=>$id]);
        // $query->row_array()['school_info'] = $this->getSchoolInfo($query->row_array()['sh_school_id']);
        return $query->row_array();
    }

    public function editSchoolShiftsInfo($data) {
        return $this->db->where('id', $data['id'])->update($this->shifts_tablename, $data);
    } 

    // 添加班制
    public function addSchoolShiftsInfo($data) {
        $this->db->insert($this->shifts_tablename, $data);
        return $this->db->insert_id();
    }

    // 获取银行账号列表
    public function getSchoolAccountList($wherecondition) {
        $query = $this->db->where($wherecondition)->get($this->saccount_tablename);
        return ['list'=>$query->result()];
    }

// 3.场地管理
    /**
     * 获取场地管理的页码
     * @param $school_id, $param, $limit
     * @return void
     **/
    public function getSitePageNum($school_id, $param, $limit)
    {
        $map = [];
        $complex = [];
        if ($school_id != '') {
            $map['site.school_id'] = $school_id;
        }

        if ($param) {
            if ($param['open'] != '') {
                $map['site_status'] = $param['open'];
            }

            $complex['school.s_school_name'] = $param['keywords'];
            $complex['site.site_name'] = $param['keywords'];
        }

        $count = $this->db->from("{$this->site_tablename} as site")
            ->join("{$this->school_tablename} as school", "school.l_school_id=site.school_id", "LEFT")
            ->where($map)
            ->group_start()
                ->or_like($complex)
            ->group_end()
            ->count_all_results();

        $page_info = [
            'pagenum' => (int) ceil( $count / $limit ),
            'count' => $count
        ];
        return $page_info;

    }

    /**
     * 获取场地信息
     * @param $school_id, $param, $start, $limit
     * @return void
     **/
    public function getSiteList($school_id, $param, $start, $limit)
    {
        $map = [];
        $complex = [];
        if ($school_id != '') {
            $map['site.school_id'] = $school_id;
        }

        if ($param) {
            if ($param['open'] != '') {
                $map['site_status'] = $param['open'];
            }

            $complex['school.s_school_name'] = $param['keywords'];
            $complex['site.site_name'] = $param['keywords'];
        }

        $sitelist = $this->db->from("{$this->site_tablename} as site")
            ->select(
                'site.*,
                 school.l_school_id,
                 school.s_school_name as school_name'
            )
            ->join("{$this->school_tablename} as school", "school.l_school_id=site.school_id", "LEFT")
            ->where($map)
            ->group_start()
                ->or_like($complex)
            ->group_end()
            ->order_by('site.id', 'desc')
            ->limit($limit, $start)
            ->get()
            ->result_array();
        if ( ! empty($sitelist)) {
            foreach($sitelist as $key => $value) {
                if ($value['add_time'] != '' AND $value['add_time'] != 0) {
                    $sitelist[$key]['addtime'] = date('Y-m-d H:i:s', $value['add_time']);
                } else {
                    $sitelist[$key]['addtime'] = '--';
                }

                $sitelist[$key]['point_url_one'] = $this->mbase->buildUrl($value['point_text_url1']);
                $sitelist[$key]['point_url_tow'] = $this->mbase->buildUrl($value['point_text_url2']);
                $sitelist[$key]['model_resource_url'] = $this->mbase->buildUrl($value['model_resource_url']);
                $sitelist[$key]['imgurl'] = $this->mbase->buildUrl($value['imgurl']);
            }

        }
        return $sitelist;
    }

    /**
     * 更新场地数据
     * @param $data
     * @return void
     **/
    public function addSchoolMessage($tablename, $data) 
    {
        $this->db->insert($tablename, $data);
        return $this->db->insert_id();
    }


    /**
     * 更新场地数据
     * @param $data
     * @return void
     **/
    public function editSiteInfo($data) {
        return $this->db->where('id', $data['id'])->update($this->site_tablename, $data);
    }

    /**
     * 获取单条场地信息
     * @param $id
     * @return void
     **/
    public function getSiteInfo($id, $school_id)
    {
        $query = $this->db
            ->get_where($this->site_tablename, ['id' => $id]);
        $siteinfo = $query->row_array();
        if ( ! empty($siteinfo)) {
            $siteinfo['sid'] = $school_id;
            $school_id = $siteinfo['school_id'];
            $school_info = $this->getSchoolInfo($school_id);
            if ( ! empty($school_info)) {
                $siteinfo['school_name'] = $school_info['s_school_name'];
            } else {
                $siteinfo['school_name'] = '嘻哈平台';
            }

            $provinceinfo = $this->mads->getCityInfoByCondition($siteinfo['province_id'], 'province');
            if ( ! empty($provinceinfo)) {
                $siteinfo['province'] = $provinceinfo['province'];
            } else {
                $siteinfo['province'] = '';
            }

            $cityinfo = $this->mads->getCityInfoByCondition($siteinfo['city_id'], 'city');
            if ( ! empty($cityinfo)) {
                $siteinfo['city'] = $cityinfo['city'];
            } else {
                $siteinfo['city'] = '';
            }

            $areainfo = $this->mads->getCityInfoByCondition($siteinfo['area_id'], 'area');
            if ( ! empty($areainfo)) {
                $siteinfo['area'] = $areainfo['area'];
            } else {
                $siteinfo['area'] = '';
            }

            $siteinfo['http_imgurl'] = $this->mbase->buildUrl($siteinfo['imgurl']);
        }

        return $siteinfo;
        
    }

// 4.驾校报名点管理
    /**
     * 获取驾校报名点页码信息
     * @param $school_id, $param, $limit
     * @return void
     **/
    public function getSignplacePageNum($school_id, $param, $limit)
    {
        $map = [];
        $complex = [];
        if ($param) {
            $map['school.s_school_name'] = $param['keywords'];
            $map['location.tl_train_address'] = $param['keywords'];
            $map['location.tl_phone'] = $param['keywords'];
        }

        if ($school_id != 0) {
            $complex['location.tl_school_id'] = $school_id;
        }

        $count = $this->db
            ->from("{$this->train_tablename} as location")
            ->join("{$this->school_tablename} as school", "school.l_school_id=location.tl_school_id", "LEFT")
            ->where($complex)
            ->group_start()
                ->or_like($map)
            ->group_end()
            ->count_all_results();
        
        $pageinfo = [
            'pagenum' => (int) ceil( $count / $limit),
            'count' => $count
        ];
        return $pageinfo;
    }

    /**
     * 获取驾校报名点数据信息
     * @param $school_id, $param, $start, $limit
     * @return void
     **/
    public function getSignplaceList($school_id, $param, $start, $limit)
    {
        $map = [];
        $complex = [];
        if ($param) {
            $map['school.s_school_name'] = $param['keywords'];
            $map['location.tl_train_address'] = $param['keywords'];
            $map['location.tl_phone'] = $param['keywords'];
        }
        if ($school_id != 0) {
            $complex['location.tl_school_id'] = $school_id;
        }
        $signplacelist = $this->db
            ->select(
                'id,
                 tl_school_id as sid,
                 tl_train_address as name,
                 tl_location_x as location_x,
                 tl_location_y as location_y,
                 tl_phone as phone,
                 tl_imgurl as imgurl,
                 location.addtime,
                 location.order,
                 school.s_school_name as school_name'
            )
            ->from("{$this->train_tablename} as location")
            ->join("{$this->school_tablename} as school", "school.l_school_id=location.tl_school_id", "LEFT")
            ->where($complex)
            ->group_start()
                ->or_like($map)
            ->group_end()
            ->limit($limit, $start)
            ->order_by('location.id', 'desc')
            ->get()
            ->result_array();
        if ( ! empty($signplacelist)) {
            foreach ($signplacelist as $key => $value ) {
                $tl_imgurl = $value['imgurl'];
                if ($tl_imgurl != '') {
                    $train_imgurl = json_decode($tl_imgurl, true);

                    if ( is_array($train_imgurl) && ! empty($train_imgurl)) {
                        foreach ($train_imgurl as $index => $imgurl) {
                            $url = $this->mbase->buildUrl($imgurl);
                            if ( $url != '') {
                                $signplacelist[$key]['location_imgurl']['location_imgurl_'.$index] = $url;
                            } else {
                                $signplacelist[$key]['location_imgurl'] = '';
                            }
                        }

                    } else {
                        $signplacelist[$key]['location_imgurl'] = '';
                    }

                } else {
                    $signplacelist[$key]['location_imgurl'] = '';
                }

                if ( $value['school_name'] == '') {
                    $signplacelist[$key]['school_name'] = '--';
                }

                if ( $value['name'] == '') {
                    $signplacelist[$key]['name'] = '--';
                }

                if ($value['addtime'] != '' AND $value['addtime'] != 0) {
                    $signplacelist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $signplacelist[$key]['addtime'] = '--';
                }
            }
        }
        return $signplacelist;
    }

    /**
     * 获取驾校报名点信息
     * @param $id
     * @return void
     **/
    public function getSignPlaceInfo($id, $school_id)
    {
        $query = $this->db
            ->get_where($this->train_tablename, ['id' => $id]);
        $signplaceinfo = $query->row_array();
        if ( ! empty($signplaceinfo)) {
           
            $signplaceinfo['sid'] = $school_id;
            $school_id = $signplaceinfo['tl_school_id'];
            $signplaceinfo['school_name'] = '嘻哈平台';
            $school_info = $this->getSchoolInfo($school_id);
            if ( ! empty($school_info)) {
                if ( $school_info['s_school_name'] != '') {
                    $signplaceinfo['school_name'] = $school_info['s_school_name'];
                } 
            } 

            $imgurl_number = ['one', 'two', 'three', 'four', 'five'];
            if ($signplaceinfo['tl_imgurl'] != '') {
                $imgurl = json_decode($signplaceinfo['tl_imgurl'], true);
                foreach ($imgurl as $key => $url) {
                    if ( $url != '') {
                        $signplaceinfo['imgurl_'.$imgurl_number[$key]] = $url;
                        $signplaceinfo['http_imgurl_'.$imgurl_number[$key]] = $this->mbase->buildUrl($url);
                    } 
                }
            }

            foreach ($imgurl_number as $no) {
                if (! isset($signplaceinfo['imgurl_'.$no])) {
                    $signplaceinfo['imgurl_'.$no] = '';
                    $signplaceinfo['http_imgurl_'.$no] = '';
                }
            }
        }
        return $signplaceinfo;
    }

// 5.轮播图管理
    /**
     * 获取驾校页数
     * @param  $param, $limit
     * @return void
     **/
    public function getSchoolBannerPageNum($school_id, $param, $limit)
    {
        $map = [];
        $complex = [];
        if ($school_id != '') {
            $map['l_school_id'] = $school_id;
        }
        if ($param) {
            $complex['l_school_id'] = $param['keywords'];
            $complex['s_school_name'] = $param['keywords'];
        }
        $query = $this->db->from($this->school_tablename)
            ->where($map);
        if ( ! empty($complex)) {
            $query = $query
                ->group_start()
                    ->or_like($complex)
                ->group_end();
        }
        $count = $query->count_all_results();
        $pageinfo = [
            'pagenum' => (int) ceil( $count / $limit),
            'count' => $count
        ];
        return $pageinfo;
    }

    /**
     * 获取驾校轮播图列表
     * @param $param | $limit | $start
     * @return void
     **/
    public function getSchoolBanner($school_id, $param, $start, $limit)
    {
        $map = [];
        $complex = [];
        if ($school_id != '') {
            $map['l_school_id'] = $school_id;
        }
        if ($param) {
            $complex['l_school_id'] = $param['keywords'];
            $complex['s_school_name'] = $param['keywords'];
        }

        
        $query = $this->db
            ->select(
                'l_school_id,
                 s_school_name as school_name,
                 s_imgurl,
                 addtime'
            )
            ->from($this->school_tablename)
            ->where($map);
        if ( ! empty($complex)) {
            $query = $query
                ->group_start()
                    ->or_like($complex)
                ->group_end();
        }
        $school_list = $query
            ->limit($limit, $start)
            ->order_by('s_order', 'asc')
            ->order_by('l_school_id', 'desc')
            ->get()
            ->result_array();
        if ( ! empty($school_list)) {
            foreach ($school_list as $key => $value) {
                if ($value['addtime'] != '') {
                    $school_list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $school_list[$key]['addtime'] = '--';
                }

                if ($value['school_name'] == '') {
                    $school_list[$key]['school_name'] = '--';
                }

                // 轮播图处理
                $imgurl_arr = [];
                $number = ['one', 'two', 'three', 'four', 'five'];
                foreach ($number as $n => $m) {
                    $school_list[$key]['imgurl_'.$m] = '';
                    $school_list[$key]['http_imgurl_'.$m] = '';
                }
                
                $s_imgurl = json_decode($value['s_imgurl'], true);
                
                if ( is_array($s_imgurl) AND ! empty($s_imgurl)) {
                    foreach ($s_imgurl as $index => $imgurl) {
                        $school_imgurl = $this->mbase->buildUrl($imgurl);
                        if ( $school_imgurl != '') {
                            $imgurl_arr[] = $school_imgurl;
                        }
                    }
                }

                $count = count($imgurl_arr);
                if ( ! empty($imgurl_arr) AND $count > 0) {
                    if ($count >= 1) {
                        $school_list[$key]['imgurl_one'] = $s_imgurl[0];
                        $school_list[$key]['http_imgurl_one'] = $imgurl_arr[0];
                    }
                    if ($count >= 2) {
                        $school_list[$key]['imgurl_two'] = $s_imgurl[1];
                        $school_list[$key]['http_imgurl_two'] = $imgurl_arr[1];
                    }
                    if ($count >= 3) {
                        $school_list[$key]['imgurl_three'] = $s_imgurl[2];
                        $school_list[$key]['http_imgurl_three'] = $imgurl_arr[2];
                    }
                    if ($count >= 4) {
                        $school_list[$key]['imgurl_four'] = $s_imgurl[3];
                        $school_list[$key]['http_imgurl_four'] = $imgurl_arr[3];
                    }
                    if ($count == 5) {
                        $school_list[$key]['imgurl_five'] = $s_imgurl[4];
                        $school_list[$key]['http_imgurl_five'] = $imgurl_arr[4];
                    }
                }

            }
        }
        return $school_list;
    }

    /**
     * 获取驾校轮播图详情
     *
     * @return  void
     **/
    public function getBannerInfo($id)
    {
        $banner_list = $this->db 
            ->select('l_school_id, s_school_name as school_name, s_imgurl')
            ->from("{$this->school_tablename}")
            ->where('l_school_id', $id)
            ->get()
            ->row_array();
        $imgurl_arr = [];
        $number = ['one', 'two', 'three', 'four', 'five'];
        foreach ($number as $key => $num) {
            $banner_list['imgurl_'.$num] = '';
            $banner_list['http_imgurl_'.$num] = '';
        
        }
        if ( ! empty($banner_list)) {
            $s_imgurl = json_decode($banner_list['s_imgurl'], true);
            if ( is_array($s_imgurl) AND ! empty($s_imgurl)) {
                foreach ($s_imgurl as $index => $imgurl) {
                    $school_imgurl = $this->mbase->buildUrl($imgurl);
                    if ( $school_imgurl != '') {
                        $imgurl_arr[] = $school_imgurl;
                    }
                }
            }
            if ( ! empty($imgurl_arr)) {
                $count = count($imgurl_arr);
                $banner_list['imgurl_one'] = $s_imgurl[0];
                $banner_list['http_imgurl_one'] = $imgurl_arr[0];
                if ($count >= 2) {
                    $banner_list['imgurl_two'] = $s_imgurl[1];
                    $banner_list['http_imgurl_two'] = $imgurl_arr[1];
                }
                if ($count >= 3) {
                    $banner_list['imgurl_three'] = $s_imgurl[2];
                    $banner_list['http_imgurl_three'] = $imgurl_arr[2];
                }
                if ($count >= 4) {
                    $banner_list['imgurl_four'] = $s_imgurl[3];
                    $banner_list['http_imgurl_four'] = $imgurl_arr[3];
                }
                if ($count == 5) {
                    $banner_list['imgurl_five'] = $s_imgurl[4];
                    $banner_list['http_imgurl_five'] = $imgurl_arr[4];
                }
            }
        }
        return $banner_list;
    }

    /**
     * 删除轮播图
     *
     * @return  void
     **/
    public function delBanner($id, $url)
    {
        $banner_list = $this->db 
            ->select('s_imgurl')
            ->from("{$this->school_tablename}")
            ->where('l_school_id', $id)
            ->get()
            ->row_array();
        // $num_arr = ['one', 'two', 'three', 'four', 'five'];
        $imgurl_arr = [];
        if ( ! empty($banner_list)) {
            $s_imgurl = array_filter(array_values(json_decode($banner_list['s_imgurl'], true)));
            if ( is_array($s_imgurl) AND ! empty($s_imgurl)) {
                foreach ($s_imgurl as $key => $imgurl) {
                    if ( $url == $imgurl) {
                        unset($s_imgurl[$key]);
                    }
                }
            }
            if (file_exists($url)) {
                unlink($url);
            }
        }
        if ( ! empty($s_imgurl)) {
            $data = ['s_imgurl' => json_encode(array_values($s_imgurl), JSON_UNESCAPED_SLASHES)];
        } else {
            $data = ['s_imgurl' => ''];
        }
        
        $update_ok = $this->db
            ->where('l_school_id', $id)
            ->update($this->school_tablename, $data);
        return $update_ok;
    }


    
}
?>