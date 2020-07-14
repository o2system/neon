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

namespace App\Panel\Controllers\System;

// ------------------------------------------------------------------------

use App\Models\Master\Countries;
use App\Panel\Http\AccessControl\Controllers\AuthorizedController;
use O2System\Framework\Models\Options;
use O2System\Framework\Models\Sql\System\Modules;
use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class Settings
 * @package App\Panel\Controllers\System
 */
class Settings extends AuthorizedController
{
    public function __reconstruct()
    {
        parent::__reconstruct();

        presenter()->offsetSet('tabs', function () {
            return view('system/settings/components/tabs', [], true);
        });
    }

    /**
     * Settings::index
     */
    public function index()
    {
        $module = models(Modules::class)->find('App\\Site', 'namespace');

        view('system/settings/site', [
            'options' => new SplArrayObject([
                'countries' => models(Countries::class)->all(),
                'languages' => models(Options::class)->languages(),
                'timezones' => models(Options::class)->timezones(),
            ]),
            'post' => $module->settings
        ]);
    }

    // ------------------------------------------------------------------------

    /**
     * Settings::process
     *
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     */
    public function process()
    {
        if (models(Modules::class)->update(input()->post(), [
            'namespace' => 'App\\Site'
        ])) {
            redirect_url(input()->server('HTTP_REFERER'));
        } else {
            print_out(models(Modules::class)->db->getQueries());
        }
    }
}
