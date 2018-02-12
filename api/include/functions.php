<?php

/*
 * 接口数据返回，默认JSON
 * @param   array   $data
 * @param   string  $type   (JSON, XML)
 */
function ajaxReturn($data = array(), $type = '') {
    //if (empty($type)) $type = 'JSON';
    $type = 'json';
    switch (strtoupper($type)) {
        case 'JSON' :
            //返回JSON数据格式到客户端 包含状态信息
            header('Content-Type:application/json; charset=utf-8');
            if (is_array($data)) {
                exit(json_encode($data));
            } elseif (is_string($data)) {
                exit($data);
            }
    }
}

/*
 * Slim:API日志记录
 * @param   Slim\Http\Request   $req
 * @param   Slim\Http\Response  $res
 * @param   PDOException        $e
 * @param   string              $msg
 * @return  bool                (true|false)
 * @author  gdc
 * @date    June 23, 2016
 */
function slimLog ($req, $res, $e = null, $msg = null) {
    $fn = 'apilog.txt';
    if ( file_exists($fn) ) {
        $fp = fopen($fn, 'a');
    } else {
        $fp = fopen($fn, 'w');
    }

    $date = date('c', time());
    
    if ( !is_null($msg) ) {
        $msg = ' ' . $msg;
    }

    $str = '';
    if ( $req instanceof \Slim\Http\Request 
         && $res instanceof \Slim\Http\Response 
    ) {
        if ( is_null($e) ) {
            $str = $req->getIp() . ' [' . $date . '] ' . '"' . $req->getMethod() . ' ' . $req->getScriptName() . ' '.json_encode($req->params()).'" '. $res->getStatus() . ' "'.$req->getUserAgent().'"' . $msg . "\n" ;
        } else if ( $e ) {
            $str = $req->getIp() . ' [' . $date . '] ' . '"' . $req->getMethod() . ' ' . $req->getScriptName() . ' '.json_encode($req->params()).'" '. $res->getStatus() . ' "'.$req->getUserAgent().'" "' . $e->getLine() . ' ' . $e->getMessage() . '"' . $msg . "\n" ;
        }
    } else {
        $str = $date . ' ' . $msg . "\n";
    }

    $save_ok = fwrite($fp, $str);

    fclose($fp);

    return $save_ok;
}


/*
 * 对接口请求的参数进行验证
 * @param array $params 需要验证的字段及验证条件
 * @param array $req 待验证的字段值列表
 * @return array('pass' => true|false, 'data' => array err_msg )
 * @author Gao Dcheng
 */

/*
 * condition | type
 * integer   | INT
 * string    | STRING
 * not null  | NOT_NULL
 */
function validate($params = array(), $req = array()) {
    if ( !is_array($params) || empty($params) || !is_array($req) || empty($req) ) {
        //return false; //调用传参不完整
        return array(
            'pass' => false, 
            'data' => array('code'=>101, 'data'=>'参数错误')); 
            //调用传参不完整
    }

    foreach ( $params as $field => $condition) {
        if ( !in_array($field, array_keys($req), true) ) {
            $err_msg = array('code' => 102, 'data' => '参数错误');
            return array('pass' => false, 'data' => $err_msg);
        }

        switch ($condition) {
            case 'INT': 
                if ( !is_numeric($req[$field]) ) {
                    $err_msg = array('code' => 102, 'data' => '参数错误');
                    return array('pass' => false, 'data' => $err_msg);
                }
                break;
            case 'STRING': 
            case 'NOT_NULL': 
                if ( !is_string($req[$field]) ) {
                    $err_msg = array('code' => 102, 'data' => '参数错误');
                    return array('pass' => false, 'data' => $err_msg);
                }
                break;
        }
    }

    return array('pass' => true, 'data' => 'all validation passed');
}
