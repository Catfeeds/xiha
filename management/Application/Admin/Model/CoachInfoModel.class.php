<?php
namespace Admin\Model;
use Think\Model;
use Think\Page;

class CoachInfoModel extends BaseModel {
    public $tableName = 'coach';

// 5.教练认证状态列表
    /**
     * 获取教练认证状态
     *
     * @return  void
     * @author  wl
     * @date    Jan 05, 2017
     **/
     public function getCoachCertificationList ($school_id) {
        $map = array();
        $coachcertificationlists = array();
        if ($school_id == 0) {
            $map['user.i_status'] = 0;
            $map['user.i_user_type'] = 1;
            $map['coach.certification_status'] = array('EQ', 2);
        } else {
            $map['user.i_status'] = 0;
            $map['user.i_user_type'] = 1;
            $map['coach.certification_status'] = array('EQ', 2);
            $map['school.l_school_id'] = $school_id;
        }
        
        $count = $this->table(C('DB_PREFIX').'coach coach')
            ->join(C('DB_PREFIX').'user user ON user.l_user_id = coach.user_id', 'LEFT')
            ->join(C('DB_PREFIX').'school school ON school.l_school_id = coach.s_school_name_id', 'LEFT')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $coachcertificationlist = $this->table(C('DB_PREFIX').'coach coach')
            ->field(
                'school.s_school_name,
                 coach.l_coach_id, 
                 s_coach_name, 
                 s_coach_phone, 
                 certification_status, 
                 coach_license_imgurl, 
                 id_card_imgurl, 
                 personal_image_url, 
                 coach_car_imgurl,
                 coach.updatetime'
            )
            ->join(C('DB_PREFIX').'user user ON user.l_user_id = coach.user_id', 'LEFT')
            ->join(C('DB_PREFIX').'school school ON school.l_school_id = coach.s_school_name_id', 'LEFT')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('l_coach_id DESC')
            ->fetchSql(false)
            ->where($map)
            ->select();
        if (!empty($coachcertificationlist)) {
            foreach ($coachcertificationlist as $key => $value) {
                if ($value['s_school_name'] == '') {
                    $coachcertificationlist[$key]['s_school_name'] = '--';
                }

                if ($value['s_coach_name'] == '') {
                    $coachcertificationlist[$key]['s_coach_name'] = '--';
                }

                if ($value['s_coach_phone'] == '') {
                    $coachcertificationlist[$key]['s_coach_phone'] = '--';
                }

                if ($value['updatetime'] != 0) {
                    $coachcertificationlist[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $coachcertificationlist[$key]['updatetime'] = '--';
                }

                $coachcertificationlist[$key]['license_imgurl'] = $this->buildUrl($value['coach_license_imgurl']);
                $coachcertificationlist[$key]['idcard_imgurl'] = $this->buildUrl($value['id_card_imgurl']);
                $coachcertificationlist[$key]['personal_imgurl'] = $this->buildUrl($value['personal_image_url']);
                $coachcertificationlist[$key]['car_imgurl'] = $this->buildUrl($value['coach_car_imgurl']);
            
            }
        }
        $coachcertificationlists = array('coachcertificationlist' => $coachcertificationlist, 'count' => $count, 'page' => $page);
        return $coachcertificationlists;
    }

    /**
     * 教练认证状态的搜索
     *
     * @return  void
     * @author  wl
     * @date    Jan 05, 2017
     **/
    public function searchCoachCertification ($param, $school_id) {
        $map = array();
        $complex = array();
        $coachcertificationlists = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        // $condition = array('i_user_type' => 1, 'i_status' => 0,);
        if ($param['search_info'] == '') {
            $complex['s_school_name'] = array('LIKE', $s_keyword);
            $complex['s_coach_name'] = array('LIKE', $s_keyword);
            $complex['s_coach_phone'] = array('LIKE', $s_keyword);
            $complex['_logic'] = 'OR';
        } else {
            $complex[$param['search_info']] = array('LIKE', $s_keyword);
        }
        $map['_complex'] = $complex;

        if ($param['certification_status'] != 0) {
            $map['certification_status'] = array('EQ', $param['certification_status']);
        } else {
            $map['coach.certification_status'] = array('EQ', 2);
        }

        if ($school_id == 0) {
            $map['user.i_status'] = 0;
            $map['user.i_user_type'] = 1;
        } else {
            $map['user.i_status'] = 0;
            $map['user.i_user_type'] = 1;
            $map['school.l_school_id'] = $school_id;
        }
        $count = $this->table(C('DB_PREFIX').'coach coach')
            ->join(C('DB_PREFIX').'user user ON user.l_user_id = coach.user_id', 'LEFT')
            ->join(C('DB_PREFIX').'school school ON school.l_school_id = coach.s_school_name_id', 'LEFT')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $coachcertificationlist = $this->table(C('DB_PREFIX').'coach coach')
            ->field(
                'school.s_school_name,
                 coach.l_coach_id, 
                 s_coach_name, 
                 s_coach_phone, 
                 certification_status, 
                 coach_license_imgurl, 
                 id_card_imgurl, 
                 personal_image_url, 
                 coach_car_imgurl,
                 coach.updatetime'
            )
            ->join(C('DB_PREFIX').'user user ON user.l_user_id = coach.user_id', 'LEFT')
            ->join(C('DB_PREFIX').'school school ON school.l_school_id = coach.s_school_name_id', 'LEFT')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('l_coach_id DESC')
            ->fetchSql(false)
            ->where($map)
            ->select();
        if (!empty($coachcertificationlist)) {
            foreach ($coachcertificationlist as $key => $value) {
                if ($value['s_school_name'] == '') {
                    $coachcertificationlist[$key]['s_school_name'] = '--';
                }

                if ($value['s_coach_name'] == '') {
                    $coachcertificationlist[$key]['s_coach_name'] = '--';
                }

                if ($value['s_coach_phone'] == '') {
                    $coachcertificationlist[$key]['s_coach_phone'] = '--';
                }

                if ($value['updatetime'] != 0) {
                    $coachcertificationlist[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $coachcertificationlist[$key]['updatetime'] = '--';
                }

                $coachcertificationlist[$key]['license_imgurl'] = $this->buildUrl($value['coach_license_imgurl']);
                $coachcertificationlist[$key]['idcard_imgurl'] = $this->buildUrl($value['id_card_imgurl']);
                $coachcertificationlist[$key]['personal_imgurl'] = $this->buildUrl($value['personal_image_url']);
                $coachcertificationlist[$key]['car_imgurl'] = $this->buildUrl($value['coach_car_imgurl']);
            }
        }
        $coachcertificationlists = array('coachcertificationlist' => $coachcertificationlist, 'count' => $count, 'page' => $page);
        return $coachcertificationlists;
    }

    /**
     * 更新的认证状态状态
     *
     * @return  void
     * @author  wl
     * @date    Jan 05, 2017
     **/
    public function updateCoachCertification ($id, $status) {
        if (!$id && !$status) {
            return false;
        }
        $data = array('certification_status' => $status);
        $result = M('coach')
            ->where('l_coach_id = :cid')
            ->bind(['cid' => $id])
            ->data($data)
            ->fetchSql(false)
            ->save();
        if ($result) {
            return $result;
        } else {
            return false;
        }

    }



}
?>
