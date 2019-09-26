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

namespace App\Manage\Modules\System\Controllers;

// ------------------------------------------------------------------------

use App\Manage\Modules\System\Http\Controller;
use O2System\Spl\Datastructures\SplArrayObject;
use App\Api\Modules\Master\Models\Banks;
use App\Api\Modules\Master\Models\Currencies;
use App\Api\Modules\Master\Models\Geodirectories;
/**
 * Class Pages
 * @package App\Manage\Modules\System\Controllers
 */
class Settings extends Controller
{
    public $model = "\App\Api\Modules\System\Models\Modules\Settings";
    public function index()
    {
        models(Geodirectories::class)->qb->from('tm_geodirectories')->count('id', 'total');
        $total_geodirectories = models(Geodirectories::class)->qb->get();
        $vars = [
            'total_banks' => count(models(Banks::class)->all()),
            'total_currencies' => count(models(Currencies::class)->all()),
            'total_geodirectories' => $total_geodirectories->info->num_total,
            'compose_post_format' => compose_post_format(),
            'compose_time_format' => compose_time_format(),
            'settings' => $this->model
        ];

        if($post = input()->post()) {
            if($this->model->insertOrUpdate($post->getArrayCopy())) {
                redirect_url('/system/settings/contents');
            }
        }
        
        view('settings/masterdata', $vars);
    }

}