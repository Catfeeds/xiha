<?php

/**
 * 
 * 优惠券模块
 *
 **/

namespace App\Http\Controllers\v1;

use Exception;
use InvalidArgumentException;
use Log;
use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\AuthController;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use SmsApi;

class CouponController extends Controller {

    /*
     * 请求对象主体
     */
    protected $request;
    protected $auth;
    protected $user;

    public function __construct(Request $request) {
        $this->request = $request;
        $this->auth = new AuthController($this->request);
        $this->user = new UserController($this->request);
    }

    /**
     * get coach coupon 
     * @param $token 用户登录标识
     * @return void
     **/
    public function getCoachCouponList () {
        $user = (new AuthController)->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $user_type = $user['i_user_type'];
        $user_phone = $user['phone'];
        $coach_id = $this->user->getCoachIdByUserId($user_id);
        if (!$coach_id) {
            Log::Info('异常：【获取当前教练的优惠券】获取的教练id不存在');
            return response()->json([
                'code' => 400,
                'msg' => '此教练的账号出现异常',
                'data' => new \stdClass,
            ]);
        }
        $whereCondition = [
            'coupon.owner_type' => 1,
            'coupon.owner_id' => $coach_id,
        ];
        $coupon_list = DB::table('coupon')
            ->select('coupon.*')
            ->where($whereCondition)
            ->orderBy('coupon.id', 'desc')
            ->get();
        $coupon_lists = [];
        $nowtime = time();
        if ($coupon_list) {
            foreach ($coupon_list as $key => $value) {
                // coupon_id
                $coupon_lists[$key]['coupon_id'] = $value->id;

                // coupon_name
                $coupon_lists[$key]['coupon_name'] = $value->coupon_name;

                // coupon_value
                $coupon_lists[$key]['coupon_value'] = $value->coupon_value;
                
                // coupon_total_num
                $coupon_lists[$key]['coupon_total_num'] = $value->coupon_total_num;
                
                // coupon_get_num
                $coupon_lists[$key]['coupon_get_num'] = $value->coupon_get_num;

                // coupon_expiretime
                $coupon_expirtime = $value->expiretime;
                $coupon_lists[$key]['coupon_expiretime'] = date('Y-m-d', $coupon_expirtime);

                // coupon_addtime/year/month/day
                if ($value->addtime && 0 != $value->addtime) {
                    $coupon_addtime = $value->addtime;
                    $coupon_lists[$key]['coupon_addtime'] = date('Y-m-d', $coupon_addtime);
                    $coupon_lists[$key]['year'] = date('Y', $coupon_addtime);
                    $coupon_lists[$key]['month'] = date('m', $coupon_addtime);
                    $coupon_lists[$key]['day'] = date('d', $coupon_addtime);
                } else {
                    $coupon_lists[$key]['coupon_addtime'] = 0;
                    $coupon_lists[$key]['year'] = 0;
                    $coupon_lists[$key]['month'] = 0;
                    $coupon_lists[$key]['day'] = 0;
                }

                // coupon_desc
                if ($value->coupon_desc) {
                    $coupon_lists[$key]['coupon_desc'] = $value->coupon_desc;
                } else {
                    $coupon_lists[$key]['coupon_desc'] = '';
                }

                // province/city/area
                $province_id = $value->province_id;
                $city_id = $value->city_id;
                $province_info = $this->getCityById($city_id);
                $area_id = $value->area_id;

                // coupon_scope | coupon_scope_text
                switch ($value->coupon_scope) {
                    case 0 : // 全国
                        $coupon_lists[$key]['coupon_scope'] = 0;
                        $coupon_lists[$key]['coupon_scope_text'] = '全国';
                        break;
                    case 1 : // 全省
                        $coupon_lists[$key]['coupon_scope'] = 1;
                        $province_info = $this->getProvinceById($province_id);
                        if ( NULL != $province_info) {
                            $province = $province_info->province;
                            $length = mb_strlen($province);
                            $province = mb_substr($province, 0, $length - 1);
                            $coupon_lists[$key]['coupon_scope_text'] = $province;
                        } else {
                            $coupon_lists[$key]['coupon_scope_text'] = '安徽';
                        }
                        break;
                    case 2 : // 全市
                        $coupon_lists[$key]['coupon_scope'] = 2;
                        $city_info = $this->getCityById($city_id);
                        if ( NULL != $city_info) {
                            $city = $city_info->city;
                            $length = mb_strlen($city);
                            $city = mb_substr($city, 0, $length - 1);
                            $coupon_lists[$key]['coupon_scope_text'] = $city;
                        } else {
                            $coupon_lists[$key]['coupon_scope_text'] = '合肥市';
                        }
                        break;
                    case 3 : // 地区
                        $coupon_lists[$key]['coupon_scope'] = 3;
                        $area_info = $this->getAreaById($area_id);
                        $area = $area_info->area;
                        $coupon_lists[$key]['coupon_scope_text'] = $area;
                        break;
                    default : 
                        $coupon_lists[$key]['coupon_scope'] = 0;
                        $coupon_lists[$key]['coupon_scope_text'] = '全国';
                        break;
                }

                // set_coupon_status 优惠券的状态
                // is_show 1 展示 0 不展示 
                // is_open 1 开启 2 不开启
                if (1 == $value->is_open && 1 == $value->is_show) { 
                    $coupon_lists[$key]['set_coupon_status'] = 2;
                    $coupon_lists[$key]['coupon_status_name'] = '已发放';
                } else {
                    $coupon_lists[$key]['set_coupon_status'] = 1;
                    $coupon_lists[$key]['coupon_status_name'] = '未发放';
                }

                if ($nowtime > $coupon_expirtime) {
                    $coupon_lists[$key]['set_coupon_status'] = 3;
                    $coupon_lists[$key]['coupon_status_name'] = '已过期';
                } 

                $coupon_total_num = $value->coupon_total_num;
                $coupon_get_num = $value->coupon_get_num;
                if ( 0 >= intval($coupon_total_num - $coupon_get_num) ) {
                    $coupon_lists[$key]['set_coupon_status'] = 4;
                    $coupon_lists[$key]['coupon_status_name'] = '已领完';
                }

                $coupon_lists[$key]['is_open'] = $value->is_open; // 1:开启 2:不开启
                $coupon_lists[$key]['is_show'] = $value->is_show; // 1:展示 0:不展示
            }
        }
        
        $data = [
            'code'  => 200,
            'msg'   => '获取教练优惠券成功',
            'data'  => ['couponlist' => $coupon_lists],
        ]; 
        return response()->json($data);
    }

    /**
     * 教练添加|编辑优惠券
     * @param string coupon_name    券名
     * @param number coupon_value   券面值
     * @param number coupon_num     券的总数
     * @param number province_id    省ID
     * @param number city_id        市ID
     * @param number expiretime     过期时间
     * @param number limit_num      个人领取数量
     * @param string coupon_code    券兑换码
     * @param number is_open        是否开启 1:开启 | 2:不开启
     * @param number is_show        是否展示 1:展示 | 0:不展示
     * @return void
     **/
    public function handleCoachCoupon () {
        
        if ( ! $this->request->has('coupon_name') 
            OR ! $this->request->has('coupon_value') 
            OR ! $this->request->has('coupon_num') 
            OR ! $this->request->has('province_id') 
            OR ! $this->request->has('city_id') 
            OR ! $this->request->has('limit_num') 
            OR ! $this->request->has('coupon_code') 
            OR ! $this->request->has('expiretime') 
            OR ! $this->request->has('is_open') 
            OR ! $this->request->has('is_show') ) 
        {
            $data = [
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass,
            ];
            Log::error('异常：【教练添加|编辑优惠券】缺少请求的参数');
            return response()->json($data);
        }

        $nowtime = time();
        $expiretime = $this->request->input('expiretime');   
        $expire_stamptime = strtotime($expiretime); 
        if ( $expire_stamptime < $nowtime ) {
            $data = [
                'code'  => 400,
                'msg'   => '过期时间不可小于当前时间',
                'data'  => new \stdClass,
            ];
            Log::error('异常：【教练添加|编辑优惠券】中过期时间小于当前时间');
            return response()->json($data);
        }

        $user = (new AuthController)->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $phone = $user['phone'];

        $coach_id = $this->user->getCoachIdByUserId($user_id);
        if ( ! $coach_id) {
            $data = [
                'code'  => 400,
                'msg'   => '教练信息异常',
                'data'  => new \stdClass,
            ];
            Log::error('异常：【教练添加|编辑优惠券】获取不到该用户对应的教练ID');
            return response()->json($data);
        }

        $coach_info = $this->user->getCoachInfoById($coach_id);
        if ($coach_info->coach_name) {
            $coach_name = $coach_info->coach_name;
        } else {
            $coach_name = '嘻哈用户'.substr($coach_info->coach_phone, -4, 4);
        }

        $coupon_name = (string)$this->request->input('coupon_name');

        $coupon_value = intval($this->request->input('coupon_value'));

        $coupon_num = intval($this->request->input('coupon_num'));

        $province_id = intval($this->request->input('province_id'));

        $city_id = intval($this->request->input('city_id'));

        $limit_num = intval($this->request->input('limit_num'));

        $coupon_code = (string)$this->request->input('coupon_code');

        $is_open = intval($this->request->input('is_open')) ? intval($this->request->input('is_open')) : 2; // 默认不开启

        $is_show = intval($this->request->input('is_show')) ? intval($this->request->input('is_show')) : 0; // 默认不展示

        // 券的范围
        // 0:全国 | 1:全省 | 2:全市 | 3:地区
        if ($province_id != 0) {
            $coupon_scope = 1; // 全省

        } else if ($city_id != 0) {
            $coupon_scope = 2; // 全市

        } else {
            $coupon_scope = 0; // 全国
        }

        if ( ! $this->request->has('coupon_id')) {
            $checkcouponcode = $this->checkCouponCode($coupon_code);
            if ($checkcouponcode) {
                $data = [
                    'code'  => 400,
                    'msg'   => '请重新生成新的兑换码',
                    'data'  => new \stdClass,
                ];
                Log::Info('异常：【教练添加优惠券】添加的教练优惠券的兑换码已经存在，需重新生成新的兑换码');
                return response()->json($data);
            }

            $coupon_id = DB::table('coupon')
                ->insertGetId([
                    'owner_type'        => 1, // 1:教练 2:驾校
                    'owner_id'          => $coach_id,
                    'owner_name'        => $coach_name,
                    'coupon_name'       => $coupon_name,
                    'coupon_desc'       => $coach_name.'在教练端添加',
                    'coupon_total_num'  => $coupon_num,
                    'coupon_get_num'    => 0,
                    'addtime'           => $nowtime,
                    'expiretime'        => $expire_stamptime,
                    'coupon_value'      => $coupon_value,
                    'coupon_category_id'=> 1, // 券种类 1：现金券
                    'coupon_code'       => $coupon_code,
                    'coupon_limit_num'  => $limit_num,
                    'province_id'       => $province_id,
                    'city_id'           => $city_id,
                    'area_id'           => 0,
                    'updatetime'        => 0,
                    'coupon_scope'      => $coupon_scope,
                    'order'             => 0,
                    'scene'             => 1, // 1:报名班制（默认） 2：预约学车
                    'is_open'           => $is_open, // 1:开启 2：不开启
                    'is_show'           => $is_show, // 1:展示 0：不展示
                ]);
            if ($coupon_id) {
                $data = [
                    'code'  => 200,
                    'msg'   => '添加成功',
                    'data'  => 'ok',
                ];
            } else {
                $data = [
                    'code'  => 400,
                    'msg'   => '添加失败',
                    'data'  => '',
                ];
            }
            // end add coach coupon

        } else { // update coach coupon

            $coupon_id = $this->request->input('coupon_id');
            $update_data = [
                'owner_type'        => 1, // 1:教练 2:驾校
                'owner_id'          => $coach_id,
                'owner_name'        => $coach_name,
                'coupon_name'       => $coupon_name,
                'coupon_desc'       => $coach_name.'在教练端添加',
                'coupon_total_num'  => $coupon_num,
                'coupon_get_num'    => 0,
                'addtime'           => $nowtime,
                'expiretime'        => $expire_stamptime,
                'coupon_value'      => $coupon_value,
                'coupon_category_id'=> 1, // 券种类 1：现金券
                'coupon_code'       => $coupon_code,
                'coupon_limit_num'  => $limit_num,
                'province_id'       => $province_id,
                'city_id'           => $city_id,
                'updatetime'        => $nowtime,
                'coupon_scope'      => $coupon_scope,
                'scene'             => 1, // 1:报名班制（默认） 2：预约学车
                'is_open'           => $is_open, // 1:开启 2：不开启
                'is_show'           => $is_show, // 1:展示 0：不展示
            ];

            $update_ok = DB::table('coupon')
                ->where('id', '=', $coupon_id)
                ->update($update_data);
            if ($update_ok >= 1) {
                $data = [
                    'code'  => 200,
                    'msg'   => '更新成功',
                    'data'  => 'ok'
                ];
            } else {
                $data = [
                    'code'  => 400,
                    'msg'   => '更新失败',
                    'data'  => ''
                ];
            }

            // end update coach coupon
        }

        return response()->json($data);
     }

    /**
     * 获取优惠券信息
     * @param string token 用户登录标识
     * @param number coupon_id 优惠券ID
     * @return void
     **/
    public function getCouponInfo () {

        if ( ! $this->request->input('coupon_id')) {
            $data = [
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass,
            ];
            Log::error('异常：【获取优惠券信息】缺少参数优惠券ID');
            return response()->json($data);
        }

        $coupon_id = $this->request->input('coupon_id');
        $user = (new AuthController)->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $coach_id = $this->user->getCoachIdByUserId($user_id);
        if ( ! $coach_id) {
            $data = [
                'code'  => 400,
                'msg'   => '教练信息异常',
                'data'  => new \stdClass,
            ];
            Log::error('异常：【获取教练优惠券信息】获取不到该用户对应的教练ID');
            return response()->json($data);
        }

        $whereCondition = [
            'id'            => $coupon_id,
            'owner_id'      => $coach_id,
            'owner_type'    => 1 // 1：教练 2：驾校
        ];

        $couponlist = DB::table('coupon')
            ->select(
                'id as coupon_id',
                'owner_id',
                'owner_type',
                'owner_name',
                'coupon_total_num',
                'expiretime',
                'coupon_value',
                'coupon_code',
                'coupon_limit_num',
                'province_id',
                'city_id',
                'area_id',
                'coupon_scope',
                'is_open',
                'is_show'
            ) 
            ->where($whereCondition)
            ->first();

        if ($couponlist) {

            $couponlist->year = intval(date('Y', $couponlist->expiretime));
            $couponlist->month = intval(date('m', $couponlist->expiretime));
            $couponlist->day = intval(date('d', $couponlist->expiretime));
            $couponlist->hour = intval(date('H', $couponlist->expiretime));
            $couponlist->minute = intval(date('i', $couponlist->expiretime));
            $couponlist->seconds = intval(date('s', $couponlist->expiretime));
            $couponlist->expiretime = date('Y-m-d H:i:s', $couponlist->expiretime);
            
            $data = [
                'code' => 200,
                'msg' => '获取成功',
                'data'  => $couponlist
            ];

        } else {
            $data = [
                'code'  => 1002,
                'msg'   => '该优惠券已不存在',
                'data'  => new \stdClass,
            ];
            Log::Info('异常：【获取教练优惠券信息】获取的优惠券可能不是当前教练的 | 优惠券已被删除');
        }

        return response()->json($data);

    }

    /**
     * 删除优惠券
     * @param string token 用户登录标识
     * @param number coupon_id 优惠券ID
     * @return void
     **/
    public function deleteCoupon () {
        if ( ! $this->request->input('coupon_id')) {
            $data = [
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass,
            ];
            Log::error('异常：【教练删除优惠券】缺少参数优惠券ID');
            return response()->json($data);
        }

        $coupon_id = $this->request->input('coupon_id');
        $user = (new AuthController)->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $coach_id = $this->user->getCoachIdByUserId($user_id);
        if ( ! $coach_id) {
            $data = [
                'code'  => 400,
                'msg'   => '教练信息异常',
                'data'  => new \stdClass,
            ];
            Log::error('异常：【教练删除优惠券】获取不到该用户对应的教练ID');
            return response()->json($data);
        }

        $whereCondition = [
            'id'            => $coupon_id,
            'owner_id'      => $coach_id,
            'owner_type'    => 1 // 1：教练 2：驾校
        ];

        $couponlist = DB::table('coupon')
            ->select('coupon.*') 
            ->where($whereCondition)
            ->first();
        if ( ! $couponlist) {
            $data = [
                'code'  => 1002,
                'msg'   => '该优惠券已不存在',
                'data'  => new \stdClass,
            ];
            Log::Info('异常：【教练删除优惠券】获取的优惠券可能不是当前教练的 | 优惠券已被删除');
            return response()->json($data);
        }

        $delcoupon = DB::table('coupon')
            ->where($whereCondition)
            ->delete();
        if ($delcoupon) {
            $data = [
                'code'  => 200,
                'msg'   => '删除成功',
                'data'  => 'ok'
            ];
        } else {
            $data = [
                'code'  => 400,
                'msg'   => '删除失败',
                'data'  => ''
            ];
        }

        return response()->json($data);
    }

    /**
     * 设置教练优惠券的发布状态
     * @param string token 用户登录标识
     * @param number is_open 是否开启  1：是 | 2：否
     * @param number is_show 是否展示  1：是 | 0：否
     * @return void
     **/
    public function setCouponStatus () {
        
        if ( ! $this->request->has('coupon_id')
            OR ! $this->request->has('is_open')
            OR ! $this->request->has('is_show')
        ) {
            $data = [
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass
            ];
            Log::error('异常：【设置教练优惠券的状态】缺少请求的参数');
            return response()->json($data);
        }

        $user = (new AuthController)->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $coupon_id = $this->request->input('coupon_id');
        $is_open = intval($this->request->input('is_open'));
        $is_show = intval($this->request->input('is_show'));
        $coach_id = $this->user->getCoachIdByUserId($user_id);
        if ( ! $coach_id) {
            $data = [
                'code'  => 400,
                'msg'   => '教练信息异常',
                'data'  => new \stdClass,
            ];
            Log::error('异常：【设置教练优惠券的状态】获取不到该用户对应的教练ID');
            return response()->json($data);
        }

        $whereCondition = [
            'id'            => $coupon_id,
            'owner_id'      => $coach_id,
            'owner_type'    => 1 // 1：教练 2：驾校
        ];

        $couponlist = DB::table('coupon')
            ->select('coupon.*') 
            ->where($whereCondition)
            ->first();
        if ( ! $couponlist) {
            $data = [
                'code'  => 1002,
                'msg'   => '该优惠券已不存在',
                'data'  => new \stdClass,
            ];
            Log::Info('异常：【设置教练优惠券的状态】获取的优惠券可能不是当前教练的 | 优惠券已被删除');
            return response()->json($data);
        } 

        $open = intval($couponlist->is_open);
        $show = intval($couponlist->is_show);
        if ($is_open != $open OR $is_show != $show) {
            $data = [
                'code'  => 400,
                'msg'   => '该优惠券的状态出现异常',
                'data'  => ''
            ];
            Log::Info('异常：【设置教练优惠券的状态】请求的优惠券的状态与数据表中的状态不一致');
            return response()->json($data);
        }

        if (1 == $is_open) { // 1:已发放

            $update_data = [
                'is_open' => 2, // 未开启
                'is_show' => 1, // 展示
                'updatetime'=> time(), 
            ];

        } elseif (2 == $is_open) { // 2：未发放

            $update_data = [
                'is_open'   => 1, // 开启
                'is_show'   => 1, // 展示
                'updatetime'=> time(), 
            ];
        }
        
        $update_ok = DB::table('coupon')
            ->where($whereCondition)
            ->update($update_data);
        if ($update_ok >= 1) {
            $data = [
                'code'  => 200,
                'msg'   => '设置成功',
                'data'  => 'ok'
            ];
        } else {
            $data = [
                'code'  => 400,
                'msg'   => '设置失败',
                'data'  => ''
            ];
        }

        return response()->json($data);

    }
    
    /**
     * 获取领取人列表
     * @param string token 用户登录标识
     * @param number coupon_id 优惠券ID
     * return void
     **/
    public function getUserCouponList () {

        if ( ! $this->request->input('coupon_id')) {
            $data = [
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass,
            ];
            Log::error('异常：【获取领取人列表】缺少参数优惠券ID');
            return response()->json($data);
        }

        $coupon_id = $this->request->input('coupon_id');
        $user = (new AuthController)->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $coach_id = $this->user->getCoachIdByUserId($user_id);
        if ( ! $coach_id) {
            $data = [
                'code'  => 400,
                'msg'   => '教练信息异常',
                'data'  => new \stdClass,
            ];
            Log::error('异常：【获取领取人列表】获取不到该用户对应的教练ID');
            return response()->json($data);
        }

        $whereCondition = [
            'coupon.id'            => $coupon_id,
            'coupon.owner_id'      => $coach_id,
            'coupon.owner_type'    => 1 // 1：教练 2：驾校
        ];

        $couponlist = DB::table('coupon')
            ->select('coupon.*') 
            ->where($whereCondition)
            ->first();
        if ( ! $couponlist) {
            $data = [
                'code'  => 1002,
                'msg'   => '该优惠券已不存在',
                'data'  => new \stdClass,
            ];
            Log::Info('异常：【获取领取人列表】获取的优惠券可能不是当前教练的 | 优惠券已被删除');
            return response()->json($data);
        }

        $usercouponlist = DB::table('user_coupon')
            ->select(
                'user_coupon.id as id',
                'user_coupon.coupon_id',
                'user_coupon.user_name',
                'user_coupon.user_phone',
                'user_coupon.coupon_name',
                'user_coupon.coupon_code',
                'user_coupon.coupon_value',
                'user_coupon.coupon_sender_owner_id as sender_id',
                'user_coupon.coupon_sender_owner_type as sender_type',
                'user_coupon.coupon_status'
            )
            ->where([
                
                ['user_coupon.coupon_id', '=', $coupon_id],
                ['user_coupon.coupon_sender_owner_id', '=', $coach_id],
                ['user_coupon.coupon_sender_owner_type', '=', 1] // 1:coach 2:school
            ])
            ->get();
        if ($usercouponlist) {
            foreach ($usercouponlist as $key => $value) {
                $user_phone = $value->user_phone;
                $user_info = DB::table('user')
                    ->select(
                        'users_info.photo_id',
                        'users_info.user_photo as user_imgurl'
                    )
                    ->leftJoin('users_info', 'users_info.user_id', '=', 'user.l_user_id')
                    ->where([
                        ['user.i_user_type', '=', 0], // 0:学员 1:教练
                        ['user.s_phone', '=', $value->user_phone]
                    ])
                    ->first();
                if ($user_info) {
                    $usercouponlist[$key]->user_imgurl = $this->buildUrl($user_info->user_imgurl);
                    $usercouponlist[$key]->photo_id = $user_info->photo_id;
                } else {
                    $usercouponlist[$key]->user_imgurl = '';
                    $usercouponlist[$key]->photo_id = 1;
                }
            }

            $data = [
                'code'  => 200,
                'msg'   => '',
                'data'  => ['usercouponlist' => $usercouponlist]
            ];
        } else {
            $data = [
                'code'  => 200,
                'msg'   => '获取成功',
                'data'  => new \stdClass,
            ];
        }

        return response()->json($data);

    }

    /**
     * 生成优惠券兑换码
     * @param number count 需生成的个数
     * @param string token 用户登录标识
     * @return void
     **/
    public function getCouponCodeList () {
        $user = (new AuthController)->getUserFromToken($this->request->input('token'));
        if ( ! $this->request->has('count')) {
            $count = 1;
        } else {
            $count = $this->request->input('count');
        }
        
        $start = 1;
        $couponcode = [];
        for (; $start <= $count; $start++) {
            $code = $this->guid();
            $code = strtoupper(md5(substr($code, -6, 6)));
            $couponcode[] = substr($code, -6, 6);
        }

        $couponcodelist = implode(',', $couponcode);
        $data = [
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => ['couponcode' => $couponcodelist],
        ];
        return response()->json($data);
    }

    /**
     * 获取省的相关信息
     * @param
     * @return void
     **/
    public function getProvinceList () {

        $province_list = DB::table('province')
            ->select('provinceid', 'province')
            ->get();

        $data = [
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => $province_list
        ];

        return response()->json($data);

    }

    /**
     * 获取市的相关信息
     * @param number provinceid 省ID
     * @return void
     **/
    public function getCityList () {

        if ( ! $this->request->has('provinceid')) {
            $data = [
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass
            ];
            Log::error('异常：【获取市的相关信息】缺少参数省ID');
            return response()->json($data);
        }

        $provinceid = $this->request->input('provinceid');

        $city_list = DB::table('city')
            ->select(
                'cityid', 
                'city'
            )
            ->where('fatherid', '=', $provinceid)
            ->orderBy('is_hot', 'asc')
            ->get();
        
        $data = [
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => $city_list
        ];
        
        return response()->json($data);

    }

    /**
     * 获取地区的相关信息
     * @param number areaid 市ID
     * @return void
     **/
    public function getAreaList () {

        if ( ! $this->request->has('cityid')) {
            $data = [
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass
            ];
            Log::error('异常：【获取地区的相关信息】缺少参数市ID');
            return response()->json($data);
        }

        $cityid = $this->request->input('cityid');

        $area_list = DB::table('area')
            ->select(
                'areaid', 
                'area'
            )
            ->where('fatherid', '=', $cityid)
            ->get();
        
        $data = [
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => $area_list
        ];
        
        return response()->json($data);

    }

    /**
     * 检查兑换码是否重复
     * @param coupon_code 
     * @return void
     **/
    private function checkCouponCode ($coupon_code) {

        $couponcode = DB::table('coupon')
            ->select('coupon.coupon_code')
            ->where('coupon_code', '=', $coupon_code)
            ->first();
        if ($couponcode) {
            return $couponcode;
        } else {
            return false;
        }

    }

    /**
     * get province
     * @param province_id 
     * @return province_info
     **/
    public function getProvinceById ( $province_id ) {
        $province = DB::table('province')
            ->select('*')
            ->where('provinceid', '=', $province_id)
            ->first();
        if ($province) {
            return $province;
        } else {
            return NULL;
        }
    }

    /**
     * get city
     * @param city_id 
     * @return city_info
     **/
    public function getCityById ( $city_id ) {
        $city = DB::table('city')
            ->select('*')
            ->where('cityid', '=', $city_id)
            ->first();
        if ($city) {
            return $city;
        } else {
            return NULL;
        }
    }

    /**
     * get area
     * @param area_id 
     * @return area_info
     **/
    public function getAreaById ( $area_id ) {
        $area = DB::table('area')
            ->select('*')
            ->where('areaid', '=', $area_id)
            ->first();
        if ($area) {
            return $area;
        } else {
            return NULL;
        }
    }









}





