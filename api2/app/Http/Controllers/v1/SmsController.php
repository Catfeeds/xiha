<?php

/**
 * 短信发送
 * 云信使 http://sms.sms.cn/
 *
 * @author gaodacheng
 * @lastModified 2017-04-21 10:42
 */

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use SmsApi as Sms;  // 云信使API
use Exception;
use Log;

class SmsController extends Controller {

    /**
     * 云信使配置前辍
     *
     * @var $config_prefix
     */
    protected $config_prefix = 'SMS_CN_';

    /**
     * 云信使账号
     *
     * @var $uid
     */
    protected $uid;

    /**
     * 云信使密码
     *
     * @var $pwd
     */
    protected $pwd;

    /**
     * 短信发送网关
     *
     * @var $_sms
     */
    protected $_sms;

    /**
     * 短信模版
     *
     * @var $template
     */
    protected $template;

    public function __construct()
    {
        $this->initialize();
    }

    /**
     * 初始化
     */
    public function initialize()
    {
        // 初始化设置账号和密码
        try
        {
            if (NULL === $uid = $this->_getConf('uid'))
            {
                throw new Exception('短信配置云信使账号未设置');
            }
            $this->setUid($uid);

            if (NULL === $pwd = $this->_getConf('pwd'))
            {
                throw new Exception('短信配置云信使密码未设置');
            }
            $this->setPwd($pwd);
        }
        catch (Exception $e)
        {
            $log = [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'msg'  => $e->getMessage(),
            ];
            Log::error('云信使初始化异常', $log);
            throw new Exception('云信使初始化异常');
        }
    }

    /**
     * 获取配置文件中的云信使的参数
     */
    protected function _getConf($field)
    {
        return env($this->config_prefix . strtoupper($field));
    }

    /**
     * 实例化一个短信发送网关
     */
    public function sms()
    {
        $this->_sms = new Sms($this->uid, $this->pwd);
        return $this;
    }

    /**
     * 向一个手机发送短信
     */
    public function send($phone, $content)
    {
        // 发送变量模版短信
        return $this->_sms->send($phone, $content, $this->template);
    }

    /**
     * 设置云信使用户名
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * 设置云信使密码
     */
    public function setPwd($pwd)
    {
        $this->pwd = $pwd;
        return $this;
    }

    /**
     * 设置短信模版
     */
    public function setTemplate($template)
    {
        $template_id = $this->_getConf('template_' . $template);
        if (NULL === $template_id)
        {
            throw new Exception('设置短信模版出错');
        }
        $this->template = $template_id;
        return $this;
    }

} /* SmsController */

?>
