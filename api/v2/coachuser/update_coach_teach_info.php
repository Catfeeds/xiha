<?php
    /**
    * 更新教练的教学信息，包括科目和驾驶执照类型
    * @param    int             $coach_id       教练id
    * @param    string          $coach_phone    教练手机号码
    * @param    int             $license_id     驾照类型
    * @param    string          $lesson_id      科目几  2,3 科目二和科目三
    * @return   json
    * @package  /api/v2/coachuser/
    * @author   gdc
    * @date     Aug 4, 2016
    **/

    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'update_coach_teach_info');
    $app->run();

    function update_coach_teach_info() {
        global $app, $crypt;

        //验证请求方式 POST
        $req = $app->request();
        $res = $app->response();
        if ( !$req->isPost() ) {
            slimLog($req, $res, null, '需要POST');
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }

        //取得参数列表
        $validate_ok = validate(array(
            'coach_id'      => 'INT',
            'coach_phone'   => 'STRING',
            'license_id'    => 'STRING',  // 2,3,4 
            'lesson_id'     => 'STRING',  // 2,3
        ), $req->params());
        if ( !$validate_ok['pass'] ) {
            slimLog($req, $res, null, '参数不正确');
            ajaxReturn($validate_ok['data']);
        }

        $p = $req->params();
        $coach_id       = $p['coach_id'];
        $coach_phone    = $p['coach_phone'];
        $lesson_id      = $p['lesson_id'];
        $license_id     = $p['license_id'];

        //ready to return
        $data = array();
        try {
            // Open connection with mysql
            $db = getConnection();

            $coach_tbl = DBPREFIX.'coach';

            // we update first
            $sql = " UPDATE `{$coach_tbl}` SET `s_coach_lesson_id` = :lesson_id, `s_coach_lisence_id` = :license_id WHERE `l_coach_id` = :cid AND `s_coach_phone` = :cphone ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('cid', $coach_id);
            $stmt->bindParam('cphone', $coach_phone);
            $stmt->bindParam('lesson_id', $lesson_id);
            $stmt->bindParam('license_id', $license_id);
            $stmt->execute();

            // we check if update is ok later on
            $sql = " SELECT `s_coach_lisence_id`, `s_coach_lesson_id` FROM `{$coach_tbl}` WHERE `l_coach_id` = :cid AND `s_coach_phone` = :cphone AND `s_coach_lisence_id` = :license_id AND `s_coach_lesson_id` = :lesson_id ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('cid', $coach_id);
            $stmt->bindParam('cphone', $coach_phone);
            $stmt->bindParam('lesson_id', $lesson_id);
            $stmt->bindParam('license_id', $license_id);
            $stmt->execute();
            $coach_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if (false === $coach_info) {
                $data = array('code' => 200, 'data' => array('coach_info' => array()));
            } else {
                $data = array('code' => 200, 'data' => array('coach_info' => $coach_info));
            }

            $db = null;
            ajaxReturn($data);

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
    /*
     * 8/31/2016 update log 教练的牌照可以多个，即C1，C2，A2可以多个
    **/
?>
