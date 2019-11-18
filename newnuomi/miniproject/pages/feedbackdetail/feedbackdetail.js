const util = require('../../utils/util.js')
var page = 1,
  last_page = 1

Page({

  /**
   * 页面的初始数据
   */
  data: {
    mineimg: '',
    img: '',
    msglist: [],
    replycontent: '',
    sendtoid: 0,
    id: '',
    mode: '',
    prefix: getApp().globalData.apiURL
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    wx.setNavigationBarTitle({
      title: options.title,
    })
    //
    page = 1;
    var id = options.id;
    var other_uid = options.other_uid;
    var mode = options.mode;
    this.setData({
      img: options.img,
      sendtoid: other_uid,
      id: id,
      mode: mode
    })
    if (mode == 0)
      id = other_uid;
    var that = this;

    util.nmAjax({
      method: "get",
      url: getApp().globalData.apiURL + 'v1/store/' + getApp().globalData.storeId,
      success: res => {
        that.setData({
          mineimg: getApp().globalData.apiURL + res.data.top_img
        })
        // util.nmAjax({
        //   method: "get",
        //   url: getApp().globalData.apiURL + 'v1/message/' + id + "?mode=" + mode,
        //   success: msgresult => {
        //     var result = msgresult.data.data;
        //     for (let i = 0; i < result.length; i++) {
        //       result.ismine = (result[i].uid_send != other_uid)
        //     }
        //     that.setData({
        //       msglist: result
        //     })
        //   }
        // })
        that.network()
      }
    })

  },
  contentchange: function(e) {
    this.setData({
      replycontent: e.detail.value
    })
  },
  send: function(e) {
    if (!(this.data.replycontent.length > 0)) {
      wx.showModal({
        title: '提示',
        content: '请输入内容',
        showCancel: false,
        success: function(res) {}
      })
      return;
    }
    var that = this;
    util.nmAjax({
      method: "post",
      url: getApp().globalData.apiURL + 'v1/message',
      data: {
        uid_receive: this.data.sendtoid,
        content: this.data.replycontent,
        state: 2
      },
      success: msgresult => {
        // 手动插入
        var msg = {
          ismine: true,
          content: that.data.replycontent,
        }
        that.data.msglist.push(msg)
        that.setData({
          msglist: that.data.msglist,
          replycontent: ''
        })

        // that.onLoad({
        //   id: that.data.id,
        //   mode: that.data.mode,
        //   img: that.data.img,
        //   other_uid: that.data.sendtoid
        // })
      }
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
    page = 1
    this.network()
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

  },
  network() {
    var that = this
    util.nmAjax({
      method: "get",
      url: `${getApp().globalData.apiURL}v1/message/${this.data.sendtoid}?mode=${this.data.mode}&page=${page}`,
      success: msgresult => {
        last_page = msgresult.data.last_page
        var result = msgresult.data.data;
        for (let i = 0; i < result.length; i++) {
          result[i].ismine = that.data.mode == 0 ? (result[i].uid_receive == that.data.sendtoid) : false
          result[i].time = util.formatData(result[i].create_time)
        }
        result = result.reverse()
        // 
        var list = that.data.msglist
        if (page == 1) {
          // 刷新
          list = result
        } else {
          // 加载
          list = list.concat(result)
        }
        that.setData({
          msglist: list
        })
      }
    })
  },
  callAct(e) {
    var idx = e.currentTarget.dataset.index,
      item = this.data.msglist[idx],
      phone = item.content.split(':')[2].split(' ')[0]
    console.log(phone)
    if (this.data.mode == 5) {
      wx.makePhoneCall({
        phoneNumber: phone,
        success: function(res) {},
        fail: function(res) {},
        complete: function(res) {},
      })
    }
  }

})