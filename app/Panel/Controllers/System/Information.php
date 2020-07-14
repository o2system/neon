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

use App\Panel\Controllers\System;

/**
 * Class Information
 * @package App\Panel\Controllers\System
 */
class Information extends System
{
    /**
     * Information::index
     */
    public function index()
    {
        view('system/information/index', [
            'configurations' => config(),
        ]);
    }

    // ------------------------------------------------------------------------

    /**
     * Information::folderPermissions
     */
    public function folderPermissions()
    {
        presenter()->page
            ->setHeader('Folder Permissions')
            ->setDescription('The application folder permissions.');

        $folderPermissions['app'] = is_really_writable( PATH_APP );
        $folderPermissions['public'] = is_really_writable( PATH_PUBLIC );
        $folderPermissions['cache'] = is_really_writable(PATH_CACHE);
        $folderPermissions['storage'] = is_really_writable(PATH_STORAGE);
        $folderPermissions['vendor'] = is_really_writable( PATH_VENDOR );

        view('system/information/folder-permissions', ['folderPermissions' => $folderPermissions]);
    }

    // ------------------------------------------------------------------------

    /**
     * Information::phpSettings
     */
    public function phpSettings()
    {
        presenter()->page
            ->setHeader('PHP Settings')
            ->setDescription('The server php settings information.');

        $phpsettings = ini_get_all();

        // remove sensitive information
        unset(
            $phpsettings['extension_dir'],
            $phpsettings['sendmail_path'],
            $phpsettings['url_rewriter.tags'],
            $phpsettings['mysqli.default_socket'],
            $phpsettings['opcache.lockfile_path'],
            $phpsettings['pdo_mysql.default_socket'],
            $phpsettings['xdebug.trace_output_dir']
        );

        view('system/information/php-settings', ['phpSettings' => $phpsettings]);
    }

    // ------------------------------------------------------------------------

    /**
     * Information::phpInfo
     */
    public function phpInfo()
    {
        presenter()->page
            ->setHeader('PHP Information')
            ->setDescription('The server php information.');

        ob_start();
        phpinfo();
        $contents = ob_get_contents();
        ob_end_clean();

        // the name attribute "module_Zend Optimizer" of an anker-tag is not xhtml valide, so replace it with "module_Zend_Optimizer"
        $phpinfo = (str_replace("module_Zend Optimizer", "module_Zend_Optimizer",
            preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $contents)));

        $phpinfo = str_replace('<table>', '<table class="table table-bordered table-striped">', $phpinfo);

        view('system/information/php-info', ['phpinfo' => $phpinfo]);
    }
}
