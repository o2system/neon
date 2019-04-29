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

namespace App\Http\AccessControl\Controllers;

// ------------------------------------------------------------------------

use App\Http\AccessControl\Middleware\UserAuthentication;
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
        middleware()->register( new UserAuthorization() );
        middleware()->register( new UserAuthentication() );
    }
}