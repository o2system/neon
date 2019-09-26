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
namespace App\Api\Modules\Pages\Models;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\RelationTrait;

/**
 * Class Metadata
 * @package App\Api\Modules\Pages\Models
 */
class Metadata extends Model
{
    use RelationTrait;

    /**
     * Metadata::$table
     *
     * @var string
     */
    public $table = 'pages_metadata';

    /**
     * Metadata::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_page',
        'name',
        'content',
    ];

    /**
     * Metadata::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        //'page'
    ];

    // ------------------------------------------------------------------------

    /**
     * Metadata::page
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function page()
    {
        return $this->belongsTo(Pages::class, 'id_page');
    }
}