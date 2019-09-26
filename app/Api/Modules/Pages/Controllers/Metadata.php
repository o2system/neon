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
 * Class Metadata
 * @package App\Api\Modules\Pages\Controllers
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
            'field'    => 'id_page',
            'label'    => 'Member Setting',
            'rules'    => 'required|integer',
            'messages' => 'Page Metadata cannot be empty!',
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
