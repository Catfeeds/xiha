<?php

namespace Xiha\Pay\Method;

interface MethodInterface
{
    public function purchase(array $data);
    public function notify(array $data);
    public function refund(array $data);
    public function query(array $data);
    public function refundQuery(array $data);
    public function cancel(array $data);
    public function close(array $data);
    public function transfer(array $data);
    public function transferQuery(array $data);
}
