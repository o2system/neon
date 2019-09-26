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

namespace App\Api\Modules\Media\Controllers;

// ------------------------------------------------------------------------

use App\Api\Modules\Media\Http\Controller;

/**
 * Class Media
 * @package App\Api\Modules\Media\Controllers
 */
class Media extends Controller
{
    /**
     * Media::$fillableColumnsWithRules
     *
     * @var array
     */
    public $fillableColumnsWithRules = [
        [
            'field'    => 'label',
            'label'    => 'Label',
            'rules'    => 'optional|integer',
            'messages' => 'Label cannot be empty!',
        ],
        [
            'field'    => 'filename',
            'label'    => 'Filename',
            'rules'    => 'required',
            'messages' => 'Filename name cannot be empty!',
        ],
        [
            'field'    => 'mime',
            'label'    => 'MIme',
            'rules'    => 'required',
            'messages' => 'Mime cannot be empty!',
        ]
    ];

}
