<?php 
    /**
     * 获取广告图接口
     * @param integer $scene 广告场景
     * @param integer $location_type 地区类型
     * @param integer $location_id 地区id
     * @param integer $device 设备类型
     * @return object
     * @author sunweiwei gaodacheng
     **/
    require 'Slim/Slim.php';
    require 'include/common.php';
    require 'include/crypt.php';
    require 'include/functions.php';
    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/','getMixAds');
    $app->run();

    function getMixAds() {
        global $app, $crypt;
        $request = $app->request();
        if ( !$request->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            setapilog('[get_mix_ads] [:error] [client ' . $request->getIp() . '] [Method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            echo json_encode($data);
            exit();
        }
        //  获取请求参数并判断合法性
        $validate_result = validate(array('scene'=>'INT', 'location_type'=>'INT', 'location_id'=>'INT', 'device'=>'INT'), $request->params());
        if (!$validate_result['pass']) {
            echo json_encode($validate_result['data']);
            exit();
        }
        $scene = $request->params('scene');
        $location_type = $request->params('location_type');
        switch ($location_type) {
            case 1: 
                $location_name = 'area_id';
                break;
            case 2: 
                $location_name = 'city_id';
                break;
            case 3: 
                $location_name = 'province_id';
                break;
            default:
               $data = array('code' => 102, 'data' => '参数错误');
               echo json_encode($data);
               exit();  
        }
        $location_id = $request->params('location_id');
        $device = $request->params('device');

        try{
            $db = getConnection();
            //根据location_type, location_id, scene条件从主表cs_ads中查询所有广告订单id
            $sql = " SELECT ads.`id`, info.`device` FROM `" . DBPREFIX . "ads` AS ads LEFT JOIN `". DBPREFIX ."ads_info` AS info ON ads.id = info.ads_id WHERE `scene_id` = :scene AND {$location_name} = :location_id AND `ads_status` != 3";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('scene', $scene, PDO::PARAM_INT);
            //$stmt->bindParam('location_name', $location_name, PDO::PARAM_STR);
            $stmt->bindParam('location_id', $location_id, PDO::PARAM_INT);
            $stmt->execute();
            $ads_info = $stmt->fetch(PDO::FETCH_ASSOC);

            if ( empty($ads_info ) ) {
                if ( $scene == 101 ) {
                    exit( json_encode( array('code' => 104, 'data' => array() ) ) );
                } else {
                    exit( json_encode( array('code' => 200, 'data' => getDefaultAds($scene)) ) );
                }
            }

            $ads_device = explode(',', $ads_info['device']);
            if ( !in_array($device, $ads_device) ) {
                exit( json_encode( array('code' => 101, 'data' => '设备错误') ) );
            }

            $ads_id = $ads_info['id'];
            $nowts = time();

            $sql = " SELECT `resource_type`, `resource_url`, `loop_time`, `ads_url`, `ads_title` FROM `". DBPREFIX ."ads_order` ";
            $sql .= " WHERE `ads_id` = :id AND `order_status` = 1002 AND `over_time` > :time  AND `device` = :dev";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $ads_id, PDO::PARAM_INT);
            $stmt->bindParam('dev', $device, PDO::PARAM_INT);
            $stmt->bindParam('time', $nowts, PDO::PARAM_INT);
            $stmt->execute();
            $order_info = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ( empty($order_info) ) {
                //如果数据库没有广告订单，去获取默认广告
                exit( json_encode( array('code' => 200, 'data' => getDefaultAds($scene) ) ) );
            }

            //广告资源url添加完整路径
            foreach ($order_info as $k => $v) {
                $order_info[$k]['resource_url'] = HOST . $v['resource_url'];
            }

            $db = null;

            exit( json_encode( array('code' => 200, 'data' => $order_info) ) );

        } catch (PDOException $e) {
            setapilog('[get_mix_ads] [:error] [client ' . $request->getIp() . '] [params ' . serialize($request->params()) . '] [1 ' . $e->getMessage() . ']');
            $data = array('code' => 1, 'data' => '网络错误');
            echo json_encode($data);
        }

    }
    
    function getDefaultAds($scene) {
        // $base_url = 'http://60.173.247.68:8081/api/ads/';
        $base_url = HOST.'/upload/ads/0/20160412/';
        $ads_url = 'http://www.xihaxueche.com/';
        $data = array();
        switch ( $scene ) {
            case 101: 
                $data = array(
                    array(
                        'resource_type' => 1, // 1 表示图片 2 表示视频
                        'resource_url'  => $base_url . 'start1242_2208.png',
                        'loop_time'     => 5,
                        'ads_url'       => $ads_url,
                        'title'         => '',
                    ),
                );
                break;
            case 102: 
                $data = array(
                    array(
                        'resource_type' => 1, // 1 表示图片 2 表示视频
                        'resource_url'  => $base_url . 'banner18.jpg',
                        'loop_time'     => 2,
                        'ads_url'       => $ads_url,
                        'title'         => '',
                    ),
                    array(
                        'resource_type' => 1, // 1 表示图片 2 表示视频
                        'resource_url'  => $base_url . 'banner19.jpg',
                        'loop_time'     => 2,
                        'ads_url'       => $ads_url,
                        'title'         => '',
                    ),
                    array(
                        'resource_type' => 1, // 1 表示图片 2 表示视频
                        'resource_url'  => $base_url . 'banner20.jpg',
                        'loop_time'     => 2,
                        'ads_url'       => $ads_url,
                        'title'         => '',
                    ),
                    array(
                        'resource_type' => 1, // 1 表示图片 2 表示视频
                        'resource_url'  => $base_url . 'banner24.jpg',
                        'loop_time'     => 2,
                        'ads_url'       => $ads_url,
                        'title'         => '',
                    ),
                    array(
                        'resource_type' => 1, // 1 表示图片 2 表示视频
                        'resource_url'  => $base_url . 'banner22.jpg',
                        'loop_time'     => 2,
                        'ads_url'       => $ads_url,
                        'title'         => '',
                    ),
                    array(
                        'resource_type' => 1, // 1 表示图片 2 表示视频
                        'resource_url'  => $base_url . 'banner21.jpg',
                        'loop_time'     => 2,
                        'ads_url'       => $ads_url,
                        'title'         => '',
                    ),
                );
                break;
            case 103: 
                $data = array(
                    array(
                        'resource_type' => 1, // 1 表示图片 2 表示视频
                        'resource_url'  => $base_url . 'question2.png',
                        'loop_time'     => 2,
                        'ads_url'       => $ads_url,
                        'title'         => '',
                    ),
                );
                break;
            case 104: 
                $data = array(
                    array(
                        'resource_type' => 1, // 1 表示图片 2 表示视频
                        'resource_url'  => $base_url . 'question1.png',
                        'loop_time'     => 2,
                        'ads_url'       => $ads_url,
                        'title'         => '',
                    ),
                );
                break;
            default :
                $data = array(
                    array(
                        'resource_type' => 1, // 1 表示图片 2 表示视频
                        'resource_url'  => $base_url . 'banner24.jpg',
                        'loop_time'     => 2,
                        'ads_url'       => $ads_url,
                        'title'         => '',
                    ),
                );
                break;
        }
        return $data;
    }

?>
