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

namespace Administrator\Controllers\Packages;

// ------------------------------------------------------------------------

use Administrator\Http\Controller;
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
}