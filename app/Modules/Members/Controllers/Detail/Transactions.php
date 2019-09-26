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

namespace App\Manage\Modules\Members\Controllers\Detail;

// ------------------------------------------------------------------------

use App\Manage\Modules\Members\Http\Controller;
use O2System\Spl\Datastructures\SplArrayObject;
use App\Api\Modules\Members\Models\Members;
/**
 * Class Members
 * @package App\Manage\Modules\Members\Controllers
 */
class Transactions extends Controller
{
	public $model = '\App\Api\Modules\Members\Models\Transactions';

	public function index($id = null)
	{
		$get = input()->get();
        if ($get == false || $get == null) {
            $get = new SplArrayObject([
                'get' => 'false'
            ]);
        }
            
        if ($id) {
            $modelMember = models(\App\Api\Modules\Members\Models\Members::class)->find($id);
            $modelMember->appendColumns = [
                'metadata'
            ];
            if (false !== ($data = $modelMember)) {
                $this->presenter->page->setHeader('PAGE_DETAIL');
                $vars = [
                    'entries' => range(10, 100, 10),
                    'get' => $get,
                    'member' => $data,
                    'transactions' => $data->transactions_filter($get),
                ];
                view('detail/transactions/index', $vars);
            } else {
                $this->output->send(204);
            }
        } else {
            $this->output->send(404);
        }
	}

    public function table($id = null)
    {
        $get = input()->get();
        if ($get == false || $get == null) {
            $get = new SplArrayObject([
                'get' => 'false'
            ]);
        }
            
        if ($id) {
            $modelMember = models(\App\Api\Modules\Members\Models\Members::class)->find($id);
            $modelMember->appendColumns = [
                'metadata'
            ];
            if (false !== ($data = $modelMember)) {
                $this->presenter->page->setHeader('PAGE_DETAIL');
                $vars = [
                    'entries' => range(10, 100, 10),
                    'get' => $get,
                    'member' => $data,
                    'transactions' => $data->transactions_filter($get),
                ];
                view('detail/transactions/table', $vars);
            } else {
                $this->output->send(204);
            }
        } else {
            $this->output->send(404);
        }
    }
}
