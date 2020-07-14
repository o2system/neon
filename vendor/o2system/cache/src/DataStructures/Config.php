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

namespace O2System\Cache\DataStructures;

// ------------------------------------------------------------------------

/**
 * Class Config
 *
 * @package O2System\Cache\Metadata
 */
class Config extends \O2System\Kernel\DataStructures\Config
{
    /**
     * Config::__construct
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (isset($config[ 'default' ])) {
            foreach ($config as $poolOffset => $poolConfig) {
                $config[ $poolOffset ] = new self($poolConfig);
            }
        }

        parent::__construct($config);
    }
}