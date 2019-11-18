const util = require('../../../../utils/util.js')
Page({

  /**
   * 页面的初始数据
   */
  data: {
    title: "",
    imgurl: '',
    content: "",
    startdata: "",
    starttime: "",
    enddata: "",
    endtime: "",
    isdoortodoor: false,
    price: 0,
    contentarr: [],
    contentindex: 0,
    phone: '',
    state: 1,
    prefix: getApp().globalData.apiURL,
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.setData({
      contentarr: ["人手不足", "材料不足"]
    })
  },
  addimg: function () {
    var self = this;
    var timestamp = Date.parse(new Date());
    timestamp = timestamp / 1000;
    wx.chooseImage({
      count: 6,
      sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
      sourceType: ['album', 'camera'],
      success: function (res) {

        var tempFilePaths = res.tempFilePaths;
        for (var i = 0; i < tempFilePaths.length; i++) {

          wx.uploadFile({
            url: getApp().globalData.apiURL + "v1/upload",
            header: {
              time: timestamp,
              random: 'SRzJxpspPYr8af5hTxFs5jMFXKxckix6',
              sign: 'M2EwMzNiYTdhYTNiZGFiZWZkOTgxMzE3ZmI5ZTVmMmQ5NGQ4ZDg3MzFmMDA3MWNhYWRhN2ZmZWEzZTdkNmUzZg==',
              token: wx.getStorageSync('token')
            },
            filePath: tempFilePaths[i],
            name: 'file',
            success: function (res) {

              var resdata = JSON.parse(res.data);
              self.setData({
                imgurl: resdata.data.imgUrl
              })

            }
          });
        }
      },
      fail: function ({
        errMsg
      }) {
        console.log('chooseImage fail, err is', errMsg)
      }
    })
  },
  titlechange: function (e) {
    this.setData({
      title: e.detail.value
    })
  },
  priceChange: function (e) {
    this.setData({
      price: e.detail.value
    })
  },
  phoneChange: function (e) {
    this.setData({
      phone: e.detail.value
    })
  },
  radioChange: function (e) {
    this.setData({
      state: e.detail.value
    })
  },
  reset: function () {
    this.setData({
      title: '',
      imgurl: '',
      price: '',
      phone: ''
    })
  },
  save: function () {
    var that = this;
    util.nmAjax({
      method: "post",
      url: getApp().globalData.apiURL + 'v1/relief',
      data: {
        title: that.data.title,
        type: that.data.phone,
        image: that.data.imgurl,
        price: that.data.price,
        state: that.data.state,
        start_time: '2019-10-06 10:30',
        end_time: '2029-10-06 10:30'
      },
      success: res => {
        wx.showModal({
          title: '提示',
          content: '提交成功',
          showCancel: false,
          success: function () {

            wx.navigateBack({
              delta: 1
            })
          }
        })

      }
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