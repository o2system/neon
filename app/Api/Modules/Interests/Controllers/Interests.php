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

namespace App\Api\Modules\Interests\Controllers;

use App\Api\Modules\Interests\Http\Controller;

// ------------------------------------------------------------------------


/**
 * Class Companies
 * @package App\Api\Modules\Companies\Controllers
 */
class Interests extends Controller
{
   public function calculate()
   {

       if($post = input()->post()){
            if($getInterestCompany = $this->model->findWhere([
                'id_company'    => $post->id_company,
                'id_product_category'    => $post->id_product_category,
            ])){
              if (count($getInterestCompany)) {
                $getInterestCompany = $getInterestCompany->first();
                $loanAmount = $post->amount - $post->downpayment;
                $calculate = calculate_monthly_sliding_rate_installments($loanAmount,$getInterestCompany->interest,$post->tenor);
                $this->sendPayload(ceil($calculate[1]));
              } else {
                $this->sendPayload('Service Provider has yet to determine the Products Categories Value');
              }
            } else {
              $this->sendPayload('Service Provider has yet to determine the Products Categories Value');
            }
       }else{
           $this->sendError(501,'bad requests should post requests');
       }
   }
}
