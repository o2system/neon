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

namespace App\Api\Modules\Posts\Controllers\Sections;

// ------------------------------------------------------------------------

use App\Api\Modules\Posts\Http\Controller;

/**
 * Class Items
 * @package App\Api\Modules\Posts\Controllers\Sections
 */
class Items extends Controller
{
	/**
     * Items::$fillableColumnsWithRules
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
            'field'    => 'id_post_section',
            'label'    => 'ID Post Section',
            'rules'    => 'required|integer',
            'messages' => 'ID Post Section cannot be empty and must be integer!',
        ],
    ];
}