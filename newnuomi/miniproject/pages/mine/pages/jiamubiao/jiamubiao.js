const util = require('../../../../utils/util.js')
Page({

  /**
   * 页面的初始数据
   */
  data: {
    typelist: [],
    goodslist: [],
    currenttype: 0,
    washtype: {},
    wash: {},
    startX: 0,
    startY: 0,
    isedit: false,
    currentEditGoods: {},
    currentedittype: 0,
    currentedittitle: '',
    currenteditoriginalprice: 0,
    currenteditprice: 0,
    iseditwash: false,
    xichekalist: [],
    adlist: [],
    iseditWashCard: false, //编辑洗车卡
    currentEditWashCard: {},
    isbuyservice: false,
    currentserviceid: 0,
    iswxpay: true,
    paymethod: 0,
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
      url: getApp().globalData.apiURL + 'v1/goods_type',
      data: {

      },
      success: res => {
        var data = res;

        this.setData({
          washtype: data.data[0].id
        });
        // data.data = data.data.slice(1);
        this.setData({
          typelist: data.data,
          currenttype: data.data[0].id
        })

        if (data.data.length > 0) {
          this.getgoodslist(data.data[0].id)
        }
        this.setwash();
      }
    })

    util.nmAjax({
      method: "get",
      url: getApp().globalData.apiURL + 'v1/wash?mode=1',
      data: {

      },
      success: res => {
        var data = res;

        that.setData({
          xichekalist: data.data
        });

      }
    })
    util.nmAjax({
      method: "get",
      url: getApp().globalData.apiURL + 'v1/advert',
      data: {

      },
      success: res => {
        var data = res.data.data;

        for (let i = 0; i < data.length; i++) {
          data[i].cover_path = getApp().globalData.apiURL + data[i].cover_path;

        }
        that.setData({
          adlist: data
        })
      }
    })
  },
  setwash: function() {
    var that = this;
    util.nmAjax({
      method: "get",
      url: getApp().globalData.apiURL + 'v1/goods',
      data: {
        type: that.data.washtype,
        mode: 1
      },
      success: res => {
        var list = res.data.data;
        var obj = {};
        if (list.length > 0) {

          if (list[0].describes == null)
            obj.describes = "";
          if (list[0].price > 0)
            obj.price = util.formatPrice(list[0].price);
          if (list[0].original_price > 0)
            obj.original_price = util.formatPrice(list[0].original_price);
          obj.isTouchMove = false;
          obj.title = list[0].title;
          obj.type = list[0].type;
          obj.id = list[0].id;
          obj.describes = list[0].describes;
        }
        that.setData({
          wash: obj
        })
      }
    })
  },
  radiochange: function(e) {

    var val = e.detail.value;
    this.setData({
      iswxpay: e.detail.value == 1,
      paymethod: e.detail.value
    })

  },
  recharge: function() {
    var that = this;
    util.nmAjax({
      method: "post",
      url: getApp().globalData.apiURL + 'v1/advert',
      data: {
        aid: this.data.currentserviceid,
        mode: this.data.paymethod
      },
      success: res => {
        if (that.data.paymethod == 0) {
          wx.showModal({
            title: '提示',
            content: '支付成功！',
            showCancel: false,
            success: function() {
              that.setData({
                isbuyservice: false
              })
              that.fetchData();
            }
          });
        } else {
          var data = res
          wx.showLoading()
          wx.requestPayment({
            nonceStr: data.data.nonceStr,
            package: data.data.package,
            signType: data.data.signType,
            timeStamp: data.data.timeStamp,
            paySign: data.data.paySign,
            success: function(result) {
              wx.showToast({
                title: '支付成功！',
                duration: 1500,
                mask: true,
              })
            },
            fail: function() {
              wx.showToast({
                title: '支付失败！',
                icon: 'none',
                duration: 1500,
                mask: true,
              })
            },
            complete: function() {
              wx.hideLoading()
              that.setData({
                isbuyservice: false
              })

              that.fetchData();
            }
          })
        }
      }
    })
  },
  closeRecharge: function() {
    this.setData({
      isbuyservice: false
    })
  },
  buy: function(event) {
    var id = event.currentTarget.dataset.id;
    this.setData({
      isbuyservice: true,
      currentserviceid: id
    })
  },
  seltype: function(event) {
    var typeid = event.currentTarget.dataset.id;
    this.setData({
      currenttype: typeid
    })
    this.getgoodslist(typeid);
  },
  seledittype: function(e) {
    var typeid = e.currentTarget.dataset.id;
    this.setData({
      currentedittype: typeid
    })
  },
  editstockbind: function(e) {
    this.setData({
      'currentEditGoods.stock': e.detail.value
    })
  },
  editoriginalpricebind: function(e) {
    this.setData({
      'currentEditGoods.original_price': e.detail.value
    })
  },
  editpricebind: function(e) {
    this.setData({
      'currentEditGoods.price': e.detail.value
    })
  },
  getgoodslist: function(typeid) {
    var that = this;

    util.nmAjax({
      method: "get",
      url: getApp().globalData.apiURL + 'v1/goods',
      data: {
        type: typeid
      },
      success: res => {
        var list = res.data.data;

        if (list.length > 0) {

          for (let j = 0; j < list.length; j++) {
            if (list[j].describes == null)
              list[j].describes = "";

            if (list[j].price > 0)
              list[j].price = util.formatPrice(list[j].price);
            else
              list[j].price = 0;

            if (list[j].original_price > 0)
              list[j].original_price = util.formatPrice(list[j].original_price);
            else
              list[j].original_price = 0;

            list[j].cover_path = getApp().globalData.apiURL + list[j].cover_path;
            list[j].stock = list[j].stock || 0;
            list[j].isTouchMove = false;
          }
        }
        that.setData({
          goodslist: list
        })
      }
    })
  },

  //手指触摸动作开始 记录起点X坐标

  touchstart: function(e) {
    this.data.goodslist.forEach(function(v, i) {

      if (v.isTouchMove) //只操作为true的

        v.isTouchMove = false;

    })


    this.setData({

      startX: e.changedTouches[0].clientX,

      startY: e.changedTouches[0].clientY,
      goodslist: this.data.goodslist
    })

  },

  //滑动事件处理

  touchmove: function(e) {

    var that = this,

      index = e.currentTarget.dataset.index, //当前索引

      startX = that.data.startX, //开始X坐标

      startY = that.data.startY, //开始Y坐标

      touchMoveX = e.changedTouches[0].clientX, //滑动变化坐标

      touchMoveY = e.changedTouches[0].clientY, //滑动变化坐标

      //获取滑动角度

      angle = that.angle({
        X: startX,
        Y: startY
      }, {
        X: touchMoveX,
        Y: touchMoveY
      });

    that.data.goodslist.forEach(function(v, i) {

      v.isTouchMove = false

      //滑动超过30度角 return

      if (Math.abs(angle) > 30) return;

      if (i == index) {

        if (touchMoveX > startX) { //右滑

          v.isTouchMove = false

        } else //左滑
        {

          v.isTouchMove = true
        }
      }
      //更新数据

      that.setData({

        goodslist: that.data.goodslist

      })
    })



  },
  angle: function(start, end) {

    var _X = end.X - start.X,

      _Y = end.Y - start.Y

    //返回角度 /Math.atan()返回数字的反正切值

    return 360 * Math.atan(_Y / _X) / (2 * Math.PI);

  },
  del: function(e) {
    var goods = this.data.goodslist[e.currentTarget.dataset.index];

    util.nmAjax({
      method: "DELETE",
      url: getApp().globalData.apiURL + 'v1/goods/' + goods.id,
      success: res => {

      }
    })
    this.data.goodslist.splice(e.currentTarget.dataset.index, 1)

    this.setData({

      goodslist: this.data.goodslist

    })

  },
  edit: function(e) {
    var goods = this.data.goodslist[e.currentTarget.dataset.index];

    this.setData({
      isedit: true,
      currentEditGoods: goods,
      iseditwash: false
    })
  },
  editwash: function(e) {
    var goods = this.data.wash;

    this.setData({
      isedit: true,
      currentEditGoods: goods,
      currentedittype: goods.type,
      iseditwash: true
    })
  },
  closebtn: function(e) {
    this.setData({
      isedit: false
    })
  },
  savegoods: function(e) {

    // if (!(this.data.currentedittype > 0)) {
    //   wx.showModal({
    //     title: '提示',
    //     content: '请选择类别',
    //     showCancel: false,
    //     success: function (res) { }
    //   })
    //   return;
    // }
    if (!this.data.currentEditGoods.stock) {
      wx.showModal({
        title: '提示',
        content: '请输入库存',
        showCancel: false,
        success: function(res) {}
      })
      return;
    }
    // if (!this.data.currentEditGoods.original_price) {
    //   wx.showModal({
    //     title: '提示',
    //     content: '请输入原价',
    //     showCancel: false,
    //     success: function (res) { }
    //   })
    //   return;
    // }
    if (!this.data.currentEditGoods.price) {
      wx.showModal({
        title: '提示',
        content: '请输入促销价',
        showCancel: false,
        success: function(res) {}
      })
      return;
    }

    var that = this;

    var data = this.data.currentEditGoods;

    if (this.data.currentEditGoods.id > 0) {
      util.nmAjax({
        method: "put",
        url: getApp().globalData.apiURL + 'v1/goods/' + this.data.currentEditGoods.id,
        data: {
          price: data.price * 100,
          type: data.goods_type,
          describes: data.describes,
          stock: this.data.currentEditGoods.stock
        },
        success: res => {
          that.getgoodslist(that.data.currenttype);
          that.setData({
            isedit: false,
            currentEditGoods: {}
          });
          that.setwash();
        }
      })
    } else {
      util.nmAjax({
        method: "post",
        url: getApp().globalData.apiURL + 'v1/goods',
        data: data,
        success: res => {
          that.getgoodslist(that.data.currenttype);
          that.setData({
            isedit: false,
            currentEditGoods: {}
          });
          that.setwash();
        }
      })
    }


  },
  add: function() {

    this.setData({
      isedit: true,
      currentEditGoods: {},
      iseditwash: false,
      currentedittype: 0
    });
  },

  //手指触摸动作开始 记录起点X坐标

  touchwashstart: function(e) {
    this.data.wash.isTouchMove = false;

    this.setData({

      startX: e.changedTouches[0].clientX,

      startY: e.changedTouches[0].clientY,
      wash: this.data.wash
    })

  },

  //滑动事件处理

  touchwashmove: function(e) {

    var that = this,

      index = e.currentTarget.dataset.index, //当前索引

      startX = that.data.startX, //开始X坐标

      startY = that.data.startY, //开始Y坐标

      touchMoveX = e.changedTouches[0].clientX, //滑动变化坐标

      touchMoveY = e.changedTouches[0].clientY, //滑动变化坐标

      //获取滑动角度

      angle = that.angle({
        X: startX,
        Y: startY
      }, {
        X: touchMoveX,
        Y: touchMoveY
      });

    this.data.wash.isTouchMove = false

    //滑动超过30度角 return

    if (Math.abs(angle) > 30) return;


    if (touchMoveX > startX) { //右滑

      this.data.wash.isTouchMove = false

    } else //左滑
    {

      this.data.wash.isTouchMove = true
    }
    //更新数据

    that.setData({

      wash: that.data.wash

    })



  },
  editWashCard: function(e) {

    var goods = this.data.xichekalist[e.currentTarget.dataset.index];
    goods.price = goods.price/100
    this.setData({
      currentEditWashCard: goods,
      iseditWashCard: true
    })
  },
  closewashcardbtn: function(e) {
    this.data.currentEditWashCard.price = this.data.currentEditWashCard.price*100
    this.setData({
      currentEditWashCard: {},
      iseditWashCard: false
    })
  },
  editWashCardNumberbind: function(e) {
    this.setData({
      'currentEditWashCard.number': e.detail.value
    })
  },
  editWashCardPricebind: function(e) {
    console.log(e.detail.value)
    this.setData({
      'currentEditWashCard.price': e.detail.value
    })
  },
  saveeditWashCard: function(e) {
    var that = this;

    that.data.currentEditWashCard.price = that.data.currentEditWashCard.price * 100

    util.nmAjax({
      method: "put",
      url: getApp().globalData.apiURL + 'v1/wash/' + that.data.currentEditWashCard.id,
      data: that.data.currentEditWashCard,
      success: res => {
        that.fetchData();
        that.setData({
          iseditWashCard: false,
          currentEditWashCard: {}
        });
      }
    })
  },
  previewImage: function(e) {
    var current = e.target.dataset.src;
    var imglist = new Array();
    imglist.push(current);
    wx.previewImage({
      current: current, // 当前显示图片的http链接  
      urls: imglist // 需要预览的图片http链接列表  
    })
  },
  uppershelf: function(e) { //上架
    var goods = this.data.goodslist[e.currentTarget.dataset.index];
    if (goods.stock && goods.stock > 0) {
      var that = this;
      if (goods.shelf == 0) {
        util.nmAjax({
          method: "get",
          isshowToast: true,
          url: getApp().globalData.apiURL + 'v1/goods/' + goods.id + '?shelf=1',
          success: res => {
            that.getgoodslist(that.data.currenttype);
          }
        })
      } else {
        goods.shelf = 1;
        util.nmAjax({
          method: "put",
          isshowToast: true,
          url: getApp().globalData.apiURL + 'v1/goods/' + goods.id,
          data: goods,
          success: res => {
            that.getgoodslist(that.data.currenttype);
          }
        })
      }
    } else {
      wx.showModal({
        title: '提示',
        content: '库存为0，不能上架',
        showCancel: false,
        success: function(res) {}
      })
      return;
    }

  },
  lowershelf: function(e) {
    var goods = this.data.goodslist[e.currentTarget.dataset.index];
    var that = this;

    util.nmAjax({
      method: "get",
      isshowToast: true,
      url: getApp().globalData.apiURL + 'v1/goods/' + goods.id + '?shelf=0',
      success: res => {
        that.getgoodslist(that.data.currenttype);
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