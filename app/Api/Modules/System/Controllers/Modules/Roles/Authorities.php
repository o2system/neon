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

namespace App\Api\Modules\System\Controllers\Modules\Roles;

// ------------------------------------------------------------------------

use App\Api\Http\Controller;
use App\Api\Modules\System\Models;

/**
 * Class Authorities
 * @package App\Api\Modules\System\Controllers\Modules\Roles
 */
class Authorities extends Controller
{
    public $params = [
        [
            'field'    => 'id_sys_module_role',
            'label'    => 'ID System Module Role',
            'rules'    => 'required',
            'messages' => 'ID System Module Role cannot be empty!',
        ]
    ];

    public function __construct()
    {
        parent::__construct();

        $this->model = new Models\Modules\Roles\Authorities();
    }
}