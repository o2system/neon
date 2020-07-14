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

namespace O2System\Html\Element;

// ------------------------------------------------------------------------

use O2System\Spl\Patterns\Structural\Composite\RenderableInterface;
use O2System\Spl\Patterns\Structural\Repository\AbstractRepository;

/**
 * Class Attributes
 *
 * @package O2System\Html\Element
 */
class Attributes extends AbstractRepository implements RenderableInterface
{
    /**
     * Attributes::setAttributeId
     *
     * @param string $id
     *
     * @return static
     */
    public function setAttributeId($id)
    {
        $this->addAttribute('id', $id);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Attributes::addAttribute
     *
     * @param string $name
     * @param string $value
     *
     * @return static
     */
    public function addAttribute($name, $value)
    {
        if ($name === 'class') {
            $this->addAttributeClass($value);
        } elseif ($name === 'style') {
            $value = rtrim($value, ';') . ';';
            $value = explode(';', $value);
            $value = array_filter($value);

            foreach ($value as $style) {
                if (preg_match_all("/(.*)(: )(.*)/", $style, $match)) {
                    $this->addAttributeStyle($match[ 1 ][ 0 ], $match[ 3 ][ 0 ]);
                } elseif (preg_match_all("/(.*)(:)(.*)/", $style, $match)) {
                    $this->addAttributeStyle($match[ 1 ][ 0 ], $match[ 3 ][ 0 ]);
                }
            }
        } elseif (is_string($value)) {
            $this->storage[ $name ] = trim($value);
        } else {
            $this->storage[ $name ] = $value;
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Attributes::addAttributeClass
     *
     * @param array|string $classes
     *
     * @return static
     */
    public function addAttributeClass($classes)
    {
        if (is_string($classes)) {
            $classes = explode(',', $classes);
        }

        $classes = array_map('trim', $classes);
        $classes = array_filter($classes);

        if ( ! $this->offsetExists('class')) {
            $this->storage[ 'class' ] = [];
        }

        $this->storage[ 'class' ] = array_merge($this->storage[ 'class' ], $classes);
        $this->storage[ 'class' ] = array_unique($this->storage[ 'class' ]);

        if (in_array('disabled', $this->storage[ 'class' ])) {
            $this->removeAttributeClass('active');
        }

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Attributes::removeAttributeClass
     *
     * @param array|string $classes
     */
    public function removeAttributeClass($classes)
    {
        if ($this->offsetExists('class')) {
            if (is_string($classes)) {
                $classes = explode(',', $classes);
            }

            $classes = array_map('trim', $classes);
            $classes = array_filter($classes);

            foreach ($classes as $class) {
                if (false !== ($key = array_search($class, $this->storage[ 'class' ]))) {
                    unset($this->storage[ 'class' ][ $key ]);
                } elseif (strpos($class, '*') !== false) {
                    $class = str_replace('*', '', $class);
                    foreach ($this->storage[ 'class' ] as $key => $value) {
                        if (preg_match("/\b$class\b/i", $value)) {
                            unset($this->storage[ 'class' ][ $key ]);
                        }
                    }
                }
            }

            if (count($this->storage[ 'class' ]) == 0) {
                unset($this->storage[ 'class' ]);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Attributes::addAttributeStyle
     *
     * @param string|array  $styles
     * @param string|null   $value
     *
     * @return static
     */
    public function addAttributeStyle($styles, $value = null)
    {
        if (is_string($styles)) {
            $styles = [$styles => $value];
        }

        if ( ! $this->offsetExists('style')) {
            $this->storage[ 'style' ] = [];
        }

        foreach ($styles as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $styles[ trim($key) ] = trim($value);
        }

        $this->storage[ 'style' ] = array_merge($this->storage[ 'style' ], $styles);

        return $this;
    }

    // ------------------------------------------------------------------------

    /**
     * Attributes::hasAttribute
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasAttribute($name)
    {
        if ($name === 'id') {
            return empty($this->storage[ 'id' ]) ? false : true;
        } elseif ($name === 'class') {
            return empty($this->storage[ 'class' ]) ? false : true;
        } else {
            return isset($this->storage[ $name ]);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Attributes::getAttribute
     *
     * @param string|null $name
     *
     * @return array|bool
     */
    public function getAttribute($name = null)
    {
        if (empty($name)) {
            return $this->storage;
        } elseif (isset($this->storage[ $name ])) {
            return $this->storage[ $name ];
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Attributes::removeAttribute
     *
     * @param string|array $attributes
     */
    public function removeAttribute($attributes)
    {
        if (is_string($attributes)) {
            $attributes = explode(',', $attributes);
        }

        $attributes = array_map('trim', $attributes);
        $attributes = array_filter($attributes);

        foreach ($attributes as $attribute) {
            if (array_key_exists($attribute, $this->storage)) {
                unset($this->storage[ $attribute ]);
            } elseif (strpos($attribute, '*') !== false) {
                $attribute = str_replace('*', '', $attribute);
                foreach ($this->storage as $key => $value) {
                    if (preg_match("/\b$attribute\b/i", $key)) {
                        unset($this->storage[ $key ]);
                    }
                }
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Attributes::getAttributeId
     *
     * @return bool
     */
    public function getAttributeId()
    {
        if ($this->hasAttributeId()) {
            return $this->storage[ 'id' ];
        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Attributes::hasAttributeId
     *
     * @return bool
     */
    public function hasAttributeId()
    {
        return (bool)empty($this->storage[ 'id' ]) ? false : true;
    }

    // ------------------------------------------------------------------------

    /**
     * Attributes::hasAttributeClass
     *
     * @param string $className
     *
     * @return bool
     */
    public function hasAttributeClass($className)
    {
        if ( ! $this->offsetExists('class')) {
            $this->storage[ 'class' ] = [];
        }

        return in_array($className, $this->storage[ 'class' ]);
    }

    // ------------------------------------------------------------------------

    /**
     * Attributes::getAttributeClass
     *
     * @return string
     */
    public function getAttributeClass()
    {
        if ( ! $this->offsetExists('class')) {
            $this->storage[ 'class' ] = [];
        }

        return implode(', ', $this->storage[ 'class' ]);
    }

    // ------------------------------------------------------------------------

    /**
     * Attributes::replaceAttributeClass
     *
     * @param string $class
     * @param string $replace
     */
    public function replaceAttributeClass($class, $replace)
    {
        if ($this->offsetExists('class')) {
            foreach ($this->storage[ 'class' ] as $key => $value) {
                if (preg_match("/\b$class\b/i", $value)) {
                    $this->storage[ 'class' ][ $key ] = str_replace($class, $replace, $value);
                }
            }

            if (count($this->storage[ 'class' ]) == 0) {
                unset($this->storage[ 'class' ]);
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Attributes::findAttributeClass
     *
     * @param string $class
     *
     * @return array|bool
     */
    public function findAttributeClass($class)
    {
        if ($this->offsetExists('class')) {
            $matches = [];

            foreach ($this->storage[ 'class' ] as $key => $value) {
                if (preg_match("/\b$class\b/i", $value)) {
                    $matches[] = $value;
                }
            }

            if (count($matches)) {
                return $matches;
            }

        }

        return false;
    }

    // ------------------------------------------------------------------------

    /**
     * Attributes::__toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    // ------------------------------------------------------------------------

    /**
     * Attributes::render
     *
     * @param array $options
     *
     * @return string
     */
    public function render(array $options = [])
    {
        $output = '';

        if ($this->count()) {
            foreach ($this->storage as $key => $value) {
                $output = trim($output);
                switch ($key) {
                    case 'style':
                        if (count($value) == 0) {
                            continue 2;
                        }

                        $output .= 'style="';
                        foreach ($value as $styleKey => $styleValue) {
                            $output .= $styleKey . ':' . $styleValue . ';';
                        }
                        $output .= '"';

                        break;
                    case 'class':
                        if (count($value) == 0) {
                            continue 2;
                        }
                        $output .= ' ' . $key . '="' . implode(' ', $value) . '"';
                        break;
                    case 'js':
                        $output .= ' ' . $key . '="' . $value . '"';
                        break;
                    default:
                        if (is_array($value)) {
                            if (count($value) == 0) {
                                continue 2;
                            }
                            $value = implode(', ', $value);
                        }

                        if (is_bool($value)) {
                            $value = $value === true ? 'true' : 'false';
                        }

                        if (in_array($key, [
                            'controls',
                            'disabled',
                            'readonly',
                            'autocomplete',
                            'checked',
                            'loop',
                            'autoplay',
                            'muted',
                        ])) {
                            $output .= ' ' . $key;
                        } else {
                            $output .= ' ' . $key . '="' . $value . '"';
                        }
                        break;
                }
            }
        }

        return $output;
    }
}