<?php
    // 首页
defined('BASEPATH') OR exit('No direct script access allowed');

class Mbalance extends CI_Model {

    public $balance_tablename = 'cs_balance';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('mschool');
    }

    /**
     * 通过用户类型和用户id获得账户可提现余额
     * @param $utype
     * @param $uid
     * @return mixed
     */
    public function getBalanceByUtypeAndUid($utype, $uid)
    {
        $where = [
            'utype' => $utype,
            'uid' => $uid,
        ];
        $result = $this->db->select('balance')->get_where($this->balance_tablename, $where);
        $balance = $result->row_array();
        if (isset($balance['balance'])) {
            return floatval($balance['balance']);
        } else {
            return floatval(0);
        }
    }

}
?>
