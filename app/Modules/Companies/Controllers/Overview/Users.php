<?php
/**
 * This file is part of the Circle Creative Web Application Project Boilerplate.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 * @author         PT. Lingkar Kreasi (Circle Creative)
 * @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */

// ------------------------------------------------------------------------

namespace App\Modules\Companies\Controllers\Overview;

// ------------------------------------------------------------------------

use App\Modules\Companies\Http\Controller;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Image\Uploader;
use O2System\Spl\DataStructures\SplArrayObject;
use App\Api\Modules\System\Models\Users as SysUsers;
use O2System\Security\Generators\Token;

/**
 * Class Companies
 * @package App\Modules\Companies\Controllers
 */
class Users extends Controller
{
    public $model = '\App\Api\Modules\Companies\Models\Users';

    public function index($id = null)
    {
        if ($id) {
            $get = input()->get();
            if ($get == false || $get == null) {
                $get = new SplArrayObject([
                    'get' => 'false'
                ]);
            }
            $this->model->qb->where('id_company', $id);
            $users = $this->model->all();
            $modelCompany = models(\App\Api\Modules\Companies\Models\Companies::class)->find($id);
            $modelCompany->appendColumns = [
                'metadata'
            ];
            if (false !== ($data = $modelCompany)) {
                $this->presenter->page->setHeader('PAGE_DETAIL');
                $vars = [
                    'company' => $data,
                    'users' => $data->users_filter($get, $data->id),
                    'entries' => range(10, 100, 10),
                    'get' => $get,
                ];
                view('detail/users/index', $vars);
            } else {
                $this->output->send(204);
            }
        } else {
            $this->output->send(404);
        }
    }

    public function table($id)
    {
        if ($id) {
            $get = input()->get();
            if ($get == false || $get == null) {
                $get = new SplArrayObject([
                    'get' => 'false'
                ]);
            }
            $modelCompany = models(\App\Api\Modules\Companies\Models\Companies::class)->find($id);
            $modelCompany->appendColumns = [
                'metadata'
            ];
            if (false !== ($data = $modelCompany)) {
                $this->presenter->page->setHeader('PAGE_DETAIL');
                $vars = [
                    'company' => $data,
                    'users' => $data->users_filter($get, $data->id),
                    'entries' => range(10, 100, 10),
                    'get' => $get,
                ];
                view('detail/users/table', $vars);
            } else {
                $this->output->send(204);
            }
        } else {
            $this->output->send(404);
        }
    }

    public function form($id_company)
    {
        $vars = [
            'post' => new SplArrayObject(),
            'id_company' => $id_company
        ];
        $id_user = input()->get('id_user');
        if ($post = input()->post()){
            $profiles['name'] = $post->username;
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

                    if (!$id_sys_user) {
                        redirect_url('companies/overview/users/form/'.$id_company);
                    } else {
                        redirect_url('companies/overview/users/form/'.$id_company, ['id_user' => $id_sys_user]);
                    }
                } else {
                    // Remove Previous Photo
                    if ($id = $post->id) {
                        if ($data = models(SysUsers::class)->find($id)) {
                            if ($data->single_profile) {
                                if ($image = $data->single_profile->avatar) {
                                    if (file_exists($imagePath . $image)) {
                                        unlink($imagePath . $image);
                                    }
                                }
                            }
                        }
                    }
                    $profiles['avatar'] = $upload->getUploadedFiles()->first()[ 'name' ];
                }
            }
            if ($post->id) {
                if($update = models(SysUsers::class)->userUpdate($post->getArrayCopy(), null, $profiles)) {
                    return redirect_url('companies/overview/users/'.$id_company);
                }
            } else {
                $insert = models(SysUsers::class)->userInsert($post->getArrayCopy(), null, $profiles);
                if ($insert['status'] == 'success') {
                    $id_sys_user = $insert['id_sys_user'];
                    if ($this->model->insert(['id_company' => $id_company, 'id_sys_user' => $id_sys_user])) {
                        return redirect_url('companies/overview/users/'.$id_company);
                    } else {
                        models(SysUsers::class)->delete($id);
                        return redirect_url('companies/overview/users/'.$id_company);
                    }
                }
            }
            
        }

        if ($id_user) {
            if (false !== ($data = models(SysUsers::class)->find($id_user))) {
                $this->presenter->page->setHeader('PAGE_DETAIL');
                $vars['post'] = $data;
            } else {
                $this->output->send(204);
            }
        }

        view('detail/users/form', $vars);
    }

    public function delete($id_company)
    {
        if ($id_user = input()->get('id_user')) {
            models(SysUsers::class)->delete($id_user);
            if ($this->model->deleteBy(['id_sys_user' => $id_user])) {
                return redirect_url(input()->server('HTTP_REFERER'));
            }
        }
        return redirect_url(input()->server('HTTP_REFERER'));
    }
}
