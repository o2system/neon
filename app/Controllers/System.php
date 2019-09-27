<?php
/**
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */
// ------------------------------------------------------------------------

namespace App\Controllers;

// ------------------------------------------------------------------------

use App\Http\Controller;

/**
 * Class System
 *
 * @package App\Controllers
 */
class System extends Controller
{
    /**
     * Index
     */
    public function index()
    {
        presenter()->page->setHeader( 'System' );

        view('system/information', [
            'configurations' => config(),
        ], true);
    }
}