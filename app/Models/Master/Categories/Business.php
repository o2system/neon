<?php
/**
 * This file is part of the WebIn Platform.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         PT. Lingkar Kreasi (Circle Creative)
 * @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */
// ------------------------------------------------------------------------

namespace App\Models\Master\Categories;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\HierarchicalTrait;

/**
 * Class Business
 * @package App\Models\Master\Categories
 */
class Business extends Model
{
    use HierarchicalTrait;

    /**
     * CompanyCategories::$table
     *
     * @var string
     */
    public $table = 'tm_categories_business';
}
