'use strict';

/**
 * 获取短信验证码
 */

const envs = require('envs');
const prefix = envs('prefix');
const assert = require('assert');
const qs = require('qs');

var chakram = require('chakram'),
    expect = chakram.expect;
// 验证码用途-注册
describe('/student/ucenter/smscode/student/reg', function () {
    const api = prefix + '/student/ucenter/smscode/student/reg';

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
    describe('business here', function () { console.log(signApi);
        //1.根据phone,timestamp,sign获取安全验证md5sign  2.根据phone,timestamp,md5sign请求发送验证码
        var signApi = prefix + '/student/demo/apisafety';
        var md5sign = "";
        var timestamp = Date.parse( new Date()) / 1000;
        var phone = '18656999023';
        var sign = 'e30e2a';
        before('request apiSafety first', function () {
            var response = chakram.get(signApi + '?' + 'phone=' + phone + '&timestamp=' + timestamp + '&sign=' + sign);
            return response.then(function (obj) {
               md5sign = obj.body.data.sign;
            });
        });

        it('md5sign should not be an empty string', function () {
            expect(md5sign).to.be.an('string').that.is.not.empty;
        });

        // 返回json字典校验
        it('should match correct json schema', function () {
            var response = chakram.get(api + '?' + 'phone=' + phone + '&timestamp=' + timestamp + '&sign=' + md5sign);
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
                            "properties": {}
                        }
                    },
                    "required": ["code", "msg", "data"]
                });
            });
        });
    });
});

describe('/student/ucenter/smscode/student/forgetpass', function () {
    const api = prefix + '/student/ucenter/smscode/student/forgetpass';

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
    describe('business here', function () { console.log(signApi);
        //1.根据phone,timestamp,sign获取安全验证md5sign  2.根据phone,timestamp,md5sign请求发送验证码
        var signApi = prefix + '/student/demo/apisafety';
        var md5sign = "";
        var timestamp = Date.parse( new Date()) / 1000;
        var phone = '18656999023';
        var sign = 'e30e2a';
        before('request apiSafety first', function () {
            var response = chakram.get(signApi + '?' + 'phone=' + phone + '&timestamp=' + timestamp + '&sign=' + sign);
            return response.then(function (obj) {
               md5sign = obj.body.data.sign;
            });
        });

        it('md5sign should not be an empty string', function () {
            expect(md5sign).to.be.an('string').that.is.not.empty;
        });

        // 返回json字典校验
        it('should match correct json schema', function () {
            var response = chakram.get(api + '?' + 'phone=' + phone + '&timestamp=' + timestamp + '&sign=' + md5sign);
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
                            "properties": {}
                        }
                    },
                    "required": ["code", "msg", "data"]
                });
            });
        });
    });
});