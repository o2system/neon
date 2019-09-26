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

namespace App\Api\Modules\Posts\Controllers;

// ------------------------------------------------------------------------

use App\Api\Modules\Posts\Http\Controller;

/**
 * Class Settings
 * @package App\Api\Modules\Posts\Controllers
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
            'field'    => 'id_post',
            'label'    => 'ID Post',
            'rules'    => 'required|integer',
            'messages' => 'ID Post cannot be empty and must be integer',
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
            'rules'    => 'optional',
        ],
    ];

}
