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

namespace App\Api\Modules\Members\Controllers;

// ------------------------------------------------------------------------

use App\Api\Modules\Members\Http\Controller;

/**
 * Class Metadata
 * @package App\Api\Modules\Members\Controllers
 */
class Metadata extends Controller
{
    /**
     * Metadata::$fillableColumnsWithRules
     *
     * @var array
     */

    public $fillableColumnsWithRules = [
        [
            'field'    => 'id_member',
            'label'    => 'Member Setting',
            'rules'    => 'required|integer',
            'messages' => 'Member metadata cannot be empty!',
        ],
        [
            'field'    => 'name',
            'label'    => 'Name',
            'rules'    => 'required',
            'messages' => 'Name cannot be empty!',
        ],
        [
            'field'    => 'content',
            'label'    => 'Content',
            'rules'    => 'required',
            'messages' => 'Content cannot be empty!',
        ]

    ];

}
