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


namespace App\Modules\Personal\Controllers;


use App\Modules\Personal\Http\Controller;
use App\Api\Modules\System\Models\Users;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Image\Uploader;
use O2System\Security\Generators\Token;
use O2System\Spl\Datastructures\SplArrayObject;

/**
 * Class Profile
 * @package App\Modules\Personal\Controllers
 */
class Profile extends Controller
{
    /**
     * @var string
     */
    public $model = '\App\Api\Modules\HumanResource\Models\Employee';

    /**
     *
     */
    public function index()
    {
        if(globals()->account->employee){
            if (false !== ($employee = $this->model->find(intval(globals()->account->employee->id)))) {
                view('profile/overview', [
                    'employee' => $employee,
                    'dates'    => models('options')->dates(),
                    'days'     => models('options')->days(),
                    'years'     => array_reverse(models('options')->years()),
                    'months'   => models('options')->months(),
                    'weeks'   => week_number_of_month(),
                ]);
            } else {
                output()->sendError(404);
            }
        }else{
            redirect_url('personal/setting');
        }
    }



    /**
     * Employee::form
     *
     * @param int|null $id
     *
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     */
    public function form($id = null)
    {
        if ($idEmployee = input()->get('id_employee')) {
            $id = $idEmployee;
        }
        $vars = [
            'post'    => new SplArrayObject([
                'user'  => new SplArrayObject()
            ]),
            'options' => new SplArrayObject([
                'genders'   => models('options')->genders(),
                'religions' => models('options')->religions(),
                'maritals'  => models('options')->maritals(),
                'dates'     => models('options')->dates(),
                'months'    => models('options')->months(),
                'years'     => models('options')->years(1945),

            ]),
        ];

        presenter()->page->setHeader('FORM_ADD');
        if (isset($id)) {
            if (false === ($data = $this->model->find(intval($id)))) {
                output()->sendError(404);
            }
            $vars[ 'post' ] = $data;
            presenter()->page->setHeader('FORM_EDIT');
        }

        if ($post = input()->post()) {
            if($post['add_user']){
                $user = $post->user;
                unset($post->user);
                if($user) {
                    foreach ($user as $key => $value) {
                        if ($key != 'password' && $key != 'password_confirm') {
                            if (models(Users::class)->isExist($key, $value)) {
                                session()->setFlash('danger', language(strtoupper($key) . '_HAS_EXISTS'));
                                redirect_url('human-resource/employee/form');
                            }
                        }
                    }
                    if ($user['password'] != $user['password_confirm']) {
                        session()->setFlash('danger', 'PASSWORD_NOT_EQUALS');
                        redirect_url('human-resource/employee/form');
                    }
                    $user['password'] = password_hash($post['password'], PASSWORD_DEFAULT);
                    $user['sso'] = 1234;
                    $user['pin'] = (new Token())->generate(8, 1);
                    unset($user['password_confirm']);
                }
            }else{
                unset($post->user);
            }
            unset($post->add_user);
            if (input()->files('photo')) {
                $imagePath = PATH_STORAGE . 'images/employee/avatar/'.dash(strtolower($post->name)).'/';
                $upload = new Uploader();
                $upload->setPath($imagePath);
                $upload->process('photo');

                if ($upload->getErrors()) {
                    $errors = new Unordered();

                    foreach ($upload->getErrors() as $code => $error) {
                        $errors->createList($error);
                    }

                    session()->setFlash('danger', $errors);
                    $redirectUrl = base_url('human-resource/employee/overview/', ['id_employee' => $post->id]) . '#profile';
                    redirect_url($redirectUrl);
                } else {
                    // Remove Previous Photo
                    if ( ! empty($post[ 'id' ])) {
                        $employee = $this->model->find($post[ 'id' ]);
                        if (is_file($imagePath . $employee[ 'photo' ])) {
                            unlink($imagePath . $employee->photo);
                        }
                    }
                    $post->photo = $upload->getUploadedFiles()->first()[ 'name' ];
                }
            }

            $action = empty($post[ 'id' ]) ? 'INSERT' : 'UPDATE';

            switch ($action) {
                case 'INSERT':
                    unset($post->id);
                    if ($this->model->insert($post->getArrayCopy())) {
                        $idEmployee = $this->model->getLastInsertId();
                        if($user){
                            models(Users::class)->insert($user);
                            $idUser = models(Users::class)->db->getLastInsertId();
                            models(\App\Api\Modules\HumanResource\Models\Employee\Users::class)->insert([
                                'id_hr_employee'    => $idEmployee,
                                'id_sys_user'       => $idUser
                            ]);
                        }
                        session()->setFlash('success', language('SUCCESS_INSERT', [$post->name]));
                        $redirectUrl = base_url('human-resource/employee/overview/', ['id_employee' => $idEmployee]) . '#profile';
                        redirect_url($redirectUrl);
                    } else {
                        print_out($this->model->getLastQuery());
                        session()->setFlash('danger', language('FAILED_INSERT', [$post->name]));
                    }
                    break;

                case 'UPDATE':
                    if(empty($post->date_resignation)){
                        unset($post->date_resignation);
                    }
                    if ($this->model->update($post->getArrayCopy())) {
                        session()->setFlash('success', language('SUCCESS_UPDATE', [$post->name]));
                        $redirectUrl = base_url('human-resource/employee/overview/', ['id_employee' => $post->id]);
                        redirect_url($redirectUrl);
                    } else {
                        session()->setFlash('danger', language('FAILED_UPDATE', [$post->name]));
                    }
                    break;
            }
        }

        view('profile/form', $vars);
    }

    // ------------------------------------------------------------------------
}
