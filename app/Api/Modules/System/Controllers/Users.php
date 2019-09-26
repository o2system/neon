<?php
/**
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */
// ------------------------------------------------------------------------

namespace App\Api\Modules\System\Controllers;

// ------------------------------------------------------------------------

use App\Api\Http\AccessControl\Controllers\PublicController;
use O2System\Security\Authentication\JsonWebToken;
use O2System\Security\Filters\Rules;
use O2System\Spl\Exceptions\Logic\DomainException;
use O2System\Spl\Exceptions\Logic\OutOfRangeException;
use O2System\Spl\Datastructures\SplArrayObject;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Filesystem\Handlers\Uploader;
use App\Api\Modules\System\Models\Users\Profiles;
use App\Api\Modules\Members\Models\Metadata;
use O2System\Kernel\Http\Message\UploadFile;

/**
 * Class Users
 * @package App\Api\Modules\System\Controllers
 */
class Users extends PublicController
{
    /**
     * Users::$fillableColumnsWithRules
     *
     * @var array
     */
    public $fillableColumnsWithRules = [
        [
            'field'    => 'email',
            'label'    => 'Email',
            'rules'    => 'required|email',
            'messages' => 'Email cannot be empty and it has to be an E-mail format!',
        ],
        [
            'field'    => 'msisdn',
            'label'    => 'MSISDN',
            'rules'    => 'required|msisdn',
            'messages' => 'MSISDN cannot be empty',
        ],
        [
            'field'    => 'username',
            'label'    => 'Username',
            'rules'    => 'required',
            'messages' => 'Username cannot be empty!',
        ],
        [
            'field'    => 'password',
            'label'    => 'Password',
            'rules'    => 'required',
            'messages' => 'Password cannot be empty!',
        ],
        [
            'field'    => 'pin',
            'label'    => 'Pin',
            'rules'    => 'required',
            'messages' => 'Pin cannot be empty!',
        ],
        [
            'field'    => 'sso',
            'label'    => 'SSO',
            'rules'    => 'required',
            'messages' => 'SSO cannot be empty!',
        ],
        [
            'field'    => 'token',
            'label'    => 'Token',
            'rules'    => 'optional',
        ],
    ];

    /**
     * Users::authenticate
     *
     * @throws OutOfRangeException
     * @throws DomainException
     */
    public function authenticate()
    {
        if($post = input()->post()) {
            $rules = new Rules($post);
            $rules->sets(
                [
                    [
                        'field'    => 'username',
                        'label'    => 'Username',
                        'rules'    => 'required',
                        'messages' => 'Username cannot be empty!',
                    ],
                    [
                        'field'    => 'password',
                        'label'    => 'Password',
                        'rules'    => 'required',
                        'messages' => 'Password cannot be empty!',
                    ],
                ]
            );

            if($rules->validate()) {
                if(services()->has('user')) {
                    if(services('user')->authenticate($post->username, $post->password)) {
                        $jwt = new JsonWebToken();
                        $token = $jwt->encode(session()->get('account'));

                        if(services('user')->loggedIn()) {
                            $this->sendPayload($token);
                        } else {
                            $this->sendPayload([
                                'success' => false,
                                'message' => 'Login failed, please try again in a few minutes!'
                            ]);
                        }
                    }
                } else {
                    $this->sendError(503, 'Service user is not exists!');
                }
            } else {
                $this->sendError(400, 'Username and password cannot be empty!');
            }
        } else {
            $this->sendError(400);
        }
    }

    public function membersUpdate()
    {
        if ($post = input()->post()) {
            $profile = $post->profile;
            $member = $post->member;
            unset($post->profile, $post->member, $post->id_sys_user);
            if($files = ($_FILES['photo']['name'] != null ? $_FILES : false)){
                $metadata = [];
                $filePath = PATH_STORAGE . 'images/users/';
                if(!file_exists($filePath)){
                    mkdir($filePath, 0777, true);
                }

                $upload = new Uploader();
                $upload->setPath($filePath);
                $upload->process('photo');

                if ($upload->getErrors()) {
                    $errors = new Unordered();

                    foreach ($upload->getErrors() as $code => $error) {
                        $errors->createList($error);
                    }
                    $this->output->send([
                        'error'  => $errors
                    ]);
                } else {
                    $filename = $upload->getUploadedFiles()->first()['name'];
                    $profile['avatar'] = $filename;
                    $member['photo'] = $filename;
                }
            }

            if (array_key_exists('citizen', $_FILES)) {
                $upload = new UploadFile($_FILES['citizen']);
                if (in_array($upload->getExtension(), ['png', 'jpg', 'jpeg'])) {
                    $upload->moveTo(PATH_STORAGE.'images'.DIRECTORY_SEPARATOR.'users'.DIRECTORY_SEPARATOR.$upload->getClientFilename());
                    $profile['citizen'] = $upload->getClientFilename();
                }
            }

            if (array_key_exists('taxpayer', $_FILES)) {
                $upload = new UploadFile($_FILES['taxpayer']);
                if (in_array($upload->getExtension(), ['png', 'jpg', 'jpeg'])) {
                    $upload->moveTo(PATH_STORAGE.'images'.DIRECTORY_SEPARATOR.'users'.DIRECTORY_SEPARATOR.$upload->getClientFilename());
                    $profile['taxpayer'] = $upload->getClientFilename();
                }
            }
            
            unset($profile['id_geodirectory']);
            
            if ($this->model->update($post->getArrayCopy())) {
                models(Metadata::class)->insert([
                    'id_member' => $member['id_member'],
                    'name' => 'photo',
                    'content' => $member['photo'],
                ]);
                if (models(Profiles::class)->update($profile, ['id_sys_user' => $profile['id_sys_user']])) {

                    $this->sendPayload([
                        'code' => 201,
                        'Successful Update Profiles Request and All is Complete',
                    ]);
                } else {
                    $this->sendError(501, 'Failed Update Profiles Request but Succeed Updating the Users');
                }
            } else {
                print_out(models(Profiles::class)->getLastQuery());
                $this->sendError(501, 'Failed Update Users Request');
            }
        } else {
            $this->sendError(400, 'Post parameters cannot be empty!');
        }
    }

    public function detectFiles()
    {
        $data = [];
        if ($files = $_FILES) {
            foreach ($files as $key => $file) {
                $imagePath = PATH_STORAGE.'images/users/';
                if (!file_exists($imagePath)) {
                    mkdir($imagePath, 0777, true);
                }

                $upload = new Uploader();
                $upload->setPath($imagePath);
                // $upload->setAllowedExtensions(['jpg', 'png', 'jpeg']);
                $upload->process($key);


                if ($upload->getErrors()) {
                    $this->output->send([
                        'errors' => $upload->getErrors(),
                    ]);
//                    print_out($upload);
                } else {
                    $file = $upload->getUploadedFiles()->first()['name'];
                    $data[$key] = $file;
//                    print_out($file);
                }
            }
        }

        return $data;
    }
}
