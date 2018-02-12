<?php

namespace App\Http\Controllers\v1;

use Exception;
use InvalidArgumentException;
use Log;
use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\WechatpayController; // pay_type = 3
use App\Http\Controllers\v1\AlipayController; // pay_type = 1
use App\Http\Controllers\v1\UnionpayController; // pay_type = 4
use App\Http\Controllers\v1\PusherController;
use App\Http\Controllers\v1\OrderController;
use App\Http\Controllers\v1\SmsController as Sms; // 短信
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;

class PayController extends Controller {

    /**
     * 支付网关
     * @var $pay
     */
    protected $pay;

    /**
     * 支付类型：支付宝(1)，微信(3)，银联(4)
     * @var $pay_type
     */
    protected $pay_type;

    public function __construct ($pay_type = 3) {
        $this->pay_type = $pay_type;
        $this->initialize();
        parent::__construct();
    }

    /**
     * 初始化
     *
     */
    private function initialize () {
        switch ($this->pay_type) {
        case 1:
            $this->pay = new AlipayController();
            break;
        case 3:
            $this->pay = new WechatpayController();
            break;
        case 4:
            $this->pay = new UnionpayController();
            break;
        default :
            $this->pay = new WechatpayController();
            break;
        }
    }

    /**
     * 支付订单
     *
     */
    public function purchase ($order_info) {
        return $this->pay->purchase($order_info);
    }

    /**
     * 查询订单
     *
     */
    public function query ($order_info) {
        return $this->pay->query($order_info);
    }

    /**
     * 关闭订单
     */
    public function close ($order_info) {
        return $this->pay->close($order_info);
    }

    /**
     * 退款
     */
    public function refund ($order_info) {
        return $this->pay->refund($order_info);
    }

    /**
     * 退款查询
     */
    public function queryRefund ($order_info) {
        return $this->pay->queryRefund($order_info);
    }

    /**
     * 提现
     *
     * @param array $param
     */
    public function transfer(array $param = [])
    {
        return $this->pay->transfer($param);
    }

    /**
     * 提现查询
     *
     * @param array $param
     */
    public function queryTransfer(array $param = [])
    {
        return $this->pay->queryTransfer($param);
    }

    /**
     * 对账单下载
     */
    public function billdownload(array $param = [])
    {
        return $this->pay->billdownload($param);
    }

    /**
     * 支付完成
     *
     * @param Array $pay_info
     */
    public function complete (array $pay_info = []) {
        $push_info = new \StdClass;
        switch ($pay_info['order_type']) {
        case 'appoint':
            $_update = [];
            if (isset($pay_info['pay_type'])) {
                $_update['study_orders.deal_type'] = $pay_info['pay_type'];
            }
            if (isset($pay_info['transaction_no'])) {
                $_update['study_orders.s_zhifu_dm'] = $pay_info['transaction_no'];
            }
            if (isset($pay_info['trade_status'])) {
                $_update['study_orders.i_status'] = $pay_info['trade_status'];
            }
            if (isset($pay_info['pay_time'])) {
                $_update['dt_zhifu_time'] = $pay_info['pay_time'];
            }
            DB::table('study_orders')
                ->where([
                    ['study_orders.l_study_order_id', '=', $pay_info['order_id']],
                ])
                ->update($_update);
            /**
             * 从redis中获取推送内容 appoint_order:{$order_no}
             */
            // begin of redis push
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
            break;
        case 'signup':
            $_update = [];
            if (isset($pay_info['pay_type'])) {
                $_update['school_orders.so_pay_type'] = $pay_info['pay_type'];
            }
            if (isset($pay_info['transaction_no'])) {
                $_update['school_orders.s_zhifu_dm'] = $pay_info['transaction_no'];
            }
            if (isset($pay_info['trade_status'])) {
                $_update['school_orders.so_order_status'] = $pay_info['trade_status'];
            }
            if (isset($pay_info['pay_time'])) {
                $_update['dt_zhifu_time'] = $pay_info['pay_time'];
            }
            DB::table('school_orders')
                ->where([
                    ['school_orders.id', '=', $pay_info['order_id']],
                ])
                ->update($_update);
            $pusher = new PusherController('student');
            $pusher->setTarget($pay_info['user_id'])
                ->setContent('【报名班制订单】您的报名班制订单状态有变动。')
                ->setType(2)
                ->setMemberId($pay_info['user_id'])
                ->setMemberType(1)
                ->setBeizhu('报名班制')
                ->setFrom('嘻哈学车')
                ->send();

            try
            {
                $order_info = (new OrderController(new Request))->getSignupOrderInfo(['order_id' => $pay_info['order_id']]);
                if (isset($order_info->shift_name) && isset($order_info->user_phone) && isset($order_info->order_no))
                {
                    $content = [
                        'order_no' => $order_info->order_no,
                        'shift_name' => $order_info->shift_name,
                    ];
                    $phone = $order_info->user_phone;
                    $sms = (new Sms())
                        ->sms()
                        ->setTemplate('student_signup_shift')
                        ->send($phone, $content);
                }
                else
                {
                    Log::error('没有要发送短信的对象', ['data' => $order_info]);
                }
            }
            catch (Exception $e)
            {
                $_data = [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'msg'  => $e->getMessage(),
                ];
                Log::error('短信发送异常', ['error' => $_data]);
            }
            break;
        default :
            break;
        }
    }

    /**
     * 支付失败
     *
     * @param Array $pay_info
     */
    public function fail ($pay_info) {
        Log::Info('complete fail:'.json_encode($pay_info));
    }

}

?>
