<!DOCTYPE html>
<html lang="zh">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="X-UA-Compatible" content="ie=edge" />
	<link rel="stylesheet" href="<?php echo base_url('assets/css/mui.min.css');?>" />
	<link rel="stylesheet" href="<?php echo base_url('assets/css/app.css');?>" />
	<link rel="stylesheet" href="<?php echo base_url('assets/css/mui.picker.css');?>" />
	<script src="<?php echo base_url('assets/js/fastclick.js'); ?>" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo base_url('assets/js/mui.min.js');?>" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo base_url('assets/js/velocity.min.js');?>" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo base_url('assets/js/vue.min.js');?>" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo base_url('assets/js/mui.picker.min.js');?>" type="text/javascript" charset="utf-8"></script>
	<title>电子教练考试记录</title>
	<script type="text/javascript">
		mui.init();
		window.addEventListener('load', function() {
			new FastClick(document.body);
		}, false);
	</script>
	<style type="text/css">
		[v-cloak] {display: none;}
		#info {
			/*padding: 20px 10px;*/
			margin-left:35%;
		}
	</style>
</head>
<body class="mui-fullscreen">