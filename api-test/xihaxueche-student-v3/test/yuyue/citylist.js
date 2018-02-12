'use strict';

/**
 * 我的报名(报名班制订单)
 */

const envs = require('envs');
const prefix = envs('prefix');
const assert = require('assert');
const qs = require('qs');
var chakram = require('chakram'),
    expect = chakram.expect;

describe('/student/citylist', function () {
    const api = prefix + '/student/citylist';

    //  测试是否为正常的接口
    describe('should be an callable api', function () {
        it('should return status 200 in headers', function () {
            var response = chakram.get(api);
            expect(response).to.have.status(200);
            return chakram.wait();
        });

        it('should return correct JSON response', function () {
            var response = chakram.get(api);
            expect(response).to.have.header("content-type", "application/json");
            expect(response).to.have.schema({
                "type": "object",
                "properties": {  //此关键字确定子实例如何验证对象，并且不直接验证直接实例本身。
                    "code": {
                        "title": "状态码",
                        "type": "number"
                    },
                    "msg": {
                        "title": "提示信息",
                        "type": "string"
                    },
                    "data": {
                        "title": "数据对象",
                        "type": "object",
                    }
                },
                "required": ["code", "msg", "data"]
            });
            return chakram.wait();
        });

        it('should not return 404 code in JSON response', function () {
            var response = chakram.get(api);
            expect(response).to.not.have.json("code", 404);
            return chakram.wait();
        });
    });


    // describe('business here', function () {
    // 	// 1.登录获取token 2.根据token获取学车报告
    //     var loginApi = prefix + '/student/ucenter/login';
    //     var token = "";
    //     before('request token first', function () {
    //         var loginArgs = {phone:'18656999023',pass:'123456'};
    //         var response = chakram.post(loginApi, loginArgs);
    //         return response.then(function (obj) {
    //            token = obj.body.data.token;
    //         });
    //     });

    //     it('login successful access a token should not be an empty string', function () {
    //         expect(token).to.be.an('string').that.is.not.empty;
    //     });

    // 	it('should match correct json schema', function () {
    // 		var type = 0;
    // 		var page = 1;
    //         var response = chakram.get(api + '?&token=' + token + '&type=' + type + '&page=' + page);
    //         return response.then(function (obj) {
    //         	expect(obj).to.have.schema({
    //                 "type": "object",
    //                 "properties": {
    //                     "code": {
    //                         "title": "状态码",
    //                         "type": "number"
    //                     },
    //                     "msg": {
    //                         "title": "提示信息",
    //                         "type": "string"
    //                     },
    //                     "data": {
    //                         "title": "数据对象",
    //                         "type": "object",
    //                         "properties": {
    //                         	"list":{
    //                         		"title":"订单列表",
    //                         		"type":"object",
    //                         		"properties":{
    //                         			"total":{
    //                         				"type": "number"
    //                         			},
    //                         			"per_page":{
    //                         				"type": "number"
    //                         			},
    //                         			"current_page":{
    //                         				"type": "number"
    //                         			},
    //                         			"last_page":{
    //                         				"type": "number"
    //                         			},

    //                         			"from":{
    //                         				"type": "number"
    //                         			},
    //                         			"to":{
    //                         				"type": "number"
    //                         			},
    //                         			"data":{
    //                         				"type":"array",
    //                         				"items": {
		  //                                       "type": "object",
	   //                                          "properties":{
	   //                                              "order_id":{
	   //                                              	"type": "number"
	   //                                              },
	   //                                              "order_no":{
	   //                                              	"type": "string"
	   //                                              },
	   //                                              "addtime":{
	   //                                              	"type": "number"
	   //                                              },
	   //                                              "pay_time":{
	   //                                              	"type": "string"
	   //                                              },
	   //                                              "money":{
	   //                                              	"type": "string"
	   //                                              },
	   //                                              "pay_type":{
	   //                                              	"type": "number"
	   //                                              },
	   //                                              "transaction_no":{
	   //                                              	"type": "string"
	   //                                              },
	   //                                              "order_status":{
	   //                                              	"type": "number"
	   //                                              },
	   //                                              "user_id":{
	   //                                              	"type": "number"
	   //                                              },
	   //                                              "user_name":{
	   //                                              	"type": "string"
	   //                                              },
	   //                                              "user_phone":{
	   //                                              	"type": "string"
	   //                                              },
	   //                                              "coach_id":{
	   //                                              	"type": "number"
	   //                                              },
	   //                                              "coach_name":{
	   //                                              	"type": "string"
	   //                                              },
	   //                                              "coach_phone":{
	   //                                              	"type": "string"
	   //                                              },
	   //                                              "license_name":{
	   //                                              	"type": "string"
	   //                                              },
	   //                                              "lesson_name":{
	   //                                              	"type": "string"
	   //                                              },
	   //                                              "appoint_date":{
	   //                                              	"type": "string"
	   //                                              },
	   //                                              "coach_imgurl":{
	   //                                              	"type": "string"
	   //                                              },
	   //                                              "school_name":{
	   //                                              	"type": "string"
	   //                                              },
	   //                                              "addtime_format":{
	   //                                              	"type": "string"
	   //                                              },
	   //                                              "pay_type_name":{
	   //                                              	"type": "string"
	   //                                              },
	   //                                              "order_status_name":{
	   //                                              	"type": "string"
	   //                                              },
	   //                                              "expire_time_list":{
	   //                                              	"type": "number"
	   //                                              }
	   //                                          },
	   //                                          "required":["order_id","order_no","pay_time","pay_type","order_status"]
		  //                                   }
    //                         			}
    //                         		}
    //                         	}
    //                         }
    //                     }
    //                 },
    //                 "required": ["code", "msg", "data"]
    //             });
    //         });
    //     });
    // });
});