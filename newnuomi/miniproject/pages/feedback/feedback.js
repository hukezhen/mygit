const util = require('../../utils/util.js')
Page({

  /**
   * 页面的初始数据
   */
  data: {
    messageTypeList: []
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
  },
  fetchdata: function() {
    var that = this;
    util.nmAjax({
      method: "get",
      url: getApp().globalData.apiURL + '/v1/message',
      success: res => {
        var result = res.data.data;

        for (let i = 0; i < result.length; i++) {
          result[i].typename = that.getname(result[i]);
          result[i].img = that.getimg(result[i]);
        }

        that.setData({
          messageTypeList: result
        })
      }
    })
  },
  getimg: function(item) {
    var img = '';
    if (item.mode == 0) {
      img = getApp().globalData.apiURL + item.other_face;
    }
    if (item.mode == 1 || item.mode == 5) {
      img = '../images/icon-xitong.png';
    }
    if (item.mode == 2) {
      img = '../images/icon-xiaofei.png';
    }
    if (item.mode == 3) {
      img = '../images/icon-hongbao.png';
    }
    if (item.mode == 4) {
      img = '../images/icon-jifen.png';
    }
    return img;
  },
  getname: function(item) {
    var typename = '';
    if (item.mode == 0) {
      typename = item.other_nickname;
    }
    if (item.mode == 1) {
      typename = '系统通知';
    }
    if (item.mode == 2) {
      typename = '账户通知';
    }
    if (item.mode == 3) {
      typename = '活动通知';
    }
    if (item.mode == 4) {
      typename = '订单通知';
    }
    if (item.mode == 5) {
      typename = '江湖求救';
    }
    return typename;
  },
  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function() {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function() {
    this.fetchdata();
  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function() {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function() {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function() {
    this.fetchdata();
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function() {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function() {

  }
})