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
 * Class FolderPermissions
 * @package Administrator\Controllers\System
 */
class FolderPermissions extends Controller
{
    /**
     * FolderPermissions::index
     */
    public function index()
    {
        $this->presenter->page
            ->setHeader('Folder Permissions')
            ->setDescription('The application folder permissions.');

        $this->presenter->page->breadcrumb->createList(new Link(language()->getLine('FOLDER_PERMISSIONS'),
            base_url('administrator/system/folder-permissions')));

        $folderPermissions['app'] = is_really_writable( PATH_APP );
        $folderPermissions['public'] = is_really_writable( PATH_PUBLIC );
        $folderPermissions['cache'] = is_really_writable(PATH_CACHE);
        $folderPermissions['storage'] = is_really_writable(PATH_STORAGE);
        $folderPermissions['vendor'] = is_really_writable( PATH_VENDOR );

        $this->view->load('system/folder-permissions', ['folderPermissions' => $folderPermissions]);
    }
}