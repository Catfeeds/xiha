<?php /* Smarty version 3.1.27, created on 2015-08-29 00:03:55
         compiled from "E:\AppServ\www\service\admin\templates\coach\show.html" */ ?>
<?php
/*%%SmartyHeaderCode:2418155e0866b64c1a8_48523790%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b9ed4cc1dc5be24a96298f43f10bb227b8ebc5df' => 
    array (
      0 => 'E:\\AppServ\\www\\service\\admin\\templates\\coach\\show.html',
      1 => 1439191460,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2418155e0866b64c1a8_48523790',
  'variables' => 
  array (
    'coachdetail' => 0,
    'lisence_name' => 0,
    'lesson_name' => 0,
    'carinfo' => 0,
    'car_list' => 0,
    'value' => 0,
    'provincelist' => 0,
    'cityinfo' => 0,
    'areainfo' => 0,
    'current_time_config' => 0,
    'key' => 0,
    'v' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_55e0866b7abda1_34615094',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_55e0866b7abda1_34615094')) {
function content_55e0866b7abda1_34615094 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '2418155e0866b64c1a8_48523790';
echo $_smarty_tpl->getSubTemplate ("library/header.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
?>

<div class="container" style="margin-top:10px;">
<div class="tab"> 
	<div class="tab-head border-main"> 
<!-- 		<strong>标题</strong> 
		<span class="tab-more"><a href="#">更多</a></span>  -->
		<ul class="tab-nav"> 
			<li class="active">
				<a href="#tab-base-info">基本信息</a>
			</li> 
			<li>
				<a href="#tab-time-config">时间配置</a>
			</li> 
	<!-- 		<li>
				<a href="#tab-units">...</a>
			</li>  -->
		</ul> 
	</div> 
	<div class="tab-body"> 
		<!-- 基本信息 -->
		<div class="tab-panel form-x active" id="tab-base-info">

			<div class="media media-x">
			 	<a class="float-left text-center" href="#"> 
					<img width="128" height="128" class="img-border radius-circle" id="imgShow" src="<?php echo $_smarty_tpl->tpl_vars['coachdetail']->value['s_coach_imgurl'];?>
" alt=""><br>

		<!-- 			<p class="button input-file bg-green">+ 浏览文件
						<input type="file" name="license_img" value="" id="license_img" data-validate="regexp#.+.(jpg|jpeg|png|gif)$:只能上传jpg|gif|png格式文件" />
					</p><br><br>
					<button class="button bg-blue" onclick="javascript:uploadcoachimg();">上传</button> -->
				</a>

				<div class="media-body">
					<div class="table-responsive"> 
						<table class="table table-bordered"> 
							<tr>
								<td width="10%" class="blue text-center text-big height-big">姓名</td>
								<td width="45%"><input type="text" class="input" id="coach_name" size="" value="<?php echo $_smarty_tpl->tpl_vars['coachdetail']->value['s_coach_name'];?>
"></td>
								<!-- <td width="35%" colspan = 2>
									<button class="button border-main" onclick="javascript:savecoachname(<?php echo $_smarty_tpl->tpl_vars['coachdetail']->value['l_coach_id'];?>
)">保存修改</button>
									<span class="coach_name_tips text-main"></span>
								</td> -->
							</tr>
							<tr>
								<td width="10%" class="blue text-center text-big height-big">电话</td>
								<td><input type="text" class="input" id="coach_phone" size="" value="<?php echo $_smarty_tpl->tpl_vars['coachdetail']->value['s_coach_phone'];?>
"></td>
								<!-- <td colspan=2><button class="button border-main" onclick="javascript:savecoachphone(<?php echo $_smarty_tpl->tpl_vars['coachdetail']->value['l_coach_id'];?>
)">保存修改</button><span class="coach_phone_tips text-main"></span></td> -->
							</tr>
							<tr>
								<td width="10%" class="blue text-center text-big height-big">培训牌照</td>
								<td><input type="text" class="input" size="" value="<?php echo $_smarty_tpl->tpl_vars['lisence_name']->value;?>
" disabled="disabled"></td>
								<!-- <td colspan=2></td> -->
							</tr>
							<tr>
								<td width="10%" class="blue text-center text-big height-big">培训科目</td>
								<td><input type="text" class="input" size="" value="<?php echo $_smarty_tpl->tpl_vars['lesson_name']->value;?>
" disabled="disabled"></td>
								<!-- <td colspan=2></td> -->
							</tr>
							<tr>
								<td width="10%" class="blue text-center text-big height-big">所属车辆</td>
								<td><input type="text" class="input car_name" size="" value="<?php if ($_smarty_tpl->tpl_vars['carinfo']->value) {
echo $_smarty_tpl->tpl_vars['carinfo']->value['name'];
} else { ?>暂无设置<?php }?>" disabled="disabled"></td>
							<!-- 	<td width="20%">
									<select class="input" id="coach_school_car" name="coach_school_car"> 
									    <option value="">请选择车辆</option> 
									    <?php if ($_smarty_tpl->tpl_vars['car_list']->value) {?>
									        <?php
$_from = $_smarty_tpl->tpl_vars['car_list']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['value']->_loop = false;
$_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->_loop = true;
$foreach_value_Sav = $_smarty_tpl->tpl_vars['value'];
?>
									         <option value="<?php echo $_smarty_tpl->tpl_vars['value']->value['id'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['value']->value['name'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['value']->value['car_no'];?>
" <?php if ($_smarty_tpl->tpl_vars['coachdetail']->value['s_coach_car_id'] == $_smarty_tpl->tpl_vars['value']->value['id']) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['value']->value['name'];?>
 (<?php echo $_smarty_tpl->tpl_vars['value']->value['car_no'];?>
)</option> <?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
									    <?php } else { ?>
									        <option value="">暂无车辆列表</option>
									    <?php }?>
									</select>
								</td> -->
								<!-- <td><button class="button border-main" onclick="javascript:savecoachcar(<?php echo $_smarty_tpl->tpl_vars['coachdetail']->value['l_coach_id'];?>
)">保存修改</button></td> -->
							</tr>

							<tr>
								<td width="10%" class="blue text-center text-big height-big">车辆牌照</td>
								<td><input type="text" class="input car_no" size="" value="<?php if ($_smarty_tpl->tpl_vars['carinfo']->value) {
echo $_smarty_tpl->tpl_vars['carinfo']->value['car_no'];
} else { ?>暂无设置<?php }?>" disabled="disabled"></td>
								<!-- <td></td>
								<td><span class="coach_name_tips text-main"></span></td> -->
							</tr>
							<!-- <tr>
								<td width="10%" class="blue text-center text-big height-big">教练地址</td>
								<td>
									<select class="input" id="province" style="width:20%; float:left" name="province"> 
									    <option value="">请选择省</option> 
									    <?php
$_from = $_smarty_tpl->tpl_vars['provincelist']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['value']->_loop = false;
$_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->_loop = true;
$foreach_value_Sav = $_smarty_tpl->tpl_vars['value'];
?>
									     <option value="<?php echo $_smarty_tpl->tpl_vars['value']->value['provinceid'];?>
" <?php if ($_smarty_tpl->tpl_vars['coachdetail']->value['province_id'] == $_smarty_tpl->tpl_vars['value']->value['provinceid']) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['value']->value['province'];?>
</option>
									    <?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
									</select>
									<select class="input" id="city" style="width:20%; float:left" name="city"> 
									    <option value="">请选择市</option>
									    <?php if ($_smarty_tpl->tpl_vars['coachdetail']->value['city_id']) {?>
									    <option value="<?php echo $_smarty_tpl->tpl_vars['cityinfo']->value['cityid'];?>
" <?php if ($_smarty_tpl->tpl_vars['coachdetail']->value['city_id'] == $_smarty_tpl->tpl_vars['cityinfo']->value['cityid']) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['cityinfo']->value['city'];?>
</option>
									    <?php }?>
									
									</select>
									<select class="input" id="area" style="width:20%; float:left" name="area"> 
									    <option value="">请选择区域</option>
									    <?php if ($_smarty_tpl->tpl_vars['coachdetail']->value['area_id']) {?>
									    <option value="<?php echo $_smarty_tpl->tpl_vars['areainfo']->value['areaid'];?>
" <?php if ($_smarty_tpl->tpl_vars['coachdetail']->value['area_id'] == $_smarty_tpl->tpl_vars['areainfo']->value['areaid']) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['areainfo']->value['area'];?>
</option>
									    <?php }?>
									</select>
								</td>
								<td colspan=2><!-- <button class="button border-main" onclick="javascript:savecoachaddress(<?php echo $_smarty_tpl->tpl_vars['coachdetail']->value['l_coach_id'];?>
)">保存修改</button> --></td>
							</tr>
							<tr>
								<td width="10%" class="blue text-center text-big height-big">教练地址</td>
								<td class="text-left height-big"><input type="text" class="input" id="coach_address" name="coach_address" value="<?php echo $_smarty_tpl->tpl_vars['coachdetail']->value['s_coach_address'];?>
" size="50" placeholder="详细地址" /></td>
								<!-- <td colspan=2><button class="button border-main" onclick="javascript:savecoachaddress(<?php echo $_smarty_tpl->tpl_vars['coachdetail']->value['l_coach_id'];?>
)">保存修改</button></td> -->
							</tr>
						</table> 
					</div>
				</div> 
			</div> 
	
		</div>
	
		<!-- 时间配置 -->
		<div class="tab-panel" id="tab-time-config">
			<div class="collapse">
				<?php if ($_smarty_tpl->tpl_vars['current_time_config']->value) {?>
					<?php
$_from = $_smarty_tpl->tpl_vars['current_time_config']->value['date_time'];
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['value'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['value']->_loop = false;
$_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->_loop = true;
$foreach_value_Sav = $_smarty_tpl->tpl_vars['value'];
?>
					<div class="panel <?php if ($_smarty_tpl->tpl_vars['key']->value == 0) {?>active<?php }?>"> 
						<div class="panel-head bg-yellow bg-inverse border-yellow"><h4><?php echo $_smarty_tpl->tpl_vars['value']->value;?>
</h4></div> 
						<div class="panel-body">
				            <table class="table" >
				                <tr>
				                    <th width="100">开始时间</th>
				                    <th width="100">结束时间</th>
				                    <th width="100">牌照</th>
				                    <th width="100">科目</th>
				                    <th width="100">单价</th>
				                </tr>


								<?php
$_from = $_smarty_tpl->tpl_vars['current_time_config']->value['time_list'][$_smarty_tpl->tpl_vars['key']->value];
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['v'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['v']->_loop = false;
$_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
foreach ($_from as $_smarty_tpl->tpl_vars['k']->value => $_smarty_tpl->tpl_vars['v']->value) {
$_smarty_tpl->tpl_vars['v']->_loop = true;
$foreach_v_Sav = $_smarty_tpl->tpl_vars['v'];
?>
			                    <tr>
			                        <td><?php echo $_smarty_tpl->tpl_vars['v']->value['start_time'];?>
:00</td>
			                        <td><?php echo $_smarty_tpl->tpl_vars['v']->value['end_time'];?>
:00</td>
			                        <td><?php echo $_smarty_tpl->tpl_vars['v']->value['lisence_name'];?>
</td>
			                        <td><?php echo $_smarty_tpl->tpl_vars['v']->value['lesson_name'];?>
</td>
			                        <td><?php echo $_smarty_tpl->tpl_vars['v']->value['money'];?>
</td>
			                    </tr>
			        			<?php
$_smarty_tpl->tpl_vars['v'] = $foreach_v_Sav;
}
?> 
				            </table> 
						</div> 
					</div>
					<?php
$_smarty_tpl->tpl_vars['value'] = $foreach_value_Sav;
}
?>
				<?php } else { ?>
					<div class="alert alert-yellow"><span class="close rotate-hover"></span><strong>注意：</strong>暂无时间配置<a href="index.php?action=coach&op=edit&id=<?php echo $_smarty_tpl->tpl_vars['coachdetail']->value['l_coach_id'];?>
" class="text-blue">点击配置时间</a></div>

				<?php }?>
			</div>

			<!-- <div class="tab">
			    <div class="tab-head float-left"> 
			         <ul class="tab-nav date_config"> 
			            时间7天日期显示
			
			            {foreach $current_time_config['date_time'] as $key => $value}
			            	<li  {if $key == 0}class="active"{/if}><a href="#tab-time-{$key}" title="">{$value}</a></li>
			       		{/foreach}
			        </ul>
			    </div>
			
			    <div class="clearfix"></div>
			    
			    每一天的时间配置
			    <div class="tab-body time_config">
					
					{foreach $current_time_config['time_list'] as $key => $value}
				        <div  {if $key == 0}class="tab-panel active"{else}class="tab-panel"{/if} id="tab-time-{$key}"> 
			
				            <table class="table" >
				                <tr>
				                    <th width="100">开始时间</th>
				                    <th width="100">结束时间</th>
				                    <th width="100">牌照</th>
				                    <th width="100">科目</th>
				                    <th width="100">单价</th>
				                </tr>
			
			                	{foreach $value as $e => $t}
			                    <tr>
			                        <td>{$t['start_time']}:00</td>
			                        <td>{$t['end_time']}:00</td>
			                        <td>{$t['lisence_name']}</td>
			                        <td>{$t['lesson_name']}</td>
			                        <td>{$t['money']}</td>
			                    </tr>
			                    {/foreach}
				                
				            </table> 
				
				         </div>
			        {/foreach}
			    </div> 
			</div> -->
			
		</div> 

	</div> 
</div>
</div>
<?php echo '<script'; ?>
>
	// 上传图片预览
	    window.onload = function () { 
	        new uploadPreview({ UpBtn: "license_img", DivShow: "imgdiv", ImgShow: "imgShow" });
	    }

	    // 选择车辆和牌照
	    $('#coach_school_car').change(function() {
	    	$('.car_name').val($(this).find('option:selected').attr('title'));
	    	$('.car_no').val($(this).find('option:selected').attr('alt'));

	    })
	    // 城市联动
	    $('#province').change(function() {
	        var province_id = $(this).val();
	        $("#city").load("index.php?action=school&op=getcity&province_id="+province_id);
	    });

	    $('#city').change(function() {
	        var city_id = $(this).val();
	        $('#area').load('index.php?action=school&op=getarea&city_id='+city_id);
	        var city_html = $(this).find('option:selected').html();
	        var province_html = $('#province').find('option:selected').html();
	        $('#s_address').html(province_html+city_html);
	    })

	    $('#area').change(function() {
	        var city_html = $('#city').find('option:selected').html();
	        var province_html = $('#province').find('option:selected').html();
	        var area_html = $(this).find('option:selected').html();
	        $('#s_address').html(province_html+city_html+area_html);
	    })

	    // 保存修改姓名
	    function savecoachname(id) {
	    	$.ajax({
	    		type:"POST",
	    		url:"index.php?action=coach&op=savename",
	    		dataType:"JSON",
	    		data:{'id':id,'coach_name':$('#coach_name').val()},
	    		success:function(data) {
	    			if(data.code == 1) {
	    				// $('#coach_name_tips').show();
	    			} else if (data.code == 0){
	    				$('.coach_name_tips').removeClass('text-main');
	    				$('.coach_name_tips').addClass('text-red');
	    			}
    				$('.coach_name_tips').html(data.msg).show().delay(3000).hide(0);
	    		}
	    	});
	    }

	    // 保存修改电话
	    function savecoachphone(id) {
	    	$.ajax({
	    		type:"POST",
	    		dataType:"JSON",
	    		url:"index.php?action=coach&op=savephone",
	    		data:{id:id,'coach_phone':$('#coach_phone').val()},
	    		success:function(data) {
	    			if(data.code == 1) {
	    				$('#coach_phone').val(data.name);
	    			}
	    		}
	    	});
	    }

	    // 保存修改所属车辆
	    function savecoachphone(id) {
	    	$.ajax({
	    		type:"POST",
	    		url:"index.php?action=coach&op=savecar",
	    		dataType:"JSON",
	    		data:{id:id,'coach_car':$('#coach_car').val()},
	    		success:function(data) {
	    			if(data.code == 1) {
	    				$('#coach_car').val(data.name);
	    			}
	    		}
	    	});
	    }

	    // 保存修改所属车辆
	    function savecoachaddress(id) {
	    	$.ajax({
	    		type:"POST",
	    		url:"index.php?action=coach&op=saveaddress",
	    		dataType:"JSON",
	    		data:{id:id,'coach_address':$('#coach_address').val(),'provinceid':$('#province').find('option:selected').val(),'cityid':$('#city').find('option:selected').val(),'areaid':$('#area').find('option:selected').val()},
	    		success:function(data) {
	    			if(data.code == 1) {
	    				$('#coach_address').val(data.name);
	    			}
	    		}
	    	});
	    }
<?php echo '</script'; ?>
>
<?php echo $_smarty_tpl->getSubTemplate ("library/footer.lbi", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
?>

<?php }
}
?>