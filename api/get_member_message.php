<?php  
/**
 * 获取成员（教练和学员）的推送消息
 * @param $member_id int 成员ID
 * @param $member_type 成员类型 
 * @param $page 页码 
 * @return string AES对称加密（加密字段xhxueche）
 * @author chenxi
 **/

require 'Slim/Slim.php';
require 'include/common.php';
require 'include/crypt.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
$app->post('/','getMsg');
$app->run();

function getMsg() {
    Global $app, $crypt;
    $request = $app->request();
    $member_id = $request->params('member_id');
    $member_type = $request->params('member_type'); // 1：学员 2：教练
    // $page = $request->params('page');

    if($member_type == '' || $member_type == '') {
        $data = array('code'=>-1, 'data'=>'参数错误');
        echo json_encode($data);
        exit();
    }

    // $page = isset($page) ? $page : 1;
    // $page = !empty($page) ? $page : 1;

    // $limit = 10;
    // $start = ($page-1) * $limit;

    try {
        $db = getConnection();

        $list = array();
        // 获取未读通知类消息
        $sql = "SELECT * FROM `cs_sms_sender` WHERE `member_id` = $member_id AND `member_type` = $member_type AND `is_read` = 2 AND `i_yw_type` = 1 ORDER BY `addtime` DESC";
        $stmt = $db->query($sql);
        $sender_info = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 获取未读消息数量
        $list['unread_notice_num'] = count($sender_info);
        // 获取最新消息
        $sql = "SELECT * FROM `cs_sms_sender` WHERE `member_id` = $member_id AND `member_type` = $member_type AND `is_read` = 1 AND `i_yw_type` = 1 ORDER BY `addtime` DESC LIMIT 0,1";
        $stmt = $db->query($sql);
        $new_sender_info = $stmt->fetch(PDO::FETCH_ASSOC);
        if($new_sender_info) {
            $list['notice_content'] = $new_sender_info['s_content'];
            $list['notice_time'] = date('Y-m-d H:i', $new_sender_info['dt_sender']);
        } else {
            $list['notice_content'] = '';
            $list['notice_time'] = '';
        }

        // 获取已读消息数量
        $sql = "SELECT * FROM `cs_sms_sender` WHERE `member_id` = $member_id AND `member_type` = $member_type AND `is_read` = 1 AND `i_yw_type` = 1 ORDER BY `addtime` DESC";
        $stmt = $db->query($sql);
        $already_sender_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $list['read_notice_num'] = count($already_sender_info);

        // 获取正常订单消息
        $sql = "SELECT * FROM `cs_sms_sender` WHERE `member_id` = $member_id AND `member_type` = $member_type AND `is_read` != 101 AND `i_yw_type` = 2 ORDER BY `addtime` DESC";
        $stmt = $db->query($sql);
        $sms_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($sms_list) {
            foreach ($sms_list as $key => $value) {
                $sms_list[$key]['addtime'] = date('Y-m-d H:i', $value['addtime']);
                $sms_list[$key]['dt_sender'] = date('Y-m-d H:i', $value['dt_sender']);

            }
        }
        $list['systemm_message'] = $sms_list;
        $data = array('code'=>200, 'data'=>$list);
        echo json_encode($data);

    } catch (PDOException $e) {
        setapilog('get_member_message:params[member_id:'.$member_id.',member_type:'.$member_type.',page:'.$page.'], error:'.$e->getMessage());
        $data = array('code'=>1, 'data'=>'网络错误');
        echo json_encode($data);
    }

}

?>

