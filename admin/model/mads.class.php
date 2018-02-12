<?php  
	// 广告管理模块

	!defined('IN_FILE') && exit('Access Denied');

	class mads extends mbase {

		/**
		 * 获取已有广告位列表
		 * @return array 
		 */
		public function getAdsPositions() {

			$ads_positions = array();
			$sql = "SELECT * FROM `{$this->_dbtabpre}ads_position`";
			$res = $this->_getAllRecords($sql);
			if ($res) {
				$ads_position = $res;
				foreach ($ads_position as $key => $value) {
					$ads_position[$key]['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
					// if ($value['scene'] == 101) {
					// 	$ads_position[$key]['scene'] = '学员端app启动图片';
					// } elseif ($value['scene'] == 102) {
					// 	$ads_position[$key]['scene'] = '学员端app首页广告图';
					// }
				}
			}
			return $ads_position;
		}
		
	}
?>	