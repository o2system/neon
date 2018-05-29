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

namespace App\Controllers;

// --------------------------------------------------------------------------------------

use App\Http\AccessControl\Controllers\AuthenticatedController;

/**
 * Class Dashboard
 *
 * @package App\Controllers
 */
class Stats extends AuthenticatedController
{
    /**
     * Dashboard::index
     */
    public function index()
    {
        presenter()->page
            ->setHeader( 'Stats' )
            ->setDescription( 'The O2CMS Stats' );

        presenter()->page->icon->setClass( 'fas fa-tachometer-alt' );

        view( 'stats' );
    }
}
