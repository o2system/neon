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

namespace App\Manage\Modules\Members\Controllers;

// ------------------------------------------------------------------------

use App\Manage\Modules\Members\Http\Controller;
use O2System\Spl\Datastructures\SplArrayObject;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Filesystem\Handlers\Uploader;
use App\Api\Modules\Members\Models\Metadata;
use App\Libraries\Rajaongkir;

/**
 * Class Members
 * @package App\Manage\Modules\Members\Controllers
 */
class Members extends Controller
{
	public $model = '\App\Api\Modules\Members\Models\Members';

	public function index()
	{
		$get = input()->get();
        if ($get == false || $get == null) {
            $get = new SplArrayObject([
                'get' => 'false'
            ]);
        }

		$vars = [
			'members' => $this->model->filter($get),
			'get' => $get,
			'entries' => range(10, 100, 10),
		];
		
		view(PATH_RESOURCES.'manage/modules/members/views/index.phtml', $vars);
	}

	public function table()
	{
		$get = input()->get();
        if ($get == false || $get == null) {
            $get = new SplArrayObject([
                'get' => 'false'
            ]);
        }
		$vars = [
			'members' => $this->model->filter($get),
			'get' => $get,
			'entries' => range(10, 100, 10),
		];
		
		view('table', $vars);
	}

	public function form($id=null)
	{
		$rajaongkir = new Rajaongkir();
		$cities = $rajaongkir->result->getCities();
		$vars = [
			'post' => new SplArrayObject(),
			'cities'    => $cities,
		];

		$this->model->appendColumns = [
			'metadata',
			'image'
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
            $post = $post->getArrayCopy();
            if($files = input()->files()['photo']){
                $filePath = PATH_STORAGE . 'images/members/';;
                if(!file_exists($filePath)){
                    mkdir($filePath, 0777, true);
                }

                $upload = new Uploader();
                $upload->setPath($filePath);
                $upload->process('photo');

                if ($upload->getErrors()) {
                    $errors = new Unordered();

                    foreach ($upload->getErrors() as $code => $error) {
                        $errors->createList($error);
                    }
                    $this->output->send([
                        'error'  => $errors
                    ]);
                } else {
                	if ($post['id']) {
                        $data = $this->model->find($post['id']);
                        if (is_file($image = $filePath.$data->metadata('photo')->photo)) {
                            unlink($image);
                        }
                    }

                    $filename = $upload->getUploadedFiles()->first()['name'];
                    $post['meta']['photo'] = $filename;
                }
            }


            if ($post['id']) {
                if ($this->model->update($post)) {
                    redirect_url('/members');
                }
            } else {
                if ($this->model->insert($post)) {
                    redirect_url('/members');
                }
            }
        }

		view('form', $vars);
	}

	public function delete($id)
	{
		if ($id) {
    		models(Metadata::class)->deleteManyBy(['id_member' => $id]);
    		if ($this->model->delete($id)) {
    			redirect_url('/members');
    		}
    		redirect_url('/members');
    	} else {
    		$this->output->send(404);
    	}
	}

	public function detail($id = null)
	{
		if ($id) {
			if (false !== ($data = $this->model->find($id))) {
				$this->presenter->page->setHeader( 'PAGE_DETAIL' );
				$vars['member'] = $data;
				view('detail', $vars);
			} else {
				$this->output->send(204);
			}
		}
	}
}