<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mbase extends CI_Model {

    public $coach_tablename = 'cs_coach';
    public $school_tablename = 'cs_school';
    protected $_permitted_type = [
        'stuimg',
        'stufp',
        'coachimg',
        'examinerimg',
        'examinerfp',
        'securityguardimg',
        'securityguradfp',
        'vehimg',
        'outletsimg',
        'occupationimg',
        'voiceprintimg',
        'epdfimg',
        'onlineimg',
        'classroomimg',
        'simulation',
        'video',
        'schoolthumb',
        'schoollicence',
        'userthumb',
        'xihaApp',
        'cars',
        'ads',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->config->load('upload');
        $this->load->library('upload');
        $this->load->model('madmin');
        $this->load->database();
    }

    public function loginauth()
    {
        if(!$this->session->loginauth) {
            redirect(base_url('admin/login'), 'location', 301);
            exit();
        }
    }

    /**
     * 从登录session信息中，获取school_id
     *
     * @param $loginauth
     * eg:
     * $loginauth "436|xihaschool|2|2|嘻哈驾校蜀山分校|5426"
     * @school_id 5426
     *
     * @return $school_id
     */
    public function getSchoolidFromLoginauth ($loginauth)
    {
        $loginauth_arr = explode('|', $loginauth);
        if ($loginauth_arr[5]) {
            $school_id =  $loginauth_arr[5];
            return $school_id;
        }

        return NULL;
    }

    /**
     * 从登录session信息中，获取role_id
     *
     * @param $role
     * eg:
     * $loginauth "436|xihaschool|2|2|嘻哈驾校蜀山分校|5426"
     * @role 1
     *
     * @return $role_id
     */
    public function getRoleIdFromLoginauth($loginauth)
    {
        $loginauth_arr = explode('|', $loginauth);
        if ($loginauth_arr[3]) {
            $role_id =  $loginauth_arr[3];
            return $role_id;
        }

        return NULL;
    }

    // 获取数据页码和总数（通用）
    public function getPageNum($tablename, $limit) {
        $count = $this->db->count_all($tablename);
        return ['pn'=>(int) ceil($count / $limit), 'count'=>$count];
    }

    // 根据条件获取单个信息
    public function getInfoByCondition($tablename, $wherecondition) {
        $query = $this->db->get_where($tablename, $wherecondition);
        return $query->row_array();
    }

    // 获取条件下的数据页码和总数（通用）
    public function getPageNumByCondition($tablename, $wherecondition, $limit) {
        $count = $this->db->where($wherecondition)->count_all_results($tablename);
        return ['pn'=>(int) ceil($count / $limit), 'count'=>$count];
    }

    // 获取牌照配置
    public function getLicenseConfigList() {
        $query = $this->db->order_by('order', 'desc')->get('cs_license_config');
        foreach($query->result() as $key => $value) {
            $query->result()[$key]->addtime = date('Y-m-d H:i:s', $value->addtime);
        }
        return ['list'=>$query->result()];
    }

    // 删除单表数据
    public function _del($tablename, $data) {
        return $this->db->delete($tablename, $data);
    }

    // 根据关键词获取列表
    public function getSearchList($select, $condition = [], $like, $tablename, $limit) {
        if ( ! empty($condition)) {
            $query = $this->db->select($select)->where($condition)->like($like)->get($tablename, $limit);
        } else {
            $query = $this->db->select($select)->like($like)->get($tablename, $limit);
        }
        return ['list'=>$query->result()];
    }

    /**
     * 获取驾校下的教练
     * @param $school_id
     * @return void
     **/
    public function searchCoachList($school_id, $like = [], $limit = 20)
    {
        $map = [];
        if ( $school_id != 0) {
            $map['school.l_school_id'] = $school_id;
        }
        // $map['coach.order_receive_status'] = 1;
        $coachlist = $this->db
            ->select(
                'coach.l_coach_id,
                 coach.s_coach_name,
                 school.s_school_name,
                 school.l_school_id'
            )
            ->from("{$this->coach_tablename} as coach")
            ->join("{$this->school_tablename} as school", "school.l_school_id=coach.s_school_name_id", "")
            ->where($map)
            ->like($like)
            ->get()
            ->result_array();
        if ( ! empty($coachlist)) {
            foreach ($coachlist as $key => $value) {
                if ($value['s_school_name'] != '' && $value['s_coach_name'] != '') {
                    $coachlist[$key]['name'] = $value['s_coach_name'].'('.$value['s_school_name'].')';
                } else {
                    $coachlist[$key]['name'] = $value['s_coach_name'];
                }
            }
        }
        return $coachlist;
    }

    /**
     * 更新数据
     * @param [$tblname, $field, $value, $data]
     * @return void
     **/
    public function updateData($tblname, $field, $value, $data)
    {   
        $where = [$field => $value];
        $update_ok = $this->db
            ->where($where)
            ->update($tblname, $data);
        return $update_ok;
    }

    /**
     * 删除数据
     * @param $id
     * @return void
     **/
    public function delData($tblname, $field, $value)
    {
        $result = $this->db
            ->where_in($field, $value)
            ->delete($tblname);
        return $result;
    }

    /**
     * 验证手机号的格式正确性
     *
     * @param str $phone
     * @return bool
     */
     public function checkPhoneFormat($phone) {
        if ( ! preg_match("/^1(3[0-9]|4[579]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/", trim($phone)) ) {
            return false;
        }
        return true;
    }

    /**
     * 验证身份证的格式正确性
     *
     * @param str $identity_id
     * @return bool
     */
    public function checkIdentityFormat($identity_id)
    {
        if ( ! preg_match('/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/', trim($identity_id)) ) {
            return false;
        } else {
            return true;
        }
    }
 

    // curl post
    public function requestPost($url = '', $post_data = array()) {
        if (empty($url) || empty($post_data)) {
            return false;
        }

        $o = "";
        foreach ( $post_data as $k => $v )
        {
            $o.= "$k=" . urlencode( $v ). "&" ;
        }
        $post_data = substr($o,0,-1);

        $postUrl = $url;
        $curlPost = $post_data;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);

        return $data;
    }

    // 获取银行配置
    public function getBankConfigList() {
        $query = $this->db->get('cs_bank_config');
        return ['list'=> $query->result()];
    }

    // 根据条件获取银行内容
    public function getBankConfigByCondition($wherecondition) {
        $query = $this->db->get_where('cs_bank_config', $wherecondition);
        return $query->row_array();
    }

    // 插入验证码
    public function insertVelidate($data) {
        $query = $this->db->get_where('cs_verification_code', ['s_phone'=>$data['s_phone']]);
        if($query->row_array()) {
            $res = $this->db->update('cs_verification_code', $data, ['s_phone'=>$data['s_phone']]);
            return $res;
        }
        $query = $this->db->insert('cs_verification_code', $data);
        return $this->db->insert_id();
    }

    // insert
    public function _insert($tablename, $data, $wherecondition) {
        $query = $this->db->get_where($tablename, $wherecondition);
        if($query->row_array()) {
            $res = $this->db->update($tablename, $data, $wherecondition);
            return $res;
        }
        $query = $this->db->insert($tablename, $data);
        return $this->db->insert_id();
    }

    // select
    public function _select($tablename, $select, $wherecondition) {
        $query = $this->db->select($select)->get_where($tablename, $wherecondition);
        return ['list'=>$query->result()];
    }

    // fetchOne
    public function _fetchOne($tablename, $select, $wherecondition) {
        $query = $this->db->select($select)->get_where($tablename, $wherecondition);
        return $query->row_array();
    }

    // 验证验证码与手机号是否一致
    public function velidatePhone($wherecondition) {
        $query = $this->db->get_where('cs_verification_code', $wherecondition);
        return $query->row_array();
    }

    /**
     * 验证用户执行权限
     * @param [$controller, $role_id]
     * @return void
     **/
    public function is_authorized($controller_name = NULL, $role_id = NULL)
    {
        if ( is_null($controller_name) OR (string)$controller_name == '') 
        {
            return "need controller name";
        }

        if (is_null($role_id) || intval($role_id) <= 0) 
        {
            return 'role id needed to be an integer';
        }

        $module_id = $this->madmin->getModuleIdByController($controller_name);
        if ($module_id <= 0) 
        {
            return 'module not exist';
        }

        $role_permission_module_list = $this->madmin->getPermissionModuleList($role_id);
        if (empty($role_permission_module_list)) 
        {
            return 'permission module list is empty';
        }

        if (in_array($module_id, $role_permission_module_list)) 
        {
            return true;
        } 
        else 
        {
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

    // 文件路径
    public function buildUrl ($url) {

        $this->config->load('upload');
        $upload_path = $this->config->item('upload_path');
        $http_host = $this->config->item('http_host');
        // var_dump($upload_path);exit;
        if (empty($url)) {
            return '';
        }

        if (substr($url, 0, 10) == '../upload/') {
            $url = str_replace('../upload/', '', $url);
        }
        if (substr($url, 0, 7) == 'upload/') {
            $url = str_replace('upload/', '', $url);
        }

        //windows specific
        if ( 'WINNT' === PHP_OS) {
            // you are under windows os
            // path separator
            if (trim(file_exists(str_replace('/', '\\', $upload_path.$url)))) {
                return $http_host.$url;
            }
            if (trim(file_exists(str_replace('/', '\\', $upload_path.'../admin/upload/'.$url)))) {
                return $http_host.'../admin/upload/'.$url;
            }
            if (trim(file_exists(str_replace('/', '\\', $upload_path.'../sadmin/upload/'.$url)))) {
                return $http_host.'../sadmin/upload/'.$url;
            }

        } else {
            // other os

            if (trim(file_exists($upload_path.$url))) {
                return $http_host.$url;
            }
            if (trim(file_exists($upload_path.'../sadmin/upload/'.$url))) {
                return $http_host.'../sadmin/upload/'.$url;
            }
            if (trim(file_exists($upload_path.'../admin/upload/'.$url))) {
                return $http_host.'../admin/upload/'.$url;
            }
        }

        return '';
    }

    /**
     * 处理上传文件
     * 
     * @return void
     **/
    public function handleAdminUpload ($type, $unique_id, $condition, $field, $tblname) 
    {
        if ( ! $unique_id && ! is_numeric($unique_id)
            OR '' == $type OR empty($condition) 
            OR '' == $field OR '' == $tblname) 
        {
            log_message('error', 'params is not exist or data type is wrong');
            $data = ['code' => 400, 'msg' => 'params type is wrong or is not exist', 'data' => new \stdClass];
            $this->output->set_content_type('application/json')
                ->set_output(json_encode( $data ))
                ->_display();
            exit;

        } 
        else 
        {
            if ( ! in_array($type, $this->_permitted_type)) {
                log_message('error', 'Type not allowed');
                $data = ['code' => 400, 'msg' => 'Type not allowed', 'data' => new \stdClass];
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode( $data ))
                    ->_display();
                exit;
            }
        }
        
        if ( FALSE !== $this->config->item('enable_sub_dir') 
            && '' !== $this->config->item('sub_dir_format') ) 
        {
            $_sub_dir = sprintf($this->config->item('sub_dir_format'), $type, $unique_id.'/'.date('Ymd'));
            $_upload_path = $this->config->item('upload_path').$_sub_dir;
            if ( $this->_mkdir($_upload_path)) 
            {
                $this->upload->set_upload_path($_upload_path);
            }
            else
            {
                $data = ['code' => 400, 'msg' => 'sub dir create error', 'data' => new \stdClass];
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode( $data ))
                    ->_display();
                exit;
            }
        }

        if ( ! $this->upload->do_upload('file'))
        {
            $error = array('error' => $this->upload->display_errors('', '')); // just msg , no 'html tag', default is '<p>'
            log_message('error', json_encode($error));
            $data = ['code' => 400, 'msg' => 'upload error', 'data' => $error];
            $this->output->set_content_type('application/json')
                ->set_output(json_encode($data))
                ->_display();
            exit;
        }
        else
        {
            // write to database
            $_relative_path = substr($this->upload->data('full_path'), strlen($this->config->item('upload_path')));
            $save_ok = $this->save_path($unique_id, $condition, $field, $_relative_path, $tblname);
            if ($save_ok) 
            {
                $data = [
                    'code' => 200, 
                    'msg' => 'upload ok', 
                    'data' => [
                        'url'=>'upload/'.$_relative_path, 
                        'file_url' => base_url('upload/'.$_relative_path)
                    ]
                ];
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode($data))
                    ->_display();
                exit();
            }
            else
            {
                log_message('error', 'path save to database error');
                $data = ['code' => 400, 'msg' => 'save error', 'data' => new \stdClass];
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode($data))
                    ->_display();
                exit();
            }
        }
        
    }

    /**
     * save in database
     * @param   string  $type
     * @param   string  $field
     * @param   string  $tblname
     * @return void
     **/
    public function save_path($unique_id, $condition, $field, $value, $tblname)
    {
        if (is_null($field))
        {
            return '';
        }
        
        $update_data = [$field => $value];
        $save_ok = $this->db
            ->where($condition)
            ->update($tblname, $update_data);
        return $save_ok;
    }

    /**
     * 创建目录
     */
    protected function _mkdir($path = '')
    {
        if ('' === $path)
        {
            log_message('error', 'path is empty');
            return FALSE;
        }

        if (file_exists($path))
        {
            if (is_dir($path))
            {
                return TRUE;
            }
            else
            {
                log_message('error', 'The path is file not a dir:'.$path);
                return FALSE;
            }
        }
        else
        {
            if (mkdir($path, 0777, TRUE)) // 递归创建目录
            {
                if (is_really_writable($path))
                {
                    return TRUE;
                }
                else
                {
                    log_message('error', 'dir is not writable');
                    return fALSE;
                }
            }
            else
            {
                log_message('error', 'dir create error');
                return FALSE;
            }
        }
    }

    // 根据行为唯一标识获取行为信息
    public function getActionByName($action)
    {
        $name = trim($action);
        $action_info = $this->db
            ->where('name', $name)
            ->get($action_tbl)
            ->row_array();
        return $action_info;
    }

}
