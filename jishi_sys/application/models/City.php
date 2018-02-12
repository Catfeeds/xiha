<?php
    // 首页
defined('BASEPATH') OR exit('No direct script access allowed');

class City extends CI_Model {

    public $cityid;
    public $city;
    public $fatherid;
    public $leter;
    public $spelling;
    public $acronym;
    public $is_hot;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getCityList($start, $limit)
    {
        $query = $this->db->get('city', $limit, $start);
        return ['list'=>$query->result()];
    }
    
    public function getCityPageNum($limit)
    {
        $count = $this->db->count_all('city');
        return ['pn'=>(int) ceil($count / $limit), 'count'=>$count];
    }
}
?>