<?php
/**
 * This file is part of the NEO ERP Application.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         PT. Lingkar Kreasi (Circle Creative)
 * @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */
// ------------------------------------------------------------------------

namespace App\Modules\Personal\Http;

// ------------------------------------------------------------------------

use App\Http\AccessControl\Controllers\AuthenticatedController;

/**
 * Class Controller
 * @package App\Modules\Personal\Http
 */
class Controller extends AuthenticatedController
{
    /**
     * Controller::$model
     *
     * @var string|\O2System\Framework\Models\Sql\Model
     */
    public $model;

    // ------------------------------------------------------------------------

    /**
     * Controller::__reconstruct
     */
    public function __reconstruct()
    {
        parent::__reconstruct();

        presenter()->page
            ->setHeader('Personal')
            ->setTitle(strtoupper(get_class_name($this)));

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

    protected function uploadImage()
    {

    }
}