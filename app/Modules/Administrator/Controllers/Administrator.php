<?php
/**
 * This file is part of the NEO ERP Application.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         PT. Lingkar Kreasi (Circle Creative)
 * @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */


namespace App\Modules\Administrator\Controllers;


use App\Api\Modules\MasterData\Models\Geodirectories;
use App\Api\Modules\System\Models\Modules;
use App\Api\Modules\System\Models\Modules\Roles;
use App\Modules\Administrator\Http\Controller;
use O2System\Spl\DataStructures\SplArrayObject;
use O2System\Image\Uploader;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;

/**
 * Class Administrator
 * @package App\Modules\Administrator\Controllers
 */
class Administrator extends Controller
{
    /**
     * @var string
     */
    public $model  = '\App\Api\Modules\System\Models\Users';

    /**
     * show all administrator users
     */
    public function index()
    {
        view('users/index',[
            'users' => $this->model->all()
        ]);
    }

    /**
     * @param null $id
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     */
    public function form($id = null)
    {
        loader()->model(Geodirectories::class);
        $vars = [
            'post'    => new SplArrayObject([
                'role'  => new SplArrayObject([
                    'id_sys_module_user_role'   => null,
                    'id'    => null,
                    'id_sys_user'   => null
                ]),
                'setting'   => new SplArrayObject([

                ]),
                'profile'   => new SplArrayObject([

                ])
            ]),
            'options' => new SplArrayObject([
                'groups'    => new SplArrayObject([
                   'HRD'    => 'HRD',
                   'Martketing' => 'MARKETING'
                ]),
                'genders'   => models('options')->genders(),
                'religions' => models('options')->religions(),
                'maritals'  => models('options')->maritals(),
                'dates'     => models('options')->dates(),
                'months'    => models('options')->months(),
                'years'     => models('options')->years(1945),
                'languages' => models('options')->languages(),
                'modules'   => models(Modules::class)->all(),
                'roles'     => models(Roles::class)->all(),
                'permission'    => new SplArrayObject([
                    'Granted'   => 'GRANTED',
                    'Denied'    => 'DENIED'
                ]),
                'countries' => models('geodirectories')->findWhere(['type' => 'COUNTRY']),
                'provinces' => models('geodirectories')->findWhere(['type' => 'PROVINCE']),
                'cities'    => models('geodirectories')->findWhere(['type' => 'CITY']),
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
            if (input()->files('photo')) {
                $imagePath = PATH_STORAGE . 'images/users/';
                if ( ! file_exists($imagePath)) {
                    mkdir($imagePath, 0777, true);
                }
                $upload = new Uploader();
                $upload->setPath($imagePath);
                $upload->process('photo');
                if ($upload->getErrors()) {
                    $errors = new Unordered();

                    foreach ($upload->getErrors() as $code => $error) {
                        $errors->createList($error);
                    }
                    $post->role = new SplArrayObject($post['roles']);
                    $post->setting = new SplArrayObject($post['settings']);
                    $post->profile = new SplArrayObject($post['profiles']);
                    $vars['post']   = $post;
                    session()->setFlash('danger', $errors);

                    if (empty($post->id)) {
                        redirect_url('administrator/form/');
                    } else {
                        redirect_url('administrator/form//' . $post[ 'id' ]);
                    }
                } else {
                    // Remove Previous Photo
                    if (isset($id)) {
                        if (file_exists($imagePath . $data[ 'photo' ])) {
                            unlink($imagePath . $data[ 'photo' ]);
                        }
                    }
                    $post[ 'photo' ] = $upload->getUploadedFiles()->first()[ 'name' ];
                }
            }
            $action = empty($post[ 'id' ]) ? 'INSERT' : 'UPDATE';

            // modify post variables

            unset($post[ 'year' ], $post[ 'month' ], $post[ 'date' ]);

            switch ($action) {
                case 'INSERT':
                    unset($post->id);
                    $post->record_create_user = $post->record_update_user = globals()->account->id;
                    $insert = $this->model->insertUser($post->getArrayCopy());
                    if($insert['status'] == 'success'){
                        session()->setFlash($insert['status'], $insert['message']);
                        redirect_url('building-management/customers');
                    }else{
                        $post->role = new SplArrayObject($post['roles']);
                        $post->setting = new SplArrayObject($post['settings']);
                        $post->profile = new SplArrayObject($post['profiles']);
                        $vars['post']   = $post;
                        session()->setFlash($insert['status'], $insert['message']);
                   }
                    break;

                case 'UPDATE':
                    $post->record_update_user = globals()->account->id;
                    print_out($post);
                    $update = $this->model->updateUser($post->getArrayCopy());
                    if ($update['status'] == 'success') {
                        session()->setFlash($update['status'], $upload['message']);
                        redirect_url('building-management/customers');
                    } else {
                        $post->role = new SplArrayObject($post['roles']);
                        $post->setting = new SplArrayObject($post['settings']);
                        $post->profile = new SplArrayObject($post['profiles']);
                        $vars['post']   = $post;
                        $vars['post']  = $post;
                        session()->setFlash($update['status'], $upload['message']);
                    }
                    break;
            }
        }
        $this->load->view('users/form', $vars);
    }
}