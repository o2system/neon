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

namespace O2System\Filesystem\Handlers;

// ------------------------------------------------------------------------

use O2System\Kernel\DataStructures\Input\Files;
use O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException;


/**
 * Class Uploader
 *
 * @package O2System\Filesystem\Handlers
 */
class Uploader extends Files
{
    /**
     * Uploader::__construct
     *
     * @param array $config
     *
     * @throws \O2System\Spl\Exceptions\Logic\BadFunctionCall\BadDependencyCallException
     */
    public function __construct(array $config = [])
    {
        parent::__construct();
        
        language()
            ->addFilePath(str_replace('Handlers', '', __DIR__) . DIRECTORY_SEPARATOR)
            ->loadFile('uploader');

        if ( ! extension_loaded('fileinfo')) {
            throw new BadDependencyCallException('UPLOADER_E_FINFO_EXTENSION');
        }

        if (isset($config[ 'path' ])) {
            $config[ 'path' ] = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $config[ 'path' ]);

            if (is_dir($config[ 'path' ])) {
                $this->path = $config[ 'path' ];
            } elseif (defined('PATH_STORAGE')) {
                if (is_dir($config[ 'path' ])) {
                    $this->path = $config[ 'path' ];
                } else {
                    $this->path = PATH_STORAGE . str_replace(PATH_STORAGE, '', $config[ 'path' ]);
                }
            } else {
                $this->path = dirname($_SERVER[ 'SCRIPT_FILENAME' ]) . DIRECTORY_SEPARATOR . $config[ 'path' ];
            }
        } elseif (defined('PATH_STORAGE')) {
            $this->path = PATH_STORAGE;
        } else {
            $this->path = dirname($_SERVER[ 'SCRIPT_FILENAME' ]) . DIRECTORY_SEPARATOR . 'upload';
        }

        $this->path = rtrim($this->path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (isset($config[ 'allowedMimes' ])) {
            $this->setAllowedMimes($config[ 'allowedMimes' ]);
        }

        if (isset($config[ 'allowedExtensions' ])) {
            $this->setAllowedExtensions($config[ 'allowedExtensions' ]);
        }
    }
}