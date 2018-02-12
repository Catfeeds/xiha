<!DOCTYPE html>
<html>
<head>
	<title><?php echo isset($title)?$title:"";?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="author" content="designer:webzhu, date:2012-03-23" />
<link rel="stylesheet" href="<?php echo urldecode(Url::urlFormat("@static/css/base.css"));?>" />
<link rel="stylesheet" href="<?php echo urldecode(Url::urlFormat("@static/css/admin.css"));?>" />
<link rel="stylesheet" href="<?php echo urldecode(Url::urlFormat("@static/css/font_icon.css"));?>" />
<?php echo JS::import('jquery');?>
<script type="text/javascript" src="<?php echo urldecode(Url::urlFormat("@static/js/common.js"));?>"></script>
<!--[if lte IE 7]><script src="<?php echo urldecode(Url::urlFormat("@static/css/fonts/lte-ie7.js"));?>"></script><![endif]-->
</head>
<body style="background: none;">
    <?php echo JS::import("form");?>
<div style="overflow: auto;width:900px;height: 450px;margin-top:10px;">
		<h3 class="lineD mt10">物流详情：</h3>
		<table class="default" style="margin-bottom: 10px;">
			<?php foreach($items as $key => $item){?>
			<tr>
				<td style="width:20%;"><?php echo isset($item['AcceptTime'])?$item['AcceptTime']:"";?></td>
				<td><?php echo isset($item['AcceptStation'])?$item['AcceptStation']:"";?></td>
			</tr>
			<?php }?>
		</table>
</div>

<script type="text/javascript">
</script>

</body>
</html>
