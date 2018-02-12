<?php 

/**
 * 获取更多评论接口
 * @param integer $id 文章id 
 * @param integer $user_id 用户id 
 * @param integer $page 页数 
 * @param integer $hot 热评（传入几给予几条热评） 
 * @return object
 * @author sunweiwei
 **/

require 'Slim/Slim.php';
require 'include/common.php';
require 'include/crypt.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$crypt = new Xcrypt('xhxueche', 'cbc', 'off');
$app->post('/','getMoreComments');
$app->run();
function getMoreComments() {
    Global $app, $crypt;
    $request = $app->request();
    $comment_post_ID = $request->params('id');//文章id
    $user_id = $request->params('user_id');//用户id
    $comment_approved = 1;    // 允许发表的评论（后台审核）
    $page = $request->params('page');
    $hot = $request->params('hot');//热评
    //如果传错误的page，赋默认值1
    if ( !isset($page) or !is_numeric($page) or $page == 0 ) {
        $page = 1;
    }
    if (!isset($hot)) {
        $hot = 0;
    } else if (!is_numeric($hot)) {
        $data['code'] = 102;
        $data['data'] = '参数错误';
        setapilog('article_comment: params[hot:' . $hot . '], code:102, error:参数类型不符');
        echo json_encode($data);
        exit ;
    }

    $limit = 10;
    $start = ($page - 1) * $limit;
    $list = array();
    try {
        $db = getConnection();
        if($hot != 0 && $hot < $limit) {
            // 获取多条评论点赞最多的的热评
            $sql = " select c.user_id, d.comment_id, c.`comment_content`, c.`comment_parent`, c.`comment_date`, count(1) as num from cs_like_dislike_comments as d left join cs_comments as c on d.comment_id = c.comment_ID left join cs_user as u on c.user_id = u.l_user_id ";
            $sql .= " WHERE u.i_status = 0 AND c.`comment_post_ID` = '{$comment_post_ID}' group by d.`comment_id` order by num DESC, c.`comment_id` DESC LIMIT {$hot}";
            $stmt = $db->query($sql);
            $comment_ids = array(0);
            $extra_comment_list = array();
            $top_comment_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if($top_comment_list) {
                if (count($top_comment_list) > 0) {
                    foreach ($top_comment_list as $key => $value) {
                        if (isset($value['comment_id']) && intval($value['comment_id']) > 0) {
                            $comment_ids[] = $value['comment_id'];
                        } else {
                            unset($top_comment_list[$key]);
                        }
                    }
                }

                // 获取不够热评数的其余评论数
                if(count($top_comment_list) < $hot) {
                    $_limit = $hot - count($top_comment_list);
                    $sql = " SELECT c.`comment_ID` AS comment_id, c.`comment_content`, c.`comment_parent`, c.`comment_date`, c.`user_id` FROM `cs_comments` as c left join `cs_user` as u on u.l_user_id = c.user_id left join cs_users_info as i on c.user_id = i.user_id ";
                    $sql .= " WHERE u.i_status = 0 AND c.`comment_ID` NOT IN (".implode(',', $comment_ids).") AND c.`comment_post_ID` = '{$comment_post_ID}' AND c.`comment_approved` = '{$comment_approved}' ORDER BY c.`comment_ID` DESC LIMIT {$_limit}";
                    $stmt = $db->query($sql);
                    $extra_comment_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }

                $list = array_merge($top_comment_list, $extra_comment_list);

            } else {
                // 所有评论都没点赞
                $sql = " SELECT c.`comment_ID` AS comment_id, c.`comment_content`, c.`comment_parent`, c.`comment_date`, c.`user_id` FROM `cs_comments` as c left join `cs_user` as u on u.l_user_id = c.user_id left join cs_users_info as i on c.user_id = i.user_id ";
                $sql .= " WHERE u.i_status = 0 AND c.`comment_post_ID` = '{$comment_post_ID}' AND c.`comment_approved` = '{$comment_approved}' ORDER BY c.`comment_ID` DESC LIMIT {$hot}";
                $stmt = $db->query($sql);
                $_top_comment_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $list = $_top_comment_list;
            }

        } else {
            // 获取所有的评论
            $sql = "SELECT c.`comment_ID` AS comment_id, c.`comment_content`, c.`comment_parent`, c.`comment_date`, c.`user_id` FROM `cs_comments` as c left join cs_user as u on u.l_user_id = c.user_id left join cs_users_info as i on c.user_id = i.user_id WHERE u.i_status = 0 AND  c.`comment_post_ID` = '{$comment_post_ID}' AND c.`comment_approved` = '{$comment_approved}' ORDER BY c.`comment_ID` DESC";
            $sql .= " LIMIT {$start}, {$limit}";
            $stmt = $db->query($sql);
            $all_comment_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $list = $all_comment_list;
        }
        $_list = array();
        if($list) {
            foreach ($list as $key => $value) {

                //获取评论用户昵称 和头像id
                $sql = "SELECT u.`s_username`, i.`photo_id`, i.`user_photo` FROM `cs_user` as u LEFT JOIN `cs_users_info` as i ON i.`user_id` = u.`l_user_id` WHERE u.`l_user_id` = '{$value['user_id']}' AND u.`i_status` = 0 "; // i_status=0-正常用户 2-已删除用户，已删除的用户的评论作废
                $stmt = $db->query($sql);
                $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user_info) {
                    $_list[$key]['user_name'] = (trim($user_info['s_username']) == '') ? '未设置昵称' : trim($user_info['s_username']);//用户昵称
                    $_list[$key]['user_photo_id'] = (intval($user_info['photo_id']) <= 0) ? 1 : intval($user_info['photo_id']);//选择的用户头像id
                    $_list[$key]['user_photo'] = $user_info['user_photo'] == '' ? "" : HTTP_HOST.$user_info['user_photo'];//自己上传的头像路径
                } else {
                    //continue;
                    $_list[$key]['user_name'] = '未设置昵称';
                    $_list[$key]['user_photo_id'] = 1;
                    $_list[$key]['user_photo'] = '';
                }

                $_list[$key]['comment_id'] = $value['comment_id'];//评论id
                $_list[$key]['id'] = $comment_post_ID;//文章id
                $_list[$key]['content'] = $value['comment_content'];//评论内容
                $_list[$key]['parent_id'] = $value['comment_parent'];//评论父id
                $_list[$key]['date'] = strtotime($value['comment_date']);//评论时间
                $_list[$key]['user_id'] = $value['user_id'];//用户id

                //获取点赞数
                $sql = "SELECT COUNT(d.`id`) AS num FROM `cs_like_dislike_comments` as d left join cs_user as u on d.user_id = u.l_user_id WHERE u.`i_status` = 0 AND d.`comment_id` = '".$value['comment_id']."'";
                $stmt = $db->query($sql);
                $like_info = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($like_info) {
                    $_list[$key]['like_num'] = intval($like_info['num']);//评论点赞数
                } else {
                    $_list[$key]['like_num'] = 0;
                }

                //判断当前用户是否对评论点赞
                $sql = "SELECT `id` FROM `cs_like_dislike_comments` WHERE `comment_id` = '{$value['comment_id']}' AND `user_id` = '{$user_id}' AND `rate_like_value` = 1";				
                $stmt = $db->query($sql);
                $is_like = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($is_like) {
                    $_list[$key]['is_like'] = 1;//当前用户对评论点赞
                } else {
                    $_list[$key]['is_like'] = 0;
                }
            }
        }
        $db = null;
        $data = array('code'=>200, 'data'=>array_values($_list));
        echo json_encode($data);

    } catch (PDOException $e) {
        setapilog('more_comments: '.json_encode($app->request()->params()).' error: '.$e->getLine().' PDO数据库错误');
        $data = array('code' => 1, 'data' => '网络错误');
        echo json_encode($data);
    }
}

?>
