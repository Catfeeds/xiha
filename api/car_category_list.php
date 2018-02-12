<?php
    /**
    * 驾校车型列表接口
    * @param    int            $school_id    驾校id
    * @return   json
    * @package  /api
    * @author   gdc
    * @date     Aug 29, 2016
    **/

    require 'Slim/Slim.php';
    require 'include/common.php';
    require 'include/crypt.php';
    require 'include/functions.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'car_category_list');
    $app->run();

    function car_category_list() {
        global $app, $crypt;

        //验证请求方式 POST
        $req = $app->request();
        $res = $app->response();
        if ( !$req->isPost() ) {
            slimLog($req, $res, null, '需要POST');
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }

        //取得参数列表

        /*
        // 不需要验证
        $validate_ok = validate(array('user_id' => 'INT'), $req->params());
        if ( !$validate_ok['pass'] ) {
            slimLog($req, $res, null, '参数不完整或参数类型不对，请核对文档与您传的参数');
            ajaxReturn($validate_ok['data']);
        }
        */

        $p = $req->params();
        //ready to return
        $data = array();
        try {
            // Open connection with mysql
            $db = getConnection();

            $school_id_flag = false;

            $tbl = DBPREFIX.'car_category';
            $sql = " SELECT * FROM `{$tbl}` WHERE 1 ";
            if (isset($p['school_id']) && intval($p['school_id'] > 0)) {
                $sql .= " AND `school_id` = :sid ";
                $school_id = $p['school_id'];
                $school_id_flag = true;
            }
            $stmt = $db->prepare($sql);
            if (true === $school_id_flag) {
                $stmt->bindParam('sid', $school_id);
            }
            $stmt->execute();
            $car_category_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 添加url前缀
            if ($car_category_list) {
                foreach ($car_category_list as $key => $value) {
                    // 车的打点图
                    if (trim($value['point_text_url'])) {
                        if (substr($value['point_text_url'], 0, 3) === '../') {
                            $value['point_text_url'] = str_replace('../', '', $value['point_text_url']);
                        }
                        if (file_exists('../'.$value['point_text_url'])) {
                            $car_category_list[$key]['point_text_url'] = HOST.$value['point_text_url'];
                        } else {
                            $car_category_list[$key]['point_text_url'] = '';
                        }
                    }
                }
            }

            $data = array('code' => 200, 'data' => array('car_category_list' => $car_category_list));

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
