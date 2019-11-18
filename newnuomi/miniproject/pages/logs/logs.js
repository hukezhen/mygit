//logs.js
const util = require('../../utils/util.js')

Page({
  data: {
    logs: [],
    mobile: '',
    code: '',
    btntext: '获取验证码',
    disabled: false,
    currentTime: 60,
    showphonetip: false
  },
  onLoad: function () {
    this.setData({
      logs: (wx.getStorageSync('logs') || []).map(log => {
        return util.formatTime(new Date(log))
      })
    })
  },
  mobileinput: function (e) {
    this.setData({
      mobile: e.detail.value
    })
  },
  codeinput: function (e) {
    this.setData({
      code: e.detail.value
    })
  },
  blurPhone: function (e) {
    var phone = e.detail.value;
    if (!(/^1[34578]\d{9}$/.test(this.data.mobile))) {

      this.setData({
        showphonetip: true
      })


    } else {
      this.setData({
        showphonetip: false
      })
    }

  },
  getcode: function () {

    if (!(/^1[34578]\d{9}$/.test(this.data.mobile))) {

      // this.setData({
      //   showphonetip: true
      // })

      wx.showToast({
        title: '请输入正确的手机号',
        icon: 'none',
        duration: 1500,
        mask: true,
      })

    } else {

      this.setData({
        showphonetip: false
      })

      var that = this
      util.nmAjax({
        method: "post",
        url: getApp().globalData.apiURL + 'v1/sms',
        data: {
          telephone: this.data.mobile,
          scene: 8
        },
        success: res => {
          var data = res.data;
          wx.showToast({
            title: '发送成功',
            icon: 'none',
            duration: 1500,
            mask: true,
          })
          that.settime();
        }
      });

    }
  },
  settime: function () {
    var that = this;
    var currentTime = that.data.currentTime;
    that.setData({
      btntext: currentTime + '秒后获取'
    })
    var interval = setInterval(function () {
      that.setData({
        btntext: (currentTime - 1) + '秒后获取',
        disabled: true
      })
      currentTime--;
      if (currentTime <= 0) {
        clearInterval(interval)
        that.setData({
          btntext: '重新获取',
          currentTime: 60,
          disabled: false
        })
      }
    }, 1000)
  },
  login: function () {
    if (!(/^1[3456789]\d{9}$/.test(this.data.mobile))) {
      wx.showToast({
        title: '请输入正确的手机号',
        icon: 'none',
        duration: 1500,
        mask: true,
      })

    } else if (this.data.code == '') {
      wx.showToast({
        title: '请输入验证码',
        icon: 'none',
        duration: 1500,
        mask: true,
      })

    } else {
      var that = this;
      util.nmAjax({
        method: "post",
        url: getApp().globalData.apiURL + 'v1/auth/banding',
        data: {
          phone: this.data.mobile,
          code: this.data.code
        },
        success: res => {

          var data = res.data;
          if (data.status) {
            wx.redirectTo({
              url: '../mine/pages/mendianxinxi/mendianxinxi?phone=' + that.data.mobile,
              success: function (res) { },
              fail: function (res) { },
              complete: function (res) { },
            })
          }
        }
      })
    }
  }

})
