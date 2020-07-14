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

use O2System\Database\DataObjects\Result;
use O2System\Database\DataStructures\Config;
use O2System\Database\NoSql\DataStructures\Query;
use O2System\Spl\Exceptions\RuntimeException;
use O2System\Spl\Traits\Collectors\ConfigCollectorTrait;

/**
 * Class AbstractConnection
 *
 * @package O2System\Database\Abstracts
 */
abstract class AbstractConnection
{
    use ConfigCollectorTrait;

    /**
     * AbstractConnection::$debugEnabled
     *
     * Connection debug mode flag.
     *
     * @var bool
     */
    public $debugEnabled = true;

    /**
     * AbstractConnection::$database
     *
     * Connection database name.
     *
     * @var string
     */
    public $database;

    /**
     * AbstractConnection::$isProtectIdentifiers
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
     * AbstractConnection::$handle
     *
     * Connection handle
     *
     * @var mixed
     */
    public $handle;
    /**
     * AbstractConnection::$platform
     *
     * Database driver platform name.
     *
     * @var string
     */
    protected $platform;
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

    protected $affectedDocuments;
    protected $lastInsertId;
    protected $lastUpsertedIds = [];

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
            ->addFilePath(str_replace('NoSql' . DIRECTORY_SEPARATOR . 'Abstracts', '', __DIR__) . DIRECTORY_SEPARATOR)
            ->loadFile('database');

        $config->merge(
            array_merge(
                [
                    'escapeCharacter'     => '"',
                    'reservedIdentifiers' => ['*'],
                    'likeEscapeStatement' => ' ESCAPE \'%s\' ',
                    'likeEscapeCharacter' => '!',
                ],
                $this->getConfig()
            )
        );

        $this->config = $config;

        $this->debugEnabled = $config->offsetGet('debugEnabled');
        $this->transactionEnabled = $config->offsetGet('transEnabled');
        $this->database = $config->offsetGet('database');

        $this->connect();
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::connect
     *
     * Establish the connection.
     *
     * @return void
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    final public function connect()
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

    //--------------------------------------------------------------------

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

    // ------------------------------------------------------------------------

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
     * @return \O2System\Database\Sql\DataStructures\Query\Statement
     */
    final public function getLatestQuery()
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
     * AbstractConnection::getDatabaseList
     *
     * Get list of current connection databases.
     *
     * @return array Returns an array.
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    abstract public function getDatabases();

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::hasCollection
     *
     * Check if collection exists at current connection database.
     *
     * @param $collection
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    public function hasCollection($collection)
    {
        if (empty($this->queriesResultCache[ 'collectionNames' ])) {
            $this->getCollections();
        }

        return (bool)in_array($collection, $this->queriesResultCache[ 'collectionNames' ]);
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::getCollections
     *
     * Get list of current database collections.
     *
     * @return array Returns an array
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    abstract public function getCollections();

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::hasCollection
     *
     * Check if collection exists at current connection database.
     *
     * @param $collection
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    public function hasKey($key, $collection)
    {
        if (empty($this->queriesResultCache[ 'collectionKeys' ][ $collection ])) {
            $this->getKeys($collection);
        }

        return (bool)in_array($key, $this->queriesResultCache[ 'collectionKeys' ][ $collection ]);
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::getKeys
     *
     * Get list of current database collections keys.
     *
     * @param string $collection The database table name.
     *
     * @return array Returns an array
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    abstract public function getKeys($collection);

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::hasIndex
     *
     * Check if table exists at current connection database.
     *
     * @param string $index      Database table field name.
     * @param string $collection Database table name.
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    public function hasIndex($index, $collection)
    {
        if (empty($this->queriesResultCache[ 'collectionIndexes' ][ $collection ])) {
            $this->getIndexes($collection);
        }

        return (bool)in_array($index, $this->queriesResultCache[ 'collectionIndexes' ][ $collection ]);
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::getIndexes
     *
     * @param string $collection The database table name.
     *
     * @return array
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    abstract public function getIndexes($collection);

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::execute
     *
     * Execute Sql statement against database.
     *
     * @param Query\BuilderCache $builderCache
     *
     * @return mixed
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    public function execute(Query\BuilderCache $builderCache, array $options = [])
    {
        // Reconnect the connection in case isn't connected yet.
        $this->reconnect();

        $statement = new Query\Statement($builderCache);

        if ($statement->getCollection()) {
            $startTime = microtime(true);
            $result = $this->platformExecuteHandler($statement, $options);

            $statement->setDuration($startTime);
            $statement->setAffectedDocuments($this->getAffectedDocuments());
            $statement->setLastInsertId($this->getLastInsertId());

            $this->queriesCache[] = $statement;

            if ($statement->hasErrors()) {
                if ($this->debugEnabled) {
                    throw new RuntimeException($statement->getLastErrorMessage(),
                        $statement->getLastErrorCode());
                }
            }

            return (bool)$result;
        }

        return false;
    }

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
     * AbstractConnection::executeHandler
     *
     * Driver dependent way method for execute the Sql statement.
     *
     * @param Query\Statement $statement Query statement object.
     *
     * @return bool
     */
    abstract protected function platformExecuteHandler(Query\Statement &$statement, array $options = []);

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::getAffectedDocuments
     *
     * Get the total number of affected rows from the last query execution.
     *
     * @return int  Returns total number of affected rows
     */
    abstract public function getAffectedDocuments();

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
     * AbstractConnection::query
     *
     * @param Query\BuilderCache $builderCache
     *
     * @return bool|\O2System\Database\DataObjects\Result Returns boolean if the query is contains writing syntax
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    public function query(Query\BuilderCache $builderCache)
    {
        // Reconnect the connection in case isn't connected yet.
        $this->reconnect();

        $result = false;
        $statement = new Query\Statement($builderCache);

        $startTime = microtime(true);

        // Run the query for real
        if ($this->disableQueryExecution === false) {
            $rows = $this->platformQueryHandler($statement);
            $result = new Result($rows);

            if ($this->transactionInProgress) {
                $this->transactionStatus = ($statement->hasErrors() ? false : true);
            }
        }

        $statement->setDuration($startTime);

        $this->queriesCache[] = $statement;

        if ($statement->hasErrors()) {
            if ($this->debugEnabled) {
                throw new RuntimeException($statement->getLastErrorMessage(),
                    $statement->getLastErrorCode());
            }

            if ($this->transactionInProgress) {
                $this->transactionStatus = false;
                $this->transactionInProgress = false;
            }

            return false;
        }

        return $result;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::platformQueryHandler
     *
     * Driver dependent way method for execute the Sql statement.
     *
     * @param Query\Statement $statement Query statement object.
     *
     * @return array
     */
    abstract protected function platformQueryHandler(Query\Statement &$statement);

    // ------------------------------------------------------------------------

    /**
     * AbstractConnection::collection
     *
     * @param string $collectionName
     *
     * @return AbstractQueryBuilder
     */
    public function collection($collectionName)
    {
        return $this->getQueryBuilder()->collection($collectionName);
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
}
