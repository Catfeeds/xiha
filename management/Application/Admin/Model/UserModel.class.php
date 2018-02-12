<?php 
namespace Admin\Model;
use Think\Model;
use Think\Page;
class UserModel extends BaseModel {

// 1.交易记录模块
    /**
     * 获取交易记录信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 26, 2016
     **/
    public function getTransRecords () {
        $count = $this->table(C('DB_PREFIX').'transaction_records')
                      ->fetchSql(false) 
                      ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $transrecordslists = array();
        $transrecordslist = $this->table(C('DB_PREFIX').'transaction_records')
                                  ->limit($Page->firstRow.','.$Page->listRows)
                                  ->order('id DESC')
                                  ->fetchSql(false)
                                  ->select();
        if ($transrecordslist) {
            foreach ($transrecordslist as $key => $value) {
                if ($value['addtime']) {
                    $transrecordslist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $transrecordslist[$key]['addtime'] = '';
                }

                if ($value['transaction_starttime']) {
                    $transrecordslist[$key]['transaction_starttime'] = date('Y-m-d H:i:s', $value['transaction_starttime']);
                } else {
                    $transrecordslist[$key]['transaction_starttime'] = '';
                }

                if ($value['transaction_endtime']) {
                    $transrecordslist[$key]['transaction_endtime'] = date('Y-m-d H:i:s', $value['transaction_endtime']);
                } else {
                    $transrecordslist[$key]['transaction_endtime'] = '';
                }
            }
        }

        $transrecordslists = array('transrecordslist' => $transrecordslist, 'count' => $count, 'page' => $page);
        return $transrecordslists;  

    }
	   
     /**
     * 搜索交易记录信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 26, 2016
     **/
    public function searchTransRecords ($param) {
        $map = array();
        $complex = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_info'] == '') {
            $complex['transaction_body'] = array('like', $s_keyword);
            $complex['transaction_no'] = array('like', $s_keyword);
            $complex['transaction_mch_name'] = array('like', $s_keyword);
            $complex['transaction_receiver_no'] = array('like', $s_keyword);
            $complex['transaction_receiver_name'] = array('like', $s_keyword);
            $complex['_logic'] = 'OR';
        } else {
            $complex[$param['search_info']] = array('like', $s_keyword);
        }
        $map['_complex'] = $complex;

        if ($param['transaction_pay_type'] != 0) {
            $map['transaction_pay_type'] = array('EQ', $param['transaction_pay_type']);
        }

        if ($param['transaction_status'] != 0) {
            $map['transaction_status'] = array('EQ', $param['transaction_status']);
        }
        $count = $this->table(C('DB_PREFIX').'transaction_records')
                      ->where($map)
                      ->fetchSql(false) 
                      ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $transrecordslists = array();
        $transrecordslist = $this->table(C('DB_PREFIX').'transaction_records')
                                  ->where($map)
                                  ->limit($Page->firstRow.','.$Page->listRows)
                                  ->order('id DESC')
                                  ->fetchSql(false)
                                  ->select();
        if ($transrecordslist) {
            foreach ($transrecordslist as $key => $value) {
                if ($value['addtime']) {
                    $transrecordslist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $transrecordslist[$key]['addtime'] = '';
                }

                if ($value['transaction_starttime']) {
                    $transrecordslist[$key]['transaction_starttime'] = date('Y-m-d H:i:s', $value['transaction_starttime']);
                } else {
                    $transrecordslist[$key]['transaction_starttime'] = '';
                }

                if ($value['transaction_endtime']) {
                    $transrecordslist[$key]['transaction_endtime'] = date('Y-m-d H:i:s', $value['transaction_endtime']);
                } else {
                    $transrecordslist[$key]['transaction_endtime'] = '';
                }
            }
        }

        $transrecordslists = array('transrecordslist' => $transrecordslist, 'count' => $count, 'page' => $page);
        return $transrecordslists;  

    }

    /**
     * 删除交易记录信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 26, 2016
     **/
    public function delTransRecords ($id) {
        if (!is_numeric($id)) {
            return false;
        }

        $result = M('transaction_records')->where('id = :tid')
            ->bind(['tid' => $id])
            ->save(array('transaction_status' => 101));
            // ->delete();
        return $result;
    }

// 2.用户银行账户管理
    /**
     * 获取银行账户关联信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 27, 2016
     **/
    public function getUserWalletList () {
        $count = $this->table(C('DB_PREFIX').'users_wallet w')
            ->join(C('DB_PREFIX').'user u ON u.l_user_id = w.user_id', 'LEFT')
            ->where(array('u.i_status' => 0))
            ->fetchSql(false)
            ->count();
        $Page = new Page($count, 10);
        $page = $this->getPage($count, 10);
        $userswalletlists = array();
        $userswalletlist = $this->table(C('DB_PREFIX').'users_wallet w')
            ->join(C('DB_PREFIX').'user u ON u.l_user_id = w.user_id', 'LEFT')
            ->where(array('u.i_status' => 0))
            ->field('w.*, u.l_user_id, s_username, s_real_name, s_phone')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->fetchSql(false)
            ->select();
        if ($userswalletlist) {
            foreach ($userswalletlist as $key => $value) {
                if ($value['addtime'] != 0) {
                    $userswalletlist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $userswalletlist[$key]['addtime'] = '';
                }
            }
        }
        $userswalletlists = array('userswalletlist' => $userswalletlist, 'count' => $count, 'page' => $page);
        return $userswalletlists;
    }

    /**
     * 搜索银行账户关联信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 27, 2016
     **/
    public function searchUsersWallet ($param) {
        $map = array();
        $complex = array();
        $s_keyword = '%'.$param['s_keyword'].'%';
        if ($param['search_info'] == '') {
            $complex['s_real_name']     = array('like', $s_keyword);
            $complex['s_username']      = array('like', $s_keyword);
            $complex['bank_user_name']  = array('like', $s_keyword);
            $complex['bank_phone']      = array('like', $s_keyword);
            $complex['bank_identifyid'] = array('like', $s_keyword);
            $complex['bank_name']       = array('like', $s_keyword);
            $complex['bank_account']    = array('like', $s_keyword);
            $complex['_logic']          = 'OR';
        } else {
            $complex[$param['search_info']] = array('like', $s_keyword);
        }
        $map['_complex'] = $complex;
        $map['i_status'] = array('EQ', 0);
        $count = $this->table(C('DB_PREFIX').'users_wallet w')
                      ->join(C('DB_PREFIX').'user u ON u.l_user_id = w.user_id', 'LEFT')
                      ->where($map)
                      ->fetchSql(false)
                      ->count();
        $Page = new Page($count, 10, $param);
        $page = $this->getPage($count, 10, $param);
        $userswalletlists = array();
        $userswalletlist = $this->table(C('DB_PREFIX').'users_wallet w')
                                ->join(C('DB_PREFIX').'user u ON u.l_user_id = w.user_id', 'LEFT')
                                ->where($map)
                                ->field('w.*, u.l_user_id, s_username, s_real_name, s_phone')
                                ->limit($Page->firstRow.','.$Page->listRows)
                                ->fetchSql(false)
                                ->select();
        if ($userswalletlist) {
            foreach ($userswalletlist as $key => $value) {
                if ($value['addtime'] != 0) {
                    $userswalletlist[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                } else {
                    $userswalletlist[$key]['addtime'] = '';
                }
            }
        }
        $userswalletlists = array('userswalletlist' => $userswalletlist, 'count' => $count, 'page' => $page);
        return $userswalletlists;
    }

     /**
     * 删除用户账户信息
     *
     * @return  void
     * @author  wl
     * @date    Oct 27, 2016
     **/
    public function delUsersWallet ($id) {
        if (!is_numeric($id)) {
            return false;
        }
        $users_wallet = M('users_wallet');
        $res = $users_wallet->where(array('id' => $id))
                        ->fetchSql(false)
                        ->delete(); 
        return $res;
    }


  /**
	 * 删除学员操作
	 *
	 * @return bool
	 * @author sun
	 **/
	public function delStudent($id) {
		$res = $this->where(array('l_user_id'=>$id))
                ->setField(array('i_status'=>2));
                // ->delete();
		return $res;
	}

	//根据s_phone,或者s_real_name搜索学员
   public function searchStudent($param) {
      $map = array($param['search_type']=>array('like', '%'.$param['s_keyword'].'%'), 'i_from'=>$param['pay_type'], 'i_status'=>'');
      $count = $this->where($map)->count();
      $Page = new Page($count, 10);
      $page = $this->getPage($count, 10, $param);
      $student_list = $this->join('cs_users_info ON cs_user.l_user_id = cs_users_info.user_id')->where($map)
      ->field(array('l_user_id','s_real_name','s_username','s_phone','sex','age','identity_id','address','i_from'))->limit($Page->firstRow.','.$Page->listRows)->select();
      $info = array($student_list, $page, $count);
      return $info;
   }

   /**
    * 根据传入的手机号判断该手机号是否已经注册会员
    *
    * @return bool
    * @author sun
    **/
   public function isPhoneRegistered($phone) {
   		if (!isset($phone)) {
   			return false;
   		}

   		$res = $this->where(array('s_phone' => $phone, 'i_status' => 0, 'i_user_type' => 0))->find();
   		if ($res) {
   			return true;//该号码已注册
   		} else {
   			return false;//该号码未注册
   		}
   }
    
}
?>
