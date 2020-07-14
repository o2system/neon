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

namespace O2System\Email;

// ------------------------------------------------------------------------

/**
 * Class Address
 *
 * @package O2System\Email
 */
class Address
{
    /**
     * Address::$email
     *
     * Email address.
     *
     * @var string
     */
    protected $email;

    /**
     * Address::$name
     *
     * Email owner name.
     *
     * @var string
     */
    protected $name;

    // ------------------------------------------------------------------------

    /**
     * Address::__construct
     *
     * @param string      $email
     * @param string|null $name
     */
    public function __construct($email = null, $name = null)
    {
        if (isset($email)) {
            $this->setEmail($email);
        }

        if (isset($name)) {
            $this->setName($name);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Address::__toString
     */
    public function __toString()
    {
        $string = '';

        if (false !== ($email = $this->getEmail())) {
            $string = $email;
        }

        if (false !== ($name = $this->getName())) {
            $string .= ' <' . $name . '>';
        }

        return $string;
    }

    // ------------------------------------------------------------------------

    /**
     * Address::getEmail
     *
     * Gets email address.
     *
     * @return bool|string
     */
    public function getEmail()
    {
        if (empty($this->email)) {
            return false;
        }

        return $this->email;
    }

    // ------------------------------------------------------------------------

    /**
     * Address::setEmail
     *
     * @param string $email
     *
     * @return static
     */
    public function setEmail($email)
    {
        $email = trim($email);

        if (preg_match('/\<(.*)\>/', $email, $matches)) {
            $email = trim(str_replace($matches[ 0 ], '', $email));
            $name = $matches[ 1 ];
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = $email;

            if (isset($name)) {
                $this->setName($name);
            }
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Address::getName
     *
     * Gets email owner name.
     *
     * @return bool|string
     */
    public function getName()
    {
        if (empty($this->name)) {
            return false;
        }

        return $this->name;
    }

    // ------------------------------------------------------------------------

    /**
     * Address::setName
     *
     * @param string $name
     *
     * @return static
     */
    public function setName($name)
    {
        $name = trim($name);

        if ($name !== '') {
            $this->name = $name;
        }

        return $this;
    }
}