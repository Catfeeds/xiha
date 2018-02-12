<?php

/**
 * 获取嘻哈头条首页推荐文章列表
 * @param integer $cid 分类id
 * @param integer $page 页码 默认1
 * @return string AES对称加密（加密字段xhxueche）
 * @author gaodacheng
 **/

require 'Slim/Slim.php';
require 'include/common.php';
require 'include/crypt.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
$app->post('/','getCateArticleList');
$app->run();

function getCateArticleList() {
    Global $app, $crypt;
    //返回的结果数组
    $_data = array();
    //configure taxonomy id
    $toutiao_taxonomy_id = 4; // Gao local: 6 头条id
    $hot_taxonomy_id = 8; //热点分类id
    $rand_num = rand(1, 999999);
    $request = $app->request();
    $page = $request->params('page');
    $taxonomy_id = $request->params('cid');
    //如果传错误的page，赋默认值1
    if ( !isset($page) or !is_numeric($page) or $page == 0 ) {
        $page = 1;
    }

    if ( !isset($taxonomy_id) or !is_numeric($taxonomy_id) or $taxonomy_id == 0) {
        $_data['code'] = 101;
        $_data['data'] = '参数错误';
        setapilog('get_cate_article_list: params[cid:' . $taxonomy_id . ',page:' . $page);
        echo json_encode($_data);
        exit;
    }
    $limit = 15; //分页
    $start = ($page - 1) * $limit;
    try {
        $db = getConnection();
        //存放获取所有列表，包括分类列表和热点文章列表
        $list = array();
        // 获取嘻哈头条分类
        $sql = "SELECT ter.`term_id` AS `taxonomy_id`, ter.`name`, tax.`count`, tax.`description`, ter.`slug` FROM `cs_terms` as ter LEFT JOIN `cs_term_taxonomy` as tax ON ter.`term_id` = tax.`term_id` WHERE tax.`taxonomy` = 'category' AND tax.`parent` = '{$toutiao_taxonomy_id}' ORDER BY tax.`count` DESC";
        $stmt = $db->query($sql);
        $term_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //分类列表读取完毕，放进数组
        $list['cate_list'] = $term_list;

        // 获取推荐文章列表(手动推荐)
        // 2）根据接收的分类ID查询“分类关系表”查询文章ID
        $sql = "SELECT re.`object_id` FROM `cs_term_relationships` as re LEFT JOIN `cs_term_taxonomy` as tax ON tax.`term_taxonomy_id` = re.`term_taxonomy_id` WHERE tax.`term_id` = '{$taxonomy_id}' ORDER BY re.`object_id` DESC";		
        $db = getConnection();
        $stmt = $db->query($sql);
        $post_ids = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $post_id = array(0);
        if ($post_ids) {
            foreach ($post_ids as $key => $value) {
                $post_id[] = $value['object_id'];
            }
        }
        // 3）根据查询的文章ID查询cs_posts表中ID，post_date（文章发布日期） post_excerpt（文章摘要），post_title（文章标题）
        $sql = "SELECT p.`ID`, p.`post_author` AS `post_author_id`,u.`display_name` AS `post_author_name`,p.`post_date`,p.`post_excerpt`, p.`post_content`, p.`post_title` FROM `cs_posts` AS `p` LEFT JOIN `cs_users` AS `u` ON p.`post_author` = u.`id` WHERE p.`ID` IN (".implode(',',$post_id).") AND p.`post_status` = 'publish' ORDER BY p.`post_date` DESC LIMIT $start, $limit";		
        //$sql = "SELECT `ID`, `post_author`,`post_date`,`post_excerpt`, `post_title` FROM `cs_posts` WHERE `ID` IN (".implode(',',$post_id).") ORDER BY `post_date` DESC LIMIT $start, $limit";		
        $stmt = $db->query($sql);
        $post_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //文章列表article_list
        $_list = array();
        if($post_list) {
            foreach ($post_list as $key => $value) {
                $resource_info = array();
                $_list[$key]['article_id'] = $value['ID'];//文章id
                $_list[$key]['taxonomy_id'] = $taxonomy_id;//文章分类id
                $_list[$key]['post_author_id'] = $value['post_author_id'];//post_author_id
                $_list[$key]['post_author_name'] = $value['post_author_name'];//post_author_name
                $_list[$key]['is_recommend'] = 2;// 2表示非推荐
                $_list[$key]['post_title'] = $value['post_title'];//文章标题
                $_list[$key]['post_date'] = $value['post_date'];//文章发布时间
                $_list[$key]['post_timestamp'] = strtotime($value['post_date']);//文章发布时间戳

                // post_time_tag  xxx[秒|分钟|小时|天|周|月|年]前
                $time_tag_config = array(
                    'year' => '年前',
                    'month' => '月前',
                    'week' => '周前',
                    'day' => '天前',
                    'hour' => '小时前',
                    'minute' => '分钟前',
                    'second' => '秒前',
                );
                $post_time = $_list[$key]['post_timestamp'];
                $now_time = time();
                if (date('Y', $post_time) != date('Y', $now_time)) {
                    $post_time_tag = ( date('Y', $now_time) - date('Y', $post_time) ).$time_tag_config['year'];
                } else if (date('m', $post_time) != date('m', $now_time)) {
                    $post_time_tag = ( date('m', $now_time) - date('m', $post_time) ).$time_tag_config['month'];
                } else if (date('d', $now_time) - date('d', $post_time) >= 7 ) {
                    $post_time_tag = intval( ( date('d', $now_time) - date('d', $post_time) ) / 7 ).$time_tag_config['week'];
                } else if (date('d', $post_time) != date('d', $now_time)) {
                    $post_time_tag = ( date('d', $now_time) - date('d', $post_time) ).$time_tag_config['day'];
                } else if (date('H', $post_time) != date('H', $now_time)) {
                    $post_time_tag = ( date('H', $now_time) - date('H', $post_time) ).$time_tag_config['hour'];
                } else if (date('m', $post_time) != date('m', $now_time)) {
                    $post_time_tag = ( date('i', $now_time) - date('i', $post_time) ).$time_tag_config['minute'];
                } else {
                    $post_time_tag = ( date('s', $now_time) - date('s', $post_time) ).$time_tag_config['second'];
                }
                $_list[$key]['post_time_tag'] = $post_time_tag; //文章发表时间标记

                $_list[$key]['post_excerpt'] = $value['post_excerpt'];//文章摘要
                //$_list[$key]['post_content'] = $value['post_content'];//文章内容

                // 根据cs_posts表中查出的ID在postmeta表中查询特色图片地址
                $sql = "SELECT m.`meta_value` FROM `cs_posts` as p LEFT JOIN `cs_postmeta` as m ON p.`ID` = m.`post_id` WHERE m.`meta_key` = '_thumbnail_id' AND m.`post_id` = '{$value['ID']}'";	
                $stmt = $db->query($sql);
                $meta_value_info = $stmt->fetch(PDO::FETCH_ASSOC);
                if($meta_value_info) {
                    $meta_value = $meta_value_info['meta_value'];
                    $sql = "SELECT `guid` FROM `cs_posts` WHERE `ID` = '{$meta_value}'";
                    $stmt = $db->query($sql);
                    $guid_info = $stmt->fetch(PDO::FETCH_ASSOC);
                    if($guid_info) {                        
                        $_thumbnail_id = $guid_info['guid'];
                        // 特色图地址
                    } else {
                        $_thumbnail_id = '';
                        // 特色图地址
                    }              
                } else {                    
                    $_thumbnail_id = '';
                }
                //$resource_info['thumbnail_img'] = $_thumbnail_id;

                //匹配文章内容中的图片
                //$pattern = '@src="([^"]+)"@';
                //$pattern = '@original=["|\']([^"]+)["|\'][\ ]+@'; //src 路径可以是双引号也可以是单引号
                $pattern = '@src=["|\']([^"]+)["|\'][\ ]+@'; //src 路径可以是双引号也可以是单引号
                preg_match_all($pattern, $value['post_content'], $matches);
                $content_img = $matches[1];

                //给display_type按$content_img结果赋值
                /*
                 * 1: 标题+缩略图
                 * 2: 标题+三张大图
                 * 3: --
                 * 4: 标题
                 */
                if ( count($content_img) >= 3 ) {
                    $display_type = 2;
                    $resource_info['img_arr'] = array($content_img[0], $content_img[1], $content_img[2]);
                    $resource_info['thumbnail_img'] = '';
                } elseif ( $_thumbnail_id ) {
                    $display_type = 1;
                    $resource_info['thumbnail_img'] = $_thumbnail_id;
                } elseif ( count($content_img) >= 1 ) {
                    $display_type = 1;
                    $resource_info['thumbnail_img'] = $content_img[0];
                } else {
                    $display_type = 4;
                    $resource_info['thumbnail_img'] = '';
                }

                $_list[$key]['display_type'] = $display_type;
                $_list[$key]['is_ads'] = 1; //不是广告推广内容
                $_list[$key]['resource_info'] = $resource_info;

            }
        }
        if ( $_list ) {
            foreach( $_list as $k => $v ) {
                //comment_count according to the article_id
                $sql = "SELECT COUNT(comment.`comment_ID`) AS `comment_count` FROM `cs_comments` AS comment LEFT JOIN `cs_user` AS user ON comment.`user_id` = user.`l_user_id` WHERE comment.`comment_post_id` = {$v['article_id']} AND user.`i_status` = 0 "; // i_status=0-用户正常 2-用户已删除
                //$_list[$k]['sql'] = $sql;
                $stmt = $db->query($sql);
                if ( $stmt ) {
                    $res = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ( $res ) {
                        $_list[$k]['comment_count'] = $res['comment_count'];
                    }
                }

                //get the article's picture in the post_content
            }
        }
        //文章列表信息读出完毕，放进数组
        $list['article_list'] = $_list;

        $_data['code'] = 200;
        $_data['data'] = $list;
        echo json_encode($_data);

    } catch(PDOException $e) {
        setapilog('get_mix_article_list:error:'.$e->getMessage());
        //$data = array('code'=>1, 'data'=>'网络错误' , 'msg' => $e->getMessage());
        $data = array('code'=>1, 'data'=>'网络错误');
        echo json_encode($data);
        // echo $crypt->encrypt(json_encode($data));
    }

}
