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

/**
 * Class QueryBuilderCache
 *
 * @package O2System\Database\Sql\DataStructures
 */
class QueryBuilderCache
{
    /**
     * QueryBuilderCache::$storage
     *
     * Query builder cache.
     *
     * @var array
     */
    protected $vars
        = [
            'select'       => [],
            'from'         => null,
            'join'         => [],
            'where'        => [],
            'orWhere'      => [],
            'whereNot'     => [],
            'orWhereNot'   => [],
            'whereIn'      => [],
            'orWhereIn'    => [],
            'whereNotIn'   => [],
            'orWhereNotIn' => [],
            'having'       => [],
            'orHaving'     => [],
            'between'      => [],
            'orBetween'    => [],
            'notBetween'   => [],
            'orNotBetween' => [],
            'like'         => [],
            'notLike'      => [],
            'orLike'       => [],
            'orNotLike'    => [],
            'limit'        => 0,
            'offset'       => 0,
            'groupBy'      => [],
            'orderBy'      => [],
            'sets'         => [],
        ];

    /**
     * QueryBuilderCache::$statement
     *
     * Query builder cache statement.
     *
     * @var string
     */
    protected $statement;

    // ------------------------------------------------------------------------

    /**
     * QueryBuilderCache::__get
     *
     *
     * @param  string $property
     *
     * @return mixed
     */
    public function &__get($property)
    {
        return $this->vars[ $property ];
    }

    // ------------------------------------------------------------------------

    /**
     * QueryBuilderCache::store
     *
     * @param  string|bool $index
     * @param  array|bool  $value
     *
     * @return static
     */
    public function store($index, $value)
    {
        if (array_key_exists($index, $this->vars)) {
            if (is_array($this->vars[ $index ])) {
                if (is_array($value)) {
                    $this->vars[ $index ] = array_merge($this->vars[ $index ], $value);
                } else {
                    array_push($this->vars[ $index ], $value);
                }
            } elseif (is_bool($this->vars[ $index ])) {
                $this->vars[ $index ] = (bool)$value;
            } else {
                $this->vars[ $index ] = $value;
            }
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * QueryBuilderCache::getStatement
     *
     * Get Statement Query Builder cache
     *
     * @return string
     */
    public function getStatement()
    {
        return $this->statement;
    }

    // ------------------------------------------------------------------------

    /**
     * QueryBuilderCache::setStatement
     *
     * Set Statement Query Builder cache
     *
     * @param string $statement
     */
    public function setStatement($statement)
    {
        $this->statement = trim($statement);
    }

    // ------------------------------------------------------------------------

    /**
     * QueryBuilderCache::reset
     *
     * Reset Query Builder cache.
     *
     * @return  static
     */
    public function reset()
    {
        $this->resetGetter();
        $this->resetModifier();

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * QueryBuilderCache::resetGetter
     *
     * Resets the query builder values.  Called by the get() function
     *
     * @return  void
     */
    public function resetGetter()
    {
        $this->resetRun(
            [
                'select'       => [],
                'from'         => null,
                'join'         => [],
                'where'        => [],
                'orWhere'      => [],
                'whereNot'     => [],
                'orWhereNot'   => [],
                'whereIn'      => [],
                'orWhereIn'    => [],
                'whereNotIn'   => [],
                'orWhereNotIn' => [],
                'having'       => [],
                'orHaving'     => [],
                'between'      => [],
                'orBetween'    => [],
                'notBetween'   => [],
                'orNotBetween' => [],
                'like'         => [],
                'notLike'      => [],
                'orLike'       => [],
                'orNotLike'    => [],
                'limit'        => 0,
                'offset'       => 0,
                'groupBy'      => [],
                'orderBy'      => [],
            ]
        );
    }

    // ------------------------------------------------------------------------

    /**
     * QueryBuilderCache::resetRun
     *
     * Resets the query builder values.  Called by the get() function
     *
     * @param   array $cacheKeys An array of fields to reset
     *
     * @return  void
     */
    protected function resetRun(array $cacheKeys)
    {
        foreach ($cacheKeys as $cacheKey => $cacheDefaultValue) {
            $this->vars[ $cacheKey ] = $cacheDefaultValue;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * QueryBuilderCache::resetModifier
     *
     * Resets the query builder "modifier" values.
     *
     * Called by the insert() update() insertBatch() updateBatch() and delete() functions
     *
     * @return  void
     */
    public function resetModifier()
    {
        $this->resetRun(
            [
                'from'         => null,
                'join'         => [],
                'where'        => [],
                'orWhere'      => [],
                'whereNot'     => [],
                'orWhereNot'   => [],
                'whereIn'      => [],
                'orWhereIn'    => [],
                'whereNotIn'   => [],
                'orWhereNotIn' => [],
                'having'       => [],
                'orHaving'     => [],
                'between'      => [],
                'orBetween'    => [],
                'notBetween'   => [],
                'orNotBetween' => [],
                'like'         => [],
                'notLike'      => [],
                'orLike'       => [],
                'orNotLike'    => [],
                'limit'        => 0,
                'sets'         => [],
            ]
        );
    }
}
