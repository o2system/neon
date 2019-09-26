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
 * Class Categories
 * @package App\Api\Modules\Posts\Controllers\Sections
 */
class Categories extends Controller
{
	/**
     * Taxonomies::$fillableColumnsWithRules
     *
     * @var array
     */
    public $fillableColumnsWithRules = [
        [
            'field'    => 'id_post_section',
            'label'    => 'Taxonomy Term',
            'rules'    => 'required|integer',
            'messages' => 'Taxonomy Term cannot be empty and it has to be integer!',
        ],
        [
            'field'    => 'id_parent',
            'label'    => 'Parent',
            'rules'    => 'integer',
            'messages' => 'ID Parent value needs to be integer',
        ],
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
            'label'    => 'Image',
            'rules'    => 'optional',
        ],
    ];
}