export default class Search {
    /**
     * Search.constructor
     */
    constructor() {
        //Post / Pages List
        $('.open-search').on('click', function(){
            $('.nav-search').addClass('open');
            $('.search-input input').focus();
        });

        $('.close-search').on('click', function (){
            $('.nav-search').removeClass('open');
        });
    }
}