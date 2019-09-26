<?php
/**
 * Created by PhpStorm.
 * User: cicle creative
 * Date: 18/09/2019
 * Time: 12:06
 */

namespace App\Api\Modules\Products\Controllers;


use App\Api\Http\AccessControl\Controllers\AuthenticatedController;
use O2System\Filesystem\Handlers\Uploader;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;

class Requests extends AuthenticatedController
{
    public $imagePath = PATH_STORAGE . 'images/upload/';
    public function methods()
    {
        if($member = globals()->account->member){
            if($this->input->files()){
                $post = [];
                $imagePath = $this->imagePath;
                if ( ! file_exists($imagePath)) {
                    mkdir($imagePath, 0777, true);
                }

                $upload = new Uploader();
                $upload->setAllowedExtensions(['png', 'jpg', 'jpeg', 'JPG', 'PNG', 'JPEG']);
                $upload->setPath($imagePath);
                $upload->process('webcam');

                if ($upload->getErrors()) {
                    $errors = new Unordered();
                    foreach ($upload->getErrors() as $code => $error) {
                        $errors->createList($error);
                    }
                    session()->setFlash('danger', $errors);
                    $this->sendError(401, $errors);
                } else {
                    if (isset($data)) {
                        if (is_file($imagePath . $data->photo)) {
                            unlink($imagePath . $data->photo);
                        }
                    }
                    $post[ 'method' ] = 'FOTO';
                    $post[ 'content' ] = $upload->getUploadedFiles()->first()[ 'name' ];
                    if($this->model->insert($post, $member)){
                        $this->sendPayload([
                            'message'   => 'success insert'
                        ]);
                    }else{
                        $this->sendError(501, 'failed insert');
                    }

                }
            }elseif ($link = input()->post('link')){

            }else{
                $this->sendError('401', 'request false');
            }
        }else{
            $this->sendError('401', 'loogin');
        }

    }
}