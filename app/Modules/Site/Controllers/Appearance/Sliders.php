<?php
/**
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */
// ------------------------------------------------------------------------

namespace App\Modules\Site\Controllers\Appearance;

// ------------------------------------------------------------------------

use App\Modules\Site\Http\Controller;
use O2System\Spl\Datastructures\SplArrayObject;
use App\Api\Modules\Taxonomies\Models\Taxonomies;
use App\Api\Modules\Taxonomies\Models\Terms;

/**
 * Class Customizer
 * @package App\Modules\Sites\Controllers
 */
class Sliders extends Controller
{
	public $model = '\App\Api\Modules\Taxonomies\Models\Taxonomies';
	public function index()
    {
        $get = input()->get();
        if ($get == false || $get == null) {
            $get = new SplArrayObject([
                'get' => 'false'
            ]);
        }
        $term = models(Terms::class)->find('sliders', 'slug');
    	$vars = [
    		'taxonomies' => $this->model->sliders_filter($get, $term->id),
            'entries' => range(10, 100, 10),
            'get' => $get,
    	];
        view('appearance/sliders/index', $vars);
    }

    public function form($id=null)
    {
        $this->presenter->page->setHeader( 'FORM_ADD' );
        $this->model->appendColumns = [];
        $term = models(Terms::class)->find('sliders', 'slug');
        $parents = $this->model->findWhere(['id_taxonomy_term' => $term->id]);
        $vars = [
            'post' => new SplArrayObject(),
            'parents' => $parents,
            'term' => $term
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
                    return redirect_url('/site/appearance/sliders');
                }
            } else {
                if ($data = $this->model->insert($post->getArrayCopy())) {
                    if ($data['status']) {
                        return redirect_url('/site/appearance/sliders/detail/'.$data['id_taxonomy']);
                    }
                }
            }
        }

        view('appearance/sliders/form', $vars);
    }
}