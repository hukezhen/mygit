const util = require('../../utils/util.js');
const utils = require('../../utils/utils.js');
var Animation = require('../../utils/Animation.js');
var Pointer = require('../../utils/Pointer.js');
var Wheel = require('../../utils/Wheel.js');
Page({

  /**
   * 页面的初始数据
   */
  data: {
    awardList: [],
    awardIdList: [],
    logList: [],
    windowWidth: 0,
    windowHeight: 0,
    wheelImg: 'assets/wheel.png',
    pointImg: 'assets/point.png',
    touch: {
      x: 0,
      y: 0,
      isPressed: false
    },
    awardId: 0,
    isStart: false,
    price: 0,
    balance: 0,
    // 
    context: null,
    animation: null,
    point: null,
    wheel: null
  },

  touchMove: function (event) {

  },

  canvasTouchStart: function (event) {
    // var touch = event.changedTouches[0];
    // touch.isPressed = true;
    // this.setData({
    //   touch: touch
    // })
    // 
  },
  luckAct() {
    this.fetchData();
  },
  canvasTouchEnd: function (event) {
    // var touch = event.changedTouches[0];
    // touch.isPressed = false;
    // this.setData({
    //   touch: touch
    // })
    // this.fetchData();
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    wx.setNavigationBarTitle({
      title: '幸运大转盘',
    })
    // this.fetchData();
    var that = this;
    // 把设备的尺寸赋值给画布，以做到全屏效果
    wx.getSystemInfo({
      success: function (res) {
        that.setData({
          windowWidth: res.windowWidth - 50,
          windowHeight: res.windowHeight - 200
        });
      },
    })

  },
  fetchData: function () {
    if (this.data.animation.isRun) {
      return;
    }
    wx.showLoading()
    var that = this;
    util.nmAjax({
      method: "post",
      url: getApp().globalData.apiURL + 'v1/turn',
      success: res => {
        wx.hideLoading()
        that.data.awardId = res.data.id
        // 获取命中下标
        for (var i = 0; i < that.data.awardList.length; i++) {
          var item = that.data.awardList[i]
          if (res.data.id == item.id) {
            that.runAnimation(i);
            that.updateLog()
            break
          }
        }

      }
    })
  },
  updateLog() {
    var that = this
    util.nmAjax({
      method: "get",
      url: getApp().globalData.apiURL + 'v1/turn',
      success: res => {
        var result = res.data;
        that.setData({
          balance: result.balance,
          logList: result.log,
        })
      }
    });
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */

  onReady: function () {
    
  },

  initCanvas(arr) {
    var slicePrizes = []
    for (var i in arr) {
      var item = arr[i]
      slicePrizes.push({
        'text': item.title,
        'img': '../images/gift.png'
      })
    }

    var that = this,
      w = this.data.windowWidth,
      h = this.data.windowHeight;
    this.data.context = wx.createCanvasContext('canvas');
    this.data.wheel = new Wheel(w / 2, h / 2.5, w / 2 - 50, slicePrizes);
    this.data.point = new Pointer(w / 2, h / 2.5, 40, this.data.wheel);
    this.data.animation = new Animation(this.data.wheel, {
      w: w,
      h: h
    });

    this.data.wheel.prizeWidth = 30;
    this.data.wheel.prizeHeight = 30;

    // 启用事件
    // point.inputEvent = true;
    // point.onInputDown = run;
    this.updateCanvas()
  },

  updateCanvas() {
    // 更新动画
    var context = this.data.context,
      wheel = this.data.wheel,
      point = this.data.point,
      animation = this.data.animation,
      w = this.data.windowWidth,
      h = this.data.windowHeight;
    // 清空
    context.clearRect(0, 0, w, h);
    // 画转盘
    wheel.draw(context);
    // 画指针
    point.draw(context);

    // 更新数据
    animation.draw(context);
    // 更新数据
    animation.update();
    // 绘图   
    context.draw()

  },

  // 开始转
  runAnimation(awardIdx) {
    var context = this.data.context,
      wheel = this.data.wheel,
      point = this.data.point,
      animation = this.data.animation,
      w = this.data.windowWidth,
      h = this.data.windowHeight,
      that = this;
    var interval = setInterval(this.updateCanvas, 16);
    // 避免重复调用
    if (animation.isRun) return;
    // 当动画完成时
    animation.onComplete = function (prize) {
      console.log(11111)
      setTimeout(() => {
        wx.showToast({
          image: prize.img,
          title: prize.text,
          duration: 1500,
          mask: true,
        })
      }, 1000);
      clearInterval(interval);
    };

    // 开始转
    animation.run();

    // 后台返回数据
    setTimeout(function () {
      // 计算奖品角度
      animation.stopTo(awardIdx);
    }, 3000);
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
    //this.fetchData();
    var that = this
    util.nmAjax({
      method: "get",
      url: getApp().globalData.apiURL + 'v1/turn',
      success: res => {
        var result = res.data;
        that.setData({
          logList: result.log,
          awardList: result.list,
          price: result.price,
          balance: result.balance,
        })
        that.initCanvas(result.list)
      }
    });
  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () { },

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