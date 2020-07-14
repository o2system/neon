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

namespace O2System\Database\DataObjects\Result;

// ------------------------------------------------------------------------

use O2System\Database\DataObjects\Result\Row\Record;
use O2System\Spl\DataStructures\Traits\ArrayConversionTrait;
use O2System\Spl\Exceptions\Logic\InvalidArgumentException;
use O2System\Spl\Iterators\ArrayIterator;
use Traversable;

/**
 * Class Row
 *
 * @package O2System\Database\DataObjects\Result
 */
class Row implements
    \IteratorAggregate,
    \ArrayAccess,
    \Countable,
    \Serializable,
    \JsonSerializable
{
    use ArrayConversionTrait;

    /**
     * Row::$columns
     *
     * List of result row fields
     *
     * @access  protected
     * @type    array
     */
    protected $columns = [];

    /**
     * Row::$record
     *
     * @var Record
     */
    protected $record;

    // ------------------------------------------------------------------------

    /**
     * Row::__construct
     *
     * @param array $columns
     */
    public function __construct(array $columns = [])
    {
        $this->record = new Record();

        foreach ($columns as $name => $value) {
            if (strpos($name, 'record_create') !== false) {
                $this->record->create->offsetSet(
                    str_replace('record_create_', '', $name),
                    $value
                );
                unset($columns[ $name ]);
            } elseif (strpos($name, 'record_update') !== false) {
                $this->record->create->offsetSet(
                    str_replace('record_update_', '', $name),
                    $value
                );
                unset($columns[ $name ]);
            } elseif (strpos($name, 'record') !== false) {
                $this->record->offsetSet(
                    str_replace('record_', '', $name),
                    $value
                );
                unset($columns[ $name ]);
            }
        }

        $this->columns = $columns;
    }

    // ------------------------------------------------------------------------

    /**
     * Row::count
     *
     * Num of row fields
     *
     * @return int
     */
    public function count()
    {
        return count($this->columns);
    }

    // ------------------------------------------------------------------------

    /**
     * Row::getFields
     *
     * Return row fields
     *
     * @return array
     */
    public function getColumns()
    {
        return array_keys($this->columns);
    }

    // ------------------------------------------------------------------------

    /**
     * Row::fetchFieldsInto
     *
     * @param string $className Name of the created class..
     * @param array  $classArgs Arguments to be passed into created class constructor.
     *
     * @return object
     * @throws \O2System\Spl\Exceptions\Logic\InvalidArgumentException
     * @throws \ReflectionException
     */
    public function fetchFieldsInto($className, array $classArgs = [])
    {
        if (is_string($className)) {
            if ( ! class_exists($className)) {
                throw new InvalidArgumentException('E_DB_FETCH_FIELDS_INTO_CLASS_NOT_FOUND', 0, [$className]);
            }
        }

        $classObject = $className;
        $reflection = new \ReflectionClass($className);

        if (count($classArgs)) {
            $constructor = $reflection->getConstructor();
            $classObject = is_null($constructor)
                ? $reflection->newInstance()
                : $reflection->newInstanceArgs(
                    $classArgs
                );
        } elseif (is_string($className)) {
            $classObject = new $className;
        }

        foreach ($this->columns as $fieldName => $fieldValue) {
            if (method_exists($classObject, $setFieldMethod = 'set' . studlycase($fieldName))) {
                call_user_func_array([&$classObject, $setFieldMethod], [$fieldValue]);
            } elseif (method_exists($classObject, '__set')) {
                $classObject->__set($fieldName, $fieldValue);
            } else {
                if ($this->isJSON($fieldValue)) {
                    $classObject->{camelcase($fieldName)} = new Columns\DataJSON(json_decode($fieldValue, true));
                } elseif ($this->isSerialized($fieldValue)) {
                    $classObject->{camelcase($fieldName)} = new Columns\DataSerialize(unserialize($fieldValue));
                } else {
                    $classObject->{camelcase($fieldName)} = $fieldValue;
                }
            }
        }

        return $classObject;
    }

    // ------------------------------------------------------------------------

    /**
     * Row::isJSON
     *
     * Checks if field value is JSON format.
     *
     * @param string $string
     *
     * @return bool
     */
    protected function isJSON($string)
    {
        // make sure provided input is of type string
        if ( ! is_string($string)) {
            return false;
        }

        // trim white spaces
        $string = trim($string);

        // get first character
        $first_char = substr($string, 0, 1);

        // get last character
        $last_char = substr($string, -1);

        // check if there is a first and last character
        if ( ! $first_char || ! $last_char) {
            return false;
        }

        // make sure first character is either { or [
        if ($first_char !== '{' && $first_char !== '[') {
            return false;
        }

        // make sure last character is either } or ]
        if ($last_char !== '}' && $last_char !== ']') {
            return false;
        }

        // let's leave the rest to PHP.
        // try to decode string
        json_decode($string);

        // check if error occurred
        if (json_last_error() === JSON_ERROR_NONE) {
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Row::isSerialized
     *
     * Checks if field value is PHP serialize format.
     *
     * @param $string
     *
     * @return bool
     */
    protected function isSerialized($string)
    {
        // if it isn't a string, it isn't serialized
        if ( ! is_string($string)) {
            return false;
        }
        $string = trim($string);
        if ('N;' == $string) {
            return true;
        }
        if ( ! preg_match('/^([adObis]):/', $string, $matches)) {
            return false;
        }
        switch ($matches[ 1 ]) {
            case 'a' :
            case 'O' :
            case 's' :
                if (preg_match("/^{$matches[1]}:[0-9]+:.*[;}]\$/s", $string)) {
                    return true;
                }
                break;
            case 'b' :
            case 'i' :
            case 'd' :
                if (preg_match("/^{$matches[1]}:[0-9.E-]+;\$/", $string)) {
                    return true;
                }
                break;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Row::getArrayCopy
     *
     * Gets array fields copy.
     *
     * @return array
     */
    public function getArrayCopy()
    {
        $fields = $this->columns;

        foreach ($fields as $fieldName => $fieldValue) {
            if ($this->isJSON($fieldValue)) {
                $fields[ $fieldName ] = new Row\Columns\DataJSON(json_decode($fieldValue, true));
            } elseif ($this->isSerialized($fieldValue)) {
                $fields[ $fieldName ] = new Row\Columns\DataSerialize(unserialize($fieldValue));
            } else {
                $fields[ $fieldName ] = $fieldValue;
            }
        }

        return $fields;
    }

    // ------------------------------------------------------------------------

    /**
     * Row::getValues
     *
     * Return row fields values.
     *
     * @return array
     */
    public function getValues()
    {
        return array_values($this->columns);
    }

    // ------------------------------------------------------------------------

    /**
     * Row::__get
     *
     * Route magic method __get into offsetGet method.
     *
     * @param string $field Field name.
     *
     * @return mixed|null
     */
    public function __get($field)
    {
        return $this->offsetGet($field);
    }

    // ------------------------------------------------------------------------

    /**
     * Row::__set
     *
     * Route magic method __set into offsetSet method.
     *
     * @param string $field Input name
     * @param mixed  $value Input value
     */
    public function __set($field, $value)
    {
        $this->offsetSet($field, $value);
    }

    // ------------------------------------------------------------------------

    /**
     * Row::offsetGet
     *
     * Offset to retrieve
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     *
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        if (isset($this->columns[ $offset ])) {

            $data = $this->columns[ $offset ];

            if ($this->isJSON($data)) {
                return new Row\Columns\DataJSON(json_decode($data, true));
            } elseif ($this->isSerialized($data)) {
                return new Row\Columns\DataSerialize(unserialize($data));
            } else {
                return $data;
            }
        } elseif(strpos($offset, 'record') !== false) {
            switch ($offset) {
                default:
                    return $this->record->{str_replace('record_', '', $offset)};
                    break;
                case 'record':
                    return $this->record;
                    break;
                case 'record_status':
                    return $this->record->status;
                    break;
                case 'record_left':
                    return $this->record->left;
                    break;
                case 'record_right':
                    return $this->record->right;
                    break;
                case 'record_depth':
                    return $this->record->depth;
                    break;
                case 'record_ordering':
                    return $this->record->ordering;
                    break;
                case 'record_create_user':
                    return $this->record->create->user;
                    break;
                case 'record_create_timestamp':
                    return $this->record->create->timestamp;
                    break;
                case 'record_update_user':
                    return $this->record->update->user;
                    break;
                case 'record_update_timestamp':
                    return $this->record->update->timestamp;
                    break;
            }
        }

        return null;
    }

    // ------------------------------------------------------------------------

    /**
     * Row::getRecord
     *
     * @return \O2System\Database\DataObjects\Result\Row\Record
     */
    public function getRecord()
    {
        return $this->record;
    }

    // ------------------------------------------------------------------------

    /**
     * Row::offsetSet
     *
     * Offset to set
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->columns[ $offset ] = $value;
    }

    // ------------------------------------------------------------------------

    /**
     * Row::getIterator
     *
     * Retrieve an external iterator
     *
     * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     *        <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new ArrayIterator($this->columns);
    }

    // ------------------------------------------------------------------------

    /**
     * Row::offsetExists
     *
     * Whether a offset exists
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->columns[ $offset ]);
    }

    // ------------------------------------------------------------------------

    /**
     * Row::offsetUnset
     *
     * Offset to unset
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($field)
    {
        unset($this->columns[ $field ]);
    }

    // ------------------------------------------------------------------------

    /**
     * String representation of object
     *
     * @link  http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize($this->rows);
    }

    // ------------------------------------------------------------------------

    /**
     * Constructs the object
     *
     * @link  http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized <p>
     *                           The string representation of the object.
     *                           </p>
     *
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        $this->rows = unserialize($serialized);
    }

    // ------------------------------------------------------------------------

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *        which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->columns;
    }
}