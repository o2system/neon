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

namespace O2System\Filesystem;

// ------------------------------------------------------------------------

/**
 * Class System
 *
 * @package O2System\Filesystem
 */
class System
{
    /**
     * System::getInfo
     *
     * Gets system info.
     *
     * @link http://php.net/manual/en/function.php-uname.php
     *
     * @return string Returns the description, as a string.
     */
    public function getInfo()
    {
        return php_uname();
    }

    // ------------------------------------------------------------------------

    /**
     * System::getHostname
     *
     * Gets system hostname.
     *
     * @return string Host name. eg. localhost.example.com.
     */
    public function getHostname()
    {
        return php_uname('n');
    }

    // ------------------------------------------------------------------------

    /**
     * System::getName
     *
     * Gets operating system name.
     *
     * @return string Operating system name. eg. FreeBSD.
     */
    public function getName()
    {
        return php_uname('s');
    }

    // ------------------------------------------------------------------------

    /**
     * System::getVersion
     *
     * Gets version of operating system.
     *
     * @return string Version information. Varies a lot between operating systems.
     */
    public function getVersion()
    {
        return php_uname('v');
    }

    // ------------------------------------------------------------------------

    /**
     * System::getRelease
     *
     * Gets release name of operating system
     *
     * @return string Release name. eg. 5.1.2-RELEASE.
     */
    public function getRelease()
    {
        return php_uname('r');
    }

    // ------------------------------------------------------------------------

    /**
     * System::getMachine
     *
     * Gets machine type.
     *
     * @return string Machine type. eg. i386.
     */
    public function getMachine()
    {
        return php_uname('m');
    }

    // ------------------------------------------------------------------------

    /**
     * System::getPhpSapi
     *
     * Returns a lowercase string that describes the type of interface (the Server API, SAPI) that PHP is using. For
     * example, in CLI PHP this string will be "cli" whereas with Apache it may have several different values depending
     * on the exact SAPI used. Possible values are listed below.
     *
     * @link http://php.net/manual/en/function.php-sapi-name.php
     *
     * @return string Returns the interface type, as a lowercase string.
     */
    public function getPhpSapi()
    {
        return php_sapi_name();
    }

    // ------------------------------------------------------------------------

    /**
     * System::getPhpVersion
     *
     * Gets the current PHP version
     *
     * @return void If the optional extension parameter is specified, phpversion() returns the version of that
     *              extension, or FALSE if there is no version information associated or the extension isn't enabled.
     */
    public function getPhpVersion()
    {
        return phpversion();
    }

    // ------------------------------------------------------------------------

    /**
     * System::getPhpExtensionVersion
     *
     * Gets php extension version.
     *
     * @param  string $extension An optional extension name.
     *
     * @return void
     */
    public function getPhpExtensionVersion($extension)
    {
        return phpversion($extension);
    }

    // ------------------------------------------------------------------------

    /**
     * System::getPhpExtensions
     *
     * Gets Php Extensions
     *
     * @param  boolean $zendExtensions
     *
     * @return array Returns an array with the names of all modules compiled and loaded
     */
    public function getPhpExtensions($zendExtensions = false)
    {
        return get_loaded_extensions($zendExtensions);
    }

    // ------------------------------------------------------------------------

    /**
     * System::isPhpExtensionLoaded
     *
     * Get Status Is Php Extension Loaded.
     *
     * @param  string $extension An optional extension name.
     *
     * @return boolean
     */
    public function isPhpExtensionLoaded($extension)
    {
        return (bool)extension_loaded($extension);
    }

    // ------------------------------------------------------------------------

    /**
     * System::getZendVersion
     *
     * Gets the version of the current Zend engine
     *
     * @return string Returns the Zend Engine version number, as a string.
     */
    public function getZendVersion()
    {
        return zend_version();
    }

    // ------------------------------------------------------------------------

    /**
     * System::getZendOptimizerVersion
     *
     * Gets Version of Zend Optimizer
     *
     * @return boolean Returns TRUE if function_name exists and is a function, FALSE otherwise.
     */
    public function getZendOptimizerVersion()
    {
        return function_exists('zend_optimizer_version') ? zend_optimizer_version() : false;
    }

    // ------------------------------------------------------------------------

    /**
     * System::getConfigurations
     *
     * @param  null|string $extension An Optional extension name
     * @param  boolean     $details
     *
     * @return mixed Returns the return value of the callback, or FALSE on error.
     */
    public function getConfigurations($extension = null, $details = true)
    {
        return call_user_func_array('ini_get_all', func_get_args());
    }

    // ------------------------------------------------------------------------

    /**
     * System::getMacAddress
     *
     * Gets system mac address.
     *
     * @return string
     */
    public function getMacAddress()
    {
        switch (PHP_OS) {
            default:
            case 'Darwin':
            case 'FreeBSD':
                $cmd = '/sbin/ifconfig';
                break;
            case 'Windows':
                $cmd = "ipconfig /all ";
                break;
        }

        $string = trim(shell_exec($cmd));

        if (preg_match_all('/([0-9a-f]{2}:){5}\w\w/i', $string, $matches)) {
            if (isset($matches[ 0 ])) {
                return reset($matches[ 0 ]); // get first mac address
            }
        } else {
            return implode(':', str_split(substr(md5('none'), 0, 12), 2));
        }
    }

    // ------------------------------------------------------------------------

    /**
     * System::getLoadAvg
     *
     * Gets system load averages.
     *
     * @param int $interval
     *
     * @return float
     */
    public function getLoadAvg($interval = 1)
    {
        $rs = sys_getloadavg();
        $interval = $interval >= 1 && 3 <= $interval ? $interval : 1;
        $load = $rs[ $interval ];

        return round(($load * 100) / $this->getCpuCores(), 2);
    }

    // ------------------------------------------------------------------------

    /**
     * System::getCpuCores
     *
     * Gets the numbers of system cores.
     *
     * @return int
     */
    public function getCpuCores()
    {
        $numCpus = 1;
        if (is_file('/proc/cpuinfo')) {
            $cpuinfo = file_get_contents('/proc/cpuinfo');
            preg_match_all('/^processor/m', $cpuinfo, $matches);
            $numCpus = count($matches[ 0 ]);
        } else {
            if ('WIN' == strtoupper(substr(PHP_OS, 0, 3))) {
                $process = @popen('wmic cpu get NumberOfCores', 'rb');
                if (false !== $process) {
                    fgets($process);
                    $numCpus = intval(fgets($process));
                    pclose($process);
                }
            } else {
                $process = @popen('sysctl -a', 'rb');
                if (false !== $process) {
                    $output = stream_get_contents($process);
                    preg_match('/hw.ncpu: (\d+)/', $output, $matches);
                    if ($matches) {
                        $numCpus = intval($matches[ 1 ][ 0 ]);
                    }
                    pclose($process);
                }
            }
        }

        return $numCpus;
    }
}