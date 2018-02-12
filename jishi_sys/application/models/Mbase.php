<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mbase extends CI_Model {

    /**
     * 表名
     */
    protected $table = '';

    /**
     * 主键
     */
    protected $primary_key = 'id';

    public function __construct()
    {
        parent::__construct();
        // 加载数据库
        $this->load->database();
    }

    /**
     * 根据主键获取一条记录
     */
    public function getOne($id)
    {
        $query = $this->db->get_where($this->table, [$this->primary_key => $id]);
        return $query->row_array();
    }

}
