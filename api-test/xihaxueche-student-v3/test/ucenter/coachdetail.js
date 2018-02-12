'use strict';

/**
 * 获取教练详情
 */

const envs = require('envs');
const prefix = envs('prefix');
const assert = require('assert');
const qs = require('qs');
var chakram = require('chakram'),
    expect = chakram.expect;

describe('/student/coach/detail', function () {
    const api = prefix + '/student/coach/detail';

    //  测试是否为正常的接口
    describe('should be an callable api', function () {
        it('should return status 200 in headers', function () {
            var response = chakram.get(api);
            expect(response).to.have.status(200);
            return chakram.wait();
        });

        it('should return correct JSON response', function () {
            var response = chakram.get(api);
            console.log(api)
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
    		var id = 11333;
    		var lng = 117.231221;
            var lat = 31.323333;
            var device = 3;

            var response = chakram.get(api + '?&id=' + id
                + '&lng=' + lng
                + '&lat=' + lat
                + '&device=' + device);
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
                            "properties":{
                                "detail":{
                                    "type": "object",
                                    "properties":{
                                        "school_name":{
                                            "type": "string"
                                        },
                                        "school_address":{
                                            "type": "string"
                                        },
                                        "school_phone":{
                                            "type": "string"
                                        },
                                        "location_x":{
                                            "type": "string"
                                        },
                                        "location_y":{
                                            "type": "string"
                                        },
                                        "school_id":{
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
                                        "certification_status":{
                                            "type": "number"
                                        },
                                        "timetraining_supported":{
                                            "type": "number"
                                        },
                                        "timetraining_min_price":{
                                            "type": "string"
                                        },
                                        "coach_star":{
                                            "type": "number"
                                        },
                                        "must_bind":{
                                            "type": "number"
                                        },
                                        "average_license_time":{
                                            "type": "string"
                                        },
                                        "lesson2_pass_rate":{
                                            "type": "string"
                                        },
                                        "lesson3_pass_rate":{
                                            "type": "string"
                                        },
                                        "coupon_supported":{
                                            "type": "number"
                                        },
                                        "is_elecoach":{
                                            "type": "number"
                                        },
                                        "tl_train_address":{
                                            "type": "string"
                                        },
                                        "tl_phone":{
                                            "type": "string"
                                        },
                                        "tl_location_x":{
                                            "type": "string"
                                        },
                                        "tl_location_x":{
                                            "type": "string"
                                        },
                                        "min_distance":{
                                            "type": "number"
                                        },
                                        "distance_unit":{
                                            "type": "string"
                                        },
                                        "coupon_id":{
                                            "type": "integer"
                                        },
                                        "coupon_name":{
                                            "type": "string"
                                        },
                                        "coupon_value":{
                                            "type": "string"
                                        },
                                        "is_open":{
                                            "type": "integer"
                                        },
                                        "coupon_url":{
                                            "type": "string"
                                        },
                                        "shifts_list":{
                                            "type": "array",
                                            "items":{
                                                "type": "object",
                                                "properties":{
                                                    "id": {
                                                        "type": "integer",
                                                    },
                                                    "sh_title": {
                                                        "type": "string",
                                                    },
                                                    "sh_money": {
                                                        "type": "string",
                                                    },
                                                    "sh_school_id": {
                                                        "type": "integer",
                                                    },
                                                    "sh_original_money": {
                                                        "type": "string",
                                                    },
                                                    "sh_tag": {
                                                        "type": "string",
                                                    },
                                                    "sh_type": {
                                                        "type": "integer",
                                                    },
                                                    "sh_description": {
                                                        "type": "string",
                                                    },
                                                    "is_promote": {
                                                        "type": "integer",
                                                    },
                                                    "sh_license_id": {
                                                        "type": "integer",
                                                    },
                                                    "sh_license_name": {
                                                        "type": "string",
                                                    },
                                                    "sh_category": {
                                                        "type": "integer",
                                                    },
                                                }
                                            }
                                        },
                                        "is_shifts_more":{
                                            "type": "integer"
                                        },
                                        "is_comment_more":{
                                            "type": "integer"
                                        },
                                        "comment_list": {
                                            "type": "array",
                                            "items":{
                                                "type": "object",
                                                "properties": {
                                                    "coach_star":{
                                                        "type": "string"
                                                    },
                                                    "coach_content":{
                                                        "type": "string"
                                                    },
                                                    "user_id":{
                                                        "type": "integer"
                                                    },
                                                    "addtime":{
                                                        "type": "string"
                                                    },
                                                    "user_name":{
                                                        "type": "string"
                                                    },
                                                    "photo_id":{
                                                        "type": "ingeter"
                                                    },
                                                    "user_photo":{
                                                        "type": "string"
                                                    }
                                                }
                                            }
                                        },
                                        "elecoach_list": {
                                            "type": "object",
                                            "properties":{
                                                "headers": {
                                                    "type": "object"
                                                },
                                                "exception": {
                                                    "type": "null"
                                                }
                                            }
                                        }
                                    },
                                    "required":["school_name","school_address","school_phone","location_x","location_y","school_id","coach_name","coach_phone","coach_imgurl","certification_status","timetraining_supported","timetraining_min_price","coach_star","must_bind","average_license_time","lesson2_pass_rate","lesson3_pass_rate","coupon_supported","is_elecoach","tl_train_address","tl_phone","tl_location_x","tl_location_y","min_distance","distance_unit","coupon_id","coupon_name","coupon_value","is_open","coupon_url","is_shifts_more","is_comment_more","elecoach_list","comment_list","shifts_list"]
                                }
                            },
                            "required":["detail"]
                        }
                    },
                    "required": ["code", "msg", "data"]
                });
            });
        });
    });
});