<?php
	namespace app\index\model;
	use think\Model;
	use think\Db;
	
	class Coach extends Model {
		
		protected function initialize() {
			parent::initialize();
		}
		
		public function getRandomCoachList() {
            $data = Db::name('coach')->field('s_coach_name, s_teach_age, s_coach_imgurl, s_coach_original_imgurl, s_coach_lesson_id')->where('s_school_name_id = 81')->order('rand()')->limit(10)->select();
			$lesson_arr = array(
				'1' => '科目一',
				'2' => '科目二',
				'3' => '科目三',
				'4' => '科目四',
			);
			if($data) {
				foreach($data as $key => $value) {
					$data[$key]['s_coach_imgurl'] = $value['s_coach_imgurl'] ? 'http://w.xihaxueche.com/service/sadmin/'.$value['s_coach_imgurl'] : '';
					$data[$key]['s_coach_original_imgurl'] = $value['s_coach_original_imgurl'] ? 'http://w.xihaxueche.com/service/sadmin/'.$value['s_coach_original_imgurl'] : '';
					$s_coach_lesson_ids = explode(',', $value['s_coach_lesson_id']);
					if(!empty($s_coach_lesson_ids)) {
						foreach($s_coach_lesson_ids as $k => $v) {
							$data[$key]['s_coach_lesson_name'][] = isset($lesson_arr[$v]) ? $lesson_arr[$v] : '';
						}
					}
				}
			}
            return $data;
		}
	}	
?>