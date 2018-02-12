// page/xh_component/index.js
var app = require('../../libs/app.js');

Page({
  data:{
    imgUrls: [
        {
            "resource_type": 1, 
            "resource_url": "/image/banner-01.png", 
            "loop_time": 2, 
            "ads_url": "", 
            "title": ""
        }, 
        {
            "resource_type": 1, 
            "resource_url": "/image/banner-02.png", 
            "loop_time": 2, 
            "ads_url": "", 
            "title": ""
        }
    ],
    indicatorDots: true,
    vertical: false,
    autoplay: false,
    interval: 3000,
    duration: 1200,
    circular: true
  },
  onLoad:function(options){
    // 页面初始化 options为页面跳转所带来的参数
    var imgUrl;
    var that = this;
    wx.request({
      url: 'http://localhost/php/api2/dist/public/v1/ads/bannerlist',
      data: {'scene': 102, 'location_type': 2, 'location_id': 340100, 'device': 1},
      method: 'GET', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
      header: {'content-type': 'application/json'}, // 设置请求的 header
      success: function(res){
        // success
        if(res.data.code == 200) {
          imgUrl = res.data.data;
        }
        console.log(res.data.code)
        that.setData({
          imgUrls: imgUrl
        });
      },
      fail: function() {
        // fail
      },
      complete: function() {
        // complete
      }
    });

  },
  onReady:function(){
    // 页面渲染完成
  },
  onShow:function(){
    // 页面显示
  },
  onHide:function(){
    // 页面隐藏
  },
  onUnload:function(){
    // 页面关闭
  }
})