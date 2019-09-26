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

namespace App\Api\Modules\Testimonials\Models;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\RelationTrait;

/**
 * Class Logs
 * @package App\Api\Modules\Testimonials\Models
 */
class Logs extends Model
{
    use RelationTrait;

    /**
     * Logs::$table
     *
     * @var string
     */
    public $table = 'transactions_logs';

    /**
     * Logs::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_transaction',
        'status',
        'timestamp',
        'expires'
    ];

    /**
     * Logs::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        //'transaction',
    ];

    // ------------------------------------------------------------------------

    /**
     * Logs::transaction
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function transaction()
    {
        return $this->belongsTo(Currencies::class, 'id_transaction');
    }
}
