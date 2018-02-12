<?php

namespace App\Http\Controllers\v3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\v3\student\PusherController;
use Log;
use Xiha\Pay\Pay;

class PayController extends Controller
{
    /**
     * @var Illuminate\Http\Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * 支付异步通知处理
     */
    public function notify($method)
    {
        switch (strtolower($method)) {
            case 'wechatpay':
                $data = ['request_params' => file_get_contents('php://input')];

                break;
            case 'alipay':
            case 'unionpay':
            default:
                $data = $this->request->input();

                break;
        }

        return (new Pay($data))->$method()->app()->notify();
    }

    /**
     * 退款异步通知处理
     */
    public function refundNotify($method)
    {
        switch (strtolower($method)) {
            case 'wechatpay':
                $data = ['request_params' => file_get_contents('php://input')];

                break;
            case 'alipay':
            case 'unionpay':
            default:
                $data = $this->request->input();

                break;
        }

        return (new Pay($data))->$method()->app()->refundNotify();
    }

    /**
     * 报名班制或驾校业务的付款后的处理
     */
    public function signupBiz()
    {
        Log::info('biz begin');
        Log::info(PHP_EOL.json_encode(
            $this->request->input(),
            JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        ));

        $pay_info = $this->request->input();
        $log = DB::table('pay_log')->select()->where(['trade_id'=>$pay_info['trade_id']])->get()->first();
        Log::info(PHP_EOL.json_encode(
            $log,
            JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        ));
        if(isset($pay_info) && isset($log)) {
            // 开启事务
            DB::beginTransaction();
            try {
                $_update = [];
                $so_order_status = 0;
                if($pay_info['gateway'] == 'Alipay') {
                    $pay_info['pay_type'] = 1;
                }else if($pay_info['gateway'] == 'Wechatpay') {
                    $pay_info['pay_type'] = 3;
                }else if($pay_info['gateway'] == 'Unionpay') {
                    $pay_info['pay_type'] = 4;
                }
                if($pay_info['trade_status'] == 'TRADE_SUCCESS' || $pay_info['trade_status'] == 'SUCCESS') {
                    $so_order_status = 1;
                }
                // 支付方式 1.支付宝  2.微信  3.银联
                if (isset($pay_info['pay_type'])) {
                    $_update['school_orders.so_pay_type'] = $pay_info['pay_type'];
                }
                // 支付宝交易号
                if (isset($pay_info['trade_id'])) {
                    $_update['school_orders.s_zhifu_dm'] = $pay_info['trade_id'];
                }
                // 支付状态
                if (isset($pay_info['trade_status'])) {
                    $_update['school_orders.so_order_status'] = $so_order_status;
                }
                // 支付时间
                if (isset($pay_info['trade_payment'])) {
                    $_update['school_orders.dt_zhifu_time'] = $pay_info['trade_payment'];
                }
                // 实际支付金额
                if(isset($pay_info['pay_amount'])) {
                    $_update['school_orders.so_total_price'] = $pay_info['pay_amount'];
                }
                //  更新订单支付状态
                DB::table('school_orders')->where([['school_orders.so_order_no', '=', $pay_info['order_id']],])->update($_update);
                $pusher = new PusherController('student');
                $order = DB::table('school_orders')->select('so_user_id as user_id')->where(['so_order_no'=>$pay_info['order_id']])->get()->first();
                $pay_info['user_id'] = $order->user_id;
                $pusher->setTarget($pay_info['user_id'])
                    ->setContent('【报名班制订单】您的报名班制订单状态有变动。')
                    ->setType(2)
                    ->setMemberId($pay_info['user_id'])
                    ->setMemberType(1)
                    ->setBeizhu('报名班制')
                    ->setFrom('嘻哈学车')
                    ->send();
 
                // 执行成功提交事务
                DB::commit();
                Log::info('【报名班制】支付日志和订单状态更新成功');
            } catch (PDOException $ex) {
                // 执行失败回滚事务
                DB::rollback();
                Log::info('【报名班制】支付日志和订单状态更新失败');
                throw new InvalidArgumentException('【报名班制订单】支付日志或订单状态更新出错，回滚事务', 400);
            } catch (\Exception $exception) {
                Log::error($exception->getMessage());
            }
        }
       
        Log::info('biz over');
    }

    /**
     * 预约计时业务的付款后的处理
     */
    public function appointBiz()
    {
        Log::info('biz begin');
        Log::info(PHP_EOL.json_encode(
            $this->request->input(),
            JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        ));
         // {
        //     "title": "嘻哈学车-报名班制",
        //     "desc": "报名班制订单支付",
        //     "attach_params": "{\"biz\":\"signup\"}",
        //     "order_id": "2017101954505151",
        //     "trade_id": "2017101921001004520265569906",
        //     "amount": "0.01",
        //     "pay_amount": "0.01",
        //     "trade_status": "TRADE_SUCCESS",  //SUCCESS
        //     "trade_create": "2017-10-19 09:09:55",
        //     "trade_payment": "2017-10-19 09:10:02",
        //     "pay_user_id": "2088702589300527",
        //     "pay_user_logon_id": "",
        //     "app_id": "2015082600234249",
        //     "gateway": "Alipay"
        // }
        $pay_info = $this->request->input();
        $log = DB::table('pay_log')->select()->where(['trade_id'=>$pay_info['trade_id']])->get()->first();
        if(isset($pay_info) && isset($log)) {
            // 开启事务
            DB::beginTransaction();
            try {
                $_update = [];
                $push_info = new \StdClass;
                $i_status = '';
                if($pay_info['gateway'] == 'Alipay') {
                    $pay_info['pay_type'] = 1;
                }else if($pay_info['gateway'] == 'Wechatpay') {
                    $pay_info['pay_type'] = 3;
                }else if($pay_info['gateway'] == 'Unionpay') {
                    $pay_info['pay_type'] = 4;
                }
                if($pay_info['trade_status'] == 'TRADE_SUCCESS' || $pay_info['trade_status'] == 'SUCCESS') {
                    $i_status = 1;
                }
                // 支付方式 1.支付宝  2.微信  3.银联
                if (isset($pay_info['pay_type'])) {
                    $_update['study_orders.deal_type'] = $pay_info['pay_type'];
                }
                // 支付宝交易号
                if (isset($pay_info['trade_id'])) {
                    $_update['study_orders.s_zhifu_dm'] = $pay_info['trade_id'];
                }
                // 支付状态
                if (isset($pay_info['trade_status'])) {
                    $_update['study_orders.i_status'] = $i_status;
                }
                // 支付时间
                if (isset($pay_info['trade_payment'])) {
                    $_update['study_orders.dt_zhifu_time'] = $pay_info['trade_payment'];
                }
                // 实际支付金额
                if(isset($pay_info['pay_amount'])) {
                    $_update['study_orders.dc_money'] = $pay_info['pay_amount'];
                }
                // 更新订单状态
                DB::table('study_orders')->where([['study_orders.l_study_order_id', '=', $pay_info['order_id']],])->update($_update);
                /**
                 * 从redis中获取推送内容 appoint_order:{$order_no}
                 */
                // begin of redis push
                $pay_info['order_no'] = $pay_info['order_id'];
                if (isset($pay_info['order_no'])) {
                    try
                    {
                        $rdata = $this->redis->get('appoint_order:'.$pay_info['order_no']);
                        if ( $rdata )
                        {
                            $order_info = json_decode( $rdata );
                            $order_info = json_decode( $order_info->content );

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
                            $push_info->member_id = $order_info->coach_id;
                            $push_info->member_type = 2;
                            $push_info->beizhu = '预约计时';
                            $push_info->from = '嘻哈学车';
                            $this->redis->rpush('msg_send_queue', json_encode($push_info, JSON_UNESCAPED_UNICODE));

                            if ($order_info->school_id == 5375) { // 梧州万达驾校
                                // push to 15078112982 娜娜 whose user_id is 5045
                                $push_info->product = 'student';
                                $push_info->target = 5045;
                                $push_info->content = '【新的预约】'.$student_content;
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
                                $push_info->content = '【新的预约】'.$student_content;
                                $push_info->type = 2;
                                $push_info->member_id = 22014;
                                $push_info->member_type = 1;
                                $push_info->beizhu = '预约计时';
                                $push_info->from = '嘻哈学车';
                                $this->redis->rpush('msg_send_queue', json_encode($push_info, JSON_UNESCAPED_UNICODE));
                            }

                            $this->redis->del('appoint_order:'.$pay_info['order_no']);
                        }
                    }
                    catch ( Exception $e )
                    {
                        //
                        Log::error('err happen:'.$e->getMessage().' at line '.$e->getLine());
                    }
                }
                // end of redis push
                // 执行成功提交事务
                DB::commit();
            } catch (PDOException $ex) {
                // 执行失败回滚事务
                DB::rollback();
                throw new InvalidArgumentException('【预约计时订单】支付日志或订单状态更新出错，回滚事务', 400);
            }
        }
        Log::info('biz over');
    }
}
