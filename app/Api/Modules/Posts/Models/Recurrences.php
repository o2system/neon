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

namespace App\Api\Modules\Posts\Models;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;

/**
 * Class Recurrences
 * @package App\Api\Modules\Posts\Models
 */
class Recurrences extends Model
{
    /**
     * Recurrences::$table
     *
     * @var string
     */
    public $table = 'posts_recurrences';

    /**
     * Recurrences::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_post',
        'repeat_start',
        'repeat_end',
        'repeat_minute',
        'repeat_hour',
        'repeat_day',
        'repeat_week',
        'repeat_month',
        'repeat_year',
    ];

    /**
     * Recurrences::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        //'post',
        'record'
    ];

    // ------------------------------------------------------------------------

    /**
     * Recurrences::post
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function post()
    {
        return $this->belongsTo(Posts::class, 'id_post');
    }
}