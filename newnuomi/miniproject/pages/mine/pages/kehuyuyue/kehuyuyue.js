const util = require('../../../../utils/util.js')
Page({

  /**
   * 页面的初始数据
   */
  data: {
    appointment:{}
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.init();
  },
  init:function(){
    var that = this;
    util.nmAjax({
      method: "get",
      url: getApp().globalData.apiURL + 'v1/store_reserve',
      data: {

      },
      success: res => {
        var result = res.data;
        if (result.start_time){
          result.start_time = util.formatHourMiniuts(result.start_time);
          result.end_time = util.formatHourMiniuts(result.end_time);
          result.promise = util.formatPrice(result.promise);
          that.setData({
            appointment: result
          })
        }else{
          that.setData({
            'appointment.start_time': "00:00",
            'appointment.end_time':"24:00"
          })
          
        }
        
      }
    });
  },
  reset:function(){
    this.init();
  },
  starttimeChange: function (e) {
    this.setData({
      'appointment.start_time': e.detail.value
    })
  },
  minus: function () {
    this.data.appointment.promise = (this.data.appointment.promise || 0) - 1
    this.setData({
      appointment: this.data.appointment
    })
  },
  add: function () {
    this.data.appointment.promise = (this.data.appointment.promise || 0) + 1
    this.setData({
      appointment: this.data.appointment
    })
  },
  endtimeChange: function (e) {
    this.setData({
      'appointment.end_time': e.detail.value
    })
  },
  numberChange: function (e) {
    this.setData({
      'appointment.number': e.detail.value
    })
  },
  remindtimeChange: function (e) {
    this.setData({
      'appointment.remind_time': e.detail.value
    })
  },
  promiseChange: function (e) {
    this.setData({
      'appointment.promise': e.detail.value
    })
  },
  save:function(e){
    this.data.appointment.promise = this.data.appointment.promise * 100;
    

    if (this.data.appointment.id > 0){
      util.nmAjax({
        method: "put",
        url: getApp().globalData.apiURL + 'v1/store_reserve/' + this.data.appointment.id,
        data: this.data.appointment,
        isshowToast: true,
        success: res => {
          wx.navigateBack({
            delta: 1
          })

        }
      });
    }else{
      util.nmAjax({
        method: "post",
        url: getApp().globalData.apiURL + 'v1/store_reserve',
        data: this.data.appointment,
        isshowToast: true,
        success: res => {
          wx.navigateBack({
            delta: 1
          })

        }
      });
    }
    
  },
  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {

  }
})