<?php
/**
 * 定时任务：当10小时或其它长度的时间预约学车完成，切换成计时班，按正常收费
 * @param    type            $var    comment
 * @return   json
 * @package  /api
 * @author   gdc
 * @date     Sep 25, 2016
 **/

require 'Slim/Slim.php';
require 'include/common.php';
require 'include/crypt.php';
require 'include/functions.php';

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
$app->any('/', 'funcname');
$app->run();

function funcname() {
    global $app, $crypt;

    $req = $app->request();
    $res = $app->response();

    /*
    //验证请求方式 POST
    if ( !$req->isPost() ) {
        slimLog($req, $res, null, '需要POST');
        ajaxReturn(array('code' => 106, 'data' => '请求错误'));
    }
    */

    /*
    //取得参数列表
    $validate_ok = validate(array('user_id' => 'INT'), $req->params());
    if ( !$validate_ok['pass'] ) {
        slimLog($req, $res, null, '参数不完整或参数类型不对，请核对文档与您传的参数');
        ajaxReturn($validate_ok['data']);
    }
    */

    // shift and learn-hour config
    $config = array(
        // 嘻哈体验驾校，超级计时班，免费预约学车3学时
        array('old_shift' => 626, 'new_shift' => 627, 'learn_hour' => 3), 
        //鸿景驾校C2计时班
        array('old_shift' => 624, 'new_shift' => 625, 'learn_hour' => 10), 
        //鸿景驾校计时班1
        array('old_shift' => 585, 'new_shift' => 597, 'learn_hour' => 10), 
        //鸿景驾校C2计时班(2)
        array('old_shift' => 632, 'new_shift' => 631, 'learn_hour' => 10), 
        //鸿景驾校C1计时班
        array('old_shift' => 628, 'new_shift' => 621, 'learn_hour' => 10), 
    );

    //ready to return
    $data = array();
    try {
        // Open connection with mysql
        $db = getConnection();

        $count = 0;
        $expire_order_list = array();
        if (empty($config) or count($config) <= 0) {
            $data = array('code' => 200, 'data' => 'no changes happened');
        } else {
            $sorder_tbl = DBPREFIX.'school_orders'; // 报名驾校
            $aorder_tbl = DBPREFIX.'study_orders';  // 预约学车
            $user_tbl = DBPREFIX.'user';
            $shift_tbl = DBPREFIX.'school_shifts';
            foreach ($config as $index => $value) {
                $sql = " SELECT o.`so_shifts_id`, shift.`sh_type` AS shift_type, shift.`sh_title` AS shift_title, shift.`sh_money` AS shift_money, o.`so_order_no` AS order_no, o.`so_phone`, u.`s_real_name` AS real_name, o.`so_user_id` AS user_id, SUM(a.`i_service_time`) AS learn_hour FROM `{$sorder_tbl}` AS o LEFT JOIN `{$user_tbl}` AS u ON o.`so_user_id` = u.`l_user_id` LEFT JOIN `{$aorder_tbl}` AS a ON o.`so_user_id` = a.`l_user_id` LEFT JOIN `{$shift_tbl}` AS shift ON o.`so_shifts_id` = shift.`id` WHERE ((o.`so_pay_type` = 2 AND o.`so_order_status` = 3) OR ( (o.`so_pay_type` = 1 OR o.`so_pay_type` = 3 OR o.`so_pay_type` = 4) AND o.`so_order_status` = 1)) AND o.`so_shifts_id` = :shid AND u.`i_status` = 0 AND (a.`i_status` = 1 OR a.`i_status` = 2 OR a.`i_status` = 1003) AND a.`deal_type` = 2 GROUP BY o.`so_user_id` ";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('shid', $value['old_shift']);
                $stmt->execute();
                $school_order_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (empty($school_order_list) || count($school_order_list) <= 0) {
                    continue;
                    // next school and it's shift
                }
                foreach ($school_order_list as $so_index => $school_order) {
                    if (intval($school_order['learn_hour']) >= intval($value['learn_hour'])) {
                        // more other work to be done here
                        // update shift to another 从普通班换到计时收费班
                        $sql = " UPDATE `{$sorder_tbl}` SET `so_shifts_id` = :shid WHERE `so_order_no` = :sonu ";
                        $stmt = $db->prepare($sql);
                        $stmt->bindParam('shid', $value['new_shift']);
                        $stmt->bindParam('sonu', $school_order['order_no']);
                        $stmt->execute();
                        // check if update is ok 检查是否更新成功
                        $sql = " SELECT `so_shifts_id` AS shift_id FROM `{$sorder_tbl}` WHERE `so_order_no` = :sonu ";
                        $stmt = $db->prepare($sql);
                        $stmt->bindParam('sonu', $school_order['order_no']);
                        $stmt->execute();
                        $update_shift_info = $stmt->fetch(PDO::FETCH_ASSOC);
                        if (isset($update_shift_info['shift_id']) && intval($update_shift_info['shift_id']) == intval($value['new_shift'])) {
                            // record some extra info to hint
                            // 记录一些可以起到提示性的信息
                            $count++;
                            $expire_order_list[] = $school_order;
                        }
                    }
                }
            }
        }
        if ($count > 0) {
            $data = array('code' => 200, 'data' => array('msg' => 'things have changed, check the order detail', 'count' => $count, 'expire_order_list' => $expire_order_list));
        } else {
            $data= array('code' => 200, 'data' => 'we found some target, but nothing has done here');
        }

        $db = null;
        echo json_encode($data);

    } catch ( PDOException $e ) {
        slimLog($req, $res, $e, 'PDO数据库异常');
        $data['code'] = 1;
        $data['data'] = '网络异常';
        $db = null;
        ajaxReturn($data);
    } catch ( ErrorException $e ) {
        slimLog($req, $res, $e, 'slim应用错误');
        $data['code'] = 1;
        $data['data'] = '网络错误';
        $db = null;
        ajaxReturn($data);
    }
} // main func
?>
