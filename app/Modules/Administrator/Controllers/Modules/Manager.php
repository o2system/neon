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

namespace Administrator\Controllers\Modules;

// ------------------------------------------------------------------------

use Administrator\Http\Controller;
use O2System\Framework\Libraries\Ui\Components\Form;
use O2System\Framework\Libraries\Ui\Components\Form\Elements\Checkbox;
use O2System\Framework\Libraries\Ui\Contents\Link;
use O2System\Framework\Libraries\Ui\Contents\Table;

/**
 * Class Manager
 *
 * @package Administrator\Controllers\Modules
 */
class Manager extends Controller
{
    /**
     * Manager::index
     */
    public function index()
    {
        $this->presenter->page
            ->setHeader( 'Modules Manager' )
            ->setDescription( 'The Modules Manager' );

        $this->presenter->page->breadcrumb->createList( new Link( language()->getLine( 'MODULES_MANAGER' ), base_url( 'administrator/modules/manager' ) ) );

        $table = new Table();
        $table->header->createRow()->createColumns([
            'Status',
            'Name',
            'Location',
            'Type',
            'Version',
            'Date',
            'Author',
            'Folder',
            'Package ID',
            'ID'
        ]);

        $registry = modules()->getRegistry();
        array_shift( $registry );

        foreach ( $registry as $key => $module )
        {
            $modules[ $key ] = $module->getProperties();
            $modules[ $key ]->type = $module->getType();
        }

        $this->view->load( 'modules/manager', [ 'modules' => $modules ] );
    }

    public function form()
    {
        $this->presenter->page
            ->setHeader( 'Modules Manager' )
            ->setDescription( 'The Modules Manager' );

        $this->presenter->page->breadcrumb->createList( new Link( language()->getLine( 'MODULES_MANAGER' ), base_url( 'administrator/modules/manager/form' ) ) );

        $this->view->load( 'modules/manager/form' );
    }
}