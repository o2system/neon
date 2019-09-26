<?php
/**
 * Created by PhpStorm.
 * User: cicle creative
 * Date: 16/09/2019
 * Time: 15:45
 */

namespace App\Api\Modules\Companies\Models;


use O2System\Framework\Models\Sql\Model;
use App\Api\Modules\Products\Models\Products as RealProducts;

class Products extends Model
{
    public $table = 'companies_products';

    public function company()
    {
        return $this->belongsTo(Companies::class, 'id_company');
    }

    public function product()
    {
        return $this->belongsTo(RealProducts::class, 'id_product');
    }
}