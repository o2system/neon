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
 * Class Blade
 *
 * @package O2System\Parser\Template\Engines
 *
 * @todo    :
 *      1. @include (done)
 *      2. @each (done)
 *      3. @inject
 *      4. @stack
 *      5. @push
 *      6. @verbatim
 */
class Blade extends AbstractEngine
{
    use ConfigCollectorTrait;

    /**
     * Blade::$extensions
     *
     * List of blade file extensions.
     *
     * @var array
     */
    protected $extensions = [
        '.php',
        '.blade.php',
        '.phtml',
    ];

    /**
     * Blade::$vars
     *
     * Blade variables
     *
     * @var array
     */
    protected $vars = [];

    /**
     * Blade::$sections
     *
     * List of blade sections.
     *
     * @var array
     */
    protected $sections = [];

    // ------------------------------------------------------------------------

    /**
     * Blade::__construct
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
     * Blade::parseString
     *
     * @param string  $string
     * @param array   $vars
     *
     * @return bool|string
     */
    public function parseString($string, array $vars = [])
    {
        $this->vars =& $vars;

        // Collect sections with no closing
        $string = preg_replace_callback('/@section((.*),(.*))/', [&$this, 'collectSection'], $string);

        // Collect sections with @show closing
        $string = preg_replace_callback(
            '/@section(.*)\s+(.*)\s+@show/',
            [&$this, 'collectSectionWithShow'],
            $string
        );

        // Collect sections with @endsection closing
        $string = preg_replace_callback(
            '/@section(.*)\s+(.*)\s+@endsection/',
            [&$this, 'collectSectionWithEnd'],
            $string
        );

        // Collect sections with @stop closing
        $string = preg_replace_callback(
            '/@section(.*)\s+(.*)\s+@stop/',
            [&$this, 'collectSectionWithEnd'],
            $string
        );

        // Collect sections with @overwrite closing
        $string = preg_replace_callback(
            '/@section(.*)\s+(.*)\s+@overwrite/',
            [&$this, 'collectSectionWithEnd'],
            $string
        );

        // Collect sections with @parent
        $string = preg_replace_callback(
            '/@section(.*)\s+@parent\s+(.*)\s+@endsection/',
            [&$this, 'collectSectionWithParent'],
            $string
        );

        // Remove blank lines
        $string = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $string);

        return $this->replaceString($string);
    }

    // ------------------------------------------------------------------------

    /**
     * Blade::replaceString
     *
     * @param string $string
     *
     * @return bool|string
     */
    private function replaceString($string)
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
                    '{{ $GLOBALS.%% }}',
                    '{{ $_SERVER }}',
                    '{{ $_SERVER[%%] }}',
                    '{{ $_SERVER.%% }}',
                    '{{ $_GET }}',
                    '{{ $_GET[%%] }}',
                    '{{ $_GET.%% }}',
                    '{{ $_POST }}',
                    '{{ $_POST[%%] }}',
                    '{{ $_POST.%% }}',
                    '{{ $_FILES }}',
                    '{{ $_FILES[%%] }}',
                    '{{ $_FILES.%% }}',
                    '{{ $_COOKIE }}',
                    '{{ $_COOKIE[%%] }}',
                    '{{ $_COOKIE.%% }}',
                    '{{ $_SESSION }}',
                    '{{ $_SESSION[%%] }}',
                    '{{ $_SESSION.%% }}',
                    '{{ $_REQUEST }}',
                    '{{ $_REQUEST[%%] }}',
                    '{{ $_REQUEST.%% }}',
                    '{{ $_ENV }}',
                    '{{ $_ENV[%%] }}',
                    '{{ $_ENV.%% }}',
                ],
                '',
                $string
            );
        }

        // php logical codes
        $logicalCodes = [
            '@if(%%)'        => '<?php if(\1): ?>',
            '@elseif(%%)'    => '<?php elseif(\1): ?>',
            '@endif'         => '<?php endif; ?>',
            '@else'          => '<?php else: ?>',
            '@unless(%%)'    => '<?php if(\1): ?>',
            '@endunless'     => '<?php endif; ?>',

            // with spaces
            '@if( %% )'      => '<?php if(\1): ?>',
            '@elseif( %% )'  => '<?php elseif(\1): ?>',
            '@unless( %% )'  => '<?php if(\1): ?>',
            '@if (%%)'       => '<?php if(\1): ?>',
            '@elseif (%%)'   => '<?php elseif(\1): ?>',
            '@unless (%%)'   => '<?php if(\1): ?>',
            '@if ( %% )'     => '<?php if(\1): ?>',
            '@elseif ( %% )' => '<?php elseif(\1): ?>',
            '@unless ( %% )' => '<?php if(\1): ?>',
        ];

        // php loop codes
        $loopCodes = [
            '@foreach(%%)'  => '<?php foreach(\1): ?>',
            '@endforeach'   => '<?php endforeach; ?>',
            '@for(%%)'      => '<?php for(\1): ?>',
            '@endfor'       => '<?php endfor; ?>',
            '@while(%%)'    => '<?php while(\1): ?>',
            '@endwhile'     => '<?php endwhile; ?>',
            '@continue'     => '<?php continue; ?>',
            '@break'        => '<?php break; ?>',

            // with spaces
            '@foreach (%%)' => '<?php foreach(\1): ?>',
            '@for (%%)'     => '<?php for(\1): ?>',
            '@while (%%)'   => '<?php while(\1): ?>',
        ];

        // php function codes
        $functionsCodes = [
            '@lang(%%)'           => '<?php echo $language->getLine(\1); ?>',
            '@include(%%)'        => '<?php echo $this->parseFile(\1); ?>',
            '@include(%%, %%)'    => '<?php echo $this->parseFile(\1, \2); ?>',
            '@yield(%%)'          => '<?php echo $this->sections[\1]; ?>',
            '@each(%%, %%, %%)'   => '<?php echo $this->parsePartials(\1, \2, \3); ?>',
            '@extends(%%)'        => '@extends not supported',
            '@choice(%%,%%)'      => '@choice not supported',

            // with spaces
            '@lang (%%)'          => '<?php echo $language->getLine(\1); ?>',
            '@include (%%)'       => '<?php echo $this->parseFile(\1); ?>',
            '@include (%%, %%)'   => '<?php echo $this->parseFile(\1, \2); ?>',
            '@yield (%%)'         => '<?php echo $this->sections[\1]; ?>',
            '@each (%%, %%, %%)'  => '<?php echo $this->parsePartials(\1, \2, \3); ?>',
            '@extends (%%)'       => '@extends not supported',
            '@choice (%%,%%)'     => '@choice not supported',
            '@lang( %% )'         => '<?php echo $language->getLine(\1); ?>',
            '@include( %% )'      => '<?php echo $this->parseFile(\1); ?>',
            '@include( %%, %% )'  => '<?php echo $this->parseFile(\1, \2); ?>',
            '@yield( %% )'        => '<?php echo $this->sections[\1]; ?>',
            '@each( %%, %%, %% )' => '<?php echo $this->parsePartials(\1, \2, \3); ?>',
            '@extends( %% )'      => '@extends not supported',
            '@choice( %%,%% )'    => '@choice not supported',
        ];

        if ($this->config[ 'allowPhpFunctions' ] === false) {
            $functionsCodes[ '@%%(%%)' ] = '';
        } elseif (is_array($this->config[ 'allowPhpFunctions' ]) AND count(
                $this->config[ 'allowPhpFunctions' ]
            ) > 0
        ) {
            foreach ($this->config[ 'allowPhpFunctions' ] as $functionName) {
                $functionsCodes[ '@' . $functionName . '(%%)' ] = '<?php echo ' . $functionName . '(\1); ?>';
            }
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
            '{{$%%}}'            => '<?php echo (!empty($\1) ? $\1 : ""); ?>',
            '{{/*}}'             => '<?php /*',
            '{{*/}}'             => '*/ ?>',
            '{{%%}}'             => '<?php echo (\1); ?>',
            '{{!! $%% !!}}'      => '<?php echo htmlentities($\1, ENT_HTML5); ?>',
            '{{-- %% --}}'       => '',

            // with spaces
            '{{ %% ? %% : %% }}' => '<?php echo (\1 ? \2 : \3); ?>',
            '{{ %% or %% }}'     => '<?php echo ( empty(\1) ? \2 : \1 ); ?>',
            '{{ %% || %% }}'     => '<?php echo ( empty(\1) ? \2 : \1 ); ?>',
            '{{ $%%->%%(%%) }}'  => '<?php echo $\1->\2(\3); ?>',
            '{{ $%%->%% }}'      => '<?php echo @$\1->\2; ?>',
            '{{ $%%[%%] }}'      => '<?php echo @$\1[\2]; ?>',
            '{{ $%%.%% }}'       => '<?php echo @$\1[\2]; ?>',
            '{{ $%% = %% }}'     => '<?php $\1 = \2; ?>',
            '{{ $%%++ }}'        => '<?php $\1++; ?>',
            '{{ $%%-- }}'        => '<?php $\1--; ?>',
            '{{ $%% }}'          => '<?php echo (!empty($\1) ? $\1 : ""); ?>',
            '{{ /* }}'           => '<?php /*',
            '{{ */ }}'           => '*/ ?>',
            '{{ %% }}'           => '<?php echo (\1); ?>',
        ];

        if ($this->config[ 'allowPhpConstants' ] === true) {
            $constantsVariables = get_defined_constants(true);

            if ( ! empty($constantsVariables[ 'user' ])) {
                foreach ($constantsVariables[ 'user' ] as $constant => $value) {
                    $variablesCodes[ '{{ ' . $constant . ' }}' ] = '<?php echo ' . $constant . '; ?>';
                }
            }
        }

        $phpCodes = array_merge($logicalCodes, $loopCodes, $functionsCodes, $variablesCodes);

        $patterns = $replace = [];
        foreach ($phpCodes as $tplCode => $phpCode) {
            $patterns[] = '#' . str_replace('%%', '(.+)', preg_quote($tplCode, '#')) . '#U';
            $replace[] = $phpCode;
        }

        /*replace our pseudo language in template with php code*/
        $string = preg_replace($patterns, $replace, $string);

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

        echo eval('?>' . preg_replace('/;*\s*\?>/', '; ?>', $string));

        $output = ob_get_contents();
        @ob_end_clean();

        return $output;
    }

    // ------------------------------------------------------------------------

    /**
     * Blade::collectSection
     *
     * @param string $match
     *
     * @return string
     */
    private function collectSection($match)
    {
        $section = str_replace(['\'', '(', ')'], '', trim($match[ 1 ]));
        $xSection = explode(',', $section);
        $xSection = array_map('trim', $xSection);

        $this->sections[ $xSection[ 0 ] ] = $this->replaceString($xSection[ 1 ]);

        return '';
    }

    // ------------------------------------------------------------------------

    /**
     * Blade::collectSectionWithShow
     *
     * @param string $match
     *
     * @return string
     */
    private function collectSectionWithShow($match)
    {
        $offset = str_replace(['(\'', '\')'], '', trim($match[ 1 ]));
        $this->sections[ $offset ] = $this->replaceString($match[ 2 ]);

        return '@yield(\'' . $offset . '\')';
    }

    // ------------------------------------------------------------------------

    /**
     * Blade::collectSectionWithEnd
     *
     * @param string $match
     *
     * @return string
     */
    private function collectSectionWithEnd($match)
    {
        $offset = str_replace(['(\'', '\')'], '', trim($match[ 1 ]));
        $content = trim($match[ 2 ]);

        $this->sections[ $offset ] = $this->replaceString($content);

        return '';
    }

    // ------------------------------------------------------------------------

    /**
     * Blade::collectSectionWithParent
     *
     * @param string $match
     *
     * @return string
     */
    private function collectSectionWithParent($match)
    {
        $offset = str_replace(['(\'', '\')'], '', trim($match[ 1 ]));
        $content = $this->replaceString($match[ 2 ]);

        if (isset($this->sections[ $offset ])) {
            $this->sections[ $offset ] .= PHP_EOL . $content;
        } else {
            $this->sections[ $offset ] = $content;
        }

        return '';
    }
}