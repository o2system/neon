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
namespace App\Modules\Administrator\Controllers\System;

// ------------------------------------------------------------------------

use App\Modules\Administrator\Http\Controller;
use O2System\Framework\Libraries\Ui\Contents\Link;

/**
 * Class Phpinfo
 * @package Administrator\Controllers\System
 */
class Phpinfo extends Controller
{
    /**
     * Phpinfo::index
     */
    public function index()
    {
        $this->presenter->page
            ->setHeader('PHP Information')
            ->setDescription('The server php information.');

        $this->presenter->page->breadcrumb->createList(new Link(language()->getLine('PHPINFO'),
            base_url('administrator/system/phpinfo')));

        ob_start();
        phpinfo();
        $contents = ob_get_contents();
        ob_end_clean();

        // the name attribute "module_Zend Optimizer" of an anker-tag is not xhtml valide, so replace it with "module_Zend_Optimizer"
        $phpinfo = (str_replace("module_Zend Optimizer", "module_Zend_Optimizer",
            preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $contents)));

        $phpinfo = str_replace('<table>', '<table class="table table-bordered table-striped">', $phpinfo);

        $this->view->load('system/phpinfo', ['phpinfo' => $phpinfo]);
    }
}