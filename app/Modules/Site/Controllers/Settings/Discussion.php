<?php
/**
 * This file is part of the O2Site PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */
// ------------------------------------------------------------------------

namespace App\Manage\Modules\Site\Controllers\Settings;

// ------------------------------------------------------------------------

use App\Manage\Modules\Site\Http\Controller;
/**
 * Class Pages
 * @package App\Manage\Modules\Site\Controllers
 */
class Discussion extends Controller
{
	public $model = "\App\Api\Modules\System\Models\Modules\Settings";
    public function index()
    {
    	$vars = ['settings' => $this->model];
    	if($post = input()->post()) {
            $post->id_sys_module = 2;
            if($this->model->insertOrUpdate($post->getArrayCopy())) {
                redirect_url('/site/settings/discussion');
            }
        }
        view('settings/discussion', $vars);
    }	
}