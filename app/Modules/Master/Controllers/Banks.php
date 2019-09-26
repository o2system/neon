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

namespace App\Manage\Modules\Master\Controllers;

// ------------------------------------------------------------------------

use App\Manage\Modules\Master\Http\Controller;
use O2System\Spl\Datastructures\SplArrayObject;

/**
 * Class Banks
 * @package App\Manage\Modules\Master\Controllers
 */
class Banks extends Controller
{
	public $model = '\App\Api\Modules\Master\Models\Banks';
	public function index()
	{
		$get = input()->get();
        if ($get == false || $get == null) {
            $get = new SplArrayObject([
                'get' => 'false'
            ]);
        }

		$vars = [
			'banks' => $this->model->filter($get),
			'entries' => range(10, 100, 10),
	        'get' => $get,
		];

		view('bank/index', $vars);
	}

	/**
	 * Form Add and Edit
	 */

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

		if ($post = input()->post()) {
            if ($post->id) {
                if ($this->model->update($post->getArrayCopy())) {
                    return redirect_url('/master/banks');
                }
            } else {
                if ($this->model->insert($post->getArrayCopy())) {
                    return redirect_url('/master/banks');
                }
            }
        }

		view('bank/form', $vars);
	}

	public function delete($id)
    {
    	if ($id) {
    		if ($data = $this->model->find($id)) {
    			$filePath = PATH_STORAGE . 'images/master/banks/';
    			if (is_file($image = $filePath.$data->metadata['photo'])) {
	                unlink($image);
	            }
    		}
    		if ($this->model->delete($id)) {
    			redirect_url($_SERVER["HTTP_REFERER"]);
    		}
    	}
    }
}