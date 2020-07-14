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

namespace O2System\Image\DataStructures;

// ------------------------------------------------------------------------

/**
 * Class Config
 *
 * @package O2System\Parser\Metadata
 */
class Config extends \O2System\Kernel\DataStructures\Config
{
    /**
     * Config::__construct
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $defaultConfig = [
            'driver'              => 'gd', // gd | imagick | gmagick
            'maintainAspectRatio' => true,
            'scaleDirective'      => 'RATIO', // RATIO | UP | DOWN
            'focus'               => 'NORTHWEST',
            'orientation'         => 'AUTO',
            'quality'             => 100,
            'cached'              => false,
            'optimizer'           => 'default',
        ];

        $config = array_merge($defaultConfig, $config);

        if ($config[ 'driver' ] === 'imagick') {
            $config[ 'driver' ] = 'imagemagick';
        } elseif ($config[ 'driver' ] === 'gmagick') {
            $config[ 'driver' ] = 'graphicsmagick';
        }

        parent::__construct($config);
    }
}