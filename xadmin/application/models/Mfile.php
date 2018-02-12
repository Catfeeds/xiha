<?php
    // 首页
defined('BASEPATH') OR exit('No direct script access allowed');

class Mcoach extends CI_Model {

    public $tablename = 'file';
    public $coachid;


    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


}