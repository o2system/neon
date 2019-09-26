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

namespace App\Api\Modules\Media\Models;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\RelationTrait;

/**
 * Class Metadata
 * @package App\Api\Modules\Media\Models
 */
class Metadata extends Model
{
    use RelationTrait;

    /**
     * Metadata::$table
     *
     * @var string
     */
    public $table = 'media_metadata';


    /**
     * Metadata::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'id_media',
        'name',
        'content',
    ];

    /**
     * Metadata::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
//        'media'
    ];

    // ------------------------------------------------------------------------

    /**
     * Metadata::media
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result\Row
     */
    public function media()
    {
        return $this->belongsTo(Media::class, 'id_media');
    }

    public function update(array $sets, array $conditions = [])
    {
        $content = $sets['content'];
        foreach ($content as $field => $value){
            parent::insertOrUpdate([
                'name'  => $field,
                'content'   => $value,
                'id_media'  => $sets['id_media'],
            ],[
                'id_media'  => $sets['id_media'],
                'name'  => $field
            ]);
        }
        return true;

    }
}
