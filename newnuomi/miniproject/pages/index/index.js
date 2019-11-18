const util = require('../../utils/util.js')
const timer = require('../../utils/wxTimer.js')
Page({
  data: {
    isReceive:false,
    taskList:[],
    currentTask: {},
    storeInfo: {},
  },
  onLoad: function () {
    var that = this;
    
    util.nmAjax({
      method: "get",
      url: getApp().globalData.apiURL + 'v1/activity',
      success: res => {
       
        var list = res.data;

        if (list.length > 0) {

          for (let j = 0; j < list.length; j++) {

            if (list[j].start_time != null)
              list[j].start_time = util.formatTime(list[j].start_time, 'h:m:s'); 
              
            if (list[j].end_time != null)
              list[j].end_time = util.formatTime(list[j].end_time, 'h:m:s');
            if (list[j].price != null)
              list[j].price = util.formatPrice(list[j].price);
          }
        }
        that.setData({
          taskList: list
        })
        this.countDown();
      }
    })

    util.nmAjax({
      method: "get",
      url: getApp().globalData.apiURL + 'v1/store/' + getApp().globalData.storeId,
      data: {

      },
      success: res => {
        res.data.activity_moneys =util.formatPrice(res.data.activity_moneys);
        that.setData({
          storeInfo: res.data
        })

      }
    })
  },
  startTimeTap: function () {

    this.data.timer = setInterval(() => { //注意箭头函数！！
      var list = this.data.taskList;
      
      for (let j = 0; j < list.length; j++) {

        if (list[j].end_time != null)
          list[j].end_time = util.formatTime(list[j].end_time, 'h:m:s');

      }
      this.setData({
        taskList: list
      })
      if (this.data.timeLeft == "0天0时0分0秒") {
        clearInterval(this.data.timer);
      }
    }, 1000);
  },
  countDown() {//倒计时函数
    // 获取当前时间，同时得到活动结束时间数组
    let newTime = new Date().getTime();
    let endTimeList = this.data.taskList;
    let countDownArr = [];

    // 对结束时间进行处理渲染到页面
    endTimeList.forEach(o => {
      let endTime = new Date(o.end_time).getTime();
      let obj = null;
      // 如果活动未结束，对时间进行处理
      if (endTime - newTime > 0) {
        let time = (endTime - newTime) / 1000;
        // 获取天、时、分、秒
        let day = parseInt(time / (60 * 60 * 24));
        let hou = parseInt(time % (60 * 60 * 24) / 3600);
        let min = parseInt(time % (60 * 60 * 24) % 3600 / 60);
        let sec = parseInt(time % (60 * 60 * 24) % 3600 % 60);
        obj = {
          day: this.timeFormat(day),
          hou: this.timeFormat(hou),
          min: this.timeFormat(min),
          sec: this.timeFormat(sec)
        }
      } else {//活动已结束，全部设置为'00'
        obj = {
          day: '00',
          hou: '00',
          min: '00',
          sec: '00'
        }
      }
      o.endtime = obj.day+'天'+obj.hou+'时'+obj.min+'分'+obj.sec;
      countDownArr.push(o);
    })
    // 渲染，然后每隔一秒执行一次倒计时函数
    this.setData({ taskList: countDownArr })
    setTimeout(this.countDown, 1000);
  },
  timeFormat(param) {//小于10的格式化函数
    return param < 10 ? '0' + param : param;
  },
  showReceive:function(e){
    const index = e.currentTarget.dataset.index;
    let list = this.data.taskList;
    let task = list[index];
    this.setData({
      isReceive: true,
      currentTask: task,
    })
  },
  close:function(e){
    this.setData({
      isReceive: false,
      currentTask: {}
    })
  },
  receiveAction:function(e){
    var receiveType = e.currentTarget.dataset.receivetype;
    util.nmAjax({
      method: "POST",
      url: getApp().globalData.apiURL + 'v1/moneys_activity',
      data: {
        aid: this.data.currentTask.id,
        type: receiveType
      },
      success: res => {
        debugger;

      }
    })
  }
})
