//Post / Pages List
$('.open-search').on('click', function(){
    $('.nav-search').addClass('open');
    $('.search-input input').focus();
});

$('.close-search').on('click', function (){
    $('.nav-search').removeClass('open');
});

$(document).on('click', '[data-toggle="search"]', function(e){
    e.preventDefault();
    $('.card-search').toggleClass('open');
});