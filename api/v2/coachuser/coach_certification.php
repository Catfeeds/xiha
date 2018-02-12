<?php
    /**
    * 教练在嘻哈平台认证接口
    * @param    string  $coach_phone            教练电话
    * @param    int     $coach_id               教练id
    * @param    IMAGE   $coach_license          教练证正面照
    * @param    IMAGE   $id_card                身份证正面照
    * @param    IMAGE   $personal_image         个人形象照
    * @param    IMAGE   $coach_car              教练车正面照
    * @return   json
    * @package  /api/v2/coachuser
    * @author   gdc
    * @date     July 25, 2016
    **/

    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'coach_certification');
    $app->run();

    function coach_certification() {
        global $app, $crypt;

        //验证请求方式 POST
        $req = $app->request();
        $res = $app->response();
        if ( !$req->isPost() ) {
            slimLog($req, $res, null, '此接口仅开放POST方式请求');
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }

        //取得参数列表
        $validate_ok = validate(array('coach_id' => 'INT', 'coach_phone' => 'STRING'), $req->params());
        if ( !$validate_ok['pass'] ) {
            slimLog($req, $res, null, '参数不完整或参数类型不对，请核对文档与您传的参数');
            ajaxReturn($validate_ok['data']);
        }

        $p = $req->params();
        $coach_id = $p['coach_id'];
        $coach_phone = $p['coach_phone'];

        //ready to return
        $data = array();
        try {
            // Open connection with mysql
            $db = getConnection();

            // if coach does not exist
            $coach_tbl = DBPREFIX.'coach';
            $sql = " SELECT `certification_status` FROM `{$coach_tbl}` WHERE `l_coach_id` = :coach_id AND `s_coach_phone` = :coach_phone ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('coach_id', $coach_id);
            $stmt->bindParam('coach_phone', $coach_phone);
            $stmt->execute();
            $coach_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!isset($coach_info['certification_status'])) {
                $data['code'] = 103;
                $data['data'] = '不存在的教练';
                slimLog($req, $res, null, '试图认证不存在的教练');
                $db = null;
                ajaxReturn($data);
            } elseif ($coach_info['certification_status'] == 3 && empty($_FILES)) {
                $data['code'] = 200;
                $data['data'] = array(
                    'coach_info' => array(
                        'certification_status' => 3,
                        'certification_text' => '已认证',
                    ),
                );
                $db = null;
                ajaxReturn($data);
            } elseif ($coach_info['certification_status'] == 2) {
                $data['code'] = 200;
                $data['data'] = array(
                    'coach_info' => array(
                        'certification_status' => 2,
                        'certification_text' => '认证中',
                    ),
                );
                $db = null;
                ajaxReturn($data);
            } elseif ($coach_info['certification_status'] == 1 && empty($_FILES)) {
                $data['code'] = 200;
                $data['data'] = array(
                    'coach_info' => array(
                        'certification_status' => 1,
                        'certification_text' => '未认证',
                    ),
                );
                $db = null;
                ajaxReturn($data);
            } elseif ($coach_info['certification_status'] == 4 && empty($_FILES)) {
                $data['code'] = 200;
                $data['data'] = array(
                    'coach_info' => array(
                        'certification_status' => 4,
                        'certification_text' => '认证失败',
                    ),
                );
                $db = null;
                ajaxReturn($data);
            } elseif (empty($_FILES)) {
                $data['code'] = 200;
                $data['data'] = array(
                    'coach_info' => array(
                        'certification_status' => 1,
                        'certification_text' => '未认证',
                    ),
                );
                $db = null;
                ajaxReturn($data);
            }

            // s.1 handle 4 picture upload
            $path = 'upload'.DIRECTORY_SEPARATOR.'coach'.DIRECTORY_SEPARATOR.$coach_id.DIRECTORY_SEPARATOR.date('Ymd', time());
            $coach_license_imgurl = handle_upload_picture($path, 'coach_license', 'coach_license_');
            $id_card_imgurl = handle_upload_picture($path, 'id_card', 'id_card_');
            $personal_image_url = handle_upload_picture($path, 'personal_image', 'personal_image_');
            $coach_car_imgurl = handle_upload_picture($path, 'coach_car', 'coach_car_');

            // s.2 save url to mysql
            $update_flag = false;
            if (!isset($coach_tbl)) {
                $coach_tbl = DBPREFIX.'coach';
            }
            $sql = " UPDATE `{$coach_tbl}` SET ";
            if (1) {
                if ($update_flag) {
                    $sql .= " , ";
                }
                $sql .= " `updatetime` = :t";
                $update_flag = true;
            }
            if ($coach_license_imgurl) {
                if ($update_flag) {
                    $sql .= " , ";
                }
                $sql .= " `coach_license_imgurl` = :coach_license";
                $update_flag = true;
            }
            if ($id_card_imgurl) {
                if ($update_flag) {
                    $sql .= " , ";
                }
                $sql .= " `id_card_imgurl` = :id_card";
                $update_flag = true;
            }
            if ($personal_image_url) {
                if ($update_flag) {
                    $sql .= " , ";
                }
                $sql .= " `personal_image_url` = :personal_image";
                $update_flag = true;
            }
            if ($coach_car_imgurl) {
                if ($update_flag) {
                    $sql .= " , ";
                }
                $sql .= " `coach_car_imgurl` = :coach_car";
                $update_flag = true;
            }
            if (!$update_flag) {
                $data['code'] = 400;
                $data['data'] = '没有收到认证所需要的图片';
                slimLog($req, $res, null, '没有收到认证所需要的图片');
                $db = null;
                ajaxReturn($data);
            }
            $sql .= " , `certification_status` = 2 ";
            $sql .= " WHERE `l_coach_id` = :coach_id AND `s_coach_phone` = :coach_phone ";
            $stmt = $db->prepare($sql);
            if (1) {
                $t = time();
                $stmt->bindParam('t', $t);
            }
            if ($coach_license_imgurl) {
                $stmt->bindParam('coach_license', $coach_license_imgurl);
            }
            if ($id_card_imgurl) {
                $stmt->bindParam('id_card', $id_card_imgurl);
            }
            if ($personal_image_url) {
                $stmt->bindParam('personal_image', $personal_image_url);
            }
            if ($coach_car_imgurl) {
                $stmt->bindParam('coach_car', $coach_car_imgurl);
            }
            $stmt->bindParam('coach_id', $coach_id);
            $stmt->bindParam('coach_phone', $coach_phone);
            $stmt->execute();

            // Check if update ok
            if (!isset($coach_tbl)) {
                $coach_tbl = DBPREFIX.'coach';
            }
            $sql = " SELECT `certification_status` FROM `{$coach_tbl}` WHERE `l_coach_id` = :coach_id AND `s_coach_phone` = :coach_phone ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('coach_id', $coach_id);
            $stmt->bindParam('coach_phone', $coach_phone);
            $stmt->execute();
            $coach_info_updated = $stmt->fetch(PDO::FETCH_ASSOC);
            if (isset($coach_info_updated['certification_status'])) {
                $data['code'] = 200;
                $data['data'] = array('coach_info' => $coach_info_updated);
            } else {
                $data['code'] = 400;
                $data['data'] = '教练认证过程出现问题';
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

    // save picture
    function handle_upload_picture($path, $picture_name, $save_prefix) {
        if (!empty($_FILES[$picture_name]) && $_FILES[$picture_name]['error'] == UPLOAD_ERR_OK) {
            $tmp_name = $_FILES[$picture_name]['tmp_name'];
            $name = $save_prefix . time();
            if (isset($_FILES[$picture_name]['type'])) {
                $mime = $_FILES[$picture_name]['type'];
                $ext = explode('/', $mime);
                if (isset($ext[1]) && $ext[0] == 'image') {
                    $name .= '.' . $ext[1];
                } else {
                    $name .= '.png';
                }
            } else {
                $name .= '.png';
            }

            if (!file_exists('../../../' . $path)) {
                mkdir('../../../' . $path, 0777, true);
            }
            $saved_ok = move_uploaded_file($tmp_name, "../../../$path/$name");
            if ($saved_ok) {
                $save_url = $path . '/' . $name;
            } else {
                $save_url = '';
            }
            return $save_url;
        }
        return false;
    }
    // save picture
?>
