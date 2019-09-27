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

namespace App\Modules\System\Controllers\Settings;

// ------------------------------------------------------------------------

use App\Modules\System\Http\Controller;
/**
 * Class Pages
 * @package App\Modules\System\Controllers
 */
class Traffic extends Controller
{
	public $model = "\App\Api\Modules\System\Models\Modules\Settings";
    public function index()
    {
    	$vars = [
            'settings' => $this->model,
            
        ];
    	if($post = input()->post()) {
            if($this->model->insertOrUpdate($post->getArrayCopy())) {
                redirect_url('/system/settings/traffic');
            }
        }
        view('settings/traffic', $vars);
    }
}