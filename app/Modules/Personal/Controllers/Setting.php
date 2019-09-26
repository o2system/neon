<?php
/**
 * This file is part of the NEO ERP Application.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @author         PT. Lingkar Kreasi (Circle Creative)
 *  @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */
// ------------------------------------------------------------------------
namespace App\Modules\Personal\Controllers;

// ------------------------------------------------------------------------

use App\Api\Modules\System\Models\Users;
use App\Modules\Personal\Http\Controller;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Image\Uploader;
use O2System\Spl\DataStructures\SplArrayObject;


/**
 * Class Setting
 * @package App\Modules\Personal\Controllers
 */
class Setting extends Controller
{
    /**
     * Setting::index
     */
    public function index()
    {
        if(globals()->account->employee){
            redirect_url('personal/profile');
        }else{
            $vars = [
                'user' => new SplArrayObject([
                    'profile'   => new SplArrayObject()
                ]),
                'genders' => models('options')->genders(),
                'religions' => models('options')->religions(),
                'maritals' => models('options')->maritals(),
            ];
            if($user =  models(Users::class)->find(session()['account']['user']['id'])){
                $vars['user'] = $user;
            }
            if($post = $this->input->post()) {
                $profile = $post->profile;
                unset($post->profile);
                $file = $this->detectPhoto('images/users/');
                if($file != false){
                    $profile['avatar'] = $file['name'];
                }
                models(Users::class)->update($post->getArrayCopy());
                models(Users\Profiles::class)->update($profile);
                redirect_url('personal/setting');
            }
            $this->view->load('settings', $vars);
        }
    }

    /**
     * @param string $path
     * @return bool|mixed
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     */
    private function detectPhoto(string $path)
    {
        if ($this->input->files('avatar'))
        {
            $imagePath = PATH_STORAGE . $path;
            if (!file_exists($imagePath)){
                mkdir($imagePath, 0770, true);
            }
            $upload = new Uploader();
            $upload->setPath($imagePath);
            $upload->process('photo');
            if ($upload->getErrors()) {
                $errors = new Unordered();
                foreach ($upload->getErrors() as $code => $error) {
                    $errors->createList($error);
                }
                $this->session->setFlash('danger', $errors);
                redirect_url($_SERVER['HTTP_REFERER']);
                return false;
            } else {
                return $file = $upload->getUploadedFiles()->first();
            }
        }
        return false;
    }
}