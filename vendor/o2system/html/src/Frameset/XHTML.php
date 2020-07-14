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

namespace O2System\Html\Frameset;

// ------------------------------------------------------------------------

use O2System\Html\Document;

/**
 * Class XHTML
 *
 * @package O2System\HTML\Frameset
 */
class XHTML extends Document
{
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
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>O2System HTML</title>
</head>
</html>
HTML;

        parent::loadHTML($htmlTemplate);
    }
}