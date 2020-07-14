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
use O2System\Spl\DataStructures\SplArrayStorage;
use App\Panel\Controllers\System\Settings as Controller;
/**
 * Class Roles
 * @package App\Panel\Controllers\Settings
 */
class Roles extends Controller
{
    /**
     * Roles::index
     * @var string|\vendor\o2system\framework\src\Models\Sql\System\Roles
     */
    public $model = 'O2System\Framework\Models\Sql\System\Modules\Roles';

    public function index()
    {
        $roles = $this->model->all();

        view('system/settings/roles/index', ['roles' => $roles]);
    }

    // ------------------------------------------------------------------------

    /**
     * Roles::form
     */
    public function form($id = null)
    {
    	$role = $this->model->find($id);

    	if($post = input()->post()) {
            if($id == null) {
                $this->model->insert($post);
                redirect_url('system/settings/roles');
            } else {
                $this->model->update($post);
                redirect_url('system/settings/roles');
            }
        }

    	view('system/settings/roles/form', ['role' => $role]);
    }
    
    
    /**
     * Roles::detail
     */
    public function detail($id)
    {
    	$role = $this->model->find($id);

    	view('system/settings/roles/detail', ['role ' => $role]);
    }

    /**
     * Roles::delete
     */
    public function delete($id)
    {
    	if ($role = $this->model->find($id)) {
    		$this->model->update(new SplArrayStorage([
	    		'id' => $id,
	    		'record_status' => 'DELETED'
	    	]));
    	}

		redirect_url('system/settings/roles');
    }
}
