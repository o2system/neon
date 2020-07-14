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

namespace O2System\Image;

// ------------------------------------------------------------------------

use ImageOptimizer\OptimizerFactory;
use O2System\Image\Optimizers\Imageoptim;
use O2System\Image\Optimizers\Optimus;

/**
 * Class Optimizer
 * @package O2System\Image
 */
class Optimizer
{
    /**
     * Optimizer::$imageFactory
     *
     * @var Imageoptim|Optimus
     */
    protected $imageFactory = null;

    // ------------------------------------------------------------------------

    /**
     * Optimizer::setImageFactory
     *
     * @param $imageFactory
     *
     * @return static
     */
    public function setImageFactory($imageFactory)
    {
        if ($imageFactory instanceof Imageoptim || $imageFactory instanceof Optimus) {
            $this->imageFactory = $imageFactory;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Optimizer::optimize
     *
     * @param string $imageFilePath
     *
     * @throws \Exception
     */
    public function optimize($imageFilePath)
    {
        if (empty($this->imageFactory)) {
            if (class_exists('\ImageOptimizer\OptimizerFactory')) {
                (new OptimizerFactory())->get()->optimize($imageFilePath);
            }
        } elseif ($this->imageFactory instanceof Imageoptim || $this->imageFactory instanceof Optimus) {
            $imageString = $this->imageFactory->optimize($imageFilePath, 'full');
            file_put_contents($imageFilePath, $imageString);
        }
    }
}