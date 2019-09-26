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

namespace App\Api\Modules\Members\Controllers;

// ------------------------------------------------------------------------

use App\Api\Modules\Members\Http\Controller;
use O2System\Spl\DataStructures\SplArrayObject;
use App\Api\Modules\System\Models\Users;
use App\Api\Modules\Members\Models\Users as MembersUsers;

/**
 * Class Members
 * @package App\Api\Modules\Members\Controllers
 */
class Members extends Controller
{
    /**
     * Members::$fillableColumnsWithRules
     *
     * @var array
     */
    // public $fillableColumnsWithRules = [
    //     [
    //         'field'    => 'id_member_category',
    //         'label'    => 'Full Name',
    //         'rules'    => 'required',
    //         'messages' => 'Member Category cannot be empty!',
    //     ],
    //     [
    //         'field'    => 'id_geodirectory',
    //         'label'    => 'Geodirectory',
    //         'rules'    => 'required',
    //         'messages' => 'Geodirectory cannot be empty!',
    //     ],
    //     [
    //         'field'    => 'fullname',
    //         'label'    => 'Full Name',
    //         'rules'    => 'required',
    //         'messages' => 'Fullname cannot be empty!',
    //     ],
    //     [
    //         'field'    => 'number',
    //         'label'    => 'Number',
    //         'rules'    => 'required',
    //         'messages' => 'Number cannot be empty!',
    //     ]

    // ];

    public function create(){
//        print_out($this->input->post());
        if ($data = $this->input->post()){
            $vars['post'] = $data;
            parent::create($vars->post);
        }
    }

    public function delete($id=null)
    {
        if ($id) {
            $_POST['id'] = $id;
            parent::delete();
        }
    }

    public function registerMember()
    {
        if ($post = input()->post()) {
            $member = [];
            $profiles = [
                'name' => $post->username
            ];
            $post->record_status = 'DELETED';
            $sys_user = models(Users::class)->userInsert($post->getArrayCopy(), null, $profiles);
            if ($sys_user['status'] == 'success') {
                if ($sys_user['id_sys_user']) {
                    $id_sys_user = $sys_user['id_sys_user'];
                    $token = $sys_user['token'];
                }
            } else {
                return $this->sendPayload([
                    'status' => 501,
                    'message' => $sys_user['message']
                ]);
            }
            
            $member = [
                'fullname' => $post->username,
                'meta' => [
                    'email' => $post->email,
                    'phone' => $post->phone,
                    'username' => $post->username
                ]
            ];
            
            if ($result = $this->model->insert($member)) {
                $id_member = $result['id_member'];
                if (false == models(MembersUsers::class)->insert([
                    'id_member' => $id_member,
                    'id_sys_user' => $id_sys_user
                ])) {
                    models(Users::class)->delete($id_sys_user);
                    $this->model->deleteManyBy(['id' => $id_member]);
                    $this->sendError(501, 'Failed Register Member Users Request');
                }
                $userkey="gcnj43"; // userkey lihat di zenziva
                $passkey="Smartworks"; // set passkey di zenziva
                $message= 'your kreditimpian verification code is '.$token;
                $url = "https://reguler.zenziva.net/apps/smsapi.php";
                $curlHandle = curl_init();
                curl_setopt($curlHandle, CURLOPT_URL, $url);
                curl_setopt($curlHandle, CURLOPT_POSTFIELDS, 'userkey='.$userkey.'&passkey='.$passkey.'&nohp='.$sets['phone'].'&pesan='.urlencode($message));
                curl_setopt($curlHandle, CURLOPT_HEADER, 0);
                curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
                curl_setopt($curlHandle, CURLOPT_POST, 1);
                $results = curl_exec($curlHandle);
                curl_close($curlHandle);
                $message = 'your kreditimpian verification code is '.$token;
                $param = http_build_query([
                    'userkey' => 'gcnj43',
                    'passkey' => 'Smartworks',
                    'nohp' => $post->phone,
                    'pesan' => $message,
                ]);
                $url = 'https://reguler.zenziva.net/apps/smsapi.php?'.$param;
                file_get_contents($url);
                // $email = new \O2System\Framework\Libraries\Email();
                // $email->subject('Kredit Impian Activation');
                // $email->from('noreply@kreditimpian.id');
                // $email->to($post->email);
                // $email->template('email/account/activate', [
                //     'activation_url' => base_url('register/activation/'.$token),
                // ]);
                // $email->send();
                $this->sendPayload([
                        'code' => 201,
                        'Successful Insert Request',
                    ]);
            } else {
                models(Users::class)->delete($id_sys_user);
                $this->sendError(501, 'Failed Register Request');
            }
        } else {
            $this->sendError(400, 'Post parameters cannot be empty!');
        }
    }

}
