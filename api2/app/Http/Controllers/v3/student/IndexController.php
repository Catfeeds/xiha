<?php
/**
 * 首页模块
 *
 * @return void
 * @author
 **/

namespace App\Http\Controllers\v3\student;

use Exception;
use App\Models\v3\student\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexController extends Controller {

    protected $request;

    public function __construct(Request $request) {
        $this->request = $request;
        parent::__construct();
    }

    // 获取城市列表（包含热门城市）
    public function getCityList() {

        $type = $this->request->has('type') ? $this->request->input('type') : 1; // 类别 1:热门城市 2：全部城市
        // DB::enableQueryLog();

        $city_id_list = DB::table('school')->groupBy('city_id')->pluck('city_id');
        // $this->redis->set('city_list', json_encode($city_id_list));
        // return $this->redis->get('city_list');

        if($type == 1) {
            $city_list = DB::table('city')
                ->select('cityid', 'city', 'fatherid', 'leter', 'spelling', 'acronym', 'is_hot')
                ->whereIn('cityid', $city_id_list)
                ->where('is_hot', 1)  // 1 热门 2 非热门
                ->take(9)
                ->get();

        } elseif($type == 2) {
            $city_list = DB::table('city')
                ->select('cityid', 'city', 'fatherid', 'leter', 'spelling', 'acronym', 'is_hot')
                ->whereIn('cityid', $city_id_list)
                ->get();

        } else {
            $city_list = array();
        }
        // print_r(DB::getQueryLog());
        $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>['list'=>$city_list]];
        return response()->json($data);
    }

     /**
     * 获取首页的图标和文字
     * @param   type    nember  客户端类型(1:学员端 | 2:教练端)
     * @return  void
     **/
    public function getIndexIcon () {

        if ( ! $this->request->has('type')) {
            log::error('异常：【获取首页的图标和文字】缺少必须参数');
            return response()->json([
                'code'  => 400,
                'msg'   => '参数错误',
                'data'  => new \stdClass,
            ]);
        }

        $type = $this->request->input('type'); // 1:student | 2:coach

        if ( ! in_array($type, [1, 2])) {

            log::error('异常：【获取首页的图标和文字】客户端类型'.$type.'不在规定的范围内');
            return response()->json([
                'code'  => 400,
                'msg'   => '客户端类型错误',
                'data'  => new \stdClass,
            ]);
        }

        $iconlist = [];
        if ( 1 == $type ) { // student

            $iconlist = [
                [
                    'imgurl' => env('APP_UPLOAD_PATH').'icon/student/signup.png',
                    'text' => '报名须知',
                ],
                [
                    'imgurl' => env('APP_UPLOAD_PATH').'icon/student/flow.png',
                    'text' => '学车流程',
                ],
                [
                    'imgurl' => env('APP_UPLOAD_PATH').'icon/student/extra.png',
                    'text' => '嘻哈号外',
                ],
                [
                    'imgurl' => env('APP_UPLOAD_PATH').'icon/student/comiusse.png',
                    'text' => '常见问题',
                ],
            ];

        } else { // coach

        }

        $data = [
            'code'  => 200,
            'msg'   => '获取成功',
            'data'  => ['list'=>$iconlist],
        ];

        return response()->json($data);

    }

    // 根据城市名称获取城市信息
    public function getCityInfoByName() {
        if (! $this->request->has('name')) {
            $data = [
                'code' => 400,
                'msg'  => '参数错误',
                'data' => '',
            ];
            return response()->json($data);
        }
        $city_name = $this->request->input('name');
        $city_info = DB::table('city')
            ->select('cityid', 'fatherid', 'city')
            ->where('city', 'like', '%'.$city_name.'%')
            ->first();
        if ($city_info) {
            $data = ['code'=>200, 'msg'=>'成功', 'data'=>$city_info];
        } else {
            $data = ['code'=>400, 'msg'=>'未找到该城市信息', 'data'=>new \Stdclass()];
        }
        return response()->json($data);
    }

    // 获取app升级信息
    public function getAppVersionInfo() {

        if(!$this->request->has('device')
            || !$this->request->has('client')
            || !$this->request->has('version')
            ) {
            return response()->json([
                'code'=>400,
                'msg'=>'参数错误',
                'data'=>'',
            ]);
        }

        $os_type = $this->request->input('device'); // 1:安卓 2:ios
        $app_client = $this->request->input('client'); // app客户端id标识 1 学员端 2 教练端 3 校长端
        $version_code = $this->request->input('version'); // app版本标识 苹果：4.0.0 安卓：59
        if($os_type == 2) {
            $version_code = str_replace('.', '', $version_code);
        }
        $version_info = DB::table('app_version')
            ->select(
                'app_name',
                'version',
                'version_code',
                'app_update_log',
                'app_download_url',
                'is_force',
                'force_least_updateversion'
            )
            ->where([
                ['os_type', '=', $os_type],
                ['app_client', '=', $app_client],
                ['version_code', '>', $version_code]
            ])
            ->orderBy('addtime', 'DESC')
            ->first();
        if(!empty($version_info)) {
            $version_info->app_download_url = $version_info->app_download_url ? env('APP_PATH').'management/'.$version_info->app_download_url : '';
            $version_info->app_update_log = $version_info->app_update_log ? $version_info->app_update_log : '例行升级';
            $data = ['code'=>200, 'msg'=>'获取成功', 'data'=>$version_info];
        } else {
            $data = ['code'=>100, 'msg'=>'获取失败', 'data'=>[]];
        }
        return response()->json($data);
    }

    // 获取广告列表
    public function getAdsBannerList() {
        // print_r($this->request);
        if(!$this->request->has('scene') || !$this->request->has('location_type') || !$this->request->has('location_id') || !$this->request->has('device')) {
            return response()->json([
                'code'=>400,
                'msg'=>'参数错误',
                'data'=>'',
            ]);
        }
        $scene          = $this->request->input('scene'); // 场景 101 102 103
        $location_type  = $this->request->input('location_type'); // 1：区域 2：城市 3：省份
        $location_id    = $this->request->input('location_id'); // 定位ID
        $device         = $this->request->input('device'); // 设备 1：ios 2：andriod
        switch ($location_type) {
        case 1:
            $location_name = 'area_id';
            break;
        case 2:
            $location_name = 'city_id';
            break;
        case 3:
            $location_name = 'province_id';
            break;
        default:
            return response()->json(['code'=>400, 'msg'=>'参数错误', 'data'=>'']);
            break;
        }
        //根据location_type, location_id, scene条件从主表cs_ads中查询所有广告订单id
        $ads_info = DB::table('ads')
            ->select(
                'ads.id',
                'ads_info.device'
            )
            ->join('ads_info', 'ads.id', '=', 'ads_info.ads_id')
            ->where([
                ['ads.scene_id', '=', "{$scene}"],
                ["ads.{$location_name}", '=', $location_id],
                ['ads.ads_status', '<>', '3']
            ])
            ->orderBy('ads.addtime', 'DESC')
            ->first();

        // print_r($ads_info);
        // print_r(DB::getQueryLog());

        if(empty($ads_info)) {
            // if($scene == 101) {
            //     $data = ['code'=>104, 'msg'=>'获取首页启动图片失败', 'data'=>[]];
            // } else {
                $data = ['code'=>200, 'msg'=>'获取默认启动图片成功', 'data'=>$this->getDefaultAds($scene)];
            // }
            return response()->json($data);
        }

        $ads_device = explode(',', $ads_info->device) ? explode(',', $ads_info->device) : [];

        if ( empty($ads_device) || !in_array($device, $ads_device) ) {
            return response()->json(['code'=>101, 'msg'=>'支持设备错误', 'data'=>'']);
        }
        $nowts = time();

        $ads_order_list = DB::table('ads_order')
            ->select(
                'resource_type',
                'resource_url',
                'loop_time',
                'ads_url',
                'ads_title'
            )
            ->where([
                ['ads_id', '=', $ads_info->id],
                ['order_status', '=', '1002'], //
                ['over_time', '>', $nowts],
                ['device', '=', $device]
            ])
            ->get();
        // print_r(DB::getQueryLog());
        // print_r($ads_order_list);

        if(empty($ads_order_list->toArray())) {
            return response()->json(['code'=>200, 'msg'=>'获取默认图成功', 'data'=>$this->getDefaultAds($scene)]);
        }

        foreach ($ads_order_list->toArray() as $k => $v) {
            $ads_order_list[$k]->resource_url = env('APP_PATH') . $v->resource_url;
        }

        $data = ['code'=>200, 'msg'=>'获取广告图成功', 'data'=>$ads_order_list];
        return response()->json($data);
    }

    // 获取默认图
    protected function getDefaultAds($scene) {
        // $base_url = 'http://60.173.247.68:8081/api/ads/';
        $base_url = env('APP_PATH').'upload/ads/0/20160412/';
        // $ads_url = 'http://www.xihaxueche.com/m/index';
        $ads_url = '';
        $data = array();
        switch ( $scene ) {
        case 101: // 学员端首页启动图
            $data = array(
                array(
                    'resource_type' => 1, // 1 表示图片 2 表示视频
                    'resource_url'  => $base_url . 'start_ads7.jpg',
                    // 'resource_url'  => $base_url . 'start_ads2.png',
                    'loop_time'     => 5,
                    'ads_url'       => $ads_url,
                    'title'         => '',
                ),
            );
            break;
        case 102: // 学员端首页轮播图
            $data = array(
                array(
                    'resource_type' => 1, // 1 表示图片 2 表示视频
                    'resource_url'  => $base_url . 'banner35.jpg',
                    'loop_time'     => 5,
                    'ads_url'       => $ads_url,
                    'title'         => '',
                ),
                // array(
                //     'resource_type' => 1, // 1 表示图片 2 表示视频
                //     'resource_url'  => $base_url . 'banner33.jpg',
                //     'loop_time'     => 5,
                //     'ads_url'       => '',
                //     'title'         => '',
                // ),
                array(
                    'resource_type' => 1, // 1 表示图片 2 表示视频
                    'resource_url'  => $base_url . 'banner31.jpg',
                    'loop_time'     => 5,
                    'ads_url'       => 'http://m.zuzuche.com/w/tidl/?pnid=E10336549',
                    'title'         => '',
                ),
                array(
                    'resource_type' => 1, // 1 表示图片 2 表示视频
                    'resource_url'  => $base_url . 'yiyuantiyan.png',
                    'loop_time'     => 5,
                    'ads_url'       => $ads_url,
                    'title'         => '',
                ),
                // array(
                //     'resource_type' => 1, // 1 表示图片 2 表示视频
                //     'resource_url'  => $base_url . 'banner29.png',
                //     'loop_time'     => 5,
                //     'ads_url'       => 'https://mp.weixin.qq.com/s?__biz=MzI3MjA2MDAyMw==&mid=2651345811&idx=1&sn=498df68be9d4b51fa17e58536097666b&chksm=f0c4a88bc7b3219dd30a9d5e57d2c352e28ec28efb8381e1ed062f74812dab2a8ce763a39dae#rd',
                //     'title'         => '',
                // ),
                // array(
                //     'resource_type' => 1, // 1 表示图片 2 表示视频
                //     'resource_url'  => $base_url . 'banner30.png',
                //     'loop_time'     => 5,
                //     'ads_url'       => 'https://mp.weixin.qq.com/s?__biz=MzI3MjA2MDAyMw==&mid=2651345811&idx=1&sn=498df68be9d4b51fa17e58536097666b&chksm=f0c4a88bc7b3219dd30a9d5e57d2c352e28ec28efb8381e1ed062f74812dab2a8ce763a39dae#rd',
                //     'title'         => '',
                // ),
                // array(
                //     'resource_type' => 1, // 1 表示图片 2 表示视频
                //     'resource_url'  => $base_url . 'banner25.png',
                //     'loop_time'     => 5,
                //     'ads_url'       => $ads_url,
                //     'title'         => '',
                // ),
                array(
                    'resource_type' => 1, // 1 表示图片 2 表示视频
                    'resource_url'  => $base_url . 'banner28.png',
                    'loop_time'     => 5,
                    'ads_url'       => $ads_url,
                    'title'         => '',
                ),
                // array(
                //     'resource_type' => 1, // 1 表示图片 2 表示视频
                //     'resource_url'  => $base_url . 'banner27.png',
                //     'loop_time'     => 5,
                //     'ads_url'       => $ads_url,
                //     'title'         => '',
                // ),
            );
            break;
        case 103:
            $data = array(
                array(
                    'resource_type' => 1, // 1 表示图片 2 表示视频
                    'resource_url'  => $base_url . 'question2.png',
                    'loop_time'     => 2,
                    'ads_url'       => $ads_url,
                    'title'         => '',
                ),
            );
            break;
        case 104:
            $data = array(
                array(
                    'resource_type' => 1, // 1 表示图片 2 表示视频
                    'resource_url'  => $base_url . 'question1.png',
                    'loop_time'     => 2,
                    'ads_url'       => $ads_url,
                    'title'         => '',
                ),
            );
            break;
        default :
            $data = array(
                array(
                    'resource_type' => 1, // 1 表示图片 2 表示视频
                    'resource_url'  => $base_url . 'banner24.jpg',
                    'loop_time'     => 2,
                    'ads_url'       => $ads_url,
                    'title'         => '',
                ),
            );
            break;
        }
        return $data;

    }

}

?>
