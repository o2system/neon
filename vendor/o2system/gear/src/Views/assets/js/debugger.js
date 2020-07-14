/*
 * This file is part of the O2System Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */
// ------------------------------------------------------------------------
(function () {
    var pre = document.getElementsByTagName('pre'),
        preLen = pre.length;
    for (var i = 0; i < preLen; i++) {
        pre[i].innerHTML = '<span class="line-number"></span>' + pre[i].innerHTML + '<span class="clear-both"></span>';
        var linesNum = pre[i].innerHTML.split(/\n/).length;
        for (var number = 0; number < linesNum; number++) {
            var lineNumber = pre[i].getElementsByTagName('span')[0];
            lineNumber.innerHTML += '<span>' + (number + 1) + '</span>';
        }
    }
})();