<?php
    /**
    * 移除地区中的全角空格
    * @param    void
    * @return   json
    * @package  /api
    * @author   gdc
    * @date     Aug 31, 2016
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

        //验证请求方式 POST
        $req = $app->request();
        $res = $app->response();
        if ( !$req->isPost() ) {
            slimLog($req, $res, null, '需要POST');
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }

        //ready to return
        $data = array();
        try {
            // Open connection with mysql
            $db = getConnection();

            $tbl = DBPREFIX.'area';
            $sql = " SELECT * FROM `{$tbl}` WHERE `area` like '%　%' "; // 全角空格
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $area_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$area_list) {
                $data = array('code' => 200, 'data' => '无须修复');
            } else {
                $update_ok_count = 0;
                $update_fail_count = 0;
                $tbl = DBPREFIX.'area';
                $sql = " UPDATE `{$tbl}` SET `area` = :area_name WHERE `id` = :area_id ";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('area_name', $area_name);
                $stmt->bindParam('area_id', $area_id);
                foreach ($area_list as $area_index => $area) {
                    $area_list[$area_index]['area'] = str_replace('　', '', $area['area']);
                    $area_name = $area_list[$area_index]['area'];
                    $area_id = $area_list[$area_index]['id'];
                    $update_ok = $stmt->execute();
                    if ($update_ok) {
                        $update_ok_count++;
                    } else {
                        $update_fail_count++;
                    }
                }
                $data = array('code' => 200, 'data' => array('ok' => $update_ok_count, 'fail' => $update_fail_count));
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
?>
