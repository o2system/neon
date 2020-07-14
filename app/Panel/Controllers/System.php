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

namespace App\Panel\Controllers;

// ------------------------------------------------------------------------

use App\Panel\Http\AccessControl\Controllers\AuthorizedController;

/**
 * Class System
 * @package App\Panel\Controllers
 */
class System extends AuthorizedController
{
    public function __reconstruct()
    {
        parent::__reconstruct();

        presenter()->offsetSet('tabs', function(){
            return view('system/information/components/tabs', [], true);
        });
    }
}