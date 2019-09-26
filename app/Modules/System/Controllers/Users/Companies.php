<?php
/**
 * Created by PhpStorm.
 * User: cicle creative
 * Date: 05/09/2019
 * Time: 16:02
 */

namespace App\Modules\Administrator\Controllers;


use App\Api\Modules\System\Models\Users;
use App\Modules\Administrator\Http\Controller;

class Companies extends Controller
{
    /**
     * @var \App\Api\Modules\Companies\Models\Companies
     */
    public $model = '\App\Api\Modules\Companies\Models\Companies';

    public function index()
    {
        view('companies/index', [
            'companies' => $this->model->all()
        ]);
    }

    public function detail($id)
    {
        if($post = input()->post()){
            models(\App\Api\Modules\Companies\Models\Companies\Users::class)
                ->qb->from('companies_users')
                ->delete(['companies_users.id_company' => $post->id_company]);
            $dataInsert = [];
            if(is_array($post->id_sys_user)){
                foreach ($post->id_sys_user as $key => $value){
                    $dataInsert[$key]['id_sys_user'] = $value;
                    $dataInsert[$key]['id_company'] = $post->id_company;
                    $dataInsert[$key]['record_create_user'] = globals()->account->id;
                    $dataInsert[$key]['record_update_user'] = globals()->account->id;
                }
                models(\App\Api\Modules\Companies\Models\Companies\Users::class)->insertMany($dataInsert);
                session()->setFlash('success', language()->getLine('MANAGE_USER_SUCCESS'));
            }
            redirect_url($_SERVER['HTTP_REFERER']);
        }

        if($company = $this->model->find($id)){
            view('companies/detail', [
                'company' => $company,
                'users' => models(Users::class)->all()
            ]);
        }else{
            session()->setFlash('danger',language()->getLine('DATA_NOT_FOUND'));
            redirect_url('administrator/companies');
        }
    }
}