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

namespace O2System\Database\NoSql\DataStructures;

// ------------------------------------------------------------------------

use O2System\Spl\Traits\Collectors\ErrorCollectorTrait;

/**
 * Class QueryStatement
 *
 * @package O2System\Database\Sql\DataStructures
 */
class QueryStatement
{
    use ErrorCollectorTrait;

    /**
     * QueryStatement::$builderCache
     *
     * @var \O2System\Database\NoSql\DataStructures\QueryBuilderCache
     */
    private $builderCache;

    /**
     * QueryStatement::$collection
     *
     * Query collection name.
     *
     * @var string
     */
    private $collection;

    /**
     * QueryStatement::$document
     *
     * Query document.
     *
     * @var array
     */
    private $document = [];

    /**
     * QueryStatement::$filter
     *
     * Query filter array.
     *
     * @var array
     */
    private $filter = [];

    /**
     * QueryStatement::$options
     *
     * Query options array.
     *
     * @var array
     */
    private $options = [];

    /**
     * QueryStatement::$startExecutionTime
     *
     * The start time in seconds with microseconds
     * for when this query was executed.
     *
     * @var float
     */
    private $startExecutionTime;

    /**
     * QueryStatement::$endExecutionTime
     *
     * The end time in seconds with microseconds
     * for when this query was executed.
     *
     * @var float
     */
    private $endExecutionTime;

    /**
     * QueryStatement::$affectedDocuments
     *
     * The numbers of affected documents.
     *
     * @var int
     */
    private $affectedDocuments;

    /**
     * QueryStatement::$lastInsertId
     *
     * The last insert id.
     *
     * @var string|int
     */
    private $lastInsertId;

    //--------------------------------------------------------------------

    /**
     * QueryStatement::__construct
     *
     * @param \O2System\Database\NoSql\DataStructures\QueryBuilderCache $queryBuilderCache
     */
    public function __construct(QueryBuilderCache $queryBuilderCache)
    {
        $this->builderCache = $queryBuilderCache;
        $this->setCollection($queryBuilderCache->from);

        if (count($queryBuilderCache->sets)) {
            $this->document = $queryBuilderCache->sets;
        }
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::getBuilderCache
     *
     * @return \O2System\Database\NoSql\DataStructures\QueryBuilderCache
     */
    public function getBuilderCache()
    {
        return $this->builderCache;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::getCollection
     *
     * Get Query Collection name
     *
     * @return string
     */
    public function getCollection()
    {
        return $this->collection;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::setCollection
     *
     * Set Query Collection name
     *
     * @param   string $collection
     *
     * @return static
     */
    public function setCollection($collection)
    {
        $this->collection = trim($collection);

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::addFilter
     *
     * Add Query Filter
     *
     * @param string $field
     * @param int    $value
     *
     * @return static
     */
    public function addFilter($field, $value)
    {
        $this->filter[ $field ] = $value;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::getFilter
     *
     * Get Query Filter
     *
     * @return array
     */
    public function getFilter()
    {
        return $this->filter;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::setFilter
     *
     * Set Query Filter Array
     *
     * @param array $filter
     *
     * @return static
     */
    public function setFilter(array $filter)
    {
        $this->filter = $filter;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::addOption
     *
     * Add Query Option
     *
     * @param string $option
     * @param mixed  $value
     *
     * @return  static
     */
    public function addOption($option, $value)
    {
        $this->options[ $option ] = $value;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::getOptions
     *
     * Get Query Options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::setOptions
     *
     * Set Query Options
     *
     * @param array $options
     *
     * @return static
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::getDocument
     *
     * Get Query Document
     *
     * @return array
     */
    public function getDocument()
    {
        return $this->document;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::setDuration
     *
     * Records the execution time of the statement using microtime(true)
     * for it's start and end values. If no end value is present, will
     * use the current time to determine total duration.
     *
     * @param int      $start
     * @param int|null $end
     *
     * @return static
     */
    public function setDuration($start, $end = null)
    {
        $this->startExecutionTime = $start;

        if (is_null($end)) {
            $end = microtime(true);
        }

        $this->endExecutionTime = $end;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::getStartExecutionTime
     *
     * Returns the start time in seconds with microseconds.
     *
     * @param bool $numberFormat
     * @param int  $decimals
     *
     * @return mixed
     */
    public function getStartExecutionTime($numberFormat = false, $decimals = 6)
    {
        if ( ! $numberFormat) {
            return $this->startExecutionTime;
        }

        return number_format($this->startExecutionTime, $decimals);
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::getExecutionDuration
     *
     * Returns the duration of this query during execution, or null if
     * the query has not been executed yet.
     *
     * @param int $decimals The accuracy of the returned time.
     *
     * @return mixed
     */
    public function getExecutionDuration($decimals = 6)
    {
        return number_format(($this->endExecutionTime - $this->startExecutionTime), $decimals);
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::getAffectedRows
     *
     * Gets numbers of affected rows.
     *
     * @return int
     */
    public function getAffectedDocuments()
    {
        return $this->affectedDocuments;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::setAffectedRows
     *
     * Sets numbers of affected rows.
     *
     * @param int $affectedDocuments Numbers of affected rows,
     *
     * @return static
     */
    public function setAffectedDocuments($affectedDocuments)
    {
        $this->affectedDocuments = $affectedDocuments;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::getAffectedRows
     *
     * Gets query last insert id.
     *
     * @return string|int
     */
    public function getLastInsertId()
    {
        return $this->lastInsertId;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::setAffectedRows
     *
     * Sets query last insert id.
     *
     * @param string|int
     *
     * @return static
     */
    public function setLastInsertId($lastInsertId)
    {
        $this->lastInsertId = $lastInsertId;

        return $this;
    }
}
