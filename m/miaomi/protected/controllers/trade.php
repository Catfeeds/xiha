<?php

class TradeController extends Controller{

    private $_alipay_config;
    private $_wxpay_config;

    public function __construct()
    {
        $this->model = new Model();
        $this->_alipay_config = array();
        $this->_wxpay_config = array();
        require_once(TINY_ROOT . "extend/Alipay/config.php");
        $this->config = $config;
        require_once(TINY_ROOT . "extend/Alipay/pagepay/service/AlipayTradeService.php");
        require_once(TINY_ROOT . "extend/Alipay/pagepay/buildermodel/AlipayTradeRefundContentBuilder.php");
        require_once(TINY_ROOT . "extend/Alipay/pagepay/buildermodel/AlipayTradeQueryContentBuilder.php");
    }

    //  生成唯一商户订单号
    public function create_trade_no()
    {
        $i = rand(0, 99999999);
        do{
            if (99999999 == $i) {
                $i = 0;
            }
            ++$i;
            $no = date("Ymd") . str_pad($i, 8, "0", STR_PAD_LEFT);
            $order_no =  $this->model->table("payment_log")->where("trade_no={$no}")->find();
        } while ($order_no);
        return $no;
    }


	/**
	 * [验证支付接口]
	 * @param  [type] $code    [支付方式]
	 * @return [object]        [支付宝接口请求提交类]
	 */
	public function loadPayment($code)
    {
        static $_PayApiObj = array();
        if(!is_object($_PayApiObj)){
            if($code = 'alipay') {
                require_once(TINY_ROOT . 'extend/'.ucfirst($code)."/{$code}.php");
                $alipay_config = $this->_alipay_config;
                $alipay_config['return_url'] = 'http://www.miaomimouse.com/index.php?con=trade&act=alipay_return_url';
                $alipay_config['notify_url'] = 'http://www.miaomimouse.com/index.php?con=trade&act=alipay_notify_url';
                $_PayApiObj = new AlipaySubmit($alipay_config);
            }else if($code = 'wxpay') {
                $wxpay_config = $this->_wxpay_config;
                $wxpay_cofing['return_url'] = 'http://www.miaomimouse.com/index.php?con=trade&act=wxpay_return_url';
                $wxpay_cofing['return_url'] = 'http://www.miaomimouse.com/index.php?con=trade&act=wxpay_return_url';
                $_PayApiObj = new Wxpay($wxpay_config);
            }else {
            	echo '非法的接口请求';
            }
        }
        return $_PayApiObj;
    }

    //  支付宝支付接口
    public function pay()
    {
        $code = Req::get('pay_code');
        $order_id = Req::get('order_id');
        $order = $this->model->table("order")->where("id={$order_id}")->find();
        if($order['pay_status'] == 1){
            return false;
        }else if($order['status'] == 2){
            return false;
        }
        if(!$total_amount = $order['order_amount']){
            return false;
        }
        //  校验支付日志
        $log = $this->model->table("payment_log")->where("order_id=$order_id")->find();
        if(!$log){
            $log = array('user_id'=>$order['user_id'],'type'=>'order','order_id'=>$order['id'],'payment'=>$code,'money'=>$total_amount);
            $log['trade_no'] = $this->create_trade_no();
            $log['amount'] = $total_amount;
            $log['create_time'] = date('Y-m-d H:i:s');
            if(!$log_id = $this->model->table("payment_log")->data($log)->insert()){
                return false;
            }
        }else if($log['payed'] == 1){
            return false;
        }
        $data = array();
        $out_trade_no = $log['trade_no'];
        $subject = '喵咪鼠标';
        $body = $order['addr'].'('.$order['accept_name'].','.$order['mobile'].')';
        $config = $this->config;
        require_once(TINY_ROOT . 'extend/Alipay/pagepay/pagepay.php');
    }



    //  支付宝订单交易查询接口
    public function alipay_trade_query($out_trade_no, $trade_no)
    {
        $out_trade_no = trim($out_trade_no);
        $trade_no = trim($trade_no);
        $RequestBuilder = new AlipayTradeQueryContentBuilder();
        $RequestBuilder->setOutTradeNo($out_trade_no);
        $RequestBuilder->setTradeNo($trade_no);
        $aop = new AlipayTradeService($this->config);
        $response = $aop->Query($RequestBuilder);
        return $response;
    }

    //  支付宝退款接口
    public function alipay_trade_refund()
    {
        if(!$order_id = (int)Req::post('order_id')) {
            $info = array('status'=>0, 'msg'=>'订单号不存在');
        }else if(!$order = $this->model->table("order")->where("id={$order_id}")->find()) {
            $info = array('status'=>0, 'msg'=>'订单不存在');
        }else if(!$payment_log = $this->model->table('payment_log')->where("order_id={$order['id']}")->find()) {
            $info = array('status'=>0, 'msg'=>'订单支付日志不存在');
        }else if(!$query = $this->alipay_trade_query($payment_log['trade_no'], $payment_log['pay_trade_no'])){
            $info = array('status'=>0, 'msg'=>'支付宝订单不存在');
        }else if($query->code != 10000){
            $info = array('status'=>0, 'msg'=>$query->msg);
        }else if($query->total_amount != (float)$payment_log['amount']){
            $info = array('status'=>0, 'msg'=>'订单金额不正确');
        }else if($query->total_amount != (float)$order['order_amount']){
            $info = array('status'=>0, 'msg'=>'订单金额不正确');
        }else if($query->trade_status !== 'TRADE_SUCCESS'){
            $info = array('status'=>0, 'msg'=>'支付宝订单状态不正确');
        }else {
            //  订单查询正常进入退款流程
            $sql = "UPDATE `tiny_order` SET `pay_status`='2' WHERE `id`='{$order_id}'"; //申请退款
            $this->model->query($sql);
            $out_trade_no = trim($query->out_trade_no);
            $trade_no = trim($query->trade_no);
            $refund_amount = trim((float)$query->total_amount);
            $out_request_no = $out_trade_no;
            $refund_reason = Req::post('refund_reason');
            //构造参数
            $RequestBuilder=new AlipayTradeRefundContentBuilder();
            $RequestBuilder->setOutTradeNo($out_trade_no);
            $RequestBuilder->setTradeNo($trade_no);
            $RequestBuilder->setRefundAmount($refund_amount);
            $RequestBuilder->setOutRequestNo($out_request_no);
            $RequestBuilder->setRefundReason($refund_reason);
            $aop = new AlipayTradeService($this->config);
            $response = $aop->Refund($RequestBuilder);

            $flag = false;
            if($response->code == 10000 && $response->fund_change == 'Y' && $response->refund_fee == $refund_amount) {
                $this->model->query('SET AUTOCOMMIT=0');
                //  退款成功更新订单相关状态
                $sql1 = "UPDATE `tiny_order` SET `pay_status`='3' WHERE `id`='{$order_id}'";
                $rlt1 = $this->model->query($sql1);
                if($rlt1) {
                    $create_time = date('Y-m-d H:i:s');
                    $sql2 = "INSERT INTO `tiny_payment_log` (`payment`, `trade_no`, `order_id`, `amount`,`pay_trade_no`,`create_time`) VALUES ('alipay', '{$out_trade_no}', '{$order_id}', '-{$refund_amount}','{$payment_log['pay_trade_no']}', '{$create_time}')";
                    $rlt2 = $this->model->query($sql2);
                    if($rlt2) {
                        $flag = true;
                    }
                }
                if($flag) {
                    //  事务提交
                    $this->model->query('COMMIT');
                    $info = array('status'=>1, 'msg'=>'退款更新订单状态成功');
                }else {
                    //  事务回滚
                    $this->model->query('ROLLBACK');
                    $info = array('status'=>0, 'msg'=>'退款更新订单状态失败');
                }
            }else {
                $info = array('status'=>0, 'msg'=>$response->msg);
            }
        }
        echo JSON::encode($info);
    }

     /**
     * 支付宝接口事务处理
     * @return [type] [description]
     */
     public function alipay_affair($verify_result=array(),$alipay_config=array())
     {
        $flag = false;
        //判断该笔订单是否在商户网站中已经做过处理
        $log = $this->model->table("payment_log")->where("trade_no={$verify_result['out_trade_no']}")->find();
        if($log) {
            //  设置事务为手动提交
            $this->model->query('SET AUTOCOMMIT=0');
            //请务必判断请求时的total_amount、seller_id与通知时获取的total_amount、seller_id为一致的
            if($log['payed'] == 0) {
                if($log['amount'] == $verify_result['total_amount'] && $alipay_config['seller_id'] == $verify_result['seller_id']) {
                    //  更新支付日志支付状态、订单状态
                    $payedtime = time();
                    $pay_trade_no = $verify_result['trade_no'];
                    $sql1 = "UPDATE `tiny_payment_log` SET `payed`='1',`payedtime`='{$payedtime}', `pay_trade_no`='{$pay_trade_no}' WHERE `log_id`='{$log['log_id']}'";
                    $rlt1 = $this->model->query($sql1);
                    if($rlt1) {
                        $sql2 = "UPDATE `tiny_order` SET `pay_status`='1',`status`='1' WHERE `id`='{$log['order_id']}'";
                        $rlt2 = $this->model->query($sql2);
                        if($rlt2) {
                            $flag = true;
                        }
                    }
                }
            }
        }
        if($flag) {
            //  事务提交
            $this->model->query('COMMIT');
            $this->redirect('http://www.miaomimouse.com');
        }else {
            //  事务回滚
            $this->model->query('ROLLBACK');
            echo '更新支付状态出错';
        }
     }

     /**
     *  支付宝页面跳转同步通知页面
     */
    public function alipay_return_url()
    {
        $arr = $_GET;
        if(isset($arr['con'])) {
            unset($arr['con']);
        }
        if(isset($arr['act'])) {
            unset($arr['act']);
        }
        $alipaySevice = new AlipayTradeService($this->config);
        $result = $alipaySevice->check($arr);
        if($result) {
            $out_trade_no = htmlspecialchars($_GET['out_trade_no']);
            $trade_no = htmlspecialchars($_GET['trade_no']);
            $seller_id = htmlspecialchars($_GET['seller_id']);
            $total_amount = htmlspecialchars($_GET['total_amount']);
            $verify_result = array('out_trade_no'=>$out_trade_no, 'trade_no'=>$trade_no, 'seller_id'=>$seller_id,'total_amount'=>$total_amount);
            $this->alipay_affair($verify_result, $this->config);
            echo "同步通知验证成功<br />";
        }
        else {
            //验证失败
            echo "同步通知验证失败";
        }
    }
}