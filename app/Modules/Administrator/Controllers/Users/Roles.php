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

namespace Administrator\Controllers\Users;

// ------------------------------------------------------------------------

use Administrator\Http\Controller;
use O2System\Framework\Libraries\Ui\Contents\Link;


/**
 * Class Roles
 *
 * @package Administrator\Controllers\Users
 */
class Roles extends Controller
{
    /**
     * Roles::index
     */
    public function index()
    {
        $this->presenter->page
            ->setHeader( 'Manage Users Roles' )
            ->setDescription( 'The Users Roles' );

        $this->presenter->page->breadcrumb->createList( new Link( language()->getLine( 'MANAGE_USERS_ROLES' ), base_url( 'administrator/users/roles' ) ) );

        $this->view->load( 'users/roles/table' );
    }

    /**
     * Roles::form
     */
    public function form()
    {
        $this->presenter->page
            ->setHeader( 'Manage Users Roles' )
            ->setDescription( 'The Users Roles' );

        $this->presenter->page->breadcrumb->createList( new Link( language()->getLine( 'MANAGE_USERS_ROLES' ), base_url( 'administrator/users/roles/form' ) ) );

        $this->view->load( 'users/roles/form' );
    }
}