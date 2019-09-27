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

namespace App\Modules\Site\Controllers\Appearance;

// ------------------------------------------------------------------------

use App\Modules\Site\Http\Controller;

/**
 * Class Themes
 * @package App\Modules\Sites\Controllers
 */
class Themes extends Controller
{
	public function index()
    {
        view('appearance/themes/index');
    }
}