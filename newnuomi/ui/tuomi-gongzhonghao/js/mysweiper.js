var mySwiper = new Swiper('.swiper-container',{
    autoplay:true,
    pagination: {
        el: '.swiper-pagination',
    },
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    on:{
      init: function(){
        swiperAnimateCache(this); //隐藏动画元素 
        this.emit('slideChangeTransitionEnd');//在初始化时触发一次slideChangeTransitionEnd事件
      }, 
      slideChangeTransitionEnd: function(){ 
        swiperAnimate(this); //每个slide切换结束时运行当前slide动画
        this.slides.eq(this.activeIndex).find('.ani').removeClass('ani');//动画只展示一次
      }
    }
    });

$(".xianshi-xiche,.service-list li .service-img").click(function(){
    $(".bigimg").show();
    $(".bigimg .maskbox").show();
})
$(".bigimg").click(function(){
    $(".bigimg").hide();
    $(".bigimg .maskbox").hide();
})

function tabSwitch(tabtile,tebcontent){     
    $(tabtile).click(function () {
        //切换标签样式
        $(this).addClass('active').siblings().removeClass('active');
        //切换div显示隐藏
        $(tebcontent).eq($(this).index()).show().siblings().hide();
    });    
}
tabSwitch(".navlist a",".cardlistbox ul")