<?php
/**
 * This file is part of the O2System Content Management System package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian
 * @copyright      Copyright (c) Steeve Andrian
 */
// ------------------------------------------------------------------------

namespace Administrator\Controllers;

// ------------------------------------------------------------------------

use Administrator\Http\Controller;
use O2System\Kernel\Http\Message\UploadFile;

/**
 * Class Settings
 * @package Administrator\Controllers
 */
class Settings extends Controller {
    private $settingsModel = NULL;

    public function __construct()
    {
        $this->settingsModel = new \Administrator\Models\Settings();
    }

    public function index($segments = '') {
        $lSegments = strtolower($segments);

        if ($this->input->post()) {
            if ($this->handlePost(in_array($segments, ['writing', 'discussion', 'traffic']) ? $segments : 'general')) {
                session()->setFlash('success', 'Save Setting Success');
            } else {
                session()->setFlash('success', 'Save Setting Success');
            }
        }

        switch ($lSegments) {
            case 'traffic':
                $traffic = $this->settingsModel->getSetting('traffic');
                presenter()->page->setContent(
                    view()->load('settings/traffic', $traffic, true)
                );
                break;
            case 'discussion':
                $discuss = $this->settingsModel->getSetting('discussion');
                presenter()->page->setContent(
                    view()->load('settings/discussion', $discuss, true)
                );
                break;
            case 'writing':
                $writing = $this->settingsModel->getSetting('writing');
                presenter()->page->setContent(
                    view()->load('settings/writing', $writing, true)
                );
                break;
            default:
                $general = $this->settingsModel->getSetting('general');
                presenter()->page->setContent(
                    view()->load('settings/general', $general, true)
                );
                break;
        }

        view('settings/settings');
    }

    private function handlePost($segment) {
        $defaultValue = [];

        switch ($segment) {
            case 'general':
                if ($this->input->post('saveSite')) {
                    $defaultValue = \Administrator\Models\Settings::DEFAULT_SITE;
                } else if ($this->input->post('saveMeta')) {
                    $defaultValue = \Administrator\Models\Settings::DEFAULT_SITE_META;
                } else {
                    $defaultValue = \Administrator\Models\Settings::DEFAULT_SITE_PRIVACY;
                }
                break;
            case 'writing':
                if ($this->input->post('composingSave')) {
                    $defaultValue = \Administrator\Models\Settings::DEFAULT_COMPOSING;
                } else {
                    $defaultValue = \Administrator\Models\Settings::DEFAULT_WRITE;
                }
                break;
            case 'discussion':
                if ($this->input->post('articleSave')) {
                    $defaultValue = \Administrator\Models\Settings::DEFAULT_ARTICLES;
                } else {
                    $defaultValue = \Administrator\Models\Settings::DEFAULT_DISCUSS;
                }
                break;
            case 'traffic':
                if ($this->input->post('saveRelated')) {
                    $defaultValue = \Administrator\Models\Settings::DEFAULT_TRAFFIC_RELATED;
                } else if ($this->input->post('saveMobile')) {
                    $defaultValue = \Administrator\Models\Settings::DEFAULT_TRAFFIC_MOBILE;
                } else if ($this->input->post('saveSeo')) {
                    $defaultValue = \Administrator\Models\Settings::DEFAULT_TRAFFIC_SEO;
                } else if ($this->input->post('saveAnalytic')) {
                    $defaultValue = \Administrator\Models\Settings::DEFAULT_TRAFFIC_GOOGLE_ANALYTIC;
                } else {
                    $defaultValue = \Administrator\Models\Settings::DEFAULT_TRAFFIC_SITE_VERIFICATION;
                }
                break;
            default:
                break;
        }

        $postData = $this->input->post();
        $rawPost = $postData->getArrayCopy();

        if (count($defaultValue) > 0) {
            if ($segment === 'general') {
                if (array_key_exists('site_logo', $_FILES)) {
                    $upload = new UploadFile($_FILES['site_logo']);
                    if (in_array($upload->getExtension(), ['png', 'jpg', 'jpeg'])) {
                        $upload->moveTo(PATH_PUBLIC.'assets'.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$upload->getClientFilename());
                        $rawPost['site_logo'] = $upload->getClientFilename();
                    }
                }
            }

            $defaultValue = array_merge($defaultValue, $rawPost);

            $this->settingsModel->saveSettings($defaultValue);

            return true;
        }

        return false;
    }
}