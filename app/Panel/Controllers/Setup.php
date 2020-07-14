<?php
/**
 * This file is part of the Circle Creative Web Application Project Boilerplate.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @author         PT. Lingkar Kreasi (Circle Creative)
 *  @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */
// ------------------------------------------------------------------------

namespace App\Panel\Controllers;

// ------------------------------------------------------------------------

use App\Panel\Http\Controller;
use O2System\Security\Authentication\JsonWebToken;

/**
 * Class Setup
 * @package App\Panel\Controllers
 */
class Setup extends Controller
{
    /**
     * Login::__reconstruct
     */
    public function __reconstruct()
    {
        parent::__reconstruct();

        presenter()->theme->setLayout('blank');
    }

    // ------------------------------------------------------------------------

    /**
     * Setup::index
     */
    public function index()
    {

        view('setup/index');
    }
}
