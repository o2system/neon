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

namespace App\Api\Modules\Pages\Controllers;

// ------------------------------------------------------------------------

use App\Api\Modules\Pages\Http\Controller;

/**
 * Class Posts
 * @package App\Api\Modules\Pages\Controllers
 */
class Posts extends Controller
{
    /**
     * Posts::$fillableColumnsWithRules
     *
     * @var array
     */
	public $fillableColumnsWithRules = [
        [
            'field'    => 'id_page',
            'label'    => 'Page Id',
            'rules'    => 'required|integer',
            'messages' => 'Page id cannot be empty!',
        ],
        [
            'field'    => 'id_post',
            'label'    => 'Post Id',
            'rules'    => 'required|integer',
            'messages' => 'Post id cannot be empty!',
        ],
    ];

}