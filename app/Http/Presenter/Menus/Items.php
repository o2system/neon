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

namespace App\Http\Presenter\Menus;

// ------------------------------------------------------------------------

use O2System\Psr\Patterns\Structural\Repository\AbstractRepository;

/**
 * Class Items
 * @package App\Http\Presenter\Menus
 */
class Items extends AbstractRepository
{
    public function render()
    {
        return implode(PHP_EOL, $this->getArrayCopy() );
    }

    public function __toString()
    {
        return $this->render();
    }
}