<?php
    // 首页
defined('BASEPATH') OR exit('No direct script access allowed');

class Muser extends CI_Model {

    public $user_tablename = 'cs_user';
    public $userinfo_tablename = 'cs_users_info';
    public $shiftsorder_tablename = "cs_school_orders";

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('mcoach');
    }

    public function getUserList($wherecondition, $start, $limit)
    {
        $query = $this->db->where($wherecondition)->order_by('l_user_id', 'DESC')->get($this->user_tablename, $limit, $start);
        foreach($query->result() as $key => $value) {
            $query->result()[$key]->addtime = date('Y-m-d H:i:s', $value->addtime);
            $query->result()[$key]->user_info = $this->getUserInfoByCondition($this->userinfo_tablename, ['user_id'=>$value->l_user_id]);
        }
        return ['list'=>$query->result()];
    }

    public function addUser($data, $_data) {
        if( ! isset($data['addtime'])) {
            $data['addtime'] = time();
        }
        if( ! isset($_data['addtime'])) {
            $_data['addtime'] = time();
        }
        $this->db->insert($this->user_tablename, $data);
        $user_id = $this->db->insert_id();
        $_data['user_id'] = $user_id;
        $this->addUsersInfo($_data);
        return $user_id;
    }

    // 添加用户详细信息
    private function addUsersInfo($data) {
        $this->db->insert($this->userinfo_tablename, $data);
        return $this->db->insert_id();
    }

    public function editUser($data, $_data) {
        $this->db->where(['l_user_id' => $data['l_user_id']])->update($this->user_tablename, $data);
        $users_info = $this->db->get_where($this->userinfo_tablename, ['user_id' => $data['l_user_id']]);
        if($users_info->row_array()) {
            return $this->db->where(['user_id' => $data['l_user_id']])->update($this->userinfo_tablename, $_data);
        } else {
            $_data['user_id'] = $data['l_user_id'];
            return $this->addUsersInfo($_data);
        }
    }

    // 编辑拉黑在线状态
    public function editUserStatus($data, $wherecondition) {
        return $this->db->where($wherecondition)->update($this->user_tablename, $data);
    }
    
    // 删除学员
    public function delInfo($tablename,$_tablename, $wherecondition) {
        $this->db->trans_start();
        $this->db->query('DELETE FROM '.$tablename.' WHERE `l_user_id` = '.$wherecondition['l_user_id']);
        $this->db->query('DELETE FROM '.$_tablename.' WHERE `user_id` = '.$wherecondition['l_user_id']);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    // 根据条件获取用户信息
    public function getUserInfoByCondition($tablename, $wherecondition) {
        $query = $this->db->get_where($tablename, $wherecondition);
        return $query->row_array();
    }

    public function getUserInfo($id) 
    {
        $student_info = $this->db
            ->select(
                'user.l_user_id,
                 user.s_username,
                 user.s_real_name,
                 user.s_phone,
                 user.i_user_type,
                 user.i_status,
                 user.i_from,
                 info.sex,
                 info.age,
                 info.identity_id,
                 info.address,
                 info.user_photo,
                 info.license_num,
                 info.school_id,
                 info.lesson_id,
                 info.lesson_name,
                 info.license_id,
                 info.license_name,
                 info.province_id,
                 info.city_id,
                 info.area_id,
                 info.learncar_status'
            )
            ->from("{$this->user_tablename} as user")
            ->join("{$this->userinfo_tablename} as info", 'user.l_user_id=info.user_id' , 'left')
            ->where(array('l_user_id'=>$id))
            ->get()
            ->row_array();
        if ( ! empty($student_info)) {
            $school_info = $this->mschool->getSchoolInfo($student_info['school_id']);
            $school_name = '';
            if ( ! empty($school_info)) {
                $school_name = $school_info['s_school_name'];
            }
            $student_info['school_name'] = $school_name;
            $student_info['http_user_photo'] = $this->mbase->buildUrl($student_info['user_photo']);
        }
        return $student_info;
    }

    /**
     * 获取驾校下的学员
     * @param  int  $school_id 驾校ID
     *
     * @return void
     **/
    public function getUserBySchoolId($school_id)
    {
        if ($school_id == 0) {
            return [];
        }

        $map = [];
        $map['school_id'] = $school_id;
        $map['i_status'] = 0;
        $map['i_user_type'] = 0;
        $map['order.so_school_id'] = $school_id;
        $userlist = $this->db
            ->select(
                'order.so_user_id,
                 user.l_user_id,
                 user.s_username,
                 user.s_real_name,
                 user.s_phone'
            )
            ->from("{$this->user_tablename} as user")
            ->join("{$this->userinfo_tablename} as info", "info.user_id = user.l_user_id", "LEFT")
            ->join("{$this->shiftsorder_tablename} as order", "order.so_user_id = user.l_user_id", "LEFT")
            ->where($map)
            ->where_not_in('so_order_status', [101])
            ->distinct('order.so_user_id')
            ->get()
            ->result_array();
        $list = [];
        if ( ! empty($userlist)) {
            foreach ($userlist as $index => $user) {
                $list[$index]['l_user_id'] = $user['l_user_id'];
                $user_name = $user['s_username'] != '' ? $user['s_username'] : $user['s_real_name'];
                if ($user_name == '') {
                    $user_name = "嘻哈用户". substr($user['s_phone'], -4, -4);
                }
                $list[$index]['user_name'] = $user_name;
                $list[$index]['user_phone'] = $user['s_phone'];
            }
        }
        return $list;
    }

    // 修改密码
    public function changeUserPass($wherecondition, $data) {
        return $this->db->where($wherecondition)->update($this->user_tablename, $data);
    }

    //  检测身份证是否已经注册
    public function isIdentityRegistered($identity_id)
    {
        if (!isset($identity_id)) {
            return false;
        }
        $res = $this->db->from("{$this->user_tablename} as u")
            ->join("{$this->userinfo_tablename} as uf", 'u.l_user_id=uf.user_id', 'left')
            ->where(array('u.i_status'=>0,'u.i_user_type'=>0,'uf.identity_id'=>$identity_id))
            ->get()
            ->row_array();
        if ($res) {
            return true;//该身份证已注册
        } else {
            return false;//该身份证未注册
        }
    }

    //  检测手机号码是否已经注册
    public function isPhoneRegistered($s_phone)
    {
        if (!isset($s_phone)) {
            return false;
        }
        $res = $this->db->from($this->user_tablename)
            ->where(array('s_phone'=>$s_phone,'i_status'=>0,'i_user_type'=>0))
            ->get()
            ->row_array();
        if ($res) {
            return true;//该号码已注册
        } else {
            return false;//该号码未注册
        }
    }

}
?>