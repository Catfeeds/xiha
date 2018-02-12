<?php
namespace Admin\Model;
use Think\Model;
use Think\Page;
   
class UsersInfoModel extends BaseModel {
    
    /**
     * 查看身份证是否已注册
     *
     * @return bool
     * @author sun
     **/
    public function isIdentityRegistered($identity) {
      if (!isset($identity)) {
        return false;
      }

      $res = $this->table(C('DB_PREFIX').'user user')
          ->join(C('DB_PREFIX').'users_info info ON user.l_user_id = info.user_id')
          ->where(
              array(
                  'i_status' => 0,
                  'i_user_type' => 0,
                  'identity_id' => $identity,
              )
          )
          ->find();
      if ($res) {
        return true;//该身份证已注册
      } else {
        return false;//该身份证未注册
      }
    }
 }
 
