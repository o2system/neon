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

namespace O2System\Database\Sql\DataStructures\Query;

// ------------------------------------------------------------------------

use O2System\Spl\Traits\Collectors\ErrorCollectorTrait;

/**
 * Class Statement
 *
 * @package O2System\Database\Sql\DataStructures
 */
class Statement
{
    use ErrorCollectorTrait;

    /**
     * Statement::$sqlStatement
     *
     * The Sql Statement.
     *
     * @var string
     */
    private $sqlStatement;

    /**
     * Statement::$SqlBinds
     *
     * The Sql Statement bindings.
     *
     * @var array
     */
    private $sqlBinds = [];

    /**
     * Statement::$SqlFinalStatement
     *
     * The compiled Sql Statement with Sql Statement binders.
     *
     * @var string
     */
    private $sqlFinalStatement;

    /**
     * Statement::$startExecutionTime
     *
     * The start time in seconds with microseconds
     * for when this query was executed.
     *
     * @var float
     */
    private $startExecutionTime;

    /**
     * Statement::$endExecutionTime
     *
     * The end time in seconds with microseconds
     * for when this query was executed.
     *
     * @var float
     */
    private $endExecutionTime;

    /**
     * Statement::$hits
     *
     * @var int
     */
    private $hits = 0;

    /**
     * Statement::$affectedRows
     *
     * The numbers of affected rows.
     *
     * @var int
     */
    private $affectedRows;

    /**
     * Statement::$lastInsertId
     *
     * The last insert id.
     *
     * @var mixed
     */
    private $lastInsertId;

    //--------------------------------------------------------------------

    /**
     * Statement::setBinds
     *
     * Will store the variables to bind into the query later.
     *
     * @param array $sqlBinds
     *
     * @return static
     */
    public function setBinds(array $sqlBinds)
    {
        $this->sqlBinds = $sqlBinds;

        return $this;
    }

    //--------------------------------------------------------------------

    public function getBinds()
    {
        return $this->sqlBinds;
    }

    //--------------------------------------------------------------------

    /**
     * Statement::setDuration
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
     * Statement::getStartExecutionTime
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
     * Statement::getExecutionDuration
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
     * Statement::getAffectedRows
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
     * Statement::setAffectedRows
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
     * Statement::getAffectedRows
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
     * Statement::setAffectedRows
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
     * Statement::isWriteStatement
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
     * Statement::replacePrefix
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
     * Statement::getSqlStatement
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
     * Statement::setStatement
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
     * Statement::getSqlFinalStatement
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
     * Statement::setSqlFinalStatement
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
     * Statement::getHits
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
     * Statement::addHit
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
     * Statement::__toString
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
