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

namespace App\Api\Modules\Posts\Models\Sections;

// ------------------------------------------------------------------------

use App\Api\Modules\Posts\Models\Sections;
use O2System\Framework\Models\Sql\Model;

/**
 * Class Categories
 * @package App\Api\Modules\Posts\Models\Sections
 */
class Categories extends Model
{
    /**
     * Categories::$table
     *
     * @var string
     */
    public $table = 'posts_sections_categories';

    /**
     * Categories::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_parent',
        'id_post_section',
        'title',
        'slug',
        'description',
        'image'
    ];

    public $appendColumns = [
        //'post'
        'record'
    ];

    // ------------------------------------------------------------------------

    /**
     * Categories::section
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function section()
    {
        return $this->belongsTo(Sections::class, 'id_post_section');
    }

    // ------------------------------------------------------------------------

    /**
     * Categories::items
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function items()
    {
        return $this->hasMany(Sections\Categories\Items::class, 'id_post_section_category');
    }
}