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

namespace App\Modules\Products\Controllers;

// ------------------------------------------------------------------------

use App\Modules\Products\Http\Controller;
use O2System\Spl\Datastructures\SplArrayObject;

/**
 * Class Products
 * @package App\Modules\Products\Controllers
 */
class Categories extends Controller
{
	public $model = '\App\Api\Modules\Products\Models\Categories';
	public function index()
	{
		$get = input()->get();
        if ($get == false || $get == null) {
            $get = new SplArrayObject([
                'get' => 'false'
            ]);
        }
		view('categories/index', [
            'categories' => $this->model->filter($get),
            'entries' => range(10, 100, 10),
            'get' => $get,
        ]);
	}

	public function form($id=null)
	{
		$vars = [
			'post' => new SplArrayObject(),
			'parents' => $this->model->all()
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
			if ($id) {
				if ($this->model->update($post->getArrayCopy())) {
					return redirect_url('/products/categories');
				}
			} else {
				if ($this->model->insert($post->getArrayCopy())) {
					return redirect_url('/products/categories');
				}
			}
		}

		view('categories/form', $vars);
	}

	public function delete($id)
	{
		if ($id) {
			if ($this->model->delete($id)) {
				return redirect_url('/products/categories');
			}
		}
	}
}