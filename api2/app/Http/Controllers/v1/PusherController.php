<?php

namespace App\Http\Controllers\v1;

use Exception;
use InvalidArgumentException;
use Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use JPush\Client as JPush;

class PusherController extends Controller {

    private $product; // 产品类型
    private $target; // 推送目标
    private $title; // 消息标题
    private $content; // 消息内容
    private $type; // 消息类型
    private $from; // 消息来源
    private $beizhu; // 消息备注
    private $member_id; // 消息对象
    private $member_type; // 用户类型

    protected $config = [
        'student' => [
            'api_key' => 'c1b9d554f52b5668cba58c75',
            'master_secret' => '68ce861810390d1e88112310',
            'title' => '嘻哈学车-学员端',
        ],
        'coach' => [
            'api_key' => '3ebbbf7c2e811171a6e5c836',
            'master_secret' => 'b4272f005b740f30d49a6758',
            'title' => '嘻哈学车-教练端',
        ],
    ];

    public function __construct($product = 'student') {
        $this->setProduct($product);
    }

    /**
     * 设置学员端，教练端
     *
     */
    public function setProduct($product) {
        $released_products = ['student', 'coach'];
        if (is_string($product)) {
            $product = strtolower($product);
            if (in_array($product, $released_products)) {
                $this->product = $product;
                $this->setTitle($this->config[$product]['title']);
            } else {
                throw new InvalidArgumentException('消息推送设置错误');
            }
        } else {
            throw new InvalidArgumentException('消息推送设置错误');
        }
        return $this;
    }

    public function setTarget($target) {
        if (!is_string($target)) {
            $target = (string)$target;
        }
        $this->target = $target;
        return $this;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    public function setFrom($from) {
        $this->from = $from;
        return $this;
    }

    public function setBeizhu($beizhu) {
        $this->beizhu = $beizhu;
        return $this;
    }

    public function setMemberId($member_id) {
        $this->member_id = $member_id;
        return $this;
    }

    public function setMemberType($member_type) {
        $this->member_type = $member_type;
        return $this;
    }

    public function buildPusher() {

        if (!is_string($this->product) || ! in_array($this->product, ['student', 'coach'])) {
            throw new InvalidArgumentException('推送平台参数错误');
        }
        $client = new JPush($this->config[$this->product]['api_key'],$this->config[$this->product]['master_secret']);
        $pusher = $client->push()->setPlatform('all');

        if (!is_string($this->target)) {
            throw new InvalidArgumentException('推送对象参数错误');
        }
        $pusher = $pusher->addAlias($this->target);

        if (!is_string($this->content)) {
            throw new InvalidArgumentException('推送内容参数错误');
        }
        $pusher = $pusher->setNotificationAlert($this->content)
            ->androidNotification($this->content, [
                'title' => $this->title,
                'extras' => [
                    'type' => $this->type,
                ],
            ])
            ->iosNotification($this->content, [
                'title' => $this->title,
                'sound' => 'sound.caf',
                'extras' => [
                    'type' => $this->type,
                ],
            ])
            ->options([
                'apns_production' => true,
                'time_to_live' => 86400,
            ])
            ;

        return $pusher;
    }

    /**
     * 推送消息给客户端
     *
     */
    public function send() {
        try {
            $response = $this->buildPusher()->send();
            $this->save($response['body']);
            return true;
        } catch (Exception $e) {
            //
        }
    }

    /**
     * 保存推送消息至数据库
     *
     */
    public function save($sender) {
        $msg_id = DB::table('sms_sender')
            ->insertGetId([
                'dt_sender' => time(),
                'i_jpush_sendno' => $sender['sendno'],
                'i_jpush_msg_id' => $sender['msg_id'],
                's_content' => $this->content,
                's_from' => $this->from,
                's_beizhu' => $this->beizhu,
                'addtime' => time(),
                'member_id' => $this->member_id,
                'member_type' => $this->member_type,
                'i_yw_type' => $this->type,
                'is_read' => 2, // 未读消息 状态值为2
            ]);
    }

}

?>
