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

namespace App\Manage\Modules\Products\Controllers;

// ------------------------------------------------------------------------

use App\Manage\Modules\Products\Http\Controller;
use App\Api\Modules\Products\Models\Categories;
use App\Api\Modules\Master\Models\Currencies;
use App\Api\Modules\Companies\Models\Companies;
use O2System\Spl\Datastructures\SplArrayObject;


/**
 * Class Products
 * @package App\Manage\Modules\Products\Controllers
 */
class Products extends Controller
{
	public $model = '\App\Api\Modules\Products\Models\Products';

	public function index()
	{
        $get = input()->get();
        if ($get == false || $get == null) {
            $get = new SplArrayObject([
                'get' => 'false'
            ]);
        }
		view('products/index', [
            'products' => $this->model->filter($get),
            'entries' => range(10, 100, 10),
            'get' => $get,
        ]);
	}

	public function form($id=null)
	{
		$vars = [
            'post' => new SplArrayObject(),
            'visibility' => visibilityOptions(),
            'status' => posts_status(),
            'categories' => models(Categories::class)->all(),
            'currencies' => models(Currencies::class)->all(),
            'conditions' => conditions(),
            'target_gender' => target_gender(),
            'target_age' => target_age(),
            'weight' => weight(),
            'length_width_height' => length_width_height(),
            'merchants' => models(Companies::class)->merchants(),
            'insurance' => insurance(),
            'variant_name' => variant_name()
        ];

        if ($id) {
            $this->model->appendColumns = [
                'metadata', 'variants', 'wholesales', 'record', 'images', 'merchant'
            ];
            if (false !== ($data = $this->model->find($id))) {
                $this->presenter->page->setHeader( 'FORM_EDIT' );
                
                $vars['post'] = $data;
            } else {
                $this->output->send(204);
            }
        }

        if ($post = input()->post()) {
            $post = $post->getArrayCopy();
            $post['wholesale'] = $this->wholesale(new SplArrayObject($post['meta']['wholesale']));
            unset($post['meta']['wholesale']);
            if ($post['id']) {
                if ($this->model->update($post)) {
                    redirect_url('/products');
                }
            } else {
                if ($this->model->insert($post)) {
                    redirect_url('/products');
                }
            }
        }

		view('products/form', $vars);
	}

    public function delete($id)
    {
        if ($id) {
            if ($this->model->delete($id)) {
                redirect_url('/products');
            }
            redirect_url('/products');
        } else {
            $this->output->send(404);
        }
        
    }

    protected function wholesale($wholesale = null) {
        if ($wholesale) {
            $wholesale_data = [];
            foreach ($wholesale['wholesale_unit'] as $key => $value) {
                if ($wholesale['wholesale_unit'][$key] != '') {
                    $wholesale_data[$key] = [
                        'wholesale_unit' => $wholesale['wholesale_unit'][$key],
                        'wholesale_price' => $wholesale['wholesale_price'][$key],
                    ];
                }
            }
            
            if (count($wholesale_data)) {
                return $wholesale_data;
            }
            return null;
        } else {
            return null;
        }
    }
}