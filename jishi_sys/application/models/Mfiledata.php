<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mfiledata extends CI_Model {

    /**
     * 表名
     */
    protected $table = 'filedata';

    /**
     * 主键
     */
    protected $primary_key = 'fileid';

    public function __construct()
    {
        parent::__construct();
        // 加载数据库
        $this->load->database();
    }

    /**
     * 根据主键获取一条记录
     */
    public function detail($id)
    {
        $query = $this->db->get_where($this->table, [$this->primary_key => $id]);
        return $query->row_array();
    }

}
