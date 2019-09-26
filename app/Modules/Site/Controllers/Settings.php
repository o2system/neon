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

namespace App\Manage\Modules\Site\Controllers;

// ------------------------------------------------------------------------

use App\Manage\Modules\Site\Http\Controller;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Filesystem\Handlers\Uploader;

/**
 * Class Posts
 * @package App\Manage\Modules\Posts\Controllers
 */
class Settings extends Controller
{
    public $model = "\App\Api\Modules\System\Models\Modules\Settings";
    public function index()
    {
        $vars = [
            'settings' => $this->model, 
            'site_name_page_title' => site_name_page_title(),
            'world_languages' => world_languages(),
            'site_timezone' => site_timezone(),
            'site_offline_message' => site_offline_message(),
            'meta_robot' => meta_robot()
        ];
        if ($post = input()->post()) {
            $post->id_sys_module = 2;
            if($files = input()->files()){
                foreach ($files as $key => $value) {
                    $imagePath = PATH_STORAGE . 'images/settings/';
                    if ( ! file_exists($imagePath)) {
                        mkdir($imagePath, 0777, true);
                    }

                    $upload = new Uploader();
                    $upload->setAllowedExtensions(['png', 'jpg', 'jpeg', 'JPG', 'PNG', 'JPEG']);
                    $upload->setPath($imagePath);
                    $upload->process($key);

                    if ($upload->getErrors()) {
                        $errors = new Unordered();
                        foreach ($upload->getErrors() as $code => $error) {
                            $errors->createList($error);
                        }

                        session()->setFlash('danger', $errors);

                        $redirectUrl = redirect_url('/site/settings');

                        redirect_url($redirectUrl);
                    } else {
                        if (isset($data)) {
                            if (is_file($imagePath . $data->photo)) {
                                unlink($imagePath . $data->photo);
                            }
                        }

                        $post[ $key ] = $upload->getUploadedFiles()->first()[ 'name' ];
                    }
                }
            }
            if ($this->model->insertOrUpdate($post->getArrayCopy())) {
                redirect_url('/site/settings');
            }
        }
        
        view('settings/index', $vars);
    }
}