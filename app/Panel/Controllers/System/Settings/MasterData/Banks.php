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
 * Class Banks
 * @package App\Panel\Controllers\System\Settings\MasterData
 */
class Banks extends MasterData
{
    /**
     * Banks::index
     * @var string|\App\Models\Master\Banks::class
     */
    public $model = '\App\Models\Master\Banks';

    public function index()
    {
        if ($keyword = input()->get('keyword')) {
    		$this->model->qb->like('name', $keyword);
		}
		
    	$banks = $this->model->all();
        
        view('system/settings/master-data/banks/index', [
            'banks' => $banks
        ]);
    }

    // ------------------------------------------------------------------------

    public function form($id = null)
    {
    	$bank = $this->model->find($id);

    	if($post = input()->post()) {
            if($id == null) {
                $this->model->insert($post);
                redirect_url('system/settings/master-data/banks');
            } else {
                $this->model->update($post);
                redirect_url('system/settings/master-data/banks');
            }
        }

    	view('system/settings/master-data/banks/form', ['bank' => $bank]);
    }

    public function detail($id)
    {
    	$bank = $this->model->find($id);

    	view('system/settings/master-data/banks/view', ['bank' => $bank]);
    }

    public function delete($id)
    {
    	if ($bank = $this->model->find($id)) {
    		$this->model->update(new SplArrayStorage([
	    		'id' => $id,
	    		'record_status' => 'DELETED'
	    	]));
    	}

		redirect_url('system/settings/master-data/banks');
    }
}