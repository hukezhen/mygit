const util = require('../../utils/util.js')
Page({
  data: {
    id:0,
    phone:''
  },
  onLoad(options) {
    var info = wx.getSystemInfoSync();
    console.log(info.SDKVersion)
    this.setData({
      canUse: getApp().compareVersion(info.SDKVersion, '2.7.9')
    })
    if(options.id && options.phone){
      this.setData({
        id:options.id,
        phone:options.phone
      })
      this.ctx = wx.createCameraContext()
    }
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
          plate: '识别中....'
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
          success: function (res) {

            var resdata = JSON.parse(res.data);

            var imgUrl = resdata.data.imgUrl


            util.nmAjax({
              method: "post",
              url: getApp().globalData.apiURL + 'api/v1/car/vin',
              data: {
                image: imgUrl,
                id: that.data.id,
                phone: that.data.phone
              },
              success: res => {
                that.setData({
                  plate: '扫描成功'
                });
                var pages = getCurrentPages();
                var beforePage = pages[pages.length - 2];
                beforePage.fetchData(that.data.id);
                wx.navigateBack({
                  delta: 1,
                })
                console.log(JSON.stringify(res));
                //"code":1,"msg":"信息完善成功","time":1569402452,"data":{"error_code":0,"reason":"成功","result":{"LevelId":"","Vin":"LFV2A21K6A3092399","VINNF":"2010","CJMC":"一汽大众","PP":"大众","CX":"速腾","PL":"1.4","FDJXH":"CFB","BSQLX":"自动","DWS":"7","PFBZ":"国4","CLDM":"FV7146TATG","SSNF":"2010","TCNF":"---","ZDJG":"16.28","NLevelID":"","SSYF":"9","SCNF":"2010","NK":"2010","CXI":"速腾","XSMC":"1.4TSI 双离合 技术版","CLLX":"轿车","JB":"紧凑型车","CSXS":"三厢","CMS":"四门","ZWS":"5","GL":"96","RYLX":"汽油","BSQMS":"双离合变速器(DSG)","RYBH":"93#","QDFS":"前轮驱动","FDJGS":"4"}}}
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
  lightAct() {
    this.setData({
      light: !this.data.light
    })
  }
})