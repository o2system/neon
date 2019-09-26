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

namespace App\Api\Modules\Taxonomies\Controllers;

// ------------------------------------------------------------------------

use App\Api\Modules\Taxonomies\Http\Controller;

/**
 * Class Terms
 * @package App\Api\Modules\Taxonomies\Controllers
 */
class Terms extends Controller
{
	/**
     * Terms::$fillableColumnsWithRules
     *
     * @var array
     */
    // public $fillableColumnsWithRules = [
    //     [
    //         'field'    => 'name',
    //         'label'    => 'Name',
    //         'rules'    => 'required|alphanumericspaces',
    //         'messages' => 'Name cannot be empty and it shouldn\'t have @-.$*()+;~:\'/%_?,=&!',
    //     ],
    //     [
    //         'field'    => 'slug',
    //         'label'    => 'Slug',
    //         'rules'    => 'required|alphadash',
    //         'messages' => 'Slug cannot be empty! the examples are like this = slug-slug-slug',
    //     ],
    //     [
    //         'field'    => 'description',
    //         'label'    => 'Description',
    //         'rules'    => 'optional',
    //     ],
    //     [
    //         'field'    => 'image',
    //         'rules'    => 'optional',
    //     ],
    //     [
    //         'field'    => 'metadata',
    //         'rules'    => 'optional',
    //     ],
    // ];
    
    public function delete($id=null)
    {
        if ($id) {
            $_POST['id'] = $id;
            parent::delete();
        }
    }
}