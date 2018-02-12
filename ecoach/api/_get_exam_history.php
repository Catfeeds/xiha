<?php
    /**
    * 获取用户训练记录 
    * @param int $user_id
    * @author gaodacheng
    **/

    require 'slim3/vendor/autoload.php';
    require 'include/common.php';
    require 'include/crypt.php';
    require 'include/functions.php';

    $app = new \Slim\App;
    $checkProxyHeaders = true; // Note: Never trust the IP address for security processes!
    $trustedProxies = ['10.0.0.1', '10.0.0.2']; // Note: Never trust the IP address for security processes!
    $app->add(new RKA\Middleware\IpAddress($checkProxyHeaders, $trustedProxies));
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'getExamHistory');
    $app->run();

    function getExamHistory($request, $response, $args) {

        // ready to return array $data
        $data = array();

        //RES_PREFIX
        //txt训练记录文件路径前辍
        !defined('RES_PREFIX') && define('RES_PREFIX', HOST . 'ecoach/api/');

        //验证请求方式 POST
        if ( !$request->isPost() ) {
            setapilog('[get_exam_history] [:error] [client ' . $request->getAttribute('ip_address') . '] [method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            $data = array('code' => 106, 'data' => '请求错误');
            return $response->withJson($data, 200);
        }

        //验证请求参数列表
        $validate_ok = validate(array('user_id' => 'INT'), $request->getParams());
        if ( !$validate_ok['pass'] ) {
            $data = $validate_ok['data'];
            return $response->withJson($data, 200);
        } 

        $user_id = $request->getParam('user_id');

        try {
            //建立数据库连接
            $db = getConnection();

            $fields_buf = array(
                'user_id',
                'exam_id',
                'site_id',
                'car_id',
                'identity_no',
                'text_url',
                'time_interval',
            );

            $table_buf = DBPREFIX . 'exam_history';

            $sql = " SELECT `".implode('`,`', $fields_buf)."` FROM `{$table_buf}` WHERE `user_id` = '{$user_id}' ";
            $stmt = $db->query($sql);
            $exam_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ( empty($exam_history) ) {
                $data = array(
                    'code' => '104',
                    'data' => '记录空',
                );
            } else {
                foreach ( $exam_history as $key => $val ) {
                    if ( array_key_exists('text_url', $val) ) {
                        if ( file_exists($val['text_url']) ) {
                            $exam_history[$key]['text_url'] = RES_PREFIX . $val['text_url'];
                        } else {
                            unset($val[$key]);
                        }
                    }
                }
                $exam_history = array_values($exam_history);
                $data = array(
                    'code' => '200', 
                    'data' => $exam_history,
                );
            }

            //关闭数据库
            $db = null;

            return $response->withJson($data, 200);
        } catch ( PDOException $e ) {
            setapilog('[get_exam_history] [:error] [client ' . $request->getAttribute('ip_address') . '] [user_id % ' . $user_id . '] [1 '.$e->getMessage().']');
            $data = array('code' => 1, 'data' => '网络异常');
            return $response->withJson($data, 200);
        }
    }
?>
