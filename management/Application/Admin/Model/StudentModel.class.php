<?php
namespace Admin\Model;
use Think\Model;
use Think\Page;

class StudentModel extends BaseModel {
    private $_link = array(
    );
    public $tableName   = 'user_exam_records';

// 1.学员列表模块
    /**
     * 获得学员的相关信息
     *
     * @return  $school_id 驾校id （0：嘻哈管理员）
     * @author  wl
     * @date    july 25, 2016;
     * @update  Nov 29, 2016
     **/
    public function getStudentList ($school_id) {
        $student_lists  = array();
        $map = array();
        $map['u.i_status'] = array('eq', '0');
        $map['u.i_user_type'] = array('eq', '0');
        if ($school_id == 0) {
            $count  = $this->table(C('DB_PREFIX') . 'user u')
                ->join(C('DB_PREFIX') . 'users_info uf ON uf.user_id = u.l_user_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'school c ON c.l_school_id = uf.school_id', 'LEFT')
                ->where($map)
                ->fetchSql(false)
                ->count();
            $Page = new Page($count, 10);
            $page = $this->getPage($count, 10);
            $student_list = $this->table(C('DB_PREFIX') . 'user u')
                ->join(C('DB_PREFIX') . 'users_info uf ON uf.user_id = u.l_user_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'school c ON c.l_school_id = uf.school_id', 'LEFT')
                ->where($map)
                ->field('c.s_school_name, l_user_id, s_username, i_user_type, i_status, s_real_name, i_from, s_phone, is_first, u.addtime, u.updatetime, uf.* ')
                ->order('l_user_id DESC')
                ->limit($Page->firstRow . ',' . $Page->listRows)
                ->fetchSql(false)
                ->select();
        } else {
            $count = $this->table(C('DB_PREFIX') . 'user u')
                ->join(C('DB_PREFIX') . 'users_info uf ON uf.user_id = u.l_user_id', 'LEFT')
                ->where('school_id = :school_id')
                ->bind(['school_id' => $school_id])
                ->where($map)
                ->fetchSql(false)
                ->count();
            $Page = new Page($count, 10);
            $page = $this->getPage($count, 10);
            $student_list = $this->table(C('DB_PREFIX') . 'user u')
                ->join(C('DB_PREFIX') . 'users_info uf ON uf.user_id = u.l_user_id', 'LEFT')
                ->where('school_id = :school_id')
                ->bind(['school_id' => $school_id])
                ->where($map)
                ->field('l_user_id, s_username, i_user_type, i_status, s_real_name, i_from, s_phone, is_first, u.addtime, u.updatetime, uf.*')
                ->order('l_user_id DESC')
                ->limit($Page->firstRow . ',' . $Page->listRows)
                ->fetchSql(false)
                ->select();
        }
        if (!empty($student_list)) {
            foreach ($student_list as $key => $value) {
                if ($value['s_school_name'] != '') {
                    $student_list[$key]['s_school_name'] = $value['s_school_name'];
                } else {
                    $student_list[$key]['s_school_name'] = '--';
                }
                if ($value['addtime'] != 0) {
                    $student_list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $student_list[$key]['addtime'] = '--';
                }
                if ($value['updatetime'] != 0) {
                    $student_list[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $student_list[$key]['updatetime'] = '--';
                }

                if ($value['uf.addtime'] != 0) {
                    $student_list[$key]['uf.addtime'] = date('Y-m-d H:i:s', $value['uf.addtime']);
                } else {
                    $student_list[$key]['uf.addtime'] = '--';
                }
                if ($value['uf.updatetime'] != 0) {
                    $student_list[$key]['uf.updatetime'] = date('Y-m-d H:i:s', $value['uf.updatetime']);
                } else {
                    $student_list[$key]['uf.updatetime'] = '--';
                }

                if ($value['address'] != '') {
                    $student_list[$key]['address'] = $value['address'];
                } else {
                    $student_list[$key]['address'] = '--';
                }

                if ($value['identity_id'] != '') {
                    $student_list[$key]['identity_id'] = $value['identity_id'];
                } else {
                    $student_list[$key]['identity_id'] = '--';
                }

                if ($value['s_phone'] == '') {
                    $student_list[$key]['s_phone'] = '--';
                }

                if ($value['s_real_name'] == '') {
                    $student_list[$key]['s_real_name'] = '--';
                }

                if ($value['lesson_name'] == '') {
                    $student_list[$key]['lesson_name'] = '--';
                }
                if ($value['license_name'] == '') {
                    $student_list[$key]['license_name'] = '--';
                }
                if ($value['learncar_status'] == '') {
                    $student_list[$key]['learncar_status'] = '--';
                }
                if ($value['s_username'] == '') {
                    $student_list[$key]['s_username'] = '--';
                }

                if ($value['xiha_coin'] == '') {
                    $student_list[$key]['xiha_coin'] = '--';
                }
                if ($value['signin_num'] == '') {
                    $student_list[$key]['signin_num'] = '--';
                }
                if ($value['age'] == '') {
                    $student_list[$key]['age'] = '--';
                }

            }
        }
        $student_lists = array('student_list' => $student_list, 'page' => $page, 'count' => $count);
        return $student_lists;
    }

    /**
     * 根据条件搜索学员
     *
     * @return  void
     * @author  wl
     * @date    july 25, 2016;
     * @update  Nov 29, 2016
     **/
    public function searchStudent($param, $school_id) {
        $map = array();
        $complex = array();
        $keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_type'] == '') {
            $complex['l_user_id'] = array('eq', $param['s_keyword']);
            $complex['s_real_name'] = array('LIKE', $keyword);
            $complex['s_phone'] = array('LIKE', $keyword);
            $complex['identity_id'] = array('LIKE', $keyword);
            $complex['c.s_school_name'] = array('LIKE', $keyword);
            $complex['_logic'] = 'OR';
        } else {
            $complex[$param['search_type']] = array('LIKE', $keyword);
            if ($param['search_type'] == 'l_user_id') {
                $complex[$param['search_type']] = array('eq', $param['s_keyword']);
            }
        }
        $map['_complex'] = $complex;
        if ($param['status'] != '') {
            $map['u.i_status'] = array('EQ', $param['status']);
        } else {
            $map['u.i_status'] = array('EQ', 0);
        }
        $map['i_user_type'] = array('EQ', 0);
        $student_lists = array();
        if ($school_id == 0) {
            $count  = $this->table(C('DB_PREFIX') . 'user u')
                ->join(C('DB_PREFIX') . 'users_info uf ON uf.user_id = u.l_user_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'school c ON c.l_school_id = uf.school_id', 'LEFT')
                // ->where(array('i_user_type' => 0))
                ->where($map)
                ->fetchSql(false)
                ->count();
            $Page = new Page($count, 10, $param);
            $page = $this->getPage($count, 10, $param);
            $student_list = $this->table(C('DB_PREFIX') . 'user u')
                ->join(C('DB_PREFIX') . 'users_info uf ON uf.user_id = u.l_user_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'school c ON c.l_school_id = uf.school_id', 'LEFT')
                // ->where(array('i_user_type' => 0))
                ->where($map)
                ->field('c.s_school_name, l_user_id, s_username, i_user_type, i_status, s_real_name, i_from, s_phone, is_first, u.addtime, u.updatetime, uf.*')
                ->order('l_user_id DESC')
                ->limit($Page->firstRow . ',' . $Page->listRows)
                ->fetchSql(false)
                ->select();
        } else {
            $count = $this->table(C('DB_PREFIX') . 'user u')
                ->join(C('DB_PREFIX') . 'users_info uf ON uf.user_id = u.l_user_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'school c ON c.l_school_id = uf.school_id', 'LEFT')
                ->where('school_id = :school_id')
                ->bind(['school_id' => $school_id])
                ->where($map)
                ->fetchSql(false)
                ->count();
            $Page = new Page($count, 10, $param);
            $page = $this->getPage($count, 10, $param);
            $student_list = $this->table(C('DB_PREFIX') . 'user u')
                ->join(C('DB_PREFIX') . 'users_info uf ON uf.user_id = u.l_user_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'school c ON c.l_school_id = uf.school_id', 'LEFT')
                ->where('school_id = :school_id')
                ->bind(['school_id' => $school_id])
                ->where($map)
                ->field('l_user_id, s_username, i_user_type, i_status, s_real_name, i_from, s_phone, is_first, u.addtime, u.updatetime, uf.*')
                ->order('l_user_id DESC')
                ->limit($Page->firstRow . ',' . $Page->listRows)
                ->fetchSql(false)
                ->select();
        }
        if (!empty($student_list)) {
            foreach ($student_list as $key => $value) {
                if ($value['s_school_name'] != '') {
                    $student_list[$key]['s_school_name'] = $value['s_school_name'];
                } else {
                    $student_list[$key]['s_school_name'] = '--';
                }
                if ($value['addtime'] != 0) {
                    $student_list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $student_list[$key]['addtime'] = '--';
                }
                if ($value['updatetime'] != 0) {
                    $student_list[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $student_list[$key]['updatetime'] = '--';
                }

                if ($value['uf.addtime'] != 0) {
                    $student_list[$key]['uf.addtime'] = date('Y-m-d H:i:s', $value['uf.addtime']);
                } else {
                    $student_list[$key]['uf.addtime'] = '--';
                }
                if ($value['uf.updatetime'] != 0) {
                    $student_list[$key]['uf.updatetime'] = date('Y-m-d H:i:s', $value['uf.updatetime']);
                } else {
                    $student_list[$key]['uf.updatetime'] = '--';
                }

                if ($value['address'] != '') {
                    $student_list[$key]['address'] = $value['address'];
                } else {
                    $student_list[$key]['address'] = '--';
                }

                if ($value['identity_id'] != '') {
                    $student_list[$key]['identity_id'] = $value['identity_id'];
                } else {
                    $student_list[$key]['identity_id'] = '--';
                }

                if ($value['s_phone'] == '') {
                    $student_list[$key]['s_phone'] = '--';
                }

                if ($value['s_real_name'] == '') {
                    $student_list[$key]['s_real_name'] = '--';
                }

                if ($value['lesson_name'] == '') {
                    $student_list[$key]['lesson_name'] = '--';
                }
                if ($value['license_name'] == '') {
                    $student_list[$key]['license_name'] = '--';
                }
                if ($value['learncar_status'] == '') {
                    $student_list[$key]['learncar_status'] = '--';
                }
                if ($value['s_username'] == '') {
                    $student_list[$key]['s_username'] = '--';
                }

                if ($value['xiha_coin'] == '') {
                    $student_list[$key]['xiha_coin'] = '--';
                }
                if ($value['signin_num'] == '') {
                    $student_list[$key]['signin_num'] = '--';
                }
                if ($value['age'] == '') {
                    $student_list[$key]['age'] = '--';
                }

            }
        }
        $student_lists = array('student_list' => $student_list, 'page' => $page, 'count' => $count);
        return $student_lists;
    }

    /**
     * 设置学员的删除状态
     *
     * @return  void
     * @author  wl
     * @date    Nov 25, 2016
     **/
    public function setStudentStatus ($id, $status) {
        if (!is_numeric($id) && !status) {
            return false;
        }
        $list = array();
        $data = array('i_status' => $status);
        $result = M('user')->where(array('l_user_id' => $id, 'i_user_type' => 0))
            ->fetchSql(false)
            ->data($data)
            ->save();
        $list['i_status'] = $result;
        $list['l_user_id'] = $id;
        if (!empty($list)) {
            return $list;
        } else {
            return array();
        }
    }

    /**
     * 根据手机号判断学员是否存在
     *
     * @return  void
     * @author  wl
     * @date    Nov 25, 2016
     **/
    public function checkStudentInfo ($phone) {
        if (!$phone) {
            return false;
        }
        $checkStudentInfo = $this->table(C('DB_PREFIX').'user')
            ->where(array('s_phone' => $phone, 'i_user_type' => 0, 'i_status' => 0))
            ->find();
        if (!empty($checkStudentInfo)) {
            return $checkStudentInfo;
        } else {
            return array();
        }
    }

    /**
     * 根据获得的id将数据带到表单中进行修改
     *
     * @return  void
     * @author  wl
     * @date    july 27, 2016
     * @update  Nov 29, 2016
     **/
    public function getUserInfoById ($id) {
        $student_list =   $this->table(C('DB_PREFIX') . 'user u')
            ->join(C('DB_PREFIX') . 'users_info uf ON uf.user_id = u.l_user_id', 'LEFT')
            ->join(C('DB_PREFIX') . 'province p ON p.provinceid = uf.province_id', 'LEFT')
            ->join(C('DB_PREFIX') . 'city ct ON ct.cityid = uf.city_id', 'LEFT')
            ->join(C('DB_PREFIX') . 'area a ON a.areaid = uf.area_id', 'LEFT')
            ->join(C('DB_PREFIX') . 'school c ON c.l_school_id = uf.school_id', 'LEFT')
            ->where('u.l_user_id = :uid AND i_user_type = :user_type AND uf.user_id = :user_id')
            ->bind(['uid' => $id, 'user_type' => 0, 'user_id' => $id])
            ->field('l_user_id, i_status, s_real_name,s_username, s_phone, age, sex, identity_id, address,i_user_type, i_from, s_school_name, learncar_status, provinceid, province, areaid, area, cityid, city, photo_id, user_photo, u.addtime, u.updatetime, uf.lesson_id, uf.lesson_name, uf.license_id, uf.license_name, license_num')
            ->fetchSql(false)
            ->find();
        if ($student_list) {
            if ($student_list['s_school_name'] == '') {
                $student_list['s_school_name'] = '';
            } else {
                $student_list['s_school_name'] = $student_list['s_school_name'];
            }
        }
        return $student_list;
    }
    /**
     * 根据科目id，获取科目名称
     *
     * @return  void
     * @author  wl
     * @date    Nov 29, 2016
     **/
    public function getLessonNameById ($lesson_id) {
        if (!is_numeric($lesson_id)) {
            return false;
        }
        $lesson_name = $this->table(C('DB_PREFIX').'lesson_config')
            ->where(array('lesson_id' => $lesson_id))
            ->fetchSql(false)
            ->getField('lesson_name');
        if ($lesson_name != '') {
            return $lesson_name;
        } else {
            return '';
        }
    }

    /**
     * 根据牌照id，获取牌照名称
     *
     * @return  void
     * @author  wl
     * @date    Nov 29, 2016
     **/
    public function getLicenseNameById ($license_id) {
        if (!is_numeric($license_id)) {
            return false;
        }
        $license_name = $this->table(C('DB_PREFIX').'license_config')
            ->where(array('license_id' => $license_id))
            ->fetchSql(false)
            ->getField('license_name');
        if ($license_name != '') {
            return $license_name;
        } else {
            return '';
        }
    }

// 2.学员回收站模块
    /**
     * 展示已经被删除的学员的列表
     *
     * @return  void
     * @author  wl
     * @date    july 26, 2016
     **/
    public function getDelStudentList ($school_id) {
        $student_lists  = array();
        if ($school_id == 0) {
            $count  = $this->table(C('DB_PREFIX') . 'user u')
                ->join(C('DB_PREFIX') . 'users_info uf ON uf.user_id = u.l_user_id', 'LEFT')
                ->where(array('i_user_type' => 0, 'i_status' => 2))
                ->fetchSql(false)
                ->count();
            $Page = new Page($count, 10);
            $page = $this->getPage($count, 10);
            $student_list = $this->table(C('DB_PREFIX') . 'user u')
                ->join(C('DB_PREFIX') . 'users_info uf ON uf.user_id = u.l_user_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'school c ON c.l_school_id = uf.school_id', 'LEFT')
                ->where(array('i_user_type' => 0, 'i_status' => 2))
                ->field('l_user_id, s_username, i_user_type, i_status, s_real_name, i_from, s_phone, is_first, u.addtime, u.updatetime, uf.*')
                ->order('l_user_id DESC')
                ->limit($Page->firstRow . ',' . $Page->listRows)
                ->fetchSql(false)
                ->select();
        } else {
            $count = $this->table(C('DB_PREFIX') . 'user u')
                ->join(C('DB_PREFIX') . 'users_info uf ON uf.user_id = u.l_user_id', 'LEFT')
                ->where('school_id = :school_id AND i_user_type = :user_type AND i_status = :i_status')
                ->bind(['school_id' => $school_id, 'user_type' => 0, 'i_status' => 2])
                ->fetchSql(false)
                ->count();
            $Page = new Page($count, 10);
            $page = $this->getPage($count, 10);
            $student_list = $this->table(C('DB_PREFIX') . 'user u')
                ->join(C('DB_PREFIX') . 'users_info uf ON uf.user_id = u.l_user_id', 'LEFT')
                ->where('school_id = :school_id AND i_user_type = :user_type AND i_status = :i_status')
                ->bind(['school_id' => $school_id, 'user_type' => 0, 'i_status' => 2])
                ->field('l_user_id, s_username, i_user_type, i_status, s_real_name, i_from, s_phone, is_first, u.addtime, u.updatetime, uf.*')
                ->order('l_user_id DESC')
                ->limit($Page->firstRow . ',' . $Page->listRows)
                ->fetchSql(false)
                ->select();
        }
        if ($student_list) {
            foreach ($student_list as $key => $value) {
                if ($value['s_school_name'] != '') {
                    $student_list[$key]['s_school_name'] = $value['s_school_name'];
                } else {
                    $student_list[$key]['s_school_name'] = '--';
                }
                if ($value['addtime'] != 0) {
                    $student_list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $student_list[$key]['addtime'] = '--';
                }
                if ($value['updatetime'] != 0) {
                    $student_list[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $student_list[$key]['updatetime'] = '--';
                }

                if ($value['uf.addtime'] != 0) {
                    $student_list[$key]['uf.addtime'] = date('Y-m-d H:i:s', $value['uf.addtime']);
                } else {
                    $student_list[$key]['uf.addtime'] = '--';
                }
                if ($value['uf.updatetime'] != 0) {
                    $student_list[$key]['uf.updatetime'] = date('Y-m-d H:i:s', $value['uf.updatetime']);
                } else {
                    $student_list[$key]['uf.updatetime'] = '--';
                }

                if ($value['address'] != '') {
                    $student_list[$key]['address'] = $value['address'];
                } else {
                    $student_list[$key]['address'] = '--';
                }

                if ($value['identity_id'] != '') {
                    $student_list[$key]['identity_id'] = $value['identity_id'];
                } else {
                    $student_list[$key]['identity_id'] = '--';
                }
            }
        }
        $student_lists = array('student_list' => $student_list, 'page' => $page, 'count' => $count);
        return $student_lists;
    }
    /**
     * 学员回收站中根据条件搜索学员
     *
     * @return  void
     * @author  wl
     * @date    july 25, 2016;
     **/
    public function searchDelStudent($param, $school_id) {
        $map = array();
        $complex = array();
        $keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_type'] == '') {
            $complex['l_user_id'] = array('eq', $param['s_keyword']);
            $complex['s_real_name'] = array('LIKE', $keyword);
            $complex['s_username'] = array('LIKE', $keyword);
            $complex['s_phone'] = array('LIKE', $keyword);
            $complex['identity_id'] = array('LIKE', $keyword);
            $complex['_logic'] = 'OR';
        } else {
            $complex[$param['search_type']] = array('LIKE', $keyword);
            if ($param['search_type'] == 'l_user_id') {
                $complex[$param['search_type']] = array('eq', $param['s_keyword']);
            }

        }
        $map['_complex'] = $complex;
        $student_lists = array();
        if ($school_id == 0) {
            $count  = $this->table(C('DB_PREFIX') . 'user u')
                ->join(C('DB_PREFIX') . 'users_info uf ON uf.user_id = u.l_user_id', 'LEFT')
                ->where(array('i_user_type' => 0, 'i_status' => 2))
                ->where($map)
                ->fetchSql(false)
                ->count();
            $Page = new Page($count, 10, $param);
            $page = $this->getPage($count, 10, $param);
            $student_list = $this->table(C('DB_PREFIX') . 'user u')
                ->join(C('DB_PREFIX') . 'users_info uf ON uf.user_id = u.l_user_id', 'LEFT')
                ->join(C('DB_PREFIX') . 'school c ON c.l_school_id = uf.school_id', 'LEFT')
                ->where(array('i_user_type' => 0, 'i_status' => 2))
                ->where($map)
                ->field('l_user_id, s_username, i_user_type, i_status, s_real_name, i_from, s_phone, is_first, u.addtime, u.updatetime, uf.*')
                ->order('l_user_id DESC')
                ->limit($Page->firstRow . ',' . $Page->listRows)
                ->fetchSql(false)
                ->select();
        } else {
            $count = $this->table(C('DB_PREFIX') . 'user u')
                ->join(C('DB_PREFIX') . 'users_info uf ON uf.user_id = u.l_user_id', 'LEFT')
                ->where('school_id = :school_id AND i_user_type = :user_type AND i_status = :i_status')
                ->bind(['school_id' => $school_id, 'user_type' => 0, 'i_status' => 2])
                ->where($map)
                ->fetchSql(false)
                ->count();
            $Page = new Page($count, 10, $param);
            $page = $this->getPage($count, 10, $param);
            $student_list = $this->table(C('DB_PREFIX') . 'user u')
                ->join(C('DB_PREFIX') . 'users_info uf ON uf.user_id = u.l_user_id', 'LEFT')
                ->where('school_id = :school_id AND i_user_type = :user_type AND i_status = :i_status')
                ->bind(['school_id' => $school_id, 'user_type' => 0, 'i_status' => 2])
                ->where($map)
                ->field('l_user_id, s_username, i_user_type, i_status, s_real_name, i_from, s_phone, is_first, u.addtime, u.updatetime, uf.*')
                ->order('l_user_id DESC')
                ->limit($Page->firstRow . ',' . $Page->listRows)
                ->fetchSql(false)
                ->select();
        }
        if ($student_list) {
            foreach ($student_list as $key => $value) {
                if ($value['s_school_name'] != '') {
                    $student_list[$key]['s_school_name'] = $value['s_school_name'];
                } else {
                    $student_list[$key]['s_school_name'] = '--';
                }

                if ($value['addtime'] != 0) {
                    $student_list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $student_list[$key]['addtime'] = '--';
                }
                if ($value['updatetime'] != 0) {
                    $student_list[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $student_list[$key]['updatetime'] = '--';
                }

                if ($value['uf.addtime'] != 0) {
                    $student_list[$key]['uf.addtime'] = date('Y-m-d H:i:s', $value['uf.addtime']);
                } else {
                    $student_list[$key]['uf.addtime'] = '--';
                }
                if ($value['uf.updatetime'] != 0) {
                    $student_list[$key]['uf.updatetime'] = date('Y-m-d H:i:s', $value['uf.updatetime']);
                } else {
                    $student_list[$key]['uf.updatetime'] = '--';
                }

                if ($value['address'] != '') {
                    $student_list[$key]['address'] = $value['address'];
                } else {
                    $student_list[$key]['address'] = '--';
                }

                if ($value['identity_id'] != '') {
                    $student_list[$key]['identity_id'] = $value['identity_id'];
                } else {
                    $student_list[$key]['identity_id'] = '--';
                }
            }
        }
        $student_lists = array('student_list' => $student_list, 'page' => $page, 'count' => $count);
        return $student_lists;
    }


// 3.在线模拟模块
    /**
     * 根据不同驾校(school_id)获得学员考试记录信息
     * @param   $school
     * @return  $records_list   返回的是user表与user_exam_records表中的数据和page(页面中页数的显示),count(条数)
     * @author  wl
     * @update  Nov 09, 2016
     * @update  Nov 30, 2016
     **/
    public function getExamRecords($school_id) {
        $records_lists  = array();
        if ($school_id == 0) {
            $count = $this->table(C('DB_PREFIX').'user_exam_records e')
                ->join(C('DB_PREFIX').'user u ON u.l_user_id = e.user_id','LEFT')
                ->where(array('i_status' => 0, 'i_user_type' => 0))
                ->count();
            $Page = new Page($count,10);
            $page = $this->getPage($count,10);
            $exam_records_list = $this->alias('e')
                ->join(C('DB_PREFIX').'user u ON u.l_user_id = e.user_id','LEFT')
                ->join(C('DB_PREFIX').'school s ON s.l_school_id = e.school_id','LEFT')
                ->field('e.*, e.addtime as add_time, u.*, u.addtime as user_add_time, s.l_school_id, s.s_school_name')
                ->where(array('i_status' => 0, 'i_user_type' => 0))
                ->order('id DESC')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->fetchSql(false)
                ->select();
        } else {
            $count = $this->table(C('DB_PREFIX').'user_exam_records e')
                ->join(C('DB_PREFIX').'user u ON u.l_user_id = e.user_id','LEFT')
                ->where("school_id = :sid")
                ->bind(['sid'=>$school_id])
                ->where(array('i_status' => 0, 'i_user_type' => 0))
                ->count();
            $Page = new Page($count,10);
            $page = $this->getPage($count,10);
            $exam_records_list = $this->alias('e')
                ->join(C('DB_PREFIX').'user u ON u.l_user_id = e.user_id','LEFT')
                ->join(C('DB_PREFIX').'school s ON s.l_school_id = e.school_id','LEFT')
                ->where('school_id = :sid')
                ->bind(['sid' => $school_id])
                ->where(array('i_status' => 0, 'i_user_type' => 0))
                ->field('e.*, e.addtime as add_time, u.*, u.addtime as user_add_time, s.l_school_id, s.s_school_name')
                ->order('id DESC')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->fetchSql(false)
                ->select();
        }
        if($exam_records_list) {
            foreach($exam_records_list as $key => $value) {
                // 考试的id
                $exam_records_list[$key]['id']= $value['id'];
                // 用户姓名或者登陆账号
                if($value['realname']) {
                    $exam_records_list[$key]['realname']= $value['realname'];
                } else {
                    $exam_records_list[$key]['realname']= $value['realname'] == '' ? $value['s_real_name'] : $value['s_username'];
                }
                // 用户的手机号码
                $exam_records_list[$key]['s_phone']= $value['phone_num'] == '' ? $value['s_phone'] : $value['phone_num'];
                // 用户的身份证号
                $exam_records_list[$key]['identify_id']= $value['identify_id'] == '' ? '--' : $value['identify_id'];
                // $list[$key]['identify_id']= $value['identify_id'];
                // 牌照
                $exam_records_list[$key]['ctype']= $value['ctype'];
                // 考试的科目
                if($value['stype'] == 1){
                    $exam_records_list[$key]['stype_name']= '科目一';
                } else if ($value['stype'] == 4) {
                    $exam_records_list[$key]['stype_name']= '科目四';
                }
                // 考试的总时间
                if($value['exam_total_time'] >= 60) {
                    $min = floor($value['exam_total_time'] / 60);
                    $sec = $value['exam_total_time'] - 60 * $min;
                    $exam_records_list[$key]['exam_total_time']= $min . '分' . $sec . '秒';
                } else {
                    $min_sec = $value['exam_total_time'];
                    $exam_records_list[$key]['exam_total_time']= $min_sec . '秒';
                }
                // 考试的分数
                $exam_records_list[$key]['score']= $value['score'];
                // 交卷的时间
                if ($value['add_time'] != 0) {
                    $exam_records_list[$key]['add_time']= date('Y-m-d H:i:s', $value['add_time']);
                } else {
                    $exam_records_list[$key]['add_time']= '';
                }
                if ($value['s_school_name'] != '') {
                    $exam_records_list[$key]['s_school_name']= $value['s_school_name'];
                } else {
                    $exam_records_list[$key]['s_school_name']= '--';
                }
            }
        }
        $records_lists = array('list' => $exam_records_list, 'page' => $page, 'count' => $count);
        return $records_lists;
    }

    /**
     * 根据学员姓名、手机号、身份证号来搜索学员
     * @param   $param        页面提交出来的数据
     * @return  $records_list 返回的要查询的信息
     * @author  wl
     * @update  Nov 09, 2016
     * @update  Nov 30, 2016
     **/
    public function searchRecords ($param, $school_id) {
        $map = array();
        $complex = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['s_keyword'] != '') {
            $complex['e.realname'] = array('LIKE', $s_keyword);
            $complex['e.phone_num'] = array('LIKE', $s_keyword);
            $complex['e.identify_id'] = array('LIKE', $s_keyword);
            $complex['u.s_real_name'] = array('LIKE', $s_keyword);
            $complex['u.s_username'] = array('LIKE', $s_keyword);
            $complex['u.s_phone'] = array('LIKE', $s_keyword);
            $complex['_logic'] = 'OR';
        }
        $map['_complex'] = $complex;
        $map['u.i_status'] = array('EQ', 0);
        $map['u.i_user_type'] = array('EQ', 0);
        $records_lists  = array();
        if ($school_id == 0) {
            $count = $this->table(C('DB_PREFIX').'user_exam_records e')
                ->join(C('DB_PREFIX').'user u ON u.l_user_id = e.user_id','LEFT')
                ->where($map)
                ->count();
            $Page = new Page($count,10, $param);
            $page = $this->getPage($count,10, $param);
            $exam_records_list = $this->alias('e')
                ->join(C('DB_PREFIX').'user u ON u.l_user_id = e.user_id','LEFT')
                ->join(C('DB_PREFIX').'school s ON s.l_school_id = e.school_id','LEFT')
                ->field('e.*, e.addtime as add_time, u.*, u.addtime as user_add_time, s.l_school_id, s.s_school_name')
                ->where($map)
                ->order('id DESC')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->fetchSql(false)
                ->select();
        } else {
            $count = $this->table(C('DB_PREFIX').'user_exam_records e')
                ->join(C('DB_PREFIX').'user u ON u.l_user_id = e.user_id','LEFT')
                ->where("school_id = :sid")
                ->bind(['sid'=>$school_id])
                ->where($map)
                ->count();
            $Page = new Page($count,10, $param);
            $page = $this->getPage($count,10, $param);
            $exam_records_list = $this->alias('e')
                ->join(C('DB_PREFIX').'user u ON u.l_user_id = e.user_id','LEFT')
                ->join(C('DB_PREFIX').'school s ON s.l_school_id = e.school_id','LEFT')
                ->where('school_id = :sid')
                ->bind(['sid' => $school_id])
                ->where($map)
                ->field('e.*, e.addtime as add_time, u.*, u.addtime as user_add_time, s.l_school_id, s.s_school_name')
                ->order('id DESC')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->fetchSql(false)
                ->select();
        }
        if($exam_records_list) {
            foreach($exam_records_list as $key => $value) {
                // 考试的id
                $exam_records_list[$key]['id']= $value['id'];
                // 用户姓名或者登陆账号
                if($value['realname']) {
                    $exam_records_list[$key]['realname']= $value['realname'];
                } else {
                    $exam_records_list[$key]['realname']= $value['realname'] == '' ? $value['s_real_name'] : $value['s_username'];
                }
                // 用户的手机号码
                $exam_records_list[$key]['s_phone']= $value['phone_num'] == '' ? $value['s_phone'] : $value['phone_num'];
                // 用户的身份证号
                $exam_records_list[$key]['identify_id']= $value['identify_id'] == '' ? '--' : $value['identify_id'];
                // $list[$key]['identify_id']= $value['identify_id'];
                // 牌照
                $exam_records_list[$key]['ctype']= $value['ctype'];
                // 考试的科目
                if($value['stype'] == 1){
                    $exam_records_list[$key]['stype_name']= '科目一';
                } else if ($value['stype'] == 4) {
                    $exam_records_list[$key]['stype_name']= '科目四';
                }
                // 考试的总时间
                if($value['exam_total_time'] >= 60) {
                    $min = floor($value['exam_total_time'] / 60);
                    $sec = $value['exam_total_time'] - 60 * $min;
                    $exam_records_list[$key]['exam_total_time']= $min . '分' . $sec . '秒';
                } else {
                    $min_sec = $value['exam_total_time'];
                    $exam_records_list[$key]['exam_total_time']= $min_sec . '秒';
                }
                // 考试的分数
                $exam_records_list[$key]['score']= $value['score'];
                // 交卷的时间
                if ($value['add_time'] != 0) {
                    $exam_records_list[$key]['add_time']= date('Y-m-d H:i:s', $value['add_time']);
                } else {
                    $exam_records_list[$key]['add_time']= '';
                }
                if ($value['s_school_name'] != '') {
                    $exam_records_list[$key]['s_school_name']= $value['s_school_name'];
                } else {
                    $exam_records_list[$key]['s_school_name']= '--';
                }
            }
        }
        $records_lists = array('list' => $exam_records_list, 'page' => $page, 'count' => $count);
        return $records_lists;
    }



}
?>
