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

namespace App\Controllers;

// ------------------------------------------------------------------------

use App\Http\AccessControl\Middleware\UserAuthentication;
use App\Http\AccessControl\Middleware\UserAuthorization;
use App\Http\Controller;
use O2System\Spl\Info\SplFileInfo;

/**
 * Class Pages
 * @package App\Controllers
 */
class Pages extends Controller
{
    public function __reconstruct()
    {
        parent::__reconstruct();

        presenter()->page->file->getMTime();

        if (false !== ($presets = presenter()->page->getPresets())) {
            switch ($presets->offsetGet('access')) {
                default:
                case 'public':
                    // Doesn't use any middleware

                    if (server_request()->getUri()->getSegments()->getPart(1) === 'login') {
                        if (session()->has('account')) {
                            redirect_url('stats');
                        }
                    }

                    break;
                case 'authenticated':
                    // Register user authentication middleware
                    $this->middleware->register(new UserAuthentication());
                    break;
                case 'authorized':
                    // Register user authentication middleware
                    $this->middleware->register(new UserAuthorization());
                    break;
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Pages::index
     */
    public function index()
    {
        if (presenter()->page->file instanceof SplFileInfo) {
            view()->page(presenter()->page->file->getRealPath());
        }
    }
}
