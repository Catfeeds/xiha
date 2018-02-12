<?php
namespace Admin\Model;
use Think\Model;
use Think\Page;

class SchoolModel extends BaseModel {
    public $tableName = 'school';

// 1.驾校列表模块
    /**
     * 获取驾校列表
     *
     * @return  void
     * @author  wl
     * @date    july 27, 2016
     * @update  july 30, 2016
     **/
    public function getSchoolInfo () {
        $count = $this->alias('s')
            // ->where(array('is_show' => 1))
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $school_list = array(); 
        $school_lists = $this->limit($Page->firstRow . ',' . $Page->listRows)
            ->order('s_order DESC, l_school_id DESC')
            // ->where(array('is_show' => 1))
            ->fetchSql(false)
            ->select(); 
        if (!empty($school_lists)) {
            foreach ($school_lists as $key => $value) {
                if ($value['addtime'] != 0) {
                    $school_lists[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $school_lists[$key]['addtime'] = '--';
                }

                if ($value['s_frdb'] == '') {
                    $school_lists[$key]['s_frdb'] = '--';
                }

                if ($value['s_frdb_mobile'] == '') {
                    $school_lists[$key]['s_frdb_mobile'] = '--';
                }

                if ($value['s_frdb_tel'] == '') {
                    $school_lists[$key]['s_frdb_tel'] = '--';
                }

                if ($value['s_zzjgdm'] == '') {
                    $school_lists[$key]['s_zzjgdm'] = '--';
                }

                if ($value['s_yh_name'] == '') {
                    $school_lists[$key]['s_yh_name'] = '--';
                }

                if ($value['s_yh_zhanghao'] == '') {
                    $school_lists[$key]['s_yh_zhanghao'] = '--';
                }

                if ($value['s_yh_huming'] == '') {
                    $school_lists[$key]['s_yh_huming'] = '--';
                }

                if ($value['s_shuoming'] == '') {
                    $school_lists[$key]['s_shuoming'] = '--';
                }

                if ($value['shifts_intro'] == '') {
                    $school_lists[$key]['shifts_intro'] = '--';
                }

                if ($value['s_address'] == '') {
                    $school_lists[$key]['s_address'] = '--';
                }

            }
        }           
        $school_list = array('school_info' => $school_lists, 'page' => $page, 'count' => $count);
        return $school_list;
    }

    /**
     * 根据相关条件搜索代理驾校
     *
     * @return  void
     * @author  wl
     * @date    july 28, 2016
     * @update  july 30, 2016
     * @update  Dec 10, 2016
     **/
    public function searchSchool ($param) {
        $map = array();
        $complex = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_info'] == '') {
            $complex['l_school_id'] = array('EQ', $param['s_keyword']);
            $complex['s_school_name'] = array('LIKE', $s_keyword);
            $complex['s_frdb'] = array('LIKE', $s_keyword);
            $complex['s_frdb_mobile'] = array('LIKE', $s_keyword);
            $complex['s_address'] = array('LIKE', $s_keyword);
            $complex['_logic'] = 'OR';
        } else {
            if ($param['search_info'] == 'l_school_id') {
                $complex[$param['search_info']] = array('EQ', $param['s_keyword']);
            }
            $complex[$param['search_info']] = array('LIKE', $s_keyword);
        }
        $map['_complex'] = $complex;
        
        if ($param['dwxz'] != '') {
            $map['i_dwxz'] = array('EQ', $param['dwxz']);
        }

        if ($param['is_show'] != '') {
            $map['is_show'] = array('EQ', $param['is_show']);
        } 

        if ($param['is_hot'] != '') {
            $map['is_hot'] = array('EQ', $param['is_hot']);
        }

        if ($param['support_coupon'] != '') {
            $map['support_coupon'] = array('EQ', $param['support_coupon']);
        }

        if ($param['province'] != '') {
            $map['province_id'] = array('EQ', $param['province']);
        }
        if ($param['city'] != '') {
            $map['city_id'] = array('EQ', $param['city']);
        }
        if ($param['area'] != '') {
            $map['area_id'] = array('EQ', $param['area']);
        }

        $count = $this->table(C('DB_PREFIX').'school')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $school_list = array();
        $school_lists = $this->table(C('DB_PREFIX').'school')
            ->where($map)
            ->order('s_order DESC, l_school_id DESC')
            ->fetchSql(false)
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->select(); 
        if (!empty($school_lists)) {
            foreach ($school_lists as $key => $value) {
                if ($value['addtime'] != 0) {
                    $school_lists[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);

                } else {
                    $school_lists[$key]['addtime'] = '--';

                }

                if ($value['s_frdb'] == '') {
                    $school_lists[$key]['s_frdb'] = '--';
                }

                if ($value['s_frdb_mobile'] == '') {
                    $school_lists[$key]['s_frdb_mobile'] = '--';
                }

                if ($value['s_frdb_tel'] == '') {
                    $school_lists[$key]['s_frdb_tel'] = '--';
                }

                if ($value['s_zzjgdm'] == '') {
                    $school_lists[$key]['s_zzjgdm'] = '--';
                }

                if ($value['s_yh_name'] == '') {
                    $school_lists[$key]['s_yh_name'] = '--';
                }

                if ($value['s_yh_zhanghao'] == '') {
                    $school_lists[$key]['s_yh_zhanghao'] = '--';
                }

                if ($value['s_yh_huming'] == '') {
                    $school_lists[$key]['s_yh_huming'] = '--';
                }

                if ($value['s_shuoming'] == '') {
                    $school_lists[$key]['s_shuoming'] = '--';
                }

                if ($value['shifts_intro'] == '') {
                    $school_lists[$key]['shifts_intro'] = '--';
                }

                if ($value['s_address'] == '') {
                    $school_lists[$key]['s_address'] = '--';
                }

            }
        }           
        $school_list = array('school_info' => $school_lists, 'page' => $page, 'count' => $count);
        return $school_list;                  

    }

    /**
     * 设置驾校的排序状态
     *
     * @return  void
     * @author  wl
     * @date    2017-4-27
     **/
    public function setSchoolOrder ($post) {
        if (empty($post)) {
            return 101; // 参数错误
        }
        if (isset($post['order'])) {
            if (!is_numeric($post['order'])) {
                return 102; //参数类型错误
            } else {
                $old_num = $this->table(C('DB_PREFIX').'school')
                    ->where('l_school_id = :cid')
                    ->bind(['cid' => $post['id']])
                    ->getField('s_order');
                if ($post['order'] === $old_num) {
                    return 105; // 尚未做任何修改
                }
            }
        }

        $data['s_order'] = $post['order'];
        $school = D('school');
        if ($res = $school->create($data)) {
            $result = $school->where('l_school_id = :cid')
                ->bind(['cid' => $post['id']])
                ->fetchSql(false)
                ->save($res);
            if ($result) {
                return 200;
            } else {
                return 400;
            }
        }

    }
    
    /**
     * 检测驾校是否存在（通过驾校名称）
     *
     * @return  void
     * @author  wl
     * @date    Dec 10, 2016
     **/
    public function checkSchool ($schoolName) {
        $checkSchool = $this->table(C('DB_PREFIX').'school')
            ->where(array('s_school_name' => $schoolName))
            ->find();
        if (!empty($checkSchool)) {
            return true;
        } else {
            return false;
        }

    }

    /**
    * 逻辑上删除驾校
    *
    * @return void
    * @author wl
    * @date   july 29, 2016
    **/
    public function changeShowSchool ($school_id) {
        $result = M('school')->where(array('l_school_id' => $school_id))
            // ->delete();
            ->save(array('is_show' => 2));
        return $result;
    }

    /**
     * 修改驾校的热门状态
     *
     * @return  void
     * @author  wl
     * @date    2017-05-04
     **/
    public function setHotStatus($id, $status) {
        if (!is_numeric($id)) {
            return false;
        }
        $list = array();
        $data = array('is_hot' => $status);
        $result = $this->where('l_school_id = :school_id')
            ->bind(['school_id' => $id])
            ->data($data)
            ->fetchSql(false)
            ->save();
        $list['id'] = $id;
        $list['is_hot'] = $result;
        return $list;
    }

    /**
     * 修改驾校的券支持状态
     *
     * @return  void
     * @author  wl
     * @date    2017-05-08
     **/
    public function setCouponStatus($id, $status) {
        if (!is_numeric($id)) {
            return false;
        }
        $list = array();
        $data = array('support_coupon' => $status);
        $result = $this->where('l_school_id = :school_id')
            ->bind(['school_id' => $id])
            ->data($data)
            ->fetchSql(false)
            ->save();
        $list['id'] = $id;
        $list['support_coupon'] = $result;
        return $list;
    }

    /**
     * 修改驾校是否展示的状态
     *
     * @return  void
     * @author  wl
     * @date    August 04, 2016
     **/
    public function setSchoolShow($id, $status) {
        if (!is_numeric($id)) {
            return false;
        }
        $list = array();
        $data = array('is_show' => $status);
        $result = $this->where('l_school_id = :school_id')
            ->bind(['school_id' => $id])
            ->data($data)
            ->fetchSql(false)
            ->save();
        $list['id'] = $id;
        $list['is_show'] = $result;
        return $list;
    }

    /**
     * 根据驾校id获得驾校所有的信息
     *
     * @return  void
     * @author  wl
     * @date    july 30, 2016
     **/
    public function getSchoolInfoById($sid) {
        $school_info = $this->alias('s')
            ->join(C('DB_PREFIX').'province p ON p.provinceid = s.province_id', 'LEFT')
            ->join(C('DB_PREFIX').'city c ON c.cityid = s.city_id', 'LEFT')
            ->join(C('DB_PREFIX').'area a ON a.areaid = s.area_id', 'LEFT')
            ->where('l_school_id = :sid')
            ->bind(['sid'=>$sid])
            ->field('s.*, p.province, p.provinceid, c.cityid, c.city, a.areaid, a.area')
            ->find(); 
        if (!empty($school_info)) {
            return $school_info;
        } else {
            return array();
        }
    }


// 2.驾校场地模块
    /**
     * 获得驾校的场地信息
     *
     * @return  void
     * @author  wl
     * @date    August 08, 2016
     * @update  Dec 12, 2016
     **/
    public function getSchoolSiteInfo ($school_id) {
        if ($school_id == 0) {
            $count = $this->table(C('DB_PREFIX') . 'site st')
                ->join(C('DB_PREFIX') . 'school s ON s.l_school_id = st.school_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'province p ON p.provinceid = st.province_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'city c ON c.cityid = st.city_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'area a ON a.areaid = st.area_id', 'LEFT')
                ->fetchSql(false)
                ->count();
            $Page = new Page($count, 10);
            $page = $this->getPage($count, 10);
            $site_lists = array();
            $site_list = $this->table(C('DB_PREFIX') . 'site st')
                ->join(C('DB_PREFIX') . 'school s ON s.l_school_id = st.school_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'province p ON p.provinceid = st.province_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'city c ON c.cityid = st.city_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'area a ON a.areaid = st.area_id', 'LEFT')
                ->field('st.id,st.site_name, st.site_desc, st.model_resource_url, st.school_id, st.province_id, st.city_id, st.area_id, st.address, st.point_text_url1, st.point_text_url2, st.imgurl, st.add_time, st.site_status, l_school_id, s_school_name,s_address, s.province_id, s.city_id, s.area_id, p.provinceid, p.province, c.cityid, c.city, a.areaid, a.area')
                ->order('st.id DESC')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->fetchSql(false)
                ->select();
        } else {
            $count = $this->table(C('DB_PREFIX') . 'site st')
                ->join(C('DB_PREFIX') . 'school s ON s.l_school_id = st.school_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'province p ON p.provinceid = st.province_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'city c ON c.cityid = st.city_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'area a ON a.areaid = st.area_id', 'LEFT')
                ->where('school_id = :sid')
                ->bind(['sid' => $school_id])
                ->fetchSql(false)
                ->count();
            $Page = new Page($count, 10);
            $page = $this->getPage($count, 10);
            $site_lists = array();
            $site_list = $this->table(C('DB_PREFIX') . 'site st')
                ->join(C('DB_PREFIX') . 'school s ON s.l_school_id = st.school_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'province p ON p.provinceid = st.province_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'city c ON c.cityid = st.city_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'area a ON a.areaid = st.area_id', 'LEFT')
                ->where('school_id = :sid')
                ->bind(['sid' => $school_id])
                ->field('st.id,st.site_name, st.site_desc, st.model_resource_url, st.school_id, st.province_id, st.city_id, st.area_id, st.address, st.point_text_url1, st.point_text_url2, st.imgurl, st.add_time, st.site_status, l_school_id, s_school_name,s_address, s.province_id, s.city_id, s.area_id, p.provinceid, p.province, c.cityid, c.city, a.areaid, a.area')
                ->order('st.id DESC')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->fetchSql(false)
                ->select();
        }
        if (!empty($site_list)) {
            foreach ($site_list as $key => $value) {
                if ($value['add_time'] != 0) {
                    $site_list[$key]['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
                } else {
                    $site_list[$key]['add_time'] = '--';
                }

                if ($value['model_resource_url'] != '') {
                    $site_list[$key]['model_resource_url'] = C('HTTP_HOST').$value['model_resource_url'];
                } else {
                    $site_list[$key]['model_resource_url'] = '';
                }

                if (file_exists($value['point_text_url1']) && $value['point_text_url1'] != '') {
                    $site_list[$key]['point_text_url1'] = C('HTTP_HOST').$value['point_text_url1'];
                } else {
                    $site_list[$key]['point_text_url1'] = '';
                }

                if (file_exists($value['point_text_url2']) && $value['point_text_url2'] != '') {
                    $site_list[$key]['point_text_url2'] = C('HTTP_HOST').$value['point_text_url2'];
                } else {
                    $site_list[$key]['point_text_url2'] = '';
                }

            }
        }
        $site_lists = array('site_list' => $site_list, 'page' => $page, 'count' => $count);
        return $site_lists;

    }
    /**
    * 搜索驾校的场地信息
    *
    * @return  void
    * @author  wl
    * @date    August 08, 2016
    * @update Dec 12, 2016
    **/
     public function searchSchoolSite ($param, $school_id) {
        $map = array();
        if ($param['site_status'] != '') {
            $map['site_status'] = array('EQ', $param['site_status']);
        }

        if ($param['s_keyword'] != '') {
            $map['site_name'] = array('LIKE', '%'.$param['s_keyword'].'%');
        } 

        if ($school_id == 0) {
            $count = $this->table(C('DB_PREFIX') . 'site st')
                ->where($map)
                ->fetchSql(false)
                ->count();
            $Page = new Page($count, 10, $param);
            $page = $this->getPage($count, 10, $param);
            $site_lists = array();
            $site_list = $this->table(C('DB_PREFIX') . 'site st')
                ->join(C('DB_PREFIX') . 'school s ON s.l_school_id = st.school_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'province p ON p.provinceid = st.province_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'city c ON c.cityid = st.city_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'area a ON a.areaid = st.area_id', 'LEFT')
                ->field('st.id,st.site_name, st.site_desc, st.model_resource_url, st.school_id, st.province_id, st.city_id, st.area_id, st.address, st.point_text_url1, st.point_text_url2, st.imgurl, st.add_time, st.site_status, l_school_id, s_school_name,s_address, s.province_id, s.city_id, s.area_id, p.provinceid, p.province, c.cityid, c.city, a.areaid, a.area')
                ->where($map)
                ->order('st.id DESC')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->fetchSql(false)
                ->select();
        } else {
            $count = $this->table(C('DB_PREFIX') . 'site st')
                ->where('school_id = :sid')
                ->bind(['sid' => $school_id])
                ->where($map)
                ->fetchSql(false)
                ->count();
            $Page = new Page($count, 10, $param);
            $page = $this->getPage($count, 10, $param);
            $site_lists = array();
            $site_list = $this->table(C('DB_PREFIX') . 'site st')
                ->join(C('DB_PREFIX') . 'school s ON s.l_school_id = st.school_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'province p ON p.provinceid = st.province_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'city c ON c.cityid = st.city_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'area a ON a.areaid = st.area_id', 'LEFT')
                ->where('school_id = :sid')
                ->bind(['sid' => $school_id])
                ->where($map)
                ->field('st.id,st.site_name, st.site_desc, st.model_resource_url, st.school_id, st.province_id, st.city_id, st.area_id, st.address, st.point_text_url1, st.point_text_url2, st.imgurl, st.add_time, st.site_status, l_school_id, s_school_name,s_address, s.province_id, s.city_id, s.area_id, p.provinceid, p.province, c.cityid, c.city, a.areaid, a.area')
                ->order('st.id DESC')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->fetchSql(false)
                ->select();
        }
        if (!empty($site_list)) {
            foreach ($site_list as $key => $value) {
                if ($value['add_time'] != 0) {
                    $site_list[$key]['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
                } else {
                    $site_list[$key]['add_time'] = '--';
                }

                if ($value['model_resource_url'] != '') {
                    $site_list[$key]['model_resource_url'] = C('HTTP_HOST').$value['model_resource_url'];
                } else {
                    $site_list[$key]['model_resource_url'] = '';
                }

                if (file_exists($value['point_text_url1']) && $value['point_text_url1'] != '') {
                    $site_list[$key]['point_text_url1'] = C('HTTP_HOST').$value['point_text_url1'];
                } else {
                    $site_list[$key]['point_text_url1'] = '';
                }

                if (file_exists($value['point_text_url2']) && $value['point_text_url2'] != '') {
                    $site_list[$key]['point_text_url2'] = C('HTTP_HOST').$value['point_text_url2'];
                } else {
                    $site_list[$key]['point_text_url2'] = '';
                }
            }
        }
        $site_lists = array('site_list' => $site_list, 'page' => $page, 'count' => $count);
        return $site_lists;
     }

     /**
      * 检测驾校场地信息是否重复（条件：驾校id + 场地名）
      *
      * @return void
      * @author wl  
      * @date   Dec 12, 2016  
      **/
     public function checkSchoolSite ($school_id, $site_name) {
        if (!school_id && $site_name) {
            return false;
        }
        $checkSchoolSite = $this->table(C('DB_PREFIX').'site')
            ->where(array('school_id' => $school_id, 'site_name' => $site_name))
            ->find();
        if (!empty($checkSchoolSite)) {
            return true;
        } else {
            return false;
        }

     }

    /**
     * 删除驾校场地
     *
     * @return  void
     * @author  wl
     * @date    August 08, 2016
     **/
    public function delSchoolSite ($sid) {
        if (!is_numeric($sid)) {
            return false;
        }
        $result = M('site')->where('id = :sid')
            ->bind(['sid' => $sid])
            ->fetchSql(false)
            ->save(array('site_status' => 2));
            // ->delete();
        return $result;
    }

     /**
     * 设置驾校场地开放的状态
     *
     * @return  void
     * @author  wl
     * @date    August 08, 2016
     **/
     public function setSiteStatus ($id, $status) {
        if (!is_numeric($id)) {
            return false;
        }
        $list = array();
        $data = array('site_status' => $status);
        $result = M('site')->where('id = :sid')
            ->bind(['sid' => $id])
            ->data($data)
            ->fetchSql(false)
            ->save();
        $list['id'] = $id;
        $list['site_status'] = $result;
        return $list;
     }

     /**
      * 通过提交的id获得相关的驾校场地信息
      *
      * @return void
      * @author wl
      * @date   August 09, 2016
      * @update Dec 12, 2016
      **/
     public function getSiteInfoById ($sid) {
        if (!is_numeric($sid)) {
            return false;
        }
        $site_list = $this->table(C('DB_PREFIX').'site st')
            ->join(C('DB_PREFIX') . 'school s ON s.l_school_id = st.school_id', 'LEFT')
            ->join(C('DB_PREFIX') . 'province p ON p.provinceid = st.province_id', 'LEFT')
            ->join(C('DB_PREFIX') . 'city c ON c.cityid = st.city_id', 'LEFT')
            ->join(C('DB_PREFIX') . 'area a ON a.areaid = st.area_id', 'LEFT')
            ->where('st.id = :sid')
            ->bind(['sid' => $sid])
            ->field('st.*, c.cityid, c.city, a.areaid, a.area, p.provinceid, p.province, s.l_school_id, s.s_school_name')
            ->fetchSql(false)
            ->find();
        if (!empty($site_list)) {
            return $site_list;
        } else {
            return array();
        }
     }

// 3.驾校班制模块
     /**
     * 获得驾校的班制列表
     *
     * @return  void
     * @author  wl
     * @date    August 01, 2016
     * @update Dec 12, 2016
     **/
    public function getSchoolShifts ($school_id) {
        $condition = array();
        // $condition['ss.deleted'] = 1;
        if ($school_id != 0) {
            $condition['sh_school_id'] = $school_id;
        }
        $count = $this->table(C('DB_PREFIX').'school_shifts ss')
            ->join(C('DB_PREFIX').'coach ca ON ca.l_coach_id = ss.coach_id', 'LEFT')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = ss.sh_school_id', 'LEFT')
            ->join(C('DB_PREFIX').'coupon c ON c.id = ss.coupon_id', 'LEFT')
            ->join(C('DB_PREFIX').'system_tag_config stc ON stc.id = ss.sh_tag_id', 'LEFT')
            ->where($condition)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $school_shifts = array(); 
        $shifts_list = $this->table(C('DB_PREFIX').'school_shifts ss')
            ->field(
                'ss.*, 
                 l_school_id, 
                 s_school_name, 
                 c.id as cid, 
                 coupon_code, 
                 stc.id as s_tag_id, 
                 tag_name, 
                 ca.l_coach_id, 
                 ca.s_coach_name'
            )
            ->join(C('DB_PREFIX').'coach ca ON ca.l_coach_id = ss.coach_id', 'LEFT')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = ss.sh_school_id', 'LEFT')
            ->join(C('DB_PREFIX').'coupon c ON c.id = ss.coupon_id', 'LEFT')
            ->join(C('DB_PREFIX').'system_tag_config stc ON stc.id = ss.sh_tag_id', 'LEFT')
            ->order('ss.order DESC, ss.id DESC')
            ->where($condition)
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->fetchSql(false)
            ->select();
        if (!empty($shifts_list)) {
            foreach ($shifts_list as $key => $value) {
                if ($value['coach_id'] == null) {
                    $shifts_list[$key]['s_coach_name'] = '--';
                }

                if ($value['addtime'] != 0) {
                    $shifts_list[$key]['addtime']  = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $shifts_list[$key]['addtime']  = '--';
                }

                if ($value['updatetime'] != 0) {
                    $shifts_list[$key]['updatetime']  = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $shifts_list[$key]['updatetime']  = '--';
                }

                if ($value['sh_license_name'] == '') {
                    $shifts_list[$key]['sh_license_name']  = '--';
                }

                if ($value['s_coach_name'] == '') {
                    $shifts_list[$key]['s_coach_name']  = '--';
                }

                if ($value['sh_description_1'] == '') {
                    $shifts_list[$key]['sh_description_1']  = '--';
                }
                if ($value['sh_description_2'] == '') {
                    $shifts_list[$key]['sh_description_2']  = '--';
                }

                if ($value['sh_info'] == '') {
                    $shifts_list[$key]['sh_info']  = '--';
                }

                if ($value['s_school_name'] == '') {
                    $shifts_list[$key]['s_school_name']  = '嘻哈平台';
                }

                if ($value['sh_tag'] == '') {
                    $shifts_list[$key]['sh_tag']  = '--';
                }

                $shifts_list[$key]['sh_imgurl'] = $this->buildUrl($value['sh_imgurl']);
            }
        } 
        $school_shifts = array('school_shifts' => $shifts_list, 'page' => $page, 'count' => $count);
        return $school_shifts; 
    }

    /**
     * 驾校班制的搜索列表
     *
     * @return  void
     * @author  wl
     * @date    Nov 02, 2016
     * @update Dec 12, 2016
     **/
    public function searchSchoolShifts ($param, $school_id) {
        $map = array();
        $complex = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_info'] == '') {
            $complex['ss.id'] = array('EQ', $param['s_keyword']);
            $complex['sh_title'] = array('LIKE', $s_keyword);
            $complex['sh_license_name'] = array('LIKE', $s_keyword);
            $complex['sh_tag'] = array('LIKE', $s_keyword);
            $complex['s.s_school_name'] = array('LIKE', $s_keyword);
            $complex['_logic'] = 'OR';
        } else {
            if ($param['search_info'] == 'id') {
                $param['search_info'] = 'ss.id';
                $complex[$param['search_info']] = array('EQ', $param['s_keyword']);
            }

            if ($param['search_info'] == 's_school_name' || $param['search_info'] == 'sh_title' || $param['search_info'] == 'sh_license_name' || $param['search_info'] == 'sh_tag' ) {
                $complex[$param['search_info']] = array('LIKE', $s_keyword);
            }

        }
        $map['_complex'] = $complex;

        if ($param['is_promote'] != 0) {
            $map['is_promote'] = array('EQ', $param['is_promote']);
        }

        if ($param['deleted'] != 0) {
            $map['deleted'] = array('EQ', $param['deleted']);
        } 

        if ($param['is_package'] != 0) {
            $map['is_package'] = array('EQ', $param['is_package']);
        }

        if ($school_id != 0) {
            $map['sh_school_id'] = $school_id;
        }
        $count = $this->table(C('DB_PREFIX').'school_shifts ss')
            ->join(C('DB_PREFIX').'coach ca ON ca.l_coach_id = ss.coach_id', 'LEFT')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = ss.sh_school_id', 'LEFT')
            ->join(C('DB_PREFIX').'coupon c ON c.id = ss.coupon_id', 'LEFT')
            ->join(C('DB_PREFIX').'system_tag_config stc ON stc.id = ss.sh_tag_id', 'LEFT')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $school_shifts = array(); 
        $shifts_list = $this->table(C('DB_PREFIX').'school_shifts ss')
            ->field(
                'ss.*, 
                 l_school_id, 
                 s_school_name, 
                 c.id as cid, 
                 coupon_code, 
                 stc.id as s_tag_id, 
                 tag_name, 
                 ca.l_coach_id, 
                 ca.s_coach_name'
            )
            ->join(C('DB_PREFIX').'coach ca ON ca.l_coach_id = ss.coach_id', 'LEFT')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = ss.sh_school_id', 'LEFT')
            ->join(C('DB_PREFIX').'coupon c ON c.id = ss.coupon_id', 'LEFT')
            ->join(C('DB_PREFIX').'system_tag_config stc ON stc.id = ss.sh_tag_id', 'LEFT')
            ->order('ss.order DESC, ss.id DESC')
            ->where($map)
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->fetchSql(false)
            ->select();
        if (!empty($shifts_list)) {
            foreach ($shifts_list as $key => $value) {
                if ($value['coach_id'] == null) {
                    $shifts_list[$key]['s_coach_name'] = '--';
                }

                if ($value['addtime'] != 0) {
                    $shifts_list[$key]['addtime']  = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $shifts_list[$key]['addtime']  = '--';
                }

                if ($value['updatetime'] != 0) {
                    $shifts_list[$key]['updatetime']  = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $shifts_list[$key]['updatetime']  = '--';
                }

                if ($value['sh_license_name'] == '') {
                    $shifts_list[$key]['sh_license_name']  = '--';
                }

                if ($value['s_coach_name'] == '') {
                    $shifts_list[$key]['s_coach_name']  = '--';
                }

                if ($value['sh_description_1'] == '') {
                    $shifts_list[$key]['sh_description_1']  = '--';
                }
                if ($value['sh_description_2'] == '') {
                    $shifts_list[$key]['sh_description_2']  = '--';
                }

                if ($value['sh_info'] == '') {
                    $shifts_list[$key]['sh_info']  = '--';
                }

                if ($value['s_school_name'] == '') {
                    $shifts_list[$key]['s_school_name']  = '嘻哈平台';
                }

                if ($value['sh_tag'] == '') {
                    $shifts_list[$key]['sh_tag']  = '--';
                }

                $shifts_list[$key]['sh_imgurl'] = $this->buildUrl($value['sh_imgurl']);

            }
        } 
        $school_shifts = array('school_shifts' => $shifts_list, 'page' => $page, 'count' => $count);
        return $school_shifts;
    }

    /**
    * 根据班制id获得一条班制信息
    *
    * @return void
    * @author wl
    * @date   August 02, 2016
    * @update Nov 02, 2016
    * @update Dec 12, 2016
    **/
    public function getSchoolShiftsById ($sid) {
        $school_shifts = $this->table(C('DB_PREFIX').'school_shifts ss')
            ->field(
                'ss.*, 
                 l_school_id, 
                 s_school_name, 
                 c.id as cid, 
                 coupon_code, 
                 stc.id as s_tag_id, 
                 tag_name, 
                 ca.l_coach_id, 
                 ca.s_coach_name'
            )
            ->join(C('DB_PREFIX').'coach ca ON ca.l_coach_id = ss.coach_id', 'LEFT')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = ss.sh_school_id', 'LEFT')
            ->join(C('DB_PREFIX').'coupon c ON c.id = ss.coupon_id', 'LEFT')
            ->join(C('DB_PREFIX').'system_tag_config stc ON stc.id = ss.sh_tag_id', 'LEFT')
            ->where('ss.id = :sid')
            ->bind(['sid' => $sid])
            ->fetchSql(false)
            ->find();
        if (!empty($school_shifts)) {
            if ($school_shifts['sh_type'] == 1) {
                $school_shifts['sh_type_name'] = '计时班';

            } elseif ($school_shifts['sh_type'] == 2) {
                $school_shifts['sh_type_name'] = '普通班';

            } elseif ($school_shifts['sh_type'] == 3) {
                $school_shifts['sh_type_name'] = 'vip班';

            }
            if ($school_shifts['s_school_name'] == '') {
                $school_shifts['s_school_name'] = '嘻哈平台';
            }
            return $school_shifts;
            
        } else {
            return array();
        }
    }

    
    /**
     * 通过school_id获取教练id和教练名称
     *
     * @return  void
     * @author  wl
     * @date    Dec 12, 2016
     **/
    public function getCoachNameBySchoolId ($school_id) {
        if (!is_numeric($school_id)) {
            return false;
        }
        
        $coach_list = $this->table(C('DB_PREFIX').'coach coach')
            ->join(C('DB_PREFIX').'user user ON user.l_user_id = coach.user_id', 'LEFT')
            ->where(array('user.i_user_type' => 1, 'user.i_status' => 0))
            ->where('s_school_name_id = :sid')
            ->bind(['sid' => $school_id])
            ->fetchSql(false)
            ->field('l_coach_id, s_coach_name')
            ->select();
        if (!empty($coach_list)) {
            return $coach_list;
        } else {
            return array();
        }

    }
    /**
     * 获取系统设置的教练标签
     *
     * @return  void
     * @author  wl
     * @date    Dec 12, 2016
     **/
    public function getCoachTag () {
        $coachtaglist = $this->table(C('DB_PREFIX').'system_tag_config')
            ->where(array('user_type' => 2))
            ->field('id, tag_name')
            ->select();
        if (!empty($coachtaglist)) {
            return $coachtaglist;
        } else {
            return array();
        }
    }

     /**
     * 获取教练设置的优惠券
     *
     * @return  void
     * @author  wl
     * @date    Dec 12, 2016
     **/
    public function getCoachCoupon ($coach_id) {
        if (!is_numeric($coach_id)) {
            return false;
        }
        $time = time();
        $coachcouponlist = $this->table(C('DB_PREFIX').'coupon')
            ->where(array('owner_type' => 1, 'owner_id' => $coach_id, 'expiretime' => array('egt', $time)))
            ->field('id, coupon_code')
            ->select();
        if (!empty($coachcouponlist)) {
            return $coachcouponlist;
        } else {
            return array();
        }
    }

    /**
    * 获取优惠券的相关信息根据school_id
    *
    * @return  void
    * @author  wl
    * @date    Nov 02, 2016
    **/
    public function getCoupCodeById ($school_id) {
        $time = time();
        if ($school_id == 0) {
            $couponlist = $this->table(C('DB_PREFIX').'coupon c')
                ->where(array('owner_type' => 3, 'owner_id' => 0, 'expiretime' => array('egt', $time)))
                ->field('id, coupon_code')
                ->fetchSql(false)
                ->select();
        } else {
            $couponlist = $this->table(C('DB_PREFIX').'coupon c')
                ->where(array('owner_type' => 2, 'owner_id' => $school_id, 'expiretime' => array('egt', $time)))
                ->field('id, coupon_code')
                ->fetchSql(false)
                ->select();
        }
        if ($couponlist) {
            return $couponlist;
        } else {
            return array();
        }
    }

    /**
    * 获取系统标签的相关信息
    *
    * @return  void
    * @author  wl
    * @date    Nov 02, 2016
    **/
    public function getSystemTagList () {
        $systemtaglist = $this->table(C('DB_PREFIX').'system_tag_config')
            ->where(array('user_type' => 3))
            ->field('id, tag_name')
            ->fetchSql(false)
            ->select();
        return $systemtaglist;
    }

    /**
    * 获取单条标签的相关信息
    *
    * @return  void
    * @author  wl
    * @date    Nov 02, 2016
    **/
    public function getSystemTagById ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $systemtaglist = $this->table(C('DB_PREFIX').'system_tag_config')
            ->where(array('id' => $id))
            ->field('id, tag_name')
            ->fetchSql(false)
            ->find();
        return $systemtaglist;
    }

    /**
     * 根据$school_id, $type, $title判断新添的驾校班制是否存在
     *
     * @return  void
     * @author  wl
     * @date    August 01, 2016
     **/
    public function getShiftsBySchoolId ($school_id, $type, $title) {
        $school_shifts = $this->table(C('DB_PREFIX').'school_shifts ss')
            ->where('sh_school_id = :school_id AND sh_type = :type AND sh_title = :title')
            ->bind(['school_id' => $school_id, 'type' => $type, 'title' => $title])
            ->fetchSql(false)
            ->find();
        return $school_shifts;
    }
    
    /**
     * 设置班制的套餐状态
     *
     * @return  void
     * @author  wl
     * @date    Apr 11, 2017
     **/
    public function setShiftsPackageStatus ($id, $status) {
        if (!is_numeric($id) || !isset($status)) {
            return false;
        }

        $list = array();
        $data = array('is_package' => $status);
        $result = M('school_shifts')
            ->where(array('id' => $id))
            ->data($data)
            ->save();
        $list['id'] = $id;
        $list['res'] = $result;
        return $list;
    }

    /**
     * 设置驾校班制的排序状态
     *
     * @return  void
     * @author  wl
     * @date    Nov 01, 2016
     **/
    public function setSchoolShiftsOrder ($post) {
        if (empty($post)) {
            return 101; // 参数错误
        }
        if (isset($post['order'])) {
            if (!is_numeric($post['order'])) {
                return 102; //参数类型错误
            } else {
                $old_num = $this->table(C('DB_PREFIX').'school_shifts')
                    ->where('id = :cid')
                    ->bind(['cid' => $post['id']])
                    ->getField('order');
                if ($post['order'] === $old_num) {
                    return 105; // 尚未做任何修改
                }
            }
        }

        $data['order'] = $post['order'];
        $school_shifts = D('school_shifts');
        if ($res = $school_shifts->create($data)) {
            $result = $school_shifts->where('id = :cid')
                ->bind(['cid' => $post['id']])
                ->fetchSql(false)
                ->save($res);
            if ($result) {
                return 200;
            } else {
                return 400;
            }
        }

    }

    /**
     * 设置驾校班制是否推荐的状态
     *
     * @return  void
     * @author  wl
     * @date    Nov 01, 2016
     **/
    public function setSchoolShiftsStatus ($id, $status) {
        if (!is_numeric($id) || !isset($status)) {
            return false;
        }

        $list = array();
        $data = array('is_promote' => $status);
        $result = M('school_shifts')
            ->where(array('id' => $id))
            ->data($data)
            ->fetchSql(false)
            ->save();
        $list['id'] = $id;
        $list['res'] = $result;
        return $list;
    }
    /**
     * 设置驾校班制是否推荐的状态
     *
     * @return  void
     * @author  wl
     * @date    Nov 01, 2016
     **/
    public function setSchoolShiftsDeletedStatus ($id, $status) {
        if (!is_numeric($id) || !isset($status)) {
            return false;
        }

        $list = array();
        $data = array('deleted' => $status);
        $result = M('school_shifts')
            ->where(array('id' => $id))
            ->data($data)
            ->fetchSql(false)
            ->save();
        $list['id'] = $id;
        $list['res'] = $result;
        return $list;
    }

    //删除班制
    /*
     * @param array $shifts_id
     * @return int $res
     * @author Gao
     */
    public function delShifts($shifts_id = array()) {
        if (!$shifts_id) {
            return false;
        }

        $map = array();
        $map['id'] = array('in', $shifts_id);
        $res = M('school_shifts')->where($map)
            ->fetchSql(false)
            ->save(array('deleted' => 2));
            // ->delete();
        return $res;
    }

// 4.驾校报名点管理模块
    /**
     * 获取所有报名点的列表
     *
     * @return  void
     * @author  wl
     * @date    August 02, 2016
     **/
    public function getTrainLocationLists($school_id) {
        $train_locations = array();
        $map = [];
        if ($school_id != 0) {
            $map['s.l_school_id'] = $school_id;
        }
        $count = $this->table(C('DB_PREFIX').'school_train_location tl')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = tl.tl_school_id', 'LEFT')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $school_train_location = $this->table(C('DB_PREFIX').'school_train_location tl')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = tl.tl_school_id', 'LEFT')
            ->where($map)
            ->field('tl.*, l_school_id, s_school_name, s_address')
            ->order('tl.order DESC, tl.id DESC')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->fetchSql(false)
            ->select();
        
        if (!empty($school_train_location)) {
            foreach ($school_train_location as $key => $value) {
                if ( $value['tl_imgurl'] != '') {
                    $tl_imgurl = json_decode($value['tl_imgurl'], true);
                    if (!empty($tl_imgurl)) {
                        foreach ($tl_imgurl as $k => $v) {
                            $imgurl = $this->buildUrl($v);
                            if ($imgurl != '') {
                                $school_train_location[$key]['imgurl'][$k]['tl_imgurl'] = $imgurl;
                                $school_train_location[$key]['imgurl'][$k]['tl_imgurl_all'] = $imgurl;
                            } 
                        }
                    } else {
                        $school_train_location[$key]['imgurl'] = array();
                    }
                } else {
                    $school_train_location[$key]['imgurl'] = array();
                }

                if ($value['addtime'] != 0) {
                    $school_train_location[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $school_train_location[$key]['addtime'] = '--';
                }

                if ($value['s_address'] == '') {
                    $school_train_location[$key]['s_address'] = '--';
                }

                if ($value['tl_school_id'] == 0) {
                    $school_train_location[$key]['s_school_name'] = '嘻哈平台';
                }
            }
        }

        $train_locations = array('train_locations' => $school_train_location, 'page' => $page, 'count' => $count);
        return $train_locations;

    }

    /**
     * 搜索驾校的报名点
     *
     * @return  void
     * @author  wl
     * @date    Dec 14, 2016
     **/
    public function searchSchoolTrainLocation ($param, $school_id) {
        $map = array();
        if ($param['s_keyword'] != '') {
            $map['tl_train_address'] = array('LIKE', '%'.$param['s_keyword'].'%');
        }
        if ( $school_id != 0) {
            $map['s.school_id'] = $school_id;
        }
        $train_locations = array();
        $count = $this->table(C('DB_PREFIX').'school_train_location tl')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = tl.tl_school_id', 'LEFT')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $school_train_location = $this->table(C('DB_PREFIX').'school_train_location tl')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = tl.tl_school_id', 'LEFT')
            ->where($map)
            ->field('tl.*, l_school_id, s_school_name, s_address')
            ->order('tl.order DESC, tl.id DESC')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->fetchSql(false)
            ->select();
        if (!empty($school_train_location)) {
            foreach ($school_train_location as $key => $value) {
                if ( $value['tl_imgurl'] != '') {
                    $tl_imgurl = json_decode($value['tl_imgurl'], true);
                    if (!empty($tl_imgurl)) {
                        foreach ($tl_imgurl as $k => $v) {
                            $imgurl = $this->buildUrl($v);
                            if ($imgurl != '') {
                                $school_train_location[$key]['imgurl'][$k]['tl_imgurl'] = $imgurl;
                                $school_train_location[$key]['imgurl'][$k]['tl_imgurl_all'] = $imgurl;
                            } 
                        }
                    } else {
                        $school_train_location[$key]['imgurl'] = array();
                    }
                } else {
                    $school_train_location[$key]['imgurl'] = array();
                }

                if ($value['addtime'] != 0) {
                    $school_train_location[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $school_train_location[$key]['addtime'] = '--';
                }

                if ($value['s_address'] == '') {
                    $school_train_location[$key]['s_address'] = '--';
                }

                if ($value['tl_school_id'] == 0) {
                    $school_train_location[$key]['s_school_name'] = '嘻哈平台';
                }
            }
        }
        $train_locations = array('train_locations' => $school_train_location, 'page' => $page, 'count' => $count);
        return $train_locations;
    }

    /**
     * 检测驾校报名点是否重复
     *
     * @return  void
     * @author  wl
     * @date    Dec 14, 2016
     **/
    public function checkSchoolTrain ($school_id, $address) {
        if (!$school_id && !$address) {
            return false;
        }
        $checkSchoolTrain = $this->table(C('DB_PREFIX').'school_train_location')
            ->where(array('tl_school_id' => $school_id, 'tl_train_address' => $address))
            ->find();
        if (!empty($checkSchoolTrain)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 设置驾校报名点的排序状态
     *
     * @return  void
     * @author  wl
     * @date    Dec 14, 2016
     **/
    public function updateSchoolTrainOrder ($post) {
        if (empty($post)) {
            return 101; // 参数错误
        }
        $order = $post['order'];
        $id = $post['id'];
        if (isset($order)) {
            if (!is_numeric($order)) {
                return 102; // 参数类型错误
            }
            $old_num = $this->table(C('DB_PREFIX').'school_train_location')
                ->where('id = :sid')
                ->bind(['sid' => $id])
                ->getField('order');
            if ($old_num === $order) {
                return 105; // 未做任何修改
            }
        }

        $data = array('order' => $order);
        $result = M('school_train_location')
            ->where('id = :sid')
            ->bind(['sid' => $id])
            ->data($data)
            ->save();
        if ($result) {
            return 200;
        } else {
            return 400;
        }
    }

    /**
     * 根据id获得此id相应的驾校报名地点的信息
     *
     * @return  void
     * @author  wl
     * @date    August 03, 2016
     * @update  Dec 12, 2016
     **/
    public function getTlInfoById ($tl_id) {
        $tl_info = $this->table(C('DB_PREFIX').'school_train_location tl')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = tl.tl_school_id', 'LEFT')
            ->where('id = :tl_id')
            ->bind(['tl_id' => $tl_id])
            ->field('tl.*, s.l_school_id, s.s_school_name')
            ->fetchSql(false)
            ->find();
        if (!empty($tl_info)) {
            if ($tl_info['s_school_name'] == '') {
                $tl_info['s_school_name'] = '未知';
            }
            return $tl_info;
        } else {
            array();
        }
    }

//删除单个报名点
     /**
      * @param integer $id
      * @return boolean
      */
    public function delTrainLocation($id) {
        if (!is_numeric($id)) {
            return false;
        }

        $res = M('school_train_location')
            ->where('id = :tl_id')
            ->bind(['tl_id' => $id])
            ->delete();
        return $res;
    }

// 5.驾校轮播图管理模块
    /**
     * 获取轮播图列表
     *
     * @return  void
     * @author  wl
     * @date    August 04, 2016
     * @update  August 05, 2016
     **/
    public function getBannerList ($school_id) {
        if (!is_numeric($school_id)) {
            return false;
        }
        $bannerlist = $this->where('l_school_id = :school_id')
            ->bind(['school_id' => $school_id])
            ->fetchSql(false)
            ->getField('s_imgurl');
        if ($bannerlist) {
            $banner_list = array_values((array)json_decode($bannerlist, true));
            $bannerSchoolList = array();
            $list = array();
            if (is_array($banner_list)) {
                foreach ($banner_list as $key => $value) {
                    $list[$key]['s_all_imgurl'] = C('HTTP_HOST').$value;
                    if (empty(is_array($list[$key]['s_all_imgurl']))) {
                        $list[$key]['s_all_imgurl'] = C('HTTP_SHOST').$value;
                    } 
                    $list[$key]['s_imgurl'] = $value;
                }
                return $list;
            }
        } else {
            return array();
        }
    }

    
    /**
     * 获得添加的图片的路径
     *
     * @return  void
     * @author  wl
     * @date    August 06, 2016
     * @update  August 19, 2016
     **/
    public function setBannerUrl ($school_id, $files) {
        if (!is_numeric($school_id) || !$files) {
            return false;
        }

        // Upload: 1. create upload object
        $upload = new \Think\Upload();

        // Upload: 2. config upload options
        $upload->maxSize = 2 * 1024 * 1024; //2M
        $upload->exts = array('jpg', 'jpeg', 'png', 'gif');
        $upload->rootPath = '../upload/';
        $upload->savePath = 'school/banner/';
        $upload->subName = $school_id . '/' . date('Y-m-d', time()); // Sub Directory
        $upload->saveName = array('uniqid', 'banner_');
        $upload->hash = false;

        // Upload: 3. upload start
        $bannerurl = $upload->upload();
        if (!$bannerurl) {
            return $upload->getError();
        }
        $bannerlist = array();
        foreach ($bannerurl as $key => $value) {
            $writePath      = $upload->rootPath . $value['savepath'] . $value['savename'];
            $bannerlist[]   = $writePath;
        }
        return $bannerlist;
    }

    /**
     * 添加图片（添加到数据库中）
     *
     * @return  void
     * @author  wl
     * @date    August 17, 2016
     **/
    public function saveBanner ($banner, $school_id) {
        if (!is_numeric($school_id)) {
            return false;
        }
        $list = array();
        $bannerlist = $this->where('l_school_id = :school_id')
            ->bind(['school_id' => $school_id])
            // ->field('s_imgurl')
            ->fetchSql(false)
            ->getField('s_imgurl');
            // ->find();
        if ($bannerlist != null && $bannerlist != 'null') {
            $banner_list = json_decode($bannerlist, true);
            $list  = array_merge($banner_list, $banner);
        } else {
            $list = $banner;
        }
        $s_imgurl = json_encode($list);
        $data = array('s_imgurl' => $s_imgurl);
        $bannerlist = $this->where('l_school_id = :school_id')
            ->bind(['school_id' => $school_id])
            ->data($data)
            ->fetchSql(false)
            ->save();
        return $bannerlist;
    }
    
    /**
     * 删除驾校的轮播图
     *
     * @return  void
     * @author  wl
     * @date    August 19, 2016
     **/
    public function delbanner ($url, $school_id) {
        $school_imgurl = $this->table(C('DB_PREFIX').'school')
            ->where('l_school_id = :sid')
            ->bind(['sid' => $school_id])
            ->field('s_imgurl')
            ->fetchSql(false)
            ->find();
        if ($school_imgurl['s_imgurl']) {
            $s_imgurl = json_decode($school_imgurl['s_imgurl'], true);
            foreach ($s_imgurl as $key => $value) {
                if ($url == $value) {
                    unset($s_imgurl[$key]);
                }
            }
            if (file_exists($url)) {
              unlink($url);
            }
            $imgurl = json_encode($s_imgurl);
            $data = array('s_imgurl' => $imgurl);
            $result = $this->where('l_school_id = :sid')
                ->bind(['sid' => $school_id])
                ->data($data)
                ->fetchSql(false)
                ->save();
            return $result;
        } else {
          return false;
        }
    }

    // 获取轮播图列表
    /*
     * @param integer $school_id 驾校id
     * @return array $banner_list 轮播图列表
    */
    public function getSchoolList($school_id = null) {
        //非整型驾校id
        if (!is_numeric($school_id)) {
            return false;
        }
        $map = array('l_school_id' => $school_id);
        $res = $this->where($map)->getField('s_imgurl');
        $imgurl_arr = array_values((array)json_decode($res));
        return $imgurl_arr;
    }



}

?>