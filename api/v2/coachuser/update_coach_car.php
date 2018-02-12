<?php
    /**
    * 教练端更新车辆信息
    * @param    int         $coach_id       教练id
    * @param    int         $car_id         车辆id
    * @param    string      $car_name       车名
    * @param    string      $car_no         车牌号
    * @param    json        $car_imgurl     车辆旧的图片url列表, json格式 ["url1", "url2"]
    * @param    FILE        $car_picture    车辆图片
    * @return   json
    * @package  /api/v2/coachuser
    * @author   gdc
    * @date     July 29, 2016
    **/

    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'update_coach_car');
    $app->run();

    function update_coach_car() {
        global $app, $crypt;

        //验证请求方式 POST
        $req = $app->request();
        $res = $app->response();
        slimLog($req, $res, null, serialize($_FILES));
        if ( !$req->isPost() ) {
            slimLog($req, $res, null, '需要POST');
            ajaxReturn(array('code' => 106, 'data' => '请求错误'));
        }

        //取得参数列表
        $validate_ok = validate(array(
            'coach_id'      => 'INT',
            'car_id'        => 'INT',
            'car_name'      => 'STRING',
            'car_no'        => 'STRING',
            'car_imgurl'    => 'STRING',
            'car_type'      => 'INT',
        ), $req->params());

        if ( !$validate_ok['pass'] ) {
            slimLog($req, $res, null, '参数不完整或参数类型不对，请核对文档与您传的参数');
            ajaxReturn($validate_ok['data']);
        }
        $p = $req->params();
        $coach_id = $p['coach_id'];
        $car_id = $p['car_id'];
        $car_name = $p['car_name'];
        $car_no = $p['car_no'];
        $car_imgurl_tmp = $p['car_imgurl'];
        $car_type = $p['car_type']; // 3 模拟车 1 普通车 2 加强版车

        //ready to return
        $data = array();
        try {
            // Open connection with mysql
            $db = getConnection();

            // get the right car
            $coach_tbl = DBPREFIX.'coach';
            $sql = " SELECT `s_coach_car_id` AS car_id, `s_school_name_id` AS school_id FROM `{$coach_tbl}` WHERE `l_coach_id` = :cid ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam('cid', $coach_id);
            $stmt->execute();
            $coach_info = $stmt->fetch(PDO::FETCH_ASSOC);
            if (empty($coach_info)) {
                $data['code'] = 103;
                $data['data'] = '教练不存在';
                slimLog($req, $res, null, '教练不存在');
                ajaxReturn($data);
            } elseif ((!isset($coach_info['car_id']) || $coach_info['car_id'] != $car_id) && $car_id != 0) {
                $data['code'] = 103;
                $data['data'] = '教练跟车辆的绑定信息不相符';
                slimLog($req, $res, null, '教练跟车辆的绑定信息不相符');
                ajaxReturn($data);
            }

            // make sure we can handle old car picture
            $old_car_imgurl = array_filter(json_decode($car_imgurl_tmp, true));
            if (!is_array($old_car_imgurl)) {
                $data['code'] = 102;
                $data['data'] = '参数错误';
                slimLog($req, $res, null, 'JSON需要双引号');
                ajaxReturn($data);
            }
            if (!empty($old_car_imgurl)) {
                foreach ($old_car_imgurl as $index => $value) {
                    $imgurl_prefix = '';
                    if (strpos($value, HTTP_HOST) !== false) {
                        $imgurl_prefix = HTTP_HOST;
                    } else if (strpos($value, S_HTTP_HOST) !== false) {
                        $imgurl_prefix = S_HTTP_HOST;
                    } else if (strpos($value, HOST) !== false) {
                        $imgurl_prefix = HOST;
                    }
                    $old_car_imgurl[$index] = substr($value, strlen($imgurl_prefix), strlen($value)-strlen($imgurl_prefix));
                }
            }

            // handle picture upload
            $new_car_imgurl = array();
            if ($_FILES && isset($_FILES['car_picture']) && is_array($_FILES['car_picture']['name'])) {
                $path = 'upload'.DIRECTORY_SEPARATOR.'car'.DIRECTORY_SEPARATOR.$coach_id.'_'.$car_id.DIRECTORY_SEPARATOR.date('Ymd', time());
                foreach ($_FILES['car_picture']['name'] as $index => $value) {
                    $new_car_imgurl[] = handle_upload_picture($path, $index, 'coach_car_');
                }
            }

            $car_imgurl = array_merge($old_car_imgurl, $new_car_imgurl);
            //保存到数据库的路径表示法不能因windows系统而异，即目录分隔符一律用 "/"
            if (is_array($car_imgurl)) {
                foreach ($car_imgurl as $index => $value) {
                    $car_imgurl[$index] = str_replace(DIRECTORY_SEPARATOR, "/", $value);
                }
            }
            $car_imgurl = json_encode($car_imgurl, JSON_UNESCAPED_SLASHES);

            // we can save now
            if ($car_id == 0) {
                $car_tbl = DBPREFIX.'cars';
                $sql = " INSERT INTO `{$car_tbl}` (`name`, `car_no`, `imgurl`, `car_type`, `school_id`, `addtime`) VALUES (:cname, :cno, :cpic, :ctype, :sid, :time) ";
                $now = time();
                $stmt = $db->prepare($sql);
                $stmt->bindParam('cname', $car_name);
                $stmt->bindParam('cno', $car_no);
                $stmt->bindParam('cpic', $car_imgurl);
                $stmt->bindParam('ctype', $car_type);
                $stmt->bindParam('sid', $coach_info['school_id']);
                $stmt->bindParam('time', $now);
                $stmt->execute();
                $new_car_id = $db->lastInsertId();
                if ($new_car_id) {
                    if (!isset($coach_tbl)) {
                        $coach_tbl = DBPREFIX.'coach';
                    }
                    $sql = " UPDATE `{$coach_tbl}` SET `s_coach_car_id` = :carid WHERE `l_coach_id` = :cid ";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam('carid', $new_car_id);
                    $stmt->bindParam('cid', $coach_id);
                    $stmt->execute();

                    $sql = " SELECT `s_coach_car_id` AS car_id FROM `{$coach_tbl}` WHERE `l_coach_id` = :cid ";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam('cid', $coach_id);
                    $stmt->execute();
                    $car_info = $stmt->fetch(PDO::FETCH_ASSOC);;
                    if (isset($car_info['car_id']) && $car_info['car_id'] == $new_car_id) {
                        $data['code'] = 200;
                    } else {
                        $data['code'] = 400;
                    }
                }
            } else {
                $new_car_id = $car_id;
                $car_tbl = DBPREFIX.'cars';
                $sql = " UPDATE `{$car_tbl}` SET `name` = :cna, `car_no` = :cno, `imgurl` = :cp, `car_type` = :ctype WHERE `id` = :cid ";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('cna', $car_name);
                $stmt->bindParam('cno', $car_no);
                $stmt->bindParam('cp', $car_imgurl);
                $stmt->bindParam('ctype', $car_type);
                $stmt->bindParam('cid', $new_car_id);
                $update_ok = $stmt->execute();
                if ($update_ok) {
                    $data['code'] = 200;
                } else {
                    $data['code'] = 400;
                }
            }
            if (isset($data['code']) && $data['code'] == 200) {
                $car_tbl = DBPREFIX.'cars';
                $sql = " SELECT `id` AS car_id, `car_no`, `name` AS car_name, `imgurl`, `car_type`, `school_id` FROM `{$car_tbl}` WHERE `id` = :cid ";
                $stmt = $db->prepare($sql);
                $stmt->bindParam('cid', $new_car_id);
                $stmt->execute();
                $new_car_info = $stmt->fetch(PDO::FETCH_ASSOC);;

                $imgurl = json_decode($new_car_info['imgurl'], true);
                $imgurl_arr = array();
                if(is_array($imgurl) && !empty($imgurl)) {
                    foreach ($imgurl as $k => $v) {
                        if(file_exists(__DIR__.'/../../../sadmin/'.$v)) {
                            $imgurl_arr[] = S_HTTP_HOST.$v;
                        } elseif(file_exists(__DIR__.'/../../../admin/'.$v)) {
                            $imgurl_arr[] = HTTP_HOST.$v;
                        } elseif(file_exists(__DIR__.'/../../../'.$v)) {
                            $imgurl_arr[] = HOST.$v;
                        }
                    }
                }
                $new_car_info['imgurl'] = $imgurl_arr;
                $new_car_info['car_type_name'] = '';
                switch ($new_car_info['car_type']) {
                case '1':
                    $new_car_info['car_type_name'] = '普通车型';
                    break;
                case '2':
                    $new_car_info['car_type_name'] = '加强车型';
                    break;
                case '3':
                    $new_car_info['car_type_name'] = '模拟车型';
                    break;
                default:
                    $new_car_info['car_type_name'] = '普通车型';
                    break;
                }
                $data['data'] = array('car_info' => $new_car_info);
            } else {
                $data['data'] = '失败';
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
        if (!empty($_FILES['car_picture']) && $_FILES['car_picture']['error'][$picture_name] == UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['car_picture']['tmp_name'][$picture_name];
            $name = $save_prefix . time() . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVMXYZ'),0,6);
            if (isset($_FILES['car_picture']['type'][$picture_name])) {
                $mime = $_FILES['car_picture']['type'][$picture_name];
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
    /*
     * 图片存放路径样式
     * upload/car/548_709/20160802/coach_car_1470107804.png
     * upload/car/[coach_id]_[car_id]/[Y-m-d]/carch_car_[timestamp].png
     * [] 中内容被实际值替换
    **/
?>
