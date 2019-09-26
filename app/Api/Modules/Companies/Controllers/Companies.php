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

namespace App\Api\Modules\Companies\Controllers;

// ------------------------------------------------------------------------

use App\Api\Modules\Companies\Http\Controller;
use App\Api\Modules\Companies\Models\Categories;
use App\Api\Modules\System\Models\Users;
use App\Api\Modules\Companies\Models\Users as CompaniesUsers;

/**
 * Class Companies
 * @package App\Api\Modules\Companies\Controllers
 */
class Companies extends Controller
{
    /**
     * Companies::$fillableColumnsWithRules
     *
     * @var array
     */
    // public $fillableColumnsWithRules = [
    //     [
    //         'field'    => 'id_company_category',
    //         'label'    => 'Company Category',
    //         'rules'    => 'required|integer',
    //         'messages' => 'Company category cannot be empty!',
    //     ],
    //     [
    //         'field'    => 'code',
    //         'label'    => 'code',
    //         'rules'    => 'required',
    //         'messages' => 'Company code cannot be empty!',
    //     ],
    //     [
    //         'field'    => 'name',
    //         'label'    => 'Company Name',
    //         'rules'    => 'required',
    //         'messages' => 'Company code cannot be empty!',
    //     ],
    // ];
    // 
    public function registerMerchant()
    {
        if ($post = input()->post()) {
            $profiles = $post->profiles;
            $companies = $post->companies;
            unset($post->companies, $post->profiles);
            $sys_user = models(Users::class)->userInsert($post->getArrayCopy(), null, $profiles);
            if ($sys_user['status'] == 'success') {
                if ($sys_user['id_sys_user']) {
                    $id_sys_user = $sys_user['id_sys_user'];
                    $token = $sys_user['token'];
                }
            } else {
                return $this->sendError(501, $sys_user['message']);
            }

            $companies['id_company_category'] = models(Categories::class)->find('MERCHANT', 'code')->id;
            $companies['meta'] = [
                'email' => $post->email,
                'phone' => $post->phone
            ];
            if ($result = $this->model->insert($companies)) {
                $id_company = $result['id_company'];
                if (false == models(CompaniesUsers::class)->insert([
                    'id_company' => $id_company,
                    'id_sys_user' => $id_sys_user
                ])) {
                    models(Users::class)->deleteManyBy(['id' => $id_sys_user]);
                    $this->model->deleteManyBy(['id' => $id_company]);
                    return $this->sendError(501, 'Failed Register Companies Users Request');
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
                $email = new \O2System\Framework\Libraries\Email();
                $email->subject(subject_user_registration());
                $email->from(from_email());
                $email->to($post->email);
                $email->template(PATH_RESOURCES.'views/email/account/activate.phtml', [
                    'activation_url' => base_url('register/activation/'.$token),
                ]);
                $email->send();
                $this->sendPayload([
                        'code' => 201,
                        'Successful Insert Request',
                    ]);
            } else {
                models(Users::class)->deleteManyBy(['id' => $id_sys_user]);
                $this->sendError(501, 'Failed Register Request');
            }
        } else {
            $this->sendError(400, 'Post parameters cannot be empty!');
        }
    }

    public function registerCreditor()
    {
        if ($post = input()->post()) {
            $profiles = $post->profiles;
            $companies = $post->companies;
            unset($post->companies, $post->profiles);
            $sys_user = models(Users::class)->userInsert($post->getArrayCopy(), null, $profiles);
            if ($sys_user['status'] == 'success') {
                if ($sys_user['id_sys_user']) {
                    $id_sys_user = $sys_user['id_sys_user'];
                    $token = $sys_user['token'];
                }
            } else {
                return $this->sendError(501, $sys_user['message']);
            }
            $companies['id_company_category'] = models(Categories::class)->find('CREDITOR', 'code')->id;
            $companies['meta'] = [
                'email' => $post->email,
                'phone' => $post->phone
            ];
            if ($result = $this->model->insert($companies)) {
                $id_company = $result['id_company'];
                if (false == models(CompaniesUsers::class)->insert([
                    'id_company' => $id_company,
                    'id_sys_user' => $id_sys_user
                ])) {
                    models(Users::class)->deleteManyBy(['id' => $id_sys_user]);
                    $this->model->deleteManyBy(['id' => $id_company]);
                    return $this->sendError(501, 'Failed Register Companies Users Request');
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
                $email = new \O2System\Framework\Libraries\Email();
                $email->subject(subject_user_registration());
                $email->from(from_email());
                $email->to($post->email);
                $email->template(PATH_RESOURCES.'views/email/account/activate.phtml', [
                    'activation_url' => base_url('register/activation/'.$token),
                ]);
                $email->send();
                $this->sendPayload([
                        'code' => 201,
                        'Successful Insert Request',
                    ]);
            } else {
                models(Users::class)->deleteManyBy(['id' => $id_sys_user]);
                $this->sendError(501, 'Failed Register Request');
            }
        } else {
            $this->sendError(400, 'Post parameters cannot be empty!');
        }
    }
}
