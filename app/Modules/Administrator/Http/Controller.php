<?php
/**
 * This file is part of the NEO ERP Application.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @author         PT. Lingkar Kreasi (Circle Creative)
 *  @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */
// ------------------------------------------------------------------------

namespace App\Modules\Administrator\Http;

// ------------------------------------------------------------------------

use App\Http\AccessControl\Controllers\AuthorizedController;

/**
 * Class Controller
 *
 * @package Administrator\Http
 */
class Controller extends AuthorizedController
{
    /**
     * Controller::__construct
     */
    public $model;
    public function __reconstruct()
    {
        parent::__reconstruct();
        $segment1 = presenter()->page->breadcrumb->childNodes->item(1);

        $this->presenter->page
            ->setHeader( 'Administrator' )
            ->setDescription( 'The O2CMS Administrator Module' );
        if (empty($this->model)) {
            $controllerClassName = get_called_class();
            $modelClassName = str_replace(['App', 'Controllers'], ['App\Api', 'Models'], $controllerClassName);

            if (class_exists($modelClassName)) {
                $this->model = new $modelClassName();
            }
        } elseif (class_exists($this->model)) {
            $this->model = new $this->model();
        }
    }
}