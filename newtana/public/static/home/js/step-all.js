

var clientHeight = $(window).height();
var stepBgHeight = $(window).height() - 70;
var customizedContentHeight = $(window).height() - 70 - 90;
var orderListHeight = $(window).height()-170
var orderDetailHeight = $(window).height()-278
$(".customized-step1-box").height(stepBgHeight)
$(".customized-content,.left-step,.step1-sweiper-box").height(customizedContentHeight)
$(".step-right-box").height(customizedContentHeight)
$(".orderlist-box .order-list-left .orderlist").height(orderListHeight)
$(".orderlist-box .order-detail-overflow").height(orderDetailHeight)
$(".yid-xieyi-overflow,.pc-xieyi-overflow").height(stepBgHeight)

// var marginTOP = clientHeight-200/2
var bottomHeight = $(".biaom-gongyi-box").height();
$(".step1-rgba-bg .step1-sweiper-box").height(customizedContentHeight-bottomHeight-20);
$(".step1-rgba-bg .step1-sweiper-box img").height(clientHeight-270);



$(".yingsi-btn").click(function(){
    $(".privacy").hide();
})

var sweiperHeit = $(".newseiperbox .swiper-slide img").height();
    var dddd = $(window).height();
    // alert(dddd)
    // $(".newseiperbox").css("height",dddd+"px")
    // alert(sweiperHeit)dddd

    var INDEX = 0;
    $(document).ready(function () {
        function sweiperYdOrPc(sweiperYdOrPc){
            var mySwiper = new Swiper(sweiperYdOrPc, {
                direction: 'horizontal',
                // loop: true,
                effect: 'fade',
                speed: 1000,
                pagination: {
                    el: '.swiper-pagination',
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                on: {
                    init: function () {
                        for (var i = 0; i < this.$el.find('.swiper-slide').length; i++) {
                            var item = this.$el.find('.swiper-slide').eq(i);
                            item.find('.desc_b_l').css({ 'height': item.find('.desc').css('height'), 'top': item.find('.desc').css('top'), 'left': item.find('.desc').css('left') });
                            item.find('.desc_b_t').css({ 'width': item.find('.desc').css('width'), 'top': item.find('.desc').css('top'), 'left': item.find('.desc').css('left') });
                            item.find('.desc_b_r').css({ 'height': item.find('.desc').css('height'), 'top': item.find('.desc').css('top'), 'right': item.find('.desc').css('right') });
                            item.find('.desc_b_b').css({ 'width': item.find('.desc').css('width'), 'bottom': parseFloat(item.css('height')) - (parseFloat(item.find('.desc').css('top')) + parseFloat(item.find('.desc').css('height'))) + 'px', 'left': item.find('.desc').css('left') });
                        }
                    },
                    slideChange: function () {

                        if (mySwiper) {
                            var active = $(mySwiper.$el.find('.swiper-slide-active'));
                            var speed = mySwiper.passedParams.speed;
                            if (INDEX < mySwiper.activeIndex) {
                                active.find('.desc_b_l').hide();
                                active.find('.desc_b_t').hide();
                                active.find('.desc_b_r').hide();
                                active.find('.desc_b_b').hide();
                                active.next().find('.desc_b_l').show();
                                active.next().find('.desc_b_t').show();
                                active.next().find('.desc_b_r').show();
                                active.next().find('.desc_b_b').show();
                                active.find('img').addClass('leave_img_next').siblings('.desc').css('border', '1px solid #ccc').addClass('leave_desc_next');

                                active.next().find('img').addClass('enter_img_next').siblings('.desc').css('border', 'none').addClass('enter_desc_next');
                                active.next().find('.desc_b_l').attr('animation-duration', speed + 's').addClass('fadeInDownBig');
                                active.next().find('.desc_b_t').attr('animation-duration', speed + 's').addClass('fadeInLeftBig');
                                active.next().find('.desc_b_r').attr('animation-duration', speed + 's').addClass('fadeInUpBig');
                                active.next().find('.desc_b_b').attr('animation-duration', speed + 's').addClass('fadeInRightBig');
                                setTimeout(function () {
                                    active.find('.desc_b_l').show();
                                    active.find('.desc_b_t').show();
                                    active.find('.desc_b_r').show();
                                    active.find('.desc_b_b').show();
                                    active.find('img').removeClass('leave_img_next').siblings('.desc').css('border', 'none').removeClass('leave_desc_next');
                                    active.next().find('img').removeClass('enter_img_next').siblings('.desc').removeClass('enter_desc_next');
                                    active.next().find('img').removeClass('leave_img_next').siblings('.desc').removeClass('leave_desc_next');
                                    active.next().find('.desc_b_l').removeClass('fadeInDownBig');
                                    active.next().find('.desc_b_t').removeClass('fadeInLeftBig');
                                    active.next().find('.desc_b_r').removeClass('fadeInUpBig');
                                    active.next().find('.desc_b_b').removeClass('fadeInRightBig');
                                }, speed)
                            } else {
                                active.find('.desc_b_l').show();
                                active.find('.desc_b_t').show();
                                active.find('.desc_b_r').show();
                                active.find('.desc_b_b').show();
                                active.prev().find('.desc_b_l').hide();
                                active.prev().find('.desc_b_t').hide();
                                active.prev().find('.desc_b_r').hide();
                                active.prev().find('.desc_b_b').hide();
                                active.find('.desc_b_l').attr('animation-duration', speed + 's').addClass('fadeOutDownBig');
                                active.find('.desc_b_t').attr('animation-duration', speed + 's').addClass('fadeOutLeftBig');
                                active.find('.desc_b_r').attr('animation-duration', speed + 's').addClass('fadeOutUpBig');
                                active.find('.desc_b_b').attr('animation-duration', speed + 's').addClass('fadeOutRightBig');
                                active.find('img').addClass('leave_img_prev').siblings('.desc').css('border', 'none').addClass('leave_desc_prev');

                                active.prev().find('.desc_b_l').hide();
                                active.prev().find('.desc_b_t').hide();
                                active.prev().find('.desc_b_r').hide();
                                active.prev().find('.desc_b_b').hide();
                                active.prev().find('img').addClass('enter_img_prev').siblings('.desc').css('border', '1px solid #ccc').addClass('enter_desc_prev');
                                setTimeout(function () {
                                    active.prev().find('.desc_b_l').show();
                                    active.prev().find('.desc_b_t').show();
                                    active.prev().find('.desc_b_r').show();
                                    active.prev().find('.desc_b_b').show();
                                    active.find('.desc_b_l').removeClass('fadeOutDownBig');
                                    active.find('.desc_b_t').removeClass('fadeOutLeftBig');
                                    active.find('.desc_b_r').removeClass('fadeOutUpBig');
                                    active.find('.desc_b_b').removeClass('fadeOutRightBig');
                                    active.find('img').removeClass('leave_img_prev').siblings('.desc').css('border', 'none').removeClass('leave_desc_prev');

                                    active.prev().find('img').removeClass('enter_img_prev').siblings('.desc').css('border', 'none').removeClass('enter_desc_prev');
                                }, speed)
                            }
                            INDEX = mySwiper.activeIndex;
                        }
                    }
                },
            });        
        }

       sweiperYdOrPc('.newseiperbox')
       sweiperYdOrPc('.yd-newseiperbox')

    })