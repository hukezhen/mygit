
const util = require('../../../../utils/util.js')

Page({

  /**
   * 页面的初始数据
   */
  data: {
    selectedgoods:{},
    carId:0,
    cls_name:'',
    numberclass:''
  },
  delselected:function(){
    this.setData({
      selectedgoods: {}
    })
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.setData({
      carId:Number(options.cid),
      cls_name: options.cls_name,
      number: options.number,
      numberclass: util.getNumberColor(options.cls_name)
    })
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {

  },
  submitorder:function(){
    var that = this;

    if(this.data.carId == 0)
    {
      wx.showModal({
        title: '提示',
        content: '请选择车辆',
        showCancel: false,
        success: function (res) { }
      })
      return;
    }
    if (!this.data.selectedgoods.id) {
      wx.showModal({
        title: '提示',
        content: '请选择产品',
        showCancel: false,
        success: function (res) { }
      })
      return;
    }

    util.nmAjax({
      method: "post",
      url: getApp().globalData.apiURL + 'v1/order',
      isshowToast: true,
      data: {
        cid:that.data.carId,
        gid:that.data.selectedgoods.gid
      },
      success: res => {
       
        wx.showModal({
          title: '提示',
          content: '提交成功！',
          showCancel: false,
          success: function () {
            wx.navigateTo({
              url: '../complete/complete?id=' + res.data.id + '&cls_name=' + that.data.cls_name + '&number=' + that.data.number + '&s_title=' + that.data.selectedgoods.title
            })
          }
        })
        
      }
    })
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