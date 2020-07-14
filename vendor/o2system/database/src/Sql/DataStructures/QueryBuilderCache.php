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

namespace O2System\Database\Sql\DataStructures;

// ------------------------------------------------------------------------

/**
 * Class QueryBuilderCache
 *
 * @package O2System\Database\Sql\DataStructures
 */
class QueryBuilderCache extends \ArrayObject
{
    /**
     * QueryBuilderCache::__construct
     *
     */
    public function __construct()
    {
        parent::__construct([
            'select'        => [],
            'union'         => [],
            'unionAll'      => [],
            'into'          => false,
            'distinct'      => false,
            'from'          => [],
            'join'          => [],
            'where'         => [],
            'having'        => [],
            'between'       => [],
            'notBetween'    => [],
            'limit'         => false,
            'offset'        => false,
            'groupBy'       => [],
            'orderBy'       => [],
            'keys'          => [],
            'sets'          => [],
            'binds'         => [],
            'aliasedTables' => [],
            'noEscape'      => [],
            'bracketOpen'   => false,
            'bracketCount'  => 0,
            'statement'     => null,
        ], \ArrayObject::ARRAY_AS_PROPS);
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
        $this->offsetSet('statement', trim($statement));
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
        return $this->offsetGet('statement');
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
                'select'        => [],
                'union'         => [],
                'unionAll'      => [],
                'into'          => false,
                'distinct'      => false,
                'from'          => [],
                'join'          => [],
                'where'         => [],
                'having'        => [],
                'between'       => [],
                'notBetween'    => [],
                'limit'         => false,
                'offset'        => false,
                'groupBy'       => [],
                'orderBy'       => [],
                'keys'          => [],
                'binds'         => [],
                'aliasedTables' => [],
                'noEscape'      => [],
                'bracketOpen'   => false,
                'bracketCount'  => 0,
                'statement'     => null,
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
        parent::__construct(array_merge([
            'select'        => [],
            'union'         => [],
            'unionAll'      => [],
            'into'          => false,
            'distinct'      => false,
            'from'          => [],
            'join'          => [],
            'where'         => [],
            'having'        => [],
            'between'       => [],
            'notBetween'    => [],
            'limit'         => false,
            'offset'        => false,
            'groupBy'       => [],
            'orderBy'       => [],
            'keys'          => [],
            'sets'          => [],
            'binds'         => [],
            'aliasedTables' => [],
            'noEscape'      => [],
            'bracketOpen'   => false,
            'bracketCount'  => 0,
            'statement'     => null,
        ], $cacheKeys), \ArrayObject::ARRAY_AS_PROPS);
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
                'from'          => [],
                'binds'         => [],
                'sets'          => [],
                'join'          => [],
                'where'         => [],
                'having'        => [],
                'between'       => [],
                'notBetween'    => [],
                'keys'          => [],
                'limit'         => false,
                'aliasedTables' => [],
                'noEscape'      => [],
                'bracketOpen'   => false,
                'bracketCount'  => 0,
                'statement'     => null,
            ]
        );
    }
}
