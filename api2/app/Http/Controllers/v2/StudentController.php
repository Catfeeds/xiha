<?php

/**
 * 学员
 *
 **/

namespace App\Http\Controllers\v2;

use Exception;
use Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\OrderController;
use App\Http\Controllers\v1\UserController;
use Illuminate\Http\Request;

class StudentController extends Controller {

    protected $request;
    protected $auth;
    protected $order;
    protected $user;

    public function __construct(Request $request) {
        $this->request = $request;
        $this->auth = new AuthController($this->request);
        $this->order = new OrderController($this->request);
        $this->user = new UserController($this->request);
    }


    /**
     * 获取教练导入|添加的学员
     * @param   string   token  用户登录标识
     * @param   number   stage  状态（1：待定 2：科目二 3：科目三 4：毕业 5：科目一 6：科目四）
     * @param   number   year   年     
     * @param   number   month  月     
     * @param   number   day    日    
     * @param   number   page   分页     
     * @return void
     **/
    public function getCoachUserList () {

        if ( ! $this->request->has('stage')
            OR ! $this->request->has('year')
            OR ! $this->request->has('month')
            OR ! $this->request->has('day')) 
        {
            $data = [
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass,
            ];
            Log::error('异常：【获取教练导入|添加的学员】缺少必须参数');
            return response()->json($data);
        }

        if ( $this->request->has('page')) {
            $page = $this->request->input('page');
        } else {
            $page = 1;
        }

        $user = $this->auth->getUserFromToken($this->request->input('token'));
        $user_phone = $user['phone'];
        $user_id = $user['user_id'];
        $coach_id = $this->user->getCoachIdByUserId($user_id);
        if ( ! $coach_id) {
            $data = [
                'code'  => 400,
                'msg'   => '账号出现异常',
                'data'  => new \stdClass,
            ];
            Log::error('异常：【获取教练导入|添加的学员】当前教练的账号出现异常[已下线 | 所在驾校下线]');
            return response()->json($data);
        }

        $stage = $this->request->input('stage');
        if ( ! in_array($stage, [1, 2, 3, 4, 5, 6])) {
             $data = [
                'code'  => 400,
                'msg'   => '状态异常',
                'data'  => new \stdClass,
            ];
            Log::error('异常：【获取教练导入|添加的学员】学员状态不在规定状态内');
            return response()->json($data);
        }

        $year = $this->request->input('year');
        $month = $this->request->input('month');
        $day = $this->request->input('day');
        
        $coach_users_time_list = DB::table('coach_users as users')
            ->select(
                'users.id',
                'users.user_name',
                'users.user_phone',
                'users.user_photo',
                'records.user_name as records_user_name',
                'records.user_phone as records_user_phone',
                'records.year',
                'records.month',
                'records.day',
                'records.timestamp',
                'records.i_stage as stage',
                'records.identity_id',
                'records.i_status',
                'records.is_bind',
                'records.is_deleted',
                'records.addtime',
                'records.updatetime'
            )
            ->join('coach_users_records as records', 'records.coach_users_id', '=', 'users.id')
            ->where([
                ['coach_id', '=', $coach_id],
                ['records.i_stage', '=', $stage],
                ['records.is_deleted', '=', 1] // 1:未删除 2:已删除
            ])
            ->orderBy('records.timestamp', 'desc')
            ->orderBy('records.updatetime', 'desc')
            ->groupBy('records.timestamp')
            ->paginate(10);

        $coach_users_time_list = $coach_users_time_list->toArray();

        if ( empty( $coach_users_time_list['data'])) {
            $data = [
                'code'  => 200,
                'msg'   => '获取成功',
                'data'  => [
                    'stage' => $stage,
                    'date_list' => $coach_users_time_list['data'],
                    'users_list' => [],
                ],
            ];
        }

        
        $date_list = [];
        foreach ($coach_users_time_list['data'] as $index => $time) {

            $date_list[$index]['year'] = $time->year;
            $date_list[$index]['month'] = sprintf('%02d', $time->month);
            $date_list[$index]['day'] = sprintf('%02d', $time->day);
            $date_list[$index]['timestamp'] = $time->timestamp;
            $total_num = $this->getTotalNum(intval($time->year), intval($time->month), intval($time->day), $coach_id, $stage);
            $date_list[$index]['total_num'] = $total_num;

        }
        
        // 获取学员列表
        $coach_users_list = DB::table('coach_users as users')
            ->select(
                'users.id',
                'users.user_name',
                'users.user_phone',
                'users.user_photo',
                'records.user_name as records_user_name',
                'records.user_phone as records_user_phone',
                'records.year',
                'records.month',
                'records.day',
                'records.timestamp',
                'records.i_stage as stage',
                'records.identity_id',
                'records.i_status',
                'records.is_bind',
                'records.is_deleted',
                'records.addtime',
                'records.updatetime'
            )
            ->join('coach_users_records as records', 'records.coach_users_id', '=', 'users.id')
            ->where([
                ['coach_id', '=', $coach_id],
                ['records.i_stage', '=', $stage],
                ['records.is_deleted', '=', 1] // 1:未删除 2:已删除
            ])
            ->orderBy('records.timestamp', 'desc')
            ->orderBy('records.updatetime', 'desc')
            ->paginate(10);

        $coach_users_list = $coach_users_list->toArray();

        if ( ! $coach_users_list) {
            $data = [
                'code'  => 200,
                'msg'   => '获取成功',
                'data'  => [
                    'stage' => $stage,
                    'date_list' => [],
                    'users_list' => [],
                ],
            ];

        } else {

            $list = [];
            foreach ($coach_users_list['data'] as $key => $value) {

                $coach_users_list['data'][$key]->user_name = $value->records_user_name != '' ? $value->records_user_name : $value->user_name;
                $coach_users_list['data'][$key]->user_phone = $value->records_user_phone != '' ? $value->records_user_phone : $value->user_phone;

                // 判断是否注册
                $coach_users_list['data'][$key]->is_register = 1; // 未注册
                $coach_users_list['data'][$key]->is_bind = $value->is_bind; // 1:已注册 2：未注册
                $user_info = $this->getUserInfoByPhone($coach_users_list['data'][$key]->user_phone);

                if ( null != $user_info) {
                    $coach_users_list['data'][$key]->is_register = 2; // 已注册
                }

                if ( $date_list ) {
                    foreach ( $date_list as $k => $v) {
                        if ($v['timestamp'] == $value->timestamp) {
                            $list[$k]['timestamp'] = $v['timestamp'];
                            $list[$k]['year'] = $value->year;
                            $list[$k]['month'] = $value->month;
                            $list[$k]['day'] = $value->day;

                            if ( isset($coach_users_list['data'][$key])) {
                                $list[$k]['list'][] = $coach_users_list['data'][$key];
                            } else {
                                $list[$k]['list'][] = [];
                            }

                        }
                    }
                }
            }

            $data = [
                'code'  => 200,
                'msg'   => '获取成功',
                'data'  => [
                    'stage' => $stage,
                    'date_list' => $date_list,
                    'users_list' => $list,
                ],
            ];
        }

        return response()->json($data);
    }

    // 判断学员是否注册
    private function getUserInfoByPhone ($phone) {
        
        $user_info = DB::table('user')
            ->select('user.*')
            ->where([
                ['user.s_phone', '=', $phone],
                ['user.i_user_type', '=', 0],
                ['user.i_status', '=', 0],
            ])
            ->first();
        
        return $user_info;
    }


    // 获取每天的人员数
    private function getTotalNum ($year, $month, $day, $coach_id, $stage) {
        
        $total_num = DB::table('coach_users_records as records')
            ->select(DB::raw('count(*) as total_num'))
            ->where([
                ['records.coach_id', '=', $coach_id], 
                ['records.year', '=', $year], 
                ['records.month', '=', $month], 
                ['records.day', '=', $day], 
                ['records.i_stage', '=', $stage], 
                ['records.is_deleted', '=', 1], 
            ])
            ->first();
        if ($total_num) {
            return $total_num->total_num;
        } else {
            return 0;
        }
    }




}