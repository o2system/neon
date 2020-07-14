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

namespace O2System\Html\Element;

// ------------------------------------------------------------------------

/**
 * Class Entity
 *
 * @package O2System\Html\Element
 */
class Entity
{
    /**
     * Entity::$entityName
     *
     * @var string
     */
    protected $entityName;

    // ------------------------------------------------------------------------

    /**
     * Entity::getEntityName
     *
     * @return string
     */
    public function getEntityName()
    {
        return $this->entityName;
    }

    // ------------------------------------------------------------------------

    /**
     * Entity::setEntityName
     *
     * @param string $entityName
     */
    public function setEntityName($entityName)
    {
        $this->entityName = camelcase($entityName);
    }
}