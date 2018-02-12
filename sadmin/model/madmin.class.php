<?php  
	!defined('IN_FILE') && exit('Access Denied');

	class madmin extends mbase {

		/**
		 * 用户登录
		 * @param string $uname 用户名
		 * @param string $passwd 密码
		 * @return mixed 登录成功返回用户信息数组( 用户名, 密码, E-mail), 失败返回错误
		 */
		public function _UserLogin($uname, $password) {
			$username = trim($uname);
			$password = md5($password);
			$sql = "SELECT `addtime` FROM {$this->_dbtabpre}admin WHERE `name` = '{$username}' AND `role_id` = 2";
			$regtime = $this->_getFirstRecord($sql);
			$list = array();

			if($regtime) {
				$sql = "SELECT * FROM {$this->_dbtabpre}admin WHERE `name` = '{$username}' AND `password` = '{$password}' AND `role_id` = 2";
				if($this->_getFirstRecord($sql)) {
					$res = $this->_db->query($sql);
					while($row = $this->_db->fetch_array($res)) {
						$list[0] = $row['name'];
						$list[1] = $row['addtime'];
						$list[2] = $row['school_id'];
						$list[3] = $row['content'];
					}
					$this->_db->free_result($res);
				} else {
					return false;
				}
			} else {
				return false;
			}
			$this->_db->close();
			return $list;
		}

		/**
		 * 管理员列表
		 * @param string $uname 用户名
		 * @param string $passwd 密码
		 * @return mixed 登录成功返回用户信息数组( 用户名, 密码, E-mail), 失败返回错误
		 */
		public function getManagerList($page='', $limit='', $school_id) {
			if($page !== '' && $limit !== '') {
				$sql = "SELECT * FROM `{$this->_dbtabpre}admin` WHERE `school_id` = $school_id ORDER BY `id` DESC LIMIT $page, $limit";
			} else {
				$sql = "SELECT * FROM `{$this->_dbtabpre}admin` WHERE `school_id` = ".$school_id ;
			}
			$res = $this->_getAllRecords($sql);

			$list = array();
			foreach ($res as $key => $value) {
				$sql = "SELECT `s_rolename` FROM `{$this->_dbtabpre}roles` WHERE `l_role_id` = ".$value['role_id'];
				$row = $this->_getFirstRecord($sql);
				$res[$key]['role_permission'] = $row['s_rolename'];
				$list = $res;
			}
			return $list;
		}

		/**
		 * 权限列表
		 * @param string $uname 用户名
		 * @param string $passwd 密码
		 * @return mixed 登录成功返回用户信息数组( 用户名, 密码, E-mail), 失败返回错误
		 */
		public function getPermissionList() {

			global $manage_config;

			$sql = "SELECT * FROM `{$this->_dbtabpre}rolepermission` as p LEFT JOIN `{$this->_dbtabpre}roles` as r ON r.`l_role_id` = p.`l_role_id` WHERE r.`l_role_id` != 1";
			$row = $this->_getAllRecords($sql);
			$list = array();
			foreach ($row as $key => $value) {
				$list[$key]['role_name'] = $value['s_rolename'];
				$list[$key]['role_description'] = $value['s_description'];
				$list[$key]['l_rolepress_id'] = $value['l_rolepress_incode'];

				$module_id = explode(',', $value['module_id']);
				foreach ($module_id as $k => $v) {
					if(in_array($v, array_keys($manage_config))) {
						$list[$key]['permission_list'][] = $manage_config[$v];
					}
				}
			}
			// echo "<pre>";
			// print_r($list);

			return $list;
		}

		/**
		 * 添加教练
		 * @param string $uname 用户名
		 * @param string $passwd 密码
		 * @return mixed 登录成功返回用户信息数组( 用户名, 密码, E-mail), 失败返回错误
		 */
		public function addManager($arr) {
			$sql = "SELECT `name` FROM `{$this->_dbtabpre}admin` WHERE `name` = '".$arr['manage_name']."'";
			$row = $this->_getFirstRecord($sql);
			if(!$row) {
				$sql = "INSERT INTO `{$this->_dbtabpre}admin` (`name`, `password`, `role_permission_id`, `role_id`, `addtime`, `school_id`) VALUES";
				$sql .= " ('".$arr['manage_name']."','".$arr['password']."','".$arr['role_permission_id']."','".$arr['role_id']."','".time()."','".$arr['school_id']."')";
				$query = $this->_db->query($sql);
				return $query;
			} else {
				return false;
			}
		}

		/**
		 * 管理员名称检测重复性
		 * @param string $name 用户名
		 * @return bool
		 */
		public function userCheck($name) {
			$sql = "SELECT `name` FROM `{$this->_dbtabpre}admin` WHERE `name` = '".$name."'";
			$row = $this->_getFirstRecord($sql);
			if($row) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * 设置角色权限
		 * @param string $name 用户名
		 * @return bool
		 */
		public function setRolePermission($arr) {
			$sql = "INSERT INTO `{$this->_dbtabpre}roles` (`s_rolename`, `s_description`) VALUES ('".$arr['manage_role_name']."','".$arr['manage_role_description']."')";
			$query = $this->_db->query($sql);
			if($query) {
				$id = $this->lastInertId();
			} else {
				return false;
			}

			$sql = "INSERT INTO `{$this->_dbtabpre}rolepermission` (`l_role_id`, `module_id`) VALUES ('".$id."', '".$arr['permission_id']."')";
			$query = $this->_db->query($sql);
			return $query;
		}

		/**
		 * 获取角色列表
		 * @return arr
		 */		
		public function getRoleList() {
			$sql = "SELECT * FROM `{$this->_dbtabpre}roles` WHERE `l_role_id` != 1";
			$row = $this->_getAllRecords($sql);
			return $row;
		}

		/**
		 * 获取角色列表
		 * @return arr
		 */	
		public function getManageInfo($id) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}admin` WHERE `id` = $id ORDER BY `id` DESC";
			$res = $this->_getFirstRecord($sql);
			$sql = "SELECT `s_rolename` FROM `{$this->_dbtabpre}roles` WHERE `l_role_id` = ".$res['role_id'];
			$row = $this->_getFirstRecord($sql);
			if($row) {
				$res['role_permission'] = $row['s_rolename'];
			} else {
				$res['role_permission'] = '';
			}
				
			return $res;
		}

		/**
		 * 修改管理员信息
		 * @return bool
		 */	
		public function updateManageInfo($arr) {
			// 获得role_id
			$sql = "SELECT `l_role_id` FROM `{$this->_dbtabpre}rolepermission` WHERE `l_rolepress_incode` = ".$arr['manage_permission_id'];
			$row = $this->_getFirstRecord($sql);
			if($row) {
				$sql = "UPDATE `{$this->_dbtabpre}admin` SET `name` = '".$arr['manage_name']."', `password` = '".md5($arr['manage_repeat_password'])."', `role_permission_id` = '".$arr['manage_permission_id']."', `role_id` = '".$row['l_role_id']."', `content` = '".$arr['manage_content']."' WHERE `id` = '".$arr['id']."'";
				$query = $this->_db->query($sql);
				return $query;				
			} else {
				return false;
			}
		}

		public function deleteManage($id) {
			$sql = "DELETE FROM `{$this->_dbtabpre}admin` WHERE `id` = $id";
			$query = $this->_db->query($sql);
			return $query;
		}	

		// 获取管理员角色表
		public function getRoleInfo($id) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}roles` WHERE `l_role_id` = $id";
			$res = $this->_getFirstRecord($sql);
			return $res;
		} 

		// 更新角色信息
		public function updateRoleInfo($arr) {
			$sql = "UPDATE `{$this->_dbtabpre}roles` SET `s_rolename` = '".$arr['manage_role_name']."', `s_description` = '".$arr['manage_role_content']."' WHERE `l_role_id` = ".$arr['id'];
			$query = $this->_db->query($sql);
			if($query) {
				$sql = "UPDATE `{$this->_dbtabpre}rolepermission` SET `module_id` = '".implode(',', $arr['permission_id'])."' WHERE `l_role_id` = '".$arr['id']."'";
				$query = $this->_db->query($sql);
				if($query) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
		//验证旧密码是否正确
		public function getoldpassword($arr) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}admin` WHERE `school_id` = ".$arr['school_id']." AND `password` = '".$arr['oldpass']."'";
			$stmt = $this->_getFirstRecord($sql);
			if($stmt) {
				return true;
			} else {
				return false;
			}
		}
		// 修改驾校密码
		public function changepassword($arr) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}admin` WHERE `school_id` = ".$arr['school_id'];
			$stmt = $this->_getFirstRecord($sql);
			if($stmt) {
				$sql = "UPDATE `{$this->_dbtabpre}admin` SET `password` = '".$arr['pass']."' WHERE `school_id` = ".$arr['school_id'];
				$stmt = $this->_db->query($sql);
				if($stmt) {
					return 2;
				} else {
					return 3;
				}
			} else {
				return 4;
			}
		}

		// 检验密码是否修改
		public function checkPwd($arr) {
			$sql = "SELECT * FROM `{$this->_dbtabpre}admin` WHERE `school_id` = ".$arr['school_id']." AND `password` = '".$arr['password']."'";
			$stmt = $this->_getFirstRecord($sql);
			if($stmt) {
				return true;
			} else {
				return false;
			}
		}
	}
?>