'use strict';

/**
 * 个人中心首页
 */

const envs = require('envs');
const prefix = envs('prefix');
const assert = require('assert');
const qs = require('qs');

var chakram = require('chakram'),
    expect = chakram.expect;

describe('/student/ucenter/my/index', function () {
    const api = prefix + '/student/ucenter/my/index';

    //  测试是否为正常的接口
    describe('should be an callable api', function () {
        it('should return status 200 in headers', function () {
            var response = chakram.post(api);
            expect(response).to.have.status(200);
            return chakram.wait();
        });

        it('should return correct JSON response', function () {
            var response = chakram.post(api);
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
            var response = chakram.post(api);
            expect(response).to.not.have.json("code", 404);
            return chakram.wait();
        });
    });

    //  测试返回数据
    describe('business here', function () {
        // 1.登录获取token 2.根据token获取个人信息
        var loginApi = prefix + '/student/ucenter/login';
        var token = "";
        before('request token first', function () {
            var loginArgs = {phone:'18656999023',pass:'123456'};
            var response = chakram.post(loginApi, loginArgs);
            return response.then(function (obj) {
               token = obj.body.data.token;
            });
        });

        it('login successful access a token should not be an empty string', function () {
            expect(token).to.be.an('string').that.is.not.empty;
        });

        it('should match correct json schema', function () {
            var response = chakram.get(api + '?&token=' + token);
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
                                "my": {
                                    "type": "object",
                                    "properties": {
                                        "primary":{
                                            "title": "用户基础信息",
                                            "type": "object",
                                            "properties": {
                                                "user_id": {
                                                    "title": "用户ID",
                                                    "type": "number",
                                                },
                                                "user_name": {
                                                    "title": "用户名",
                                                    "type": "string"
                                                },
                                                "real_name": {
                                                    "title": "真实姓名",
                                                    "type": "string"
                                                },
                                                "phone": {
                                                    "title": "手机号码",
                                                    "type": "string"
                                                },
                                                "photo_id": {
                                                    "title": "头像id",
                                                    "type": "number"
                                                },
                                                "sex": {
                                                    "title": "性别",
                                                    "type": "number"
                                                },
                                                "age": {
                                                    "title": "年龄",
                                                    "type": "number"
                                                },
                                                "school_id": {
                                                    "title": "驾校id",
                                                    "type": "number"
                                                },
                                                "license_id": {
                                                    "title": "牌照id",
                                                    "type": "number"
                                                },
                                                "license_name": {
                                                    "title": "牌照名称",
                                                    "type": "string"
                                                },
                                                "exam_license_name": {
                                                    "title": "学员选取的做题类型 C1， A2等",
                                                    "type": "string"
                                                },
                                                "lesson_id": {
                                                    "title": "科目id",
                                                    "type": "number"
                                                },
                                                "lesson_name": {
                                                    "title": "科目名称",
                                                    "type": "string"
                                                },
                                                "area_id": {
                                                    "title": "区县id",
                                                    "type": "number"
                                                },
                                                "city_id": {
                                                    "title": "城市id",
                                                    "type": "number"
                                                },
                                                "province_id": {
                                                    "title": "省份id",
                                                    "type": "number"
                                                },
                                                "learn_car_status": {
                                                    "title": "科目学习中",
                                                    "type": "string"
                                                },
                                                "license_num": {
                                                    "title": "几次领证",
                                                    "type": "number"
                                                },
                                                "identity_id": {
                                                    "title": "身份证号码",
                                                    "type": "string"
                                                },
                                                "address": {
                                                    "title": "住址",
                                                    "type": "string"
                                                },
                                            },
                                            "require":["user_id","user_name","real_name","phone","photo_id","user_photo","sex","age","school_id","license_id","license_name","exam_license_name","lesson_id","lesson_name","area_id","city_id","province_id","learn_car_status","license_num","identity_id","address"]
                                        },
                                        "coupon":{
                                            "title": "优惠券",
                                            "type": "object",
                                            "propreties":{
                                                "total":{
                                                    "title":"优惠券数量",
                                                    "type":"number",
                                                }
                                            },
                                            "require":["total"]
                                        },
                                        "my_coach":{
                                            "title": "教练绑定信息",
                                            "type": "object",
                                            "properties":{
                                                "coach_id":{
                                                    "title":"教练id",
                                                    "type":"number"
                                                },
                                                "bind_status":{
                                                    "title":"绑定",
                                                    "type":"object",
                                                    "properties":{
                                                        "code":{
                                                            "title":"状态码",
                                                            "type":"number"
                                                        },
                                                        "text":{
                                                            "title":"操作结果提示",
                                                            "type":"string"
                                                        }
                                                    }
                                                },
                                                "coach_name":{
                                                    "title":"教练名称",
                                                    "type":"string"
                                                },
                                                "coach_phone":{
                                                    "title":"教练手机号",
                                                    "type":"string"
                                                },
                                                "must_bind":{
                                                    "title":"是否必须绑定才能预约",
                                                    "type":"number"
                                                }
                                            },
                                        },
                                        "message":{
                                            "title": "优惠券",
                                            "type": "object",
                                            "propreties":{
                                                "unread":{
                                                    "title":"未读消息",
                                                    "type":"number"
                                                }
                                            },
                                            "required":["unread"]
                                        },
                                        "school_info":{
                                            "title":"驾校信息",
                                            "type":"object",
                                            "properties":{
                                                "signup_school_id":{
                                                    "title":"驾校id",
                                                    "type":"number"
                                                },
                                                "signup_school_name":{
                                                    "title":"驾校名称",
                                                    "type":"string"
                                                },
                                                "signup_school_count":{
                                                    "title":"报名驾校的数量",
                                                    "type":"number"
                                                },
                                                "signup_status":{
                                                    "title":"状态",
                                                    "type":"number"
                                                }
                                            },
                                            "required":["signup_school_id","signup_school_name","signup_school_count","signup_status"]
                                        }
                                    },
                                    "required":["primary","coupon","my_coach","message","school_info"]
                                },

                            },
                            "required": ["my"]
                        }
                    },
                    "required": ["code", "msg", "data"]
                });
            });
        });
    });
});