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

namespace App\Http\Presenter\Menus;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Contents\Icon;
use O2System\Framework\Libraries\Ui\Contents\Link;
use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Group
 * @package App\Http\Presenter\Menus
 */
class Group extends Element
{
    public $items;

    // ------------------------------------------------------------------------

    /**
     * Group::__construct
     *
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct('li');
        $this->attributes->addAttributeClass('nav-small-cap');

        $this->setName($name);

        $this->items = new Items();
    }

    // ------------------------------------------------------------------------

    /**
     * Group::setName
     *
     * @param string $name
     *
     * @return static
     */
    public function setName($name)
    {
        $this->textContent->push(language()->getLine($name));
        $this->entity->setEntityName(camelcase($name));

        return $this;
    }

    // ------------------------------------------------------------------------

    public function setItems(array $items)
    {
        foreach ($items as $item) {
            $this->addItem($item);
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    public function addItem(array $item)
    {
        $li = new Element('li');

        $label = new Element('span');
        $label->textContent->push(language()->getLine($item[ 'label' ]));

        if (empty($item[ 'href' ])) {
            $item[ 'href' ] = 'javascript:void(0);';
        }

        $link = new Link($label, $item[ 'href' ]);

        if (isset($item[ 'icon' ])) {
            $link->setIcon(new Icon($item[ 'icon' ]));
        }

        if (strpos(current_url(), $item[ 'href' ]) !== false) {
            $li->attributes->addAttributeClass('active');
            $link->attributes->addAttributeClass('active');
        }

        $li->childNodes->push($link);

        if (isset($item[ 'add' ])) {
            $add = new Link(language()->getLine('Add'), $item[ 'add' ]);
            $add->attributes->addAttributeClass('sidebar-button');
            $li->childNodes->push($add);
        }

        if (isset($item[ 'customize' ])) {
            $customize = new Link(language()->getLine('Customize'), $item[ 'customize' ]);
            $customize->attributes->addAttributeClass('sidebar-button');
            $li->childNodes->push($customize);
        }

        $this->items->store(camelcase(strtolower($item[ 'label' ])), $li);

        return $this;
    }
}