'use strict';

/**
 * 读取消息
 */

const envs = require('envs');
const prefix = envs('prefix');
const assert = require('assert');
const qs = require('qs');
var chakram = require('chakram'),
    expect = chakram.expect;

describe('/student/ucenter/message/read', function () {
    const api = prefix + '/student/ucenter/message/read';

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


    describe('business here', function () {
    	// 1.登录获取token 2.根据token获取学车报告
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
    		var message_id = 1;
            var args = {token:token,message_id:message_id};
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
                            "type": "object"
                        }
                    },
                    "required": ["code", "msg", "data"]
                });
            });
        });
    });
});