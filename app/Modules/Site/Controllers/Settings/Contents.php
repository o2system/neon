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
use App\Api\Modules\Taxonomies\Models\Taxonomies;
/**
 * Class Pages
 * @package App\Manage\Modules\Site\Controllers
 */
class Contents extends Controller
{
    public $model = "\App\Api\Modules\System\Models\Modules\Settings";
    public function index()
    {
        $vars = [
            'total_taxonomies' => count(models(Taxonomies::class)->all()),
            'compose_post_format' => compose_post_format(),
            'compose_time_format' => compose_time_format(),
            'settings' => $this->model
        ];

        if($post = input()->post()) {
            $post->id_sys_module = 2;
            if($this->model->insertOrUpdate($post->getArrayCopy())) {
                redirect_url('/site/settings/contents');
            }
        }
        
        view('settings/contents', $vars);
    }
}
   