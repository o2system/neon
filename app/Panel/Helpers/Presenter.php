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

if( ! function_exists('add_sidebar_navigation_group') )
{
    function add_sidebar_navigation_group($name, $icon = null, $settingHref = null)
    {
        if(presenter()->navigations instanceof \O2System\Framework\Http\Presenter\Repositories\Navigations) {
            if( ! presenter()->navigations->offsetExists('sidebar')) {
                presenter()->navigations->create('sidebar');
            }
        }

        if($sidebar = presenter()->navigations->offsetGet('sidebar')) {
            $list = $sidebar->createList();
            $list->attributes->addAttributeClass('nav-small-cap');

            // Title
            $list->childNodes->push($container = new \O2System\Html\Element('div'));
            $container->childNodes->push($icon = new \O2System\Framework\Libraries\Ui\Contents\Icon($icon));
            $container->childNodes->push($span = new \O2System\Html\Element('span'));
            $span->textContent->push(language($name));

            // Setting
            if(isset($settingHref)) {
                if(strpos($settingHref, 'http') === false) {
                    $settingHref = base_url($settingHref);
                }

                $list->childNodes->push($container = new \O2System\Html\Element('div'));
                $container->attributes->addAttributeClass('ml-auto');
                $container->childNodes->push($span = new \O2System\Html\Element('span'));
                $span->childNodes->push($link = new \O2System\Framework\Libraries\Ui\Contents\Link());
                $link->attributes->addAttributeClass('text-white');
                $link->setAttributeHref($settingHref);
                $link->setIcon(new \O2System\Framework\Libraries\Ui\Contents\Icon('fas fa-cog'));
            }
        }
    }
}

if( ! function_exists('add_sidebar_navigation_item') )
{
    function add_sidebar_navigation_item($name, $href, $icon = null, array $childs = [])
    {
        $segments = [];

        if(strpos($href, 'http') === false) {
            $segments = explode('/', $href);
            $href = base_url($href);
        }

        if(presenter()->navigations instanceof \O2System\Framework\Http\Presenter\Repositories\Navigations) {
            if( ! presenter()->navigations->offsetExists('sidebar')) {
                presenter()->navigations->create('sidebar');
            }
        }

        if($sidebar = presenter()->navigations->offsetGet('sidebar')) {
            $list = $sidebar->createList();
            $list->attributes->addAttributeClass('sidebar-item');
            $list->childNodes->push($link = new \O2System\Framework\Libraries\Ui\Contents\Link());

            $link->attributes->addAttribute('href', $href);
            $link->attributes->addAttributeClass('sidebar-link');

            if(server_request()->getUri()->segments->has(reset($segments)) && server_request()->getUri()->segments->has(end($segments))) {
                $link->attributes->addAttributeClass('active');
            }

            $link->setIcon($icon = new \O2System\Framework\Libraries\Ui\Contents\Icon($icon));

            // Text
            $link->childNodes->push($span = new \O2System\Html\Element('span'));
            $span->attributes->addAttributeClass('hide-menu');
            $span->textContent->push(language($name));

            if(count($childs)) {
                $link->attributes->addAttributeClass('has-arrow');
                $link->setAttributeHref('#');
                $link->attributes->addAttribute('aria-expanded', 'false');

                $list->childNodes->push($childList = new \O2System\Framework\Libraries\Ui\Contents\Lists\Unordered([
                    'aria-expanded' => 'false',
                    'class' => 'collapse first-level'
                ]));

                foreach($childs as $child) {
                    $childListItem = $childList->createList();
                    $childListItem->attributes->addAttributeClass('sidebar-item');
                    $childListItem->childNodes->push($link = new \O2System\Framework\Libraries\Ui\Contents\Link());

                    if(strpos($child['href'], 'http') === false) {
                        $segments = explode('/', $child['href']);
                        $child['href'] = base_url($child['href']);
                    }

                    $link->attributes->addAttribute('href', $child['href']);
                    $link->attributes->addAttributeClass('sidebar-link');
                    $link->setIcon($icon = new \O2System\Framework\Libraries\Ui\Contents\Icon($child['icon']));


                    // Text
                    $link->childNodes->push($span = new \O2System\Html\Element('span'));
                    $span->attributes->addAttributeClass('hide-menu');
                    $span->textContent->push(language($child['name']));
                }
            }
        }
    }
}