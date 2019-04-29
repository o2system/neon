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

namespace App\Api\Modules\System\Controllers;

// ------------------------------------------------------------------------

use App\Api\Http\Controller;
use App\Api\Modules\System\Models;

/**
 * Class Modules
 * @package App\Api\Modules\System\Controllers
 */
class Modules extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->model = new Models\Modules();
    }
}