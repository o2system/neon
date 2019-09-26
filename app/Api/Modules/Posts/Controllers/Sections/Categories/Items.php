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

namespace App\Api\Modules\Posts\Controllers\Sections\Categories;

// ------------------------------------------------------------------------

use App\Api\Modules\Posts\Http\Controller;

/**
 * Class Items
 * @package App\Api\Modules\Posts\Controllers\Sections\Categories
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
            'field'    => 'id_post_section_category',
            'label'    => 'ID Post Section Category',
            'rules'    => 'required|integer',
            'messages' => 'ID Post Section Category cannot be empty and must be integer!',
        ],
    ];
}