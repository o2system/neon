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

namespace App\Panel\Controllers\Appearance;

// ------------------------------------------------------------------------

use App\Panel\Http\Controller;

/**
 * Class Customize
 * @package App\Panel\Controllers
 */
class Customize extends Controller
{
    /**
     * Customize::index
     */
    public function index()
    {
        presenter()->theme->setLayout('customizer');

        view('appearance/customize/index');
    }
}
