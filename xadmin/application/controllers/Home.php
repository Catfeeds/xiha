<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//
class Home extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
        if(!$this->session->loginauth) {
            redirect(base_url('admin/login'));
        } else {
            redirect(base_url('admin/index'));
        }
    }

    public function index()
    {
        $this->load->view(TEMPLATE.'/header');
        $this->load->view(TEMPLATE.'/menu');
        $this->load->view(TEMPLATE.'/footer');
    }
}
?>
