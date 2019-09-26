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

namespace App\Api\Modules\System\Models\Modules;

// ------------------------------------------------------------------------

use App\Api\Modules\System\Models\Modules;
use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\RelationTrait;

/**
 * Class Segments
 * @package App\Api\Modules\System\Models\Modules
 */
class Segments extends Model
{
    use RelationTrait;

    /**
     * Segments::$table
     *
     * @var string
     */
    public $table = 'sys_modules_segments';

    public $visibleColumns = [
        'id',
        'id_sys_module',
        'name',
        'uri',
        'slug',
        'class'
    ];

    // ------------------------------------------------------------------------

    /**
     * Segments::module
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function module()
    {
        return $this->belongsTo(Modules::class, 'id_sys_module');
    }

    // ------------------------------------------------------------------------

    /**
     * Segments::name
     *
     * return string
     */
    public function name()
    {
        return readable(snakecase(get_class_name($this->row->class)), true);
    }

    // ------------------------------------------------------------------------

    /**
     * Segments::roles
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function roles()
    {
         return $this->hasMany(Modules\Segments\Authorities\Roles::class, 'id_sys_module_segment');
    }

    // ------------------------------------------------------------------------

    /**
     * Segments::users
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function users()
    {
        return $this->hasMany(Modules\Segments\Authorities\Users::class, 'id_sys_module_segment');
    }
}