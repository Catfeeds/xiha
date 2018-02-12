<?php

/**
 * 订单模块
 * lumen查询构造器
 * @return void
 * @author
 **/

namespace App\Http\Controllers\v3\student;

use Exception;
use InvalidArgumentException;
use Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\v3\student\AuthController;
use App\Http\Controllers\v3\student\SignupController;
use App\Http\Controllers\v3\student\CoachController;
use App\Http\Controllers\v3\student\UserController;
use App\Http\Controllers\v3\student\WechatpayController;
use App\Http\Controllers\v3\student\PayController;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Http\Controllers\v3\student\PusherController;
use Xiha\Pay\Pay;

class OrderController extends Controller {

    protected $request;
    protected $auth;
    protected $signup;
    protected $user;
    /**
     * 通用支付
     */
    protected $pay;

    public function __construct(Request $request) {
        $this->request = $request;
        $this->auth = new AuthController();
        $this->signup = new SignupController($this->request);
        $this->user = new UserController($this->request);
        parent::__construct();
    }

        // 获取我的报名班制订单列表
    public function getMySignupOrdersList() {
        if(!$this->request->has('type')) {
            $type = 0;
        }
        // $type 0全部  1待付款  2待评价 3已取消
        $user = (new AuthController())->getUserFromToken($this->request->input('token'));
        $type = $this->request->input('type');
        $page = !$this->request->has('page') ? intval($this->request->input('page')) : 1;
        $phone = $user['phone'];
        $user_id = $user['user_id'];
        $i_user_type = $user['i_user_type'];

        $allow_type = [1, 2, 3, 4, 5]; //
        if(!in_array($type, $allow_type)) {
            $type = 0;
        }
        $commonwhereCondition = [
            ['school_orders.so_user_id', '=', $user_id],
            ['school_orders.so_phone', '=', $phone],
            ['school_orders.so_order_status', '<>', '101']
        ];
        // 获取报名班制订单列表
        // 支付方式： 1：支付宝 2：线下支付 3：微信支付 4：银联支付
        switch ($type) {
        case 1:  // 待付款
            $whereCondition = function($query) {
                $query->where('school_orders.so_order_status', '=', '4')
                    ->whereIn('school_orders.so_pay_type', [1, 3, 4])
                    ->orWhere(function($_query) {
                        $_query->where('school_orders.so_order_status', '=', '1')
                            ->where('school_orders.so_pay_type', '=', '2');
                    });
            };
            break;
        case 2:  // 待评价
            $whereCondition = function($query) {
                $query->where('school_orders.so_order_status', '=', '1')
                    ->whereIn('school_orders.so_pay_type', [1, 3, 4])
                    ->orWhere(function($_query) {
                        $_query->where('school_orders.so_order_status', '=', '3')
                            ->where('school_orders.so_pay_type', '=', '2');
                    });
            };
            break;
        // case 3:
        //     $whereCondition = function($query) {
        //         $query->where('school_orders.so_order_status', '=', '5')->where('so_comment_status', '=', '1')
        //             ->whereIn('school_orders.so_pay_type', [1, 3, 4])
        //             ->orWhere( function($_query) {
        //                 $_query->where('school_orders.so_order_status', '=', '5')
        //                     ->where('school_orders.so_pay_type', '=', '2');
        //             });
        //     };
        //     break;
        case 3:
            $whereCondition = function($query) {
                $query->where('school_orders.so_order_status', '=', '3')
                    ->whereIn('school_orders.so_pay_type', [1, 3, 4])
                    ->orWhere(function($_query) {
                        $_query->where('school_orders.so_order_status', '=', '2')
                            ->where('school_orders.so_pay_type', '=', '2');
                    });
            };
            break;
        case 0: // 全部
            $whereCondition = function($query) {
                $query->whereIn('school_orders.so_order_status', [1, 3, 4])
                    ->whereIn('school_orders.so_pay_type', [1, 3, 4]);
            };
            break;
        default:
            $whereCondition = function($query) {
                $query->whereIn('school_orders.so_order_status', [1, 3, 4])
                    ->whereIn('school_orders.so_pay_type', [1, 3, 4]);
            };
            break;
        }

        DB::connection()->enableQueryLog();

        $order_list = DB::table('school_orders')
            ->select(
                'school_orders.id as order_id',
                'school_orders.so_order_no as order_no',
                'school.s_school_name as school_name',
                'school_orders.so_school_id as school_id',
                'school_orders.so_original_price as original_price',
                'school_orders.so_final_price as final_price',
                'school_orders.so_total_price as total_price',
                'school_orders.so_shifts_id as shifts_id',
                'school_orders.so_pay_type as pay_type',
                'school_orders.so_order_status as order_status',
                'school_orders.so_comment_status as comment_status',
                'school_orders.dt_zhifu_time as pay_time',  // 订单支付的完成时间
                'school_orders.so_coach_id as coach_id',
                'school_orders.so_licence as license',
                'school_orders.coupon_name',
                'school_orders.coupon_value',
                'school_orders.coupon_code',
                'school_orders.addtime',
                'school_shifts.sh_title as shift_name'
            )
            ->leftJoin('school_shifts', 'school_orders.so_shifts_id', '=', 'school_shifts.id')
            ->join('school', 'school_orders.so_school_id', '=', 'school.l_school_id')
            ->where($commonwhereCondition)
            ->where($whereCondition)
            ->orderBy('school_orders.id', 'desc')
            ->paginate(10);

        $_order_list = $order_list->toArray();

        if(!empty($_order_list['data'])) {
            foreach ($order_list as $key => $value) {

                $order_list[$key]->addtime_format = date('Y-m-d H:i:s', $value->addtime);

                // 如果订单中的教练ID等于0，表示是驾校设置的订单
                if($value->coach_id == 0) {

                    $order_list[$key]->coach_name = '';

                    // 获取驾校头像
                    $s_thumb = DB::table('school')
                        ->where([
                            'l_school_id' => $value->school_id
                        ])
                        ->value('s_thumb');
                    $order_list[$key]->photo_url = $this->buildUrl($s_thumb);
                    /*
                    // 获取驾校班制信息
                    $shifts_info = $this->signup->getSchoolShiftsListById($value->shifts_id, $value->school_id);

                    $order_list[$key]->shift_name = isset($shifts_info->sh_title) ? trim($shifts_info->sh_title) : '';
                     */

                    $order_list[$key]->is_school_shifts = 1; // 是驾校设置的班制
                } else {

                    // 获取当前教练信息
                    $coach_name = DB::table('coach')
                        ->where(['l_coach_id' => $value->coach_id])
                        ->value('s_coach_name');
                    $order_list[$key]->coach_name = $coach_name ? $coach_name : '嘻哈教练';
                    $coach_imgurl = DB::table('coach')
                        ->where(['l_coach_id' => $value->coach_id])
                        ->value('s_coach_imgurl');
                    $order_list[$key]->photo_url = $this->buildUrl($coach_imgurl);
                    /*
                    // 获取教练班制信息
                    $shifts_info = $this->signup->getCoachShiftsList($value->shifts_id, $value->coach_id);
                    $order_list[$key]->shift_name = isset($shifts_info->sh_title) ? trim($shifts_info->sh_title) : '';
                     */

                    $order_list[$key]->is_school_shifts = 2; // 是教练设置的班制
                }

                if($value->coupon_value == 0) {
                    $order_list[$key]->coupon_used = 1; // 没有使用优惠券
                } else {
                    $order_list[$key]->coupon_used = 2; // 使用优惠券
                }
                $order_list[$key]->price_unit = '￥';


                // 支付方式名称
                switch ($value->pay_type) {
                case '1':
                    $order_list[$key]->pay_type_name = '支付宝';
                    break;
                case '2':
                    $order_list[$key]->pay_type_name = '线下支付';
                    break;
                case '3':
                    $order_list[$key]->pay_type_name = '微信支付';
                    break;
                case '4':
                    $order_list[$key]->pay_type_name = '银联支付';
                    break;
                default:
                    $order_list[$key]->pay_type_name = '线下支付';
                    break;
                }

                // 订单可操作动作列表
                $order_group_actions = [];
                // 订单状态名称
                switch ($value->order_status) {
                case '1':
                    if(2 == $value->pay_type) {
                        // 已付款、线下：1.立即评价
                        $order_status_name = '未付款';
                        $order_group_actions = ['立即评价'];
                    } else {
                        // 已付款、线上:1.立即评价
                        $order_status_name = '已付款';
                        $order_group_actions = ['立即评价'];
                    }

                    break;
                case '2':
                    if(2 == $value->pay_type) {
                        // 退款中、线下 没有操作
                        $order_status_name = '已取消';
                        $order_group_actions = [];
                    } else {
                        // 退款中、线上 没有操作
                        $order_status_name = '退款中';
                        $order_group_actions = [];
                    }
                    break;
                case '3':
                    if(2 == $value->pay_type) {
                        // 已取消、线下：1.再次购买
                        $order_status_name = '已付款';
                        $order_group_actions = ['再次购买'];
                    } else {
                        // 已取消、线上：1.再次购买
                        $order_status_name = '已取消';
                        $order_group_actions = ['再次购买'];
                    }
                    break;
                case '4':  // 下单成功未付款
                    if(2 == $value->pay_type) {
                        // 未付款、线下
                        //$order_status_name = '退款中';
                        $order_status_name = '未付款';
                        $order_group_actions = [];
                    } else {
                        // 未付款、线上：1.立即支付 2.取消订单
                        $order_status_name = '未付款';
                        $order_group_actions = ['立即支付','取消订单'];
                    }
                    break;
                case '1001':
                    $order_status_name = '等待付款';
                    break;
                case '1006':
                    $order_status_name = '退款处理中';
                    break;
                case '1007':
                    $order_status_name = '已退款';
                    break;

                default:
                    $order_status_name = '状态未知';
                    break;
                }
                $order_list[$key]->order_status_name = $order_status_name;
                $order_list[$key]->order_group_actions = $order_group_actions;
                //unset($order_list[$key]->school_id);
                unset($order_list[$key]->final_price);
                //unset($order_list[$key]->shifts_id);
                unset($order_list[$key]->pay_type);
                //unset($order_list[$key]->order_status);
                unset($order_list[$key]->pay_time);
                //unset($order_list[$key]->coach_id);
                unset($order_list[$key]->coupon_name);
                unset($order_list[$key]->coupon_value);
                unset($order_list[$key]->coupon_code);
                unset($order_list[$key]->addtime);
                unset($order_list[$key]->coach_name);
                //runset($order_list[$key]->is_school_shifts);
                unset($order_list[$key]->coupon_used);
                unset($order_list[$key]->price_unit);
            }

        }
        $queries = DB::getQueryLog();

        $data = ['code'=>200, 'msg'=>'获取订单成功', 'data'=>['list'=>$order_list]];
        return response()->json($data);
    }

    // 获取用户报名班制订单情况
    public function getUserShiftOrders($user_id) {
        $commonwhereCondition = [
            ['school_orders.so_user_id', '=', $user_id],
            ['school_orders.so_order_status', '<>', '101']
        ];

        $whereCondition = function($query) {
            $query->where('school_orders.so_order_status', '=', '1')
                ->whereIn('school_orders.so_pay_type', [1, 3, 4])
                ->orWhere(function($_query) {
                    $_query->where('school_orders.so_order_status', '=', '3')
                        ->where('school_orders.so_pay_type', '=', '2');
                });
        };
        $order_list = DB::table('school_orders')
            ->select(
                'school_shifts.sh_school_id',
                'school_shifts.sh_type',
                'school_shifts.sh_license_name',
                'school_shifts.sh_title'
            )
            ->where($commonwhereCondition)
            ->where($whereCondition)
            ->join('school_shifts', 'school_orders.so_shifts_id', '=', 'school_shifts.id')
            ->orderBy('school_orders.id', 'desc')
            ->first();
        return $order_list;
    }

    /**
     * 获取我的计时培训订单列表
     *
     * @param string $token
     * @param int $type (0-全部,1-未付款,2-已付款,3-已完成,4-已取消)
     * @return void
     */
    public function getMyAppointOrdersList() {
        $user = (new AuthController())->getUserFromToken($this->request->input('token'));
        if ( ! $this->request->has('type') ) {
            $type = 0;
        } else {
            $type = intval($this->request->input('type'));
            if (! in_array($type, [1,2,3,4])) {
                $type = 0;
            }
        }
        // --  --  预约计时订单状态
        // 1   Paid    已付款
        // 2   Completed   已完成
        // 3   Canceled    已取消
        // 101 Deleted 已删除

        // 1001 proceeding  正在付款中：等待用户付款操作
        // 1002    paid    已付款：成功付款0元或多元
        // 1003    unpaid  未付款：因付款超时等原因所致
        // 1004    cancelling  取消订单中：用户提交申请，等待系统处理
        // 1005    cancelled   已取消订单：系统完成处理，成功取消订单
        // 1006    refunding   用户退款中：多因误操作或对服务不满意而申请退款
        // 1007    refunded    已退款：成功退回到用户的支付帐户或专门设置的退款接收帐户
        // 1008    service_available   正在服务中:有些订单有时效性，这种情况下取消订单不作退款处理，只有在有效期内的订单才能进行退款结算
        // 1009    deleting    删除订单：对于过期订单或无效订单或超出时间未付款订单作删除处理
        // 1010    deleted 已删除订单： 不能进行任何操作
        // 1011    completed   已完成订单：不能进行任何操作
        switch ($type) {
        case 0:
            $whereCon = function ($query) {
                $query->whereIn('i_status', [1001,1002,1003,1,2]); // 待付款 待完成 待评价
            };
            //全部
            break;
        case 1:
            //未付款 /待付款
            $whereCon = function ($query) {
                $query->whereIn('i_status', [1001,1003]);
            };
            break;
        case 2:
            //已付款 /待完成
            $whereCon = function ($query) {
                $query->whereIn('i_status', [1,1002]);
            };
            break;
        case 3:
            //已完成 /待评价
            $whereCon = function ($query) {
                $query->where('i_status', '=', 2)->where('so_comment_status', '=', 1);
            };
            break;
        case 4:
            //已取消
            $whereCon = function ($query) {
                $query->whereIn('i_status', [3, 1006]); // 1006 退款中 3 已取消
            };
            break;
        default :
            $whereCon = function ($query) {
                $query->whereIn('i_status', [1001,1002,1003,1,2]); // 待付款 待完成 待评价
            };
            break;
        }

        // 查询条件
        $where = [];
        $where[] = ['l_user_id', '=', $user['user_id']];
        $where[] = ['i_status', '<>', 101];//去除已删除的订单
        if ($this->request->has('coach_id')) {
            $where[] = ['study_orders.l_coach_id', '=', $this->request->input('coach_id')];//约某个教练的所有订单
        }

        DB::connection()->enableQueryLog();
        $order_list = DB::table('study_orders')
            ->select(
                'study_orders.l_study_order_id as order_id',
                'study_orders.s_order_no as order_no',
                'study_orders.dt_order_time as addtime',
                'study_orders.dt_zhifu_time as pay_time',
                'study_orders.dc_money as money',
                'study_orders.deal_type as pay_type',
                'study_orders.s_zhifu_dm as transaction_no',
                'study_orders.i_status as order_status',
                'study_orders.l_user_id as user_id',
                'study_orders.s_user_name as user_name',
                'study_orders.s_user_phone as user_phone',
                'study_orders.l_coach_id as coach_id',
                'study_orders.s_coach_name as coach_name',
                'study_orders.s_coach_phone as coach_phone',
                'study_orders.s_lisence_name as license_name',
                'study_orders.s_lesson_name as lesson_name',
                'study_orders.dt_appoint_time as appoint_date',
                'study_orders.time_config_id',
                'study_orders.so_comment_status as comment_status'
            )
            ->where($where)
            ->where($whereCon)
            ->orderBy('l_study_order_id', 'desc')
            ->paginate(10)
            ;
        $queries = DB::getQueryLog();

        $order_no_list  = [];
        $coach_id_list  = [];
        $school_id_list = [];
        foreach ($order_list as $index => $order) {
            $order_no_list[] = $order->order_no;
            $coach_id_list[] = $order->coach_id;
        }
        $coach_list = DB::table('coach')
            ->select(
                'coach.l_coach_id as coach_id',
                'coach.s_school_name_id as school_id',
                'coach.s_coach_imgurl as coach_imgurl'
            )
            ->whereIn('coach.l_coach_id', $coach_id_list)
            ->get();
        $school_id_list = [];
        foreach ($coach_list as $index => $coach) {
            $school_id_list[] = $coach->school_id;
        }
        $school_list = DB::table('school')
            ->select(
                's_school_name as school_name',
                'l_school_id as school_id'
            )
            ->whereIn('school.l_school_id', $school_id_list)
            ->get();
        $comment_list = DB::table('coach_comment')
            ->select(
                'coach_comment.id as comment_id',
                'coach_comment.order_no',
                'coach_comment.coach_content as comment_content',
                'coach_comment.coach_star as comment_star',
                'coach_comment.addtime as comment_time'
            )
            ->whereIn('coach_comment.order_no', $order_no_list)
            ->get();

        $coach_time_config_list_raw = DB::table('coach_time_config')
            ->select(
                'id',
                'start_time as start_hour',
                'start_minute as start_minute',
                'end_time as end_hour',
                'end_minute as end_minute'
            )
            ->where('status', '=', 1)
            ->get();
        $coach_time_config_list = [];
        foreach ($coach_time_config_list_raw as $i => $v) {
            $v->start_minute = ($v->start_minute < 10) ? '0'.$v->start_minute : $v->start_minute;
            $v->end_minute = ($v->end_minute < 10) ? '0'.$v->end_minute : $v->end_minute;
            $time_area = $v->start_hour.':'.$v->start_minute.'-'.$v->end_hour.':'.$v->end_minute;
            $coach_time_config_list[$v->id] = $time_area;
        }

        //订单列表信息细化完善
        foreach ($order_list as $index => $order) {
            if ($coach_list) {
                foreach ($coach_list as $i => $coach) {
                    if ($coach->coach_id == $order->coach_id) {
                        // 教练头像
                        $order_list[$index]->coach_imgurl = $this->buildUrl($coach->coach_imgurl);
                        if ($school_list) {
                            foreach ($school_list as $j => $school) {
                                if ($school->school_id == $coach->school_id) {
                                    $order_list[$index]->school_name = $school->school_name;
                                }
                            }
                        } else {
                            $order_list[$index]->school_name = '嘻哈自营驾校';
                        }
                    }
                }
            } else {
                $order_list[$index]->school_name = '嘻哈自营驾校';
            }
            $order_list[$index]->addtime_format = date('Y-m-d H:i:s', $order->addtime);
            // 评论状态
            if ($comment_list) {
                foreach ($comment_list as $j => $comment) {
                    if ($comment->order_no == $order->order_no) {
                        if ( ! is_null($comment->comment_id) ) {
                            $order_list[$index]->comment_status = 2;
                            $order_list[$index]->comment_time_format = date('Y-m-d H:i', $comment->comment_time);
                        }
                    }
                }
            } else {
                $order_list[$index]->comment_status = 1;
                $order_list[$index]->comment_time_format = '';
            }

            //约车时间填充
            // - 约车日期
            $appoint_date = array_filter(explode(' ', $order->appoint_date));
            if (! empty($order->appoint_date) && count($appoint_date) > 0 ) {
                $order->appoint_date = $appoint_date[0];
            } else {
                $order->appoint_date = '';
            }
            // - 时间段列表
            $appoint_time_list = [];
            if (! empty($order->time_config_id) &&
                $time_config_list = array_filter(explode(',', $order->time_config_id)) )
            {
                foreach ($time_config_list as $i => $v) {
                    $appoint_time_list[] = $coach_time_config_list[$v];
                }
            }
            $order_list[$index]->appoint_time_list = $appoint_time_list;
            unset($order->time_config_id);

            // 支付方式名称
            switch ($order->pay_type) {
            case '1':
                $pay_type_name = '支付宝';
                break;
            case '2':
                $pay_type_name = '线下支付';
                break;
            case '3':
                $pay_type_name = '微信支付';
                break;
            case '4':
                $pay_type_name = '银联支付';
                break;
            default:
                $pay_type_name = '其它方式';
                break;
            }
            $order_list[$index]->pay_type_name = $pay_type_name;
            $order_group_actions = [];
            // 订单状态名称
            switch ($order->order_status) {
            case 1:
                $status_name = '已付款';
                $order_group_actions = ['取消订单'];
                break;
            case 2:
                $status_name = '已完成';
                $order_group_actions = ['立即评价'];
                break;
            case 3:
                $status_name = '已取消';
                $order_group_actions = [];
                break;
            case 1001:
                $status_name = '等待付款';
                $order_group_actions = ['立即支付','取消订单'];
                break;
            case 1002:
                $status_name = '已付款';
                $order_group_actions = ['取消订单'];
                break;
            case 1003:
                $status_name = '未付款';
                $order_group_actions = ['立即支付','取消订单'];
                break;
            case 1006:
                $status_name = '退款中';
                $order_group_actions = [];
                break;
            case 1007:
                $status_name = '已退款';
                $order_group_actions = [];
                break;
            case 101:
                $status_name = '已删除';
                $order_group_actions = [];
                break;
            default:
                $status_name = '其它状态';
                $order_group_actions = [];
                break;
            }
            $order_list[$index]->order_status_name = $status_name;
            $order_list[$index]->order_group_actions = $order_group_actions;

            $order_list[$index]->expire_time_limit = 180; // 180秒，付款超时3分钟不可再付款
            if ( 24156 === $user['user_id'] || 1 === $user['user_id']) {
                $order_list[$index]->expire_time_limit = 30; // 30秒，付款超时3分钟不可再付款, debug for gdc and chenxi
            } else if (21741 === $user['user_id']) {
                $order_list[$index]->expire_time_limit = 3600; // 1小时，付款超时3分钟不可再付款, debug for wangling
            }

            //unset($order_list[$index]->addtime);
            unset($order_list[$index]->pay_type);
            unset($order_list[$index]->transaction_no);
            //unset($order_list[$index]->order_status);
            unset($order_list[$index]->pay_time);
            unset($order_list[$index]->user_id);
            unset($order_list[$index]->user_name);
            unset($order_list[$index]->user_phone);
            unset($order_list[$index]->coach_phone);
        }

        $data = [
            'code' => 200,
            'msg'  => '获取订单成功',
            'data' => [
                'list' => $order_list,
            ],
        ];
        return response()->json($data);
    }

    // 下单（预约计时订单和报名班制订单）
    public function submitOrder($order_type) {
        $order_type_arr = ['signup', 'appoint'];
        if(!in_array($order_type, $order_type_arr)) {
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => new \stdClass
            ]);
        }
        $user = (new AuthController())->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        if('signup' == $order_type) {
            // shifts_id,licence,real_name,phone,identity_id
            if(!$this->request->has('shifts_id')
                || !$this->request->has('licence')
                || !$this->request->has('real_name')
                || !$this->request->has('phone')
                || !$this->request->has('identity_id')
            ) {
                throw new InvalidArgumentException('参数错误', 400);
            }
            $shifts_id      = $this->request->input('shifts_id');
            $licence        = $this->request->input('licence');
            $real_name      = $this->request->input('real_name');
            $phone          = $this->request->input('phone');
            $identity_id    = $this->request->input('identity_id');
            if(!$this->user->checkPhoneFormat($phone)) {
                throw new InvalidArgumentException('手机号码格式错误', 400);
            }

            //$shifts_info = $this->signup->getSchoolShiftsListById($shifts_id, $school_id);
            $shifts_info = DB::table('school_shifts')
                ->select('id','sh_school_id','coach_id','sh_license_name','sh_type')
                ->where([['id', '=', $shifts_id],['deleted', '=', 1]])
                ->first();
            if ( ! $shifts_info) {
                $data = ['code'=>1003,'msg'=>'该班制不存在，请重新选择','data'=>new \stdClass,];
                Log::error('异常：【提交报名班制订单】该驾校的此条班制已经被删除');
                return response()->json($data);
            }

            $params = [
                'user_id'     => $user_id,
                'real_name'   => $real_name,
                'phone'       => $phone,
                'identity_id' => $identity_id,
                'shifts_id'   => $shifts_id,
                'school_id'   => $shifts_info->sh_school_id ? $shifts_info->sh_school_id : 0,
                'coach_id'    => $shifts_info->coach_id ? $shifts_info->coach_id : 0,
                'licence'     => $shifts_info->sh_license_name ? $shifts_info->sh_license_name : '',
                'sh_type'     => $shifts_info->sh_type ? $shifts_info->sh_type : 1 // 班制分类 1:驾校设置的 2:教练设置的
            ];
            $res = $this->signupOrderSubmitOperate($params);
        } else {
            // 预约计时
            if(!$this->request->has('coach_id')
                || !$this->request->has('time_configs')
                || !$this->request->has('date')
                || !$this->request->has('money')
                || !$this->request->has('phone')
                || !$this->request->has('real_name')
                || !$this->request->has('identity_id')
                || !$this->request->has('coach_name')
                || !$this->request->has('coach_phone')
            ) {
                throw new InvalidArgumentException('参数错误', 400);
            }

            $coach_id = $this->request->input('coach_id');
            $pay_type = 1;
            $time_configs = $this->request->input('time_configs');
            $date = $this->request->input('date');
            $money = $this->request->input('money');
            $phone = $this->request->input('phone');
            $real_name = $this->request->input('real_name');
            $identity_id = $this->request->input('identity_id');
            $coach_name = $this->request->input('coach_name');
            $coach_phone = $this->request->input('coach_phone');

            // 判断当前教练是否开启接单
            $coach_info = DB::table('coach')
                ->select(
                    'order_receive_status',
                    'must_bind'
                )
                ->where(['l_coach_id'=>$coach_id])
                ->first();
            if(empty($coach_info)) {
                throw new InvalidArgumentException('当前教练状态异常不可预约，请选择其他教练', 1001);
            }

            // 必须绑定
            if($coach_info->must_bind == 1) {

                // 获取当前学员是否已经与教练绑定
                $bind_info = $this->getCoachUserRelation($coach_id, $user_id);

                if($bind_info) {
                    switch($bind_info) {
                    case '3':  // 学员申请绑定教练
                        return response()->json([
                            'code' => 400,
                            'msg'  => '申请绑定教练中，请耐心等待',
                            'data' => new \Stdclass()
                        ]);
                        break;
                    case '4': // 教练申请绑定学员
                        return response()->json([
                            'code' => 400,
                            'msg'  => '教练正在与你绑定中，请同意绑定',
                            'data' => new \Stdclass()
                        ]);
                        break;
                    case '5': // 学员申请解绑教练
                        return response()->json([
                            'code' => 400,
                            'msg'  => '你正在申请解绑教练，不能预约计时',
                            'data' => new \Stdclass()
                        ]);
                        break;
                    case '6': // 教练申请解绑学员
                        return response()->json([
                            'code' => 400,
                            'msg'  => '教练正在申请解绑你，不能预约计时',
                            'data' => new \Stdclass()
                        ]);
                        break;
                    }
                } else {
                    return response()->json([
                        'code' => 400,
                        'msg'  => '你未与教练绑定，不能预约计时',
                        'data' => new \Stdclass()
                    ]);
                }
            }

            // 是否接单的关系
            if($coach_info->order_receive_status != 1) {
                throw new InvalidArgumentException('当前教练不在线，不能预约该教练时间', 1001);
            }

            $params = [
                'user_id' => $user_id,
                'coach_id' => $coach_id,
                'pay_type' => $pay_type,
                'time_configs' => $time_configs, // [{'id'：1, 'is_coach_set': 1},{'id'：2, 'is_coach_set': 1}]
                'date' => $date,
                'money' => $money,
                'phone' => $phone,
                'real_name' => $real_name,
                'identity_id' => $identity_id,
                'coach_name' => $coach_name,
                'coach_phone' => $coach_phone,
            ];
            $res = $this->timeOrderSubmitOperate($params);
        }
        return $res;

    }

    // 获取教练与学员绑定情况
    public function getCoachUserRelation($coach_id, $user_id) {

        $bind_status = DB::table('coach_user_relation')
            ->where([
                ['bind_status', '<>', 2],
                ['user_id', '=', $user_id],
                ['coach_id', '=', $coach_id]
            ])
            ->value('bind_status');
        return $bind_status;
    }

    // 报名班制下单操作
    private function signupOrderSubmitOperate($params) {
        if(empty($params)) {
            return false;
        }
        $user_id        = $params['user_id'] ? $params['user_id'] : '';
        $real_name      = $params['real_name'] ? $params['real_name'] : '';
        $phone          = $params['phone'] ? $params['phone'] : '';
        $identity_id    = $params['identity_id'] ? $params['identity_id'] : '';
        $shifts_id      = $params['shifts_id'] ? $params['shifts_id'] : 0;
        $school_id      = $params['school_id'] ? $params['school_id'] : 0;
        $coach_id       = $params['coach_id'] ? $params['coach_id'] : 0;
        $licence        = $params['licence'] ? $params['licence'] : '';
        $sh_category    = $params['sh_type'] ? $params['sh_type'] : 1;  // 班制分类 1:驾校设置的 2:教练设置的
        $pay_type       = 1;  // 支付宝
        $order_type     = 4;  // 报名成功未付款
        $so_order_no    = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);

        // 通过班制ID获取班制信息
        if(1 == $sh_category) {
            $shifts_info = DB::table('school_shifts')
                ->select(
                    'sh_money',
                    'sh_original_money'
                )
                ->where(['id' => $shifts_id])
                ->first();

        } else {
            $shifts_info = DB::table('school_shifts')
                ->select(
                    'sh_money',
                    'sh_original_money'
                )
                ->where(['id' => $shifts_id])
                ->whereNotNull('coach_id')
                ->first();
        }
        if(empty($shifts_info)) {
            throw new InvalidArgumentException('班制不存在', 400);
        }
        $coupon_name = '';
        $coupon_value = 0;
        $coupon_code = '';
        $coupon_id = 0;
        $total_price = 0;
        $total_price = $shifts_info->sh_money - $coupon_value > 0 ? $shifts_info->sh_money - $coupon_value : 0;

        if($total_price == 0) {
            $pay_type       = 2;  // 线下支付
            $order_type     = 3;  // 报名成功已付款
        }
        // 获取是否已报名
        $commonwhereCondition = [
            ['so_user_id', '=', $user_id],
            ['so_phone', '=', $phone],
            ['so_user_identity_id', '=', $identity_id],
            ['so_order_status', '<>', '101']
        ];

        $whereCondition = function($query) {
            $query->whereIn('so_order_status', [1, 2, 4])
                ->whereIn('so_pay_type', [1, 3, 4])
                ->orWhere(function($_query) {
                    $_query->whereIn('so_order_status', [1, 3, 4])
                        ->where('so_pay_type', '=', '2');
                });
        };
        $order_info = DB::table('school_orders')
            ->select(
                'id'
            )
            ->where($commonwhereCondition)
            ->where($whereCondition)
            ->first();

        /**
            if(!empty($order_info)) {
                return response()->json([
                    'code' => 1002,
                    'msg'  => '您已报过名，请在我的报名中查看',
                    'data' => new \stdClass
                ]);
            }
         */

        // 更新用户信息
        DB::transaction(function () use ($real_name, $identity_id, $school_id, $user_id) {
            // 更新user表
            $res = DB::table('user')->where(['l_user_id'=>$user_id])->update(['s_real_name'=>$real_name]);
            // 更新users_info表
            $users_info = DB::table('users_info')
                ->select('user_id')
                ->where(['user_id' => $user_id])
                ->first();
            if(!empty($users_info)) {
                $_res = DB::table('users_info')->where(['user_id'=>$user_id])->update(['identity_id'=>$identity_id, 'school_id'=>$school_id]);
            } else {
                // 新增
                $_res = DB::table('users_info')->insert([
                    'x' => 0,
                    'y' => 0,
                    'user_id' => $user_id,
                    'sex' => 1,
                    'age' => 18,
                    'identity_id' => $identity_id,
                    'address' => '',
                    'user_photo' => '',
                    'license_name' => 0,
                    'school_id' => $school_id,
                    'lesson_name' => '',
                    'province_id' => 0,
                    'city_id' => 0,
                    'area_id' => 0,
                    'photo_id' => rand(1, 16),
                    'learncar_status' => '科目二学习中'
                ]);
            }
        });

        DB::beginTransaction();

        // 修改优惠券状态
        $coupon_res = true;
        if($coupon_id) {
            $coupon_res = DB::table('user_coupon')
                ->where(['id'=>$coupon_id])
                ->update([
                    'coupon_status' => 2
                ]);
        }
        // 下单
        $insert_id = DB::table('school_orders')->insertGetId([
            'so_school_id' => $school_id,
            'so_final_price' => $shifts_info->sh_money,
            'so_original_price' => $shifts_info->sh_original_money,
            'so_total_price' => $total_price,
            'so_shifts_id' => $shifts_id,
            'so_pay_type' => $pay_type,
            'so_order_status' => $order_type,
            'so_comment_status' => 1,
            'so_order_no' => $so_order_no,
            's_zhifu_dm' => $this->guid(false),
            'so_user_id' => $user_id,
            'so_coach_id' => $coach_id,
            'so_user_identity_id' => $identity_id,
            'so_licence' => $licence,
            'so_username' => $real_name,
            'so_phone' => $phone,
            'coupon_name' => $coupon_name,
            'coupon_value' => $coupon_value,
            'coupon_code' => $coupon_code,
            'addtime' => time()
        ]);

        if($insert_id && $coupon_res) {

            $order_info = $this->getSignupOrderInfo(['order_id' => $insert_id]);
            $content = $order_info->school_name.'/'.$order_info->shift_name.'/'.$order_info->license.',订单号'.$order_info->order_no;
            // 推送
            $push_info = new \StdClass;
            $push_info->product = 'student';
            $push_info->target = $user_id;
            $push_info->content = '【新的报名】详情:'.$content;
            $push_info->type = 2;
            $push_info->member_id = $user_id;
            $push_info->member_type = 1;
            $push_info->beizhu = '报名班制';
            $push_info->from = '嘻哈学车';
            $this->redis->rpush('msg_send_queue', json_encode($push_info, JSON_UNESCAPED_UNICODE));
            $data = [
                'code' => 200,
                'msg'  => '报名成功',
                'data' => [
                    'order_info' => $this->getSignupOrderInfo(['order_id' => $insert_id]),
                ]
            ];
            DB::commit();
        } else {
            $data = [
                'code' => 200,
                'msg'  => '报名成功',
                'data' => [
                    'order_info' => $this->getSignupOrderInfo(['order_id' => $insert_id]),
                ]
            ];
            DB::rollBack();
        }
        return response()->json($data);

    }

    // 预约计时下单操作
    private function timeOrderSubmitOperate($params) {
        // $params = [
        //     'coach_id' => $coach_id,
        //     'pay_type' => $pay_type,
        //     'time_configs' => $time_configs, // [{"id": 1, "is_coach_set": 1}, {"id": 2, "is_coach_set": 1}]
        //     'date' => $date,
        //     'money' => $money
        // ];
        $date_arr       = explode('-', $params['date']) ? explode('-', $params['date']) : [];
        if(empty($date_arr)) {
            throw new InvalidArgumentException('日期格式错误', 400);
        }

        $user_id         = $params['user_id'];
        $coach_id        = $params['coach_id'];
        $pay_type        = $params['pay_type'];
        $time_configs    = $params['time_configs'];
        $year            = isset($date_arr[0]) ? $date_arr[0] : 0;
        $month           = isset($date_arr[1]) ? $date_arr[1] : 0;
        $day             = isset($date_arr[2]) ? $date_arr[2] : 0;
        $money           = $params['money'];
        $real_name       = $params['real_name'] ? $params['real_name'] : '';
        $phone           = $params['phone'] ? $params['phone'] : '';
        $identity_id     = $params['identity_id'] ? $params['identity_id'] : '';
        $coach_name      = $params['coach_name'] ? $params['coach_name'] : '';
        $coach_phone     = $params['coach_phone'] ? $params['coach_phone'] : '';
        $order_status    = 1003;                                                        //兼容老的
        $dt_zhifu_time   = date('Y-m-d H:i:s', time());
        $time_config_ids = [];
        $pay_type        = 1;                                                           // 1 默认支付宝
        $time_configs_arr = json_decode($time_configs, true) ? json_decode($time_configs, true) : [];
        $time_configs_arr_num = count($time_configs_arr);                               // 选择的时间段数量
        if(empty($time_configs_arr)) {
            throw new InvalidArgumentException('时间格式错误', 400);
        }

        $signup_order_info = DB::table('school_orders')
            ->select(
                'id as order_id',
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
            ->first();

        if ($signup_order_info
            && isset($signup_order_info->free_study_hour)
            && intval($signup_order_info->free_study_hour) >= 0
        ) {
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
            if ($quota <= 0) {
                if ($money <= 0) {
                    throw new InvalidArgumentException('您的优惠学时'.$free_study_hour.'个已用完，请联系驾校', 400);
                }
            } else {
                if ($time_configs_arr_num > $quota) {
                    throw new InvalidArgumentException('您还能免费预约学时：'.$quota.'个', 400);
                }

                $money = 0;
            }
        }
        if ( 0 == $money ) {
            $order_status = 1;                                                          // 如果钱为0元，状态为已付款。
            $pay_type = 2;                                                              //支付方式为线下支付
        }

        // 缓冲处理预约时间
        try {
            if ($this->redis->getset('coach:'.$coach_id.':'.$params['date'], 0) > 0) {
                throw new InvalidArgumentException('系统繁忙，请稍后重试', 400);
            } else {
                $this->redis->incr('coach:'.$coach_id.':'.$params['date']);
            }
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (Exception $e) {
            //
        }

        // $user_study_orders_time_num = count($_time_config_ids);  // 当前用户预约当前教练的时间端数量
        $cancel_order_time = 2; // 取消次数 取消次数过多，当天就不可被预约
        $sum_appoint_time = 2; // 总共可被预约时间段
        // $pre_order_limit_time = 1; // 下单时间提前一个小时下单

        // 获取当前教练的驾校ID
        $school_id = DB::table('coach')->where(['l_coach_id' => $coach_id])->value('s_school_name_id');

        if($school_id) {

            // 判断当前用户预约的时间段是否超过当天预约最大限制
            $user_study_orders_info = $this->getStudyOrderInfo($coach_id, $year, $month, $day, $user_id);
            $user_study_orders_time_num = 0;
            $_time_config_ids = [];
            if(!empty($user_study_orders_info)) {
                foreach ($user_study_orders_info as $key => $value) {
                    $_time_config_ids = explode(',', $value->time_config_id);
                    $user_study_orders_time_num += count($_time_config_ids); // 当前用户预约当前教练的时间端数量
                }
            }
            // 获取驾校的所设置的时间计时配置
            $school_config = DB::table('school_config')
                ->select(
                    'i_cancel_order_time',
                    'i_sum_appoint_time'
                )
                ->where(['l_school_id'=>$school_id])
                ->first();

            if(!empty($school_config)) {
                $cancel_order_time = $school_config->i_cancel_order_time;
                $sum_appoint_time = $school_config->i_sum_appoint_time;
            }
            if($user_study_orders_time_num + $time_configs_arr_num > $sum_appoint_time) {
                throw new InvalidArgumentException('你当天预约时间段数超过可预约限制', 400);
            }
            // 当前取消次数过多不能预约学车
            $user_cancel_study_orders_info = $this->getStudyOrderInfo($coach_id, $year, $month, $day, $user_id, 1);
            $user_cancel_study_orders_time_num = 0;
            $_time_config_ids = [];

            if(!empty($user_cancel_study_orders_info)) {
                foreach ($user_cancel_study_orders_info as $key => $value) {
                    $_time_config_ids = explode(',', $value->time_config_id);
                    $user_cancel_study_orders_time_num += count($_time_config_ids); // 当前用户取消当前教练的时间端数量
                }
            }
            // Log::info('用户取消时间次数：'.$user_cancel_study_orders_info);
            if($user_cancel_study_orders_time_num >= $cancel_order_time) {
                throw new InvalidArgumentException('你当天取消时间段过多不能再次预约', 1002);
            }
        }

        // 生成订单
        $s_order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);

        $mcoach = new CoachController($this->request);

        // 获取传递总价是否与服务器设置一致
        $time_config_info = $this->getTimeConfigInfo($mcoach, $time_configs_arr, $coach_id, $year, $month, $day);

        if(empty(array_filter($time_config_info))) {
            throw new InvalidArgumentException('时间段不存在或者已取消', 400);
        }
        $_money = 0;
        $min_time = 0;
        $max_time = 0;
        $min_minute = 0;
        $start_time_arr = [];
        $start_minute_arr = [];
        foreach (array_filter($time_config_info) as $key => $value) {
            $_money += $value['price'];
            $start_time_arr[$value['start_time']] = $value['start_minute'];
        }
        $min_time = min(array_keys($start_time_arr));
        $max_time = max(array_keys($start_time_arr));
        foreach ($start_time_arr as $key => $value) {
            if($key == $min_time) {
                $min_minute = $value;
            }
        }
        $min_time_format = $params['date'].' '.$min_time.':'.$min_minute.':00';
        if(time() > strtotime($min_time_format)) {
            throw new InvalidArgumentException('时间段已过期不能预约', 400);
        }

        // 判断当前时间段是否已被预约
        $study_order_info = $this->getStudyOrderInfo($coach_id, $year, $month, $day);
        $appointed_time_configs_id_arr = [];
        if(!empty($study_order_info)) {
            foreach ($study_order_info as $key => $value) {
                $_time_config_ids = explode(',', $value->time_config_id);
                foreach ($_time_config_ids as $k => $v) {
                    $appointed_time_configs_id_arr[] = $v;
                }
            }
        }
        foreach ($time_configs_arr as $key => $value) {
            $time_config_ids[] = $value['id'];
            if(in_array($value['id'], $appointed_time_configs_id_arr)) {
                throw new InvalidArgumentException('你当前所选时间有被预约，请查看我的预约', 1001);
            }
        }
        if(empty($time_config_ids)) {
            throw new InvalidArgumentException('时间段错误', 400);
        }
        $time_config_ids_str = implode(',', $time_config_ids);
        $i_service_time = count($time_config_ids);
        // return $appointed_time_configs_id_arr;

        // return $study_order_info;

        // 获取学员设置的科目牌照
        // $user_info = DB::table('users_info')
        //                     ->select(
        //                         'users_info.lesson_id',
        //                         'users_info.lesson_name',
        //                         'users_info.license_id',
        //                         'users_info.license_name'
        //                     )
        //                     ->where(['user_id'=>$user_id])
        //                     ->first();
        $user_info = $this->user->getUserInfoById($user_id);
        if(empty($user_info)) {
            throw new InvalidArgumentException('你当前账号异常，不能预约计时', 400);
        }
        $s_lisence_name = $user_info->license_name;
        $s_lesson_name = $user_info->lesson_name;

        DB::beginTransaction();
        $appoint_time_id = DB::table('coach_appoint_time')->insertGetId([
            'coach_id' => $coach_id,
            'time_config_id' => $time_config_ids_str,
            'user_id' => $user_id,
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'addtime' => time()
        ]);

        $_res = DB::table('study_orders')->insertGetId([
            's_order_no' => $s_order_no,
            'dt_order_time' => time(),
            'appoint_time_id' => $appoint_time_id,
            'time_config_id' => $time_config_ids_str,
            'l_user_id' => $user_id,
            's_user_name' => $real_name,
            's_user_phone' => $phone,
            'l_coach_id' => $coach_id,
            's_coach_name' => $coach_name,
            's_coach_phone' => $coach_phone,
            's_address' => '',
            's_lisence_name' => $s_lisence_name,
            's_lesson_name' => $s_lesson_name,
            'dc_money' => $money,
            'dt_appoint_time' => $params['date'],
            'i_start_hour' => $min_time,
            'i_end_hour' => $max_time,
            'i_service_time' => $i_service_time,
            'i_status' => $order_status,
            's_zhifu_dm' => $this->guid(false),
            'dt_zhifu_time' => $dt_zhifu_time,
            'deal_type' => $pay_type,
            's_time_money_detail' => '',
            's_order_time' => '0',
            's_order_money' => '0',
            's_lisence_type' => '0',
            'cancel_type' => '0',
            'cancel_reason' => '',
            's_beizhu' => '',
            'so_comment_status' => 1
        ]);

        if($appoint_time_id && $_res) {
            $order_info = $this->getAppointOrderInfo(['order_id' => $_res]);
            $appoint_info = $this->getAppointInfoFromAppointOrder(['order_id' => $_res]);


            // 线上支付
            if ( $money > 0 )
            {
                // redis
                try
                {
                    $queue = 'appoint_order_all';
                    if ( $user_id == 24156 || $user_id === 1)
                    {
                        // debug for gdc's user_id = 24156 and chenxi's user_id = 1
                        $queue = 'appoint_order_all_debug';
                    }
                    $this->redis->rpush($queue, json_encode($order_info, JSON_UNESCAPED_UNICODE)); // 添加到列表

                    // 待发送的消息，分别给学员端和教练端的消息
                    $appoint_push = [
                        'user_id' => $order_info->user_id,
                        'user_phone' => $order_info->user_phone,
                        'coach_id' => $order_info->coach_id,
                        'coach_phone' => $order_info->coach_phone,
                        'content' => json_encode($order_info),
                        'from' => '嘻哈学车',
                        'beizhu' => '预约计时',
                        'type' => 2,
                    ];
                    $this->redis->set('appoint_order:'.$order_info->order_no, json_encode( $appoint_push ));
                }
                catch ( Exception $e )
                {
                    Log::Info($e->getMessage().':'.$e->getLine());
                }
                // redis
                ;
            }
            else
            {
                // begin 待发送的消息, 加入发送队列
                $push_info = new \StdClass;
                // 学员端
                $student_content = sprintf('详情: %s | %s | %s | %s %s | 订单号%s',$order_info->coach_name, $order_info->license_name, $order_info->lesson_name, $order_info->appoint_date, $order_info->appoint_time, $order_info->order_no);
                $push_info->product = 'student';
                $push_info->target = $order_info->user_id;
                $push_info->content = '【新的预约】'.$student_content;
                $push_info->type = 2;
                $push_info->member_id = $order_info->user_id;
                $push_info->member_type = 1;
                $push_info->beizhu = '预约计时';
                $push_info->from = '嘻哈学车';
                $this->redis->rpush('msg_send_queue', json_encode($push_info, JSON_UNESCAPED_UNICODE));
                // 教练端
                $coach_content = sprintf('详情: %s | %s | %s | %s %s | 订单号%s',$order_info->user_name, $order_info->license_name, $order_info->lesson_name, $order_info->appoint_date, $order_info->appoint_time, $order_info->order_no);
                $push_info->product = 'coach';
                $push_info->target = $order_info->coach_phone;
                $push_info->content = '【新的预约】'.$coach_content;
                $push_info->type = 2;
                $push_info->member_id =  $order_info->coach_id;
                $push_info->member_type =  2;
                $push_info->beizhu = '预约计时';
                $push_info->from = '嘻哈学车';
                $this->redis->rpush('msg_send_queue', json_encode($push_info, JSON_UNESCAPED_UNICODE));

                if ($order_info->school_id == 5375) { // 梧州万达驾校
                    // push to 15078112982 娜娜 whose user_id is 5045
                    $push_info->product = 'student';
                    $push_info->target = 5045;
                    $push_info->content = '【新的预约】'.$appoint_info;
                    $push_info->type = 2;
                    $push_info->member_id = 5045;
                    $push_info->member_type = 1;
                    $push_info->beizhu = '预约计时';
                    $push_info->from = '嘻哈学车';
                    $this->redis->rpush('msg_send_queue', json_encode($push_info, JSON_UNESCAPED_UNICODE));
                } elseif ($order_info->school_id == 81) { // 广东鸿景驾校
                    // push to 13824616066 植莹莹 whose user_id is 22014
                    $push_info->product = 'student';
                    $push_info->target = 22014;
                    $push_info->content = '【新的预约】'.$appoint_info;
                    $push_info->type = 2;
                    $push_info->member_id = 22014;
                    $push_info->member_type = 1;
                    $push_info->beizhu = '预约计时';
                    $push_info->from = '嘻哈学车';
                    $this->redis->rpush('msg_send_queue', json_encode($push_info, JSON_UNESCAPED_UNICODE));
                }

            }

            $data = ['code'=>200, 'msg'=>'预约成功', 'data'=>['order_info' => $order_info]];
            DB::commit();
        } else {
            $data = ['code'=>400, 'msg'=>'预约失败', 'data'=>new \Stdclass()];
            DB::rollBack();
        }
        try {
            $this->redis->getset('coach:'.$coach_id.':'.$params['date'], 0);
        } catch (Exception $e) {
            //
        }
        return response()->json($data);
    }

    // 获取当前教练被预约时间情况，当前学员预约当前教练的时间情况，当前学员取消当前教练时间情况
    public function getStudyOrderInfo($coach_id, $year, $month, $day, $user_id='', $type='') {
        $whereCondition = [
            'study_orders.l_coach_id'=> $coach_id,
        ];
        if($user_id) {
            $whereCondition['study_orders.l_user_id'] = $user_id;
        }
        $query = DB::table('study_orders')
            ->select(
                'study_orders.time_config_id'
            )
            ->join('coach_appoint_time', 'study_orders.appoint_time_id', '=', 'coach_appoint_time.id')
            ->where($whereCondition);
        // 取消情况
        if($type == 1) {
            $date = date('Y-m-d');
            // Log::info('current_date：'.$date);
            $today_begin = strtotime($date);
            $today_end = $today_begin + 86400;
            $query = $query->whereIn('study_orders.i_status', [3]); // [3, 101] 3 是已取消 101 是已删除， 取消的订单不包括101
            $query = $query->where([
                ['study_orders.cancel_time', '<=', $today_end],
                ['study_orders.cancel_time', '>=', $today_begin],
            ]);
        } else {
            $query = $query->whereNotIn('study_orders.i_status', [3, 101]);
        }
        $query = $query->where([
            ['coach_appoint_time.year', '=', $year],
            ['coach_appoint_time.month', '=', $month],
            ['coach_appoint_time.day', '=', $day],
        ]);
        $study_info = $query->get();
        return $study_info;
    }

    // 获取所设置的时间配置的价格
    public function getTimeConfigInfo($mcoach, $time_configs_arr, $coach_id, $year, $month, $day) {

        // $time_configs = "[{"id": 1, "is_coach_set": 1}, {"id": 2, "is_coach_set": 2}]"; 1：是教练设置的时间段 2：驾校设置

        $time_ids = [];
        foreach ($time_configs_arr as $key => $value) {
            $time_ids[$value['id']] = $value['is_coach_set'];
        }

        $time_list = [];
        // 判断时间配置正在的设置对象 （驾校？ 教练？）
        $default_time_list = $mcoach->getDefaultTimeConfig(array_keys($time_ids));
        $current_time_list = $mcoach->getCurrentCoachTimeConfig($default_time_list, $coach_id, $year, $month, $day);

        foreach ($default_time_list as $key => $value) {
            $current_time_id = isset($current_time_list[$key]['id']) ? $current_time_list[$key]['id'] : 0;
            if($value['id'] == $current_time_id) {
                $default_time_list[$key]['license_no'] = isset($current_time_list[$key]['license_no']) ? $current_time_list[$key]['license_no'] : 'C1';
                $default_time_list[$key]['subjects'] = isset($current_time_list[$key]['subjects']) ? $current_time_list[$key]['subjects'] : '科目二';
                $default_time_list[$key]['price'] = isset($current_time_list[$key]['price']) ? $current_time_list[$key]['price'] : '130';
                $default_time_list[$key]['start_time'] = isset($current_time_list[$key]['start_time']) ? $current_time_list[$key]['start_time'] : '0';
                $default_time_list[$key]['end_time'] = isset($current_time_list[$key]['end_time']) ? $current_time_list[$key]['end_time'] : '0';
                $default_time_list[$key]['start_minute'] = isset($current_time_list[$key]['start_minute']) && $current_time_list[$key]['start_minute'] != 0 ? $current_time_list[$key]['start_minute'] : '00';
                $default_time_list[$key]['end_minute'] = isset($current_time_list[$key]['end_minute']) && $current_time_list[$key]['end_minute'] != 0 ? $current_time_list[$key]['end_minute'] : '00';
                $default_time_list[$key]['start_time_format'] = $default_time_list[$key]['start_time'].':'.$default_time_list[$key]['start_minute'];
                $default_time_list[$key]['end_time_format'] = $default_time_list[$key]['end_time'].':'.$default_time_list[$key]['end_minute'];
                $default_time_list[$key]['is_coach_set'] = 1;
            } else {
                $default_time_list[$key]['is_coach_set'] = 2;

            }
        }
        return $default_time_list;
    }

    // 获取支付方式
    public function getPayMethods() {
        if(!$this->request->has('type')) {
            $type = 1;
        } else {
            $type = $this->request->input('type');
        }
        $type_arr = [1, 2];
        if(!in_array($type, $type_arr)) {
            $type = 1;
        }

        if ( $this->request->has('money') ) {
            $money = $this->request->input('money');
            if ( $money <= 0 ) {
                // offline
                $data = [
                    'code' => 200,
                    'msg'  => '成功',
                    'data' => [
                        [
                            'pay_type' => 2,
                            'account_name' => '线下支付',
                            'account_slug' => 'downpay',
                            'account_description' => '到驾校支付',
                        ],
                    ],
                ];
                return response()->json($data);
            }
        }

        $pay_list = DB::table('pay_account_config')
            ->select(
                'id',
                'account_name',
                'account_slug',
                'account_description',
                'pay_type'
            )
            ->where(['is_open' => 1])
            ->whereIn('pay_scope', [$type, 3])
            ->orderBy('order', 'desc')
            ->get();
        $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$pay_list];
        return response()->json($data);
    }

    /**
     * 支付：报名班制和预约计时
     *
     * @param $order_type (signup-报名班制|appoint-预约计时)
     * @param $pay_type (1|2|3|4)
     * @param $pay_from (1-APP|2-Web)
     * @param $order_id
     * @param $order_no
     * @return Response
     */
    public function payOrder() {
        $user_param = (new AuthController())->getUserFromToken($this->request->input('token'));
        if ( ! $this->request->has('order_type')
            || ! $this->request->has('pay_type')
            || ! $this->request->has('pay_from')
            || ! $this->request->has('order_id')
            || ! $this->request->has('order_no')
        ) {
            throw new InvalidArgumentException('参数错误', 400);
        }
        $order_id = $this->request->input('order_id');
        $order_no = $this->request->input('order_no');
        $pay_type = $this->request->input('pay_type');
        $order_param = ['order_id' => $order_id, 'order_no' => $order_no, 'pay_type' => $pay_type];
        $order_type = $this->request->input('order_type');

        switch ($order_type) {
        case 'signup':
            $pay_param = $this->paySignupOrder($order_param, $user_param);
            break;
        case 'appoint':
            $pay_param = $this->payAppointOrder($order_param, $user_param);
            break;
        default :
            Log::info('订单类型不支持');
            throw new InvalidArgumentException('订单类型不支持', 400);
            break;
        }

        $_err = FALSE;
        $_err_msg = '';
        switch ($pay_type) {
        case 1:
            $pay_info['alipay'] = (new Pay($pay_param))->alipay()->app()->purchase();
            break;
        case 3:
            $pay_info['wxpay'] = (new Pay($pay_param))->wechatpay()->app()->purchase();
            break;
        case 4:
            $pay_info['wxpay'] = (new Pay($pay_param))->unionpay()->app()->purchase();
            break;
        case 2:
        default :
            throw new InvalidArgumentException('该支付方式不支持', 1001);
            break;
        }

        if ( ! $pay_info ) {
            throw new InvalidArgumentException("该订单无法发起支付:".$order_param['order_no'], 1001);
        }

        if ($_err) {
            if ('' === $_err_msg) {
                $_err_msg = '下单出错啦';
            }
            return response()->json(['code' => 400, 'msg' => $_err_msg, 'data' => new \stdClass]);
        }

        $data = [
            'code' => 200,
            'msg'  => '获取支付信息成功',
            'data' => [
                'pay' => $pay_info,
            ],
        ];

        return response()->json($data);
    }

    /**
     * 报名班制支付操作
     *
     * @param $order_param
     */
    private function paySignupOrder(Array $order_param, Array $user_param) {
        if (! $this->canSignupOrderBePayed($order_param, $user_param)) {
            throw new InvalidArgumentException('此订单无须再支付', 1001);
        }

        // 更新支付方式
        DB::table('school_orders')
            ->where([
                ['id', '=', $order_param['order_id']],
            ])
            ->update([
                'so_pay_type' => $order_param['pay_type'],
            ]);
        //  ['title', 'desc', 'order_id', 'amount', 'attach_params'];
        $order_info = $this->getSignupOrderInfo($order_param);;
        $pay_param = [
            'order_id' => $order_info->order_no,
            'amount' => $order_info->total_price,
            'title' => "嘻哈学车-报名班制",
            'desc'=>'报名班制订单支付',
            'attach_params' => json_encode(['biz'=>'signup']),
        ];
        // test
        $pay_param['amount'] = 0.01;

        return $pay_param;
    }

    /**
     * 预约计时订单允许支付吗
     *
     * @param $order_param
     */
    private function canSignupOrderBePayed(Array $order_param, Array $user_param) {
        $order_info = $this->getSignupOrderInfo($order_param);

        // 订单不存在
        if ( ! $order_info ) {
            return false;
        }

        // 订单为本人的订单否
        if ( $order_info->user_id != $user_param['user_id'] ) {
            return false;
        }

        // 支付方式有 支付宝1 线下支付2 微信支付3 银联支付4
        if ( ! in_array($order_info->pay_type, [1,2,3,4]) ) {
            return false;
        }

        // 订单状态 未付款(线下为1,线上为4) 已取消(线下为2,线上为3)
        // -- 线下时，1,2
        // -- 线上时，3,4
        if ( ! ($order_info->pay_type == 2 && in_array($order_info->order_status, [1, 2]))
            && ! (in_array($order_info->pay_type, [1,3,4]) && in_array($order_info->order_status, [3,4]))
        ) {
            return false;
        }

        return true;
    }

    /**
     * 预约计时支付操作
     *
     * @param $order_param
     */
    private function payAppointOrder(Array $order_param, Array $user_param) {
        if (! $this->canAppointOrderBePayed($order_param, $user_param)) {
            throw new InvalidArgumentException('此订单无须再支付', 1001);
        }

        // 更新支付方式
        DB::table('study_orders')
            ->where([
                ['l_study_order_id', '=', $order_param['order_id']],
            ])
            ->update([
                'deal_type' => $order_param['pay_type'],
            ]);

        $order_info = $this->getAppointOrderInfo($order_param);;
        $order_info->title = "嘻哈学车-预约计时";
        $pay_param = [
            'order_id' => $order_info->order_id,
            'order_no' => $order_info->order_no,
            'order_time' => $order_info->addtime,
            'money' => $order_info->money,
            'title' => "嘻哈学车-预约计时",
            'order_package' => [
                'order_id' => $order_info->order_id,
                'order_type' => 'appoint',
                'user_id' => $order_info->user_id,
                'user_name' => $order_info->user_name,
                'user_phone' => $user_param['phone'],
            ],
        ];

        return $pay_param;
    }

    /**
     * 预约计时订单允许支付吗
     *
     * @param $order_param
     */
    private function canAppointOrderBePayed(Array $order_param, Array $user_param) {
        $order_info = $this->getAppointOrderInfo($order_param);

        // 订单不存在
        if ( ! $order_info ) {
            return false;
        }

        // 订单为本人的订单否
        if ( $order_info->user_id != $user_param['user_id'] ) {
            return false;
        }

        // 订单状态 付款中1001 未付款1003
        if ( ! in_array($order_info->order_status, [1001, 1003]) ) {
            return false;
        }

        return true;
    }

    /**
     * 支付异步回调接口
     */
    public function payNotify() {
        Log::info('#############################3');
        Log::info(json_encode($this->request, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        Log::info('#############################3');
    }

    /**
     * 支付同步通知接口
     */
    public function payReturn() {
        Log::info('#############################3');
        Log::info(json_encode($this->request, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        Log::info('#############################3');
    }

    // 获取预约当前教练的所有有效计时订单
    public function getCoachAppointOrders($coach_id, $year, $month, $day) {

        if(trim($coach_id) == '' || trim($year) == '' || trim($month) == '' || trim($day) == '') {
            return false;
        }
        $commonwhereCondition = [
            'study_orders.l_coach_id'=>$coach_id,
            'coach_appoint_time.year'=>$year,
            'coach_appoint_time.month'=>$month,
            'coach_appoint_time.day'=>$day
        ];

        // $whereCondition = function($query) {
        //     $query->whereIn('study_orders.i_status', [1, 2, 3, 4])
        //             ->whereIn('study_orders.deal_type', [1, 2, 1003, 1006, 1001]);
        // };
        $whereCondition = function($query) {
            $query->whereNotIn('study_orders.i_status', [3, 101]);
        };
        $order_list = DB::table('study_orders')
            ->select(
                'study_orders.l_study_order_id',
                'study_orders.appoint_time_id',
                'study_orders.time_config_id',
                'study_orders.s_lisence_name',
                'study_orders.s_lesson_name',
                'coach_appoint_time.year',
                'coach_appoint_time.month',
                'coach_appoint_time.day'
            )
            ->leftJoin('coach_appoint_time', 'study_orders.appoint_time_id', '=', 'coach_appoint_time.id')
            ->where($commonwhereCondition)
            ->where($whereCondition)
            ->get();
        return $order_list;
    }

    /**
     * 获取取消原因列表
     *
     * @param $type
     * @return void
     */
    public function getCancelReason($type) {
        if(!$type) {
            return response()->json(['code'=>400,'msg'=>'参数不存在','data'=> new \stdClass]);
        }else if(!in_array($type, array('appoint', 'signup'))) {
            return response()->json(['code'=>400,'msg'=>'参数格式错误','data'=> new \stdClass]);
        }else {
            switch($type) {
            case 'appoint':
                $reason_list = $this->getCancelAppointReasonList();
                break;
            case 'signup':
                $reason_list = $this->getCancelSignupReasonList();
                break;
            default :
                $reason_list = [];
                break;
            }
            $data = ['code' => 200, 'msg' => '成功', 'data' => ['list' => $reason_list]];
            return response()->json($data);
        }
    }

    /**
     * 取消预约学车订单的原因列表
     */
    private function getCancelAppointReasonList() {
        return [
            '临时有事，去不了',
            '计划有变，重新预约其它时间',
            '和教练协商一致取消',
        ];
    }

    /**
     * 取消报名驾校订单的原因列表
     */
    private function getCancelSignupReasonList() {
        return [
            '想换一所驾校报名',
            '因故，无法再学习',
        ];
    }

    /**
     * 取消订单接口
     *
     * @param $token
     * @param $order_type (appoint | signup)
     * @param $order_id
     * @param $order_no
     * @param $cancel_reason
     * @return Response
     */
    public function cancelOrder() {
        // 用户
        $user_param = (new AuthController())->getUserFromToken($this->request->input('token'));

        if (! $this->request->has('order_id') || ! $this->request->has('order_no') || ! $this->request->has('cancel_reason')) {
            throw new InvalidArgumentException('参数错误', 400);
        }
        $order_id = $this->request->input('order_id');
        $order_no = $this->request->input('order_no');
        $order = ['order_id' => $order_id, 'order_no' => $order_no];
        $cancel_reason = $this->request->input('cancel_reason');

        switch ($this->request->input('order_type')) {
        case 'appoint':
            // 预约学车订单的取消操作
            $canceled = $this->cancelAppointOrder($order, $user_param, $cancel_reason);
            $appoint_info = $this->getAppointInfoFromAppointOrder($order);
            $order_info = $this->getAppointOrderInfo($order);
            // 获取提前取消订单的时长
            $order_user_id = $order_info->user_id;
            $coach_school_id = $order_info->school_id;
            $cancel_time = DB::table('school_config')
                ->select(
                    'cancel_in_advance'
                )
                ->where([
                    ['l_school_id', '=', $coach_school_id],
                ])
                ->first();
            if (null !== $cancel_time) {
                $cancel_in_advance = $cancel_time->cancel_in_advance;
            } else {
                $cancel_in_advance = 2;
            }
            if ($canceled) {
                $data = [
                    'code' => 200,
                    'msg'  => '取消预约计时订单成功',
                    'data' => new \stdClass,
                ];
                $push_info = new \StdClass;
                $push_info->product = 'student';
                $push_info->target = $user_param['user_id'];
                $push_info->content = '【取消预约】详情: '.$appoint_info;
                $push_info->type = 2;
                $push_info->member_id = $user_param['user_id'];
                $push_info->member_type = 1;
                $push_info->beizhu = '取消预约';
                $push_info->from = '嘻哈学车';
                $this->redis->rpush('msg_send_queue', json_encode($push_info, JSON_UNESCAPED_UNICODE));

                $push_info->product = 'coach';
                $push_info->target = $order_info->coach_phone;
                $push_info->content = '【取消预约】详情: '.$appoint_info;
                $push_info->type = 2;
                $push_info->member_id = $order_info->coach_id;
                $push_info->member_type = 2;
                $push_info->beizhu = '取消预约';
                $push_info->from = '嘻哈学车';
                $this->redis->rpush('msg_send_queue', json_encode($push_info, JSON_UNESCAPED_UNICODE));

                try {
                    if ($order_info->school_id == 81) { // 广东鸿景驾校
                        // push to 13824616066 植莹莹 whose user_id is 22014
                        $push_info = new \StdClass;
                        $push_info->product = 'student';
                        $push_info->target = 22104;
                        $push_info->content = '【取消预约】详情: '.$appoint_info;
                        $push_info->type = 2;
                        $push_info->member_id = 22014;
                        $push_info->member_type = 1;
                        $push_info->beizhu = '取消预约';
                        $push_info->from = '嘻哈学车';
                        $this->redis->rpush('msg_send_queue', json_encode($push_info, JSON_UNESCAPED_UNICODE));
                    } elseif ($order_info->school_id == 5375) { // 梧州万达驾校
                        // push to 15078112982 娜娜 whose user_id is 5045
                        $push_info = new \StdClass;
                        $push_info->product = 'student';
                        $push_info->target = 5045;
                        $push_info->content = '【取消预约】详情: '.$appoint_info;
                        $push_info->type = 2;
                        $push_info->member_id = 5045;
                        $push_info->member_type = 1;
                        $push_info->beizhu = '取消预约';
                        $push_info->from = '嘻哈学车';
                        $this->redis->rpush('msg_send_queue', json_encode($push_info, JSON_UNESCAPED_UNICODE));
                    }
                } catch (Exception $e) {
                }
            } else {
                $data = [
                    'code' => 400,
                    'msg'  => '需提前'.$cancel_in_advance.'小时取消订单',
                    'data' => new \stdClass,
                ];
                // throw new InvalidArgumentException('需提前'.$cancel_in_advance.'小时取消订单', 400);
            }
            break;
        case 'signup':
            // 报名驾校订单的取消操作
            $canceled = $this->cancelSignupOrder($order, $user_param, $cancel_reason);
            $order_info = $this->getSignupOrderInfo(['order_id' => $order_id]);
            $content = $order_info->school_name.'/'.$order_info->shift_name.'/'.$order_info->license.',订单号'.$order_info->order_no;
            if ($canceled) {
                $data = [
                    'code' => 200,
                    'msg'  => '取消报名班制订单成功',
                    'data' => new \stdClass,
                ];
                $push_info = new \StdClass;
                $push_info->product = 'student';
                $push_info->target = $user_param['user_id'];
                $push_info->content = '【取消报名】详情: '.$content;
                $push_info->type = 2;
                $push_info->member_id = $user_param['user_id'];
                $push_info->member_type = 1;
                $push_info->beizhu = '取消报名';
                $push_info->from = '嘻哈学车';
                $this->redis->rpush('msg_send_queue', json_encode($push_info, JSON_UNESCAPED_UNICODE));
            } else {
                $data = [
                    'code' => 400,
                    'msg'  => '取消报名班制订单失败',
                    'data' => new \stdClass,
                ];
            }
            break;
        default :
            // 其它
            $canceled = false;
            $canceled = 2;
            break;
        }
        return response()->json($data);
    }

    /**
     * 取消预约学车订单
     *
     * @param $order_param
     * @param $user_param
     * @param $cancel_reason
     * @return bool
     */
    private function cancelAppointOrder(Array $order_param, Array $user_param, $cancel_reason = '') {
        // 可以取消吗
        if ( ! $this->canAppointOrderBeCanceled($order_param, $user_param) ) {
            return false;
        }

        $order_info = $this->getAppointOrderInfo($order_param);

        switch ($order_info->order_status) {
            //已付款，取消即申请退款
        case 1:
            $update_order_status = 1006;
            if ( $order_info->money <= 0 ) {
                // 如果订单金额少于或等于0元，无须退款，直接取消订单即可
                $update_order_status = 3;
            }
            break;
            //还没付款，取消就取消吧
        case 1003:
            $update_order_status = 3;
            break;
            // 其它，也取消
        default :
            $update_order_status = 3;
            break;
        }

        switch ($user_param['i_user_type']) {
        case 0:
            $cancel_from = 1; // 学员端取消
            break;
        case 1:
            $cancel_from = 2; // 教练端取消
            break;
        default :
            $cancel_from = 0; // 未知来源
            break;
        }
        // actually cancel
        $cancel_order = DB::table('study_orders')
            ->where([
                ['study_orders.l_study_order_id', '=', $order_param['order_id']],
                ['study_orders.s_order_no', '=', $order_param['order_no']],
            ])
            ->update([
                'study_orders.i_status' => $update_order_status,
                'study_orders.cancel_type' => $cancel_from,
                'study_orders.cancel_reason' => $cancel_reason,
                'study_orders.cancel_time' => time(),
            ]);
        if ($cancel_order) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取预约学车订单详情
     *
     * @param $order_param
     * @return \stdClass $order_info
     */
    public function getAppointOrderInfo(Array $order_param) {
        if (! Arr::exists($order_param, 'order_id')) {
            return new \Stdclass();
        }
        $where[] = ['study_orders.l_study_order_id', '=', $order_param['order_id']];
        if (Arr::exists($order_param, 'order_no')) {
            $where[] = ['study_orders.s_order_no', '=', $order_param['order_no']];
        }
        $order_info = DB::table('study_orders')
            ->select(
                'study_orders.l_study_order_id as order_id',
                'study_orders.s_order_no as order_no',
                'study_orders.l_user_id as user_id',
                'study_orders.s_user_name as user_name',
                'study_orders.s_user_phone as user_phone',
                'study_orders.l_coach_id as coach_id',
                'study_orders.s_coach_name as coach_name',
                'study_orders.s_coach_phone as coach_phone',
                'study_orders.s_lisence_name as license_name',
                'study_orders.s_lesson_name as lesson_name',
                'school.s_school_name as school_name',
                'school.l_school_id as school_id',
                'study_orders.dc_money as money',
                'study_orders.s_zhifu_dm as transaction_no',
                'study_orders.dt_zhifu_time as pay_time',
                'study_orders.dt_appoint_time as appoint_date',
                'study_orders.dt_order_time as addtime',
                'study_orders.time_config_id as time_config_id',
                'study_orders.deal_type as pay_type',
                'study_orders.i_status as order_status'
            )
            ->leftJoin('coach', 'coach.l_coach_id', '=', 'study_orders.l_coach_id')
            ->leftJoin('school', 'school.l_school_id', '=', 'coach.s_school_name_id')
            ->where($where)
            ->first();
        if ($order_info) {
            $order_info->addtime_format = date('Y-m-d H:i:s', $order_info->addtime);
            $order_info->appoint_date = explode(' ', $order_info->appoint_date)[0];
            $order_info->appoint_time = $this->getTimeStringFromTimeConfigId( $order_info->time_config_id );
            $order_info->appoint_begin_time = strtotime($order_info->appoint_date.' '.explode('-', $order_info->appoint_time)[0]);
            $school_config = DB::table('school_config')
                ->select(
                    'school_config.cancel_in_advance as cancel_in_advance'
                )
                ->where('school_config.l_school_id', '=', $order_info->school_id)
                ->first();
            if ($school_config) {
                $order_info->cancel_in_advance = $school_config->cancel_in_advance;
            } else {
                $order_info->cancel_in_advance = 2; // 默认须提前2小时取消预约计时的订单
            }

            return $order_info;
        } else {
            return new \stdclass;
        }
    }


    /**
     * 预约学车订单可以被取消吗
     *
     * @param Array $order_param
     * @param Array $user_param
     * @return bool
     */
    private function canAppointOrderBeCanceled(Array $order_param, Array $user_param) {
        $order_info = $this->getAppointOrderInfo($order_param);

        // 订单不存在，就不取消了呗
        if ( ! $order_info ) {
            return false;
        }

        // 订单为本人的订单否
        if ( $order_info->user_id != $user_param['user_id'] ) {
            return false;
        }

        // 支付方式有 支付宝1 线下支付2 微信支付3 银联支付4
        if ( ! in_array($order_info->pay_type, [1,2,3,4]) ) {
            return false;
        }

        // 订单状态 已取消3 未付款1003 待完成1
        if ( ! in_array($order_info->order_status, [1, 3, 1003]) ) {
            return false;
        }

        // 按要求提前取消的吗
        if ( (time() + $order_info->cancel_in_advance * 3600) > $order_info->appoint_begin_time ) {
            return false;
        }

        return true;
    }

    /**
     * 取消报名驾校订单
     *
     * @param $order_param
     * @param $user_param
     * @param $cancel_reason
     * @return bool
     */
    private function cancelSignupOrder(Array $order_param, Array $user_param, $cancel_reason = '') {
        if ( ! $this->canSignupOrderBeCanceled($order_param, $user_param) ) {
            return false;
        }

        $order_info = $this->getSignupOrderInfo($order_param);
        if ($order_info->pay_type == 2) {
            // 线下支付的订单，如何处理
            switch ($order_info->order_status) {
                //已付款，取消即申请退款
            case 3:
                $update_order_status = 4;
                break;
                //还没付款，取消就取消吧
            case 1:
                $update_order_status = 2;
                break;
                // 其它，也取消
            default :
                $update_order_status = 2;
                break;
            }
        } else if (in_array($order_info->pay_type, [1,3,4])) {
            // 线上支付的订单，又如何处理
            switch ($order_info->order_status) {
                //已付款，取消即申请退款
            case 1:
                $update_order_status = 2;
                break;
                //还没付款，取消就取消吧
            case 4:
                $update_order_status = 3;
                break;
                // 其它，也取消
            default :
                $update_order_status = 3;
                break;
            }
        }
        // actually cancel
        $cancel_order = DB::table('school_orders')
            ->where([
                ['school_orders.id', '=', $order_param['order_id']],
                ['school_orders.so_order_no', '=', $order_param['order_no']],
            ])
            ->update([
                'school_orders.so_order_status' => $update_order_status,
                'school_orders.cancel_type' => 1, // 学员端取消状态字1
                'school_orders.cancel_reason' => $cancel_reason,
                'school_orders.cancel_time' => time(),
            ]);
        if ($cancel_order) {
            return true;
        } else {
            return false;
        }

        return true;
    }

    /**
     * 报名驾校订单可以取消吗
     *
     * @param $order_param
     * @param $user_param
     * @return bool
     */
    private function canSignupOrderBeCanceled(Array $order_param, Array $user_param) {
        $order_info = $this->getSignupOrderInfo($order_param);

        // 订单不存在，就不取消了呗
        if ( ! $order_info ) {
            return false;
        }

        // 订单为本人的订单否
        if ( $order_info->user_id != $user_param['user_id'] ) {
            return false;
        }

        // 支付方式有 支付宝1 线下支付2 微信支付3 银联支付4
        if ( ! in_array($order_info->pay_type, [1,2,3,4]) ) {
            return false;
        }

        // 订单状态 未付款(线下为1,线上为4) 已付款(线下为3,线上为1)
        // -- 线下时，1,3
        // -- 线上时，1,4
        if ( ! ($order_info->pay_type == 2 && in_array($order_info->order_status, [1, 3]))
            && ! (in_array($order_info->pay_type, [1,3,4]) && in_array($order_info->order_status, [1,4]))
        ) {
            return false;
        }

        return true;
    }

    /**
     * 获取报名驾校订单详情
     *
     * @param $order_param
     * @return \stdClass $order_info
     */
    public function getSignupOrderInfo(Array $order_param) {
        if (! Arr::exists($order_param, 'order_id')) {
            return new \Stdclass();
        }
        $where[] = ['school_orders.id', '=', $order_param['order_id']];
        if (Arr::exists($order_param, 'order_no')) {
            $where[] = ['school_orders.so_order_no', '=', $order_param['order_no']];
        }
        $order_info = DB::table('school_orders')
            ->select(
                'school_orders.id as order_id',
                'school.s_school_name as school_name',
                'school_orders.so_order_no as order_no',
                'school_orders.s_zhifu_dm as transaction_no',
                'school_orders.so_final_price as final_price',
                'school_orders.so_original_price as original_price',
                'school_orders.so_total_price as total_price',
                'school_orders.so_school_id as school_id',
                'school_orders.so_shifts_id as shift_id',
                'school_orders.so_pay_type as pay_type',
                'school_orders.so_order_status as order_status',
                'school_orders.so_user_id as user_id',
                'school_orders.so_username as user_name',
                'school_orders.so_phone as user_phone',
                'school_orders.so_coach_id as coach_id',
                'school_orders.so_licence as license',
                'school_orders.addtime',
                'school_shifts.sh_title as shift_name'
            )
            ->leftJoin('school_shifts', 'school_orders.so_shifts_id', '=', 'school_shifts.id')
            ->leftJoin('school', 'school_orders.so_school_id', '=', 'school.l_school_id')
            ->where($where)
            ->first();
        if ($order_info) {
            $order_info->addtime_format = date('Y-m-d H:i:s', $order_info->addtime);
        }
        return $order_info;
    }

    /**
     * 评论订单接口
     *
     * @param $token
     * @param $order_id
     * @param $order_no
     * @param $comment_content
     * @return Response
     */
    public function commentOrder() {
        // 用户
        $user_param = (new AuthController())->getUserFromToken($this->request->input('token'));

        if (! $this->request->has('order_id') || ! $this->request->has('order_no') || ! $this->request->has('comment_content')) {
            return [
                'code' => 400,
                'msg'  => '参数错误',
                'data' => new \stdClass,
            ];
        }
        $order_id = $this->request->input('order_id');
        $order_no = $this->request->input('order_no');
        $order = ['order_id' => $order_id, 'order_no' => $order_no];
        $comment_content = $this->request->input('comment_content');
        $comment_star = $this->request->input('comment_star');
        $comment = ['star' => $comment_star, 'content' => $comment_content];

        switch ($this->request->input('order_type')) {
        case 'appoint':
            // 预约学车订单的评价
            $commented = $this->commentAppointOrder($order, $user_param, $comment);
            break;
        case 'signup':
            // 报名驾校订单的评价
            $commented = $this->commentSignupOrder($order, $user_param, $comment);
            break;
        default :
            // 其它
            $commented = false;
            break;
        }

        if ($commented) {
            $data = [
                'code' => 200,
                'msg'  => '评价成功',
                'data' => new \stdClass,
            ];
        } else {
            $data = [
                'code' => 400,
                'msg'  => '评价失败',
                'data' => new \stdClass,
            ];
        }

        return response()->json($data);
    }

    /**
     * 评价预约学车的订单
     *
     * @param $order_param
     * @param $user_param
     * @return bool
     */
    private function commentAppointOrder(Array $order_param, Array $user_param, Array $comment) {
        // 你可以评价吗
        if ( ! $this->canAppointOrderBeCommented($order_param, $user_param) ) {
            return false;
        }

        $order_info = $this->getAppointOrderInfo($order_param);

        // actually comment
        $commented = DB::table('coach_comment')
            ->insertGetId([
                'coach_comment.order_no' => $order_param['order_no'],
                'coach_comment.coach_id' => $order_info->coach_id,
                'coach_comment.coach_content' => $comment['content'],
                'coach_comment.coach_star' => $comment['star'],
                'coach_comment.user_id' => $user_param['user_id'],
                'coach_comment.type' => 1, // 评价来源 预约学车 1
                'coach_comment.addtime' => time(),
            ]);
        if ($commented) {
            // 更新预约学车订单评价状态 ２０１７－０９－２６　１３：３４：５７　by 夏玉峰
            DB::table('study_orders')->where('l_study_order_id', '=', $order_param['order_id'])->update(['comment_status' => 1]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 预约学车订单可以评价吗
     *
     * @param $order_param
     * @param $user_param
     * @return bool
     */
    private function canAppointOrderBeCommented(Array $order_param, Array $user_param) {
        $order_info = $this->getAppointOrderInfo($order_param);

        // 订单存在吗
        if (! $order_info ) {
            return false;
        }

        // 订单是你的吗
        if ( $order_info->user_id != $user_param['user_id'] ) {
            return false;
        }

        // 已完成的订单方能评价
        if ( $order_info->order_status != 2 ) {
            return false;
        }

        $comment_info = DB::table('coach_comment')
            ->where([
                ['coach_comment.user_id', '=', $user_param['user_id']],
                ['coach_comment.order_no', '=', $order_param['order_no']],
            ])
            ->first();

        // 已经评价过了呀
        if ($comment_info) {
            return false;
        }

        return true;
    }

    /**
     * 评价报名驾校订单
     *
     * @param $order_param
     * @param $user_param
     * @return bool
     */
    private function commentSignupOrder(Array $order_param, Array $user_param, Array $comment) {
        // 你可以评价吗
        if ( ! $this->canSignupOrderBeCommented($order_param, $user_param) ) {
            return false;
        }

        $order_info = $this->getSignupOrderInfo($order_param);

        // actually comment
        $commented = DB::table('coach_comment')
            ->insertGetId([
                'coach_comment.order_no' => $order_param['order_no'],
                'coach_comment.school_id' => $order_info->school_id,
                'coach_comment.school_content' => $comment['content'],
                'coach_comment.school_star' => $comment['star'],
                'coach_comment.user_id' => $user_param['user_id'],
                'coach_comment.type' => 2, // 评价来源 报名驾校 2
                'coach_comment.addtime' => time(),
            ]);
        if ($commented) {
            $update_order_comment_status = DB::table('school_orders')
                ->where([
                    ['school_orders.id', '=', $order_param['order_id']],
                    ['school_orders.so_order_no', '=', $order_param['order_no']],
                ])
                ->update([
                    'so_comment_status' => 2,
                ]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 报名驾校订单可以评价吗
     *
     * @param $order_param
     * @param $user_param
     * @return bool
     */
    private function canSignupOrderBeCommented(Array $order_param, Array $user_param) {
        $order_info = $this->getSignupOrderInfo($order_param);

        // 订单存在吗
        if (! $order_info ) {
            return false;
        }

        // 订单是你的吗
        if ( $order_info->user_id != $user_param['user_id'] ) {
            return false;
        }

        // 已付款的订单方能评价
        if ( ! ($order_info->pay_type == 2 && $order_info->order_status == 3)
            && ! (in_array($order_info->pay_type, [1,3,4]) && $order_info->order_status == 1)
        ) {
            return false;
        }

        $comment_info = DB::table('coach_comment')
            ->where([
                ['coach_comment.user_id', '=', $user_param['user_id']],
                ['coach_comment.order_no', '=', $order_param['order_no']],
            ])
            ->first();

        // 已经评价过了呀
        if ($comment_info) {
            return false;
        }

        return true;
    }

    // 生成唯一识别码
    public function guid($opt = true){       //  Set to true/false as your default way to do this.

        if( function_exists('com_create_guid')) {
            if( $opt ){
                return com_create_guid();
            } else {
                return trim( com_create_guid(), '{}' );
            }
        } else {
            mt_srand( (double)microtime() * 10000 );    // optional for php 4.2.0 and up.
            $charid = strtoupper( md5(uniqid(rand(), true)) );
            $hyphen = chr( 45 );    // "-"
            $left_curly = $opt ? chr(123) : "";     //  "{"
            $right_curly = $opt ? chr(125) : "";    //  "}"
            $uuid = $left_curly
                . substr( $charid, 0, 8 ) . $hyphen
                . substr( $charid, 8, 4 ) . $hyphen
                . substr( $charid, 12, 4 ) . $hyphen
                . substr( $charid, 16, 4 ) . $hyphen
                . substr( $charid, 20, 12 )
                . $right_curly;
            return $uuid;
        }
    }

    /**
     * 预约计时订单超时接口
     *  - 取消订单
     * @param $order_id
     * @return void
     */
    public function timeoutAppointTimeOrder() {
        // Step 1 cancel
        $order_id = $this->request->input('order_id');

        $order_info = $this->getAppointOrderInfo(['order_id' => $order_id]);

        // 未成功付款的订单才有超时订单操作，否则直接返回
        switch ($order_info->order_status) {
        case 1003: // 未付款取消
        case 1001: // 付款中取消
            $order_status = 3;
            break;
        default :
            return 'ok';
            break;
        }

        // 取消订单操作
        DB::table('study_orders')
            ->where('l_study_order_id', '=', $order_id)
            ->update([
                'i_status' => $order_status,
                'cancel_type' => 0,
                'cancel_reason' => '订单付款超时，自动取消',
                'cancel_time' => time(),
                's_beizhu' => 'swoole task server',
            ]);
    }

    public function sendMessage() {
        $push_info = $this->request->input();
        try {
            if (
                !empty($push_info)
                && isset($push_info['product'])
                && isset($push_info['target'])
                && isset($push_info['content'])
                && isset($push_info['type'])
                && isset($push_info['member_id'])
                && isset($push_info['member_type'])
                && isset($push_info['beizhu'])
                && isset($push_info['from'])
            ) {
                $pusher = new PusherController($push_info['product']);
                $pusher->setTarget($push_info['target'])
                    ->setContent($push_info['content'])
                    ->setType($push_info['type'])
                    ->setMemberId($push_info['member_id'])
                    ->setMemberType($push_info['member_type'])
                    ->setBeizhu($push_info['beizhu'])
                    ->setFrom($push_info['from'])
                    ->send();
            }
        } catch (Exception $e) {
            Log::error('消息发送出现异常,消息内容:'.json_encode($push_info, JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * 获取学员预约教练的学车信息
     *
     * @param Array ['order_id' => $order_id, 'order_no' => $order_no]
     * @return mixed
     */
    private function getAppointInfoFromAppointOrder($param) {
        if ( isset($param['order_id']) ) {
            $order_id = $param['order_id'];
        } else {
            return null;
        }
        $order_info = $this->getAppointOrderInfo($param);
        if ( ! $order_info ) {
            return null;
        }
        //$appoint_info = sprintf("学员：%s(电话%s)约了教练：%s(电话%s)在%s的以下时间段%s学车(%s，%s)。",
        $appoint_info = sprintf("学员：%s(电话%s)约了教练：%s(电话%s)在%s的以下时间段%s学车(%s，%s)。",
            $order_info->user_name,
            $order_info->user_phone,
            $order_info->coach_name,
            $order_info->coach_phone,
            $order_info->appoint_date,
            $order_info->appoint_time,
            $order_info->license_name,
            $order_info->lesson_name
        );
        return $appoint_info;
    }

    /**
     * 获取1-24个时间段
     *
     * @param ids (eg: 2,3)
     * @return String (eg: 8:00-9:00, 9:00-10:00)
     */
    private function getTimeStringFromTimeConfigId($ids) {
        if ( ! is_string($ids) ) {
            return null;
        }
        $id_list = array_filter( explode(',', $ids) );
        if ( empty($id_list) ) {
            return null;
        }

        $time_config = DB::table('coach_time_config')
            ->select(
                'id',
                'start_time',
                'start_minute',
                'end_time',
                'end_minute'
            )
            ->where([
                ['status', '=', 1], // 开放状态
            ])
            ->get()
            ;
        $time_list = [];
        foreach ($time_config as $key => $config) {
            $start_minute = ($config->start_minute <= 9) ? '0'.$config->start_minute: $config->start_minute;
            $end_minute = ($config->end_minute <= 9) ? '0'.$config->end_minute: $config->end_minute;
            $time_list[$config->id] = $config->start_time.':'.$start_minute.'-'.$config->end_time.':'.$end_minute;
        }
        if ( empty( $time_list ) ) {
            return null;
        }

        $appoint_time_list = [];
        foreach ($id_list as $id) {
            if ( array_key_exists($id, $time_list) ) {
                $appoint_time_list[] = $time_list[$id];
            }
        }
        if ( empty( $appoint_time_list ) ) {
            return '';
        }
        return implode(',', $appoint_time_list);
    }

    /**
     * 发送优惠券
     */
    public function sendCoupon($params) {
        $order_info = $this->getSignupOrderInfo(['order_id' => $params['order_id']]);
        $debug['order_info'] = $order_info;

        $config_list = array();
        $config = new \stdClass;
        $config->school_id = 4694;
        $config->school_name = '嘻哈测试驾校';
        $config->content = '【券到账】恭喜您，券已到账，请查收';
        $config->shift_id = 542;
        $config->coupon_id = 28;
        $config_list[] = $config;

        $config = new \stdClass;
        $config->school_id = 4694;
        $config->school_name = '嘻哈测试驾校';
        $config->content = '【券到账】恭喜您，券已到账，请查收';
        $config->shift_id = 652;
        $config->coupon_id = 28;
        $config_list[] = $config;

        $config = new \stdClass;
        $config->school_id = 72;
        $config->school_name = '安农大';
        $config->content = '【嘻哈学车】恭喜您获得351元学车优惠券，请于4月20日前使用';
        $config->shift_id = 649;
        $config->coupon_id = 29;
        $config_list[] = $config;

        $config = new \stdClass;
        $config->school_id = 4694;
        $config->school_name = '安农大';
        $config->content = '【嘻哈学车】恭喜您获得701元学车优惠券，请于4月20日前使用';
        $config->shift_id = 650;
        $config->coupon_id = 30;
        $config_list[] = $config;

        foreach ($config_list as $i => $config) {
            if ($order_info->school_id == $config->school_id && $order_info->shift_id == $config->shift_id) {
                $this->user->couponOperation('', $config->coupon_id, 340000, 340100, $order_info->user_phone, $order_info->user_name);
                $push_info = new \StdClass;
                $push_info->product = 'student';
                $push_info->target = $order_info->user_id;
                $push_info->content = $config->content;
                $push_info->type = 1;
                $push_info->member_id = $order_info->user_id;
                $push_info->member_type = 1;
                $push_info->beizhu = '券到账';
                $push_info->from = '嘻哈学车';
                $this->redis->rpush('msg_send_queue', json_encode($push_info, JSON_UNESCAPED_UNICODE));
                Log::info('券发送成功 user_phone->'.$order_info->user_phone);
                break;
            }
        }
    }

    /**
     * 订单退款
     * URL: /order/{order_type}/refund
     */
    public function refund($order_type = '') {
        if ( ! isset($order_type) OR '' === $order_type OR ! in_array($order_type, ['signup', 'appoint'])) {
            Log::error('order_type不允许');
            return response()->json(['code' => 400, 'msg' => 'Not allowed', 'data' => new \stdClass]);
        }

        if ( ! $this->request->has('order_no') && ! $this->request->has('order_id')) {
            return response()->json(['code' => 400, 'msg' => 'Order info needed', 'data' => new \stdClass]);
        }

        $order_param = [];
        if ($this->request->has('order_no')) {
            $order_param['order_no'] = $this->request->input('order_no');
        }
        if ($this->request->has('order_id')) {
            $order_param['order_id'] = $this->request->input('order_id');
        }

        switch ($order_type) {
        case 'signup':
            $refund_result = $this->_refundSignupOrder($order_param);
            break;
        case 'appoint':
            $refund_result = $this->_refundAppointOrder($order_param);
            break;
        default:
            Log::info('order_type不允许');
            return response()->json(['code' => 400, 'msg' => 'Not allowed', 'data' => new \stdClass]);
            break;
        }
        return $refund_result;
    }

    /**
     * 报名驾校订单退款
     */
    protected function _refundSignupOrder(Array $order_param) {
        if ( ! ($order_info = $this->getSignupOrderInfo($order_param))) {
            Log::info('No such order', ['order_param' => $order_param]);
            return response()->json(['code' => 400, 'msg' => 'No such order', 'data' => new \stdClass]);
        }

        // make sure the order has been paied or canceled
        if (isset($order_info->pay_type) && in_array($order_info->pay_type, [1, 3, 4])
            && isset($order_info->order_status) && in_array((int)$order_info->order_status, [1, 3, 1002, 1005])) {
            $refund_info = [
                'user_id'           => $order_info->user_id,
                'order_type'        => 'signup',
                'order_id'          => $order_info->order_id,
                'order_no'          => $order_info->order_no,
                'money'             => $order_info->total_price, // 单位：元
                'transaction_no'    => $order_info->transaction_no,
                'order_package' => [
                    'order_id'      => $order_info->order_id,
                    'order_type'    => 'signup',
                    'user_id'       => $order_info->user_id,
                    'user_name'     => $order_info->user_name,
                    'user_phone'    => $order_info->user_phone,
                ],
            ];
            if ($refund_result = (new PayController($order_info->pay_type))->refund($refund_info)) {
                return response()->json(['code' => 200, 'msg' => 'OK', 'data' => ['refund' => $refund_result]]);
            } else {
                return response()->json(['code' => 400, 'msg' => 'Refund fail', 'data' => new \stdClass]);
            }
        }

        Log::info('The order is either online or paid');
        return response()->json(['code' => 400, 'msg' => 'Not allowed', 'data' => new \stdClass]);
    }

    /**
     * 预约计时订单退款
     */
    protected function _refundAppointOrder(Array $order_param) {
        if ( ! ($order_info = $this->getAppointOrderInfo(($order_param)))) {
            return response()->json(['code' => 400, 'msg' => 'No such order', 'data' => new \stdClass]);
        }

        // make sure the order has been paied or canceled
        if (isset($order_info->pay_type) && in_array($order_info->pay_type, [1, 3, 4])
            && isset($order_info->order_status) && in_array((int)$order_info->order_status, [1, 3, 1002, 1005])) {
            $refund_info = [
                'user_id'           => $order_info->user_id,
                'order_type'        => 'appoint',
                'order_id'          => $order_info->order_id,
                'order_no'          => $order_info->order_no,
                'money'             => $order_info->money, // 单位：元
                'transaction_no'    => $order_info->transaction_no,
                'order_package' => [
                    'order_id'      => $order_info->order_id,
                    'order_type'    => 'appoint',
                    'user_id'       => $order_info->user_id,
                    'user_name'     => $order_info->user_name,
                    'user_phone'    => $order_info->user_phone,
                ],
            ];
            if ($refund_result = (new PayController($order_info->pay_type))->refund($refund_info)) {
                return response()->json(['code' => 200, 'msg' => 'OK', 'data' => ['refund' => $refund_result]]);
            } else {
                return response()->json(['code' => 400, 'msg' => 'Refund fail', 'data' => new \stdClass]);
            }
        }

        Log::info('The order maybe wrong');
        return response()->json(['code' => 400, 'msg' => 'Not allowed', 'data' => new \stdClass]);
    }

    /**
     * 查询订单
     *
     * @param int $order_id
     * @param int $order_no
     */
    public function query($order_type = '') {
        if ( ! isset($order_type) OR '' === $order_type OR ! in_array($order_type, ['signup', 'appoint'])) {
            Log::error('order_type不允许');
            return response()->json(['code' => 400, 'msg' => 'Not allowed', 'data' => new \stdClass]);
        }

        if ( ! $this->request->has('order_no') && ! $this->request->has('order_id')) {
            return response()->json(['code' => 400, 'msg' => 'Order info needed', 'data' => new \stdClass]);
        }

        $order_param = [];
        if ($this->request->has('order_no')) {
            $order_param['order_no'] = $this->request->input('order_no');
        }
        if ($this->request->has('order_id')) {
            $order_param['order_id'] = $this->request->input('order_id');
        }

        switch ($order_type) {
        case 'signup':
            $order_info = $this->getSignupOrderInfo($order_param);
            if ($order_info) {
                $query_info = [
                    'order_no' => $order_info->order_no,
                    'order_id' => $order_info->order_id,
                    'transaction_no' => $order_info->transaction_no,
                    'money' => $order_info->total_price,
                ];
            } else {
                Log::error('无此订单');
                return response()->json(['code' => 400, 'msg' => 'No such order', 'data' => new \stdClass]);
            }
            break;
        case 'appoint':
            $order_info = $this->getAppointOrderInfo($order_param);
            if ($order_info) {
                $query_info = [
                    'order_no' => $order_info->order_no,
                    'order_id' => $order_info->order_id,
                    'transaction_no' => $order_info->transaction_no,
                    'money' => $order_info->money,
                ];
            } else {
                Log::error('无此订单');
                return response()->json(['code' => 400, 'msg' => 'No such order', 'data' => new \stdClass]);
            }
            break;
        default:
            Log::error('order_type不允许');
            return response()->json(['code' => 400, 'msg' => 'Not allowed', 'data' => new \stdClass]);
            break;
        }

        if (isset($order_info->pay_type) && in_array($order_info->pay_type, [1, 3, 4])) { // 在线支付 1支付宝 3微信 4银联
            $pay = new PayController($order_info->pay_type);
            $query_result = $pay->query($query_info);
            return response()->json(['code' => 200, 'msg' => 'order query ok', 'data' => ['query' => $query_result]]);
        } else {
            Log::info('pay_type no need to query');
            return response()->json(['code' => 400, 'msg' => 'Not allowed', 'data' => new \stdClass]);
        }

    }

    /**
     * 退款订单查询
     *
     * @param int $order_id
     * @param int $order_no
     */
    public function queryRefund($order_type = '') {
        if ( ! isset($order_type) OR '' === $order_type OR ! in_array($order_type, ['signup', 'appoint'])) {
            Log::error('order_type不允许');
            return response()->json(['code' => 400, 'msg' => 'Not allowed', 'data' => new \stdClass]);
        }

        if ( ! $this->request->has('order_no') && ! $this->request->has('order_id')) {
            return response()->json(['code' => 400, 'msg' => 'Order info needed', 'data' => new \stdClass]);
        }

        $order_param = [];
        if ($this->request->has('order_no')) {
            $order_param['order_no'] = $this->request->input('order_no');
        }
        if ($this->request->has('order_id')) {
            $order_param['order_id'] = $this->request->input('order_id');
        }

        switch ($order_type) {
        case 'signup':
            $order_info = $this->getSignupOrderInfo($order_param);
            if ($order_info) {
                $query_info = [
                    'order_no' => $order_info->order_no,
                    'order_id' => $order_info->order_id,
                    'transaction_no' => $order_info->transaction_no,
                    'money' => $order_info->total_price,
                ];
            } else {
                Log::error('无此订单');
                return response()->json(['code' => 400, 'msg' => 'No such order', 'data' => new \stdClass]);
            }
            break;
        case 'appoint':
            $order_info = $this->getAppointOrderInfo($order_param);
            if ($order_info) {
                $query_info = [
                    'order_no' => $order_info->order_no,
                    'order_id' => $order_info->order_id,
                    'transaction_no' => $order_info->transaction_no,
                    'money' => $order_info->money,
                ];
            } else {
                Log::error('无此订单');
                return response()->json(['code' => 400, 'msg' => 'No such order', 'data' => new \stdClass]);
            }
            break;
        default:
            Log::error('order_type不允许');
            return response()->json(['code' => 400, 'msg' => 'Not allowed', 'data' => new \stdClass]);
            break;
        }

        if (isset($order_info->pay_type) && in_array($order_info->pay_type, [1, 3, 4])) { // 在线支付 1支付宝 3微信 4银联
            $pay = new PayController($order_info->pay_type);
            $query_result = $pay->queryRefund($query_info);
            return response()->json(['code' => 200, 'msg' => 'refund query ok', 'data' => ['refund_query' => $query_result]]);
        } else {
            Log::info('pay_type no need to query');
            return response()->json(['code' => 400, 'msg' => 'Not allowed', 'data' => new \stdClass]);
        }

    }
}
