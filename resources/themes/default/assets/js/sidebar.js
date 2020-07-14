const leftSidebar = $('.sidebar-left');
const leftSidebarToggle = $('[data-toggle="sidebar-left"]');

if ($(window).width() < 991) {
    $('.venus-admin').removeClass('mini-sidebar-left');
    leftSidebarToggle.on('click', function () {
        $('.venus-admin').toggleClass('open-sidebar-left');
    });
} else {
    leftSidebarToggle.on('click', function () {
        $('.venus-admin').toggleClass('mini-sidebar-left');
    });
}
$(".sidebar-left").hover(function(){
    $(".header-left").addClass("expand-logo")
},function(){
    $(".header-left").removeClass("expand-logo")
});