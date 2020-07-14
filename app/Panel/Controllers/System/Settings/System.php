<?php
/**
 * This file is part of the O2System Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */

// ------------------------------------------------------------------------

namespace App\Panel\Controllers\System\Settings;

// ------------------------------------------------------------------------

use App\Panel\Controllers\System\Settings as Controller;

/**
 * Class System
 * @package App\Panel\Controllers\Settings
 */
class System extends Controller
{
    /**
     * System::index
     */
    public function index()
    {
        view('system/settings/system/index');
    }
}
