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

namespace App\Modules\Transactions\Controllers;

// ------------------------------------------------------------------------

use App\Api\Modules\Master\Models\Currencies;
use App\Api\Modules\Products\Models\Categories;
use App\Api\Modules\Products\Models\Requests;
use App\Api\Modules\Products\Models\Requests\Images;
use App\Api\Modules\System\Models\Modules\Users\Notifications;
use App\Api\Modules\Transactions\Models\Logs;
use App\Libraries\Rajaongkir;
use App\Modules\Transactions\Http\Controller;
use O2System\Filesystem\Handlers\Uploader;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class Transactions
 * @package App\Modules\Transactions\Controllers
 */
class Transactions extends Controller
{
    /**
     * @var \App\Api\Modules\Transactions\Models\Transactions
     */
    public $model = '\App\Api\Modules\Transactions\Models\Transactions';

    public function index()
    {
        $get = input()->get();
        if ($get == false || $get == null) {
            $get = new SplArrayObject([
                'get' => 'false'
            ]);
        }
        
        view('index', [
            'transactions'  => $this->model->filter($get),
            'get'   => $get,
            'statistics' => $this->model->statistics(),
            'options'   => new SplArrayObject([
                'entries'   => range(10, 100, 10)
            ])
        ]);
    }


    public function table()
    {
        $get = input()->get();
        if ($get == false || $get == null) {
            $get = new SplArrayObject([
                'get' => 'false'
            ]);
        }
        
        view('table', [
            'transactions'  => $this->model->filter($get),
            'get'   => $get,
            'statistics' => $this->model->statistics(),
            'options'   => new SplArrayObject([
                'entries'   => range(10, 100, 10)
            ])
        ]);
    }
    public function detail()
    {
        if ($data = $this->model->find(input()->get('id'))){
            view('detail', [
                'transaction'   => $data
            ]);
        }else{
            redirect_url('transactions');
        }
    }

    public function chat()
    {
        if ($data = $this->model->find(input()->get('id'))){
            view('chat', [
                'transaction'   => $data
            ]);
        }else{
            redirect_url('transactions');
        }
    }

    public function edit($id = null)
    {
        if ($id) {
            $data = $this->model->find($id);
            $data->record_status = $data->record_status == "UNPUBLISH" ? "PUBLISH" : "UNPUBLISH";
            $this->model->update(['record_status' => $data->record_status], ['id' => $data->id]);
            redirect_url('transactions');
        } else {
            redirect_url('transactions');
        }
    }

    public function delete($id = null)
    {
        if ($id) {
            $this->model->delete($id);
            redirect_url('transactions');
        }else{
            $this->output->send(204);
        }
    }

    public function confirmRequest()
    {
        presenter()->page->setTitle(language('PRODUCT_REQUEST'));
        if($transaction = $this->model->find(input()->get('id'))){
            if($post = input()->post()){
                $metadata = $post->metadata;
                unset($post->metadata);
                if($metadata){
                    foreach ($metadata  as $field => $name){
                        if($metadata = models(Requests\Metadata::class)->findWhere([
                            'id_product_request'    => $transaction->reference_id,
                            'name'  => $field,
                        ], 1)){
                            models(Requests\Metadata::class)->update([
                                'id'    => $metadata->id,
                                'name'  => $field,
                                'content'   => $name,
                            ]);
                        }else{
                            models(Requests\Metadata::class)->insert([
                                'name'  => $field,
                                'content'   => $name,
                                'id_product_request'    => $transaction->reference_id
                            ]);
                        }
                    }

                }
                $post->id = $transaction->reference_id;
                models(Requests::class)->update($post->getArrayCopy());
                models(Logs::class)->insert([
                    'id_transaction' => $transaction->id,
                    'status'    => 'ON_REQUEST_CONFIRM',
                    'timestamp' => timestamp(),
                    'expires'   => date('Y-m-d h:i:s', strtotime("+30 days")),
                ]);
                models(Notifications::class)->insert([
                    'sys_module_user_sender_id' => globals()->account->user->id,
                    'sys_module_user_recipient_id' => $transaction->memberTransaction->member->user->id,
                    'reference_id'  => $transaction->id,
                    'reference_model'   => 'App\Api\Modules\Transactions\Models\Transactions',
                    'message'   => 'product anda sudah ditanggapi admin',
                    'metadata'  => 'ON_REQUEST_CONFIRM'
                ]);
                if($files = input()->files('photo')){
                    $image = [];
                    $image['id_product_request'] = $transaction->reference_id;
                    $imagePath = PATH_STORAGE . 'images/upload/';
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

                        session()->setFlash('danger', $errors);

                    } else {
                        if (isset($data)) {
                            if (is_file($imagePath . $data->photo)) {
                                unlink($imagePath . $data->photo);
                            }
                        }

                        $image[ 'filename' ] = $upload->getUploadedFiles()->first()[ 'name' ];
                        $image[ 'mime' ] = explode('/',$upload->getUploadedFiles()->first()[ 'mime' ])[0];
                        if($requestImage = models(Images::class)->findWhere([
                            'id_product_request'    => $image['id_product_request']
                        ], 1)){
                            $image['id'] = $requestImage->id;
                            models(Images::class)->update($image);
                        }else{
                            models(Images::class)->insert($image);
                        }
                    }
                }
                redirect_url('transactions');
            }
            $rajaongkir = new Rajaongkir();
            view('confirm-request',[
                'transaction'   => $transaction,
                'options'   => new SplArrayObject([
                    'currencies'    => models(Currencies::class)->all(),
                    'categories'    => models(Categories::class)->all(),
                    'origins'   =>  $rajaongkir->result->getCities()
                ])
            ]);
        }else{
            redirect_url('transactions');
        }
    }


}
