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

namespace App\Api\Modules\Posts\Models\Sections\Categories;

// ------------------------------------------------------------------------

use App\Api\Modules\Posts\Models\Posts;
use App\Api\Modules\Posts\Models\Sections;
use O2System\Framework\Models\Sql\Model;

/**
 * Class Items
 * @package App\Api\Modules\Posts\Models\Sections\Categories
 */
class Items extends Model
{
    /**
     * Items::$table
     *
     * @var string
     */
    public $table = 'posts_sections_categories_items';

    /**
     * Items::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_post_section_category',
        'id_post',
    ];

    /**
     * Items::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        //'category',
        //'post',
        'record'
    ];

    // ------------------------------------------------------------------------

    /**
     * Items::category
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function category()
    {
        return $this->belongsTo(Sections\Categories::class, 'id_post_section_category');
    }

    // ------------------------------------------------------------------------

    /**
     * Items::post
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function post()
    {
        return $this->belongsTo(Posts::class, 'id_post');
    }
}