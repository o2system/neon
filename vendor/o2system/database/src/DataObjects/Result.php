<?php
/**
 * This file is part of the O2System Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */

// ------------------------------------------------------------------------

namespace O2System\Database\DataObjects;

// ------------------------------------------------------------------------

use O2System\Database\DataObjects\Result\Info;
use O2System\Database\DataObjects\Result\Row;
use O2System\Spl\DataStructures\Traits\ArrayConversionTrait;

/**
 * Class Result
 *
 * @package O2System\Database\DataObjects
 */
class Result extends \SplFixedArray
{
    use ArrayConversionTrait;

    /**
     * Result::$info
     *
     * @var Info
     */
    protected $info;

    // ------------------------------------------------------------------------

    /**
     * Result::__construct
     *
     * @param array $rows
     */
    public function __construct(array $rows)
    {
        $this->info = new Info();
        $this->info->num_total = $this->info->num_rows = count($rows);
        
        parent::__construct($this->info->num_rows);

        foreach($rows as $offset => $row) {
            $this->offsetSet($offset, $row);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Result::setNumPerPage
     *
     * @param int $numPerPage
     */
    public function setNumPerPage($numPerPage)
    {
        $this->info->num_per_page = (int)$numPerPage;
    }

    // ------------------------------------------------------------------------

    /**
     * Result::setNumFoundRows
     *
     * @param int $numFounds
     */
    public function setNumFounds($numFounds)
    {
        $this->info->num_founds = (int)$numFounds;

        if($this->info->num_founds > 0 and $this->info->num_per_page > 0) {
            $this->info->num_pages = round($this->info->num_founds / $this->info->num_per_page);
            $this->info->numbering->start = 1;
            $this->info->numbering->end = $this->info->num_per_page;

            if(isset($_GET['page']) and $_GET['page'] > 1) {
                $this->info->numbering->start = ($_GET['page'] - 1) * $this->info->num_per_page;
                $this->info->numbering->end = $this->info->numbering->start + $this->info->num_per_page;
            }
        } else {
            $this->info->num_pages = 1;
            $this->info->numbering->start = 1;
            $this->info->numbering->end = $this->info->num_founds;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Result::setTotalRows
     *
     * @param int $totalRows
     */
    public function setNumTotal($totalRows)
    {
        $this->info->num_total = (int)$totalRows;
    }

    // ------------------------------------------------------------------------

    /**
     * Result::first
     *
     * Gets first result row data.
     *
     * @return \O2System\Database\DataObjects\Result\Row|null
     */
    public function first()
    {
        if($this->count()) {
            $this->rewind();

            return $this->current();
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Result::last
     *
     * Gets last result row data.
     *
     * @return \O2System\Database\DataObjects\Result\Row|null
     */
    public function last()
    {
        if($this->count()) {
            $index = $this->count() - 1;

            if ($this->offsetExists($index)) {
                return $this->offsetGet($index);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Result::previous
     *
     * Move backward to previous element.
     *
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function previous()
    {
        prev($this);
    }

    // ------------------------------------------------------------------------

    /**
     * Result::isEmpty
     *
     * Checks if the array storage is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return ($this->count() == 0 ? true : false);
    }

    // ------------------------------------------------------------------------

    /**
     * Result::getArrayCopy
     *
     * Creates a copy of result rows.
     *
     * @return array A copy of the result rows.
     */
    public function getArrayCopy()
    {
        return $this->toArray();
    }

    // ------------------------------------------------------------------------

    /**
     * Result::offsetSet
     *
     * Offset to set
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        if(is_array($value)) {
            parent::offsetSet($offset, new Row($value));
        } else {
            parent::offsetSet($offset, $value);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Result::__get
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->{$property};
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Result::getInfo
     *
     * @return \O2System\Database\DataObjects\Result\Info
     */
    public function getInfo()
    {
        return $this->info;
    }

    // ------------------------------------------------------------------------

    /**
     * Result::countAll
     *
     * Count all elements
     *
     * @return int Total row as an integer.
     *        </p>
     *        <p>
     *        The return value is cast to an integer.
     *
     */
    public function countAll()
    {
        return $this->info->num_founds;
    }
}