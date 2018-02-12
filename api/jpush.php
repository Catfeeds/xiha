<?php

	// 学员端消息推送
	require_once 'vendor/autoload.php';

	use JPush\Model as M;
	use JPush\JPushClient;
	use JPush\Exception\APIConnectionException;
	use JPush\Exception\APIRequestException;

	$br = '<br/>';

	// 教练端
	$app_key = '3ebbbf7c2e811171a6e5c836';
	$master_secret = 'b4272f005b740f30d49a6758';

	// 学员端
	$app_key = 'c1b9d554f52b5668cba58c75';
	$master_secret = '68ce861810390d1e88112310';
	// $app_key = 'def2acfdfdb1b7bef4f89a33';
	// $master_secret = '6158449e5224d14ef702858d';
	// $app_key = '10b28c06ed9479354c8066ec';
	// $master_secret = '8f8e9eeeb71e77f9c0f764c5';
	$client = new JPushClient($app_key, $master_secret);

	try {
		// $payload = M\audience(
		//     M\tag(array("tag1", "tag2")),
		//     M\tag_and(array("tag3")),
		//     M\alias(array("alias1", "alias2")),
		//     M\registration_id(array("id1", "id2"))); //15056032300学员 18355181375教练
		$payload = M\audience(M\alias(array('15056032300')));
	    $result = $client->push()
	        ->setPlatform(M\all)
	        ->setAudience($payload)
	        ->setNotification(M\notification('消息推送测试！', M\ios("Hi, IOS", "happy", 1, true), M\android('Hi, android')))
	        ->send();

	    // print_r($result);
	    
	    echo 'Push Success.' . $br;
	    echo 'sendno : ' . $result->sendno . $br;
	    echo 'msg_id : ' .$result->msg_id . $br;
	    echo 'Response JSON : ' . $result->json . $br;
	} catch (APIRequestException $e) {
	    echo 'Push Fail.' . $br;
	    echo 'Http Code : ' . $e->httpCode . $br;
	    echo 'code : ' . $e->code . $br;
	    echo 'message : ' . $e->message . $br;
	    echo 'Response JSON : ' . $e->json . $br;
	    echo 'rateLimitLimit : ' . $e->rateLimitLimit . $br;
	    echo 'rateLimitRemaining : ' . $e->rateLimitRemaining . $br;
	    echo 'rateLimitReset : ' . $e->rateLimitReset . $br;
	} catch (APIConnectionException $e) {
	    echo 'Push Fail.' . $br;
	    echo 'message' . $e->getMessage() . $br;
	}
	
	// try {
	//     $result = $client->push()
	//         ->setPlatform(M\platform('ios', 'android'))
	//         ->setAudience(M\audience(M\tag(array('555','666')), M\alias(array('555', '666'))))
	//         ->setNotification(M\notification('Hi, JPush', M\android('Hi, android'), M\ios('Hi, ios', 'happy', 1, true, null, 'THE-CATEGORY')))
	//         ->setMessage(M\message('msg content', null, null, array('key'=>'value')))
	//         ->setOptions(M\options(123456, null, null, false, 0))
	//         ->printJSON()
	//         ->send();

	//     echo 'Push Success.' . $br;
	//     echo 'sendno : ' . $result->sendno . $br;
	//     echo 'msg_id : ' .$result->msg_id . $br;
	//     echo 'Response JSON : ' . $result->json . $br;
	// } catch (APIRequestException $e) {
	//     echo 'Push Fail.' . $br;
	//     echo 'Http Code : ' . $e->httpCode . $br;
	//     echo 'code : ' . $e->code . $br;
	//     echo 'message : ' . $e->message . $br;
	//     echo 'Response JSON : ' . $e->json . $br;
	//     echo 'rateLimitLimit : ' . $e->rateLimitLimit . $br;
	//     echo 'rateLimitRemaining : ' . $e->rateLimitRemaining . $br;
	//     echo 'rateLimitReset : ' . $e->rateLimitReset . $br;
	// } catch (APIConnectionException $e) {
	//     echo 'Push Fail: ' . $br;
	//     echo 'Error Message: ' . $e->getMessage() . $br;
	//     //response timeout means your request has probably be received by JPUsh Server,please check that whether need to be pushed again.
	//     echo 'IsResponseTimeout: ' . $e->isResponseTimeout . $br;
	// }
?>