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
 * Class Ticket
 *
 * @package App\Controllers
 */
class Tutorial extends AuthenticatedController
{
    /**
     * Ticket::index
     */
    public function index()
    {
        view( 'tutorial/index' );
    }

    public function personal()
    {
        view( 'tutorial/personal' );
    }

    public function humanResource()
    {
        view( 'tutorial/human-resource' );
    }

    public function company()
    {
        view( 'tutorial/company' );
    }

    public function libraries()
    {
        view( 'tutorial/libraries' );
    }

    public function eLearning()
    {
        view( 'tutorial/e-learning' );
    }

    public function eDocument()
    {
        view( 'tutorial/e-document' );
    }

    public function eFiles()
    {
        view( 'tutorial/e-files' );
    }
}
