<?php
/**
 * Created by PhpStorm.
 * User: cicle creative
 * Date: 16/09/2019
 * Time: 15:45
 */

namespace App\Api\Modules\Companies\Models;


use O2System\Framework\Models\Sql\Model;

class Transactions extends Model
{
    public $table = 'companies_transactions';

    public function company()
    {
        return $this->belongsTo(Companies::class, 'id_company');
    }
}