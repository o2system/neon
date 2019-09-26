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

namespace App\Manage\Modules\Site\Controllers\Appearance;

// ------------------------------------------------------------------------

use App\Manage\Modules\Site\Http\Controller;

/**
 * Class Themes
 * @package App\Manage\Modules\Sites\Controllers
 */
class Themes extends Controller
{
	public function index()
    {
        view('appearance/themes/index');
    }
}