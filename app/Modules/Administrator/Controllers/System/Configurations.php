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

namespace Administrator\Controllers\System;

// ------------------------------------------------------------------------

use Administrator\Http\Controller;
use O2System\Framework\Libraries\Ui\Contents\Link;

/**
 * Class Configurations
 * @package Administrator\Controllers\System
 */
class Configurations extends Controller
{
    /**
     * Configurations::index
     */
    public function index()
    {
        $this->presenter->page
            ->setHeader('Configurations')
            ->setDescription('The application configurations.');

        $this->presenter->page->breadcrumb->createList(new Link(language()->getLine('CONFIGURATIONS'),
            base_url('administrator/system/configurations')));

        $this->view->load('system/configurations', ['configurations' => config()]);
    }
}