<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Article extends CI_Controller {
	
	public function __construct() {
        parent::__construct();
        $this->load->helper('url');
	}

	public function index()
	{
        $subject_type = $this->input->get('st') ? $this->input->get('st') : 'kemu1'; // 科目类别  1: 科目一  。。。 5:领证
        $license_type = $this->input->get('lt') ? $this->input->get('lt') : 'car'; // 牌照类别  1: 小车  。。。 		
        $from = $this->input->get('f') ? $this->input->get('f') : 1; // 来源 1： 科一，四考规，2：科一，四答题技巧，3：科二，三学车技巧，4：科二，三秘籍，5： 科二，三学车报告， 6：科二，三电子教练
		$cate_id = 0;        
		
        switch ($from) {
        	case '1':
				$cate_id = $subject_type == 'kemu1' ? 41 : 54;
        		$title = $subject_type == 'kemu1' ? '科一考规' : '科四考规';
        		break;
        	case '2':
				$cate_id = $subject_type == 'kemu1' ? 40 : 50;
        		$title = $subject_type == 'kemu1' ? '科一答题技巧' : '科四答题技巧';
        		break;
        	case '3':
				$cate_id = $subject_type == 'kemu2' ? 44 : 48;
        		$title = $subject_type == 'kemu2' ? '科二学车技巧' : '科三学车技巧';
        		break;
        	case '4':
				$cate_id = $subject_type == 'kemu2' ? 45 : 49;
        		$title = $subject_type == 'kemu2' ? '科二秘籍' : '科三秘籍';
        		break;
        	case '5':
        		$title = $subject_type == 'kemu2' ? '科二学车报告' : '科三学车报告';
        		break;
        	case '6':
        		$title = $subject_type == 'kemu2' ? '科二电子教练' : '科三电子教练';
        		break;
        	default:
        		$title = '';
        		break;
        }
        $data = [
        	'subject_type' => $subject_type,
        	'license_type' => $license_type,
        	'from' => $from,
        	'title' => $title,
        	'cate_id' => $cate_id,
        ];
		$this->load->view('default/header');
		$this->load->view('default/article', $data);
		$this->load->view('default/footer');
	}
	
	public function detail()
	{
        $subject_type = $this->input->get('st') ? $this->input->get('st') : 'kemu1'; // 科目类别  1: 科目一  。。。 5:领证
        $license_type = $this->input->get('lt') ? $this->input->get('lt') : 'car'; // 牌照类别  1: 小车  。。。 		
        $from = $this->input->get('f') ? $this->input->get('f') : 1; // 来源 1： 科一，四考规，2：科一，四答题技巧，3：科二，三学车技巧，4：科二，三秘籍，5： 科二，三学车报告， 6：科二，三电子教练，7：视频
        $id = $this->input->get('id') ? $this->input->get('id') : 1; // 视频，文章ID
        
        switch ($from) {
        	case '1':
        		$title = $subject_type == 'kemu1' ? '科一考规' : '科四考规';
        		break;
        	case '2':
        		$title = $subject_type == 'kemu1' ? '科一答题技巧' : '科四答题技巧';
        		break;
        	case '3':
        		$title = $subject_type == 'kemu2' ? '科二学车技巧' : '科三学车技巧';
        		break;
        	case '4':
        		$title = $subject_type == 'kemu2' ? '科二秘籍' : '科三秘籍';
        		break;
        	case '5':
        		$title = $subject_type == 'kemu2' ? '科二学车报告' : '科三学车报告';
        		break;
        	case '6':
        		$title = $subject_type == 'kemu2' ? '科二电子教练' : '科三电子教练';
        		break;
        	case '7':
        		$title = $subject_type == 'kemu2' ? '科二学车视频' : '科三学车视频';
        		break;
        	default:
        		$title = '';
        		break;
        }
        $data = [
        	'subject_type' => $subject_type,
        	'license_type' => $license_type,
        	'from' => $from,
        	'title' => $title,
        	'id' => $id,
        ];
		$this->load->view('default/header');
		if($from == 7) {
			$this->load->view('default/video_detail', $data);
		} else {
			$this->load->view('default/article_detail', $data);
		}
		$this->load->view('default/footer');
	}	
}
