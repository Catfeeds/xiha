<?php
    /**
    * 教练端注册之补充驾校
    * @param    string      $school_name 驾校的名字
    * @param    string      $phone       驾校的号码
    * @param    string      $address     驾校的地址          
    * @param    int         $province_id 省份id
    * @param    int         $city_id     城市id
    * @return   json
    * @package  api/v2/coachuser
    * @author   wl
    * @date     July 15, 2016
    **/
    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'addNewSchool');
    $app->run();

    function addNewSchool() {
        global $app, $crypt;

        //验证请求方式 POST
        $req = $app->request();
        $res = $app->response();
       
        if ( !$req->isPost() ) {
            slimLog($req, $res,null,'此接口仅开放POST请求');
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }

        //取得参数列表
        $validate_ok = validate(
            array(
                'school_name'   => 'STRING',
                'phone'         => 'STRING',
                'address'       => 'STRING',
                'province_id'   => 'INT',
                'city_id'       => 'INT',
            ), $req->params());
        if ( !$validate_ok['pass'] ) {
            slimLog($req, $res,null,'参数不完整或类型不对');
            ajaxReturn($validate_ok['data']);
        }

        $p              = $req->params();
        $school_name    = $p['school_name'];
        $telphone       = $p['phone'];
        $address        = $p['address'];
        $city_id        = $p['city_id'];
        $province_id    = $p['province_id'];
        $addtime        = time();

        //ready to return
        $data = array();
        try {
            // Open connection with mysql
            $db     = getConnection();
            $school = DBPREFIX.'school';
            
            // 判断驾校是否存在
            $sql = " SELECT `s_school_name` FROM `{$school}` WHERE `s_school_name` = :sname AND `province_id` = :pid AND `city_id` = :cid ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('sname', $school_name);
            $stmt->bindParam('pid', $province_id);
            $stmt->bindParam('cid', $city_id);
            $stmt->execute();
            $s_school_name = $stmt->fetch(PDO::FETCH_ASSOC);
            if(empty($s_school_name)) {
                // 添加驾校
                $sql = " INSERT INTO {$school} (`s_school_name`,`s_frdb_tel`,`s_address`, `province_id`, `city_id`, `is_show`, `addtime`) VALUES ('{$school_name}','{$telphone}','{$address}', '{$province_id}', '{$city_id}', 2, '{$addtime}') ";
                // is_show = 1-展示 2-不展示
                $db->query($sql); 
                $school_id = $db->lastInsertId();
            } else {
                $data = array('code' => 103,'data' => '此驾校已经存在');
                $db = null;
                ajaxReturn($data);
            }
            $data = array('code' => 200,'data' => '添加驾校成功');
            if ($school_id) {
                $data['code'] = 200;
                $data['data'] = array('school_info' => array(
                    'school_id'     => (int)$school_id,
                    'school_name'   => $school_name,
                    'is_show'       => 2,//1-展示 2-不展示
                ));
            } else {
                $data['code'] = 400;
                $data['data'] = '添加驾校失败';
            }
            // shut down the connection
            $db = null;
            ajaxReturn($data);
        } catch ( PDOException $e ) {
            slimLog($req, $res, $e, '网络异常');
            $data['code'] = 1;
            $data['data'] = '网络异常';
            ajaxReturn($data);
        } catch ( ErrorException $e ) {
            slimLog($req, $res, $e, 'slim应用错误');
            $data['code'] = 1;
            $data['data'] = '网络错误';
            ajaxReturn($data);
        }
    } // main func
    /*
    * CHANGE LOG
    * @update   July 22, 2016 [gdc 添加两个id字段，标示驾校的位置]
    * @update   July 22, 2016 [gdc 添加成功，则返回新添加的驾校id；另，判断驾校是否已经存在，条件多加province_id和city_id]
    */
?>
