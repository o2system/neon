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


namespace App\Modules\Administrator\Controllers\Packages;
// ------------------------------------------------------------------------

use App\Modules\Administrator\Http\Controller;
use O2System\Framework\Libraries\Ui\Contents\Link;

/**
 * Class Manage
 *
 * @package Administrator\Controllers\Modules
 */
class Manage extends Controller
{
    /**
     * Manage::index
     */
    public function index()
    {
        $this->presenter->page
            ->setHeader( 'Modules Manage' )
            ->setDescription( 'The Modules Manage' );

        $this->presenter->page->breadcrumb->createList( new Link( language()->getLine( 'MODULES_MANAGER' ), base_url( 'administrator/modules/manage' ) ) );

        $this->view->load( 'modules/manage' );
    }
}