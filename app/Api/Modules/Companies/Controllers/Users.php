<?php
/**
 * This file is part of the Circle Creative Web Application Project Boilerplate.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @author         PT. Lingkar Kreasi (Circle Creative)
 *  @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */
// ------------------------------------------------------------------------

namespace App\Api\Modules\Companies\Controllers;

// ------------------------------------------------------------------------

use App\Api\Modules\Companies\Http\Controller;

/**
 * Class Users
 * @package App\Api\Modules\Companies\Controllers
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
            'field'    => 'id_company',
            'label'    => 'Company id',
            'rules'    => 'required|integer',
            'messages' => 'Company ID cannot be empty and must be integer!',
        ],
        [
            'field'    => 'id_sys_user',
            'label'    => 'User id',
            'rules'    => 'required|integer',
            'messages' => 'User ID cannot be empty and must be integer!',
        ]
    ];

}
