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

namespace App\Api\Modules\Companies\Models;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;

/**
 * Class Metadata
 * @package App\Api\Modules\Companies\Models
 */
class Metadata extends Model
{
    /**
     * Metadata::$table
     *
     * @var string
     */
    public $table = 'companies_metadata';

    /**
     * Metadata::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_company',
        'name',
        'content',
    ];

    /**
     * Metadata::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
//        'company',
//        'user'
    ];

    // ------------------------------------------------------------------------

    /**
     * Metadata::company
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function company()
    {
        return  $this->belongsTo(Companies::class, 'id_company');
    }
}
