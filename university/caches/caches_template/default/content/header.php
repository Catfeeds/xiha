<?php defined('IN_PHPCMS') or exit('No permission resources.'); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php if(isset($SEO['title']) && !empty($SEO['title'])) { ?><?php echo $SEO['title'];?><?php } ?><?php echo $SEO['site_title'];?></title>
<link rel="stylesheet" href="<?php echo PLUGIN_STATICS_PATH;?>swiper/css/swiper.min.css" />
<script src="<?php echo PLUGIN_STATICS_PATH;?>swiper/js/swiper.min.js"></script>
<link type="text/css" href="<?php echo CSS_PATH;?>system-2.css" tppabs="http://www.ahu.edu.cn/_css/_system/system.css" rel="stylesheet"/>
<link href="<?php echo CSS_PATH;?>system-1.css" tppabs="http://www.ahu.edu.cn/_css/tpl2/system.css" type="text/css" rel="stylesheet"> 
<link href="<?php echo CSS_PATH;?>system.css" tppabs="http://www.ahu.edu.cn/_css/tpl2/system.css" type="text/css" rel="stylesheet"> 
<link href="<?php echo CSS_PATH;?>default.css" tppabs="http://www.ahu.edu.cn/_css/tpl2/default/default.css" type="text/css" rel="stylesheet"> 
<link type="text/css" href="<?php echo CSS_PATH;?>simplenews.css" tppabs="http://www.ahu.edu.cn/_js/_portletPlugs/simpleNews/css/simplenews.css" rel="stylesheet" />
<link type="text/css" href="<?php echo CSS_PATH;?>sudyNav.css" tppabs="http://www.ahu.edu.cn/_js/_portletPlugs/sudyNavi/css/sudyNav.css" rel="stylesheet" />
<meta name="keywords" content="<?php echo $SEO['keyword'];?>">
<meta name="description" content="<?php echo $SEO['description'];?>">
<link href="<?php echo CSS_PATH;?>reset.css" rel="stylesheet" type="text/css" />
<link href="<?php echo CSS_PATH;?>default_blue.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="<?php echo JS_PATH;?>jquery-3.2.1.min.js" tppabs="http://www.ahu.edu.cn/_js/jquery.min.js" sudy-wp-context="" sudy-wp-siteId="6"></script>

<script type="text/javascript" src="<?php echo JS_PATH;?>datepicker_lang_HK.js" tppabs="http://www.ahu.edu.cn/_js/_portletPlugs/datepicker/js/datepicker_lang_HK.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery.sudyNav.js" tppabs="http://www.ahu.edu.cn/_js/_portletPlugs/sudyNavi/jquery.sudyNav.js"></script>
<link rel="stylesheet" href="<?php echo CSS_PATH;?>style.css" tppabs="http://www.ahu.edu.cn/_upload/tpl/00/0f/15/template15/style.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo CSS_PATH;?>jia.css" tppabs="http://www.ahu.edu.cn/_upload/tpl/00/0f/15/template15/css/jia.css" type="text/css" media="all" />
<script type="text/javascript" src="<?php echo JS_PATH;?>jia.js" tppabs="http://www.ahu.edu.cn/_upload/tpl/00/0f/15/template15/js/jia.js">
</script>
<link rel="stylesheet" href="<?php echo CSS_PATH;?>extends.css" tppabs="http://www.ahu.edu.cn/_upload/tpl/00/0f/15/template15/extends/extends.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo CSS_PATH;?>debris.css" tppabs="http://www.ahu.edu.cn/_upload/tpl/00/0f/15/template15/css/debris.css" type="text/css" media="all" />

<!--  -->

</head>
<body>
<div class="wrapper" id="header-1" style="position:absolute; left:0px; top:0px; z-index:1000;">
    <div class="inner">
        <div class="header-1-r">
            <div class="header-1-r-3" frag="面板8">
                <div frag="窗口8">
                    <!-- No Data -->
                </div>
            </div>
            <div class="header-1-r-1" frag="面板2">
                <div frag="窗口2">
                    <div id="wp_nav_w2"> 
                        <ul class="wp_nav" data-nav-config="{drop_v: 'down', drop_w: 'right', dir: 'y', opacity_main: '-1', opacity_sub: '-1', dWidth: '0'}">
                              
                             <li class="nav-item i3 "> 
                                 <a href="http://www1.ahu.edu.cn/sjxx/" tppabs="http://www1.ahu.edu.cn/sjxx/" title="书记信箱" target="_blank"><span class="item-name">书记信箱</span></a><i class="mark"></i> 
                                  
                             </li> 
                              
                             <li class="nav-item i4 "> 
                                 <a href="http://www1.ahu.edu.cn/xzxx/" tppabs="http://www1.ahu.edu.cn/xzxx/" title="校长信箱" target="_blank"><span class="item-name">校长信箱</span></a><i class="mark"></i> 
                                  
                             </li> 
                              
                        </ul> 
                    </div>
                </div>
            </div>
            <div class="header-1-r-2" frag="面板3">
                <div frag="窗口3">
                    <form method="POST" action="http://www.ahu.edu.cn/_web/search/doSearch.do?_p=YXM9NiZ0PTE1JmQ9MTgmcD0xJm09U04m" target="_blank" onsubmit="if ($('#keyword').val() === '请输入关键字') { $('#keyword').val(''); }">
                        <div class="wp_search">
                            <table>
                                <tr>
                                    <td height="25px">
                                        <input id="keyword" name="keyword" style="width: 150px" class="keyword" type="text" value="请输入关键字" onfocus="if (this.value === '请输入关键字') { this.value = ''; }" onblur="if (this.value === '') { this.value = '请输入关键字'; }" />
                                    </td>
                                    <td>
                                        <input name="btnsearch" class="search" type="submit" value=""/>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<div class="wrapper" id="header" style="position:absolute; left:0px; top:46px; z-index:1000;">
    <div class="inner">
        <a class="head-l" href="index.php"></a>
        <div class="head-r" frag="面板4">
            <div frag="窗口4">
                <div id="wp_nav_w4"> 
                    <ul class="wp_nav" data-nav-config="{drop_v: 'down', drop_w: 'left', dir: 'y', opacity_main: '-1', opacity_sub: '-1', dWidth: '0'}">
                        <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=89762d79a4ea5720e809916b057ded38&action=category&catid=0&num=25&siteid=%24siteid&order=listorder+DESC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'category')) {$data = $content_tag->category(array('catid'=>'0','siteid'=>$siteid,'order'=>'listorder DESC','limit'=>'25',));}?>
                        <?php $n=1;if(is_array($data)) foreach($data AS $r) { ?>
                        <li class="nav-item i<?php echo $r['catid'];?> "> 
                            <?php if($r[catid] == 76 || $r[catid] == 77) { ?>
                                <a href="<?php echo $r['url'];?>" tppabs="http://www.ahu.edu.cn/42/list.htm" title="<?php echo $r['catname'];?>" target="_self"><span class="item-name"><?php echo $r['catname'];?></span></a>
                             <?php } else { ?>
                                <a href="javascript:;" tppabs="http://www.ahu.edu.cn/42/list.htm" title="<?php echo $r['catname'];?>" target="_self"><span class="item-name"><?php echo $r['catname'];?></span></a>
                             <?php } ?>
                             <i class="mark"></i> 
                             <ul class="sub-nav"> 
                                <?php if(defined('IN_ADMIN')  && !defined('HTML')) {echo "<div class=\"admin_piao\" pc_action=\"content\" data=\"op=content&tag_md5=3a950c5e169eb3cf98cde58470228be2&action=category&catid=%24r%5Bcatid%5D&num=25&order=listorder+DESC\"><a href=\"javascript:void(0)\" class=\"admin_piao_edit\">编辑</a>";}$content_tag = pc_base::load_app_class("content_tag", "content");if (method_exists($content_tag, 'category')) {$data = $content_tag->category(array('catid'=>$r[catid],'order'=>'listorder DESC','limit'=>'25',));}?>
                                    <?php $n=1;if(is_array($data)) foreach($data AS $v) { ?>
                                        <li class="nav-item i1-1 "> 
                                            <a href="<?php echo $v['url'];?>" tppabs="http://www.ahu.edu.cn/148/list.htm" title="<?php echo $v['catname'];?>" target="_self"><span class="item-name"><?php echo $v['catname'];?></span></a><i class="mark"></i> 
                                        </li> 
                                    <?php $n++;}unset($n); ?>
                                <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                             </ul> 
                         </li> 
                        <?php $n++;}unset($n); ?>
                        <?php if(defined('IN_ADMIN') && !defined('HTML')) {echo '</div>';}?>
                         
                    </ul> 
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>