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

use O2System\Spl\Exceptions\RuntimeException;

/**
 * Class AbstractForge
 * @package O2System\Database\Sql\Abstracts
 */
abstract class AbstractForge
{
    /**
     * AbstractForge::$conn
     *
     * Query Builder database connection instance.
     *
     * @var AbstractConnection
     */
    protected $conn;

    /**
     * AbstractForge::$unsignedSupportColumnTypes
     *
     * UNSIGNED support
     *
     * @var array
     */
    protected $unsignedSupportColumnTypes = [];

    /**
     * AbstractForge::$quotedTableOptions
     *
     * Table Options list which required to be quoted
     *
     * @var array
     */
    protected $quotedTableOptions = [];

    /**
     * AbstractForge::$nullStatement
     *
     * NULL value representation in CREATE/ALTER TABLE statements
     *
     * @var string
     */
    protected $nullStatement = 'NULL';

    //--------------------------------------------------------------------

    /**
     * AbstractQueryBuilder::__construct
     *
     * @param \O2System\Database\Sql\Abstracts\AbstractConnection $conn
     */
    public function __construct(AbstractConnection &$conn)
    {
        $this->conn =& $conn;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractForge::createDatabase
     *
     * @param string $database
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function createDatabase($database)
    {
        if (false !== ($statement = $this->platformCreateDatabaseStatement($database))) {
            if ( ! $this->conn->query($statement) and $this->conn->debugEnable) {
                throw new RuntimeException('Unable to create the specified database.');
            }

            return true;
        }

        if ($this->conn->debugEnable) {
            throw new RuntimeException('This feature is not available for the database you are using.');
        }

        return false;
    }
    //--------------------------------------------------------------------

    /**
     * AbstractForge::platformCreateDatabaseStatement
     *
     * @param string $database
     *
     * @return string
     */
    abstract public function platformCreateDatabaseStatement($database);

    //--------------------------------------------------------------------

    /**
     * AbstractForge::dropDatabase
     *
     * @param string $database
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function dropDatabase($database)
    {
        if (false !== ($statement = $this->platformDropDatabaseStatement($database))) {
            if ( ! $this->conn->query($statement) and $this->conn->debugEnable) {
                throw new RuntimeException('Unable to drop the specified database.');
            }

            return true;
        }

        if ($this->conn->debugEnable) {
            throw new RuntimeException('This feature is not available for the database you are using.');
        }

        return false;
    }
    //--------------------------------------------------------------------

    /**
     * AbstractForge::platformDropDatabaseStatement
     *
     * @param string $database
     *
     * @return string
     */
    abstract public function platformDropDatabaseStatement($database);

    //--------------------------------------------------------------------

    /**
     * AbstractForge::backupDatabase
     *
     * @param string $database
     * @param string $backupFilePath
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function backupDatabase($database, $backupFilePath)
    {
        if (false !== ($statement = $this->platformBackupDatabaseStatement($database, $backupFilePath))) {
            if ( ! $this->conn->query($statement) and $this->conn->debugEnable) {
                throw new RuntimeException('Unable to backup the specified database.');
            }

            return true;
        }

        if ($this->conn->debugEnable) {
            throw new RuntimeException('This feature is not available for the database you are using.');
        }

        return false;
    }
    //--------------------------------------------------------------------

    /**
     * AbstractForge::platformBackupDatabaseStatement
     *
     * @param string $database
     * @param string $backupFilePath
     *
     * @return string
     */
    abstract public function platformBackupDatabaseStatement($database, $backupFilePath);

    //--------------------------------------------------------------------

    /**
     * AbstractForge::prepareTableNameStatement
     *
     * @param string $table
     *
     * @return string
     */
    public function prepareTableNameStatement($table)
    {
        $prefix = $this->conn->getConfig('tablePrefix');
        $table = str_replace($prefix, '', $table);

        return $prefix . $table;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractForge::runTableStatementQuery
     *
     * @param string $statement
     * @param string $errorMessage
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    protected function runTableStatementQuery($statement, $errorMessage)
    {
        if ($statement !== false) {
            if ($this->conn->query($statement)) {
                return true;
            } elseif ($this->conn->debugEnable) {
                throw new RuntimeException($errorMessage);
            }
        } elseif ($this->conn->debugEnable) {
            throw new RuntimeException('This feature is not available for the database you are using.');
        }

        return false;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractForge::createTable
     *
     * @param string $table
     * @param array  $columns
     * @param bool   $force
     * @param array  $attributes
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function createTable($table, array $columns = [], $force = false, array $attributes = [])
    {
        return $this->runTableStatementQuery(
            $this->platformCreateTableStatement(
                $this->prepareTableNameStatement($table),
                $columns,
                $force,
                $attributes
            ),
            'Unable to create table on the specified database'
        );
    }

    //--------------------------------------------------------------------

    /**
     * AbstractForge::platformCreateTableStatement
     *
     * @param string $table
     * @param array  $columns
     * @param bool   $force
     * @param array  $attributes
     *
     * @return mixed
     */
    abstract public function platformCreateTableStatement(
        $table,
        array $columns = [],
        $force = false,
        array $attributes = []
    );

    //--------------------------------------------------------------------

    /**
     * AbstractForge::dropTable
     *
     * @param string $table
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function dropTable($table)
    {
        return $this->runTableStatementQuery(
            $this->platformDropDatabaseStatement(
                $this->prepareTableNameStatement($table)
            ),
            'Unable to drop the specified database table.'
        );
    }
    //--------------------------------------------------------------------

    /**
     * AbstractForge::platformDropTableStatement
     *
     * @param string $table
     *
     * @return string
     */
    abstract public function platformDropTableStatement($table);

    //--------------------------------------------------------------------

    /**
     * AbstractForge::truncateTable
     *
     * @param string $table
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function truncateTable($table)
    {
        return $this->runTableStatementQuery(
            $this->platformTruncateTableStatement(
                $this->prepareTableNameStatement($table)
            ),
            'Unable to truncate the specified database table.'
        );
    }

    //--------------------------------------------------------------------

    /**
     * AbstractForge::platformTruncateTableStatement
     *
     * @param string $table
     *
     * @return mixed
     */
    abstract protected function platformTruncateTableStatement($table);

    //--------------------------------------------------------------------

    /**
     * AbstractForge::addTableColumn
     *
     * @param string $table
     * @param string $column
     * @param array  $attributes
     *
     * @return bool|\O2System\Database\DataObjects\Result
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function addTableColumn($table, $column, array $attributes)
    {
        return $this->runTableStatementQuery(
            $this->platformAlterTableStatement(
                $this->prepareTableNameStatement($table),
                $column,
                $attributes,
                'ADD'
            ),
            'Unable to add column to specified database table'
        );
    }

    //--------------------------------------------------------------------

    /**
     * AbstractForge::modifyTableColumn
     *
     * @param string $table
     * @param string $column
     * @param array  $attributes
     *
     * @return bool|\O2System\Database\DataObjects\Result
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function modifyTableColumn($table, $column, array $attributes)
    {
        $table = $this->conn->getConfig('tablePrefix') . $table;

        return $this->runTableStatementQuery(
            $this->platformAlterTableStatement(
                $this->prepareTableNameStatement($table),
                $column,
                $attributes,
                'MODIFY'
            ),
            'Unable to modify column on specified database table'
        );
    }

    //--------------------------------------------------------------------

    /**
     * AbstractForge::platformAlterTableStatement
     *
     * @param string $table
     * @param string $column
     * @param array  $attributes
     * @param string $action
     *
     * @return string
     */
    abstract public function platformAlterTableStatement($table, $column, array $attributes, $action);

    //--------------------------------------------------------------------

    /**
     * AbstractForge::addDropColumn
     *
     * @param string $table
     * @param string $column
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function dropTableColumn($table, $column)
    {
        if (false === ($sqlStatement = $this->platformAlterTableDropColumnStatement($table, $column))) {
            if ($this->conn->debugEnable) {
                throw new RuntimeException('This feature is not available for the database you are using.');
            }

            return false;
        }
        return $this->runTableStatementQuery(
            $this->platformAlterTableDropColumnStatement(
                $this->prepareTableNameStatement($table),
                $column
            ),
            'Unable to drop column on specified database table'
        );
    }

    //--------------------------------------------------------------------

    /**
     * AbstractForge::platformAlterTableDropColumnStatement
     *
     * @param string $table
     * @param string $column
     *
     * @return string
     */
    abstract protected function platformAlterTableDropColumnStatement($table, $column);

    //--------------------------------------------------------------------

    /**
     * AbstractForge::addTablePrimaryKey
     *
     * @param string $table
     * @param string $column
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function addTablePrimaryKey($table, $column)
    {
        return $this->addTablePrimaryKeys($table, [$column]);
    }

    //--------------------------------------------------------------------

    /**
     * AbstractForge::addTablePrimaryKeys
     *
     * @param string $table
     * @param array  $columns
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function addTablePrimaryKeys($table, array $columns)
    {
        return $this->runTableStatementQuery(
            $this->platformAlterTablePrimaryKeysStatement(
                $this->prepareTableNameStatement($table),
                $columns
            ),
            'Unable to add unique constraint to specified database table'
        );
    }

    //--------------------------------------------------------------------

    /**
     * AbstractForge::platformAlterTablePrimaryKeysStatement
     *
     * @param string $table
     * @param array  $columns
     *
     * @return mixed
     */
    abstract protected function platformAlterTablePrimaryKeysStatement($table, array $columns);

    //--------------------------------------------------------------------

    /**
     * AbstractForge::addTableForeignKey
     *
     * @param string $table
     * @param string $column
     * @param string $references
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function addTableForeignKey($table, $column, $references)
    {
        return $this->runTableStatementQuery(
            $this->platformAlterTableForeignKeyStatement(
                $this->prepareTableNameStatement($table),
                $column,
                $references
            ),
            'Unable to add foreign key constraint to specified database table'
        );
    }

    //--------------------------------------------------------------------

    /**
     * AbstractForge::platformAlterTableForeignKeyStatement
     *
     * @param string $table
     * @param string $column
     * @param string $references
     *
     * @return string
     */
    abstract protected function platformAlterTableForeignKeyStatement($table, $column, $references);

    //--------------------------------------------------------------------

    /**
     * AbstractForge::dropTableForeignKey
     *
     * @param string $table
     * @param string $column
     *
     * @return bool|\O2System\Database\DataObjects\Result
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function dropTableForeignKey($table, $column)
    {
        return $this->runTableStatementQuery(
            $this->platformDropTableForeignKeyStatement(
                $this->prepareTableNameStatement($table),
                $column
            ),
            'Unable to drop foreign key on specified database table'
        );
    }

    //--------------------------------------------------------------------

    /**
     * AbstractForge::platformDropTableForeignKeyStatement
     *
     * @param string $table
     * @param string $column
     *
     * @return string
     */
    abstract protected function platformDropTableForeignKeyStatement($table, $column);

    //--------------------------------------------------------------------

    /**
     * AbstractForge::addTableUniques
     *
     * @param string $table
     * @param string $column
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function addTableUnique($table, $column)
    {
        return $this->addTableUniques($table, [$column]);
    }

    //--------------------------------------------------------------------

    /**
     * AbstractForge::addTableUniques
     *
     * @param string $table
     * @param array  $columns
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function addTableUniques($table, array $columns)
    {
        return $this->runTableStatementQuery(
            $this->platformAlterTableUniquesStatement(
                $this->prepareTableNameStatement($table),
                $columns
            ),
            'Unable to add unique constraint to specified database table'
        );
    }

    //--------------------------------------------------------------------

    /**
     * AbstractForge::platformAlterTableUniquesStatement
     *
     * @param string $table
     * @param array  $columns
     *
     * @return mixed
     */
    abstract protected function platformAlterTableUniquesStatement($table, array $columns);

    //--------------------------------------------------------------------

    /**
     * AbstractForge::createTableIndex
     *
     * @param string $table
     * @param string $column
     * @param bool   $unique
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function createTableIndex($table, $column, $unique = false)
    {
        return $this->createTableIndexes($table, [$column], $unique);
    }

    //--------------------------------------------------------------------

    /**
     * AbstractForge::createTableIndexes
     *
     * @param string $table
     * @param array  $columns
     * @param bool   $unique
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function createTableIndexes($table, array $columns, $unique = false)
    {
        return $this->runTableStatementQuery(
            $this->platformCreateTableIndexesStatement(
                $this->prepareTableNameStatement($table),
                $columns,
                $unique
            ),
            'Unable to add indexes to specified database table'
        );
    }

    //--------------------------------------------------------------------

    /**
     * AbstractForge::platformCreateTableIndexesStatement
     *
     * @param string $table
     * @param array  $columns
     * @param bool   $unique
     *
     * @return string
     */
    abstract protected function platformCreateTableIndexesStatement($table, array $columns, $unique = false);

    //--------------------------------------------------------------------

    /**
     * AbstractForge::dropTableIndex
     *
     * @param string $table
     * @param string $column
     *
     * @return bool|\O2System\Database\DataObjects\Result
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function dropTableIndex($table, $column)
    {
        return $this->dropTableIndexes($table, [$column]);
    }

    //--------------------------------------------------------------------

    /**
     * AbstractForge::dropTableIndexes
     *
     * @param string $table
     * @param array  $columns
     *
     * @return bool|\O2System\Database\DataObjects\Result
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function dropTableIndexes($table, array $columns)
    {
        return $this->runTableStatementQuery(
            $this->platformDropTableIndexesStatement(
                $this->prepareTableNameStatement($table),
                $columns
            ),
            'Unable to drop indexes on specified database table'
        );
    }

    //--------------------------------------------------------------------

    /**
     * AbstractForge::platformDropTableIndexesStatement
     *
     * @param string $table
     * @param array  $columns
     *
     * @return string
     */
    abstract protected function platformDropTableIndexesStatement($table, array $columns);

    //--------------------------------------------------------------------

    /**
     * AbstractForge::setTableColumnDefaultValue
     *
     * @param string $table
     * @param string $column
     * @param mixed  $value
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function setTableColumnDefaultValue($table, $column, $value)
    {
        return $this->runTableStatementQuery(
            $this->platformAlterTableSetColumnDefaultValueStatement(
                $this->prepareTableNameStatement($table),
                $column,
                $value
            ),
            'Unable to set default value to specified database table'
        );
    }

    //--------------------------------------------------------------------

    /**
     * AbstractForge::platformAlterTableSetColumnDefaultValueStatement
     *
     * @param string $table
     * @param string $column
     * @param mixed  $value
     *
     * @return string
     */
    abstract protected function platformAlterTableSetColumnDefaultValueStatement($table, $column, $value);

    //--------------------------------------------------------------------

    /**
     * AbstractForge::dropTableColumnDefaultValue
     *
     * @param string $table
     * @param string $column
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function dropTableColumnDefaultValue($table, $column)
    {
        return $this->runTableStatementQuery(
            $this->platformAlterTableDropColumnDefaultValueStatement(
                $this->prepareTableNameStatement($table),
                $column
            ),
            'Unable to drop default value on specified database table'
        );
    }

    //--------------------------------------------------------------------

    /**
     * AbstractForge::platformAlterTableDropColumnDefaultValueStatement
     *
     * @param string $table
     * @param string $column
     *
     * @return string
     */
    abstract protected function platformAlterTableDropColumnDefaultValueStatement($table, $column);

    //--------------------------------------------------------------------

    /**
     * AbstractForge::addTableColumnCheck
     *
     * @param string $table
     * @param array  $conditions
     *
     * @return bool|\O2System\Database\DataObjects\Result
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function addTableColumnCheck($table, array $conditions)
    {
        return $this->runTableStatementQuery(
            $this->platformAlterTableCheckStatement(
                $this->prepareTableNameStatement($table),
                $conditions
            ),
            'Unable to add value check constraint to specified database table'
        );
    }

    //--------------------------------------------------------------------

    /**
     * AbstractForge::platformAlterTableCheckStatement
     *
     * @param string $table
     * @param array  $conditions
     *
     * @return string
     */
    abstract protected function platformAlterTableCheckStatement($table, array $conditions);

    //--------------------------------------------------------------------

    /**
     * AbstractForge::dropTableColumnCheck
     *
     * @param string $table
     * @param array  $columns
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function dropTableColumnCheck($table, array $columns)
    {
        return $this->runTableStatementQuery(
            $this->platformAlterTableDropCheckStatement(
                $this->prepareTableNameStatement($table),
                $columns
            ),
            'Unable to drop value check constraint on specified database table'
        );
    }

    //--------------------------------------------------------------------

    /**
     * AbstractForge::platformAlterTableDropCheckStatement
     *
     * @param string $table
     * @param array  $columns
     *
     * @return mixed
     */
    abstract protected function platformAlterTableDropCheckStatement($table, array $columns);

    //--------------------------------------------------------------------

    /**
     * AbstractForge::createView
     *
     * @param string $name
     * @param string $query
     * @param bool   $force
     *
     * @return false
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function createView($name, $query, $force = false)
    {
        if (false !== ($statement = $this->platformCreateViewStatement($name, $query, $force))) {
            if (false !== ($result = $this->conn->query($statement))) {
                return $result;
            }

            if ($this->conn->debugEnable) {
                throw new RuntimeException('Unable to create view specified database');
            }
        }

        if ($this->conn->debugEnable) {
            throw new RuntimeException('This feature is not available for the database you are using.');
        }

        return false;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractForge::platformCreateViewStatement
     *
     * @param string $name
     * @param string $query
     * @param bool   $force
     *
     * @return string
     */
    abstract protected function platformCreateViewStatement($name, $query, $force = false);

    //--------------------------------------------------------------------

    /**
     * AbstractForge::dropView
     *
     * @param string $name
     *
     * @return bool
     * @throws \O2System\Spl\Exceptions\RuntimeException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function dropView($name)
    {
        if (false !== ($statement = $this->platformDropViewStatement($name))) {
            if (false !== ($result = $this->conn->query($statement))) {
                return $result;
            }

            if ($this->conn->debugEnable) {
                throw new RuntimeException('Unable to drop view on specified database');
            }
        }

        if ($this->conn->debugEnable) {
            throw new RuntimeException('This feature is not available for the database you are using.');
        }

        return false;
    }

    //--------------------------------------------------------------------

    /**
     * AbstractForge::platformDropViewStatement
     *
     * @param string $name
     *
     * @return string
     */
    abstract protected function platformDropViewStatement($name);
}