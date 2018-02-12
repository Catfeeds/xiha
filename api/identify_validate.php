<?php  
    /**
     * 获取驾校详情
     * @param $id 驾校ID 1
     * @param $lng 学员经度 117.144356
     * @param $lat 学员维度 31.839411
     * @param $member_id int 学员或者教练ID
     * @return string AES对称加密（加密字段xhxueche）
     * @author chenxi
     **/

    require 'Slim/Slim.php';
    require 'include/common.php';
    require 'include/crypt.php';
    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->get('/id/:id','identify');
    $app->run();

    // 获取教练学员信息
    function identify($id) {
        if($id == '') {
            $data = array('code'=>-1, 'data'=>'请填写身份证');
            echo json_encode($data);
            exit();
        }   

        $ch = curl_init();
        $url = 'http://apis.baidu.com/apistore/idservice/id?id='.$id;
        $header = array(
            'apikey:3f476886841e800307821a2edb3b50c6',
        );
        // 添加apikey到header
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        curl_setopt($ch , CURLOPT_URL, $url);
        $res = curl_exec($ch);
        // var_dump(json_decode($res, true));
        echo $res;
        exit();
    }
?>