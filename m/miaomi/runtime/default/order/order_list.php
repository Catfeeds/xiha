<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo isset($admin_title)?$admin_title:"";?>-喵咪鼠标</title>
<meta name="author" content="designer:webzhu, date:2012-03-23" />
<link type="image/x-icon" href="<?php echo urldecode(Url::urlFormat("@favicon.ico"));?>" rel="icon">
<link rel="stylesheet" type="text/css" href="<?php echo urldecode(Url::urlFormat("@static/css/base.css"));?>" />
<link rel="stylesheet" type="text/css" href="<?php echo urldecode(Url::urlFormat("@static/css/admin.css"));?>" />
<link rel="stylesheet" type="text/css" href="<?php echo urldecode(Url::urlFormat("@static/css/font_icon.css"));?>" />
<?php echo JS::import('jquery');?>
<script type="text/javascript" src="<?php echo urldecode(Url::urlFormat("@static/js/common.js"));?>"></script>
<!--[if lte IE 7]><script src="<?php echo urldecode(Url::urlFormat("@static/css/fonts/lte-ie7.js"));?>"></script><![endif]-->
</head>
<body >
<div id="header">
	<div class="nav_sub">
			    	您好[<?php echo isset($manager['name'])?$manager['name']:"";?>]&nbsp; | <a href="<?php echo urldecode(Url::urlFormat("/index/index"));?>" target="_blank">返回前台</a> | <a href="<?php echo urldecode(Url::urlFormat("/admin/logout"));?>">退出</a>
	</div>
    <div id="Logo"><a href=""><img src="<?php echo urldecode(Url::urlFormat("@static/images/logo_min2.png"));?>" width="128" height="42"></a></div>
	<ul id="main_nav" class="clearfix">
	<?php foreach($mainMenu as $key => $item){?>
		<li <?php if($key==$menu_index['menu']){?>class="active"<?php }?>><a href="<?php echo urldecode(Url::urlFormat("$item[link]"));?>"  ><?php echo isset($item['name'])?$item['name']:"";?></a></li>
	<?php }?>
	</ul>
</div>
<div id="mainContent">
	<div id="sidebar" >
		<ul class="menu" style="margin-top:15px;">
		<?php foreach($subMenu as $key => $item){?>
			<?php if($item['name']!='第三方整合' && $item['name']!='数据库管理'){?>
			<li class="submenu">
			<ul><li class="sub-index"><b><a href="javascript:;"><?php echo isset($item['name'])?$item['name']:"";?></a></b></li>
			<?php foreach($menu->getNodes($item['link']) as $key => $item){?>
			<?php if(substr($item['link'],-5)!='_edit' && !$item['hidden'] && $item['name']!='主题设置' && $item['name']!='其它配置' && $item['name']!='邮箱配置' && $item['name']!='支付方式' && $item['name']!='权限列表'){?>
				<li><a href='<?php echo urldecode(Url::urlFormat("$item[link]"));?>' <?php if($item['link']==$nav_link){?>class="current"<?php }?> ><?php echo isset($item['name'])?$item['name']:"";?></a></li>
				<?php }?>
			<?php }?>
			</ul>
			</li>
			<?php }?>
		<?php }?>
		</ul>
	</div>
	<div id="content" >

		<?php if(!isset($msg)){?><?php $msg=Req::post('msg');?><?php }?>
		<?php if(!isset($validator)){?><?php $validator=Req::post('validator');?><?php }?>
		<?php if(isset($msg[0])){?>
		<div id="message-bar" class="message_<?php echo isset($msg[0])?$msg[0]:"";?>"><?php echo isset($msg[1])?$msg[1]:"";?></div>
		<?php }elseif(isset($validator)){?>
		<div class="message_warning"><?php echo isset($validator['msg'])?$validator['msg']:"";?></div>
		<?php }?>
		<?php echo JS::import('dialog?skin=brief');?>
<?php echo JS::import('dialogtools');?>
<form action="" method="post">
<div class="tools_bar clearfix">
    <a class="icon-checkbox-checked icon-checkbox-unchecked" href="javascript:;" onclick="tools_select('id[]',this)" title="全选" data="true"> 全选 </a>
    <a  class="icon-remove-2" href="javascript:;" onclick="tools_submit({action:'<?php echo urldecode(Url::urlFormat("/order/order_del"));?>',msg:'删除后无法恢复，你真的删除吗？'})" title="删除"> 删除</a>
    <a class="icon-delicious" href="<?php echo urldecode(Url::urlFormat("/order/order_list"));?>"> 全部订单</a>
    <a class="icon-eye-blocked" href="<?php echo urldecode(Url::urlFormat("/order/order_list/status/2"));?>"> 未审核</a>
    <a class="icon-cogs" href="<?php echo urldecode(Url::urlFormat("/order/order_list/status/3"));?>"> 执行中</a>
    <span class="fr"><a href='javascript:;' id="refresh" class="icon-chrome" style="" > 刷新</a></span>
    <span class="fr"><a href='javascript:;' id="condition" class="icon-search" style="" > 筛选条件</a><input id="condition_input" type="hidden" name="condition" value="<?php echo isset($condition)?$condition:"";?>"></span>
</div>
<table class="default" >
    <tr>
        <th style="width:9%;">订单编号</th>
        <th style="width:9%;">操作</th>
        <th style="width:9%;">订单号</th>
        <th style="width:9%;">订单金额 </th>
        <!-- <th style="width:9%;">会员账号</th> -->
        <th style="width:9%;">收货人</th>
        <th style="width:9%;">收货人电话</th>
        <th style="width:9%;">支付方式</th>
        <th style="width:9%;">支付状态</th>
        <th style="width:9%;">配送状态</th>
        <th style="width:9%;">订单状态</th>
        <th style="width:9%;">打印</th>
    </tr>
    <?php $item=null; $obj = new Query("order as od");$obj->fields = "od.*,us.name as uname,us.status as ustatus";$obj->join = "left join user as us on od.user_id = us.id";$obj->where = "$where";$obj->page = "1";$obj->order = "id desc";$items = $obj->find(); foreach($items as $key => $item){?>
        <tr>
        <td style="width:50px"><input type="checkbox" name="id[]" value="<?php echo isset($item['id'])?$item['id']:"";?>"><!-- <i class="icon-order-<?php echo isset($item['type'])?$item['type']:"";?>"></i> --><?php echo isset($item['id'])?$item['id']:"";?></td>

            <td style="width:70px" class="btn_min"><div class="operat hidden"><a  class="icon-cog action" href="javascript:;"> 处理</a><div class="menu_select"><ul>
                <li><a class="icon-eye" href="javascript:;" onclick="view(<?php echo isset($item['id'])?$item['id']:"";?>)"> 查看</a></li>
                <?php if($item['status'] == 1 || $item['status'] == 2){?>
                <li><a class="icon-pencil" href="javascript:;" onclick="edit(<?php echo isset($item['id'])?$item['id']:"";?>)"> 编辑</a></li>
                <li><a class="icon-drawer-3" href="javascript:;" onclick="change_status(<?php echo isset($item['id'])?$item['id']:"";?>,3,null)"> 审核</a></li>
                <?php }?>
                <?php if($item['status'] == 3){?>
                <?php if($item['delivery_status'] == 0){?>
                <li><a class="icon-truck" href="javascript:;" onclick="send(<?php echo isset($item['id'])?$item['id']:"";?>)"> 发货</a></li>
                <?php }?>
                <li><a class="icon-switch"  href="javascript:;" onclick="change_status(<?php echo isset($item['id'])?$item['id']:"";?>,4,null)"> 完成</a></li>
                <?php }?>
                <?php if($item['status'] <= 4){?>
                <li><a class="icon-remove" href="javascript:;" onclick="change_status(<?php echo isset($item['id'])?$item['id']:"";?>,6,null)"> 作废</a></li>
                <?php }?>
                <?php if($item['status'] == 5 || $item['status'] == 6){?>
                <li><a class="icon-close" href="javascript:confirm_action('<?php echo urldecode(Url::urlFormat("/order/order_del/id/$item[id]"));?>')"> 删除</a></li>
                <?php }?>
                <li><a class="icon-attachment"  href="javascript:;" onclick="change_status(<?php echo isset($item['id'])?$item['id']:"";?>,null,'note')"> 备注</a></li></ul></div></div> </td>
            <td style="width:100px"><?php echo isset($item['id'])?$item['id']:"";?></td><td style="width:70px"><?php echo isset($item['order_amount'])?$item['order_amount']:"";?></td><!-- <td style="width:70px"><?php echo isset($item['uname'])?$item['uname']:"";?></td> --><td style="width:70px"><?php echo isset($item['accept_name'])?$item['accept_name']:"";?></td><td style="width:80px"><?php echo isset($item['mobile'])?$item['mobile']:"";?></td><td style="width:80px"><?php if($item['pay_code']=='alipay'){?>支付宝<?php }?><?php if($item['pay_code']=='wxpay'){?>微信<?php }?></td><td style="width:50px"><?php echo isset($pay_status[$item['pay_status']])?$pay_status[$item['pay_status']]:"";?></td>
        <td style="width:50px">
            <?php echo isset($delivery_status[$item['delivery_status']])?$delivery_status[$item['delivery_status']]:"";?>
            <?php if($item['status']>2 && $item['pay_status']==1 && $item['delivery_status']==1){?>
            <a class="" href="javascript:order_traces(<?php echo isset($item['id'])?$item['id']:"";?>);" style="font-weight: 600;" target="order">【查看物流】</a>
            <?php }?>
        </td>
        <td style="width:50px" id="status_<?php echo isset($item['id'])?$item['id']:"";?>"><?php echo isset($status[$item['status']])?$status[$item['status']]:"";?></td>
        <td style="width:60px" class="btn_min">
            <div class="operat hidden">
                <a class="icon-print" href="<?php echo urldecode(Url::urlFormat("/order/print_order/id/$item[id]"));?>" target="order"> 打印订单</a>
                <!-- <div class="menu_select">
                    <ul>
                        <li><a class="icon-print" href="<?php echo urldecode(Url::urlFormat("/order/print_order/id/$item[id]"));?>" target="order"> 订单</a></li>
                        <li><a class="icon-print" href="<?php echo urldecode(Url::urlFormat("/order/print_product/id/$item[id]"));?>" target="product" > 购物单</a></li>
                        <li><a class="icon-print" href="<?php echo urldecode(Url::urlFormat("/order/print_picking/id/$item[id]"));?>" target="picking"> 配送单</a></li>
                        <li><a class="icon-print" href="<?php echo urldecode(Url::urlFormat("/order/print_express/id/$item[id]"));?>" target="express"> 快递单</a></li>

                    </ul>
                </div> -->
            </div>
        </td></tr>
    <?php }?>
</table>
</form>
<div class="page_nav">
<?php echo $obj->pageBar();?>
</div>
<div id="status_dialog" style="display: none; position: relative;" class="form2">
    <h3 id="order_title">操作缘由：</h3>
        <input type="hidden" name="id" id="order_id" value="">
        <input type="hidden" name="status" id="order_status" value="">
        <input type="hidden" name="op" id="order_op" value="">
        <textarea id="order_remark" name="remark"></textarea>
    <div class="tc"><button class="focus_button" onclick="submit_status()">提交</button></div>
</div>
<script type="text/javascript">
    function edit(id){
        art.dialog.open("<?php echo urldecode(Url::urlFormat("/order/order_edit/id/"));?>"+id,{id:'edit_dialog',title:'订单编辑',resize:false,width:900,height:450});
    }
    function view(id){
        art.dialog.open("<?php echo urldecode(Url::urlFormat("/order/order_view/id/"));?>"+id,{id:'view_dialog',title:'查看订单',resize:false,width:900,height:450});
    }
    function send(id){
        art.dialog.open("<?php echo urldecode(Url::urlFormat("/order/order_send/id/"));?>"+id,{id:'send_dialog',title:'发货',resize:false,width:900,height:450});
    }

     function order_traces(id){
        art.dialog.open("<?php echo urldecode(Url::urlFormat("/order/order_traces/id/"));?>"+id,{id:'traces_dialog',title:'物流信息',resize:false,width:900,height:450});
    }

    function send_dialog_close(){
        art.dialog({id:'send_dialog'}).close();
        tools_reload();
    }
    function change_status(id,status,op){
        var title = '';
        if(status==null){
            if(op=='del') title = '删除订单';
            else if(op=='note') title = '备注订单';
        }else{
            if(status == 3) title = '审核订单';
            else if(status == 4) title = '完成订单';
            else if(status == 6) title = '作废订单';
        }
        $("#order_id").val(id);
        $("#order_status").val(status);
        $("#order_op").val(op);
        art.dialog({id:'status_dialog',title:title,resize:false,width:500,height:200,padding:'0 5px',content:document.getElementById('status_dialog')});
    }
    function submit_status(){
        var order_status = ['<span class="red">等待审核</span>','<span class="red">等待审核</span>','<span class="red">等待审核</span>','已审核','已完成','已取消','<span class="red"><s>已作废</s></span>'];
        var remark= $("#order_remark").val();
        var id = $("#order_id").val();
        var status = $("#order_status").val();
        var op = $("#order_op").val();
        if(remark != ''){
            $.post('<?php echo urldecode(Url::urlFormat("/order/order_status"));?>',{id:id,status:status,op:op,remark:remark},function(data){
                art.dialog({id:'status_dialog'}).close();
                if(data['status']=='success'){
                    $("#status_"+id).html(order_status[status]);
                    art.dialog.tips("<p class='"+data['status']+"'>"+data['msg']+"成功！</p>");
                }else{
                    art.dialog.tips("<p class='"+data['status']+"'>"+data['msg']+"失败！</p>");
                }
                setTimeout("tools_reload()",2000);
            },'json');
        }else{
            art.dialog.tips("<p class='warning'>操作缘由不能为空!</p>");
        }

    }
    function send_close(){
        art.dialog({id:'send_dialog'}).close();
        art.dialog.tips("<p class='success'>发货成功！</p>");
    }
    function close(){
        art.dialog({id:'edit_dialog'}).close();
        art.dialog.tips("<p class='success'>订单编辑成功！</p>");
    }
    <?php $payment="";?>
    <?php $item=null; $query = new Query("payment");$items = $query->find(); foreach($items as $key => $item){?>
        <?php $payment.=$item['id'].':'."'".$item['pay_name']."',";?>
    <?php }?>
    <?php $payment=trim($payment,',');?>
$("#condition").on("click",function(){
  $("body").Condition({input:"#condition_input",okVal:'高级搜索',callback:function(data){tools_submit({action:'<?php echo urldecode(Url::urlFormat("/order/order_list"));?>',method:'get'});},
    data:{
        'od.id':{name:'订单编号'},
        pay_status:{name:'支付状态',values:{0:'未付款', 1:'已付款', 2:'申请退款', 3:'已退款'}},
        delivery_status:{name:'发货状态',values:{0:'未发货',1:'已发货', 2:'已签收', 3:'申请换货', 4:'已换货'}},
        'od.status':{name:'订单状态',values:{2:'等待审核',3:'已审核',4:'完成',5:'取消',6:'废除'}},
        pay_code:{name:'支付方式',values:{alipay:'支付宝',wxpay:'微信'}},
    }});
})
$('#refresh').on('click', function() {
    window.location.href = "<?php echo urldecode(Url::urlFormat("/order/order_list"));?>";
})
</script>

	</div>
</div>
<script type="text/javascript">
	$(function () {
		if('<?php echo Req::args("con");?>'=='admin'){
			$(".submenu .current").parent().parent().parent().addClass('current');
		}else{
			$(".submenu").addClass('current');
		}
		$(".submenu .sub-index").on("click",function(){
			$(this).parent().parent().toggleClass('current');
		})
	})

</script>
</body>
</html>

