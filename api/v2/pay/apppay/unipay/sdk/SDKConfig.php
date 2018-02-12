<?php
 
// ######(以下配置为PM环境：入网测试环境用，生产环境配置见文档说明)#######
// 签名证书路径
// const SDK_SIGN_CERT_PATH = '../../certs_test/acp_test_sign.pfx';
const SDK_SIGN_CERT_PATH = 'certs/acp_xiha_sign.pfx';

// 签名证书密码
// const SDK_SIGN_CERT_PWD = '000000';
const SDK_SIGN_CERT_PWD = 'cf7652115';

// 密码加密证书（这条一般用不到的请随便配）
// const SDK_ENCRYPT_CERT_PATH = '../../certs_test/acp_test_enc.cer';
const SDK_ENCRYPT_CERT_PATH = 'certs/acp_prod_enc.cer';

// 验签证书路径（请配到文件夹，不要配到具体文件）
// const SDK_VERIFY_CERT_DIR = '../../certs_test/';
const SDK_VERIFY_CERT_DIR = 'certs/';

// 前台请求地址
// const SDK_FRONT_TRANS_URL = 'https://101.231.204.80:5000/gateway/api/frontTransReq.do';
const SDK_FRONT_TRANS_URL = 'https://gateway.95516.com/gateway/api/frontTransReq.do';

// 后台请求地址
// const SDK_BACK_TRANS_URL = 'https://101.231.204.80:5000/gateway/api/backTransReq.do';
const SDK_BACK_TRANS_URL = 'https://gateway.95516.com/gateway/api/backTransReq.do';

// 批量交易
// const SDK_BATCH_TRANS_URL = 'https://101.231.204.80:5000/gateway/api/batchTrans.do';
const SDK_BATCH_TRANS_URL = 'https://gateway.95516.com/gateway/api/batchTrans.do';

//单笔查询请求地址
// const SDK_SINGLE_QUERY_URL = 'https://101.231.204.80:5000/gateway/api/queryTrans.do';
const SDK_SINGLE_QUERY_URL = 'https://gateway.95516.com/gateway/api/queryTrans.do';

//文件传输请求地址
// const SDK_FILE_QUERY_URL = 'https://101.231.204.80:9080/';
const SDK_FILE_QUERY_URL = 'https://filedownload.95516.com/';

//有卡交易地址
// const SDK_Card_Request_Url = 'https://101.231.204.80:5000/gateway/api/cardTransReq.do';
const SDK_Card_Request_Url = 'https://gateway.95516.com/gateway/api/cardTransReq.do';

//App交易地址
// const SDK_App_Request_Url = 'https://101.231.204.80:5000/gateway/api/appTransReq.do';
const SDK_App_Request_Url = 'https://gateway.95516.com/gateway/api/appTransReq.do';

// 前台通知地址 (商户自行配置通知地址)
// const SDK_FRONT_NOTIFY_URL = 'http://localhost:8085/upacp_sdk_php/demo/api_05_app/FrontReceive.php';
// const SDK_FRONT_NOTIFY_URL = 'http://60.173.247.68:8081/api/v2/pay/apppay/unipay/notify_url.php';
const SDK_FRONT_NOTIFY_URL = 'http://180.153.52.71:8001/service/api/v2/pay/apppay/unipay/notify_url.php';

// 后台通知地址 (商户自行配置通知地址，需配置外网能访问的地址)
// const SDK_BACK_NOTIFY_URL = 'http://222.222.222.222/upacp_sdk_php/demo/api_05_app/BackReceive.php';
// const SDK_BACK_NOTIFY_URL = 'http://localhost/php/api/unipay/unipay_demo/demo/api_05_app/BackReceive.php';
// const SDK_BACK_NOTIFY_URL = 'http://60.173.247.68:8081/api/v2/pay/apppay/unipay/notify_url.php';
const SDK_BACK_NOTIFY_URL = 'http://180.153.52.71:8001/service/api/v2/pay/apppay/unipay/notify_url.php';

//文件下载目录 
// const SDK_FILE_DOWN_PATH = '../../file/';
const SDK_FILE_DOWN_PATH = 'file/';

//日志 目录 
// const SDK_LOG_FILE_PATH = '../../logs/';
const SDK_LOG_FILE_PATH = 'logs/';

//日志级别，关掉的话改PhpLog::OFF
const SDK_LOG_LEVEL = PhpLog::DEBUG;




/** 以下缴费产品使用，其余产品用不到，无视即可 */
// 前台请求地址
// const JF_SDK_FRONT_TRANS_URL = 'https://101.231.204.80:5000/jiaofei/api/frontTransReq.do';
const JF_SDK_FRONT_TRANS_URL = 'https://gateway.95516.com/jiaofei/api/frontTransReq.do';

// 后台请求地址
// const JF_SDK_BACK_TRANS_URL = 'https://101.231.204.80:5000/jiaofei/api/backTransReq.do';
const JF_SDK_BACK_TRANS_URL = 'https://gateway.95516.com/jiaofei/api/backTransReq.do';

// 单笔查询请求地址
// const JF_SDK_SINGLE_QUERY_URL = 'https://101.231.204.80:5000/jiaofei/api/queryTrans.do';
const JF_SDK_SINGLE_QUERY_URL = 'https://gateway.95516.com/jiaofei/api/queryTrans.do';

// 有卡交易地址
// const JF_SDK_CARD_TRANS_URL = 'https://101.231.204.80:5000/jiaofei/api/cardTransReq.do';
const JF_SDK_CARD_TRANS_URL = 'https://gateway.95516.com/jiaofei/api/cardTransReq.do';

// App交易地址
// const JF_SDK_APP_TRANS_URL = 'https://101.231.204.80:5000/jiaofei/api/appTransReq.do';
const JF_SDK_APP_TRANS_URL = 'https://gateway.95516.com/jiaofei/api/appTransReq.do';

?>
