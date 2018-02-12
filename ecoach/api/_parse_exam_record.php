<?php
    /**
    * parse_exam_record 
    * @param string $exam_record
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
    $app->any('/', 'parseExamRecord');
    $app->run();

    function parseExamRecord($request, $response, $args) {

        // ready to return array $data
        $data = array();

        //验证请求方式 POST
        if ( !$request->isPost() ) {
            setapilog('[update_user_info] [:error] [client ' . $request->getAttribute('ip_address') . '] [method % ' . $request->getMethod() . '] [106 错误的请求方式]');
            $data = array('code' => 106, 'data' => '请求错误');
            return $response->withJson($data, 200);
        }

        //验证请求参数列表
        $validate_ok = validate(array('exam_record' => 'STRING', 'user_id' => 'INT'), $request->getParams());
        if ( !$validate_ok['pass'] ) {
            $data = $validate_ok['data'];
            return $response->withJson($data, 200);
        } 
        $exam_record = $request->getParam('exam_record');
        $user_id = $request->getParam('user_id');

        //文件不存在
        if ( !file_exists($exam_record) ) {
            $data = array(
                'code' => 104,
                'data' => '文件不存在',
            );
            return $response->withJson($data, 200);
        }

        $fp = file_get_contents($exam_record);
        //用换行符分割每一条数据记录
        $exam_record_list = explode(";", $fp);
        //return $response->withJson($exam_record_list, 200);

        try {
            //建立数据库连接
            $db = getConnection();

            $sql = array();
            foreach ($exam_record_list as $key => $val) {
                $original_data = explode(',', $val);
                //获取表头
                $table_head = array_shift($original_data);
                //将用户id添加进原始数据第一个元素位置
                //array_unshift($original_data, $user_id);
                switch ( $table_head ) {
                    case 'EXAM': 
                        if ( count($original_data) !== 7 ) {
                            continue;
                        }
                        $table = DBPREFIX . 'exam_result_model';
                        $fields_buf = array(
                            'site_id',
                            'car_id',
                            'user_id',
                            'exam_id',
                            'item_content',
                            'point_penalty',
                            'time_interval',
                        );
                        if ( !isset($sql[$table_head]) ) {
                            $sql[$table_head] = " INSERT INTO `{$table}` (`".implode("`,`", $fields_buf)."`) VALUES ";
                        }
                        $sql[$table_head] .= " ('".implode("','", $original_data)."'), ";
                        break;
                    case 'XWYD2': 
                        if ( count($original_data) !== 18 ) {
                            continue;
                        }
                        $table = DBPREFIX . 'xwyd2_model';
                        $fields_buf = array(
                            'site_id',
                            'car_id',
                            'user_id', //用户id
                            'exam_id', //训练id
                            'retain_position', //预留位置，固定值
                            'date', //年月日 如20160502
                            'time', //时分秒 如142609
                            'course_angle', //航向角 度
                            'pitch_angle',  //俯仰角 度
                            'roll_angle',   //回滚角 度
                            'east_distance',//东向距离 米
                            'north_distance',//北向距离 米
                            'sky_distance', //天向距离 米
                            'speed', //速度 公里/小时
                            'advance_distance', //前进距离 米
                            'back_distance', //后退距离 米
                            'gps_status', //差分状态 4: RTK 5:
                            'time_interval', //距离1970年时间
                        );
                        if ( !isset($sql[$table_head]) ) {
                            $sql[$table_head] = " INSERT INTO `{$table}` (`".implode("`,`", $fields_buf)."`) VALUES ";
                        }
                        $sql[$table_head] .= " ('".implode("','", $original_data)."'), ";
                        break;
                    case 'XWCJ': 
                        if ( count($original_data) !== 34 ) {
                            continue;
                        }
                        $table = DBPREFIX . 'xwcj_model';
                        $fields_buf = array(
                            'site_id',
                            'car_id',
                            'user_id',
                            'exam_id',
                            'sig1',
                            'sig2',
                            'sig3',
                            'sig4',
                            'sig5',
                            'sig6',
                            'sig7',
                            'sig8',
                            'sig9',
                            'sig10',
                            'sig11',
                            'sig12',
                            'sig13',
                            'sig14',
                            'sig15',
                            'sig16',
                            'sig17',
                            'sig18',
                            'sig19',
                            'sig20',
                            'sig21',
                            'sig22',
                            'sig23',
                            'sig24',
                            'sig25',
                            'sig26',
                            'sig27',
                            'sig28',
                            'sig29',
                            'time_interval',
                        );
                        if ( !isset($sql[$table_head]) ) {
                            $sql[$table_head] = " INSERT INTO `{$table}` (`".implode("`,`", $fields_buf)."`) VALUES ";
                        }
                        $sql[$table_head] .= " ('".implode("','", $original_data)."'), ";
                        break;
                    default: break;
                }
            }

            foreach ( $sql as $key => $val) {
                $long_sql = substr($val, 0, -2);
                $stmt = $db->query($long_sql);
            }
            return $response->withJson(array('code' => '200', 'data' => $stmt), 200);

            //关闭数据库
            $db = null;
        } catch ( PDOException $e ) {
            setapilog('[comment_like] [:error] [client ' . $request->getAttribute('ip_address') . '] [user_id % ' . $user_id . '] ['. $e->getLine().' ' .$e->getMessage().']');
            $data = array('code' => 1, 'data' => '网络异常');
            return $response->withJson($data, 200);
        }
    }
?>
