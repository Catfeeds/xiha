<?php

/**
 * RequestController
 *
 * 转发所有的请求
 */

namespace App\Http\Controllers\v3\student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;

class RequestController extends Controller {

    protected $request;

    public function __construct (Request $request)
    {
        $this->request = $request;
    }

    public function options ()
    {
        Log::info('OPTIONS '. $this->request->url());
        return response()->json(['code' => 200, 'msg' => 'options is ok', 'data' => new \stdClass]);
    }

}
