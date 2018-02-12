'use strict';

const envs = require('envs');
const prefix = envs('prefix');
const assert = require('assert');
const qs = require('qs');

var chakram = require('chakram'),
    expect = chakram.expect;

var ok_tests = [
    {args: {car_type: 'car', course: 'kemu1'}, expected: 200},
    {args: {car_type: 'car', course: 'kemu4'}, expected: 200},
    {args: {car_type: 'truck', course: 'kemu1'}, expected: 200},
    {args: {car_type: 'truck', course: 'kemu4'}, expected: 200},
    {args: {car_type: 'bus', course: 'kemu1'}, expected: 200},
    {args: {car_type: 'bus', course: 'kemu4'}, expected: 200},
    {args: {car_type: 'moto', course: 'kemu1'}, expected: 200},
    {args: {car_type: 'moto', course: 'kemu4'}, expected: 200},
    {args: {car_type: 'keyun', course: 'zigezheng'}, expected: 200},
    {args: {car_type: 'huoyun', course: 'zigezheng'}, expected: 200},
    {args: {car_type: 'weixian', course: 'zigezheng'}, expected: 200},
    {args: {car_type: 'jiaolian', course: 'zigezheng'}, expected: 200},
    {args: {car_type: 'chuzu', course: 'zigezheng'}, expected: 200}
];

var fail_tests = [
    {args: {car_type: 'non_exist', course: 'non_exist'}, expected: 400},
    {args: {car_type: 'car', course: 'non_exist'}, expected: 400},
    {args: {car_type: 'non_exist', course: 'kemu1'}, expected: 400},
    {args: {car_type: '', course: ''}, expected: 400}
];

describe('/student/exam/special', function () {
    const api = prefix + '/student/exam/special';
    describe('should be an callable api', function () {
        it('should return status 200 in headers', function () {
            // this.skip();
            var response = chakram.get(api);
            expect(response).to.have.status(200);
            return chakram.wait();
        });
        it('should return correct JSON response', function () {
            // this.skip();
            var response = chakram.get(api);
            expect(response).to.have.header("content-type", "application/json");
            expect(response).to.have.schema({
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
                    }
                },
                "required": ["code", "msg", "data"]
            });
            return chakram.wait();
        });
        it('should not return 404 code in JSON response', function () {
            // this.skip();
            var response = chakram.get(api);
            expect(response).to.not.have.json("code", 404);
            return chakram.wait();
        });
    });

    describe('business here', function () {
        describe('different car_type and course parameters', function () {
            ok_tests.concat(fail_tests).forEach(function (test) {
                var args = test.args,
                    expected = test.expected;
                it('should return ' + expected + ' with args ' + qs.stringify(args), function () {
                    // this.skip();
                    var response = chakram.get(api + '?' + qs.stringify(args));
                    return response.then(function (obj) {
                        expect(obj.body.code).to.be.equal(expected);
                    });
                });
            });
        });
        describe('api result', function () {
            var args = ok_tests[0].args;
            it('should match correct json schema', function () {
                // this.skip();
                var response = chakram.get(api + '?' + qs.stringify(args));
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
                                    "count": {
                                        "type": "number"
                                    },
                                    "list": {
                                        "type": "array",
                                        "items": {
                                            "type": "object",
                                            "properties": {
                                                "tag_id": {
                                                    "title": "标签id",
                                                    "type": "number"
                                                },
                                                "tag": {
                                                    "title": "标签",
                                                    "type": "string"
                                                },
                                                "count": {
                                                    "title": "数目",
                                                    "type": "number"
                                                }
                                            },
                                            "required": ["tag_id", "tag", "count"]
                                        }
                                    }
                                },
                                "required": ["list", "count"]
                            }
                        },
                        "required": ["code", "msg", "data"]
                    });
                });
            });
        });
    });

});
