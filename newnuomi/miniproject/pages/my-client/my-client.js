const util = require('../../utils/util.js')

var page = 1,
  last_page = 1

Page({

  /**
   * 页面的初始数据
   */
  data: {
    id: 0,
    clientlist: [],
    typelist: [{
      id: 1,
      title: '我的客户'
    }, {
      id: 2,
      title: '新增客户'
    }],
    currenttype: 1,
    searchkey: '',
    currentClient: {},
    couponCard: {},
    redPacket: {},
    sendCouponCard: false,
    sendRedPacket: false,
    redPacketAmount: "",
    redPacketNote: '',
    storeInfo: {},
    prefix: getApp().globalData.apiURL,
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {

  },
  getStoreInfo: function() {
    var that = this;
    util.nmAjax({
      method: "get",
      url: getApp().globalData.apiURL + 'v1/store/' + getApp().globalData.storeId,
      data: {

      },
      success: res => {
        that.setData({
          storeInfo: res.data
        })
      }
    })
  },
  getClientList: function() {
    var that = this;
    var data = {};
    data.type = that.data.currenttype;
    if (that.data.searchkey != '') {
      data.number = that.data.searchkey;
    }
    util.nmAjax({
      method: "get",
      url: getApp().globalData.apiURL + 'v1/customer',
      data: data,
      success: res => {
        last_page = res.data.last_page
        var list = res.data.data, result = [];

        if (list.length > 0) {

          for (let j = 0; j < list.length; j++) {

            if (list[j].create_time != null)
              list[j].create_time = util.formatTime(list[j].create_time, 'Y/M/D h:m:s');
            if (list[j].cls_name != null)
              list[j].color = util.getNumberColor(list[j].cls_name);

            if (list[j].cover_path)
              list[j].cover_path = getApp().globalData.apiURL + list[j].cover_path;
          }

          // 
          if (page == 1) {
            // 刷新
            result = list
          } else {
            // 加载
            result = list.concat(list)
          }
          that.setData({
            clientlist: list
          })
        }
      }
    })
  },
  seltype: function(event) {
    var typeid = event.currentTarget.dataset.id;
    this.setData({
      currenttype: event.currentTarget.dataset.id
    })
    this.getClientList();
  },
  searchkeyChange: function(e) {
    this.setData({
      searchkey: e.detail.value
    })
  },
  search: function() {
    this.getClientList();
  },
  ring: function(e) {


    const index = e.currentTarget.dataset.index;
    let list = this.data.clientlist;
    let client = list[index];
    if (client.phone) {
      wx.makePhoneCall({
        phoneNumber: client.phone
      })
    } else {
      wx.showToast({
        title: '请先邀请客户注册',
        icon: 'none',
        duration: 1500,
        mask: true,
      })
    }

  },
  couponCard: function(e) {
    const index = e.currentTarget.dataset.index;
    let list = this.data.clientlist;
    let client = list[index];
    var that = this;

    if (client.phone) {
      util.nmAjax({
        method: "get",
        url: getApp().globalData.apiURL + 'v1/store_leisure_wash',
        success: res => {
          var result = res.data;
          result.price = util.formatPrice(result.price);

          that.setData({
            sendCouponCard: true,
            currentClient: client,
            couponCard: result
          })
        }
      })
    } else {
      wx.showToast({
        title: '请先邀请客户注册',
        icon: 'none',
        duration: 1500,
        mask: true,
      })
    }


  },
  closeSendCouponCard: function() {
    this.setData({
      sendCouponCard: false,
      currentClient: {},
      couponCard: {}
    })
  },
  sendCouponCardEvent: function() {

    var that = this;
    util.nmAjax({
      method: "post",
      url: getApp().globalData.apiURL + 'v1/car_leisure_wash',
      data: {
        cid: that.data.currentClient.aid
      },
      success: res => {
        var result = res.data;
        wx.showModal({
          title: '提示',
          content: res.msg,
          showCancel: false,
          success: function(res) {}
        })
        that.setData({
          sendCouponCard: false,
          currentClient: {},
          couponCard: {}
        })
      }
    })
  },
  redPacket: function(e) {
    const index = e.currentTarget.dataset.index;
    let list = this.data.clientlist;
    let client = list[index];
    var that = this;

    if(client.phone) {
      that.getStoreInfo();
      that.setData({
        sendRedPacket: true,
        currentClient: client,
      })
    }else {
      wx.showToast({
        title: '请先邀请客户注册',
        icon: 'none',
        duration: 1500,
        mask: true,
      })
    }

    // util.nmAjax({
    //   method: "get",
    //   url: getApp().globalData.apiURL + 'v1/store_leisure_wash',
    //   success: res => {
    //     var result = res.data;
    //     result.price = util.formatPrice(result.price);

    //     that.setData({
    //       sendCouponCard: true,
    //       currentClient: client,
    //       couponCard: result
    //     })
    //   }
    // })

  },
  redPacketAmountChange: function(e) {
    this.setData({
      redPacketAmount: e.detail.value
    })
  },
  redPacketNoteChange: function(e) {
    this.setData({
      redPacketNote: e.detail.value
    })
  },
  sendRedPacketEvent: function(e) {
    if (this.data.redPacketAmount == 0) {
      wx.showModal({
        title: '提示',
        content: '请输入红包金额',
        showCancel: false,
        success: function(res) {}
      })
      return;
    }
    var that = this;
    util.nmAjax({
      method: "post",
      url: getApp().globalData.apiURL + 'v1/car_activity',
      data: {
        cid: that.data.currentClient.aid,
        moneys: that.data.redPacketAmount * 100,
        title: that.data.redPacketNote == '' ? '店小利薄，不成敬意' : that.data.redPacketNote,
        type: 1
      },
      success: res => {
        var result = res.data;
        wx.showModal({
          title: '提示',
          content: res.msg,
          showCancel: false,
          success: function(res) {}
        })
        that.setData({
          sendRedPacket: false,
          currentClient: {},
          redPacket: {},
          redPacketAmount: "",
          redPacketNote: ""
        })
      }
    })
  },
  closeSendRedPacket: function() {
    this.setData({
      sendRedPacket: false,
      currentClient: {},
      redPacket: {},
      redPacketAmount: "",
      redPacketNote: ""
    })
  },
  /**
   *
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function() {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function() {
    this.onPullDownRefresh()
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
    page = 1
    this.getClientList();
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function() {
    // 加载
    if (last_page > page) {
      page++
      this.network()
    }
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function() {

  }
})