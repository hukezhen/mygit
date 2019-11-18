const util = require('../../../../utils/util.js')
Page({

  /**
   * 页面的初始数据
   */
  data: {
    rechargetypes: [],
    currentindex: 0,
    checked: false,
    modalName: null
  },
  getrechargetypes: function() {
    var that = this;
    util.nmAjax({
      method: "get",
      url: getApp().globalData.apiURL + 'v1/grade',
      success: res => {

        that.setData({
          rechargetypes: res.data
        })
      }
    })
  },
  seltype: function(event) {
    var typeid = event.currentTarget.dataset.index;
    this.setData({
      currentindex: event.currentTarget.dataset.index
    })
  },
  checkedTap: function() {
    var checked = this.data.checked;
    this.setData({
      "checked": !checked
    })
  },
  pay: function(mode) {
    wx.showLoading()
    var item = this.data.rechargetypes[this.data.currentindex];
    util.nmAjax({
      method: "post",
      url: getApp().globalData.apiURL + 'v1/grade',
      data: {
        id: item.id,
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
          setTimeout(()=> {
            wx.navigateBack({
              delta: 1,
            })
          }, 1500)
        } else {
          // wx
          pay['success'] = function(e) {
            wx.showToast({
              title: '支付成功',
              icon: 'success',
              duration: 1500,
              mask: true,
            })
            setTimeout(()=> {
              wx.navigateBack({
                delta: 1,
              })
            },1000)
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
              wx.navigateBack({
                delta: 1,
              })
            }
          }
          wx.requestPayment(pay)
        }
      }
    })

  },
  hideModal(e) {
    var mode = e.currentTarget.id
    console.log(mode)
    if (mode > -1) {
      this.pay(mode)
    }
    this.setData({
      modalName: null
    })
  },
  showModal(e) {
    if (!this.data.checked) {
      wx.showModal({
        title: '提示',
        content: '同意用户协议后再支付',
        showCancel: false,
        success: function(res) {}
      })
    } else {
      this.setData({
        modalName: ''
      })
    }
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {

    this.getrechargetypes();
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