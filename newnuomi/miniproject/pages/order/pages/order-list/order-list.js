
const util = require('../../../../utils/util.js')
Page({

  /**
   * 页面的初始数据
   */
  data: {
    orderlist: [],
    currentNavtab: -1,
  },
  switchTab: function (e) {
    var idx = e.currentTarget.dataset.idx;
    this.setData({
      currentNavtab: idx
    });
    this.getorderlist(idx);
  },
  getorderlist: function (type) {
    var data = {};
    if (type != -1) data.pay_state = type;
    var that = this;
    util.nmAjax({
      method: "get",
      url: getApp().globalData.apiURL + 'v1/order',
      data: data,
      success: res => {
        var data = res.data.data;

        if (data.length > 0) {

          for (let i = 0; i < data.length; i++) {
            data[i].order_time = util.formatData(data[i].order_time);
            data[i].statestr = that.getstatestr(data[i]);
            data[i].numberclass = util.getNumberColor(data[i].cls_name);

            data[i].show = (data[i].pay_state == 0 && data[i].state_tags == '预约订单')
            var url = '../order-detail/order-detail?id=' + data[i].id;
            if (data[i].pay_state == 0 || data[i].pay_state == 1) {
              url = '../complete/complete?id=' + data[i].id + '&cls_name=' + data[i].cls_name + '&number=' + data[i].c_number
            }
            data[i].url = url;
          }

          that.setData({
            orderlist: data
          })
        } else {
          that.setData({
            orderlist: []
          })
        }

      }
    })
  },
  getstatestr: function (item) {
    if (item.reserve_state == 1) {
      if (item.pay_state == 1) {
        return "待核销";
      } else if (item.pay_state == 2) {
        return '已核销';
      }
    }else {
      if (item.state == 0)
        return "新提交";
      if (item.state == 1)
        return "待支付";
      if (item.state == 12)
        return "服务完成";
      if (item.state == 2)
        return "已支付";
      if (item.state == 9)
        return "已取消";
    }
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
  },
  receive: function (e) {
    const index = e.currentTarget.dataset.index;
    let list = this.data.orderlist;
    let order = list[index];
    var that = this;
    util.nmAjax({
      method: "put",
      url: getApp().globalData.apiURL + 'v1/orders_reserve/' + order.id,
      success: res => {
        wx.showModal({
          title: '提示',
          content: '接单成功',
          showCancel: false,
          success: function (res) {
            that.getorderlist(that.data.currentNavtab);
           }
        })
      }
    })
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
    this.getorderlist(-1);
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

  },
  checkAct(e) {
    // const index = e.currentTarget.dataset.index;
    // let list = this.data.orderlist;
    // let order = list[index];
    // var that = this;
    // util.nmAjax({
    //   method: "put",
    //   url: getApp().globalData.apiURL + 'v1/orders_reserve/' + order.id,
    //   success: res => {
    //     wx.showModal({
    //       title: '提示',
    //       content: '接单成功',
    //       showCancel: false,
    //       success: function (res) { }
    //     })
    //   }
    // })
  }
})