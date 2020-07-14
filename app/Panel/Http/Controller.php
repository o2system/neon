<?php
/**
 * This file is part of the Circle Creative Web Application Project Boilerplate.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @author         PT. Lingkar Kreasi (Circle Creative)
 *  @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */
// ------------------------------------------------------------------------

namespace App\Panel\Http;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Contents\Lists\Unordered;
use O2System\Framework\Models\Sql\System\Modules;
use O2System\Framework\Models\Sql\System\Users\Notifications;

/**
 * Class Controller
 *
 * @package App\Http
 */
class Controller extends \App\Http\Controller
{
    /**
     * Controller::__reconstruct
     */
    public function __reconstruct()
    {
        parent::__reconstruct();
        loader()->helper('Presenter');

        presenter()->meta->title->prepend('Nitro');

        $className = get_class_name($this);

        // Set Model
        if (empty($this->model)) {
            $controllerClassName = get_called_class();
            $modelClassName = str_replace('Panel\Controllers', 'Api\Models', $controllerClassName);

            if (class_exists($modelClassName)) {
                $this->model = new $modelClassName();
            } else {
                $modelClassName = str_replace('Panel\Controllers', 'Panel\Models', $controllerClassName);

                if (class_exists($modelClassName)) {
                    $this->model = new $modelClassName();
                }
            }
        } elseif (class_exists($this->model)) {
            $this->model = new $this->model();
        }

        // Set Page Title and Header
        presenter()->page
            ->setHeader(strtoupper($className))
            ->setTitle(strtoupper($className));

        // Set Breadcrumb
        presenter()->page->breadcrumb->attributes->addAttributeClass('p-0 border-0 m-0');

        // Set Navigations
        $navigationsSets = [];
        $navigationsConfigFilePath = globals()->app->getPath() . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'Navigations.php';
        if(is_file($navigationsConfigFilePath)) {
            include_once($navigationsConfigFilePath);

            if(isset($navigations)) {
                $navigationsSets = $navigations;
                unset($navigations);
            }
        }

        if(isset($navigationsSets['sidebar']['system'])) {
            $systemNavigationsSets['sidebar']['system'] = $navigationsSets['sidebar']['system'];
            unset($navigationsSets['sidebar']['system']);
        }

        // Load Modules
        $modules = models(Modules::class)->findWhere(['id_parent' => globals()->app->id]);
        if($modules->count()) {
            foreach($modules as $module) {
                $navigationsConfigFilePath = PATH_ROOT . trim(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $module->path), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'Navigations.php';
                if(is_file($navigationsConfigFilePath)) {
                    include_once($navigationsConfigFilePath);

                    if(isset($navigations)) {
                        $navigationsSets = array_merge_recursive($navigationsSets, $navigations);
                        unset($navigations);
                    }
                }
            }
        }

        if(isset($systemNavigationsSets)) {
            $navigationsSets = array_merge_recursive($navigationsSets, $systemNavigationsSets);
        }

        if(!empty($navigationsSets)) {
            foreach($navigationsSets as $group => $lists) {
                $navigation = presenter()->navigations->create($group);
                if($navigation instanceof Unordered) {
                    foreach($lists as $list) {
                        $list['type'] = empty($list['type']) ? 'item' : $list['type'];

                        if($list['type'] === 'group') {
                            $list['settings'] = empty($list['settings']) ? null : $list['settings'];

                            add_sidebar_navigation_group($list['name'], $list['icon'], $list['settings']);

                            if(isset($list['items'])) {
                                foreach($list['items'] as $item) {
                                    $item['href'] = empty($item['href']) ? '#' : $item['href'];
                                    $item['icon'] = empty($item['icon']) ? null : $item['icon'];
                                    $item['childs'] = empty($item['childs']) ? [] : $item['childs'];

                                    add_sidebar_navigation_item($item['name'], $item['href'], $item['icon'], $item['childs']);
                                }
                            }
                        } else {
                            $list['href'] = empty($list['href']) ? '#' : $list['href'];
                            $list['icon'] = empty($list['icon']) ? null : $list['icon'];
                            $list['childs'] = empty($list['childs']) ? [] : $list['childs'];

                            add_sidebar_navigation_item($list['name'], $list['href'], $list['icon'], $list['childs']);
                        }
                    }
                }
            }
        }

        // Set Notifications
        presenter()->offsetSet('notifications', models(Notifications::class)->findWhere([
            'id_sys_user' => session()->account->id,
            'status' => 'UNSEEN'
        ]));
    }

    // ------------------------------------------------------------------------

    /**
     * Controller::route
     *
     * @param string $method
     * @param array $args
     */
    public function route($method, array $args = [])
    {
        if(in_array($method, ['edit', 'add'])) {
            $method = 'form';
        }

        if(method_exists($this, $method)) {
            call_user_func_array([$this, $method], $args);
        }
    }
}
