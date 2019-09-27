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

namespace App\Modules\Master\Controllers;

// ------------------------------------------------------------------------

use App\Modules\Master\Http\Controller;
use O2System\Spl\Datastructures\SplArrayObject;

/**
 * Class Geodirectories
 * @package App\Modules\Master\Controllers
 */
class Geodirectories extends Controller
{
	public $model = '\App\Api\Modules\Master\Models\Geodirectories';
	public function index()
	{
        $get = input()->get();
        if ($get == false || $get == null) {
            $get = new SplArrayObject([
                'get' => 'false'
            ]);
        }
		$this->model->qb->orderBy('id', 'DESC');
        
        $this->rebuildTree();

		$vars = [
            'geodirectories' => $this->model->filter($get),
            'entries' => range(100, 10000, 50),
            'get' => $get,
        ];

        view('geodirectory/index', $vars);
	}

	public function form($id=null)
    {
        $this->presenter->page->setHeader( 'FORM_ADD' );

        $vars = [
            'post' => new SplArrayObject(),
            'types' => geodirectory_types(),
            'parents' => $this->model->all(null, 100)
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
                    return redirect_url('/master/geodirectories');
                }
            } else {
                if ($this->model->insert($post->getArrayCopy())) {
                    return redirect_url('/master/geodirectories');
                }
            }
        }

        view('geodirectory/form', $vars);
    }

    public function delete($id)
    {
        if ($id) {
            if ($this->model->delete($id)) {
                return redirect_url('/master/geodirectories');
            }
            return false;
        }
    }
}