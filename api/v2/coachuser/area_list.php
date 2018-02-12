<?php
    /**
    * 获取城市之下的地区列表
    * @param    void
    * @param    int     $city_id    城市id
    * @return   json
    * @package  /api/v2/coachuser/
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
    $app->any('/', 'area_list');
    $app->run();

    function area_list() {
        global $app, $crypt, $redis_conf;

        //验证请求方式 POST
        $req = $app->request();
        $res = $app->response();
        if ( !$req->isPost() ) {
            slimLog($req, $res, null, '此接口仅开放POST方式请求');
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }

        //  获取请求参数并判断合法性
        $validate_result = validate(
        	array(
        		'city_id'      =>'INT', 
    		), $req->params());

        if (!$validate_result['pass']) {
            ajaxReturn($validate_result['data']);
        }

        $p = $req->params();
        $city_id = $p['city_id'];

        //ready to return
        $data = array();
        try {
            // Open connection with mysql
            $db = getConnection();
            $area_tbl = DBPREFIX . 'area';
            $sql = " SELECT * FROM `{$area_tbl}` WHERE `fatherid` = :cid ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('cid', $city_id);
            $stmt->execute();
            $area_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (is_array($area_list)) {
                $data['code'] = 200;
                $data['data'] = array('area_list' => $area_list);
            } else {
                $data['code'] = 200;
                $data['data'] = array('area_list' => array());
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
