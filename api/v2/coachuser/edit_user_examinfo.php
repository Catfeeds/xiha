<?php
    /**
    * 教练端编辑培训信息
    * @param    int     $coach_id           教练ID
    * @param    int     $user_id            学员ID
    * @param    int     $exam_records       包括考试的时间戳(exam_timestamp),科目(lesson)(2:科目二；3;科目三)
    * @return   json
    * @package  api/v2/coachuser
    * @author   wl
    * @date     July 11, 2016
    * @update   July 20, 2016 [gdc, bug fixed: 零条考试记录时sql语法错误]
    **/
    //exam_records样例
        /*
            [
                {
                    "exam_timestamp": 1468465621,
                    "lesson": 2
                },
                {
                    "exam_timestamp": 1468465621,
                    "lesson": 3
                },
                {
                    "exam_timestamp": 1468465621,
                    "lesson": 3
                }
            ]
        */

    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'editUserExamInfo');
    $app->run();

    function editUserExamInfo() {
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
                'coach_id'          => 'INT',
                'user_id'           => 'INT',
                'exam_records'      => 'STRING',
            ), $req->params());

        if ( !$validate_ok['pass'] ) {
            slimLog($req, $res,null,'参数不完整或类型不对');
            ajaxReturn($validate_ok['data']);
        }

        $p = $req->params();
        $coach_id       = $p['coach_id'];
        $coach_users_id = $p['user_id'];
        $exam_records   = $p['exam_records'];
        $updatetime     = time();
        $addtime        = time();
        //ready to return
        $data = array();
        try {
            // Open connection with mysql
            $db = getConnection();
            $coach_users_records = DBPREFIX.'coach_users_records';
            $coach_users_exam_records = DBPREFIX.'coach_users_exam_records';
            $exam_records_arr = json_decode($exam_records, true);

            //  判断学员是否存在
            $sql = " SELECT 1 FROM `{$coach_users_records}` WHERE `coach_users_id` = :uid AND `coach_id` = :cid";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('uid',$coach_users_id, PDO::PARAM_INT);
            $stmt->bindParam('cid',$coach_id, PDO::PARAM_INT);
            $stmt->execute();
            $coach_users_info = $stmt->fetch(PDO::FETCH_ASSOC);

            if(empty($coach_users_info)) {
                $data = array('code' => 103,'data' => '不存在此学员');
                $db = null;
                ajaxReturn($data);
            } 

            //del
            $tbl = DBPREFIX . 'coach_users_exam_records';
            $sql = " DELETE FROM `{$tbl}` WHERE `coach_users_id` = :uid AND `coach_id` = :cid ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('uid',$coach_users_id, PDO::PARAM_INT);
            $stmt->bindParam('cid',$coach_id, PDO::PARAM_INT);
            $stmt->execute();

            //save
            if(!$exam_records_arr) {
                $data = array('code'=>200, 'data'=>'保存成功');
                $db = null;
                ajaxReturn($data);
            } else {
                $sql = " INSERT INTO `{$coach_users_exam_records}` (`coach_id`,`coach_users_id`,`exam_timestamp`,`addtime`,`lesson`,`update_time`) VALUES ";
                foreach ($exam_records_arr as $key => $value) {
                    if ($key == 0) {
                        $sql .= " ( '{$coach_id}','{$coach_users_id}','{$value['exam_timestamp']}','{$addtime}','{$value['lesson']}','$updatetime' ) ";
                    } else {
                        $sql .= " , ( '{$coach_id}','{$coach_users_id}','{$value['exam_timestamp']}','{$addtime}','{$value['lesson']}','$updatetime' ) ";
                    }
                }
                $result = $db->query($sql);
                $data = array('code' => 200, 'data' => '保存成功');
                $db = null;
                ajaxReturn($data);
            }
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
?>
