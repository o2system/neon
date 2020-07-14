<?php
/**
 * This file is part of the O2System Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */

// ------------------------------------------------------------------------

namespace App\Panel\Controllers\System\Settings\MasterData;

// ------------------------------------------------------------------------

use App\Panel\Controllers\System\Settings\MasterData;
use O2System\Framework\Libraries\Generator\Datatable;
use O2System\Spl\DataStructures\SplArrayStorage;

/**
 * Class Countries
 * @package App\Panel\Controllers\System\Settings\MasterData
 */
class Countries extends MasterData
{
    /**
     * Countries::index
     * @var string|\App\Models\Master\Countries::class
     */
    public $model = '\App\Models\Master\Countries';

    public function index()
    {
        if ($keyword = input()->get('keyword')) {
    		$this->model->qb->like('name', $keyword);
		}

        $countries = $this->model->all();

        view('system/settings/master-data/countries/index', ['countries' => $countries]);
    }

    // ------------------------------------------------------------------------

    public function form($id = null)
    {
    	$country = $this->model->find($id);

    	if($post = input()->post()) {
            if($id == null) {
                $this->model->insert($post);
                redirect_url('system/settings/master-data/countries');
            } else {
                $this->model->update($post);
                redirect_url('system/settings/master-data/countries');
            }
        }

    	view('system/settings/master-data/countries/form', ['country' => $country]);
    }

    public function detail($id)
    {
    	$country = $this->model->find($id);

    	view('system/settings/master-data/countries/view', ['country' => $country]);
    }

    public function delete($id)
    {
    	if ($country = $this->model->find($id)) {
    		$this->model->update(new SplArrayStorage([
	    		'id' => $id,
	    		'record_status' => 'DELETED'
	    	]));
    	}

		redirect_url('system/settings/master-data/countries');
    }
}