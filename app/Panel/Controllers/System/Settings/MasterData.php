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

use App\Models\Master\Banks;
use App\Models\Master\Currencies;
use App\Models\Master\Countries;
use App\Panel\Controllers\System\Settings as Controller;
use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class MasterData
 * @package App\Panel\Controllers\Settings
 */
class MasterData extends Controller
{
    /**
     * MasterData::index
     */
    public function index()
    {
        $masters = [
            'banks' => new SplArrayObject([
                'label' => language('BANKS'),
                'numOfRecords' => models(Banks::class)->getNumOfRecords()
            ]),
            'currencies' => new SplArrayObject([
                'label' => language('CURRENCIES'),
                'numOfRecords' => models(Currencies::class)->getNumOfRecords()
            ]),
            'countries' => new SplArrayObject([
                'label' => language('Countries'),
                'numOfRecords' => models(Countries::class)->getNumOfRecords()
            ])
        ];

        view('system/settings/master-data/index', [
            'masters' => $masters
        ]);
    }

    // ------------------------------------------------------------------------
}
