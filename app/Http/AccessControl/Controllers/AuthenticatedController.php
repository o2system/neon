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

namespace App\Http\AccessControl\Controllers;

// ------------------------------------------------------------------------

use App\Http\AccessControl\Middleware\UserAuthentication;
use App\Http\Controller;

/**
 * Class AuthenticatedController
 *
 * @package App\Http\AccessControl\Controllers
 */
class AuthenticatedController extends Controller
{
    /**
     * AuthenticatedController::__construct
     */
    public function __reconstruct()
    {
        parent::__reconstruct();

        // Register user authentication middleware
        $this->middleware->register( new UserAuthentication() );
    }
}