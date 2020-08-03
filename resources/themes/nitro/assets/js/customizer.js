/*
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Teguh Rianto
 *  @copyright      Copyright (c) Teguh Rianto
 */

import "jquery-slinky/dist/slinky.min.js";

class Customizer {
    /**
     * Customizer.constructor
     */
    constructor() {
        //Customizer Slinky Menu
        $('#customize-menu').slinky({
            title: true,
            theme: 'customize-menu'
        });

        //Customize Layout
        $("body").trigger("resize");

        $.jResize = function (options) {

            // jResize default options for customisation, ViewPort size, Background Color and Font Color
            $.jResize.defaults = {
                viewPortSizes: ["320px", "480px", "540px", "600px", "768px", "960px", "1024px", "1280px"],
                backgroundColor: '444',
                fontColor: 'FFF'
            }

            options = $.extend({}, $.jResize.defaults, options);

            // Variables
            /*var resizer        = '<div class="viewports" style="position:fixed;top:0;left:0;right:0;overflow:auto;z-index:9999;background:#'
                    + options.backgroundColor + ';color:#' + options.fontColor + ';box-shadow:0 0 3px #222;"><ul class="viewlist">'
                        + '</ul><div style="clear:both;"></div></div>';*/

            var viewPortWidths = options.viewPortSizes;

            var viewPortList = '';

            /*var addressBar     = '<div class="address-bar"><div class="input-group"><input id="address-bar" type="text" class="form-control" placeholder="http://" value="http://localhost/projects/circle-creative.com"><span class="input-group-btn"><button class="btn" type="button">Go!</button></span></div></div>';*/

            // Wrap all HTML inside the <body>
            $('.main-customize').wrapInner('<div id="resizer" />');

            // Insert our resizing plugin
            //$('#resizer').before(resizer);

            // Loop through the array, using the each to dynamically generate our ViewPort lists
            $.each(viewPortWidths, function (go, className) {
                //$('.viewlist').append($('<li class="' + className + '"' + '>' + className + '</li>'));
                $('.' + className + '').click(function () {
                    $('#resizer').animate({
                        width: '' + className + ''
                    }, 300);

                    $('.active').removeClass('active');
                    $(this).addClass('active');
                });
            });

            // Prepend address bar
            //$('.viewports').prepend(addressBar);

            // Prepend our Reset button
            // $('.viewlist').prepend('<li class="reset" style="' + viewPortList + '">Reset</li>');

            // Slidedown the viewport navigation and animate the resizer
            var height = $('.viewlist').outerHeight();
            $('.viewports').hide().slideDown('300');
            $('#resizer').css({ margin: '0 auto' }).animate({ marginTop: height });

            // Allow for Reset
            $('.reset').click(function () {
                $('#resizer').css({
                    width: 'auto'
                });
            });
        };

        $.jResize({
            viewPortSizes: ['240px', '320px', '480px', '600px', '768px', '800px', '1024px', '1136px', '1152px', '1280px', '1366px'], // ViewPort Widths
            backgroundColor: '444', // HEX Code
            fontColor: 'FFF' // HEX Code
        });
    }
}

export default new Customizer();