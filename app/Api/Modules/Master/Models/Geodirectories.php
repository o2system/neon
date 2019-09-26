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

namespace App\Api\Modules\Master\Models;

// ------------------------------------------------------------------------

use O2System\Framework\Models\Sql\Model;
use O2System\Framework\Models\Sql\Traits\HierarchicalTrait;

/**
 * Class Geodirectories
 * @package App\Api\Modules\Master\Models
 */
class Geodirectories extends Model
{
    use HierarchicalTrait;

    /**
     * Geodirectories::$table
     *
     * @var string
     */
    public $table = 'tm_geodirectories';

    // ------------------------------------------------------------------------

    /**
     * Geodirectories::cities
     *
     * @return bool|\O2System\Database\DataObjects\Result
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function cities()
    {
        if($result = $this->db->query("
            SELECT 
                tm_geodirectories.*,
                CONCAT(tm_geodirectories.name, ', ', provinces.name, ' - ', countries.name) AS fullname
            FROM tm_geodirectories
            LEFT JOIN tm_geodirectories provinces ON provinces.id = tm_geodirectories.id_parent
            LEFT JOIN tm_geodirectories countries ON countries.id = provinces.id_parent
            WHERE tm_geodirectories.type IN ('CITY','DISTRICT')
        ")){
            return $result;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Geodirectories::parents
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function parents()
    {
        return $this->getParents($this->row->id);
    }

    // ------------------------------------------------------------------------

    /**
     * Geodirectories::childs
     *
     * @return bool|\O2System\Framework\Models\Sql\DataObjects\Result
     */
    public function childs()
    {
        return $this->getChilds($this->row->id);
    }

    // ------------------------------------------------------------------------

    /**
     * Geodirectories::continent
     *
     * @return bool|\O2System\Database\DataObjects\Result\Row
     */
    public function continent()
    {
        $this->qb->where($this->table . '.type', 'CONTINENT');

        if($result = $this->getParents($this->row->id)) {
            if($result->count()) {
                return $result->first();
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Geodirectories::subcontinent
     *
     * @return bool|\O2System\Database\DataObjects\Result\Row
     */
    public function subContinent()
    {
        $this->qb->where($this->table . '.type', 'SUBCONTINENT');

        if($result = $this->getParents($this->row->id)) {
            if($result->count()) {
                return $result->first();
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Geodirectories::country
     *
     * @return bool|\O2System\Database\DataObjects\Result\Row
     */
    public function country()
    {
        $this->qb->where($this->table . '.type', 'COUNTRY');
        if($result = $this->getParents($this->row->id)) {
            if($result->count()) {
                return $result->first();
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Geodirectories::province
     *
     * @return bool|\O2System\Database\DataObjects\Result\Row
     */
    public function province()
    {
        $this->qb->where($this->table . '.type', 'PROVINCE');

        if($result = $this->getParents($this->row->id)) {
            if($result->count()) {
                return $result->first();
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Geodirectories::district
     *
     * @return bool|\O2System\Database\DataObjects\Result\Row
     */
    public function district()
    {
        $this->qb->where($this->table . '.type', 'DISTRICT');

        if($result = $this->getParents($this->row->id)) {
            if($result->count()) {
                return $result->first();
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Geodirectories::subDistrict
     *
     * @return bool|\O2System\Database\DataObjects\Result\Row
     */
    public function subDistrict()
    {
        $this->qb->where($this->table . '.type', 'SUBDISTRICT');

        if($result = $this->getParents($this->row->id)) {
            if($result->count()) {
                return $result->first();
            }
        }

        return false;
    }

    public function insert($post)
    {
        if ($post) {
            if (parent::insert($post)) {
               return true;
            }
            return false;
        } else {
            return false;
        }
    }

    public function update($post, $conditions = null)
    {
        if ($post) {
            if (parent::update($post)) {
                return true;
            }
            return false;
        } else {
            return false;
        }
    }

    public function delete($id)
    {
        if ($id) {
            if (parent::delete($id)) {
                return true;
            }
            return false;
        }
    }

    public function filter($get)
    {
        if ($keyword = $get->keyword) {
            $this->qb->like('name', $keyword);
        }

        if ($get->entries) {
            $all = (is_numeric($get->entries) ? $this->allWithPaging(null, $get->entries) : $this->all());
        } else {
            $all = $this->allWithPaging(null, 150);
        }

        return $all;
    }
}
