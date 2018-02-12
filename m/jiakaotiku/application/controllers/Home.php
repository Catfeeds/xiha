<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
	
	public function __construct() {
        parent::__construct();
        $this->load->helper('url');
	}

	public function index()
	{
        $subject_type = $this->input->get('st') ? $this->input->get('st') : 'kemu1'; // 科目类别  1: 科目一  。。。 5:领证
        $license_type = $this->input->get('lt') ? $this->input->get('lt') : 'car'; // 牌照类别  1: 小车  。。。 		
        
        $data = [
        	'subject_type' => $subject_type,
        	'license_type' => $license_type
        ];
		$this->load->view('default/header');
		switch ($subject_type) {
			case 'kemu1':
				$this->load->view('default/index', $data);
				break;
			case 'kemu2':
				$this->load->view('default/lesson2', $data);
				break;			
			case 'kemu3':
				$this->load->view('default/lesson3', $data);
				break;
			case 'kemu4':
				$this->load->view('default/lesson4', $data);
				break;
			default:
				$this->load->view('default/graduate', $data);
				break;
		}
		$this->load->view('default/footer');
	}
	
	//章节练习
	public function chapter()
	{
        $subject_type = $this->input->get('st') ? $this->input->get('st') : 1; // 科目类别  1: 科目一  。。。 5:领证
        $license_type = $this->input->get('lt') ? $this->input->get('lt') : 1; // 牌照类别  1: 小车  。。。 		
        
        $data = [
        	'subject_type' => $subject_type,
        	'license_type' => $license_type
        ];
		$this->load->view('default/header');
		$this->load->view('default/chapter', $data);
		$this->load->view('default/footer');
	}
	//专项练习
	public function special()
	{
        $subject_type = $this->input->get('st') ? $this->input->get('st') : 1; // 科目类别  1: 科目一  。。。 5:领证
        $license_type = $this->input->get('lt') ? $this->input->get('lt') : 1; // 牌照类别  1: 小车  。。。 		
        
        $data = [
        	'subject_type' => $subject_type,
        	'license_type' => $license_type
        ];
		$this->load->view('default/header');
		$this->load->view('default/special', $data);
		$this->load->view('default/footer');
	}
	
	//排行榜
	public function rank()
	{
        $subject_type = $this->input->get('st') ? $this->input->get('st') : 1; // 科目类别  1: 科目一  。。。 5:领证
        $license_type = $this->input->get('lt') ? $this->input->get('lt') : 1; // 牌照类别  1: 小车  。。。 		
        
        $data = [
        	'subject_type' => $subject_type,
        	'license_type' => $license_type
        ];
		$this->load->view('default/header');
		$this->load->view('default/rank', $data);
		$this->load->view('default/footer');
	}
	
	//考前冲刺
	public function sprint()
	{
        $subject_type = $this->input->get('st') ? $this->input->get('st') : 1; // 科目类别  1: 科目一  。。。 5:领证
        $license_type = $this->input->get('lt') ? $this->input->get('lt') : 1; // 牌照类别  1: 小车  。。。 		
        
        $data = [
        	'subject_type' => $subject_type,
        	'license_type' => $license_type
        ];
		$this->load->view('default/header');
		$this->load->view('default/sprint', $data);
		$this->load->view('default/footer');
	}

	//模拟考试
	public function exam()
	{
        $subject_type = $this->input->get('st') ? $this->input->get('st') : 1; // 科目类别  1: 科目一  。。。 5:领证
        $license_type = $this->input->get('lt') ? $this->input->get('lt') : 1; // 牌照类别  1: 小车  。。。 		
        
        $data = [
        	'subject_type' => $subject_type,
        	'license_type' => $license_type
        ];
		$this->load->view('default/header');
		$this->load->view('default/exam', $data);
		$this->load->view('default/footer');
	}
	
	//单个题目
	public function question()
	{
        $subject_type = $this->input->get('st') ? $this->input->get('st') : 1; // 科目类别  1: 科目一  。。。 5:领证
        $license_type = $this->input->get('lt') ? $this->input->get('lt') : 1; // 牌照类别  1: 小车  。。。 		
        $from = $this->input->get('f') ? $this->input->get('f') : 1; // 来源 1： 顺序练习，2：模拟考试，3：随机练习，4：专项练习，5： 章节练习， 6：错题收藏，7：考前冲刺	
        switch ($from) {
        	case '1':
        		$title = '顺序练习';
        		break;
			case '2':
				$title = '模拟考试';
				break;
			case '3':
				$title = '随机练习';
				break;
			case '4':
				$title = '专项练习';
				break;
			case '5':
				$title = '章节练习';
				break;
			case '6':
				$title = '错题收藏';
				break;
			case '7':
				$title = '考前冲刺';
				break;
        	default:
        		$title = '顺序练习';
        		break;
        }
        $data = [
        	'subject_type' => $subject_type,
        	'license_type' => $license_type,
        	'from' => $from,
        	'title' => $title,
        ];
		$this->load->view('default/header');
		$this->load->view('default/question', $data);
		$this->load->view('default/footer');
	}
	
}
