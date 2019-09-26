/*
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 *  @copyright      Copyright (c) Steeve Andrian Salim
 */
// ------------------------------------------------------------------------

import $ from "jquery";

/**
 * Class Search
 */
class Search {
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

export default new Search();