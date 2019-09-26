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
class Server extends Controller
{
	public $model = "\App\Api\Modules\System\Models\Modules\Settings";
    public function index()
    {
    	$vars = [
            'settings' => $this->model,
            'error_reporting_systems' => error_reporting_systems(),
            'force_https' => force_https(),
            'database_type' => database_type()
        ];
    	if($post = input()->post()) {
            if ($post->ftp_password) {
                $post->ftp_password = password_hash($post->ftp_password, PASSWORD_DEFAULT);
            }
            if ($post->proxy_password) {
                $post->proxy_password = password_hash($post->proxy_password, PASSWORD_DEFAULT);
            }
            if($this->model->insertOrUpdate($post->getArrayCopy())) {
                redirect_url('/system/settings/server');
            }
        }
        view('settings/server', $vars);
    }	
}