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
 * Class Media
 * @package App\Api\Modules\Posts\Controllers
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
            'field'    => 'id_post',
            'label'    => 'ID Post',
            'rules'    => 'required|integer',
            'messages' => 'ID Post cannot be empty and must be integer',
        ],
        [
            'field'    => 'id_media',
            'label'    => 'ID Media',
            'rules'    => 'required|integer',
            'messages' => 'ID media cannot be empty and must be integer!',
        ],
        [
            'field'    => 'default',
            'label'    => 'Default',
            'rules'    => 'required',
            'messages' => 'Default cannot be empty and must be integer and value should be YES or NO!',
        ],
    ];
<<<<<<< HEAD
}
=======

}
>>>>>>> c637e40498f08cd34f230a7e6b670e73d15beb73
