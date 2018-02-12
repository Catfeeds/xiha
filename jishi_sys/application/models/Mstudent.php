<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mstudent extends CI_Model {

    // 表名
    protected $table = 'student';

    // 主键
    protected $primary_key = 'stuid';

    // 字段
    protected $fields = [];

    public function __construct()
    {
        parent::__construct();
        $this->initialize();
    }

    public function initialize ()
    {
        $this->load->database();

        // 初始化字段
        $this->fields = $this->db->list_fields($this->table);
    }

    /**
     * 添加一条记录
     */
    public function add(array $data = [])
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function list($limit, $offset, array $where = [])
    {
        $query = $this->db->order_by($this->primary_key, 'DESC')->get_where($this->table, $where, $limit, $offset);
        return $query->result_array();
    }

    public function delete($id)
    {
        return $this->db->delete($this->table, [$this->primary_key => $id]);
    }

    /**
     * 获取记录总数
     *
     * @param $where array 查询过滤条件
     * @return int
     */
    public function total(array $where = [])
    {
        return $this->db->count_all($this->table);
    }

    /**
     * 获取一条记录
     *
     * @param $id int
     * @return mixed
     */
    public function detail($id) {
        $query = $this->db->get_where($this->table, [$this->primary_key => $id]);
        return $query->row_array();
    }

    /**
     * 更新一条记录
     *
     * @param $data array
     * @param $id int
     */
    public function update(array $data = [], $id)
    {
        return $this->db->update($this->table, $data, [$this->primary_key => $id]);
    }

    /**
     * 返回此表的所有字段
     *
     * @return array $fields
     */
    public function getFields ()
    {
        return $this->fields;
    }

    /**
     * 获取主键
     */
    public function getPrimaryKey ()
    {
        return $this->primary_key;
    }

} /* Mcoach class ends */

?>
