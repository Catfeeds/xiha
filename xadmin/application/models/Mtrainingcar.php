<?php
    // 首页
defined('BASEPATH') OR exit('No direct script access allowed');

class Mtrainingcar extends CI_Model {

    public $tablename = 'trainingcar';
    public $carid;
    public $inscode;
    public $franum;
    public $engnum;
    public $licnum;
    public $platecolor;
    public $photo;
    public $manufacture;
    public $brand;
    public $model;
    public $perdritype;
    public $buydate;
    public $carnum;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getTrainingCarList($start, $limit)
    {
        $query = $this->db->order_by('carid', 'DESC')->get($this->tablename, $limit, $start);
        return ['list'=>$query->result()];
    }
    
    public function getTrainingcarPageNum($limit)
    {
        $count = $this->db->count_all($this->tablename);
        return ['pn'=>(int) ceil($count / $limit), 'count'=>$count];
    }

    public function addTrainingcarInfo($data) {
        $this->db->insert($this->tablename, $data);
        return $this->db->insert_id();
    }

    public function editTrainingcarInfo($data) {
        return $this->db->where('carid', $data['carid'])->update($this->tablename, $data);
    }

    public function delTrainingcarInfo($data) {
        return $this->db->delete($this->tablename, $data);
    }

    public function getTrainingcarInfo($id) {
        $query = $this->db->get_where($this->tablename, ['carid'=>$id]);
        return $query->row_array();
    }
    
}
?>