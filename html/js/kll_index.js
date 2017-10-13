/**

 * Created by CLS on 2017/4/27.

 */

$(function () {

    var mySwiper = new Swiper(".swiper-box", {

        autoplay: 3000,//可选选项，自动滑动

        speed: 500,//滑动速率

        loop: true,//循环

        pagination : '.swiper-pagination',//分页器

        autoplayDisableOnInteraction : false,

        observer:true,//修改swiper自己或子元素时，自动初始化swiper

        observeParents:true,//修改swiper的父元素时，自动初始化swiper

    });



	 var swiper2 = new Swiper('.swiper-box02', {

        slidesPerView : 'auto',

        paginationClickable: true,

        spaceBetween: 10,

        loop:true,

        autoplay:3000,

        autoplayDisableOnInteraction : false,

        observer:true,//修改swiper自己或子元素时，自动初始化swiper

        observeParents:true,//修改swiper的父元素时，自动初始化swiper

    });

});

