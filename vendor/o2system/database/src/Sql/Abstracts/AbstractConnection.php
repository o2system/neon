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

namespace O2System\Database\Sql\Abstracts;

// ------------------------------------------------------------------------

use O2System\Cache\Item;
use O2System\Database\DataObjects\Result;
use O2System\Database\DataStructures\Config;
use O2System\Database\Sql\DataStructures\Query\Statement;
use O2System\Spl\Exceptions\RuntimeException;
use O2System\Spl\Traits\Collectors\ConfigCollectorTrait;
use O2System\Spl\Traits\Collectors\ErrorCollectorTrait;

/**
 * Class AbstractConnection
 *
 * @package O2System\Database\Sql\Abstracts
 */
abstract class AbstractConnection
{
    use ConfigCollectorTrait;
    use ErrorCollectorTrait;

    /**
     * AbstractConnection::$debugEnable
     *
     * Connection debug mode flag.
     *
     * @var bool
     */
    public $debugEnable = false;

    /**
     * AbstractConnection::$transactionEnable
     *
     * Connection debug mode flag.
     *
     * @var bool
     */
    public $transactionEnable = false;
    /**
     * AbstractConnection::$database
     *
     * Connection database name.
     *
     * @var string
     */
    public $database;
    /**
     * AbstractConnection::$swapTablePrefix
     *
     * Swap database table prefix.
     *
     * @var string
     */
    public $swapTablePrefix;
    /**
     * AbstractConnection::$protectIdentifiers
     *
     * Protect identifiers mode flag.
     *
     * @var bool
     */
    public $protectIdentifiers = true;
    /**
     * AbstractConnection::$disableQueryExecution
     *
     * Query execution mode flag.
     *
     * @var bool
     */
    public $disableQueryExecution = false;
    /**
     * AbstractConnection::$queriesResultCache
     *
     * Array of query objects that have executed
     * on this connection.
     *
     * @var array
     */
    public $queriesResultCache = [];
    /**
     * AbstractConnection::$cacheEnable
     *
     * Connection cache mode flag.
     *
     * @var bool
     */
    protected $cacheEnable = false;
    /**
     * AbstractConnection::$platform
     *
     * Database driver platform name.
     *
     * @var string
     */
    protected $platform;

    /**
     * AbstractConnection::$handle
     *
     * Connection handle
     *
     * @var mixed
     */
    protected $handle;

    /**
     * AbstractConnection::$persistent
     *
     * Connection persistent mode flag.
     *
     * @var bool
     */
    protected $persistent = true;

    /**
     * AbstractConnection::$connectTimeStart
     *
     * Microtime when connection was made.
     *
     * @var float
     */
    protected $connectTimeStart;

    /**
     * AbstractConnection::$connectTimeDuration
     *
     * How long it took to establish connection.
     *
     * @var float
     */
    protected $connectTimeDuration;

    /**
     * AbstractConnection::$transactionInProgress
     *
     * Transaction is in progress.
     *
     * @var bool
     */
    protected $transactionInProgress = false;

    /**
     * AbstractConnection::$transactionStatus
     *
     * Transaction status flag.
     *
     * @var bool
     */
    protected $transactionStatus = false;

    /**
     * AbstractConnection::$transactionDepth
     *
     * Transaction depth numbers.
     *
     * @var int
     */
    protected $transactionDepth = 0;

    /**
     * AbstractConnection::$queriesCache
     *
     * Array of query objects that have executed
     * on this connection.
     *
     * @var array
     */
    protected $queriesCache = [];

    /**
     * AbstractConnection::$queryBuilder
     *
     * Query Builder instance.
     *
     * @var AbstractQueryBuilder
     */
    protected $queryBuilder;

    /**
     * AbstractConnection::$forge
     *
     * Forge instance.
     *
     * @var AbstractForge
     */
    protected $forge;

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::__construct
     *
     * @param \O2System\Database\DataStructures\Config $config
     *
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    public function __construct(Config $config)
    {
        language()
            ->addFilePath(str_replace('Sql' . DIRECTORY_SEPARATOR . 'Abstracts', '', __DIR__) . DIRECTORY_SEPARATOR)
            ->loadFile('database');

        $config->merge(
            array_merge(
                [
                    'escapeCharacter'     => '"',
                    'reservedIdentifiers' => ['*'],
                    'likeEscapeStatement' => ' ESCAPE \'%s\' ',
                    'likeEscapeCharacter' => '!',
                    'bindMarker'          => '?',
                ],
                $this->getConfig()
            )
        );

        $this->config = $config;

        $this->debugEnable = (bool)$config->offsetGet('debugEnable');
        $this->transactionEnable = (bool)$config->offsetGet('transEnable');
        $this->database = $config->offsetGet('database');

        $this->connect(
            ($config->offsetExists('persistent')
                ? $this->persistent = $config->persistent
                : true
            )
        );
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::connect
     *
     * Establish the connection.
     *
     * @param bool $persistent
     *
     * @return void
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    final public function connect($persistent = true)
    {
        /* If an established connection is available, then there's
         * no need to connect and select the database.
         *
         * Depending on the database driver, conn_id can be either
         * boolean TRUE, a resource or an object.
         */
        if ($this->handle) {
            return;
        }

        //--------------------------------------------------------------------

        $this->persistent = $persistent;
        $this->connectTimeStart = microtime(true);

        // Connect to the database and set the connection ID
        $this->platformConnectHandler($this->config);

        // No connection resource? Check if there is a failover else throw an error
        if ( ! $this->handle) {
            // Check if there is a failover set
            if ( ! empty($this->config[ 'failover' ]) && is_array($this->config[ 'failover' ])) {
                // Go over all the failovers
                foreach ($this->config[ 'failover' ] as $failover) {

                    // Try to connect
                    $this->platformConnectHandler($failover = new Config($failover));

                    // If a connection is made break the foreach loop
                    if ($this->handle) {
                        $this->config = $failover;
                        break;
                    }
                }
            }

            // We still don't have a connection?
            if ( ! $this->handle) {
                throw new RuntimeException('DB_E_UNABLE_TO_CONNECT', 0, [$this->platform]);
            }
        }

        $this->connectTimeDuration = microtime(true) - $this->connectTimeStart;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::platformConnectHandler
     *
     * Driver dependent way method for open the connection.
     *
     * @param Config $config The connection configuration.
     *
     * @return mixed
     */
    abstract protected function platformConnectHandler(Config $config);

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::isSupported
     *
     * Check if the platform is supported.
     *
     * @return bool
     */
    abstract public function isSupported();

    //--------------------------------------------------------------------

    /**
     * AbstractConnection::getPlatform
     *
     * Get the name of the database platform of this connection.
     *
     * @return string The name of the database platform.
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractConnection::getVersion
     *
     * Get the version of the database platform of this connection.
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject
     */
    public function getPlatformInfo()
    {
        if (isset($this->queriesResultCache[ 'platformInfo' ])) {
            return $this->queriesResultCache[ 'platformInfo' ];
        }

        return $this->queriesResultCache[ 'platformInfo' ] = $this->platformGetPlatformInfoHandler();
    }

    //--------------------------------------------------------------------

    /**
     * AbstractConnection::platformGetPlatformVersionHandler
     *
     * Platform getting version handler.
     *
     * @return \O2System\Spl\DataStructures\SplArrayObject
     */
    abstract protected function platformGetPlatformInfoHandler();

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::reconnect
     *
     * Keep or establish the connection if no queries have been sent for
     * a length of time exceeding the server's idle timeout.
     *
     * @return void
     */
    public function reconnect()
    {
        if (empty($this->handle)) {
            $this->platformConnectHandler($this->config);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::connected
     *
     * Determine if the connection is connected
     *
     * @return bool
     */
    final public function connected()
    {
        return (bool)($this->handle === false
            ? false
            : true);
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::disconnect
     *
     * Disconnect database connection.
     *
     * @return void
     */
    final public function disconnect()
    {
        if ($this->handle) {
            $this->platformDisconnectHandler();
            $this->handle = false;
        }
    }

    //--------------------------------------------------------------------

    /**
     * AbstractConnection::disconnectHandler
     *
     * Driver dependent way method for closing the connection.
     *
     * @return mixed
     */
    abstract protected function platformDisconnectHandler();

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::getConnectionTimeStart
     *
     * Returns the time we started to connect to this database in
     * seconds with microseconds.
     *
     * Used by the Debug Toolbar's timeline.
     *
     * @return float
     */
    final public function getConnectTimeStart()
    {
        return (int)$this->connectTimeStart;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractConnection::getConnectTimeDuration
     *
     * Returns the number of seconds with microseconds that it took
     * to connect to the database.
     *
     * Used by the Debug Toolbar's timeline.
     *
     * @param int $decimals
     *
     * @return mixed
     */
    final public function getConnectTimeDuration($decimals = 6)
    {
        return number_format($this->connectTimeDuration, $decimals);
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::getQueries
     *
     * Returns Queries Collections
     *
     * @return array
     */
    final public function getQueries()
    {
        return $this->queriesCache;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::getQueriesCount
     *
     * Returns the total number of queries that have been performed
     * on this connection.
     *
     * @return int
     */
    final public function getQueriesCount()
    {
        return (int)count($this->queriesCache);
    }

    //--------------------------------------------------------------------

    /**
     * AbstractConnection::getLastQuery
     *
     * Returns the last query's statement object.
     *
     * @return Statement
     */
    public function getLastQuery()
    {
        return end($this->queriesCache);
    }

    //--------------------------------------------------------------------

    /**
     * AbstractConnection::setDatabase
     *
     * Set a specific database table to use.
     *
     * @param string $database Database name.
     *
     * @return static
     */
    public function setDatabase($database)
    {
        $this->database = $database;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractConnection::hasDatabase
     *
     * Check if the database exists or not.
     *
     * @param string $databaseName The database name.
     *
     * @return bool Returns false if database doesn't exists.
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    final public function hasDatabase($databaseName)
    {
        if (empty($this->queriesResultCache[ 'databaseNames' ])) {
            $this->getDatabases();
        }

        return (bool)in_array($databaseName, $this->queriesResultCache[ 'databaseNames' ]);
    }

    //--------------------------------------------------------------------

    /**
     * AbstractConnection::getDatabases
     *
     * Get list of current connection databases.
     *
     * @return array Returns an array.
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    abstract public function getDatabases();

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::setTablePrefix
     *
     * @param string $tablePrefix The database table prefix.
     *
     * @return string
     */
    final public function setTablePrefix($tablePrefix)
    {
        return $this->config[ 'tablePrefix' ] = $tablePrefix;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::hasTable
     *
     * Check if table exists at current connection database.
     *
     * @param $table
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    public function hasTable($table)
    {
        $table = $this->prefixTable($table);

        if (empty($this->queriesResultCache[ 'tableNames' ])) {
            $this->getTables();
        }

        return (bool)in_array($table, $this->queriesResultCache[ 'tableNames' ]);
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::prefixTable
     *
     * @param string $tableName Database table name.
     *
     * @return string Returns prefixed table name.
     */
    final public function prefixTable($tableName)
    {
        $tablePrefix = $this->config[ 'tablePrefix' ];

        if (empty($tablePrefix)) {
            return $tableName;
        }

        return $tablePrefix . str_replace($tablePrefix, '', $tableName);
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::getTables
     *
     * Get list of current database tables.
     *
     * @param bool $prefixLimit If sets TRUE the query will be limit using database table prefix.
     *
     * @return array Returns an array
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    abstract public function getTables($prefixLimit = false);

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::hasColumn
     *
     * Check if table exists at current connection database.
     *
     * @param string $column Database table field name.
     * @param string $table  Database table name.
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    public function hasColumn($column, $table)
    {
        $table = $this->prefixTable($table);

        if (empty($this->queriesResultCache[ 'tableColumns' ][ $table ])) {
            $this->getColumns($table);
        }

        return (bool)isset($this->queriesResultCache[ 'tableColumns' ][ $table ][ $column ]);
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::getColumns
     *
     * @param string $table The database table name.
     *
     * @return array
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    abstract public function getColumns($table);

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::execute
     *
     * Execute Sql statement against database.
     *
     * @param string $sqlStatement The Sql statement.
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    public function execute($sqlStatement)
    {
        if (empty($this->handle)) {
            $this->connect();
        }

        $queryStatement = new Statement();
        $queryStatement->setSqlStatement($sqlStatement);
        $queryStatement->setSqlFinalStatement($sqlStatement);

        $startTime = microtime(true);
        $result = $this->platformExecuteHandler($queryStatement);

        $queryStatement->setDuration($startTime);
        $queryStatement->setAffectedRows($this->getAffectedRows());
        $queryStatement->setLastInsertId($this->getLastInsertId());

        if ( ! array_key_exists($queryStatement->getKey(), $this->queriesCache)) {
            $this->queriesCache[ $queryStatement->getKey() ] = $queryStatement;
        }

        if ($queryStatement->hasErrors()) {
            if ($this->transactionInProgress) {
                $this->transactionStatus = false;
                $this->transactionRollBack();
            }

            $errorMessage = $queryStatement->getLastErrorMessage() .
                "on sql statement: \r\n" . $sqlStatement . "\r\n";

            if ($this->debugEnable) {
                throw new RuntimeException($errorMessage, $queryStatement->getLastErrorCode());
            } else {
                $this->addError($errorMessage, $queryStatement->getLastErrorCode());
            }

            return false;
        }

        if ($this->transactionInProgress) {
            $this->transactionStatus = true;
        }

        return (bool)$result;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::executeHandler
     *
     * Driver dependent way method for execute the Sql statement.
     *
     * @param Statement $statement Query object.
     *
     * @return bool
     */
    abstract protected function platformExecuteHandler(Statement &$statement);

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::getAffectedRows
     *
     * Get the total number of affected rows from the last query execution.
     *
     * @return int  Returns total number of affected rows
     */
    abstract public function getAffectedRows();

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::getLastInsertId
     *
     * Get last insert id from the last insert query execution.
     *
     * @return int  Returns total number of affected rows
     */
    abstract public function getLastInsertId();

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::transactionRollBack
     *
     * RollBack a transaction.
     *
     * @return bool
     */
    public function transactionRollBack()
    {
        if ($this->transactionInProgress) {
            return $this->platformTransactionRollBackHandler();
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::platformTransactionRollBackHandler
     *
     * Platform rolling back a transaction handler.
     *
     * @return bool
     */
    abstract protected function platformTransactionRollBackHandler();

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::query
     *
     * @param string $sqlStatement
     * @param array  $binds
     *
     * @return bool|\O2System\Database\DataObjects\Result Returns boolean if the query is contains writing syntax
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function query($sqlStatement, array $binds = [])
    {
        if (empty($this->handle)) {
            $this->connect($this->persistent);
        }

        $result = false;
        $queryStatement = new Statement();

        $queryStatement->setSqlStatement($sqlStatement, $binds);
        $queryStatement->setSqlFinalStatement($this->compileSqlBinds($sqlStatement, $binds));

        if ( ! empty($this->swapTablePrefix) AND ! empty($this->config->tablePrefix)) {
            $queryStatement->swapTablePrefix($this->config->tablePrefix, $this->swapTablePrefix);
        }

        $startTime = microtime(true);
        $result = new Result([]);

        // Run the query for real
        if ($this->disableQueryExecution === false) {
            if ($queryStatement->isWriteStatement()) {
                $result = $this->platformExecuteHandler($queryStatement);

                if ($this->transactionInProgress) {
                    $this->transactionStatus = $result;
                }
            } else {
                if (class_exists('O2System\Framework', false) &&
                    $this->cacheEnable === true
                ) {
                    $cacheKey = $queryStatement->getKey();
                    $cacheHandle = cache()->getItemPool('default');

                    if (cache()->hasItemPool('database')) {
                        $cacheHandle = cache()->getItemPool('output');
                    }

                    if ($cacheHandle instanceof \Psr\Cache\CacheItemPoolInterface) {
                        if ($cacheHandle->hasItem($cacheKey)) {
                            $rows = $cacheHandle->getItem($cacheKey)->get();
                        } else {
                            $rows = $this->platformQueryHandler($queryStatement);
                            $cacheHandle->save(new Item($cacheKey, $rows));
                        }
                    }

                    if ( ! isset($rows)) {
                        $rows = $this->platformQueryHandler($queryStatement);
                    }

                    $this->cache($this->config->cacheEnable);

                } else {
                    $rows = $this->platformQueryHandler($queryStatement);
                }

                $result = new Result($rows);
            }
        }

        $queryStatement->setDuration($startTime);

        if ( ! array_key_exists($queryStatement->getKey(), $this->queriesCache)) {
            $this->queriesCache[ $queryStatement->getKey() ] = $queryStatement;
        } else {
            $this->queriesCache[ $queryStatement->getKey() ]->addHit(1);
        }

        if ($queryStatement->hasErrors()) {
            $errorMessage = $queryStatement->getLastErrorMessage() .
                "on sql statement: \r\n" . $sqlStatement . "\r\n";

            if ($this->debugEnable) {
                throw new RuntimeException($errorMessage, $queryStatement->getLastErrorCode());
            } else {
                $this->addError($errorMessage, $queryStatement->getLastErrorMessage());
            }

            if ($this->transactionInProgress) {
                $this->transactionStatus = false;
                $this->transactionRollBack();
                $this->transactionInProgress = false;
            }

            return $result;
        }

        if ($this->transactionInProgress) {
            $this->transactionStatus = true;
        }

        return $result;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::compileSqlBinds
     *
     * Escapes and inserts any binds into the final Sql statement object.
     *
     * @return string
     */
    public function compileSqlBinds($sqlStatement, array $binds = [])
    {
        $hasSqlBinders = strpos($sqlStatement, ':') !== false;

        if (empty($binds) || empty($this->config[ 'bindMarker' ]) ||
            (strpos($sqlStatement, $this->config[ 'bindMarker' ]) === false &&
                $hasSqlBinders === false)
        ) {
            return $sqlStatement;
        }

        if ( ! is_array($binds)) {
            $sqlBinds = [$binds];
            $bindCount = 1;
        } else {
            $sqlBinds = $binds;
            $bindCount = count($sqlBinds);
        }

        // Reverse the binds so that duplicate named binds
        // will be processed prior to the original binds.
        if ( ! is_numeric(key(array_slice($sqlBinds, 0, 1)))) {
            $sqlBinds = array_reverse($sqlBinds);
        }

        // We'll need marker length later
        $markerLength = strlen($this->config[ 'bindMarker' ]);

        if ($hasSqlBinders) {
            $sqlStatement = $this->replaceNamedBinds($sqlStatement, $sqlBinds);
        } else {
            $sqlStatement = $this->replaceSimpleBinds($sqlStatement, $sqlBinds, $bindCount, $markerLength);
        }

        return $sqlStatement . ';';
    }

    //--------------------------------------------------------------------

    /**
     * AbstractConnection::replaceNamedBinds
     *
     * Match bindings.
     *
     * @param string $sqlStatement
     * @param array  $sqlBinds
     *
     * @return string
     */
    protected function replaceNamedBinds($sqlStatement, array $sqlBinds)
    {
        foreach ($sqlBinds as $bindSearch => $bindReplace) {
            $escapedValue = $this->escape($bindReplace);

            // In order to correctly handle backlashes in saved strings
            // we will need to preg_quote, so remove the wrapping escape characters
            // otherwise it will get escaped.
            if (is_array($bindReplace)) {
                foreach ($bindReplace as &$bindReplaceItem) {
                    $bindReplaceItem = preg_quote($bindReplaceItem);
                }

                $escapedValue = implode(',', $escapedValue);
            } elseif (strpos($bindReplace, ' AND ') !== false) {
                $escapedValue = $bindReplace;
            } else {
                $escapedValue = preg_quote(trim($escapedValue, $this->config[ 'escapeCharacter' ]));
            }

            if (preg_match("/\(.+?\)/", $bindSearch)) {
                $bindSearch = str_replace('(', '\(', str_replace(')', '\)', $bindSearch));
            }

            $sqlStatement = preg_replace('/:' . $bindSearch . '(?!\w)/', $escapedValue, $sqlStatement);
        }

        return $sqlStatement;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractConnection::escape
     *
     * Escape string
     *
     * @param $string
     *
     * @return int|string
     */
    final public function escape($string)
    {
        if (is_array($string)) {
            $string = array_map([&$this, 'escape'], $string);

            return $string;
        } else {
            if (is_string($string) OR (is_object($string) && method_exists($string, '__toString'))) {
                return "'" . $this->escapeString($string) . "'";
            } else {
                if (is_bool($string)) {
                    return ($string === false)
                        ? 0
                        : 1;
                } else {
                    if ($string === null) {
                        return 'NULL';
                    }
                }
            }
        }

        return $string;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractConnection::escapeString
     *
     * Escape String
     *
     * @param string|\string[] $string
     * @param bool             $like
     *
     * @return array|string|\string[]
     */
    final public function escapeString($string, $like = false)
    {
        if (is_array($string)) {
            foreach ($string as $key => $value) {
                $string[ $key ] = $this->escapeString($value, $like);
            }

            return $string;
        }

        $string = $this->platformEscapeStringHandler($string);

        // escape LIKE condition wildcards
        if ($like === true) {
            $string = str_replace(
                [$this->config[ 'likeEscapeCharacter' ], '%', '_'],
                [
                    $this->config[ 'likeEscapeCharacter' ] . $this->config[ 'likeEscapeCharacter' ],
                    $this->config[ 'likeEscapeCharacter' ] . '%',
                    $this->config[ 'likeEscapeCharacter' ] . '_',
                ],
                $string
            );
        }

        // fixed escaping string bugs !_
        $string = str_replace('!_', '_', $string);

        return $string;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::platformEscapeStringHandler
     *
     * Platform escape string handler.
     *
     * @param string $string
     *
     * @return string
     */
    protected function platformEscapeStringHandler($string)
    {
        return str_replace("'", "''", remove_invisible_characters($string));
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::replaceSimpleBinds
     *
     * Match bindings
     *
     * @param string $sqlStatement
     * @param array  $sqlBinds
     * @param int    $bindCount
     * @param int    $markerLength
     *
     * @return string
     */
    protected function replaceSimpleBinds($sqlStatement, array $sqlBinds, $bindCount, $markerLength)
    {
        // Make sure not to replace a chunk inside a string that happens to match the bind marker
        if ($chunk = preg_match_all("/'[^']*'/i", $sqlStatement, $matches)) {
            $chunk = preg_match_all(
                '/' . preg_quote($this->config[ 'bindMarker' ], '/') . '/i',
                str_replace(
                    $matches[ 0 ],
                    str_replace($this->config[ 'bindMarker' ], str_repeat(' ', $markerLength), $matches[ 0 ]),
                    $sqlStatement,
                    $chunk
                ),
                $matches,
                PREG_OFFSET_CAPTURE
            );

            // Bind values' count must match the count of markers in the query
            if ($bindCount !== $chunk) {
                return $sqlStatement;
            }
        } // Number of binds must match bindMarkers in the string.
        else {
            if (($chunk = preg_match_all(
                    '/' . preg_quote($this->config[ 'bindMarker' ], '/') . '/i',
                    $sqlStatement,
                    $matches,
                    PREG_OFFSET_CAPTURE
                )) !== $bindCount
            ) {
                return $sqlStatement;
            }
        }

        do {
            $chunk--;
            $escapedValue = $this->escape($sqlBinds[ $chunk ]);
            if (is_array($escapedValue)) {
                $escapedValue = '(' . implode(',', $escapedValue) . ')';
            }
            $sqlStatement = substr_replace($sqlStatement, $escapedValue, $matches[ 0 ][ $chunk ][ 1 ], $markerLength);
        } while ($chunk !== 0);

        return $sqlStatement;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractConnection::platformQueryHandler
     *
     * Driver dependent way method for execute the Sql statement.
     *
     * @param Statement $statement Query object.
     *
     * @return array
     */
    abstract protected function platformQueryHandler(Statement &$statement);

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::cache
     *
     * @param  boolean $mode
     *
     * @return static
     */
    public function cache($mode = true)
    {
        $this->cacheEnable = (bool)$mode;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractConnection::getTransactionStatus
     *
     * Get transaction status.
     *
     * @return bool
     */
    public function getTransactionStatus()
    {
        return (bool)$this->transactionStatus;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractConnection::transactionSuccess
     *
     * Get transaction status.
     *
     * @return bool
     */
    public function transactionSuccess()
    {
        return $this->getTransactionStatus();
    }

    //--------------------------------------------------------------------

    /**
     * AbstractConnection::transactionBegin
     *
     * Starting a transaction.
     *
     * @return bool
     */
    public function transactionBegin()
    {
        $this->transactionInProgress = false;

        /**
         * checks if the transaction already started
         * then we only increment the transaction depth.
         */
        if ($this->transactionDepth > 0) {
            $this->transactionInProgress = true;
            $this->transactionDepth++;

            return true;
        }

        if ($this->platformTransactionBeginHandler()) {
            $this->transactionInProgress = true;
            $this->transactionDepth++;

            return true;
        }

        return $this->transactionInProgress;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::platformTransactionBeginHandler
     *
     * Platform beginning a transaction handler.
     *
     * @return bool
     */
    abstract protected function platformTransactionBeginHandler();

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::transactionCommit
     *
     * Commit a transaction.
     *
     * @return bool
     */
    public function transactionCommit()
    {
        if ($this->transactionInProgress) {
            if ($this->transactionStatus) {
                $this->platformTransactionCommitHandler();

                return true;
            }
        }

        return $this->transactionRollBack();
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::platformTransactionCommitHandler
     *
     * Platform committing a transaction handler.
     *
     * @return bool
     */
    abstract protected function platformTransactionCommitHandler();

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::likeString
     *
     * Escape Like String
     *
     * @param $string
     *
     * @return array|string|\string[]
     */
    final public function escapeLikeString($string)
    {
        return $this->escapeString($string, true);
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::protectIdentifiers
     *
     * This function is used extensively by the Query Builder class, and by
     * a couple functions in this class.
     * It takes a column or table name (optionally with an alias) and inserts
     * the table prefix onto it. Some logic is necessary in order to deal with
     * column names that include the path. Consider a query like this:
     *
     * SELECT hostname.database.table.column AS c FROM hostname.database.table
     *
     * Or a query with aliasing:
     *
     * SELECT m.member_id, m.member_name FROM members AS m
     *
     * Since the column name can include up to four segments (host, DB, table, column)
     * or also have an alias prefix, we need to do a bit of work to figure this out and
     * insert the table prefix (if it exists) in the proper position, and escape only
     * the correct identifiers.
     *
     * @param    string|array
     * @param    bool
     * @param    mixed
     * @param    bool
     *
     * @return  mixed
     */
    final public function protectIdentifiers(
        $item,
        $prefixSingle = false,
        $protectIdentifiers = null,
        $fieldExists = true
    ) {
        if ( ! is_bool($protectIdentifiers)) {
            $protectIdentifiers = $this->protectIdentifiers;
        }

        if (is_array($item)) {
            $escapedArray = [];
            foreach ($item as $key => $value) {
                $escapedArray[ $this->protectIdentifiers($key) ] = $this->protectIdentifiers(
                    $value,
                    $prefixSingle,
                    $protectIdentifiers,
                    $fieldExists
                );
            }

            return $escapedArray;
        }

        // This is basically a bug fix for queries that use MAX, MIN, etc.
        // If a parenthesis is found we know that we do not need to
        // escape the data or add a prefix.
        //
        // Added exception for single quotes as well, we don't want to alter
        // literal strings.
        if (strcspn($item, "()'") !== strlen($item)) {
            return $item;
        }

        // Convert tabs or multiple spaces into single spaces
        $item = preg_replace('/\s+/', ' ', trim($item));

        // If the item has an alias declaration we remove it and set it aside.
        // Note: strripos() is used in order to support spaces in table names
        if ($offset = strripos($item, ' AS ')) {
            $alias = ($protectIdentifiers)
                ? substr($item, $offset, 4) . $this->escapeIdentifiers(substr($item, $offset + 4))
                : substr($item, $offset);
            $item = substr($item, 0, $offset);
        } elseif ($offset = strrpos($item, ' ')) {
            $alias = ($protectIdentifiers)
                ? ' ' . $this->escapeIdentifiers(substr($item, $offset + 1))
                : substr($item, $offset);
            $item = substr($item, 0, $offset);
        } else {
            $alias = '';
        }

        // Break the string apart if it contains periods, then insert the table prefix
        // in the correct location, assuming the period doesn't indicate that we're dealing
        // with an alias. While we're at it, we will escape the components
        if (strpos($item, '.') !== false) {
            $parts = explode('.', $item);

            $aliasedTables = [];

            if ($this->queryBuilder instanceof AbstractQueryBuilder) {
                $aliasedTables = $this->queryBuilder->getAliasedTables();
            }

            // Does the first segment of the exploded item match
            // one of the aliases previously identified? If so,
            // we have nothing more to do other than escape the item
            //
            // NOTE: The ! empty() condition prevents this method
            //       from breaking when Query Builder isn't enabled.
            if ( ! empty($aliasedTables) AND in_array($parts[ 0 ], $aliasedTables)) {
                if ($protectIdentifiers === true) {
                    foreach ($parts as $key => $val) {
                        if ( ! in_array($val, $this->config[ 'reservedIdentifiers' ])) {
                            $parts[ $key ] = $this->escapeIdentifiers($val);
                        }
                    }

                    $item = implode('.', $parts);
                }

                return $item . $alias;
            }

            // Is there a table prefix defined in the config file? If not, no need to do anything
            if ($this->config->tablePrefix !== '') {
                // We now add the table prefix based on some logic.
                // Do we have 4 segments (hostname.database.table.column)?
                // If so, we add the table prefix to the column name in the 3rd segment.
                if (isset($parts[ 3 ])) {
                    $i = 2;
                }
                // Do we have 3 segments (database.table.column)?
                // If so, we add the table prefix to the column name in 2nd position
                elseif (isset($parts[ 2 ])) {
                    $i = 1;
                }
                // Do we have 2 segments (table.column)?
                // If so, we add the table prefix to the column name in 1st segment
                else {
                    $i = 0;
                }

                // This flag is set when the supplied $item does not contain a field name.
                // This can happen when this function is being called from a JOIN.
                if ($fieldExists === false) {
                    $i++;
                }

                // Verify table prefix and replace if necessary
                if ($this->swapTablePrefix !== '' && strpos($parts[ $i ], $this->swapTablePrefix) === 0) {
                    $parts[ $i ] = preg_replace(
                        '/^' . $this->swapTablePrefix . '(\S+?)/',
                        $this->config->tablePrefix . '\\1',
                        $parts[ $i ]
                    );
                } // We only add the table prefix if it does not already exist
                elseif (strpos($parts[ $i ], $this->config->tablePrefix) !== 0) {
                    $parts[ $i ] = $this->config->tablePrefix . $parts[ $i ];
                }

                // Put the parts back together
                $item = implode('.', $parts);
            }

            if ($protectIdentifiers === true) {
                $item = $this->escapeIdentifiers($item);
            }

            return $item . $alias;
        }

        // In some cases, especially 'from', we end up running through
        // protect_identifiers twice. This algorithm won't work when
        // it contains the escapeChar so strip it out.
        $item = trim($item, $this->config[ 'escapeCharacter' ]);

        // Is there a table prefix? If not, no need to insert it
        if ($this->config->tablePrefix !== '') {
            // Verify table prefix and replace if necessary
            if ($this->swapTablePrefix !== '' && strpos($item, $this->swapTablePrefix) === 0) {
                $item = preg_replace(
                    '/^' . $this->swapTablePrefix . '(\S+?)/',
                    $this->config->tablePrefix . '\\1',
                    $item
                );
            } // Do we prefix an item with no segments?
            elseif ($prefixSingle === true && strpos($item, $this->config->tablePrefix) !== 0) {
                $item = $this->config->tablePrefix . $item;
            }
        }

        if ($protectIdentifiers === true && ! in_array($item, $this->config[ 'reservedIdentifiers' ])) {
            $item = $this->escapeIdentifiers($item);
        }

        return $item . $alias;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::escapeIdentifiers
     *
     * Escape the Sql Identifiers
     *
     * This function escapes column and table names
     *
     * @param    mixed
     *
     * @return    mixed
     */
    final public function escapeIdentifiers($item)
    {
        if ($this->config[ 'escapeCharacter' ] === '' OR empty($item) OR in_array(
                $item,
                $this->config[ 'reservedIdentifiers' ]
            )
        ) {
            return $item;
        } elseif (is_array($item)) {
            foreach ($item as $key => $value) {
                $item[ $key ] = $this->escapeIdentifiers($value);
            }

            return $item;
        } // Avoid breaking functions and literal values inside queries
        elseif (ctype_digit(
                $item
            ) OR $item[ 0 ] === "'" OR ($this->config[ 'escapeCharacter' ] !== '"' && $item[ 0 ] === '"') OR
            strpos($item, '(') !== false
        ) {
            return $item;
        }

        static $pregEscapeCharacters = [];

        if (empty($pregEscapeCharacters)) {
            if (is_array($this->config[ 'escapeCharacter' ])) {
                $pregEscapeCharacters = [
                    preg_quote($this->config[ 'escapeCharacter' ][ 0 ], '/'),
                    preg_quote($this->config[ 'escapeCharacter' ][ 1 ], '/'),
                    $this->config[ 'escapeCharacter' ][ 0 ],
                    $this->config[ 'escapeCharacter' ][ 1 ],
                ];
            } else {
                $pregEscapeCharacters[ 0 ]
                    = $pregEscapeCharacters[ 1 ] = preg_quote($this->config[ 'escapeCharacter' ], '/');
                $pregEscapeCharacters[ 2 ] = $pregEscapeCharacters[ 3 ] = $this->config[ 'escapeCharacter' ];
            }
        }

        foreach ($this->config[ 'reservedIdentifiers' ] as $id) {
            if (strpos($item, '.' . $id) !== false) {
                return preg_replace(
                    '/'
                    . $pregEscapeCharacters[ 0 ]
                    . '?([^'
                    . $pregEscapeCharacters[ 1 ]
                    . '\.]+)'
                    . $pregEscapeCharacters[ 1 ]
                    . '?\./i',
                    $pregEscapeCharacters[ 2 ] . '$1' . $pregEscapeCharacters[ 3 ] . '.',
                    $item
                );
            }
        }

        return preg_replace(
            '/'
            . $pregEscapeCharacters[ 0 ]
            . '?([^'
            . $pregEscapeCharacters[ 1 ]
            . '\.]+)'
            . $pregEscapeCharacters[ 1 ]
            . '?(\.)?/i',
            $pregEscapeCharacters[ 2 ] . '$1' . $pregEscapeCharacters[ 3 ] . '$2',
            $item
        );
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::table
     *
     * Get connection query builder.
     *
     * @return AbstractQueryBuilder
     */
    public function table($tableName)
    {
        return $this->getQueryBuilder()->from($tableName);
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::getQueryBuilder
     *
     * Get connection query builder.
     *
     * @return AbstractQueryBuilder
     */
    public function getQueryBuilder()
    {
        if ( ! $this->queryBuilder instanceof AbstractQueryBuilder) {
            $className = str_replace('Connection', 'QueryBuilder', get_called_class());

            if (class_exists($className)) {
                $this->queryBuilder = new $className($this);
            }
        }

        return $this->queryBuilder;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::getForge
     *
     * Get connection forge.
     *
     * @return AbstractForge
     */
    public function getForge()
    {
        if ( ! $this->forge instanceof AbstractForge) {
            $className = str_replace('Connection', 'Forge', get_called_class());

            if (class_exists($className)) {
                $this->forge = new $className($this);
            }
        }

        return $this->forge;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::prepareSqlStatement
     *
     * Platform preparing a Sql statement.
     *
     * @param string $sqlStatement Sql Statement to be prepared.
     * @param array  $options      Preparing Sql statement options.
     *
     * @return string
     */
    abstract protected function platformPrepareSqlStatement($sqlStatement, array $options = []);
}
