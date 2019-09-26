<?php
/**
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */
// ------------------------------------------------------------------------

namespace App\Manage\Controllers;

// ------------------------------------------------------------------------

use App\Manage\Http\AccessControl\Controllers\AuthenticatedController;
use App\Api\Modules\Companies\Models\Companies;
use App\Api\Modules\Testimonials\Models\Testimonials;
use App\Api\Modules\Transactions\Models\Logs;

/**
 * Class Dashboard
 *
 * @package App\Controllers
 */
class Dashboard extends AuthenticatedController
{
    /**
     * Index
     */
    public function index()
    {
    	if (!$total_companies = count(models(Companies::class)->all())) {
    		$total_companies = 0;
    	}

    	if (!$total_testimonials = count(models(Testimonials::class)->all())) {
    		$total_testimonials = 0;
    	}

        $total_sales = 0;
    	models(Logs::class)->qb->select('transactions_logs.*');
    	models(Logs::class)->qb->join('transactions', 'transactions.id = transactions_logs.id_transaction');
    	models(Logs::class)->qb->where('transactions_logs.status', 'DELIVERED');
    	models(Logs::class)->qb->where('transactions.reference_model', '\App\Api\Modules\Products\Models\Products');
    	if ($logs = models(Logs::class)->all()) {
            if ( ! $total_sales = count($logs)) {
                $total_sales = 0;
            }
    	}
        presenter()->page->setHeader( 'Dashboard' );

        view('dashboard/index', [
        	'total_companies' => $total_companies,
        	'total_testimonials' => $total_testimonials,
        	'total_sales' => $total_sales
        ]);
    }

    public function logout()
    {
        services( 'user' )->logout();
        redirect_url('login');
    }
}