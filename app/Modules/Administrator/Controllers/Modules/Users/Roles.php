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
            ->setHeader( 'Users Roles' )
            ->setDescription( 'The Users Roles' );

        $this->presenter->page->breadcrumb->createList( new Link( language()->getLine( 'USERS_ROLES' ), base_url( 'administrator/users/roles' ) ) );

        $this->view->load( 'users/roles/table' );
    }

    public function form()
    {
        $this->presenter->page
            ->setHeader( 'Users Roles' )
            ->setDescription( 'The Users Roles' );

        $this->presenter->page->breadcrumb->createList( new Link( language()->getLine( 'USERS_ROLES' ), base_url( 'administrator/users/roles/form' ) ) );

        $this->view->load( 'users/roles/form' );
    }
}