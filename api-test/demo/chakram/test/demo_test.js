var chakram = require('chakram'),
    expect = chakram.expect;

// 测试用例
describe('api: /v1/citylist', function () {

    // 访问正确的接口，应该返回200状态码
    it('should return 200 status code upon the correct api url', function () {
        var response = chakram.get('http://60.173.247.68:50001/php/api2/dist/public/v1/citylist');
        expect(response).to.have.status(200);

        return chakram.wait();
    });
});
