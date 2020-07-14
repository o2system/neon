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

namespace O2System\Database\Sql\Drivers\MySql;

// ------------------------------------------------------------------------

use O2System\Database\Sql\Abstracts\AbstractQueryBuilder;

/**
 * Class QueryBuilder
 *
 * @package O2System\Database\Sql\Drivers\MySql
 */
class QueryBuilder extends AbstractQueryBuilder
{
    /**
     * AbstractQueryBuilder::countAllResult
     *
     * Perform execution of count all result from Query Builder along with WHERE, LIKE, HAVING, GROUP BY, and LIMIT Sql
     * statement.
     *
     * @param bool $reset Whether perform reset Query Builder or not
     *
     * @return int
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     * @access   public
     */
    public function countAllResults($reset = true)
    {
        // generate Sql statement
        $sqlStatement = $this->getSqlStatement();

        if ($this->testMode) {
            return $sqlStatement;
        }

        $this->conn->query($sqlStatement, $this->builderCache->binds);
        $result = $this->conn->query('SELECT FOUND_ROWS() AS numrows;');

        if ($reset === true) {
            $this->builderCache->reset();
        }

        if ($result->count() == 0) {
            return 0;
        }

        return (int)$result->first()->numrows;
    }

    //--------------------------------------------------------------------

    /**
     * Platform independent LIKE statement builder.
     *
     * @param string|null $prefix
     * @param string      $column
     * @param string|null $not
     * @param string      $bind
     * @param bool        $caseSensitive
     *
     * @return string
     */
    protected function platformPrepareLikeStatement(
        $prefix = null,
        $column,
        $not = null,
        $bind,
        $caseSensitive = false
    ) {
        $likeStatement = "{$prefix} {$column} {$not} LIKE :{$bind}";

        if ($caseSensitive === true) {
            $likeStatement = "{$prefix} LOWER({$column}) {$not} LIKE :{$bind}";
        }

        return $likeStatement;
    }

    //--------------------------------------------------------------------

    /**
     * QueryBuilder::platformInsertStatement
     *
     * Generates a platform-specific insert string from the supplied data.
     *
     * @param string $table  Table name.
     * @param array  $keys   Insert keys.
     * @param array  $values Insert values.
     *
     * @return string
     */
    protected function platformInsertStatement($table, array $keys, array $values)
    {
        return 'INSERT INTO '
            . $table
            . ' ('
            . implode(', ', $keys)
            . ') VALUES ('
            . implode(', ', $values)
            . ')';
    }

    //--------------------------------------------------------------------

    /**
     * QueryBuilder::platformInsertBatchStatement
     *
     * @param string $table
     * @param array  $keys
     * @param array  $values
     *
     * @return mixed
     */
    protected function platformInsertBatchStatement($table, array $keys, array $values)
    {
        return 'INSERT INTO '
            . $table
            . ' ('
            . implode(', ', $keys)
            . ') VALUES '
            . implode(', ', $values);
    }

    //--------------------------------------------------------------------

    /**
     * QueryBuilder::platformReplaceStatement
     *
     * Generates a platform-specific update string from the supplied data.
     *
     * @param string $table  Table name.
     * @param array  $keys   Insert keys.
     * @param array  $values Insert values.
     *
     * @return string
     */
    protected function platformReplaceStatement($table, array $keys, array $values)
    {
        return 'REPLACE INTO ' . $table . ' (' . implode(', ', $keys) . ') VALUES (' . implode(', ', $values) . ')';
    }

    //--------------------------------------------------------------------

    /**
     * QueryBuilder::platformUpdateStatement
     *
     * Generates a platform-specific update string from the supplied data.
     *
     * @param string $table    Table name.
     * @param array  $sets     An associative array of set values.
     *                         sets[][field => value]
     *
     * @return string
     */
    protected function platformUpdateStatement($table, array $sets)
    {
        $columns = [];

        foreach ($sets as $key => $val) {
            $columns[] = $key . ' = ' . $val;
        }

        return 'UPDATE ' . $table . ' SET ' . implode(', ', $columns)
            . $this->compileWhereHavingStatement('where')
            . $this->compileOrderByStatement()
            . $this->compileLimitStatement();
    }

    //--------------------------------------------------------------------

    /**
     * QueryBuilder::platformUpdateBatchStatement
     *
     * Generates a platform-specific batch update string from the supplied data.
     *
     * @param string $table  Table name
     * @param array  $values Update data
     * @param string $index  WHERE key
     *
     * @return    string
     */
    protected function platformUpdateBatchStatement($table, $values, $index)
    {
        $ids = [];
        $columns = [];

        foreach ($values as $key => $value) {
            $ids[] = $value[ $index ];

            foreach (array_keys($value) as $field) {
                if ($field !== $index) {
                    $columns[ $field ][] = 'WHEN ' . $index . ' = ' . $value[ $index ] . ' THEN ' . $value[ $field ];
                }
            }
        }

        $cases = '';
        foreach ($columns as $key => $value) {
            $cases .= $key . " = CASE \n"
                . implode("\n", $value) . "\n"
                . 'ELSE ' . $key . ' END, ';
        }

        $this->where($index . ' IN(' . implode(',', $ids) . ')', null, false);

        return 'UPDATE ' . $table . ' SET ' . substr($cases, 0, -2) . $this->compileWhereHavingStatement('where');
    }

    //--------------------------------------------------------------------

    /**
     * QueryBuilder::platformDeleteStatement
     *
     * Generates a platform-specific delete string from the supplied data
     *
     * @param string $table The table name.
     *
     * @return  string
     */
    protected function platformDeleteStatement($table)
    {
        return 'DELETE FROM ' . $table
            . $this->compileWhereHavingStatement('where')
            . $this->compileLimitStatement();
    }

    //--------------------------------------------------------------------

    /**
     * QueryBuilder::platformTruncateStatement
     *
     * Generates a platform-specific truncate statement.
     *
     * @param string $table The table name.
     *
     * @return  string
     */
    protected function platformTruncateStatement($table)
    {
        return 'TRUNCATE ' . $table;
    }

    //--------------------------------------------------------------------

    /**
     * QueryBuilder::compileSelectStatement
     *
     * Compile the SELECT statement
     *
     * Generates a query string based on which functions were used.
     * Should not be called directly.
     *
     * @param bool $selectOverride
     *
     * @return    string
     */
    protected function compileSelectStatement($selectOverride = false)
    {
        $sqlStatement = parent::compileSelectStatement($selectOverride);

        if ($this->isSubQuery) {
            return $sqlStatement;
        } elseif(strpos($sqlStatement, 'COUNT') !== false) {
            return $sqlStatement;
        }

        return str_replace('SELECT', 'SELECT SQL_CALC_FOUND_ROWS', $sqlStatement);
    }
}
