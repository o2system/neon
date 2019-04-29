<?php
/**
 * This file is part of the NEO ERP Application.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         PT. Lingkar Kreasi (Circle Creative)
 * @copyright      Copyright (c) PT. Lingkar Kreasi (Circle Creative)
 */
// ------------------------------------------------------------------------

namespace App\Http;

// ------------------------------------------------------------------------

use App\Api\Modules\Company\Models\Company;
use App\Http\Presenter\Page;
use O2System\Framework\Libraries\Ui\Contents\Link;

/**
 * Class Controller
 *
 * @package App\Http
 */
class Controller extends \O2System\Framework\Http\Controller
{
    /**
     * Controller::__construct
     */
    public function __reconstruct()
    {
        presenter()->store('page', new Page());
        $className = get_class_name($this);

        presenter()->page
            ->setHeader('O2System Neon')
            ->setDescription('O2System Neon Boilerplate');

        presenter()->page->breadcrumb->createList(new Link(
            language(strtoupper($className)),
            base_url(strtolower($className))
        ));
    }

    // ------------------------------------------------------------------------

    /**
     * Controller::route
     *
     * @param string $method
     * @param array  $args
     */
    public function route($method, array $args = [])
    {
        if (in_array($method, ['add', 'edit'])) {
            call_user_func_array([&$this, 'form'], $args);
        } else {
            call_user_func_array([&$this, $method], $args);
        }
    }
}
