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

/**
 * Class Terms
 * @package App\Modules\Taxonomies\Controllers
 */
class Terms extends Controller
{
	public $model = '\App\Api\Modules\Taxonomies\Models\Terms';
    public function index()
    {
        $vars = [
            'terms' => $this->model->all()
        ];

        view('masterdata/taxonomies/terms/index', $vars);
    }

    public function form($id=null)
    {
        $this->presenter->page->setHeader( 'FORM_ADD' );
        $vars = [
            'post' => new SplArrayObject(),
        ];

        if ($id) {
            if (false !== ($data = $this->model->find($id))) {
                $this->presenter->page->setHeader( 'FORM_EDIT' );
                $vars['post'] = $data;
            } else {
                $this->output->send(204);
            }
        }

        view('masterdata/taxonomies/terms/form', $vars);
    }

    public function delete($id=null)
    {
        if ($id) {
            if ($this->model->delete($id)) {
                redirect_url('/manage/taxonomies/terms');
            }
            redirect_url('/manage/taxonomies/terms');
        } else {
            $this->output->send(404);
        }
    }
}
