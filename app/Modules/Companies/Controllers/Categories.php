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

namespace App\Manage\Modules\Companies\Controllers;

// ------------------------------------------------------------------------

use App\Manage\Modules\Companies\Http\Controller;
use O2System\Spl\Datastructures\SplArrayObject;

/**
 * Class Companies
 * @package App\Manage\Modules\Companies\Controllers
 */
class Categories extends Controller
{
	public $model = '\App\Api\Modules\Companies\Models\Categories';
	public function index()
	{
		$vars = [
			'categories' => $this->model->all()
		];
		view('categories/index', $vars);
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
				if ($this->model->update($post)) {
					return redirect_url('/companies/categories');
				}
			} else {
				if ($this->model->create($post)) {
					return redirect_url('/companies/categories');
				}
			}
		}

		view('categories/form', $vars);
	}

	public function delete($id)
	{
		if ($id) {
			if ($this->model->delete($id)) {
				return redirect_url('/companies/categories');
			}
		}
	}
}