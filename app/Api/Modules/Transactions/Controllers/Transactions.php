<?php
/**
 * This file is part of the Circle Creative Web Application Project Boilerplate.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @author         PT. Lingkar Kreasi (Circle Creative)
 *  @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */
// ------------------------------------------------------------------------

namespace App\Api\Modules\Transactions\Controllers;

// ------------------------------------------------------------------------

use App\Api\Modules\System\Models\Users;
use App\Api\Modules\Transactions\Http\Controller;
use O2System\Filesystem\Handlers\Uploader;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;

/**
 * Class Transactions
 * @package App\Api\Modules\Transactions\Controllers
 */
class Transactions extends Controller
{
    /**
     * Transactions::$fillableColumnsWithRules
     *
     * @var array
     */


    public $fillableColumnsWithRules = [
        [
            'field'    => 'number',
            'label'    => 'Reference Model',
            'rules'    => 'required',
            'messages' => 'Reference Model cannot be empty!',
        ]
    ];


    public function addCart()
    {
        if($member = globals()->account->member){
            if($post = input()->post()) {
                if ($this->model->addCart($post, $member)) {
                    $this->sendPayload([
                        'message' => 'success insert'
                    ]);
                }
            }
        }else{
            session()->set('referer', $_SERVER['HTTP_REFERER']);
            $this->sendError(501, 'login');
        }
    }

    public function addWishlist()
    {
        if($member = globals()->account->member){
            if($post = input()->post()) {
                if ($this->model->addWishlist($post, $member)) {
                    $this->sendPayload([
                        'message' => 'success insert'
                    ]);
                }
            }
        }else{

            session()->set('referer', $_SERVER['HTTP_REFERER']);
            $this->sendError(501, 'login');
        }
    }

    public function updateWishlistToChart()
    {
        if($member = globals()->account->member){
            if($post = input()->post()) {
                if ($this->model->updateWishlist($post)) {
                    $this->sendPayload([
                        'message' => 'success update'
                    ]);
                }
            }
        }else{
            session()->set('referer', $_SERVER['HTTP_REFERER']);
            $this->sendError(501, 'login');
        }
    }

    public function paidDownPayment()
    {
        if($post = input()->post()){

            if($logs = $post->logs){
                models(\App\Api\Modules\Transactions\Models\Logs::class)->insert($logs);
            }
            $post[ 'content' ] = 'no-image.jpg';
            $post['name'] = 'confirm';
            unset($post->logs);
            if($files = input()->files()){
                foreach ($files as $key => $value) {
                    $imagePath = PATH_STORAGE . 'images/upload/';
                    if ( ! file_exists($imagePath)) {
                        mkdir($imagePath, 0777, true);
                    }

                    $upload = new Uploader();
                    $upload->setAllowedExtensions(['png', 'jpg', 'jpeg', 'JPG', 'PNG', 'JPEG']);
                    $upload->setPath($imagePath);
                    $upload->process($key);

                    if ($upload->getErrors()) {
                        $errors = new Unordered();
                        foreach ($upload->getErrors() as $code => $error) {
                            $errors->createList($error);
                        }

                        session()->setFlash('danger', $errors);

                        $redirectUrl = base_url('learning/lessons/form/');

                        redirect_url($redirectUrl);
                    } else {
                        if (isset($data)) {
                            if (is_file($imagePath . $data->photo)) {
                                unlink($imagePath . $data->photo);
                            }
                        }

                        $post[ 'content' ] = $upload->getUploadedFiles()->first()[ 'name' ];
                    }
                }
            }
            models(\App\Api\Modules\Transactions\Models\Metadata::class)
                ->insert($post->getArrayCopy());
        }else{
            $this->sendError(401, 'request post');
        }
    }

}
