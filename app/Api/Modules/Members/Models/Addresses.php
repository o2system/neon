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
use App\Libraries\Rajaongkir;

/**
 * Class Addresses
 * @package App\Api\Modules\Members\Models
 */
class Addresses extends Model
{
    use RelationTrait;

    /**
     * Addresses::$table
     *
     * @var string
     */
    public $table = 'members_addresses';

    /**
     * Addresses::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_member',
        'receiver',
        'address_name',
        'phone',
        'postal_code',
        'address',
        'id_geodirectory',
        'main_address',
        'id_member'
    ];

    /**
     * Addresses::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        'city',
    ];

    // ------------------------------------------------------------------------

    /**
     * Addresses::member
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function member()
    {
        return $this->belongsTo(Members::class, 'id_member');
    }

    public function city()
    {
        $rajaongkir = new Rajaongkir();
        $city = $rajaongkir->result->getCity($this->row->id_geodirectory);
        if ($city) {
            return $city;
        } else {
            return false;
        }
    }
}