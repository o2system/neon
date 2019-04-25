<?php
/**
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */
// ------------------------------------------------------------------------

// Valid PHP Version?
$minPHPVersion = '7.2';
if (phpversion() < $minPHPVersion)
{
    die("Your PHP version must be {$minPHPVersion} or higher to run O2System. Current version: " . phpversion());
}
unset($minPHPVersion);

// ------------------------------------------------------------------------

define( 'STARTUP_TIME', microtime( true ) );
define( 'STARTUP_MEMORY', memory_get_usage( true ) );

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     testing
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 *
 */
if ( ! defined( 'ENVIRONMENT' ) ) {
    /**
     * Environment Stage
     *
     * @value DEVELOPMENT|TESTING|PRODUCTION
     */
    define( 'ENVIRONMENT', 'DEVELOPMENT' );

    /**
     * Environment Debug Stage
     *
     * @value DEVELOPER|TESTER|PUBLIC
     */
    $_ENV[ 'DEBUG_STAGE' ] = 'DEVELOPER';
}

/*
 *---------------------------------------------------------------
 * APP FOLDER NAME
 *---------------------------------------------------------------
 *
 * Application folder name.
 *
 * NO TRAILING SLASH!
 */
if ( ! defined( 'DIR_APP' ) ) {
    define( 'DIR_APP', 'app' );
}

/*
 *---------------------------------------------------------------
 * CACHE FOLDER NAME
 *---------------------------------------------------------------
 *
 * Caching folder name.
 *
 * NO TRAILING SLASH!
 */
if ( ! defined( 'DIR_CACHE' ) ) {
    define( 'DIR_CACHE', 'cache' );
}

/*
 *---------------------------------------------------------------
 * STORAGE FOLDER NAME
 *---------------------------------------------------------------
 *
 * Storage folder name.
 *
 * NO TRAILING SLASH!
 */
if ( ! defined( 'DIR_STORAGE' ) ) {
    define( 'DIR_STORAGE', 'storage' );
}

/*
 *---------------------------------------------------------------
 * RESOURCES FOLDER NAME
 *---------------------------------------------------------------
 *
 * Resources folder name.
 *
 * NO TRAILING SLASH!
 */
if ( ! defined( 'DIR_RESOURCES' ) ) {
    define( 'DIR_RESOURCES', 'resources' );
}

/*
 *---------------------------------------------------------------
 * DATABASE FOLDER NAME
 *---------------------------------------------------------------
 *
 * Database folder name.
 *
 * NO TRAILING SLASH!
 */
if ( ! defined( 'DIR_DATABASE' ) ) {
    define( 'DIR_DATABASE', 'database' );
}

/*
 *---------------------------------------------------------------
 * PUBLIC FOLDER NAME
 *---------------------------------------------------------------
 *
 * Accessible folder by public.
 *
 * NO TRAILING SLASH!
 */
if ( ! defined( 'DIR_PUBLIC' ) ) {
    // cpanel based hosting
    if(is_dir('../public_html')) {
        define( 'DIR_PUBLIC', 'public' );
    } else {
        define( 'DIR_PUBLIC', 'public' );
    }
}

/*
 *---------------------------------------------------------------
 * DEFINE ROOT PATH
 *---------------------------------------------------------------
 */
define( 'PATH_ROOT', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );

/*
 *---------------------------------------------------------------
 * POINTING FRONT CONTROLLER DIRECTORY
 *---------------------------------------------------------------
 *
 * Ensure the current directory is pointing to the front controller's directory
 */
chdir( __DIR__ . DIRECTORY_SEPARATOR );

/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We'll require it
| into the script here so that we do not have to worry about the
| loading of any our classes "manually". Feels great to relax.
|
*/
require __DIR__ . '/vendor/autoload.php';

/*
 * ------------------------------------------------------
 * STARTUP O2SYSTEM
 * ------------------------------------------------------
 */
if ( class_exists( 'O2System\Framework', false ) ) {
    O2System\Framework::getInstance();
}
