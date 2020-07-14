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

namespace O2System\Email\DataStructures;

// ------------------------------------------------------------------------

/**
 * Class Config
 *
 * @package O2System\Email\DataStructures
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
            'protocol'  => 'mail',
            'userAgent' => 'O2System\Email',
            'wordwrap'  => false,
        ];

        if (isset($config[ 'protocol' ])) {
            switch ($config[ 'protocol' ]) {
                default:
                case 'mail':

                    break;

                case 'sendmail':

                    // Path to sendmail binary
                    $defaultConfig[ 'mailPath' ] = '/usr/sbin/sendmail';

                    break;

                case 'smtp':

                    // SMTP Host can be an IP Address or domain name
                    $defaultConfig[ 'host' ] = '';

                    // SMTP Port by default it's set to 25
                    $defaultConfig[ 'port' ] = 25;

                    // SMTP Username
                    $defaultConfig[ 'user' ] = '';

                    // SMTP Password
                    $defaultConfig[ 'pass' ] = '';

                    // SMTP Encryption empty, tls or ssl
                    $defaultConfig[ 'encryption' ] = '';

                    break;
            }
        }

        $config = array_merge($defaultConfig, $config);

        parent::__construct($config);
    }
}