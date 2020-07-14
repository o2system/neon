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

$navigations = [
    'sidebar' => [
        'site' => [
            'name' => language('SITE'),
            'icon' => 'fas fa-cube',
            'type' => 'group',
            'items' => [
                'dashboard' => [
                    'name' => language('DASHBOARD'),
                    'href' => 'dashboard',
                    'icon' => 'fas fa-tachometer-alt'
                ],
                'pages' => [
                    'name' => language('PAGES'),
                    'href' => 'pages',
                    'icon' => 'fas fa-file-alt'
                ],
                'posts' => [
                    'name' => language('POSTS'),
                    'href' => 'posts',
                    'icon' => 'fas fa-align-left'
                ],
                'media' => [
                    'name' => language('MEDIA'),
                    'href' => 'media',
                    'icon' => 'fas fa-image'
                ],
                'tags' => [
                    'name' => language('TAGS'),
                    'href' => 'tags',
                    'icon' => 'fas fa-tags'
                ],
                'taxonomies' => [
                    'name' => language('TAXONOMIES'),
                    'href' => 'taxonomies',
                    'icon' => 'fas fa-project-diagram'
                ],
                'People' => [
                    'name' => language('PEOPLE'),
                    'href' => 'people',
                    'icon' => 'fas fa-users'
                ],
                'appearance' => [
                    'name' => language('APPEARANCE'),
                    'href' => '#',
                    'icon' => 'fas fa-palette',
                    'childs' => [
                        'themes' => [
                            'name' => language('THEMES'),
                            'href' => 'appearance/themes',
                            'icon' => 'fas fa-brush'
                        ],
                        'customize' => [
                            'name' => language('CUSTOMIZE'),
                            'href' => 'appearance/customize',
                            'icon' => 'fas fa-swatchbook'
                        ],
                        'sliders' => [
                            'name' => language('SLIDERS'),
                            'href' => 'appearance/sliders',
                            'icon' => 'fas fa-images'
                        ],
                    ],
                ],
            ]
        ],
        'system' => [
            'name' => language('SYSTEM'),
            'icon' => 'fas fa-server',
            'type' => 'group',
            'items' => [
                'information' => [
                    'name' => language('INFORMATION'),
                    'href' => 'system/information',
                    'icon' => 'fas fa-info-circle'
                ],
                'modules' => [
                    'name' => language('MODULES'),
                    'href' => 'system/modules',
                    'icon' => 'fas fa-cubes'
                ],
                'settings' => [
                    'name' => language('SETTINGS'),
                    'href' => 'system/settings',
                    'icon' => 'fas fa-cogs'
                ],
            ]
        ]
    ]
];