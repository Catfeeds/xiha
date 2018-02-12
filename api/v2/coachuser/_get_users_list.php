<?php  
    /**
     * 获取学员列表（练车和考试的都在一个接口）
     * @param integer $coach_id 教练id
     * @param string $type 学员列表类型 train练车，exam考试
     * @param integer $page 分页数字
     * @return 
     * @author gaodacheng
     **/
    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';
    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/','getUsersList');
    $app->run();

    function getUsersList() {
        Global $app, $crypt;
        $limit = 10;
        $page = 0;
        $r = $app->request();
        if ( !$r->isPost() ) {
            $data = array('code' => 106, 'data' => '请求错误');
            setapilog('[get_users_list] [:error] [client ' . $r->getIp() . '] [Method % ' . $r->getMethod() . '] [106 错误的请求方式]');
            exit(json_encode($data));
        }

        //  获取请求参数并判断合法性
        $validate_result = validate(
            array(
                'coach_id' => 'INT',
                'type' => 'STRING',
                'page' => 'INT',
            ), $r->params()
        );
        //参数检测出现问题则退出
        if (!$validate_result['pass']) {
            exit(json_encode($validate_result['data']));
        }
        $p = $r->params();
        $page = $p['page'];
        if ( $page === 0 ) {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $coach_id = $p['coach_id'];
        $type = $p['type'];
        $todayts = strtotime( date( 'Y-m-d', time() ) );

        try {
            $db = getConnection();
            
            //教练是否存在
            $sql = "SELECT 1 FROM `".DBPREFIX."coach` WHERE `l_coach_id` = '{$coach_id}' ";
            $stmt = $db->query($sql);
            $coach_exists = $stmt->fetch(PDO::FETCH_ASSOC);
            if ( $coach_exists === false ) {
                $data = array(
                    'code' => '103',
                    'data' => '参数错误',
                );
                setapilog('[get_users_list] [:warn] [param '.serialize($p).'] [103 教练不存在]');
                exit(json_encode($data));
            }
            
            //获取此教练所有关联学员
            $sql = " SELECT `coach_users_id` AS `id` FROM `".DBPREFIX."coach_users_relation` WHERE `coach_id` = '{$coach_id}' ";
            $stmt = $db->query($sql);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $user_list = [];
            foreach ( $res as $val ) {
                $user_list[] = $val['id'];
            }
            if ( !isset( $user_list ) || empty( $user_list ) ) {
                $data = [
                    'code' => '104',
                    'data' => '参数错误',
                ];
                setapilog('[get_users_list] [:warn] [param '.serialize($p).'] [104 教练无相关学员信息]');
                exit(json_encode($data));
            }

            if ( $type === "exam" ) {
                $sql = " SELECT `year`, `month`, `day`, `timestamp`, COUNT(id) AS `count` FROM `".DBPREFIX."coach_users_exam_records` WHERE `coach_users_id` IN ('".implode("','", $user_list)."') AND `timestamp` >= '{$todayts}' GROUP BY `timestamp` LIMIT $start, $limit ";
                $stmt = $db->query($sql);
                $exam_user_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ( !isset($exam_user_list) || empty($exam_user_list) ) {
                    $data = [
                        'code' => '104',
                        'data' => '教练无更多考试安排了',
                    ];
                    setapilog('[get_users_list] [:warn] [param '.serialize($p).'] [104 教练无更多考试安排了]');
                    exit(json_encode($data));
                }
                foreach ( $exam_user_list as $key => $val ) {
                    $sql = " SELECT `coach_users_id` FROM `".DBPREFIX."coach_users_exam_records` WHERE `coach_users_id` IN ('".implode("','", $user_list)."') AND `timestamp` = '{$val['timestamp']}' ";
                    $stmt = $db->query($sql);
                    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $coach_users_ids = [];
                    if ( !empty($res) ) {
                        foreach ( $res as $coach_user) {
                            $coach_users_ids[] = $coach_user['coach_users_id'];
                        }
                        $sql = " SELECT u.`id` AS `coach_users_id`, u.`user_phone` AS `phone`, u.`user_name` AS `name`, u.`user_photo` AS `photo`, r.`exam_stage` FROM `".DBPREFIX."coach_users` AS `u` LEFT JOIN `".DBPREFIX."coach_users_exam_records` AS `r` ON u.`id` = r.`coach_users_id` WHERE u.`id` IN ('".implode("','", $coach_users_ids)."') ";
                        $stmt = $db->query($sql);
                        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $exam_user_list[$key]['userlist'] = $users;
                    }
                }
                exit(json_encode($exam_user_list));
            } elseif ( $type === "train" ) {
                echo 'train223';
            }
            exit();

        } catch(PDOException $e) {
            setapilog('[get_users_list] [:error] [client ' . $r->getIp() . '] [params ' . serialize($r->params()) . '] [1 ' . $e->getMessage() . ']');
            $data = array('code'=>1, 'data'=>$e->getMessage());
            exit(json_encode($data));
        }
    }

?>
