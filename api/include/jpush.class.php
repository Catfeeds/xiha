<?php  
    
    // 推送消息类
    
    class Jpush {

        private $_appkeys = '';
        private $_mastersecret = '';
        private $_platform = '';
        private $_audience = '';
        private $_notification = '';
        private $_message = '';
        // private $_option = '';

        public function __construct($mastersecret='', $appkeys='', $platform='', $audience='', $notification='', $message='') {
            $this->_appkeys = $appkeys;
            $this->_mastersecret = $mastersecret;
            $this->_platform = $platform;
            $this->_audience = $audience;
            $this->_notification = $notification;
            $this->_message = $message;
            // $this->_option = $option;
        }

        public function send() {
            require_once '../vendor/autoload.php';
            $client = new JPushClient($this->_appkeys, $this->_mastersecret);
            try {
                $result = $client->push()
                    ->setPlatform($this->_platform)
                    ->setAudience($this->_audience)
                    ->setNotification($this->_notification)
                    ->setMessage($this->_message)
                    // ->setOptions($this->_option)
                    ->printJSON()
                    ->send();

                return $result;
                echo 'Push Success.' . $br;
                echo 'sendno : ' . $result->sendno . $br;
                echo 'msg_id : ' .$result->msg_id . $br;
                echo 'Response JSON : ' . $result->json . $br;
            } catch (APIRequestException $e) {

                return false;
                echo 'Push Fail.' . $br;
                echo 'Http Code : ' . $e->httpCode . $br;
                echo 'code : ' . $e->code . $br;
                echo 'message : ' . $e->message . $br;
                echo 'Response JSON : ' . $e->json . $br;
                echo 'rateLimitLimit : ' . $e->rateLimitLimit . $br;
                echo 'rateLimitRemaining : ' . $e->rateLimitRemaining . $br;
                echo 'rateLimitReset : ' . $e->rateLimitReset . $br;
            } catch (APIConnectionException $e) {
                return false;
                echo 'Push Fail: ' . $br;
                echo 'Error Message: ' . $e->getMessage() . $br;
                //response timeout means your request has probably be received by JPUsh Server,please check that whether need to be pushed again.
                echo 'IsResponseTimeout: ' . $e->isResponseTimeout . $br;
            }
        }

    }
?>
