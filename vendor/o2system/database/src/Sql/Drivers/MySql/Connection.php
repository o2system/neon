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

use O2System\Database\DataStructures\Config;
use O2System\Database\Sql\Abstracts\AbstractConnection;
use O2System\Database\Sql\DataStructures\Query;
use O2System\Spl\DataStructures\SplArrayObject;
use O2System\Spl\Exceptions\RuntimeException;

/**
 * Class Connection
 *
 * @package O2System\Database\Sql\Drivers\MySql
 */
class Connection extends AbstractConnection
{
    /**
     * Connection::$isDeleteHack
     *
     * DELETE hack flag
     *
     * Whether to use the MySql "delete hack" which allows the number
     * of affected rows to be shown. Uses a preg_replace when enabled,
     * adding a bit more processing to all queries.
     *
     * @var    bool
     */
    public $isDeleteHack = true;

    /**
     * Connection::$platform
     *
     * Database driver platform name.
     *
     * @var string
     */
    protected $platform = 'MySQL';

    /**
     * Connection::$config
     *
     * Connection configurations.
     *
     * @var array
     */
    protected $config
        = [
            'escapeCharacter'     => '`',
            'reservedIdentifiers' => ['*'],
            'likeEscapeStatement' => ' ESCAPE \'%s\' ',
            'likeEscapeCharacter' => '!',
        ];

    /**
     * Connection::$handle
     *
     * MySqli Connection Instance.
     *
     * @var \mysqli
     */
    protected $handle;

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
        return extension_loaded('mysqli');
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
        $database = empty($database)
            ? $this->database
            : $database;

        if ($this->handle->select_db($database)) {
            $this->database = $database;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Connection::getDatabases
     *
     * Get list of current connection databases.
     *
     * @return array Returns an array.
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getDatabases()
    {
        if (empty($this->queriesResultCache[ 'databaseNames' ])) {
            $result = $this->query('SHOW DATABASES');

            if ($result->count()) {
                foreach ($result as $row) {

                    if ( ! isset($key)) {
                        if (isset($row[ 'database' ])) {
                            $key = 'database';
                        } elseif (isset($row[ 'Database' ])) {
                            $key = 'Database';
                        } elseif (isset($row[ 'DATABASE' ])) {
                            $key = 'DATABASE';
                        } else {
                            /* We have no other choice but to just get the first element's key.
                             * Due to array_shift() accepting its argument by reference, if
                             * E_STRICT is on, this would trigger a warning. So we'll have to
                             * assign it first.
                             */
                            $key = array_keys($row);
                            $key = array_shift($key);
                        }
                    }

                    $this->queriesResultCache[ 'databaseNames' ][] = $row->offsetGet($key);
                }
            }
        }

        return $this->queriesResultCache[ 'databaseNames' ];
    }

    // ------------------------------------------------------------------------

    /**
     * Connection::getTables
     *
     * Get list of current database tables.
     *
     * @param bool $prefixLimit If sets TRUE the query will be limit using database table prefix.
     *
     * @return array Returns an array
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getTables($prefixLimit = false)
    {
        if (empty($this->queriesResultCache[ 'tableNames' ])) {

            $sqlStatement = 'SHOW TABLES FROM ' . $this->escapeIdentifiers($this->config[ 'database' ]);

            if ($prefixLimit !== false && $this->config[ 'tablePrefix' ] !== '') {
                $sqlStatement .= " LIKE '" . $this->escapeLikeString($this->config[ 'tablePrefix' ]) . "%'";
            }

            $result = $this->query($sqlStatement);

            $this->queriesResultCache[ 'tableNames' ] = [];

            if ($result->count()) {
                foreach ($result as $row) {
                    // Do we know from which column to get the table name?
                    if ( ! isset($key)) {
                        if (isset($row[ 'table_name' ])) {
                            $key = 'table_name';
                        } elseif (isset($row[ 'TABLE_NAME' ])) {
                            $key = 'TABLE_NAME';
                        } else {
                            /* We have no other choice but to just get the first element's key.
                             * Due to array_shift() accepting its argument by reference, if
                             * E_STRICT is on, this would trigger a warning. So we'll have to
                             * assign it first.
                             */
                            $key = array_keys($row->getArrayCopy());
                            $key = array_shift($key);
                        }
                    }

                    $this->queriesResultCache[ 'tableNames' ][] = $row->offsetGet($key);
                }
            }
        }

        return $this->queriesResultCache[ 'tableNames' ];
    }

    // ------------------------------------------------------------------------

    /**
     * Connection::getColumns
     *
     * @param string $table The database table name.
     *
     * @return array
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getColumns($table)
    {
        $table = $this->prefixTable($table);

        if (empty($this->queriesResultCache[ 'tableColumns' ][ $table ])) {
            $result = $this->query('SHOW COLUMNS FROM ' . $this->protectIdentifiers($table, true, null, false));

            if ($result->count()) {
                foreach ($result as $row) {
                    // Do we know from where to get the column's name?
                    if ( ! isset($key)) {
                        if (isset($row[ 'column_name' ])) {
                            $key = 'column_name';
                        } elseif (isset($row[ 'COLUMN_NAME' ])) {
                            $key = 'COLUMN_NAME';
                        } else {
                            /* We have no other choice but to just get the first element's key.
                             * Due to array_shift() accepting its argument by reference, if
                             * E_STRICT is on, this would trigger a warning. So we'll have to
                             * assign it first.
                             */
                            $key = array_keys($row->getArrayCopy());
                            $key = array_shift($key);
                        }
                    }

                    $this->queriesResultCache[ 'tableColumns' ][ $table ][ $row->offsetGet($key) ] = new SplArrayObject($row->getArrayCopy());
                }
            }
        }

        return $this->queriesResultCache[ 'tableColumns' ][ $table ];
    }

    // ------------------------------------------------------------------------

    /**
     * Connection::reconnect
     *
     * Keep or establish the connection if no queries have been sent for
     * a length of time exceeding the server's idle timeout.
     *
     * @return void
     */
    public function reconnect()
    {
        if ($this->handle !== false && $this->handle->ping() === false) {
            $this->handle = false;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Connection::getAffectedRows
     *
     * Get the total number of affected rows from the last query execution.
     *
     * @return int  Returns total number of affected rows
     */
    public function getAffectedRows()
    {
        return $this->handle->affected_rows;
    }

    //--------------------------------------------------------------------

    /**
     * Connection::getLastInsertId
     *
     * Get last insert id from the last insert query execution.
     *
     * @return int  Returns total number of affected rows
     */
    public function getLastInsertId()
    {
        return $this->handle->insert_id;
    }

    // ------------------------------------------------------------------------

    /**
     * Connection::getLastQuery
     *
     * Returns the last query's statement object.
     *
     * @return string
     */
    public function getLastQuery()
    {
        $last = end($this->queriesCache);

        for($i = 0; $i < 2; $i++) {
            if(method_exists($last, 'getSqlStatement')) {
                if (
                    strpos($last->getSqlStatement(), 'SELECT FOUND_ROWS() AS numrows;') !== false or
                    strpos($last->getSqlStatement(), 'SELECT COUNT(*) AS `numrows`') !== false
                ) {
                    $last = prev($this->queriesCache);
                }
            } else {
                $last = prev($this->queriesCache);
            }
        }

        return $last;
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
        $server[ 'version' ][ 'string' ] = $this->handle->server_info;
        $server[ 'version' ][ 'number' ] = $this->handle->server_version;
        $server[ 'stats' ] = $this->handle->get_connection_stats();

        $client[ 'version' ][ 'string' ] = $this->handle->client_info;
        $client[ 'version' ][ 'number' ] = $this->handle->client_version;
        $client[ 'stats' ] = mysqli_get_client_stats();

        return new SplArrayObject([
            'name'     => $this->getPlatform(),
            'host'     => $this->handle->host_info,
            'state'    => $this->handle->sqlstate,
            'protocol' => $this->handle->protocol_version,
            'server'   => $server,
            'client'   => $client,
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
        // Do we have a socket path?
        if ($config->hostname[ 0 ] === '/') {
            $hostname = null;
            $port = null;
            $socket = $config->hostname;
        } else {
            $hostname = ($config->persistent === true)
                ? 'p:' . $config->hostname
                : $config->hostname;
            $port = empty($config->port)
                ? null
                : $config->port;
            $socket = null;
        }

        $flags = ($config->compress === true)
            ? MYSQLI_CLIENT_COMPRESS
            : 0;
        $this->handle = mysqli_init();
        //$this->handle->autocommit( ( $this->transactionEnable ? true : false ) );

        $this->handle->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);

        if (isset($config->strictOn)) {
            if ($config->strictOn) {
                $this->handle->options(
                    MYSQLI_INIT_COMMAND,
                    'SET SESSION Sql_mode = CONCAT(@@Sql_mode, ",", "STRICT_ALL_TABLES")'
                );
            } else {
                $this->handle->options(
                    MYSQLI_INIT_COMMAND,
                    'SET SESSION Sql_mode =
					REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
					@@Sql_mode,
					"STRICT_ALL_TABLES,", ""),
					",STRICT_ALL_TABLES", ""),
					"STRICT_ALL_TABLES", ""),
					"STRICT_TRANS_TABLES,", ""),
					",STRICT_TRANS_TABLES", ""),
					"STRICT_TRANS_TABLES", "")'
                );
            }
        }

        if (is_array($config->encrypt)) {
            $ssl = [];
            empty($config->encrypt[ 'ssl_key' ]) OR $ssl[ 'key' ] = $config->encrypt[ 'ssl_key' ];
            empty($config->encrypt[ 'ssl_cert' ]) OR $ssl[ 'cert' ] = $config->encrypt[ 'ssl_cert' ];
            empty($config->encrypt[ 'ssl_ca' ]) OR $ssl[ 'ca' ] = $config->encrypt[ 'ssl_ca' ];
            empty($config->encrypt[ 'ssl_capath' ]) OR $ssl[ 'capath' ] = $config->encrypt[ 'ssl_capath' ];
            empty($config->encrypt[ 'ssl_cipher' ]) OR $ssl[ 'cipher' ] = $config->encrypt[ 'ssl_cipher' ];

            if ( ! empty($ssl)) {
                if (isset($config->encrypt[ 'ssl_verify' ])) {
                    if ($config->encrypt[ 'ssl_verify' ]) {
                        defined('MYSQLI_OPT_SSL_VERIFY_SERVER_CERT')
                        && $this->handle->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);
                    }
                    // Apparently (when it exists), setting MYSQLI_OPT_SSL_VERIFY_SERVER_CERT
                    // to FALSE didn't do anything, so PHP 5.6.16 introduced yet another
                    // constant ...
                    //
                    // https://secure.php.net/ChangeLog-5.php#5.6.16
                    // https://bugs.php.net/bug.php?id=68344
                    elseif (defined('MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT')) {
                        $this->handle->options(MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT, true);
                    }
                }

                $flags |= MYSQLI_CLIENT_SSL;
                $this->handle->ssl_set(
                    isset($ssl[ 'key' ])
                        ? $ssl[ 'key' ]
                        : null,
                    isset($ssl[ 'cert' ])
                        ? $ssl[ 'cert' ]
                        : null,
                    isset($ssl[ 'ca' ])
                        ? $ssl[ 'ca' ]
                        : null,
                    isset($ssl[ 'capath' ])
                        ? $ssl[ 'capath' ]
                        : null,
                    isset($ssl[ 'cipher' ])
                        ? $ssl[ 'cipher' ]
                        : null
                );
            }
        }

        if ($this->handle->real_connect(
            $hostname,
            $config->username,
            $config->password,
            $config->database,
            $port,
            $socket,
            $flags
        )
        ) {
            // Prior to version 5.7.3, MySql silently downgrades to an unencrypted connection if SSL setup fails
            if (
                ($flags & MYSQLI_CLIENT_SSL)
                AND version_compare($this->handle->client_info, '5.7.3', '<=')
                AND empty($this->handle->query("SHOW STATUS LIKE 'ssl_cipher'")
                    ->fetch_object()->Value)
            ) {
                $this->handle->close();
                // 'MySqli was configured for an SSL connection, but got an unencrypted connection instead!';
                logger()->error('E_DB_CONNECTION_SSL', [$this->platform]);

                if ($config->debugEnable) {
                    throw new RuntimeException('E_DB_CONNECTION_SSL');
                }

                return;
            }

            if ( ! $this->handle->set_charset($config->charset)) {
                // "Database: Unable to set the configured connection charset ('{$this->charset}')."
                logger()->error('E_DB_CONNECTION_CHARSET', [$config->charset]);
                $this->handle->close();

                if ($config->debugEnable) {
                    // 'Unable to set client connection character set: ' . $this->charset
                    throw new RuntimeException('E_DB_CONNECTION_CHARSET', [$config->charset]);
                }
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Connection::disconnectHandler
     *
     * Driver dependent way method for closing the connection.
     *
     * @return mixed
     */
    protected function platformDisconnectHandler()
    {
        $this->handle->close();
    }

    // ------------------------------------------------------------------------

    /**
     * Connection::executeHandler
     *
     * Driver dependent way method for execute the Sql statement.
     *
     * @param Query\Statement $statement Query object.
     *
     * @return bool
     */
    protected function platformExecuteHandler(Query\Statement &$statement)
    {
        if (false !== $this->handle->query($statement->getSqlFinalStatement())) {
            return true;
        }

        // Set query error information
        $statement->addError($this->handle->errno, $this->handle->error);

        return false;

    }

    // ------------------------------------------------------------------------

    /**
     * Connection::platformQueryHandler
     *
     * Driver dependent way method for execute the Sql statement.
     *
     * @param Query\Statement $statement Query object.
     *
     * @return array
     */
    protected function platformQueryHandler(Query\Statement &$statement)
    {
        $rows = [];

        if ($result = $this->handle->query($statement->getSqlFinalStatement())) {
            $rows = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $statement->addError($this->handle->errno, $this->handle->error);
        }

        return $rows;
    }

    //--------------------------------------------------------------------

    /**
     * Connection::platformTransactionBeginHandler
     *
     * Platform beginning a transaction handler.
     *
     * @return bool
     */
    protected function platformTransactionBeginHandler()
    {
        if ($this->transactionInProgress === false) {
            // Begin transaction using autocommit function set to false
            $this->handle->autocommit(false);

            // Flag for there is a transaction progress
            $this->transactionInProgress = true;

            // Flag for error checking
            $this->transactionStatus = true;
        }


        return $this->transactionInProgress;
    }

    // ------------------------------------------------------------------------

    /**
     * Connection::platformTransactionCommitHandler
     *
     * Platform committing a transaction handler.
     *
     * @return bool
     */
    protected function platformTransactionCommitHandler()
    {
        if ($this->transactionStatus === true) {
            $this->handle->commit();
            $this->handle->autocommit(true);
            $this->transactionInProgress = false;

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Connection::platformTransactionRollBackHandler
     *
     * Platform rolling back a transaction handler.
     *
     * @return void
     */
    protected function platformTransactionRollBackHandler()
    {
        $this->handle->rollback();
        $this->transactionInProgress = false;
        $this->handle->autocommit(true);
    }

    // ------------------------------------------------------------------------

    /**
     * Connection::prepareSqlStatement
     *
     * Platform preparing a Sql statement.
     *
     * @param string $sqlStatement Sql Statement to be prepared.
     * @param array  $options      Preparing Sql statement options.
     *
     * @return string
     */
    protected function platformPrepareSqlStatement($sqlStatement, array $options = [])
    {
        // mysqli_affected_rows() returns 0 for "DELETE FROM TABLE" queries. This hack
        // modifies the query so that it a proper number of affected rows is returned.
        if ($this->isDeleteHack === true && preg_match('/^\s*DELETE\s+FROM\s+(\S+)\s*$/i', $sqlStatement)) {
            return trim($sqlStatement) . ' WHERE 1=1';
        }

        return $sqlStatement;
    }

    // ------------------------------------------------------------------------

    /**
     * Connection::platformEscapeStringHandler
     *
     * Platform escape string handler.
     *
     * @param string $string
     *
     * @return string
     */
    protected function platformEscapeStringHandler($string)
    {
        return $this->handle->real_escape_string($string);
    }
}
