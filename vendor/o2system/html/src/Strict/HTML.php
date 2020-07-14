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

namespace O2System\Html\Strict;

// ------------------------------------------------------------------------

use O2System\Html\Document;

/**
 * Class HTML
 *
 * @package O2System\HTML\Strict
 */
class HTML extends Document
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>O2System HTML</title>
</head>
<body>

</body>
</html>
HTML;

        parent::loadHTML($htmlTemplate);
    }
}