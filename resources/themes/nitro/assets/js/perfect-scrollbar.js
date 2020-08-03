import PerfectScrollbar from 'perfect-scrollbar';
window.PerfectScrollbar = PerfectScrollbar;

$(".perfect-scrollbar").each(function(){
    const ps = new PerfectScrollbar($(this)[0]);
});