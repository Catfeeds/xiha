<?php //提交短信
$post_data = array();
$post_data['userid'] = 1318;
$post_data['account'] = 'XHWL';
$post_data['password'] = '369852';
$post_data['content'] = '生日快乐【嘻哈学车】'; 
$post_data['mobile'] = '17355100855';
$post_data['extno'] = '';
$post_data['sendtime'] = ''; //不定时发送值为空，定时发送，输入格式YYYY-MM-DD HH：mm：ss的日期值
$url='http://115.29.242.32:8888/sms.aspx?action=send';
$o='';
foreach ($post_data as $k=>$v)
{
   $o.="$k=".urlencode($v).'&';
}
$post_data=substr($o,0,-1);
$ch = curl_init();
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果需要将结果直接返回到变量里，那加上这句。
$result = curl_exec($ch);
?>
