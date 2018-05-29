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

// ------------------------------------------------------------------------

use App\Http\AccessControl\Controllers\AuthorizedController;
use App\Models\System\Themes;
use O2System\Framework\Libraries\Ui\Contents\Link;

/**
 * Class Appearance
 * @package App\Controllers
 */
class Appearance extends AuthorizedController
{
    public function index()
    {
        presenter()->page
            ->setHeader( 'Appearance' )
            ->setDescription( 'Manage Appearance' );

        presenter()->page->icon->setClass( 'fa fa-image' );

        presenter()->page->breadcrumb->createList( new Link(
            language()->getLine( 'APPEARANCE' ),
            base_url( 'appearance' )
        ) );

        $themes = new Themes();
        //print_out($themes->all());

        view( 'appearance/list' );
    }

    /**
     * Appearance::customize
     */
    public function customize()
    {
        presenter()->assets->loadPackage('slinky-menu');

        /**
         * @todo: create theme customize
         */

        presenter()->theme->setLayout( 'customize' );
        view( 'appearance/customize' );
    }
}