<?php

/**
 * 用户模型
 */

!defined('IN_FILE') && exit('Access Denied');

class muser extends mbase {

	/**
	 * 获取UCenter配置文件, 如不存在则自动生成
	 * @return string 配置文件路径及文件名
	 */
	private function _getUcConfig() {
		$file = MOBILE_ROOT.'./data/config/uc_config.inc.php';
		if(!file_exists($file)) {
			$config = $this->_getFirstResult("SELECT `VALUE` FROM `{$this->_dbtabpre}shop_config` WHERE `code`='integrate_config'");
			$config = unserialize($config);
			$data = "<?php\r\n".
				"\r\n".
				"/**\r\n".
				" * UCenter配置文件\r\n".
				" * 文件说明: 该配置文件系自动生成, 如需更新请先删除\r\n".
				" * 创建时间: ".date("Y-m-d H:i:s")."\r\n".
				" */\r\n".
				"\r\n".
				"!defined('IN_FILE') && exit('Access Denied');\r\n".
				"\r\n"."define('UC_CONNECT', '{$config['uc_connect']}');\r\n".
				"define('UC_DBHOST', '{$config['db_host']}');\r\n".
				"define('UC_DBUSER', '{$config['db_user']}');\r\n".
				"define('UC_DBPW', '{$config['db_pass']}');\r\n".
				"define('UC_DBNAME', '{$config['db_name']}');\r\n".
				"define('UC_DBCHARSET', '{$config['db_charset']}');\r\n".
				"define('UC_DBTABLEPRE', '{$config['db_name']}.{$config['db_pre']}');\r\n".
				"define('UC_DBCONNECT', 0);\r\n".
				"\r\n".
				"define('UC_KEY', '{$config['uc_key']}');\r\n".
				"define('UC_API', '{$config['uc_url']}');\r\n".
				"define('UC_CHARSET', '{$config['uc_charset']}');\r\n".
				"define('UC_IP', '{$config['uc_ip']}');\r\n".
				"define('UC_APPID', '{$config['uc_id']}');\r\n".
				"define('UC_PPP', '20');\r\n".
				"\r\n".
				"?>";
			file_put_contents($file, $data, LOCK_EX);
		}
		return $file;
	}

	/**
	 * 用户登录
	 * @param string $uname 用户名
	 * @param string $passwd 密码
	 * @return mixed 登录成功返回用户信息数组(UC用户ID, 用户名, 密码, E-mail), 失败返回错误代码
	 */
	public function userLogin($uname, $passwd) {
		require $this->_getUcConfig();
		require MOBILE_ROOT.'./libs/uc_client/client.php';
		return uc_user_login($uname, $passwd);
	}

	/**
	 * 用户登录
	 * @param string $uname 用户名
	 * @param string $passwd 密码
	 * @return mixed 登录成功返回用户信息数组( 用户名, 密码, E-mail), 失败返回错误
	 */
	public function _UserLogin($uname, $passwd) {
		$username = trim($uname);
		$sql = "SELECT `regtime` FROM {$this->_dbtabpre}members WHERE `uname` = '{$username}'";
		$regtime = $this->_getFirstRecord($sql);
		$password = $this->encrypt_passwd_enhanced($passwd, $username, $regtime['regtime']);
		$sql = "SELECT `uname`, `password`, `email`, `reg_ip`, `regtime` FROM `{$this->_dbtabpre}members` WHERE `uname` = '{$username}' AND `password` = '{$password}'";
		$list = array();
		if($this->_getFirstRecord($sql)) {
			$res = $this->_db->query($sql);
			while($row = $this->_db->fetch_array($res)) {
				$list[0] = 1;
				$list[1] = $row['uname'];
				$list[2] = $row['password'];
				$list[3] = $row['email'];
				$list[4] = $row['reg_ip'];
				$list[5] = $row['regtime'];
			}
		} else {
			return false;
		}
		return $list;
	}
	/**
	 * 用户注册
	 * @param string $uname 用户名
	 * @param string $passwd 密码
	 * @param string $email E-mail地址
	 * @return int 注册成功返回UC用户ID, 失败返回负的错误代码
	 */
	public function userRegister($uname, $passwd, $email) {
		require $this->_getUcConfig();
		require MOBILE_ROOT.'./libs/uc_client/client.php';
		$uc_uid = uc_user_register($uname, $passwd, $email);
		return $uc_uid;
	}

	/**
	 * 用户注册
	 * @param string $uname 用户名
	 * @param string $passwd 密码
	 * @param string $email E-mail地址
	 * @return int 注册成功返回1, 
				-1 => '您输入的用户名不正确!',
				-2 => '包含不允许注册的词语!',
				-3 => '该用户名已经存在!',
				-4 => '您输入的E-mail地址不正确!',
				-5 => '该E-mail地址不允许注册!',
				-6 => '该E-mail地址已经被注册!'
	 */
	 public function _UserRegister($uname, $passwd, $email) {
		$username = trim($uname);
		$regtime = time();
		$password = $this->encrypt_passwd_enhanced($passwd, $uname, $regtime);
		$email = trim($email);
		$uid = 1;

		//该email地址不正确
		if (preg_match("/^[a-z]([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?$/i", $email)) {
			$uid = -4;
		}

		//该email地址已经被注册
		$sql = "SELECT `email` FROM `{$this->_dbtabpre}members` WHERE `email` = '{$email}'";	
		if($this->_getFirstRecord($sql)) {
			$uid = -6;
		}

		//该用户名已存在
		$sql = "SELECT `uname` FROM `{$this->_dbtabpre}members` WHERE `uname` = '{$username}'";
		if($this->_getFirstRecord($sql)) {
			$uid = -3;
		}
		return $uid;
	}

	/**
	 * 通过用户ID获取用户资料
	 * @param int $uid 用户ID
	 * @return array 用户资料
	 */
	public function getUserByUid($uid) {
		$uid = intval($uid);
		if($uid <= 0) {
			return false;
		}
		$sql = "SELECT `member_id`, `member_lv_id`, `uname`, `name`, `lastname`, `firstname`, `password`, `area`, `mobile`, `tel`, `email`, `zip`, `addr`, `province`, `city`, `order_num`, `refer_id`, `refer_url`, `refer_time`, `c_refer_id`, `c_refer_url`, `c_refer_time`, `b_year`, `b_month`, `b_day`, `sex`, `addon`, `wedlock`, `education`, `vocation`, `interest`, `advance`, `point_freeze`, `point_history`, `point`, `score_rate`, `reg_ip`, `regtime`, `state`, `pay_time`, `biz_money`, `pw_answer`, `pw_question`, `fav_tags`, `custom`, `cur`, `lang`, `unreadmsg`, `disabled`, `advance_freeze`, `remark`, `role_type`, `remark_type`, `login_count`, `experience`, `foreign_id`, `member_refer`
			FROM `{$this->_dbtabpre}members` WHERE `member_id`='{$uid}'";
		return $this->_getFirstRecord($sql);
	}

	/**
	 * 通过用户名获取用户资料
	 * @param string $uname 用户名
	 * @return array 用户资料
	 */
	public function getUserByUname($uname) {
		$uname = trim($uname);
		if($uname == '') {
			return false;
		}
		$uname = mysqlesc($uname);
		$sql = "SELECT `member_id`, `member_lv_id`, `uname`, `name`, `lastname`, `firstname`, `password`, `area`, `mobile`, `tel`, `email`, `zip`, `addr`, `province`, `city`, `order_num`, `refer_id`, `refer_url`, `refer_time`, `c_refer_id`, `c_refer_url`, `c_refer_time`, `b_year`, `b_month`, `b_day`, `sex`, `addon`, `wedlock`, `education`, `vocation`, `interest`, `advance`, `point_freeze`, `point_history`, `point`, `score_rate`, `reg_ip`, `regtime`, `state`, `pay_time`, `biz_money`, `pw_answer`, `pw_question`, `fav_tags`, `custom`, `cur`, `lang`, `unreadmsg`, `disabled`, `advance_freeze`, `remark`, `role_type`, `remark_type`, `login_count`, `experience`, `foreign_id`, `member_refer`
		FROM `{$this->_dbtabpre}members` WHERE `uname`='{$uname}'";
		return $this->_getFirstRecord($sql);
	}

	/**
	 * 获取会员等级ID及折扣
	 * @param int $uid 用户ID
	 * @return array 会员等级ID及折扣数组
	 */
	public function getUserRankDiscount($uid) {
		$rankdiscount = array('rankid' => 0, 'discount' => 1);
		if($user = $this->getUserByUid($uid)) {
			if($user['member_lv_id']) {
				$sql = "SELECT `member_lv_id`, `dis_count` FROM `{$this->_dbtabpre}member_lv`
					WHERE `member_lv_id`='{$user['member_lv_id']}'";
			}
			if($row = $this->_getFirstRecord($sql)) {
				$rankdiscount['rankid'] = $row['member_lv_id'];
				$rankdiscount['discount'] = $row['dis_count'];
			}
		}
		return $rankdiscount;
	}

	/**
	 * 新建用户
	 * @param string $uname 用户名
	 * @param string $passwd 用户密码(明文)
	 * @param string $email E-mail地址
	 * @return bool 成功与否
	 */
	public function addUser($ucenter) {
		$uname = $ucenter[1];
		$email = $ucenter[3];
		$regip = $ucenter[4];
		$regtime = $ucenter[5];
		$passwd = $this->encrypt_passwd_enhanced($ucenter[2], $uname, $regtime);
		$uname = mysqlesc($uname);
		$email = mysqlesc($email);
		$fields = array('member_id' => NULL, 'member_lv_id' => '14', 'uname' => $uname, 'name' => NULL,
			'lastname' => NULL, 'firstname' => NULL, 'password' => $passwd, 'area' => NULL,
			'mobile' => NULL, 'tel' => NULL, 'email' => $email, 'zip' => NULL, 'addr' => NULL,
			'province' => NULL, 'city' => NULL, 'order_num' => '0', 'refer_id' => NULL,
			'refer_url' => NULL, 'refer_time' => NULL, 'c_refer_id' => NULL, 'c_refer_url' => NULL,
			'c_refer_time' => NULL,'b_year' => NULL,'b_month' => NULL,'b_day' => NULL,
			'sex' => '1','addon' => 'a:1:{s:4:"cart";s:0:"";}','wedlock' => '0','education' => NULL,
			'vocation' => NULL,'interest' => NULL,'advance' => '0.000','point_freeze' => '0',
			'point_history' => '0','point' => '0','score_rate' => NULL,'reg_ip' => $regip,
			'regtime' => $regtime,'state' => '0','pay_time' => NULL,'biz_money' => '0.000',
			'pw_answer' => NULL,'pw_question' => NULL,'fav_tags' => NULL,'custom' => NULL,'cur' => 'CNY',
			'lang' => NULL,'unreadmsg' => '0','disabled' => false,'advance_freeze' =>'0.000',
			'remark' => NULL,'role_type' => NULL,'remark_type' => 'b1','login_count' => '0',
			'experience' => '0','foreign_id' => '','member_refer' => 'local');
		return $this->_insertRecord($fields, 'members');
	}

	/**
	 * 获取用户收藏数量
	 * @param int $uid 用户ID
	 * @return int 用户ID的收藏数量
	 */
	public function getFavNum($uid) {
		$uid = intval($uid);
		$sql = "SELECT addon FROM `{$this->_dbtabpre}members` WHERE `member_id`='{$uid}'";
		$addon = $this->_getFirstResult($sql);
		$isfav = strpos($addon,'fav');
		$addonarr = unserialize($addon);
		if($isfav){
			return $favnum = count($addonarr['fav']);
		} else {
			return $favnum = 0;
		}
	}

	/**
	 * 获取用户收藏列表
	 * @param int $uid 用户ID
	 * @param int $start 起始偏移量
	 * @param int $limit 最大行数
	 * @return array 用户ID的收藏列表
	 */
	public function getFavList($uid, $start = 0, $limit = 20) {
		$uid = intval($uid);
		$sql = "SELECT addon FROM `{$this->_dbtabpre}members` WHERE `member_id`='{$uid}'";
		$addon = $this->_getFirstResult($sql);
		$addonarr = unserialize($addon);
		if(!isset($addonarr['fav'])){
			$isfav = 0;
		}else{
			$isfav = 1;
		}
		
		$list = array();
		if($isfav) {
			$addonlen = count($addonarr['fav']);
			$addonfav = array_slice($addonarr['fav'] ,$start ,$limit);
			$favstr = implode(",",$addonfav);
			$sql = "SELECT `goods_id`, `name`, `thumbnail_pic`, `price` FROM `{$this->_dbtabpre}goods` WHERE `goods_id`in({$favstr}) ORDER BY `goods_id` DESC LIMIT 0, {$limit}";
			$query = $this->_db->query($sql);
			$userdiscount = $this->getUserRankDiscount($uid);
			while($row = $this->_db->fetch_array($query)) {
				$thumbnail_pic = explode('|', $row['thumbnail_pic']);
				$row['thumbnail_pic'] = $thumbnail_pic[0];
				$row['price'] = $row['price'] * $userdiscount['discount'];    // 会员价
				$row['final_price'] = priceformat($row['price']);    // 最终价
				$list[] = $row;
			}
		}
		return $list;
	}

	/**
	 * 是否已收藏商品
	 * @param int $uid 用户ID
	 * @param int $goodid 商品ID
	 * @return bool 是否已收藏商品
	 */
	public function isInFav($uid, $goodid) {
		$uid = intval($uid);
		$goodid = intval($goodid);
		if($uid <= 0 || $goodid <= 0) {
			return false;
		} else {
			$addonarr = array();
			$sql = "SELECT `addon` FROM `{$this->_dbtabpre}members`
				WHERE `member_id`='{$uid}'";
			$addon = $this->_getFirstResult($sql);
			$addonarr = unserialize($addon);
			if(isset($addonarr['fav']) && in_array($goodid, $addonarr['fav'])){
				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 * 添加到收藏夹
	 * @param int $uid 用户ID
	 * @param int $goodid 商品ID
	 * @return bool 是否收藏成功
	 */
	public function addToFav($uid, $goodid) {
		$uid = intval($uid);
		$goodid = intval($goodid);
		if($uid <= 0 || $goodid <= 0) {
			return false;
		} else {
			$addonarr = array(); 
			$sql = "SELECT `addon` FROM `{$this->_dbtabpre}members`
				WHERE `member_id`='{$uid}'";
			$addon = $this->_getFirstResult($sql);
			$addonarr = unserialize($addon);

			if(in_array('fav',array_keys($addonarr)) && (!in_array($goodid, $addonarr['fav']))){
				array_push($addonarr['fav'],strval($goodid));
			} else {
				$favarr = array('fav'=>array());
				$addonarr = array_merge($addonarr,$favarr);
				array_push($addonarr['fav'],strval($goodid));
			}
			
			$addon = serialize($addonarr);
			$sql = "UPDATE `{$this->_dbtabpre}members` SET `addon`='{$addon}'
					WHERE `member_id`='{$uid}'";
			return $this->_db->query($sql);
		}
	}

	/**
	 * 从收藏夹删除
	 * @param int $uid 用户ID
	 * @param int $goodid 商品ID
	 * @return bool 是否删除成功
	 */
	public function delFromFav($uid, $goodid) {
		$uid = intval($uid);
		$goodid = intval($goodid);
		if($uid <= 0 || $goodid <= 0) {
			return false;
		} else {
			$addonarr = array(); 
			$sql = "SELECT `addon` FROM `{$this->_dbtabpre}members`
				WHERE `member_id`='{$uid}'";
			$addon = $this->_getFirstResult($sql);
			$addonarr = unserialize($addon);

			if(in_array('fav',array_keys($addonarr)) && (in_array($goodid, $addonarr['fav']))){
				unset($addonarr['fav'][array_search($goodid,$addonarr['fav'])]);
				if(count($addonarr['fav']) == 0){
					unset($addonarr['fav']);
				};
			}
			$addon = serialize($addonarr);
			$sql = "UPDATE `{$this->_dbtabpre}members` SET `addon`='{$addon}'
					WHERE `member_id`='{$uid}'";
			return $this->_db->query($sql);
		}
	}

	/**
	 * 获取收货人信息
	 * @param int $uid 用户ID
	 * @return array 收货人信息
	 */
	public function getAddrList($uid) {
		$uid = intval($uid);
		$addrlist = array();
		if($uid > 0) {
			$mflow = new mflow($this->_db);
			$sql = "SELECT `addr_id`, `member_id`, `name`, `area`, `country`,
				`province`, `city`, `addr`, `zip`, `tel`, `mobile`, `def_addr`
				FROM `{$this->_dbtabpre}member_addrs` WHERE `member_id`='{$uid}'
				ORDER BY `addr_id` DESC";
			$query = $this->_db->query($sql);
			while($row = $this->_db->fetch_array($query)) {
				$addrlist[] = $row;
			}
		}
		return $addrlist;
	}

	/**
	 * 通过收货人地址ID获取收货人信息
	 * @param int $addrid 收货人地址ID
	 * @return array 收货人信息
	 */
	public function getAddrById($addrid) {
		$addrid = intval($addrid);
		if($addrid > 0) {
			$sql = "SELECT `addr_id`, `member_id`, `name`, `area`, `country`,
				`province`, `city`, `addr`, `zip`, `tel`, `mobile`, `def_addr`
				FROM `{$this->_dbtabpre}member_addrs` WHERE `addr_id`='{$addrid}'
				ORDER BY `addr_id` DESC";
			return $this->_getFirstRecord($sql);
		} else {
			return false;
		}
	}

	/**
	 * 保存为新收货人地址(最大允许5条记录)
	 * @param int $uid 用户ID
	 * @return array $address 收货人地址数组
	 * @return bool 成功与否
	 */
	public function addAddr($uid, $address) {
		$uid = intval($uid);
		if($uid > 0) {
			$sql = "SELECT COUNT(*) FROM `{$this->_dbtabpre}member_addrs`
				WHERE `member_id`='{$uid}'";
			$addrcnt = $this->_getFirstResult($sql);
			if($addrcnt >= 5) {
				return false;
			} else {
				return $this->_insertRecord($address, 'member_addrs');
			}
		} else {
			return false;
		}
	}

	/**
	 * 更新收货人地址
	 * @param int $uid 用户ID
	 * @param int $addrid 收货人地址ID
	 * @param array $address 收货人地址数组
	 * @return bool 成功与否
	 */
	public function updateAddr($uid, $addrid, $address) {
		$uid = intval($uid);
		$addrid = intval($addrid);
		if($uid > 0 && $addrid > 0 && is_array($address)) {
			$wherefields = array('addr_id' => $addrid, 'member_id' => $uid);
			return $this->_updateRecord($address, $wherefields, 'member_addrs');
		} else {
			return false;
		}
	}

	/**
	 * 获取用户订单总数
	 * @param int $uid 用户ID
	 * @return int 用户ID的订单总数
	 */
	public function getOrderNum($uid) {
		$uid = intval($uid);
		$sql = "SELECT COUNT(*) FROM `{$this->_dbtabpre}orders` WHERE `member_id`='{$uid}'";
		return $this->_getFirstResult($sql);
	}

	/**
	 * 获取用户订单
	 * @param int $uid 用户ID
	 * @param int $start 起始偏移量
	 * @param int $limit 最大行数
	 * @return array 用户ID的所有订单
	 */
	public function getOrderList($uid, $start = 0, $limit = 20) {
		require MOBILE_ROOT.'./include/enum.inc.php';
		$uid = intval($uid);
		$sql = "SELECT `order_id`, `confirm`, `status`, `pay_status`, `ship_status`,
			`final_amount`,`itemnum`, `acttime` FROM `{$this->_dbtabpre}orders` 
			WHERE `member_id`='{$uid}'
			ORDER BY `acttime` DESC LIMIT {$start}, {$limit}";
		$query = $this->_db->query($sql);
		$list = array();
		while($row = $this->_db->fetch_array($query)) {
			$row['order_status'] = $_ENUM['status'][$row['status']];
			$row['order_pay_status'] = $_ENUM['order_pay_status'][$row['pay_status']];
			$row['ship_status'] = $_ENUM['ship_status'][$row['ship_status']];
			$row['final_amount'] = priceformat($row['final_amount']);
			$row['acttime'] = timeformat($row['acttime']);
			$list[] = $row;
		}
		return $list;
	}

	/**
	 * 获取订单详情
	 * @param int $orderid 订单ID
	 * @return array 订单ID的详情
	 */
	public function getOrderById($orderid) {
		require MOBILE_ROOT.'./include/enum.inc.php';
		$orderid = is_numeric($orderid) ? $orderid : 0;
		$sql = "SELECT o.`order_id`, o.`confirm`, o.`member_id`, o.`status`, o.`pay_status`,
			o.`ship_status`, o.`itemnum`, o.`acttime`, o.`cost_item`, o.`total_amount`,
			o.`final_amount`, o.`cost_freight`, o.`shipping_id`, o.`shipping`, o.`payment`,
			p.`custom_name`, p.`pay_type`, p.`config`, p.`fee`, p.`fee`
			FROM `{$this->_dbtabpre}orders` o
			LEFT JOIN `{$this->_dbtabpre}payment_cfg` p ON p.`id` = o.`payment`
			WHERE o.`order_id`='{$orderid}'";
		if($result = $this->_getFirstRecord($sql)) {
			$result['custom_name'] = $result['payment'] == -1 ? "货到付款" : $result['custom_name'];
			$result['order_status'] = $_ENUM['status'][$result['status']];
			$result['order_pay_status'] = $_ENUM['order_pay_status'][$result['pay_status']];
			$result['ship_status'] = $_ENUM['ship_status'][$result['ship_status']];
			$result['cost_item'] = priceformat($result['cost_item']);
			$result['cost_freight'] = priceformat($result['cost_freight']);
			$result['total_amount'] = priceformat($result['total_amount']);
			$result['acttime'] = timeformat($result['acttime']);
			return $result;
		} else {
			return false;
		}
	}

	/**
	 * 获取订单商品
	 * @param int $orderid 订单ID
	 * @return array 订单ID的商品
	 */
	public function getGoodListByOrderid($orderid) {
		$orderid = is_numeric($orderid) ? $orderid : 0;
		$sql = "SELECT oi.`item_id`, oi.`order_id`, oi.`product_id`, oi.`name`, oi.`price`,
			oi.`amount`, oi.`nums`, g.`thumbnail_pic`, g.`goods_id`
			FROM `{$this->_dbtabpre}order_items` oi
			LEFT JOIN `{$this->_dbtabpre}products` p ON p.`product_id`=oi.`product_id`
			LEFT JOIN `{$this->_dbtabpre}goods` g ON g.`goods_id`= p.`goods_id`
			WHERE oi.`order_id`='{$orderid}' ORDER BY oi.`item_id` ASC";
		$query = $this->_db->query($sql);
		$list = array();
		while($row = $this->_db->fetch_array($query)) {
			$thumbnail_pic = explode('|', $row['thumbnail_pic']);
			$row['thumbnail_pic'] = $thumbnail_pic[0];
			$row['price'] = priceformat($row['price']);
			$row['amount'] = priceformat($row['amount']);
			$list[] = $row;
		}
		return $list;
	}

	/**
	 * 更新订单状态
	 * @param int $orderid 订单ID
	 * @param string $field 状态字段
	 * @param int $status 状态码
	 * @return bool 成功与否
	 */
	public function updateOrderStatus($orderid, $field, $status) {
		require MOBILE_ROOT.'./include/enum.inc.php';
		$orderid = intval($orderid);
		$field = in_array($field, array('order', 'shipping', 'pay')) ? $field : '';
		$status = $field != '' ? (in_array($status, array_keys($_ENUM[$field.'_status'])) ? $status : '') : '';
		if($orderid > 0 && $field != '' && $status != '') {
			if($field == 'pay' && $status == 2) {
				$this->_updateRecord(array('is_paid' => '1'), array('order_id' => $orderid), 'pay_log');
			}
			$this->_updateRecord(array($field.'_status' => $status), array('order_id' => $orderid), 'order_info');
			return $this->_db->affected_rows() > 0 ? true : false;
		} else {
			return false;
		}
	}

	/**
	 * 通过订单号查询订单ID
	 * @param string $ordersn 订单号
	 * @return int 订单ID
	 */
	public function getOrderidByOrdersn($ordersn) {
		$sql = "SELECT `order_id` FROM `{$this->_dbtabpre}order_info`
			WHERE `order_sn`='{$ordersn}'";
		return $this->_getFirstResult($sql);
	}

	/**
	 * 会员注册密码加密算法
	 * @param string $pwd 密码
	 * @param string $uname 用户名
	 * @param string $regtime 注册时间
	 * @return string 加密后的算法
	 */
    public function encrypt_passwd_enhanced( $pwd, $uname, $regtime )
    {
        if ( !$pwd || !$uname || !$regtime )
        {
            return false;
        }
        $pwd = md5( md5( trim( $pwd ) ).strtolower( $uname ).$regtime );
        return "s".substr( $pwd, 0, 31 );
    }
//	/**
//	 * 更新数据库会话记录
//	 * @return bool 成功与否
//	 */
//	public function updateSession() {
//
//		// 会话相关数据
//		$sid = $GLOBALS['sid'];
//		$sdata = $_SESSION;
//		$user = $GLOBALS['user'];
//
//		// 处理重复会话数据
//		if($user['user_id']) {
//			$dsessions = $this->_getAllRecords("SELECT `sesskey` FROM `{$this->_dbtabpre}sessions`
//				WHERE `userid`='{$user['user_id']}' AND `sesskey`!='{$sid}' AND CHAR_LENGTH(`sesskey`)='26'");
//			foreach($dsessions as $dsession) {
//				$this->_db->query("UPDATE `{$this->_dbtabpre}cart` SET `session_id`='{$sid}'
//					WHERE `session_id`='{$dsession['sesskey']}'");    // 合并购物车商品
//				$this->_db->query("DELETE FROM `{$this->_dbtabpre}sessions`
//					WHERE `sesskey`='{$dsession['sesskey']}'");
//			}
//		}
//
//		// 更新会话记录
//		$sql = "REPLACE INTO `{$this->_dbtabpre}sessions` SET `sesskey`='{$sid}',
//			`expiry`={$this->timestamp} + 1440, `userid`='{$user['user_id']}',
//			`adminid`='0', `ip`='{$this->onlineip}', `user_name`='{$user['user_name']}',
//			`user_rank`='{$sdata['user']['rankid']}', `discount`='{$sdata['user']['discount']}',
//			`email`='{$user['email']}', `data`=''";
//		return $this->_db->query($sql);
//
//	}

}

?>