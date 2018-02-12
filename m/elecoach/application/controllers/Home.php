<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct() {
        parent::__construct();
        $this->load->helper('url');
	}

	//	模拟考试(电子教练考试记录)
	public function index()
	{
		$token = $this->input->get('token');
		if(!$token) {
			$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ4aWhheHVlY2hlX3Byb2R1Y3Rpb24iLCJhdWQiOiJ4aWhheHVlY2hlX3Byb2R1Y3Rpb24iLCJqdGkiOiJiYzA3MGE2OGM2YSIsImlhdCI6MTQ5OTgyNDM0MiwibmJmIjoxNDk5ODI0MzQyLCJleHAiOjE1MDA0MjkxNDIsInVzZXIiOiJleUpwZGlJNkltOTRjR1l5U0dzMVYwOXpRMkZ4YkdsY0wzcDNUVkJuUFQwaUxDSjJZV3gxWlNJNklrdFJRWFpTVm1oYWEyWXlkVE5NYW1GNWFVTnBUVVJFTWxFemFXa3JZVEI2VldONFJUTlBlRWRhSzFOcFluVktkbTAwYm1kM2JFRjRTa2gzTWpodlNYbDVOelpsVm5kQmVGaEJTM3BDVTBveVYyNW5iRFJtTWxsMFV6RnJTM0ZLVEd4NVkxSk5NVWRhYkZwRFVXUXhPR0o0UVU1bU1VUkRZVVI0ZFhaaVNEazFJaXdpYldGaklqb2laR1F6WldGaVpEZzNZamMzT1dVMVlqY3pOalUxWVRNNE9XUTROVEprWVRCaFlUSTVaVEUwTkdZeU9UQTVORE0wTmpJME5ETmlZalprTTJReVl6aGhNaUo5In0.J0604tyNTDysVQ7eWZ4vLAN2-2hjaE8V0gV0rJu7P34";
		}
		$this->load->view('ecoach/header', array('title'=>'模拟考试'));
		$this->load->view('ecoach/list', array('token'=>$token));
		$this->load->view('ecoach/footer');
	}

	//	常规训练
	public function training()
	{
		$token = $this->input->get('token');
		if(!$token) {
			$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ4aWhheHVlY2hlX3Byb2R1Y3Rpb24iLCJhdWQiOiJ4aWhheHVlY2hlX3Byb2R1Y3Rpb24iLCJqdGkiOiJiYzA3MGE2OGM2YSIsImlhdCI6MTQ5OTgyNDM0MiwibmJmIjoxNDk5ODI0MzQyLCJleHAiOjE1MDA0MjkxNDIsInVzZXIiOiJleUpwZGlJNkltOTRjR1l5U0dzMVYwOXpRMkZ4YkdsY0wzcDNUVkJuUFQwaUxDSjJZV3gxWlNJNklrdFJRWFpTVm1oYWEyWXlkVE5NYW1GNWFVTnBUVVJFTWxFemFXa3JZVEI2VldONFJUTlBlRWRhSzFOcFluVktkbTAwYm1kM2JFRjRTa2gzTWpodlNYbDVOelpsVm5kQmVGaEJTM3BDVTBveVYyNW5iRFJtTWxsMFV6RnJTM0ZLVEd4NVkxSk5NVWRhYkZwRFVXUXhPR0o0UVU1bU1VUkRZVVI0ZFhaaVNEazFJaXdpYldGaklqb2laR1F6WldGaVpEZzNZamMzT1dVMVlqY3pOalUxWVRNNE9XUTROVEprWVRCaFlUSTVaVEUwTkdZeU9UQTVORE0wTmpJME5ETmlZalprTTJReVl6aGhNaUo5In0.J0604tyNTDysVQ7eWZ4vLAN2-2hjaE8V0gV0rJu7P34";
		}
		$this->load->view('ecoach/header', array('title'=>'常规训练'));
		$this->load->view('ecoach/training', array('token'=>$token));
		$this->load->view('ecoach/footer');
	}
}
