<?php
/**
 * This file is part of the NEO ERP Application.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         PT. Lingkar Kreasi (Circle Creative)
 * @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */

// ------------------------------------------------------------------------

namespace App\Modules\Administrator\Controllers\System;

// ------------------------------------------------------------------------

use App\Modules\Administrator\Http\Controller;
use O2System\Framework\Libraries\Ui\Contents\Link;

/**
 * Class PhpSettings
 * @package Administrator\Controllers\System
 */
class PhpSettings extends Controller
{
    /**
     * PhpSettings::index
     */
    public function index()
    {
        $this->presenter->page
            ->setHeader('PHP Settings')
            ->setDescription('The server php settings information.');

        $this->presenter->page->breadcrumb->createList(new Link(language()->getLine('PHP_SETTINGS'),
            base_url('administrator/system/phpinfo')));

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

        $this->view->load('system/phpsettings', ['phpSettings' => $phpsettings]);
    }
}