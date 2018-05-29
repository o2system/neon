<?php
/**
 * This file is part of the O2System Content Management System package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian
 * @copyright      Copyright (c) Steeve Andrian
 */
// ------------------------------------------------------------------------

namespace Personal\Controllers;

// ------------------------------------------------------------------------

use Personal\Http\Controller;

/**
 * Class Personal
 *
 * @package Personal\Controllers
 */
class Personal extends Controller
{
    /**
     * Profile::index
     */
    public function index()
    {
        ( new Profile() )->index();
    }
}