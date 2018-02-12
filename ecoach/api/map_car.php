<?php
/**
 * 获取场地和车辆的打点坐标图
 * @param    int     $site_id    场地id[默认 1]
 * @param    int     $car_id     车辆id[默认 1]
 * @return   json
 * @package  /api
 * @author   gdc
 * @date     July 21, 2016
 **/

require 'Slim/Slim.php';
require 'include/common.php';
require 'include/crypt.php';
require 'include/functions.php';

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
$app->any('/', 'map_car');
$app->run();

function map_car() {
    global $app, $crypt;

    //验证请求方式 POST
    $req = $app->request();
    $res = $app->response();
    if ( !$req->isPost() ) {
        slimLog($req, $res, null, '需要POST方式请求');
        ajaxReturn(array('code' => 106, 'data' => '请求错误'));
    }

    //取得参数列表
    $validate_ok = validate(
        array(
            'site_id' => 'INT',
            'car_id' => 'INT',
        ), $req->params());
    if ( !$validate_ok['pass'] ) {
        slimLog($req, $res, null, '参数不正确');
        ajaxReturn($validate_ok['data']);
    }

    $p = $req->params();
    $site_id = $p['site_id'];
    $car_id = $p['car_id'];
    //ready to return
    $data = array();
    /*
    if ($site_id == '1' && $car_id == '1') {
        $point = array(
            'car'   => HOST . 'ecoach/upload/car/che.txt',
            'site'  => HOST . 'ecoach/upload/map/tu.txt',
            'site2' => HOST . 'ecoach/upload/map/bayinangang.txt',
        );
    } else {
        $point = array(
            'car'   => '',
            'site'  => '',
            'site2' => '',
        );
        $point = array(
            'car'   => HOST . 'ecoach/upload/car/che.txt',
            'site'  => HOST . 'ecoach/upload/map/tu.txt',
            'site2' => HOST . 'ecoach/upload/map/bayinangang.txt',
        );
    }
    $data['code'] = 200;
    $data['data'] = array('point' => $point);
    ajaxReturn($data);
    */

    try {
        // Open connection with mysql
        $db = getConnection();

        $map = array();

        $site_tbl = DBPREFIX.'site';
        $sql = " SELECT point_text_url1 as site, point_text_url2 as site2 FROM {$site_tbl} WHERE `id` = :site_id ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('site_id', $site_id);
        $stmt->execute();
        $site_info = $stmt->fetch(PDO::FETCH_OBJ);
        if (is_object($site_info)) {
            if (substr($site_info->site, 0, 3) == '../') {
                $site_info->site = substr($site_info->site, 3, strlen($site_info->site) - 3);
            }
            if (substr($site_info->site2, 0, 3) == '../') {
                $site_info->site2 = substr($site_info->site2, 3, strlen($site_info->site2) - 3);
            }

            if (! empty($site_info->site) && file_exists(join(array(APP_ROOT, $site_info->site)))) {
                $site = HOST.$site_info->site;
            } else {
                $site = HOST.'ecoach/upload/map/tu.txt';
            }
            if (!empty($site_info->site2) && file_exists(join(array(APP_ROOT, $site_info->site2)))) {
                $site2 = HOST.$site_info->site2;
            } else {
                $site2 = HOST.'ecoach/upload/map/tu.txt';
            }
        } else {
            $site = HOST.'ecoach/upload/map/tu.txt';
            $site2 = HOST.'ecoach/upload/map/tu.txt';
        }

        $car_cate_tbl = DBPREFIX.'car_category';
        $sql = " SELECT `point_text_url` AS car FROM {$car_cate_tbl} WHERE `id` = :car_id ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam('car_id', $car_id);
        $stmt->execute();
        $car_info = $stmt->fetch(PDO::FETCH_OBJ);
        if (is_object($car_info)) {
            if (substr($car_info->car, 0, 3) == '../') {
                $car_info->car = substr($car_info->car, 3, strlen($car_info->car) - 3);
            }
            if (! empty($car_info->car) && file_exists(join(array(APP_ROOT, $car_info->car)))) {
                $car = HOST.$car_info->car;
            } else {
                $car = HOST.'ecoach/upload/map/car.txt';
            }
        } else {
            $car = HOST.'ecoach/upload/car/che.txt';
        }

        $data = array(
            'code' => 200,
            'data' => array(
                'point' => array(
                    'car' => $car,
                    'site' => $site,
                    'site2' => $site2,
                ),
            ),
        );

        $db = null;
        ajaxReturn($data);

    } catch ( PDOException $e ) {
        slimLog($req, $res, $e, '网络异常');
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
