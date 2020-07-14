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
     * QueryStatement::$sqlStatement
     *
     * The Sql Statement.
     *
     * @var string
     */
    private $sqlStatement;

    /**
     * QueryStatement::$SqlBinds
     *
     * The Sql Statement bindings.
     *
     * @var array
     */
    private $sqlBinds = [];


    /**
     * QueryStatement::$SqlFinalStatement
     *
     * The compiled Sql Statement with Sql Statement binders.
     *
     * @var string
     */
    private $sqlFinalStatement;

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

    private $hits = 0;

    /**
     * QueryStatement::$affectedRows
     *
     * The numbers of affected rows.
     *
     * @var int
     */
    private $affectedRows;

    /**
     * QueryStatement::$lastInsertId
     *
     * The last insert id.
     *
     * @var mixed
     */
    private $lastInsertId;

    /**
     * QueryStatement::$error
     *
     * The query execution error info.
     *
     * @var array
     */
    private $error;

    //--------------------------------------------------------------------

    /**
     * QueryStatement::setBinds
     *
     * Will store the variables to bind into the query later.
     *
     * @param array $SqlBinds
     *
     * @return static
     */
    public function setBinds(array $SqlBinds)
    {
        $this->sqlBinds = $SqlBinds;

        return $this;
    }

    //--------------------------------------------------------------------

    public function getBinds()
    {
        return $this->sqlBinds;
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
     * QueryStatement::setErrorInfo
     *
     * Stores the occurred error information when the query was executed.
     *
     * @param int    $errorCode
     * @param string $errorMessage
     *
     * @return static
     */
    public function setError($errorCode, $errorMessage)
    {
        $this->error[ $errorCode ] = $errorMessage;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::getAffectedRows
     *
     * Gets numbers of affected rows.
     *
     * @return int
     */
    public function getAffectedRows()
    {
        return (int)$this->affectedRows;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::setAffectedRows
     *
     * Sets numbers of affected rows.
     *
     * @param int $affectedRows Numbers of affected rows.
     *
     * @return static
     */
    public function setAffectedRows($affectedRows)
    {
        $this->affectedRows = $affectedRows;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::getAffectedRows
     *
     * Gets numbers of affected rows.
     *
     * @return int
     */
    public function getLastInsertId()
    {
        return $this->affectedRows;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::setAffectedRows
     *
     * Sets numbers of affected rows.
     *
     * @param int $affectedRows Numbers of affected rows.
     *
     * @return static
     */
    public function setLastInsertId($affectedRows)
    {
        $this->affectedRows = $affectedRows;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::isWriteStatement
     *
     * Determines if the Sql statement is a write-syntax query or not.
     *
     * @return bool
     */
    public function isWriteStatement()
    {
        return (bool)preg_match(
            '/^\s*"?(SET|INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|TRUNCATE|LOAD|COPY|ALTER|RENAME|GRANT|REVOKE|LOCK|UNLOCK|REINDEX)\s/i',
            $this->sqlStatement
        );
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::replacePrefix
     *
     * Replace all table prefix with new prefix.
     *
     * @param string $search
     * @param string $replace
     *
     * @return mixed
     */
    public function swapTablePrefix($search, $replace)
    {
        $Sql = empty($this->sqlFinalStatement) ? $this->sqlStatement : $this->sqlFinalStatement;

        $this->sqlFinalStatement = preg_replace('/(\W)' . $search . '(\S+?)/', '\\1' . $replace . '\\2', $Sql);

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::getSqlStatement
     *
     * Get the original Sql statement.
     *
     * @return string   The Sql statement string.
     */
    public function getSqlStatement()
    {
        return $this->sqlStatement;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::setStatement
     *
     * Sets the raw query string to use for this statement.
     *
     * @param string $sqlStatement The Sql Statement.
     * @param array  $SqlBinds     The Sql Statement bindings.
     *
     * @return static
     */
    public function setSqlStatement($sqlStatement, array $SqlBinds = [])
    {
        $this->sqlStatement = $sqlStatement;
        $this->sqlBinds = $SqlBinds;

        return $this;
    }

    //--------------------------------------------------------------------

    public function getKey()
    {
        return md5($this->getSqlFinalStatement());
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::getSqlFinalStatement
     *
     * Returns the final, processed query string after binding, etal
     * has been performed.
     *
     * @return string
     */
    public function getSqlFinalStatement()
    {
        return $this->sqlFinalStatement;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::setSqlFinalStatement
     *
     * @param string $finalStatement
     *
     * @return static
     */
    public function setSqlFinalStatement($finalStatement)
    {
        $this->sqlFinalStatement = $finalStatement;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::getHits
     *
     * Gets num of hits.
     *
     * @return int
     */
    public function getHits()
    {
        return $this->hits;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::addHit
     *
     * @param int $hit
     *
     * @return $this
     */
    public function addHit($hit = 1)
    {
        $this->hits = $this->hits + (int)$hit;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * QueryStatement::__toString
     *
     * Convert this query into compiled Sql Statement string.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getSqlFinalStatement();
    }
}
