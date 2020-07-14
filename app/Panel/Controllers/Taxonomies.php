<?php
/**
 * This file is part of the WebIn Platform.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @author         PT. Lingkar Kreasi (Circle Creative)
 *  @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */
// ------------------------------------------------------------------------

namespace App\Panel\Controllers;

// ------------------------------------------------------------------------

use App\Panel\Http\AccessControl\Controllers\AuthorizedController;
use O2System\Spl\DataStructures\SplArrayStorage;

/**
 * Class Taxonomies
 * @package App\Panel\Controllers\Taxonomies
 */
class Taxonomies extends AuthorizedController
{
    /**
     * Taxonomies::index
	 * @var string|\App\Models\Taxonomies
     */
    public $model = 'App\Models\Taxonomies';

    public function index()
    {
    	if ($keyword = input()->get('keyword')) {
    		$this->model->qb->like('name', $keyword);
		}
		
        $taxonomies = $this->model->all();
        
    	view('taxonomies/index', ['taxonomies' => $taxonomies]);
    }

    public function form($id = null)
    {
    	$taxonomies = $this->model->find($id);
    	if($post = input()->post()) {
            if($id == null) {
                $this->model->insert($post);
                redirect_url('taxonomies/index');
            } else {
                $this->model->update($post);
                redirect_url('taxonomies/index');
            }
        }

    	view('taxonomies/form', ['taxonomies' => $taxonomies]);
    }

    public function detail($id)
    {
    	$taxonomies = $this->model->find($id);

    	view('taxonomies/view', ['taxonomies' => $taxonomies]);
    }

    public function delete($id)
    {
    	if ($taxonomies = $this->model->find($id)) {
    		$this->model->update(new SplArrayStorage([
	    		'id' => $id,
	    		'record_status' => 'DELETED'
	    	])
	    	, [
				'id' => $id 
			]);
    	}

		redirect_url('taxonomies/index');
    }
}

