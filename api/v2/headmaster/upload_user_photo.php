<?php
    /**
     * 上传用户头像接口
     * @param integer $user_id 用户id
     * @package api/v2/headmaster
     * @author gaodacheng
     * @date 2016-04-11
     */

    require '../../Slim/Slim.php';
    require '../../include/common.php';
    require '../../include/crypt.php';
    require '../../include/functions.php';

    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->any('/', 'uploadUserPhoto');
    $app->response->headers->set('Content-Type', 'application/json');
    $app->run();

    function uploadUserPhoto() {
        global $app, $crypt;

        //验证请求方式 POST
        $r = $app->request();
        if ( !$r->isPost() ) {
            setapilog('[update_user_info] [:error] [client ' . $r->getIp() . '] [method % ' . $r->getMethod() . '] [106 错误的请求方式]');
            exit( json_encode(array('code' => 106, 'data' => '请求错误')) );
        }

        //取得参数列表
        $validate_ok = validate(array('user_id' => 'INT'), $r->params());
        if ( !$validate_ok['pass'] ) {
            exit( json_encode($validate_ok['data']) );
        }

        //用户id
        $user_id = $r->params('user_id');
        /*
        setapilog('upload_user_photo: ' . serialize($_FILES));
        exit( json_encode( array('code' => 201, 'data' => 'I got your photo') ) );
        */

        //头像 _FILES
        if ( empty( $_FILES )  || empty( $_FILES['user_photo'] ) ) {
            exit( json_encode( array( 'code' => 101, 'data' => '参数错误') ) );
        }

        //保存
        $error = $_FILES['user_photo']['error'];
        if ( $error == UPLOAD_ERR_OK ) {
            $tmp_name = $_FILES['user_photo']['tmp_name'];
            $name = 'thumb' . time() . substr( str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6 ) . '.png';
            $path = 'upload/hmaster/thumb/' . $user_id . '/' . date('Ymd', time());
            if ( !file_exists( '../../../' . $path ) ) {
                //目录不存在，创建目录
                if ( mkdir( '../../../' . $path, 0777, true ) ) {
                    //移动文件到目标文件夹
                    $move_ok = move_uploaded_file($tmp_name, "../../../$path/$name");
                } else {
                    exit( json_encode( array( 'code' => '400', 'data' => '上传头像失败' ) ) );
                }
            } else {
                //已经存在目录，移动文件到目标文件夹
                $move_ok = move_uploaded_file($tmp_name, "../../../$path/$name");
            }
        }

        try {
            //建立数据库连接
            $db = getConnection();
            $sql = " UPDATE `" . DBPREFIX . "user` SET `s_imgurl` = :user_photo WHERE `l_user_id` = :id ";
            $stmt = $db->prepare($sql);
            $savename = "$path/$name";
            $stmt->bindParam('id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam('user_photo', $savename, PDO::PARAM_STR);
            $update_ok = $stmt->execute();
            //关闭数据库
            $db = null;
            if ( $move_ok && $update_ok ) {
                exit( json_encode(array('code' => 200, 'data' => HOST . "/{$savename}")) );
            } else {
                exit( json_encode(array('code' => 400, 'data' => '头像修改失败')) );
            }
        } catch ( PDOException $e ) {
            setapilog('[comment_like] [:error] [client ' . $r->getIP() . '] [user_id,type % ' . $user_id . '] [1 网络异常]');
            exit( json_encode(array('code' => 1, 'data' => '网络异常')) );
        }
    }
?>
