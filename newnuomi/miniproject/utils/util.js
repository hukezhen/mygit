const formatTime = d => {
  var date = new Date(d * 1000)
  const year = date.getFullYear()
  const month = date.getMonth() + 1
  const day = date.getDate()
  const hour = date.getHours()
  const minute = date.getMinutes()
  const second = date.getSeconds()

  return [year, month, day].map(formatNumber).join('/') + ' ' + [hour, minute, second].map(formatNumber).join(':')
}

const formatData = d => {
  var date = new Date(d * 1000)
  const year = date.getFullYear()
  const month = date.getMonth() + 1
  const day = date.getDate()
  const hour = date.getHours()
  const minute = date.getMinutes()
  const second = date.getSeconds()

  return [year, month, day].map(formatNumber).join('/') + ' ' + [hour, minute].map(formatNumber).join(':')
}

const formatHourMiniuts = d => {
  var date = new Date(d * 1000)
  const year = date.getFullYear()
  const month = date.getMonth() + 1
  const day = date.getDate()
  const hour = date.getHours()
  const minute = date.getMinutes()
  const second = date.getSeconds()

  return [hour, minute].map(formatNumber).join(':')
}

const formatPrice = price => {
  return price / 100;
}

const getNumberColor = n => {
  if(n == null) {
    return 'xiaoxingche-chepai';
  }
  n = n.toString();

  if (n == '小型汽车')
    return 'xiaoxingche-chepai';
  if (n == '新能源车')
    return 'xinnengyuan-chepai';
  if (n == '大型汽车')
    return 'daxingche-chepai';
  if (n == '警车')
    return 'junyong-chepai';

  //junyong-chepai
  //dashiguan-chepai
  return 'xiaoxingche-chepai';
}

//取倒计时（天时分秒）
function getTimeLeft(datetimeTo) {
  // 计算目标与现在时间差（毫秒）
  let time1 = new Date(datetimeTo).getTime();
  let time2 = new Date().getTime();
  let mss = time1 - time2;

  // 将时间差（毫秒）格式为：天时分秒
  let days = parseInt(mss / (1000 * 60 * 60 * 24));
  let hours = parseInt((mss % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  let minutes = parseInt((mss % (1000 * 60 * 60)) / (1000 * 60));
  let seconds = parseInt((mss % (1000 * 60)) / 1000);

  return days + "天" + hours + "时" + minutes + "分" + seconds + "秒"
}
const nmAjax = option => {
  var timestamp = Date.parse(new Date());
  timestamp = timestamp / 1000;

  if (option.isshowToast) {
    wx.showToast({
      title: '正在加载',
      icon: 'loading',
      duration: 1000
    })
  }
  var header = {
    time: timestamp,
    random: 'SRzJxpspPYr8af5hTxFs5jMFXKxckix6',
    sign: 'M2EwMzNiYTdhYTNiZGFiZWZkOTgxMzE3ZmI5ZTVmMmQ5NGQ4ZDg3MzFmMDA3MWNhYWRhN2ZmZWEzZTdkNmUzZg==',
  }
  if (wx.getStorageSync('token')) {
    header.token = wx.getStorageSync('token')
  }
  wx.request({
    method: option.method,
    url: option.url,
    header: header,
    data: option.data,
    success: res => {
      wx.hideLoading();
      if (option.complete) {
        option.complete(res.data);
      }
      if (res.data.code == 1) {
        option.success(res.data)
      } else if (res.data.code == -40001) {
        wx.showToast({
          title: '登录失效，请刷新重试',
          icon: 'none',
          duration: 1500,
          mask: true,
          success: function(res) {
            wx.setStorageSync('uid', null)
            wx.setStorageSync('token', null)
            getApp().userLogin()
          },
        })
      } else if (res.data.code == 0) {
        wx.hideLoading();
        console.log(res.data.data.state);
        console.log(res.data.data.state);

        if (res.data.data.state) {
          wx.showModal({
            title: '提示',
            content: res.data.msg,
            showCancel: false,
            success: function () {
              if (res.data.data.state == 1) {
                wx.navigateTo({
                  url: '../logs/logs',
                })
              } else
                if (res.data.data.state == 4) {
                  wx.navigateTo({
                    url: '../mine/pages/mendianxinxi/mendianxinxi',
                  })
                }
            }
          })
        } else {
          wx.showToast({
            title: res.data.msg,
            icon: 'none',
            duration: 1500,
            mask: true,
          })
        }
      }
    }
  })
}

const formatNumber = n => {
  n = n.toString()
  return n[1] ? n : '0' + n
}


module.exports = {
  formatTime: formatTime,
  formatData: formatData,
  nmAjax: nmAjax,
  formatPrice: formatPrice,
  getNumberColor: getNumberColor,
  formatHourMiniuts: formatHourMiniuts,
  getTimeLeft: getTimeLeft
}