<?php

namespace Xiha\Qrcode\DataTypes;

class User implements DataTypeInterface
{
    /**
     * The prefix of the QrCode.
     *
     * @var string
     */
    private $prefix = 'XHUSER';

    /**
     * The separator between the variables.
     *
     * @var string
     */
    private $separator = ',';

    /**
     * $phone
     *
     * @var string
     */
    private $phone;

    /**
     * $identity_id
     *
     * @var string
     */
    private $identity_id;

    /**
     * $user_id
     *
     * @var int
     */
    private $user_id;

    /**
     * $user_name
     *
     * @var string
     */
    private $user_name;

    /**
     * $photo_id
     *
     * @var int
     */
    private $photo_id;

    /**
     * 从给定用户信息数组中创建一个学员数据类型
     */
    public function create(array $arguments)
    {
        $this->setProperties($arguments);
    }

    /**
     * 返回正确的学员二维码
     */
    public function __toString()
    {
        return $this->buildUserString();
    }

    /**
     * 设置字段
     */
    protected function setProperties(array $arguments)
    {
        if (isset($arguments['phone'])) {
            $this->phone = $arguments['phone'];
        }
        if (isset($arguments['identity_id'])) {
            $this->identity_id = $arguments['identity_id'];
        }
        if (isset($arguments['user_id'])) {
            $this->user_id = $arguments['user_id'];
        }
        if (isset($arguments['user_name'])) {
            $this->user_name = $arguments['user_name'];
        }
        if (isset($arguments['photo_id'])) {
            $this->photo_id = $arguments['photo_id'];
        }
    }

    /**
     * 构建学员二维码
     */
    protected function buildUserString()
    {
        $user = $this->prefix;

        if ($this->phone == null) {
            $user .= "{$this->separator}"."";
        } else {
            $user .= "{$this->separator}"."{$this->phone}";
        }
        if ($this->identity_id == null) {
            $user .= "{$this->separator}"."";
        } else {
            $user .= "{$this->separator}"."{$this->identity_id}";
        }
        if ($this->user_id == null) {
            $user .= "{$this->separator}"."";
        } else {
            $user .= "{$this->separator}"."{$this->user_id}";
        }
        if ($this->user_name == null) {
            $user .= "{$this->separator}"."";
        } else {
            $user .= "{$this->separator}"."{$this->user_name}";
        }
        if ($this->photo_id == null) {
            $user .= "{$this->separator}"."";
        } else {
            $user .= "{$this->separator}"."{$this->photo_id}";
        }

        return $user;
    }
}
