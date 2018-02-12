<?php  
    /**
     * 更新学员用户信息
     * @param $user_name string 学员姓名
     * @param $sex int 学员性别 1：男 2：女 0：未知
     * @param $age int 年龄
     * @param $address string 地址
     * @param $i_card string 身份证号码
     * @param $license_num int 几次领证
     * @param $user_id int 用户ID
     * @return string AES对称加密（加密字段xhxueche）
     * @author chenxi
     **/

    require 'Slim/Slim.php';
    require 'include/common.php';
    require 'include/crypt.php';
    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->post('/','updateuserinfo');
    $app->run();

    // 完成学员信息
    function updateuserinfo() {
        global $app, $crypt;
        $request = $app->request();
        $user_name = $request->params('user_name');
        $real_name = $request->params('real_name');
        $sex = $request->params('sex');
        $age = $request->params('age');
        $address = $request->params('address');
        $i_card = $request->params('i_card');
        $license_num = $request->params('license_num');
        $user_id = $request->params('user_id');
        $photo_id = $request->params('photo_id');

        $user_name = empty($user_name) ? '' : $user_name;
        $real_name = empty($real_name) ? '' : $real_name;
        $sex = empty($sex) ? '' : $sex;
        $age = empty($age) ? '' : $age;
        $address = empty($address) ? '' : $address;
        $i_card = empty($i_card) ? '' : $i_card;
        $license_num = empty($license_num) ? 0 : $license_num;
        $user_id = empty($user_id) ? '' : $user_id;
        $photo_id = empty($photo_id) ? '' : $photo_id;

        //setapilog('update_user_info:params[user_name:'.$user_name.',real_name:'.$real_name.',sex:'.$sex.',age:'.$age.',address:'.$address.',i_card:'.$i_card.',license_num:'.$license_num.',user_id:'.$user_id.'], error:');    
        try {
            $sql = "UPDATE `cs_user` SET `s_username` = :user_name, `s_real_name` = :real_name WHERE `l_user_id` = :user_id";
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('user_name', $user_name);
            $stmt->bindParam('real_name', $real_name);
            $stmt->bindParam('user_id', $user_id);
            $res = $stmt->execute();

            if($res) {
                // 查找当前有没有用户信息
                $sql = "SELECT * FROM `cs_users_info` WHERE `user_id` = $user_id";
                $stmt = $db->query($sql);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if($row) {
                    $sql = "UPDATE `cs_users_info` SET `sex` = '".$sex."', `age` = '".$age."', `identity_id` = '".$i_card."', `address` = '".$address."', `license_num` = '".$license_num."', `photo_id` = '".$photo_id."' WHERE `user_id` = '".$user_id."'";                        
                    //setapilog($sql);
                } else {
                    $sql = "INSERT INTO `cs_users_info` (`user_id`, `sex`, `age`, `identity_id`, `address`, `license_num`, `photo_id`) VALUES ('".$user_id."', '".$sex."', '".$age."', '".$i_card."', '".$address."', '".$license_num."', '".$photo_id."')";
                }
    
                $query = $db->query($sql);
                if($query) {
                    $data = array('code'=>200, 'data'=>'更新成功');
                } else {
                    $data = array('code'=>-1, 'data'=>'更新失败');
                }
            } else {
                $data = array('code'=>-2, 'data'=>'更新失败');
            }
            echo json_encode($data);
            exit;
        }catch(PDOException $e) {
            // $data = array('code'=>1, 'data'=>$e->getMessage());
            setapilog('update_user_info:params[user_name:'.$user_name.',real_name:'.$real_name.',sex:'.$sex.',age:'.$age.',address:'.$address.',i_card:'.$i_card.',license_num:'.$license_num.',user_id:'.$user_id.'], error:'.$e->getMessage());    
            $data = array('code'=>1, 'data'=>'网络错误');
            echo json_encode($data);
            exit;
        }

    }
?>
