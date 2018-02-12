<?php
    // 首页
defined('BASEPATH') OR exit('No direct script access allowed');

class Mcouponcode extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->coupon_code_tbl = $this->db->dbprefix('coupon_code');
    }

    public function create($data)
    {
        $query = $this->db->insert($this->coupon_code_tbl, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $res = $this->db->update($this->coupon_code_tbl, $data, array('id'=>$id));
        return $res;
    }

    public function delInfo($data)
    {
        $res = $this->db->delete($this->coupon_code_tbl, $data);
        return $res;
    }
}
