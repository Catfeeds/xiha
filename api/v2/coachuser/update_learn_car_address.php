<?php
    /**
    * 更新教练的学车地址
    * @parm     int     $coach_id       教练id
    * @parm     STRING  $coach_phone    教练手机号码
    * @parm     int     $city_id        城市id
    * @parm     int     $area_id        区或县id
    * @parm     STRING  $coach_address  详细地址
    * @return   json
    * @package  /api/v2/coachuser
    * @author   gdc
    * @date     July 29, 2016
    **/

    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'update_learn_car_address');
    $app->run();

    function update_learn_car_address() {
        global $app, $crypt;

        //验证请求方式 POST
        $req = $app->request();
        $res = $app->response();
        if ( !$req->isPost() ) {
            slimLog($req, $res, null, '此接口仅开放POST方式请求');
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }

        //取得参数列表
        $validate_ok = validate(array(
            'coach_id'      => 'INT',
            'coach_phone'   => 'STRING',
            'city_id'       => 'INT',
            'area_id'       => 'INT',
            'coach_address' => 'STRING',
        ), $req->params());
        if ( !$validate_ok['pass'] ) {
            slimLog($req, $res, null, '参数不正确');
            ajaxReturn($validate_ok['data']);
        }

        $p = $req->params();
        $coach_id = $p['coach_id'];
        $coach_phone = $p['coach_phone'];
        $city_id = $p['city_id'];
        $area_id = $p['area_id'];
        $coach_address = $p['coach_address'];

        //ready to return
        $data = array();
        try {
            // Open connection with mysql
            $db = getConnection();

            // check we have this coach
            $coach_tbl = DBPREFIX.'coach';
            $sql = " SELECT 1 FROM `{$coach_tbl}` WHERE `l_coach_id` = :cid AND `s_coach_phone` = :cp ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('cid', $coach_id);
            $stmt->bindParam('cp', $coach_phone);
            $stmt->execute();
            $coach_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if (empty($coach_info)) {
                $data['code'] = 103;
                $data['data'] = '无此教练';
                $db = null;
                slimLog($req, $res, null, '无此教练');
                ajaxReturn($data);
            }

            // first we need province_id according to the provided city_id
            $city_tbl = DBPREFIX.'city';
            $sql = " SELECT `fatherid` AS province_id FROM `{$city_tbl}` WHERE `cityid` = :cid ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('cid', $city_id);
            $stmt->execute();
            $province_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if (isset($province_info['province_id'])) {
                $province_id = intval($province_info['province_id']);
            } else {
                $province_id = 0;
            }

            // we can update now
            $sql = " UPDATE `{$coach_tbl}` SET `province_id` = :provinceid, `city_id` = :cityid, `area_id` = :areaid, `s_coach_address` = :ca WHERE `l_coach_id` = :cid AND `s_coach_phone` = :cp ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('provinceid', $province_id);
            $stmt->bindParam('cityid', $city_id);
            $stmt->bindParam('areaid', $area_id);
            $stmt->bindParam('ca', $coach_address);
            $stmt->bindParam('cid', $coach_id);
            $stmt->bindParam('cp', $coach_phone);
            $update_ok = $stmt->execute();
            if ($update_ok) {
                $data['code'] = 200;
                $data['data'] = '成功';
            } else {
                $data['code'] = 400;
                $data['data'] = '失败';
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
            slimLog($req, $res, $e, 'slim应用解析错误');
            $data['code'] = 1;
            $data['data'] = '网络错误';
            $db = null;
            ajaxReturn($data);
        }
    } // main func
    /*
     * 8/31/2016 update log 通过城市找出province_id也更新
    **/
?>
