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
// ------------------------------------------------------------------------

namespace App\Manage\Modules\System\Controllers\Users;

// ------------------------------------------------------------------------

use App\Manage\Modules\System\Http\Controller;
use O2System\Kernel\Http\Message\UploadFile;
use O2System\Security\Generators\Token;

/**
 * Class Settings
 * @package Administrator\Controllers
 */
class Settings extends Controller 
{
    public $model  = '\App\Api\Modules\System\Models\Users';

    public function index()
    {
        if ($post = input()->post()) {
            $param = $post->getArrayCopy();
            if (!globals()->account->id) {
                output()->sendError(404);
            } else {
                $param['id'] = globals()->account->id;
            }

            if ($param[ 'password' ] != $param[ 'password_confirm' ]) {
                 $update = ['status' => 'danger', 'message' => language()->getLine('ALERT_PASSWORD_MISMATCH')];
                 session()->setFlash($update['status'], $update['message']);
                 return redirect_url('system/users/settings');
            }
            unset($param[ 'password_confirm' ]);
            $param['msisdn'] = "085280790088";
            $param[ 'pin' ] = $token = (new Token())->generate(10, 2);
            $param[ 'sso' ] = 123345;

            // Password mutation.
            if ($param[ 'password' ] == null) {
                unset($param[ 'password' ]);
            } else {
                $param[ 'password' ] = password_hash($param[ 'password' ], PASSWORD_DEFAULT);
            }
            if ($this->model->update($param)) {
                session()->setFlash('success', 'SUCCESS_UPDATE');
                redirect_url('system/users/settings');
            } else {
                session()->setFlash('danger', 'FAILED_UPDATE');
            }
        }


        if ($id = globals()->account->id) {
            if (false === ($data = $this->model->find($id))) {
                output()->sendError(404);
            }
            $data->password = password_hash($data['password'], PASSWORD_DEFAULT);
            $vars[ 'post' ] = $data;
            presenter()->page->setTitle('FORM_EDIT');
            view('users/settings/form', $vars);

        } else {
            output()->sendError(404);
        }
    }
    // private $settingsModel = NULL;

    // public function __construct()
    // {
    //     $this->settingsModel = self::class;
    // }

    // public function index($segments = '') {
    //     $lSegments = strtolower($segments);

    //     if ($this->input->post()) {
    //         if ($this->handlePost(in_array($segments, ['writing', 'discussion', 'traffic']) ? $segments : 'general')) {
    //             session()->setFlash('success', 'Save Setting Success');
    //         } else {
    //             session()->setFlash('success', 'Save Setting Success');
    //         }
    //     }

    //     switch ($lSegments) {
    //         case 'traffic':
    //             $traffic = $this->settingsModel->getSetting('traffic');
    //             presenter()->page->setContent(
    //                 view()->load('settings/traffic', $traffic, true)
    //             );
    //             break;
    //         case 'discussion':
    //             $discuss = $this->settingsModel->getSetting('discussion');
    //             presenter()->page->setContent(
    //                 view()->load('settings/discussion', $discuss, true)
    //             );
    //             break;
    //         case 'writing':
    //             $writing = $this->settingsModel->getSetting('writing');
    //             presenter()->page->setContent(
    //                 view()->load('settings/writing', $writing, true)
    //             );
    //             break;
    //         default:
    //             $general = $this->settingsModel->getSetting('general');
    //             presenter()->page->setContent(
    //                 view()->load('settings/general', $general, true)
    //             );
    //             break;
    //     }

    //     view('settings/settings');
    // }

    // private function handlePost($segment) {
    //     $defaultValue = [];

    //     switch ($segment) {
    //         case 'general':
    //             if ($this->input->post('saveSite')) {
    //                 $defaultValue = self::DEFAULT_SITE;
    //             } else if ($this->input->post('saveMeta')) {
    //                 $defaultValue = self::DEFAULT_SITE_META;
    //             } else {
    //                 $defaultValue = self::DEFAULT_SITE_PRIVACY;
    //             }
    //             break;
    //         case 'writing':
    //             if ($this->input->post('composingSave')) {
    //                 $defaultValue = self::DEFAULT_COMPOSING;
    //             } else {
    //                 $defaultValue = self::DEFAULT_WRITE;
    //             }
    //             break;
    //         case 'discussion':
    //             if ($this->input->post('articleSave')) {
    //                 $defaultValue = self::DEFAULT_ARTICLES;
    //             } else {
    //                 $defaultValue = self::DEFAULT_DISCUSS;
    //             }
    //             break;
    //         case 'traffic':
    //             if ($this->input->post('saveRelated')) {
    //                 $defaultValue = self::DEFAULT_TRAFFIC_RELATED;
    //             } else if ($this->input->post('saveMobile')) {
    //                 $defaultValue = self::DEFAULT_TRAFFIC_MOBILE;
    //             } else if ($this->input->post('saveSeo')) {
    //                 $defaultValue = self::DEFAULT_TRAFFIC_SEO;
    //             } else if ($this->input->post('saveAnalytic')) {
    //                 $defaultValue = self::DEFAULT_TRAFFIC_GOOGLE_ANALYTIC;
    //             } else {
    //                 $defaultValue = self::DEFAULT_TRAFFIC_SITE_VERIFICATION;
    //             }
    //             break;
    //         default:
    //             break;
    //     }

    //     $postData = $this->input->post();
    //     $rawPost = $postData->getArrayCopy();

    //     if (count($defaultValue) > 0) {
    //         if ($segment === 'general') {
    //             if (array_key_exists('site_logo', $_FILES)) {
    //                 $upload = new UploadFile($_FILES['site_logo']);
    //                 if (in_array($upload->getExtension(), ['png', 'jpg', 'jpeg'])) {
    //                     $upload->moveTo(PATH_PUBLIC.'assets'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$upload->getClientFilename());
    //                     $rawPost['site_logo'] = $upload->getClientFilename();
    //                 }
    //             }
    //         }

    //         $defaultValue = array_merge($defaultValue, $rawPost);

    //         $this->settingsModel->saveSettings($defaultValue);

    //         return true;
    //     }

    //     return false;
    // }
}