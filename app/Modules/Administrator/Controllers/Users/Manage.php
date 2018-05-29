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
 * Class Manage
 *
 * @package Administrator\Controllers\Users
 */
class Manage extends Controller
{
    /**
     * Manage::index
     */
    public function index()
    {
        $this->presenter->page
            ->setHeader( 'Manage Users' )
            ->setDescription( 'The Users Manager' );

        $this->presenter->page->breadcrumb->createList( new Link( language()->getLine( 'MANAGE_USERS' ), base_url( 'administrator/users/manage' ) ) );

        $u = models('users')->getAllUsers();

        $this->view->load( 'users/manage/table', ['users' => $u] );
    }

    public function form($idUser = NULL)
    {
        if ($idUser !== NULL) {
            $this->presenter->page
                ->setHeader( 'Manage Users' )
                ->setDescription( 'The Users Manager' );

            $this->presenter->page->breadcrumb->createList( new Link( language()->getLine( 'MANAGE_USERS' ), base_url( 'administrator/users/manager/form' ) ) );

            $u = models('users')->findAccount($idUser);

            $this->view->load( 'users/manage/form', ['user' => $u] );
        } else {
            redirect_url(base_url('administrator/users/manage'));
        }
    }
}