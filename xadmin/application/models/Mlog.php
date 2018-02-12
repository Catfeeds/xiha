<?php


class Mlog extends CI_Model{

    public $admin_tbl = 'cs_admin';
    public $action_tbl = 'cs_actions';
    public $action_log_tbl = 'cs_action_log';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function loginauth()
    {
        if(!$this->session->loginauth) {
            redirect(base_url('admin/login'), 'location', 301);
            exit();
        }
    }

    /**
     * 从登录session信息中，获取登陆者ID
     *
     * @param $login_id
     * eg:
     * $loginauth "436|xihaschool|2|2|嘻哈驾校蜀山分校|5426"
     * @login_id 436
     *
     * @return $login_id
     */
    public function getLoginUserId($loginauth)
    {
        $loginauth_arr = explode('|', $loginauth);
        if ($loginauth_arr[0]) {
            $login_id =  $loginauth_arr[0];
            return $login_id;
        }

        return NULL;

    }

    /**
     * 根据用户ID获取用户昵称
     * @param  integer $uid 用户ID
     * @return string   用户昵称
    **/
    private function getOperator($uid = 0)
    {
        $operator = $this->db
            ->from($this->admin_tbl)
            ->select('content')
            ->where('id', $uid)
            ->get()
            ->row_array();
        if ( ! empty($operator)) {
            return $operator['content'];
        }

        return NULL;
    }


    /**
     * 时间戳格式化
     * @param int $time
     * @return string 完整的时间显示
     * @author huajie <banhuajie@163.com>
    **/
    private function time_format($time = NULL,$format='Y-m-d H:i'){
        $time = $time === NULL ? time() : intval($time);
        return date($format, $time);
    }

    /**
     * 具体操作
     * @param   string $intro 行为说明 
     * @return  string $specify_intro行为说明
     * @author  wl
     * @date    Feb 21, 2017
    */
    private function specify_intro ($intro = '') {
        $intro_info = $intro;
        return $intro_info;
    }

    /**
     * 记录行为日志，并执行该行为的规则
     * @param string $action 行为标识
     * @param string $model 触发行为的模型名
     * @param int $record_id 触发行为的记录id
     * @param int $user_id 执行行为的用户id
     * @param string $intro 执行行为的描述
     * @return boolean
     * @author huajie <banhuajie@163.com>
    */
    public function action_log($action = null, $model = null, $record_id = null, $user_id = null, $intro = ''){

        if(empty($action) OR empty($model) OR empty($record_id)){
            return '参数不能为空';
        }

        // 判断行为是否执行
        $action_info = $this->getActionByName($action);
        if($action_info['status'] != 1){
            return '该行为已被禁用或删除';
        }
        
        //插入行为日志
        $data['action_id']      =   $action_info['id'];
        $data['user_id']        =   $user_id;
        $data['action_ip']      =   ip2long($this->get_client_ip());
        $data['model']          =   $model;
        $data['record_id']      =   $record_id;
        $data['create_time']    =   time();
        $data['intro']          =   $intro;
        
        // 解析日志规则,生成日志备注
        if( ! empty($action_info['log'])){
            if(preg_match_all('/\[(\S+?)\]/', $action_info['log'], $match)){
                $log['user']    =   $user_id;
                $log['record']  =   $record_id;
                $log['model']   =   $model;
                $log['time']    =   time();
                $log['intro']   =   $intro;
                $log['data']    =   array('user'=>$user_id,'model'=>$model,'record'=>$record_id,'time'=>time(),'intro' => $intro);
                foreach ($match[1] as $value){
                    $param = explode('|', $value);
                    if(isset($param[1])){
                        $replace[] = call_user_func(array('Mlog', $param[1]), $log[$param[0]]);
                    }else{
                        $replace[] = $log[$param[0]];
                    }
                }
                $data['remark'] = str_replace($match[0], $replace, $action_info['log']);
            }else{
                $data['remark'] =   $action_info['log'];
            }
        }else{
            //未定义日志规则，记录操作url
            $data['remark'] = '操作url：'.$_SERVER['REQUEST_URI'];
        }
        
        $query = $this->db
            ->insert($this->action_log_tbl, $data);
        return $this->db->insert_id();
    }

    // 根据行为唯一标识获取行为信息
    public function getActionByName($action)
    {
        $name = trim($action);
        $action_info = $this->db
            ->where('name', $name)
            ->get($this->action_tbl)
            ->row_array();
        return $action_info;
    }

    /**
     * 获取客户端IP地址
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param boolean $adv 是否进行高级模式获取（有可能被伪装） 
     * @return mixed
    **/
    function get_client_ip($type = 0,$adv=false) 
    {
        $type = $type ? 1 : 0;
        static $ip = NULL;

        if ($ip !== NULL) return $ip[$type];

        if($adv)
        {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) 
            {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown',$arr);
                if(false !== $pos) unset($arr[$pos]);
                $ip = trim($arr[0]);
            }
            elseif (isset($_SERVER['HTTP_CLIENT_IP'])) 
            {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
            elseif (isset($_SERVER['REMOTE_ADDR'])) 
            {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        }
        elseif (isset($_SERVER['REMOTE_ADDR'])) 
        {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        // IP地址合法验证
        $long = sprintf("%u",ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }














}