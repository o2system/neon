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

namespace O2System\Gear;

// ------------------------------------------------------------------------

/**
 * O2System Gear Console
 *
 * @package O2System\Gear
 */
class Console
{
    /**
     * Console::LOG_MESSAGE
     *
     * @var int
     */
    const LOG_MESSAGE = 0;

    /**
     * Console::LOG_MESSAGE
     *
     * @var int
     */
    const INFO_MESSAGE = 1;

    /**
     * Console::LOG_MESSAGE
     *
     * @var int
     */
    const WARNING_MESSAGE = 2;

    /**
     * Console::LOG_MESSAGE
     *
     * @var int
     */
    const ERROR_MESSAGE = 3;

    /**
     * Console::LOG_MESSAGE
     *
     * @var int
     */
    const DEBUG_MESSAGE = 4;

    // ------------------------------------------------------------------------

    /**
     * Console::$label
     *
     * @var string
     */
    private $label;

    /**
     * Console::$expression
     *
     * @var mixed
     */
    private $expression;

    /**
     * Console::$messageType
     *
     * @var int
     */
    private $messageType;

    // ------------------------------------------------------------------------

    /**
     * Console::__construct
     *
     * @param string   $label
     * @param mixed    $expression
     * @param int      $messageType
     */
    public function __construct($label, $expression, $messageType = self::LOG_MESSAGE)
    {
        $this->label = $label;
        $this->expression = $expression;
        $this->messageType = $messageType;
    }

    // ------------------------------------------------------------------------

    /**
     * Console::send
     */
    public function send()
    {
        $this->expression = is_object($this->expression) || is_array($this->expression)
            ? 'JSON.parse(\'' . json_encode(
                $this->expression
            ) . '\')'
            : '\'' . $this->expression . '\'';

        echo '<script type="text/javascript">' . PHP_EOL;

        switch ($this->messageType) {
            default:
            case self::LOG_MESSAGE :
                $messageType = 'log';
                $backgroundColor = '#777777';
                $textColor = '#ffffff';
                break;
            case self::INFO_MESSAGE :
                $messageType = 'info';
                $backgroundColor = '#5bc0de';
                $textColor = '#ffffff';
                break;
            case self::WARNING_MESSAGE :
                $messageType = 'warn';
                $backgroundColor = '#f0ad4e';
                $textColor = '#ffffff';
                break;
            case self::ERROR_MESSAGE :
                $messageType = 'error';
                $backgroundColor = '#d9534f';
                $textColor = '#ffffff';
                break;
            case self::DEBUG_MESSAGE :
                $messageType = 'debug';
                $backgroundColor = '#333333';
                $textColor = '#ffffff';
                break;
        }

        if ( ! empty($this->label)) {
            echo "console." . $messageType . "('%c " . $this->label . " ', 'background: " . $backgroundColor . "; color: " . $textColor . "');" . PHP_EOL;
        }

        echo "console." . $messageType . "(" . $this->expression . ");" . PHP_EOL;

        echo '</script>' . PHP_EOL;
    }
}