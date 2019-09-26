<?php
/**
 * This file is part of the Circle Creative Web Application Project Boilerplate.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @author         PT. Lingkar Kreasi (Circle Creative)
 *  @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */
// ------------------------------------------------------------------------

namespace App\Api\Modules\Members\Models;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\RelationTrait;

/**
 * Class Metadata
 * @package App\Api\Modules\Members\Models
 */
class Metadata extends Model
{
    use RelationTrait;

    /**
     * Metadata::$table
     *
     * @var string
     */
    public $table = 'members_metadata';

    /**
     * Metadata::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_member',
        'name',
        'content',
    ];

    /**
     * Metadata::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        //'member',
    ];

    // ------------------------------------------------------------------------

    /**
     * Metadata::member
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function member()
    {
        return $this->belongsTo(Members::class, 'id_member');
    }
}