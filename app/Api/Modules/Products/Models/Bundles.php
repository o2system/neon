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

use O2System\Framework\Models\Sql\Model;

/**
 * Class Bundles
 * @package App\Api\Modules\Products\Models
 */
class Bundles extends Model
{
    /**
     * Bundles::$table
     *
     * @var string
     */
    public $table = 'products_bundles';

    /**
     * Bundles::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        //'post'
        'record'
    ];
}