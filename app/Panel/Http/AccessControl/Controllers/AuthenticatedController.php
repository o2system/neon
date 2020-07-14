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

use App\Panel\Http\AccessControl\Middleware\UserAuthentication;
use App\Panel\Http\Controller;

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
        middleware()->register(new UserAuthentication());
    }
}
