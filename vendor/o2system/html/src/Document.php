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

namespace O2System\Html;

// ------------------------------------------------------------------------

/**
 * Class Document
 *
 * @package O2System\HTML
 */
class Document extends \DOMDocument
{
    /**
     * Document Meta Nodes
     *
     * @var \O2System\Html\Dom\Lists\Meta
     */
    public $metaNodes;

    /**
     * Document Link Nodes
     *
     * @var \O2System\Html\Dom\Lists\Asset
     */
    public $linkNodes;

    /**
     * Document Style Content
     *
     * @var \O2System\Html\Dom\Style
     */
    public $styleContent;

    /**
     * Document Script Nodes
     *
     * @var \O2System\Html\Dom\Lists\Asset
     */
    public $headScriptNodes;

    /**
     * Document Script Content
     *
     * @var \O2System\Html\Dom\Script
     */
    public $headScriptContent;

    /**
     * Document Script Nodes
     *
     * @var \O2System\Html\Dom\Lists\Asset
     */
    public $bodyScriptNodes;

    /**
     * Document Script Content
     *
     * @var \O2System\Html\Dom\Script
     */
    public $bodyScriptContent;

    // ------------------------------------------------------------------------

    /**
     * Document::__construct
     *
     * @param string $version  Document version.
     * @param string $encoding Document encoding.
     *
     * @return Document
     */
    public function __construct($version = '1.0', $encoding = 'UTF-8')
    {
        language()
            ->addFilePath(__DIR__ . DIRECTORY_SEPARATOR)
            ->loadFile('html');

        parent::__construct($version, $encoding);

        $this->registerNodeClass('DOMElement', '\O2System\Html\Dom\Element');
        $this->registerNodeClass('DOMAttr', '\O2System\Html\Dom\Attr');

        $this->formatOutput = true;

        $this->metaNodes = new Dom\Lists\Meta($this);

        $this->linkNodes = new Dom\Lists\Asset($this);
        $this->linkNodes->element = 'link';

        $this->styleContent = new Dom\Style();

        $this->headScriptNodes = new Dom\Lists\Asset($this);
        $this->headScriptNodes->element = 'script';
        $this->headScriptContent = new Dom\Script();

        $this->bodyScriptNodes = new Dom\Lists\Asset($this);
        $this->bodyScriptNodes->element = 'script';
        $this->bodyScriptContent = new Dom\Script();

        $this->loadHTMLTemplate();
    }

    // ------------------------------------------------------------------------

    /**
     * Document::loadHTMLTemplate
     *
     * Load HTML template from a file.
     *
     * @return void
     */
    protected function loadHTMLTemplate()
    {
        $htmlTemplate = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>O2System HTML</title>
</head>
<body>
</body>
</html>
HTML;

        parent::loadHTML($htmlTemplate);
    }

    // ------------------------------------------------------------------------

    /**
     * Document::__get
     *
     * @param string $tagName The document tag element.
     *
     * @return mixed The value at the specified index or false.
     */
    public function &__get($tagName)
    {
        $getDocument[ $tagName ] = null;

        if (in_array($tagName, ['html', 'head', 'body', 'title'])) {
            $getDocument[ $tagName ] = $this->getElementsByTagName($tagName)->item(0);
        }

        return $getDocument[ $tagName ];
    }

    // ------------------------------------------------------------------------

    /**
     * Document::saveHTMLFile
     *
     * Dumps the internal document into a file using HTML formatting
     *
     * @see   http://php.net/manual/domdocument.savehtmlfile.php
     *
     * @param string $filePath <p>
     *                         The path to the saved HTML document.
     *                         </p>
     *
     * @return int the number of bytes written or false if an error occurred.
     * @since 5.0
     */
    public function saveHTMLFile($filePath)
    {
        if ( ! is_string($filePath)) {
            throw new \InvalidArgumentException('The filename argument must be of type string');
        }

        if ( ! is_writable($filePath)) {
            return false;
        }

        $result = $this->saveHTML();
        file_put_contents($filePath, $result);
        $bytesWritten = filesize($filePath);

        if ($bytesWritten === strlen($result)) {
            return $bytesWritten;
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Document::saveHTML
     *
     * Dumps the internal document into a string using HTML formatting.
     *
     * @see   http://php.net/manual/domdocument.savehtml.php
     *
     * @param \DOMNode $node [optional] parameter to output a subset of the document.
     *
     * @return string the HTML, or false if an error occurred.
     * @since 5.0
     */
    public function saveHTML(\DOMNode $node = null)
    {
        $headElement = $this->getElementsByTagName('head')->item(0);

        $styleContent = trim($this->styleContent->__toString());

        if ( ! empty($styleContent)) {
            $styleElement = $this->createElement('style', $styleContent);
            $styleElement->setAttribute('type', 'text/css');
            $headElement->appendChild($styleElement);
        }

        $titleElement = $this->getElementsByTagName('title')->item(0);

        // Insert Meta
        if ($this->metaNodes->count()) {
            $metaNodes = array_reverse($this->metaNodes->getArrayCopy());

            foreach ($metaNodes as $metaNode) {
                $headElement->insertBefore($this->importNode($metaNode), $titleElement);
            }
        }

        // Insert Link
        if ($this->linkNodes->count()) {
            foreach ($this->linkNodes as $linkNode) {
                $headElement->appendChild($this->importNode($linkNode));
            }
        }

        // Insert Head Script
        if (count($this->headScriptNodes)) {
            foreach ($this->headScriptNodes as $scriptNode) {
                $headElement->appendChild($this->importNode($scriptNode));
            }
        }

        $headScriptContent = trim($this->headScriptContent->__toString());

        if ( ! empty($headScriptContent)) {
            $scriptElement = $this->createElement('script', $headScriptContent);
            $scriptElement->setAttribute('type', 'text/javascript');
            $headElement->appendChild($scriptElement);
        }

        $bodyElement = $this->getElementsByTagName('body')->item(0);

        // Insert Body Script
        if (count($this->bodyScriptNodes)) {
            foreach ($this->bodyScriptNodes as $scriptNode) {
                $bodyElement->appendChild($this->importNode($scriptNode));
            }
        }

        $bodyScriptContent = trim($this->bodyScriptContent->__toString());

        if ( ! empty($bodyScriptContent)) {
            $scriptElement = $this->createElement('script', $bodyScriptContent);
            $scriptElement->setAttribute('type', 'text/javascript');
            $bodyElement->appendChild($scriptElement);
        }

        $output = parent::saveHTML($node);

        if ($this->formatOutput === true) {
            $beautifier = new Dom\Beautifier();
            $output = $beautifier->format($output);
        }

        return (string)$output;
    }

    // ------------------------------------------------------------------------

    /**
     * Document::find
     *
     * JQuery style document expression finder.
     *
     * @param string $expression String of document expression.
     *
     * @return Dom\Lists\Nodes
     */
    public function find($expression)
    {
        $xpath = new Dom\XPath($this);

        $xpath->registerNamespace("php", "http://php.net/xpath");
        $xpath->registerPhpFunctions();

        return $xpath->query($expression);
    }

    // ------------------------------------------------------------------------

    /**
     * Document::importSourceNode
     *
     * Import HTML source code into document.
     *
     * @param string $source HTML Source Code.
     *
     * @return \DOMNode|\O2System\Html\Dom\Element
     */
    public function importSourceNode($source)
    {
        $DOMDocument = new self();
        $DOMDocument->loadHTML($source);

        $this->metaNodes->import($DOMDocument->metaNodes);
        $this->headScriptNodes->import($DOMDocument->headScriptNodes);
        $this->bodyScriptNodes->import($DOMDocument->bodyScriptNodes);
        $this->linkNodes->import($DOMDocument->linkNodes);
        $this->styleContent->import($DOMDocument->styleContent);
        $this->headScriptContent->import($DOMDocument->headScriptContent);
        $this->bodyScriptContent->import($DOMDocument->bodyScriptContent);

        $bodyElement = $DOMDocument->getElementsByTagName('body')->item(0);

        if ($bodyElement->firstChild instanceof Dom\Element) {
            return $bodyElement->firstChild;
        } elseif ($bodyElement->firstChild instanceof \DOMText) {
            foreach ($bodyElement->childNodes as $childNode) {
                if ($childNode instanceof Dom\Element) {
                    return $childNode->cloneNode(true);
                    break;
                }
            }
        }

        return $bodyElement;
    }

    // ------------------------------------------------------------------------

    /**
     * Document::loadHTML
     *
     * Load HTML from a string.
     *
     * @see   http://php.net/manual/domdocument.loadhtml.php
     *
     * @param string     $source  <p>
     *                            The HTML string.
     *                            </p>
     * @param int|string $options [optional] <p>
     *                            Since PHP 5.4.0 and Libxml 2.6.0, you may also
     *                            use the options parameter to specify additional Libxml parameters.
     *                            </p>
     *
     * @return bool true on success or false on failure. If called statically, returns a
     * DOMDocument and issues E_STRICT
     * warning.
     * @since 5.0
     */
    public function loadHTML($source, $options = 0)
    {
        // Enables libxml errors handling
        $internalErrorsOptionValue = libxml_use_internal_errors();

        if ($internalErrorsOptionValue === false) {
            libxml_use_internal_errors(true);
        }

        $source = $this->parseHTML($source);

        $DOMDocument = new \DOMDocument();
        $DOMDocument->formatOutput = true;
        $DOMDocument->preserveWhiteSpace = false;

        if ($this->encoding === 'UTF-8') {
            if (function_exists('mb_convert_encoding')) {
                $source = mb_convert_encoding($source, 'HTML-ENTITIES', 'UTF-8');
            } else {
                $source = utf8_decode($source);
            }

            $DOMDocument->encoding = 'UTF-8';
        }

        if (empty($source)) {
            return false;
        }

        $DOMDocument->loadHTML($source, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $headElement = $this->getElementsByTagName('head')->item(0);
        $bodyElement = $this->getElementsByTagName('body')->item(0);

        // Import head attributes
        if (null !== ($sourceHeadElement = $DOMDocument->getElementsByTagName('head')->item(0))) {
            if ($sourceHeadElement->attributes->length > 0) {
                foreach ($sourceHeadElement->attributes as $attribute) {
                    $headElement->setAttribute($attribute->name, $attribute->value);
                }
            }
        }

        // Import body attributes and child nodes
        if (null !== ($sourceBodyElement = $DOMDocument->getElementsByTagName('body')->item(0))) {
            // Import body attributes
            if ($sourceBodyElement->attributes->length > 0) {
                foreach ($sourceBodyElement->attributes as $attribute) {
                    $bodyElement->setAttribute($attribute->name, $attribute->value);
                }
            }

            // Import body child nodes
            foreach ($sourceBodyElement->childNodes as $childNode) {
                $childNode = $this->importNode($childNode, true);
                $bodyElement->appendChild($childNode);
            }
        } elseif ($bodyChildNode = $this->importNode($DOMDocument->firstChild, true)) {
            $bodyElement->appendChild($bodyChildNode);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Document::parseHTML
     *
     * Parse HTML Source Code.
     *
     * @param string $source HTML Source Code.
     *
     * @return mixed
     */
    private function parseHTML($source)
    {
        $DOMDocument = new \DOMDocument();

        // Has inline script element
        if (preg_match_all('/<script((?:(?!src=).)*?)>(.*?)<\/script>/smix', $source, $matches)) {
            if (isset($matches[ 2 ])) {
                foreach ($matches[ 2 ] as $match) {
                    $script = trim($match);
                    $this->bodyScriptContent[ md5($script) ] = $script . PHP_EOL;
                }
            }
        }

        // Remove all inline script first
        $source = preg_replace('/<script((?:(?!src=).)*?)>(.*?)<\/script>/smix', '', $source);

        $DOMDocument->loadHTML($source);

        $DOMXPath = new \DOMXPath($DOMDocument);
        $metas = $DOMXPath->query('//meta');
        foreach ($metas as $meta) {
            $attributes = [];
            foreach ($meta->attributes as $name => $attribute) {
                $attributes[ $name ] = $attribute->nodeValue;
            }

            $this->metaNodes->createElement($attributes);
        }

        $source = preg_replace('#<meta(.*?)>#is', '', $source); // clean up all inline meta tags

        $links = $DOMXPath->query('//link'); // find all inline link tags
        foreach ($links as $link) {
            $attributes = [];
            foreach ($link->attributes as $name => $attribute) {
                $attributes[ $name ] = $attribute->nodeValue;
            }

            $this->linkNodes->createElement($attributes);
        }

        $source = preg_replace('#<link(.*?)>#is', '', $source); // clean up all inline meta tags

        $scripts = $DOMXPath->query('//head/script'); // find all inline script tags
        foreach ($scripts as $script) {
            $attributes = [];
            foreach ($script->attributes as $name => $attribute) {
                $attributes[ $name ] = $attribute->nodeValue;
            }
        }

        $scripts = $DOMXPath->query('//body/script'); // find all inline script tags
        foreach ($scripts as $script) {
            $attributes = [];
            foreach ($script->attributes as $name => $attribute) {
                $attributes[ $name ] = $attribute->nodeValue;
            }

            if ($script->textContent == '') {
                $this->bodyScriptNodes->createElement($attributes);
            }
        }

        $source = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $source);

        // Has inline style Element
        if (preg_match_all('/((<[\\s\\/]*style\\b[^>]*>)([^>]*)(<\\/style>))/i', $source, $matches)) {
            if (isset($matches[ 3 ])) {
                foreach ($matches[ 3 ] as $match) {
                    $style = trim($match);
                    $this->styleContent[ md5($style) ] = $style . PHP_EOL;
                }
            }

            $source = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $source);
        }

        $codes = $DOMXPath->query('//code');
        foreach ($codes as $code) {
            if ($code->textContent == '') {
                $code = str_replace(['{{php', '/php}}'], ['<?php', '?>'], $code->textContent);
                $code = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\r\n", $code);
                $source = str_replace($code, htmlentities($code), $source);
            }
        }

        $source = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\r\n", $source);

        return $source;
    }

    // ------------------------------------------------------------------------

    /**
     * Document::load
     *
     * Load HTML from a file.
     *
     * @link  http://php.net/manual/domdocument.load.php
     *
     * @param string     $filePath <p>
     *                             The path to the HTML document.
     *                             </p>
     * @param int|string $options  [optional] <p>
     *                             Bitwise OR
     *                             of the libxml option constants.
     *                             </p>
     *
     * @return mixed true on success or false on failure. If called statically, returns a
     * DOMDocument and issues E_STRICT
     * warning.
     * @since 5.0
     */
    public function load($filePath, $options = null)
    {
        if (file_exists($filePath)) {
            return $this->loadHTMLFile($filePath, $options);
        } elseif (is_string($filePath)) {
            return $this->loadHTML($filePath, $options);
        } elseif ( ! empty($filePath)) {
            return parent::load($filePath, $options);
        }
    }

    /**
     * Document::loadHTMLFile
     *
     * Load HTML from a file.
     *
     * @see   http://php.net/manual/domdocument.loadhtmlfile.php
     *
     * @param string     $filePath <p>
     *                             The path to the HTML file.
     *                             </p>
     * @param int|string $options  [optional] <p>
     *
     * Since PHP 5.4.0 and Libxml 2.6.0, you may also
     * use the options parameter to specify additional Libxml parameters.
     * </p>
     *
     * @return bool true on success or false on failure. If called statically, returns a
     * DOMDocument and issues E_STRICT
     * warning.
     * @since 5.0
     */
    public function loadHTMLFile($filePath, $options = 0)
    {
        return $this->loadHTML(file_get_contents($filePath), $options);
    }

    // ------------------------------------------------------------------------

    /**
     * Document::__toString
     *
     * Convert document into HTML source code string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->saveHTML();
    }
}