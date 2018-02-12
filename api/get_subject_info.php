<?php  
    /**
     * 获取专题信息
     * @param 
     * @param
     * @param
     * @return string AES对称加密（加密字段xhxueche）
     * @author gaodacheng
     **/

    require 'Slim/Slim.php';
    require 'include/common.php';
    require 'include/crypt.php';
    \Slim\Slim::registerAutoloader();
    $app = new \Slim\Slim();
    $crypt = new Xcrypt('xhxueche', 'cbc', 'off');
    $app->post('/','getSubjectInfo');
    $app->run();

    // Get subject info
    function getSubjectInfo() {
        global $app, $crypt;
        $request = $app->request();

        /*
         * 布署app接口时，配置参数
         */
        $type = $request->params('special') ? $request->params('special') : 2; //专题ID
        if ($type == 2) {
            $top_banner_id = array(26, 22); //人物和公司
            $special_id = 2; //专栏分类ID
            $online_video_id = 3; //在线教育分类ID
        } else {
            $data = array('code' => -1, 'data' => '参数错误');
            echo json_encode($data);
            exit();
        }

        $res = array(); //接口返回的结果
        try {
            $_online_video_list = array();
            $_special_list = array();
            $_top_banner_list = array();
            $b_list = array();
            $s_list = array();
            $v_list = array();
            $db = getConnection();
            //顶部
            $sql = "SELECT `ID`, `post_excerpt`, `post_title` FROM `cs_posts` WHERE `ID` IN (" . implode(',', $top_banner_id) . ")";
            $stmt = $db->query($sql);
            $top_banner_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($top_banner_list) {
                foreach($top_banner_list as $key => $value) {
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

                    $_top_banner_list[$key]['thumbnail_img'] = $_thumbnail_id;
                    $_top_banner_list[$key]['post_title'] = $value['post_title'];
                    $_top_banner_list[$key]['post_excerpt'] = $value['post_excerpt'];
                    $_top_banner_list[$key]['article_id'] = $value['ID'];
                }
            }

            //中间专栏
            $sql = "select ID,post_title,post_date from cs_posts,cs_term_relationships,cs_term_taxonomy where ID=object_id and cs_term_relationships.term_taxonomy_id = cs_term_taxonomy.term_taxonomy_id and post_type='post' and post_status = 'publish' and cs_term_relationships.term_taxonomy_id = '{$special_id}' and taxonomy = 'category' order by post_date desc limit 5";
            $stmt = $db->query($sql);
            $special_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($special_list) {
                foreach($special_list as $key => $value) {
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

                    $_special_list[$key]['thumbnail_img'] = $_thumbnail_id;
                    $_special_list[$key]['post_title'] = $value['post_title'];
                    $_special_list[$key]['post_date'] = $value['post_date'];
                    $_special_list[$key]['article_id'] = $value['ID'];
                }
            }
            
            //底部在线视频教育
            $sql = "select ID,post_title,post_date, post_content from cs_posts,cs_term_relationships,cs_term_taxonomy where ID=object_id and cs_term_relationships.term_taxonomy_id = cs_term_taxonomy.term_taxonomy_id and post_type='post' and post_status = 'publish' and cs_term_relationships.term_taxonomy_id = '{$online_video_id}' and taxonomy = 'category' order by post_date desc limit 4";
            $stmt = $db->query($sql);
            $online_video_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($online_video_list) {
                foreach($online_video_list as $key => $value) {
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

                    $_online_video_list[$key]['thumbnail_img'] = $_thumbnail_id;
                    $_online_video_list[$key]['post_content'] = $value['post_content'];
                    $_online_video_list[$key]['post_title'] = $value['post_title'];
                    $_online_video_list[$key]['article_id'] = $value['ID'];
                }
            }
            $special['taxonomy_id'] = $special_id;
            $online_video_list['taxonomy_id'] = $online_video_id;

            $b_list = array_values($_top_banner_list);
            $s_list['taxonomy_id'] = $special_id;
            $s_list['list'] = array_values($_special_list);
            $v_list['taxonomy_id'] = $online_video_id;
            $v_list['list'] = array_values($_online_video_list);

            $list = array('info' => $b_list, 'special_list' => $s_list, 'video_list' => $v_list);
            $res = array('code' => 200, 'data' => $list);
            echo json_encode($res);
        } catch (PDOException $e) {
            setapilog('getSubjectInfo:,error:' . $e->getMessage());
            $data = array('code' => 1, 'data' => '网络错误');
            echo json_encode($data);
        }
    }
