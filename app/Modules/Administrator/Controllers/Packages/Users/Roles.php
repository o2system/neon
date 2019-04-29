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

namespace Administrator\Controllers\Users;

// ------------------------------------------------------------------------

use App\Modules\Administrator\Http\Controller;
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