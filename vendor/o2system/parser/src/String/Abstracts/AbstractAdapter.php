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

namespace O2System\Parser\String\Abstracts;

// ------------------------------------------------------------------------

use O2System\Spl\Traits\Collectors\ConfigCollectorTrait;

/**
 * Class AbstractAdapter
 *
 * @package O2System\Parser\String\Abstracts
 */
abstract class AbstractAdapter
{
    use ConfigCollectorTrait;

    /**
     * AbstractAdapter::$engine
     *
     * Driver Engine
     *
     * @var object
     */
    protected $engine;

    /**
     * AbstractAdapter::$string
     *
     * Driver Raw String
     *
     * @var string
     */
    protected $string;

    // ------------------------------------------------------------------------

    /**
     * AbstractAdapter::initialize
     *
     * @param array $config
     */
    abstract public function initialize(array $config = []);

    // ------------------------------------------------------------------------

    /**
     * AbstractAdapter::isInitialize
     *
     * @return bool
     */
    public function isInitialize()
    {
        return (bool)(empty($this->engine) ? false : true);
    }

    // --------------------------------------------------------------------------------------


    /**
     * AbstractAdapter::getEngine
     *
     * @return object
     */
    public function &getEngine()
    {
        return $this->engine;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractAdapter::setEngine
     *
     * @param object $engine
     *
     * @return bool
     */
    public function setEngine($engine)
    {
        if ($this->isValidEngine($engine)) {
            $this->engine =& $engine;

            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractAdapter::isValidEngine
     *
     * @param object $engine
     *
     * @return mixed
     */
    abstract protected function isValidEngine($engine);

    // ------------------------------------------------------------------------

    /**
     * AbstractAdapter::__call
     *
     * @param string  $method
     * @param array   $arguments
     *
     * @return mixed|null
     */
    public function __call($method, array $arguments = [])
    {
        if (method_exists($this, $method)) {
            return call_user_func_array([&$this, $method], $arguments);
        } elseif (method_exists($this->engine, $method)) {
            return call_user_func_array([&$this->engine, $method], $arguments);
        }

        return null;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractAdapter::isSupported
     *
     * @return bool
     */
    abstract public function isSupported();

    // ------------------------------------------------------------------------

    /**
     * AbstractAdapter::loadFile
     *
     * @param string $filePath
     *
     * @return bool
     */
    public function loadFile($filePath)
    {
        if ($filePath instanceof \SplFileInfo) {
            $filePath = $filePath->getRealPath();
        }

        if (is_file($filePath)) {
            return $this->loadString(file_get_contents($filePath));
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractAdapter::loadString
     *
     * @param string $string
     *
     * @return bool
     */
    public function loadString($string)
    {
        $this->string = htmlspecialchars_decode($string);

        return (bool)empty($this->string);
    }

    // ------------------------------------------------------------------------

    /**
     * AbstractAdapter::parse
     *
     * @param array $vars Variable to be parsed.
     *
     * @return string
     */
    abstract public function parse(array $vars = []);
}