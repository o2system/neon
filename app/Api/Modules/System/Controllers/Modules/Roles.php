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

namespace App\Api\Modules\System\Controllers\Modules;

// ------------------------------------------------------------------------

use App\Api\Modules\System\Http\Controller;
use App\Api\Modules\System\Models;

/**
 * Class Roles
 * @package App\Api\Modules\System\Controllers\Modules
 */
class Roles extends Controller
{
    /**
     * Roles::$param
     *
     * @var array
     */
    public $params = [
        [
            'field'    => 'id_sys_module',
            'label'    => 'ID System Module',
            'rules'    => 'required',
            'messages' => 'ID System Module cannot be empty!',
        ]
    ];

    /**
     * Roles::$roles
     *
     * @var array
     */
    public $roles = [
        [
            'field'    => 'code',
            'label'    => 'Code',
            'rules'    => 'required',
            'messages' => 'Code cannot be empty!',
        ],
        [
            'field'    => 'label',
            'label'    => 'Label',
            'rules'    => 'required',
            'messages' => 'Label cannot be empty!',
        ],
    ];

    /**
     * Roles::$fields
     *
     * @var array
     */
    public $fields = [
        'id',
        'id_sys_module',
        'code',
        'label'
    ];

    /**
     * Roles constructor.
     * @throws \ReflectionException
     */
    public function __construct()
    {
        parent::__construct();

        $this->model = new Models\Modules\Roles();
    }
}
