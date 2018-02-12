<?php
namespace Admin\Model;
use Think\Model;
use Think\Page;

class CoachModel extends BaseModel {
    public $tableName = 'coach';
// 1.教练列表部分
    /**
     * 获取教练列表
     *
     * @return  void
     * @author  wl
     * @date    Sep 05, 2016
     * @date    Nov 26, 2016
     **/
    public function getCoachList ($school_id) {
        $map = array();
        $map['i_status'] = array('EQ', 0); // i_status = 0-正常 2-已删除
        $map['i_user_type'] = array('EQ', 1); // i_user_type = 0-学员 1-教练
        if ($school_id != 0) {
            $map['ca.s_school_name_id'] = $school_id;
        } else {
            if ('ca.s_school_name_id' != 0) {
                $map['s.is_show'] = 1; // 1:展示 2:不展示
                $map['s.l_school_id'] = array('gt', 0); 
            }
        }
        
        $count = $this->alias('ca')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = ca.s_school_name_id', 'LEFT')
            ->join(C('DB_PREFIX').'cars c ON c.id = ca.s_coach_car_id', 'LEFT')
            ->join(C('DB_PREFIX').'user u ON u.l_user_id = ca.user_id', 'LEFT')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        // 获取驾照表中的所有字段与值
        $license_configs = $this->getLicenseInfo();
        // 获取科目表中的所有字段与值
        $lesson_configs = $this->getLessonInfo();
        $coach_lists = array();
        $coach_list = $this->alias('ca')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = ca.s_school_name_id', 'LEFT')
            ->join(C('DB_PREFIX').'cars c ON c.id = ca.s_coach_car_id', 'LEFT')
            ->join(C('DB_PREFIX').'user u ON u.l_user_id = ca.user_id', 'LEFT')
            ->where($map)
            ->field('ca.*, s.l_school_id, s.s_school_name, c.name, c.id car_id, u.i_status, u.i_user_type')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('ca.l_coach_id DESC')
            ->fetchSql(false)
            ->select();
        
        if (!empty($coach_list)) {
            foreach ($coach_list as $key => $value) {
                $_coach_license_id = explode(',', $value['s_coach_lisence_id']);
                $_coach_lesson_id = explode(',', $value['s_coach_lesson_id']);
                $coach_lesson_ids = array();
                $coach_license_ids = array();
                if ($_coach_license_id && count($_coach_license_id) > 0) {
                    foreach ($_coach_license_id as $coach_license_index => $coach_license_id) {
                        if (array_key_exists($coach_license_id, $license_configs)) { // 或者isset也可以
                            $license_name = $license_configs[$coach_license_id]['license_name'];
                            $coach_license_ids[] = $license_name;
                        }
                    }
                }

                if ($_coach_lesson_id && count($_coach_lesson_id) > 0) {
                    foreach ($_coach_lesson_id as $coach_lesson_index => $coach_lesson_id) {
                        if (array_key_exists($coach_lesson_id, $lesson_configs)) {
                            $lesson_name = $lesson_configs[$coach_lesson_id]['lesson_name'];
                            $coach_lesson_ids[] = $lesson_name;
                        }
                    }
                }
                $coach_list[$key]['coach_license'] = implode(',', $coach_license_ids);
                $coach_list[$key]['coach_lesson'] = implode(',', $coach_lesson_ids);

                if ($value['s_coach_content'] == '') {
                    $coach_list[$key]['s_coach_content'] = '--';
                }

                if ($value['s_coach_address'] == '') {
                    $coach_list[$key]['s_coach_address'] = '--';
                }

                if ($value['s_teach_age'] == '') {
                    $coach_list[$key]['s_teach_age'] = '0';
                }

                if ($value['timetraining_supported'] == 0) {
                    $coach_list[$key]['timetraining_supported_value'] = '不支持';

                } elseif ($value['timetraining_supported'] == 1) {
                    $coach_list[$key]['timetraining_supported_value'] = '支持';

                }

                if ($value['certification_status'] == 1) {
                    $coach_list[$key]['certification_status_value'] = '未认证';

                } elseif ($value['certification_status'] == 2) {
                    $coach_list[$key]['certification_status_value'] = '认证中';

                } elseif ($value['certification_status'] == 3) {
                    $coach_list[$key]['certification_status_value'] = '已认证';

                } elseif ($value['certification_status'] == 4) {
                    $coach_list[$key]['certification_status_value'] = '认证失败';

                }

                if ($value['s_coach_sex'] == 1) {
                    $coach_list[$key]['coach_sex'] = '男';

                } elseif ($value['s_coach_sex'] == 0) {
                    $coach_list[$key]['coach_sex'] = '女';

                } else {
                    $coach_list[$key]['coach_sex'] = '男';

                }

                if ($value['name'] == '') {
                    $coach_list[$key]['name'] = '--';
                }

                if ($value['addtime'] != 0) {
                    $coach_list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $coach_list[$key]['addtime'] = '--';
                }
                if ($value['updatetime'] != 0) {
                    $coach_list[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $coach_list[$key]['updatetime'] = '--';
                }
                if ($value['s_coach_name'] == '') {
                    $coach_list[$key]['s_coach_name'] = '--';
                }
                if ($value['s_coach_phone'] == '') {
                    $coach_list[$key]['s_coach_phone'] = '--';
                }
                if ($value['s_school_name_id'] == 0) {
                    $coach_list[$key]['s_school_name'] = '嘻哈平台';
                }
                if ($value['s_coach_lesson_id'] == '') {
                    $coach_list[$key]['coach_lesson'] = '--';
                }
                if ($value['s_coach_lisence_id'] == '') {
                    $coach_list[$key]['coach_license'] = '--';
                }

                if ($value['lesson2_pass_rate'] == '') {
                    $coach_list[$key]['lesson2_pass_rate'] = '--';
                } else {
                    $empty = explode('%', $value['lesson2_pass_rate']);
                    if (count($empty) > 1) {
                        $value['lesson2_pass_rate'] = str_replace('%', '', $value['lesson2_pass_rate']);
                    }
                    $coach_list[$key]['lesson2_pass_rate'] = $value['lesson2_pass_rate'];
                }

                if ($value['lesson3_pass_rate'] == '') {
                    $coach_list[$key]['lesson3_pass_rate'] = '--';
                } else {
                    $empty = explode('%', $value['lesson3_pass_rate']);
                    if (count($empty) > 1) {
                        $value['lesson3_pass_rate'] = str_replace('%', '', $value['lesson3_pass_rate']);
                    }
                    $coach_list[$key]['lesson3_pass_rate'] = $value['lesson3_pass_rate'];
                }

                // if ($value['s_coach_lesson_id'] == 0) {
                //     $coach_list[$key]['coach_lesson'] = '---';
                // }
                
                // if ($value['s_coach_lisence_id'] == 0) {
                //     $coach_list[$key]['coach_license'] = '---';
                // }
                
            }

        }
        $coach_lists = array('coach_list' => $coach_list, 'page' => $page, 'count' => $count);
        return $coach_lists;
    }

    /**
     * 根据条件搜索教练的相关信息
     *
     * @return  void
     * @author  wl
     * @date    Sep 06, 2016
     **/
    public function searchCoach ($param, $school_id) {
        $map = array();
        $complex = array();
        $keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_type'] == '') {
            $complex['ca.l_coach_id'] = array('EQ', $param['s_keyword']);
            $complex['s_coach_name'] = array('LIKE', $keyword);
            $complex['s_coach_phone'] = array('LIKE', $keyword);
            $complex['s.s_school_name'] = array('LIKE', $keyword);
            $complex['_logic'] = 'OR';
        } else {
            if ($param['search_type'] == 'l_coach_id') {
                $complex[$param['search_type']] = array('EQ', $param['s_keyword']);
            }
            $complex[$param['search_type']] = array('LIKE', $keyword);
        }
        $map['_complex'] = $complex;

        // 删除状态
        if ($param['status'] != '') {
            $map['u.i_status'] = array('EQ', $param['status']);
        } else {
            $map['u.i_status'] = array('EQ', 0);
        }

        if ($param['search_star'] != '') {
            $map['i_coach_star'] = array('EQ', $param['search_star']);
        }

        if ($param['certification_status'] != 0) {
            $map['certification_status'] = array('EQ', $param['certification_status']);
        }

        $map['i_user_type'] = array('EQ', 1); // 0-学员 1-教练
        if ($school_id != 0) {
            $map['ca.s_school_name_id'] = $school_id;
        } else {
            if ('ca.s_school_name_id' != 0) {
                $map['s.is_show'] = 1; // 1:展示 2:不展示
                $map['s.l_school_id'] = array('gt', 0); 
            }
        }
        
        $count = $this->alias('ca')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = ca.s_school_name_id', 'LEFT')
            ->join(C('DB_PREFIX').'cars c ON c.id = ca.s_coach_car_id', 'LEFT')
            ->join(C('DB_PREFIX').'user u ON u.l_user_id = ca.user_id', 'LEFT')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        // 获取驾照表中的所有字段与值
        $license_configs = $this->getLicenseInfo();
        // 获取科目表中的所有字段与值
        $lesson_configs = $this->getLessonInfo();
        $coach_lists = array();
        $coach_list = $this->alias('ca')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = ca.s_school_name_id', 'LEFT')
            ->join(C('DB_PREFIX').'cars c ON c.id = ca.s_coach_car_id', 'LEFT')
            ->join(C('DB_PREFIX').'user u ON u.l_user_id = ca.user_id', 'LEFT')
            ->where($map)
            ->field('ca.*, s.l_school_id, s.s_school_name, c.name, c.id car_id, u.i_status, u.i_user_type')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('ca.l_coach_id DESC')
            ->fetchSql(false)
            ->select();
        if (!empty($coach_list)) {
            foreach ($coach_list as $key => $value) {
                $_coach_license_id = explode(',', $value['s_coach_lisence_id']);
                $_coach_lesson_id = explode(',', $value['s_coach_lesson_id']);
                $coach_lesson_ids = array();
                $coach_license_ids = array();
                if ($_coach_license_id && count($_coach_license_id) > 0) {
                    foreach ($_coach_license_id as $coach_license_index => $coach_license_id) {
                        if (array_key_exists($coach_license_id, $license_configs)) { // 或者isset也可以
                            $license_name = $license_configs[$coach_license_id]['license_name'];
                            $coach_license_ids[] = $license_name;
                        }
                    }
                }

                if ($_coach_lesson_id && count($_coach_lesson_id) > 0) {
                    foreach ($_coach_lesson_id as $coach_lesson_index => $coach_lesson_id) {
                        if (array_key_exists($coach_lesson_id, $lesson_configs)) {
                            $lesson_name = $lesson_configs[$coach_lesson_id]['lesson_name'];
                            $coach_lesson_ids[] = $lesson_name;
                        }
                    }
                }
                $coach_list[$key]['coach_license'] = implode(',', $coach_license_ids);
                $coach_list[$key]['coach_lesson'] = implode(',', $coach_lesson_ids);

                if ($value['s_coach_content'] == '') {
                    $coach_list[$key]['s_coach_content'] = '--';
                }

                if ($value['s_coach_address'] == '') {
                    $coach_list[$key]['s_coach_address'] = '--';
                }

                if ($value['s_teach_age'] == '') {
                    $coach_list[$key]['s_teach_age'] = '0';
                }

                if ($value['timetraining_supported'] == 0) {
                    $coach_list[$key]['timetraining_supported_value'] = '不支持';

                } elseif ($value['timetraining_supported'] == 1) {
                    $coach_list[$key]['timetraining_supported_value'] = '支持';

                }

                if ($value['certification_status'] == 1) {
                    $coach_list[$key]['certification_status_value'] = '未认证';

                } elseif ($value['certification_status'] == 2) {
                    $coach_list[$key]['certification_status_value'] = '认证中';

                } elseif ($value['certification_status'] == 3) {
                    $coach_list[$key]['certification_status_value'] = '已认证';

                } elseif ($value['certification_status'] == 4) {
                    $coach_list[$key]['certification_status_value'] = '认证失败';

                }

                if ($value['s_coach_sex'] == 1) {
                    $coach_list[$key]['coach_sex'] = '男';

                } elseif ($value['s_coach_sex'] == 2) {
                    $coach_list[$key]['coach_sex'] = '女';

                } else {
                    $coach_list[$key]['coach_sex'] = '男';

                }

                if ($value['name'] == '') {
                    $coach_list[$key]['name'] = '--';
                }

                if ($value['addtime'] != 0) {
                    $coach_list[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $coach_list[$key]['addtime'] = '--';
                }
                if ($value['updatetime'] != 0) {
                    $coach_list[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $coach_list[$key]['updatetime'] = '--';
                }
                if ($value['s_coach_name'] == '') {
                    $coach_list[$key]['s_coach_name'] = '--';
                }
                if ($value['s_coach_phone'] == '') {
                    $coach_list[$key]['s_coach_phone'] = '--';
                }
                if ($value['s_school_name_id'] == 0) {
                    $coach_list[$key]['s_school_name'] = '嘻哈平台';
                }

                if ($value['s_coach_lesson_id'] == '') {
                    $coach_list[$key]['coach_lesson'] = '--';
                }
                if ($value['s_coach_lisence_id'] == '') {
                    $coach_list[$key]['coach_license'] = '--';
                }

                if ($value['lesson2_pass_rate'] == '') {
                    $coach_list[$key]['lesson2_pass_rate'] = '--';
                } else {
                    $empty = explode('%', $value['lesson2_pass_rate']);
                    if (count($empty) > 1) {
                        $value['lesson2_pass_rate'] = str_replace('%', '', $value['lesson2_pass_rate']);
                    }
                    $coach_list[$key]['lesson2_pass_rate'] = $value['lesson2_pass_rate'];
                }

                if ($value['lesson3_pass_rate'] == '') {
                    $coach_list[$key]['lesson3_pass_rate'] = '--';
                } else {
                    $empty = explode('%', $value['lesson3_pass_rate']);
                    if (count($empty) > 1) {
                        $value['lesson3_pass_rate'] = str_replace('%', '', $value['lesson3_pass_rate']);
                    }
                    $coach_list[$key]['lesson3_pass_rate'] = $value['lesson3_pass_rate'];
                }

                // if ($value['s_coach_lesson_id'] == 0) {
                //     $coach_list[$key]['coach_lesson'] = '---';
                // }

                // if ($value['s_coach_lisence_id'] == 0) {
                //     $coach_list[$key]['coach_license'] = '---';
                // }
            }

        }
        $coach_lists = array('coach_list' => $coach_list, 'page' => $page, 'count' => $count);
        return $coach_lists;
    }

    /**
     * 设置教练与学员的绑定状态
     *
     * @return  void
     * @author  wl
     * @date    Jan 04, 2017
     **/
    public function setCoachMustBind ($id, $status) {
        if (!$id && !$status) {
            return false;
        }
        $data = array('must_bind' => $status);
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

    /**
     * 设置教练的排序状态
     *
     * @return  void
     * @author  wl
     * @date    Dec 09, 2016
     **/
    public function updateCoachOrder ($post) {
        if (empty($post)) {
            return 101; // 参数错误
        }
        $id = $post['id'];
        $order = $post['i_order'];
        if (isset($order)) {
            if (!is_numeric($order)) {
                return 102; // 参数的类型错误
            } else {
                $old_num = $this->table(C('DB_PREFIX').'coach')
                    ->where(array('l_coach_id' => $id))
                    ->getField('i_order');
                if ($order === $old_num) {
                    return 105; // 未做任何修改
                }
            } 
        }

        $coach = D('coach');
        $data = array('i_order' => $order);
        if ($res = $coach->create($data)) {
            $result = $coach->where(array('l_coach_id' => $id))->save($res);
            if ($result) {
                return 200;
            } else {
                return 400;
            }
        }
    }

    /**
     * 设置教练是否支持优惠券的状态
     *
     * @return  void
     * @author  wl
     * @date    Dec 09, 2016
     **/
    public function setSupportStatus ($id, $status) {
        if (!is_numeric($id) && !is_numeric($status)) {
            return false;
        }
        $list = array();
        $data = array('coupon_supported' => $status);
        $result = M('coach')->where(array('l_coach_id' => $id))
            ->data($data)
            ->fetchSql(false)
            ->save();
        $list['id'] = $id;
        $list['res'] = $result;
        return $list;
    }

    /**
     * 设置教练是否支持计时培训的状态
     *
     * @return  void
     * @author  wl
     * @date    Jan 10, 2017
     **/
    public function setTrainingSupport ($id, $status) {
        if (!is_numeric($id) && !is_numeric($status)) {
            return false;
        }
        $list = array();
        $data = array('timetraining_supported' => $status);
        $result = M('coach')->where(array('l_coach_id' => $id))
            ->data($data)
            ->fetchSql(false)
            ->save();
        $list['id'] = $id;
        $list['res'] = $result;
        return $list;
    }

    /**
     * 设置教练的删除状态
     *
     * @return  void
     * @author  wl
     * @date    Nov 28, 2016
     **/
    public function setDelCoachStatus ($id, $status) {
        if (!is_numeric($id) && !$status) {
            return false;
        }
        $list = array();
        $data = array('i_status' => $status);
        $user_id = $this->table(C('DB_PREFIX').'coach')
            ->where(array('l_coach_id' => $id))
            ->getField('user_id');
        $result = M('user u')
            ->where(array('l_user_id' => $user_id))
            ->fetchSql(false)
            ->data($data)
            ->save();
        $list['i_status'] = $result;
        $list['l_coach_id'] = $id;
        if (!empty($list)) {
            return $list;
        } else {
            return array();
        }
    }

    /**
     * 根据驾校id和教练的name来判断此教练是否存在
     *
     * @return  void
     * @author  wl
     * @date    Nov 14, 2016
     **/
    public function checkCoachInfo ($phone) {
        if (!is_numeric($phone)) {
            return false;
        }

        $check_coach_info = $this->table(C('DB_PREFIX').'user user')
            ->join(C('DB_PREFIX').'coach coach on user.l_user_id = coach.user_id', 'LEFT')
            ->where(
                array(
                    's_coach_phone' => $phone,
                    'i_user_type' => 1,
                    'i_status' => 0,
                )
            )
            ->fetchSql(false)
            ->find();
        if (!empty($check_coach_info)) {
            return $check_coach_info;
        } else {
            return array();
        }
    }

    /**
     * 设置教练的热门状态
     *
     * @return  void
     * @author  wl
     * @date    Dec 21, 2016
     **/
    public function setCoachHotStatus ($id, $status) {
        if (!$id && !$status) {
            return false;
        } 
        $list = array();
        $data = array('is_hot' => $status);
        $result = M('Coach')->where('l_coach_id = :cid')
            ->bind(['cid' => $id])
            ->data($data)
            ->fetchSql(false)
            ->save();
        $list['id'] = $id;
        $list['is_hot'] = $result;
        return $list;
    }

    /**
     * 设置教练的在线状态
     *
     * @return  void
     * @author  wl
     * @date    Sep 06, 2016
     **/
    public function setCoachStatus ($id, $status) {
        if (!$id) {
            return false;
        } 
        $list = array();
        $data = array('order_receive_status' => $status);
        $result = M('Coach')->where('l_coach_id = :cid')
            ->bind(['cid' => $id])
            ->data($data)
            ->fetchSql(false)
            ->save();
        $list['id'] = $id;
        $list['order_receive_status'] = $result;
        return $list;
    }

    /**
     * 删除教练(1、逻辑删除只是改变其订单状态；2、真正删除)
     *
     * @return  void
     * @author  wl
     * @date    Sep 06, 2016
     **/
    public function delCoach ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $uid = $this->table(C('DB_PREFIX').'coach')->where('l_coach_id = :cid')
            ->bind(['cid' => $id])
            ->field('user_id')
            ->fetchSql(false)
            ->find();
        $user_id = '';
        foreach ($uid as $key => $value) {
            $user_id = $value;
        }
        $result = M('Coach')->where('l_coach_id = :cid')
            ->bind(['cid' => $id])
            ->fetchSql(false)
            ->save(array('order_receive_status' => 0));
            // ->delete();
        $res = M('user')->where('l_user_id = :uid')
            ->bind(['uid' => $user_id])
            ->fetchSql(false)
            ->save(array('i_status' => 2));
        if ($res) {
            return $res;                    
        }
    }

    /**
     * 获取id对应的教练信息（预览）
     *
     * @return  void
     * @author  wl  
     * @date    Dec 05, 2016  
     **/
    public function showCoachInfoById ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $coach_list = $this->table(C('DB_PREFIX').'coach ca')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = ca.s_school_name_id', 'LEFT')
            ->join(C('DB_PREFIX').'cars c ON c.id = ca.s_coach_car_id', 'LEFT')
            ->join(C('DB_PREFIX').'province p ON p.provinceid = ca.province_id', 'LEFT')
            ->join(C('DB_PREFIX').'area a ON a.areaid = ca.area_id', 'LEFT')
            ->join(C('DB_PREFIX').'city ct ON ct.cityid = ca.city_id', 'LEFT')
            ->where('l_coach_id = :cid')
            ->bind(['cid' => $id])
            ->field('ca.*, s.l_school_id, s.s_school_name, c.name, c.id car_id, c.car_no, p.*, a.*, ct.*')
            ->find();
        // 获取驾照表中的所有字段与值
        $license_configs = $this->getLicenseInfo();
        // 获取科目表中的所有字段与值
        $lesson_configs = $this->getLessonInfo();
        if ($coach_list) {
            $_coach_license_id = isset($coach_list['s_coach_lisence_id']) ? explode(',', $coach_list['s_coach_lisence_id']) : array();
            $_coach_lesson_id = isset($coach_list['s_coach_lesson_id']) ? explode(',', $coach_list['s_coach_lesson_id']) : array();
            $coach_lesson_ids = array();
            $coach_license_ids = array();
            if ($_coach_license_id && count($_coach_license_id) > 0) {
                foreach ($_coach_license_id as $coach_license_index => $coach_license_id) {
                    if (array_key_exists($coach_license_id, $license_configs)) { // 或者isset也可以
                        $license_name = $license_configs[$coach_license_id]['license_name'];
                        $coach_license_ids[] = $license_name;
                    }
                }
            }

            if ($_coach_lesson_id && count($_coach_lesson_id) > 0) {
                foreach ($_coach_lesson_id as $coach_lesson_index => $coach_lesson_id) {
                    if (array_key_exists($coach_lesson_id, $lesson_configs)) {
                        $lesson_name = $lesson_configs[$coach_lesson_id]['lesson_name'];
                        $coach_lesson_ids[] = $lesson_name;
                    }
                }
            }

            $coach_list['coach_license'] = implode('，', $coach_license_ids);
            $coach_list['coach_lesson'] = implode('，', $coach_lesson_ids);

            if ($coach_list['s_coach_content'] == '') {
                $coach_list['s_coach_content'] = '暂未填写';
            }

            if ($coach_list['s_yh_name'] == '') {
                $coach_list['s_yh_name'] = '暂未填写';
            }
            if ($coach_list['s_yh_zhanghao'] == '') {
                $coach_list['s_yh_zhanghao'] = '暂未填写';
            }

            if ($coach_list['timetraining_supported'] == 0) {
                $coach_list['timetraining_supported_value'] = '不支持';

            } elseif ($coach_list['timetraining_supported'] == 1) {
                $coach_list['timetraining_supported_value'] = '支持';

            }

            if ($coach_list['certification_status'] == 1) {
                $coach_list['certification_status_value'] = '未认证';

            } elseif ($coach_list['certification_status'] == 2) {
                $coach_list['certification_status_value'] = '认证中';

            } elseif ($coach_list['certification_status'] == 3) {
                $coach_list['certification_status_value'] = '已认证';

            } elseif ($coach_list['certification_status'] == 4) {
                $coach_list['certification_status_value'] = '认证失败';

            }
            
            if ($coach_list['s_coach_sex'] == 1) {
                $coach_list['coach_sex'] = '男';

            } elseif ($coach_list['s_coach_sex'] == 2) {
                $coach_list['coach_sex'] = '女';

            } else {
                $coach_list['coach_sex'] = '男';

            }

            if ($coach_list['s_teach_age'] == '') {
                $coach_list['s_teach_age'] = 0;
            }

            if ($coach_list['province'] == null) {
                $coach_list['province'] = '';
            }

            if ($coach_list['city'] == null) {
                $coach_list['city'] = '';
            }

            if ($coach_list['area'] == null) {
                $coach_list['area'] = '';
            }

            if ($coach_list['i_type'] == 0) {
                $coach_list['type_name'] = '普通教练';

            } elseif ($coach_list['i_type'] == 1) {
                $coach_list['type_name'] = '金牌教练';

            } elseif ($coach_list['i_type'] == 2) {
                $coach_list['type_name'] = '二级教练';

            } elseif ($coach_list['i_type'] == 3) {
                $coach_list['type_name'] = '三级教练';

            } elseif ($coach_list['i_type'] == 4) {
                $coach_list['type_name'] = '四级教练';

            } elseif ($coach_list['i_type'] == 5) {
                $coach_list['type_name'] = '二级教练,全国优秀教练';

            } elseif ($coach_list['i_type'] == 6) {
                $coach_list['type_name'] = '三级教练,全国优秀教练';

            } 

            if (file_exists($coach_list['s_coach_imgurl']) && $coach_list['s_coach_imgurl'] != '') {
                $coach_list['s_coach_imgurl'] = C('HTTP_HOST').$coach_list['s_coach_imgurl'];

            } else {
                $coach_list['s_coach_imgurl'] = '';

            }

            if ($coach_list['addtime'] != 0) {
                $coach_list['addtime'] = date('Y-m-d H:i:s', $coach_list['addtime']);

            } else {
                $coach_list['addtime'] = '';
            }

            if ($coach_list['updatetime'] != 0) {
                $coach_list['updatetime'] = date('Y-m-d H:i:s', $coach_list['updatetime']);

            } else {
                $coach_list['updatetime'] = '';
            }

            if ($coach_list['s_coach_name'] == '') {
                $coach_list['s_coach_name'] = '暂无';
            }

            if ($coach_list['s_coach_phone'] == '') {
                $coach_list['s_coach_phone'] = '暂无';
            }

            if ($coach_list['s_school_name_id'] == 0) {
                $coach_list['s_school_name'] = '嘻哈平台';
            }

        }
        return $coach_list;
    }

    /**
     * 获取该教练预约学车的信息
     *
     * @return  void
     * @author  wl
     * @date    Dec 06, 2016
     **/
    public function getAppointList ($coach_id) {
        if (!is_numeric($coach_id)) {
            return false;
        }
        $count = $this->table(C('DB_PREFIX').'study_orders study')
            ->where(array('l_coach_id' => $coach_id, 'i_status' => array('neq', 101)))
            // ->where('l_coach_id = :coach_id AND i_status != 1')
            // ->bind(['coach_id' => $coach_id])
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 5);
        $page = $this->getPage($count, 5);
        $appoint_list = $this->table(C('DB_PREFIX').'study_orders study')
            ->where(array('l_coach_id' => $coach_id, 'i_status' => array('neq', 101)))
            // ->where('l_coach_id = :coach_id AND i_status != 1')
            // ->bind(['coach_id' => $coach_id])
            ->order('l_study_order_id DESC')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        if (!empty($appoint_list)) {
            foreach ($appoint_list as $key => $value) {
                // 根据预约的用户id获取用户表中的相关信息
                $user_id = $value['l_user_id'];
                $user_info = $this->table(C('DB_PREFIX').'user user')
                    ->join(C('DB_PREFIX').'users_info users On users.user_id = user.l_user_id', 'LEFT')
                    ->where(array('l_user_id' => $user_id, 'i_user_type' => array('neq', 1), 'i_status' => 0))
                    ->field('s_username, s_real_name, user.s_phone')
                    ->find();
                if (!empty($user_info)) {
                    if ($value['s_user_name'] != '') {
                        $appoint_list[$key]['user_name'] = $value['s_user_name'];
                    } else {
                        if ($user_info['s_real_name'] != '') {
                            $appoint_list[$key]['user_name'] = $user_info['s_real_name'];
                        } elseif ($user_info['s_username'] != '') {
                            $appoint_list[$key]['user_name'] = $user_info['s_username'];
                        } else {
                            $appoint_list[$key]['user_name'] = '嘻哈用户'.substr($value['s_user_phone'], -4);
                        }
                    }
                } else {
                    $appoint_list[$key]['user_name'] = '嘻哈用户'.substr($value['s_user_phone'], -4);
                }

                // 获取教练的被预约的时间
                $appoint_time_id = $value['appoint_time_id'];
                $coach_appoint_time = $this->table(C('DB_PREFIX').'coach_appoint_time ')
                    ->where('id = :pid')
                    ->bind(['pid' => $appoint_time_id])
                    ->find();
                $time_config_id_arr = array();
                if (!empty($coach_appoint_time)) {
                    $time_config_id_arr = array_filter(explode(',', $coach_appoint_time['time_config_id']));
                    $coach_time = array();
                    if (!empty($time_config_id_arr)) {
                        $coach_time_config = $this->table(C('DB_PREFIX').'coach_time_config')
                            ->where(array('id' => array('in', $time_config_id_arr)))
                            ->select();
                        if (!empty($coach_time_config)) {
                            foreach ($coach_time_config as $coach_time_config_key => $coach_time_config_value) {
                                $coach_time[] = $coach_time_config_value['start_time'].':'.'00-'.$coach_time_config_value['end_time'].':00';
                            }
                        }
                        $appoint_list[$key]['coach_appoint_time'] = implode(', ', $coach_time);
                        $appoint_list[$key]['coach_appoint_date'] = date('Y-m-d', strtotime($value['dt_appoint_time']));
                        $appoint_list[$key]['coach_appoint_list'] = date('Y-m-d', strtotime($value['dt_appoint_time'])).' '.implode(', ', $coach_time);
                    } else {
                        $appoint_list[$key]['coach_appoint_date'] = date('Y-m-d', strtotime($value['dt_appoint_time']));
                        $appoint_list[$key]['coach_appoint_list'] = date('Y-m-d', strtotime($value['dt_appoint_time']));
                    }
                }
                if ($value['dt_order_time'] != 0) {
                    $appoint_list[$key]['dt_order_time'] = date('Y-m-d H:i:s', $value['dt_order_time']);
                } else {
                    $appoint_list[$key]['dt_order_time'] = '';
                }

            }
        }
        return $appoint_list;
    }

    /**
     * 根据教练id获取报名驾校的相关信息
     *
     * @return  void
     * @author  wl
     * @date    Dec 06， 2016
     **/
    public function getSignUpList ($coach_id) {
        if (!is_numeric($coach_id)) {
            return false;
        }
        $map = array();
        $map['so_order_status'] = array('neq', 101);
        $map['so_coach_id'] = $coach_id;
        $map['user.i_user_type'] = array('neq', 1);
        $map['user.i_status'] = array('eq', 0);
        $count = $this->table(C('DB_PREFIX').'school_orders orders')
            ->join(C('DB_PREFIX').'user user ON user.l_user_id = orders.so_user_id', 'LEFT')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = orders.so_school_id', 'LEFT')
            ->join(C('DB_PREFIX').'coach_shifts shifts ON shifts.id = orders.so_shifts_id', 'LEFT')
            ->where($map)
            ->where(array('so_coach_id' => $coach_id))
            ->count();
        $Page = new Page($count, 5);
        $signuplists = array();
        $signuplist = $this->table(C('DB_PREFIX').'school_orders orders')
            ->join(C('DB_PREFIX').'user user ON user.l_user_id = orders.so_user_id', 'LEFT')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = orders.so_school_id', 'LEFT')
            ->join(C('DB_PREFIX').'school_shifts shifts ON shifts.id = orders.so_shifts_id', 'LEFT')
            ->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->field('orders.*, user.s_username, user.s_real_name, user.s_phone, s.s_school_name, shifts.sh_title')
            ->order('orders.id DESC')
            ->select();
        if (!empty($signuplist)) {
            foreach ($signuplist as $key => $value) {
                if ($value['addtime'] != 0) {
                    $signuplist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $signuplist[$key]['addtime'] = '';
                }
                if ($value['so_username'] != '') {
                    $signuplist[$key]['user_name'] = $value['so_username'];
                } elseif ($value['s_real_name'] != '') {
                    $signuplist[$key]['user_name'] = $value['s_real_name'];
                } elseif ($value['s_username'] != '') {
                    $signuplist[$key]['user_name'] = $value['s_username'];
                } else {
                    if ($value['s_phone'] != '') {
                        $signuplist[$key]['user_name'] = '嘻哈用户'.substr($value['s_phone'], -4);
                    } else {
                        $signuplist[$key]['user_name'] = '嘻哈用户';
                    }
                }

                if ($value['s_school_name'] != '') {
                    $signuplist[$key]['s_school_name'] = $value['s_school_name'];
                } else {
                    $signuplist[$key]['s_school_name'] = '嘻哈平台';
                }

                if ($value['sh_title'] != '') {
                    $signuplist[$key]['sh_title'] = $value['sh_title'];
                } else {
                    $signuplist[$key]['sh_title'] = '普通班';
                }
            }
        }
        return $signuplist;
    }
    /**
     * 通过教练id获取学员评价教练的相关信息
     *
     * @return  void
     * @author  wl
     * @date    Dec 06, 2016
     **/
    public function getCommentList ($coach_id) {
        if (!is_numeric($coach_id)) {
            return false;
        }
        // 获取驾照表中的所有字段与值
        $license_configs = $this->getLicenseInfo();
        // 获取科目表中的所有字段与值
        $lesson_configs = $this->getLessonInfo();
        $count = $this->table(C('DB_PREFIX').'coach_comment comment')
            ->join(C('DB_PREFIX').'coach c ON c.l_coach_id = comment.coach_id', 'LEFT')
            ->join(C('DB_PREFIX').'user user ON user.l_user_id = comment.user_id', 'LEFT')
            ->where(array('comment.coach_id' => $coach_id, 'user.i_user_type' => 0, 'i_status' => 0))
            ->count();
        $Page = new Page($count, 5);
        $commentlist = $this->table(C('DB_PREFIX').'coach_comment comment')
            ->join(C('DB_PREFIX').'coach c ON c.l_coach_id = comment.coach_id', 'LEFT')
            ->join(C('DB_PREFIX').'user user ON user.l_user_id = comment.user_id', 'LEFT')
            ->where(array('comment.coach_id' => $coach_id, 'user.i_user_type' => 0, 'i_status' => 0))
            ->field('id, comment.addtime, comment.coach_id, comment.user_id, comment.type, comment.order_no, user.s_username, user.s_real_name, user.s_phone, c.s_coach_name, c.s_coach_phone')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('comment.id DESC')
            ->select();
        if (!empty($commentlist)) {
            foreach ($commentlist as $key => $value) {

                if ($value['addtime'] != 0) {
                    $commentlist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $commentlist[$key]['addtime'] = '';
                }

                if ($value['s_real_name'] != '') {
                    $commentlist[$key]['user_name'] = $value['s_real_name'];
                } elseif ($value['s_username'] != '') {
                    $commentlist[$key]['user_name'] = $value['s_username'];
                } else {
                    if ($value['s_phone'] != '') {
                        $commentlist[$key]['user_name'] = '嘻哈用户'.substr($value['s_phone'], -4);
                    } else {
                        $commentlist[$key]['user_name'] = '嘻哈用户';
                    }
                }
                if ($value['order_no'] != '') {
                    $order_no = $value['order_no'];
                    // 报名驾校
                    $school_orders = $this->table(C('DB_PREFIX').'school_orders orders')
                        ->join(C('DB_PREFIX').'school_shifts shifts ON shifts.id = orders.so_shifts_id', 'LEFT')
                        ->where(array('so_order_status' => 1, 'orders.so_order_no' => $order_no, 'so_coach_id' => $coach_id))
                        ->fetchSql(false)
                        ->getField('shifts.sh_title');
                    if ($school_orders != '') {
                        $commentlist[$key]['coment_extra_info'] = '设置的'.$school_orders;
                    } else {
                        // $commentlist[$key]['coment_extra_info'] = '';
                        // 预约学车
                        $study_orders = $this->table(C('DB_PREFIX').'study_orders orders')
                            ->where(array('s_order_no' => $order_no, 'l_coach_id' => $coach_id, 'i_status' => 2 ))
                            ->field('orders.*')
                            ->find();
                        if (!empty($study_orders)) {
                            $appoint_time_id = $study_orders['appoint_time_id'];
                            $coach_appoint_time = $this->table(C('DB_PREFIX').'coach_appoint_time ')
                                ->where(array('id' =>  $appoint_time_id))
                                ->find();
                            $time_config_id = array();
                            if (!empty($coach_appoint_time)) {
                                $time_config_id = array_filter(explode(',', $coach_appoint_time['time_config_id']));
                                if (!empty($time_config_id)) {
                                    $time_confing_ids = $this->table(C('DB_PREFIX').'coach_time_config')
                                        ->where(array('id' => array('in', $time_config_id))) 
                                        ->select();
                                     if (!empty($time_confing_ids)) {
                                        foreach ($time_confing_ids as $coach_time_config_key => $coach_time_config_value) {
                                            $coach_time[] = $coach_time_config_value['start_time'].':'.'00-'.$coach_time_config_value['end_time'].':00';
                                        }
                                    }
                                    if ($value['dt_appoint_time'] != 0) {
                                        $commentlist[$key]['coach_appoint_time'] = implode(', ', $coach_time);
                                        $commentlist[$key]['coach_appoint_date'] = date('Y-m-d', strtotime($value['dt_appoint_time']));
                                        $commentlist[$key]['coment_extra_info'] = date('Y-m-d', strtotime($value['dt_appoint_time'])).' '.implode(', ', $coach_time).'的时间段';
                                    } else {
                                        $commentlist[$key]['coach_appoint_time'] = implode(', ', $coach_time);
                                        $commentlist[$key]['coach_appoint_date'] = '';
                                        $commentlist[$key]['coment_extra_info'] = implode(', ', $coach_time).'的时间段';

                                    }
                                } else {
                                    if ($value['dt_appoint_time'] != 0) {
                                        $commentlist[$key]['coach_appoint_date'] = date('Y-m-d', strtotime($value['dt_appoint_time']));
                                        $commentlist[$key]['coment_extra_info'] = date('Y-m-d', strtotime($value['dt_appoint_time']));
                                    } else {
                                        $commentlist[$key]['coach_appoint_date'] = '';
                                        $commentlist[$key]['coment_extra_info'] = '';
                                    }
                                }
                            }
                        } else {
                            $commentlist[$key]['coach_appoint_time'] = '';
                            $commentlist[$key]['coach_appoint_date'] = '';
                            $commentlist[$key]['coment_extra_info'] = '';
                        }
                    }
                } 
            }
        }
        return $commentlist;
    }

    /**
     * 根据教练id获得单条教练信息
     *
     * @return  void
     * @author  wl
     * @date    Sep 08, 2016
     **/
    public function getCoachInfoById ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $coach_list = $this->table(C('DB_PREFIX').'coach ca')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = ca.s_school_name_id', 'LEFT')
            ->join(C('DB_PREFIX').'cars c ON c.id = ca.s_coach_car_id', 'LEFT')
            ->join(C('DB_PREFIX').'province p ON p.provinceid = ca.province_id', 'LEFT')
            ->join(C('DB_PREFIX').'area a ON a.areaid = ca.area_id', 'LEFT')
            ->join(C('DB_PREFIX').'city ct ON ct.cityid = ca.city_id', 'LEFT')
            ->where('l_coach_id = :cid')
            ->bind(['cid' => $id])
            ->field('ca.*, s.l_school_id, s.s_school_name, c.name, c.id car_id, c.car_no, p.*, a.*, ct.*')
            ->find();
        // 获取驾照表中的所有字段与值
        $license_configs = $this->getLicenseInfo();
        // 获取科目表中的所有字段与值
        $lesson_configs = $this->productLesson();
        if ($coach_list) {
            $_coach_license_id = isset($coach_list['s_coach_lisence_id']) ? explode(',', $coach_list['s_coach_lisence_id']) : array();
            $_coach_lesson_id = isset($coach_list['s_coach_lesson_id']) ? explode(',', $coach_list['s_coach_lesson_id']) : array();
            $license_list = array();
            $lesson_list = array();
            // 查看对应教练的牌照是否存在于牌照表中
            foreach ($license_configs as $key => $value) {
                $license_list[$key]['coach_license_name'] = $value['license_name'];
                $license_list[$key]['coach_license_id'] = $value['license_id'];
                if (in_array($key, $_coach_license_id)) {
                    $license_list[$key]['is_key'] = 1;
                } else {
                    $license_list[$key]['is_key'] = 2;
                }
            }
            // 查看对应教练的科目是否存在于科目表中
            foreach ($lesson_configs as $key => $value) {
                $lesson_list[$key]['coach_lesson_name'] = $value['lesson_name'];
                $lesson_list[$key]['coach_lesson_id'] = $value['lesson_id'];
                if (in_array($key, $_coach_lesson_id)) {
                    $lesson_list[$key]['is_key'] = 1;
                } else {
                    $lesson_list[$key]['is_key'] = 2;
                }
            }

            $coach_list['coach_license'] = $license_list;
            $coach_list['coach_lesson'] = $lesson_list;
            
            if ($coach_list['addtime'] != 0) {
                $coach_list['addtime'] = date('Y-m-d H:i:s', $coach_list['addtime']);
            } else {
                $coach_list['addtime'] = '';
            }
            if ($coach_list['updatetime'] != 0) {
                $coach_list['updatetime'] = date('Y-m-d H:i:s', $coach_list['updatetime']);
            } else {
                $coach_list['updatetime'] = '';
            }

            if ($coach_list['s_coach_name'] == '') {
                $coach_list['s_coach_name'] = '暂无';
            }
            if ($coach_list['s_coach_phone'] == '') {
                $coach_list['s_coach_phone'] = '暂无';
            }
            if ($coach_list['s_school_name_id'] == 0) {
                $coach_list['s_school_name'] = '嘻哈平台';
            }

            $coach_list['coach_imgurl'] = $this->buildUrl($coach_list['s_coach_imgurl']);
        }
        return $coach_list;
    }
    /**
     * 获取预约计时的评价星级
     *
     * @return  void
     * @author  wl
     * @date    Dec 07, 2016
     **/
    public function getStudyCommentInfo ($coach_id) {
        if (!$coach_id) {
            return false;
        }
        // 预约及时评价信息
        $star_info = array();
        $comment_stars = array();
        $count = $this->table(C('DB_PREFIX').'coach_comment comment')
            ->where(array('coach_id' => $coach_id, 'type' => 1, 'coach_star'))
            ->count();
        $comment_info = $this->table(C('DB_PREFIX').'coach_comment comment')
            // ->join(C('DB_PREFIX').'school_orders orders ON orders.so_order_no = comment.order_no')
            ->where(array('coach_id' => $coach_id, 'type' => 1))
            ->field('coach_star')
            ->select();
        if (!empty($comment_info)) {
            foreach ($comment_info as $key => $value) {
                foreach ($value as $k => $v) {
                    $comment_stars[$key] = $v;
                }
            }
            if (!empty($comment_stars)) {
                $star_info['star_sum'] = number_format(array_sum($comment_stars), 1);
                $star_info['star_avg'] = number_format(array_sum($comment_stars)/$count, 1);
                if ($star_info['star_avg'] < 3 && $star_info['star_avg'] >= 0) {
                    $star_info['star_content'] = '差评';
                } else if ($star_info['star_avg'] < 4 && $star_info['star_avg'] >= 3) {
                    $star_info['star_content'] = '中评';
                } else if ($star_info['star_avg'] >= 4) {
                    $star_info['star_content'] = '好评';
                }
            } else {
                $star_info['star_sum'] = number_format(4, 1);
                $star_info['star_avg'] = number_format(4, 1);
                $star_info['star_content'] = '好评';
            }
        } else {
            $star_info['star_sum'] = number_format(4, 1);
            $star_info['star_avg'] = number_format(4, 1);
            $star_info['star_content'] = '好评';
        }
        return $star_info;
    }

    /**
     * 获取报名班制的评价星级
     *
     * @return  void
     * @author  wl
     * @date    Dec 07, 2016
     **/
    public function getShiftsCommentInfo ($coach_id, $school_id) {
        if (!$coach_id) {
            return false;
        }
        // 预约及时评价信息
        $star_info = array();
        $comment_stars = array();
        $count = $this->table(C('DB_PREFIX').'coach_comment comment')
            ->where(array('coach_id' => $coach_id, 'type' => 2, 'school_id' => $school_id))
            ->count();
        $comment_info = $this->table(C('DB_PREFIX').'coach_comment comment')
            // ->join(C('DB_PREFIX').'school_orders orders ON orders.so_order_no = comment.order_no')
            ->where(array('coach_id' => $coach_id, 'type' => 2, 'school_id' => $school_id))
            ->field('coach_star')
            ->select();
        if (!empty($comment_info)) {
            foreach ($comment_info as $key => $value) {
                foreach ($value as $k => $v) {
                    $comment_stars[$key] = $v;
                }
            }
            if (!empty($comment_stars)) {
                $star_info['star_sum'] = number_format(array_sum($comment_stars), 1);
                $star_info['star_avg'] = number_format(array_sum($comment_stars)/$count, 1);
                if ($star_info['star_avg'] < 3 && $star_info['star_avg'] >= 0) {
                    $star_info['star_content'] = '差评';
                } else if ($star_info['star_avg'] < 4 && $star_info['star_avg'] >= 3) {
                    $star_info['star_content'] = '中评';
                } else if ($star_info['star_avg'] >= 4) {
                    $star_info['star_content'] = '好评';
                }
            } else {
                $star_info['star_sum'] = number_format(4, 1);
                $star_info['star_avg'] = number_format(4, 1);
                $star_info['star_content'] = '好评';
            }
        } else {
            $star_info['star_sum'] = number_format(4, 1);
            $star_info['star_avg'] = number_format(4, 1);
            $star_info['content'] = '好评';
        }
        return $star_info;
    }



    /**
     * 获取教练预约的情况
     *
     * @return  void
     * @author  wl
     * @date    Dec 08, 2016
     **/
    public function getCoachFinalTimeConfig ($coach_id, $school_id, $date) {
        $lesson_config = array('1' => '科目一', '2' => '科目二', '3' => '科目三', '4' => '科目四');
        $lisence_config = array('1' => 'C1', '2' => 'C2', '3' => 'C5', '4' => 'A1', '5' => 'A2', '6' => 'B1', '7' => 'B2', '8' => 'D', '9' => 'E', '10' => 'F');
        
        $coach_time_list = array();
        // $date_config = $this->getCoachDateTimeConfig();
        // $coach_time_list['date_time'] = $date_config;
        $date_format = array();
        $date_format = explode('-', $date);
        $year = $date_format[0];
        $month = $date_format[1];
        $day = $date_format[2];

        // 获取驾校的时间配置
        $s_time_list = array();
        $is_automatic = 1;
        $school_config = $this->table(C('DB_PREFIX').'school_config')
            ->where(array('l_school_id' => $school_id))
            ->field('s_time_list, is_automatic')
            ->find();
        if (!empty($school_config)) {
            $s_time_list = array_filter(explode(',', $school_config['s_time_list']));
            $is_automatic = $school_config['is_automatic'];
        }

        // 获取教练的时间配置
        $coach_config_info = $this->table(C('DB_PREFIX').'coach')
            ->where(array('l_coach_id' => $coach_id))
            ->field('s_am_subject, s_pm_subject, s_am_time_list, s_pm_time_list, s_coach_lisence_id, s_coach_lesson_id')
            ->find();
        $s_am_subject = 2;
        $s_pm_subject = 3;
        $s_am_time_list = array();
        $s_pm_time_list = array();
        $s_coach_lisence_id_list = array();
        $s_coach_lesson_id_list = array();
        if (!empty($coach_config_info)) {
            $s_am_subject = $coach_config_info['s_am_subject'];
            $s_pm_subject = $coach_config_info['s_pm_subject'];
            $s_am_time_list = isset($coach_config_info['s_am_time_list']) ? array_filter(explode(',', $coach_config_info['s_am_time_list'])) : array();
            $s_pm_time_list = isset($coach_config_info['s_pm_time_list']) ? array_filter(explode(',', $coach_config_info['s_pm_time_list'])) : array();
            $s_coach_lisence_id_list = isset($coach_config_info['s_coach_lisence_id']) ? array_filter(explode(',', $coach_config_info['s_coach_lisence_id'])) : array();
            $s_coach_lesson_id_list = isset($coach_config_info['s_coach_lesson_id']) ? array_filter(explode(',', $coach_config_info['s_coach_lesson_id'])) : array();
        }

        if (!empty($s_am_time_list) && !empty($s_pm_time_list)) {
            $time_config_ids_arr = array_merge($s_am_time_list, $s_pm_time_list);
        } else {
            $time_config_ids_arr = $s_time_list;
        }

        // study_orders与coach_appoint_time数据表相结合获取相关信息
        $appoint_time_config_id = $this->table(C('DB_PREFIX').'study_orders orders')
            ->join(C('DB_PREFIX').'coach_appoint_time appoint ON appoint.id = orders.appoint_time_id', 'LEFT')
            ->where(
                array(
                    'appoint.id' => array('not in', ''),
                    'appoint.year' => $year,
                    'appoint.month' => $month,
                    'appoint.day' => $day,
                    'orders.l_coach_id' => $coach_id,
                    'orders.i_status' => array('not in', array('3', '101')),
                )
            )
            ->field('orders.time_config_id')
            ->fetchSql(false)
            ->select();
        $time_config_ids = array();
        $time_config_id_arr = array();
        if ($appoint_time_config_id) {
            foreach ($appoint_time_config_id as $key => $value) {
                $time_config_ids = array_filter(explode(',', $value['time_config_id']));
                foreach ($time_config_ids as $k => $v) {
                    $time_config_id_arr[] = $v; 
                }
            }
        }

        // 获取当前教练所设置的时间端配置
        $time_config_id = array();
        $time_lisence_config_id = array();
        $time_lesson_config_id = array();
        $time_config_money_id = array();
        $current_time_config = $this->table(C('DB_PREFIX').'current_coach_time_configuration config')
            ->where(
                array(
                    'coach_id' => $coach_id, 
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                )
            )
            ->find();
        if (!empty($current_time_config)) {
            $time_config_id = explode(',', $current_time_config['time_config_id']);
            $time_lisence_config_id = json_decode($current_time_config['time_lisence_config_id'], true);
            $time_lesson_config_id = json_decode($current_time_config['time_lesson_config_id'], true);
            $time_config_money_id = json_decode($current_time_config['time_config_money_id'], true);
        }
        // 在coach_time_config表中查询相关信息
        $map = array();
        $order = "id";
        $map['i_status'] = 1;
        if (!empty($time_config_ids_arr) && empty($current_time_config)) {
            $map['id'] = array('in', $time_config_ids_arr);
            $order = "start_time DESC";
        }
        $coach_time_config = $this->table(C('DB_PREFIX').'coach_time_config')
            ->where($map)
            ->order($order)
            ->fetchSql(false)
            ->select();
        $_coach_time_config = array();

        if (!empty($coach_time_config)) {
            // 1.获取教练时间配置列表
            foreach ($coach_time_config as $key => $value) {
                $_coach_time_config[$key]['id'] = $value['id'];
                $_coach_time_config[$key]['license_no'] = $value['license_no'];
                $_coach_time_config[$key]['subjects'] = $value['subjects'];
                $_coach_time_config[$key]['price'] = $value['price'];
                $_coach_time_config[$key]['status'] = $value['status'];
                $_coach_time_config[$key]['start_time'] = $value['start_time'];
                $_coach_time_config[$key]['end_time'] = $value['end_time'];
                $_coach_time_config[$key]['start_minute'] = $value['start_minute'];
                $_coach_time_config[$key]['end_minute'] = $value['end_minute'];
                
                if (count($s_coach_lisence_id_list) == 1 && is_array($s_coach_lisence_id_list)) {
                    $_coach_time_config[$key]['license_no'] = isset($lisence_config[$s_coach_lisence_id_list[0]]) ? $lisence_config[$s_coach_lisence_id_list[0]] : 'C1';
                }
                if (count($s_coach_lesson_id_list) == 1 && is_array($s_coach_lesson_id_list)) {
                    $_coach_time_config[$key]['subjects'] = isset($lesson_config[$s_coach_lesson_id_list[0]]) ? $lesson_config[$s_coach_lesson_id_list[0]] : 'C1';
                }

                if (!empty($current_time_config)) {
                    // 获取是否设置的状态
                    if (in_array($value['id'], $time_config_id)) {
                        $_coach_time_config[$key]['is_set'] = 1; //教练设置时间配置
                        $_coach_time_config[$key]['price'] = $time_config_money_id[$value['id']];
                        $_coach_time_config[$key]['license_no'] = $time_lisence_config_id[$value['id']];
                        $_coach_time_config[$key]['subjects'] = $time_lesson_config_id[$value['id']];
                    } else {
                        $_coach_time_config[$key]['is_set'] = 2; //教练未设置时间配置
                        $_coach_time_config[$key]['subjects'] = $value['subjects'];
                    }

                    if ($value['addtime'] != 0) {
                        $_coach_time_config[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                    } else {
                        $_coach_time_config[$key]['addtime'] = '';
                    }

                    // 设置是否预约的状态
                    if (!empty($time_config_id_arr)) {
                        if (in_array($value['id'], $time_config_id_arr)) {
                            $_coach_time_config[$key]['is_appoint'] = 1;//被预约
                        } else {
                            $_coach_time_config[$key]['is_appoint'] = 2;//未被预约
                        }
                    } else {
                        $_coach_time_config[$key]['is_appoint'] = 2;//未被预约
                    }

                } else {

                    // 若教练设置了上午和下午的时间
                    if (!empty($s_am_time_list) && !empty($s_pm_time_list)) {
                        if (in_array($value['id'], $s_am_time_list)) {
                            if ($s_am_subject == 1) {
                                $_coach_time_config[$key]['subjects'] = '科目一';

                            } elseif ($s_am_subject == 2) {
                                $_coach_time_config[$key]['subjects'] = '科目二';

                            } elseif ($s_am_subject == 3) {
                                $_coach_time_config[$key]['subjects'] = '科目三';

                            }elseif ($s_am_subject == 4) {
                                $_coach_time_config[$key]['subjects'] = '科目四';

                            }
                        }

                        if (in_array($value['id'], $s_pm_time_list)) {
                            if ($s_pm_subject == 1) {
                                $_coach_time_config[$key]['subjects'] = '科目一';

                            } elseif ($s_pm_subject == 2) {
                                $_coach_time_config[$key]['subjects'] = '科目二';

                            } elseif ($s_pm_subject == 3) {
                                $_coach_time_config[$key]['subjects'] = '科目三';

                            }elseif ($s_pm_subject == 4) {
                                $_coach_time_config[$key]['subjects'] = '科目四';

                            }
                        }

                    // 教练未设置，驾校设置了
                    } else {

                        if ($value['end_time'] <= 12) {
                            if ($s_am_subject == 1) {
                                $_coach_time_config[$key]['subjects'] = '科目一';

                            } elseif ($s_am_subject == 2) {
                                $_coach_time_config[$key]['subjects'] = '科目二';

                            } elseif ($s_am_subject == 3) {
                                $_coach_time_config[$key]['subjects'] = '科目三';

                            }elseif ($s_am_subject == 4) {
                                $_coach_time_config[$key]['subjects'] = '科目四';

                            }

                        } else {

                            if ($s_pm_subject == 1) {
                                $_coach_time_config[$key]['subjects'] = '科目一';

                            } elseif ($s_pm_subject == 2) {
                                $_coach_time_config[$key]['subjects'] = '科目二';

                            } elseif ($s_pm_subject == 3) {
                                $_coach_time_config[$key]['subjects'] = '科目三';

                            }elseif ($s_pm_subject == 4) {
                                $_coach_time_config[$key]['subjects'] = '科目四';
                            }
                        }
                    }
                    $_coach_time_config[$key]['is_set'] = 1;
                    // 设置是否预约的状态
                    if (!empty($time_config_id_arr)) {
                        if (in_array($value['id'], $time_config_id_arr)) {
                            $_coach_time_config[$key]['is_appoint'] = 1;//被预约
                        } else {
                            $_coach_time_config[$key]['is_appoint'] = 2;//未被预约
                        }
                    } else {
                        $_coach_time_config[$key]['is_appoint'] = 2;//未被预约
                    }
                }
            }
            // 2.获取教练的上午与下午的时间设置
            $coach_am_list = array();
            $coach_pm_list = array();
            foreach ($_coach_time_config as $key => $value) {
                $start_minute = $value['start_minute'] == 0 ? '00' : $value['start_minute'];
                $end_minute = $value['end_minute'] == 0 ? '00' : $value['end_minute'];
                // $final_start_time = $value['start_time'].':'.$start_minute;
                // $final_end_time = $value['end_time'].':'.$end_minute;
                $row[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
                $row[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;

                if (!empty($s_am_time_list) && !empty($s_pm_time_list)) {
                    if (in_array($value['id'], $s_am_time_list)) {
                        $coach_am_list[$key]['id'] = $value['id'];
                        $coach_am_list[$key]['price'] = $value['price'];
                        $coach_am_list[$key]['status'] = $value['status'];
                        $coach_am_list[$key]['is_set'] = $value['is_set'];
                        $coach_am_list[$key]['subjects'] = $value['subjects'];
                        $coach_am_list[$key]['license_no'] = $value['license_no'];
                        $coach_am_list[$key]['is_appoint'] = $value['is_appoint'];
                        $coach_am_list[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
                        $coach_am_list[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;
                    }

                    if (in_array($value['id'], $s_pm_time_list)) {

                        $coach_pm_list[$key]['id'] = $value['id'];
                        $coach_pm_list[$key]['price'] = $value['price'];
                        $coach_pm_list[$key]['status'] = $value['status'];
                        $coach_pm_list[$key]['is_set'] = $value['is_set'];
                        $coach_pm_list[$key]['subjects'] = $value['subjects'];
                        $coach_pm_list[$key]['license_no'] = $value['license_no'];
                        $coach_pm_list[$key]['is_appoint'] = $value['is_appoint'];
                        $coach_pm_list[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
                        $coach_pm_list[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;
                    }

                } else {

                    if ($value['end_time'] <= 12) {
                        $coach_am_list[$key]['id'] = $value['id'];
                        $coach_am_list[$key]['price'] = $value['price'];
                        $coach_am_list[$key]['status'] = $value['status'];
                        $coach_am_list[$key]['is_set'] = $value['is_set'];
                        $coach_am_list[$key]['subjects'] = $value['subjects'];
                        $coach_am_list[$key]['license_no'] = $value['license_no'];
                        $coach_am_list[$key]['is_appoint'] = $value['is_appoint'];
                        $coach_am_list[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
                        $coach_am_list[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;
                   
                    } else {

                        $coach_pm_list[$key]['id'] = $value['id'];
                        $coach_pm_list[$key]['price'] = $value['price'];
                        $coach_pm_list[$key]['status'] = $value['status'];
                        $coach_pm_list[$key]['is_set'] = $value['is_set'];
                        $coach_pm_list[$key]['subjects'] = $value['subjects'];
                        $coach_pm_list[$key]['license_no'] = $value['license_no'];
                        $coach_pm_list[$key]['is_appoint'] = $value['is_appoint'];
                        $coach_pm_list[$key]['final_start_time'] = $value['start_time'].':'.$start_minute;
                        $coach_pm_list[$key]['final_end_time'] = $value['end_time'].':'.$end_minute;
                    }
                }
            }
        }

        $coach_time_list['time_list'] = $coach_time_config;
        $coach_time_list['am_time_list'] = $coach_am_list;
        $coach_time_list['pm_time_list'] = $coach_pm_list;
        $coach_time_list['date'] = $month.'-'.$day;
        return $coach_time_list;
    }

    /**
     * 获取未来7天的时间（包括当天）
     *
     * @return  void
     * @author  wl
     * @date    Dec 07, 2016
     **/
    public function getCoachDateTimeConfig () {
        $current_time = time();
        $year = date('Y', $current_time);
        $month = date('m', $current_time);
        $day = date('d', $current_time);

        // 构建一个时间
        $build_date_timestamp = mktime(0, 0, 0, $month, $day, $year);

        // 循环7天时间
        $date_config = array();
        for ($i = 0; $i <= 7; $i++) {
            $date_config[$i]['fulldate'] = date('Y-m-d', $build_date_timestamp + ( 24 * 3600 * $i));
            $date_config[$i]['date'] = date('m-d', $build_date_timestamp + ( 24 * 3600 * $i));
            $date_config[$i]['month'] = date('m', $build_date_timestamp + ( 24 * 3600 * $i));
            $date_config[$i]['day'] = date('d', $build_date_timestamp + ( 24 * 3600 * $i));
            $date_config[$i]['date_format'] = intval(date('m', $build_date_timestamp + ( 24 * 3600 * $i))).'-'.intval(date('d', $build_date_timestamp + ( 24 * 3600 * $i)));
        }
        return $date_config;
    }

    /**
     * 获取报名情况
     *
     * @return  void
     * @author  wl
     * @date    Dec 07, 2016
     **/
    public function getCoachSignUpInfo ($coach_id) {
        if (!is_numeric($coach_id)) {
            return array();
        }
        
        $coach_shifts = $this->table(C('DB_PREFIX').'school_shifts shifts')
            ->where(array('coach_id' => $coach_id))
            ->fetchSql(false)
            ->select();
        if ($coach_shifts) {
            return $coach_shifts;
        } else {
            return array();
        }
    }
    /**
     * 获取教练班制的个数
     *
     * @return  void
     * @author  wl
     * @date    Dec 07, 2016
     **/
    public function getCoachSignUpList ($coach_id) {
        if (!is_numeric($coach_id)) {
            return $count = 0;
        }
        $coach_shifts = array();
        $so_shifts_ids = $this->table(C('DB_PREFIX').'school_shifts shifts')
            ->where(array('coach_id' => $coach_id))
            ->field('id')
            ->select();
        if (!empty($so_shifts_ids)) {
            foreach ($so_shifts_ids as $key => $value) {
                foreach ($value as $k => $v) {
                    $count = $this->table(C('DB_PREFIX').'school_orders orders')
                        ->where(
                            array(
                                'so_order_status' => array('in', array('1', '4')), 
                                'so_coach_id' => $coach_id, 
                                'so_shifts_id' => $v
                            )
                        )
                        ->fetchSql(false)
                        ->count();
                    $shifts_title = $this->table(C('DB_PREFIX').'school_orders orders')
                        ->join(C('DB_PREFIX').'school_shifts shifts ON shifts.id = orders.so_shifts_id', 'LEFT')
                        ->where(
                            array(
                                'so_order_status' => array('in', array('1', '4')), 
                                'so_coach_id' => $coach_id, 
                                'shifts.coach_id' => $coach_id, 
                                'so_shifts_id' => $v
                            )
                        )
                        ->fetchSql(false)
                        ->getField('sh_title');
                    if ($count != 0 && $shifts_title != '') {
                        $coach_shifts[$key]['count'] = $count;
                        $coach_shifts[$key]['shifts_title'] = $shifts_title;
                    } else {
                        $coach_shifts[$key]['count'] = 0;
                        $coach_shifts[$key]['shifts_title'] = '暂无';
                    }
                }
            }
            return $coach_shifts;
        } else {
            return array();
        }
    }



    /**
     * 生成可变的日期配置
     *
     * @return  void
     * @author  wl
     * @date    Sep 09, 2016
     **/
    public function getChangeTimeConfig () {
        $current_time   = time();
        $year           = date('Y', $current_time);
        $month          = date('m', $current_time);
        $day            = date('d', $current_time);

        // 构建一个时间
        $build_date_timestamp = mktime(0, 0, 0, $month, $day, $year);

        // 循环7天日期
        $date_config = array();
        for ($i = 0; $i <= 6; $i++) {
            $date_config['date'][] = date('Y-m-d', $build_date_timestamp + (3600 * 24 * $i));
            $date_config['weekday'][] = date('w', $build_date_timestamp + (3600 * 24 * $i));
        }
        // 从coach_time_config获取当前的时间配置
        $coach_time_config = $this->table(C('DB_PREFIX').'coach_time_config')
            ->fetchSql(false)
            ->select();
        $date_config['time'] = $coach_time_config;
        return $date_config;
    }

    /**
     * 获取当前教练时间配置
     *
     * @return  void
     * @author  wl
     * @date    Sep 12, 2016
     **/
    public function getCoachCurrentTimeConfig ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        // 教练被预约的时间状态
        $study_orders = $this->table(C('DB_PREFIX').'study_orders orders')
            ->join(C('DB_PREFIX').'coach_appoint_time appoint ON appoint.id = orders.appoint_time_id', 'LEFT')
            ->where(array('l_coach_id' => $id))
            ->field('appoint.time_config_id') 
            ->order('l_study_order_id DESC')
            ->select();

        $current_coach_time = $this->table(C('DB_PREFIX').'current_coach_time_configuration')
            ->where('coach_id = :cid')
            ->bind(['cid' => $id])
            ->select();
        $coach_time_list = array();
        $time_list = array();
        if ($current_coach_time) {
            foreach ($current_coach_time as $key => $value) {
                $money_config   = json_decode($value['time_config_money_id'], true);
                $license_config = json_decode($value['time_lisence_config_id'], true);
                $lesson_config  = json_decode($value['time_lesson_config_id'], true);
                $time_config    = explode(',', $value['time_config_id']);
                $date_config[]  = $value['month'].'-'.$value['day'];

                // 从教练的时间配置表中找到相应的时间配置
                $coach_time_config = $this->table(C('DB_PREFIX').'coach_time_config')
                    ->where(array('id' => array('in', $time_config)))
                    ->select();
                if ($coach_time_config) {
                    foreach ($coach_time_config as $k => $v) {
                        $time_list[$k]['id'] = $v['id'];
                        $time_list[$k]['start_time'] = $v['start_time'];
                        $time_list[$k]['end_time'] = $v['end_time'];
                        $time_list[$k]['license_name'] = $license_config[$v['id']];
                        $time_list[$k]['lesson_name'] = $lesson_config[$v['id']];
                        $time_list[$k]['money'] = $money_config[$v['id']];
                    }
                }
                $coach_time_list['time_list'] = $time_list;
            }
            $coach_time_list['date_time'] = $date_config;
            return $coach_time_list;
        }
    }

    /**
     * 删除前一天的时间数据
     *
     * @return  void
     * @author  wl
     * @date    Sep 09, 2016
     **/
    public function delPreTime($coach_id, $date) {
        $pre_time = strtotime($date) - 16*3600;
        $year = date('Y', $pre_time);
        $month = date('m', $pre_time);
        if ($month < 10) {
            $month = substr($month, 1, 2);
        }

        $day = date('d', $pre_time);
        if ($day < 10) {
            $day = substr($day, 1, 2);
        }
        $current_coach_time = $this->table(C('DB_PREFIX').'current_coach_time_configuration')
            ->where(array('coach_id' => $coach_id, 'year' => $year, 'month' => $month, 'day' => $day))
            ->fetchSql(false)
            ->delete();
        return $current_coach_time;
    }

    /**
     * 删除此教练所有的的时间数据
     *
     * @return  void
     * @author  wl
     * @date    Sep 10, 2016
     **/
    public function delAllTime($coach_id) {
        $current_coach_time = $this->table(C('DB_PREFIX').'current_coach_time_configuration')
            ->where(array('coach_id' => $coach_id))
            ->fetchSql(false)
            ->delete();
        return $current_coach_time;
    }

    /**
     * 更新或者添加时间配置
     *
     * @return  void
     * @author  wl
     * @date    Sep 10, 2016
     **/
    public function updateCoachTime ($post) {
        if (!empty($post)) {
            $coach_id   = $post['coach_id'];
            $year       = $post['year'];
            $month      = $post['month'];
            $day        = $post['day'];
            // 搜索当天日期的数据
            $current_coach_time = $this->table(C('DB_PREFIX').'current_coach_time_configuration')
                ->where('coach_id = :cid AND year = :y AND month = :m AND day = :d')
                ->bind(['cid' => $coach_id, 'y' => $year, 'm' => $month, 'd' => $day])
                ->select();
            if (!empty($current_coach_time)) {
                $data = array(
                    'current_time'          => $post['current_time'], 
                    'time_config_money_id'  => $post['time_config_money_id'], 
                    'time_config_id'        => $post['time_config_id'],
                    'time_lisence_config_id'=> $post['time_lisence_config_id'],
                    'time_lesson_config_id' => $post['time_lesson_config_id'],
                    'updatetime'            => time(),
                );
                $current_coach_time = D('current_coach_time_configuration');
                if ($res = $current_coach_time->create($data)) {
                    $result = $current_coach_time->where('coach_id = :cid AND year = :y AND month = :m AND day = :d')
                        ->bind(['cid' => $coach_id, 'y' => $year, 'm' => $month, 'd' => $day])
                        ->save($res); 
                    if ($result) {
                        return $result;
                    } else {
                        return false;
                    }
                }
            } else {
                $data = array(
                    'coach_id'              => $coach_id,
                    'year'                  => $year,
                    'month'                 => $month,
                    'day'                   => $day,
                    'current_time'          => $post['current_time'], 
                    'time_config_money_id'  => $post['time_config_money_id'], 
                    'time_config_id'        => $post['time_config_id'],
                    'time_lisence_config_id'=> $post['time_lisence_config_id'],
                    'time_lesson_config_id' => $post['time_lesson_config_id'],
                    'addtime'               => time()
                );
                $current_coach_time = D('current_coach_time_configuration');
                if ($res = $current_coach_time->create($data)) {
                    $result = $current_coach_time->add($res); 
                    if ($result) {
                        return $result;
                    } else {
                        return false;
                    }
                }
            }
        } else {
            return false;
        }
    }

    /**
     * 获得牌照表中的信息
     *
     * @return  void
     * @author  wl
     * @date    Sep 08, 2016
     **/
    public function getLicenseInfo () {
        $licenseInfo = $this->table(C('DB_PREFIX').'license_config lc')
            ->order('lc.order DESC')
            ->fetchSql(false)
            ->select();
        $licenseInfos = array();
        if (!empty($licenseInfo)) {
            foreach ($licenseInfo as $key => $value) {
                $licenseInfos[$value['id']] = $value;
            }
        }
        return $licenseInfos;
    }

    /**
     * 获得科目表中的信息
     *
     * @return  void
     * @author  wl
     * @date    Sep 08, 2016
     **/
    public function getLessonInfo () {
        $lessonInfo = $this->table(C('DB_PREFIX').'lesson_config lc')
            ->fetchSql(false)
            ->select();
        $lessonInfos = array();
        if (!empty($lessonInfo)) {
            foreach ($lessonInfo as $key => $value) {
                $lessonInfos[$value['id']] = $value;
            } 
        }
        return $lessonInfos;
    }

    /**
     * 自己配置科目名称
     *
     * @return  void
     * @author  wl
     * @date    Jan 04, 2017
     **/
    public function productLesson () {
        $lessonInfo = array();
        $lessonInfo = array(
            '1' => array('lesson_id' => 1, 'lesson_name' => '科目一'),
            '2' => array('lesson_id' => 2, 'lesson_name' => '科目二'),
            '3' => array('lesson_id' => 3, 'lesson_name' => '科目三'),
            '4' => array('lesson_id' => 4, 'lesson_name' => '科目四'),
        );
        return $lessonInfo;
    }


    // 获取所有的教练的所有信息
    /*
     * @return array $infolist or bool
     * @author Gao
     */
    public function getCoachInfoBySchool($school_id = null) {
        if (!$school_id) {
            return array();
        }
        $map['s_school_name_id'] = $school_id;
        $coach_info_list = $this->where($map)->select();
        return $coach_info_list;
    }

    // get all coach IDs
    /*
     * @return array $idlist
     * @author Gao
     */
    public function getCoachIdBySchool($school_id = null) {
        if (!$school_id) {
            return array();
        }
        $map['s_school_name_id'] = $school_id;
        $coach_id_list = $this->where($map)->getField('l_coach_id', true);
        return $coach_id_list;
    }

    //获取所有教练
    public function getSystemCoachList($school_id) {
        $coach_list = $this->join('cs_cars ON cs_coach.s_coach_car_id = cs_cars.id')
            ->where(array('s_school_name_id'=>$school_id))
            ->order('l_coach_id desc')
            ->getField('l_coach_id, s_coach_name, name, car_type, car_no');
        if($coach_list) {
            foreach ($coach_list as $key => $value) {
                if($value['car_type'] == 1) {
                    $coach_list[$key]['car_type_name'] = '普通车型';

                } else if($value['car_type'] == 2) {
                    $coach_list[$key]['car_type_name'] = '加强车型';

                } else if($value['car_type'] == 3) {
                    $coach_list[$key]['car_type_name'] = '模拟车型';

                } else {
                    $coach_list[$key]['car_type_name'] = '普通车型';

                }
            }
        }
        return $coach_list;
    }

// 2.模板关联管理部分
    /**
     * 教练模板关联列表管理的展示
     *
     * @return  void
     * @author  wl
     * @date    Oct 19, 2016
     **/
    public function getTempRelation ($school_id) {
        $map = array();
        if ($school_id != 0) {
            $coach_id_arr = $this->table(C('DB_PREFIX').'coach')
                ->field('l_coach_id as coach_id')
                ->where(array('s_school_name_id' => $school_id))
                ->select();
            $temp_owner_ids = array();
            if (!empty($coach_id_arr)) {
                foreach ( $coach_id_arr as $key => $value ) {
                    $temp_owner_ids[$key] = $value['coach_id'];
                }
            }
            array_unshift($temp_owner_ids, $school_id);
            $map['t.temp_owner_id'] = array('in', $temp_owner_ids);
        }

        $map['t.is_deleted'] = 1;
        
        $count = $this->table(C('DB_PREFIX').'template_relationship t')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $temprelationlists = array();
        $temprelationlist = $this->table(C('DB_PREFIX').'template_relationship t')
            ->where($map)
            ->limit($Page->firstRow.','.$page->listRows)
            ->order('id DESC')
            ->fetchSql(false)
            ->select();
        if ($temprelationlist) {
            foreach ($temprelationlist as $key => $value) {

                if ($value['lesson_name'] == '') {
                    $temprelationlist[$key]['lesson_name'] = '--';

                } 

                if ($value['license_name'] == '') {
                    $temprelationlist[$key]['license_name'] = '--';

                } 

                if ($value['addtime'] != 0 && $value['addtime'] != '') {
                    $temprelationlist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);

                } else {
                    $temprelationlist[$key]['addtime'] = '--';

                }

                if ($value['updatetime'] != 0 && $value['updatetime'] != '') {
                    $temprelationlist[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);

                } else {
                    $temprelationlist[$key]['updatetime'] = '--';

                }

                switch ($value['weekday']) {
                    case 1 : $temprelationlist[$key]['weekday_name'] = '一'; break;
                    case 2 : $temprelationlist[$key]['weekday_name'] = '二'; break;
                    case 3 : $temprelationlist[$key]['weekday_name'] = '三'; break;
                    case 4 : $temprelationlist[$key]['weekday_name'] = '四'; break;
                    case 5 : $temprelationlist[$key]['weekday_name'] = '五'; break;
                    case 6 : $temprelationlist[$key]['weekday_name'] = '六'; break;
                    case 7 : $temprelationlist[$key]['weekday_name'] = '日'; break;
                    default : $temprelationlist[$key]['weekday_name'] = '一'; break;

                }
            }
        }
        $temprelationlists = array('temprelationlist' => $temprelationlist, 'page' => $page, 'count' => $count);
        return $temprelationlists;
    }

    /**
     * 教练模板关联列表管理的展示
     *
     * @return  void
     * @author  wl
     * @date    Oct 20, 2016
     **/
    public function searchTempRelation ($param, $school_id) {
        $map = array();
        $complex = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_info'] == '' ) {
            $complex['temp_name'] = array('LIKE', $s_keyword);
            $complex['temp_owner_name'] = array('LIKE', $s_keyword);
            $complex['_logic'] = 'OR';
        } else {
            $complex[$param['search_info']] = array('LIKE', $s_keyword);
        }
        $map['_complex'] = $complex;

        if ($param['is_default'] != 0) {
            $map['is_default'] = array('EQ', $param['is_default']);
        }
        if ($param['is_online'] != 0) {
            $map['is_online'] = array('EQ', $param['is_online']);
        }

        if ($param['weekday'] != 0) {
            $map['weekday'] = array('EQ', $param['weekday']);
        }

        if ($param['temp_type'] != 0) {
            $map['temp_type'] = array('EQ', $param['temp_type']);
        }

        $map['is_deleted'] = 1;

        if ($school_id != 0) {
            $coach_id_arr = $this->table(C('DB_PREFIX').'coach')
                ->field('l_coach_id as coach_id')
                ->where(array('s_school_name_id' => $school_id))
                ->select();
            $temp_owner_ids = array();
            if (!empty($coach_id_arr)) {
                foreach ( $coach_id_arr as $key => $value ) {
                    $temp_owner_ids[$key] = $value['coach_id'];
                }
            }
            array_unshift($temp_owner_ids, $school_id);
            $map['t.temp_owner_id'] = array('in', $temp_owner_ids);
        }
        
        $count = $this->table(C('DB_PREFIX').'template_relationship t')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $temprelationlists = array();
        $temprelationlist = $this->table(C('DB_PREFIX').'template_relationship t')
            ->where($map)
            ->limit($Page->firstRow.','.$page->listRows)
            ->order('id DESC')
            ->fetchSql(false)
            ->select();

        if ($temprelationlist) {
            foreach ($temprelationlist as $key => $value) {

                if ($value['lesson_name'] == '') {
                    $temprelationlist[$key]['lesson_name'] = '--';

                } 

                if ($value['license_name'] == '') {
                    $temprelationlist[$key]['license_name'] = '--';

                } 

                if ($value['addtime'] != 0 && $value['addtime'] != '') {
                    $temprelationlist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);

                } else {
                    $temprelationlist[$key]['addtime'] = '--';

                }
                if ($value['updatetime'] != 0 && $value['updatetime'] != '') {
                    $temprelationlist[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);

                } else {
                    $temprelationlist[$key]['updatetime'] = '--';

                }

                switch ($value['weekday']) {
                    case 1 : $temprelationlist[$key]['weekday_name'] = '一'; break;
                    case 2 : $temprelationlist[$key]['weekday_name'] = '二'; break;
                    case 3 : $temprelationlist[$key]['weekday_name'] = '三'; break;
                    case 4 : $temprelationlist[$key]['weekday_name'] = '四'; break;
                    case 5 : $temprelationlist[$key]['weekday_name'] = '五'; break;
                    case 6 : $temprelationlist[$key]['weekday_name'] = '六'; break;
                    case 7 : $temprelationlist[$key]['weekday_name'] = '日'; break;
                    default : $temprelationlist[$key]['weekday_name'] = '一'; break;
                }
            }
        }
        $temprelationlists = array('temprelationlist' => $temprelationlist, 'page' => $page, 'count' => $count);
        return $temprelationlists;
    }

    /**
    * 检查模板是否重复
    * @param    $type       用户类型 1 coach | 2 school
    * @param    $owner_id   用户id
    * @param    $name       模板名称
    * @return   void
    **/
    public function checkRepTemp ( $type, $owner_id, $name ) {

        $map = array(
            'temp_type' => $type,
            'temp_owner_id' => $owner_id,
            'temp_name' => $name
        );
        $checktemp = $this->table(C('DB_PREFIX').'template_relationship')
            ->where($map)
            ->find();
        if ( !empty($checktemp) ) {
            return true;
        } 
        return false;
    }

    /**
     * 根据temp_type的不同获取不同的角色名称
     *
     * @return  void
     * @author  wl
     * @date    Oct 20, 2016
     **/
    public function getTempOwnerName ($type, $school_id) {
        if (!isset($type) && !$school_id) {
            return false;
        }
        $map = array();
        if ( $school_id != 0 ) {
            if ( $type == 1) { //coach
                $map['coach.s_school_name_id'] = $school_id;
            } else if ( $type == 2 ) {
                $map['school.l_school_id'] = $school_id;
            }
        }

        if ($type == 1) {
            $coach_list = $this->table(C('DB_PREFIX').'coach coach')
                ->field('l_coach_id, s_coach_name, s_school_name')
                ->join(C('DB_PREFIX').'school school ON school.l_school_id = coach.s_school_name_id')
                ->where($map)
                ->select();
            if (!empty($coach_list)) {
                foreach ($coach_list as $key => $value) {
                    if ($value['s_school_name'] == '') {
                        $coach_list[$key]['s_school_name'] = '嘻哈平台';
                    }
                }
                return $coach_list;
            } else {
                return array();
            }
        } elseif ($type == 2) {
            $school_list = $this->table(C('DB_PREFIX').'school school')
                ->field('l_school_id, s_school_name')
                ->where($map)
                ->select();
            if ($school_list) {
                return $school_list;
            } else {
                return array();
            }
        }

    }

    /**
    * 单条获取模板用户名称
    * @param    
    * @return 
    **/
    public function getOwnerNameById ( $id, $type ) {
        if (!$id && !$type) {
            return false;
        }
        if ( $type == 1) { // coach
            $coach_name = $this->table(C('DB_PREFIX').'coach')
                ->where(array('l_coach_id' => $id))
                ->getField('s_coach_name');
            if (!empty($coach_name)) {
                $user_name = $coach_name;
            }
        } elseif ( $type == 2 ) { // school
            $school_name = $this->table(C('DB_PREFIX').'school')
                ->where(array('l_school_id' => $id))
                ->getField('s_school_name');
            if (!empty($school_name)) {
                $user_name = $school_name;
            }
        }
        return $user_name;
    }

    /**
     * 获取单条模板信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 20, 2016
     **/
    public function getTempRelationById ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $temprelationlist = $this->table(C('DB_PREFIX').'template_relationship')
            ->where(array('id' => $id))
            ->fetchSql(false)
            ->find();
        if ($temprelationlist) {
            if ($temprelationlist['temp_type'] == 1) {
                $owner_name = $this->table(C('DB_PREFIX').'coach')
                    ->where('l_coach_id = :oid')
                    ->bind(['oid' => $temprelationlist['temp_owner_id']])
                    ->getField('s_coach_name');
                if ($owner_name) {
                    $temprelationlist['temp_owner_name'] = $owner_name;
                } else {
                    $temprelationlist['temp_owner_name'] = '';
                }
            } elseif ($temprelationlist['temp_type'] == 2) {
                $owner_name = $this->table(C('DB_PREFIX').'school')
                    ->where('l_school_id = :oid')
                    ->bind(['oid' => $temprelationlist['temp_owner_id']])
                    ->getField('s_school_name');
                if ($owner_name) {
                    $temprelationlist['temp_owner_name'] = $owner_name;
                } else {
                    $temprelationlist['temp_owner_name'] = '';
                }
            }

            if ($temprelationlist['addtime'] != 0) {
                $temprelationlist['addtime'] = date('Y-m-d H:i:s', $temprelationlist['addtime']);
            } else {
                $temprelationlist['addtime'] = '';
            }
            if ($temprelationlist['updatetime'] != 0) {
                $temprelationlist['updatetime'] = date('Y-m-d H:i:s', $temprelationlist['updatetime']);
            } else {
                $temprelationlist['updatetime'] = '';
            }

            if ($temprelationlist['temp_type'] == 1) {
                $temprelationlist['temp_type_name'] = '教练';
            } elseif ($temprelationlist['temp_type'] == 2) {
                $temprelationlist['temp_type_name'] = '驾校';
            } else {
                $temprelationlist['temp_type_name'] = '嘻哈';
            }
        }
        return $temprelationlist;
    }

    /**
     * 删除模板
     *
     * @return  void
     * @author  wl
     * @date    Oct 20, 2016
     **/
    public function delCoachTempRelation ($id) {
        if (!is_numeric($id)) {
            return false;
        }

        $result = M('template_relationship')
            ->where(array('id' => $id))
            ->fetchSql(false)
            ->save(array('is_deleted' => 2));
        
        if ($result) {
            $config_list = $this->table(C('DB_PREFIX'.'time_config_template'))
                ->where(array('temp_id' => $id, 'deleted' => 1))
                ->find();
            if ( ! empty($config_list)) {
                $res = M('time_config_template')->where(array('temp_id' => $id))
                ->save(array('deleted' => 2));
            }
        }

        return $result;
    }

    /**
     * 设置模板是否是默认的
     *
     * @return  void
     * @author  wl
     * @date    Oct 20, 2016
     **/
    public function setCoachDefault ($id, $status) {
        if (!is_numeric($id) || !isset($status)) {
            return false;
        }

        $list = array();
        $data = array('is_default' => $status);
        $result = M('template_relationship')
            ->where(array('id' => $id))
            ->save($data);
        $list['id']     = $id;
        $list['res']    = $result;
        return $list;
    }

    /**
     * 设置是否在线的
     *
     * @return  void
     * @author  wl
     * @date    Oct 20, 2016
     **/
    public function setCoachOnline ($id, $status) {
        if (!is_numeric($id) || !isset($status)) {
            return false;
        }

        $list = array();
        $data = array('is_online' => $status);
        $result = M('template_relationship')
            ->where(array('id' => $id))
            ->save($data);
        $list['id']     = $id;
        $list['res']    = $result;
        return $list;
    }


// 3.模板列表部分
    /**
     * 获取模板列表的相关信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 20, 2016
     **/
    public function getTimeConfTemp ($school_id) {
        $map = array();
        if ($school_id != 0) {
            $coach_id_arr = $this->table(C('DB_PREFIX').'coach') 
                ->field('l_coach_id as coach_id')
                ->where(array('s_school_name_id' => $school_id))
                ->select();
            $ids_arr = array();
            if ( !empty($coach_id_arr) ) {
                foreach ($coach_id_arr as $key => $value) {
                    $ids_arr[$key] = $value['coach_id'];
                }
            }
            array_unshift($ids_arr, $school_id);
            $map['tr.temp_owner_id'] = array('in', $ids_arr);
        }
        $map['tc.deleted'] = 1;
        $count = $this->table(C('DB_PREFIX').'time_config_template tc')
            ->join(C('DB_PREFIX').'template_relationship tr ON tr.id = tc.temp_id', 'LEFT')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $timeconftemplists = array();
        $timeconftemplist = $this->table(C('DB_PREFIX').'time_config_template tc')
            ->field(
                'tc.*, 
                 tr.id as tempid, 
                 tr.temp_name,
                 tr.temp_owner_name'
            )
            ->join(C('DB_PREFIX').'template_relationship tr ON tr.id = tc.temp_id', 'LEFT')
            ->where($map)
            ->order('id DESC')
            ->fetchSql(false)
            ->select();
        if ($timeconftemplist) {
            foreach ($timeconftemplist as $key => $value) {
                
                if ($value['lesson_name'] == "") {
                    $timeconftemplist[$key]['lesson_name'] = '--';
                }

                if ($value['license_name'] == "") {
                    $timeconftemplist[$key]['license_name'] = '--';
                }

                if ($value['addtime'] != 0) {
                    $timeconftemplist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $timeconftemplist[$key]['addtime'] = '--';
                }

                if ($value['updatetime'] != 0) {
                    $timeconftemplist[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $timeconftemplist[$key]['updatetime'] = '--';
                }
            }
        }
        $timeconftemplists = array('timeconftemplist' => $timeconftemplist, 'count' => $count, 'page' => $page);
        return $timeconftemplists;

    }

    /**
     * 根据条件搜索获取模板列表的相关信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 21, 2016
     **/
    public function searchTimeConfTemp ($param, $school_id) {
        $map = array();
        $complex = array();
        $timeconftemplists = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_info'] == '') {
            $complex['temp_name'] = array('like', $s_keyword);
            $complex['temp_owner_name'] = array('like', $s_keyword);
            $complex['tc.lesson_name'] = array('like', $s_keyword);
            $complex['tc.max_user_num'] = array('like', $s_keyword);
            $complex['_logic'] = "OR";
        } else {
            if ($param['search_info'] == 'lesson_name') {
                $param['search_info'] == 'tc.lesson_name';
            }
            $complex[$param['search_info']] = array('like', $s_keyword);
        }
        $map['_complex'] = $complex;
        if ($school_id != 0) {
            $coach_id_arr = $this->table(C('DB_PREFIX').'coach') 
                ->field('l_coach_id as coach_id')
                ->where(array('s_school_name_id' => $school_id))
                ->select();
            $ids_arr = array();
            if ( !empty($coach_id_arr) ) {
                foreach ($coach_id_arr as $key => $value) {
                    $ids_arr[$key] = $value['coach_id'];
                }
            }
            array_unshift($ids_arr, $school_id);
            $map['tr.temp_owner_id'] = array('in', $ids_arr);
        }
        $map['tc.deleted'] = 1;
        $count = $this->table(C('DB_PREFIX').'time_config_template tc')
            ->join(C('DB_PREFIX').'template_relationship tr ON tr.id = tc.temp_id', 'LEFT')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $timeconftemplist = $this->table(C('DB_PREFIX').'time_config_template tc')
            ->field(
                'tc.*, 
                 tr.id as tempid, 
                 tr.temp_name,
                 tr.temp_owner_name'
            )
            ->join(C('DB_PREFIX').'template_relationship tr ON tr.id = tc.temp_id', 'LEFT')
            ->where($map)
            ->order('id DESC')
            ->fetchSql(false)
            ->select();
        if ($timeconftemplist) {
            foreach ($timeconftemplist as $key => $value) {

                if ($value['lesson_name'] == "") {
                    $timeconftemplist[$key]['lesson_name'] = '--';
                }

                if ($value['license_name'] == "") {
                    $timeconftemplist[$key]['license_name'] = '--';
                }
                
                if ($value['addtime'] != 0) {
                    $timeconftemplist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $timeconftemplist[$key]['addtime'] = '--';
                }

                if ($value['updatetime'] != 0) {
                    $timeconftemplist[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $timeconftemplist[$key]['updatetime'] = '--';
                }
            }
        }
        $timeconftemplists = array('timeconftemplist' => $timeconftemplist, 'count' => $count, 'page' => $page);
        return $timeconftemplists;

    }

    /**
     * 获取单条时间模板
     *
     * @return  void
     * @author  wl
     * @date    Oct 21, 2016
     **/
    public function getTimeConfTempById ($id) {
        $temprelationlist = $this->table(C('DB_PREFIX').'time_config_template tc')
            ->join(C('DB_PREFIX').'template_relationship tr ON tr.id = tc.temp_id', 'LEFT')
            ->where(array('tc.id' => $id))
            ->field('tc.*, tr.id as tempid, temp_name, temp_owner_name')
            ->find();
        if ($temprelationlist) {
            if ($temprelationlist['addtime'] !=0 ) {
                $temprelationlist['addtime'] = date('Y-m-d H:i:s', $temprelationlist['addtime']);
            } else {
                $temprelationlist['addtime'] = '';
            }

            if ($temprelationlist['updatetime'] !=0 ) {
                $temprelationlist['updatetime'] = date('Y-m-d H:i:s', $temprelationlist['updatetime']);
            } else {
                $temprelationlist['updatetime'] = '';
            }
        }
        return $temprelationlist;
    }

    /**
     * 获取
     *
     * @return  void
     * @author  wl
     * @date    Oct 21, 2016
     **/
    public function getTempName ($school_id) {
        $map = array();
        if ($school_id != 0) {
            $coach_id_arr = $this->table(C('DB_PREFIX').'coach') 
                ->field('l_coach_id as coach_id')
                ->where(array('s_school_name_id' => $school_id))
                ->select();
            $ids_arr = array();
            if ( !empty($coach_id_arr) ) {
                foreach ($coach_id_arr as $key => $value) {
                    $ids_arr[$key] = $value['coach_id'];
                }
            }
            array_unshift($ids_arr, $school_id);
            $map['temp_owner_id'] = array('in', $ids_arr);
        }

        $temprelationlist = $this->table(C('DB_PREFIX').'template_relationship tr')
            ->field('tr.id, tr.temp_name, tr.temp_owner_name')
            ->where($map)
            ->select();
        if (!empty($temprelationlist)) {
            return $temprelationlist;
        } else {
            return array();
        }
    }


    /**
     * 逻辑删除时间模板
     *
     * @return  void
     * @author  wl
     * @date    Oct 21, 2016
     **/
    public function delTimeConfTemp ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $result = M('time_config_template')->where(array('id' => $id))
            ->save(array('deleted' => 2));
        // ->delete();
        return $result;
    }

    /**
     * 设置时间模板是否在线的状态
     *
     * @return  void
     * @author  wl
     * @date    Oct 21, 2016
     **/
    public function setTimeTempOnline ($id, $status) {
        if (!is_numeric($id)) {
            return false;
        }
        $list = array();
        $data = array('is_online' => $status);
        $result = M('time_config_template')
            ->where(array('id' => $id))
            ->data($data)
            ->fetchSql(false)
            ->save();
        $list['id'] = $id;
        $list['res'] = $result;
        return $list;
    }


    /**
     * 获取单条牌照的相关信息
     *
     * @return  void
     * @author  wl
     * @date    Nov 01, 2016
     **/
    public function getLicenseInfoById ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $licenselist = $this->table(C('DB_PREFIX').'license_config')
            ->where(array('id' => $id))
            ->field('id, license_name')
            ->fetchSql(false)
            ->find();
        return $licenselist;
    }

// 4.教练绑定管理
    /**
     * 获取教练与学员的绑定关系
     *
     * @return  void
     * @author  wl
     * @date    Dec 15, 2016
     **/
    public function getCoachUserRelationList ($school_id) {
        if ($school_id == 0) {
            $count = $this->table(C('DB_PREFIX').'coach_user_relation relation')
                ->join(C('DB_PREFIX').'coach coach ON coach.l_coach_id = relation.coach_id', 'LEFT')
                ->join(C('DB_PREFIX').'school school ON school.l_school_id = coach.s_school_name_id', 'LEFT')
                ->join(C('DB_PREFIX').'user user ON user.l_user_id = relation.user_id', 'LEFT')
                ->where(array('i_status' => 0, 'i_user_type' => 0))
                ->count();
            $Page = new Page($count, 10);
            $page = $this->getPage($count, 10);
            $coachuserlists = array();
            $coachuserlist = $this->table(C('DB_PREFIX').'coach_user_relation relation')
                ->join(C('DB_PREFIX').'coach coach ON coach.l_coach_id = relation.coach_id', 'LEFT')
                ->join(C('DB_PREFIX').'school school ON school.l_school_id = coach.s_school_name_id', 'LEFT')
                ->join(C('DB_PREFIX').'user user ON user.l_user_id = relation.user_id', 'LEFT')
                ->where(array('i_status' => 0, 'i_user_type' => 0))
                ->order('relation.id DESC, relation.addtime DESC')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->field('relation.*, l_coach_id, s_coach_name, s_coach_phone, l_user_id, s_username, s_real_name, s_phone, l_school_id, s_school_name')
                ->select();
        } else {
            $count = $this->table(C('DB_PREFIX').'coach_user_relation relation')
                ->join(C('DB_PREFIX').'coach coach ON coach.l_coach_id = relation.coach_id', 'LEFT')
                ->join(C('DB_PREFIX').'school school ON school.l_school_id = coach.s_school_name_id', 'LEFT')
                ->join(C('DB_PREFIX').'user user ON user.l_user_id = relation.user_id', 'LEFT')
                ->where(array('i_status' => 0, 'i_user_type' => 0, 'l_school_id' => $school_id))
                ->count();
            $Page = new Page($count, 10);
            $page = $this->getPage($count, 10);
            $coachuserlists = array();
            $coachuserlist = $this->table(C('DB_PREFIX').'coach_user_relation relation')
                ->join(C('DB_PREFIX').'coach coach ON coach.l_coach_id = relation.coach_id', 'LEFT')
                ->join(C('DB_PREFIX').'school school ON school.l_school_id = coach.s_school_name_id', 'LEFT')
                ->join(C('DB_PREFIX').'user user ON user.l_user_id = relation.user_id', 'LEFT')
                ->where(array('i_status' => 0, 'i_user_type' => 0, 'l_school_id' => $school_id))
                // ->where(array('i_status' => 0, 'i_user_type' => 0))
                ->order('relation.id DESC, relation.addtime DESC')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->field('relation.*, l_coach_id, s_coach_name, s_coach_phone, l_user_id, s_username, s_real_name, s_phone, l_school_id, s_school_name')
                ->select();
        }
        if (!empty($coachuserlist)) {
            foreach ($coachuserlist as $key => $value) {
                if ($value['s_school_name'] == '') {
                    $coachuserlist[$key]['s_school_name'] = '--';
                }

                if ($value['addtime'] != 0) {
                    $coachuserlist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $coachuserlist[$key]['addtime'] = '--';
                }

                if ($value['updatetime'] != 0) {
                    $coachuserlist[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $coachuserlist[$key]['updatetime'] = '--';
                }

                if ($value['s_real_name'] != '') {
                    $coachuserlist[$key]['user_name'] = $value['s_real_name'];
                } else {
                    $coachuserlist[$key]['user_name'] = '--';
                }

                if ($value['s_coach_name'] != '') {
                    $coachuserlist[$key]['coach_name'] = $value['s_coach_name'];
                } else {
                    $coachuserlist[$key]['coach_name'] = '--';
                }

                if ($value['s_coach_phone'] != '') {
                    $coachuserlist[$key]['coach_phone'] = $value['s_coach_phone'];
                } else {
                    $coachuserlist[$key]['coach_phone'] = '--';
                }

                if ($value['s_phone'] != '') {
                    $coachuserlist[$key]['user_phone'] = $value['s_phone'];
                } else {
                    $coachuserlist[$key]['user_phone'] = '--';
                }

                if ($value['lesson_name'] == null) {
                    $coachuserlist[$key]['lesson_name'] = '--';
                }

                if ($value['license_name'] == null) {
                    $coachuserlist[$key]['license_name'] = '--';
                }

            }
        }

        $coachuserlists = array('coachuserlist' => $coachuserlist, 'count' => $count, 'page' => $page);
        return $coachuserlists;
    }

    /**
     * 根据搜索条件获取教练与学员的绑定关系
     *
     * @return  void
     * @author  wl
     * @date    Dec 15, 2016
     **/
    public function searchCoachUserRelation ($param, $school_id) {
        $map = array();
        $complex = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_info'] == '') {
            $complex['s_coach_name'] = array('LIKE', $s_keyword);
            $complex['s_coach_phone'] = array('LIKE', $s_keyword);
            $complex['s_real_name'] = array('LIKE', $s_keyword);
            $complex['s_phone'] = array('LIKE', $s_keyword);
            $complex['lesson_name'] = array('LIKE', $s_keyword);
            $complex['license_name'] = array('LIKE', $s_keyword);
            $complex['_logic'] = 'OR';
        } else {
            $complex[$param['search_info']] = array('LIKE', $param);
        }
        $map['_complex'] = $complex;

        if ($param['bind_status'] != 0) {
            $map['bind_status'] = array('EQ', $param['bind_status']);
        }
        $map['i_status'] = array('EQ', 0);
        $map['i_user_type'] = array('EQ', 0);
        if ($school_id == 0) {
            $count = $this->table(C('DB_PREFIX').'coach_user_relation relation')
                ->join(C('DB_PREFIX').'coach coach ON coach.l_coach_id = relation.coach_id', 'LEFT')
                ->join(C('DB_PREFIX').'school school ON school.l_school_id = coach.s_school_name_id', 'LEFT')
                ->join(C('DB_PREFIX').'user user ON user.l_user_id = relation.user_id', 'LEFT')
                ->where($map)
                ->count();
            $Page = new Page($count, 10, $param);
            $page = $this->getPage($count, 10, $param);
            $coachuserlists = array();
            $coachuserlist = $this->table(C('DB_PREFIX').'coach_user_relation relation')
                ->join(C('DB_PREFIX').'coach coach ON coach.l_coach_id = relation.coach_id', 'LEFT')
                ->join(C('DB_PREFIX').'school school ON school.l_school_id = coach.s_school_name_id', 'LEFT')
                ->join(C('DB_PREFIX').'user user ON user.l_user_id = relation.user_id', 'LEFT')
                ->where($map)
                ->order('relation.id DESC, relation.addtime DESC')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->field('relation.*, l_coach_id, s_coach_name, s_coach_phone, l_user_id, s_username, s_real_name, s_phone, l_school_id, s_school_name')
                ->select();
        } else {
            $count = $this->table(C('DB_PREFIX').'coach_user_relation relation')
                ->join(C('DB_PREFIX').'coach coach ON coach.l_coach_id = relation.coach_id', 'LEFT')
                ->join(C('DB_PREFIX').'school school ON school.l_school_id = coach.s_school_name_id', 'LEFT')
                ->join(C('DB_PREFIX').'user user ON user.l_user_id = relation.user_id', 'LEFT')
                ->where(array('l_school_id' => $school_id))
                ->where($map)
                ->count();
            $Page = new Page($count, 10, $param);
            $page = $this->getPage($count, 10, $param);
            $coachuserlists = array();
            $coachuserlist = $this->table(C('DB_PREFIX').'coach_user_relation relation')
                ->join(C('DB_PREFIX').'coach coach ON coach.l_coach_id = relation.coach_id', 'LEFT')
                ->join(C('DB_PREFIX').'school school ON school.l_school_id = coach.s_school_name_id', 'LEFT')
                ->join(C('DB_PREFIX').'user user ON user.l_user_id = relation.user_id', 'LEFT')
                ->where($map)
                ->where(array('l_school_id' => $school_id))
                ->order('relation.id DESC, relation.addtime DESC')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->field('relation.*, l_coach_id, s_coach_name, s_coach_phone, l_user_id, s_username, s_real_name, s_phone, l_school_id, s_school_name')
                ->select();
        }
        if (!empty($coachuserlist)) {
            foreach ($coachuserlist as $key => $value) {
                if ($value['s_school_name'] == '') {
                    $coachuserlist[$key]['s_school_name'] = '--';
                }
                
                if ($value['addtime'] != 0) {
                    $coachuserlist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $coachuserlist[$key]['addtime'] = '--';
                }

                if ($value['updatetime'] != 0) {
                    $coachuserlist[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $coachuserlist[$key]['updatetime'] = '--';
                }

                if ($value['s_real_name'] != '') {
                    $coachuserlist[$key]['user_name'] = $value['s_real_name'];
                } else {
                    $coachuserlist[$key]['user_name'] = '--';
                }

                if ($value['s_coach_name'] != '') {
                    $coachuserlist[$key]['coach_name'] = $value['s_coach_name'];
                } else {
                    $coachuserlist[$key]['coach_name'] = '--';
                }

                if ($value['s_coach_phone'] != '') {
                    $coachuserlist[$key]['coach_phone'] = $value['s_coach_phone'];
                } else {
                    $coachuserlist[$key]['coach_phone'] = '--';
                }

                if ($value['s_phone'] != '') {
                    $coachuserlist[$key]['user_phone'] = $value['s_phone'];
                } else {
                    $coachuserlist[$key]['user_phone'] = '--';
                }

                if ($value['lesson_name'] == null) {
                    $coachuserlist[$key]['lesson_name'] = '--';
                }

                if ($value['license_name'] == null) {
                    $coachuserlist[$key]['license_name'] = '--';
                }
            }
        }
        $coachuserlists = array('coachuserlist' => $coachuserlist, 'count' => $count, 'page' => $page);
        return $coachuserlists;
    }

    /**
     * 更新教练与学员之间的绑定状态
     *
     * @return  void
     * @author  wl
     * @date    Dec 15， 2016
     **/
    public function updateCoachBindStatus ($id, $status) {
        if (!$id && !$status) {
            return false;
        }
        $data = array('bind_status' => $status);
        $result = M('coach_user_relation')
            ->where('id = :rid')
            ->bind(['rid' => $id])
            ->data($data)
            ->fetchSql(false)
            ->save();
        if ($result) {
            return $result;
        } else {
            return false;
        }

    }

    /************************************************************** 
     * 
     *  使用特定function对数组中所有元素做处理 
     *  @param  string  &$array     要处理的字符串 
     *  @param  string  $function   要执行的函数 
     *  @return boolean $apply_to_keys_also     是否也应用到key上 
     *  @access public 
     * 
     *************************************************************/  
    public function arrayRecursive(&$array, $function, $apply_to_keys_also = false)  
    {  
        static $recursive_counter = 0;  
        if (++$recursive_counter > 1000) {  
            die('possible deep recursion attack');  
        }  
        foreach ($array as $key => $value) {  
            if (is_array($value)) {  
                arrayRecursive($array[$key], $function, $apply_to_keys_also);  
            } else {  
                $array[$key] = $function($value);  
            }  

            if ($apply_to_keys_also && is_string($key)) {  
                $new_key = $function($key);  
                if ($new_key != $key) {  
                    $array[$new_key] = $array[$key];  
                    unset($array[$key]);  
                }  
            }  
        }  
        $recursive_counter--;  
    } 

    /************************************************************** 
     * 
     *  将数组转换为JSON字符串（兼容中文） 
     *  @param  array   $array      要转换的数组 
     *  @return string      转换得到的json字符串 
     *  @access public 
     * 
     *************************************************************/  
    public function JSON($array) {  
        $this->arrayRecursive($array, 'urlencode', true);  
        $json = json_encode($array);  
        return urldecode($json);  
    }

}
?>
