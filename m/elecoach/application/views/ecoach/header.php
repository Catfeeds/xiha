<!DOCTYPE html>
<html lang="zh">
<head>
	<meta charset="UTF-8" />
	<meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="yes" name="apple-touch-fullscreen">
    <meta content="telephone=no,email=no" name="format-detection">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta http-equiv="X-UA-Compatible" content="ie=edge" />
	<link rel="stylesheet" href="<?php echo base_url('assets/css/mui.min.css');?>" />
	<link rel="stylesheet" href="<?php echo base_url('assets/css/app.css');?>" />
	<link rel="stylesheet" href="<?php echo base_url('assets/css/mui.picker.min.css');?>" />
	<link rel="stylesheet" href="<?php echo base_url('assets/css/mui.dtpicker.css');?>" />
	<link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap.min.css');?>" />
	<link rel="stylesheet" href="<?php echo base_url('assets/css/mobiscroll.custom-2.16.1.min.css');?>" />
	<script src="<?php echo base_url('assets/js/flexible_css.js');?>" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo base_url('assets/js/flexible.js');?>" type="text/javascript" charset="utf-8"></script>
	<!-- <script src="<?php echo base_url('assets/js/fastclick.js'); ?>" type="text/javascript" charset="utf-8"></script> -->
	<script src="<?php echo base_url('assets/js/mui.min.js');?>" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo base_url('assets/js/velocity.min.js');?>" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo base_url('assets/js/vue.min.js');?>" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo base_url('assets/js/mui.picker.min.js');?>" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo base_url('assets/js/mui.dtpicker.js');?>" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo base_url('assets/js/echarts-all.js');?>" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo base_url('assets/js/jquery.min.js');?>" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo base_url('assets/js/mobiscroll.custom-2.16.1.min.js');?>" type="text/javascript" charset="utf-8"></script>
	<title><?php echo $title; ?></title>
	<script type="text/javascript">
		mui.init({
			swipeBack: false,
		});
		window.addEventListener('load', function() {
			//new FastClick(document.body);
		}, false);
	</script>
	<style type="text/css">
		[v-cloak] {display: none;}
		html {font-size: 16px!important;}
		.mui-content {
			height: 0rem;
			margin: 0rem;
			background-color: #efeff4;
		}
		h5.mui-content-padded {
			margin-left: 0.1875rem;
			margin-top: 1.25rem !important;
		}
		h5.mui-content-padded:first-child {
			margin-top: 0.75rem !important;
		}
		.mui-btn {
			font-size: 1rem;
			padding: 0.5rem;
			margin: 0.1875rem;
		}
		.ui-alert {
			text-align: center;
			padding: 1.25rem 0.625rem;
			font-size: 1rem;
		}
		* {
			-webkit-touch-callout: none;
			-webkit-user-select: none;
		}
		.mui-bar {
		    background-color: none !important;
	     	box-shadow: none !important;
		}
		#info {
			margin: 0 auto;
		}
		.mydate-btn {
			width: 2.025rem;
			height: 2.025rem;
			float: right;
			background-image: url(<?php echo base_url('assets/images/rili2x.png'); ?>);
			background-size:100%;
		}
		.mui-card-header1 {
			/*margin-top: 0.3125rem;*/
			width: 100%;
			height: 2.625rem;
			background-color: #fffefd;
			text-align:center;
		}
		.content1{
			width: 100%;
			height: 21rem;
			padding: 0.9375rem;
			margin: 0 auto;
			background-image: url(<?php echo base_url('assets/images/bg1.png'); ?>);
			background-size:98.5%;
		}
		.content1-inner1 {
			height: 94%;
			margin: 0 auto;
			padding: 0 0.625rem 0.1rem 0.625rem;
			background: #ffffff;
		    -webkit-border-radius: 0.25rem;
		}
		.avatar{
			width: 4rem;
			height: 4rem;
			margin: -0.5rem 0.625rem 0rem 0.625rem;
			border-radius: 50%;
		}
		.profile {
			height: 5.1125rem;
			padding: 1.2375rem 0rem 1.25rem 0rem;
		}
		.username{
			font-size: 1.5rem;
			margin: 0.625rem 0rem 0rem 0rem;
		}
		.idcard{
			font-size: 1rem;
		}
		#tbl{
			height: 3.25rem;
			padding: 0.3125rem 0.625rem;
			text-align: center;
			margin: 0.225rem 0rem 0.225rem 0rem;
		}
		#tbl-thead{
			background-color: #eee;
		}
		#tbl-thead tr{
			height: 1.6rem;
		}
		#tbl-tr{
		}
		.mui-grid-view.mui-grid-9 {
		    margin: 0;
		    padding: 0;
		    border-top: none;
		    border-left: none;
		    background-color: #f2f2f2;
		}
		.mui-table-view.mui-grid-view {
			padding: 0 auto;
		}
		.mui-grid-view.mui-grid-9 .mui-table-view-cell {
		    margin: 0;
		    padding: 0.6875rem 0.9375rem;
		    vertical-align: top;
		    border-right: 0.0625rem solid #e8e7e3;
		    border-bottom: none;
		}
		.mui-icon {
			margin-bottom: 1.25rem;
			margin-right: 0.625rem;
		}
		.mui-icon .mui-badge{
			line-height: 2.4;
			background: #65f3ae;
		}
		.content1 .pie-chart{
			background-color: #fffefd;
		}
		.content2{
			width: 100%;
			height: 15rem;
			margin: 0 auto;
			padding: 0 0.9375rem 0 0.9375rem;
			background-image: url(<?php echo base_url('assets/images/bg2.png'); ?>);

			background-size:100%;
		}
		.content2-inner2 {
			margin: 0 auto;
			padding: 0 0.625rem 0.1rem 0.625rem;
			background:#FFF;border:1px solid #DDD;
			-webkit-border-radius: 0.25rem;
		}
		.content3{
			width: 100%;
			height: 100%;
			margin: 0 auto;
			background: #ffffff;
		}
		.content3-inner3 {
			margin: 0 auto;
			padding: 0 0.625rem 0.1rem 0.625rem;
			background: #ffffff;
			/*-webkit-border-radius: 0.25rem;*/
		}
		.tbl2-tr{
			height: 1.8rem;
		}
		.chart {
			height: 12.5rem;
			margin: 0rem;
			padding: 0rem;
			text-align: center;
			vertical-align:middle;
		}
		#chart-title2{
			color:#a0a0a0;
		}
		.score{
			float:left;
		}
		.score b{
			font-size: 1.5rem;
			color:#65f3ad;
		}
		.nums{
			float:right;
		}
		.nums b{
			font-size: 1.5rem;
			color:#65f3ad;
		}
		.tbl2-tr.q{
			background-color: #cafae0;
		}
		.mui-grid-view.mui-grid-9 .mui-table-view-cell {
			padding: 0rem;
		}
        .times{
        	width: 3.125rem;
			height: 3.125rem;
			margin: 0rem auto;
			text-align: center;
			background-color: #fffefd; /* Can be set to transparent */
	      	border: 0.0625rem #88f2c5 solid;
			-webkit-border-radius: 6.25rem;
        }
        .times div{
        	margin: 0rem auto;
        	padding: 0.9375rem 1.25rem 0.9375rem 0.625rem;
        }
        .circle { width: 12.5rem; height: 12.5rem; position: absolute; border-radius: 50%; background: #0cc; } .pie_left, .pie_right { width: 12.5rem; height: 12.5rem; position: absolute; top: 0;left: 0; } .left, .right { display: block; width:12.5rem; height:12.5rem; background:#00aacc; border-radius: 50%; position: absolute; top: 0; left: 0; transform: rotate(30deg); } .pie_right, .right { clip:rect(0,auto,auto,6.25rem); } .pie_left, .left { clip:rect(0,6.25rem,auto,0); } .mask { width: 9.375rem; height: 9.375rem; border-radius: 50%; left: 1.5625rem; top: 1.5625rem; background: #FFF; position: absolute; text-align: center; line-height: 9.375rem; font-size: 1rem; }
        .mbsc-ios .dwb {
		    color: #333333;
		}
		.mbsc-ios .dwwb {
		    /*color: #f7f7f7;*/
		    color: #9d9d9d;
		}
		.mbsc-ios .dwbc {
		    border-bottom: 1px solid #c8c7cc;
		}

		.t-content1 {
			width: 100%;
			height: 16.3rem;
			margin: 0 auto;
			color: #ffffff;
			background-image: url(<?php echo base_url('assets/images/t_bg1.png'); ?>);
			background-repeat: no-repeat,
			background-size:100%;
		}

		.t-content1-inner1 {
			height: 100%;
			margin: 0 auto;
			padding: 0 0.625rem 0.1rem 0.625rem;
		}
		.t-mui-card-header {
			width: 100%;
			height: 16%;

			text-align:center;
		}
		.t-mydate-btn {
			width: 2.025rem;
			height: 2.025rem;
			float: right;
			background-image: url(<?php echo base_url('assets/images/rili2.png'); ?>);
			background-size:100%;
		}
		#t-content1-main {
			width: 100%;
			height: 83%;
			margin: 0 auto;
		}
		.t-profile {
			height: 100%;
			margin: 0 auto;
			padding-left: 10%;
			padding-top: 10%;
		}
		.t-avatar {
			width: 6.5rem;
			height: 6.5rem;
			border-radius: 50%;
		}
		.t-info {
			padding-left: 38%;
			padding-top: 6%;
		}
		.t-username {
			font-size: 1.5rem;
		}
		.t-idcard{
			font-size: 1rem;
			margin-top: 0.4rem;
			color: #ffffff;
		}
		.mui-segmented-control .mui-control-item {
			text-decoration: none;
			font-weight: 600;
		}
		.mui-segmented-control.mui-segmented-control-inverted .mui-control-item.mui-active {
			color: #30c1a2;
		    border-bottom: 2px solid #31c0a2;
		}
		.t-content2 {
			width: 100%;
			height: 23.5rem;
			margin: 0 auto;
			padding-top: 10%;
			background-color: #7fe4d3;
			background-image: url(<?php echo base_url('assets/images/bg2.png'); ?>);
			background-size:100%;
			background-repeat: no-repeat;
		}
		.t-content2-inner2 {
			margin: 0 auto;
			width: 95%;
		}
	</style>
</head>
<body class="mui-fullscreen">