<?php
    /**
    * 获取所有城市列表
    * @param    void
    * @return   json
    * @package  /api/v2/coachuser/
    * @author   gdc
    * @date     July 14, 2016
    **/

    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'city_list');
    $app->run();

    function city_list() {
        global $app, $crypt, $redis_conf;

        //验证请求方式 POST
        $req = $app->request();
        $res = $app->response();
        if ( !$req->isGet() ) {
            slimLog($req, $res, null, '此接口仅开放GET方式请求');
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }

        $redis_already_up = false; //redis启动

        //ready to return
        $data = array();
        try {
            // redis
            $redis = getRedisConnection();
            if ($redis) {
                $redis_already_up = true;
                    if ($redis->exists('coachuser:city_list')) {
                        $data['code'] = 200;
                        $data['data'] = unserialize($redis->get('coachuser:city_list'));
                        ajaxReturn($data);
                    }
            }
            // redis
            
            // Open connection with mysql
            $db = getConnection();
            $tbl = DBPREFIX . 'city';
            $sql = " SELECT * FROM `{$tbl}` WHERE 1 ORDER BY `acronym` ";
            $stmt = $db->query($sql);
            $city_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (is_array($city_list)) {
                $data['code'] = 200;
                $data['data'] = array('city_list' => $city_list);

                // if redis is up
                if ($redis_already_up) {
                    $redis->set('coachuser:city_list', serialize($data['data']), $redis_conf['TTL_WEEK']);
                } // redis save
            } else {
                $data['code'] = 200;
                $data['data'] = array('city_list' => array());
            }

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
