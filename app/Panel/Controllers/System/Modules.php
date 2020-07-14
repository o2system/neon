<?php
/**
 * This file is part of the O2System Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */

// ------------------------------------------------------------------------

namespace App\Panel\Controllers\System;

// ------------------------------------------------------------------------

use App\Panel\Http\AccessControl\Controllers\AuthorizedController;
use O2System\Filesystem\Directory;
use O2System\Kernel\Http\Message\UploadFile;
use O2System\Spl\DataStructures\SplArrayObject;
use O2System\Spl\DataStructures\SplArrayStorage;

/**
 * Class Modules
 * @package App\Panel\Controllers\System
 */
class Modules extends AuthorizedController
{
    /**
     * Modules::index
     */
    public function index()
    {
        $modules = [];
        if (is_dir($modulesDirectoryPath = globals()->app->getPath() . DIRECTORY_SEPARATOR . 'Modules')) {
            $modulesDirectories = scandir($modulesDirectoryPath);
            $modulesDirectories = array_diff($modulesDirectories, ['.', '..']);

            foreach ($modulesDirectories as $moduleDirectory) {
                $moduleManifestFilePath = $modulesDirectoryPath . DIRECTORY_SEPARATOR . $moduleDirectory . DIRECTORY_SEPARATOR . 'module.json';
                $moduleManifest = file_get_contents($moduleManifestFilePath);
                $moduleManifest = new SplArrayObject(json_decode($moduleManifest, true));
                $moduleManifest['key'] = dash($moduleDirectory);
                $moduleManifest['active'] = false;

                if($result = models(\O2System\Framework\Models\Sql\System\Modules::class)->findWhere([
                    'namespace' => globals()->app->getNamespace() . 'Modules\\' . studlycase($moduleDirectory)
                ], 1)) {
                    $moduleManifest['active'] = true;
                }

                $modules[$moduleManifest['key']] = $moduleManifest;
            }
        }

        view('system/modules/index', [
            'modules' => $modules
        ]);
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::form
     */
    public function form()
    {
        view('system/modules/form');
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::install
     */
    public function install()
    {
        if (input()->get('file')) {
            if ($files = input()->files()) {
                foreach ($files as $file) {
                    if ($file instanceof UploadFile) {
                        if ($file->getExtension() === 'zip') {
                            $file->setPath($temporaryFilePath = PATH_CACHE . 'temporary' . DIRECTORY_SEPARATOR);

                            if ($file->store()) {
                                // Unzip module
                                $zip = new \ZipArchive();
                                if ($zip->open($modulePackageFile = $temporaryFilePath . $file->getName())) {
                                    $zip->extractTo(globals()->app->getRealPath() . 'Modules' . DIRECTORY_SEPARATOR);
                                    $zip->close();

                                    if ($this->processPackageFile($modulePackageFile)) {
                                        session()->setFlash('success', 'Module install successful');
                                    } else {
                                        session()->setFlash('danger', 'Module install failed');
                                    }
                                } else {
                                    output()->sendPayload([
                                        'message' => 'Module zip is unreadable'
                                    ]);
                                }

                                unlink($modulePackageFile);
                            }
                        } else {
                            session()->setFlash('danger', 'Module package must-be in zip format');
                        }
                    }
                }
            } else {
                session()->setFlash('danger', 'Module package upload failed');
            }
        } elseif (input()->get('folder')) {
            if (is_file($modulePackageFile = input()->post('filePath'))) {
                if ($this->processPackageFile($modulePackageFile)) {
                    session()->setFlash('success', 'Module install successful');
                } else {
                    session()->setFlash('danger', 'Module install failed');
                }
            } else {
                session()->setFlash('danger', 'Module package file not found!');
            }
        } elseif (input()->get('url')) {
            $fileUrl = input()->post('fileUrl');
            $temporaryFilePath = PATH_CACHE . 'temporary' . DIRECTORY_SEPARATOR;
            $modulePackageFile = $temporaryFilePath . pathinfo($fileUrl, PATHINFO_BASENAME);

            if (!is_writable($temporaryFilePath)) {
                if (!file_exists($temporaryFilePath)) {
                    mkdir($temporaryFilePath, 0777, true);
                }
            }

            if (!copy($fileUrl, $modulePackageFile)) {
                session()->setFlash('danger', 'Failed to copy module zip file from url: ' . $fileUrl);
            } else {
                $zip = new \ZipArchive();
                if ($zip->open($modulePackageFile)) {
                    $zip->extractTo(globals()->app->getRealPath() . 'Modules' . DIRECTORY_SEPARATOR);
                    $zip->close();

                    if ($this->processPackageFile($modulePackageFile)) {
                        session()->setFlash('success', 'Module install successful');
                    } else {
                        session()->setFlash('danger', 'Module install failed');
                    }
                } else {
                    session()->setFlash('danger', 'Module zip is unreadable');
                }

                unlink($modulePackageFile);
            }
        }

        redirect_url('system/modules');
    }

    // ------------------------------------------------------------------------

    /**
     * Modules::processFile
     *
     * @param string $modulePackageFile
     */
    protected function processPackageFile($modulePackageFile)
    {
        $filePath = globals()->app->getRealPath() . 'Modules' . DIRECTORY_SEPARATOR;

        $moduleSegment = dash(pathinfo($modulePackageFile, PATHINFO_FILENAME));
        $moduleName = studlycase($moduleSegment);

        if (is_dir($moduleDirectory = $filePath . $moduleName)) {
            if (is_file($moduleManifest = $moduleDirectory . DIRECTORY_SEPARATOR . 'module.json')) {
                $moduleManifest = json_decode(file_get_contents($moduleManifest), true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    $moduleManifest = new SplArrayObject($moduleManifest);
                }

                $app = models(\O2System\Framework\Models\Sql\System\Modules::class)->find('/' . implode('/',
                        globals()->app->getSegments()), 'endpoint');

                $_POST = [
                    'id_parent' => $app->id,
                    'endpoint' => implode('/', globals()->app->getSegments()) . '/' . $moduleSegment,
                    'namespace' => $moduleNamespace = $app->namespace . '\\Modules\\' . $moduleName,
                    'path' => $app->path . '/' . 'Modules' . '/' . $moduleName,
                    'record_type' => 'MODULE'
                ];

                if (models(\O2System\Framework\Models\Sql\System\Modules::class)->insert(input()->post())) {
                    if (is_file($installerScript = $moduleDirectory . 'module.php')) {
                        include($installerScript);

                        if (class_exists($installerScriptClassName = $moduleNamespace . '\\Installer')) {
                            (new $installerScriptClassName())->execute();
                        }
                        unlink($installerScript);
                    }

                    sleep(5);
                    unlink($modulePackageFile);

                    return true;
                }
            }
        }

        return false;
    }
    // ------------------------------------------------------------------------

    /**
     * Modules::detail
     *
     * @param string $segment
     */
    public function detail($segment)
    {
        view('system/modules/detail');
    }
    // ------------------------------------------------------------------------

    /**
     * Modules::activate
     *
     * @param string $module
     */
    public function activate($segment)
    {
        if($module = models(\O2System\Framework\Models\Sql\System\Modules::class)->findWhere([
            'endpoint' => (implode('/', globals()->app->getSegments())) . '/' . $segment
        ], 1)) {
            models(\O2System\Framework\Models\Sql\System\Modules::class)->publish($module->id);
        } else {
            models(\O2System\Framework\Models\Sql\System\Modules::class)->insert(new SplArrayStorage([
                'id_parent' => globals()->app->id,
                'endpoint' => implode('/', globals()->app->getSegments()) . '/' . $segment,
                'namespace' => $moduleNamespace = globals()->app->getNamespace() . '\\Modules\\' . studlycase($segment),
                'path' => str_replace([PATH_ROOT, '\\'], ['', '/'], globals()->app->getPath()) . '/' . 'Modules' . '/' . studlycase($segment),
                'record_type' => 'MODULE'
            ]));
        }

        redirect_url('system/modules');
    }
    // ------------------------------------------------------------------------

    /**
     * Module::uninstall
     *
     * @param string $segment
     */
    public function uninstall($segment)
    {
        if (is_dir($moduleDirectoryPath = globals()->app->getPath() . DIRECTORY_SEPARATOR . 'Modules' . studlycase($segment))) {
            if($module = models(\O2System\Framework\Models\Sql\System\Modules::class)->findWhere([
                'endpoint' => (implode('/', globals()->app->getSegments())) . '/' . $segment
            ], 1)) {
                models(\O2System\Framework\Models\Sql\System\Modules::class)->delete($module->id);
            }

            $moduleDirectory = new Directory($moduleDirectoryPath);
            $moduleDirectory->delete();
        }

        redirect_url('system/modules');
    }
}