<?php
    /**
    * App分享接口
    * @param    type            $type    分享的类型(0：学员端；1：教练端)
    * @return   json
    * @package  /api/shareApp.php
    * @author   wl
    * @date     Nov 03, 2016
    **/

    require 'Slim/Slim.php';
    require 'include/common.php';
    require 'include/crypt.php';
    require 'include/functions.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'shareApp');
    $app->run();

    function shareApp () {
        global $app, $crypt;

        $req = $app->request();
        $res = $app->response();

        //验证请求方式 POST
        if ( !$req->isPost() ) {
            slimLog($req, $res, null, '需要POST');
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }

        //取得参数列表并判断合法性
        $validate_ok = validate(
            array(
                'type' => 'INT',
            ), $req->params());

        if ( !$validate_ok['pass'] ) {
            slimLog($req, $res, null, '参数不完整或参数类型不对，请核对文档与您传的参数');
            ajaxReturn($validate_ok['data']);
        }

        $post   = $req->params();
        $type   = $post['type'];
        if (!in_array($type, array(0, 1))) {
            $type = 0;
        }
        
        //ready to return
        $data = array();
        try {

            if ($type == 0) {
                $share_content = '嘻哈学车，一键学车，轻松拿照';
            } else if ($type == 1) {
                $share_content = '嘻哈学车，让你招生更轻松，管理更方便';
            }
            $to_share = array(
                            'share_title'   => '嘻哈学车',
                            'share_content' => $share_content,
                            'share_link'    => 'http://m.xihaxueche.com:8001/html_h5/index.html',
                            'share_pic'     => '',
                        );

            $data = array(
                        'code' => 200,
                        'msg'  => 'OK',
                        'data' => $to_share,
                    );
            ajaxReturn($data);

        } catch ( PDOException $e ) {
            slimLog($req, $res, $e, 'PDO数据库异常');
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
?>
