<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mbase extends CI_Model {

    protected $_permitted_type = [
        'miaomi',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->config->load('upload');
        $this->load->model('madmin');
        $this->load->library('upload');
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

    // 文件路径
    public function buildUrl ($url) {

        $this->config->load('upload');
        $upload_path = $this->config->item('upload_path');
        $http_host = $this->config->item('http_host');
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
            if (trim(file_exists(str_replace('/', '\\', $upload_path.'../upload/'.$url)))) {
                return $http_host.'../upload/'.$url;
            }

        } else {
            // other os

            if (trim(file_exists($upload_path.$url))) {
                return $http_host.$url;
            }
            if (trim(file_exists($upload_path.'../upload/'.$url))) {
                return $http_host.'../upload/'.$url;
            }
        }
        return '';
    }

}
