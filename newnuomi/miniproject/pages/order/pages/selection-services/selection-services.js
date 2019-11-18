// pages/order/pages/selection-services/selection-services.js

const util = require('../../../../utils/util.js')
Page({

  /**
   * 页面的初始数据
   */
  data: {
    id: 0,
    typelist: [],
    goodslist:[],
    currenttype:0
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.fetchData();
  },
  fetchData:function(){
    util.nmAjax({
      method: "get",
      url: getApp().globalData.apiURL + 'v1/goods_type',
      data: {
       
      },
      success: res => {
        var data = res;
        this.setData({
          typelist : data.data
        })
       
        if (data.data.length > 0){
          this.getgoodslist(data.data[0].id)
        }
      }
    })
  },
  seltype: function (event) {
    var typeid = event.currentTarget.dataset.id;
    this.setData({
      currenttype: event.currentTarget.dataset.index
    })
    this.getgoodslist(typeid);
  },
  getgoodslist:function(typeid){
    var that = this;
    
    util.nmAjax({
      method: "get",
      url: getApp().globalData.apiURL + 'v1/goods',
      data: {
        type: typeid,
        mode:2
      },
      success: res => {
        var list = res.data.data;
        
        if (list.length > 0) {

          for (let j = 0; j < list.length; j++) {
            if (list[j].describes==null)
              list[j].describes="";
            if (list[j].price > 0)
              list[j].price = util.formatPrice(list[j].price);
          }
        }
        that.setData({
          goodslist:list
        })
      }
    })
  }, selectgoods:function(e){
      const index = e.currentTarget.dataset.index;
      let list = this.data.goodslist;
    let selectedgoods = list[index];

      var pages = getCurrentPages(); //页面指针数组 
      var prepage = pages[pages.length - 2]; //上一页面指针 

      prepage.setData({
        selectedgoods: selectedgoods
      })
      wx.navigateBack({
        delta: 1,
      });
    
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