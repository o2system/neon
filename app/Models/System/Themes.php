<?php
/**
 * This file is part of the O2System Content Management System package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian
 * @copyright      Copyright (c) Steeve Andrian
 */
// ------------------------------------------------------------------------

namespace App\Models\System;

// ------------------------------------------------------------------------

use O2System\Framework\Datastructures\Module\Theme;
use O2System\Spl\Info\SplDirectoryInfo;

/**
 * Class Themes
 * @package App\Models\System
 */
class Themes
{
    public function all()
    {
        $themes = [];
        $directoryInfo = new SplDirectoryInfo( PATH_PUBLIC . 'themes' . DIRECTORY_SEPARATOR );

        foreach( $directoryInfo->getTree() as $themeName => $themeTree ) {
            if ( is_dir( $themePath = $directoryInfo->getRealPath() . $themeName . DIRECTORY_SEPARATOR ) ) {
                $themeObject = new Theme( $themePath );

                if ( $themeObject->isValid() ) {
                    $themes[ $themeName ] = $themeObject;
                }
            }
        }

        return $themes;
    }
}