<?php
/**
 * This file is part of the WebIn Platform.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @author         PT. Lingkar Kreasi (Circle Creative)
 *  @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */
// ------------------------------------------------------------------------

namespace App\Panel\Http\AccessControl\Controllers;

// ------------------------------------------------------------------------

use App\Panel\Http\Controller;
use App\Panel\Http\AccessControl\Middleware\UserAuthentication;
use App\Panel\Http\AccessControl\Middleware\UserAuthorization;

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
        middleware()->register(new UserAuthorization());
    }
}
