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

namespace App\Api\Modules\System\Models\Modules\Roles;

// ------------------------------------------------------------------------

use App\Api\Modules\System\Models\Modules\Roles;
use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\RelationTrait;

/**
 * Class Authorities
 * @package App\Api\Modules\System\Models\Modules\Roles
 */
class Authorities extends Model
{
    use RelationTrait;

    /**
     * Authorities::$table
     *
     * @var string
     */
    public $table = 'sys_modules_roles_authorities';

    // ------------------------------------------------------------------------

    /**
     * Authorities::role
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function role()
    {
        return $this->belongsTo(Roles::class, 'id_sys_module_role');
    }
}