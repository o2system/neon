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

namespace App\Manage\Modules\System\Controllers\Users;

// ------------------------------------------------------------------------

use App\Api\Modules\System\Models\Modules\Roles;
use App\Manage\Modules\System\Http\Controller;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Image\Uploader;
use O2System\Spl\DataStructures\SplArrayObject;
use App\Api\Modules\System\Models\Modules\Users as ModulesUsers;
use App\Api\Modules\System\Models\Users\Profiles;

/**
 * Class Administrator
 * @package App\Modules\Administrator\Controllers
 */
class Manage extends Controller
{
    /**
     * @var string
     */
    public $model  = '\App\Api\Modules\System\Models\Users';

    // ------------------------------------------------------------------------

    /**
     * show all administrator users
     */
    public function index()
    {
        view('users/index',[
            'users' => $this->model->all()
        ]);
    }

    // ------------------------------------------------------------------------

    /**
     * @param null $id
     * @throws BadDependencyCallException
     */
    public function form($id = null)
    {
        $vars = [
            'post'    => new SplArrayObject([
            ]),
            'options' => new SplArrayObject([
                'genders'   => models('options')->genders(),
                'religions' => models('options')->religions(),
                'maritals' => models('options')->maritals(),
                'roles'     => models(Roles::class)->all(),
            ]),
        ];

        presenter()->page->setTitle('FORM_ADD');

        if (isset($id)) {
            if (false === ($data = $this->model->find(intval($id)))) {
                output()->sendError(404);
            }
            $data->password = password_hash($data['password'], PASSWORD_DEFAULT);
            $vars[ 'post' ] = $data;

            presenter()->page->setTitle('FORM_EDIT');
        }

        if ($post = input()->post()) {
            $profile = $post->profile;
            $role = $post->role;
            unset($post->profile, $post->role);
            if (input()->files('photo')) {
                $imagePath = PATH_STORAGE . 'images/users/';
                if ( ! file_exists($imagePath)) {
                    mkdir($imagePath, 0777, true);
                }
                $upload = new Uploader();
                $upload->setPath($imagePath);
                $upload->setAllowedExtensions('gif,jpg,jpeg,png');
                $upload->process('photo');
                if ($upload->getErrors()) {
                    $errors = new Unordered();

                    foreach ($upload->getErrors() as $code => $error) {
                        $errors->createList($error);
                    }
                    
                    session()->setFlash('danger', $errors);

                    if (empty($post->id)) {
                        redirect_url('system/users/manage/form/');
                    } else {
                        redirect_url('system/users/manage/form/' . $post[ 'id' ]);
                    }
                } else {
                    // Remove Previous Photo
                    if ($id = $post[ 'id' ]) {
                        if ($data = $this->model->find($id)) {
                            if ($data->profile) {
                                if ($image = $data->profile->avatar) {
                                    if (file_exists($imagePath . $image)) {
                                        unlink($imagePath . $image);
                                    }
                                }
                            }
                        }
                    }
                    $profile['avatar'] = $upload->getUploadedFiles()->first()[ 'name' ];
                }
            }

            $action = empty($post[ 'id' ]) ? 'INSERT' : 'UPDATE';
            switch ($action) {
                case 'INSERT':
                    $insert = $this->model->userInsert($post->getArrayCopy(), $role, $profile);
                    if($insert['status'] == 'success'){
                        session()->setFlash($insert['status'], $insert['message']);
                        redirect_url('system/users/manage');
                    }else{
                        session()->setFlash($insert['status'], $insert['message']);
                   }
                    break;

                case 'UPDATE':
                    $update = $this->model->userUpdate($post->getArrayCopy(), $role, $profile);
                    if ($update['status'] == 'success') {
                        session()->setFlash($update['status'], $update['message']);
                        redirect_url('system/users/manage/form/'.$post[ 'id' ]);
                    } else {
                        session()->setFlash($update['status'], $update['message']);
                    }
                    break;
            }
        }
        $this->load->view('users/form', $vars);
    }

    // ------------------------------------------------------------------------

    public function delete($id)
    {
        if($data = $this->model->find($id)){
            if ($data->profile) {
                if ($image = $data->profile->avatar) {
                    $imagePath = PATH_STORAGE . 'images/users/';
                    if (file_exists($imagePath . $image)) {
                        unlink($imagePath . $image);
                    }
                }
            }
            models(ModulesUsers::class)->deleteManyBy(['id_sys_user' => $data->id]);
            models(Profiles::class)->deleteManyBy(['id_sys_user' => $data->id]);
            $this->model->delete($id);
            session()->setFlash('success', 'SUCCESS_DELETE');
        }else{
            session()->setFlash('danger', 'FAILED_DELETE');
        }
        redirect_url('system/users/manage');
    }

    // ------------------------------------------------------------------------

    // public function detail($id = null)
    // {
    //    if($user = $this->model->find($id)){
    //         $this->load->view('users/detail',[
    //             'user'  => $user,
    //             'modules'   => models(\App\Api\Modules\System\Models\Modules::class)
    //             ->all(),
    //             'module'    => models(\App\Api\Modules\System\Models\Modules::class)->find(input()->get('id_segment'))
    //         ]);
    //     }else{
    //         $this->output->send(403);
    //     }
    // }

    // ------------------------------------------------------------------------

    // public function settings(){
    //     $this->load->view('users/settings');
    // }
}