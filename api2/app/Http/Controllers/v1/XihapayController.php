<?php

/**
 * 嘻哈钱包
 */

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller; // 基类
use App\Http\Controllers\v1\PayController; // 基类
use Illuminate\Http\Request; // HTTP请求
use Exception;
use Log;

class XihapayController extends Controller {

    /**
     * @var Request
     */
    protected $request;

    /**
     * 构造方法
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * 提现接口
     */
    public function promotionTransfer()
    {
        // 参数获取
        try
        {
            // 账户类型
            if ( ! $this->request->has('type'))
            {
                throw new Exception('提现账户类型未设置');
            }
            if ( ! in_array($type = $this->request->input('type'), [1, 3, 4]))
            {
                throw new Exception('设置的提现账户类型不支持');
            }

            // 账户
            if ( ! $this->request->has('account'))
            {
                throw new Exception('提现账户未设置');
            }
            $account = $this->request->input('account');

            // 金额
            if ( ! $this->request->has('money'))
            {
                throw new Exception('提现金额未设置');
            }
            if ( ($money = (float)$this->request->input('money')) <= 0)
            {
                throw new Exception('此提现金额不允许');
            }

            // 单号
            if ( ! $this->request->has('order_no'))
            {
                throw new Exception('转账单号未设置');
            }
            $order_no = $this->request->input('order_no');
        }
        catch (Exception $e)
        {
            Log::info('请求参数错误', ['data' => ['err_msg' => $e->getMessage(), 'args' => $this->request->input()]]);
            return response()->json(['code' => 400, 'msg' => $e->getMessage(), 'data' => new \stdClass]);
        }

        $_pay = new PayController($type);
        $_param = ['money' => $money, 'account' => $account, 'order_no' => $order_no];
        $transfer_result = $_pay->transfer($_param);
        return response()->json(['code' => 200, 'msg' => 'ok', 'data' => ['transfer' => $transfer_result]]);
    }

    /**
     * 提现查询接口
     */
    public function queryTransfer()
    {
        // 参数获取
        try
        {
            // 账户类型
            if ( ! $this->request->has('type'))
            {
                throw new Exception('账户类型未设置');
            }
            if ( ! in_array($type = $this->request->input('type'), [1, 3, 4]))
            {
                throw new Exception('设置的账户类型不支持');
            }


            // 转账单号
            if ( ! $this->request->has('order_no'))
            {
                throw new Exception('转账单号未设置');
            }
            $order_no = $this->request->input('order_no');

            if ($this->request->has('transaction_no'))
            {
                $transaction_no = $this->request->input('transaction_no');
            }
        }
        catch (Exception $e)
        {
            Log::info('请求参数错误', ['data' => ['err_msg' => $e->getMessage(), 'args' => $this->request->input()]]);
            return response()->json(['code' => 400, 'msg' => $e->getMessage(), 'data' => new \stdClass]);
        }

        $_pay = new PayController($type);
        $_param = ['order_no' => $order_no];
        if (isset($transaction_no))
        {
            $_param['transaction_no'] = $transaction_no;
        }
        $result = $_pay->queryTransfer($_param);
        return response()->json(['code' => 200, 'msg' => 'ok', 'data' => ['query_transfer' => $result]]);
    }

    /**
     * 对账单下载接口
     */
    public function billDownload()
    {
        // 参数获取
        try
        {
            // 账户类型
            if ( ! $this->request->has('type'))
            {
                throw new Exception('账户类型未设置');
            }
            if ( ! in_array($type = $this->request->input('type'), [1, 3, 4]))
            {
                throw new Exception('设置的账户类型不支持');
            }

            // 账单日期 需要下载的账单日期，最晚是当期日期的前一天
            if ( ! $this->request->has('date'))
            {
                throw new Exception('账单日期未设置');
            }
            $date = $this->request->input('date');
        }
        catch (Exception $e)
        {
            Log::info('请求参数错误', ['data' => ['err_msg' => $e->getMessage(), 'args' => $this->request->input()]]);
            return response()->json(['code' => 400, 'msg' => $e->getMessage(), 'data' => new \stdClass]);
        }

        $_pay = new PayController($type);
        $_param = ['date' => $date];
        $result = $_pay->billdownload($_param);
        return response()->json(['code' => 200, 'msg' => 'ok', 'data' => ['bill_download' => $result]]);
    }

}
