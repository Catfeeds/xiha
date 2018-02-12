<?php

/**
 * 测试模块
 * lumen查询构造器 https://laravel-china.org/docs/5.3/queries
 * @return void
 * @author
 **/

namespace App\Http\Controllers\v1;

use Exception;
use Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\OrderController;
use App\Http\Controllers\v1\UserController;
use Illuminate\Http\Request;

class CoachController extends Controller {

    protected $request;
    protected $auth;
    protected $order;
    protected $user;

    public function __construct(Request $request) {
        $this->request = $request;
        $this->auth = new AuthController();
        $this->order = new OrderController($this->request);
        $this->user = new UserController($this->request);
    }

    // 获取教练时间配置
    public function getCoachTimeList() {
        if(!$this->request->has('coach_id') || !$this->request->has('year') || !$this->request->has('month') || !$this->request->has('day')) {
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => ''
            ]);
        }
        $coach_id   = $this->request->input('coach_id');
        $user_school_id = $this->request->input('school_id'); // 班制对应的school_id
        $coach_school_id = DB::table('coach')->where('l_coach_id', $coach_id)->value('s_school_name_id'); // 教练所对应的school_id
        $sh_type    = $this->request->input('sh_type'); // 班制类型 1：计时班
        $year       = $this->request->input('year');
        $month      = $this->request->input('month');
        $day        = $this->request->input('day');
        $user       = (new AuthController())->getUserFromToken($this->request->input('token'));
        $user_id    = $user['user_id'];

        // 获取学员科目牌照等信息
        $user_info = $this->user->getUserInfoById($user_id);
        $license_no = isset($user_info->license_name) && $user_info->license_name != '' ? $user_info->license_name : '';
        $lesson_no = isset($user_info->lesson_id) ? $user_info->lesson_id : 0;

        if($license_no === '') {
            return response()->json([
                'code' => 1000,
                'msg'  => '请在我的个人资料中设置考证类型',
                'data' => ''
            ]);
        }
        if($lesson_no === 0) {
            return response()->json([
                'code' => 1000,
                'msg'  => '请在我的科目中设置您的科目阶段',
                'data' => ''
            ]);
        }
        $lesson_arr = [
            '1' => '科目一',
            '2' => '科目二',
            '3' => '科目三',
            '4' => '科目四',
        ];

        $license_list = DB::table('license_config')
            ->select(
                'license_id',
                'license_name'
            )
            ->where([
                ['is_open', '=', 1],
            ])
            ->get();
        $license_arr = [];
        foreach ($license_list as $license) {
            $license_arr[$license->license_id] = $license->license_name;
        }

        if(!in_array($lesson_no, array_keys($lesson_arr))) {
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => ''
            ]);
        }

        $coach_info = $this->user->getCoachInfoById($coach_id);

        if(empty($coach_info)) {
            return response()->json([
                'code' => 1001,
                'msg'  => '当前教练账号异常或不接单，请预约其他教练',
                'data' => new \Stdclass()
            ]);
        }
        $coach_lesson_name = '';
        $coach_license_name = '';
        $school_id = $coach_info->school_id ? $coach_info->school_id : 0;
        $coach_license_id_arr = explode(',', $coach_info->license_id);
        $coach_lesson_id_arr = explode(',', $coach_info->lesson_id);

        foreach($coach_lesson_id_arr as $lesson) {
            if (isset($lesson_arr[$lesson])) {
                $coach_lesson_name .= $lesson_arr[$lesson];
            }
        }
        foreach($coach_license_id_arr as $license) {
            if (isset($license_arr[$license])) {
                $coach_license_name .= $license_arr[$license];
            }
        }

        $coach_lesson_name = isset($lesson_arr[$coach_info->lesson_id]) ? $lesson_arr[$coach_info->lesson_id] : '';

        $_data = [];
        $times_list = [];
        $time_ids = '';
        $cancel_order_time = 2; // 取消次数 取消次数过多，当天就不可被预约
        $cancel_in_advance = 2; // 取消订单需要提前小时数
        $sum_appoint_time = 2; // 总共可被预约时间段
        $pre_order_limit_time = 1; // 下单时间提前一个小时下单
        $is_automatic = 1; // 是否自动生成时间段 1：自动生成 2：不自动生成
        $current_time = time();

        // 获取驾校的所设置的时间计时配置
        $school_config = DB::table('school_config')
            ->select(
                'i_cancel_order_time',
                'cancel_in_advance',
                'i_sum_appoint_time',
                's_time_list',
                'is_automatic'
            )
            ->where(['l_school_id'=>$school_id])
            ->first();

        if(!empty($school_config)) {
            $time_ids = $school_config->s_time_list;
            $cancel_order_time = $school_config->i_cancel_order_time;
            $cancel_in_advance = $school_config->cancel_in_advance;
            $sum_appoint_time = $school_config->i_sum_appoint_time;
            $is_automatic = $school_config->is_automatic;
        }
        $time_ids_arr = $time_ids == '' ? [] : explode(',', $time_ids);

        // 获取默认时间配置表的时间配置
        $default_time_config = $this->getDefaultTimeConfig($time_ids_arr);

        // 获取当前时间段教练设置的学车时间
        $current_coach_time = $this->getCurrentCoachTimeConfig($default_time_config, $coach_id, $year, $month, $day);

        // 教练的当前天的时间主动关闭了
        if (null === $current_coach_time) {
            return response()->json([
                'code' => 1001,
                'msg'  => '教练关闭了今天的教学时间哦',
                'data' => [
                    'times_list' => [],
                    'tips_info' => [
                        'cancel_order_time'     => $cancel_order_time,
                        'cancel_in_advance'     => $cancel_in_advance,
                        'sum_appoint_time'      => $sum_appoint_time,
                        'pre_order_limit_time'  => $pre_order_limit_time,
                        'tips'                  => '请提前'.$pre_order_limit_time.'个小时预约|如需取消预约学车的订单请提前'.$cancel_in_advance.'小时|每天最多可预约'.$sum_appoint_time.'个时间段|每天最多取消'.$cancel_order_time.'个时间段，否则当天将不可预约',
                    ],
                ],
            ]);
        }

        // is_automatic = 1-自动生成 2-不自动生成
        if($is_automatic == 2) {
            // 不自动生成时间
            if(empty($current_coach_time)) {
                // 此情况是教练设置了时间，但是将所有时间都关闭了的，不应当再取默认的时间设置
                $times_list = $default_time_config;
                return response()->json([
                    'code' => 1001,
                    'msg'  => '教练关闭了今天的教学时间哦',
                    'data' => [
                        'times_list' => [],
                        'tips_info' => [
                            'cancel_order_time'     => $cancel_order_time,
                            'cancel_in_advance'     => $cancel_in_advance,
                            'sum_appoint_time'      => $sum_appoint_time,
                            'pre_order_limit_time'  => $pre_order_limit_time,
                            'tips'                  => '请提前'.$pre_order_limit_time.'个小时预约|如需取消预约学车的订单请提前'.$cancel_in_advance.'小时|每天最多可预约'.$sum_appoint_time.'个时间段|每天最多取消'.$cancel_order_time.'个时间段，否则当天将不可预约',
                        ],
                    ],
                ]);
                $times_list = [];
            } else {
                $times_list = $current_coach_time;
            }

        } else {
            // 自动生成时间
            if(!empty($current_coach_time)) {
                $times_list = $current_coach_time;
            } else {
                $times_list = $default_time_config;
            }
        }

        // 如果此时列表为空，是教练没有设置可预约的时间
        if(empty($times_list)) {
            return response()->json([
                'code'=> 1001,
                'msg'=> '此教练还没有开放时间段呢，要不我们换一个教练约吧！',
                'data'=> new \Stdclass()
            ]);
        }

        // 获取当前时间段是否被预约或者过期
        $appoint_order_list = $this->order->getCoachAppointOrders($coach_id, $year, $month, $day);

        $user_study_orders_info = $this->order->getStudyOrderInfo($coach_id, $year, $month, $day, $user_id);
        $user_study_orders_time_num = 0;
        $_time_configs_id_arr = [];
        if (!empty($user_study_orders_info)) {
            foreach ($user_study_orders_info as $study => $study_time) {
                if (!empty($study_time->time_config_id)) {
                    $_time_configs_id_arr = explode(',', $study_time->time_config_id);
                    $user_study_orders_time_num += count($_time_configs_id_arr);
                }
            }
        }

        $_list = [];
        foreach ($times_list as $key => $value) {
            // 构建时间段最低时间
            $min_start_time = strtotime($year.'-'.$month.'-'.$day.' '.$value['start_time_format']);
            $times_list[$key]['is_appointed'] = 2; // 可预约
            $times_list[$key]['money_unit'] = '￥';
            $times_list[$key]['price_unit'] = '元/人';
            // 判断当前用户报名的班制是否满预约足计时的价格变动 (非计时班&&学员报名的班制正是教练所在驾校 价格为0)
            if(1 != $sh_type && $school_id == $user_school_id) {
                $times_list[$key]['price'] = '0';
            }
            // 不支持计时的话，不能约
            if ($coach_info->order_receive_status == 0 || $coach_info->timetraining_supported == 0) {
                $times_list[$key]['is_appointed'] = 1; // 不可预约
            }
            if(!empty($appoint_order_list->toArray())) {
                foreach($appoint_order_list as $k => $v) {
                    $appoint_time_config_ids = array_filter(explode(',', $v->time_config_id));
                    if(in_array($value['id'], $appoint_time_config_ids)) {
                        $times_list[$key]['is_appointed'] = 1; // 不可预约（订单被预约）
                    }
                    if($min_start_time - $current_time < $pre_order_limit_time * 3600) {
                        $times_list[$key]['is_appointed'] = 1; // 不可预约（时间过期）
                    }
                }
            } else {
                if($min_start_time - $current_time < $pre_order_limit_time * 3600) {
                    $times_list[$key]['is_appointed'] = 1; // 不可预约（时间过期）
                }
            }

            try {

                if ( ! in_array($lesson_no, $coach_lesson_id_arr)) {
                    $times_list[$key]['is_appointed'] = 1; // 不可预约(学员与教练的科目不一致)
                }

                $coach_license_name_arr = [];
                foreach($coach_license_id_arr as $license) {
                    $coach_license_name_arr[$license]= $license_arr[$license];
                }

                if ( ! in_array($license_no, $coach_license_name_arr)) {
                    $times_list[$key]['is_appointed'] = 1; // 不可预约(学员与教练的牌照不一致)
                }
            } catch(Exception $e){
                Log::Info('File:'.$e->getFile().'Line:'.$e->getLine().',Error:'.$e->getMessage());
            }

            // 当天预约次数已超过
            if ($user_study_orders_time_num >= $sum_appoint_time) {
                $times_list[$key]['is_appointed'] = 1; // 不可预约（时间过期）
            }

            $times_list[$key]['license_no'] = $license_arr[$user_info->license_id];
            $times_list[$key]['subjects'] = $user_info->lesson_name;
            $_list[] = $times_list[$key];
        }
        $times_list = $_list;
        usort($times_list, function($a, $b) {
            if ($a['start_time'] == $b['start_time']) {
                return 0;
            }
            return ($a['start_time'] < $b['start_time']) ? -1 : 1;
        });

        // 如果有免费quota，修改价格为0元
        $quota = 0;
        $signed_license_name = '';
        $signup_order_info = DB::table('school_orders')
            ->select(
                'id as order_id',
                'so_licence as license_name',
                'free_study_hour'
            )
            ->where([
                ['school_orders.so_order_status', '<>', 101],
                ['school_orders.so_user_id', '=', $user_id],
            ])
            ->where(function ($query) {
                $query->where(function ($query) {
                    // 线下支付，已付款
                    $query->where('school_orders.so_pay_type', '=', 2)
                        ->where('school_orders.so_order_status', '=', 3);
                })
                    ->orWhere(function ($query) {
                        // 线上支付(支付宝1, 微信3, 银联4)，已付款
                        $query->whereIn('school_orders.so_pay_type', [1, 3, 4])
                            ->where('school_orders.so_order_status', '=', 1);
                    });
            })
            ->orderBy('id', 'desc')
            ->limit(1)
            ->first();

        if ($signup_order_info) {
            if (isset($signup_order_info->free_study_hour) && intval($signup_order_info->free_study_hour) >= 0) {
                $free_study_hour = intval($signup_order_info->free_study_hour);
                $appoint_order_info = DB::table('study_orders')
                    ->select(
                        DB::raw('SUM(i_service_time) as total_study_hour')
                    )
                    ->where([
                        ['study_orders.l_user_id', '=', $user_id],
                        ['study_orders.dc_money', '<=', 0],
                    ])
                    ->whereIn('study_orders.i_status', [1, 2, 1003, 1001]) // 已付款1 ，已完成2 ，未付款1003, 付款中1001
                    ->first();
                if ($appoint_order_info && isset($appoint_order_info->total_study_hour)) {
                    $total_study_hour = intval($appoint_order_info->total_study_hour);
                } else {
                    $total_study_hour = 0;
                }

                $quota = ($free_study_hour-$total_study_hour) > 0 ? ($free_study_hour-$total_study_hour) : 0;
            }

            if (isset($signup_order_info->license_name)) {
                if (!empty($signup_order_info->license_name)) {
                    $signed_license_name = $signup_order_info->license_name;
                } else {
                    Log::Info('报名的班制信息不全，license_name为空'.json_encode($user_info, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                }
            }
        }

        if ($quota > 0 || !empty($signed_license_name)) {
            // 有免费的quota哦
            foreach ($times_list as $index => $time) {
                if ($quota > 0 && $coach_school_id == $user_school_id) {
                    if (isset($time['price'])) {
                        $times_list[$index]['price'] = 0.00;
                    }
                }

                if (!empty($signed_license_name)) {
                    if ($signed_license_name != $license_no) {
                        $times_list[$index]['is_appointed'] = 1; // 因为预约计时的班制与您报名的班制不符，不可预约
                    }
                }
            }
        }
        // 如果有免费quota，修改价格为0元

        $_data['times_list'] = $times_list;
        $_data['tips_info'] = [
            'cancel_order_time'     => $cancel_order_time,
            'cancel_in_advance'     => $cancel_in_advance,
            'sum_appoint_time'      => $sum_appoint_time,
            'pre_order_limit_time'  => $pre_order_limit_time,
            'tips'                  => '请提前'.$pre_order_limit_time.'个小时预约|如需取消预约学车的订单请提前'.$cancel_in_advance.'小时|每天最多可预约'.$sum_appoint_time.'个时间段|每天最多取消'.$cancel_order_time.'个时间段，否则当天将不可预约',
        ];
        $data = ['code'=>200, 'msg'=>'获取时间列表成功', 'data'=>$_data];
        return response()->json($data);
    }

    // 获取默认时间配置表中的时间 coach_time_config
    public function getDefaultTimeConfig($time_ids=[]) {
        $query = DB::table('coach_time_config')
            ->select(
                'id',
                'start_time',
                'end_time',
                'license_no',
                'subjects',
                'price',
                'start_minute',
                'end_minute'
            )
            ->where(['status'=>1]);
        if(!empty($time_ids)) {
            $query = $query->whereIn('id', $time_ids);
        }
        $time_list = $query->get();
        $list = [];
        foreach($time_list as $key => $value) {
            $list[$key]['id'] = $value->id;
            $list[$key]['license_no'] = $value->license_no;
            $list[$key]['subjects'] = $value->subjects;
            $list[$key]['price'] = $value->price;
            $list[$key]['start_time'] = $value->start_time;
            $list[$key]['end_time'] = $value->end_time;
            $list[$key]['start_minute'] = $value->start_minute == '0' ? '00' : $value->start_minute;
            $list[$key]['end_minute'] = $value->end_minute == '0' ? '00' : $value->end_minute;
            $list[$key]['start_time_format'] = $value->start_time.':'.$list[$key]['start_minute'];
            $list[$key]['end_time_format'] = $value->end_time.':'.$list[$key]['end_minute'];
            $list[$key]['is_coach_set'] = 2; // 1：是教练设置的时间段 2：驾校设置
        }
        return $list;
    }

    // 获取教练设置的时间 current_coach_time_configuration
    public function getCurrentCoachTimeConfig($default_time_config, $coach_id, $year, $month, $day) {
        $coach_info = $this->user->getCoachInfoById($coach_id);
        $day_time_list = DB::table('current_coach_time_configuration')
            ->select(
                'id',
                'current_time',
                'time_config_money_id',
                'time_config_id',
                'time_lisence_config_id',
                'time_lesson_config_id'
            )
            ->where([
                'coach_id'=>$coach_id,
                'year'=>$year,
                'month'=>$month,
                'day'=>$day
            ])
            ->first();

        $list = [];

        if ( $day_time_list ) {
            // 教练自定义设置了时间
            $time_config_id_arr         = isset($day_time_list->time_config_id) ? array_filter(explode(',', $day_time_list->time_config_id)) : [];
            $time_lisence_config_id_arr = isset($day_time_list->time_lisence_config_id) ? json_decode($day_time_list->time_lisence_config_id, true) : [];
            $time_lesson_config_id_arr  = isset($day_time_list->time_lesson_config_id) ? json_decode($day_time_list->time_lesson_config_id, true) : [];
            $time_money_config_id_arr   = isset($day_time_list->time_config_money_id) ? json_decode($day_time_list->time_config_money_id, true) : [];

            if(!empty($time_config_id_arr)) {
                // 教练有打开的时间段
                foreach($time_config_id_arr as $key => $value) {

                    foreach ($default_time_config as $k => $v) {
                        if($value == $v['id']) {
                            $list[$key]['id'] = $value;
                            $list[$key]['license_no'] = isset($time_lisence_config_id_arr[$value]) ? $time_lisence_config_id_arr[$value] : 'C1';
                            $list[$key]['subjects'] = isset($time_lesson_config_id_arr[$value]) ? $time_lesson_config_id_arr[$value] : '科目二';
                            $list[$key]['price'] = isset($time_money_config_id_arr[$value]) ? $time_money_config_id_arr[$value] : '130';
                            $list[$key]['start_time'] = $v['start_time'];
                            $list[$key]['end_time'] = $v['end_time'];
                            $list[$key]['start_minute'] = $v['start_minute'] == '0' ? '00' : $v['start_minute'];
                            $list[$key]['end_minute'] = $v['end_minute'] == '0' ? '00' : $v['end_minute'];
                            $list[$key]['start_time_format'] = $v['start_time']. ':'.$list[$key]['start_minute'];
                            $list[$key]['end_time_format'] = $v['end_time']. ':'.$list[$key]['end_minute'];
                            $list[$key]['is_coach_set'] = 1; // 1：是教练设置的时间段 2：驾校设置
                            $list[$key]['coach_license_id_list'] = array_filter(explode(',', $coach_info->license_id));
                            $list[$key]['coach_lesson_id_list'] = array_filter(explode(',', $coach_info->lesson_id));
                        }
                    }
                }
            } else {
                // 教练将所有的时间段关闭了
                return null;
            }
        } else {
            // 教练没有自定义设置时间
            // 获取教练设置的上午和下午科目及时间安排，在coach表中
            $stop = 0;
            if ($coach_info) {
                $am_subject = ($coach_info->s_am_subject == '2') ? '科目二' : ( ($coach_info->s_am_subject == '3') ? '科目三' : '科目二');
                $pm_subject = ($coach_info->s_pm_subject == '2') ? '科目二' : ( ($coach_info->s_pm_subject == '3') ? '科目三' : '科目三');
                $am_time_list = array_filter(explode(',', $coach_info->s_am_time_list));
                $pm_time_list = array_filter(explode(',', $coach_info->s_pm_time_list));
                if ( ! empty($am_time_list) ) {
                    foreach($am_time_list as $key => $value) {
                        foreach ($default_time_config as $k => $v) {
                            if($value == $v['id']) {
                                $list[$key]['id'] = $value;
                                $list[$key]['license_no'] = isset($time_lisence_config_id_arr[$value]) ? $time_lisence_config_id_arr[$value] : 'C1';
                                $list[$key]['subjects'] = isset($time_lesson_config_id_arr[$value]) ? $time_lesson_config_id_arr[$value] : '科目二';
                                $list[$key]['price'] = isset($time_money_config_id_arr[$value]) ? $time_money_config_id_arr[$value] : '130';
                                $list[$key]['start_time'] = $v['start_time'];
                                $list[$key]['end_time'] = $v['end_time'];
                                $list[$key]['start_minute'] = $v['start_minute'] == '0' ? '00' : $v['start_minute'];
                                $list[$key]['end_minute'] = $v['end_minute'] == '0' ? '00' : $v['end_minute'];
                                $list[$key]['start_time_format'] = $v['start_time']. ':'.$list[$key]['start_minute'];
                                $list[$key]['end_time_format'] = $v['end_time']. ':'.$list[$key]['end_minute'];
                                $list[$key]['is_coach_set'] = 1; // 1：是教练设置的时间段 2：驾校设置
                                $list[$key]['coach_license_id_list'] = array_filter(explode(',', $coach_info->license_id));
                                $list[$key]['coach_lesson_id_list'] = array_filter(explode(',', $coach_info->lesson_id));
                            }
                        }
                    }
                    $stop = $key+1;
                }

                if ( ! empty($pm_time_list) ) {
                    foreach($pm_time_list as $key_another => $value) {
                        $key = $key_another + $stop;
                        foreach ($default_time_config as $k => $v) {
                            if($value == $v['id']) {
                                $list[$key]['id'] = $value;
                                $list[$key]['license_no'] = isset($time_lisence_config_id_arr[$value]) ? $time_lisence_config_id_arr[$value] : 'C1';
                                $list[$key]['subjects'] = isset($time_lesson_config_id_arr[$value]) ? $time_lesson_config_id_arr[$value] : '科目二';
                                $list[$key]['price'] = isset($time_money_config_id_arr[$value]) ? $time_money_config_id_arr[$value] : '130';
                                $list[$key]['start_time'] = $v['start_time'];
                                $list[$key]['end_time'] = $v['end_time'];
                                $list[$key]['start_minute'] = $v['start_minute'] == '0' ? '00' : $v['start_minute'];
                                $list[$key]['end_minute'] = $v['end_minute'] == '0' ? '00' : $v['end_minute'];
                                $list[$key]['start_time_format'] = $v['start_time']. ':'.$list[$key]['start_minute'];
                                $list[$key]['end_time_format'] = $v['end_time']. ':'.$list[$key]['end_minute'];
                                $list[$key]['is_coach_set'] = 1; // 1：是教练设置的时间段 2：驾校设置
                                $list[$key]['coach_license_id_list'] = array_filter(explode(',', $coach_info->license_id));
                                $list[$key]['coach_lesson_id_list'] = array_filter(explode(',', $coach_info->lesson_id));
                            }
                        }
                    }
                }
            }
        }
        return $list;
    }

    // 获取日期时间配置
    public function getCoachDateList() {

        if(!$this->request->has('coach_id')) {
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => ''
            ]);
        }

        $coach_id = $this->request->input('coach_id');
        $school_id = DB::table('coach')->where('l_coach_id', $coach_id)->value('s_school_name_id');

        $_data = [];
        $tips = '';
        $user = (new AuthController())->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $user_info = $this->user->getUserInfoById($user_id);
        $coach_info = $this->user->getCoachInfoById($coach_id);
        if (isset($coach_info->coach_name) && isset($user_info->license_name) && isset($user_info->lesson_name)) {
            $appoint_desc = '您选择的是: '.$coach_info->coach_name.' | '.$user_info->license_name.' | '.$user_info->lesson_name.' | ';
        }

        $lesson_arr = [
            '1' => '科目一',
            '2' => '科目二',
            '3' => '科目三',
            '4' => '科目四',
        ];

        $license_list = DB::table('license_config')
            ->select(
                'license_id',
                'license_name'
            )
            ->where([
                ['is_open', '=', 1],
            ])
            ->get();
        $license_arr = [];
        foreach ($license_list as $license) {
            $license_arr[$license->license_id] = $license->license_name;
        }

        $license_no = isset($user_info->license_name) && $user_info->license_name != '' ? $user_info->license_name : '';
        $lesson_no = isset($user_info->lesson_id) ? $user_info->lesson_id : 0;
        $coach_license_id_arr = explode(',', $coach_info->license_id);
        $coach_lesson_id_arr = explode(',', $coach_info->lesson_id);

        $coach_lesson_name_arr = [];
        foreach ($coach_lesson_id_arr as $lesson_id) {
            if (isset($lesson_arr[$lesson_id])) {
                $coach_lesson_name_arr[$lesson_id] = $lesson_arr[$lesson_id];
            }
        }
        $coach_lesson_name = implode(',', $coach_lesson_name_arr);

        $coach_license_name_arr = [];
        foreach($coach_license_id_arr as $license) {
            if (isset($license_arr[$license])) {
                $coach_license_name_arr[$license]= $license_arr[$license];
            }
        }

        $coach_license_name = implode(',', $coach_license_name_arr);

        // 获取当前用户报名班制的信息
        $order_info = $this->order->getUserShiftOrders($user_id);
        $_data['shifts_info'] = [
            'tips'=>$appoint_desc.$tips,
            'school_id'=> -1,
            'sh_type'=>-1,
            'sh_title'=>''
        ];

        if(!empty($order_info)) {
            // 有免费quota的时候提示语也顺应修改说明一下
            $quota = 0;
            $signed_license_name = '';
            $signup_order_info = DB::table('school_orders')
                ->select(
                    'id as order_id',
                    'so_licence as license_name',
                    'free_study_hour'
                )
                ->where([
                    ['school_orders.so_order_status', '<>', 101],
                    ['school_orders.so_user_id', '=', $user_id],
                ])
                ->where(function ($query) {
                    $query->where(function ($query) {
                        // 线下支付，已付款
                        $query->where('school_orders.so_pay_type', '=', 2)
                            ->where('school_orders.so_order_status', '=', 3);
                    })
                        ->orWhere(function ($query) {
                            // 线上支付(支付宝1, 微信3, 银联4)，已付款
                            $query->whereIn('school_orders.so_pay_type', [1, 3, 4])
                                ->where('school_orders.so_order_status', '=', 1);
                        });
                })
                ->orderBy('id', 'desc')
                ->first();

            if ($signup_order_info) {
                if (isset($signup_order_info->free_study_hour) && intval($signup_order_info->free_study_hour) >= 0) {
                    $free_study_hour = intval($signup_order_info->free_study_hour);
                    $appoint_order_info = DB::table('study_orders')
                        ->select(DB::raw('SUM(i_service_time) as total_study_hour'))
                        ->where([
                            ['study_orders.l_user_id', '=', $user_id],
                            ['study_orders.dc_money', '<=', 0],
                        ])
                        ->whereIn('study_orders.i_status', [1, 2, 1003, 1001]) // 已付款1 ，已完成2 ，未付款1003, 付款中1001
                        ->first();
                    if ($appoint_order_info && isset($appoint_order_info->total_study_hour)) {
                        $total_study_hour = intval($appoint_order_info->total_study_hour);
                    } else {
                        $total_study_hour = 0;
                    }

                    $quota = ($free_study_hour-$total_study_hour) > 0 ? ($free_study_hour-$total_study_hour) : 0;
                }

                if (isset($signup_order_info->license_name)) {
                    if (!empty($signup_order_info->license_name)) {
                        $signed_license_name = $signup_order_info->license_name;
                    } else {
                        Log::Info('报名的班制信息不全，license_name为空'.json_encode($user_info, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    }
                }
            }
            // 有免费quota的时候提示语也顺应修改说明一下

            if($school_id == $order_info->sh_school_id) {
                if($order_info->sh_type == 1) {
                    $tips = $appoint_desc.'您已报名'.$order_info->sh_title.'('.$order_info->sh_license_name.')，预约练车计时收费';
                } else {
                    $tips = $appoint_desc.'您已报名'.$order_info->sh_title.'('.$order_info->sh_license_name.')，可免费预约练车';
                }
                if ($quota > 0) {
                    $tips .= '(还可免费预约学时:'.$quota.'个)';
                }
            } else {
                    $tips = '您未报名此驾校，将实行计时收费';
            }

            if (!empty($signed_license_name)) {
                if ($signed_license_name != $user_info->license_name) {
                    $tips = $appoint_desc.'您报名的班制'.$signed_license_name.'与您的考证类型'.$user_info->license_name.'不符，因此无法预约！';
                }
            }

            // 不支持计时的话，不能约
            if ($coach_info->order_receive_status == 0 || $coach_info->timetraining_supported == 0) {
                $tips = $appoint_desc.'暂时不支持预约计时';
            }

            try {
                // 科目不一致的情况
                if ( ! in_array($lesson_no, $coach_lesson_id_arr)) {
                    $tips = $appoint_desc.'您的科目'.$user_info->lesson_name.'与该教练设置的科目'.$coach_lesson_name.'不一致';
                }

                // 牌照不一致的情况
                if ( ! in_array($license_no, $coach_license_name_arr)) {
                    $tips = $appoint_desc.'您的牌照'.$user_info->license_name.'与该教练设置的牌照'.$coach_license_name.'不一致';
                }
            } catch(Exception $e){
                Log::Info('File:'.$e->getFile().'Line:'.$e->getLine().',Error:'.$e->getMessage());
            }

            $_data['shifts_info'] = [
                'tips'=>$tips,
                'school_id'=> $order_info->sh_school_id,
                'sh_type'=> $order_info->sh_type,
                'sh_title'=> $order_info->sh_title
            ];
        } else {
            try {
                // 科目不一致
                if ( ! in_array($lesson_no, $coach_lesson_id_arr)) {
                    $tips = $appoint_desc.'您的科目'.$user_info->lesson_name.'与该教练设置的科目'.$coach_lesson_name.'不一致';
                }

                // 牌照不一致
                Log::Info(json_encode($coach_info->license_id,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
                if ( ! in_array($license_no, $coach_license_name_arr)) {
                    $tips = $appoint_desc.'您的牌照'.$user_info->license_name.'与该教练设置的牌照'.$coach_license_name.'不一致';
                }
            } catch(Exception $e){
                Log::Info('File:'.$e->getFile().'Line:'.$e->getLine().',Error:'.$e->getMessage());
            }

            if ($tips) {
                $_data['shifts_info']['tips'] = $tips;

            } else {
                $_data['shifts_info']['tips'] = $appoint_desc.'您未报名此驾校，计时收费';

            }
        }

        $date_list = $this->getDateList(6);
        $_data['date_list'] = $date_list;
        $data = ['code'=>200, 'msg'=>'获取日期成功', 'data'=>$_data];
        return response()->json($data);
    }

    // 获取默认从今天开始的7天日期
    private function getDateList($limit=6) {
        $current_time = time();
        $year = date('Y', $current_time); //年
        $month = intval(date('m', $current_time)); //月
        $day = intval(date('d', $current_time)); //日

        // 构建一个时间
        $build_date_timestamp = mktime(0,0,0,$month,$day,$year);

        // 循环7天日期
        $date_config = array();
        for($i = 0; $i <= $limit; $i++) {
            $date_config[$i]['date'] = date('m-d', $build_date_timestamp + ( 24 * 3600 * $i));
            $date_config[$i]['year'] = intval(date('Y', $build_date_timestamp + ( 24 * 3600 * $i)));
            $date_config[$i]['month'] = intval(date('m', $build_date_timestamp + ( 24 * 3600 * $i)));
            $date_config[$i]['day'] = intval(date('d', $build_date_timestamp + ( 24 * 3600 * $i)));
            $date_config[$i]['date_format'] = intval(date('m', $build_date_timestamp + ( 24 * 3600 * $i))).'-'.intval(date('d', $build_date_timestamp + ( 24 * 3600 * $i)));

        }
        return $date_config;
    }

}
?>
