const util = require('../../../../utils/util.js')
Page({

  /**
   * 页面的初始数据
   */
  data: {
    storeInfo: {},
    logs: {},
    isCashOut: false,
    isRecharge: false,
    iswxpay: false,
    paymethod: 0,
    cashOutAmount: '',
    rechargeAmount: '',
    moneyslog: []
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    this.fetchData();
  },
  fetchData: function() {
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
    util.nmAjax({
      method: "get",
      url: getApp().globalData.apiURL + 'v1/moneys_log',
      success: res => {

        var logs = res.data.data;
        for (let i = 0; i < logs.length; i++) {
          logs[i].statestr = (logs[i].state == 1 ? "收入" : "支出")
          logs[i].create_time = util.formatData(logs[i].create_time)
        }
        that.setData({
          moneyslog: logs
        })
      }
    })
  },
  goCashOut: function() {
    this.setData({
      isCashOut: true,
      cashOutAmount: ''
    });
  },
  goRecharge: function() {
    this.setData({
      isRecharge: true,
      rechargeAmount: ''
    });
  },
  closeCashOunt: function() {
    this.setData({
      isCashOut: false,
      cashOutAmount: ''
    });
  },
  closeRecharge: function() {
    this.setData({
      isRecharge: false,
      rechargeAmount: ''
    });
  },
  rechargeAmountChange: function(e) {
    this.setData({
      rechargeAmount: e.detail.value
    })
  },
  cashOutAmountChange: function(e) {
    this.setData({
      cashOutAmount: e.detail.value
    })
  },

  recharge: function() {
    if (!this.data.rechargeAmount) {
      wx.showModal({
        title: '提示',
        content: '请输入金额',
        showCancel: false,
        success: function(res) {}
      })
      return;
    }
    wx.showLoading()
    var mode = this.data.paymethod, that= this
    util.nmAjax({
      method: "post",
      url: getApp().globalData.apiURL + 'v1/recharge',
      data: {
        price: this.data.rechargeAmount * 100,
        mode: mode
      },
      success: res => {
        var pay = res.data
        if (mode == 0) {
          wx.hideLoading()
          wx.showToast({
            title: '支付成功',
            duration: 1500,
            mask: true,
          })
          that.fetchData();
          that.setData({
            isRecharge: false,
            rechargeAmount: ''
          });
        } else {
          // wx
          pay['success'] = function(e) {
            wx.showToast({
              title: '支付成功',
              icon: 'success',
              duration: 1500,
              mask: true,
            })
            setTimeout(() => {
              wx.navigateBack({
                delta: 1,
              })
            }, 1000)
          }
          pay['fail'] = function(e) {
            wx.showToast({
              title: '支付失败',
              icon: 'none',
              duration: 1500,
              mask: true,
            })
            pay['complete'] = function(e) {
              wx.hideLoading()
              that.fetchData();
              that.setData({
                isRecharge: false,
                rechargeAmount: ''
              });
            }
          }
          wx.requestPayment(pay)
        }
      }
    })
  },
  // recharge: function(e) {
  //   if (!this.data.rechargeAmount) {
  //     wx.showModal({
  //       title: '提示',
  //       content: '请输入金额',
  //       showCancel: false,
  //       success: function(res) {}
  //     })
  //     return;
  //   }
  //   var that = this;
  //   util.nmAjax({
  //     method: "post",
  //     url: getApp().globalData.apiURL + 'v1/recharge',
  //     data: {
  //       price: this.data.rechargeAmount
  //     },
  //     success: res => {

  //       util.nmAjax({
  //         method: "get",
  //         url: getApp().globalData.apiURL + 'v1/payment/' + res.data.order_sn + "?mode=" + that.data.paymethod,
  //         success: data => {
  //           if (that.data.paymethod == 1) {
  //             wx.showModal({
  //               title: '提示',
  //               content: '支付成功！',
  //               showCancel: false,
  //               success: function() {

  //                 that.fetchData();
  //                 that.setData({
  //                   isRecharge: false,
  //                   rechargeAmount: ''
  //                 });

  //               }
  //             });
  //           } else {
  //             wx.requestPayment({
  //               nonceStr: data.data.nonceStr,
  //               package: data.data.package,
  //               signType: data.data.signType,
  //               timeStamp: data.data.timeStamp,
  //               paySign: data.data.paySign,
  //               success: function(result) {

  //                 wx.showModal({
  //                   title: '提示',
  //                   content: '支付成功！',
  //                   showCancel: false,
  //                   success: function() {

  //                     that.fetchData();
  //                     that.setData({
  //                       isRecharge: false,
  //                       rechargeAmount: ''
  //                     });

  //                   }
  //                 });


  //               },
  //               fail: function() {},
  //               complete: function() {}

  //             })
  //           }

  //         }
  //       })


  //     }
  //   })
  // },
  cashOut: function(e) {
    if (!this.data.cashOutAmount) {
      wx.showModal({
        title: '提示',
        content: '请输入金额',
        showCancel: false,
        success: function(res) {}
      })
      return;
    }

  },
  radiochange: function(e) {

    this.setData({
      iswxpay: e.detail.value == 1,
      paymethod: e.detail.value
    })

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