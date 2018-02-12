<?php
    // 首页
defined('BASEPATH') OR exit('No direct script access allowed');

class Mtags extends CI_Model {

    public $systags_tablename = 'cs_system_tag_config';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // 根据用户类型获取标签列表
    public function getSysTagListByType($type=1, $start='', $limit='')
    {
        if($start == '' || $limit == '') {
            $query = $this->db->where('user_type', $type)->order_by('id', 'DESC')->get($this->systags_tablename);
        } else {
            $query = $this->db->where('user_type', $type)->order_by('id', 'DESC')->get($this->systags_tablename, $limit, $start);
        }
        foreach($query->result() as $key => $value) {
            $query->result()[$key]->addtime = date('Y-m-d H:i:s', $value->addtime);
        }
        return ['list'=>$query->result()];
    }

    // 添加标签
    public function addTag($tablename, $data) {
        $this->db->insert($tablename, $data);
        return $this->db->insert_id();
    }
}