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
use App\Api\Modules\Master\Models\Banks as MasterBanks;

/**
 * Class Banks
 * @package App\Api\Modules\Members\Models
 */
class Banks extends Model
{
    use RelationTrait;

    /**
     * Banks::$table
     *
     * @var string
     */
    public $table = 'members_banks';

    /**
     * Banks::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_member',
        'id_bank',
        'name',
        'account_number',
        'city',
        'main_bank'
    ];

    /**
     * Banks::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        //'member',
    ];

    // ------------------------------------------------------------------------

    /**
     * Banks::member
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function member()
    {
        return $this->belongsTo(Members::class, 'id_member');
    }

    public function bank()
    {
        return $this->belongsTo(MasterBanks::class, 'id_bank');
    }
}