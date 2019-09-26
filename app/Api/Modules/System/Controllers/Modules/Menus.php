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

namespace App\Api\Modules\System\Controllers\Modules;

// ------------------------------------------------------------------------

use App\Api\Modules\System\Http\Controller;

/**
 * Class Menus
 * @package App\Api\Modules\System\Controllers\Modules
 */
class Menus extends Controller
{
    public $fillableColumnsWithRules = [
        [
            'field'    => 'id_sys_module',
            'label'    => 'ID Sys Module',
            'rules'    => 'required|integer',
            'messages' => 'ID Sys Module cannot be empty and it has to be integer!',
        ],
        [
            'field'    => 'position',
            'label'    => 'Position',
            'rules'    => 'optional',
        ],
        [
            'field'    => 'label',
            'label'    => 'Label',
            'rules'    => 'optional',
        ],
        [
            'field'    => 'description',
            'label'    => 'Description',
            'rules'    => 'optional',
        ],
        [
            'field'    => 'href',
            'label'    => 'HREF',
            'rules'    => 'optional',
        ],
        [
            'field'    => 'attributes',
            'label'    => 'Attributes',
            'rules'    => 'optional',
        ],
        [
            'field'    => 'settings',
            'label'    => 'Settings',
            'rules'    => 'optional',
        ],
        [
            'field'    => 'metadata',
            'label'    => 'Metadata',
            'rules'    => 'optional',
        ],
    ];

}
