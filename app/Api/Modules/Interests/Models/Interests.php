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

namespace App\Api\Modules\Interests\Models;

// ------------------------------------------------------------------------

use App\Api\Modules\Companies\Models\Companies;
use App\Api\Modules\Products\Models\Categories;
use O2System\Framework\Models\Sql\Model;

/**
 * Class Categories
 * @package App\Api\Modules\Companies\Models
 */
class Interests extends Model
{
    /**
     * Categories::$table
     *
     * @var string
     */
    public $table = 'interests';

    public $appendColumns = ['category', 'company'];

    public function category()
    {
        return $this->belongsTo(Categories::class, 'id_product_category');
    }

    public function image()
    {
        if ($data = $this->belongsTo(Categories::class, 'id_product_category')) {
            return $data->image;
        }
        return storage_url('/images/default/no-image.jpg');
    }

    public function company()
    {
        return $this->belongsTo(Companies::class, 'id_company');
    }

    public function companies_interests_all($get=null)
    {
        if ($company = globals()->account->company) {
            $this->qb->select('interests.*');
            $this->qb->join('products_categories', 'interests.id_product_category = products_categories.id');
            $this->qb->where('interests.id_company', $company->id);
            if ($get) {
                if ($get->period) {
                    $time_data = explode('-', str_replace(' ', '', $get->period));
                    $this->qb->where('DATE(interests.record_create_timestamp) >=', date('Y-m-d', strtotime($time_data[0])));
                    $this->qb->where('DATE(interests.record_create_timestamp) <=', date('Y-m-d', strtotime($time_data[1])));
                }

                if ($keyword = $get->keyword) {
                    $this->qb->like('products_categories.name', $keyword);
                }

                if ($get->entries) {
                    $all = (is_numeric($get->entries) ? $this->allWithPaging(null, $get->entries) : $this->all());
                } else {
                    $all = $this->allWithPaging();
                }

                return $all;
            }
            
            return $this->allWithPaging();
        } else {
            return false;
        }
    }
}
