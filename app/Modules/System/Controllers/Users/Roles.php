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

namespace App\Modules\System\Controllers\Users;

use App\Api\Modules\System\Models\Modules;
use App\Modules\System\Http\Controller;
use O2System\Spl\DataStructures\SplArrayObject;
use O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException;

/**
 * Class Roles
 * @package App\Modules\Administrator\Controllers\Modules
 */
class Roles extends Controller
{
    /**
     * show all administrator roles
     */
    public $model = '\App\Api\Modules\System\Models\Modules\Roles';

    /**
     * Roles::index
     */
    public function index()
    {
        view('users/roles/index', [
            'roles' => $this->model->all()
        ]);
    }

    /**
     * @param null $id
     * @throws BadDependencyCallException
     */
    public function form($id = null)
    {
        presenter()->page->setTitle('FORM_ADD');
        $vars = [
            'post'    => new SplArrayObject(),
            'options'  => new SplArrayObject([
                'modules'   => models(Modules::class)->all(),
                // 'parents'   => $this->model->findWhere([
                //     'id !=' => $id
                // ])
            ])
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
            $post->code = strtoupper($post->code);
            $action = empty($post[ 'id' ]) ? 'INSERT' : 'UPDATE';
            switch ($action) {
                case 'INSERT':
                    unset($post->id);
                    $post->record_create_user = $post->record_update_user = globals()->account->id;

                    if ($this->model->insert($post->getArrayCopy())) {
                        session()->setFlash('success', language('SUCCESS_INSERT'));
                        $redirectUrl = base_url('system/users/roles');
                        redirect_url($redirectUrl);
                    } else {
                        session()->setFlash('danger', language('FAILED_INSERT'));
                    }
                    break;

                case 'UPDATE':
                    $post->record_update_user = globals()->account->id;

                    if ($this->model->update($post->getArrayCopy())) {
                        session()->setFlash('success', language('SUCCESS_UPDATE'));
                        $redirectUrl = base_url('system/users/roles');
                        redirect_url($redirectUrl);
                    } else {
                        session()->setFlash('danger', language('FAILED_UPDATE'));
                    }
                    break;
            }
        }
        view('users/roles/form', $vars);
    }

    /**
     * @param null $id
     * @throws BadDependencyCallException
     */
    public function detail($id = null)
    {
        if($role = $this->model->find($id)){
            view('users/roles/detail',[
                'role'  => $role,
                'modules'   => models(\App\Api\Modules\System\Models\Modules::class)
                    ->all(),
                'module'    => models(\App\Api\Modules\System\Models\Modules::class)->find(input()->get('id_segment'))

            ]);
        }else{
            $this->output->send(403);
        }
    }

    

    public function delete($id = null)
    {
        if (false !== ($data = $this->model->find($id))) {
            if ($this->model->delete($data->id)) {
                session()->setFlash('success', language('SUCCESS_DELETE'));
            } else {
                session()->setFlash('success', language('FAILED_DELETE'));
            }
            $redirectUrl = base_url('system/users/roles');
            redirect_url($redirectUrl);
        } else {
            output()->sendError(403);
        }
    }
}