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

namespace App\Api\Modules\Pages\Controllers;

// ------------------------------------------------------------------------

use App\Api\Modules\Pages\Http\Controller;

/**
 * Class Settings
 * @package App\Api\Modules\Pages\Controllers
 */
class Settings extends Controller
{
    /**
     * Settings::$fillableColumnsWithRules
     *
     * @var array
     */

    public $fillableColumnsWithRules = [
        [
            'field'    => 'id_page',
            'label'    => 'Page Id',
            'rules'    => 'required|integer',
            'messages' => 'Page id cannot be empty!',
        ],
        [
            'field'    => 'key',
            'label'    => 'Key',
            'rules'    => 'required',
            'messages' => 'Key cannot be empty!',
        ],
        [
            'field'    => 'value',
            'label'    => 'Value',
            'rules'    => 'required',
            'messages' => 'Value cannot be empty!',
        ],

    ];

}
