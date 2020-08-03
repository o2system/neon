//Active Href
var currentUrl = window.location.href;
var activeSlug = $('a[href="'+ currentUrl +'"]');
activeSlug.addClass('active');

$('.nav-pages:not(.nav-pages-tab)').on('click', '.nav-link', function(){
    $('.nav-pages').find('.nav-link').removeClass('active');
    $(this).addClass('active');
});