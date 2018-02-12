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
    $db = getConnection();
    $request = $app->request();
    $member_id = $request->params('member_id');
    $member_type = $request->params('member_type');
    $page = $request->params('page');

    if($member_type == '' || $member_type == '') {
        $data = array('code'=>-1, 'data'=>'参数错误');
        echo json_encode($data);
        exit();
    }

    $page = isset($page) ? $page : 1;
    $page = !empty($page) ? $page : 1;

    $limit = 10;
    $start = ($page-1) * $limit;

    $sql = "SELECT * FROM `cs_sms_sender` WHERE `member_id` = $member_id AND `member_type` = $member_type AND `i_yw_type` = 1 AND `is_read` != 101 ORDER BY `addtime` DESC ";
    $stmt = $db->query($sql);
    $sender_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($sender_info as $key => $value) {
        $sender_info[$key]['addtime'] = date('Y-m-d H:i', $value['addtime']);
        $sender_info[$key]['dt_sender'] = date('Y-m-d H:i', $value['dt_sender']);
    }
    $data = array('code'=>200, 'data'=>$sender_info);
    echo json_encode($data);
    exit();
}
