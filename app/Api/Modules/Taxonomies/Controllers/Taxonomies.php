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
 * Class Taxonomies
 * @package App\Api\Modules\Taxonomies\Controllers
 */
class Taxonomies extends Controller
{
	/**
     * Taxonomies::$fillableColumnsWithRules
     *
     * @var array
     */
    // public $fillableColumnsWithRules = [
    //     [
    //         'field'    => 'id_taxonomy_term',
    //         'label'    => 'Taxonomy Term',
    //         'rules'    => 'required|integer',
    //         'messages' => 'Taxonomy Term cannot be empty and it has to be integer!',
    //     ],
    //     [
    //         'field'    => 'id_parent',
    //         'label'    => 'Parent',
    //         'rules'    => 'integer',
    //         'messages' => 'ID Parent value needs to be integer',
    //     ],
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
    // ];
    public function delete($id = null){
        if ($id) {
            $_POST['id'] = $id;
            parent::delete();
        }
    }
}