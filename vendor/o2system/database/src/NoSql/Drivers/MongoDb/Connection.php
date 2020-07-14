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

namespace O2System\Database\NoSql\Drivers\MongoDb;

// ------------------------------------------------------------------------

use O2System\Database\DataStructures\Config;
use O2System\Database\NoSql\Abstracts\AbstractConnection;
use O2System\Database\NoSql\DataStructures\Query\Statement;
use O2System\Spl\DataStructures\SplArrayObject;
use O2System\Spl\Exceptions\RuntimeException;

/**
 * Class Connection
 *
 * @package O2System\Database\NoSql\Drivers\MongoDb
 */
class Connection extends AbstractConnection
{
    /**
     * Connection::$handle
     *
     * MongoDb Connection Instance.
     *
     * @var \MongoDb\Driver\Manager
     */
    public $handle;
    /**
     * Connection::$server
     *
     * MongoDb Server Instance.
     *
     * @var \MongoDb\Driver\Server
     */
    public $server;
    /**
     * Connection::$platform
     *
     * Database driver platform name.
     *
     * @var string
     */
    protected $platform = 'MongoDb';

    // ------------------------------------------------------------------------

    /**
     * Connection::isSupported
     *
     * Check if the platform is supported.
     *
     * @return bool
     */
    public function isSupported()
    {
        return extension_loaded('mongoDb');
    }

    // ------------------------------------------------------------------------

    /**
     * Connection::setDatabase
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

    // ------------------------------------------------------------------------

    /**
     * Connection::getDatabases
     *
     * Get list of current connection databases.
     *
     * @return array
     */
    public function getDatabases()
    {
        $cursor = $this->server->executeCommand('admin', new \MongoDb\Driver\Command(['listDatabases' => 1]));

        $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);
        $result = current($cursor->toArray());

        $this->queriesResultCache[ 'databaseNames' ] = [];

        if ( ! empty($result[ 'databases' ])) {
            foreach ($result[ 'databases' ] as $database) {
                if ( ! in_array($database[ 'name' ], ['admin', 'local'])) {
                    $this->queriesResultCache[ 'databaseNames' ][] = $database[ 'name' ];
                }
            }
        }

        return $this->queriesResultCache[ 'databaseNames' ];
    }

    // ------------------------------------------------------------------------

    /**
     * Connection::getCollections
     *
     * Get list of current database collections.
     *
     * @return array Returns an array
     */
    public function getCollections()
    {
        $cursor = $this->server->executeCommand($this->database,
            new \MongoDb\Driver\Command(['listCollections' => 1]));
        $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);

        $result = new \IteratorIterator($cursor);

        $this->queriesResultCache[ 'collectionNames' ] = [];

        foreach ($result as $collection) {
            if ($collection[ 'type' ] === 'collection') {
                $this->queriesResultCache[ 'collectionNames' ][] = $collection[ 'name' ];
            }
        }

        return $this->queriesResultCache[ 'collectionNames' ];
    }

    // ------------------------------------------------------------------------

    /**
     * Connection::getKeys
     *
     * @param string $collection The database collection name.
     *
     * @return array
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    public function getKeys($collection)
    {
        $cursor = $this->server->executeQuery($this->database . '.' . $collection,
            new \MongoDb\Driver\Query([], ['limit' => 1]));

        $result = current($cursor->toArray());

        $this->queriesResultCache[ 'collectionKeys' ][ $collection ] = [];

        foreach (get_object_vars($result) as $key => $value) {
            $this->queriesResultCache[ 'collectionKeys' ][ $collection ][] = $key;
        }

        return $this->queriesResultCache[ 'collectionKeys' ][ $collection ];
    }

    // ------------------------------------------------------------------------

    /**
     * Connection::getIndexes
     *
     * @param string $collection The database collection name.
     *
     * @return array
     * @throws \O2System\Spl\Exceptions\RuntimeException
     */
    public function getIndexes($collection)
    {
        $cursor = $this->server->executeCommand($this->database,
            new \MongoDb\Driver\Command(['listIndexes' => $collection]));
        $cursor->setTypeMap(['root' => 'array', 'document' => 'array']);

        $result = new \IteratorIterator($cursor);

        $this->queriesResultCache[ 'collectionIndexes' ][ $collection ] = [];

        foreach ($result as $index) {
            $this->queriesResultCache[ 'collectionIndexes' ][ $collection ][] = $index[ 'name' ];
        }

        return $this->queriesResultCache[ 'collectionIndexes' ][ $collection ];
    }

    // ------------------------------------------------------------------------

    /**
     * Connection::getAffectedDocuments
     *
     * Get the total number of affected rows from the last query execution.
     *
     * @return int  Returns total number of affected rows
     */
    public function getAffectedDocuments()
    {
        return $this->affectedDocuments;
    }

    // ------------------------------------------------------------------------

    /**
     * Connection::getLastInsertId
     *
     * Get last insert id from the last insert query execution.
     *
     * @return int  Returns total number of affected rows
     */
    public function getLastInsertId()
    {
        return $this->lastInsertId;
    }

    //--------------------------------------------------------------------

    /**
     * Connection::platformGetPlatformVersionHandler
     *
     * Platform getting version handler.
     *
     * @return SplArrayObject
     */
    protected function platformGetPlatformInfoHandler()
    {
        $metadata = $this->server->getInfo();
        $metadata[ 'latency' ] = $this->server->getLatency();
        $metadata[ 'type' ] = $this->server->getType();
        $metadata[ 'tags' ] = $this->server->getTags();

        return new SplArrayObject([
            'name'     => $this->getPlatform(),
            'host'     => $this->server->getHost(),
            'port'     => $this->server->getPort(),
            'metadata' => $metadata,
        ]);
    }

    // ------------------------------------------------------------------------

    /**
     * Connection::platformConnectHandler
     *
     * Establish the connection.
     *
     * @param Config $config
     *
     * @return void
     * @throws RuntimeException
     */
    protected function platformConnectHandler(Config $config)
    {
        $hostname = empty($config->hostname) ? 'localhost' : $config->hostname;
        $port = empty($config->port) ? 27071 : $config->port;
        $this->database = $config->database;

        $this->handle = new \MongoDb\Driver\Manager('mongoDb://' . $hostname . ':' . $port);
        $this->server = $this->handle->selectServer(new \MongoDb\Driver\ReadPreference(\MongoDb\Driver\ReadPreference::RP_PRIMARY));
    }

    // ------------------------------------------------------------------------

    /**
     * Connection::platformDisconnectHandler
     *
     * Driver dependent way method for closing the connection.
     *
     * @return mixed
     */
    protected function platformDisconnectHandler()
    {
        $this->server = null;
    }

    // ------------------------------------------------------------------------

    /**
     * Connection::platformExecuteHandler
     *
     * Driver dependent way method for execute the Sql statement.
     *
     * @param Statement $statement Query object.
     *
     * @return bool
     */
    protected function platformExecuteHandler(Statement &$statement, array $options = [])
    {
        $this->parseQueryStatement($statement);

        if (isset($options[ 'method' ])) {

            $method = $options[ 'method' ];
            unset($options[ 'method' ]);

            $options = array_merge(['safe' => true, 'ordered' => true], $options);
            $bulk = new \MongoDb\Driver\BulkWrite($options);
            $documents = $statement->getDocument();

            if (is_numeric(key($documents))) { // batch process
                foreach ($documents as $document) {
                    switch ($method) {
                        case 'insert':
                            $this->lastInsertId = $bulk->insert($document);
                            break;
                        case 'update':
                            $this->lastInsertId = $bulk->update($statement->getFilter(),
                                $document, $statement->getOptions());
                            break;
                        case 'delete':
                            $this->lastInsertId = $bulk->delete($statement->getFilter(),
                                $statement->getOptions());
                            break;
                    }
                }
            } else {
                switch ($method) {
                    case 'insert':
                        $this->lastInsertId = $bulk->insert($documents);
                        break;
                    case 'update':
                        $this->lastInsertId = $bulk->update($statement->getFilter(),
                            $documents, $statement->getOptions());
                        break;
                    case 'delete':
                        $this->lastInsertId = $bulk->delete($statement->getFilter(),
                            $statement->getOptions());
                        break;
                }
            }

            try {
                $result = $this->handle->executeBulkWrite($this->database . '.' . $statement->getCollection(),
                    $bulk);

                $this->lastUpsertedIds = $result->getUpsertedIds();

                switch ($method) {
                    case 'insert':
                        $this->affectedDocuments = $result->getInsertedCount();
                        break;
                    case 'update':
                        $this->affectedDocuments = $result->getModifiedCount();
                        break;
                    case 'delete':
                        $this->affectedDocuments = $result->getDeletedCount();
                        break;
                }

                return true;
            } catch (\MongoDb\Driver\Exception\BulkWriteException $e) {
                $statement->addError($e->getCode(), $e->getMessage());
            }
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Connection::parseQueryStatement
     *
     * @param  Statement $statement Query object.
     *
     * @return object
     */
    protected function parseQueryStatement(Statement &$statement)
    {
        $builderCache = $statement->getBuilderCache();

        // Projection Option
        if (count($builderCache->select)) {

            $projection = [];

            foreach ($builderCache->select as $field) {
                $projection[ $field ] = 1;
            }

            $statement->addOption('projection', $projection);
        }

        // Filter Where In
        if (count($builderCache->whereIn)) {
            foreach ($builderCache->whereIn as $field => $clause) {
                if (count($builderCache->orWhereIn)) {
                    $builderCache->orWhere[ $field ] = ['$in' => $clause];
                } else {
                    $builderCache->where[ $field ] = ['$in' => $clause];
                }
            }
        }

        // Filter Where Not In
        if (count($builderCache->whereNotIn)) {
            foreach ($builderCache->whereNotIn as $field => $clause) {
                $builderCache->where[ $field ] = ['$nin' => $clause];
            }
        }

        // Filter Or Where In
        if (count($builderCache->orWhereIn)) {
            foreach ($builderCache->orWhereIn as $field => $clause) {
                $builderCache->orWhere[ $field ] = ['$in' => $clause];
            }
        }

        // Filter Or Where Not In
        if (count($builderCache->orWhereNotIn)) {
            foreach ($builderCache->orWhereNotIn as $field => $clause) {
                $builderCache->orWhere[ $field ] = ['$nin' => $clause];
            }
        }

        // Filter Where Between
        if (count($builderCache->between)) {
            foreach ($builderCache->between as $field => $clause) {
                $builderCache->where[ $field ] = ['$gte' => $clause[ 'start' ], '$lte' => $clause[ 'end' ]];
            }
        }

        // Filter Or Where Between
        if (count($builderCache->orBetween)) {
            foreach ($builderCache->orBetween as $field => $clause) {
                $builderCache->orWhere[ $field ] = ['$gte' => $clause[ 'start' ], '$lte' => $clause[ 'end' ]];
            }
        }

        // Filter Where Not Between
        if (count($builderCache->notBetween)) {
            foreach ($builderCache->notBetween as $field => $clause) {
                $builderCache->where[ $field ][ '$not' ] = [
                    '$gte' => $clause[ 'start' ],
                    '$lte' => $clause[ 'end' ],
                ];
            }
        }

        // Filter Or Where Not Between
        if (count($builderCache->orNotBetween)) {
            foreach ($builderCache->orNotBetween as $field => $clause) {
                $builderCache->orWhere[ $field ][ '$not' ] = [
                    '$gte' => $clause[ 'start' ],
                    '$lte' => $clause[ 'end' ],
                ];
            }
        }

        // Filter Where
        if (count($builderCache->where)) {
            foreach ($builderCache->where as $field => $clause) {
                $statement->addFilter($field, $clause);
            }
        }

        // Filter Or Where
        if (count($builderCache->orWhere)) {
            $orWhere = [];
            foreach ($builderCache->orWhere as $field => $clause) {
                $orWhere[] = [$field => $clause];
            }

            $statement->addFilter('$or', $orWhere);
        }

        // Limit Option
        if ($builderCache->limit > 0) {
            $statement->addOption('limit', $builderCache->limit);
        }

        // Offset Option
        if ($builderCache->offset > 0) {
            $statement->addOption('skip', $builderCache->offset);
        }

        // Order Option
        if (count($builderCache->orderBy)) {

            $sort = [];
            foreach ($builderCache->orderBy as $field => $direction) {
                $direction = $direction === 'ASC' ? 1 : -1;
                $sort[ $field ] = $direction;
            }

            $statement->addOption('sort', $sort);
        }

        return $statement;
    }

    // ------------------------------------------------------------------------

    /**
     * Connection::platformQueryHandler
     *
     * Driver dependent way method for execute the Sql statement.
     *
     * @param \O2System\Database\NoSql\DataStructures\Query\Statement $statement Query object.
     *
     * @return array
     */
    protected function platformQueryHandler(Statement &$statement)
    {
        $this->parseQueryStatement($statement);

        $cursor = $this->server->executeQuery($this->database . '.' . $statement->getCollection(),
            new \MongoDb\Driver\Query($statement->getFilter(), $statement->getOptions()));

        return $cursor->toArray();
    }
}
