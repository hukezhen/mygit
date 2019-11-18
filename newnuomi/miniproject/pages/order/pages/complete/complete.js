const util = require('../../../../utils/util.js')
Page({

  /**
   * 页面的初始数据
   */
  data: {
    order: {},
    imglist: [],
    pay_type: 1,
    activity_id: 0,
    activitymoney: 0,
    iswash: false,
    cls_name: '',
    number: '',
    numberclass: '',
    s_title: '',
    redpacket: []
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.setData({
      s_title: options.s_title,
      cls_name: options.cls_name,
      number: options.number,
      numberclass: util.getNumberColor(options.cls_name)
    })
    var that = this;
    util.nmAjax({
      method: "get",
      url: getApp().globalData.apiURL + 'v1/order/' + options.id,
      data: {

      },
      success: res => {

        res.data.order_time = util.formatTime(res.data.order_time, 'Y/M/D h:m');
        res.data.price = util.formatPrice(res.data.price);

        if (res.data.car_wash == null) {
          that.setData({
            iswash: false
          })
        }
        if (res.data.car_info != null) {
          res.data.car_info.moneys = util.formatPrice(res.data.car_info.moneys);
        }
        if (res.data.car_activity_info != null && res.data.car_activity_info.length > 0) {
          var list = res.data.car_activity_info;
          var redpacket = [],
            max = {}
          for (let j = 0; j < list.length; j++) {
            if (res.data.price * 100 > list[j].moneys) {
              list[j].moneys = util.formatPrice(list[j].moneys);
              redpacket.push(list[j])
              if (max.moneys) {
                max = max.moneys > list[j].moneys ? max : list[j]
              } else {
                max = list[j]
              }
            }
          }
          console.log(max)
          if (redpacket.length > 0) {
            that.setData({
              activity_id: max.id,
              activitymoney: max.moneys
            });
          }
          that.setData({
            redpacket: redpacket
          })
        }

        res.data.submitprice = (res.data.price - that.data.activitymoney).toFixed(2);
        that.setData({
          order: res.data
        })

      }
    })
  },
  addimg: function () {
    var self = this;
    wx.chooseImage({
      count: 3,
      sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
      sourceType: ['album', 'camera'],
      success: function (res) {

        var tempFilePaths = res.tempFilePaths;
        var list = self.data.imglist;
        if (list.length + tempFilePaths.length > 3) {
          wx.showModal({
            title: '提示',
            content: '最多只能上传3张图片',
            showCancel: false,
          })
        } else {
          list = list.concat(tempFilePaths);
          self.setData({
            imglist: list
          })
        }
        // for (var i = 0; i < tempFilePaths.length; i++) {

        //   wx.uploadFile({
        //     url: getApp().globalData.apiURL + "v1/upload",
        //     header: {
        //       time: timestamp,
        //       random: 'SRzJxpspPYr8af5hTxFs5jMFXKxckix6',
        //       sign: 'M2EwMzNiYTdhYTNiZGFiZWZkOTgxMzE3ZmI5ZTVmMmQ5NGQ4ZDg3MzFmMDA3MWNhYWRhN2ZmZWEzZTdkNmUzZg==',
        //       token: wx.getStorageSync('token')
        //     },
        //     filePath: tempFilePaths[i],
        //     name: 'file',
        //     success: function (res) {

        //       var resdata = JSON.parse(res.data);
        //       var list = self.data.imglist;
        //       if (list.length > 3) {
        //         wx.showModal({
        //           title: '提示',
        //           content: '最多只能上传3张图片',
        //           showCancel: false,
        //           success: function (res) {

        //           }
        //         })
        //       } else {
        //         list.push(getApp().globalData.apiURL + resdata.data.imgUrl);
        //         self.setData({
        //           imglist: list
        //         })
        //       }
        //     }
        //   });
        // }
      },
      fail: function ({
        errMsg
      }) {
        console.log('chooseImage fail, err is', errMsg)
      }
    })
  },
  uploadImage(callback) {
    var tempFilePaths = this.data.imglist,
      urls = [];
    var timestamp = Date.parse(new Date()) / 1000;
    self = this;
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
          urls.push(getApp().globalData.apiURL + resdata.data.imgUrl);
          // complete
          if (urls.length == tempFilePaths.length) {
            callback(urls);
          }
        }
      });
    }
  },
  radioChange: function (e) {
    let value = e.detail.value;
    this.setData({
      pay_type: value
    })
  },
  redradioChange: function (e) {
    let value = e.detail.value;
    this.setData({
      activity_id: value
    })
  },
  selactivityid: function (e) {
    console.log(e)
    var idx = e.currentTarget.dataset.idx;
    var price = this.data.redpacket[idx].moneys;
    console.log(price)
    this.setData({
      activity_id: this.data.redpacket[idx].id,
      activitymoney: price,
      'order.submitprice': (this.data.order.price - price).toFixed(2)
    })
  },
  pay: function () {
    var that = this;

    if (that.data.pay_type == 1) {
      if (that.data.order.car_info.moneys == 0) {
        // wx.showModal({
        //   title: '提示',
        //   content: '余额不足,到公众号充值',
        //   showCancel: false,
        //   success: function () {
        //   }
        // })
        // return;
      }
    }

    if (this.data.imglist.length == 0) {
      wx.showToast({
        title: '请选择图片后重试',
        icon: 'none',
        duration: 1500,
        mask: true,
      })
      return
    }

    var data = {};
    data.pay_type = that.data.pay_type;
    // data.image = that.data.imglist;

    if (that.data.activity_id > 0) {
      data.activity_id = that.data.activity_id;
    }
    if (data.pay_type == 2) { //次卡要传卡id
      data.wash_id = that.data.order.car_wash.id;
    }
    wx.showLoading();
    this.uploadImage(urls => {
      data.image = urls;
      wx.hideLoading();
      //
      util.nmAjax({
        method: "put",
        url: getApp().globalData.apiURL + 'v1/order/' + that.data.order.id,
        data: data,
        success: res => {

          wx.showModal({
            title: '提示',
            content: '提交成功！',
            showCancel: false,
            success: function () {
              wx.navigateTo({
                url: '../xianshika-zhifu/xianshika-zhifu?id=' + that.data.order.id
              })
            }
          })

        }
      })
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

  previewAct(e) {
    wx.previewImage({
      current: e.currentTarget.id,
      urls: this.data.imglist,
    })
  },
  deleAct(e) {
    this.data.imglist.splice(e.currentTarget.id, 1)
    this.setData({
      imglist: this.data.imglist
    })
  }
})