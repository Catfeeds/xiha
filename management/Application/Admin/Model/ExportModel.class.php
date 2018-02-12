<?php
namespace Admin\Model;
use Think\Model;
use Think\Page;
//这种模型类基本是直接操作数据库的，所以在命名规范上和数据表是对应的
class ExportModel extends BaseModel {
    public $tableName = 'school_orders';


    /**
     * 获取下载的学员信息
     *
     * @return  void
     * @author  wl
     * @date    Mar 27, 2017
     **/
    public function getStudentListDownload ($param, $school_id) {
        $map = array();
        $complex = array();
        $student_lists = array();
        $begin_num = intval(intval($param['begin_num']) - 1);
        $end_num = intval($param['end_num']);
        if ($school_id != 0) {
            $map['school.l_school_id'] = $school_id;
        }
        $map['i_user_type'] = array('EQ', 0);
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_type'] == '') {
            $complex['l_user_id'] = array('eq', $param['s_keyword']);
            $complex['s_real_name'] = array('LIKE', $s_keyword);
            $complex['s_phone'] = array('LIKE', $s_keyword);
            $complex['identity_id'] = array('LIKE', $s_keyword);
            $complex['school.s_school_name'] = array('LIKE', $s_keyword);
            $complex['_logic'] = 'OR';
        } else {
            $complex[$param['search_type']] = array('LIKE', $s_keyword);
            if ($param['search_type'] == 'l_user_id') {
                $complex[$param['search_type']] = array('eq', $param['s_keyword']);
            }

        }
        $map['_complex'] = $complex;

        if ($param['status'] != '') {
            $map['user.i_status'] = array('EQ', $param['status']);
        } else {
            $map['user.i_status'] = array('EQ', 0);
        }

        $student_list = $this->table(C('DB_PREFIX').'user as user')
            ->field(
                'user.l_user_id as l_user_id,
                 user.s_real_name as real_name,
                 user.s_username as s_username,
                 user.s_phone as s_phone,
                 user.i_from as i_from,
                 users_info.addtime as add_time,
                 users_info.updatetime as update_time,
                 users_info.sex as user_sex,
                 users_info.age as user_age,
                 users_info.identity_id as identity_id,
                 users_info.address as address,
                 users_info.license_num as license_num,
                 users_info.school_id as school_id,
                 users_info.lesson_id as lesson_id,
                 users_info.lesson_name as lesson_name,
                 users_info.license_id as license_id,
                 users_info.license_name as license_name,
                 users_info.learncar_status as learncar_status,
                 school.l_school_id as l_school_id,
                 school.s_school_name as s_school_name'
            )
            ->join(C('DB_PREFIX').'users_info as users_info ON users_info.user_id = user.l_user_id', 'LEFT')
            ->join(C('DB_PREFIX').'school as school ON school.l_school_id = users_info.school_id', 'LEFT')
            ->where($map)
            ->order('user.l_user_id DESC')
            ->limit($begin_num, $end_num)
            ->fetchSql(false)
            ->select();
        $push = array(
            'l_user_id' => '用户ID',
            'school_name' => '驾校名称',
            'real_name' => '用户姓名',
            's_username' => '用户昵称',
            'user_phone' => '手机号',
            'identity_id' => '身份证',
            'sex_name' => '性别',
            'user_age' => '年龄',
            'lesson_name' => '科目名称',
            'license_name' => '牌照名称',
            'learncar_status_name' => '学习状态',
            'address' => '地址',
            'license_num' => '领证次数',
            'app_from' => '来源',
            'add_time' => '注册时间',
            'update_time' => '最近更新',
        );
        if ($school_id != 0) {
            $push = array(
                'l_user_id' => '用户ID',
                'real_name' => '用户姓名',
                's_username' => '用户昵称',
                'user_phone' => '手机号',
                'identity_id' => '身份证',
                'sex_name' => '性别',
                'user_age' => '年龄',
                'lesson_name' => '科目名称',
                'license_name' => '牌照名称',
                'learncar_status_name' => '学习状态',
                'address' => '地址',
                'license_num' => '领证次数',
            );
        }
        if (!empty($student_list)) {
            foreach ($student_list as $key => $value) {
                $student_lists[$key]['l_user_id'] = $value['l_user_id'];
                if ($school_id == 0) {
                    if ($value['s_school_name'] != '') {
                        $student_lists[$key]['school_name'] = $value['s_school_name'];
                    } else {
                        $student_lists[$key]['school_name'] = '--';
                    }
                }

                if ($value['real_name'] != '') {
                    $student_lists[$key]['real_name'] = $value['real_name'];
                } else {
                    $student_lists[$key]['real_name'] = '--';
                }

                if ($value['s_username'] != '') {
                    $student_lists[$key]['s_username'] = $value['s_username'];
                } else {
                    $student_lists[$key]['s_username'] = '--';
                }

                if ($value['s_phone'] != '') {
                    $student_lists[$key]['user_phone'] = $value['s_phone'];
                } else {
                    $student_lists[$key]['user_phone'] = '--';
                }

                if ($value['identity_id'] != '') {
                    $student_lists[$key]['identity_id'] = $value['identity_id'];
                } else {
                    $student_lists[$key]['identity_id'] = '--';
                }

                if ($value['user_sex'] != '' && $value['user_sex'] != 0) {
                    if ($value['user_sex'] == 1) {
                        $student_lists[$key]['sex_name'] = '男';

                    } elseif ($value['user_sex'] == 2) {
                        $student_lists[$key]['sex_name'] = '女';

                    } else {
                        $student_lists[$key]['sex_name'] = '未知';
                    }

                } else {
                    $student_lists[$key]['sex_name'] = '未知';
                }

                if ($value['user_age'] != '') {
                    $student_lists[$key]['user_age'] = $value['user_age'];

                } else {
                    $student_lists[$key]['user_age'] = '未知';
                }

                if ($value['lesson_name'] != '' && $value['lesson_name'] != 0) {
                    $student_lists[$key]['lesson_name'] = $value['lesson_name'];
                } else {
                    $student_lists[$key]['lesson_name'] = '--';
                }

                if ($value['license_name'] != '' && $value['license_name'] != 0) {
                    $student_lists[$key]['license_name'] = $value['license_name'];
                } else {
                    $student_lists[$key]['license_name'] = '--';
                }

                if ($value['learncar_status'] != '') {
                    switch ($value['learncar_status']) {
                        case '0':
                            $student_lists[$key]['learncar_status_name'] = '未报名';
                            break;
                        case '1':
                            $student_lists[$key]['learncar_status_name'] = '科目一学习中';
                            break;
                        case '2':
                            $student_lists[$key]['learncar_status_name'] = '科目二学习中';
                            break;
                        case '3':
                            $student_lists[$key]['learncar_status_name'] = '科目三学习中';
                            break;
                        case '4':
                            $student_lists[$key]['learncar_status_name'] = '科目四学习中';
                            break;
                        case '100':
                            $student_lists[$key]['learncar_status_name'] = '已毕业';
                            break;
                        default:
                            $student_lists[$key]['learncar_status_name'] = '未知状态';
                            break;
                    }
                } else {
                    $student_lists[$key]['learncar_status_name'] = '未知状态';
                }

                if ($value['address'] != '' && $value['address'] != 0) {
                    $student_lists[$key]['address'] = $value['address'];
                } else {
                    $student_lists[$key]['address'] = '--';
                }

                if ($value['license_num'] != '' && $value['license_num'] != 0) {
                    $student_lists[$key]['license_num'] = $value['license_num'];
                } else {
                    $student_lists[$key]['license_num'] = '0';
                }

                if ($value['i_from'] != '') {
                    switch ($value['i_from']) {
                        case '0':
                            $student_lists[$key]['app_from'] = '苹果';
                            break;
                        case '1':
                            $student_lists[$key]['app_from'] = '安卓';
                            break;
                        case '2':
                            $student_lists[$key]['app_from'] = '线下';
                            break;
                        default:
                            $student_lists[$key]['app_from'] = '未知来源';
                            break;
                    }
                } 
                
                if ($school_id == 0) {

                    if ($value['add_time'] != 0 && $value['add_time'] != '') {
                        $student_lists[$key]['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
                    } else {
                        $student_lists[$key]['add_time'] = '--';
                    }

                    if ($value['update_time'] != 0 && $value['update_time'] != '') {
                        $student_lists[$key]['update_time'] = date('Y-m-d H:i:s', $value['update_time']);
                    } else {
                        $student_lists[$key]['update_time'] = '--';
                    }

                }
            }

        }
        $result = array_unshift($student_lists, $push);
        return $student_lists;

    }
    



    /**
     * 根据搜索的条件下载对应的excel文档
     *
     * @return  void
     * @author  wl
     * @date    Mar 06, 2017
     **/
    public function getDownloadSchoolOrders ($param, $school_id) {
        $map = array();
        $complex = array();
        $order_lists = array();
        $begin_num = intval(intval($param['begin_num']) - 1);
        $end_num = $param['end_num'];
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
        $order_list = $this->table(C('DB_PREFIX').'school_orders r')
            ->join(C('DB_PREFIX').'school_shifts ss ON ss.id = r.so_shifts_id', 'LEFT')
            ->join(C('DB_PREFIX').'school s ON s.l_school_id = r.so_school_id', 'LEFT')
            ->limit($begin_num.','.$end_num)
            ->fetchSql(false)
            ->where($map)
            ->field('r.id, s_school_name, so_username, so_phone, so_original_price, so_final_price, so_total_price, so_order_no, s_zhifu_dm, sh_title, sh_type, so_pay_type, so_order_status, so_user_identity_id, free_study_hour, so_licence,  r.addtime ')
            ->order('r.id desc')
            ->select();
        $push_array = array(
            'id' => '订单ID',
            'school_name' => '驾校名称',
            'user_name' => '学员姓名',
            'user_phone' => '学员手机',
            'original_price' => '原始价格（元）',
            'final_price' => '最终价格（元）',
            'total_price' => '实付价格（元）',
            'shifts_title' => '班制名称',
            'shifts_type_name' => '班制类型',
            'pay_type_name' => '支付类型',
            'order_status_name' => '订单状态',
            'user_identity' => '用户身份证',
            'free_hour' => '免费时长（小时）',
            'licence_name' => '牌照',
            'addtime' => '添加时间',
        );
        if ($school_id != 0) {
            $push_array = array(
                'id' => '订单ID',
                'user_name' => '学员姓名',
                'user_phone' => '学员手机',
                'original_price' => '原始价格（元）',
                'final_price' => '最终价格（元）',
                'total_price' => '实付价格（元）',
                'shifts_title' => '班制名称',
                'shifts_type_name' => '班制类型',
                'pay_type_name' => '支付类型',
                'order_status_name' => '订单状态',
                'user_identity' => '用户身份证',
                'free_hour' => '免费时长（小时）',
                'licence_name' => '牌照',
                'addtime' => '添加时间',
            );
        }
        foreach ($order_list as $key => $value) {
            if ($value['id'] != '') {
                $order_lists[$key]['id'] = $value['id'];
            }

            if ($value['s_school_name'] != '') {
                $order_lists[$key]['school_name'] = $value['s_school_name'];
            } else {
                $order_lists[$key]['school_name'] = '--';
            }

            if ($value['so_username'] != '') {
                $order_lists[$key]['user_name'] = $value['so_username'];
            } else {
                $order_lists[$key]['user_name'] = '--';
            }

            if ($value['so_phone'] != '') {
                $order_lists[$key]['user_phone'] = $value['so_phone'];
            } else {
                $order_lists[$key]['user_phone'] = '--';
            }

            if ($value['so_original_price'] != '') {
                $order_lists[$key]['original_price'] = $value['so_original_price'];
            } else {
                $order_lists[$key]['original_price'] = '--';
            }

            if ($value['so_final_price'] != '') {
                $order_lists[$key]['final_price'] = $value['so_final_price'];
            } else {
                $order_lists[$key]['final_price'] = '--';
            }

            if ($value['so_total_price'] != '') {
                $order_lists[$key]['total_price'] = $value['so_total_price'];
            } else {
                $order_lists[$key]['total_price'] = '--';
            }

            if ($value['sh_title'] != '') {
                $order_lists[$key]['shifts_title'] = $value['sh_title'];
            } else {
                $order_lists[$key]['shifts_title'] = '--';
            }

            if ($value['sh_type'] == '1') {
                $order_lists[$key]['shifts_type_name'] = '计时班';
            } else {
                $order_lists[$key]['shifts_type_name'] = '非计时班';
            }

            if ($value['so_pay_type'] == '1') {
                $order_lists[$key]['pay_type_name'] = '支付宝支付';

            } else if ($value['so_pay_type'] == '2') {
                $order_lists[$key]['pay_type_name'] = '线下支付';

            } else if ($value['so_pay_type'] == '3') {
                $order_lists[$key]['pay_type_name'] = '微信支付';

            } else if ($value['so_pay_type'] == '4') {
                $order_lists[$key]['pay_type_name'] = '银联支付';

            } else {
                $order_lists[$key]['pay_type_name'] = '其他支付形式';
            }

            if ($value['so_pay_type'] == '2') {
                if ($value['so_order_status'] == '1') {
                    $order_lists[$key]['order_status_name'] = '未付款';

                } elseif ($value['so_order_status'] == '2') {
                    $order_lists[$key]['order_status_name'] = '已取消';

                } elseif ($value['so_order_status'] == '3') {
                    $order_lists[$key]['order_status_name'] = '已付款';

                } elseif ($value['so_order_status'] == '4') {
                    $order_lists[$key]['order_status_name'] = '退款中';

                }
            } else {
                if ($value['so_order_status'] == '1') {
                    $order_lists[$key]['order_status_name'] = '已付款';

                } elseif ($value['so_order_status'] == '2') {
                    $order_lists[$key]['order_status_name'] = '退款中';

                } elseif ($value['so_order_status'] == '3') {
                    $order_lists[$key]['order_status_name'] = '已取消';

                } elseif ($value['so_order_status'] == '4') {
                    $order_lists[$key]['order_status_name'] = '未付款';

                }
            }

            if ($value['so_order_status'] == '101') {
                $order_lists[$key]['order_status_name'] = '已删除';
            }

            if ($value['so_user_identity_id'] != '') {
                $order_lists[$key]['user_identity'] = $value['so_user_identity_id'];
            } else {
                $order_lists[$key]['user_identity'] = '--';
            }

            if ($value['free_study_hour'] == '-1') {
                $order_lists[$key]['free_hour'] = '不限时长';
            } else {
                $order_lists[$key]['free_hour'] = $value['free_study_hour'];
            }

            if ($value['so_licence'] != '') {
                $order_lists[$key]['licence_name'] = $value['so_licence'];
            } else {
                $order_lists[$key]['licence_name'] = '--';
            }

            if ($value['addtime'] != '') {
                $order_lists[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
            } else {
                $order_lists[$key]['addtime'] = '--';
            }
        }
        $result = array_unshift($order_lists, $push_array);
        return $order_lists;
    }




















}
