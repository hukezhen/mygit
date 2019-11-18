//app.js
const util = require('/utils/util.js')
App({
  globalData: {
    userInfo: null,
    apiURL: 'https://nuomi.xianzhou.cn/',
    openId: "",
    uid: 0,
    token: '',
    hasUserInfo: false,
    avatarUrl: "",
    nickName: "",
    storeId: 0,
    id: 0
  },

  userLogin: function (callback) {
    if (wx.getStorageSync('token')) {
      var that = this
      wx.checkSession({
        success: function () {
          //session_key 未过期，并且在本生命周期一直有效
          console.log('session_key 未过期')
          that.globalData.token = wx.getStorageSync('token');
          that.globalData.uid = wx.getStorageSync('uid');
          that.globalData.storeId = wx.getStorageSync('uid');
          if (callback) {
            // that.updateUser(callback)
            callback()
          }
        },
        fail: function () {
          // session_key 已经失效，需要重新执行登录流程      
          console.log('session_key 已经失效')
          that.wxLogin(callback)
        }
      });
    } else {
      this.wxLogin(callback)
    }

  },
  wxLogin(callback) {
    var timestamp = Date.parse(new Date());
    timestamp = timestamp / 1000;
    var that = this;
    wx.login({
      success: res => {
        var that = this;
        wx.request({
          method: "post",
          url: this.globalData.apiURL + 'v1/auth/code',
          header: {
            time: timestamp,
            random: 'SRzJxpspPYr8af5hTxFs5jMFXKxckix6',
            sign: 'M2EwMzNiYTdhYTNiZGFiZWZkOTgxMzE3ZmI5ZTVmMmQ5NGQ4ZDg3MzFmMDA3MWNhYWRhN2ZmZWEzZTdkNmUzZg=='
          },
          data: {
            code: res.code
          },
          success: res => {
            var data = res.data;

            that.globalData.uid = res.data.data.uid;
            that.globalData.storeId = res.data.data.uid;
            that.globalData.token = res.data.data.token;
            wx.setStorageSync('token', res.data.data.token)
            wx.setStorageSync('uid', res.data.data.uid)
            if (callback) {
              callback(res);
            }
          }
        })
      },
      fail: function (res) {
        console.log(res)
      },

    })
  },
  onLaunch: function () {

    // 展示本地存储能力
    // var logs = wx.getStorageSync('logs') || []
    // logs.unshift(Date.now())
    // wx.setStorageSync('logs', logs)

    // var timestamp = Date.parse(new Date());
    // timestamp = timestamp / 1000;
    // var that = this;
    // wx.login({
    //   success: res => {
    //     var that = this;
    //     wx.request({
    //       method: "post",
    //       url: this.globalData.apiURL + 'v1/auth/code',
    //       header: {
    //         time: timestamp,
    //         random: 'SRzJxpspPYr8af5hTxFs5jMFXKxckix6',
    //         sign: 'M2EwMzNiYTdhYTNiZGFiZWZkOTgxMzE3ZmI5ZTVmMmQ5NGQ4ZDg3MzFmMDA3MWNhYWRhN2ZmZWEzZTdkNmUzZg=='
    //       },
    //       data: {
    //         code: res.code
    //       },
    //       success: res => {
    //         var data = res.data;

    //         that.globalData.uid = res.data.data.uid;
    //         that.globalData.storeId = res.data.data.uid;
    //         that.globalData.token = res.data.data.token;
    //         wx.setStorageSync('token', res.data.data.token)
    //         wx.setStorageSync('uid', res.data.data.uid)
    //         if (this.checkLoginReadyCallback) {
    //           this.checkLoginReadyCallback(res);
    //         }
    //         // wx.request({
    //         //   method: "get",
    //         //   url: this.globalData.apiURL + 'v1/store/' + res.data.data.uid, 
    //         //   header: {
    //         //     time: timestamp,
    //         //     random: 'SRzJxpspPYr8af5hTxFs5jMFXKxckix6',
    //         //     sign: 'M2EwMzNiYTdhYTNiZGFiZWZkOTgxMzE3ZmI5ZTVmMmQ5NGQ4ZDg3MzFmMDA3MWNhYWRhN2ZmZWEzZTdkNmUzZg==',
    //         //     token: res.data.data.token
    //         //   },
    //         //   success: storeinfo => {

    //         //     if (storeinfo.data.code == 1) {//已验证
    //         //       //由于这里是网络请求，可能会在 Page.onLoad 之后才返回
    //         //       // 所以此处加入 callback 以防止这种情况

    //         //       that.globalData.id = storeinfo.data.data.id;
    //         //       if (this.checkLoginReadyCallback) {
    //         //         this.checkLoginReadyCallback(res);
    //         //       }
    //         //       // wx.switchTab({
    //         //       //   url: '../myhome/myhome'
    //         //       // })

    //         //     } else {

    //         //       if (storeinfo.data.data.state == 1){  //未注册不做操作

    //         //         wx.navigateTo({ url: '../logs/logs' })
    //         //       } else if (storeinfo.data.data.state == 2) { 

    //         //         wx.showModal({

    //         //           title: '提示',
    //         //           content: '您提交的资料审核中',
    //         //           showCancel: false,
    //         //           success: function () {
    //         //             wx.navigateTo({ url: '../logs/logs' })
    //         //           }
    //         //         })
    //         //       } else if (storeinfo.data.data.state == 3) { 
    //         //         wx.showModal({
    //         //           title: '提示',
    //         //           content: '您提交的资料审核不通过',
    //         //           showCancel: false,
    //         //           success: function () {

    //         //           }
    //         //         })
    //         //       } else if (storeinfo.data.data.state == 4) {  //没有填写完善资料

    //         //         // wx.navigateTo({ url: '../mine/pages/mendianxinxi/mendianxinxi' })
    //         //       }
    //         //     }
    //         //   }
    //         // })


    //       }
    //     })


    //   }
    // })

  },
  compareVersion(v1, v2) {
    v1 = v1.split('.')
    v2 = v2.split('.')
    const len = Math.max(v1.length, v2.length)

    while (v1.length < len) {
      v1.push('0')
    }
    while (v2.length < len) {
      v2.push('0')
    }

    for (let i = 0; i < len; i++) {
      const num1 = parseInt(v1[i])
      const num2 = parseInt(v2[i])

      if (num1 > num2) {
        return 1
      } else if (num1 < num2) {
        return -1
      }
    }

    return 0
  }

})