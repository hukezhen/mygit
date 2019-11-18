// pages/mine/pages/myprize/myprize.js

const util = require('../../../../utils/util.js')
var page = 1,
  last_page = 1

Page({

  /**
   * 页面的初始数据
   */
  data: {
    prefix: getApp().globalData.apiURL,
    list: []
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.onPullDownRefresh()

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
    page = 1
    this.network();
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
    if (last_page > page) {
      page++
      this.network()
    }
  },

  network() {
    var that = this
    util.nmAjax({
      method: "get",
      url: getApp().globalData.apiURL + 'v1/turn/mine',
      success: res => {
        var res = res.data;
        last_page = res.last_page
        var list = this.data.list

        for (var i in res.data) {
          var item = res.data[i]
          item.time = util.formatData(item.create_time)
        }

        if (page == 1) {
          // 刷新
          list = res.data
        } else {
          // 加载
          list = list.concat(res.data)
        }
        that.setData({
          list: list
        })
      }
    });
  }
})