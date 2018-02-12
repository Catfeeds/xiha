<?php
    /**
     * 编辑学员基本信息
     * @param   int     $coach_id           教练ID
     * @param   int     $user_id            学员ID
     * @param   string  $user_name          学员姓名
     * @param   string  $identity_id        身份证
     * @param   string  $user_phone         学员号码
     * @param   int     $school_id          学员报名的驾校id
     * @param   int     $stage              [optional]学习阶段（1：待定 2：科目二 3：科目三 4：毕业）
     * @param   int     $photo_id           [optional]学员头像ID (1,2,3...16)
     * @param   FILE    $photo_img          [optional]学员上传本地图片作为头像
     * @param   FILE    $identity_img       [optional]学员的身份证照片
     * @return  json
     * @author  wl
     * @date    July 11, 2016
     * @update  July 13, 2016 [gdc]
     * @update  July 20, 2016 [gdc 将旧的图片删除，附上侵占硬盘空间]
     **/
    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';
    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/','editUserBaseInfo');
    $app->run();

    function editUserBaseInfo() {
        Global $app, $crypt;
        $req = $app->request();
        $res = $app->response();
        if(!$req->isPost()) {
            slimLog($req,$res,null,'此接口仅开放POST请求');
            $data = array('code' => 106, 'data' => '请求错误');
            ajaxReturn($data);
        }

        //   取得参数列表
        $validate_result = validate (
            array(
                'coach_id'          => 'INT',
                'user_id'           => 'INT',
                'user_name'         => 'STRING',
                'user_phone'        => 'STRING',
            ),$req->params());
        if(!$validate_result['pass']) {
            slimLog($req, $res, null, '参数不完整或类型不对');
            $data = $validate_result['data'];
            $db = null;
            ajaxReturn($data);
        }

        $p              = $req->params();
        $coach_id       = $p['coach_id'];
        $user_id        = $p['user_id'];
        $user_phone     = $p['user_phone'];
        $user_name      = $p['user_name'];
        $identity_id    = isset($p['identity_id']) ? $p['identity_id'] : '';
        $identity_img   = ''; // url
        $stage          = isset($p['stage']) ? $p['stage'] : '1'; //如果没有传值，状态为‘待定’
        $photo_id       = isset($p['photo_id']) ? (int)$p['photo_id'] : 0;
        $photo_img      = ''; // url
        $school_id      = isset($p['school_id']) ? (int)$p['school_id'] : 0;
        $updatetime     = time();

        try{
            $db = getConnection();
            $coach_users_records = DBPREFIX.'coach_users_records';

            //  判断学员是否存在
            $sql = " SELECT 1 FROM `{$coach_users_records}` WHERE `coach_users_id` = :uid AND `coach_id` = :cid";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('uid',$user_id, PDO::PARAM_INT);
            $stmt->bindParam('cid',$coach_id, PDO::PARAM_INT);
            $stmt->execute();
            $coach_users_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$coach_users_info) {
                $data = array('code' => 103, 'data' => '不存在此学员');
                $db = null;
                ajaxReturn($data);
            }

            //处理图片上传
            if (!empty($_FILES)) {
                // 图片处理一 身份证图片
                if (!empty($_FILES['identity_img']) && $_FILES['identity_img']['error'] == UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES['identity_img']['tmp_name'];
                    $name = 'identity_' . $coach_id . '_' . $user_id . '_' . $updatetime;
                    if (isset($_FILES['identity_img']['type'])) {
                        $mime = $_FILES['identity_img']['type'];
                        $ext = explode('/', $mime);
                        if (isset($ext[1])) {
                            $name .= '.' . $ext[1];
                        } else {
                            $name .= '.png';
                        }
                    } else {
                        $name .= '.png';
                    }
                    $path = 'upload/coachuser/identity/' . date('Ymd', $updatetime);

                    //del old picture
                    $existed_picture = glob(realpath('../../../' . 'upload/coachuser/identity/') . '/*/identity_'.$coach_id.'_'.$user_id.'_*.*');
                    if ($existed_picture) {
                        foreach ($existed_picture as $picture) {
                            unlink($picture);
                        }
                    }
                    //del old picture

                    if (!file_exists('../../../' . $path)) {
                        mkdir('../../../' . $path, 0777, true);
                    }
                    $saved_ok = move_uploaded_file($tmp_name, "../../../$path/$name");
                    if ($saved_ok) {
                        $identity_img = $path . '/' . $name;
                    }
                } // identity_img 保存

                // 图片处理二 用户本地上传头像
                if (!empty($_FILES['photo_img']) && $_FILES['photo_img']['error'] == UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES['photo_img']['tmp_name'];
                    $name = 'userphoto_' . $coach_id . '_' . $user_id . '_' . $updatetime;
                    if (isset($_FILES['photo_img']['type'])) {
                        $mime = $_FILES['photo_img']['type'];
                        $ext = explode('/', $mime);
                        if (isset($ext[1])) {
                            $name .= '.' . $ext[1];
                        } else {
                            $name .= '.png';
                        }
                    } else {
                        $name .= '.png';
                    }
                    $path = 'upload/coachuser/userphoto/' . date('Ymd', $updatetime);

                    //del old picture
                    $existed_picture = glob(realpath('../../../' . 'upload/coachuser/userphoto/') . '/*/userphoto_'.$coach_id.'_'.$user_id.'_*.*');
                    if ($existed_picture) {
                        foreach ($existed_picture as $picture) {
                            unlink($picture);
                        }
                    }
                    //del old picture

                    if (!file_exists('../../../' . $path)) {
                        mkdir('../../../' . $path, 0777, true);
                    }
                    $saved_ok = move_uploaded_file($tmp_name, "../../../$path/$name");
                    if ($saved_ok) {
                        $photo_img = $path . '/' . $name;
                    }
                } // photo_img 保存
            } //图片上传

            // 更新用户信息
            $sql = " UPDATE `{$coach_users_records}` SET `user_name` = :uname,`user_phone` = :uphone,`i_stage` = :stage,`identity_id` = :identity, `updatetime` = :ut ";
            $sql .= " , `photo_id` = :photo_id ";
            if ($photo_img || $photo_id != 0) {
                $sql .= " , `photo_img` = :photo_img ";
            }
            if ($identity_img) {
                $sql .= " , `identity_img` = :identity_img ";
            }
            $where = " WHERE `coach_users_id` = :uid AND `coach_id` = :cid";
            $sql .= $where;
            $stmt = $db->prepare($sql);
            $stmt->bindParam('uid', $user_id);
            $stmt->bindParam('cid', $coach_id);
            $stmt->bindParam('uname', $user_name);
            $stmt->bindParam('uphone', $user_phone);
            $stmt->bindParam('stage', $stage);
            if ($photo_id != 0) {
                $photo_img = '';
            }
            if ($photo_img) {
                $photo_id = 0;
            }
            $stmt->bindParam('photo_id', $photo_id);
            if ($photo_img || $photo_id != 0) {
                $stmt->bindParam('photo_img', $photo_img);
            }
            $stmt->bindParam('identity', $identity_id);
            if ($identity_img) {
                $stmt->bindParam('identity_img', $identity_img);
            }
            $stmt->bindParam('ut', $updatetime);
            $result = $stmt->execute();

            if ($school_id != 0) {
                //coach_users TABLE
                // c.1 get school name according to school_id field in school table
                $tbl = DBPREFIX . 'school';
                $sql = " SELECT `s_school_name` AS school_name FROM `{$tbl}` WHERE `l_school_id` = :school_id ";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('school_id', $school_id);
                $stmt->execute();
                $school_info = $stmt->fetch(PDO::FETCH_ASSOC);
                // c.2 save school name and school id in coach_users table
                if (isset($school_info['school_name']) && $school_info['school_name']) {
                    $school_name = $school_info['school_name'];
                    $tbl = DBPREFIX.'coach_users';
                    $sql = " UPDATE `{$tbl}` SET `signup_school_id` = :school_id, `signup_school_name` = :school_name WHERE `id` = :user_id ";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam('school_id', $school_id);
                    $stmt->bindParam('school_name', $school_name);
                    $stmt->bindParam('user_id', $user_id);
                    $update_school_name = $stmt->execute();
                }
                //coach_users TABLE
            }

            if($result) {
                $data = array('code' => 200, 'data' => '保存成功');
            } else {
                $data = array('code' => 400, 'data' => '保存失败');
            }

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
     * 需要注意的地方:
     *
     * 1)
     * 如果photo_img传图片了，将photo_id清空为数字0.
     * 如果photo_img没有传图片过来，保留photo_id的值不变.
     *
     * 2)
     * 如果身份证图片没有上传图片，保持identity_img在数据库中的值不变.
    **/
?>
