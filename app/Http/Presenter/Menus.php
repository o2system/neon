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

namespace App\Http\Presenter;

// ------------------------------------------------------------------------

use App\Http\Presenter\Menus\Item;
use App\Http\Presenter\Menus\Group;
use O2System\Psr\Patterns\Structural\Repository\AbstractRepository;

/**
 * Class Menus
 *
 * @package App\Datastructures
 */
class Menus extends AbstractRepository
{
    /**
     * Menus::__construct
     */
    public function __construct()
    {
        // Main menus
        $this->store('main', (new Group('MAIN'))->setItems([
            'stats' => [
                'label' => 'STATS',
                'icon' => 'fas fa-tachometer-alt',
                'href' => base_url('stats')
            ],
            'system' => [
                'label' => 'SYSTEM',
                'icon' => 'fas fa-server',
                'href' => base_url('system')
            ]
        ]));

        // Manage menus
        $this->store('manage', (new Group('MANAGE'))->setItems([
            'pages' => [
                'label' => 'PAGES',
                'icon' => 'fas fa-file-code',
                'href' => base_url('pages'),
                'add' => base_url('pages/form'),
            ],
            'posts' => [
                'label' => 'POSTS',
                'icon' => 'fas fa-file-alt',
                'href' => base_url('posts'),
                'add' => base_url('posts/form'),
            ],
            'media' => [
                'label' => 'MEDIA',
                'icon' => 'fas fa-file-image',
                'href' => base_url('media')
            ]
        ]));

        // Personalize menus
        $this->store('personalize', (new Group('PERSONALIZE'))->setItems([
            'appearance' => [
                'label' => 'APPEARANCE',
                'icon' => 'fas fa-paint-brush',
                'href' => base_url('appearance'),
                'customize' => base_url('appearance/customize'),
            ]
        ]));

        // Administrator menus
        $this->store('configure', (new Group('CONFIGURE'))->setItems([
            'settings' => [
                'label' => 'SETTINGS',
                'icon' => 'fas fa-cogs',
                'href' => base_url('administrator/settings')
            ],
            'users' => [
                'label' => 'USERS',
                'icon' => 'fas fa-users',
                'href' => base_url('administrator/users/manage')
            ],
            'packages' => [
                'label' => 'PACKAGES',
                'icon' => 'fas fa-file-archive',
                'href' => base_url('administrator/packages/manage')
            ]
        ]));
    }
}