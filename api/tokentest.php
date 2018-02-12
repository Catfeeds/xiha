<?php  
	require 'include/token.php';
	$token = new Form_token_Core();
	$grante_token = $token->grante_token('464564654');
	$res = $token->is_token('464564654', $grante_token);
	$token->drop_token('464564654');
	echo $grante_token;
?>