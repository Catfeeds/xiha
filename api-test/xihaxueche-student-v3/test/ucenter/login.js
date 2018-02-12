'use strict';

/**
 * 手机密码登录
 */

const envs = require('envs');
const prefix = envs('prefix');
const assert = require('assert');
const qs = require('qs');

var chakram = require('chakram'),
    expect = chakram.expect;

describe('/student/ucenter/login', function () {
    const api = prefix + '/student/ucenter/login';

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

        // 返回json字典校验
        it('should match correct json schema', function () {
            var args = {phone:'18656999023',  pass: '123456'};
            var response = chakram.post(api, args);
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
                                "token": {
                                    "title": "登录标识",
                                    "type": "string"
                                },
                                "expires_in": {
                                    "title": "时间戳",
                                    "type": "number"
                                },
                                "user_id": {
                                    "title": "用户ID",
                                    "type": "number"
                                },
                                "phone": {
                                    "title": "用户手机号",
                                    "type": "string"
                                }
                            },
                            "required": ["token", "expires_in", "user_id", "phone"]
                        }
                    },
                    "required": ["code", "msg", "data"]
                });
            });
        });
    });
});