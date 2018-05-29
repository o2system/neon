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

namespace Administrator\Http;

// ------------------------------------------------------------------------

use App\Http\AccessControl\Controllers\AuthorizedController;
use O2System\Framework\Libraries\Ui\Contents\Link;

/**
 * Class Controller
 *
 * @package Administrator\Http
 */
class Controller extends AuthorizedController
{
    /**
     * Controller::__construct
     */
    public function __reconstruct()
    {
        parent::__reconstruct();

        $this->presenter->page
            ->setHeader( 'Administrator' )
            ->setDescription( 'The O2CMS Administrator Module' );

        $this->presenter->page->breadcrumb->createList( new Link( language()->getLine( 'ADMINISTRATOR' ), '#' ) );

        $this->presenter->page->icon->setClass( 'fa fa-user' );
    }
}