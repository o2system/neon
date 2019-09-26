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

namespace App\Modules\Personal\Http;

// ------------------------------------------------------------------------

use App\Api\Modules\Scrum\Models\Boards\Cards\Tasks;
use App\Http\AccessControl\Controllers\AuthenticatedController;
use O2System\Framework\Libraries\Ui\Contents\Link;
use O2System\Framework\Models\Sql\Model;

/**
 * Class Controller
 * @package App\Modules\Personal\Http
 */
class Controller extends AuthenticatedController
{
    /**
     * Controller::$model
     *
     * @var string|Model
     */
    public $model;

    public $pathModule = PATH_RESOURCES.'modules/personal/views/';

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

        $segment = presenter()->page->breadcrumb;
        if($item = $segment->childNodes->item(3)){
            if(get_class_name(get_parent_class($this)) === 'Payroll'){
                if($item->childNodes->first()->textContent->first() == 'Requisition'){
                    $item->childNodes->first()->attributes->href = base_url('personal/payroll');
                }
            }elseif (get_class_name(get_parent_class($this)) === 'Evaluations'){
                if($item->childNodes->first()->textContent->first() == 'Review'){
                    $item->childNodes->first()->attributes->href = base_url('personal/evaluations');
                }
            }
            else{
                if($item->childNodes->first()->textContent->first() == 'Requisition'){
                    $item->childNodes->first()->attributes->href = base_url('personal/payroll');
                }elseif ($item->childNodes->first()->textContent->first() == 'Cards'){
                    if($card = models(Tasks::class)->find(input()->get('id'))->card){
                        $item->childNodes->first()->attributes->href = base_url('personal/boards/cards', ['id' => $card->id]);
                    }
                }
            }
        };
    }
}