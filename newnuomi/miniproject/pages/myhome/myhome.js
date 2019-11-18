const util = require('../../utils/util.js')
Page({

  /**
   * 页面的初始数据
   */
  data: {
    storeInfo: {},
    img: {},
    isshowai: false,
    aitypes: [
      { id: 1, name: '按月发' },
      { id: 2, name: '按季度发' },
      { id: 3, name: '按特定群体发' }],
    currentaiindex: 0
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {

  },
  fetchData: function (e) {
    var that = this;
    util.nmAjax({
      method: "get",
      url: getApp().globalData.apiURL + 'v1/store/' + getApp().globalData.storeId,
      data: {

      },
      success: res => {

        that.setData({
          storeInfo: res.data,
          img: getApp().globalData.apiURL + res.data.top_img
        })

      }
    })
  },
  switchChange: function (e) {
    this.setData({
      isshowai: e.detail.value
    });

  }, selai: function (event) {
    this.setData({
      currentaiindex: event.currentTarget.dataset.index
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
    this.onPullDownRefresh()
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
    getApp().userLogin(res => {
      this.fetchData();
    });
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {

  },

})