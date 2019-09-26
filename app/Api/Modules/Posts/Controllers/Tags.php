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
 * Class Tags
 * @package App\Api\Modules\Posts\Controllers
 */
class Tags extends Controller
{
    /**
     * Tags::$fillableColumnsWithRules
     *
     * @var array
     */
    public $fillableColumnsWithRules = [
        [
            'field'    => 'name',
            'label'    => 'Name',
            'rules'    => 'required|alphanumericspaces',
            'messages' => 'Name cannot be empty and it shouldn\'t have @-.$*()+;~:\'/%_?,=&!',
        ],
    ];

}
