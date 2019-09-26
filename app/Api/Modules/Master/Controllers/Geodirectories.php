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

namespace App\Api\Modules\Master\Controllers;

// ------------------------------------------------------------------------

use App\Api\Modules\Master\Http\Controller;

/**
 * Class Geodirectories
 * @package App\Api\Modules\Master\Controllers
 */
class Geodirectories extends Controller
{
    public function delete($id = null){
        if ($id) {
            $_POST['id'] = $id;
            parent::delete();
        }
    }
}