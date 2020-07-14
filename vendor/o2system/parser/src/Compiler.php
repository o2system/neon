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

namespace O2System\Parser;

// ------------------------------------------------------------------------

use O2System\Spl\Patterns\Structural\Provider\AbstractProvider;

/**
 * Class Compiler
 *
 * @package O2System\Parser
 */
class Compiler extends AbstractProvider
{
    /**
     * Compiler::$config
     * 
     * Compiler Config
     *
     * @var DataStructures\Config
     */
    private $config;

    /**
     * Compiler::$sourceFilePath
     *
     * Compiler Source File Path
     *
     * @var string
     */
    private $sourceFilePath;

    /**
     * Compiler::$sourceFileDirectory
     *
     * Compiler Source File Directory
     *
     * @var string
     */
    private $sourceFileDirectory;

    /**
     * Compiler::$sourceString
     *
     * Compiler Source String
     *
     * @var string
     */
    private $sourceString;

    /**
     * Compiler::$vars
     *
     * Compiler Vars
     *
     * @var array
     */
    private $vars = [];

    /**
     * Compiler::$template
     *
     * @var \O2System\Parser\Template\Collection
     */
    private $template;

    /**
     * Compiler::$string
     *
     * @var \O2System\Parser\String\Collection
     */
    private $string;

    // ------------------------------------------------------------------------

    /**
     * Compiler::__construct
     *
     * @param DataStructures\Config $config
     */
    public function __construct(DataStructures\Config $config)
    {
        language()
            ->addFilePath(__DIR__ . DIRECTORY_SEPARATOR)
            ->loadFile('parser');

        $this->template = new Template\Collection();
        $this->string = new String\Collection();

        $this->config = $config;

        if ($this->config->offsetExists('template')) {
            if(is_array($this->config->template)) {
                foreach($this->config->template as $engine) {
                    $this->template->load($engine);
                }
            }
        }

        if ($this->config->offsetExists('string')) {
            if(is_array($this->config->string)) {
                foreach($this->config->string as $engine) {
                    $this->string->load($engine);
                }
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Compiler::getSourceString
     *
     * @return string
     */
    public function getSourceString()
    {
        return $this->sourceString;
    }

    // ------------------------------------------------------------------------

    /**
     * Compiler::loadFile
     *
     * @param string $filePath
     *
     * @return bool
     */
    public function loadFile($filePath)
    {
        if ($filePath instanceof \SplFileInfo) {
            $filePath = $filePath->getRealPath();
        }

        if (isset($this->sourceFileDirectory)) {
            if (is_file($this->sourceFileDirectory . $filePath)) {
                $filePath = $this->sourceFileDirectory . $filePath;
            }
        }

        if (is_file($filePath)) {
            $this->sourceFilePath = realpath($filePath);
            $this->sourceFileDirectory = pathinfo($this->sourceFilePath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR;

            return $this->loadString(file_get_contents($filePath));
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Compiler::loadString
     *
     * @param string $string
     *
     * @return bool
     */
    public function loadString($string)
    {
        $this->sourceString = $string;

        if ($this->config->allowPhpScripts === false) {
            $this->sourceString = preg_replace(
                '/<\\?.*(\\?>|$)/Us',
                '',
                str_replace('<?=', '<?php echo ', $this->sourceString)
            );
        }

        $this->sourceString = str_replace(
            [
                '__DIR__',
                '__FILE__',
            ],
            [
                "'" . $this->getSourceFileDirectory() . "'",
                "'" . $this->getSourceFilePath() . "'",
            ],
            $this->sourceString
        );

        return empty($this->sourceString);
    }

    // ------------------------------------------------------------------------

    /**
     * Compiler::getSourceFileDirectory
     *
     * @return string
     */
    public function getSourceFileDirectory()
    {
        return $this->sourceFileDirectory;
    }

    // ------------------------------------------------------------------------

    /**
     * Compiler::getSourceFilePath
     *
     * @return string
     */
    public function getSourceFilePath()
    {
        return $this->sourceFilePath;
    }

    // ------------------------------------------------------------------------

    /**
     * Compiler::parse
     *
     * @param array $vars
     *
     * @return bool|string Returns FALSE if failed.
     */
    public function parse(array $vars = [])
    {
        $this->loadVars($vars);

        $output = $this->parsePhp();

        // Run Template Engines
        $output = $this->template->parse($output, $this->vars);

        // Run String Engines
        $output = $this->string->parse($output, $this->vars);

        return $output;
    }

    // ------------------------------------------------------------------------

    /**
     * Compiler::parsePhp
     *
     * @param array $vars
     *
     * @return bool|string Returns FALSE if failed
     */
    public function parsePhp(array $vars = [])
    {
        if(count($vars)) {
            $this->loadVars($vars);
        }

        extract($this->vars);

        /*
         * Buffer the output
         *
         * We buffer the output for two reasons:
         * 1. Speed. You get a significant speed boost.
         * 2. So that the final rendered template can be post-processed by
         *  the output class. Why do we need post processing? For one thing,
         *  in order to show the elapsed page load time. Unless we can
         *  intercept the content right before it's sent to the browser and
         *  then stop the timer it won't be accurate.
         */
        ob_start();

        echo eval('?>' . str_replace([';?>', ')?>', ');?>'], ['; ?>', '); ?>', '); ?>'], $this->sourceString));

        $output = ob_get_contents();
        @ob_end_clean();

        $lastError = error_get_last();

        if (is_array($lastError)) {
            $this->errorFilePath = $this->getSourceFilePath();
        }

        return $output;
    }

    // ------------------------------------------------------------------------

    /**
     * Compiler::loadVars
     *
     * @param array $vars
     *
     * @return bool
     */
    public function loadVars(array $vars)
    {
        $this->vars = array_merge($this->vars, $vars);

        return (bool)empty($this->vars);
    }
}