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

namespace App\Api\Modules\System\Models\Modules\Segments\Authorities;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use App\Api\Modules\System\Models\Modules\Segments;

/**
 * Class Users
 * @package App\Api\Modules\System\Models\Modules\Segments\Authorities
 */
class Users extends Model
{
    /**
     * Users::$table
     *
     * @var string
     */
    public $table = 'sys_modules_segments_authorities_users';

    /**
     * Users::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_sys_module_segment',
        'id_sys_user'
    ];

    // ------------------------------------------------------------------------

    /**
     * Roles;:permissionsOptions
     *
     * @return array
     */
    public function permissionsOptions()
    {
        return [
            'GRANTED' => language('GRANTED'),
            'DENIED' => language('DENIED'),
            'WRITE' => language('WRITE')
        ];
    }

    // ------------------------------------------------------------------------

    /**
     * Roles::segment
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function segment()
    {
        return $this->belongsTo(Segments::class, 'id_sys_module_segment');
    }

    // ------------------------------------------------------------------------

    /**
     * Roles::user
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function user()
    {
        return $this->belongsTo(\App\Api\Modules\System\Models\Users::class, 'id_sys_user');
    }
}