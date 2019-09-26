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

namespace App\Api\Modules\System\Controllers\Modules\Segments\Authorities;

// ------------------------------------------------------------------------

use App\Api\Modules\System\Http\Controller;

/**
 * Class Users
 * @package App\Api\Modules\System\Controllers\Modules\Segments\Authorities
 */
class Users extends Controller
{
    /**
     * Users::$fillableColumnsWithRules
     *
     * @var array
     */
    public $fillableColumnsWithRules = [
        [
            'field'    => 'id_sys_module_segment',
            'label'    => 'ID Sys Module Segment',
            'rules'    => 'required|integer',
            'messages' => 'ID Sys Module Segment cannot be empty and must be integer!',
        ],
        [
            'field'    => 'id_sys_user',
            'label'    => 'ID Sys User',
            'rules'    => 'required|integer',
            'messages' => 'ID Sys User cannot be empty and must be integer!',
        ],
        [
            'field'    => 'permission',
            'label'    => 'Permission',
            'rules'    => 'optional',
            'messages' => 'Permission cannot be empty! Examples DENIED, GRANTED, WRITE'
        ],
    ];

}
