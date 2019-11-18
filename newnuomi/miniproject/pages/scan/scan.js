const util = require('../../utils/util.js')
Page({
  data: {
    prefix: getApp().globalData.apiURL,
    qrcode: null,
    light: false
  },
  onLoad() {

    var info = wx.getSystemInfoSync();
    console.log(info.SDKVersion)
    


    // wx.navigateTo({ url: '../lpr/pages/kehu-shuxing/kehu-shuxing?id=' + 10})
    this.ctx = wx.createCameraContext();

    // qrcode
    util.nmAjax({
      method: "get",
      url: getApp().globalData.apiURL + 'v1/setting',
      data: {},
      success: res => {
        console.log(res)
        this.setData({
          qrcode: res.data.qr_code,
          canUse: getApp().compareVersion(info.SDKVersion, '2.7.9')
        })
      }
    })
  },

  takePhoto() {
    this.ctx.takePhoto({
      quality: 'normal',
      success: (res) => {
        console.log('照片拍摄成功')
        console.log('照片路径' + res.tempImagePath);
        var that = this;
        var localPath = res.tempImagePath;
        that.setData({
          plate: '识别中...'
        });
        var timestamp = Date.parse(new Date());
        timestamp = timestamp / 1000;

        wx.uploadFile({
          url: getApp().globalData.apiURL + "v1/upload",
          header: {
            time: timestamp,
            random: 'SRzJxpspPYr8af5hTxFs5jMFXKxckix6',
            sign: 'M2EwMzNiYTdhYTNiZGFiZWZkOTgxMzE3ZmI5ZTVmMmQ5NGQ4ZDg3MzFmMDA3MWNhYWRhN2ZmZWEzZTdkNmUzZg==',
            token: getApp().globalData.token
          },
          filePath: res.tempImagePath,
          name: 'file',
          success: function(res) {


            var resdata = JSON.parse(res.data);

            var imgUrl = resdata.data.imgUrl


            util.nmAjax({
              method: "post",
              url: getApp().globalData.apiURL + 'api/v1/car/number',
              data: {
                configure: "{\"multi_crop\":false}",
                image: imgUrl
              },
              success: res => {

                if (res.code == 0) {} else {
                  wx.navigateTo({
                    url: '../lpr/pages/kehu-shuxing/kehu-shuxing?id=' + res.data.id
                  })
                }
              },
              complete: res => {
                that.setData({
                  plate: ''
                });
              }
            })
          }
        });

      }
    })
  },
  error(e) {
    console.log(e.detail)
  },
  saveAct() {
    var that = this
    //判断用户是否授权"保存到相册"
    wx.getSetting({
      success(res) {
        //没有权限，发起授权
        if (!res.authSetting['scope.writePhotosAlbum']) {
          wx.authorize({
            scope: 'scope.writePhotosAlbum',
            success() { //用户允许授权，保存图片到相册
              savePhoto();
            },
            fail() { //用户点击拒绝授权，跳转到设置页，引导用户授权
              wx.openSetting({
                success() {
                  wx.authorize({
                    scope: 'scope.writePhotosAlbum',
                    success() {
                      savePhoto();
                    }
                  })
                }
              })
            }
          })
        } else { //用户已授权，保存到相册
          savePhoto()
        }
      }
    })

    function savePhoto() {

      var url = that.data.prefix + that.data.qrcode

      wx.getImageInfo({
        src: url,
        success(sres) {
          console.log(sres)
          wx.saveImageToPhotosAlbum({
            filePath: sres.path,
            success: function(fres) {
              //console.log(fres);
              console.log(fres)
              wx.showToast({
                title: '保存成功',
                duration: 1500,
                mask: true,
              })
            }
          })
        }
      })
    }
  },
  lightAct() {
    console.log(1212)
    this.setData({
      light: !this.data.light
    })
  }
})