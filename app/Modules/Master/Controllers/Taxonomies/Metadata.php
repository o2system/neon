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

namespace App\Manage\Modules\Master\Controllers\Taxonomies;

// ------------------------------------------------------------------------

use App\Manage\Modules\Master\Http\Controller;

/**
 * Class Metadata
 * @package App\Manage\Modules\Taxonomies\Controllers
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
            'field'    => 'id_taxonomy',
            'label'    => 'Taxonomy Term',
            'rules'    => 'required|integer',
            'messages' => 'ID Taxonomy cannot be empty and it has to be integer!',
        ],
        [
            'field'    => 'name',
            'label'    => 'Name',
            'rules'    => 'required|alphanumericspaces',
            'messages' => 'Name cannot be empty and it shouldn\'t have @-.$*()+;~:\'/%_?,=&!',
        ],
        [
        	'field' => 'content',
            'rules' => 'optional'
        ]
    ];
}