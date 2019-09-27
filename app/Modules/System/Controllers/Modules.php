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

namespace App\Modules\System\Controllers;


use App\Api\Modules\System\Models\Modules\Roles;
use App\Api\Modules\System\Models\Modules\Users;
use App\Modules\System\Http\Controller;
use O2System\Spl\DataStructures\SplArrayObject;

class Modules extends Controller
{
    public $model = '\App\Api\Modules\System\Models\Modules';
    public function index()
    {
        view('module/index', [
            'modules' => $this->model->all()
        ]);
    }

    /**
     * @param null $id
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     */
    public function form($id = null)
    {
        presenter()->page->setTitle('FORM_ADD');
        $vars = [
            'post'    => new SplArrayObject(),
            'options'  => new SplArrayObject([
                'types'   => new SplArrayObject(['APP','MODULE','COMPONENT','PLUGIN']),
                'parents'   => $this->model->findWhere([
                    'id !=' => $id
                ]),
                'users' => models(Users::class)->all(),
                'roles' => models(Roles::class)->allWithPaging()
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
            if ($this->model->form($post)) {
                $redirectUrl = base_url('/system/modules');
                redirect_url($redirectUrl);
            } else {
                session()->setFlash('danger', language('FAILED_UPDATE'));
            }
        }
        view('module/form', $vars);
    }

    /**
     * @param null $id
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     */
    public function detail($id = null)
    {
        set_time_limit(3600);
        if($module = $this->model->find($id)){
            presenter()->page->setTitle($module->namespace);
            view('module/detail',[
                'module'  => $module,
                'roles' => models(Roles::class)->findWhere([
                    'id_sys_module' => $module->parent->id
                ]),
                'users' => models(Users::class)->all()
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
                session()->setFlash('danger', language('FAILED_DELETE'));
            }
            $redirectUrl = base_url('/system/modules');
            redirect_url($redirectUrl);
        } else {
            output()->sendError(403);
        }
    }

    public function addUser()
    {
        if($post = input()->post()){
            $this->model->addUser($post);
        }
        redirect_url(base_url('/system/modules/detail/'.input()->get('id_module')));
    }
}