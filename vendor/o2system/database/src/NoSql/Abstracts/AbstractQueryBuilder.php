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

namespace O2System\Database\NoSql\Abstracts;

// ------------------------------------------------------------------------

use O2System\Database\NoSql\DataStructures\Query\BuilderCache;

/**
 * Class AbstractQueryBuilder
 *
 * @package O2System\Database\Abstracts
 */
abstract class AbstractQueryBuilder
{
    /**
     * AbstractQueryBuilder::testMode
     *
     * If true, no queries will actually be
     * ran against the database.
     *
     * @var bool
     */
    public $testMode = false;

    /**
     * AbstractQueryBuilder::$conn
     *
     * Query database connection instance.
     *
     * @var AbstractConnection
     */
    protected $conn;

    /**
     * AbstractQueryBuilder::$builderCache
     *
     * Query builder cache instance.
     *
     * @var \O2System\Database\NoSql\DataStructures\Query\BuilderCache
     */
    protected $builderCache;

    // ------------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::__construct.
     *
     * @param AbstractConnection $conn
     */
    public function __construct(AbstractConnection $conn)
    {
        $this->conn = $conn;
        $this->builderCache = new BuilderCache();
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::select
     *
     * Add SELECT Sql statement portions into Query Builder.
     *
     * @param string|array $field        String of field name
     *                                   Array list of string field names
     *                                   Array list of static
     *
     * @return static
     */
    public function select($field)
    {
        if (strpos($field, ',') !== false) {
            $field = explode(', ', $field);
        } else {
            $field = [$field];
        }

        $field = array_map('trim', $field);

        foreach ($field as $key) {
            $this->builderCache->store('select', $key);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::collection
     *
     * @param string $collection Collection name.
     *
     * @return static
     */
    public function collection($collection)
    {
        $this->from($collection);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::from
     *
     * Generates FROM Sql statement portions into Query Builder.
     *
     * @param string $collection Collection name
     *
     * @return  static
     */
    public function from($collection)
    {
        $this->builderCache->store('from', trim($collection));

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::join
     *
     * Add JOIN Sql statement portions into Query Builder.
     *
     * @param string $collection Collection name
     * @param null   $condition  Join conditions: table.column = other_table.column
     *
     * @return static
     */
    public function join($collection, $condition = null)
    {

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::orWhere
     *
     * Add OR WHERE Sql statement portions into Query Builder
     *
     * @param string|array $field Input name, array of [field => value] (grouped where)
     * @param null|string  $value Input criteria or UPPERCASE grouped type AND|OR
     *
     * @return static
     */
    public function orWhere($field, $value = null)
    {
        $this->prepareWhere($field, $value, 'orWhere');

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::prepareWhereIn
     *
     * @param string|array $field
     * @param null|mixed   $value
     * @param string       $cacheKey
     */
    protected function prepareWhere($field, $value = null, $cacheKey)
    {
        if (is_array($field)) {
            foreach ($field as $name => $value) {
                $this->prepareWhere($name, $value, $cacheKey);
            }
        } elseif (isset($value)) {
            $this->builderCache->store($cacheKey, [$field => $value]);
        }
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::whereIn
     *
     * Add WHERE IN Sql statement portions into Query Builder
     *
     * @param string $field  Input name
     * @param array  $values Array of values criteria
     *
     * @return static
     */
    public function whereIn($field, array $values = [])
    {
        $this->prepareWhereIn($field, $values, 'whereIn');

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::prepareWhereIn
     *
     * @param string $field
     * @param array  $values
     * @param string $cacheKey
     */
    protected function prepareWhereIn($field, array $values = [], $cacheKey)
    {
        if (is_array($field)) {
            foreach ($field as $name => $values) {
                $this->prepareWhereIn($name, $values, $cacheKey);
            }
        } elseif (count($values)) {
            $this->builderCache->store($cacheKey, [$field => $values]);
        }
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::orWhereIn
     *
     * Add OR WHERE IN Sql statement portions into Query Builder
     *
     * @param string $field  Input name
     * @param array  $values Array of values criteria
     *
     * @return static
     */
    public function orWhereIn($field, array $values = [])
    {
        $this->prepareWhereIn($field, $values, 'orWhereIn');

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::whereNotIn
     *
     * Add WHERE NOT IN Sql statement portions into Query Builder
     *
     * @param string $field  Input name
     * @param array  $values Array of values criteria
     *
     * @return static
     */
    public function whereNotIn($field, array $values = [])
    {
        $this->prepareWhereIn($field, $values, 'whereNotIn');

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::orWhereNotIn
     *
     * Add OR WHERE NOT IN Sql statement portions into Query Builder
     *
     * @param string $field  Input name
     * @param array  $values Array of values criteria
     *
     * @return static
     */
    public function orWhereNotIn($field, array $values = [])
    {
        $this->prepareWhereIn($field, $values, 'orWhereNotIn');

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::whereBetween
     *
     * Add WHERE BETWEEN Sql statement portions into Query Builder
     *
     * @param string $field
     * @param int    $start
     * @param int    $end
     *
     * @return static
     */
    public function whereBetween($field, $start, $end)
    {
        $this->prepareWhereBetween($field, $start, $end, 'between');

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::prepareWhereBetween
     *
     * @param string $field
     * @param int    $start
     * @param int    $end
     * @param string $cacheKey
     */
    protected function prepareWhereBetween($field, $start, $end, $cacheKey)
    {
        $this->builderCache->store($cacheKey, [
            $field => [
                'start' => $start,
                'end'   => $end,
            ],
        ]);
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::orWhereBetween
     *
     * Add OR WHERE BETWEEN Sql statement portions into Query Builder
     *
     * @param string $field
     * @param int    $start
     * @param int    $end
     *
     * @return static
     */
    public function orWhereBetween($field, $start, $end)
    {
        $this->prepareWhereBetween($field, $start, $end, 'orBetween');

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::whereNotBetween
     *
     * Add WHERE NOT BETWEEN Sql statement portions into Query Builder
     *
     * @param string $field
     * @param int    $start
     * @param int    $end
     *
     * @return static
     */
    public function whereNotBetween($field, $start, $end)
    {
        $this->prepareWhereBetween($field, $start, $end, 'notBetween');

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::orWhereNotBetween
     *
     * Add OR WHERE NOT BETWEEN Sql statement portions into Query Builder
     *
     * @param string $field
     * @param int    $start
     * @param int    $end
     *
     * @return static
     */
    public function orWhereNotBetween($field, $start, $end)
    {
        $this->prepareWhereBetween($field, $start, $end, 'orNotBetween');

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::like
     *
     * Generates a %LIKE% Sql statement portions of the query.
     * Separates multiple calls with 'AND'.
     *
     * @param string $field         Input name
     * @param string $match         Input criteria match
     * @param string $wildcard      UPPERCASE positions of wildcard character BOTH|BEFORE|AFTER
     * @param bool   $caseSensitive Whether perform case sensitive LIKE or not
     *
     * @return static
     */
    public function like($field, $match = '', $wildcard = 'BOTH', $caseSensitive = true)
    {
        $this->prepareLike($field, $match, $wildcard, $caseSensitive, 'like');

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::prepareLike
     *
     * @param string $field
     * @param string $match
     * @param string $wildcard
     * @param bool   $caseSensitive
     * @param string $cacheKey
     */
    protected function prepareLike($field, $match, $wildcard, $caseSensitive, $cacheKey)
    {
        $match = quotemeta(trim($match));

        switch ($wildcard) {
            default:
            case 'BOTH':
                $match = '^' . $match . '$';
                break;
            case 'BEFORE':
                $match = '^' . $match;
                break;
            case 'AFTER':
                $match = '^' . $match;
                break;
        }

        $flags = 'm|x|s';
        if ($caseSensitive === false) {
            $flags .= '|i';
        }

        $this->builderCache->store($cacheKey, [$field => new \MongoDB\BSON\Regex($match, $flags)]);
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::orLike
     *
     * Add OR LIKE Sql statement portions into Query Builder
     *
     * @param string $field         Input name
     * @param string $match         Input criteria match
     * @param string $wildcard      UPPERCASE positions of wildcard character BOTH|BEFORE|AFTER
     * @param bool   $caseSensitive Whether perform case sensitive LIKE or not
     *
     * @return static
     */
    public function orLike($field, $match = '', $wildcard = 'BOTH', $caseSensitive = true)
    {
        $this->prepareLike($field, $match, $wildcard, $caseSensitive, 'orLike');

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::notLike
     *
     * Add NOT LIKE Sql statement portions into Query Builder
     *
     * @param string $field         Input name
     * @param string $match         Input criteria match
     * @param string $wildcard      UPPERCASE positions of wildcard character BOTH|BEFORE|AFTER
     * @param bool   $caseSensitive Whether perform case sensitive LIKE or not
     *
     * @return static
     */
    public function notLike($field, $match = '', $wildcard = 'BOTH', $caseSensitive = true)
    {
        $this->prepareLike($field, $match, $wildcard, $caseSensitive, 'notLike');

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::orNotLike
     *
     * Add OR NOT LIKE Sql statement portions into Query Builder
     *
     * @param string $field         Input name
     * @param string $match         Input criteria match
     * @param string $wildcard      UPPERCASE positions of wildcard character BOTH|BEFORE|AFTER
     * @param bool   $caseSensitive Whether perform case sensitive LIKE or not
     *
     * @return static
     */
    public function orNotLike($field, $match = '', $wildcard = 'BOTH', $caseSensitive = true)
    {
        $this->prepareLike($field, $match, $wildcard, $caseSensitive, 'orNotLike');

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::groupBy
     *
     * Add GROUP BY Sql statement into Query Builder.
     *
     * @param string $field
     *
     * @return static
     */
    public function groupBy($field)
    {
        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::orderBy
     *
     * Add ORDER BY Sql statement portions into Query Builder.
     *
     * @param string $field
     * @param string $direction
     *
     * @return static
     */
    public function orderBy($field, $direction = 'ASC')
    {
        $this->builderCache->store('orderBy', [$field => strtoupper($direction)]);

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::page
     *
     * Add Set LIMIT, OFFSET Sql statement by page number and entries.
     *
     * @param int  $page    Page number
     * @param null $entries Num entries of each page
     *
     * @return static
     */
    public function page($page = 1, $entries = null)
    {
        $page = (int)intval($page);

        $entries = (int)(isset($entries)
            ? $entries
            : ($this->builderCache->limit === false
                ? 5
                : $this->builderCache->limit
            )
        );

        $offset = ($page - 1) * $entries;

        $this->limit($entries, $offset);

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::limit
     *
     * Add LIMIT,OFFSET Sql statement into Query Builder.
     *
     * @param    int $limit  LIMIT value
     * @param    int $offset OFFSET value
     *
     * @return    static
     */
    public function limit($limit, $offset = 0)
    {
        $this->builderCache->store('limit', $limit);
        $this->offset($offset);

        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::offset
     *
     * Add OFFSET Sql statement into Query Builder.
     *
     * @param    int $offset OFFSET value
     *
     * @return    static
     */
    public function offset($offset)
    {
        $this->builderCache->store('offset', $offset);

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::get
     *
     * Perform execution of Sql Query Builder and run ConnectionInterface::query()
     *
     * @param null|int $limit
     * @param null|int $offset
     *
     * @return bool|\O2System\Database\DataObjects\Result
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    public function get($limit = null, $offset = null)
    {
        if (isset($limit)) {
            $this->limit($limit, $offset);
        }

        if (false !== ($result = $this->conn->query($this->builderCache))) {
            $result->setNumTotal($this->countAllResults(true));
        }

        $this->builderCache->reset();

        return $result;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::countAllResult
     *
     * Returns numbers of total documents.
     *
     * @param bool $reset Whether perform reset Query Builder or not
     *
     * @return int
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @access   public
     */
    abstract public function countAllResults($reset = true);

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::getWhere
     *
     * Perform execution of Sql Query Builder and run ConnectionInterface::query()
     *
     * @param array    $where
     * @param null|int $limit
     * @param null|int $offset
     *
     * @return bool|\O2System\Database\DataObjects\Result
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    public function getWhere(array $where = [], $limit = null, $offset = null)
    {
        $this->where($where);

        if (isset($limit)) {
            $this->limit($limit, $offset);
        }

        if (false !== ($result = $this->conn->query($this->builderCache))) {
            $result->setNumTotal($this->countAllResults(true));
        }

        $this->builderCache->reset();

        return $result;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::where
     *
     * Add WHERE Sql statement portions into Query Builder
     *
     * @param string|array $field Input name, array of [field => value] (grouped where)
     * @param null|string  $value Input criteria or UPPERCASE grouped type AND|OR
     *
     * @return static
     */
    public function where($field, $value = null)
    {
        $this->prepareWhere($field, $value, 'where');

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::countAll
     *
     * Returns numbers of query result.
     *
     * @access  public
     * @return int|string
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    abstract public function countAll();

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::insert
     *
     * @param array $sets
     *
     * @return bool
     */
    public function insert(array $sets)
    {
        $this->builderCache->store('sets', $sets);

        return $this->platformInsertHandler($this->builderCache);
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::platformInsertHandler
     *
     * @param \O2System\Database\NoSql\DataStructures\Query\BuilderCache $builderCache
     *
     * @return bool
     */
    abstract protected function platformInsertHandler(BuilderCache $builderCache);

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::insertBatch
     *
     * @param array $sets
     *
     * @return bool
     */
    public function insertBatch(array $sets)
    {
        $this->builderCache->store('sets', $sets);

        return $this->platformInsertBatchHandler($this->builderCache);
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::platformInsertBatchHandler
     *
     * @param BuilderCache $builderCache
     *
     * @return bool
     */
    abstract protected function platformInsertBatchHandler(BuilderCache $builderCache);

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::update
     *
     * @param array $sets
     * @param array $where
     *
     * @return bool
     */
    public function update(array $sets, array $where = [])
    {
        $this->where($where);
        $this->builderCache->store('sets', $sets);

        return $this->platformUpdateHandler($this->builderCache);
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::platformUpdateHandler
     *
     * @param BuilderCache $builderCache
     *
     * @return bool
     */
    abstract protected function platformUpdateHandler(BuilderCache $builderCache);

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::updateBatch
     *
     * @param array $sets
     * @param array $where
     *
     * @return bool
     */
    public function updateBatch(array $sets, array $where = [])
    {
        $this->where($where);
        $this->builderCache->store('sets', $sets);

        return $this->platformUpdateBatchHandler($this->builderCache);
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::platformUpdateBatchHandler
     *
     * @param BuilderCache $builderCache
     *
     * @return bool
     */
    abstract protected function platformUpdateBatchHandler(BuilderCache $builderCache);

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::replace
     *
     * @param array $sets
     * @param array $where
     *
     * @return bool
     */
    public function replace(array $sets, array $where = [])
    {
        $this->where($where);
        $this->builderCache->store('sets', $sets);

        return $this->platformReplaceHandler($this->builderCache);
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::platformReplaceHandler
     *
     * @param BuilderCache $builderCache
     *
     * @return bool
     */
    abstract protected function platformReplaceHandler(BuilderCache $builderCache);

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::replaceBatch
     *
     * @param array $sets
     * @param array $where
     *
     * @return bool
     */
    public function replaceBatch(array $sets, array $where = [])
    {
        $this->where($where);
        $this->builderCache->store('sets', $sets);

        return $this->platformReplaceBatchHandler($this->builderCache);
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::platformReplaceBatchHandler
     *
     * @param BuilderCache $builderCache
     *
     * @return bool
     */
    abstract protected function platformReplaceBatchHandler(BuilderCache $builderCache);

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::delete
     *
     * @param array $where Where clause.
     * @param int   $limit Limit clause.
     *
     * @return bool
     */
    public function delete($where = [], $limit = null)
    {
        $this->where($where);

        if (isset($limit)) {
            $this->limit($limit);
        }

        return $this->platformDeleteHandler($this->builderCache);
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::platformDeleteHandler
     *
     * @param BuilderCache $builderCache
     *
     * @return bool
     */
    abstract protected function platformDeleteHandler(BuilderCache $builderCache);

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::deleteBatch
     *
     * @param array $where Where clause.
     * @param int   $limit Limit clause.
     *
     * @return bool
     */
    public function deleteBatch($where = [], $limit = null)
    {
        $this->where($where);

        if (isset($limit)) {
            $this->limit($limit);
        }

        return $this->platformDeleteBatchHandler($this->builderCache);
    }

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::platformDeleteBatchHandler
     *
     * @param BuilderCache $builderCache
     *
     * @return bool
     */
    abstract protected function platformDeleteBatchHandler(BuilderCache $builderCache);
}
