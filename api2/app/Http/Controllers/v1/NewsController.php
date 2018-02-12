<?php

/**
 * 媒体模块
 * lumen查询构造器 https://laravel-china.org/docs/5.3/queries
 * @return void
 * @author cx
 **/

namespace App\Http\Controllers\v1;

use Exception;
use Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\v1\IndexController;
use App\Http\Controllers\v1\TimeController;
use App\Http\Controllers\v1\AuthController;

class NewsController extends Controller {

    protected $request;
    protected $auth;

    public function __construct(Request $request) {
        $this->request = $request;
        $this->auth = new AuthController();
    }

    // 获取号外文章列表 随机产生广告连接
    public function getIndexArticleList() {

        $cate_id = '11, 18';  // 嘻哈号外的文章列表
        $limit = 10;
        // $page = $this->request->has('page') ? intval($this->request->input('page')) : 1;
        $page = 1;
        $start = ($page - 1) * $limit;

        $device = $this->request->has('device') ? intval($this->request->input('device')) : 1;
        // 设备 1：安卓 2：ios

        $_data = [];
        // 获取嘻哈号外分类下面的分类
        $category_list = DB::select('SELECT `id`, `title` FROM `xh_category` WHERE `parent_id` IN ('.$cate_id.') GROUP BY `sort` DESC, `id` ASC');
        $_data['cate_list'] = $category_list;
        $_data['news_url'] = 'http://m.xihaxueche.com:8001/v2/student/public/article?device='.$device.'&type=2';
        if(empty($category_list)) {
            return response()->json(['code'=>200, 'msg'=>'当前分类下没有数据', 'data'=>[]]);
        }

        $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$_data];
        return response()->json($data);
    }

    // 获取文章列表
    public function getArticleList() {
        if(!$this->request->has('cate_id')) {
            return response()->json([
                'code'=>400,
                'msg'=>'参数错误',
                'data'=>'',
            ]);
        }
        $_data = [];
        $cate_id = $this->request->has('cate_id') ? $this->request->input('cate_id') : 0;
        $limit = 10;
        $type = $this->request->has('type') ? intval($this->request->input('type')) : 1; // 1:文章 2：问题
        $page = $this->request->has('page') ? intval($this->request->input('page')) : 1;
        $device = $this->request->has('device') ? intval($this->request->input('device')) : 1;
        // 设备 1：安卓 2：ios
        if(!in_array($type, [1,2])) {
            return response()->json([
                'code'=>400,
                'msg'=>'参数错误',
                'data'=>'',
            ]);
        }
        // 获取文章嵌套广告内容
        // $Index = new IndexController($this->request);
        // $ads_obj = $Index->getAdsBannerList()->getData();
        // print_r($ads_obj);

        if($type == 1) {
            $article_page = $this->getArticlePage($cate_id, $limit);
            if( empty($article_page) ) {
                $_data['total'] = 0;
                $_data['last_page'] = 0;
            } else {
                $_data['total'] = $article_page['total'];
                $_data['last_page'] = $article_page['last_page'];
            }
            $_data['per_page'] = $limit;
            $_data['current_page'] = $page;
            $_data['article_list'] = $this->_getArticleList($device, $cate_id, $page, $limit);
        } else {
           $questions_page = $this->getQuestionsPage($cate_id, $limit);
            if( empty($questions_page) ) {
                $_data['total'] = 0;
                $_data['last_page'] = 0;
            } else {
                $_data['total'] = $questions_page['total'];
                $_data['last_page'] = $questions_page['last_page'];
            }
            $_data['per_page'] = $limit;
            $_data['current_page'] = $page;
            $_data['article_list'] = $this->_getQuestionsList($cate_id, $page, $limit);
        }
        $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$_data];
        return response()->json($data);

    }

    // 获取文章列表 公共处理方法
    private function _getArticleList($device, $cid, $page=1, $limit=10) {

        if(trim($cid) == '' || trim($page) == '' || trim($limit) == '' || trim($device) == '') {
            return [];
        }
        $start = ($page - 1) * $limit;
        $article_list = DB::select('SELECT `id`, `title`, `message`, `comments`, `votes`, `views`, `add_time`, `has_attach`, `is_recommend` FROM `xh_article` WHERE `category_id` = :cid AND `lock` = 0 ORDER BY `sort` DESC, add_time DESC, id DESC LIMIT :start, :limit', ['cid' => $cid, 'start' => $start, 'limit' => $limit]);
        $_article_list = [];
        // print_r($article_list);
        if(!empty($article_list)) {
            foreach($article_list as $key => $value) {
                $_article_list[$key]['id'] = $value->id;
                $_article_list[$key]['title'] = trim(str_replace('&amp', '', $value->title));
                $_article_list[$key]['comments'] = $value->comments;
                $_article_list[$key]['votes'] = $value->votes;
                $_article_list[$key]['views'] = $value->views;
                $_article_list[$key]['add_time'] = $value->add_time;
                $_article_list[$key]['has_attach'] = $value->has_attach;
                $_article_list[$key]['is_recommend'] = $value->is_recommend;
                $_article_list[$key]['add_format_time'] = date('Y-m-d', $value->add_time);
                if($value->has_attach == 1) {
                    $pattern = '/\[attach\]([0-9]+)\[\/attach]/';
                    $attach_arr = $this->getAttachList($this->parse_attachs($value->message), 'article');
                    $_article_list[$key]['article_thumb'] = empty($attach_arr) ? '' : $attach_arr[0];
                    $_article_list[$key]['has_thumb'] = 1;
                } else {
                    $_article_list[$key]['article_thumb'] = '';
                    $_article_list[$key]['has_thumb'] = 2; // 1：有缩略图 2：没有缩略图
                }
                $_article_list[$key]['is_ads'] = 1;  // 1: 不是广告 2：是广告

                // 格式化时间
                $Time = new TimeController(time(), $value->add_time);
                $_article_list[$key]['add_time_tag'] = $Time->index();

                // 详情地址
                $_article_list[$key]['article_url'] = 'http://m.xihaxueche.com:8001/v2/student/public/article/detail?device='.$device.'&type=2&cid='.$cid.'&id='.$value->id;
            }
        }
        return $_article_list;
    }

    // 获取分类下的问题列表
    private function _getQuestionsList($cid, $page=1, $limit=10) {
        if(trim($cid) == '' || trim($page) == '' || trim($limit) == '') {
            return [];
        }
        $start = ($page - 1) * $limit;
        $questions_list = DB::select('SELECT `question_id` as id, `question_content` as title, `question_detail` as message, `comment_count` as comments, `agree_count` as votes, `view_count` as views, `add_time`, `has_attach`, `is_recommend` FROM `xh_question` WHERE `category_id` = :cid AND `lock` = 0 ORDER BY `sort` DESC, add_time DESC, question_id DESC LIMIT :start, :limit', ['cid' => $cid, 'start' => $start, 'limit' => $limit]);
        $_questions_list = [];
        if(!empty($questions_list)) {
            foreach($questions_list as $key => $value) {
                $_questions_list[$key]['id'] = $value->id;
                $_questions_list[$key]['title'] = trim(str_replace('&amp', '', $value->title));
                $_questions_list[$key]['comments'] = $value->comments;
                $_questions_list[$key]['votes'] = $value->votes;
                $_questions_list[$key]['views'] = $value->views;
                $_questions_list[$key]['add_time'] = $value->add_time;
                $_questions_list[$key]['has_attach'] = $value->has_attach;
                $_questions_list[$key]['is_recommend'] = $value->is_recommend;
                $_questions_list[$key]['add_format_time'] = date('Y-m-d', $value->add_time);
                if($value->has_attach == 1) {
                    $pattern = '/\[attach\]([0-9]+)\[\/attach]/';
                    $attach_arr = $this->getAttachList($this->parse_attachs($value->message), 'question');
                    $_questions_list[$key]['article_thumb'] = empty($attach_arr) ? '' : $attach_arr[0];
                    $_questions_list[$key]['has_thumb'] = 1;
                } else {
                    $_questions_list[$key]['article_thumb'] = '';
                    $_questions_list[$key]['has_thumb'] = 2; // 1：有缩略图 2：没有缩略图
                }
                $_questions_list[$key]['is_ads'] = 1;  // 1: 不是广告 2：是广告

                // 格式化时间
                $Time = new TimeController(time(), $value->add_time);
                $_questions_list[$key]['add_time_tag'] = $Time->index();

                // 详情地址
                $_questions_list[$key]['article_url'] = 'http://news.xihaxueche.com:8001/?/m/article/'.$value->id;
            }
        }
        return $_questions_list;
    }

    // 获取文章总数以及分页数
    private function getArticlePage($catid, $limit=10) {
        if(trim($catid) == '') {
            return [];
        }
        $data = [];
        $article_page = DB::select('SELECT count(1) as total FROM `xh_article` WHERE `category_id` = :cid AND `lock` = 0', ['cid' => $catid]);
        $pageTotal = ceil($article_page[0]->total / $limit);
        $data['total'] = $article_page[0]->total;
        $data['last_page'] = $pageTotal;
        return $data;
    }

    // 获取问题总数以及分页数
    private function getQuestionsPage($catid, $limit=10) {
        if(trim($catid) == '') {
            return [];
        }
        $data = [];
        $questions_page = DB::select('SELECT count(1) as total FROM `xh_question` WHERE `category_id` = :cid AND `lock` = 0', ['cid' => $catid]);
        $pageTotal = ceil($questions_page[0]->total / $limit);
        $data['total'] = $questions_page[0]->total;
        $data['last_page'] = $pageTotal;
        return $data;
    }

    // 获取文章详情
    public function getArticleDetail() {
        if(!$this->request->has('id')) {
            return response()->json([
                'code'=>400,
                'msg'=>'参数错误',
                'data'=>'',
            ]);
        }
        $id = $this->request->has('id') ? intval($this->request->input('id')) : 0;

        $article_info = DB::select('SELECT `title`, `uid`, `message`, `views`, `votes`, `category_id`, `add_time` FROM `xh_article` WHERE `id` = :aid AND `lock` = 0', ['aid' => $id]);

        if(empty($article_info)) {
            return response()->json(['code'=>400, 'msg'=>'当前文章不存在或链接已失效', 'data'=>[]]);
        }

        $title = $article_info[0]->title;
        $title = str_replace('&amp', '', $title);
        $article_info[0]->title = $title;
        $pattern = '/\[attach\]([0-9]+)\[\/attach]/';
        $attach_arr = $this->getAttachList($this->parse_attachs($article_info[0]->message),'article');

        if(!empty($attach_arr)) {
            foreach($attach_arr as $key => $value) {
                $attach_arr[$key] = '<div style="width:100%; text-align:center;"><img style="padding:10px 0px; max-width:700px;" src="'.$value.'"></></div>';
            }
            $attach_unformat_arr = $this->parse_attachs($article_info[0]->message, false);
            $message = str_replace($attach_unformat_arr, $attach_arr, $article_info[0]->message);

        } else {
            $message = $article_info[0]->message;
        }
        $message = preg_replace('/(\r\n|\n)/', '<br/>', $message);

        $user_info = DB::select('SELECT `user_name` FROM `xh_users` WHERE `uid` = :_uid', ['_uid' => $article_info[0]->uid]);
        $article_info[0]->user_name = $user_info[0]->user_name;
        $article_info[0]->message = $message;
        $article_info[0]->add_format_time = date('Y-m-d H:i:s', $article_info[0]->add_time);

        $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$article_info[0]];
        return response()->json($data);
    }

    // 获取attach id
    private function parse_attachs($str, $attach_type = true) {
        if($attach_type) {
            $pattern = '/\[attach\]([0-9]+)\[\/attach]/';
        } else {
            $pattern = '/(\[attach\][0-9]+\[\/attach])/';
        }
        if(preg_match_all($pattern, $str, $matches)) {
            return array_unique($matches[1]);
        } else {
            return [];
        }
    }

    // 根据attachID获取图片地址
    private function getAttachList($attach_arr, $item_type) {
        if(!is_array($attach_arr) || empty($attach_arr)) {
            return [];
        }
        $attach_str = implode(',', $attach_arr);
        // DB::enableQueryLog();
        $data = DB::select('SELECT `id`, `file_location`, `item_type`, `add_time` FROM `xh_attach` WHERE `id` IN ('.$attach_str.') AND `is_image` = :_is_image AND `item_type` = :_item_type ORDER BY find_in_set(`id`, :attach_str)', ['attach_str' => $attach_str, '_is_image' => 1, '_item_type' => $item_type]);
        // print_r(DB::getQueryLog());
        $list = array();
        if(!empty($data)) {
            foreach($data as $key => $value) {
                $date_dir = gmdate('Ymd', $value->add_time);
                $list[$key] = 'http://news.xihaxueche.com:8001/uploads/'.$value->item_type.'/'.$date_dir.'/'.$value->file_location;
            }
        }
        return $list;
    }

    public function getArticledisOrLike() {
        if(!$this->request->has('type')
            || !$this->request->has('item_id')
            || !$this->request->has('item_uid')
        ) {
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => ''
            ]);
        }
        $type           = $this->request->input('type'); // 类型 1：点赞 2：取消点赞 3：状态查询
        $item_id        = $this->request->input('item_id'); // 文章ID
        $item_uid       = $this->request->input('item_uid'); // 发布文章的发布者ID
        $user           = (new AuthController())->getUserFromToken($this->request->input('token'));
        $user_id        = $user['user_id']; // 学车用户ID
        $phone          = $user['phone'];
        $i_user_type    = $user['i_user_type'];

        // 验证当前文章是否为发布者发布的
        $res = DB::select('SELECT 1 FROM `xh_article` WHERE `id` = :item_id AND `uid` = :item_uid', ['item_id' => $item_id, 'item_uid' => $item_uid]);
        if(empty($res)) {
            return response()->json([
                'code' => 400,
                'msg'  => '当前文章与发布者关联错误',
                'data' => '',
            ]);
        }

        // 根据用户手机号获取用户威望等信息
        $hw_user_info = DB::select('SELECT user.`reputation_group`, ugroup.`reputation_factor`, user.`uid`  FROM `xh_users` AS user LEFT JOIN `xh_users_group` AS ugroup ON user.`reputation_group` = ugroup.`group_id` WHERE user.`forbidden` = :_forbidden AND user.`mobile` = :user_mobile', ['_forbidden' => 1, 'user_mobile' => $phone]);

        $reputation_factor = isset($hw_user_info[0]->reputation_factor) ? $hw_user_info[0]->reputation_factor : 1;
        // $hw_user_id = isset($hw_user_info[0]->uid) ? $hw_user_info[0]->uid : $user_id;
        // 查询有无点赞
        $vote_info = DB::select('SELECT `id` FROM `xh_article_vote` WHERE `uid` = :_uid AND `item_id` = :_item_id', ['_uid'=>$user_id, '_item_id'=>$item_id]);

        $data = [
            'code' => 400,
            'msg'  => '操作错误',
            'data' => '',
        ];
        switch ($type) {
        case '1': // 点赞
            if(empty($vote_info)) {
                $params = ['_uid'=>$user_id, '_type' => 'article', '_item_id' => $item_id, '_rating' => 1, '_time' => time(), '_reputation_factor' => $reputation_factor, '_item_uid' => $item_uid];

                DB::beginTransaction();
                // 在article_votes表中新增一条记录
                $res = DB::insert('INSERT INTO `xh_article_vote` (`uid`, `type`, `item_id`, `rating`, `time`, `reputation_factor`, `item_uid`) VALUES (:_uid, :_type, :_item_id, :_rating, :_time, :_reputation_factor, :_item_uid)', $params);

                // 更改article表 votes+1
                $_res = DB::update('UPDATE `xh_article` SET `votes` = `votes` + 1 WHERE `id` = :_id', ['_id' => $item_id]);


                if($res && $_res) {
                    // 获取votes
                    $votes_info = DB::select('SELECT `votes` FROM `xh_article` WHERE `id` = :_id', ['_id' => $item_id]);
                    $votes = isset($votes_info[0]->votes) ? $votes_info[0]->votes : 0;
                    $data = ['code'=>200, 'msg'=>'点赞成功', 'data'=>['status'=>1, 'votes' => $votes]];
                    DB::commit();
                } else {
                    $data = ['code'=>400, 'msg'=>'点赞失败', 'data'=>''];
                    DB::rollBack();
                }

            } else {
                $data = ['code'=>200, 'msg'=>'已点过赞', 'data'=>''];
            }
            break;

        case '2': // 取消点赞
            if(!empty($vote_info)) {
                $params = ['_uid' => $user_id, '_item_id' => $item_id];

                DB::beginTransaction();
                $res = DB::delete('DELETE FROM `xh_article_vote` WHERE `uid` = :_uid AND `item_id` = :_item_id', $params);

                $_res = DB::update('UPDATE `xh_article` SET `votes` = `votes` - 1 WHERE `id` = :_id', ['_id' => $item_id]);
                if($res && $_res) {
                    $votes_info = DB::select('SELECT `votes` FROM `xh_article` WHERE `id` = :_id', ['_id' => $item_id]);
                    $votes = isset($votes_info[0]->votes) ? $votes_info[0]->votes : 0;
                    $data = ['code'=>200, 'msg'=>'取消点赞成功', 'data'=>['status'=>2, 'votes' => $votes]];
                    DB::commit();
                } else {
                    $data = ['code'=>400, 'msg'=>'取消点赞失败', 'data'=>''];
                    DB::rollBack();
                }

            } else {
                $data = ['code'=>400, 'msg'=>'取消点赞操作失败', 'data'=>''];
            }
            break;

        case '3':
            $votes_info = DB::select('SELECT `votes` FROM `xh_article` WHERE `id` = :_id', ['_id' => $item_id]);
            $votes = isset($votes_info[0]->votes) ? $votes_info[0]->votes : 0;
            if(!empty($vote_info)) {
                $data = ['code'=>200, 'msg'=>'当前是已点赞状态', 'data'=>['status'=>1, 'votes' => $votes]]; // 已点赞状态
            } else {
                $data = ['code'=>200, 'msg'=>'当前是未点赞状态', 'data'=>['status'=>2, 'votes' => $votes]]; // 未点赞状态
            }
            break;
        default:
            $data = ['code'=>400, 'msg'=>'参数错误', 'data'=>''];
            return response()->json($data);
            break;
        }
        return response()->json($data);

    }

    // 获取拿驾照下面的文章列表
    public function getGraduateInfo() {
        if(!$this->request->has('device')) {
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => '',
            ]);
        }
        $device = $this->request->input('device');
        if(!in_array($device, ['1', '2', '3'])) {
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => '',
            ]);
        }

        $type = 2; // 1：话题 2：文章
        $_data = [
            'notices_info' => [
                [
                    'id' => 149,
                    'title' => '驾照过期换证',
                    'article_url' => 'http://m.xihaxueche.com:8001/v2/student/public/article/detail?device='.$device.'&type='.$type.'&cid=60&id=149'
                ],
                [
                    'id' => 141,
                    'title' => '补办的办法',
                    'article_url' => 'http://m.xihaxueche.com:8001/v2/student/public/article/detail?device='.$device.'&type='.$type.'&cid=60&id=141'
                ],
                [
                    'id' => 139,
                    'title' => '驾照遗失补办',
                    'article_url' => 'http://m.xihaxueche.com:8001/v2/student/public/article/detail?device='.$device.'&type='.$type.'&cid=60&id=139'
                ],
                [
                    'id' => 151,
                    'title' => '年龄增长换证',
                    'article_url' => 'http://m.xihaxueche.com:8001/v2/student/public/article/detail?device='.$device.'&type='.$type.'&cid=60&id=151'
                ]
            ],
            'new_info' => [
                [
                    'id' => 153,
                    'title' => '上路时需要注意哪些',
                    'article_url' => 'http://m.xihaxueche.com:8001/v2/student/public/article/detail?device='.$device.'&type='.$type.'&cid=61&id=153'
                ],
                [
                    'id' => 154,
                    'title' => '开车时应该注意的要点',
                    'article_url' => 'http://m.xihaxueche.com:8001/v2/student/public/article/detail?device='.$device.'&type='.$type.'&cid=61&id=154'
                ],
                [
                    'id' => 155,
                    'title' => '停车时注意的事项',
                    'article_url' => 'http://m.xihaxueche.com:8001/v2/student/public/article/detail?device='.$device.'&type='.$type.'&cid=61&id=155'
                ],
                [
                    'id' => 156,
                    'title' => '开车十大注意事项',
                    'article_url' => 'http://m.xihaxueche.com:8001/v2/student/public/article/detail?device='.$device.'&type='.$type.'&cid=61&id=156'
                ]
            ]
        ];

        $data = ['code'=>200, 'msg'=>'获取拿驾照数据成功', 'data'=>$_data];
        return response()->json($data);
    }

    /**
     * Options请求错误的解决办法
     */
    public function optionsArticleDetail()
    {
        Log::info('[options] request hit');
        return response()->json(['code' => 200, 'msg' => 'ok',  'data' => new \stdClass,]);
    }

}
?>
