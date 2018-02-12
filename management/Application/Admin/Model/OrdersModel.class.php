<?php
namespace Admin\Model;

use Think\Model;
use Think\Page;

//这种模型类基本是直接操作数据库的，所以在命名规范上和数据表是对应的
class OrdersModel extends BaseModel {

    private $_link = array(
    );
    public $tableName = 'school_orders';

    // 1.报名驾校订单管理模块
    /**
     * 将班制表、驾校订单表和驾校表联合起来
     *
     * @param   int  $school_id (0:嘻哈后台，查看所有驾校的班制和订单)
     * @param   bool $filter 是否过滤测试学员手机的数据，默认是false，不过滤
     * @author  wl
     * @date    july 20, 2016
     * @update  july 21, 2016
     **/
    public function schoolOrdersList($school_id, $filter = false) {
        $order_lists = array();
        $map = array();
        $map['so_order_status'] = array('NEQ', 101);
        if ($school_id != 0) {
            $map['r.so_school_id'] = $school_id;
        }

        if ($filter) {
            $test_accs = $this->table(C('DB_PREFIX').'test_account acc')
            ->field('value')
            ->where(array('acc.field' => array('eq', 'stu_phone')))
            ->select();
            if ($test_accs && is_array($test_accs) && ! empty($test_accs)) {
                $filter_objs = array();
                foreach ($test_accs as $k => $v) {
                    // $v['value'] = '17355100856'
                    if (isset($v['value'])) {
                        $filter_objs[] = $v['value'];
                    }
                }
                if (! empty($filter_objs)) {
                    $map['r.so_phone'] = array('NOT IN', $filter_objs);
                }
            }
        }

        $count = $this->table(C('DB_PREFIX').'school_orders r')
            ->join(C('DB_PREFIX').'school_shifts s ON s.id = r.so_shifts_id', 'LEFT')
            ->join(C('DB_PREFIX').'school i ON i.l_school_id = r.so_school_id', 'LEFT')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $school_orders_lists = $this->table(C('DB_PREFIX').'school_orders r')
            ->join(C('DB_PREFIX').'school_shifts s ON s.id = r.so_shifts_id', 'LEFT')
            ->join(C('DB_PREFIX').'school i ON i.l_school_id = r.so_school_id', 'LEFT')
            ->field('r.*,s.id sh_id, so_shifts_id, sh_title, sh_type, r.addtime,  l_school_id, s_school_name')
            ->where($map)
            ->order('r.id DESC')
            ->fetchSql(false)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        if (!empty($school_orders_lists)) {
            foreach ($school_orders_lists as $key => $value) {

                if ($value['addtime'] != 0) {
                    $school_orders_lists[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $school_orders_lists[$key]['addtime'] = '--';
                }

                if ($value['cancel_time'] != 0) {
                    $school_orders_lists[$key]['cancel_time'] = date('Y-m-d H:i:s', $value['cancel_time']);
                } else {
                    $school_orders_lists[$key]['cancel_time'] = '--';
                }

                if ($value['cancel_type'] == 1) {
                    $school_orders_lists[$key]['cancel_type'] = '学员端';
                } else if ($value['cancel_type'] == 2) {
                    $school_orders_lists[$key]['cancel_type'] = '驾校';
                } else {
                    $school_orders_lists[$key]['cancel_type'] = '--';
                }

                if ($value['cancel_reason'] == '') {
                    $school_orders_lists[$key]['cancel_reason'] = '--';
                }

                if ($value['so_username'] == '') {
                    $school_orders_lists[$key]['so_username'] = '--';
                }

                if ($value['so_order_no'] == '') {
                    $school_orders_lists[$key]['so_order_no'] = '--';
                }

                if ($value['s_zhifu_dm'] == '') {
                    $school_orders_lists[$key]['s_zhifu_dm'] = '--';
                }

                if ($value['sh_title'] == null) {
                    $school_orders_lists[$key]['sh_title'] = '--';
                }

                if ($value['s_school_name'] == null) {
                    $school_orders_lists[$key]['s_school_name'] = '--';
                }
            }
        }
        $order_lists = array('order_lists' => $school_orders_lists, 'page' => $page, 'count' => $count);
        return $order_lists;
    }

    /**
     * 根据不同条件搜索相应的订单
     *
     * @param   int  $school_id (0:嘻哈后台，查看所有驾校的班制和订单)
     * @param   bool $filter 是否过滤测试学员手机的数据，默认是false，不过滤
     * @author  wl
     * @date    july 21, 2016
     * @update  july 22, 2016
     **/
    public function searchSchoolOrders($param, $school_id, $pageCount = 10, $filter = false) {
        $map = array();
        $complex = array();
        $order_lists = array();
        $keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_type'] == '') {
            $complex['r.id'] = array('EQ', $param['s_keyword']);
            $complex['so_username'] = array('like', $keyword);
            $complex['so_phone'] = array('like', $keyword);
            $complex['so_order_no'] = array('like', $keyword);
            $complex['s_zhifu_dm'] = array('like', $keyword);
            $complex['so_user_identity_id'] = array('like', $keyword);
            $complex['s_school_name'] = array('like', $keyword);
            $complex['_logic'] = 'OR';
        } else {
            if ($param['search_type'] == 'id') {
                $param['search_type'] = 'r.id';
                $complex[$param['search_type']] = array('EQ', $param['s_keyword']);
            } else {
                $complex[$param['search_type']] = array('like', $keyword);
            }
        }

        $map['_complex'] = $complex;
        if (intval($param['pay_type']) != 0) {
            $map['so_pay_type'] = array('EQ', intval($param['pay_type']));
        }

        if ($param['order_status'] != 0) {
            $map['so_order_status'] = array('EQ', $param['order_status']);
        } else {
            $map['so_order_status'] = array('NEQ', 101); // 101-订单被删除
        }

        if ($school_id != 0) {
            $map['r.so_school_id'] = $school_id;
        }

        if ($filter) {
            $test_accs = $this->table(C('DB_PREFIX').'test_account acc')
            ->field('value')
            ->where(array('acc.field' => array('eq', 'stu_phone')))
            ->select();
            if ($test_accs && is_array($test_accs) && ! empty($test_accs)) {
                $filter_objs = array();
                foreach ($test_accs as $k => $v) {
                    // $v['value'] = '17355100856'
                    if (isset($v['value'])) {
                        $filter_objs[] = $v['value'];
                    }
                }
                if (! empty($filter_objs)) {
                    $map['r.so_phone'] = array('NOT IN', $filter_objs);
                }
            }
        }

        $count = $this->table(C('DB_PREFIX').'school_orders r')
            ->join(C('DB_PREFIX').'school_shifts s ON s.id = r.so_shifts_id', 'LEFT')
            ->join(C('DB_PREFIX').'school i ON i.l_school_id = r.so_school_id', 'LEFT')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, $pageCount, $param);
        $page = $this->getPage($count, $pageCount, $param);
        $school_orders_lists = $this->table(C('DB_PREFIX').'school_orders r')
            ->join(C('DB_PREFIX').'school_shifts s ON s.id = r.so_shifts_id', 'LEFT')
            ->join(C('DB_PREFIX').'school i ON i.l_school_id = r.so_school_id', 'LEFT')
            ->where($map)
            ->field('r.*,s.id sh_id, so_shifts_id, sh_title, sh_type, r.addtime,  l_school_id, s_school_name')
            ->order('r.id DESC')
            ->fetchSql(false)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        if (!empty($school_orders_lists)) {
            foreach ($school_orders_lists as $key => $value) {

                if ($value['addtime'] != 0) {
                    $school_orders_lists[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $school_orders_lists[$key]['addtime'] = '--';
                }

                if ($value['cancel_time'] != 0) {
                    $school_orders_lists[$key]['cancel_time'] = date('Y-m-d H:i:s', $value['cancel_time']);
                } else {
                    $school_orders_lists[$key]['cancel_time'] = '--';
                }

                if ($value['cancel_type'] == 1) {
                    $school_orders_lists[$key]['cancel_type'] = '学员端';
                } else if ($value['cancel_type'] == 2) {
                    $school_orders_lists[$key]['cancel_type'] = '驾校';
                } else {
                    $school_orders_lists[$key]['cancel_type'] = '--';
                }

                if ($value['cancel_reason'] == '') {
                    $school_orders_lists[$key]['cancel_reason'] = '--';
                }

                if ($value['so_username'] == '') {
                    $school_orders_lists[$key]['so_username'] = '--';
                }

                if ($value['so_order_no'] == '') {
                    $school_orders_lists[$key]['so_order_no'] = '--';
                }

                if ($value['s_zhifu_dm'] == '') {
                    $school_orders_lists[$key]['s_zhifu_dm'] = '--';
                }

                if ($value['sh_title'] == null) {
                    $school_orders_lists[$key]['sh_title'] = '--';
                }

                if ($value['s_school_name'] == null) {
                    $school_orders_lists[$key]['s_school_name'] = '--';
                }
            }
        }
        $order_lists = array('order_lists' => $school_orders_lists, 'page' => $page, 'count' => $count);
        return $order_lists;
    }



    /**
     * 通过传过来的id获得相应的字段
     *
     * @return  void
     * @author  wl
     * @date    August 10, 2010
     **/
    public function getSchoolOrdersById ($oid) {
        if (!is_numeric($oid)) {
            return false;
        }
        $order_list = $this->table(C('DB_PREFIX').'school_orders r')
            ->join(C('DB_PREFIX').'school_shifts s ON s.id = r.so_shifts_id', 'LEFT')
            ->where('r.id = :oid')
            ->bind(['oid' => $oid])
            ->field('r.*, s.id sh_id, so_shifts_id, sh_title, sh_type, sh_license_name')
            ->find();
        if (!empty($order_list)) {

            if ($order_list['sh_type'] == 1) {
                $order_list['sh_type_name'] = '计时班';
            } else {
                $order_list['sh_type_name'] = '非计时班';
            }
        }
        return $order_list;
    }

    /**
     *  通过手机号从user表中获得用户的id
     *
     * @return  void
     * @author  wl
     * @date    july 21, 2016
     **/
    public function getUserInfoByUserTab($phone) {
        $user_info = $this->table(C('DB_PREFIX').'user u')
            //->join(C('DB_PREFIX').'users_info uf ON uf.user_id = u.l_user_id')
            ->where('s_phone = :p AND i_user_type = 0 AND i_status = 0')
            // i_status=0-正常用户 2-被删除的用户
            // i_user_type=0-学员 1-教练员
            ->bind(['p' => $phone])
            ->field('l_user_id, s_phone')
            ->fetchSql(false)
            ->find();
        return $user_info;
    }

    /**
     *  通过驾校id从user表中获得班制的相关字段
     *
     * @return  void
     * @author  wl
     * @date    july 21, 2016
     **/
    public function getSchoolShifts ($school_id) {
        $map = array();
        $complex = array();
        $complex['sh_school_id'] = array('eq', $school_id);
        $string = "( school_shifts.coach_id = 0 OR  school_shifts.coach_id IS NULL )";
        $complex['_string'] = $string;
        $complex['_logic'] = 'AND';
        $map = $complex;
        $shifts_list = $this->table(C('DB_PREFIX').'school_shifts school_shifts')
            ->field('sh_school_id, sh_title, sh_type, id sh_id, sh_type, sh_license_name')
            ->where($map)
            ->fetchSql(false)
            ->select();
        if (!empty($shifts_list)) {
            foreach ($shifts_list as $key => $value) {
                if ($value['sh_type'] == 1) {
                    $shifts_list[$key]['sh_type_name'] = '计时班';
                } else {
                    $shifts_list[$key]['sh_type_name'] = '非计时班';
                }
            }
        }
        return $shifts_list;
    }

    /**
     * 获取驾校订单中报名驾校成功的订单信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 17, 2016
     **/
    public function checkSchoolOrders ($phone){
        $map = array();
        $string = " ( so_pay_type = 2 AND so_order_status != 2) OR
            ( so_pay_type IN (1, 3, 4) AND so_order_status != 3) ";
        $map['_string'] = $string;
        $map['so_phone'] = array('EQ', $phone);
        $map['so_order_status'] = array('NEQ', 101);
        $res = $this->where($map)->fetchSql(False)->find();
        if($res) {
            return false;
        }
        return true;
        /*
        $checkSchoolOrders = $this->table(C('DB_PREFIX').'school_orders')
            ->where(array(
                'so_phone' => $phone,
                'so_pay_type' => 2,
                'so_order_status' => array('not in', '2, 101')
            ))
            ->fetchSql(false)
            ->find();
        return $checkSchoolOrders;
         */
    }

    /**
     * 获取订单状态
     *
     * @return  void
     * @author  wl
     * @date    Feb 21, 2017
     **/
    public function getSchoolOrdersStatus ($id) {
        $order_info = $this->table(C('DB_PREFIX').'school_orders')
            ->where(array('id' => $id))
            ->field('so_pay_type, so_order_status')
            ->find();
        return $order_info;

    }


    /**
     * 设置报名驾校订单的状态
     *
     * @return  void
     * @author  wl
     * @date    Dec 21, 2016
     **/
    public function setSchoolOrderStatus ($id, $status, $type, $school_id) {
        if (!$id && !$status && !$type) {
            return false;
        }
        $now_time = time();
        $data = array('so_order_status' => $status, 'addtime' => $now_time);
        if ($type == 2) {
            if ($status == 2) {
                $data = array('so_order_status' => 2, 'cancel_time' => $now_time, 'cancel_reason' => '后台取消', 'cancel_type' => 2, 'addtime' => $now_time);
            }
            // else if ($status == 3) {
            //     $data = array('so_order_status' => 3, 'so_pay_type' => 2); // 2：线下支付
            // }
        } else {
            if ($status == 3) {
                $data = array('so_order_status' => 3, 'cancel_time' => $now_time, 'cancel_reason' => '后台取消' , 'cancel_type' => 2, 'addtime' => $now_time);
            }
            // else if ($status == 1) {
            //     $data = array('so_order_status' => 3, 'so_pay_type' => 2); // 2：线下支付
            // }
        }
        if ( ($type == 2 && $status == 3)
              || (in_array($type, array(1, 3, 4)) && $status == 1))
        {

            $user_id = $this->table(C('DB_PREFIX').'school_orders orders')
                ->where(array('id' => $id))
                ->getField('so_user_id');

            $update_ok = M('users_info')
                ->where(array('user_id' => $user_id))
                ->fetchsql(false)
                ->save(array('school_id' => $school_id));
        }

        $result = M('school_orders')
            ->where('id = :oid')
            ->bind(['oid' => $id])
            ->data($data)
            ->fetchSql(false)
            ->save();
        if ($result) {
            return $result;
        } else {
            return false;
        }

    }

    // 2.预约学车模块
    /**
     * 根据不同的驾校id从study_orders表中获得列表展示数据
     *
     * @param   int     $school_id
     * @param   bool    $filter
     * @return  void
     * @author  wl
     * @date    july 22, 2016
     **/
    public function getStudyOrdersLists ($school_id, $filter = false) {
        $study_orders = array();
        $map = array();
        $condition = array();
        $map['i_status'] = array('NEQ', 101);
        $condition['i_status'] = array('EQ', 2);
        if ($school_id != 0) {
            $map['sc.l_school_id'] = $school_id;
            $condition['sc.l_school_id'] = $school_id;
        }

        if ($filter) {
            $test_accs = $this->table(C('DB_PREFIX').'test_account acc')
            ->field('value')
            ->where(array('acc.field' => array('eq', 'stu_phone')))
            ->select();
            if ($test_accs && is_array($test_accs) && ! empty($test_accs)) {
                $filter_objs = array();
                foreach ($test_accs as $k => $v) {
                    // $v['value'] = '17355100856'
                    if (isset($v['value'])) {
                        $filter_objs[] = $v['value'];
                    }
                }
                if (! empty($filter_objs)) {
                    $map['s.s_user_phone'] = array('NOT IN', $filter_objs);
                }
            }
        }

        $count = $this->table(C('DB_PREFIX').'study_orders s')
            ->join(C('DB_PREFIX').'coach c ON c.l_coach_id = s.l_coach_id', 'LEFT')
            ->join(C('DB_PREFIX').'school sc ON sc.l_school_id = c.s_school_name_id', 'LEFT')
            ->where($map)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $study_orders_lists = $this->table(C('DB_PREFIX').'study_orders s')
            ->join(C('DB_PREFIX').'coach c ON c.l_coach_id = s.l_coach_id', 'LEFT')
            ->join(C('DB_PREFIX').'school sc ON sc.l_school_id = c.s_school_name_id', 'LEFT')
            ->where($map)
            ->field('s_school_name, c.s_coach_phone, c.s_coach_name, i_status, deal_type, s.*')
            ->order('l_study_order_id DESC')
            ->fetchSql(false)
            ->limit($Page->firstRow . ',' .$Page->listRows)
            ->select();
        $service_time = $this->table(C('DB_PREFIX').'study_orders s')
            ->join(C('DB_PREFIX').'coach c ON c.l_coach_id = s.l_coach_id', 'LEFT')
            ->join(C('DB_PREFIX').'school sc ON sc.l_school_id = c.s_school_name_id', 'LEFT')
            ->where($condition)
            ->fetchSql(false)
            ->sum('i_service_time');
        if (!empty($study_orders_lists)) {
            foreach ($study_orders_lists as $key => $value) {

                if ($value['cancel_time'] != 0) {
                    $study_orders_lists[$key]['cancel_time'] = date('Y-m-d H:i:s', $value['cancel_time']);
                } else {
                    $study_orders_lists[$key]['cancel_time'] = '--';
                }
                if ($value['cancel_reason'] == '') {
                    $study_orders_lists[$key]['cancel_reason'] = '--';
                }

                if ($value['cancel_type'] == 1) {
                    $study_orders_lists[$key]['cancel_type'] = '学员端';
                } else if ($value['cancel_type'] == 2) {
                    $study_orders_lists[$key]['cancel_type'] = '教练端';
                } else if ($value['cancel_type'] == 3) {
                    $study_orders_lists[$key]['cancel_type'] = '后台';
                } else {
                    $study_orders_lists[$key]['cancel_type'] = '--';
                }
                if ($value['s_user_name'] == '') {
                    $study_orders_lists[$key]['s_user_name'] = '--';
                }
                if ($value['s_user_phone'] == '') {
                    $study_orders_lists[$key]['s_user_phone'] = '--';
                }
                if ($value['s_address'] == '') {
                    $study_orders_lists[$key]['s_address'] = '--';
                }
                if ($value['s_lisence_name'] == '') {
                    $study_orders_lists[$key]['s_lisence_name'] = '--';
                }
                if ($value['s_lesson_name'] == '') {
                    $study_orders_lists[$key]['s_lesson_name'] = '--';
                }
                if ($value['s_zhifu_dm'] == '') {
                    $study_orders_lists[$key]['s_zhifu_dm'] = '--';
                }
                if ($value['s_school_name'] == '') {
                    $study_orders_lists[$key]['s_school_name'] = '--';
                }

                if ($value['dt_order_time'] != '') {
                    if ($value['dt_order_time'] != 0) {
                        $study_orders_lists[$key]['dt_order_time'] = date('Y-m-d H:i:s', $value['dt_order_time']);
                    } else {
                        $study_orders_lists[$key]['dt_order_time'] = '--';
                    }
                } else {
                    $study_orders_lists[$key]['dt_order_time'] = '--';
                }

                // 获取身份证信息
                // $user_id = $value['l_user_id'];
                // $identity_id = $this->table(C('DB_PREFIX').'users_info')
                //     ->where('user_id = :uid')
                //     ->bind(['uid' => $user_id])
                //     ->getField('identity_id');
                // if ($identity_id) {
                //     $study_orders_lists[$key]['identity_id'] = $identity_id;
                // } else {
                //     $study_orders_lists[$key]['identity_id'] = '--';
                // }

                $appoint_time_id = $value['appoint_time_id'];
                //  获取预约时间
                $appoint_time = $this->table(C('DB_PREFIX').'coach_appoint_time')
                    ->where(array('id' => $appoint_time_id))
                    ->find();
                $time_config_id_arr = array();
                if ($appoint_time) {
                    $time_config_id_arr = array_filter(explode(',', $appoint_time['time_config_id']));
                    // 获取时间配置中的id
                    if ($time_config_id_arr) {
                        $time_config_time = array();
                        $time_config_arr = $this->table(C('DB_PREFIX').'coach_time_config')
                            ->where(array('id' => array('in', $time_config_id_arr)))
                            ->select();
                        if ($time_config_arr) {
                            foreach ($time_config_arr as $time_config_index => $time_config_value) {
                                $time_config_time[] = $time_config_value['start_time'].':00-'.$time_config_value['end_time'].':00';
                            }
                        }
                        $study_orders_lists[$key]['appoint_time_date'] = date("Y-m-d", strtotime($value['dt_appoint_time']));
                        $study_orders_lists[$key]['appoint_time'] = implode('<br/>', $time_config_time);
                        $study_orders_lists[$key]['appoint_time_list'] = date("Y-m-d", strtotime($value['dt_appoint_time'])).' <br/>'.implode('<br/>', $time_config_time);
                    } else {
                        $study_orders_lists[$key]['appoint_time_date'] = date("Y-m-d", strtotime($value['dt_appoint_time']));
                        $study_orders_lists[$key]['appoint_time_list'] = date("Y-m-d", strtotime($value['dt_appoint_time']));
                    }
                }
            }
        }
        $study_orders = array('study_orders' => $study_orders_lists, 'page' =>$page, 'count' => $count, 'total_service_time' => $service_time);
        return $study_orders;
    }



    /**
     * 搜索学车订单
     *
     * @param   int     $school_id
     * @param   bool    $filter  default: false
     * @return  mixed $study_orders  根据搜索的条件获得的学车预约列表
     * @author  wl
     * @date    july 22, 2016
     **/
    public function searchStudyOrderList($param, $school_id, $filter = false) {
        $map = array();
        $where = array();
        // $whereCondition = array();
        $complex = array();
        $study_orders = array();
        $keyword = '%'.$param['s_keyword'].'%';
        if (trim((string)$param['search_type']) == '') {
            $complex['l_study_order_id'] = array('eq', $param['s_keyword']);
            $complex['s_user_name'] = array('like', $keyword);
            $complex['s_order_no'] = array('like', $keyword);
            $complex['s_zhifu_dm'] = array('like', $keyword); // 第三方支付平台的订单号
            $complex['s_user_phone'] = array('like', $keyword);
            $complex['s.s_coach_phone'] = array('like', $keyword);
            $complex['s.s_coach_name'] = array('like', $keyword);
            $complex['sc.s_school_name'] = array('like', $keyword);
            // $string = " s_coach_phone LIKE '{$keyword}' OR s_coach_name LIKE '{$keyword}' ";
            // $complex['_string'] = $string;
            $complex['_logic'] = 'OR';
        } else {
            if ($param['search_type'] == 's_coach_phone') {
                $param['search_type'] = 's.s_coach_phone';
            }
            if ($param['search_type'] == 's_coach_name') {
                $param['search_type'] = 's.s_coach_name';
            }
            $complex[$param['search_type']] = array('like', $keyword);
            if ($param['search_type'] == 'l_study_order_id') {
                $complex[$param['search_type']] = array('EQ', $param['s_keyword']);
            }
        }
        $map['_complex'] = $complex;

        if ($filter) {
            $test_accs = $this->table(C('DB_PREFIX').'test_account acc')
            ->field('value')
            ->where(array('acc.field' => array('eq', 'stu_phone')))
            ->select();
            if ($test_accs && is_array($test_accs) && ! empty($test_accs)) {
                $filter_objs = array();
                foreach ($test_accs as $k => $v) {
                    // $v['value'] = '17355100856'
                    if (isset($v['value'])) {
                        $filter_objs[] = $v['value'];
                    }
                }
                if (! empty($filter_objs)) {
                    $map['s.s_user_phone'] = array('NOT IN', $filter_objs);
                }
            }
        }

        if (intval($param['deal_type']) != 0) {
            $map['deal_type'] = array('EQ', intval($param['deal_type']));
        }

        if (intval($param['i_status']) != 0) {
            $map['i_status'] = array('EQ', intval($param['i_status']));
        } else {
            $map['i_status'] = array('NEQ', 101); // 101-已删除订单
        }

        // 按预约日期搜索
        if ($param['appoint_time'] != 0) {
            $map['dt_appoint_time'] = array('LIKE', '%'.$param['appoint_time'].'%');
        }

        if ($school_id != 0) {
            $map['sc.l_school_id'] = $school_id;
        }

        $count = $this->table(C('DB_PREFIX').'study_orders s')
            ->join(C('DB_PREFIX').'coach c ON c.l_coach_id = s.l_coach_id', 'LEFT')
            ->join(C('DB_PREFIX').'school sc ON sc.l_school_id = c.s_school_name_id', 'LEFT')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $study_orders_lists = $this->table(C('DB_PREFIX').'study_orders s')
            ->join(C('DB_PREFIX').'coach c ON c.l_coach_id = s.l_coach_id', 'LEFT')
            ->join(C('DB_PREFIX').'school sc ON sc.l_school_id = c.s_school_name_id', 'LEFT')
            ->where($map)
            ->field('s_school_name, c.s_coach_phone, c.s_coach_name, i_status, deal_type, s.*')
            ->order('l_study_order_id DESC')
            ->fetchSql(false)
            ->limit($Page->firstRow . ',' .$Page->listRows)
            ->select();
        $service_time = $this->table(C('DB_PREFIX').'study_orders s')
            ->join(C('DB_PREFIX').'coach c ON c.l_coach_id = s.l_coach_id', 'LEFT')
            ->join(C('DB_PREFIX').'school sc ON sc.l_school_id = c.s_school_name_id', 'LEFT')
            ->where($map)
            ->where(array('s.i_status' => 2))
            ->fetchSql(false)
            ->sum('i_service_time');
        if (!empty($study_orders_lists)) {
            foreach ($study_orders_lists as $key => $value) {

                if ($value['cancel_time'] != 0) {
                    $study_orders_lists[$key]['cancel_time'] = date('Y-m-d H:i:s', $value['cancel_time']);
                } else {
                    $study_orders_lists[$key]['cancel_time'] = '--';
                }

                if ($value['cancel_reason'] == '') {
                    $study_orders_lists[$key]['cancel_reason'] = '--';
                }

                if ($value['cancel_type'] == 1) {
                    $study_orders_lists[$key]['cancel_type'] = '学员端';
                } else if ($value['cancel_type'] == 2) {
                    $study_orders_lists[$key]['cancel_type'] = '教练端';
                } else if ($value['cancel_type'] == 3) {
                    $study_orders_lists[$key]['cancel_type'] = '后台';
                } else {
                    $study_orders_lists[$key]['cancel_type'] = '--';
                }

                if ($value['s_user_name'] == '') {
                    $study_orders_lists[$key]['s_user_name'] = '--';
                }
                if ($value['s_user_phone'] == '') {
                    $study_orders_lists[$key]['s_user_phone'] = '--';
                }
                if ($value['s_address'] == '') {
                    $study_orders_lists[$key]['s_address'] = '--';
                }
                if ($value['s_lisence_name'] == '') {
                    $study_orders_lists[$key]['s_lisence_name'] = '--';
                }
                if ($value['s_lesson_name'] == '') {
                    $study_orders_lists[$key]['s_lesson_name'] = '--';
                }

                if ($value['s_zhifu_dm'] == '') {
                    $study_orders_lists[$key]['s_zhifu_dm'] = '--';
                }

                if ($value['s_school_name'] == '') {
                    $study_orders_lists[$key]['s_school_name'] = '--';
                }

                if ($value['dt_order_time'] != '') {
                    if ($value['dt_order_time'] != 0) {
                        $study_orders_lists[$key]['dt_order_time'] = date('Y-m-d H:i:s', $value['dt_order_time']);
                    } else {
                        $study_orders_lists[$key]['dt_order_time'] = '--';
                    }
                } else {
                    $study_orders_lists[$key]['dt_order_time'] = '--';
                }

                // 获取身份证信息
                // $user_id = $value['l_user_id'];
                // $identity_id = $this->table(C('DB_PREFIX').'users_info')
                //     ->where('user_id = :uid')
                //     ->bind(['uid' => $user_id])
                //     ->getField('identity_id');
                // if ($identity_id) {
                //     $study_orders_lists[$key]['identity_id'] = $identity_id;
                // } else {
                //     $study_orders_lists[$key]['identity_id'] = '--';
                // }

                $appoint_time_id = $value['appoint_time_id'];
                //  获取预约时间
                $appoint_time = $this->table(C('DB_PREFIX').'coach_appoint_time')
                    ->where(array('id' => $appoint_time_id))
                    ->find();
                $time_config_id_arr = array();
                if ($appoint_time) {
                    $time_config_id_arr = array_filter(explode(',', $appoint_time['time_config_id']));
                    // 获取时间配置中的id
                    if ($time_config_id_arr) {
                        $time_config_time = array();
                        $time_config_arr = $this->table(C('DB_PREFIX').'coach_time_config')
                            ->where(array('id' => array('in', $time_config_id_arr)))
                            ->select();
                        if ($time_config_arr) {
                            foreach ($time_config_arr as $time_config_index => $time_config_value) {
                                $time_config_time[] = $time_config_value['start_time'].':00-'.$time_config_value['end_time'].':00';
                            }
                        }
                        $study_orders_lists[$key]['appoint_time_date'] = date("Y-m-d", strtotime($value['dt_appoint_time']));
                        $study_orders_lists[$key]['appoint_time'] = implode('<br/>', $time_config_time);
                        $study_orders_lists[$key]['appoint_time_list'] = date("Y-m-d", strtotime($value['dt_appoint_time'])).' <br/>'.implode('<br/>', $time_config_time);
                    } else {
                        $study_orders_lists[$key]['appoint_time_date'] = date("Y-m-d", strtotime($value['dt_appoint_time']));
                        $study_orders_lists[$key]['appoint_time_list'] = date("Y-m-d", strtotime($value['dt_appoint_time']));
                    }
                }
            }
        }
        $study_orders = array('study_orders' => $study_orders_lists, 'page' =>$page, 'count' => $count, 'total_service_time' => $service_time);
        return $study_orders;
    }

    /**
     * 设置预约学车订单的状态
     *
     * @return  void
     * @author  wl
     * @date    Dec 21, 2016
     **/
    public function setStudyOrdersStatus ($id, $status) {
        if (!$id && !$status) {
            return false;
        }
        $now_time = time();
        $data = array('i_status' => $status);
        if ($status == 3) {
            $data = array('i_status' => 3, 'cancel_time' => $now_time, 'cancel_reason' => '后台取消', 'cancel_type' => 3);//3:后台取消
        }
        // else if ($status == 1) {
        //     $data = array('i_status' => 1, 'deal_type' => 2); // 2：线下支付
        // }
        $result = M('study_orders')
            ->where('l_study_order_id = :sid')
            ->bind(['sid' => $id])
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
     * 通过订单id获取订单状态和订单被预约的时间
     *
     * @return  void
     * @author  wl
     * @date    Fri 17, 2017
     **/
    public function getStudyOrdersStausById ($id) {
        $study_orders_info = $this->table(C('DB_PREFIX').'study_orders')
            ->where(array('l_study_order_id' => $id))
            // ->getField('i_status');
            ->field('i_status, deal_type, appoint_time_id, time_config_id, dt_appoint_time, l_coach_id, dt_order_time')
            ->find();
        $lists = array();
        $time_configs = array();
        if (!empty($study_orders_info)) {
            $coach_id = $study_orders_info['l_coach_id'];
            $order_time = $study_orders_info['dt_order_time'];
            $deal_type = $study_orders_info['deal_type'];
            // 获取教练被预约的时间和状态
            $dt_appoint_time_arr = array_filter(explode(' ', $study_orders_info['dt_appoint_time']));
            $dt_appoint_time = $dt_appoint_time_arr[0];
            $appoint_time_id = $study_orders_info['appoint_time_id'];
            $time_config_id_str = $this->table(C('DB_PREFIX').'coach_appoint_time')
                ->where(array('id' => $appoint_time_id))
                ->getField('time_config_id');
            if (!empty($time_config_id_str)) {
                $time_config_id_arr = array_filter(explode(',', $time_config_id_str));
                if (!empty($time_config_id_arr)) {
                    $coach_time_config = $this->table(C('DB_PREFIX').'coach_time_config')
                        ->where(array('id' => array('in', $time_config_id_arr)))
                        ->select();
                    if (!empty($coach_time_config)) {
                        foreach ($coach_time_config as $index => $time) {
                            $time_configs[] = $time['end_time'].':'.($time['end_minute'] < 10 ? '0'.$time['end_minute'] : $time['end_minute']) .':00';
                        }
                    }
                    $final_end_time = max($time_configs);
                }
            }
            $final_date = $dt_appoint_time.' '.$final_end_time;
            $lists['status'] = $study_orders_info['i_status'];
            $lists['end_time'] = strtotime($final_date);
            $lists['pay_type'] = $deal_type;
            // 取消订单需要多长时间
            $school_id = $this->table(C('DB_PREFIX').'coach c')
                ->join(C('DB_PREFIX').'school s ON s.l_school_id = c.s_school_name_id')
                ->where(array('c.l_coach_id' => $coach_id))
                ->getField('s.l_school_id');
            if (!empty($school_id)) {
                $cancel_in_advance = $this->table(C('DB_PREFIX').'school_config sc')
                    ->where(array('l_school_id' => $school_id))
                    ->getField('cancel_in_advance');
                if ($cancel_in_advance != '') {
                    $cancel_time = $cancel_in_advance;
                } else {
                    $cancel_time = 2;
                }
                $final_cancel_time = intval((time()-$order_time)/3600);
                $lists['gap_time'] = $final_cancel_time;
                $lists['cancel_time'] = $cancel_time;
            } else {
                $cancel_time = 2;
                $final_cancel_time = intval((time()-$order_time)/3600);
                $lists['gap_time'] = $final_cancel_time;
                $lists['cancel_time'] = $cancel_time;
            }
        }
        // var_dump($lists);exit;
        return $lists;
    }

    /**
     * 获取预约学车的订单状态
     *
     * @return  void
     * @author  wl
     * @date    Feb 21, 2017
     **/
    public function getStudyOrderStatus ($id) {
        $study_order_info = $this->table(C('DB_PREFIX').'study_orders')
            ->where(array('l_study_order_id' => $id))
            ->field('deal_type, i_status')
            ->find();
        return $study_order_info;
    }


    /**
     * 获取预约学车订单详情
     *
     * @param $order_param
     * @return $order_info
     * @date Mar 08, 2017
     */
    public function getAppointOrderInfo(Array $order_param) {
        if (! array_key_exists('order_id', $order_param)) {
            return array();
        }
        $where[] = array('study_orders.l_study_order_id' => $order_param['order_id']);
        if (array_key_exists('order_no', $order_param)) {
            $where[] = array('study_orders.s_order_no' => $order_param['order_no']);
        }
        $order_info = $this->table(C('DB_PREFIX').'study_orders study_orders')
            ->field(
                'study_orders.l_study_order_id as order_id,
                study_orders.s_order_no as order_no,
                study_orders.l_user_id as user_id,
                study_orders.s_user_name as user_name,
                study_orders.s_user_phone as user_phone,
                study_orders.l_coach_id as coach_id,
                study_orders.s_coach_name as coach_name,
                study_orders.s_coach_phone as coach_phone,
                study_orders.s_lisence_name as license_name,
                study_orders.s_lesson_name as lesson_name,
                school.s_school_name as school_name,
                school.l_school_id as school_id,
                study_orders.dc_money as money,
                study_orders.s_zhifu_dm as transaction_no,
                study_orders.dt_zhifu_time as pay_time,
                study_orders.dt_appoint_time as appoint_date,
                study_orders.dt_order_time as addtime,
                study_orders.time_config_id as time_config_id,
                study_orders.deal_type as pay_type,
                study_orders.i_status as order_status'
            )
            ->join(C('DB_PREFIX').'coach coach on coach.l_coach_id = study_orders.l_coach_id', 'LEFT')
            ->join(C('DB_PREFIX').'school school on school.l_school_id = coach.s_school_name_id', 'LEFT')
            ->where($where)
            ->find();
        if ($order_info) {
            $order_info['addtime_format'] = date('Y-m-d H:i:s', $order_info['addtime']);
            $order_info['appoint_date'] = explode(' ', $order_info['appoint_date'])[0];
            $school_config = $this->table(C('DB_PREFIX').'school_config school_config')
                ->field(
                    'school_config.cancel_in_advance as cancel_in_advance'
                )
                ->where(array('school_config.l_school_id' => $order_info['school_id']))
                ->find();
            if ($school_config) {
                $order_info['cancel_in_advance'] = $school_config['cancel_in_advance'];
            } else {
                $order_info['cancel_in_advance'] = 2; // 默认须提前2小时取消预约计时的订单
            }

            return $order_info;
        } else {
            return array();
        }
    }

}
