const util = require('../../../../utils/util.js')
Page({

  /**
   * 页面的初始数据
   */
  data: {
    storeInfo: {},
    imgurl: {},
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    if (options.phone) {
      this.setData({
        'storeInfo.phone': options.phone
      })
    }

    this.init();

  },
  reset: function () {
    this.init();
  },
  init: function () {
    var that = this;
    var timestamp = Date.parse(new Date());
    timestamp = timestamp / 1000;
    wx.request({
      method: "get",
      url: getApp().globalData.apiURL + 'v1/store/' + getApp().globalData.storeId,
      header: {
        time: timestamp,
        random: 'SRzJxpspPYr8af5hTxFs5jMFXKxckix6',
        sign: 'M2EwMzNiYTdhYTNiZGFiZWZkOTgxMzE3ZmI5ZTVmMmQ5NGQ4ZDg3MzFmMDA3MWNhYWRhN2ZmZWEzZTdkNmUzZg==',
        token: getApp().globalData.token
      },
      data: {

      },
      success: res => {
        if (res.data.code == 1) {

          res.data.locationname = res.data.data.name;
          that.setData({
            storeInfo: res.data.data,
            imgurl: getApp().globalData.apiURL + res.data.data.top_img
          })
        } else {
          this.setData({
            'storeInfo.phone': res.data.data.phone
          })
        }

      }
    });
  },
  addimg: function () {
    var self = this;
    var timestamp = Date.parse(new Date());
    timestamp = timestamp / 1000;
    wx.chooseImage({
      count: 1,
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
              token: getApp().globalData.token
            },
            filePath: tempFilePaths[i],
            name: 'file',
            success: function (res) {

              var resdata = JSON.parse(res.data);
              self.setData({
                'storeInfo.top_img': resdata.data.imgUrl,
                imgurl: getApp().globalData.apiURL + resdata.data.imgUrl
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
  getLocation: function (e) {
    var that = this;
    // wx.getLocation({
    //   type:"gcj02",
    //   success: function(res) {
    //     console.log(res);

    // wx.openLocation({
    //   latitude: Number(that.data.storeInfo.lat),
    //     longitude: Number(that.data.storeInfo.lng),
    //   scale:18,
    //   success:function(res){
    wx.chooseLocation({
      success: function (res) {

        that.setData({
          "storeInfo.locationname": res.address,
          "storeInfo.lat": res.latitude,
          "storeInfo.lng": res.longitude,
        })
      },
    })
    //   }
    // })
    //   },
    // })
  },
  namechange: function (e) {
    this.setData({
      'storeInfo.name': e.detail.value
    })
  },
  nicknamechange: function (e) {
    this.setData({
      'storeInfo.nickname': e.detail.value
    })
  },
  phonechange: function (e) {
    this.setData({
      'storeInfo.phone': e.detail.value
    })
  },
  save: function (e) {

    console.log()
    if (this.data.storeInfo.top_img == null || this.data.storeInfo.top_img == '') {
      wx.showToast({
        title: '请输选择头像',
        icon: 'none',
        duration: 1500,
        mask: true,
      })

    } else if (this.data.storeInfo.name == null || this.data.storeInfo.name == '') {
      wx.showToast({
        title: '请输入门店名称',
        icon: 'none',
        duration: 1500,
        mask: true,
      })
    } else if (this.data.storeInfo.lat == null || this.data.storeInfo.lat == '') {
      wx.showToast({
        title: '请输入门店地址',
        icon: 'none',
        duration: 1500,
        mask: true,
      })
    } else
      if (this.data.storeInfo.nickname == null || this.data.storeInfo.nickname == '') {
        wx.showToast({
          title: '请输入昵称',
          icon: 'none',
          duration: 1500,
          mask: true,
        })
      } else if (!(/^1[3456789]\d{9}$/.test(this.data.storeInfo.phone))) {
        wx.showToast({
          title: '请输入正确的手机号',
          icon: 'none',
          duration: 1500,
          mask: true,
        })

      } else {
        if (this.data.storeInfo.id > 0) {
          var that = this;
          util.nmAjax({
            method: "put",
            url: getApp().globalData.apiURL + 'v1/store/' + getApp().globalData.storeId,
            data: that.data.storeInfo,
            success: res => {
              wx.navigateBack({
                delta: 1
              })
            }
          });
        } else {

          var that = this;
          util.nmAjax({
            method: "post",
            url: getApp().globalData.apiURL + 'v1/store',
            data: that.data.storeInfo,
            success: res => {

              wx.showModal({
                title: '提示',
                content: '提交审核成功',
                showCancel: false,
                success: function () {
                  wx.navigateBack({
                    delta: 1,
                  })
                }
              })
            }
          });
        }
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