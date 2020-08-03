const leftSidebar = $('.aside-left');
const leftSidebarToggle = $('[data-toggle="aside-left"]');

if ($(window).width() < 991) {
    $('.nitro-admin').removeClass('mini-aside-left');
    leftSidebarToggle.on('click', function () {
        $('.nitro-admin').toggleClass('open-aside-left');
    });
} else {
    leftSidebarToggle.on('click', function () {
        $('.nitro-admin').toggleClass('mini-aside-left');
    });
}
$(".aside-left").hover(function(){
    $(".header-left").addClass("expand-logo")
},function(){
    $(".header-left").removeClass("expand-logo")
});