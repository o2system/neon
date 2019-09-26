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

namespace App\Api\Modules\Products\Models;

// ------------------------------------------------------------------------

use App\Api\Modules\Products\Models\Variants\Items;
use O2System\Framework\Models\Sql\Model;

/**
 * Class Variants
 * @package App\Api\Modules\Products\Models
 */
class Variants extends Model
{
    /**
     * Variants::$table
     *
     * @var string
     */
    public $table = 'products_variants';

    public $appendColumns = [
        'value'
    ];

    public function value()
    {
        if ($data = $this->hasOne(Variants\Metadata::class, 'id_product_variant')) {
            return $data->value;
        }
    }
}