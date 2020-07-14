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

namespace App\Panel\Controllers\System\Settings;

// ------------------------------------------------------------------------

use App\Panel\Controllers\System\Settings\MasterData;
use O2System\Spl\DataStructures\SplArrayStorage;

/**
 * Class Currencies
 * @package App\Panel\Modules\Store\Controllers\Settings\MasterData
 */
class Currencies extends MasterData
{
    /**
     * Currency::index
     * @var string|\App\Models\Master\currencies::class
     */
    public $model = '\App\Models\Master\Currencies';

    public function index()
    {
        if ($keyword = input()->get('keyword')) {
    		$this->model->qb->like('name', $keyword);
		}
		
    	$currencies = $this->model->all();

        view('system/settings/master-data/currencies/index', [
            'currencies' => $currencies
        ]);
    }

    // ------------------------------------------------------------------------

    public function form($id = null)
    {
    	$currency = $this->model->find($id);

    	if($post = input()->post()) {
            if($id == null) {
                $this->model->insert($post);
                redirect_url('system/settings/master-data/currencies');
            } else {
                $this->model->update($post);
                redirect_url('system/settings/master-data/currencies');
            }
        }

    	view('system/settings/master-data/currencies/form', ['currency' => $currency]);
    }

    public function detail($id)
    {
    	$currency = $this->model->find($id);

    	view('system/settings/master-data/currencies/view', ['currency' => $currency]);
    }

    public function delete($id)
    {
    	if ($currency = $this->model->find($id)) {
    		$this->model->update(new SplArrayStorage([
	    		'id' => $id,
	    		'record_status' => 'DELETED'
	    	]));
    	}

		redirect_url('system/settings/master-data/currencies');
    }
}