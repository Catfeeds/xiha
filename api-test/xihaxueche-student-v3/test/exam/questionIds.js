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

describe('/student/exam/questionIds', function () {

    const api = prefix + '/student/exam/questionIds';

    var args = {car_type: 'car', course: 'kemu1'};

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
        it('should return code 400 without car_type and course', function () {
            // this.skip();
            var response = chakram.get(api);
            expect(response).to.have.json('code', 400);
            return chakram.wait();
        });

        ok_tests.concat(fail_tests).forEach(function (test) {
            it('should return code ' + test.expected + ' with args ' + qs.stringify(test.args), function () {
                // this.skip();
                var response = chakram.get(api + '?' + qs.stringify(test.args));
                return response.then(function (obj) {
                    expect(obj.body.code).to.be.equal(test.expected);
                });
            });
        });

        ok_tests.forEach(function (test) {
            var args = test.args;
            it('should match correct json schema with args ' + qs.stringify(args), function () {
                // this.skip();
                var response = chakram.get(api + '?' + qs.stringify(args));
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
                            "properties": {
                                "count": {
                                    "title": "题目id的总数",
                                    "type": "number"
                                },
                                "list": {
                                    "type": "array",
                                    "items": {
                                        "title": "题目id",
                                        "type": "number"
                                    }
                                }
                            },
                            "required": ["count", "list"]
                        }
                    },
                    "required": ["code", "msg", "data"]
                });
                return chakram.wait();
            });
        });

        ok_tests.forEach(function (test) {
            it('should work upon \'limit\' arg and args ' + qs.stringify(test.args), function () {
                // this.skip();
                var args = test.args;
                var limit = 10;
                args.limit = limit;
                var response = chakram.get(api + '?' + qs.stringify(args));
                return response.then(function (obj) {
                    expect(obj.body.data.count).to.be.equal(limit);
                    expect(obj.body.data.list).to.have.lengthOf(limit);
                });
            });
        });

        it('should work upon \'chapter_id\' arg and args', function () {
            this.skip();
        });

        it('should work upon \'tag_id\' arg and args', function () {
            this.skip();
        });

    });

});
