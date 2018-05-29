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

use App\Http\AccessControl\Middleware\UserAuthorization;
use App\Http\Controller;

/**
 * Class AuthorizedController
 *
 * @package App\Http\AccessControl\Controllers
 */
class AuthorizedController extends Controller
{
    /**
     * AuthorizedController::__construct
     */
    public function __reconstruct()
    {
        parent::__reconstruct();

        // Register user authentication middleware
        $this->middleware->register( new UserAuthorization() );
    }
}