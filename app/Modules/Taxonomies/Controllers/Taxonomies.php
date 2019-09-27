<?php
/**
 * This file is part of the Circle Creative Web Application Project Boilerplate.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         PT. Lingkar Kreasi (Circle Creative)
 * @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */
// ------------------------------------------------------------------------

namespace App\Modules\Taxonomies\Controllers;

// ------------------------------------------------------------------------

use App\Modules\Taxonomies\Http\Controller;
use O2System\Spl\Datastructures\SplArrayObject;
use App\Api\Modules\Taxonomies\Models\Terms;

/**
 * Class Taxonomies
 * @package App\Modules\Taxonomies\Controllers
 */
class Taxonomies extends Controller
{
    public $model = '\App\Api\Modules\Taxonomies\Models\Taxonomies';
	public function index()
    {
        $vars = [
            'taxonomies' => $this->model->all()
        ];

        view('masterdata/taxonomies/index', $vars);
    }

    public function form($id=null)
    {
        $this->presenter->page->setHeader( 'FORM_ADD' );
        models(Terms::class)->appendColumns = [];
        $vars = [
            'post' => new SplArrayObject(),
            'parents' => $this->model->all(),
            'terms' => models(Terms::class)->all()
        ];

        if ($id) {
            if (false !== ($data = $this->model->find($id))) {
                $this->presenter->page->setHeader( 'FORM_EDIT' );
                $vars['post'] = $data;
            } else {
                $this->output->send(204);
            }
        }

        view('masterdata/taxonomies/form', $vars);
    }

    public function delete($id=null)
    {
        if ($id) {
            if ($this->model->delete($id)) {
                redirect_url('/manage/taxonomies');
            }
            redirect_url('/manage/taxonomies');
        } else {
            $this->output->send(404);
        }
    }
}
