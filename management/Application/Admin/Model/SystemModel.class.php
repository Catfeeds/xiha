<?php
namespace Admin\Model;
use Think\Model;
use Think\Page;

class SystemModel extends BaseModel {
  public $tableName = 'action';

// 6.短信列表
    /**
    * 获取短信信息
    *
    * @return void
    * @author
    **/
    public function getSmsLists () {
        $smslists = array();
        $condition = array(
                'is_read' => array('neq', 101)
            );
        $count = $this->table(C('DB_PREFIX').'sms_sender sms_sender')
            ->where($condition)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $smslist = $this->table(C('DB_PREFIX').'sms_sender sms_sender')
            ->where($condition)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('sms_sender.id DESC')
            ->fetchSql(false)
            ->select();
        if (!empty($smslist)) {
            foreach ($smslist as $key => $value) {
                $member_id = $value['member_id'];
                $member_type = $value['member_type'];

                $smslists[$key]['id'] = $value['id'];
                $smslists[$key]['user_type'] = $value['member_type'];

                switch ($member_type) {
                    case '1':
                        $member_type = '0';break; // student
                    case '2':
                        $member_type = '1';break; // coach
                    default:
                        $member_type = '';break;
                }

                if ($member_type === '0') { // student
                    $where = array(
                            'i_user_type' => 0,
                            'i_status' => 0,
                            'user.l_user_id' => $member_id,
                            'users_info.user_id' => array('gt', 0)
                        );
                    $student_info = $this->table(C('DB_PREFIX').'user user')
                        ->field(
                            'user.s_real_name as user_name,
                             user.s_phone as user_phone'
                        )
                        ->join(C('DB_PREFIX').'users_info users_info ON users_info.user_id = user.l_user_id', 'LEFT')
                        ->where($where)
                        ->find();
                    if (!empty($student_info)) {
                        $smslists[$key]['user_name'] = $student_info['user_name'];
                        $smslists[$key]['user_phone'] = $student_info['user_phone'];
                    } else {
                        $smslists[$key]['user_name'] = '--';
                        $smslists[$key]['user_phone'] = '--';
                    }

                } elseif ( $member_type === '1') { // coach
                    $where = array(
                            'i_user_type' => 1,
                            'i_status' => 0,
                            // 'user.l_user_id' => $member_id,
                            'coach.user_id' => array('gt', 0),
                            'coach.l_coach_id' => $member_id
                        );
                    $coach_info = $this->table(C('DB_PREFIX').'user user')
                        ->field(
                            's_coach_name as user_name,
                             s_coach_phone as user_phone'
                        )
                        ->join(C('DB_PREFIX').'coach coach ON coach.user_id = user.l_user_id', 'LEFT')
                        ->where($where)
                        ->find();
                    if ($coach_info != '') {
                        $smslists[$key]['user_name'] = $coach_info['user_name'];
                        $smslists[$key]['user_phone'] = $coach_info['user_phone'];
                    } else {
                        $smslists[$key]['user_name'] = '--';
                        $smslists[$key]['user_phone'] = '--';
                    }
                }
                $smslists[$key]['msg_type'] = $value['i_yw_type'];
                $smslists[$key]['msg_from'] = $value['s_from'] != '' ? $value['s_from'] : '--';
                $smslists[$key]['is_read'] = $value['is_read'];
                $smslists[$key]['msg_id'] = $value['i_jpush_msg_id'];
                $smslists[$key]['send_no'] = $value['i_jpush_sendno'];
                $smslists[$key]['send_time'] = $value['dt_sender'];
                $smslists[$key]['msg_content'] = $value['s_content'] != '' ? $value['s_content'] : '';
                $smslists[$key]['msg_beizhu'] = $value['s_beizhu'] != '' ? $value['s_beizhu'] : '--';
                if ($value['addtime'] != 0) {
                    $smslists[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $smslists[$key]['addtime'] = '--';
                }
            }

        }

        $smslists = array('smslist' => $smslists, 'count' => $count, 'page' => $page);
        return $smslists;
    }

    /**
     * 短信搜索
     *
     * @return  void
     * @author  wl
     * @date    Mar 09, 2017
     **/
    public function searchSmsLists ($param) {
        $map = array();
        $complex = array();
        $smslists = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_info'] == '') {
            // $complex['s_real_name'] = array('LIKE', $s_keyword);
            $complex['s_beizhu'] = array('LIKE', $s_keyword); // 标题
            $complex['s_content'] = array('LIKE', $s_keyword);
            $complex['_logic'] = 'OR';
        } else {
            $complex[$param['search_info']] = array('LIKE', $s_keyword);
        }
        $map['_complex'] = $complex;

        if ($param['is_read'] != 0) {
            $map['is_read'] = array('EQ', $param['is_read']);
        } else {
            $map['is_read'] = array('NEQ', 101);
        }

        if ($param['user_type'] != 0) {
            $map['member_type'] = array('EQ', $param['user_type']);
        }

        if ($param['msg_type'] != 0) {
            $map['i_yw_type'] = array('EQ', $param['msg_type']);
        }

        $count = $this->table(C('DB_PREFIX').'sms_sender sms_sender')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $smslist = $this->table(C('DB_PREFIX').'sms_sender sms_sender')
            ->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('sms_sender.id DESC')
            ->fetchSql(false)
            ->select();
        if (!empty($smslist)) {
            foreach ($smslist as $key => $value) {
                $member_id = $value['member_id'];
                $member_type = $value['member_type'];

                $smslists[$key]['id'] = $value['id'];
                $smslists[$key]['user_type'] = $value['member_type'];

                switch ($member_type) {
                    case '1':
                        $member_type = '0';break; // student
                    case '2':
                        $member_type = '1';break; // coach
                    default:
                        $member_type = '';break;
                }

                if ($member_type === '0') { // student
                    $where = array(
                            'i_user_type' => 0,
                            'i_status' => 0,
                            'user.l_user_id' => $member_id,
                            'users_info.user_id' => array('gt', 0)
                        );
                    $student_info = $this->table(C('DB_PREFIX').'user user')
                        ->field(
                            'user.s_real_name as user_name,
                             user.s_phone as user_phone'
                        )
                        ->join(C('DB_PREFIX').'users_info users_info ON users_info.user_id = user.l_user_id', 'LEFT')
                        ->where($where)
                        ->find();
                    if (!empty($student_info)) {
                        $smslists[$key]['user_name'] = $student_info['user_name'];
                        $smslists[$key]['user_phone'] = $student_info['user_phone'];
                    } else {
                        $smslists[$key]['user_name'] = '--';
                        $smslists[$key]['user_phone'] = '--';
                    }

                } elseif ( $member_type === '1') { // coach
                    $where = array(
                            'i_user_type' => 1,
                            'i_status' => 0,
                            // 'user.l_user_id' => $member_id,
                            'coach.user_id' => array('gt', 0),
                            'coach.l_coach_id' => $member_id
                        );
                    $coach_info = $this->table(C('DB_PREFIX').'user user')
                        ->field(
                            's_coach_name as user_name,
                             s_coach_phone as user_phone'
                        )
                        ->join(C('DB_PREFIX').'coach coach ON coach.user_id = user.l_user_id', 'LEFT')
                        ->where($where)
                        ->find();
                    if ($coach_info != '') {
                        $smslists[$key]['user_name'] = $coach_info['user_name'];
                        $smslists[$key]['user_phone'] = $coach_info['user_phone'];
                    } else {
                        $smslists[$key]['user_name'] = '--';
                        $smslists[$key]['user_phone'] = '--';
                    }
                }

                $smslists[$key]['msg_type'] = $value['i_yw_type'];
                $smslists[$key]['msg_from'] = $value['s_from'] != '' ? $value['s_from'] : '--';
                $smslists[$key]['is_read'] = $value['is_read'];
                $smslists[$key]['msg_id'] = $value['i_jpush_msg_id'];
                $smslists[$key]['send_no'] = $value['i_jpush_sendno'];
                $smslists[$key]['send_time'] = $value['dt_sender'];
                $smslists[$key]['msg_content'] = $value['s_content'] != '' ? $value['s_content'] : '';
                $smslists[$key]['msg_beizhu'] = $value['s_beizhu'] != '' ? $value['s_beizhu'] : '--';
                if ($value['addtime'] != 0) {
                    $smslists[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $smslists[$key]['addtime'] = '--';
                }
            }

        }

        $smslists = array('smslist' => $smslists, 'count' => $count, 'page' => $page);
        return $smslists;
    }

    /**
     * 获取教练手机号
     *
     * @return  void
     * @author  wl
     * @date    Mar 10, 2017
     **/
    public function getStudentInfoByPhone ($phone) {
        $where = array(
                'i_user_type' => 0,
                'i_status' => 0,
                'user.s_phone' => $phone,
                'users_info.user_id' => array('gt', 0)
            );
        $student_info = $this->table(C('DB_PREFIX').'user user')
            ->join(C('DB_PREFIX').'users_info users_info ON users_info.user_id = user.l_user_id')
            ->where($where)
            ->getField('l_user_id as user_id');
        if ($student_info != '') {
            return $student_info;
        } else {
            return '';
        }
    }

    /**
     * 获取教练手机号
     *
     * @return  void
     * @author  wl
     * @date    Mar 10, 2017
     **/
    public function getCoachInfoByPhone ($phone) {
        $where = array(
                'i_user_type' => 1,
                'i_status' => 0,
                's_coach_phone' => $phone,
                'coach.user_id' => array('gt', 0)
            );
        $coach_id = $this->table(C('DB_PREFIX').'coach coach')
            ->join(C('DB_PREFIX').'user user ON user.l_user_id = coach.user_id')
            ->where($where)
            ->getField('l_coach_id as user_id');
        if ($coach_id != '') {
            return $coach_id;
        } else {
            return '';
        }
    }

    /**
     * 删除id对应的短信
     *
     * @return  void
     * @author  wl
     * @date    Mar 09, 2017
     **/
    public function delSms ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $where = array('id' => $id);
        $data = array('is_read' => 101);
        $result = M('sms_sender')
            ->where($where)
            ->fetchSql(false)
            ->save($data);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }


// 1.系统行为模块
    /**
     * 系统行为的列表展示
     *
     * @return  void
     * @author  wl
     * @date    Nov 15, 2016
     **/
    public function getSystemAction () {
        $count = $this->table(C('DB_PREFIX').'action')
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $systemActionLists = array();
        $systemActionList = $this->table(C('DB_PREFIX').'action')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('id DESC')
            ->fetchSql(false)
            ->select();
        if (!empty($systemActionList)) {
            foreach ($systemActionList as $key => $value) {
                if ($value['name'] == '') {
                    $systemActionList[$key]['name'] = '--';
                }

                if ($value['title'] == '') {
                    $systemActionList[$key]['title'] = '--';
                }

                if ($value['log'] == '') {
                    $systemActionList[$key]['log'] = '--';
                }

                if ($value['add_time'] != 0) {
                    $systemActionList[$key]['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
                } else {
                    $systemActionList[$key]['add_time'] = '--';
                }

                if ($value['update_time'] != 0) {
                    $systemActionList[$key]['update_time'] = date('Y-m-d H:i:s', $value['update_time']);
                } else {
                    $systemActionList[$key]['update_time'] = '--';
                }
            }
        }
        $systemActionLists = array('systemActionList' => $systemActionList, 'count' => $count, 'page' => $page);
        return $systemActionLists;
    }

    /**
     * 系统行为的搜索
     *
     * @return  void
     * @author  wl
     * @date    Nov 15, 2016
     **/
    public function searchSystemAction ($param) {
        $map = array();
        $complex = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['action_info'] == '') {
            $complex['name'] = array('LIKE', $s_keyword);
            $complex['title'] = array('LIKE', $s_keyword);
            $complex['_logic'] = 'OR';
        } else {
            $complex[$param['action_info']] = array("LIKE", $s_keyword);
        }
        $map['_complex'] = $complex;
        $count = $this->table(C('DB_PREFIX').'action')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $systemActionLists = array();
        $systemActionList = $this->table(C('DB_PREFIX').'action')
            ->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('id DESC')
            ->fetchSql(false)
            ->select();
        if (!empty($systemActionList)) {
            foreach ($systemActionList as $key => $value) {
                if ($value['name'] == '') {
                    $systemActionList[$key]['name'] = '--';
                }

                if ($value['title'] == '') {
                    $systemActionList[$key]['title'] = '--';
                }

                if ($value['log'] == '') {
                    $systemActionList[$key]['log'] = '--';
                }

                if ($value['add_time'] != 0) {
                    $systemActionList[$key]['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
                } else {
                    $systemActionList[$key]['add_time'] = '--';
                }

                if ($value['update_time'] != 0) {
                    $systemActionList[$key]['update_time'] = date('Y-m-d H:i:s', $value['update_time']);
                } else {
                    $systemActionList[$key]['update_time'] = '--';
                }


            }
        }
        $systemActionLists = array('systemActionList' => $systemActionList, 'count' => $count, 'page' => $page);
        return $systemActionLists;
    }

    /**
     * 通过行为唯一标识来检测是否存在
     *
     * @return  void
     * @author  wl
     * @date    Nov 15, 2016
     **/
    public function checkActionInfo ($name) {
        if (!trim($name)) {
            return false;
        }
        $check_info = $this->table(C('DB_PREFIX').'action')
            ->fetchSql(false)
            ->where(array('name' => $name))
            ->find();
        if (!empty($check_info)) {
            return $check_info;
        } else {
            return array();
        }
    }

    /**
     * 通过系统行为id来获取系统行为信息
     *
     * @return  void
     * @author  wl
     * @date    Nov 15, 2016
     **/
    public function getActionInfoById ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $check_info = $this->table(C('DB_PREFIX').'action')
            ->fetchSql(false)
            ->where(array('id' => $id))
            ->find();
        if (!empty($check_info)) {
            return $check_info;
        } else {
            return array();
        }
    }

    /**
    * 设置系统行为的状态
    *
    * @return  void
    * @author  wl
    * @date    Nov 15, 2016
    **/
    public function setSystemActionStatus ($id, $status) {
        if (!is_numeric($id) && !is_numeric($status)) {
            return false;
        }
        $list = array();
        $data = array('status' => $status);
        $result = M('action')->where('id = :cid')
            ->bind(['cid' => $id])
            ->fetchSql(false)
            ->data($data)
            ->save();
        $list['id'] = $id;
        $list['status'] = $result;
        return $list;
    }

    /**
     * 删除单条系统行为
     *
     * @return  void
     * @author  wl
     * @date    Nov 15, 2016
     **/
    public function delSystemAction ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $result = $this->table(C('DB_PREFIX').'Action')
            ->where(array('id' => $id))
            ->fetchSql(false)
            ->save(array('status' => 2));
            // ->delete();
        return $result;
    }

    /**
     * 删除多条系统行为
     *
     * @return  void
     * @author  wl
     * @date    Nov 15, 2016
     **/
    public function delSystemActions ($id_arr) {
        if (empty($id_arr)) {
            return false;
        }
        $result = $this->table(C('DB_PREFIX').'Action')
            ->where(array('id' => array('in', $id_arr)))
            ->fetchSql(false)
            ->save(array('status' => 2));
            // ->delete();
        return $result;
    }

    /**
     * 恢复多条系统行为
     *
     * @return  void
     * @author  wl
     * @date    Nov 15, 2016
     **/
    public function recoverSystemActions ($id_arr) {
        if (empty($id_arr)) {
            return false;
        }
        $result = $this->table(C('DB_PREFIX').'Action')
            ->where(array('id' => array('in', $id_arr)))
            ->fetchSql(false)
            ->save(array('status' => 1));
            // ->delete();
        return $result;
    }

// 2.行为日志模块
    /**
     * 根据不同的登陆者id获取不同的列表
     *
     * @return  void
     * @author  wl
     * @date    Sep 13, 2016
     * @update  Nov 21, 2016
     * @update  Nov 25, 2016
     **/
    public function getActionLogList ($role_id, $school_id) {
        if ($role_id == 1 && $school_id == 0) {
            $count = $this->table(C('DB_PREFIX').'action_log al')
                ->join(C('DB_PREFIX').'action ac ON ac.id = al.action_id', 'LEFT')
                ->join(C('DB_PREFIX').'admin a ON a.id = al.user_id', 'LEFT')
                ->join(C('DB_PREFIX').'roles r ON r.l_role_id = a.role_id', 'LEFT')
                ->count();
            $Page = new Page($count, 10);
            $page = $this->getPage($count, 10);
            $action_infos = array();
            $action_info = $this->table(C('DB_PREFIX').'action_log al')
                ->join(C('DB_PREFIX').'action ac ON ac.id = al.action_id', 'LEFT')
                ->join(C('DB_PREFIX').'admin a ON a.id = al.user_id', 'LEFT')
                ->join(C('DB_PREFIX').'roles r ON r.l_role_id = a.role_id', 'LEFT')
                ->field('al.*, a.id as uid, a.content, ac.id as acid, ac.title, ac.name, r.l_role_id, r.s_rolename')
                ->order('al.id DESC')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();
        } elseif ($role_id != 1 && $school_id == 0) {
            // 嘻哈信息管理员/嘻哈测试者/嘻哈开发者/嘻哈客服部 拥有跟超级管理员同样的权限查看所有日志
            if(in_array($role_id, array(5,8,9,10))) {
                $condition = array(
                    //'a.role_id' => $role_id,
                    //'a.school_id' => array('eq', 0),
                );
                $count = $this->table(C('DB_PREFIX').'action_log al')
                    ->join(C('DB_PREFIX').'action ac ON ac.id = al.action_id', 'LEFT')
                    ->join(C('DB_PREFIX').'admin a ON a.id = al.user_id', 'LEFT')
                    ->join(C('DB_PREFIX').'roles r ON r.l_role_id = 1', 'LEFT')
                    ->where($condition)
                    ->count();
                $Page = new Page($count, 10);
                $page = $this->getPage($count, 10);
                $action_infos = array();
                $action_info = $this->table(C('DB_PREFIX').'action_log al')
                    ->join(C('DB_PREFIX').'action ac ON ac.id = al.action_id', 'LEFT')
                    ->join(C('DB_PREFIX').'admin a ON a.id = al.user_id', 'LEFT')
                    ->join(C('DB_PREFIX').'roles r ON r.l_role_id = 1', 'LEFT')
                    ->where($condition)
                    ->field('al.*, a.id as uid, a.content, ac.id as acid, ac.title, ac.name, r.l_role_id, r.s_rolename')
                    ->order('al.id DESC')
                    ->limit($Page->firstRow.','.$Page->listRows)
                    ->select();
            }else {
                $condition = array(
                    //'a.role_id' => $role_id,
                    'a.school_id' => array('eq', 0),
                );
                $count = $this->table(C('DB_PREFIX').'action_log al')
                    ->join(C('DB_PREFIX').'action ac ON ac.id = al.action_id', 'LEFT')
                    ->join(C('DB_PREFIX').'admin a ON a.id = al.user_id', 'LEFT')
                    ->join(C('DB_PREFIX').'roles r ON r.l_role_id > 0', 'LEFT')
                    ->where($condition)
                    ->count();
                $Page = new Page($count, 10);
                $page = $this->getPage($count, 10);
                $action_infos = array();
                $action_info = $this->table(C('DB_PREFIX').'action_log al')
                    ->join(C('DB_PREFIX').'action ac ON ac.id = al.action_id', 'LEFT')
                    ->join(C('DB_PREFIX').'admin a ON a.id = al.user_id', 'LEFT')
                    ->join(C('DB_PREFIX').'roles r ON r.l_role_id > 0', 'LEFT')
                    ->where($condition)
                    ->field('al.*, a.id as uid, a.content, ac.id as acid, ac.title, ac.name, r.l_role_id, r.s_rolename')
                    ->order('al.id DESC')
                    ->limit($Page->firstRow.','.$Page->listRows)
                    ->select();
            }

        } elseif ($role_id != 1 && $school_id != 0) {
            $condition = array(
                'a.school_id' => $school_id,
                // 'a.role_id' => $role_id,
            );
            $count = $this->table(C('DB_PREFIX').'action_log al')
                ->join(C('DB_PREFIX').'action ac ON ac.id = al.action_id', 'LEFT')
                ->join(C('DB_PREFIX').'admin a ON a.id = al.user_id', 'LEFT')
                ->join(C('DB_PREFIX').'roles r ON r.l_role_id > 0', 'LEFT')
                ->where($condition)
                ->count();
            $Page = new Page($count, 10);
            $page = $this->getPage($count, 10);
            $action_infos = array();
            $action_info = $this->table(C('DB_PREFIX').'action_log al')
                ->join(C('DB_PREFIX').'action ac ON ac.id = al.action_id', 'LEFT')
                ->join(C('DB_PREFIX').'admin a ON a.id = al.user_id', 'LEFT')
                ->join(C('DB_PREFIX').'roles r ON r.l_role_id > 0', 'LEFT')
                ->where($condition)
                ->field('al.*, a.id as uid, a.content, ac.id as acid, ac.title, ac.name, r.l_role_id, r.s_rolename')
                ->order('al.id DESC')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();
        }
        if ($action_info) {
            foreach ($action_info as $key => $value) {
                if ($value['create_time'] != 0) {
                    $action_info[$key]['create_time'] = date('Y-m-d H:i:s', $value['create_time']);
                } else {
                    $action_info[$key]['create_time'] = '--';
                }
            }
        }
        $action_infos = array('action_info' => $action_info, 'page' => $page, 'count' => $count);
        return $action_infos;
    }

    /**
     * 获取角色类别role表中
     *
     * @return  void
     * @author  wl
     * @date    Nov 25, 2016
     **/
    public function getRoleList ($role_id, $school_id) {
        if ($school_id == 0 && $role_id == 1) {
            $rolelist = $this->table(C('DB_PREFIX').'roles r')
                ->fetchSql(false)
                ->field('l_role_id, s_rolename')
                ->select();
        } elseif ($role_id != 1 && $school_id == 0) {
            $rolelist = $this->table(C('DB_PREFIX').'roles r')
                ->where(array('r.l_role_id' => $role_id))
                ->fetchSql(false)
                ->field('l_role_id, s_rolename')
                ->select();
        } elseif ($role_id != 1 && $school_id != 0) {
            $rolelist = $this->table(C('DB_PREFIX').'roles r')
                ->join(C('DB_PREFIX').'admin a ON a.role_id = r.l_role_id', 'LEFT')
                ->where(array('a.school_id' => $school_id))
                ->fetchSql(false)
                ->field('l_role_id, s_rolename')
                ->select();
        }
        if ($rolelist) {
            return $rolelist;
        } else {
            return array();
        }
    }

    /**
     * 根据搜索登陆者的相关日志
     *
     * @return  void
     * @author  wl
     * @date    Sep 13, 2016
     * @update  Nov 21, 2016
     * @update  Nov 25, 2016
     **/
    public function searchActionLogList ($param, $role_id, $school_id) {
        $map = array();
        $complex = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_info'] == '') {
            $complex['al.id'] = array('EQ', $param['s_keyword']);
            $complex['a.content'] = array('like', $s_keyword);
            $complex['ac.title'] = array('like', $s_keyword);
            $complex['_logic'] = 'OR';
        } else {
            if ($param['search_info'] == 'id') {
                $param['search_info'] = 'al.id';
                $complex[$param['search_info']] = array('eq', $param['s_keyword']);
            }
            if ($param['search_info'] == 'content') {
                $param['search_info'] = 'a.content';
                $complex[$param['search_info']] = array('like', $param['s_keyword']);
            }
            if ($param['search_info'] == 'title') {
                $param['search_info'] = 'ac.title';
                $complex[$param['search_info']] = array('like', $param['s_keyword']);
            }
        }
        $map['_complex'] = $complex;
        if ($param['role_id'] != '') {
            $map['r.l_role_id'] = array('eq', $param['role_id']);
        }
        if ($role_id == 1 && $school_id == 0) {
            $count = $this->table(C('DB_PREFIX').'action_log al')
                ->join(C('DB_PREFIX').'action ac ON ac.id = al.action_id', 'LEFT')
                ->join(C('DB_PREFIX').'admin a ON a.id = al.user_id', 'LEFT')
                ->join(C('DB_PREFIX').'roles r ON r.l_role_id = a.role_id', 'LEFT')
                ->where($map)
                ->count();
            $Page = new Page($count, 10, $param);
            $page = $this->getPage($count, 10, $param);
            $action_infos = array();
            $action_info = $this->table(C('DB_PREFIX').'action_log al')
                ->join(C('DB_PREFIX').'action ac ON ac.id = al.action_id', 'LEFT')
                ->join(C('DB_PREFIX').'admin a ON a.id = al.user_id', 'LEFT')
                ->join(C('DB_PREFIX').'roles r ON r.l_role_id = a.role_id', 'LEFT')
                ->where($map)
                ->field('al.*, a.id as uid, a.content, ac.id as acid, ac.title, ac.name, r.l_role_id, r.s_rolename')
                ->order('al.id DESC')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();
        } elseif ($role_id != 1 && $school_id == 0) {
            // 嘻哈信息管理员/嘻哈测试者/嘻哈开发者/嘻哈客服部 拥有跟超级管理员同样的权限查看所有日志
            if(in_array($role_id, array(5,8,9,10))) {
                $condition = array(
                    //'a.role_id' => $role_id,
                    //'a.school_id' => array('eq', 0),
                );
                $count = $this->table(C('DB_PREFIX').'action_log al')
                    ->join(C('DB_PREFIX').'action ac ON ac.id = al.action_id', 'LEFT')
                    ->join(C('DB_PREFIX').'admin a ON a.id = al.user_id', 'LEFT')
                    ->join(C('DB_PREFIX').'roles r ON r.l_role_id = 1', 'LEFT')
                    ->where($condition)
                    ->count();
                $Page = new Page($count, 10);
                $page = $this->getPage($count, 10);
                $action_infos = array();
                $action_info = $this->table(C('DB_PREFIX').'action_log al')
                    ->join(C('DB_PREFIX').'action ac ON ac.id = al.action_id', 'LEFT')
                    ->join(C('DB_PREFIX').'admin a ON a.id = al.user_id', 'LEFT')
                    ->join(C('DB_PREFIX').'roles r ON r.l_role_id = 1', 'LEFT')
                    ->where($condition)
                    ->field('al.*, a.id as uid, a.content, ac.id as acid, ac.title, ac.name, r.l_role_id, r.s_rolename')
                    ->order('al.id DESC')
                    ->limit($Page->firstRow.','.$Page->listRows)
                    ->select();
            }else {
                $condition = array(
                    //'a.role_id' => $role_id,
                    'a.school_id' => array('eq', 0),
                );
                $count = $this->table(C('DB_PREFIX').'action_log al')
                    ->join(C('DB_PREFIX').'action ac ON ac.id = al.action_id', 'LEFT')
                    ->join(C('DB_PREFIX').'admin a ON a.id = al.user_id', 'LEFT')
                    ->join(C('DB_PREFIX').'roles r ON r.l_role_id > 0', 'LEFT')
                    ->where($condition)
                    ->count();
                $Page = new Page($count, 10);
                $page = $this->getPage($count, 10);
                $action_infos = array();
                $action_info = $this->table(C('DB_PREFIX').'action_log al')
                    ->join(C('DB_PREFIX').'action ac ON ac.id = al.action_id', 'LEFT')
                    ->join(C('DB_PREFIX').'admin a ON a.id = al.user_id', 'LEFT')
                    ->join(C('DB_PREFIX').'roles r ON r.l_role_id > 0', 'LEFT')
                    ->where($condition)
                    ->field('al.*, a.id as uid, a.content, ac.id as acid, ac.title, ac.name, r.l_role_id, r.s_rolename')
                    ->order('al.id DESC')
                    ->limit($Page->firstRow.','.$Page->listRows)
                    ->select();
            }
        } elseif ($role_id != 1 && $school_id != 0) {
            $condition = array(
                'a.school_id' => $school_id,
                // 'a.role_id' => $role_id,
            );
            $count = $this->table(C('DB_PREFIX').'action_log al')
                ->join(C('DB_PREFIX').'action ac ON ac.id = al.action_id', 'LEFT')
                ->join(C('DB_PREFIX').'admin a ON a.id = al.user_id', 'LEFT')
                ->join(C('DB_PREFIX').'roles r ON r.l_role_id > 0', 'LEFT')
                ->where($map)
                ->where($condition)
                ->count();
            $Page = new Page($count, 10, $param);
            $page = $this->getPage($count, 10, $param);
            $action_infos = array();
            $action_info = $this->table(C('DB_PREFIX').'action_log al')
                ->join(C('DB_PREFIX').'action ac ON ac.id = al.action_id', 'LEFT')
                ->join(C('DB_PREFIX').'admin a ON a.id = al.user_id', 'LEFT')
                ->join(C('DB_PREFIX').'roles r ON r.l_role_id > 0', 'LEFT')
                ->where($map)
                ->where($condition)
                ->field('al.*, a.id as uid, a.content, ac.id as acid, ac.title, ac.name, r.l_role_id, r.s_rolename')
                ->order('al.id DESC')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();
        }

        if ($action_info) {
            foreach ($action_info as $key => $value) {
                if ($value['create_time'] != 0) {
                    $action_info[$key]['create_time'] = date('Y-m-d H:i:s', $value['create_time']);
                } else {
                    $action_info[$key]['create_time'] = '';
                }
            }
        }
        $action_infos = array('action_info' => $action_info, 'page' => $page, 'count' => $count);
        return $action_infos;

    }
    /**
    * 设置行为日志的状态
    *
    * @return  void
    * @author  wl
    * @date    Sep 19, 2016
    **/
    public function setActionLogStatus ($id, $status) {
        if (!$id) {
            return false;
        }
        $list = array();
        $data = array('status' => $status);
        $result = M('action_log')->where('id = :cid')
            ->bind(['cid' => $id])
            ->fetchSql(false)
            ->data($data)
            ->save();
        $list['status'] = $result;
        $list['id']         = $id;
        return $list;
    }

    /**
     * 删除单条日志记录
     *
     * @return  void
     * @author  wl
     * @date    Sep 13, 2016
     **/
    public function delActionLog ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $result = $this->where(array('id' => $id))
            ->fetchSql(false)
            ->save(array('status' => 2));
            // ->delete();
        return $result;
    }

    /**
     * 删除多条日志记录
     *
     * @return  void
     * @author  wl
     * @date    Sep 13, 2016
     **/
    public function delActionLogs ($id_arr) {
        if (empty($id_arr)) {
            return false;
        }
        $result = $this->where(array('id' => array('in', $id_arr)))
            ->fetchSql(false)
            ->save(array('status' => 2));
            // ->delete();
        return $result;
    }

// 3.系统标签
    /**
     * 获取系统标签的列表
     *
     * @return  void
     * @author  wl
     * @date    Oct 24, 2016
     **/
    public function getTagConfigList () {
        $count = $this->table(C('DB_PREFIX').'system_tag_config s')
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $tagconfiglists = array();
        $tagconfiglist = $this->table(C('DB_PREFIX').'system_tag_config s')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('s.order ASC, id DESC')
            ->fetchSql(false)
            ->select();
        if (!empty($tagconfiglist)) {
            foreach ($tagconfiglist as $key => $value) {
                if ($value['addtime'] != 0) {
                    $tagconfiglist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $tagconfiglist[$key]['addtime'] = '--';
                }
                if ($value['updatetime'] != 0) {
                    $tagconfiglist[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $tagconfiglist[$key]['updatetime'] = '--';
                }

            }
        }
        $tagconfiglists = array('tagconfiglist' => $tagconfiglist, 'count' => $count, 'page' => $page);
        return $tagconfiglists;
    }

    /**
     * 搜索标签
     *
     * @return  void
     * @author  wl
     * @date    Oct 24, 2016
     **/
    public function searchTagConfig ($param) {
        $map = array();
        $complex = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_info'] == '') {
            $complex['tag_name'] = array('like', $s_keyword);
            $complex['tag_slug'] = array('like', $s_keyword);
            $complex['_logic']  = 'OR';
        } else {
            $complex[$param['search_info']] = array('like', $s_keyword);
        }
        $map['_complex'] = $complex;

        if ($param['user_type'] != 0) {
            $map['user_type'] = array('EQ', $param['user_type']);
        }

        $count = $this->table(C('DB_PREFIX').'system_tag_config s')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $tagconfiglists = array();
        $tagconfiglist = $this->table(C('DB_PREFIX').'system_tag_config s')
            ->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('s.order ASC, id DESC')
            ->fetchSql(false)
            ->select();
        if (!empty($tagconfiglist)) {
            foreach ($tagconfiglist as $key => $value) {
                if ($value['addtime'] != 0) {
                    $tagconfiglist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $tagconfiglist[$key]['addtime'] = '--';
                }

                if ($value['updatetime'] != 0) {
                    $tagconfiglist[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $tagconfiglist[$key]['updatetime'] = '--';
                }
            }
        }
        $tagconfiglists = array('tagconfiglist' => $tagconfiglist, 'count' => $count, 'page' => $page);
        return $tagconfiglists;
    }

    /**
     * 获取单条标签信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 24, 2016
     **/
    public function getTagConfigById ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $tagconfiglist = $this->table(C('DB_PREFIX').'system_tag_config')
            ->where(array('id' => $id))
            ->find();
        if (!empty($tagconfiglist)) {
            if ($tagconfiglist['addtime'] != 0) {
                $tagconfiglist['addtime'] = date('Y-m-d H:i:s', $tagconfiglist['addtime']);
            } else {
                $tagconfiglist['addtime'] = '';
            }

            if ($tagconfiglist['updatetime'] != 0) {
                $tagconfiglist['updatetime'] = date('Y-m-d H:i:s', $tagconfiglist['updatetime']);
            } else {
                $tagconfiglist['updatetime'] = '';
            }
        } else {
            $tagconfiglist = array();
        }
        return $tagconfiglist;
    }

    /**
     * 删除系统标签
     *
     * @return  void
     * @author  wl
     * @date    Oct 24, 2016
     **/
    public function delTagConfig ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $result = $this->table(C('DB_PREFIX').'system_tag_config')
            ->where(array('id' => $id))
            ->fetchSql(false)
            ->delete();
        return $result;
    }

    /**
    * 设置系统标签状态
    *
    * @return  void
    * @author  wl
    * @date    Oct 24, 2016
    **/
    public function updateTagConfigOrder ($post) {
        if (empty($post)) {
            return 101; // 参数错误
        }
        if (isset($post['order'])) {
            if (!is_numeric($post['order'])) {
                return 102; //参数类型错误
            } else {
                $old_num = $this->table(C('DB_PREFIX').'system_tag_config')
                    ->where('id = :cid')
                    ->bind(['cid' => $post['id']])
                    ->getField('order');
                if ($post['order'] === $old_num) {
                    return 105; // 尚未做任何修改
                }
            }
        }
        $data['order'] = $post['order'];
        $tagconfig = D('system_tag_config');
        if ($res = $tagconfig->create($data)) {
            $result = $tagconfig->where('id = :cid')
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

// 4.用户自定义标签
    /**
     * 获取用户自定义标签列表
     *
     * @return  void
     * @author  wl
     * @date    Oct 24, 2016
     **/
    public function getUserTagList () {
        $count = $this->table(C('DB_PREFIX').'user_tag')
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $usertaglists = array();
        $usertaglist = $this->table(C('DB_PREFIX').'user_tag u')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('u.order ASC, id DESC')
            ->fetchSql(false)
            ->select();
        if (!empty($usertaglist)) {
            foreach ($usertaglist as $key => $value) {

                if ($value['addtime'] != 0) {
                    $usertaglist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $usertaglist[$key]['addtime'] = '--';
                }

                if ($value['updatetime'] != 0) {
                    $usertaglist[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $usertaglist[$key]['updatetime'] = '--';
                }

                if ($value['user_type'] == 1) {
                    $usertaglist[$key]['type_name'] = '学员';
                    $user_name = $this->table(C('DB_PREFIX').'user u')
                        ->where(array('l_user_id' => $value['user_id'], 'i_user_type' => 0, 'i_status' => 0))
                        ->field('s_username, s_real_name')
                        ->find();
                    if ($user_name['s_real_name'] != '') {
                        $usertaglist[$key]['user_name'] = $user_name['s_real_name'];
                    } else {
                        $usertaglist[$key]['user_name'] = $user_name['s_username'];
                    }
                } elseif ($value['user_type'] == 2) {
                    $usertaglist[$key]['type_name'] = '教练';
                    $user_name = $this->table(C('DB_PREFIX').'coach c')
                        ->where(array('l_coach_id' => $value['user_id']))
                        ->getField('s_coach_name');
                    $usertaglist[$key]['user_name'] = $user_name;
                } elseif ($value['user_type'] == 3) {
                    $usertaglist[$key]['type_name'] = '驾校';
                    $user_name = $this->table(C('DB_PREFIX').'school s')
                        ->where(array('l_school_id' => $value['user_id']))
                        ->getField('s_school_name');
                    $usertaglist[$key]['user_name'] = $user_name;
                }
            }
        }
        $usertaglists = array('usertaglist' => $usertaglist, 'count' => $count, 'page' => $page);
        return $usertaglists;
    }

    /**
     * 搜索自定义标签
     *
     * @return  void
     * @author  wl
     * @date    Oct 24, 2016
     **/
    public function searchUserTag ($param) {
        $map = array();
        $complex = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_info'] == '') {
          $complex['tag_name'] = array('like', $s_keyword);
          $complex['tag_slug'] = array('like', $s_keyword);
          $complex['_logic']  = 'OR';
        } else {
          $complex[$param['search_info']] = array('like', $s_keyword);
        }
        $map['_complex'] = $complex;

        if ($param['user_type'] != 0) {
          $map['user_type'] = array('EQ', $param['user_type']);
        }

        if ($param['is_system'] != 0) {
          $map['is_system'] = array('EQ', $param['is_system']);
        }

        $count = $this->table(C('DB_PREFIX').'user_tag')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $usertaglists = array();
        $usertaglist = $this->table(C('DB_PREFIX').'user_tag u')
            ->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('u.order ASC, id DESC')
            ->fetchSql(false)
            ->select();
        if ($usertaglist) {
            foreach ($usertaglist as $key => $value) {
                if ($value['addtime'] != 0) {
                    $usertaglist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $usertaglist[$key]['addtime'] = '--';
                }

                if ($value['updatetime'] != 0) {
                    $usertaglist[$key]['updatetime'] = date('Y-m-d H:i:s', $value['updatetime']);
                } else {
                    $usertaglist[$key]['updatetime'] = '--';
                }

                if ($value['user_type'] == 1) {
                    $usertaglist[$key]['type_name'] = '学员';
                    $user_name = $this->table(C('DB_PREFIX').'user u')
                        ->where(array('l_user_id' => $value['user_id'], 'i_user_type' => 0, 'i_status' => 0))
                        ->field('s_username, s_real_name')
                        ->find();
                    if ($user_name['s_real_name'] != '') {
                        $usertaglist[$key]['user_name'] = $user_name['s_real_name'];
                    } else {
                        $usertaglist[$key]['user_name'] = $user_name['s_username'];
                    }
                } elseif ($value['user_type'] == 2) {
                    $usertaglist[$key]['type_name'] = '教练';
                    $user_name = $this->table(C('DB_PREFIX').'coach c')
                        ->where(array('l_coach_id' => $value['user_id']))
                        ->getField('s_coach_name');
                    $usertaglist[$key]['user_name'] = $user_name;
                } elseif ($value['user_type'] == 3) {
                    $usertaglist[$key]['type_name'] = '驾校';
                    $user_name = $this->table(C('DB_PREFIX').'school s')
                        ->where(array('l_school_id' => $value['user_id']))
                        ->getField('s_school_name');
                    $usertaglist[$key]['user_name'] = $user_name;
                }
            }
        }
        $usertaglists = array('usertaglist' => $usertaglist, 'count' => $count, 'page' => $page);
        return $usertaglists;
    }

    /**
     * 删除自定义标签
     *
     * @return  void
     * @author  wl
     * @date    Oct 24, 2016
     **/
    public function delUserTag ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $result = $this->table(C('DB_PREFIX').'user_tag')
            ->where(array('id' => $id))
            ->fetchSql(false)
            ->delete();
        return $result;
    }

    /**
    * 设置自定义标签状态
    *
    * @return  void
    * @author  wl
    * @date    Oct 24, 2016
    **/
    public function updateUserTagOrder ($post) {
        if (empty($post)) {
            return 101; // 参数错误
        }
        if (isset($post['order'])) {
            if (!is_numeric($post['order'])) {
                return 102; //参数类型错误
            } else {
                $old_num = $this->table(C('DB_PREFIX').'user_tag')
                    ->where('id = :cid')
                    ->bind(['cid' => $post['id']])
                    ->getField('order');
                if ($post['order'] === $old_num) {
                    return 105; // 尚未做任何修改
                }
            }
        }
        $data['order'] = $post['order'];
        $tagconfig = D('user_tag');
        if ($res = $tagconfig->create($data)) {
            $result = $tagconfig->where('id = :cid')
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

// 5.用户账户支持配置管理

    /**
     * 获取用户账户支持配置列表
     *
     * @return  void
     * @author  wl
     * @date    Oct 25, 2016
     **/
    public function getPayAccountList () {
        $count = $this->table(C('DB_PREFIX').'pay_account_config')
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $payaccountlists = array();
        $payaccountlist = $this->table(C('DB_PREFIX').'pay_account_config p')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('p.order ASC, id DESC')
            ->fetchSql(false)
            ->select();
        if (!empty($payaccountlist)) {
            foreach ($payaccountlist as $key => $value) {
                if ($value['addtime'] != 0) {
                    $payaccountlist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $payaccountlist[$key]['addtime'] = '--';
                }
            }
        }
        $payaccountlists = array('payaccountlist' => $payaccountlist, 'count' => $count, 'page' => $page);
        return $payaccountlists;
    }

    /**
     * 搜索用户账户支持配置信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 25, 2016
     **/
    public function searchPayAccount ($param) {
        $map = array();
        $complex = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_info'] == '') {
            $complex['account_name'] = array('like', $s_keyword);
            $complex['account_slug'] = array('like', $s_keyword);
            $complex['account_description'] = array('like', $s_keyword);
            $complex['_logic'] = 'OR';
        } else {
            $complex[$param['search_info']] = array('like', $s_keyword);
        }
        $map['_complex'] = $complex;

        if ($param['is_open'] != 0) {
            $map['is_open'] = array('EQ', $param['is_open']);
        }

        if ($param['is_bank'] != 0) {
            $map['is_bank'] = array('EQ', $param['is_bank']);
        }

        $count = $this->table(C('DB_PREFIX').'pay_account_config')
            ->where($map)
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $payaccountlists = array();
        $payaccountlist = $this->table(C('DB_PREFIX').'pay_account_config p')
            ->where($map)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('p.order ASC, id DESC')
            ->fetchSql(false)
            ->select();
        if (!empty($payaccountlist)) {
            foreach ($payaccountlist as $key => $value) {
                if ($value['addtime'] != 0) {
                    $payaccountlist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $payaccountlist[$key]['addtime'] = '--';
                }
            }
        }
        $payaccountlists = array('payaccountlist' => $payaccountlist, 'count' => $count, 'page' => $page);
        return $payaccountlists;
    }

    /**
     * 获取单条账户配置信息(根据id)
     *
     * @return  void
     * @author  wl
     * @date    Oct 25, 2016
     **/
    public function getPayAccountById ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $payaccountlist = $this->table(C('DB_PREFIX').'pay_account_config')
            ->where(array('id' => $id))
            ->find();
        if (!empty($payaccountlist)) {
            if ($payaccountlist['addtime'] != 0) {
                $payaccountlist['addtime'] = date('Y-m-d H:i:s', $payaccountlist['addtime']);
            } else {
                $payaccountlist['addtime'] = '';
            }
        }
        return  $payaccountlist;
    }

    /**
     * 获取单条账户配置信息(根据id)
     *
     * @return  void
     * @author  wl
     * @date    Oct 25, 2016
     **/
    public function getPayAccountByName ($name) {
        if (!$name) {
            return false;
        }
        $payaccountlist = $this->table(C('DB_PREFIX').'pay_account_config')
            ->where(array('account_name' => $name))
            ->find();
        if (!empty($payaccountlist)) {
            if ($payaccountlist['addtime'] != 0) {
                $payaccountlist['addtime'] = date('Y-m-d H:i:s', $payaccountlist['addtime']);
            } else {
                $payaccountlist['addtime'] = '';
            }
        }
        return $payaccountlist;
    }


    /**
     * 删除用户账户配置信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 25, 2016
     **/
    public function delPayAccount ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $result = M('pay_account_config')
            ->where(array('id' => $id))
            ->fetchSql(false)
            ->save(array('is_open' => 2));
            // ->delete();
        return $result;
    }


    /**
    * 设置用户账户配置的排序状态
    *
    * @return  void
    * @author  wl
    * @date    Oct 25, 2016
    **/
    public function updatePayAccountOrder ($post) {
        if (empty($post)) {
            return 101; // 参数错误
        }
        if (isset($post['order'])) {
            if (!is_numeric($post['order'])) {
                return 102; //参数类型错误
            } else {
                $old_num = $this->table(C('DB_PREFIX').'pay_account_config')
                    ->where('id = :cid')
                    ->bind(['cid' => $post['id']])
                    ->getField('order');
                if ($post['order'] === $old_num) {
                    return 105; // 尚未做任何修改
                }
            }
        }
        $data['order'] = $post['order'];
        $payaccountconfig = D('pay_account_config');
        if ($res = $payaccountconfig->create($data)) {
            $result = $payaccountconfig->where('id = :cid')
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
     * 设置用户账户配置状态
     *
     * @return  void
     * @author  wl
     * @date    Oct 25, 2016
     **/
    public function setOpenStatus ($id, $status) {
        if (!is_numeric($id) || !isset($status)) {
            return false;
        }

        $list = array();
        $data = array('is_open' => $status);
        $result = M('pay_account_config')
            ->where(array('id' => $id))
            ->save($data);
        $list['id'] = $id;
        $list['res'] = $result;
        return $list;
    }






















}
?>