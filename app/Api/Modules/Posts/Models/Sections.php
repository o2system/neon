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

use App\Api\Modules\Posts\Models\Sections\Categories;
use App\Api\Modules\Posts\Models\Sections\Items;
use O2System\Framework\Models\Sql\Model;

/**
 * Class Sections
 * @package App\Api\Modules\Posts\Models
 */
class Sections extends Model
{
    /**
     * Sections::$table
     *
     * @var string
     */
    public $table = 'posts_sections';

    /**
     * Sections::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'title',
        'slug',
        'description',
        'image',
        'visibility',
    ];

    /**
     * Sections::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        'record'
    ];

    // ------------------------------------------------------------------------

    /**
     * Sections::visibilityOptions
     *
     * @return array
     */
    public function visibilityOptions()
    {
        return [
            'PUBLIC' => language('PUBLIC'),
            'READONLY' => language('READONLY'),
            'PROTECTED' => language('PROTECTED'),
            'PRIVATE' => language('PRIVATE')
        ];
    }

    // ------------------------------------------------------------------------

    /**
     * Sections::categories
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function categories()
    {
        return $this->hasMany(Categories::class, 'id_post_section');
    }

    // ------------------------------------------------------------------------

    /**
     * Sections::items
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function items()
    {
        return $this->hasMany(Items::class, 'id_post_section');
    }
}