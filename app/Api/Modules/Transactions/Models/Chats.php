<?php
/**
 * Created by PhpStorm.
 * User: cicle creative
 * Date: 20/09/2019
 * Time: 10:10
 */

namespace App\Api\Modules\Transactions\Models;


use App\Api\Modules\System\Models\Users;
use O2System\Filesystem\Handlers\Uploader;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Framework\Models\Sql\Model;

class Chats extends Model
{
    public $table = 'transactions_chats';

    public $uploadedImageFilePath = PATH_STORAGE . 'images/upload/';

    public $appendColumns = ['message', 'image'];

    public function transaction()
    {
        return $this->belongsTo(Transactions::class, 'id_transaction');
    }

    public function message()
    {
        return $this->row->metadata->message;
    }


    public function image()
    {
        if($image = $this->row->metadata->image){
            if(is_file($path = $this->uploadedImageFilePath.$image)){
                return storage_url($path);
            }
        }
        return null;
    }



    public function insert(array $sets)
    {
        if(input()->files('upload')){
            $imagePath = $this->uploadedImageFilePath;
            if ( ! file_exists($imagePath)) {
                mkdir($imagePath, 0777, true);
            }

            $upload = new Uploader();
            $upload->setPath($imagePath);
            $upload->process('upload');

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
                $metadata = $sets['metadata'];
                unset($sets['metadata']);
                $metadata['image'] = $upload->getUploadedFiles()->first()[ 'name' ];
                $sets['metadata'] = $metadata;
            }
        }
        if(parent::insert($sets))
        {
            return $this->find($this->db->getLastInsertId());
        }
    }

    public function user()
    {
        $user =  $this->belongsTo(Users::class, 'id_sys_user');
        if($user->profile){
            $profile = $user->profile;
        }
        if($user->member){
            $profile = $user->member;
        };
        return $profile;
    }
}