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

namespace App\Manage\Modules\System\Controllers\Settings;

// ------------------------------------------------------------------------

use App\Manage\Modules\System\Http\Controller;
/**
 * Class Pages
 * @package App\Manage\Modules\System\Controllers
 */
class Emails extends Controller
{
	public $model = "\App\Api\Modules\System\Models\Modules\Settings";
    public function index()
    {
    	$vars = [
            'settings' => $this->model,
            'emails_mailer' => emails_mailer()
        ];
        if($post = input()->post()) {
            if($this->model->insertOrUpdate($post->getArrayCopy())) {
                return redirect_url('system/settings/emails'); // if success
            }
        }
        view('settings/emails', $vars);
    }	
}