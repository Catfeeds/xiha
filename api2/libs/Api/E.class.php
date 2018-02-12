<?php

class E extends \Exception
{
    private $errors = [
        1000 => '云端执行成功',
        1001 => '云端参数无效',
        1002 => '系统忙',

        2001 => 'token失效，请重新登录',
    ];

    private $extra;

    public function __construct($code = 9999, $extra = '')
    {
        parent::__construct('', $code);
        $this->extra = $extra;
    }

    public function getMsg()
    {
        return isset($this->errors[$this->code]) ? $this->errors[$this->code] : '';
    }

    public function getExtra()
    {
        if (is_array($this->extra)) {
            return $this->extra;
        }
        $extra = json_decode($this->extra);
        if ($extra) {
            return $extra;
        }

        return $this->extra;
    }

}