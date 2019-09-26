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
use O2System\Spl\DataStructures\SplArrayObject;

/**
 * Class Media
 * @package App\Api\Modules\Media\Models
 */
class Media extends Model
{
    use RelationTrait;

    /**
     * Media::$table
     *
     * @var string
     */
    public $table = 'media';

    /**
     * Media::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'label',
        'filename',
        'mime',
    ];

    /**
     * Media::$appendColumns
     *
     * @var array
     */
    public $appendColumns = [
        'metadata',
        'path'
    ];

    protected  $imagePath = PATH_STORAGE . 'images/site/media/';

    // ------------------------------------------------------------------------

    /**
     * Media::metadata
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject|null
     */
    public function metadata()
    {
        $metadata = new SplArrayObject();
        $metadata->offsetSet($this->row->getArrayCopy());
        if($result = $this->hasMany(Metadata::class, 'id_media')) {
            foreach($result as $row) {
                $metadata->offsetSet($row->name, $row->content);
            }

            return $metadata;
        }

        return null;
    }

    public function path()
    {
        $path = $this->imagePath.$this->row->filename;
        if(is_file($path)){
            return storage_url($path);
        }
        return storage_url('images/default/no-image.jpg');
    }

}
