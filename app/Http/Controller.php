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

namespace App\Http;

// ------------------------------------------------------------------------
use O2System\Framework\Models\Sql\System\Modules;

/**
 * Class Controller
 *
 * @package App\Http
 */
class Controller extends \O2System\Framework\Http\Controller
{
    /**
     * Controller::__reconstruct
     */
    public function __reconstruct()
    {
        presenter()->meta->offsetSet('generator', 'O2System Nitro Boilerplate');

        if($app = models(Modules::class)->find(rtrim(globals()->app->getNamespace(), '\\'), 'namespace')) {
            globals()->app->id = $app->id;
            globals()->app->settings = $app->settings;
        }
    }
}