<?php
/**
 * 获取更多评论
 * lumen查询构造器 https://laravel-china.org/docs/5.3/queries
 * @return void
 * @author cx
 **/

namespace App\Http\Controllers\v1;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use Illuminate\Pagination\Paginator;

class CommentController extends Controller {

    protected $request;

    public function __construct(Request $request) {
        $this->request = $request;        
    }

    // 获取教练更多评论
    public function getMoreComments() {
        // var_dump($this->request->input());
        if(!$this->request->has('type') || !$this->request->has('mid')) {
            return response()->json([
                'code'=>400, 
                'msg'=>'参数错误',
                'data'=>'',
            ]);
        }
        $type = $this->request->input('type');  // 1：教练名片 2：帖子评论 3：驾校评论
        $mid = $this->request->input('mid'); // 类型ID 教练ID, 帖子ID...
        $limit = $this->request->has('limit') ? $this->request->input('limit') : 10; // 每页限制条数
        $page = $this->request->has('page') ? $this->request->input('page') : 1; // 页码

        $start = ($page - 1) * $limit;
        $_comment_list = array();

        if($type == 1) {

            // 获取评论列表（3条最近）
            $comment_list = DB::table('coach_comment')
                ->select(
                    'coach_comment.coach_star',
                    'coach_comment.coach_content',
                    'coach_comment.user_id',
                    'coach_comment.addtime',
                    'user.s_username as user_name',
                    'users_info.photo_id',
                    'users_info.user_photo'
                )
                ->join('user', 'coach_comment.user_id', '=', 'user.l_user_id')
                ->join('users_info', 'coach_comment.user_id', '=', 'users_info.user_id')
                ->where([
                    'coach_comment.coach_id'=>$mid,
                    'coach_comment.type'=>1 // 1：预约学车 2：报名驾校
                ])
                ->orderBy('coach_comment.addtime', 'DESC')
                ->paginate($limit);
            // ->skip($start)
            // ->take($limit)
            // ->get();
            $_comment_list = $comment_list->toArray();
            // print_r($_comment_list['data']);
            if(!empty($_comment_list['data'])) {
                foreach ($comment_list as $key => $value) {
                    $comment_list[$key]->user_name    = $comment_list[$key]->user_name ? $comment_list[$key]->user_name : '嘻哈学员';
                    $comment_list[$key]->user_photo    = $comment_list[$key]->user_photo ? env('APP_PATH').'admin/'.$comment_list[$key]->user_photo : '';
                    $comment_list[$key]->addtime       = $comment_list[$key]->addtime ? date('Y-m-d H:i', $comment_list[$key]->addtime) : date('Y-m-d H:i', time());
                    $comment_list[$key]->coach_content = $comment_list[$key]->coach_content ? $comment_list[$key]->coach_content : '默认好评';

                }
            }
        } else if($type == 2) {

        } else {
        
            $comment_list = DB::table('coach_comment')
                ->select(
                    'coach_comment.school_star',
                    'coach_comment.school_content',
                    'coach_comment.user_id',
                    'coach_comment.addtime',
                    'user.s_username as user_name',
                    'users_info.photo_id',
                    'users_info.user_photo'
                )
                ->join('user', 'coach_comment.user_id', '=', 'user.l_user_id')
                ->join('users_info', 'coach_comment.user_id', '=', 'users_info.user_id')
                ->where([
                    'coach_comment.school_id'=>$mid,
                    'coach_comment.type'=>2 // 1：预约学车 2：报名驾校
                ])
                ->orderBy('coach_comment.addtime', 'DESC')
                ->paginate($limit);
 
            $_comment_list = $comment_list->toArray();
            
            if(!empty($_comment_list['data'])) {
                foreach ($comment_list as $key => $value) {
                    $comment_list[$key]->user_name    = $comment_list[$key]->user_name ? $comment_list[$key]->user_name : '嘻哈学员';
                    $comment_list[$key]->user_photo    = $comment_list[$key]->user_photo ? env('APP_PATH').'admin/'.$comment_list[$key]->user_photo : '';
                    $comment_list[$key]->addtime       = $comment_list[$key]->addtime ? date('Y-m-d H:i', $comment_list[$key]->addtime) : date('Y-m-d H:i', time());
                    $comment_list[$key]->school_content = $comment_list[$key]->school_content ? $comment_list[$key]->school_content : '默认好评';

                }
            } 
        }
        $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$comment_list];
        return response()->json($data);
    }

}
