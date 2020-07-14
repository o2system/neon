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

use O2System\Database\Sql\Abstracts\AbstractForge;

/**
 * Class Forge
 * @package O2System\Database\Sql\Drivers\MySql
 */
class Forge extends AbstractForge
{
    /**
     * Forge::$unsignedSupportColumnTypes
     *
     * UNSIGNED support
     *
     * @var array
     */
    protected $unsignedSupportColumnTypes = [
        'TINYINT',
        'SMALLINT',
        'MEDIUMINT',
        'INT',
        'INTEGER',
        'BIGINT',
        'REAL',
        'DOUBLE',
        'DOUBLE PRECISION',
        'FLOAT',
        'DECIMAL',
        'NUMERIC',
    ];

    /**
     * Forge::$quotedTableOptions
     *
     * Table Options list which required to be quoted
     *
     * @var array
     */
    protected $quotedTableOptions = [
        'COMMENT',
        'COMPRESSION',
        'CONNECTION',
        'DATA DIRECTORY',
        'INDEX DIRECTORY',
        'ENCRYPTION',
        'PASSWORD',
    ];

    /**
     * Forge::$nullStatement
     *
     * NULL value representation in CREATE/ALTER TABLE statements
     *
     * @var string
     */
    protected $nullStatement = 'NULL';

    // ------------------------------------------------------------------------

    /**
     * Forge::platformCreateDatabaseStatement
     *
     * @param string $database
     *
     * @return string
     */
    public function platformCreateDatabaseStatement($database)
    {
        return 'CREATE DATABASE ' . $database;
    }

    // ------------------------------------------------------------------------

    /**
     * Forge::platformDropDatabaseStatement
     *
     * @param string $database
     *
     * @return string
     */
    public function platformDropDatabaseStatement($database)
    {
        return 'DROP DATABASE ' . $this->conn->escapeIdentifiers($database);
    }

    // ------------------------------------------------------------------------

    /**
     * Forge::platformBackupDatabaseStatement
     *
     * @param string $database
     * @param string $backupFilePath
     *
     * @return string
     */
    public function platformBackupDatabaseStatement($database, $backupFilePath)
    {
        return 'BACKUP DATABASE ' . $this->conn->escapeIdentifiers($database) . ' TO DISK=' . $this->conn->escape($backupFilePath);
    }

    // ------------------------------------------------------------------------

    /**
     * Forge::platformCreateTableStatement
     *
     * @param string $table
     * @param array  $columns
     * @param bool   $force
     * @param array  $attributes
     *
     * @return mixed
     */
    public function platformCreateTableStatement($table, array $columns = [], $force = false, array $attributes = [])
    {
        $primaryKeys = $foreignKeys = $uniqueKeys = $indexesKeys = [];

        // Open Statement
        $statementLines[] = 'CREATE TABLE ' . ($force === true ? ' IF NOT EXISTS ' : '') . $this->conn->escapeIdentifiers($table) . ' (';

        // Columns Statement
        $columnStatements = [];
        foreach ($columns as $columnName => $columnAttributes) {
            if (isset($columnAttributes[ 'type' ])) {
                if (isset($columnAttributes[ 'foreign_key' ])) {
                    $foreignKeys[ $columnName ] = $columnAttributes[ 'foreign_key' ];
                }

                $columnStatementLine = [];
                $columnName = $this->conn->escapeIdentifiers($columnName);
                $columnAttributes[ 'type' ] = strtoupper($columnAttributes[ 'type' ]);

                if (isset($columnAttributes[ 'primary_key' ])) {
                    if ($columnAttributes[ 'primary_key' ] === true) {
                        $primaryKeys[] = $columnName;
                    }
                }

                if (isset($columnAttributes[ 'unique' ])) {
                    if ($columnAttributes[ 'unique' ] === true) {
                        $uniqueKeys[] = $columnName;
                    }
                }

                if ($columnAttributes[ 'type' ] === 'ENUM') {
                    if (empty($columnAttributes[ 'value' ])) {
                        continue;
                    } else {
                        if (is_string($columnAttributes[ 'value' ])) {
                            $columnAttributes[ 'value' ] = explode(',', $columnAttributes[ 'value' ]);
                        }

                        $columnAttributes[ 'value' ] = array_map(function ($value) {
                            return $this->conn->escape(str_replace('\'', '', trim($value)));
                        }, $columnAttributes[ 'value' ]);

                        $columnStatementLine[] = $columnName .
                            ' ' . $columnAttributes[ 'type' ] . '(' .
                            implode(',', $columnAttributes[ 'value' ])
                            . ')';
                    }
                } elseif (isset($columnAttributes[ 'length' ])) {
                    $columnStatementLine[] = $columnName . ' ' . $columnAttributes[ 'type' ] . '(' . $columnAttributes[ 'length' ] . ')';
                } else {
                    $columnStatementLine[] = $columnName . ' ' . $columnAttributes[ 'type' ];
                }

                if (isset($columnAttributes[ 'unsigned' ])) {
                    if ($columnAttributes[ 'unsigned' ] === true) {
                        if (in_array($columnAttributes[ 'type' ], $this->unsignedSupportColumnTypes)) {
                            $columnStatementLine[] = 'UNSIGNED';
                        }
                    }
                }

                if (isset($columnAttributes[ 'collate' ])) {
                    $columnStatementLine[] = 'COLLATE ' . $columnAttributes[ 'collate' ];
                } elseif (in_array($columnAttributes[ 'type' ],
                    ['CHAR', 'VARCHAR', 'TEXT', 'LONGTEXT', 'TINYTEXT', 'ENUM'])) {
                    $columnStatementLine[] = 'COLLATE ' . $this->conn->getConfig('collate');
                }

                if (isset($columnAttributes[ 'not_null' ])) {
                    if ($columnAttributes[ 'not_null' ] === true) {
                        $columnStatementLine[] = 'NOT NULL';
                    }
                } elseif (isset($columnAttributes[ 'null' ])) {
                    if ($columnAttributes[ 'null' ] === false) {
                        $columnStatementLine[] = 'NOT NULL';
                    }
                }

                if (isset($columnAttributes[ 'timestamp' ])) {
                    if ($columnAttributes[ 'timestamp' ] === true) {
                        $columnStatementLine[] = 'ON UPDATE CURRENT_TIMESTAMP';
                    }
                }

                if (isset($columnAttributes[ 'default' ])) {
                    $columnStatementLine[] = 'DEFAULT ' . $this->conn->escape($columnAttributes[ 'default' ]);
                }

                if (isset($columnAttributes[ 'auto_increment' ])) {
                    if ($columnAttributes[ 'auto_increment' ] === true) {
                        $columnStatementLine[] = 'AUTO_INCREMENT';
                    }
                }

                if (isset($columnAttributes[ 'comment' ])) {
                    $columnStatementLine[] = 'COMMENT ' . $columnAttributes[ 'comment' ];
                }

                $columnStatements[] = "\t" . implode(' ', $columnStatementLine);
            }
        }

        // Keys Statement
        $keyStatements = [];
        $constraintStatements = [];

        // Primary Key Statement
        if (count($primaryKeys)) {
            $keyStatements[] = 'PRIMARY KEY (' . implode(', ',
                    $primaryKeys) . ')' . (count($primaryKeys) >= 2 ? ' USING BTREE' : '');
        }

        // Unique Key Statement
        if (count($uniqueKeys)) {
            if (count($uniqueKeys) == 1) {
                $constraintStatements[] = 'UNIQUE (' . implode(',', $uniqueKeys) . ')';
            } else {
                $uniqueName = 'idx_' . implode('_', $uniqueKeys);
                $constraintStatements[] = 'CONSTRAINT ' . $uniqueName . ' UNIQUE (' . implode(',', $uniqueKeys) . ')';
            }
        }

        // Foreign Keys Statement
        if (count($foreignKeys)) {
            foreach ($foreignKeys as $foreignKeyColumnName => $foreignKeyAttributes) {
                if (empty($foreignKeyAttributes[ 'name' ])) {
                    $foreignKeyAttributes[ 'name' ] = 'fk_' . $foreignKeyColumnName;
                }

                if (isset($foreignKeyAttributes[ 'references' ])) {
                    $keyStatements[] = 'KEY ' .
                        $this->conn->escapeIdentifiers($foreignKeyAttributes[ 'name' ]) .
                        ' (' . $this->conn->escapeIdentifiers($foreignKeyColumnName) . ')';

                    $referenceParts = array_map('trim', explode('.', $foreignKeyAttributes[ 'references' ]));
                    list($referenceTable, $referenceColumn) = $referenceParts;

                    $referenceOnDelete = 'NO ACTION';
                    $referenceOnUpdate = 'NO ACTION';

                    $validReferenceActions = [
                        'NO ACTION',
                        'CASCADE',
                        'RESTRICT',
                        'SET NULL',
                    ];

                    if (isset($foreignKeyAttributes[ 'on_delete' ])) {
                        if (in_array($foreignKeyAttributes[ 'on_delete' ], $validReferenceActions)) {
                            $referenceOnDelete = $foreignKeyAttributes[ 'on_delete' ];
                        }
                    }

                    if (isset($foreignKeyAttributes[ 'on_update' ])) {
                        if (in_array($foreignKeyAttributes[ 'on_update' ], $validReferenceActions)) {
                            $referenceOnUpdate = $foreignKeyAttributes[ 'on_update' ];
                        }
                    }

                    $constraintStatements[] = 'CONSTRAINT ' .
                        $this->conn->escapeIdentifiers($foreignKeyAttributes[ 'name' ]) .
                        ' FOREIGN KEY (' . $this->conn->escapeIdentifiers($foreignKeyColumnName) . ') REFERENCES ' .
                        $this->conn->escapeIdentifiers($referenceTable) . ' (' . $this->conn->escapeIdentifiers($referenceColumn) .
                        ') ON DELETE ' . $referenceOnDelete . ' ON UPDATE ' . $referenceOnUpdate;
                }
            }
        }

        $statementLines[] = implode(',' . PHP_EOL,
            array_merge($columnStatements, $keyStatements, $constraintStatements));

        if (empty($attributes)) {
            $attributes[ 'engine' ] = 'InnoDB';
        }

        if( ! array_key_exists('charset', $attributes) ) {
            $attributes[ 'charset' ] = $this->conn->getConfig('charset');
        }

        if( ! array_key_exists('collate', $attributes) ) {
            $attributes[ 'collate' ] = $this->conn->getConfig('collate');
        }

        $attributeStatements = [];
        foreach ($attributes as $key => $value) {
            if(is_string($key)) {
                $key = strtoupper(dash($key));

                if ($key === 'CHARSET') {
                    $attributeStatements[] =  'DEFAULT CHARSET=' . $value;
                } elseif(in_array($key, $this->quotedTableOptions)) {
                    $attributeStatements[] = $this->conn->escape($key) . '=' . $value;
                } else {
                    $attributeStatements[] = $this->conn->escapeString($key) . '=' . $value;
                }
            }
        }

        $statementLines[] = ') ' . implode(' ', $attributeStatements);

        return implode(PHP_EOL, $statementLines) . ';';
    }

    // ------------------------------------------------------------------------

    /**
     * Forge::platformDropTableStatement
     *
     * @param string $table
     *
     * @return string
     */
    public function platformDropTableStatement($table)
    {
        return 'DROP TABLE ' . $this->conn->escapeIdentifiers($table);
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractForge::platformTruncateTableStatement
     *
     * @param string $table
     *
     * @return mixed
     */
    protected function platformTruncateTableStatement($table)
    {
        return 'TRUNCATE TABLE ' . $this->conn->escapeIdentifiers($table);
    }

    // ------------------------------------------------------------------------

    /**
     * Forge::platformAlterTableStatement
     *
     * @param string $table
     * @param string $column
     * @param array  $attributes
     * @param string $action
     *
     * @return string
     */
    public function platformAlterTableStatement($table, $column, array $attributes, $action)
    {
        $statementLines[] = 'ALTER TABLE ' . $this->conn->escapeIdentifiers($table);

        $alterTableStatementStrings[] = strtoupper($action);
        $alterTableStatementStrings[] = $this->conn->escapeIdentifiers($column);

        if (isset($attributes[ 'type' ])) {
            if ($attributes[ 'type' ] === 'ENUM') {
                if (empty($attributes[ 'value' ])) {
                    return false;
                } else {
                    if (is_string($attributes[ 'value' ])) {
                        $attributes[ 'value' ] = explode(',', $attributes[ 'value' ]);
                    }

                    $attributes[ 'value' ] = array_map(function ($value) {
                        return $this->conn->escape(str_replace('\'', '', trim($value)));
                    }, $attributes[ 'value' ]);

                    $alterTableStatementStrings[] = $attributes[ 'type' ] . '(' .
                        implode(',', $attributes[ 'value' ])
                        . ')';
                }
            } elseif (isset($attributes[ 'length' ])) {
                $alterTableStatementStrings[] = $attributes[ 'type' ] . '(' . $attributes[ 'length' ] . ')';
            } else {
                $alterTableStatementStrings[] = $attributes[ 'type' ];
            }

            if (isset($attributes[ 'unsigned' ])) {
                if ($attributes[ 'unsigned' ] === true) {
                    if (in_array($attributes[ 'type' ],
                        ['INT', 'BIGINT', 'SMALLINT', 'TINYINT', 'FLOAT', 'DECIMAL', 'REAL'])) {
                        $alterTableStatementStrings[] = 'UNSIGNED';
                    }
                }
            }

            if (isset($attributes[ 'collate' ])) {
                $alterTableStatementStrings[] = 'COLLATE ' . $attributes[ 'collate' ];
            } elseif (in_array($attributes[ 'type' ],
                ['CHAR', 'VARCHAR', 'TEXT', 'LONGTEXT', 'TINYTEXT', 'ENUM'])) {
                $alterTableStatementStrings[] = 'COLLATE ' . $this->conn->getConfig('collate');
            }

            if (isset($attributes[ 'not_null' ])) {
                if ($attributes[ 'not_null' ] === true) {
                    $alterTableStatementStrings[] = 'NOT NULL';
                }
            }

            if (isset($attributes[ 'default' ])) {
                $alterTableStatementStrings[] = 'DEFAULT ' . $this->conn->escape($attributes[ 'default' ]);
            }

            if (isset($attributes[ 'auto_increment' ])) {
                if ($attributes[ 'auto_increment' ] === true) {
                    $alterTableStatementStrings[] = 'AUTO_INCREMENT ';
                }
            }

            if (isset($attributes[ 'comment' ])) {
                $alterTableStatementStrings[] = 'COMMENT ' . $attributes[ 'comment' ];
            }

            $statementLines[] = implode(' ', $alterTableStatementStrings) . ';';
        }

        return implode(PHP_EOL, $statementLines);
    }

    // ------------------------------------------------------------------------

    /**
     * Forge::platformAlterTableDropColumnStatement
     *
     * @param string $table
     * @param string $column
     *
     * @return string
     */
    protected function platformAlterTableDropColumnStatement($table, $column)
    {
        $statementLines[] = 'ALTER TABLE ' . $this->conn->escapeIdentifiers($table);
        $statementLines[] = 'DROP COLUMN ' . $this->conn->escapeIdentifiers($column);

        return implode(PHP_EOL, $statementLines);
    }

    // ------------------------------------------------------------------------

    /**
     * Forge::platformAlterTablePrimaryKeysStatement
     *
     * @param string $table
     * @param array  $columns
     *
     * @return mixed
     */
    protected function platformAlterTablePrimaryKeysStatement($table, array $columns)
    {
        $statementLines[] = 'ALTER TABLE ' . $this->conn->escapeIdentifiers($table);

        $keys = array_map(function ($column) {
            return $this->conn->escapeIdentifiers($column);
        }, $columns);

        if (count($columns) == 1) {
            $statementLines[] = 'ADD PRIMARY KEY (' . implode(',', $keys) . ');';
        } else {
            $statementLines[] = 'ADD CONSTRAINT ' .
                $this->conn->escapeIdentifiers('pk_' . implode('_', $columns)) .
                ' PRIMARY KEY (' . implode(',', $keys) . ');';
        }

        return implode(PHP_EOL, $statementLines);
    }

    // ------------------------------------------------------------------------

    /**
     * Forge::platformAlterTableForeignKeyStatement
     *
     * @param string $table
     * @param string $column
     * @param string $references
     *
     * @return string
     */
    protected function platformAlterTableForeignKeyStatement($table, $column, $references)
    {
        $statementLines[] = 'ALTER TABLE ' . $this->conn->escapeIdentifiers($table);
        $statementLines[] = 'ADD CONSTRAINT ' . $this->conn->escapeIdentifiers('fk_' . $column);

        $referenceParts = array_map('trim', explode('.', $references));
        list($referenceTable, $referenceColumn) = $referenceParts;

        $statementLines[] = 'FOREIGN KEY (' . $this->conn->escapeIdentifiers($column) . ') REFERENCES ' .
            $this->conn->escapeIdentifiers($referenceTable) . '(' .
            $this->conn->escapeIdentifiers($referenceColumn) . ')';

        return implode(PHP_EOL, $statementLines);
    }

    // ------------------------------------------------------------------------

    /**
     * Forge::platformDropTableForeignKeyStatement
     *
     * @param string $table
     * @param string $column
     *
     * @return string
     */
    protected function platformDropTableForeignKeyStatement($table, $column)
    {
        $statementLines[] = 'ALTER TABLE ' . $this->conn->escapeIdentifiers($table);
        $statementLines[] = 'DROP FOREIGN KEY ' . $this->conn->escapeIdentifiers('fk_' . $column);

        return implode(PHP_EOL, $statementLines);
    }

    // ------------------------------------------------------------------------

    /**
     * Forge::platformAlterTableUniquesStatement
     *
     * @param string $table
     * @param array  $columns
     *
     * @return string
     */
    protected function platformAlterTableUniquesStatement($table, array $columns)
    {
        $statementLines[] = 'ALTER TABLE ' . $this->conn->escapeIdentifiers($table);

        $keys = array_map(function ($column) {
            return $this->conn->escapeIdentifiers($column);
        }, $columns);

        if (count($columns) == 1) {
            $statementLines[] = 'ADD UNIQUE (' . implode(',', $keys) . ');';
        } else {
            $statementLines[] = 'ADD CONSTRAINT ' .
                $this->conn->escapeIdentifiers('idx_' . implode('_', $columns)) .
                ' UNIQUE (' . implode(',', $keys) . ');';
        }

        return implode(PHP_EOL, $statementLines);
    }

    // ------------------------------------------------------------------------

    /**
     * Forge::platformCreateTableIndexesStatement
     *
     * @param string $table
     * @param array  $columns
     * @param bool   $unique
     *
     * @return string
     */
    protected function platformCreateTableIndexesStatement($table, array $columns, $unique = false)
    {
        $keys = array_map(function ($column) {
            return $this->conn->escapeIdentifiers($column);
        }, $columns);

        $statementLines[] = 'CREATE' . ($unique === true ? ' UNIQUE ' : ' ') .
            'INDEX ' . $this->conn->escapeIdentifiers('idx_' . implode('_', $columns));
        $statementLines[] = 'ON ' . $this->conn->escapeIdentifiers($table) . ' (' . implode(', ', $keys) . ');';

        return implode(PHP_EOL, $statementLines);
    }

    // ------------------------------------------------------------------------

    /**
     * Forge::platformDropTableIndexesStatement
     *
     * @param string $table
     * @param array  $columns
     *
     * @return string
     */
    protected function platformDropTableIndexesStatement($table, $columns)
    {
        $statementLines[] = 'ALTER TABLE ' . $this->conn->escapeIdentifiers($table);
        $statementLines[] = 'DROP INDEX ' . $this->conn->escapeIdentifiers('idx_' . implode('_', $columns));

        return implode(PHP_EOL, $statementLines);
    }

    // ------------------------------------------------------------------------

    /**
     * Forge::platformAlterTableSetColumnDefaultValueStatement
     *
     * @param string $table
     * @param string $column
     * @param mixed  $value
     *
     * @return string
     */
    protected function platformAlterTableSetColumnDefaultValueStatement($table, $column, $value)
    {
        $statementLines[] = 'ALTER TABLE ' . $this->conn->escapeIdentifiers($table);
        $statementLines[] = 'ALTER ' . $this->conn->escapeIdentifiers($column) . ' SET DEFAULT ' . $this->conn->escape($value);

        return implode(PHP_EOL, $statementLines);
    }

    // ------------------------------------------------------------------------

    /**
     * Forge::platformAlterTableDropColumnDefaultValue
     *
     * @param string $table
     * @param string $column
     *
     * @return string
     */
    protected function platformAlterTableDropColumnDefaultValueStatement($table, $column)
    {
        $statementLines[] = 'ALTER TABLE ' . $this->conn->escapeIdentifiers($table);
        $statementLines[] = 'ALTER ' . $this->conn->escapeIdentifiers($column) . ' DROP DEFAULT;';

        return implode(PHP_EOL, $statementLines);
    }

    // ------------------------------------------------------------------------

    /**
     * Forge::platformAlterTableCheckStatement
     *
     * @param string $table
     * @param array  $conditions
     *
     * @return string
     */
    protected function platformAlterTableCheckStatement($table, array $conditions)
    {
        $statementLines[] = 'ALTER TABLE ' . $this->conn->escapeIdentifiers($table);

        $columns = array_keys($conditions);

        if (count($conditions) == 1) {
            $statementLines[] = 'ADD CHECK (' . $this->conn->escapeIdentifiers($columns[ 0 ]) . ')';
        } else {
            $conditionStatementStrings = [];

            foreach ($conditions as $column => $condition) {
                if (preg_match('/\s*(?:<|>|!)?=\s*|\s*<>?\s*|\s*>\s*/i', $column, $match, PREG_OFFSET_CAPTURE)) {
                    $operator = trim($match[ 0 ]);
                    $column = trim(str_replace($operator, '', $column));

                    $conditionStatementStrings[] = $this->conn->escapeIdentifiers($column) . $operator . $this->conn->escape($condition);
                } else {
                    $conditionStatementStrings[] = $this->conn->escapeIdentifiers($column) . '=' . $this->conn->escape($condition);
                }
            }

            $statementLines[] = 'ADD CONSTRAINT ' .
                $this->conn->escapeIdentifiers('chk_' . implode('_', $columns)) .
                ' (' . implode(' AND ', $conditionStatementStrings) . ');';
        }

        return implode(PHP_EOL, $statementLines);
    }

    // ------------------------------------------------------------------------

    /**
     * Forge::platformAlterTableDropCheckStatement
     *
     * @param string $table
     * @param array  $columns
     *
     * @return mixed
     */
    protected function platformAlterTableDropCheckStatement($table, array $columns)
    {
        $statementLines[] = 'ALTER TABLE ' . $this->conn->escapeIdentifiers($table);
        $statementLines[] = 'DROP CHECK ' . $this->conn->escapeIdentifiers('chk_' . implode('_', $columns)) . ';';

        return implode(PHP_EOL, $statementLines);
    }

    // ------------------------------------------------------------------------

    /**
     * Forge::platformCreateViewStatement
     *
     * @param string $name
     * @param string $query
     * @param bool   $force
     *
     * @return string
     */
    protected function platformCreateViewStatement($name, $query, $force = false)
    {
        if ($force === true) {
            $statementLines[] = 'CREATE VIEW OR REPLACE VIEW ' . $this->conn->escapeIdentifiers('view_' . $name) . ' AS';
        } else {
            $statementLines[] = 'CREATE VIEW ' . $this->conn->escapeIdentifiers('view_' . $name) . ' AS';
        }

        $statementLines[] = $query;

        return implode(PHP_EOL, $statementLines);
    }

    // ------------------------------------------------------------------------

    /**
     * Forge::platformDropViewStatement
     *
     * @param string $name
     *
     * @return string
     */
    protected function platformDropViewStatement($name)
    {
        return 'DROP VIEW ' . $this->conn->escapeIdentifiers('view_' . $name) . ';';
    }
}