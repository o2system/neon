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

use O2System\Kernel\Datastructures\Config;

$presenter = new Config( [], Config::STD_OFFSET );

/**
 * Presenter Enabled
 *
 * Auto start presenter as framework service.
 */
$presenter->enabled = true;

/**
 * Presenter Debug Toolbar
 *
 * @var bool
 */
$presenter->debugToolBar = true;

/**
 * Presenter Theme
 *
 * @var string
 */
$presenter->theme = 'default';

/**
 * Presenter Assets
 *
 * @var array
 */
$presenter->assets = [

    /**
     * Webpack assets
     *
     * @var bool
     */
    'webpack'  => false,

    // --------------------------------------------------------------------

    /**
     * Autoload view assets
     *
     * @var array
     */
    'autoload' => [
        'head' => [
            'css'   => [  ],
            'fonts' => [ 
                'font-awesome',
                'https://fonts.googleapis.com/css?family=Maven+Pro:400,700|Oxygen:400,700'
            ],
        ],
        'body' => [
            'js' => [
                'jquery.js',
                'jquery-migrate.js',
                'o2system.js',
            ],
        ],
        'packages' => [
            'jquery-ui',
            'bootstrap' => [
                'libraries' => [
                    'popper'
                ]
            ],
            'tinymce',
            'o2system-ui' => [
                'themes' => ['multipurpose', 'admin'],
                'plugins' => [
                    'admin/table'
                ]
            ],
        ]
    ],

    // --------------------------------------------------------------------
];
