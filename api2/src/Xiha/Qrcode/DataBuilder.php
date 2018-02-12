<?php

namespace Xiha\Qrcode;

use Xiha\Qrcode\DataTypes\User;
use Xiha\Qrcode\DataTypes\Coach;

class DataBuilder
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $data;

    /**
     * create DataBuilder from $type and $data
     */
    public function fromTypeAndData($type, $data)
    {
        $this->type = $type;
        $this->data = $data;

        return $this;
    }

    public function build()
    {
        $dataType = $this->createClass($this->type);
        $dataType->create($this->data);

        return strval($dataType);
    }

    public function getType()
    {
        return $this->type;
    }

    public function getData()
    {
        return $this->data;
    }

    protected function createClass($type)
    {
        $class = $this->formatClass($type);

        if (! class_exists($class)) {
            throw new \Exception("class:{$class} not exist");
        }

        return new $class();
    }

    protected function formatClass($type)
    {
        $type = ucfirst($type);

        $class = "Xiha\Qrcode\DataTypes\\".$type;

        return $class;
    }
}
