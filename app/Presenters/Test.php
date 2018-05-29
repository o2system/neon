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

namespace App\Presenters;

// ------------------------------------------------------------------------

use O2System\Framework\Http\Presenter;

/**
 * Class Test
 *
 * @package \App\Presenters
 */
class Test extends Presenter
{
    public function __construct()
    {
        parent::__construct();

        $username = input()->get('username');
        $username = empty( $username ) ? 'Guest' : $username;

        $this->store('username', $username );
    }
}