<?php
/**
 * 用户模块
 */

namespace App\Http\Controllers\v1;

use Exception;
use InvalidArgumentException;
use Log;
use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\SmsController as Sms; // 短信
use App\Http\Controllers\v1\Encryption;
use Illuminate\Support\Facades\Crypt;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use SmsApi;

class UserController extends Controller
{

    /*
     * 请求对象主体
     */
    protected $request;
    protected $auth;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->auth = new AuthController($this->request);
    }

    /**
     * 获取短信验证码
     *
     * @param  string $user_type (student|coach)
     * @param  string $operation (reg|login|forgetpass)
     * @param  string $phone
     * @return void
     */
    public function getSmsCode($user_type, $operation)
    {
        if (false === ($op = $this->checkOperation($operation))) {
            return ['code' => 400, 'msg' => '参数错误', 'data' => ''];
        }

        if (false === ($i_user_type = $this->checkUserType($user_type))) {
            return ['code' => 400, 'msg' => '参数错误', 'data' => ''];
        }

        if (false === ($phone = $this->checkPhone($i_user_type, $op))) {
            return ['code' => 400, 'msg' => '手机参数错误', 'data' => ''];
        } elseif (is_array($phone)) {
            return ['code' => 400, 'msg' => $phone['msg'], 'data' => ''];
        }
        $phone = $this->request->input('phone');

        if ($user_type == 'coach') {
            $template = 'coach_code';
        } elseif ($user_type == 'student') {
            $template = 'student_code';
        } else {
            $template = 'student_code';
        }

        try {
            $smscode = $this->randNumber();
            $contentParam = ['code' => $smscode];
            $result = (new Sms())
                ->sms()
                ->setTemplate($template)
                ->send($phone, $contentParam);

            if ('100' == $result['stat']) {
                $oldcode = DB::table('verification_code')
                    ->where('s_phone', '=', $phone)
                    ->orderBy('addtime', 'desc')
                    ->first();
                if ($oldcode) {
                    DB::table('verification_code')
                        ->where('s_phone', '=', $phone)
                        ->orderBy('addtime', 'desc')
                        ->limit(1)
                        ->update(['s_code' => $smscode, 'addtime' => time()]);
                } else {
                    DB::table('verification_code')
                        ->insert([
                            's_phone' => $phone,
                            's_code' => $smscode,
                            'addtime' => time(),
                        ]);
                }
                return ['code' => 200, 'msg' => '发送成功', 'data' => $smscode];
            } else {
                return ['code' => 400, 'msg' => '发送失败,'.$result['message'], 'data' => ''];
            }
        } catch (Exception $e) {
            $_data = [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'msg'  => $e->getMessage(),
            ];
            Log::error('短信发送异常', ['error' => $_data]);
        }
    }

    /**
     * 注册新用户
     *
     * @param $user_type (coach|student)
     * @param string $phone
     * @param string $code
     * @param string $pass (raw)
     * @param string $user_name
     * @return void
     */
    public function register($user_type)
    {

        // $user_type
        if (false === ($i_user_type = $this->checkUserType($user_type))) {
            return ['code' => 400, 'msg' => '参数错误', 'data' => ''];
        }

        // $phone
        if (false === ($phone = $this->checkPhone($i_user_type, 'reg'))) {
            return ['code' => 400, 'msg' => '手机参数错误', 'data' => ''];
        } elseif (is_array($phone)) {
            return ['code' => 400, 'msg' => $phone['msg'], 'data' => ''];
        }

        // $code
        if (false === ($code = $this->checkCode($this->request, $phone, 86400))) {
            return ['code' => 400, 'msg' => '验证码失效', 'data' => ''];
        } elseif (is_array($code)) {
            return ['code' => 400, 'msg' => $code['msg'], 'data' => ''];
        }

        // $user_name
        $user_name = ($this->request->has('user_name')) ? substr($this->request->input('user_name'), 0, 50) : '嘻哈用户'.substr($phone, -4, 4);

        // $pass
        if (! $this->request->has('pass')) {
            return ['code' => 400, 'msg' => '请填写密码', 'data' => ''];
        } elseif (strlen($this->request->input('pass')) < 6) {
            return ['code' => 400, 'msg' => '密码长度不能低于6位', 'data' => ''];
        }
        $pass = md5($this->request->input('pass'));

        // $identity_id
        if (! $this->request->has('identity_id')) {
            $identity_id = '';
        } elseif (false === $this->checkIdentity($this->request->input('identity_id'))) {
            Log::error('身份证的位数有误(15 | 18)且最后一位可能是X');
            return ['code' => 400, 'msg' => '身份证格式有误', 'data' => ''];
        } else {
            $identity_id = $this->request->input('identity_id');
        }

        DB::beginTransaction();
        // 主表登录表
        $user_id = DB::table('user')
            ->insertGetId([
                's_phone'       => $phone,
                's_username'    => $user_name,
                's_password'    => $pass,
                'i_user_type'   => $i_user_type,
                'i_status'      => 0, // 0-正常 2-已删除
                's_real_name'   => $user_name,
                'content'       => '欢迎注册成为嘻哈学车用户',
            ]);

        // 副表信息表
        if ($user_id) {
            if ('student' == $user_type) {
                $user = DB::table('users_info')
                    ->insertGetId([
                        'user_id'       => $user_id,
                        'identity_id'   => $identity_id,          // 身份证信息空
                        'address'       => '',          // 地址信息空
                        'school_id'     => 0,           // 新注册学员，未报名驾校，归嘻哈统一管理
                        'sex'           => 1,           // 性别１男
                        'age'           => 0,           // 年龄　０
                        'license_id'    => 0,           // 牌照信息为空
                        'license_name'  => '',          // 牌照信息为空
                        'lesson_id'     => 0,           // 科目信息为空
                        'lesson_name'   => '未报名',    // 科目信息为空
                        'addtime'       => time(),      // 记录注册时间
                    ]);
                $user_info = $this->getUserInfoById($user_id);
                $this->couponOperation('', 22, 340000, 340100, $user_info->phone, $user_info->real_name);
                $this->couponOperation('', 23, 340000, 340100, $user_info->phone, $user_info->real_name);
            } elseif ('coach' == $user_type) {
                $user = DB::table('coach')
                    ->insertGetId([
                        's_coach_phone'         => $phone,
                        'user_id'               => $user_id,
                        's_coach_name'          => $user_name,
                        's_teach_age'           => 0,
                        's_coach_sex'           => 1,
                        's_school_name_id'      => 0,
                        's_coach_address'       => '',
                        'order_receive_status'  => 0,
                        'addtime'               => time(),
                    ]);
            }
            if ($user) {
                DB::commit();
                Log::info($user_type.'新用户注册成功 phone->'.$phone);
                return ['code' => 200, 'msg' => '注册成功', 'data' => new \stdClass];
            } else {
                DB::rollBack();
                Log::info($user_type.'新用户注册失败 phone->'.$phone);
                return ['code' => 400, 'msg' => '注册失败', 'data' => new \stdClass];
            }
        } else {
            DB::rollBack();
            Log::info($user_type.'新用户注册失败 phone->'.$phone);
            return ['code' => 400, 'msg' => '注册失败', 'data' => new \stdClass];
        }
    }

    /**
     * 验证省份证的信息
     * @param $identity_id
     * @return void
     **/
    public function checkIdentity($identity_id)
    {
        if (preg_match('/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/', $identity_id)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 用户登录
     *
     * @param string $user_type
     * @param string $phone
     * @param string $pass
     * @return void
     */
    public function login($user_type)
    {

        // $user_type
        if (false === ($i_user_type = $this->checkUserType($user_type))) {
            return ['code' => 400, 'msg' => '参数错误', 'data' => ''];
        }

        // $phone
        if (false === ($phone = $this->checkPhone($i_user_type, 'login'))) {
            return ['code' => 400, 'msg' => '手机参数错误', 'data' => ''];
        } elseif (is_array($phone)) {
            return ['code' => 400, 'msg' => $phone['msg'], 'data' => ''];
        }

        // $pass
        if (! $this->request->has('pass')) {
            return response()->json(['code' => 400, 'msg' => '请填写密码', 'data' => '']);
        }
        $pass = $this->request->input('pass');

        // $user_id
        if (false === ($user_id = $this->userLogin($phone, $pass, $i_user_type))) {
            return response()->json(['code' => 400, 'msg' => '密码不正确', 'data' => '']);
        } elseif (is_array($user_id)) {
            return response()->json(['code' => 400, 'msg' => $user_id['msg'], 'data' => '']);
        }

        $payload = [
            'user_id'     => $user_id,
            'i_user_type' => $i_user_type,
            'phone'       => $phone,
        ];
        $auth = new AuthController();
        $config = $auth->getJWTConfig();
        $token = $auth->getToken($payload);
        if ($auth->verifyToken($token)) {
            if (0 == $i_user_type) {
                $user_info = $this->getUserInfoById($user_id);
                Log::info('学员登录 phone->'.$user_info->phone);
                $this->couponOperation('', 22, 340000, 340100, $user_info->phone, $user_info->real_name);
                $this->couponOperation('', 23, 340000, 340100, $user_info->phone, $user_info->real_name);
            }
            $data = [
                'code' => 200,
                'msg' => '登录成功',
                'data' => [
                    'token' => $token,
                    'expires_in' => $config['exp'],
                    'user_id' => $user_id,
                    'phone' => $phone,
                ],
            ];
        } else {
            $data = [
                'code' => 400,
                'msg' => '登录失败',
                'data' => [],
            ];
        }
        return response()->json($data);
    }

    /**
     * 刷新用户token
     *
     * @param string $token
     * @return void
     */
    public function refresh()
    {
        $token = $this->request->input('token');
        $auth = new AuthController();
        $jwtconfig = $auth->getJWTConfig();
        $user = $auth->getUserFromToken($token);
        $token = $auth->refresh($token);

        if ($user && $token) {
            $data = [
                'code' => 200,
                'msg'  => '成功',
                'data' => [
                    'token'      => $token,
                    'expires_in' => $jwtconfig['exp'],
                    'user_id'    => $user['user_id'],
                    'phone'      => $user['phone'],
                ],
            ];
        } else {
            $data = [
                'code' => 400,
                'msg'  => '参数错误',
                'data' => [
                    'token'      =>'',
                    'expires_in' =>-1,
                    'user_id'    =>0,
                    'phone'      =>'',
                ],
            ];
        }
        return response()->json($data);
    }

    /**
     * 第三方授权登录
     *
     * @param string $third_key
     * @param int    $third_type (1:微信|2:QQ)
     * @param string $user_name
     * @return void
     */
    public function thirdLogin($user_type)
    {

        // $i_user_type
        if (false === ($i_user_type = $this->checkUserType($user_type))) {
            $data = ['code' => 400, 'msg' => '不允许的用户类型', 'data' => ''];
            return response()->json($data);
        }

        // $third_type
        if (false === ($third_type = $this->checkThirdType($this->request))) {
            $data = ['code' => 400, 'msg' => '参数不正确', 'data' => ''];
            return response()->json($data);
        } elseif (is_array($third_type)) {
            $data = ['code' => 400, 'msg' => $third_type['msg'], 'data' => ''];
            return response()->json($data);
        }

        // $third_key
        if (! $this->request->has('third_key')) {
            $data = ['code' => 400, 'msg' => '参数不正确', 'data' => ''];
            return response()->json($data);
        }
        $third_key = $this->request->input('third_key');

        if (! $this->request->has('user_name')) {
            $user_name = '嘻哈用户'.time()%10000;
        } else {
            $user_name = $this->request->input('user_name');
        }

        $third_login = DB::table('third_login')
            ->select(
                'third_login.user_id',
                'user.s_phone as phone'
            )
            ->leftJoin('user', 'user.l_user_id', '=', 'third_login.user_id')
            ->where([
                ['third_login.third_type', '=', $third_type],
                ['third_login.third_key' , '=', $third_key],
                ['third_login.i_user_type', '=', $i_user_type],
                ['user.i_status', '=', 0],
                ['user.s_phone', '<>', ''],
            ])
            ->first();
        if ($third_login) {
            // 不是第一次
            $user_id = $third_login->user_id;
            $phone = $third_login->phone;
            if (0 == $i_user_type) {
                $user_info = $this->getUserInfoById($user_id);
                Log::info('学员第三方登录 phone->'.$user_info->phone);
                $this->couponOperation('', 22, 340000, 340100, $user_info->phone, $user_info->real_name);
                $this->couponOperation('', 23, 340000, 340100, $user_info->phone, $user_info->real_name);
            }

            // 生成token
            $payload = [
                'user_id'     => $user_id,
                'i_user_type' => $i_user_type,
                'phone'       => $phone,
            ];
            $auth = new AuthController();
            $config = $auth->getJWTConfig();
            $token = $auth->getToken($payload);
            $data = [
                'code' => 200,
                'msg'  => '成功',
                'data' => [
                    'token'       => $token,
                    'expires_in'  => $config['exp'],
                    'user_id'     => $user_id,
                    'i_user_type' => $i_user_type,
                    'phone'       => $phone,
                ],
            ];
        } else {
            $data = [
                'code' => 102,
                'msg'  => '还没有绑定',
                'data' => new \stdClass,
            ];
        }
        return $data;
    }

    /**
     * 第三方授权登录 ( coach )
     *
     * @param string    $third_key  true    用户第三方登录的标识
     * @param int       $third_type true    第三方类型(1:微信|2:QQ)
     * @param int       $with_phone true    带上手机号（1：yes | 2：no）
     * @param string    $phone      false   用户手机号
     * @param string    $code       false   短信验证码
     * @param string    $user_name  false   第三方账号获取到的昵称，用于新用户注册时的用户名
     * @return void
     */
    public function userThirdLogin($user_type)
    {

        // $i_user_type
        if (false === ($i_user_type = $this->checkUserType($user_type))) {
            $data = ['code' => 400, 'msg' => '不存在的用户类型', 'data' => new \stdClass,];
            Log::Info('异常：请求的用户类型不存在');
            return response()->json($data);
        }

        // 必须参数的判断
        if (!$this->request->has('third_key')
            || !$this->request->has('third_type')
            || !$this->request->has('with_phone')) {
            $data = [
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass,
            ];
            Log::Info('异常：缺失必须请求的参数');
            return response()->json($data);
        }

        // $third_type
        if (false === ($third_type = $this->checkThirdType($this->request))) {
            $data = ['code' => 400, 'msg' => '参数错误', 'data' => new \stdClass,];
            Log::Info('异常：请求的第三方类型参数错误');
            return response()->json($data);
        } elseif (is_array($third_type)) {
            $data = ['code' => 400, 'msg' => $third_type['msg'], 'data' => new \stdClass,];
            Log::Info('异常：请求的第三方类型参数错误');
            return response()->json($data);
        }
        
        // $third_key
        $third_key = $this->request->input('third_key');

        // $with_phone
        $with_phone = $this->request->input('with_phone');
        if (!in_array($with_phone, ['1', '2'])) { // 1:带手机号 2:不带手机号
            $data = ['code' => 400, 'msg' => '参数不在范围内', 'data' => new \stdClass,];
            Log::Info('异常：with_phone的传值错误');
            return response()->json($data);
        }

        if (1 ==  $with_phone) {
            if (!$this->request->has('phone') && !$this->request->has('code')) {
                $data = ['code' => 102, 'msg' => '请绑定手机号', 'data' => new \stdClass];
                Log::Info('异常：暂未绑定手机号');
                return response()->json($data);
            }

            $user_phone = $this->request->input('phone');
            $code = $this->request->input('code');
            if (!$this->checkPhoneFormat($user_phone)) {
                $data = ['code' => 400, 'msg' => '手机格式错误', 'data' => new \stdClass,];
                Log::Info('异常：手机号的格式错误');
                return response()->json($data);
            }
        }
       
        // $user_name
        if ($this->request->has('user_name')) {
            $user_name = trim((string)$this->request->input('user_name'));
        } else {
            $user_name = '嘻哈用户'.time()%10000;
        }

        $nowtime = time();
        $third_login = DB::table('third_login')
            ->select(
                'third_login.user_id',
                'user.s_phone as phone'
            )
            ->leftJoin('user', 'user.l_user_id', '=', 'third_login.user_id')
            ->where([
                ['third_login.third_type', '=', $third_type],
                ['third_login.third_key' , '=', $third_key],
                ['third_login.i_user_type', '=', $i_user_type],
                ['user.i_status', '=', 0],
                ['user.s_phone', '<>', ''],
            ])
            ->first();
        if ($third_login) { // 已绑定
            // 不是第一次
            $user_id = $third_login->user_id;
            $user_phone = $third_login->phone;
            if (0 == $i_user_type) {
                $user_info = $this->getUserInfoById($user_id);
                if (!$user_info) {
                    $users_info_id = DB::table('users_info')
                        ->insertGetId([
                        'user_id'       => $user_id,
                        'identity_id'   => '',
                        'address'       => '',
                        'age'           => 0,
                        'sex'           => 1,
                        'school_id'     => 0,
                        'addtime'       => $nowtime
                        ]);
                    if (!$users_info_id) {
                        $data = ['code' => 400, 'msg' => '学员信息出错', 'data' => new \stdClass,];
                        Log::Info('异常：学员信息附表在插入的时候出错');
                        return response()->json($data);
                    }
                }

                $user_info = $this->getUserInfoById($user_id);
                Log::info('学员第三方登录 phone->'.$user_info->phone);
                $this->couponOperation('', 22, 340000, 340100, $user_info->phone, $user_info->real_name);
                $this->couponOperation('', 23, 340000, 340100, $user_info->phone, $user_info->real_name);
            } elseif (1 == $i_user_type) {
                $phone_coach_info = $this->getCoachIdByUserId($user_id);
                if (!$phone_coach_info) {
                    $phone_coach_id = DB::table('coach')
                        ->insertGetId([
                            's_coach_phone'         => $user_phone,
                            'user_id'               => $user_id,
                            's_coach_name'          => $user_name,
                            's_teach_age'           => 0,
                            's_coach_sex'           => 1,
                            's_school_name_id'      => 0,
                            's_coach_address'       => '',
                            'addtime'               => $nowtime,
                            'order_receive_status'  => 0,
                        ]);
                    if (!$phone_coach_id) {
                        $data = ['code' => 400, 'msg' => '教练信息出错', 'data' => new \stdClass,];
                        Log::Info('异常：教练信息附表在插入的时候出错');
                        return response()->json($data);
                    }
                }
            }
        } else { // 还未绑定
            // 第一次登录
            if (1 != $with_phone || !isset($user_phone)) {
                $data = [
                    'code'  => 102,
                    'msg'   => '请绑定手机号',
                    'data'  => new \stdClass,
                ];
                return response()->json($data);
            }

            // 检查获取的验证码
            $begin_time = $nowtime - (60 * 3); // 有效时间3分钟
            $end_time = $nowtime;
            $verification_info = DB::table('verification_code')
                ->select(
                    'id as id',
                    's_phone as s_phone',
                    's_code as s_code',
                    'addtime as addtime'
                )
                ->where([
                    ['s_phone', '=', $user_phone],
                    ['s_code', '=', $code],
                    ['addtime', '>=', $begin_time],
                    ['addtime', '<=', $end_time],
                ])
                ->first();
            if (!$verification_info) {
                $data = [
                    'code'  => 400,
                    'msg'   => '请重新获取手机验证码',
                    'data'  => new \stdClass,
                ];
                Log::Info('异常：手机验证码可能超时或者未获取到');
                return response()->json($data);
            }

            // 更新数据
            $phone_user = DB::table('user')
                ->select('l_user_id as user_id')
                ->where([
                    ['i_status', '=', 0],
                    ['s_phone', '=', $user_phone],
                    ['i_user_type', '=', $i_user_type]
                ])
                ->first();

            if ($phone_user) { // user表中存在此用户
                $user_id = $phone_user->user_id;
                if (0 == $i_user_type) { // begin student
                    $student_info = $this->getUserInfoById($user_id);
                    if (!$student_info) {
                        $users_info_id = DB::table('users_info')
                            ->insertGetId([
                                'user_id'       => $user_id,
                                'identity_id'   => '',
                                'address'       => '',
                                'age'           => 0,
                                'sex'           => 1,
                                'school_id'     => 0,
                                'addtime'       => $nowtime
                            ]);
                        if (!$users_info_id) {
                            $data = ['code' => 400, 'msg' => '学员信息出错', 'data' => new \stdClass,];
                            Log::Info('异常：学员信息附表在插入的时候出错');
                            return response()->json($data);
                        }
                    }

                    $user_info = $this->getUserInfoById($user_id);
                    Log::info('学员第三方登录 phone->'.$user_info->phone);
                    $this->couponOperation('', 22, 340000, 340100, $user_info->phone, $user_info->real_name);
                    $this->couponOperation('', 23, 340000, 340100, $user_info->phone, $user_info->real_name);
                    // end student
                } elseif (1 == $i_user_type) { // begin coach
                    $phone_coach_info = $this->getCoachIdByUserId($user_id);
                    if (!$phone_coach_info) {
                        $phone_coach_id = DB::table('coach')
                            ->insertGetId([
                                's_coach_phone'         => $user_phone,
                                'user_id'               => $user_id,
                                's_coach_name'          => $user_name,
                                's_teach_age'           => 0,
                                's_coach_sex'           => 1,
                                's_school_name_id'      => 0,
                                's_coach_address'       => '',
                                'addtime'               => $nowtime,
                                'order_receive_status'  => 0,
                            ]);
                        if (!$phone_coach_id) {
                            $data = ['code' => 400, 'msg' => '教练信息出错', 'data' => new \stdClass,];
                            Log::Info('异常：教练信息附表在插入的时候出错');
                            return response()->json($data);
                        }
                    }
                }
                // end user表中存在此用户
            } else { // user表中不存在此用户
                $pass = md5('123456');
                $l_user_id = DB::table('user')
                    ->insertGetId([
                        's_phone'       => $user_phone,
                        's_password'    => $pass,
                        's_username'    => $user_name,
                        'i_user_type'   => $i_user_type,
                        'i_status'      => 0,
                        's_real_name'   => $user_name,
                        'content'       => '使用第三方登陆成为嘻哈学车用户'
                    ]);
                if ($l_user_id) {
                    $user_id = $l_user_id;
                }
                if (0 == $i_user_type) { // begin student
                    $users_info_id = DB::table('users_info')
                        ->insertGetId([
                            'user_id'       => $user_id,
                            'identity_id'   => '',
                            'address'       => '',
                            'age'           => 0,
                            'sex'           => 1,
                            'school_id'     => 0,
                            'addtime'       => $nowtime
                        ]);
                    if (!$users_info_id) {
                        $data = ['code' => 400, 'msg' => '学员信息出错', 'data' => new \stdClass,];
                        Log::Info('异常：学员信息附表在插入的时候出错');
                        return response()->json($data);
                    }
                    $user_info = $this->getUserInfoById($user_id);
                    Log::info('学员第三方登录 phone->'.$user_info->phone);
                    $this->couponOperation('', 22, 340000, 340100, $user_info->phone, $user_info->real_name);
                    $this->couponOperation('', 23, 340000, 340100, $user_info->phone, $user_info->real_name);
                    // end student
                } elseif (1 == $i_user_type) { // begin coach
                    $phone_coach_id = DB::table('coach')
                        ->insertGetId([
                            's_coach_phone'         => $user_phone,
                            'user_id'               => $user_id,
                            's_coach_name'          => $user_name,
                            's_teach_age'           => 0,
                            's_coach_sex'           => 1,
                            's_school_name_id'      => 0,
                            's_coach_address'       => '',
                            'addtime'               => $nowtime,
                            'order_receive_status'  => 0,
                        ]);
                    if (!$phone_coach_id) {
                        $data = ['code' => 400, 'msg' => '教练信息出错', 'data' => new \stdClass,];
                        Log::Info('异常：教练信息附表在插入的时候出错');
                        return response()->json($data);
                    }
                }
            } // end user表中不存在此用户
            
            // 将用户信息插入third_login表中
            $insert_third_login = DB::table('third_login')
                ->insertGetId([
                    'third_key'         => $third_key,
                    'third_type'        => $third_type,
                    'user_id'           => $user_id,
                    'i_user_type'       => $i_user_type,
                    'add_time'          => $nowtime,
                ]);
            if (!$insert_third_login) {
                $data = ['code' => 400, 'msg' => '第三方登录出错', 'data' => new \stdClass,];
                Log::Info('异常：插入第三方登录数据表时出错');
                return response()->json($data);
            }

            if (1 == $i_user_type) { // 教练
                $coach_id = $this->getCoachIdByUserId($user_id);
                $update_ok = DB::table('coach')
                    ->where('l_coach_id', '=', $coach_id)
                    ->update(['is_first' => 1]);
            }
        } // end first third login

        // 生成token
        $payload = [
            'user_id'     => $user_id,
            'i_user_type' => $i_user_type,
            'phone'       => $user_phone,
        ];
        $auth = new AuthController();
        $config = $auth->getJWTConfig();
        $token = $auth->getToken($payload);
        $data = [
            'code' => 200,
            'msg'  => '成功',
            'data' => [
                'token'       => $token,
                'expires_in'  => $config['exp'],
                'user_id'     => $user_id,
                'i_user_type' => $i_user_type,
                'phone'       => $user_phone,
            ],
        ];

        return $data;
    }

    /**
     * 检测支持的第三个登录类型
     *
     * @param Request $request
     * @return int
     */
    private function checkThirdType($request, $param = 'third_type')
    {
        $supported_third_login = [
            'weixin' => 1,
            'qq'     => 2,
        ];
        if (! $request->has($param)) {
            return false;
        }
        $third_type = $request->input($param);

        if (! array_key_exists($third_type, $supported_third_login)) {
            return ['msg' => '不支持此第三方登录平台'];
        }
        
        return $supported_third_login[$third_type];
    }

    /**
     * 个人中心-首页
     *
     * @param JWT $token
     * @return void
     */
    public function myIndex()
    {
        $my = [
            'primary' => [],
            'coupon'  => [],
            'my_coach'=> [],
            'message' => [],
            'school_info' => [],
        ];

        $user = (new AuthController())->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $phone = $user['phone'];

        // 姓名和头像
        if ($user_info = $this->getUserInfoById($user_id)) {
            $my['primary'] = $user_info;
        } else {
            $my['primary'] = new \stdClass;
        }

        // 优惠券简讯
        if ($coupon_count = $this->getMyCouponCount($phone)) {
            $my['coupon']['total'] = $coupon_count;
        } else {
            $my['coupon']['total'] = 0;
        }

        // 我的教练
        if ($bind_coach = $this->getMyBindCoachId($user_id)) {
            $my['my_coach']['coach_id'] = $bind_coach->coach_id;
            $config = [
                1 => '已成功绑定',
                2 => '已成功解绑',
                3 => '绑定教练审核中',
                4 => '绑定学员审核中',
                5 => '解除绑定教练等待确认',
                6 => '解除绑定学员等待确认',
            ];
            $my['my_coach']['bind_status'] = [
                'code' => $bind_coach->bind_status,
                'text' => $config[$bind_coach->bind_status],
            ];
            if ($coach_info = $this->getCoachInfoById($bind_coach->coach_id)) {
                $my['my_coach']['coach_name'] = $coach_info->coach_name;
                $my['my_coach']['coach_phone'] = $coach_info->coach_phone;
                $my['my_coach']['must_bind'] = $coach_info->must_bind;
            } else {
                $my['my_coach']['coach_name'] = '';
            }
        } else {
            $my['my_coach']['coach_id'] = 0;
            $my['my_coach']['bind_status'] = [
                'code' => 0,
                'text' => '未绑定任何教练',
            ];
            $my['my_coach']['coach_name'] = '';
        }

        // 未读消息
        if ($message_count = $this->getUnreadMessageCount($user_id)) {
            $my['message']['unread'] = $message_count;
        } else {
            $my['message']['unread'] = 0;
        }
        
        $school_info = $this->getMySchoolInfo($user_id, $phone);
        $my['school_info'] = $school_info;

        $data = [
            'code' => 200,
            'msg'  => '成功',
            'data' => [
                'my' => $my,
            ],
        ];
        return response()->json($data);
    }

    // 获取我的驾校的相关信息
    public function getMySchoolInfo($user_id, $user_phone)
    {
        $where = [
            'so_user_id'        => $user_id,
            'so_phone'          => $user_phone,
            'school.is_show'    => 1,
        ];

        $whereCondition = function ($query) {
            $query->whereIn('so_pay_type', [1, 3, 4])
                ->where('so_order_status', '=', 1)
                ->orWhere(function ($_query) {
                    $_query->where('so_pay_type', '=', 2)
                        ->where('so_order_status', '=', 3);
                });
        };
        
        $school_ids = DB::table('school_orders as orders')
            ->select('so_school_id')
            ->leftJoin('school', 'school.l_school_id', '=', 'orders.so_school_id')
            ->where($where)
            ->where($whereCondition)
            ->distinct()
            ->orderBy('dt_zhifu_time', 'desc')
            ->get();

        $school_id_arr = [];
        if ($school_ids) {
            foreach ($school_ids as $key => $value) {
                $school_id_arr[] = $value->so_school_id;
            }
        }

        $whereCon = function ($query) {
            $query->whereIn('so_pay_type', [1, 3, 4])
                ->where('so_order_status', '=', 4) // 报名成功未付款
                ->orWhere(function ($_query) {
                    $_query->where('so_pay_type', '=', 2)
                        ->where('so_order_status', '=', 1); // 报名成功未付款
                });
        };

        $school_info = new \stdClass;
        $school_info->signup_school_id = 0;
        $school_info->signup_school_name = '';
        $school_info->signup_school_count = 0;
        
        $school_info->signup_status = 0; // 未报名
        $school_orders_status = DB::table('school_orders as orders')
            ->select('so_school_id')
            ->leftJoin('school', 'school.l_school_id', '=', 'orders.so_school_id')
            ->where($where)
            ->where($whereCon)
            ->get();
        $school_orders_status = $school_orders_status->toArray();
        if (!empty($school_orders_status)) {
            $school_info->signup_status = 1; // 报名成功未付款
        }

        if (! empty($school_id_arr)) {
            $school_id = $school_id_arr[0];
            $school_info = DB::table('school')
                ->select(
                    'l_school_id as signup_school_id',
                    's_school_name as signup_school_name'
                )
                ->where([
                    ['l_school_id', '=', $school_id],
                    ['is_show', '=', 1], // 1:展示 2:不展示
                ])
                ->first();

            $school_info->signup_school_count = count($school_id_arr);
            $school_info->signup_status = 2; // 已报名
        }

        return $school_info;
    }

    /**
     * 我的绑定教练id
     *
     * @param  int $user_id
     * @return int $coach_id
     */
    private function getMyBindCoachId($user_id)
    {
        $bind_coach = DB::table('coach_user_relation')
            ->select('coach_user_relation.coach_id', 'coach_user_relation.bind_status')
            ->leftJoin('coach', 'coach.l_coach_id', '=', 'coach_user_relation.coach_id')
            ->leftJoin('user', 'user.l_user_id', '=', 'coach.user_id')
            ->where([
                ['coach_user_relation.user_id', '=', $user_id],
                ['coach_user_relation.bind_status', '<>', 2],
                // ['coach.order_receive_status', '=', 1], // 教练在线，可以预约的状态为1
                ['user.i_user_type', '=', 1], // 教练用户
                ['user.i_status', '=', 0],    // 教练是正常用户
            ])
            ->first();
        return $bind_coach;
    }

    /**
     * 获取教练基本信息
     *
     * @param int $coach_id
     * @return \stdClass $coach_info
     */
    public function getCoachInfoById($coach_id)
    {
        $coach_info = DB::table('coach')
            ->select(
                'l_coach_id as coach_id',
                's_coach_name as coach_name',
                's_coach_phone as coach_phone',
                's_teach_age as teach_age',
                's_coach_sex as s_coach_sex',
                'certification_status',
                'i_coach_star as coach_star',
                'i_type',
                'coach.must_bind',
                'timetraining_supported',
                'coupon_supported',
                'order_receive_status',
                's_coach_lisence_id as license_id',
                's_coach_lesson_id as lesson_id',
                's_school_name_id as school_id',
                's_am_subject',
                's_pm_subject',
                's_am_time_list',
                's_pm_time_list',
                'is_hot',
                'province_id as province_id',
                'city_id as city_id',
                'area_id as area_id',
                's_coach_car_id as car_id',
                's_coach_content as coach_content',
                's_coach_imgurl as coach_imgurl',
                's_coach_address as coach_address',
                'shift_min_price as shift_min_price',
                'shift_max_price as shift_max_price',
                'good_coach_star as good_coach_star', // 教练好评总数
                'coach_star_count as coach_star_count', // 星级评价总数
                'coach_license_imgurl as coach_license_imgurl', // 教练证图片
                'personal_image_url as personal_imgurl', // 教练个人形象证
                'coach_car_imgurl as coach_car_imgurl', // 教练车图片
                'id_card_imgurl as id_card_imgurl' // 教练身份证图片
           )
            ->leftJoin('user', 'coach.user_id', '=', 'user.l_user_id')
            ->where([
                ['coach.l_coach_id', '=', $coach_id],
                ['user.i_user_type', '=', 1],
                ['user.i_status', '=', 0],
            ])
            ->first();
        return $coach_info;
    }

    /**
     * 获取教练详细信息
     *
     * @param int $coach_id
     * @return \stdClass $coach_info
     */
    public function getCoachDetailInfoById($coach_id)
    {
        $user_id = $this->getUserIdByCoachId($coach_id);
        $coach_info = $this->getCoachInfoById($coach_id);

        $coachinfo = new \stdClass;
        // user_id
        $coachinfo->user_id = $user_id;

        // coach_id
        $coachinfo->coach_id = $coach_info->coach_id;
        
        // coach_name
        $coachinfo->coach_name = $coach_info->coach_name;

        // coach_phone
        $coachinfo->coach_phone = $coach_info->coach_phone;

        // coach_address
        $coachinfo->coach_address = $coach_info->coach_address;
        
        // coach_sex
        if ($coach_info->s_coach_sex) {
            $coachinfo->coach_sex = $coach_info->s_coach_sex;
        } else {
            $coachinfo->coach_sex = 1; // 1:boy 2:girl
        }
        
        // teach_age
        if ($coach_info->teach_age) {
            $coachinfo->teach_age = $coach_info->teach_age;
        } else {
            $coachinfo->teach_age = 0;
        }
        
        // coach_star
        if ($coach_info->coach_star) {
            $coachinfo->coach_star = $coach_info->coach_star;
        } else {
            $coachinfo->coach_star = 3;
        }

        // coach_content
        $coachinfo->coach_content = $coach_info->coach_content;

        // must_bind
        $coachinfo->must_bind = $coach_info->must_bind;

        // online
        $coachinfo->online = $coach_info->order_receive_status;

        // coupon_supported
        $coachinfo->coupon_supported = $coach_info->coupon_supported;

        // timetraining_supported
        $coachinfo->timetraining_supported = $coach_info->timetraining_supported;

        // shift_min_price
        $coachinfo->shift_min_price = $coach_info->shift_min_price;

        // shift_max_price
        $coachinfo->shift_max_price = $coach_info->shift_max_price;
        

        // province_id
        $coachinfo->province_id = $coach_info->province_id;
        
        // city_id
        $coachinfo->city_id = $coach_info->city_id;

        // area_id
        $coachinfo->area_id = $coach_info->area_id;

        // 组合教练证图片
        $coachinfo->coach_license_imgurl = $this->buildUrl($coach_info->coach_license_imgurl);

        // 教练个人生活照图片
        $coachinfo->personal_imgurl = $this->buildUrl($coach_info->personal_imgurl);

        // 教练车图片
        $coachinfo->coach_car_imgurl = $this->buildUrl($coach_info->coach_car_imgurl);

        // 教练身份证图片
        $coachinfo->id_card_imgurl = $this->buildUrl($coach_info->id_card_imgurl);

        // 组合教练头像
        $coachinfo->coach_imgurl = $this->buildUrl($coach_info->coach_imgurl);

        // certification_status
        $coachinfo->certification_status = $coach_info->certification_status;

        // certification_status_text
        switch ($coach_info->certification_status) {
            case '1':
                $coachinfo->certification_status_text = '未认证';
                break;
            case '2':
                $coachinfo->certification_status_text = '认证中';
                break;
            case '3':
                $coachinfo->certification_status_text = '已认证';
                break;
            case '4':
                $coachinfo->certification_status_text = '认证失败';
                break;
            default:
                $coachinfo->certification_status_text = '未知状态';
                break;
        }

        // school_id
        $coachinfo->school_id = $coach_info->school_id;

        // school_name
        if ($coach_info->school_id != '' || $coach_info->school_id != 0) {
            $school_info = DB::table('school')
                ->select('s_school_name as school_name')
                ->where('l_school_id', '=', $coach_info->school_id)
                ->whereNotNull('l_school_id')
                ->first();
            if ('' != $school_info->school_name) {
                $coachinfo->school_name = $school_info->school_name;
            } else {
                $coachinfo->school_name = '嘻哈平台';
            }
        } else {
            $coachinfo->school_name = '嘻哈平台';
        }

        // 获取好评率 good_star_rate
        $good_coach_star = $coach_info->good_coach_star;
        $coach_star_count = $coach_info->coach_star_count;
        if (0 == $coach_star_count) {
            $coachinfo->good_star_rate = 0;
        } else {
            $coachinfo->good_star_rate = floor(($good_coach_star / $coach_star_count) * 100);
        }

        // 获取学员总数 student_num
        $num = DB::table('study_orders')
            ->select('l_study_orders')
            ->where([
                ['i_status', '=', 2],
                ['l_coach_id', '=', $coach_id],
            ])
            ->count();
        $coachinfo->student_num = $num;

        // 获取科目名称列表(数组)
        $lesson_id = $coach_info->lesson_id;
        if ('' != $lesson_id) {
            $lesson_id_arr = explode(',', $lesson_id);
            $coachinfo->lesson_id_list = $lesson_id_arr;
            foreach ($lesson_id_arr as $lesson_key => $lesson_value) {
                $lesson_info = DB::table('lesson_config')
                    ->select('lesson_name')
                    ->where('lesson_id', '=', $lesson_value)
                    ->first();
                $lesson_name[] = $lesson_info->lesson_name;
            }
            if (!empty($lesson_name)) {
                $coachinfo->lesson_name_list = $lesson_name;
            } else {
                $coachinfo->lesson_name_list = [];
            }
        } else {
            $coachinfo->lesson_id_list = [];
            $coachinfo->lesson_name_list = [];
        }

        // 获取牌照名称列表(数组) license_name_list
        $license_id = $coach_info->license_id;
        if ('' != $license_id) {
            $license_id_arr = explode(',', $license_id);
            $coachinfo->license_id_list = $license_id_arr;
            foreach ($license_id_arr as $license_key => $license_value) {
                $license_info = DB::table('license_config')
                    ->select('license_name')
                    ->where('license_id', '=', $license_value)
                    ->first();
                $license_name[] = $license_info->license_name;
            }
            if (!empty($license_name)) {
                $coachinfo->license_name_list = $license_name;
            } else {
                $coachinfo->license_name_list = [];
            }
        } else {
            $coachinfo->license_id_list = [];
            $coachinfo->license_name_list = [];
        }

        // 获取教练的车辆信息 coach_car
        $car_info = [];
        $coach_car_id = $coach_info->car_id;
        unset($coach_info->car_id);
        if (0 !== (int)$coach_car_id) {
            $car_info = $this->getCarInfoById($coach_car_id);
            $coachinfo->coach_car = $car_info;
        } else {
            $coachinfo->coach_car = $car_info;
        }
        return $coachinfo;
    }

    /**
     * 获取教练车辆信息
     *
     * @access public
     * @param  int $car_id          车辆id
     * @return string $car_info     车辆信息
     */
    public function getCarInfoById($car_id)
    {
        $cars_info = [];
        if (0 !== (int)$car_id) {
            $cars_info = DB::table('cars')
                ->select(
                    'id as car_id',
                    'name as car_name',
                    'imgurl as car_picture',
                    'car_no as car_number',
                    'car_type as car_type'
                )
                ->where('id', '=', $car_id)
                ->whereNotNull('id')
                ->first();
            $imgurl_arr = [];
            if ($cars_info) {
                if ($cars_info->car_picture) {
                    try {
                        $car_imgurl = json_decode($cars_info->car_picture, true);
                    } catch (Exception $e) {
                        Log::Info('读取图片错误');
                    }

                    if ($car_imgurl) {
                        foreach ($car_imgurl as $img_key => $img_value) {
                            $imgurl = $this->buildUrl($img_value);
                            if ('' !== $imgurl) {
                                $imgurl_arr[] = $imgurl;
                            } else {
                                $imgurl_arr = [];
                            }
                        }
                        $cars_info->car_picture = $imgurl_arr;
                    }
                }
            }
        }
        return $cars_info;
    }

    /**
     * 获取用户信息
     *
     * @access public
     * @param  int $user_id
     * @return string $user_name
     */
    public function getUserNameById($user_id, $user_type, $phone)
    {
        $user_name = '';
        if ($user_type == 0) { // 学员
            $user_info = $this->getUserInfoById($user_id);
            if ($user_info && isset($user_info->real_name)) {
                $user_name = $user_info->real_name;
            }
        } elseif ($user_type == 1) { // 教练
            $coach_id = $this->getCoachIdByUserId($user_id);
            $coach_info = $this->getCoachInfoById($coach_id);
            if ($coach_info && isset($coach_info->coach_name)) {
                $user_name = $coach_info->coach_name;
            }
        }
        return $user_name;
    }

    /**
     * 获取学员信息
     *
     * @access private
     * @param  int $user_id
     * @return \stdClass $user_info
     */
    public function getUserInfoById($user_id)
    {
        $user_info = DB::table('user')
            ->select(
                'user.l_user_id as user_id',
                'user.s_username as user_name',
                'user.s_real_name as real_name',
                'user.s_phone as phone',
                'users_info.photo_id',
                'users_info.user_photo',
                'users_info.sex',
                'users_info.age',
                'users_info.school_id',
                'users_info.license_id',
                'users_info.license_name',
                'users_info.exam_license_name',
                'users_info.lesson_id',
                'users_info.lesson_name',
                'users_info.area_id',
                'users_info.city_id',
                'users_info.province_id',
                'users_info.learncar_status as learn_car_status',
                'users_info.license_num',
                'users_info.identity_id',
                'users_info.address'
            )
            ->leftJoin('users_info', 'user.l_user_id', '=', 'users_info.user_id')
            ->where([
                ['user.l_user_id', '=', $user_id],
                ['user.i_status', '=', 0],
                ['user.i_user_type', '=', 0],
            ])
            ->first();
        return $user_info;
    }

    /**
     * 个人资料
     *
     * @param JWT $token
     * @return void
     */
    public function profile($user_type)
    {
        if (false === ($i_user_type = $this->checkUserType($user_type))) {
            return ['code' => 400, 'msg' => '参数错误', 'data' => ''];
        }

        $user = (new AuthController())->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $phone = $user['phone'];
        if ($i_user_type == 0) {
            $user_info = DB::table('user')
                ->select(
                    'l_user_id as user_id',
                    'photo_id',
                    'user_photo',
                    's_username as user_name',
                    's_real_name as real_name',
                    'sex',
                    'age',
                    's_phone as phone',
                    'school_id',
                    'license_name',
                    'lesson_name',
                    'area_id',
                    'city_id',
                    'province_id',
                    'learncar_status as learn_car_status',
                    'license_num',
                    'identity_id',
                    'address'
                )
                ->leftJoin('users_info', 'user.l_user_id', '=', 'users_info.user_id')
                ->where([
                    ['l_user_id', '=', $user_id],
                    ['i_user_type', '=', 0],
                    ['i_status', '=', 0],
                ])
                ->first();
            if ($user_info) {
                if ((! isset($user_info->photo_id)) || ($user_info->photo_id <= 0)) {
                    $user_info->photo_id = 1;
                }
                $user_info->qrcode = '';
                $data = [
                    'code' => 200,
                    'msg'  => 'OK',
                    'data' => $user_info,
                ];
            } else {
                $data = [
                    'code' => 401,
                    'msg'  => '请重新登录',
                    'data' => [],
                ];
            }
        } elseif ($i_user_type == 1) {
            $coach_message = DB::table('coach')
                ->select('coach.l_coach_id as coach_id')
                ->join('user', 'user.l_user_id', '=', 'coach.user_id')
                ->where([
                    ['user.i_status', '=', 0],
                    ['user.i_user_type', '=', 1],
                    ['coach.s_coach_phone', '=', $phone],
                ])
                ->first();
            if (!$coach_message) {
                $data = [
                    'code' => 400,
                    'msg'  => '获取教练详情失败',
                    'data' => [
                        'coach_info' => new \stdClass,
                    ],
                ];
                return response()->json($data);
            } else {
                $coach_id = $coach_message->coach_id;
                $coach_info = $this->getCoachDetailInfoById($coach_id);
            }
            if ($coach_info) {
                $data = [
                    'code' => 200,
                    'msg'  => '获取教练详情成功',
                    'data' => [
                        'coach_info' => $coach_info,
                    ],
                ];
            } else {
                $data = [
                    'code' => 400,
                    'msg'  => '获取教练详情失败',
                    'data' => [
                        'coach_info' => new \stdClass,
                    ],
                ];
            }
        }

        return response()->json($data);
    }

    /**
     * 更改个人资料信息
     *
     * @param JWT    $token
     * @param int    $photo_id
     * @param string $user_name
     * @param string $real_name
     * @param int    $sex
     * @param int    $age
     * @param string $identity_id
     * @param string $address
     * @param int    $province_id
     * @param int    $city_id
     * @param int    $area_id
     * @param int    $license_id
     * @param int    $lesson_id
     * @return void
     */
    public function updateProfile()
    {
        $user = (new AuthController())->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $phone = $user['phone'];

        $raw_data = [];
        if ($this->request->has('photo_id')) {
            $raw_data['info.photo_id'] = $this->request->input('photo_id');
        }
        if ($this->request->has('user_name')) {
            $raw_data['user.s_username'] = $this->request->input('user_name');
        }
        if ($this->request->has('real_name')) {
            $raw_data['user.s_real_name'] = $this->request->input('real_name');
        }
        if ($this->request->has('sex')) {
            $raw_data['info.sex'] = $this->request->input('sex');
        }
        if ($this->request->has('age')) {
            $raw_data['info.age'] = $this->request->input('age');
        }
        if ($this->request->has('identity_id')) {
            $raw_data['info.identity_id'] = $this->request->input('identity_id');
        }
        if ($this->request->has('address')) {
            $raw_data['info.address'] = $this->request->input('address');
        }
        if ($this->request->has('province_id')) {
            $raw_data['info.province_id'] = $this->request->input('province_id');
        }
        if ($this->request->has('city_id')) {
            $raw_data['info.city_id'] = $this->request->input('city_id');
        }
        if ($this->request->has('area_id')) {
            $raw_data['info.area_id'] = $this->request->input('area_id');
        }
        $raw_data['info.updatetime'] = time(); // 学员信息更新，记录更新时间

        // lesson_id
        if ($this->request->has('lesson_id')) {
            $lesson_info = DB::table('lesson_config')
                ->select('lesson_id', 'lesson_name')
                ->where([
                    ['lesson_id', '=', $this->request->input('lesson_id')],
                    ['is_open', '=', 1], // 1 代表开放
                ])
                ->first();
            if (! $lesson_info) {
                $data = [
                    'code' => 400,
                    'msg'  => '科目信息不对',
                    'data' => '',
                ];
                return response()->json($data);
            } else {
                $raw_data['info.lesson_id'] = $lesson_info->lesson_id;
                $raw_data['info.lesson_name'] = $lesson_info->lesson_name;
            }
        }

        // 学车的牌照类型 license_id
        if ($this->request->has('license_id')) {
            $license_info = DB::table('license_config')
                ->select('license_id', 'license_name')
                ->where([
                    ['license_id', '=', $this->request->input('license_id')],
                    ['is_open', '=', 1], // 1 代表开放
                ])
                ->first();
            if (! $license_info) {
                $data = [
                    'code' => 400,
                    'msg'  => '考试类型不对',
                    'data' => '',
                ];
                return response()->json($data);
            } else {
                $raw_data['info.license_id'] = $license_info->license_id;
                $raw_data['info.license_name'] = $license_info->license_name;
            }
        }

        // 学员选择题库类型 exam_license_name
        if ($this->request->has('exam_license_name')) {
            $licenseValid = DB::table('license_config')
                ->select('license_id')
                ->where('license_name', '=', $this->request->input('exam_license_name'))
                ->first();
            if (! $licenseValid) {
                $data = [
                    'code' => 400,
                    'msg'  => '牌照类型不对',
                    'data' => '',
                ];
                return response()->json($data);
            }
            $raw_data['info.exam_license_name'] = $this->request->input('exam_license_name');
        }

        if (empty($raw_data)) {
            return response()->json([
                'code' => 200,
                'msg'  => '未作更改',
                'data' => new \stdClass,
            ]);
        }
        $updated = DB::table('user as user')
            ->leftJoin('users_info as info', 'user.l_user_id', '=', 'info.user_id')
            ->where([
                ['s_phone', '=', $phone],
                ['i_status', '=', 0],
                ['l_user_id', '=', $user_id],
            ])
            ->update($raw_data);
        if ($updated >= 1) {
            // 返回的是受影响的行数
            $data = [
                'code' => 200,
                'msg'  => '更改成功',
                'data' => '',
            ];
        } else {
            $data = [
                'code' => 400,
                'msg'  => '更改失败',
                'data' => '',
            ];
        }
        return response()->json($data);
    }

    /**
     * 根据user_id获取coach_id
     *
     * @param int $user_id
     * @return int $coach_id
     * */
    public function getCoachIdByUserId($user_id)
    {
        $coach = DB::table('coach as coach')
            ->select(
                'l_coach_id as coach_id'
            )
            ->leftJoin('user as user', 'coach.user_id', '=', 'user.l_user_id')
            ->where([
                ['coach.user_id', '=', $user_id],
                ['user.i_user_type', '=', 1], // 1 是教练
                ['user.i_status', '=', 0], // 0 是正常用户
            ])
            ->first();
        if ($coach && isset($coach->coach_id)) {
            return intval($coach->coach_id);
        } else {
            return 0;
        }
    }

    /**
     * 根据coach_id获取user_id
     *
     * @param int $coach_id
     * @return int $user_id
     * */
    public function getUserIdByCoachId($coach_id)
    {
        $coach = DB::table('coach as coach')
            ->select(
                'user_id'
            )
            ->leftJoin('user as user', 'coach.user_id', '=', 'user.l_user_id')
            ->where([
                ['coach.l_coach_id', '=', $coach_id],
                ['user.i_user_type', '=', 1], // 1 是教练
                ['user.i_status', '=', 0], // 0 是正常用户
            ])
            ->first();
        if ($coach && isset($coach->user_id)) {
            return intval($coach->user_id);
        } else {
            return false;
        }
    }

    /**
     * 更新教练个人资料
     *
     * @param   string  $token              登录标识 (JWT token)
     * @param   string  $coach_name         教练名字
     * @param   string  $teach_age          教齡
     * @param   int     $must_bind          预约学车必须绑定否 1是　2否 0未设置
     * @param   string  $coach_sex          教练性别
     * @param   int     $lesson2_pass_rate  科目二通过率 (80)%
     * @param   int     $lesson3_pass_rate  科目三通过率 (80)%
     * @param   string  $lesson_id          科目id (2,3)
     * @param   string  $license_id         牌照id (1,3,4)
     * @param   FILE    $coach_imgurl       教练头像
     * @param   int     $school_id          驾校id
     * @param   string  $coach_content      教练签名
     * @param   int     $province_id        省id
     * @param   int     $city_id            市id
     * @param   int     $area_id            区id
     * @param   string  $coach_address      教练地址
     * @param   int     $online             教练是否在线接单(字段 order_receive_status) 1是 0否
     * @param   int     $coupon_supported   是否支持优惠券报名本人设置的班制 1是　0否
     * @param   int     $shift_min_price    教练设置班制时的价格区间，左区间
     * @param   int     $shift_max_price    教练设置班制时的价格区间，右区间
     * @return \Response
     */
    public function updateCoachProfile()
    {
        $user = (new AuthController())->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $coach_id = $this->getCoachIdByUserId($user_id);
        $original_coach_info = $this->getCoachInfoById($coach_id);
        $coach_info = $this->getCoachDetailInfoById($coach_id);
        $user_phone = $user['phone'];

        $update_data = [];
        // 教练姓名
        if ($this->request->has('coach_name')) {
            $coach_name = $this->request->input('coach_name');
            $length = mb_strlen($coach_name);
            if ($length > 10) {
                $data = [
                    'code' => 400,
                    'msg' => '亲，名字不可超过10个字呦',
                    'data' => '',
                ];
                return response()->json($data);
            }
            $update_data['s_coach_name'] = $coach_name;
        }
        // 教龄
        if ($this->request->has('teach_age')) {
            $teach_age = $this->request->input('teach_age');
            if ($teach_age < 0 || $teach_age > 60) {
                $data = [
                    'code' => 400,
                    'msg' => '亲，教龄的范围在0-60之间哦',
                    'data' => new \stdClass,
                ];
                return response()->json($data);
            }
            $update_data['s_teach_age'] = $teach_age;
        }
        // 绑定状态
        if ($this->request->has('must_bind')) {
            $update_data['must_bind'] = $this->request->input('must_bind');
        }
        // 性别
        if ($this->request->has('coach_sex')) {
            $coach_sex = $this->request->input('coach_sex');
            $sex = ['0', '1'];
            if (!in_array($coach_sex, $sex)) {
                $update_data['s_coach_sex'] = '0';
            } else {
                $update_data['s_coach_sex'] = $coach_sex;
            }
        }
        // 科目二通过率
        if ($this->request->has('lesson2_pass_rate')) {
            $lesson2_pass_rate = $this->request->input('lesson2_pass_rate');
            if ($lesson2_pass_rate < 0) {
                $data = [
                    'code' => 400,
                    'msg' => '通过率不可低于0',
                    'data' => new \stdClass,
                ];
                return response()->json($data);
            } elseif ($lesson2_pass_rate > 100) {
                $data = [
                    'code' => 400,
                    'msg' => '通过率不可超过100',
                    'data' => new \stdClass,
                ];
                return response()->json($data);
            }
            $update_data['lesson2_pass_rate'] = $lesson2_pass_rate;
        }
        // 科目三通过率
        if ($this->request->has('lesson3_pass_rate')) {
            $lesson3_pass_rate = $this->request->input('lesson3_pass_rate');
            if ($lesson3_pass_rate < 0) {
                $data = [
                    'code' => 400,
                    'msg' => '通过率不可低于0',
                    'data' => new \stdClass,
                ];
                return response()->json($data);
            } elseif ($lesson3_pass_rate > 100) {
                $data = [
                    'code' => 400,
                    'msg' => '通过率不可超过100',
                    'data' => new \stdClass,
                ];
                return response()->json($data);
            }
            $update_data['lesson3_pass_rate'] = $lesson3_pass_rate;
        }
        // 驾校id
        if ($this->request->has('school_id')) {
            $update_data['s_school_name_id'] = $this->request->input('school_id');
        }
        // 教练自我评价
        if ($this->request->has('coach_content')) {
            $coach_content = $this->request->input('coach_content');
            $length = mb_strlen($coach_content);
            if ($length > 100) {
                $data = [
                    'code' => 400,
                    'msg' => '亲，不可超过100个字哦',
                    'data' => new \stdClass,
                ];
                return response()->json($data);
            }
            $update_data['s_coach_content'] = $coach_content;
        }
        // 省id
        if ($this->request->has('province_id')) {
            $province_id = $this->request->input('province_id');
            $province_info = DB::table('province')
                ->select('provinceid')
                ->where('provinceid', '=', $province_id)
                ->first();
            if ($province_info) {
                $update_data['province_id'] = $province_info->provinceid;
            } else {
                $data = [
                    'code' => 400,
                    'msg' => '不存在此省',
                    'data' => new \stdClass,
                ];
                return response()->json($data);
            }
        }
        // 市id
        if ($this->request->has('city_id')) {
            $city_id = $this->request->input('city_id');
            $city_info = DB::table('city')
                ->select('cityid')
                ->where('cityid', '=', $city_id)
                ->first();
            if ($city_info) {
                $update_data['city_id'] = $city_info->cityid;
            } else {
                $data = [
                    'code' => 400,
                    'msg' => '不存在此市',
                    'data' => new \stdClass,
                ];
                return response()->json($data);
            }
        }
        // 地区id
        if ($this->request->has('area_id')) {
            $area_id = $this->request->input('area_id');
            $area_info = DB::table('area')
                ->select('areaid')
                ->where('areaid', '=', $area_id)
                ->first();
            if ($area_info) {
                $update_data['area_id'] = $area_info->areaid;
            } else {
                $data = [
                    'code' => 400,
                    'msg' => '不存在此地区',
                    'data' => new \stdClass,
                ];
                return response()->json($data);
            }
        }
        // 教练地址
        if ($this->request->has('coach_address')) {
            $update_data['s_coach_address'] = $this->request->input('coach_address');
        }
        // 在线状态
        if ($this->request->has('online')) {
            $update_data['order_receive_status'] = $this->request->input('online');
        }
        // 券支持状态
        if ($this->request->has('coupon_supported')) {
            $update_data['coupon_supported'] = $this->request->input('coupon_supported');
        }

        if ($this->request->has('shift_min_price') && $this->request->has('shift_max_price')) {
            $update_data['shift_min_price'] = $this->request->input('shift_min_price');
            $update_data['shift_max_price'] = $this->request->input('shift_max_price');
            if (isset($update_data['shift_max_price']) && isset($update_data['shift_min_price'])) {
                if ($update_data['shift_max_price'] < $update_data['shift_min_price']) {
                    return response()->json(['code' => 400, 'msg' => '请调整班制价格区间，最低价格应不高于最高价格', 'data' => new \stdClass]);
                }
            }
        } else {
            if ($this->request->has('shift_min_price')) {
                if ($this->request->input('shift_min_price') > $coach_info->shift_max_price) {
                    return response()->json(['code' => 400, 'msg' => '班制最低价格不可高于'.$coach_info->shift_max_price, 'data' => new \stdClass]);
                }
                $update_data['shift_min_price'] = $this->request->input('shift_min_price');
            }
            if ($this->request->has('shift_max_price')) {
                if ($this->request->input('shift_max_price') < $coach_info->shift_min_price) {
                    return response()->json(['code' => 400, 'msg' => '班制最高价格不可低于'.$coach_info->shift_min_price, 'data' => new \stdClass]);
                }
                $update_data['shift_max_price'] = $this->request->input('shift_max_price');
            }
        }
        
        // 科目id
        if ($this->request->has('lesson_id')) {
            $lesson_id = $this->request->input('lesson_id');
            $lesson_id_arr = explode(',', $lesson_id);
            $lesson_info = DB::table('lesson_config')
                ->select('lesson_id')
                // ->whereIn('lesson_id', $lesson_id_arr)
                ->where('is_open', '=', 1)
                ->get();
            $lesson_arr_id = [];
            foreach ($lesson_info as $key => $value) {
                foreach ($value as $k => $v) {
                    $lesson_arr_id[] = $v;
                }
            }
            foreach ($lesson_id_arr as $key => $value) {
                if (!in_array($value, $lesson_arr_id)) {
                    $data = [
                        'code' => 400,
                        'msg' => '科目类型错误',
                        'data' => new \stdClass,
                    ];
                    return response()->json($data);
                }
            }
            $update_data['s_coach_lesson_id'] = $lesson_id;
        }
        // 牌照id
        if ($this->request->has('license_id')) {
            $license_id = $this->request->input('license_id');
            $license_id_arr = explode(',', $license_id);
            $license_info = DB::table('license_config')
                ->select('license_id')
                ->where('is_open', '=', 1)
                ->get();
            $license_arr_id = [];
            foreach ($license_info as $key => $value) {
                foreach ($value as $k => $v) {
                    $license_arr_id[] = $v;
                }
            }
            foreach ($license_id_arr as $key => $value) {
                if (!in_array($value, $license_arr_id)) {
                    $data = [
                        'code' => 400,
                        'msg' => '牌照类型错误',
                        'data' => new \stdClass,
                    ];
                    return response()->json($data);
                }
            }
            $update_data['s_coach_lisence_id'] = $license_id;
        }

        $nowtime = time();
        // 更新教练头像
        if ($this->request->hasFile('coach_imgurl')) {
            $prefix = 'coachimg'.$nowtime; // 文件名前缀
            // $path = app()->UPLOAD_PATH;
            $dir = 'coach/'.$coach_id.'/'.date('Ymd', time()); // 创建文件路径
            $field_value = $this->uploadFile('coach_imgurl', 'upload/', $dir, $prefix);
            $update_data['s_coach_imgurl'] = $field_value;
        } else {
            $update_data['s_coach_imgurl'] = $original_coach_info->coach_imgurl;
        }

        // 更新教练证图片
        if ($this->request->hasFile('coach_license_imgurl')) {
            $prefix = 'coachlicense'.$nowtime; // 文件名前缀
            // $path = app()->UPLOAD_PATH;
            $dir = 'coach/'.$coach_id.'/'.date('Ymd', time()); // 创建文件路径
            $field_value = $this->uploadFile('coach_license_imgurl', 'upload/', $dir, $prefix);
            $update_data['coach_license_imgurl'] = $field_value;
        } else {
            $update_data['coach_license_imgurl'] = $original_coach_info->coach_license_imgurl;
        }

        // 更新教练个人形象照
        if ($this->request->hasFile('personal_imgurl')) {
            $prefix = 'personalimgurl'.$nowtime; // 文件名前缀
            $dir = 'coach/'.$coach_id.'/'.date('Ymd', time()); // 创建文件路径
            $field_value = $this->uploadFile('personal_imgurl', 'upload/', $dir, $prefix);
            $update_data['personal_image_url'] = $field_value;
        } else {
            $update_data['personal_image_url'] = $original_coach_info->personal_imgurl;
        }

        // 更新教练车图片
        if ($this->request->hasFile('coach_car_imgurl')) {
            $prefix = 'coachcarimg'.$nowtime; // 文件名前缀
            $dir = 'coach/'.$coach_id.'/'.date('Ymd', time()); // 创建文件路径
            $field_value = $this->uploadFile('coach_car_imgurl', 'upload/', $dir, $prefix);
            $update_data['coach_car_imgurl'] = $field_value;
        } else {
            $update_data['coach_car_imgurl'] = $original_coach_info->coach_car_imgurl;
        }

        // 更新教练车图片
        if ($this->request->hasFile('id_card_imgurl')) {
            $prefix = 'idcardimg'.$nowtime; // 文件名前缀
            $dir = 'coach/'.$coach_id.'/'.date('Ymd', time()); // 创建文件路径
            $field_value = $this->uploadFile('id_card_imgurl', 'upload/', $dir, $prefix);
            $update_data['id_card_imgurl'] = $field_value;
        } else {
            $update_data['id_card_imgurl'] = $original_coach_info->id_card_imgurl;
        }

        if ($update_data['coach_license_imgurl'] != ''
            || $update_data['personal_image_url'] != ''
            || $update_data['coach_car_imgurl'] != ''
            || $update_data['id_card_imgurl'] != ''
        ) {
            $update_data['certification_status'] = 2;
        }

        $update_data['coach.updatetime'] = time();
        $update = DB::table('coach')
            ->where([
                ['user.i_status', '=', 0],
                ['user.i_user_type', '=', 1],
                ['user.l_user_id', '=', $user_id],
                ['coach.user_id', '=', $user_id],
                ['coach.s_coach_phone', '=', $user_phone],
            ])
            ->leftJoin('user', 'user.l_user_id', '=', 'coach.user_id')
            ->update($update_data);
        if ($update >= 1) {
            $coach_info = $this->getCoachDetailInfoById($coach_id);
            $data = [
                'code' => 200,
                'msg' => '更新教练个人资料信息成功',
                'data' => [
                    'coach_info' => $coach_info,
                ],
            ];
        } else {
            $data = [
                'code' => 400,
                'msg' => '更新教练个人资料信息失败',
                'data' => new \stdClass,
            ];
        }
        return response()->json($data);
    }


    /**
     * 更新教练车辆信息
     *
     * @param   string          $token              登录标识 (JWT token)
     * @param   number          $car_id             可选的车id
     * @param   string          $car_name           车辆名称
     * @param   string          $car_number         车牌号
     * @param   string          $car_type           车辆型号
     * @param   array[string]   $car_picture        车辆图片
     * @param   file            $car_picture_upload 车辆图片
     * @return \Response
     */
    public function updateCoachCarInfo()
    {
        $user = (new AuthController())->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $user_type = $user['i_user_type'];
        $user_phone = $user['phone'];
        $coach_id = $this->getCoachIdByUserId($user_id);
        $coach_info = $this->getCoachInfoById($coach_id);
        if (!$coach_info) {
            $data = [
                'code' => 400,
                'msg' => '此教练账号出现异常',
                'data' => new \stdClass,
            ];
            return response()->json($data);
        }


        $update_data = [];
        if ('' !== $coach_info->school_id) {
            $update_data['school_id'] = $coach_info->school_id;
        } else {
            $update_data['school_id'] = 0;
        }
        $update_data['addtime'] = time();

        // 车辆存在（更新）
        if ($this->request->has('car_id')
            && 0 !== (int)$this->request->has('car_id')) {
            $car_id = $this->request->input('car_id');
            $car_info = $this->getCarInfoById($car_id);
            if (0 !== (int)$coach_info->car_id) {
                $coach_car_id = $coach_info->car_id;
                if ($car_id !== $coach_car_id) {
                    $data = [
                        'code' => 400,
                        'msg' => '您绑定的车辆的相关信息出现异常',
                        'data' => new \stdClass,
                    ];
                    Log::Info('异常：教练绑定的车辆与请求的车辆不一致');
                    return response()->json($data);
                }
            }

            if (!$car_info) {
                $data = [
                    'code' => 400,
                    'msg' => '该车辆信息出现异常',
                    'data' => new \stdClass,
                ];
                Log::Info('异常：车辆不存在');
                return response()->json($data);
            }
            $update_data['id'] = $car_id;

            if ($this->request->has('car_name')) {
                $update_data['name'] = $this->request->input('car_name');
            } else {
                $update_data['name'] = $car_info->car_name;
            }

            if ($this->request->has('car_number')) {
                $update_data['car_no'] = $this->request->input('car_number');
            } else {
                $update_data['car_no'] = $car_info->car_number;
            }

            if ($this->request->has('car_type')) {
                $car_type = (int)$this->request->input('car_type');
                if (!in_array($car_type, [1, 2, 3])) { // 1：普通车型（默认） 2：加强车型 3：模拟车型
                    $car_type = 1;
                }
            } else {
                $car_type = (int)$car_info->car_type;
                if (!in_array($car_type, [1, 2, 3])) {
                    $car_type = 1;
                }
            }
            $update_data['car_type'] = $car_type;

            // 处理已存在的照片
            if ($this->request->has('car_picture')) {
                $car_picture = [];
                $old_car_imgurl = $this->request->input('car_picture');
                $old_car_imgurl_arr = array_filter(json_decode($old_car_imgurl, true));
                if (!is_array($old_car_imgurl_arr)) {
                    $data = [
                        'code' => 400,
                        'msg' => '参数错误',
                        'data' => new \stdClass,
                    ];
                    Log::Info('异常：图片存储的json格式错误');
                    return response()->json($data);
                }
                if ($old_car_imgurl_arr) {
                    foreach ($old_car_imgurl_arr as $key => $value) {
                        $imgurl_prefix = '';
                        $upload_path = env('APP_PATH');
                        if (false !== strpos($value, $upload_path)) {
                            $imgurl_prefix = $upload_path;
                        } elseif (false !== strpos($value, $upload_path.'sadmin/')) {
                            $imgurl_prefix = $upload_path.'sadmin/';
                        } elseif (false !== strpos($value, $upload_path.'admin/')) {
                            $imgurl_prefix = $upload_path.'admin/';
                        }
                        $car_picture[] = substr($value, strlen($imgurl_prefix), strlen($value) - strlen($imgurl_prefix));
                    }
                }
                $car_picture = $car_picture;
            } else {
                $car_picture = [];
            }

            // 补充上传的图片
            if ($this->request->hasFile('car_picture_upload')) {
                $files = $_FILES;
                $car_picture_upload = [];
                $fieldname = $files['car_picture_upload'];
                if (is_array($fieldname['name'])) { // 是数组
                    foreach ($fieldname['name'] as $field_index => $field_value) {
                        if ($fieldname['error'][$field_index] === UPLOAD_ERR_OK) {
                            $exts_type = $fieldname['type'][$field_index];
                            $exts_type_arr = explode('/', $exts_type);
                            $exts =  ['jpg', 'png', 'jpeg', 'gif'];
                            if (!in_array($exts_type_arr[1], $exts)) {
                                $data = [
                                    'code' => 400,
                                    'msg' => '上传的文件格式错误',
                                    'data' => new \stdClass,
                                ];
                                return response()->json($data);
                            }
                        }
                    }
                } else { // 非数组
                    if ($fieldname['error'] === UPLOAD_ERR_OK) {
                        $exts_type = $fieldname['type'];
                        $exts_type_arr = explode('/', $exts_type);
                        $exts =  ['jpg', 'png', 'jpeg', 'gif'];
                        if (!in_array($exts_type_arr[1], $exts)) {
                            $data = [
                                'code' => 400,
                                'msg' => '上传的文件格式错误',
                                'data' => new \stdClass,
                            ];
                            return response()->json($data);
                        }
                    }
                }
                $prefix = 'car_coach_';
                $dir = 'car/'.$coach_id.'_'.$car_id.'/'.date('Ymd', time()).'/'; // 创建文件路径
                $picture_upload = $this->uploadFiles($fieldname, $prefix, $dir);
                $car_picture_upload = $picture_upload;
            } else {
                $car_picture_upload = [];
            }

            if ((int)(count($car_picture) + count($car_picture_upload)) > 5) {
                $can_upload_count = (int)(5 - (int)count($car_picture));
                $data = [
                    'code' => 400,
                    'msg' => '您还可以上传'.$can_upload_count.'张图片',
                    'data' => new \stdClass,
                ];
                return response()->json($data);
            }

            $s_car_imgurl = array_merge($car_picture, $car_picture_upload);
            $update_data['imgurl'] = json_encode($s_car_imgurl);
            $update_ok = DB::table('cars')
                ->where('cars.id', '=', $car_id)
                ->update($update_data);
            if ($update_ok == 1) {
                $cars_info = $this->getCarInfoById($car_id);
                $data = [
                    'code' => 200,
                    'msg' => '更新成功',
                    'data' => $cars_info,
                ];
            } else {
                $data = [
                    'code' => 400,
                    'msg' => '更新失败',
                    'data' => new \stdClass,
                ];
            }
        } else {
            // 车辆不存在（新增）
            if ((false === $this->request->has('car_name') || '' === $this->request->input('car_name'))
                && (false === $this->request->has('car_number') || '' === $this->request->input('car_number'))) {
                $data = [
                    'code' => 400,
                    'msg' => '车名或车牌号不能为空',
                    'data' => new \stdClass,
                ];
                return response()->json($data);
            }

            if ($this->request->has('car_name')) {
                $update_data['name'] = $this->request->input('car_name');
            } else {
                $update_data['name'] = '';
            }

            if ($this->request->has('car_number')) {
                $update_data['car_no'] = $this->request->input('car_number');
            } else {
                $update_data['car_no'] = '';
            }

            if ($this->request->has('car_type')) {
                $update_data['car_type'] = $this->request->input('car_type');
            } else {
                $update_data['car_type'] = 1;
            }
            $car_id = DB::table('cars')
                ->insertGetId([
                    'name' => $update_data['name'],
                    'car_no' => $update_data['car_no'],
                    'car_type' => $update_data['car_type'],
                    'school_id' => $update_data['school_id'],
                    'addtime' => $update_data['addtime'],
                ]);
            if ($car_id) {
                // 更新教练表中的车辆id
                $coach_data['s_coach_car_id'] = $car_id;
                $update_ok = DB::table('coach')
                    ->where('l_coach_id', '=', $coach_id)
                    ->update($coach_data);
                // 更新图片
                $new_data = [];
                if ($this->request->hasFile('car_picture_upload')) {
                    $files = $_FILES;
                    $car_picture_upload = [];
                    $fieldname = $files['car_picture_upload'];
                    if (is_array($fieldname['name'])) { // 是数组
                        foreach ($fieldname['name'] as $field_index => $field_value) {
                            if ($fieldname['error'][$field_index] === UPLOAD_ERR_OK) {
                                $exts_type = $fieldname['type'][$field_index];
                                $exts_type_arr = explode('/', $exts_type);
                                $exts =  ['jpg', 'png', 'jpeg', 'gif'];
                                if (!in_array($exts_type_arr[1], $exts)) {
                                    $data = [
                                        'code' => 400,
                                        'msg' => '上传的文件格式错误',
                                        'data' => new \stdClass,
                                    ];
                                    return response()->json($data);
                                }
                            }
                        }
                    } else { // 非数组
                        if ($fieldname['error'] === UPLOAD_ERR_OK) {
                            $exts_type = $fieldname['type'];
                            $exts_type_arr = explode('/', $exts_type);
                            $exts =  ['jpg', 'png', 'jpeg', 'gif'];
                            if (!in_array($exts_type_arr[1], $exts)) {
                                $data = [
                                    'code' => 400,
                                    'msg' => '上传的文件格式错误',
                                    'data' => new \stdClass,
                                ];
                                return response()->json($data);
                            }
                        }
                    }

                    $prefix = 'car_coach_';
                    $dir = 'car/'.$coach_id.'_'.$car_id.'/'.date('Ymd', time()).'/'; // 创建文件路径
                    $picture_upload = $this->uploadFiles($fieldname, $prefix, $dir);
                    $car_picture_upload = $picture_upload;
                } else {
                    $car_picture_upload = [];
                }
                $new_data['imgurl'] = json_encode($car_picture_upload);
                $new_data['addtime'] = time();
                $update_ok = DB::table('cars')
                    ->where('id', '=', $car_id)
                    ->update($new_data);
                if ($update_ok >= 1) {
                    $car_info = $this->getCarInfoById($car_id);
                    $data = [
                        'code' => 200,
                        'msg' => '新增成功',
                        'data' => $car_info,
                    ];
                } else {
                    $data = [
                        'code' => 200,
                        'msg' => '新增失败',
                        'data' => new \stdClass,
                    ];
                }
            } else {
                $data = [
                    'code' => 200,
                    'msg' => '新增失败',
                    'data' => new \stdClass,
                ];
            }
        }
        return response()->json($data);
    }

    /**
    * 获取或生成用户的二维码信息
    *
    * @param  JWT   $token
    * @return void
    */
    public function getUserQrcode($user_type)
    {
        if (false === ($type = $this->checkUserType($user_type))) {
            $data = ['code' => 400, 'msg' => '不存在的用户类型', 'data' => new \stdClass];
            Log::Info('[UserController/getUserQrcode]异常：请求的用户类型不存在');
            return response()->json($data);
        }

        $user = (new AuthController())->getUserFromToken($this->request->input('token'));
        $i_user_type = $user['i_user_type'];
        $user_id = $user['user_id'];
        $user_phone = $user['phone'];
        if ($i_user_type != $type) {
            $data = ['code' => 400, 'msg' => '参数错误', 'data' => new \stdClass,];
            Log::Info('异常：请求的用户类型与token中的用户类型不同');
            return response()->json($data);
        }

        $userinfo = [];
        if (0 == $i_user_type) { // student
            // get user_id
            $userinfo['user_id'] = $user_id;

            // get user_phone
            $userinfo['user_phone'] = $user_phone;

            $user_info = $this->getUserInfoById($user_id);
            // return response()->json($user_info);
            if (!$user_info) {
                $data = ['code' => 400, 'msg' => '用户信息出现异常', 'data' => new \stdClass,];
                return response()->json($data);
            }

            // get user_name
            if ('' != $user_info->real_name) {
                $userinfo['user_name'] = $user_info->real_name;
            } else {
                $userinfo['user_name'] = '';
            }

            // get photo_id
            if (0 == $user_info->photo_id) {
                $userinfo['photo_id'] = $user_info->photo_id;
            } else {
                $userinfo['photo_id'] = 1;
            }

            // get sex
            $userinfo['user_sex'] = $user_info->sex;

            // get identy_id
            $userinfo['identity_id'] = $user_info->identity_id;
           
            // get province
            if (0 != $user_info->province_id) {
                $province_id = $user_info->province_id;
                $province_info = $this->getProvinceInfo($province_id);
                if (!$province_info) {
                    $userinfo['province'] = '';
                } else {
                    $userinfo['province'] = $province_info->province;
                }
            } else {
                $userinfo['province'] = '';
            }

            // get city
            if (0 != $user_info->city_id) {
                $city_id = $user_info->city_id;
                $city_info = $this->getCityInfo($city_id);
                if (!$city_info) {
                    $userinfo['city'] = '';
                } else {
                    $userinfo['city'] = $city_info->city;
                }
            } else {
                $userinfo['city'] = $city_info->city;
            }

            // absolut path
            $abs_path = app('UPLOAD_PATH').'studentqrcode/'.$user_phone.'/'.date('Ymd', time()).'/';
            if (!file_exists($abs_path)) {
                if (mkdir($abs_path, 0777, true)) {
                    $abs_path = $abs_path;
                }
            }

            $nowtime = time();
            $path = env('APP_UPLOAD_PATH');
            $abs_filename = $abs_path.'qrcode_'.$nowtime.'.png';
            $content_arr = [
                'XHUSER',
                $userinfo['user_id'],
                $userinfo['user_phone'],
                $userinfo['identity_id'],
                $userinfo['user_name'],
                $userinfo['photo_id'],
                rand(),
            ];
            $content_str = implode(',', $content_arr);
            $userinfo['qrcode_url'] = $path.'studentqrcode/'.$user_phone.'/'.date('Ymd', time()).'/qrcode_'.$nowtime.'.png';
            QrCode::errorCorrection('M'); // 15% 的字节码恢复率
            QrCode::format('png');
            QrCode::size(300);
            QrCode::encoding('UTF-8')->generate($content_str, $abs_filename);
            
            $userinfo['qrcode_desc'] = '扫一扫二维码，可登陆电子教练';
            if (empty($userinfo['identity_id'])) {
                $userinfo['errmsg'] = '请完善您的身份证信息';
                $userinfo['qrcode_url'] = '';
            }
            unset($userinfo['identity_id']);

            if (file_exists($abs_filename)) {
                $data = ['code' => 200, 'msg' => '成功', 'data' => ['qrcodeinfo' => $userinfo]];
            } else {
                $userinfo['qrcode_url'] = '';
                $data = ['code' => 200, 'msg' => '成功', 'data' => ['qrcodeinfo' => $userinfo]];
            }
        } elseif (1 == $i_user_type) { // coach
            $coach_id = $this->getCoachIdByUserId($user_id);
            $coach_info = $this->getCoachInfoById($coach_id);
            if (!$coach_info) {
                $data = ['code' => 400, 'msg' => '用户信息出现异常', 'data' => new \stdClass,];
                return response()->json($data);
            }

            // get coach_id
            $coach_id = $coach_info->coach_id;
            $userinfo['coach_id'] = $coach_id;
             
            // get coach_name
            $coach_name = $coach_info->coach_name;
            $userinfo['coach_name'] = $coach_name;
            
            // get coach_phone
            $coach_phone = $coach_info->coach_phone;
            $userinfo['coach_phone'] = $coach_phone;

            // get coach_imgurl
            $coach_imgurl = $coach_info->coach_imgurl;
            $coach_imgurl = $this->buildUrl($coach_imgurl);
            
            $params_1 = $params_2 = $params_3 = 1;
            $token = urlencode($coach_id.'|'.$coach_name.'|'.$coach_phone.'|'.$params_1.'|'.$params_2.'|'.$params_3.'|'.rand());
           
            // $_token = Crypt::encrypt($token);
            $_token = (new EncryptionController)->encode($token);
            
            // absolut path
            $abs_path = app('UPLOAD_PATH').'coachqrcode/'.$coach_phone.'/'.date('Ymd', time()).'/';
            if (!file_exists($abs_path)) {
                if (mkdir($abs_path, 0777, true)) {
                    $abs_path = $abs_path;
                }
            }

            $nowtime = time();
            $path = env('APP_PATH');
            $upload_path = env('APP_UPLOAD_PATH');
            $abs_filename = $abs_path.'qrcode_'.$coach_id.'_'.$nowtime.'.png';
            $content_str = $path."m/coachuser/token/".urlencode($_token);
            $qrcode_url = $upload_path.'coachqrcode/'.$coach_phone.'/'.date('Ymd', time()).'/'.'qrcode_'.$coach_id.'_'.$nowtime.'.png';
            $userinfo['qrcode_url'] = $qrcode_url;
            QrCode::errorCorrection('M'); // 15% 的字节码恢复率
            QrCode::format('png');
            QrCode::size(300);
            if ($coach_imgurl) {
                QrCode::encoding('UTF-8')->merge($coach_imgurl, .3, true)->generate($content_str, $abs_filename);
            } else {
                QrCode::encoding('UTF-8')->generate($content_str, $abs_filename);
            }

            
            if (file_exists($abs_filename)) {
                $data = ['code' => 200, 'msg' => '成功', 'data' => ['qrcodeinfo' => $userinfo]];
            } else {
                $userinfo['qrcode_url'] = '';
                $data = ['code' => 200, 'msg' => '成功', 'data' => ['qrcodeinfo' => $userinfo]];
            }
        }
        return $data;
    }

    /**
    * 获取省份信息
    * @param    int province_id  false
    * @return   province_info
    */
    public function getProvinceInfo($province_id = '')
    {
        if ($province_id == '') {
            $province_info = DB::table('province')
                ->select('province.*')
                ->select();
        } else {
            $province_info = DB::table('province')
                ->select('province.*')
                ->where('provinceid', '=', $province_id)
                ->first();
        }
        return $province_info;
    }

    /**
    * 获取城市信息
    * @param    int city_id  false
    * @return   city_info
    */
    public function getCityInfo($city_id = '')
    {
        if ($city_id == '') {
            $city_info = DB::table('city')
                ->select('city.*')
                ->select();
        } else {
            $city_info = DB::table('city')
                ->select('city.*')
                ->where('cityid', '=', $city_id)
                ->first();
        }
        return $city_info;
    }

    /**
     * 一键分享App
     *
     * @param void
     * @return void
     */
    public function shareApp()
    {
        if ($this->request->has('type')) {
            $type = intval($this->request->input('type'));
        } else {
            $type = 0;
        }
        if (! in_array($type, [0, 1])) {
            $type = 0;
        }

        if ($type == 0) {
            $share_content = '嘻哈学车，一键学车，轻松拿照';
        } elseif ($type == 1) {
            $share_content = '嘻哈学车，让你招生更轻松，管理更方便';
        }
        $to_share = [
            'share_title'   => '嘻哈学车 - 让学车更轻松，更便捷',
            'share_content' => $share_content,
            'share_link'    => 'http://m.xihaxueche.com:8001/html_h5/index.html',
            'share_pic'     => '',
        ];
        $data = [
            'code' => 200,
            'msg'  => 'OK',
            'data' => $to_share,
        ];
        return response()->json($data);
    }

    /**
     * 分享报名班制成功
     * @param   number id  用户ID
     * @param   number rid  用户ID
     * @return  void
     **/
    public function shareSignup()
    {
        if (! $this->request->has('id')
            or ! $this->request->has('rid')
            ) {
            $data = [
                'code'  =>  400,
                'msg'   =>  '缺少参数',
                'data'  =>  new \stdClass,
            ];
            Log::error('异常：【分享报名班制成功】缺少必须参数');
            return response()->json($data);
        }

        $user_id = $this->request->input('id');
        $order_id = $this->request->input('rid');

        $where = [
            'id'            => $order_id,
            'so_user_id'    => $user_id,
        ];
        $whereCondition = function ($query) {
            $query->where('school_orders.so_order_status', '=', '1')
                ->whereIn('school_orders.so_pay_type', [1, 3, 4])
                ->orWhere(function ($_query) {
                    $_query->where('school_orders.so_order_status', '=', '3')
                        ->where('school_orders.so_pay_type', '=', '2');
                });
        };

        $orders_info = DB::table('school_orders')
            ->select('so_username', 'so_shifts_id', 'so_user_id')
            ->where($where)
            ->where($whereCondition)
            ->first();

        if (! $orders_info) {
            $data = [
                'code'  =>  400,
                'data'  =>  '此订单尚未支付成功',
                'msg'   =>  new \stdClass,
            ];
            Log::error('异常：【分享报名班制成功】订单号'.$order_id.'尚未支付成功！');
            return response()->json($data);
        }

        $url = env('APP_PATH').'m/v2/student/public/signup/signupshare?id='.$user_id.'&rid='.$order_id;
        $share_content = '快来和我一起学车吧';
        $to_share = [
            'share_title'   => '嘻哈学车',
            'share_content' => $share_content,
            'share_link'    => $url,
            'share_pic'     => '',
        ];

        $data = [
            'code' => 200,
            'msg'  => 'OK',
            'data' => $to_share,
        ];

        return response()->json($data);
    }


    /**
     * 获取未读消息数目
     *
     * @param int $user_id
     * @return int $message_count
     */
    private function getUnreadMessageCount($user_id, $user_type = 0)
    {
        switch ($user_type) {
        case 0:
            $member_type = 1;
            break;
        case 1:
            $member_type = 2;
            break;
        default:
            $member_type = 1;
            break;
        }

        $message_count = DB::table('sms_sender')
            ->where([
                ['member_id', '=', $user_id],
                ['member_type', '=', $member_type],
                ['is_read', '=', 2], // 1-已读，2-未读
            ])
            ->count();
        return $message_count;
    }

    /**
     * 获取优惠卷数目
     *
     * @param string $phone
     * @return int $coupon_count
     */
    private function getMyCouponCount($phone)
    {
        $coupon_count = DB::table('user_coupon')
            ->where([
                ['user_phone', '=', $phone],
                ['coupon_status', '=', 1], // 1-未使用的coupon
                ['expiretime', '>=', time()], // 未过期
            ])
            ->count();
        return $coupon_count;
    }

    /**
     * 获取消息列表
     *
     * @param   string      $message_type (notice|order[default])
     * @return  \Response
     */
    public function getMessageList()
    {
        $user = (new AuthController())->getUserFromToken($this->request->input('token'));

        $user_id = $user['user_id'];
        $member_id = $user_id;
        $phone = $user['phone'];
        $i_user_type = $user['i_user_type'];

        switch ($i_user_type) {
        case 0:
            $member_type = 1;
            break;
        case 1:
            $member_id = $this->getCoachIdByUserId($user_id);
            $member_type = 2;
            break;
        default:
            $member_type = 1;
            break;
        }

        $message_type = $this->request->input('message_type', 'order');
        switch ($message_type) {
        case 'notice':
            $i_yw_type = 1;
            break;
        case 'order':
            $i_yw_type = 2;
            break;
        default:
            $i_yw_type = 2;
            break;
        }

        $page = $this->request->input('page', 1);
        if ($page <= 0) {
            $page = 1;
        }
        $limit = 10;

        $message_list = DB::table('sms_sender')
            ->select('id', 's_beizhu as beizu', 'is_read', 'addtime', 's_content as content')
            ->where([
                ['member_id', '=', $member_id],
                ['member_type', '=', $member_type],
                ['i_yw_type', '=', $i_yw_type],
                ['is_read', '!=', 101],
            ])
            ->orderBy('addtime', 'desc')
            ->paginate($limit);
            
        if (count($message_list->toArray()['data']) > 0) {
            foreach ($message_list as $i => $msg) {
                $message_list[$i]->push_time = date('Y-m-d H:i:s', $msg->addtime);
            }
        }

        $data = [
            'code' => 200,
            'msg'  => '成功获取消息列表',
            'data' => [
                'message_list' => $message_list,
            ],
        ];
        return response()->json($data);
    }

    /**
     * 读消息接口
     *
     * @param   int     $message_id 消息id
     * @return  \Response
     */
    public function readMessage()
    {
        $user_param = (new AuthController())->getUserFromToken($this->request->input('token'));
        if (! $this->request->has('message_id')) {
            Log::info('message_id为必填字段', ['message_id' => $message_id]);
            throw new InvalidArgumentException('读消息参数错误');
        }
        $message_id = $this->request->input('message_id');
        $user_id = $user_param['user_id'];
        $member_id = $user_id;
        $user_type = $user_param['i_user_type'];
        switch ($user_type) {
        case '0': // 学员
            $member_type = 1;
            break;
        case '1': // 教练
            $member_type = 2;
            try {
                $member_id = $this->getCoachIdByUserId($user_id);
                if ($member_id <= 0) {
                    Log::info('该教练不存在 readMessage接口', ['message_id' => $message_id]);
                    throw new InvalidArgumentException('读消息错误');
                }
            } catch (Exception $e) {
                Log::info('获取教练id出现异常', ['message_id' => $message_id]);
                throw new InvalidArgumentException('读消息错误');
            }
            break;
        default:
            $member_type = 1;
            break;
        }

        $message_info = DB::table('sms_sender')
            ->where([
                ['sms_sender.id', '=', $message_id],
                ['sms_sender.member_id', '=', $member_id],
                ['sms_sender.member_type', '=', $member_type], // 学员0+1 教练1+1
                ['sms_sender.is_read', '=', 2], // 2-消息还未读
            ])
            ->first();

        if ($message_info) {
            $read = DB::table('sms_sender')
                ->where([
                    ['sms_sender.id', '=', $message_id],
                ])
                ->update([
                    'is_read' => 1,
                ]);
            if ($read) {
                $data = [
                    'code' => 200,
                    'msg'  => '成功',
                    'data' => new \stdClass,
                ];
            } else {
                $data = [
                    'code' => 400,
                    'msg'  => '失败',
                    'data' => new \stdClass,
                ];
            }
        } else {
            $data = [
                'code' => 200,
                'msg'  => '成功',
                'data' => new \stdClass,
            ];
        }
        return response()->json($data);
    }

    /**
     * 删除消息接口
     *
     * @param   string $message_id   消息ID (如：23,34,45)
     * @return  \Response
     */
    public function deleteMessage()
    {
        $user = (new AuthController())->getUserFromToken($this->request->input('token'));
        if (! $this->request->has('message_id')) {
            Log::error('删除消息接口：必填字段needed：message_id');
            throw new InvalidArgumentException('删除消息参数错误');
        }
        $message_id = $this->request->input('message_id');
        $message_ids_arr = explode(',', $message_id);
        $user_id = $user['user_id'];
        $member_id = $user_id;
        $user_type = $user['i_user_type'];
        switch ($user_type) {
        case '0': // 学员
            $member_type = 1;
            break;
        case '1': // 教练
            $member_type = 2;
            $member_id = $this->getCoachIdByUserId($user_id);
            break;
        default:
            $member_type = 1;
            break;
        }
        $message_info = DB::table('sms_sender')
            ->where([
                ['sms_sender.member_id', '=', $member_id],
                ['sms_sender.member_type', '=', $member_type], // 0+1 学员,1+1 教练
            ])
            ->whereIn('is_read', [1, 2]) // 1 已读, 2 未读
            ->whereIn('sms_sender.id', $message_ids_arr) // 1 已读, 2 未读
            ->get();
        $message_info = $message_info->toArray();
        if (empty($message_info)) {
            $data = [
                'code' => 400,
                'msg' => '不存在的消息',
                'data' => new \stdClass,
            ];
            Log::Info('不存在的消息', ['message_id' => $message_id]);
            return response()->json($data);
        }
        $update_ok = DB::table('sms_sender')
            ->whereIn('id', $message_ids_arr)
            ->update([
                'is_read' => 101, // 101-删除状态
            ]);
        if ($update_ok >= 1) {
            $data = [
                'code' => 200,
                'msg'  => '成功',
                'data' => new \stdClass,
            ];
        } else {
            $data = [
                'code' => 400,
                'msg'  => '失败',
                'data' => new \stdClass,
            ];
        }
        return response()->json($data);
    }

    /**
     * 获取我的教练
     *
     * @param string $token
     * @return void
     */
    public function getMyCoach()
    {
        $user = (new AuthController())->getUserFromToken($this->request->input('token'));

        $user_id = $user['user_id'];
        $phone = $user['phone'];
        $i_user_type = $user['i_user_type'];

        // 如果不是学员(i_user_type为0)，返回
        if (0 != $i_user_type) {
            $data = [
                'code' => 401,
                'msg'  => '请您重新在学员端登录',
                'data' => [
                    'coach' => [],
                ],
            ];
            return response()->json($data);
        }

        $my_coach = $this->getBindRelation(['coach_user_relation.user_id', '=', $user_id]);

        if (! $my_coach) {
            $data = [
                'code' => 400,
                'msg'  => '您还未绑定教练',
                'data' => '',
            ];
            return response()->json($data);
        }

        $coach_detail = DB::table('coach')
            ->select(
                'l_coach_id as coach_id',
                's_coach_name as coach_name',
                's_coach_phone as coach_phone',
                's_teach_age as teach_age',
                'i_type as coach_type',
                'coach.is_hot',
                's_coach_sex as coach_sex',
                's_coach_imgurl as coach_photo_url',
                'certification_status',
                's_school_name_id as school_id',
                's_coach_car_id as car_id',
                'coach.must_bind',
                'i_coach_star as coach_star',
                's_school_name as school_name'
            )
            ->leftJoin('user', 'coach.user_id', '=', 'user.l_user_id')
            ->leftJoin('school', 'school.l_school_id', '=', 'coach.s_school_name_id')
            ->where([
                ['l_coach_id', '=', $my_coach->coach_id],
                ['i_status', '=', 0],
                ['i_user_type', '=', 1],
            ])
            ->first();
        if ($coach_detail) {
            $coach_detail->bind_status = $my_coach->bind_status;

            // 教练的头像url
            $coach_detail->coach_photo_url = $this->buildUrl($coach_detail->coach_photo_url);

            $data = [
                'code' => 200,
                'msg'  => '获取教练信息成功',
                'data' => [
                    'coach_info' => $coach_detail,
                ],
            ];
        } else {
            $data = [
                'code' => 400,
                'msg'  => '教练信息获取失败',
                'data' => '',
            ];
        }

        return response()->json($data);
    }

    /**
     * 按手机，密码登录
     *
     * @param string $phone
     * @return void
     */
    private function userLogin($phone = '', $pass = '', $i_user_type = 0)
    {
        $user = DB::table('user')
            ->select('l_user_id as uid', 's_password as pass')
            ->where([
                ['s_phone', '=', $phone],
                ['i_user_type', '=', $i_user_type],
                ['i_status', '=', 0]
            ])
            ->orderBy('l_user_id', 'desc')
            ->first();
        if (! $user) {
            return ['msg' => '用户不存在'];
        } elseif (md5($pass) != $user->pass) {
            return ['msg' => '密码不正确'];
        }
        return intval($user->uid);
    }

    /**
     * 手机号有效
     *
     * @param string  $phone
     * @param string  $i_user_type (0|1)
     * @param string  $op (reg|forgetpass)
     * @return bool|string
     */
    private function checkPhone($i_user_type = 0, $op = '')
    {
        if (! $this->request->has('phone')) {
            return ['msg' => '手机未填写'];
        }
        $phone = $this->request->input('phone');
        if (! $this->checkPhoneFormat($phone)) {
            return ['msg' => '手机格式有误'];
        }
        $user = DB::table('user')
            ->where([
                ['i_user_type', '=', $i_user_type],
                ['i_status', '=', 0], // 0-正常 2-已删除
                ['s_phone', '=', $phone],
            ])
            ->first();
        switch ($op) {
            case 'reg':
                if ($user) {
                    return ['msg' => '您已注册，请登录'];
                }
                break;
            case 'forgetpass':
                if ($user) {
                    return $phone;
                } else {
                    return ['msg' => '此手机尚未注册'];
                }
                break;
            case 'login':
                if ($user) {
                    return $phone;
                } else {
                    return ['msg' => '此手机尚未注册'];
                }
                break;
            case 'coupon':
                return $phone;
                break;
            default:
                return $phone;
                break;
        }
        return $phone;
    }

    /**
     * 验证手机号的格式正确性
     *
     * @param str $phone
     * @return bool
     */
    public function checkPhoneFormat($phone)
    {
        if (! preg_match("/^1(3[0-9]|4[579]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/", trim($phone))) {
            return false;
        }
        return true;
    }

    /**
     * 注册的用户类型
     *
     * @param str $user_type
     * @return bool|int
     */
    private function checkUserType($user_type = '')
    {
        $user_type_list = [
            'student' => 0,
            'coach' => 1,
        ];
        if (! in_array($user_type, array_keys($user_type_list))) {
            return false;
        }
        return $user_type_list[$user_type];
    }

    /**
     * 验证码的操作
     *
     * @param str $operation
     * @return bool|str
     */
    private function checkOperation($operation = '')
    {
        $operation_permitted = [
            'reg',
            'coupon',
            'login',
            'forgetpass',
        ];
        if (! in_array($operation, $operation_permitted)) {
            return false;
        }
        return $operation;
    }

    /**
     * 验证码检查
     *
     * @param Request $request
     * @param string $phone
     * @param int    $ttl (default=1800)  30分钟
     * @return void
     */
    private function checkCode($request, $phone, $ttl = 1800)
    {
        if (! $request->has('code')) {
            return ['msg' => '验证码参数不存在'];
        }

        $code = $request->input('code');
        if (6 != strlen($code)) {
            return ['msg' => '6位验证码格式不对', 'data' => $code];
        }

        $record = DB::table('verification_code')
            ->where([
                ['s_phone', '=', $phone],
                ['s_code', '=', $code],
                ['addtime', '>=', time() - $ttl],
                ['addtime', '<=', time()],
            ])
            ->orderBy('addtime', 'desc')
            ->first();
        if ($record) {
            return true;
        } else {
            return ['msg' => '验证码已失效，请重新获取'];
        }
    }

    /**
     * 获取我的优惠券
     *
     * @return void
     * @author cx
     **/
    public function getMyCouponList()
    {
        $user = (new AuthController())->getUserFromToken($this->request->input('token'));

        $user_id = $user['user_id'];
        $i_user_type = $user['i_user_type'];
        $phone = $user['phone'];
        $page = $this->request->has('page') ? intval($this->request->input('page')) : 1;
        $limit = 10;

        $coupon_list = DB::table('user_coupon')
            ->select(
                'id as coupon_id',
                'coupon_name',
                'coupon_desc',
                'coupon_code',
                'coupon_value',
                'coupon_category_id',
                'coupon_sender_owner_id', // 发放券的id
                'coupon_sender_owner_type', // （1：教练，2：驾校，3：嘻哈）
                'coupon_status',  // 1： 未使用，2： 已使用，:3：已过期 ，4：已删除
                'coupon_type',   // （1：自己领取，2：系统推送）
                'expiretime',
                'province_id',
                'city_id',
                'coupon_scope'
            )
            ->where([
                ['user_phone', '=', DB::raw($phone)],
                ['expiretime', '>=', time()],
                ['coupon_sender_owner_type', '=', 1]
            ])
            ->whereNotIn('coupon_status', [2,3,4])
            ->paginate($limit);
        $_coupon_list = $coupon_list->toArray();
        if (!empty($_coupon_list['data'])) {
            foreach ($_coupon_list['data'] as $key => $value) {
                switch ($value->coupon_status) {
                case '1':
                    $_coupon_list['data'][$key]->coupon_status_name = '未使用';
                    break;
                case '2':
                    $_coupon_list['data'][$key]->coupon_status_name = '已使用';
                    break;
                case '3':
                    $_coupon_list['data'][$key]->coupon_status_name = '已过期';
                    break;
                case '4':
                    $_coupon_list['data'][$key]->coupon_status_name = '已删除';
                    break;
                default:
                    $_coupon_list['data'][$key]->coupon_status_name = '已使用';
                    break;
                }

                switch ($value->coupon_sender_owner_type) {
                case '1':
                    $member_info = DB::table('coach')
                        ->select('s_coach_name as member_name')
                        ->where(['l_coach_id'=>"{$value->coupon_sender_owner_id}"])
                        ->first();
                    if ($member_info) {
                        $coupon_format_desc = "只适用于".$member_info->member_name."教练报名班制优惠";
                    } else {
                        $coupon_format_desc = "只适用于教练报名班制优惠";
                    }
                    break;

                case '2':
                    $member_info = DB::table('school')
                        ->select('s_school_name as member_name')
                        ->where(['l_school_id'=>"{$value->coupon_sender_owner_id}"])
                        ->first();
                    $coupon_format_desc = "适用于".$member_info->member_name."驾校下的教练报名班制优惠";
                    break;

                case '3':
                    $coupon_format_desc = "嘻哈学车优惠券，适用于所有报名班制优惠";
                    break;

                default:
                    $coupon_format_desc = "嘻哈学车优惠券，适用于所有报名班制优惠";
                    break;
                }
            }
            $_coupon_list['coupon_format_desc'] = $coupon_format_desc;
            $_coupon_list['expiretime_format'] = date('Y-m-d H:i:s', $value->expiretime);
        }
        $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$coupon_list];
        return response()->json($data);
    }

    /**
     * 登录状态下兑换优惠券
     *
     * @return void
     * @author cx
     **/
    public function exchangeCoupon()
    {
        if (!$this->request->has('city_id') || !$this->request->has('province_id')) {
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => ''
            ]);
        }
        $coupon_id = '';

        // 登陆时候
        if ($this->request->has('token')) {
            $user = (new AuthController())->getUserFromToken($this->request->input('token'));
            if (!$user) {
                return response()->json([
                    'code' => 401,
                    'msg'  => '登录过期，请重新登录',
                    'data' => '',
                ]);
            }
            $phone = $user['phone'];

            // 非登陆时候
        } else {
            if (!$this->request->has('coach_id') || !$this->request->has('coupon_id')) {
                return response()->json([
                    'code' => 400,
                    'msg'  => '参数错误',
                    'data' => ''
                ]);
            }
            if (!$this->request->has('phone') || !$this->request->has('name') || !$this->request->has('code')) {
                return response()->json([
                    'code' => 400,
                    'msg'  => '手机号，姓名或验证码不能为空',
                    'data' => ''
                ]);
            }
            $phone = $this->request->input('phone');
            $code = $this->request->input('code');
            $coach_id = $this->request->input('coach_id');
            $coupon_id = $this->request->input('coupon_id');

            // 验证验证码是否正确
            if (false === ($_code = $this->checkCode($this->request, $phone, 86400))) {
                return ['code' => 400, 'msg' => '验证码失效', 'data' => ''];
            } elseif (is_array($_code)) {
                return ['code' => 400, 'msg' => $_code['msg'], 'data' => ''];
            }

            // 验证手机号格式
            if (!$this->checkPhoneFormat($phone)) {
                return response()->json([
                    'code' => 400,
                    'msg'  => '号码格式错误',
                    'data' => ''
                ]);
            }
        }

        $coupon_code = $this ->request ->has('coupon_code') ? $this ->request ->input('coupon_code') : '';
        $owner_id    = $this ->request ->input('owner_id'); // 券所有者ID
        $owner_type  = $this ->request ->input('owner_type'); // 券角色类别（1：教练 2：驾校）
        $city_id     = $this ->request ->input('city_id');
        $province_id = $this ->request ->input('province_id');
        $name        = $this ->request ->has('name') ? $this        ->request ->input('name')        : '嘻哈学员'.substr($phone, 7, 4);
        $res         = $this ->couponOperation($coupon_code, $coupon_id, $province_id, $city_id, $phone, $name);
        return $res;
    }

    // 券领取操作
    public function couponOperation($coupon_code='', $coupon_id='', $province_id, $city_id, $phone, $name)
    {
        // 查找当前优惠码涉及的优惠券剩余多少

        $coupon_code_info = null;
        $coupon_info = null;
        // 按coupon_code取出coupon_id
        if ($coupon_code) {
            $coupon_code_info = DB::table('coupon_code')
                ->select(
                    'id',
                    'coupon_id',
                    'coupon_code',
                    'is_used',
                    'addtime',
                    'updatetime'
                )
                ->where([
                    ['coupon_code', '=', $coupon_code],
                ])
                ->first();
            if (! $coupon_code_info) {
                $coupon_info = DB::table('coupon')
                    ->select(
                        'id',
                        'owner_type',
                        'owner_id',
                        'coupon_name',
                        'coupon_desc',
                        'coupon_value',
                        'coupon_category_id',
                        'province_id',
                        'city_id',
                        'area_id',
                        'coupon_total_num',
                        'coupon_get_num',
                        'coupon_limit_num',
                        'coupon_scope',
                        'is_open', // 1 for open
                        'addtime',
                        'expiretime'
                    )
                    ->where([
                        ['coupon_code', '=', $coupon_code],
                    ])
                    ->first();
                if (! $coupon_info) {
                    Log::info('无此优惠码，无此优惠券信息 coupon_code->'.$coupon_code);
                    return response()->json(['code' => 400, 'msg' => '您的优惠码不可用', 'data' => new \stdClass]);
                } else {
                    $coupon_code_info = DB::table('coupon_code')
                        ->select(
                            'id',
                            'coupon_id',
                            'coupon_code',
                            'is_used', // 0 is not used
                            'addtime',
                            'updatetime'
                        )
                        ->where([
                            ['is_used', '=', 0],
                        ])
                        ->first();
                }
            } else {
                $coupon_info = DB::table('coupon')
                    ->select(
                        'id',
                        'owner_type',
                        'owner_id',
                        'coupon_name',
                        'coupon_desc',
                        'coupon_value',
                        'coupon_category_id',
                        'province_id',
                        'city_id',
                        'area_id',
                        'coupon_total_num',
                        'coupon_get_num',
                        'coupon_limit_num',
                        'coupon_scope',
                        'is_open', // 1 for open
                        'addtime',
                        'expiretime'
                    )
                    ->where([
                        ['id', '=', $coupon_code_info->coupon_id],
                    ])
                    ->first();
                if (! $coupon_info) {
                    Log::info('有此优惠码，无对应优惠券信息 coupon_code->'.$coupon_code);
                    return response()->json(['code' => 400, 'msg' => '您的优惠码不可用', 'data' => new \stdClass]);
                }
            }
        } else {
            Log::info('查找优惠券 coupon_id->'.$coupon_id);
            $coupon_info = DB::table('coupon')
                ->select(
                    'id',
                    'owner_type',
                    'owner_id',
                    'coupon_name',
                    'coupon_desc',
                    'coupon_value',
                    'coupon_category_id',
                    'province_id',
                    'city_id',
                    'area_id',
                    'coupon_total_num',
                    'coupon_get_num',
                    'coupon_limit_num',
                    'coupon_scope',
                    'is_open', // 1 for open
                    'addtime',
                    'expiretime'
                )
                ->where([
                    ['id', '=', $coupon_id],
                ])
                ->first();
            if (! $coupon_info) {
                Log::info('领取的优惠券不存在 coupon_id->'.$coupon_id);
                return response()->json(['code' => 400, 'msg' => '此优惠不可用', 'data' => new \stdClass]);
            } else {
                $coupon_code_info = DB::table('coupon_code')
                    ->select(
                        'id',
                        'coupon_id',
                        'coupon_code',
                        'is_used', // 0 is not used
                        'addtime',
                        'updatetime'
                    )
                    ->where([
                        ['is_used', '=', 0],
                    ])
                    ->first();
            }
        }

        // here you got a coupon_code($coupon_code_info) and a coupon($coupon_info)
        // satisfy some prerequisites
        //   1. it must be open
        //   2. between addtime and expiretime
        //   3. quota
        //   4. coupon_code is not used yet
        if ($coupon_info->is_open != 1) {
            return response()->json(['code' => 400, 'msg' => '优惠券不可用', 'data' => new \stdClass]);
        }
        $now = time();
        if ($coupon_info->addtime > $now) {
            Log::info('没到兑换时间 time->'.date('Y-m-d H:i:s', $coupon_info->addtime));
            return response()->json(['code' => 400, 'msg' => '兑换优惠马上可用，敬请期待', 'data' => new \stdClass]);
        }
        if ($coupon_info->expiretime < $now) {
            Log::info('兑换过期 time->'.date('Y-m-d H:i:s', $coupon_info->expiretime));
            return response()->json(['code' => 400, 'msg' => '此优惠码已过期', 'data' => new \stdClass]);
        }
        if ($coupon_info->coupon_total_num <= 0) {
            Log::info('优惠券总数少于0');
            return response()->json(['code' => 400, 'msg' => '此优惠码不可用', 'data' => new \stdClass]);
        }
        if ($coupon_info->coupon_total_num - abs($coupon_info->coupon_get_num) <= 0) {
            Log::info('优惠券数无剩余');
            return response()->json(['code' => 400, 'msg' => '此优惠码超出兑换次数', 'data' => new \stdClass]);
        }
        if ($coupon_code_info) {
            if ($coupon_code_info->is_used != 0) {
                Log::info('使用过的优惠码 coupon_code->'.$coupon_code);
                return response()->json(['code' => 400, 'msg' => '使用过的优惠码', 'data' => new \stdClass]);
            }
        }

        // 券是否支持该地区
        switch ($coupon_info->coupon_scope) {
        case '0':  // 全国

            break;
        case '1':  // 全省
            if ($province_id != $coupon_info->province_id) {
                Log::info('券不支持当前省 province->'.$province_id);
                return response()->json(['code'=>400, 'msg'=>'呜呜~~券不支持当前省份', 'data'=>'']);
            }
            break;

        case '2':  // 全市
            if ($city_id != $coupon_info->city_id) {
                Log::info('券不支持当前城市 city->'.$city_id);
                return response()->json(['code'=>400, 'msg'=>'呜呜~~券不支持当前城市', 'data'=>'']);
            }
            break;
        default:
            return response()->json(['code'=>400, 'msg'=>'呜呜~~券不支持当前城市', 'data'=>'']);
            break;
        }
        // 券是否已领取或者领取的数量超过了设置的领取限制
        $user_coupon_info = DB::table('user_coupon')
            ->select(
                DB::raw('coupon_status, coupon_type, expiretime')
            )
            ->where([
                ['user_phone', '=', $phone],
                ['coupon_id', '=', $coupon_info->id],
                ['coupon_status', '<>', 4], // 4 is deleted
            ])
            ->get();

        if (!empty($user_coupon_info->toArray())) {
            $user_coupon_count = count($user_coupon_info);

            // 达到领取最大限制
            if ($coupon_info->coupon_limit_num - $user_coupon_count <= 0) {
                Log::info('此优惠码已完成兑换 coupon_code->'.$coupon_code);
                return response()->json(['code'=>400, 'msg'=>'此优惠码已完成兑换', 'data'=>new \stdClass]);
            } else {
                DB::transaction(function () use ($name, $phone, $coupon_code, $coupon_info, $province_id, $city_id, $coupon_code_info) {
                    // 领取学车券
                    $res = DB::table('user_coupon')->insert([
                        'user_name'                 => $name,
                        'user_phone'                => $phone,
                        'coupon_id'                 => $coupon_info->id,
                        'coupon_name'               => $coupon_info->coupon_name,
                        'coupon_desc'               => $coupon_info->coupon_desc,
                        'coupon_code'               => $coupon_code,
                        'coupon_value'              => $coupon_info->coupon_value,
                        'coupon_category_id'        => $coupon_info->coupon_category_id,
                        'coupon_sender_owner_id'    => $coupon_info->owner_id,
                        'coupon_sender_owner_type'  => $coupon_info->owner_type,
                        'coupon_status'             => 1, // 1:未使用 2：已使用 3：已过期 4：已删除
                        'coupon_type'               => 1, // 1：自己领取 2：系统推送
                        'province_id'               => $province_id,
                        'city_id'                   => $city_id,
                        'area_id'                   => $coupon_info->area_id,
                        'coupon_scope'              => $coupon_info->coupon_scope,
                        'addtime'                   => time(),
                        'expiretime'                => $coupon_info->expiretime
                    ]);

                    // coupon表增加领取次数
                    $get_num = $coupon_info->coupon_get_num + 1;
                    $_res = DB::table('coupon')->where(['id'=>$coupon_info->id])->update(['coupon_get_num'=>$get_num]);
                    // 更新coupon_code状态和兑换时间
                    if ($coupon_code_info) {
                        DB::table('coupon_code')
                            ->where([
                                ['id', '=', $coupon_code_info->id],
                            ])
                            ->update([
                                'updatetime'=>time(),
                                'is_used'=>1, // used
                            ]);
                    }
                });
            }
        } else {
            DB::transaction(function () use ($name, $phone, $coupon_code, $coupon_info, $province_id, $city_id, $coupon_code_info) {
                // 领取学车券
                $res = DB::table('user_coupon')->insert([
                    'user_name'                 => $name,
                    'user_phone'                => $phone,
                    'coupon_id'                 => $coupon_info->id,
                    'coupon_name'               => $coupon_info->coupon_name,
                    'coupon_desc'               => $coupon_info->coupon_desc,
                    'coupon_code'               => $coupon_code,
                    'coupon_value'              => $coupon_info->coupon_value,
                    'coupon_category_id'        => $coupon_info->coupon_category_id,
                    'coupon_sender_owner_id'    => $coupon_info->owner_id,
                    'coupon_sender_owner_type'  => $coupon_info->owner_type,
                    'coupon_status'             => 1, // 1:未使用 2：已使用 3：已过期 4：已删除
                    'coupon_type'               => 1, // 1：自己领取 2：系统推送
                    'province_id'               => $province_id,
                    'city_id'                   => $city_id,
                    'area_id'                   => $coupon_info->area_id,
                    'coupon_scope'              => $coupon_info->coupon_scope,
                    'addtime'                   => time(),
                    'expiretime'                => $coupon_info->expiretime
                ]);

                // coupon表增加领取次数
                $get_num = $coupon_info->coupon_get_num + 1;
                $_res = DB::table('coupon')->where(['id'=>$coupon_info->id])->update(['coupon_get_num'=>$get_num]);
                // 更新coupon_code状态和兑换时间
                if ($coupon_code_info) {
                    DB::table('coupon_code')
                        ->where([
                            ['id', '=', $coupon_code_info->id],
                        ])
                        ->update([
                            'updatetime'=>time(),
                            'is_used'=>1, // used
                        ]);
                }
            });
        }

        $data = ['code'=>200, 'msg'=>'领取成功，请在我的学车券中查看', 'data'=>$coupon_info];
        return response()->json($data);
    }

    /**
     * 申请绑定教练
     *
     * @param int $coach_id
     * @param int $coach_phone
     * @return void
     */
    public function bindCoach()
    {
        $current_time = time();

        $user = (new AuthController())->getUserFromToken($this->request->input('token'));
        if (($coach_id = intval($this->request->input('coach_id'))) <= 0) {
            throw new InvalidArgumentException('参数错误', 400);
        }
        if (! $this->request->has('coach_phone')) {
            throw new InvalidArgumentException('参数错误', 400);
        } else {
            $coach_phone = $this->request->input('coach_phone');
        }
        if (! $coach_info = $this->getCoachInfoById($coach_id)) {
            throw new InvalidArgumentException('参数错误', 400);
        } elseif ($coach_phone != $coach_info->coach_phone) {
            throw new InvalidArgumentException('参数错误', 400);
        }

        $bind_relation = $this->getBindRelation(['coach_user_relation.user_id', '=', $user['user_id']]);
        if ($bind_relation) {
            $coach_info = $this->getCoachInfoById($bind_relation->coach_id);
            $coach_name = $coach_info->coach_name;
            switch ($bind_relation->bind_status) {
            case 1:
                // 已经和其他人绑定成功
                if ($bind_relation->coach_id == $coach_id) {
                    $data = [
                        'code' => 400,
                        'msg'  => '您已和'.$coach_name.'教练绑定',
                        'data' => new \stdClass,
                    ];
                } else {
                    $data = [
                        'code' => 400,
                        'msg'  => '您已'.$coach_name.'教练绑定，不可再绑定其他人',
                        'data' => new \stdClass,
                    ];
                }
                break;
            case 3:
                $data = [
                    'code' => 400,
                    'msg'  => '您已申请与'.$coach_name.'教练绑定，等待通过',
                    'data' => new \stdClass,
                ];
                break;
            case 4:
                if ($bind_relation->coach_id == $coach_id) {
                    $confirm_bind = DB::table('coach_user_relation')
                        ->where('id', '=', $bind_relation->id)
                        ->update([
                            'bind_status' => 1,
                            'updatetime'  => $current_time,
                        ])
                        ;
                    if ($confirm_bind) {
                        $data = [
                            'code' => 200,
                            'msg'  => '您和'.$coach_name.'绑定成功',
                            'data' => new \stdClass,
                        ];
                    } else {
                        $data = [
                            'code' => 400,
                            'msg'  => '您和'.$coach_name.'绑定失败',
                            'data' => new \stdClass,
                        ];
                    }
                } else {
                    $data = [
                        'code' => 400,
                        'msg'  => $coach_name.'教练申请与您绑定',
                        'data' => new \stdClass,
                    ];
                }
                break;
            case 5:
            case 6:
                $data = [
                    'code' => 400,
                    'msg'  => '现在不可申请绑定教练',
                    'data' => new \stdClass,
                ];
                break;
            default:
                $data = [
                    'code' => 400,
                    'msg'  => '参数错误',
                    'data' => new \stdClass,
                ];
                break;
            }
        } else {
            $coach_name = $coach_info->coach_name;
            $must_bind = $coach_info->must_bind;
            if ($must_bind != 1) { // 不是必须绑定学员 设置已绑定
                $new_relation = DB::table('coach_user_relation')
                    ->insertGetId([
                        'coach_id'      => $coach_id,
                        'user_id'       => $user['user_id'],
                        'coach_user_id' => 0,
                        'bind_status'   => 1,
                        'addtime'       => $current_time,
                        'updatetime'    => $current_time,
                    ]);
            } else {
                $new_relation = DB::table('coach_user_relation')
                    ->insertGetId([
                        'coach_id'      => $coach_id,
                        'user_id'       => $user['user_id'],
                        'coach_user_id' => 0,
                        'bind_status'   => 3,
                        'addtime'       => $current_time,
                        'updatetime'    => $current_time,
                    ]);
            }
            if ($new_relation) {
                $data = [
                    'code' => 200,
                    'msg'  => '和'.$coach_name.'教练的绑定申请提交成功',
                    'data' => new \stdClass,
                ];
            } else {
                $data = [
                    'code' => 400,
                    'msg'  => '和'.$coach_name.'教练的绑定申请提交失败',
                    'data' => new \stdClass,
                ];
            }
            $bind_relation = $this->getBindRelation(['coach_user_relation.id', '=', $new_relation]);
        }
        $data['data'] = [
            'coach_info' => $coach_info,
            'bind_relationship'   => $bind_relation,
        ];

        return response()->json($data);
    }

    /**
     * 解除与教练绑定
     *
     * @param int $coach_id
     * @param int $coach_phone
     */
    public function unbindCoach()
    {
        $current_time = time();

        $user = (new AuthController())->getUserFromToken($this->request->input('token'));
        if (($coach_id = intval($this->request->input('coach_id'))) <= 0) {
            throw new InvalidArgumentException('参数错误', 400);
        }
        if (! $this->request->has('coach_phone')) {
            throw new InvalidArgumentException('参数错误', 400);
        } else {
            $coach_phone = $this->request->input('coach_phone');
        }
        if (! $coach_info = $this->getCoachInfoById($coach_id)) {
            throw new InvalidArgumentException('参数错误', 400);
        } elseif ($coach_phone != $coach_info->coach_phone) {
            throw new InvalidArgumentException('参数错误', 400);
        }

        $bind_relation = $this->getBindRelation(['coach_user_relation.user_id', '=', $user['user_id']]);
        if ($bind_relation) {
            $bind_coach_info = $this->getCoachInfoById($bind_relation->coach_id);
            switch ($bind_relation->bind_status) {
            case 1:
            case 3:
            case 4:
            case 5:
            case 6:
                if ($bind_relation->coach_id == $coach_id) {
                    // 将要解绑的教练和已经绑定的教练是同一位
                    $confirm_unbind = DB::table('coach_user_relation')
                        ->where('id', '=', $bind_relation->id)
                        ->update([
                            'bind_status' => 2, // 2-绑定解除状态码
                            'updatetime' => $current_time,
                        ])
                        ;
                    if ($confirm_unbind) {
                        $data = [
                            'code' => 200,
                            'msg'  => '解除成功',
                            'data' => new \stdClass,
                        ];
                    } else {
                        $data = [
                            'code' => 400,
                            'msg'  => '解除失败',
                            'data' => new \stdClass,
                        ];
                    }
                } else {
                    // 绑定的是其它教练
                    $data = [
                        'code' => 400,
                        'msg'  => '您还未和'.$coach_info->coach_name.'绑定，无须操作',
                        'data' => new \stdClass,
                    ];
                }
                break;
            default:
                $data = [
                    'code' => 400,
                    'msg'  => '参数错误',
                    'data' => new \stdClass,
                ];
                break;
            }
        } else {
            $data = [
                'code' => 200,
                'msg'  => '解除成功',
                'data' => new \stdClass,
            ];
        }

        return response()->json($data);
    }

    /**
     * 获取教练与学员的绑定关系
     *
     * @param array $bind_param
     * @return \stdClass $bind_relation
     */
    private function getBindRelation(array $bind_param)
    {
        $bind_relation = DB::table('coach_user_relation')
            ->select(
                'coach_user_relation.id',
                'coach_user_relation.user_id',
                'coach_user_relation.coach_id',
                'coach_user_relation.bind_status',
                'coach_user_relation.coach_user_id'
                )
            ->leftJoin('coach', 'coach.l_coach_id', '=', 'coach_user_relation.coach_id')
            ->leftJoin('user', 'user.l_user_id', '=', 'coach.user_id')
            ->where([$bind_param])
            ->where([
                ['coach_user_relation.bind_status', '<>', 2],
                ['user.i_user_type', '=', 1], // 教练用户
                ['user.i_status', '=', 0],    // 教练是正常用户
            ])
            ->first();
        return $bind_relation;
    }

    /**
     * app feedback 反馈
     *
     * @param string $content 反馈内容信息
     * @return \Response
     */
    public function feedback()
    {
        if (!$this->request->has('content')) {
            $data = [
                'code' => 400,
                'msg'  => '参数错误',
                'data' => new \stdClass,
            ];
            return response()->json($data);
        }
        $user = (new AuthController())->getUserFromToken($this->request->input('token'));
        $content = $this->request->input('content');
        $user_id = $user['user_id'];
        $user_type = $user['i_user_type'];
        $user_phone = $user['phone'];
        $user_name = $this->getUserNameById($user_id, $user_type, $user_phone);
        $res = DB::table('feedback')
            ->insert([
                'content' => $content,
                'user_id' => $user_id,
                'user_type' => $user_type,
                'name' => $user_name,
                'phone' => $user_phone,
                'addtime' => time()
            ]);
        if ($res) {
            $data = [
                'code' => 200,
                'msg'  => '反馈成功',
                'data' => new \stdClass
            ];
        } else {
            $data = [
                'code' => 400,
                'msg'  => '反馈失败',
                'data' => new \stdClass
            ];
        }
        return response()->json($data);
    }

    // 获取我的考试记录
    public function getMyExamRecords()
    {
        $lesson_id = $this->request->has('lesson_id') ? $this->request->input('lesson_id') : '1';
        $user = (new AuthController())->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $user_info = $this->getUserInfoById($user_id);
        if (!$user_info->exam_license_name) {
            $data = [
                'code' => 200,
                'msg'  => '暂无考试记录',
                'data' => [],
            ];
            return response()->json($data);
        }
        $exam_license_name = $user_info->exam_license_name;
        $page = $this->request->has('page') ? intval($this->request->input('page')) : 1;
        $limit = 10;
        $record_list = DB::table('user_exam_records')
                                ->select(
                                    'score',
                                    'exam_total_time',
                                    'os',
                                    'stype',
                                    'ctype',
                                    'addtime'
                                )
                                ->where(['stype' => $lesson_id, 'ctype' => $exam_license_name, 'user_id' => $user_id])
                                ->orderBy('addtime', 'DESC')
                                ->paginate($limit);
        foreach ($record_list as $key => $value) {
            //    $record_list[$key]->exam_total_time_format = $value->exam_total_time / 60;
            $record_list[$key]->addtime_format = date('Y-m-d H:i', $value->addtime);
        }
        $data = ['code'=>200, 'msg'=>'获取考试记录成功', 'data'=>$record_list];
        return response()->json($data);
    }

    /**
     * 修改密码
     *
     * @param $phone
     * @param $user_type
     * @param $old_pass
     * @param $new_pass
     * @param $repeat_pass
     *
     * @return \Reponse
     * */
    public function changepass()
    {
        if (!$this->request->has('phone')
            || !$this->request->has('old_pass')
            || !$this->request->has('new_pass')
            || !$this->request->has('repeat_pass')
        ) {
            $data = [
                'code' => 400,
                'msg'  => '参数错误',
                'data' => new \stdClass,
            ];
            return response()->json($data);
        }
        $phone = $this->request->input('phone');
        $old_pass = $this->request->input('old_pass');
        $new_pass = $this->request->input('new_pass');
        $repeat_pass = $this->request->input('repeat_pass');
        $user = (new AuthController())->getUserFromToken($this->request->input('token'));
        $user_id = $user['user_id'];
        $_phone = $user['phone'];
        if ($_phone !== $phone) {
            return response()->json([
                'code' => 400,
                'msg'  => '你需要修改密码的号码错误',
                'data' => new \stdClass,
            ]);
        }
        if ($new_pass != $repeat_pass) {
            return response()->json([
                'code' => 400,
                'msg'  => '两次输入密码不一致',
                'data' => new \stdClass,
            ]);
        }

        $res = DB::table('user')
            ->where([
                's_phone' => $phone,
                's_password' => md5($old_pass),
                'l_user_id' => $user_id
            ])
            ->value('l_user_id');
        if (!$res) {
            return response()->json([
                'code' => 400,
                'msg'  => '旧密码错误',
                'data' => new \stdClass,
            ]);
        }

        $_res = DB::table('user')
            ->where(['l_user_id'=>$user_id])
            ->update([
                's_password' => md5($repeat_pass)
            ]);

        if (!$_res) {
            return response()->json([
                'code' => 400,
                'msg'  => '请勿和旧密码相同',
                'data' => new \stdClass,
            ]);
        }
        $pusher = new PusherController('student');
        $pusher->setTarget($user_id)
            ->setContent('【修改密码】您于'.date('m-d H:i').'修改了嘻哈学车账户登录密码，请使用新密码登录。')
            ->setType(1) // 系统消息
            ->setMemberId($user_id)
            ->setMemberType(1)
            ->setBeizhu('修改密码')
            ->setFrom('嘻哈学车')
            ->send();
        return response()->json([
            'code' => 200,
            'msg'  => '修改密码成功',
            'data' => new \stdClass,
        ]);
    }

    /**
     * 忘记密码
     *
     * @param $phone
     * @param $code
     * @param $user_type
     * @param $new_pass
     * @param $repeat_pass
     *
     * @return \Reponse
     * */
    public function forgetpass()
    {
        if (!$this->request->has('phone') || !$this->request->has('user_type') || !$this->request->has('code') || !$this->request->has('new_pass') || !$this->request->has('repeat_pass')) {
            return response()->json([
                'code' => 400,
                'msg'  => '参数错误',
                'data' => new \stdClass,
            ]);
        }
        $phone = $this->request->input('phone');
        $user_type = $this->request->input('user_type');
        $code = $this->request->input('code');
        $new_pass = $this->request->input('new_pass');
        $repeat_pass = $this->request->input('repeat_pass');
        if ($new_pass != $repeat_pass) {
            return response()->json([
                'code' => 400,
                'msg'  => '两次输入密码不相同',
                'data' => new \stdClass,
            ]);
        }

        $res = DB::table('verification_code')
            ->select('id')
            ->where([
                's_phone' => $phone,
                's_code' => $code
            ])
            ->first();
        if (empty($res)) {
            return response()->json([
                'code' => 400,
                'msg'  => '验证码错误',
                'data' => new \stdClass,
            ]);
        }

        $res = DB::table('user')
            ->where([
                's_phone' => $phone,
                'i_user_type' => $user_type,
            ])
            ->update([
                's_password' => md5($repeat_pass)
            ]);
        if ($res) {
            return response()->json([
                'code' => 200,
                'msg'  => '密码修改成功，请重新登录',
                'data' => new \stdClass,
            ]);
        } else {
            return response()->json([
                'code' => 400,
                'msg'  => '请勿与旧密码相同',
                'data' => new \stdClass,
            ]);
        }
    }

    public function qrcode()
    {
        $user = (new AuthController())->getUserFromToken($this->request->input('token'));
        $user_info = $this->getUserInfoById($user['user_id']);
        $type = $this->request->input('type');
        if (empty($type)) {
            $type = 'user';
        }

        try {
            $data_builder = new DataBuilder();
            $qrcode_contents = $data_builder->fromTypeAndData($type, (array)$user_info)->build();
        } catch (Exception $exception) {
            Log::error('api:ucenter/qrcode 构造二维码数据源时发生异常');

            $data = ['code' => 500, 'msg' => 'Err', 'data' => new \stdClass()];
            return response()->json($data);
        }

        $qrcode = QrCode::format('png')
            ->errorCorrection('M')
            ->size(400)
            ->encoding('utf-8')
            ->margin(1)
            ->generate($qrcode_contents);
        return response($qrcode, 200)->header('Content-Type', 'image/png');
    }
}
