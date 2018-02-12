'use strict';

/**
 * 获取、搜索驾校/教练列表
 */

const envs = require('envs');
const prefix = envs('prefix');
const assert = require('assert');
const qs = require('qs');
var chakram = require('chakram'),
    expect = chakram.expect;

describe('/student/coach/index', function () {
    const api = prefix + '/student/coach/index';

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


    describe('business here', function () {

    	it('should match correct json schema', function () {
    		var city_id = 0;
    		var school_id = 1;
            var order = 0;
            var keyword = '';
            var assign_keyword = '';
            var license_id = '';
            var price_range = '';
            var page = 1;
            var response = chakram.get(api + '?&city_id=' + city_id
                + '&school_id=' + school_id
                + '&order=' + order
                + '&keyword=' + keyword
                + '&assign_keyword=' + assign_keyword
                + '&license_id=' + license_id
                + '&price_range=' + price_range
                + '&page=' + page);
            return response.then(function (obj) {
            	expect(obj).to.have.schema({
                    "type": "object",
                    "properties": {
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
                            "properties": {
                            	"list":{
                            		"title":"订单列表",
                            		"type":"object",
                            		"properties":{
                            			"data":{
                            				"type":"array",
                            				"items": {
		                                        "type": "object",
	                                            "properties":{
	                                                "coach_id":{
	                                                	"type": "number"
	                                                },
                                                    "coach_name":{
                                                        "type": "string"
                                                    },
                                                    "coach_phone":{
                                                        "type": "string"
                                                    },
                                                    "coach_imgurl":{
                                                        "type": "string"
                                                    },
                                                    "teach_age":{
                                                        "type": "number"
                                                    },
                                                    "coach_sex":{
                                                        "type": "number"
                                                    },
                                                    "license_id":{
                                                        "type": "string"
                                                    },
                                                    "coach_star":{
                                                        "type": "number"
                                                    },
                                                    "i_type":{
                                                        "type": "number"
                                                    },
                                                    "must_bind":{
                                                        "type": "number"
                                                    },
                                                    "shift_max_price":{
                                                        "type": "number"
                                                    },
                                                    "timetraining_supported":{
                                                        "type": "number"
                                                    },
                                                    "coupon_supported":{
                                                        "type": "number"
                                                    },
                                                    "timetraining_min_price":{
                                                        "type": "string"
                                                    },
                                                    "is_elecoach":{
                                                        "type": "number"
                                                    },
                                                    "school_id":{
                                                        "type": "number"
                                                    },
                                                    "school_name":{
                                                        "type": "string"
                                                    },
                                                    "certified":{
                                                        "type": "number"
                                                    },
                                                    "signup_num":{
                                                        "type": "number"
                                                    }
	                                            },
	                                            "required":["coach_id","coach_name","coach_phone","coach_imgurl","teach_age","coach_sex","license_id","coach_star","i_type","must_bind","shift_min_price","shift_max_price","timetraining_supported","coupon_supported","timetraining_min_price","is_elecoach","school_id","school_name","certified","signup_num"]
		                                    }
                            			}
                            		}
                            	},
                                "required":['list']
                            }
                        }
                    },
                    "required": ["code", "msg", "data"]
                });
            });
        });
    });
});