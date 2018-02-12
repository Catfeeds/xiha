<?php
/**
* 注册学员处理模块
* @author sun
**/
namespace Admin\Model;
use Think\Model\RelationModel;

class RegisterUserModel extends RelationModel {
	public $tableName = 'user'; 
	protected $_link = array(
        'UsersInfo'=>array(
            'mapping_type'      => self::HAS_ONE,
            'foreign_key'      => 'user_id',

            ),
        );
   	
   	/**
   	 * 关联写入注册用户
   	 *
   	 * @return 
   	 * @author sun
   	 **/
   	public function registerUser($info, $school_id) {
      	if (empty($info)) {
      		return false;
      	}
      	$passwd = md5('123456');
  		  $data = array();
        if ( $info['user_phone'] != '') {
            $data['s_phone'] = $info['user_phone'] ;
            $data['s_real_name'] = $info['real_name'] ? $info['real_name'] : '嘻哈用户'.substr($data['s_phone'], -4);
            $data['s_username'] = '嘻哈用户'.substr($data['s_phone'], -4);
        } else {
            $data['s_phone'] = '';
            $data['s_real_name'] = $info['real_name'] ? $info['real_name'] : '';
            $data['s_username'] = '嘻哈用户'.substr(time(), -4);
        }
        $data['s_password'] = $passwd;
        $data['i_user_type'] = 0;
        $data['i_status'] = 0;
        $data['l_yw_incode'] = 0;
        $data['i_from'] = $info['i_from'] ? $info['i_from'] : 2;
		    $data['is_first'] = 0;
		    $data['s_imgurl'] = '';
		    $data['content'] = '';
        $data['coach_id'] = 0;
		    $data['addtime'] = time();
    		$data['UsersInfo'] = array(
    		  'x' =>0,
    		  'y' =>0,
    		  'sex' => $info['sex'] ? $info['sex'] : 1,
    		  'age' => $info['age'] ? $info['age'] : 0,
    		  'identity_id' => $info['identity_id'] ? $info['identity_id'] : '',
    		  'address' => $info['address'] ? $info['address'] : '',
          // 'user_photo' =>$info['user_photo'] ? $info['user_photo'] : '',
    		  'user_photo' => '',
    		  'license_num' => $info['license_num'] ? $info['license_num'] : 0,
    		  'school_id' => $school_id,
          'lesson_id' => $info['lesson_id'] ? $info['lesson_id'] : 1,
          'lesson_name' => $info['lesson_name'] ? $info['lesson_name'] : '科目一',
          'license_id' => $info['license_id'] != '' ? $info['license_id'] : 1,
    		  'license_name' => $info['license_name'] ? $info['license_name'] : 'C1',
    		  'province_id' => $info['province'] ? $info['province'] : 0,
    		  'city_id' => $info['city'] ? $info['city'] : 0,
    		  'area_id' => $info['area'] ? $info['area'] : 0,
          'photo_id' => 0 ,
    		  // 'photo_id'   		 =>$info['photo_id'] == '' ? 0 : $info['photo_id'],
          // 'learncar_status'  =>$info['learncar_status'] ? $info['learncar_status'] : '科目一学习中',
          'learncar_status' => '科目一学习中',
          'addtime' => time(),
		);
		$result = $this->relation(true)->fetchSql(false)->add($data);
		return $result;
   	}
}

?>   	
