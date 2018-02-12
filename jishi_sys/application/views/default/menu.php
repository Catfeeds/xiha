<header class="navbar-wrapper">
    <div class="navbar navbar-fixed-top">
        <div class="container-fluid cl"> <a class="logo navbar-logo f-l mr-10 hidden-xs" href="#">计时培训管理系统</a>
            <span class="logo navbar-slogan f-l mr-10 hidden-xs">v1.0</span>
            <a aria-hidden="false" class="nav-toggle Hui-iconfont visible-xs" href="javascript:;">&#xe667;</a>
            <nav class="nav navbar-nav">
                <ul class="cl">
                    <li class="dropDown dropDown_hover"><a href="javascript:;" class="dropDown_A"><i class="Hui-iconfont">&#xe600;</i> 新增 <i class="Hui-iconfont">&#xe6d5;</i></a>
                        <ul class="dropDown-menu menu radius box-shadow">
                            <li><a href="javascript:;" onclick="article_add('添加培训机构','article-add.html')"><i class="Hui-iconfont">&#xe616;</i> 培训机构</a></li>
                            <li><a href="javascript:;" onclick="picture_add('添加教练员','picture-add.html')"><i class="Hui-iconfont">&#xe613;</i> 教练员</a></li>
                            <li><a href="javascript:;" onclick="product_add('添加训练车','product-add.html')"><i class="Hui-iconfont">&#xe620;</i> 训练车</a></li>
                            <li><a href="javascript:;" onclick="member_add('添加考核员','member-add.html','','510')"><i class="Hui-iconfont">&#xe60d;</i> 考核员</a></li>
                            <li><a href="javascript:;" onclick="member_add('添加安全员','member-add.html','','510')"><i class="Hui-iconfont">&#xe60d;</i> 安全员</a></li>
                            <li><a href="javascript:;" onclick="member_add('添加学员','member-add.html','','510')"><i class="Hui-iconfont">&#xe60d;</i> 学员</a></li>
                            <li><a href="javascript:;" onclick="member_add('添加终端设备','member-add.html','','510')"><i class="Hui-iconfont">&#xe60d;</i> 终端设备</a></li>
                            <li><a href="javascript:;" onclick="member_add('添加收费标准','member-add.html','','510')"><i class="Hui-iconfont">&#xe60d;</i> 收费标准</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
            <nav id="Hui-userbar" class="nav navbar-nav navbar-userbar hidden-xs">
                <ul class="cl">
                    <li>超级管理员</li>
                    <li class="dropDown dropDown_hover">
                        <a href="#" class="dropDown_A">admin <i class="Hui-iconfont">&#xe6d5;</i></a>
                        <ul class="dropDown-menu menu radius box-shadow">
                            <li><a href="javascript:;" onClick="myselfinfo()">个人信息</a></li>
                            <li><a href="#">切换账户</a></li>
                            <li><a href="#">退出</a></li>
                        </ul>
                    </li>
                    <li id="Hui-msg"> <a href="#" title="消息"><span class="badge badge-danger">1</span><i class="Hui-iconfont" style="font-size:18px">&#xe68a;</i></a> </li>
                    <li id="Hui-skin" class="dropDown right dropDown_hover"> <a href="javascript:;" class="dropDown_A" title="换肤"><i class="Hui-iconfont" style="font-size:18px">&#xe62a;</i></a>
                        <ul class="dropDown-menu menu radius box-shadow">
                            <li><a href="javascript:;" data-val="default" title="默认（黑色）">默认（黑色）</a></li>
                            <li><a href="javascript:;" data-val="blue" title="蓝色">蓝色</a></li>
                            <li><a href="javascript:;" data-val="green" title="绿色">绿色</a></li>
                            <li><a href="javascript:;" data-val="red" title="红色">红色</a></li>
                            <li><a href="javascript:;" data-val="yellow" title="黄色">黄色</a></li>
                            <li><a href="javascript:;" data-val="orange" title="橙色">橙色</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>
<aside class="Hui-aside">
    <div class="menu_dropdown bk_2">
        <dl id="menu-institution">
            <dt><i class="Hui-iconfont">&#xe616;</i> 培训机构管理<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
            <dd>
            <ul>
                <li><a data-href="<?php echo base_url('coach/index'); ?>" data-title="教练员" href="javascript:void(0)">教练员</a></li>
                <li><a data-href="<?php echo base_url('trainingcar/index'); ?>" data-title="训练车" href="javascript:void(0)">训练车</a></li>
                <li><a data-href="<?php echo base_url('examiner/index'); ?>" data-title="考核员" href="javascript:void(0)">考核员</a></li>
                <li><a data-href="<?php echo base_url('securityguard/index'); ?>" data-title="安全员" href="javascript:void(0)">安全员</a></li>
                <li><a data-href="<?php echo base_url('charstandard/index'); ?>" data-title="培训时段" href="javascript:void(0)">收费标准</a></li>
                <li><a data-href="<?php echo base_url('student/index'); ?>" data-title="学员" href="javascript:void(0)">学员</a></li>
                <li><a data-href="<?php echo base_url('device/index'); ?>" data-title="学员" href="javascript:void(0)">计时终端</a></li>
            </ul>
            </dd>
        </dl>

    </div>
</aside>
<div class="dislpayArrow hidden-xs"><a class="pngfix" href="javascript:void(0);" onClick="displaynavbar(this)"></a></div>
<section class="Hui-article-box">
    <div id="Hui-tabNav" class="Hui-tabNav hidden-xs">
        <div class="Hui-tabNav-wp">
            <ul id="min_title_list" class="acrossTab cl">
                <li class="active">
                    <span title="我的桌面" data-href="<?php echo base_url('coach/index'); ?>">我的桌面</span>
                    <em></em>
                </li>
            </ul>
        </div>
        <div class="Hui-tabNav-more btn-group"><a id="js-tabNav-prev" class="btn radius btn-default size-S" href="javascript:;"><i class="Hui-iconfont">&#xe6d4;</i></a><a id="js-tabNav-next" class="btn radius btn-default size-S" href="javascript:;"><i class="Hui-iconfont">&#xe6d7;</i></a></div>
    </div>
    <div id="iframe_box" class="Hui-article">
        <div class="show_iframe">
            <div style="display:none" class="loading"></div>
            <iframe scrolling="yes" frameborder="0" src="<?php echo base_url('coach/index'); ?>"></iframe>
        </div>
    </div>
</section>

<div class="contextMenu" id="Huiadminmenu">
    <ul>
        <li id="closethis">关闭当前 </li>
        <li id="closeall">关闭全部 </li>
    </ul>
</div>
