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

namespace O2System\Parser\Template\Engines;

// ------------------------------------------------------------------------

use O2System\Parser\Template\Abstracts\AbstractEngine;
use O2System\Spl\Traits\Collectors\ConfigCollectorTrait;

/**
 * Class Noodle
 *
 * @package O2System\Parser\Template\Engines
 */
class Noodle extends AbstractEngine
{
    use ConfigCollectorTrait;

    /**
     * Noodle::$extensions
     *
     * List of noodle file extensions.
     *
     * @var array
     */
    protected $extensions = [
        '.php',
        '.htm',
        '.html',
        '.noodle.php',
        '.noodle.phtml',
        '.phtml',
    ];

    // ------------------------------------------------------------------------

    /**
     * Noodle::__construct
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'allowPhpGlobals'   => true,
            'allowPhpFunctions' => true,
            'allowPhpConstants' => true,
        ], $config);
    }

    // ------------------------------------------------------------------------

    /**
     * Noodle::parseString
     *
     * @param string $string
     * @param array  $vars
     *
     * @return false|string Returns FALSE if failed.
     * @throws \Exception
     */
    public function parseString($string, array $vars = [])
    {
        if ($this->config[ 'allowPhpGlobals' ] === false) {
            $string = str_replace(
                [
                    '{{$GLOBALS}}',
                    '{{$GLOBALS[%%]}}',
                    '{{$_SERVER}}',
                    '{{$_SERVER[%%]}}',
                    '{{$_GET}}',
                    '{{$_GET[%%]}}',
                    '{{$_POST}}',
                    '{{$_POST[%%]}}',
                    '{{$_FILES}}',
                    '{{$_FILES[%%]}}',
                    '{{$_COOKIE}}',
                    '{{$_COOKIE[%%]}}',
                    '{{$_SESSION}}',
                    '{{$_SESSION[%%]}}',
                    '{{$_REQUEST}}',
                    '{{$_REQUEST[%%]}}',
                    '{{$_ENV}}',
                    '{{$_ENV[%%]}}',

                    // with spaces
                    '{{ $GLOBALS }}',
                    '{{ $GLOBALS[%%] }}',
                    '{{ $_SERVER }}',
                    '{{ $_SERVER[%%] }}',
                    '{{ $_GET }}',
                    '{{ $_GET[%%] }}',
                    '{{ $_POST }}',
                    '{{ $_POST[%%] }}',
                    '{{ $_FILES }}',
                    '{{ $_FILES[%%] }}',
                    '{{ $_COOKIE }}',
                    '{{ $_COOKIE[%%] }}',
                    '{{ $_SESSION }}',
                    '{{ $_SESSION[%%] }}',
                    '{{ $_REQUEST }}',
                    '{{ $_REQUEST[%%] }}',
                    '{{ $_ENV }}',
                    '{{ $_ENV[%%] }}',
                ],
                '',
                $string
            );
        }

        // php logical codes
        $logicalCodes = [
            '{{if(%%)}}'       => '<?php if(\1): ?>',
            '{{elseif(%%)}}'   => '<?php elseif(\1): ?>',
            '{{/if}}'          => '<?php endif; ?>',
            '{{endif}}'        => '<?php endif; ?>',
            '{{else}}'         => '<?php else: ?>',
            '{{unless(%%)}}'   => '<?php if(\1): ?>',
            '{{endunless}}'    => '<?php endif; ?>',

            // with spaces
            '{{ if(%%) }}'     => '<?php if(\1): ?>',
            '{{ elseif(%%) }}' => '<?php elseif(\1): ?>',
            '{{ /if }}'        => '<?php endif; ?>',
            '{{ endif }}'      => '<?php endif; ?>',
            '{{ else }}'       => '<?php else: ?>',
            '{{ unless(%%) }}' => '<?php if(\1): ?>',
            '{{ endunless }}'  => '<?php endif; ?>',
        ];

        // php loop codes
        $loopCodes = [
            '{{for(%%)}}'       => '<?php for(\1): ?>',
            '{{/for}}'          => '<?php endfor; ?>',
            '{{endfor}}'        => '<?php endfor; ?>',
            '{{foreach(%%)}}'   => '<?php foreach(\1): ?>',
            '{{/foreach}}'      => '<?php endforeach; ?>',
            '{{endforeach}}'    => '<?php endforeach; ?>',
            '{{while(%%)}}'     => '<?php while(\1): ?>',
            '{{/while}}'        => '<?php endwhile; ?>',
            '{{endwhile}}'      => '<?php endwhile; ?>',
            '{{continue}}'      => '<?php continue; ?>',
            '{{break}}'         => '<?php break; ?>',

            // with spaces
            '{{ for(%%) }}'     => '<?php for(\1): ?>',
            '{{ /for }}'        => '<?php endfor; ?>',
            '{{ endfor }}'      => '<?php endfor; ?>',
            '{{ foreach(%%) }}' => '<?php foreach(\1): ?>',
            '{{ /foreach }}'    => '<?php endforeach; ?>',
            '{{ endforeach }}'  => '<?php endforeach; ?>',
            '{{ while(%%) }}'   => '<?php while(\1): ?>',
            '{{ /while }}'      => '<?php endwhile; ?>',
            '{{ endwhile }}'    => '<?php endwhile; ?>',
            '{{ continue }}'    => '<?php continue; ?>',
            '{{ break }}'       => '<?php break; ?>',
        ];

        // php function codes
        $functionsCodes = [];
        if ($this->config[ 'allowPhpFunctions' ] === false) {
            $functionsCodes = [
                '{{%%(%%)}}' => '',
            ];
        } elseif (is_array($this->config[ 'allowPhpFunctions' ]) AND count(
                $this->config[ 'allowPhpFunctions' ]
            ) > 0
        ) {
            foreach ($this->config[ 'allowPhpFunctions' ] as $function_name) {
                if (function_exists($function_name)) {
                    $functionsCodes[ '{{' . $function_name . '(%%)}}' ] = '<?php echo ' . $function_name . '(\1); ?>';
                }
            }
        } else {
            $functionsCodes = [
                '{{%%()}}'               => '<?php echo \1(); ?>',
                '{{%%(%%)}}'             => '<?php echo \1(\2); ?>',
                '{{lang(%%)}}'           => '<?php echo $language->getLine(\1); ?>',
                '{{each(%%, %%, %%)}}'   => '<?php echo $this->parsePartials(\1, \2, \3); ?>',
                '{{include(%%)}}'        => '<?php echo $this->parseFile(\1); ?>',
                '{{include(%%, %%)}}'    => '<?php echo $this->parseFile(\1, \2); ?>',

                // with spaces
                '{{ %%() }}'             => '<?php echo \1(); ?>',
                '{{ %%(%%) }}'           => '<?php echo \1(\2); ?>',
                '{{ lang(%%) }}'         => '<?php echo $language->getLine(\1); ?>',
                '{{ each(%%, %%, %%) }}' => '<?php echo $this->parsePartials(\1, \2, \3); ?>',
                '{{ include(%%) }}'      => '<?php echo $this->parseFile(\1); ?>',
                '{{ include(%%, %%) }}'  => '<?php echo $this->parseFile(\1, \2); ?>',
            ];
        }

        // php variables codes
        $variablesCodes = [
            '{{%% ? %% : %%}}'   => '<?php echo (\1 ? \2 : \3); ?>',
            '{{%% or %%}}'       => '<?php echo ( empty(\1) ? \2 : \1 ); ?>',
            '{{%% || %%}}'       => '<?php echo ( empty(\1) ? \2 : \1 ); ?>',
            '{{$%%->%%(%%)}}'    => '<?php echo $\1->\2(\3); ?>',
            '{{$%%->%%}}'        => '<?php echo @$\1->\2; ?>',
            '{{$%%[%%]}}'        => '<?php echo @$\1[\2]; ?>',
            '{{$%%.%%}}'         => '<?php echo @$\1[\2]; ?>',
            '{{$%% = %%}}'       => '<?php $\1 = \2; ?>',
            '{{$%%++}}'          => '<?php $\1++; ?>',
            '{{$%%--}}'          => '<?php $\1--; ?>',
            '{{$%%}}'            => '<?php echo (isset($\1) ? $\1 : ""); ?>',
            '{{/*}}'             => '<?php /*',
            '{{*/}}'             => '*/ ?>',
            '{{!!$%%!!}}'        => '<?php echo htmlentities($\1, ENT_HTML5); ?>',
            '{{--%%--}}'         => '',

            // with spaces
            '{{ %% ? %% : %% }}' => '<?php echo (\1 ? \2 : \3); ?>',
            '{{ %% or %% }}'     => '<?php echo ( empty(\1) ? \'\2\' : \1 ); ?>',
            '{{ %% || %% }}'     => '<?php echo ( empty(\1) ? \'\2\' : \1 ); ?>',
            '{{ $%%->%%(%%) }}'  => '<?php echo $\1->\2(\3); ?>',
            '{{ $%%->%% }}'      => '<?php echo $\1->\2; ?>',
            '{{ $%%[%%] }}'      => '<?php echo $\1->\2; ?>',
            '{{ $%%.%% }}'       => '<?php echo $\1->\2; ?>',
            '{{ $%% = %% }}'     => '<?php $\1 = \2; ?>',
            '{{ $%%++ }}'        => '<?php $\1++; ?>',
            '{{ $%%-- }}'        => '<?php $\1--; ?>',
            '{{ $%% }}'          => '<?php echo (isset($\1) ? $\1 : ""); ?>',
            '{{ /* }}'           => '<?php /*',
            '{{ */ }}'           => '*/ ?>',
            '{{ !!$%%!! }}'      => '<?php echo htmlentities($\1, ENT_HTML5); ?>',
            '{{ --%%-- }}'       => '',
        ];

        if ($this->config[ 'allowPhpConstants' ] === true) {
            $constantsVariables = get_defined_constants(true);

            if ( ! empty($constantsVariables[ 'user' ])) {
                foreach ($constantsVariables[ 'user' ] as $constant => $value) {
                    if (defined($constant)) {
                        $variablesCodes[ '{{' . $constant . '}}' ] = '<?php echo ' . $constant . '; ?>';
                    }
                }
            }
        }

        $phpCodes = array_merge($logicalCodes, $loopCodes, $variablesCodes, $functionsCodes);

        $patterns = $replace = [];
        foreach ($phpCodes as $tplCode => $phpCode) {
            $patterns[] = '#' . str_replace('%%', '(.+)', preg_quote($tplCode, '#')) . '#U';
            $replace[] = $phpCode;
        }

        /*replace our pseudo language in template with php code*/
        $string = preg_replace($patterns, $replace, $string);

        extract($vars);

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

        try {
            echo @eval('?>' . @preg_replace('/;*\s*\?>/', '; ?>', $string));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);

        }

        $output = ob_get_contents();
        @ob_end_clean();

        return $output;
    }
}