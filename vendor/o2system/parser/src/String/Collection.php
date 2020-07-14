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

namespace O2System\Parser\String;

// ------------------------------------------------------------------------

use O2System\Spl\Patterns\Structural\Provider\AbstractProvider;

/**
 * Class Collection
 * @package O2System\Parser\String
 */
class Collection extends AbstractProvider
{
    /**
     * Collection::$supportedAdapters
     *
     * @var array
     */
    public $supportedAdapters = [

    ];

    // ------------------------------------------------------------------------

    public function __construct()
    {
        if ($handle = opendir(__DIR__ . DIRECTORY_SEPARATOR . 'Adapters')) {

            while (false !== ($file = readdir($handle))) {

                if ($file != "." && $file != "..") {
                    array_push($this->supportedAdapters, pathinfo($file, PATHINFO_FILENAME));
                }
            }

            closedir($handle);
        }

        if ($handle = opendir(__DIR__ . DIRECTORY_SEPARATOR . 'Engines')) {

            while (false !== ($file = readdir($handle))) {

                if ($file != "." && $file != "..") {
                    array_push($this->supportedAdapters, pathinfo($file, PATHINFO_FILENAME));
                }
            }

            closedir($handle);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Collection::load
     *
     * @param string $engine
     * @param array  $config
     *
     * @return bool
     */
    public function load($engine, array $config = [])
    {
        if(class_exists($adapter = '\O2System\Parser\String\Adapters\\' . $engine)) {
            $this->register((new $adapter())->setConfig($config), $adapter);

            return $this->__isset($adapter);
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Collection::parse
     *
     * @param string $string
     * @param array  $vars
     *
     * @return string
     */
    public function parse($string, array $vars = [])
    {
        $output = $string;

        if(is_file($string)) {
            $output = file_get_contents($string);
        }

        if($this->count()) {
            foreach($this->registry as $adapter) {
                if($adapter instanceof Abstracts\AbstractAdapter) {
                    if($adapter->isSupported()) {
                        $adapter->initialize();
                        $adapter->loadString($output);
                        $output = $adapter->parse($vars);
                    }
                }
            }
        }

        return $output;
    }
}