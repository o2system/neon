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

namespace Personal\Controllers;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Contents\Link;
use O2System\Kernel\Http\Message\UploadFile;
use Personal\Http\Controller;

/**
 * Class Profile
 *
 * @package Personal\Controllers
 */
class Profile extends Controller
{
    /**
     * Profile::index
     */
    public function index()
    {
        $this->presenter->page
            ->setHeader( 'Profile' )
            ->setDescription( 'The Personal Profile' );

        $this->presenter->assets->loadPackage('crop');
        $this->presenter->page->breadcrumb->createList( new Link( language()->getLine( 'PROFILE' ), base_url( 'personal/profile' ) ) );
        $this->view->load( 'profile' );
    }

    public function update() {
        $postData = $this->input->post();
        $postData = $postData !== FALSE ? $postData->getArrayCopy() : [];

        if (count($_FILES) > 0) {
            if ($this->handleFiles()) {
                return;
            }
        }

        $profileData = array_filter($postData, function ($value, $keyName) {
            return preg_match('/^profile/', $keyName);
        }, ARRAY_FILTER_USE_BOTH);

        $bioData = array_filter($postData, function ($value, $keyName) {
            return preg_match('/^biodata/', $keyName);
        }, ARRAY_FILTER_USE_BOTH);

        if (count($profileData) > 0) {
            $this->handleUpdateProfile($profileData);
        }

        if (count($bioData) > 0) {
            $this->handleUpdateBioData($bioData);
        }
    }

    private function handleUpdateProfile($profileData) {
        $updateData = [];

        foreach ($profileData as $key => $value) {
            $match = [];
            preg_match('/^profile-(\w+)-name/', $key, $match);

            if (count($match) === 2) {
                $updateData['name_'.$match[1]] = $value;
            }
        }

        if (count($updateData) > 0) {
            models('users')->rawUpdateProfile($updateData);
        }
    }

    private function handleUpdateBioData($bioData) {
        $updateData = [];

        foreach ($bioData as $key => $value) {
            if (preg_match('/status$/', $key)) {
                $updateData['marital'] = $value;
            } else {
                $k = strlen('biodata-');
                $updateData[substr($key, $k, strlen($key)-$k)] = $value;
            }
        }

        if (count($updateData) > 0) {
            models('users')->rawUpdateProfile($updateData);
        }
    }

    private function handleFiles() {
        $uploadKey = array_keys($_FILES)[0];

        if (in_array($uploadKey, ['cover', 'avatar'])) {
            $upload = new UploadFile($_FILES[$uploadKey]);
            $md5Filename = md5(random_bytes(10)).'.'.$upload->getExtension();
            if ($uploadKey === 'avatar') {
                $destPath = PATH_STORAGE.'users'.DIRECTORY_SEPARATOR.session()->get('account')->username.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR;
            } else {
                // Cover Destination Per Users Directory Must Exists
                $destPath = PATH_STORAGE . 'users' . DIRECTORY_SEPARATOR . session()->get('account')->username . DIRECTORY_SEPARATOR . 'cover' . DIRECTORY_SEPARATOR;
            }

            $upload->moveTo($destPath.$md5Filename);
            models('users')->rawUpdateProfile([$uploadKey=>$md5Filename]);

            return true;
        }

        return false;
    }
}