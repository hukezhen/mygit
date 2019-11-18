const util = require('../../../../utils/util.js')
Page({

  /**
   * 页面的初始数据
   */
  data: {
    storeInfo: {},
    leisure:{},
    typearr: [],
    typeindex: 0,
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.init();
  },
  reset:function(){
    this.init();
  },
  init: function () {
    var that = this;
    this.setData({
      'leisure.start_time': '00:00',
      'leisure.end_time': '24:00',
      'leisure.price': 1,
      'leisure.number': 1
    })
    util.nmAjax({
      method: "get",
      url: getApp().globalData.apiURL + 'v1/store/' + getApp().globalData.storeId,
      data: {

      },
      success: res => {
        res.data.top_img = getApp().globalData.apiURL + res.data.top_img
        that.setData({
          storeInfo: res.data
        })

      }
    });
    var timestamp = Date.parse(new Date());
    timestamp = timestamp / 1000;
    wx.request({
      method: "get",
      url: getApp().globalData.apiURL + 'v1/store_leisure_wash',
      header: {
        time: timestamp,
        random: 'SRzJxpspPYr8af5hTxFs5jMFXKxckix6',
        sign: 'M2EwMzNiYTdhYTNiZGFiZWZkOTgxMzE3ZmI5ZTVmMmQ5NGQ4ZDg3MzFmMDA3MWNhYWRhN2ZmZWEzZTdkNmUzZg==',
        token: getApp().globalData.token
      },
      data: {},
      success: res => {
        var data = res.data;
        
        if (data.code !=0){

          if (data.data.price > 0) {
            data.data.price = util.formatPrice(data.data.price);
          } else {
            data.data.price = 1;
          }
          that.setData({
            leisure: data.data,
            typeindex: data.data.type
          })
        }
      }
    })

    this.setData({
      typearr: ["请选择", "新客户", "老客户"]
    })
  },
  bindtypeChange:function(e){
    const eindex = e.detail.value;
    const name = e.currentTarget.dataset.pickername;
    this.setData({
      typeindex: eindex,
      'leisure.type': e.detail.value
    })
  },
  minus:function(){
    this.data.leisure.price = (this.data.leisure.price||0) - 1
    this.setData({
      leisure: this.data.leisure
    })
  },
  add:function(){
    this.data.leisure.price = (this.data.leisure.price||0) + 1
    this.setData({
      leisure: this.data.leisure
    })
  },
  minusnumber: function () {
    this.data.leisure.number = (this.data.leisure.number||0) - 1
    this.setData({
      leisure: this.data.leisure
    })
  },
  addnumber: function () {
    this.data.leisure.number = (this.data.leisure.number||0) + 1
    this.setData({
      leisure: this.data.leisure
    })
  },
  starttimeChange:function(e){
    this.setData({
      'leisure.start_time': e.detail.value
    })
  },
  endtimeChange:function(e){
    this.setData({
      'leisure.end_time': e.detail.value
    })
  },
  numberChange: function (e) {
    this.setData({
      'leisure.number': e.detail.value
    })
  },
  priceChange:function(e){
    this.setData({
      'leisure.price': e.detail.value
    })
  },
  save:function(){

    if (this.data.leisure.price > 99 || this.data.leisure.price<1) {
      wx.showModal({
        title: '提示',
        content: '价格范围是1-99元',
        showCancel: false,
        success: function (res) { }
      })
      return;
    }
    if (this.data.leisure.number > 9 ) {
      wx.showModal({
        title: '提示',
        content: '每日发卡数最多不能超过9张',
        showCancel: false,
        success: function (res) { }
      })
      return;
    }
    if (this.data.leisure.number < 1) {
      wx.showModal({
        title: '提示',
        content: '每日发卡数最多不能小于1张',
        showCancel: false,
        success: function (res) { }
      })
      return;
    }
    if (!this.data.leisure.type) {
      wx.showModal({
        title: '提示',
        content: '请选择客户类型',
        showCancel: false,
        success: function (res) { }
      })
      return;
    }
    this.data.leisure.price = this.data.leisure.price*100;

    if(this.data.leisure.id > 0){
      util.nmAjax({
        method: "put",
        url: getApp().globalData.apiURL + 'v1/store_leisure_wash/' + this.data.leisure.id,
        data: this.data.leisure,
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
        url: getApp().globalData.apiURL + 'v1/store_leisure_wash',
        data: this.data.leisure,
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