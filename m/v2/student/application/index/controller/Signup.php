<?php

//报名控制器

namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\View;

class Signup extends Controller
{
	protected $request;
	protected $params;
	protected $device;
	protected $token;
	
	public function _initialize() {
		$this->request = Request::Instance();
		$this->params = $this->request->param();
		$this->device = $this->request->has('device') ? $this->params['device'] : '1'; // 1:web 2:ios 3:andriod
		$this->token = $this->request->has('token', 'get') ? $this->request->get('token') : null;
		$this->assign('token', $this->token);
		$this->assign('r', time());
		$this->assign('device', $this->device);
        $this->assign('title', '报名列表');
	}
	
    // 首页
    public function index() {
        return $this->fetch('signup/index');
    }
	
	//教练详情
	public function detail() {
        $this->assign('title', '教练名片');
        return $this->fetch('signup/detail');
	}
	
	//班型
	public function shifts() {
	   	$this->assign('title', '班型');
    	return $this->fetch('signup/shifts');
	}
	
	//我的订单
	public function myorder() {
		$this->assign('title', '我的订单');
		if(isset($this->token) && trim($this->token) != '') {
	    	return $this->fetch('signup/myorder');
		} else {
			$this->redirect('./ucenter/login', ['redirect_url' => urlencode(url('/signup/myorder')), 'device' => $this->device]);
		}
	}

	//领取嘻哈券
	public function  getcoupon() {
		$this->assign('title', '嘻哈券领取');
		// if(isset($this->token) && trim($this->token) != '') {
			$this->request = Request::Instance();
			$this->params = $this->request->param();
			$this->device = $this->request->has('device') ? $this->params['device'] : '1'; // 1:web 2:ios 3:andriod
			$school_id = $this->request->has('id') ? $this->params['id'] : '';
			$coach_id = $this->request->has('coach_id') ? $this->params['coach_id'] : '';
			$coupon_id = $this->request->has('id') ? $this->params['id'] : '';
			$coach_info = $this->getCoachInfoById($coach_id);
			$coupon_info = $this->getCouponInfoById($coupon_id);
			if (!empty($coach_info)) {
				if ($coach_info['s_coach_name'] != '') {
					$coach_name = $coach_info['s_coach_name'];
				} else {
					$coach_name = '未知';
				}

				if ($coach_info['s_school_name'] != '') {
					$school_name = $coach_info['s_school_name'];
				} else {
					$school_name = '嘻哈';
				}

				if ($coach_info['s_coach_imgurl'] != '') {
					$coach_imgurl = $coach_info['s_coach_imgurl'];
				} else {
					$coach_imgurl = '';
				}

			} else {
				$coach_name = '暂无';
				$coach_imgurl = '';
				$school_name = '嘻哈';
			}
			if (!empty($coupon_info)) {
				$province_id = $coupon_info['province_id'];
				$city_id = $coupon_info['city_id'];
				$area_id = $coupon_info['area_id'];
				$coupon_value_string = $coupon_info['coupon_value_string'];
			} else {
				$province_id = 0;
				$city_id = 0;
				$area_id = 0;
				$coupon_value_string = '暂未设置';
			}
			
			$this->assign('coach_id', $coach_id);
			$this->assign('coupon_id', $coupon_id);
			$this->assign('area_id', $area_id);
			$this->assign('city_id', $city_id);
			$this->assign('province_id', $province_id);
			$this->assign('coach_name', $coach_name);
			$this->assign('coach_imgurl', $coach_imgurl);
			$this->assign('coupon_value_string', $coupon_value_string);
	    	return $this->fetch('signup/getcoupon');
		// } else {
		// 	$this->redirect('./ucenter/login', ['redirect_url' => urlencode(url('/signup/getCoupon')), 'device' => $this->device]);
		// }
	}

	//领取嘻哈券
	public function  signupshare () {
		$this->assign('title', '报名分享');
		$this->request = Request::Instance();
		$this->params = $this->request->param();
		$user_id = $this->request->has('id') ? $this->params['id'] : '';
		$order_id = $this->request->has('rid') ? $this->params['rid'] : '';
		$order_info = $this->getOrderInfo($user_id, $order_id);
		$this->assign('orders_info', $order_info);
		return $this->fetch('signup/signupshare');
	}


	private function getCoachInfoById ($coach_id) {
		$condition = [
			'coach.l_coach_id' => $coach_id,
		];
		$location_php = "http://w.xihaxueche.com/service/";
		$location_admin = "http://w.xihaxueche.com/service/admin/";
		$location_sadmin = "http://w.xihaxueche.com/service/sadmin/";
		$coach_info = Db::table('cs_coach')
			->alias('coach')	
			->join('cs_school school', 'school.l_school_id = coach.s_school_name_id', 'LEFT')
			->where($condition)
			->field('l_coach_id, s_coach_name, l_school_id, s_school_name, s_coach_imgurl, s_coach_lisence_id')
			->find();
		if (!empty($coach_info)) {
			if ($coach_info['s_coach_imgurl'] != '') {
				if ( !file_exists($location_admin.$coach_info['s_coach_imgurl'])) {
					$coach_info['s_coach_imgurl'] = $location_sadmin.$coach_info['s_coach_imgurl'];
				} else {
					$coach_info['s_coach_imgurl'] = $location_admin.$coach_info['s_coach_imgurl'];
				}

			} else {
				$coach_info['s_coach_imgurl'] = '';
			}
			if ($coach_info['s_coach_lisence_id'] != '') {
				$license_name = Db::table('cs_license_config')
					->where(array('license_id' => $coach_info['s_coach_lisence_id'], 'is_open' => 1))
					->fetchSql(false)
					->find();
				if (!empty($license_name)) {
					$coach_info['coach_lisence_name'] = $license_name['license_name'];
				} 
			}
			return $coach_info;
		} else {
			return array();
		}
    }

    private function getCouponInfoById ($coupon_id) {
		$condition = [
			'id' => $coupon_id,
		];
		$coupon_info = Db::table('cs_coupon')
			->where($condition)
			->field('province_id, city_id, area_id, coupon_value, coupon_scope, coupon_category_id')
			->find();
		if (!empty($coupon_info)) {
			if ($coupon_info['coupon_category_id'] == 1) {
				if ($coupon_info['coupon_value'] == '') {
					$coupon_info['coupon_value_string'] = '￥ 0 '.'元';
				} else {
					$coupon_info['coupon_value_string'] = '￥'.$coupon_info['coupon_value'].'元';
				}
			} elseif ($coupon_info['coupon_category_id'] == 2) {
				if ($coupon_info['coupon_value'] == '') {
					$coupon_info['coupon_value_string'] = '暂未设置';
				} else {
					$coupon_info['coupon_value_string'] = $coupon_info['coupon_value'].'折';
				}
			}
			return $coupon_info;
		} else {
			return array();
		}
    }

	// 获取当前用户订单信息
	private function getOrderInfo ($user_id, $order_id) {

		$whereCondition = [
			'so_user_id' => $user_id,
			'id' => $order_id,
		];
		$order_info = Db::table('cs_school_orders as orders')
			->field('so_username, so_shifts_id, so_order_no, dt_zhifu_time, so_total_price, so_licence')
			->where($whereCondition)
			->fetchsql(false)
			->find();
		if (!empty($order_info)) {
			$order_info['total_price'] = $order_info['so_total_price'];
			$order_info['license_name'] = $order_info['so_licence'];
			$shifts_id = $order_info['so_shifts_id'];
			$shifts_info = Db::table('cs_school_shifts')
				->field('sh_title, sh_license_name, sh_money')
				->where(array('id' => $shifts_id))
				->find();
			if (!empty($shifts_info)) {
				$order_info['sh_title'] = $shifts_info['sh_title'];
				$order_info['sh_money'] = $shifts_info['sh_money'];
			} else {
				$order_info['sh_title'] = '';
				$order_info['sh_money'] = 0;
			}

			$photo_id = 1;
			$user_info = Db::table('cs_users_info')
				->field('photo_id')
				->where(array('user_id' => $user_id))
				->find();
			if ( ! empty($user_info)) {
				$photo_id = $user_info['photo_id'];
			}

			$user_imgurl = $photo_id.'.png';
			$order_info['user_imgurl'] = $user_imgurl;
		}

		return $order_info;

	}
	



}

?>