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
 * Class Recurrences
 * @package App\Api\Modules\Posts\Controllers
 */
class Recurrences extends Controller
{
    /**
     * Media::$fillableColumnsWithRules
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
            'field'    => 'repeat_start',
            'label'    => 'Repeat Start',
            'rules'    => 'optional|integer',
            'messages' => 'Repeat Start cannot be empty and must be integer!',
        ],
        [
            'field'    => 'repeat_end',
            'label'    => 'Repeat End',
            'rules'    => 'optional|integer',
            'messages' => 'Repeat End cannot be empty and must be integer!',
        ],
    ];

}
