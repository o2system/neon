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

namespace App\Controllers;

// ------------------------------------------------------------------------

use App\Http\AccessControl\Controllers\AuthorizedController;

/**
 * Class Dashboard
 * @package App\Controllers
 */
class Dashboard extends AuthorizedController
{
    /**
     * Dashboard::index
     */
    public function index()
    {
        view('dashboard/index');
    }
}