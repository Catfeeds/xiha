<?php
/**
 * 用户模块
 */

namespace App\Http\Controllers\v3\student;

use Exception;
use InvalidArgumentException;
use Log;
use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\AuthController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;

class DriveController extends Controller {
    
    /*
     * 请求对象主体
     */
    protected $request;
    protected $auth;

    public function __construct(Request $request) {
        $this->request = $request;
        $this->auth = new AuthController($this->request);;
    }

    /**
     * 获取题数
     * @param   string  $car_type   car（小车），truck（货车），bus（客车），moto（摩托车），wangyue（网约车），huoyun（货运），weixian（危险品）， jiaolian（教练员），chuzu（出租车），keyun（客运车）
     * @return void
     **/
    public function getQuestionCount()
    {
        if( ! $this->request->has('car_type')) 
        {
            Log::error("缺少必须参数car_type");
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => new \stdClass
            ]);
        }

        $car_type_arr = ['car', 'truck', 'bus', 'moto', 'wangyue', 'huoyun', 'weixian', 'jiaolian', 'chuzu', 'keyun'];
        $car_type = $this->request->input('car_type') ? trim($this->request->input('car_type')) : '';
        if ( ! in_array($car_type, $car_type_arr)) {
            Log::error("牌照类型不在规定范围类");
            return response()->json([
                'code' => 400,
                'msg'  => '牌照错误',
                'data' => new \stdClass
            ]);
        }

        $list = [];
        // 驾驶证(科目一，科目四)
        if ( in_array($car_type, ['car', 'truck', 'bus', 'moto'])) {

            $kemu1_count = $this->getQuestionsCount($car_type, 'kemu1');
            $kemu4_count = $this->getQuestionsCount($car_type, 'kemu4');
            $list = [
                'type' => 1,
                'kemu1_count' => $kemu1_count,
                'kemu4_count' => $kemu4_count,
            ];

        } else {
            // 资格证(资格证)
            $count = $this->getQuestionsCount($car_type);
            $list = [
                'type' => 2,
                'count' => $count
            ];
        }

        return response([
            'code' => 200,
            'msg' => '获取成功',
            'data' => $list,
        ]);

    }


    /**
     * 获取题库列表
     * @param 
     * @return void
     **/
    public function getQuestionLicenseList()
    {

        $list = [];
        $driveList = [
            ['id' => 1, 'car_type' => 'car', 'title' => '小车', 'license' => 'C1/C2/C3'],
            ['id' => 2, 'car_type' => 'truck', 'title' => '货车', 'license' => 'A2/B2'],
            ['id' => 3, 'car_type' => 'bus', 'title' => '客车', 'license' => 'A1/A3/B1'],
            ['id' => 4, 'car_type' => 'moto', 'title' => '摩托车', 'license' => 'D/E/F'],
        ];
        $certList = [
            ['id' => 5, 'car_type' => 'wangyue', 'title' => '网约车', 'license' => ''],
            ['id' => 6, 'car_type' => 'huoyun', 'title' => '货运', 'license' => ''],
            ['id' => 7, 'car_type' => 'weixian', 'title' => '危险品', 'license' => ''],
            ['id' => 8, 'car_type' => 'jiaolian', 'title' => '教练员', 'license' => ''],
            ['id' => 9, 'car_type' => 'chuzu', 'title' => '出租车', 'license' => ''],
            ['id' => 10, 'car_type' => 'keyun', 'title' => '客运', 'license' => ''],
        ];

        return response([
            'code' => 200,
            'msg' => '获取成功',
            'data' => [
                'drivelist' => $driveList,
                'certlist' => $certList,
            ]
        ]);

    }


    /**
     * 设置题库web
     * @param 
     * @return void
     **/
    public function getQuestionSetting()
    {
        $course_arr  = ['kemu1', 'kemu4', 'zigezheng'];
        if( ! $this->request->has('course')) 
        {
            Log::error("缺少必须参数course科目");
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => new \stdClass
            ]);
        }

        $course = $this->request->input('course') ? trim($this->request->input('course')) : '';
        if ( ! in_array($course, $course_arr)) {
            Log::error("科目不在规定范围类");
            return response()->json([
                'code' => 400,
                'msg'  => '科目错误',
                'data' => new \stdClass
            ]);
        }

        $list = [];
        $licenseList = new \stdClass;
        $list = [
            ['car_type' => 'car', 'updatetime' => '2017/08/31'],
            ['car_type' => 'truck', 'updatetime' => '2017/08/31'],
            ['car_type' => 'bus', 'updatetime' => '2017/08/31'],
            ['car_type' => 'moto', 'updatetime' => '2017/08/31'],
            ['car_type' => 'wangyue', 'updatetime' => '2017/08/31'],
            ['car_type' => 'huoyun', 'updatetime' => '2017/08/31'],
            ['car_type' => 'weixian', 'updatetime' => '2017/08/31'],
            ['car_type' => 'jiaolian', 'updatetime' => '2017/08/31'],
            ['car_type' => 'chuzu', 'updatetime' => '2017/08/31'],
            ['car_type' => 'keyun', 'updatetime' => '2017/08/31'],
        ];

        foreach ($list as $index => $licenseinfo) {
            $car_type = $licenseinfo['car_type'];
            $count = $this->getQuestionsCount($car_type, $course);
            $list[$index]['count'] = $count;            
        }
       
        return response([
            'code' => 200,
            'msg' => '获取成功',
            'data' => $list,
        ]);
    }

    // 获取题目数量，通过牌照和科目
    public function getQuestionsCount($car_type, $course = '')
    {
        if ($course == '') {
            $map = ['car_type' => $car_type,];
        } else {
            $map = [
                'car_type' => $car_type,
                'course' => $course,
            ];
        }

        $count = 0;
        $count = DB::table('questions')
            ->where($map)
            ->count();
        return $count;
    }


    /**
     * 获取题目id
     * @param    string must $car_type     car（小车），truck（货车），bus（客车），moto（摩托车），wangyue（网约车），huoyun（货运），weixian（危险品），  jioalian（教练员），chuzu（出租车），keyun（客运车）
     * @param    string must $course       kemu1，kemu4，zigezheng
     * @param    int      $chapter_id   章节ID（在章节练习中点击详情必须传）
     * @param    int      $tag_id       专项练习ID（在专项练习中点击专项练习必须传）
     * @param    int      $rand         0：题目未打乱，1：题目打乱
     * @param    int      $limit        题数（用于模拟考试） 50 | 90 | 100 
     * @return   void;
     **/
    public function getQuestionIdsList()
    {
        $car_type_arr = ['car', 'truck', 'bus', 'moto', 'wangyue', 'huoyun', 'weixian', 'jiaolian', 'chuzu', 'keyun'];
        $course_arr  = ['kemu1', 'kemu4', 'zigezheng'];
        if( ! $this->request->has('car_type') 
            OR ! $this->request->has('course')) 
        {
            Log::error("缺少必须参数牌照和科目信息");
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => new \stdClass
            ]);
        }

        $car_type = $this->request->input('car_type') ? trim($this->request->input('car_type')) : '';
        $course = $this->request->input('course') ? trim($this->request->input('course')) : '';

        if ( ! in_array($car_type, $car_type_arr)) {
            Log::error("牌照类型不在规定范围类");
            return response()->json([
                'code' => 400,
                'msg'  => '牌照错误',
                'data' => new \stdClass
            ]);
        }
        
        if ( ! in_array($course, $course_arr)) {
            Log::error("科目不在规定范围类");
            return response()->json([
                'code' => 400,
                'msg'  => '科目错误',
                'data' => new \stdClass
            ]);
        }
        
        $chapter_id = $this->request->input('chapter_id') ? (int) $this->request->input('chapter_id') : 0;        
        $tag_id = $this->request->input('tag_id') ? (int) $this->request->input('tag_id') : 0;       
        $limit = $this->request->input('limit') ? (int) $this->request->input('limit') : 0;       
        $rand = $this->request->input('rand') ? (int) $this->request->input('rand') : 0;       
        
        $map = [];
        $question_id_arr = [];
        $map = [
            'car_type' => $car_type,
            'course' => $course,
        ];

        $table = 'cs_questions';
        if ( 1 === $rand ) { // 随机
            $question_id_arr = DB::select(
                "select id from {$table} where car_type = :car_type AND course = :course order by rand() * 16251", $map
            );
        } 
        
        if ( 0 !== $chapter_id) { // 章节
            $map['chapter_id'] = $chapter_id;
            $question_id_arr = DB::select(
                "select id from {$table} where car_type = :car_type AND course = :course AND chapter_id = :chapter_id order by rand() * 16251", $map
            );
        }
        
        if ( 0 !== $tag_id) { // 专项
            $complex['tag.tag_id'] = $tag_id;
            $questions_id = DB::table('question_tag_relationship as tag')
                ->select('tag.question_id')
                ->where($map)
                ->where($complex)
                ->get();
            $questions_ids = [];
            if ( ! empty($questions_id)) {
                foreach ($questions_id as $id => $question) {
                    $questions_ids[] = $question->question_id;
                }
            }

            $question_id_arr = DB::table('questions')
                ->select('id')
                ->whereIn('question_id', $questions_ids)
                ->where($map)
                ->get();
        }

        if ( 0 !== $limit) { // 模拟
            $question_id_arr = DB::select(
                "select id from {$table} where car_type = :car_type AND course = :course AND difficulty > 2 order by rand() * 16251 limit {$limit}", $map
            );
        }

        if ( empty($question_id_arr)) { // 顺序练习
            $question_id_arr = DB::table('questions')
                ->select('id')
                ->where($map)
                ->orderBy('id', 'asc')
                ->get();
        }

        $question_ids = '';
        $questionsid = [];
        if ( ! empty($question_id_arr)) {
            foreach ($question_id_arr as $key => $value) {
                $questionsid[] = $value->id;
            }
            $count = count($questionsid);
        }

        return response([
            'code' => 200,
            'msg' => '获取成功',
            'data' => [
                'count' => $count,
                'list' => $questionsid,
            ],
        ]);
    }

    /**
     * 获取题目详情列表
     * @param   string  $question_ids   题目id
     * @return void
     **/
    public function getQuestionsList()
    {
        if ( ! $this->request->has('question_ids')) {
            Log::error("缺少必须参数question_ids");
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => new \stdClass
            ]);
        }

        $question_ids = $this->request->input('question_ids') ? trim($this->request->input('question_ids')) : '0';
        $question_ids_arr = array_filter(explode(',', $question_ids));
        $total = $this->request->input('limit') ? intval($this->request->input('limit')) : 0;
        if (0 === $total) {
            $limit = 25;
        } else {
            $limit = $total;
        }
        $page = 1;
        $start = ($page - 1) * $limit;
        $question_list = DB::table('questions')
            ->whereIn('id', $question_ids_arr)
            ->skip($start)
            ->limit($limit)
            ->get();
        $list = [];
        $count = 0;
        if ( ! empty($question_list)) {
            $count = count($question_list);
            foreach ($question_list as $key => $value) {
                $list[$key]['id'] = $value->id;
                $list[$key]['car_type'] = $value->car_type;
                $list[$key]['course'] = $value->course;
                $list[$key]['chapter_id'] = $value->chapter_id;
                $list[$key]['difficulty'] = $value->difficulty;
                $list[$key]['question'] = $value->question;
                $list[$key]['answer'] = $value->answer;
                $list[$key]['restore_answer'] = $this->restore_answer($value->answer);
                $list[$key]['option_type'] = $value->option_type;
                $list[$key]['difficulty'] = $value->difficulty;
                $list[$key]['explain'] = $value->explain;
                $list[$key]['media_content'] = $value->media_content;
                if ($value->media_content != '') {
                    $list[$key]['media_content'] = env('HTTP_APP_ROOT').'/m/assets/images/v2/'.$value->media_content;
                } 
                $list[$key]['media_height'] = $value->media_height;
                $list[$key]['media_width'] = $value->media_width;
                $list[$key]['wrong_rate'] = $value->wrong_rate;
                $list[$key]['media_type'] = $value->media_type;
                $option_list = [];
                $_option = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
                foreach ($_option as $option) {
                    $k = 'option_' . $option;
                    if ( ! empty($value->{$k})) {
                        $option_list[] = ['tag' => strtoupper($option), 'content' => $value->{$k}];
                    }
                }

                $list[$key]['options'] = array_filter($option_list);
                
            }
        }

        return response([
            'code' => 200,
            'msg' => '获取成功',
            'data' => [
                'count' => $count,
                'list' => $list,
            ],
        ]);
    }

    // 转化答案
    public function restore_answer ($answer) {
        $e = array();
        for ($a = 4; 12 > $a; $a++) {
            $i = $answer & 1 << $a;
            if ($i) {
                $e[] = $i;
            }
        }
        $a = array();
        foreach ($e as $v) {
            if ($v == 16) {
                $t = 'A';
            } elseif ($v == 32) {
                $t = 'B';
            } elseif ($v == 64) {
                $t = 'C';
            } elseif ($v == 128) {
                $t = 'D';
            } elseif ($v == 256) {
                $t = 'E';
            } elseif ($v == 512) {
                $t = 'F';
            } elseif ($v == 1024) {
                $t = 'G';
            } elseif ($v == 2048) {
                $t = 'H';
            }
            if ( ! in_array($t, $a)) {
                $a[] = $t;
            }
        }
        return implode('', $a);
    }

     /**
     * 添加题目的评论
     * @param   string  $token          用户登录标识
     * @param   int     $question_id    题目的主键ID
     * @param   string  $content        评论的内容 
     * @return void
     **/
    public function submitQuestionComments()
    {
        if ( ! $this->request->has('question_id')
            OR ! $this->request->has('content')) 
        {
            Log::error("缺少必须参数");
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => new \stdClass
            ]);
        }

        $user = $this->auth->getUserFromToken($this->request->input('token'));
        $question_id = $this->request->input('question_id') ? intval($this->request->input('question_id')) : 0;
        $content = $this->request->input('content') ? trim($this->request->input('content')) : '';
        $user_id = $user['user_id'];
        $user_phone = $user['phone'];
        $user_name = '嘻哈用户'.$user_phone;
        $user_info = $this->getUserInfo($user_id);
        if ( ! empty($user_info)) {
            $user_name = $user_info->real_name;
        }
        
        $param = [
            'question_id' => $question_id,
            'content' => $content,
            'user_id' => $user_id,
            'user_phone' => $user_phone,
            'user_name' => $user_name,
            'parent_id' => 0,
            'votes' => 0,
            'addtime' => time()
        ];

        $question_info = $this->getQuestionComment($user_id, $question_id, $content);
        if ( ! empty($question_info)) {
            $id = $question_info->id;
            $result = DB::table('user_question_comments')
                ->where('id', $id)
                ->update($param);

            $data = [
                'code' => 200,
                'msg' => '添加成功',
                'data' => $id,
            ];
        } else {
            $result = DB::table('user_question_comments')
                ->insertGetId($param);

            $data = [
                'code' => 200,
                'msg' => '添加成功',
                'data' => $result,
            ];
        }

        return response($data);
    }

    // 获取评论的信息
    public function getQuestionComment($user_id, $question_id, $content)
    {
        $map = [];
        $map = [
            'user_id' => $user_id,
            'question_id' => $question_id,
            'content'   => $content
        ];

        $comments_info = DB::table('user_question_comments')
            ->where($map)
            ->first();
        return $comments_info;
    }

    /**
     * 添加题目评论点赞
     * @param   string  $token      用户登录标识
     * @param   int     $item_id    视频详情的主键ID
     * @param   int     $item_uid   发布者ID
     * @param   string  $type       默认 video 视频
     * @param   void
     **/
    public function submitQuestionLike()
    {
        if ( ! $this->request->input('item_id')
            OR ! $this->request->input('item_uid')
            OR ! $this->request->input('type')) 
        {
            Log::error("缺少必须参数");
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => ''
            ]);
        }

        $user = $this->auth->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $type = $this->request->input('type') ? trim($this->request->input('type')) : 'question';
        $item_id = $this->request->input('item_id') ? intval($this->request->input('item_id')) : '';
        $item_uid = $this->request->input('item_uid') ? intval($this->request->input('item_uid')) : '';
        $video_info = $this->getCommentInfoByCondition($item_id, $item_uid, $type);
        if ( empty($video_info)) {
            Log::error('当前问题不存在');
            return response()->json([
                'code' => 400,
                'msg'  => '当前问题不存在',
                'data' => '',
            ]);
        }        
        
        $time = time();
        $param = [
            'uid'       => $user_id,
            'item_id'   => $item_id,
            'item_uid'  => $item_uid,
            'type'      => $type,
            'add_time'  => $time,
        ];

        $map = ['uid' => $user_id, 'item_id' => $item_id, 'type' => $type];
        $votes_list = $this->getVotesByCondition($map);
        if ( ! empty($votes_list)) {
            $data = [
                'code'  => 200,
                'msg'   => '已点过赞',
                'data'  => ''
            ];
        } else {
            DB::beginTransaction();
            $res = DB::table('like_comments')
                ->insertGetId($param);

            $_res = DB::update('UPDATE `cs_user_question_comments` SET `votes` = `votes` + 1 WHERE `id` = :_id', ['_id' => $item_id]);

            if ($res AND $_res) {
                $video_info = $this->getCommentInfoByCondition($item_id, $item_uid, $type);
                $votes = $video_info->votes;
                $data = ['code'  => 200, 'msg'   => '点赞成功', 'data'  => "{$votes}"];
                DB::commit();
            } else {
                $data = ['code'  => 200, 'msg'   => '点赞失败', 'data'  => ''];
                DB::rollBack();
            }
        }
        return response($data);
    }





    /**
     * 获取章节练习列表
     * @param   string  $car_type   car（小车），truck（货车），bus（客车），moto（摩托车），wangyue（网约车），huoyun（货运），weixian（危险品），  jioalian（教练员），chuzu（出租车），keyun（客运车）
     * @param   string  $course     kemu1，kemu4，zigezheng
     * @return void
     **/
    public function getChaptersList()
    {
        $car_type_arr = ['car', 'truck', 'bus', 'moto', 'wangyue', 'huoyun', 'weixian', 'jiaolian', 'chuzu', 'keyun'];
        $course_arr  = ['kemu1', 'kemu4', 'zigezheng'];
        if( ! $this->request->has('car_type') 
            OR ! $this->request->has('course')) 
        {
            Log::error("缺少必须参数牌照和科目信息");
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => new \stdClass
            ]);
        }

        $car_type = $this->request->input('car_type') ? trim($this->request->input('car_type')) : '';
        $course = $this->request->input('course') ? trim($this->request->input('course')) : '';

        if ( ! in_array($car_type, $car_type_arr)) {
            Log::error("牌照类型不在规定范围类");
            return response()->json([
                'code' => 400,
                'msg'  => '牌照错误',
                'data' => new \stdClass
            ]);
        }
        
        if ( ! in_array($course, $course_arr)) {
            Log::error("科目不在规定范围类");
            return response()->json([
                'code' => 400,
                'msg'  => '科目错误',
                'data' => new \stdClass
            ]);
        }

        $map = [
            'car_type' => $car_type,
            'course' => $course,
        ];

        $chapter_list = DB::table('chapters')
            ->where($map)
            ->orderBy('chapter', 'asc')
            ->get();
        
        $data = [
            'code' => 200,
            'msg' => '获取成功',
            'data' => [
                'count' => count($chapter_list),
                'list' => $chapter_list,
            ]
        ];
        
        return response($data);
    }

    /**
     * 获取章节练习列表
     * @param   string  $car_type   car（小车），truck（货车），bus（客车），moto（摩托车），wangyue（网约车），huoyun（货运），weixian（危险品），  jioalian（教练员），chuzu（出租车），keyun（客运车）
     * @param   string  $course     kemu1，kemu4，zigezheng
     * @return void
     **/
    public function getSpecialList()
    {
        $car_type_arr = ['car', 'truck', 'bus', 'moto', 'wangyue', 'huoyun', 'weixian', 'jiaolian', 'chuzu', 'keyun'];
        $course_arr  = ['kemu1', 'kemu4', 'zigezheng'];
        if( ! $this->request->has('car_type') 
            OR ! $this->request->has('course')) 
        {
            Log::error("缺少必须参数牌照和科目信息");
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => new \stdClass
            ]);
        }

        $car_type = $this->request->input('car_type') ? trim($this->request->input('car_type')) : '';
        $course = $this->request->input('course') ? trim($this->request->input('course')) : '';

        if ( ! in_array($car_type, $car_type_arr)) {
            Log::error("牌照类型不在规定范围类");
            return response()->json([
                'code' => 400,
                'msg'  => '牌照错误',
                'data' => new \stdClass
            ]);
        }
        
        if ( ! in_array($course, $course_arr)) {
            Log::error("科目不在规定范围类");
            return response()->json([
                'code' => 400,
                'msg'  => '科目错误',
                'data' => new \stdClass
            ]);
        }

        $map = [
            'car_type' => $car_type,
            'course' => $course,
        ];

        $tag_list = DB::table('question_tag_relationship')
            ->select('tag_id', 'tag', 'car_type', 'course')
            ->where($map)
            ->distinct('tag')
            ->orderBy('tag_id', 'asc')
            ->get();

        if ( ! empty($tag_list)) {
            foreach ($tag_list as $key => $value) {
                $tag_id = $value->tag_id;
                $count = $this->getSpecialQuestionCount($car_type, $course, $tag_id);
                $tag_list[$key]->count = $count;
                
            }
        }

        $data = [
            'code' => 200,
            'msg' => '获取成功',
            'data' => [
                'count' => count($tag_list),
                'list' => $tag_list,
            ]
        ];
        return response($data);
    }

    // 获取章节练习的题目数
    public function getSpecialQuestionCount($car_type, $course, $tag_id)
    {
        $map = [
            'car_type' => $car_type,
            'course' => $course,
            'tag_id' => $tag_id,
        ];

        $count = DB::table('question_tag_relationship as tag')
            ->where($map)
            ->count();

        return $count;
    }

    /**
     * 添加用户收藏的题目
     * @param   string      $token          用户登录标识
     * @param   int         $questions_id   题目详情表中的主键ID
     * @param   int         $chapter_id     章节ID
     * @return void
     **/
     public function submitCollectionExam()
     {
        $user_info = $this->auth->getUserFromToken($this->request->input('token'));
        $user_id = $user_info['user_id'];
        if ( ! $this->request->has('chapter_id')
            OR ! $this->request->has('questions_id')) 
        {
            Log::error("缺少必须参数");
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => new \stdClass
            ]);
        }

        $questions_id = $this->request->input('questions_id') ? intval($this->request->input('questions_id')) : 0;
        $chapter_id = $this->request->input('chapter_id') ? intval($this->request->input('chapter_id')) : 0;

        $param = [
            'user_id' => $user_id,
            'questions_id' => $questions_id,
            'chapter_id' => $chapter_id,
            'is_show' => 1,
            'addtime' => time(),
        ];

        $collections = $this->checkCollectionsRepeat($user_id, $questions_id);
        if ( NULL == $collections) {
            $result = DB::table('user_questions_collection')
                ->insertGetId($param);
        } else {
            $map = [
                'user_id' => $user_id,
                'questions_id' => $questions_id
            ];

            $result = DB::table('user_questions_collection')
                ->where($map)
                ->update($param);
        }

        $data = [
            'code' => 200,
            'msg' => '添加成功',
            'data' => $result
        ];

        return response($data);
    }

    /**
     * 添加用户做错误的题目
     * @param   string      $token          用户登录标识
     * @param   int         $questions_id   题目详情表中的主键ID
     * @param   int         $chapter_id     章节ID
     * @return void
     **/
     public function submitErrorExam()
     {
        $user_info = $this->auth->getUserFromToken($this->request->input('token'));
        $user_id = $user_info['user_id'];
        if ( ! $this->request->has('chapter_id')
            OR ! $this->request->has('questions_id')) 
        {
            Log::error("缺少必须参数");
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => new \stdClass
            ]);
        }

        $questions_id = $this->request->input('questions_id') ? intval($this->request->input('questions_id')) : 0;
        $chapter_id = $this->request->input('chapter_id') ? intval($this->request->input('chapter_id')) : 0;
        
        $param = [
            'user_id' => $user_id,
            'questions_id' => $questions_id,
            'chapter_id' => $chapter_id,
            'is_show' => 1,
            'addtime' => time(),
        ];

        $errorlist = $this->checkErrorRepeat($user_id, $questions_id);
        if ( NULL == $errorlist) {
            $result = DB::table('user_questions_error')
                ->insertGetId($param);
        } else {
            $map = [
                'user_id' => $user_id,
                'questions_id' => $questions_id
            ];

            $result = DB::table('user_questions_error')
                ->where($map)
                ->update($param);
        }

        $data = [
            'code' => 200,
            'msg' => '添加成功',
            'data' => $result
        ];

        return response($data);
    }

    /**
     * 判断错误的题目是否重复
     * @param   
     * @return void
     **/
     public function checkErrorRepeat($user_id, $questions_id)
     {
         $map = [
             'user_id' => $user_id,
             'questions_id' => $questions_id
         ];
         $list = DB::table('user_questions_error')
             ->where($map)
             ->first();
         return $list;
     }

    /**
     * 判断收藏的题目是否重复
     * @param   
     * @return void
     **/
    public function checkCollectionsRepeat($user_id, $questions_id)
    {
        $map = [
            'user_id' => $user_id,
            'questions_id' => $questions_id
        ];
        $list = DB::table('user_questions_collection')
            ->where($map)
            ->first();
        return $list;
    }

    
    /**
     * 获取我的错题|收藏列表
     * @param   string  $token  用户登录标识
     * @return void
     **/
    public function getErrorCollectionList()
    {
        if ( ! $this->request->has('type')) {
            Log::error("缺少必须参数type");
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => new \stdClass
            ]);
        }

        $type = $this->request->input('type') ? trim($this->request->input('type')) : '';
        if ( ! in_array($type, ['error', 'collection'])) {
            Log::error("type值不在规定范围内");
            return response()->json([
                'code' => 400,
                'msg'  => '参数值错误',
                'data' => new \stdClass
            ]);
        }
        
        $user_info = $this->auth->getUserFromToken($this->request->input('token'));
        $user_id = $user_info['user_id'];
        $map = [];
        $map['user_id'] = $user_id;
        $map['is_show'] = 1; // 未被移除错题
        $title = '';
        if ($type == 'error') {
            $_list = DB::table('user_questions_error')
                ->select('chapter_id')
                ->where($map)
                ->distinct('chapter_id')
                ->get();
            $title = '全部错题';
        } elseif ($type == 'collection') {
            $_list = DB::table('user_questions_collection')
                ->select('chapter_id')
                ->where($map)
                ->distinct('chapter_id')
                ->get();
            $title = '全部收藏';
        }
        $chapter_ids_arr = [];
        $list = [];
        $total = $this->getUserQuestionCount($user_id, $type);
        $total_info = [
            'chapter_id' => 0,
            'chapter_title' => $title,
            'count' => $total
        ];
        if ( ! empty($_list)) {
            foreach ($_list as $key => $chapter) {
                $chapter_id = $chapter->chapter_id;
                $count = $this->getUserQuestionTotal($user_id, $chapter_id, $type);
                $list['total'] = $total_info;
                $list['branch'][$key]['chapter_id'] = $chapter_id;
                $list['branch'][$key]['chapter_title'] = $this->getChapterInfo($chapter_id);
                $list['branch'][$key]['count'] = $count;
            }
        }

        return response([
            'code' => 200,
            'msg' => '获取成功',
            'data' => $list
        ]);
    }        

    // 通过章节ID获取章节信息
    public function getChapterInfo($chapter_id)
    {
        $map = [];
        $map['chapter_id'] = $chapter_id;
        $chapter_info = DB::table('chapters')
            ->where($map)
            ->first();
        $chapter_name = '';
        if ( ! empty($chapter_info)) {
            $chapter_name = $chapter_info->title;
        }

        return $chapter_name;
    }

    // 我的错题|收藏的每个章节总数
    public function getUserQuestionTotal($user_id, $chapter_id, $type = '')
    {
        if ($type == '') {
            return 0;
        }

        $map = [];
        $map['user_id'] = $user_id;
        $map['chapter_id'] = $chapter_id;
        $map['is_show'] = 1;
        $chapter_count = 0;
        if ($type == 'error') {
            $chapter_count = DB::table('user_questions_error')
                ->where($map)
                ->count();

        } elseif ($type == 'collection') {
            $chapter_count = DB::table('user_questions_collection')
                ->where($map)
                ->count();
        }

        return $chapter_count;
    }

    // 我的错题|收藏全部题数
    public function getUserQuestionCount($user_id, $type = '')
    {
        if ($type == '') {
            return 0;
        }

        $map = [];
        $map['user_id'] = $user_id;
        $map['is_show'] = 1;
        $chapter_count = 0;
        if ($type == 'error') {
            $chapter_count = DB::table('user_questions_error')
                ->where($map)
                ->count();
        } elseif ($type == 'collection') {
            $chapter_count = DB::table('user_questions_collection')
            ->where($map)
            ->count();
        }
        return $chapter_count;
    }

    /**
     * 取消错题|收藏
     * @param   string  $token          用户登录标识  
     * @param   int     $questions_id   题目详情表中的主键ID
     * @param   string  $type           类型error(错题),collection(收藏)
     * @return  void    
     **/
    public function cancelErrorCollection()
    {
        if ( ! $this->request->has('type')
            OR ! $this->request->has('questions_id')) 
        {
            Log::error("缺少必须参数");
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => new \stdClass
            ]);
        }

        $type = $this->request->input('type') ? trim($this->request->input('type')) : '';
        if ( ! in_array($type, ['error', 'collection'])) {
            Log::error("type值不在规定范围内");
            return response()->json([
                'code' => 400,
                'msg'  => '参数值错误',
                'data' => new \stdClass
            ]);
        }

        $map = [];
        $questions_id = $this->request->input('questions_id') ? intval($this->request->input('questions_id')) : 0;
        $user_info = $this->auth->getUserFromToken($this->request->input('token'));
        $user_id = $user_info['user_id'];
        $map = [
            'user_id' => $user_id,
            'questions_id' => $questions_id,
        ];
        
        $updatedata = ['is_show' => 0, 'addtime' => time()];

        if ($type == 'error') {
            $result = DB::table('user_questions_error')
                ->where($map)
                ->update($updatedata);
        } else {
            $result = DB::table('user_questions_error')
                ->where($map)
                ->update($updatedata);
        }

        return response([
            'code' => 200,
            'msg' => '取消成功',
            'data' => $result
        ]);
    }

    /**
     * 获取我的错题|收藏题目ID
     * @param   string  $token          用户登录标识
     * @param   int     $chapter_id     类型error(错题), collection(收藏)
     * @param   string  $type           类型error(错题), collection(收藏)
     * @return void
     **/
    public function getUserQuestionIds()
    {
        if ( ! $this->request->has('type')
            OR ! $this->request->has('chapter_id')) 
        {
            Log::error("缺少必须参数");
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => new \stdClass
            ]);
        }

        $type = $this->request->input('type') ? trim($this->request->input('type')) : '';
        if ( ! in_array($type, ['error', 'collection'])) {
            Log::error("type值不在规定范围内");
            return response()->json([
                'code' => 400,
                'msg'  => '参数值错误',
                'data' => new \stdClass
            ]);
        }

        $map = [];
        $chapter_id = $this->request->input('chapter_id') ? intval($this->request->input('chapter_id')) : 0;
        $user_info = $this->auth->getUserFromToken($this->request->input('token'));
        $user_id = $user_info['user_id'];

        if ( $chapter_id == 0) {
            $map = ['is_show' => 1,'user_id' => $user_id];
        } else {
            $map = ['is_show' => 1, 'user_id' => $user_id, 'chapter_id' => $chapter_id];
        }

        if ($type == 'error') {
            $_list = DB::table('user_questions_error')
                ->select('questions_id')
                ->where($map)
                ->get();

        } elseif ($type == 'collection') {
            $_list = DB::table('user_questions_collection')
                ->select('questions_id')
                ->where($map)
                ->get();
        }
        $list = [];
        if ( ! empty($_list)) {
            foreach ($_list as $index => $questionsid) {
                $list[] = $questionsid->questions_id;
            }
        }
        $count = count($list);
        return response([
            'code' => 200,
            'msg' => '获取成功',
            'data' => [
                'count' => $count,
                'list' => $list,
            ]
        ]);
    }

    /**
     * 获取当前用户的考试记录
     * @param   string    $token  用户登录标识
     * @void    void
     **/
    public function getMyExamRecords()
    {
        if ( ! $this->request->has('car_type')
            OR ! $this->request->has('course')) 
        {
            Log::error("缺少必须参数");
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => new \stdClass
            ]);
        }

        $user = $this->auth->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $user_phone = $user['phone'];

        $car_type_arr = ['car', 'truck', 'bus', 'moto', 'wangyue', 'huoyun', 'weixian', 'jiaolian', 'chuzu', 'keyun'];
        $course_arr  = ['kemu1', 'kemu4', 'zigezheng'];
        $car_type = $this->request->input('car_type') ? trim($this->request->input('car_type')) : '';
        $course = $this->request->input('course') ? trim($this->request->input('course')) : '';
        $page = 1;
        $limit = 10;
        if ( ! in_array($car_type, $car_type_arr)) {
            Log::error("牌照类型不在规定范围类");
            return response()->json([
                'code' => 400,
                'msg'  => '牌照错误',
                'data' => new \stdClass
            ]);
        }
        
        if ( ! in_array($course, $course_arr)) {
            Log::error("科目不在规定范围类");
            return response()->json([
                'code' => 400,
                'msg'  => '科目错误',
                'data' => new \stdClass
            ]);
        }

        $map = [
            'user_id' => $user_id,
            'car_type' => $car_type,
            'course' => $course
        ];

        $records_list = DB::table('user_exam_records')
            ->select('id', 'user_id', 'car_type', 'course', 'realname', 'phone_num', 'identify_id', 'error_exam_id', 'score', 'exam_total_time', 'school_id', 'os', 'addtime')
            ->where($map)
            ->orderBy('addtime', 'desc')
            ->paginate();
        if (count($records_list->toArray()['data']) > 0) {
            foreach ($records_list as $index => $records) {
                $time = $records->exam_total_time;
                // 考试时间
                if ($time > 60) {
                    $min = floor($time / 60);
                    $sec = $time - 60 * $min;
                    $total = $min . '分' . $sec . '秒';
                    $records_list[$index]->exam_total_time = $total;
                } else {
                    $records_list[$index]->exam_total_time = $time . '秒';
                }

                if ( ! empty($records->addtime)) {
                    $m = intval(date('m', $records->addtime));
                    $d = intval(date('d', $records->addtime));
                    $records_list[$index]->addtime = $m . '月' . $d . '号';
                }
            }
        }

        $user_score_info = $this->getUserScoreInfo($user_id, $course, $car_type);
        $max_score = $user_score_info['max_score'];
        $avg_score = $user_score_info['avg_score'];
        $qualified_count = $user_score_info['qualified_count'];
        $total_count = $user_score_info['total_count'];

        $data = [
            'code' => 200,
            'msg' => '获取成功',
            'data' => [
                'max_score' => $max_score,
                'avg_score' => $avg_score,
                'qualified_count' => $qualified_count,
                'total_count' => $total_count,
                'list' => $records_list
            ]
        ];
        return response($data);
    }

    // 获取用户最高分数
    public function getUserScoreInfo($user_id, $course, $car_type)
    {
        $map = [];
        $total_count = 0;
        $qualified_count = 0;
        $map = [
            'user_id' => $user_id,
            'course' => $course,
            'car_type' => $car_type
        ];

        $max_score = DB::table('user_exam_records')
            ->where($map)
            ->max('score');
        $avg_score = DB::table('user_exam_records')
            ->where($map)
            ->avg('score');
        $qualified_count = DB::table('user_exam_records')
            ->select('score')
            ->where($map)
            ->where('score', '>', '90')
            ->get();
        $total_count = DB::table('user_exam_records')
            ->select('score')
            ->where($map)
            ->get();
        if ( ! empty($qualified_count)) {
            $qualified_count = count($qualified_count);
        }

        if ( ! empty($qualified_count)) {
            $total_count = count($total_count);
        }
        $avg_score = round($avg_score);
        $list = [];
        $list = [
            'max_score' => $max_score,
            'avg_score' => $avg_score,
            'qualified_count' => $qualified_count,
            'total_count' => $total_count,
        ];

        return $list;

    }

    /**
     * 新增我的考试记录
     * @param   string  $token          用户登录标识 
     * @param   string  $car_type       car（小车），truck（货车），bus（客车），moto（摩托车），wangyue（网约车），huoyun（货运），weixian（危险品），  jiaolian（教练员），chuzu（出租车），keyun（客运车）
     * @param   string  $course         kemu1(科目一)，kemu4(科目四)，zigezheng(资格证)
     * @param   int     $score          考试分数
     * @param   int     $total_time     考试总时间（单位：秒）
     * @param   string  $error_exam_id  错题ID（字符串，逗号隔开）
     * @param   int     $os             1:ios, 2:android, 3:web
     * @return  void
     **/
    public function submitMyExamRecords()
    {
        if ( ! $this->request->has('car_type')
            OR ! $this->request->has('course')
            OR ! $this->request->has('score')
            OR ! $this->request->has('total_time')
            OR ! $this->request->has('os')) 
        {
            Log::error("缺少必须参数");
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => new \stdClass
            ]);
        }

        $user = $this->auth->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $user_phone = $user['phone'];
        
        $car_type_arr = ['car', 'truck', 'bus', 'moto', 'wangyue', 'huoyun', 'weixian', 'jiaolian', 'chuzu', 'keyun'];
        $course_arr  = ['kemu1', 'kemu4', 'zigezheng'];
        $car_type = $this->request->input('car_type') ? trim($this->request->input('car_type')) : '';
        $course = $this->request->input('course') ? trim($this->request->input('course')) : '';

        if ( ! in_array($car_type, $car_type_arr)) {
            Log::error("牌照类型不在规定范围类");
            return response()->json([
                'code' => 400,
                'msg'  => '牌照错误',
                'data' => new \stdClass
            ]);
        }
        
        if ( ! in_array($course, $course_arr)) {
            Log::error("科目不在规定范围类");
            return response()->json([
                'code' => 400,
                'msg'  => '科目错误',
                'data' => new \stdClass
            ]);
        }

        $realname = '';
        $identity_id = '';
        $school_id = 0;
        $score = $this->request->input('score') ? intval($this->request->input('score')) : 0;
        $total_time = $this->request->input('total_time') ? intval($this->request->input('total_time')) : 0;
        $error_exam_ids = $this->request->input('error_exam_id') ? trim($this->request->input('error_exam_id')) : '';
        $os = $this->request->input('os') ? $this->request->input('os') : 'ios';
        $user_info = $this->getUserInfo($user_id);
        if ( ! empty($user_info)) {
            $realname = $user_info->real_name;
            $identity_id = $user_info->identity_id;
            $school_id = $user_info->school_id;
        }

        if ($realname == '') {
            $realname = '嘻哈用户'.$user_phone;
        }
        
        $time = time();
        $param = [
            'user_id' => $user_id,
            'car_type' => $car_type,
            'course' => $course,
            'realname' => $realname,
            'phone_num' => $user_phone,
            'identify_id' => $identity_id,
            'error_exam_id' => $error_exam_ids,
            'score' => $score,
            'exam_total_time' => $total_time,
            'school_id' => $school_id,
            'os' => $os,
            'addtime' => $time
        ];

        $result = DB::table('user_exam_records')
            ->insertGetId($param);
        if ($result > 0) {
            $data = [
                'code' => 200,
                'msg' => '添加成功',
                'data' => $result
            ];

        } else {
            $data = [
                'code' => 200,
                'msg' => '添加成功',
                'data' => $result
            ];
        }

        return response($data);

    }

    // 获取用户信息
    public function getUserInfo($user_id)
    {
        $map = [];
        $map = [
            'user_id' => $user_id,
            'i_user_type' => 0,
            'i_status' => 0,
        ];
        $user_info = DB::table('user')
            ->select(
                'l_user_id',
                's_real_name as real_name',
                'identity_id',
                'photo_id',
                'user_photo',
                'school_id'
            )
            ->leftjoin('users_info as info', 'info.user_id','=', 'user.l_user_id')
            ->where($map)
            ->first();
        if ( ! empty($user_info)) {
            $user_info->user_photo = $this->buildUrl($user_info->user_photo);
        }
        
        return $user_info;
    }

    /**
     * 获取学车视频列表
     * @param   string  $car_type   牌照类型(car, bus, truck, moto)
     * @param   string  $course     科目类型(kemu2 | kemu3)
     * @param   void
     **/
    public function getVideoList()
    {
        $car_type_arr = ['car', 'truck', 'bus', 'moto'];
        $course_arr  = ['kemu2', 'kemu3'];
        if( ! $this->request->has('car_type') 
            OR ! $this->request->has('course')) 
        {
            Log::error("缺少必须参数牌照和科目信息");
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => new \stdClass
            ]);
        }

        $car_type = $this->request->input('car_type') ? trim($this->request->input('car_type')) : '';
        $course = $this->request->input('course') ? trim($this->request->input('course')) : '';

        if ( ! in_array($car_type, $car_type_arr)) {
            Log::error("牌照类型不在规定范围类");
            return response()->json([
                'code' => 400,
                'msg'  => '牌照错误',
                'data' => new \stdClass
            ]);
        }
        
        if ( ! in_array($course, $course_arr)) {
            Log::error("科目不在规定范围类");
            return response()->json([
                'code' => 400,
                'msg'  => '科目错误',
                'data' => new \stdClass
            ]);
        }

        $count = 0;
        $map = [];
        $map = [
            'car_type' => $car_type,
            'course' => $course,
            'is_open' => 1 // 1:open 0:close
        ];

        $video_list = DB::table('video')
            ->where($map)
            ->orderBy('v_order', 'asc')
            ->get();
        if ( ! empty($video_list)) {
            $count = count($video_list);
            foreach ($video_list as $index => $video) {
                $video_list[$index]->pic_url = $this->buildUrl($video->pic_url);
                $video_list[$index]->video_url = $this->buildUrl($video->video_url);
                $video_time = $video->video_time;
                if ($video_time > 60) {
                    $min = floor($video_time / 60);
                    $sec = $video_time - 60 *$min;
                    if ( $sec == 0) {
                        $video_list[$index]->video_time = $min . ':' . '00';
                    } else {
                        $video_list[$index]->video_time = $min . ':' . $sec;
                    }
                } else {
                    $video_list[$index]->video_time = $video_time . ':' . '00';
                }

                if ( empty($video->addtime) AND $video->addtime != 0) {
                    $video_list[$index]->addtime = date('Y-m-d H:i:s', $video->addtime);
                }

                if ( empty($video->updatetime) AND $video->updatetime != 0) {
                    $video_list[$index]->updatetime = date('Y-m-d H:i:s', $video->updatetime);
                }
                
            }
        }

        $data = [
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => [
                'count' => $count,
                'list' => $video_list
            ]
        ];

        return response($data);
    }

    /**
     * 获取学车视频详情
     * @param   string  $token      用户登录标识
     * @param   int     $video_id   学车视频主键ID
     * @return  void
     **/
    public function getVideoDetail()
    {
        if ( ! $this->request->has('video_id')) 
        {
            Log::error("缺少必须参数");
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => new \stdClass
            ]);
        }

        $map = [];
        $video_id = $this->request->input('video_id') ? intval($this->request->input('video_id')) : 0;
        $map = ['video_id' => $video_id];
        $video_list = DB::table('video_comments as detail')
            ->where($map)
            ->orderBy('addtime', 'desc')
            ->get();

        $_list = new \stdClass;
        $_list->count = 0;
        $_list->views = 0;
        $_list->video_title = '';
        $_list->pic_url = '';
        $_list->video_url = '';
        $_list->video_desc = '';
        $_list->list = [];
        $views_info = $this->getViewsByVideoId($video_id);
        if ( ! empty($views_info)) {
            $_list->views = $views_info->views;
            $_list->video_title = $views_info->title;
            $_list->pic_url = $views_info->pic_url;
            $_list->video_url = $views_info->video_url;
            $_list->video_desc = $views_info->video_desc;
        } 

        if ( ! empty($video_list)) {
            $_list->count = count($video_list);
            foreach ($video_list as $video => $detail) {
                $uid = $detail->user_id;
                $user_info = $this->getUserInfo($uid);
                if ( ! empty($user_info)) {
                    $video_list[$video]->photo_id = $user_info->photo_id;
                    $video_list[$video]->user_photo = $user_info->user_photo;
                } else {
                    $video_list[$video]->photo_id = 1;
                    $video_list[$video]->user_photo = '';
                }

                if ( $this->request->has('token')) {
                    $user = $this->auth->getUserFromToken($this->request->input('token'));
                    $user_id = $user['user_id'];
                    $user_phone = $user['phone'];
                    $votes_list = $this->getLikeCommentsByCondition($user_id, $uid, $detail->id, 'video');
                    if ( ! empty($votes_list)) {
                        $video_list[$video]->is_like = 1;
                    } else {
                        $video_list[$video]->is_like = 0;
                    }
                } else {
                    $video_list[$video]->is_like = 0;
                }

                $_list->list[$video] = $detail;

            }
        }
        return response([
            'code' => 200,
            'msg' => '获取成功',
            'data' => $_list
        ]);
    }

    // 获取点赞信息
    public function getLikeCommentsByCondition($uid, $item_uid, $item_id, $type)
    {
        $map = [];
        $map = [
            'uid' => $uid,
            'item_uid' => $item_uid,
            'item_id' => $item_id,
            'type' => $type
        ];

        $votes_list = DB::table('like_comments')
            ->where($map)
            ->first();
        return $votes_list;
    }


    /**
     * 添加学车视频的评论
     * @param   string  $token      用户登录标识
     * @param   int     $video_id   学车视频主键ID
     * @param   string  $content    评论内容 
     * @return void
     **/
    public function submitVideoComments()
    {
        if ( ! $this->request->has('video_id')
            OR ! $this->request->has('content')) 
        {
            Log::error("缺少必须参数");
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => new \stdClass
            ]);
        }

        $user = $this->auth->getUserFromToken($this->request->input('token'));
        $video_id = $this->request->input('video_id') ? intval($this->request->input('video_id')) : 0;
        $content = $this->request->input('content') ? trim($this->request->input('content')) : '';
        $user_id = $user['user_id'];
        $user_phone = $user['phone'];
        $user_info = $this->getUserInfo($user_id);
        $user_name = '';
        if ( ! empty($user_info)) {
            $user_name = $user_info->real_name;
            if ($user_name == '') {
                $user_name = '嘻哈用户'.$user_phone;
            }
        }

        $time = time();
        $param = [
            'video_id' => $video_id,
            'content' => $content,
            'user_id' => $user_id,
            'user_phone' => $user_phone,
            'user_name' => $user_name,
            'parent_id' => 0,
            'votes' => 0,
            'addtime' => $time
        ];

        $video_list = $this->getUserVideoDetail($user_id, $video_id, $content);
        if ( ! empty($video_list)) {
            $id = $video_list->id;
            $result = DB::table('video_comments')
                ->where('id', $id)
                ->update($param);

            $data = [
                'code' => 200,
                'msg' => '添加成功',
                'data' => $id,
            ];
        } else {
            $result = DB::table('video_comments')
                ->insertGetId($param);

            $data = [
                'code' => 200,
                'msg' => '添加成功',
                'data' => $result,
            ];
        }

        return response($data);
    }

    /**
     * 新增视频浏览量
     * @param   int     $video_id   学车视频主键ID   
     * @return  void
     **/
    public function increaseViews()
    {   
        if ( ! $this->request->has('video_id')) {
            Log::error("缺少必须参数");
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => new \stdClass
            ]);
        }

        $video_id = $this->request->input('video_id') ? intval($this->request->input('video_id')) : '';
        $video_info = $this->getViewsByVideoId($video_id);
        $views = 0;
        if ( ! empty($video_info)) {
            $views = $video_info->views;
        }

        $count = $views + 1;
        $updatedata = ['views' => $count];
        $result = DB::table('video')
            ->where('id', $video_id)
            ->update($updatedata);
        if ($result >= 1) {
            $data = [
                'code' => 200,
                'msg' => '更新成功',
                'data' => $result
            ];
        } else {
            $data = [
                'code' => 400,
                'msg' => '更新失败',
                'data' => $result
            ];
        }
        return response($data);
    }

    // 获取此科目被浏览的总数
    public function getViewsByVideoId($video_id)
    {
        $video_info = DB::table('video')
            ->where('id', $video_id)
            ->first();
        if ( ! empty($video_info)) {
            $video_info->pic_url = $this->buildUrl($video_info->pic_url);
            $video_info->video_url = $this->buildUrl($video_info->video_url);
        }
        return $video_info;
    }

    // 获取用户的学车视频的评论
    public function getUserVideoDetail($user_id, $video_id, $content)
    {   
        $map = [
            'user_id' => $user_id,
            'video_id' => $video_id,
            'content' => $content
        ];
        $list = DB::table('video_comments')
            ->where($map)
            ->first();
        return $list;
    }

    
    /**
     * 给视频下的评论点赞
     * @param   string  $token      用户登录标识
     * @param   int     $item_id    视频详情的主键ID
     * @param   int     $item_uid   发布者ID
     * @param   string  $type       默认 video 视频
     * @param   void
     **/
    public function submitLike()
    {
        if ( ! $this->request->input('item_id')
            OR ! $this->request->input('item_uid')
            OR ! $this->request->input('type')) 
        {
            Log::error("缺少必须参数");
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => ''
            ]);
        }

        $user = $this->auth->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $type = $this->request->input('type') ? trim($this->request->input('type')) : 'video';
        $item_id = $this->request->input('item_id') ? intval($this->request->input('item_id')) : '';
        $item_uid = $this->request->input('item_uid') ? intval($this->request->input('item_uid')) : '';
        $video_info = $this->getCommentInfoByCondition($item_id, $item_uid, $type);
        if ( empty($video_info)) {
            Log::error('当前视频不存在');
            return response()->json([
                'code' => 400,
                'msg'  => '当前视频不存在',
                'data' => '',
            ]);
        }        
        
        $time = time();
        $param = [
            'uid'       => $user_id,
            'item_id'   => $item_id,
            'item_uid'  => $item_uid,
            'type'      => $type,
            'add_time'  => $time,
        ];

        $map = ['uid' => $user_id, 'item_id' => $item_id, 'type' => $type];
        $votes_list = $this->getVotesByCondition($map);
        if ( ! empty($votes_list)) {
            $data = [
                'code'  => 200,
                'msg'   => '已点过赞',
                'data'  => ''
            ];
        } else {
            DB::beginTransaction();
            $res = DB::table('like_comments')
                ->insertGetId($param);

            $_res = DB::update('UPDATE `cs_video_comments` SET `votes` = `votes` + 1 WHERE `id` = :_id', ['_id' => $item_id]);

            if ($res AND $_res) {
                $video_info = $this->getCommentInfoByCondition($item_id, $item_uid, $type);
                $votes = $video_info->votes;
                $data = ['code'  => 200, 'msg'   => '点赞成功', 'data'  => "{$votes}"];
                DB::commit();
            } else {
                $data = ['code'  => 200, 'msg'   => '点赞失败', 'data'  => ''];
                DB::rollBack();
            }
        }
        return response($data);
    }


    // 获取评论的相关信息
    public function getCommentInfoByCondition($item_id, $item_uid, $type)
    {
        if ( $type == 'video') {
            $_list = DB::table('video_comments')
                ->where('id', $item_id)
                ->where('user_id', $item_uid)
                ->first();
        } else {
            $_list = DB::table('user_question_comments')
            ->where('id', $item_id)
            ->where('user_id', $item_uid)
            ->first();
        }
        return $_list;
    }

    // 获取用户的点赞信息
    public function getVotesByCondition($condition)
    {   
        $vote_list = DB::table('like_comments')
            ->where($condition)
            ->first();
        return $vote_list;
    }
    
    /**
     * 获取题目评论列表
     * @param   string  $token          用户登录标识 非必须
     * @param   int     $question_id    问题ID
     * @return  void
     **/
    public function getUserQuestionsList()
    {
        if ( ! $this->request->has('question_id')) {
            Log::error("缺少必须参数");
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => ''
            ]);
        }

        $question_id = $this->request->input('question_id') ? intval($this->request->input('question_id')) : 0;
        $page = $this->request->input('page') ? intval($this->request->input('page')) : 1;
        $limit = 10;
        $comment_list = DB::table('user_question_comments')
            ->where('question_id', $question_id)
            ->orderBy('addtime', 'desc')
            ->paginate($limit);
        if ( ! empty($comment_list)) {
            foreach ($comment_list as $index => $comment) {
                $uid = $comment->user_id;
                $user_info = $this->getUserInfo($uid);
                if ( ! empty($user_info)) {
                    $comment_list[$index]->photo_id = $user_info->photo_id;
                    $comment_list[$index]->user_photo = $user_info->user_photo;
                } else {
                    $comment_list[$index]->photo_id = 1;
                    $comment_list[$index]->user_photo = '';
                }

                if ( $this->request->has('token')) {
                    $user = $this->auth->getUserFromToken($this->request->input('token'));
                    $user_id = $user['user_id'];
                    $user_phone = $user['phone'];
                    $like_list = $this->getLikeCommentsByCondition($user_id, $uid, $comment->id, 'question');
                    if ( ! empty($like_list)) {
                        $comment_list[$index]->is_like = 1;
                    } else {
                        $comment_list[$index]->is_like = 0;
                    }
                } else {
                    $comment_list[$index]->is_like = 0;
                }

                if ( ! empty($comment->addtime) AND $comment->addtime != 0) {
                    $comment_list[$index]->addtime = date('Y-m-d H:i:s', $comment->addtime);
                }

            }
        }

        return response([
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => $comment_list
        ]);
    }

        
}