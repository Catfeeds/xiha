<?php
    /**
    * 获取用户训练记录 
    * @param    int     $user_id   学员用户id
    * @author   gdc
    **/

    require 'Slim/Slim.php';
    require 'include/common.php';
    require 'include/crypt.php';
    require 'include/functions.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'getExamHistory');
    $app->run();

    function getExamHistory() {
        Global $app, $crypt;
        $req = $app->request();
        $res = $app->response();

        //RES_PREFIX
        //txt训练记录文件路径前辍
        !defined('RES_PREFIX') && define('RES_PREFIX', HOST . 'ecoach/api/');

        //验证请求方式 POST
        if ( !$req->isPost() ) {
            slimLog($req, $res, null, '需要POST请求');
            $data = array('code' => 106, 'data' => '请求错误');
            ajaxReturn($data);
        }

        //验证请求参数列表
        $validate_ok = validate(array('user_id' => 'INT'), $req->params());
        if ( !$validate_ok['pass'] ) {
            $data = $validate_ok['data'];
            slimLog($req, $res, null, '参数有误');
            ajaxReturn($data);
        } 

        $p = $req->params();;
        $user_id = $p['user_id'];

        // ready to return array $data
        $data = array();

        try {
            //建立数据库连接
            $db = getConnection();

            //we need id_card first
            $users_info_tbl = DBPREFIX.'users_info';
            $sql = " SELECT `identity_id` FROM `{$users_info_tbl}` WHERE `user_id` = :uid ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('uid', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user_info === false) {
                $data = array('code' => 200, 'data' => array());
                slimLog($req, $res, null, '不存在的用户');
                ajaxReturn($data);
            }
            $identity_id = $user_info['identity_id'];

            // new we can get exam history according to their 'identity_id'
            $history_tbl = DBPREFIX . 'exam_history';
            $sql = " SELECT * FROM `{$history_tbl}` WHERE `identity_id` = :id_card ORDER BY `time_interval` DESC ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id_card', $identity_id);
            $stmt->execute();
            $exam_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (array() === $exam_history) {
                $data = array('code' => 200, 'data' => array());
                $db = null;
                $stmt = null;
                ajaxReturn($data);
            }
            if (is_array($exam_history)) {
                //场地信息 包括图片，地址，名称
                $site_tbl = DBPREFIX.'site';
                $sql = " SELECT `site_name`, `id`, `imgurl`, `address`  FROM `{$site_tbl}` WHERE `id` = :site_id AND `site_status` = 1 ";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('site_id', $site_id);
                foreach($exam_history as $index => $value) {
                    $exam_history[$index]['text_url'] = RES_PREFIX.$value['text_url'];
                    if (file_exists(__DIR__.DIRECTORY_SEPARATOR.$value['text_url'])) {
                        $exam_history[$index]['downloadable'] = 1;
                    } else {
                        $exam_history[$index]['downloadable'] = 2;
                    }
                    $site_id = $value['site_id'];
                    $stmt->execute();
                    $site_info = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (false === $site_info) {
                        $exam_history[$index]['site_name'] = '';
                        $exam_history[$index]['site_address'] = '';
                        $exam_history[$index]['imgurl'] = '';
                    } else {
                        $exam_history[$index]['site_name'] = $site_info['site_name'];
                        $exam_history[$index]['site_address'] = $site_info['address'];
                        if (file_exists(__DIR__.'/../../'.$site_info['imgurl'])) {
                            $exam_history[$index]['imgurl'] = HOST.$site_info['imgurl'];
                        } else {
                            $exam_history[$index]['imgurl'] = '';
                        }
                    }
                }
            }
            $data = array('code' => 200, 'data' => $exam_history);

            //关闭数据库
            $db = null;
            ajaxReturn($data);

        } catch ( PDOException $e ) {
            slimLog($req, $res, $e, '网络错误');
            $data = array('code' => 1, 'data' => '网络错误');
            $db = null;
            ajaxReturn($data);
        } catch ( Exception $e) {
            $data = array('code' => 1, 'data' => '网络错误');
            slimLog($req, $res, $e, 'slim应用错误');
            $db = null;
            ajaxReturn($data);
        }
    } // main func
    /*
     * 2016-08-02 gdc 初步完成
    **/
?>
