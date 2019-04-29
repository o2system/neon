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

namespace App\Http;

// ------------------------------------------------------------------------

/**
 * Class Presenter
 * @package App\Http
 */
class Presenter extends \O2System\Framework\Http\Presenter
{
    /**
     * Presenter::$menus
     *
     * @var \App\Http\Presenter\Menus
     */
    public $menus;

    // ------------------------------------------------------------------------

    /**
     * Presenter::__construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->menus = new Presenter\Menus();
    }
}