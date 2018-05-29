<?php
/**
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */
// ------------------------------------------------------------------------

namespace Administrator\Controllers\Modules;

// ------------------------------------------------------------------------

use Administrator\Http\Controller;
use Administrator\Libraries\InstallerIntefaces;
use Administrator\Libraries\YbModuleInstaller;
use O2System\Framework\Libraries\Ui\Contents\Link;

/**
 * Class Installer
 *
 * @package Administrator\Controllers\Modules
 */
class Installer extends Controller
{
    /**
     * Installer::index
     */
    public function index()
    {
        $this->presenter->page
            ->setHeader( 'Modules Installer' )
            ->setDescription( 'The Modules Installer' );

        $this->presenter->page->breadcrumb->createList( new Link( language()->getLine( 'MODULES_INSTALLER' ), base_url( 'administrator/modules/installer' ) ) );

        if ($this->input->get('module-uri'))
        {
            $this->installModule($this->input->get('module-uri'), InstallerIntefaces::INSTALL_EXTERNAL_RESOURCE);
        }
        else if ($this->input->get('file-path'))
        {
            $this->installModule($this->input->get('file-path'), InstallerIntefaces::INSTALL_FROM_LOCAL_FILE);
        }

        $this->view->load( 'modules/installer' );
    }

    public function form()
    {
        $this->presenter->page
            ->setHeader( 'Users Manage' )
            ->setDescription( 'The Users Manage' );

        $this->presenter->page->breadcrumb->createList( new Link( language()->getLine( 'USERS_MANAGER' ), base_url( 'administrator/users/manage/form' ) ) );

        $this->view->load( 'users/manage/form' );
    }

    // Begin Model For Install Module 

    private function installModule($zipFile, $installType)
    {
        $ybInstaller = new YbModuleInstaller();

        if ($ybInstaller->installModule($zipFile, $installType))
        {
            $this->session->setFlash('success', 'Install Module Berhasil');
        }
        else
        {
            $this->session->setFlash('failed', 'Install Module Berhasil');
        }
    }
}