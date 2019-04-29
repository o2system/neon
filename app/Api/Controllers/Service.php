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

namespace App\Api\Controllers;

// ------------------------------------------------------------------------

use App\Api\Http\Controller;

/**
 * Class Service
 * @package App\Api\Controllers
 */
class Service extends Controller
{
    /**
     * Service::index
     *
     * @throws \Exception
     */
    public function index()
    {
        $this->sendPayload('Hello World!');
    }
}