<?php
/**
 * This file is part of the NEO ERP Application.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         PT. Lingkar Kreasi (Circle Creative)
 * @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */
// ------------------------------------------------------------------------

if ( ! function_exists('get_current_account')) {
    /**
     * get_current_account
     *
     * @return \ArrayObject
     */
    function get_current_account()
    {
        if (globals()->offsetExists('account')) {
            return globals()->offsetGet('account');
        }

        return new \ArrayObject([
            'id' => null,
            'username' => 'guest',
        ]);
    }
}

// ------------------------------------------------------------------------