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
 * Class Segments
 * @package App\Api\Modules\System\Controllers\Modules
 */
class Segments extends Controller
{
    /**
     * Users::$fillableColumnsWithRules
     *
     * @var array
     */
    public $fillableColumnsWithRules = [
        [
            'field'    => 'id_sys_module',
            'label'    => 'ID Sys Module',
            'rules'    => 'required|integer',
            'messages' => 'ID Sys Module cannot be empty and it has to be integer!',
        ],
        [
            'field'    => 'name',
            'label'    => 'Name',
            'rules'    => 'required|alphanumericspaces',
            'messages' => 'Name cannot be empty and it shouldn\'t have @-.$*()+;~:\'/%_?,=&!',
        ],
        [
            'field'    => 'uri',
            'label'    => 'URI',
            'rules'    => 'required',
            'messages' => 'URI cannot be empty!',
        ],
        [
            'field'    => 'slug',
            'label'    => 'Slug',
            'rules'    => 'required|alphadash',
            'messages' => 'Slug cannot be empty! the examples are like this = slug-slug-slug',
        ],
        [
            'field'    => 'class',
            'label'    => 'Class',
            'rules'    => 'required',
            'messages' => 'Class cannot be empty!',
        ],
    ];

}
