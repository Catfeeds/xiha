<?php
/**
 *
 * 教练班制设置
 *
 **/
namespace App\Http\Controllers\v3\student;

use Exception;
use InvalidArgumentException;
use Log;
use App\Http\Controllers\Controller;
use App\Http\Controllers\v3\student\AuthController;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use SmsApi;

class ShiftsController extends Controller {
    /*
     * 请求对象主体
     */
    protected $request;
    protected $auth;
    protected $user;

    public function __construct(Request $request) {
        $this->request = $request;
        $this->auth = new AuthController($this->request);
        $this->user = new UserController($this->request);
    }

    /**
     * 获取教练设置的班制列表
     * @param string token 用户登录标识
     * @return void
     **/
    public function getCoachShiftsList () {
        $user       = (new AuthController)->getUserFromToken($this->request->input('token'));
        $user_id    = $user['user_id'];
        $coach_id = $this->request->input('coach_id');
        //$coach_id   = $this->user->getCoachIdByUserId($user_id);
        // if ( ! $coach_id) {
        //     Log::error('异常：【获取教练班制列表】获取的教练不存在');
        //     return response()->json([
        //         'code' => 400,
        //         'msg' => '此教练的账号出现异常',
        //         'data' => new \stdClass,
        //     ]);
        // }

        $shiftslist = DB::table('school_shifts')
            ->select(
                'id as sh_id',
                'sh_school_id as school_id',
                'coach_id',
                'sh_title',
                'sh_money',
                'sh_original_money',
                'sh_license_name',
                'sh_license_id',
                'sh_tag',
                'is_promote',
                'sh_description_2 as sh_description'
            )
            ->where([
                ['school_shifts.coach_id', '=', $coach_id],
                ['school_shifts.deleted', '=', 1] // 1：未删除 2：已删除
            ])
            ->orderBy('school_shifts.id', 'DESC')
            ->get();
        $data = [
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => ['list' => $shiftslist],
        ];

        return response()->json($data);
    }

    /**
     * 获取教练班制详情
     * @param string token 用户登录标识
     * @param number sh_id 班制ID
     * @return void
     **/
    public function getCoachShiftsDetail () {

        if ( ! $this->request->has('sh_id')) {
            $data = [
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass,
            ];
            Log::error('异常：【获取教练班制详情】缺少参数班制ID');
            return response()->json($data);
        }
        $sh_id      = intval($this->request->input('sh_id'));
        $user       = (new AuthController)->getUserFromToken($this->request->input('token'));
        $user_id    = $user['user_id'];
        $coach_id   = $this->user->getCoachIdByUserId($user_id);
        if ( ! $coach_id) {
            Log::error('异常：【获取教练班制详情】获取的教练不存在');
            return response()->json([
                'code' => 400,
                'msg' => '此教练的账号出现异常',
                'data' => new \stdClass,
            ]);
        }

        $shiftslist = $this->getCoachShiftsById($sh_id, $coach_id);
        if ($shiftslist) {

            $sh_type = $shiftslist->sh_type;
            if (1 == $sh_type) {
                $shiftslist->sh_type_name = '计时班';

            } elseif (in_array($sh_type, ['2', '3'])) {
                $shiftslist->sh_type_name = '非计时班';

            } else {
                $shiftslist->sh_type_name = '非计时班';
            }

            $data = [
                'code'  => 200,
                'msg'   => '获取成功',
                'data'  => $shiftslist,
            ];
        } else {
            $data = [
                'code'  => 1002,
                'msg'   => '当前班制不存在',
                'data'  => new \stdClass,
            ];
            Log::error('异常：【获取教练班制详情】当前班制可能已经不存在 | 不属于该教练');
        }
        return response()->json($data);

    }


    /**
     * 添加或编辑教练的班制
     * @param string token              用户登录标识
     * @param number sh_id              班制ID（添加时不传 | 编辑时必须传）
     * @param string sh_title           班制名称
     * @param string sh_license_name    班制牌照名称
     * @param string sh_original_money  班制原始价格
     * @param string sh_money           班制最终价格
     * @param string sh_description     班制描述
     * @param string sh_type            班制类型（1：计时班 | 其他类型为非计时班）
     * @return void
     **/
    public function handleCoachShifts () {

        if ( ! $this->request->has('sh_title')
            OR ! $this->request->has('sh_license_name')
            OR ! $this->request->has('sh_original_money')
            OR ! $this->request->has('sh_money')
            OR ! $this->request->has('sh_description')
            OR ! $this->request->has('sh_type'))
        {
            $data = [
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass,
            ];
            Log::Info('异常：【添加或编辑教练的班制】缺少参数');
            return response()->json($data);
        }
        $sh_title       = $this->request->input('sh_title');
        $license_name   = $this->request->input('sh_license_name');
        $original_money = $this->request->input('sh_original_money');
        $sh_money       = $this->request->input('sh_money');
        $description    = $this->request->input('sh_description');
        $sh_type        = intval($this->request->input('sh_type'));
        $user           = (new AuthController)->getUserFromToken($this->request->input('token'));
        $user_id        = $user['user_id'];
        $coach_id       = $this->user->getCoachIdByUserId($user_id);
        if ( ! $coach_id) {
            Log::error('异常：【添加或编辑教练的班制】获取的教练不存在');
            return response()->json([
                'code' => 400,
                'msg' => '此教练的账号出现异常',
                'data' => new \stdClass,
            ]);
        }

        $coach_info = $this->user->getCoachInfoById($coach_id);
        if ($coach_info) {
            $school_info = DB::table('school')
                ->select('school.l_school_id as school_id')
                ->where('l_school_id', '=', $coach_info->school_id)
                ->first();
            if ($school_info) {
                $sh_school_id = $school_info->school_id;
            } else {
                $sh_school_id = '';
            }
        } else {
            $sh_school_id = '';
        }

        // 1:计时 2:普通 3:vip
        if ( 1 != $sh_type) {

            if ( false === strpos('vip', $sh_title)
                && false === strpos('Vip', $sh_title)
                && false === strpos('vIp', $sh_title)
                && false === strpos('viP', $sh_title)
                && false === strpos('VIp', $sh_title)
                && false === strpos('VIP', $sh_title))
            {
                $sh_type = 2; // 2:普通

            } elseif ( false === strpos('普通', $sh_title)) {
                 $sh_type = 3; // 3:vip

            } else {
                 $sh_type = 2;
            }
        }

        // sh_license_id
        $license_info = $this->getLicenseInfo($license_name);
        if (NULL != $license_info) {
            $license_id = $license_info->license_id;
        } else {
            $license_id = 1; // default C1
            $license_name = 'C1';
        }

        $nowtime = time();
        if ( ! $this->request->has('sh_id')) { // add
            $sh_id = DB::table('school_shifts')
                ->insertGetId([
                    'sh_school_id'      => $sh_school_id,
                    'coach_id'          => $coach_id,
                    'sh_title'          => $sh_title,
                    'sh_money'          => $sh_money,
                    'sh_original_money' => $original_money,
                    'sh_type'           => $sh_type,
                    'sh_description_1'  => '',
                    'sh_license_name'   => $license_name,
                    'sh_license_id'     => $license_id,
                    'is_promote'        => 2,
                    'sh_description_2'  => $description,
                    'deleted'           => 1,
                    'addtime'           => $nowtime
                ]);
            if ($sh_id) {
                $data = [
                    'code'  => 200,
                    'msg'   => '添加成功',
                    'data'  => 'ok',
                ];
            } else {
                $data = [
                    'code'  => 400,
                    'msg'   => '添加失败',
                    'data'  => '',
                ];
            }
            // end add

        } else { // update

            $sh_id = $this->request->input('sh_id');
            $update_data = [
                'sh_title'          => $sh_title,
                'sh_type'           => $sh_type,
                'sh_license_id'     => $license_id,
                'sh_license_name'   => $license_name,
                'sh_original_money' => $original_money,
                'sh_money'          => $sh_money,
                'sh_description_2'  => $description,
                'updatetime'        => $nowtime
            ];

            $update_ok = DB::table('school_shifts')
                ->where('id', '=', $sh_id)
                ->update($update_data);
            if ($update_ok >= 1) {
                $data = [
                    'code'  => 200,
                    'msg'   => '更新成功',
                    'data'  => 'ok'
                ];
            } else {
                $data = [
                    'code'  => 400,
                    'msg'   => '更新失败',
                    'data'  => ''
                ];
            }
        }

        return response()->json($data);

    }

    /**
     * 删除教练班制
     * @param string token 用户登录标识
     * @param nember sh_id 班制ID
     * @return void
     **/
    public function deleteCoachShifts () {

        if ( ! $this->request->has('sh_id')) {
            $data = [
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass,
            ];
            Log::error('异常：【删除教练班制】缺少参数班制ID');
            return response()->json($data);
        }
        $sh_id      = intval($this->request->input('sh_id'));
        $user       = (new AuthController)->getUserFromToken($this->request->input('token'));
        $user_id    = $user['user_id'];
        $coach_id   = $this->user->getCoachIdByUserId($user_id);
        if ( ! $coach_id) {
            Log::error('异常：【删除教练班制】获取的教练不存在');
            return response()->json([
                'code' => 400,
                'msg' => '此教练的账号出现异常',
                'data' => new \stdClass,
            ]);
        }

        $shiftslist = $this->getCoachShiftsById($sh_id, $coach_id);
        if ( ! $shiftslist) {
            $data = [
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass,
            ];
            Log::error('异常：【删除教练班制】当前班制已不存在 | 当前班制非当前教练的');
            return response()->json($data);
        }

        $update_data = [
            'deleted' => 2,
        ];

        $update_ok = DB::table('school_shifts')
            ->where([
                ['id', '=', $sh_id],
                ['coach_id', '=', $coach_id],
            ])
            ->update($update_data);
        if ($update_ok >= 1) {
            $data = [
                'code'  => 200,
                'msg'   => '删除成功',
                'data'  => 'ok'
            ];
        } else {
            $data = [
                'code'  => 400,
                'msg'   => '删除失败',
                'data'  => ''
            ];
        }

        return response()->json($data);

    }

    /**
     * 获取教练的班制信息
     * @param number sh_id 班制ID
     * @param number coach_id 教练ID
     * @return void
     **/
    public function getCoachShiftsById ($sh_id, $coach_id) {

        $whereCondition = [
            'school_shifts.id' => $sh_id,
            'school_shifts.coach_id' => $coach_id,
            'school_shifts.deleted' => 1,
        ];

        $shiftslist = DB::table('school_shifts')
            ->select(
                'id as sh_id',
                'sh_school_id as school_id',
                'coach_id',
                'sh_title',
                'sh_money',
                'sh_original_money',
                'sh_license_name',
                'sh_license_id',
                'sh_description_2 as sh_description',
                'sh_type'
            )
            ->where($whereCondition)
            ->first();
        if ($shiftslist) {
            return $shiftslist;
        } else {
            return NULL;
        }
    }

    /**
     * 获取牌照信息
     * @param string license_name 牌照名称
     * @return void
     **/
    public function getLicenseInfo ($license_name) {

        $license_info = DB::table('license_config')
            ->select('license_config.*')
            ->where([
                ['license_name', '=', $license_name],
                ['is_open', '=', 1],
            ])
            ->first();
        if ($license_info) {
            return $license_info;
        } else {
            return NULL;
        }

    }



















}










