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
 * Class Sections
 * @package App\Api\Modules\Posts\Controllers
 */
class Sections extends Controller
{
    /**
     * Section::$fillableColumnsWithRules
     *
     * @var array
     */
    public $fillableColumnsWithRules = [
        [
            'field'    => 'title',
            'label'    => 'Title',
            'rules'    => 'required|alphanumericspaces',
            'messages' => 'Title cannot be empty and it shouldn\'t have @-.$*()+;~:\'/%_?,=&!',
        ],
        [
            'field'    => 'slug',
            'label'    => 'Slug',
            'rules'    => 'required|alphadash',
            'messages' => 'Slug cannot be empty! the examples are like this = slug-slug-slug',
        ],
        [
            'field'    => 'description',
            'label'    => 'Description',
            'rules'    => 'optional',
        ],
        [
            'field'    => 'image',
            'rules'    => 'optional',
        ],
        [
            'field'    => 'visibility',
            'rules'    => 'optional',
        ],
    ];

}
