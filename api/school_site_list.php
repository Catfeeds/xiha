<?php
    /**
    * 驾校场地列表接口
    * @param    int            $school_id    驾校id(可选)
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
    $app->any('/', 'school_site_list');
    $app->run();

    function school_site_list() {
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

            $tbl = DBPREFIX.'site';
            $sql = " SELECT * FROM `{$tbl}` WHERE `site_status` = 1 "; // site_status=1-开放场地
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
            $site_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // 添加url前辍
            if (is_array($site_list) && count($site_list) > 0) {
                foreach ($site_list as $index => $site) {
                    // 打点文件1
                    if (trim($site['point_text_url1'])) {
                        if (substr($site['point_text_url1'], 0, 3) === '../') {
                            $site['point_text_url1'] = str_replace('../', '', $site['point_text_url1']);
                        }
                        if (file_exists('../'.$site['point_text_url1'])) {
                            $site_list[$index]['point_text_url1'] = HOST.$site['point_text_url1'];
                        } else {
                            $site_list[$index]['point_text_url1'] = '';
                        }
                    }

                    // 打点文件2
                    if (trim($site['point_text_url2'])) {
                        if (substr($site['point_text_url2'], 0, 3) === '../') {
                            $site['point_text_url2'] = str_replace('../', '', $site['point_text_url2']);
                        }
                        if (file_exists('../'.$site['point_text_url2'])) {
                            $site_list[$index]['point_text_url2'] = HOST.$site['point_text_url2'];
                        } else {
                            $site_list[$index]['point_text_url2'] = '';
                        }
                    }

                    // 风采图片
                    if (trim($site['imgurl'])) {
                        if (substr($site['imgurl'], 0, 3) === '../') {
                            $site['imgurl'] = str_replace('../', '', $site['imgurl']);
                        }
                        if (file_exists('../'.$site['point_text_url2'])) {
                            $site_list[$index]['imgurl'] = HOST.$site['imgurl'];
                        } else {
                            $site_list[$index]['imgurl'] = '';
                        }
                    }

                    // 3D模型资源链接
                    if (trim($site['model_resource_url'])) {
                        if (substr($site['model_resource_url'], 0, 3) === '../') {
                            $site['model_resource_url'] = str_replace('../', '', $site['model_resource_url']);
                        }
                        if (file_exists('../'.$site['model_resource_url'])) {
                            $site_list[$index]['model_resource_url'] = HOST.$site['model_resource_url'];
                        } else {
                            $site_list[$index]['model_resource_url'] = '';
                        }
                    }
                }
            }

            $data = array('code' => 200, 'data' => array('site_list' => $site_list));

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
