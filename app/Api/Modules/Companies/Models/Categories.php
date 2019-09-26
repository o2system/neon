<?php
/**
 * This file is part of the Circle Creative Web Application Project Boilerplate.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @author         PT. Lingkar Kreasi (Circle Creative)
 *  @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */
// ------------------------------------------------------------------------

namespace App\Api\Modules\Companies\Models;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Spl\Datastructures\SplArrayObject;
use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Filesystem\Handlers\Uploader;

/**
 * Class Categories
 * @package App\Api\Modules\Companies\Models
 */
class Categories extends Model
{
    /**
     * Categories::$table
     *
     * @var string
     */
    public $table = 'companies_categories';

    /**
     * Categories::$visibleColumns
     *
     * @var array
     */
    public $visibleColumns = [
        'id',
        'code',
        'title',
        'description',
        'image',
    ];

    public $appendColumns = [
//        'company'
    ];

    public function companies()
    {
        models(Companies::class)->appendColumns = [
            'image'
        ];
        $data = $this->hasMany(Companies::class, 'id_company_category');
        if (count($data)) {
            return $data;
        }
        return false;
    }

    public function merchants()
    {
        if ($data = $this->find(1)) {
            if ($data = $data->companies) {
                if (count($data)) {
                    return $data;
                }
                return false;
            }
            return false;
        }
        return false;
    }
    
    public function creditors()
    {
        if ($data = $this->find(2)) {
            if ($data = $data->companies) {
                if (count($data)) {
                    return $data;
                }
                return false;
            }
            return false;
        }
        return false;
    }
    public function create($post)
    {
        if ($post) {
            $post->code = strtoupper(substr($post->title, 0, 3));
            if (parent::insert($post->getArrayCopy())) {
                return true;
            }
            return false;
        }
        return false;
    }

    public function update($post, array $condition = [])
    {
        if ($post) {
            $post->code = strtoupper(substr($post->title, 0, 3));
            if (parent::update($post->getArrayCopy())) {
                return true;
            }
            return false;
        }
        return false;
    }
}
