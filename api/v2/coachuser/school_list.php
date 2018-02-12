<?php
    /**
    * 获取一个城市正文的所有驾校列表
    * @param    int                 $city_id    城市id
    * @return   json
    * @package  /api/v2/coachuser
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
    $app->any('/', 'school_list');
    $app->run();

    function school_list() {
        global $app, $crypt;

        //验证请求方式 POST
        $req = $app->request();
        $res = $app->response();
        if ( !$req->isPost() ) {
            slimLog($req, $res, null, '此接口仅开放POST方式请求');
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }

        //取得参数列表
        $validate_ok = validate(array('city_id' => 'INT'), $req->params());
        if ( !$validate_ok['pass'] ) {
            slimLog($req, $res);
            ajaxReturn($validate_ok['data']);
        }

        $p = $req->params();
        $city_id = $p['city_id'];

        //ready to return
        $data = array();
        try {
            // Open connection with mysql
            $db = getConnection();

            $tbl = DBPREFIX .'school';
            $sql = " SELECT `l_school_id` AS school_id, `s_school_name` AS school_name, `is_show` FROM `{$tbl}` WHERE `city_id` = :city_id ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('city_id', $city_id, PDO::PARAM_INT);
            $stmt->execute();
            $school_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (is_array($school_list)) {
                foreach ($school_list as $index => $school) {
                    $school_list[$index]['school_id'] = (int)trim($school['school_id']);
                    $school_list[$index]['school_name'] = trim($school['school_name']);
                    $school_list[$index]['is_show'] = (int)trim($school['is_show']);
                }
            } else {
                $school_list = array();
            }
            
            $city_tbl = DBPREFIX . 'city';
            $province_tbl = DBPREFIX . 'province';
            $sql = " SELECT c.spelling, c.acronym, c.leter, c.city AS city_name, p.provinceid AS province_id, p.province AS province_name FROM `{$city_tbl}` AS c LEFT JOIN `{$province_tbl}` AS p ON c.fatherid = p.provinceid WHERE c.cityid = :city_id ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('city_id', $city_id, PDO::PARAM_INT);
            $stmt->execute();
            $city_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if (is_array($city_info)) {
                $city_info['province_id'] = isset($city_info['province_id']) ? (int)trim($city_info['province_id']): 0;
                $city_info['province_name'] = isset($city_info['province_name']) ? trim($city_info['province_name']): '';
                $city_info['city_name'] = isset($city_info['city_name']) ? trim($city_info['city_name']): '';
                $city_info['acronym'] = isset($city_info['acronym']) ? trim($city_info['acronym']): '';
                $city_info['spelling'] = isset($city_info['spelling']) ? trim($city_info['spelling']): '';
                $city_info['letter'] = isset($city_info['leter']) ? trim($city_info['leter']): '';
                unset($city_info['leter']);
                ksort($city_info);
            } else {
                $city_info = array();
            }

            $data['code'] = 200;
            $data['data'] = array(
                'city_id'       => (int)$city_id,
                'city_info'     => $city_info,
                'school_list'   => $school_list,
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
    /**
     * 注意的地方:
     * cs_school表
     * is_show 是否展示 1 展示 2 不展示
     * city_id 城市id
    **/
?>
