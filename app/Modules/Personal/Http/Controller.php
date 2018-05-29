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

namespace Personal\Http;

// ------------------------------------------------------------------------

use App\Http\AccessControl\Controllers\AuthenticatedController;
use O2System\Framework\Libraries\Ui\Contents\Link;
use O2System\Framework\Libraries\Ui\Components\Navbar;

/**
 * Class Controller
 *
 * @package Personal\Http
 */
class Controller extends AuthenticatedController
{
    protected $navigation;

    /**
     * Controller::__reconstruct
     */
    public function __reconstruct()
    {
        parent::__reconstruct();

        presenter()->page
            ->setHeader( 'Personal' )
            ->setDescription( 'The Personal Module' );

        presenter()->page->breadcrumb->createList( new Link( language()->getLine( 'PERSONAL' ), '#' ) );

        presenter()->page->icon->setClass( 'fa fa-user' );

        // Navigation
        $this->navigation = new Navbar();

        $this->navigation->attributes->addAttributeClass( [ 'account-cover-navbar', 'navbar', 'navbar-expand-lg', 'navbar-light', 'bg-grey', 'p-relative' ] );

        $items = [ 'PROFILE', 'SETTING' ];
        foreach( $items as $item ) {
            $this->navigation->nav->createLink( language()->getLine( $item ), base_url( [ 'personal', strtolower( $item ) ] ) );
        }

        presenter()->page->menus->store( 'cover', $this->navigation );
    }
}