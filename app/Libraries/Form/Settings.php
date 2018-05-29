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

namespace App\Libraries\Form;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Components\Form\Elements\Select;

/**
 * Class Settings
 * @package App\Libraries\Form
 */
class Settings
{
    private $meta = [];

    public function __construct(array $meta = [])
    {
        $this->meta = $meta;
    }

    public function language($selected = 'en-US')
    {
        $select = new Select(['name' => 'language', 'class' => 'select2']);

        $registry = language()->getRegistry();

        foreach ($registry as $key => $language) {
            $properties = $language->getProperties();
            $options[ $properties->name ] = $key;
        }

        $select->createOptions($options, $selected);

        return $select;
    }

    public function format($selected = 'STANDARD')
    {
        $select = new Select(['name' => 'format', 'class' => 'select2']);
        $select->createOptions([
            'Standard' => 'STANDARD',
            'Image'    => 'IMAGE',
            'Video'    => 'VIDEO',
            'Audio'    => 'AUDIO',
            'Gallery'  => 'GALLERY',
            'Quote'    => 'QUOTE',
        ], $selected);

        return $select;
    }

    public function recordStatus($selected = 'PUBLISH')
    {
        $select = new Select(['name' => 'status']);
        $select->createOptions([
            'Publish'   => 'PUBLISH',
            'Draft'     => 'DRAFT',
            'Scheduled' => 'SCHEDULED',
        ], $selected);

        return $select;
    }

    public function visibility($selected = 'PUBLIC')
    {
        $select = new Select(['name' => 'visibility']);
        $select->createOptions([
            'Public'    => 'PUBLIC',
            'Protected' => 'PROTECTED',
            'Private'   => 'PRIVATE',
        ], $selected);

        return $select;
    }

    public function metarobot($selected = 'NONE')
    {
        $select = new Select(['name' => 'meta[robot]']);
        $select->createOptions([
            'None'       => 'NONE',
            'No ODP'     => 'NO_ODP',
            'No YDIR'    => 'NO_YDIR',
            'No Archive' => 'NO_ARCHIVE',
            'No Snippet' => 'NO_SNIPPET',
        ], $selected);

        return $select;
    }
}