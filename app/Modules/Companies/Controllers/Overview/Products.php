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

namespace App\Modules\Companies\Controllers\Overview;

// ------------------------------------------------------------------------

use App\Modules\Companies\Http\Controller;
use O2System\Spl\Datastructures\SplArrayObject;
use App\Api\Modules\Companies\Models\Companies;
/**
 * Class Companies
 * @package App\Modules\Companies\Controllers
 */
class Products extends Controller
{
	public $model = '\App\Api\Modules\Companies\Models\Products';

	public function index($id = null)
	{
		$get = input()->get();
        if ($get == false || $get == null) {
            $get = new SplArrayObject([
                'get' => 'false'
            ]);
        }
            
        if ($id) {
            $modelCompany = models(\App\Api\Modules\Companies\Models\Companies::class)->find($id);
            $modelCompany->appendColumns = [
                'metadata'
            ];
            if (false !== ($data = $modelCompany)) {
                $this->presenter->page->setHeader('PAGE_DETAIL');
                $vars = [
                    'entries' => range(10, 100, 10),
                    'get' => $get,
                    'company' => $data,
                    'products' => $data->products_filter($get)
                ];
                view('detail/products/index', $vars);
            } else {
                $this->output->send(204);
            }
        } else {
            $this->output->send(404);
        }
	}

	public function form($idCompany){
		$id = input()->get('id_contact');
		$vars = [
			'post' => new SplArrayObject(),
			'idCompany' => $idCompany
		];

		if ($id) {
			if (false !== ($data = $this->model->find($id))) {
				$this->presenter->page->setHeader( 'PAGE_DETAIL' );
				$vars['post'] = $data;
			} else {
				$this->output->send(204);
			}
		}

		view('detail/contact/form', $vars);
	}
}
