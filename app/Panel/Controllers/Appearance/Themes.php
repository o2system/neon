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

namespace App\Panel\Controllers\Appearance;

// ------------------------------------------------------------------------

use App\Panel\Http\AccessControl\Controllers\AuthorizedController;
use O2System\Framework\Models\Sql\System\Modules;
use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class Themes
 * @package App\Panel\Controllers
 */
class Themes extends AuthorizedController
{
    /**
     * Themes::index
     */
    public function index()
    {
        if (is_dir($themeDirectoryPath = PATH_RESOURCES . 'site' . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR)) {
            $themeDirectories = scandir($themeDirectoryPath);
            $themeDirectories = array_diff($themeDirectories, ['.', '..']);

            foreach ($themeDirectories as $themeDirectory) {
                $themeManifestFilePath = $themeDirectoryPath . DIRECTORY_SEPARATOR . $themeDirectory . DIRECTORY_SEPARATOR . 'theme.json';
                $themeManifest = file_get_contents($themeManifestFilePath);
                $themeManifest = new SplArrayObject(json_decode($themeManifest, true));

                if (is_file($themeScreenshotFilePath = $themeDirectoryPath . DIRECTORY_SEPARATOR . $themeDirectory . DIRECTORY_SEPARATOR . 'theme.jpg')) {
                    $themeManifest->offsetSet('screenshot', resources_url($themeScreenshotFilePath));
                }

                $themes[dash($themeDirectory)] = $themeManifest;
            }
        }

        /** Current active theme */
        $module = models(Modules::class)->find('App\\Site', 'namespace');
    
        view('appearance/themes/index', [
            'themes' => $themes,
            'settings' => $module->settings
        ]);
    }

    // ------------------------------------------------------------------------

    public function process()
    {
        $_POST = [
            'settings' => [
                'theme' => input()->get('theme')
            ]
        ];

        if (models(Modules::class)->update(input()->post(), [
            'namespace' => 'App\\Site'
        ])) {
            redirect_url(input()->server('HTTP_REFERER'));
        } else {
            echo 'Failed';
        }
    }
}
