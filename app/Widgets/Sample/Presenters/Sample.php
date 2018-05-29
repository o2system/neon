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

namespace Widgets\Sample\Presenters;

// ------------------------------------------------------------------------

use O2System\Framework\Http\Presenter\Widget;

/**
 * Class Sample
 *
 * @package Widgets\SocialMedia\Presenters
 */
class Sample extends Widget
{
    public function __construct()
    {
        $this->store('foo', 'bar');
    }
}