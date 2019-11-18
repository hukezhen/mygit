const util = require('../../../../utils/util.js')
Page({

  /**
   * 页面的初始数据
   */
  data: {
    order: {}
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var that = this;
    util.nmAjax({
      method: "get",
      url: getApp().globalData.apiURL + 'v1/order/' + options.id,
      data: {

      },
      success: res => {

        res.data.order_time = util.formatTime(res.data.order_time, 'Y/M/D h:m');
        res.data.pay_time = util.formatTime(res.data.pay_time, 'Y/M/D h:m');
        res.data.price = util.formatPrice(res.data.price);
        if (res.data.car_info != null) {
          res.data.car_info.moneys = util.formatPrice(res.data.car_info.moneys);
        }
        if (res.data.car_activity_info != null && res.data.car_activity_info.length > 0) {
          var list = res.data.car_activity_info;
          for (let j = 0; j < list.length; j++) {
            that.setData({
              activity_id: list[0].id
            });
            if (list[j].moneys > 0)
              list[j].moneys = util.formatPrice(list[j].moneys);

          }
        }
        that.setData({
          order: res.data
        })

      }
    })
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