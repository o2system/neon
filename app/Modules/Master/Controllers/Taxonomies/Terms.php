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

namespace App\Modules\Master\Controllers\Taxonomies;

// ------------------------------------------------------------------------

use App\Modules\Master\Http\Controller;
use O2System\Spl\Datastructures\SplArrayObject;
use App\Api\Modules\Taxonomies\Models\Taxonomies;

/**
 * Class Terms
 * @package App\Modules\Taxonomies\Controllers
 */
class Terms extends Controller
{
	public $model = '\App\Api\Modules\Taxonomies\Models\Terms';
	public function index()
	{
		$get = input()->get();
        if ($get == false || $get == null) {
            $get = new SplArrayObject([
                'get' => 'false'
            ]);
        }

        if ($keyword = $get->keyword) {
            $this->model->qb->like('name', $keyword);
        }

        if ($get->entries) {
            $all = (is_numeric($get->entries) ? $this->model->allWithPaging(null, $get->entries) : $this->all());
        } else {
            $all = $this->model->allWithPaging();
        }

		$vars = [
			'terms' => $all,
			'entries' => range(10, 100, 10),
	        'get' => $get,
		];
	    view('taxonomies/terms/index', $vars);
	}

	public function form($id=null)
	{
		$this->presenter->page->setHeader( 'FORM_ADD' );
		$vars = [
			'post' => new SplArrayObject(),
			'masterdata_status' => masterdata_status()
		];

		if ($id) {
			if (false !== ($data = $this->model->find($id))) {
				$this->presenter->page->setHeader( 'FORM_EDIT' );
				$vars['post'] = $data;
			} else {
				$this->output->send(204);
			}
		}

		if ($post = input()->post()) {
			if ($post->id) {
				if ($this->model->update($post->getArrayCopy())) {
					return redirect_url('/master/taxonomies/terms');
				}
			} else {
				if ($this->model->insert($post->getArrayCopy())) {
					return redirect_url('/master/taxonomies/terms');
				}
			}
		}

		view('taxonomies/terms/form', $vars);
	}

	public function delete($id)
    {
        if ($id) {
            $data = $this->model->find($id);
            $filePath = PATH_STORAGE . 'images/master/taxonomies/terms/'.$data->image;
            if (is_file($filePath)) {
                unlink($filePath);
            }
            if ($this->model->delete($id)) {
                redirect_url(input()->server('HTTP_REFERER'));
            }
        }
    }
}