<?php
    /**
    * 接口模版
    * @param    type            $var    comment
    * @return   json
    * @package  /path/from/api
    * @author   gdc
    * @date     July 8, 2016
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

        $req = $app->request();
        $res = $app->response();

        /*
        //验证请求方式 POST
        if ( !$req->isPost() ) {
            slimLog($req, $res, null, '需要POST');
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }
        */

        /*
        //取得参数列表
        $validate_ok = validate(array('user_id' => 'INT'), $req->params());
        if ( !$validate_ok['pass'] ) {
            slimLog($req, $res, null, '参数不完整或参数类型不对，请核对文档与您传的参数');
            ajaxReturn($validate_ok['data']);
        }
        */

        //ready to return
        $data = array();
        try {
            // Open connection with mysql
            $db = getConnection();
            $sql = " SELECT COUNT(1) AS article_count FROM `xh_article` WHERE `category_id` IN (19, 20) ";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $article_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if (isset($article_info['article_count'])) {
                $t_begin = strtotime('2015-02-01');
                $t_end = strtotime('2016-10-01');
                $article_total = intval($article_info['article_count']);
                $rand_data = array();
                $rand_ts = array();
                for ($i = 0; $i < $article_total; $i++) {
                    $t = rand($t_begin, $t_end);
                    $rand_ts[] = $t;
                }
                asort($rand_ts);
                $rand_ts = array_values($rand_ts);
                foreach ($rand_ts as $i => $t) {
                    $rand_data[$i]['v'] = rand(10000, 99999);
                    $rand_data[$i]['t'] = $t;
                }

                $sql = " SELECT id, category_id, add_time, views FROM `xh_article` WHERE `category_id` IN (19, 20) ORDER BY id DESC ";
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $article_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($article_list && count($article_list) >0 ) {
                    $count = 0;
                    //$sql = " UPDATE `xh_article` SET `views` = :v , `add_time` = :t WHERE `id` = :i ";
                    $sql = " UPDATE `xh_article` SET `views` = :v WHERE `id` = :i ";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam('v', $v);
                    //$stmt->bindParam('t', $t);
                    $stmt->bindParam('i', $i);
                    foreach ($article_list as $index => $article) {
                        $v = $rand_data[$index]['v'];
                        $t = $rand_data[$index]['t'];
                        $i = $article['id'];
                        $update_ok = $stmt->execute();
                        if ($update_ok) {
                            $count++;
                        }
                    }
                    $data = array('code' => 200, 'data' => $count);
                } else {
                    $data = array('code' => 200, 'data' => 'nothing to be done.');
                }

            } else {
                $data = array('code' => 200, 'data' => 'nothing to be done.');
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
