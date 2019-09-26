<?php
/**
 * Loan installments calculation helpers.
 *
 * @author        Steeve Andrian Salim
 * @copyright     Copyright (c) Steeve Andrian Salim
 */
// ------------------------------------------------------------------------

/**
 * calculate_monthly_flat_rate_installments
 *
 * Calculation of loans with flat interest rates.
 *
 * @param  float $loan_amount
 * @param  int   $interest_rate
 * @param  int   $num_of_months
 *
 * @return array
 */
if ( ! function_exists('calculate_monthly_flat_rate_installments')) {
    function calculate_monthly_flat_rate_installments($loan_amount, $interest_rate, $num_of_months = 12)
    {
        $principal_installments = $loan_amount / $num_of_months;
        $loan_interest = ($loan_amount * ($interest_rate / 100) / $num_of_months);
        $monthly_installment = $principal_installments + $loan_interest;

        $monthly_installments = [];

        for ($month = 1; $month <= $num_of_months; $month++) {
            $monthly_installments[ $month ] = $monthly_installment;
        }

        return $monthly_installments;
    }
}

// ------------------------------------------------------------------------

/**
 * calculate_monthly_sliding_rate_installments
 *
 * Credit calculation with effective interest rate / sliding rate.
 *
 * @param  float $loan_amount
 * @param  int   $interest_rate
 * @param  int   $num_of_months
 *
 * @return array
 */
if ( ! function_exists('calculate_monthly_sliding_rate_installments')) {
    function calculate_monthly_sliding_rate_installments($loan_amount, $interest_rate, $num_of_months = 12)
    {
        $principal_installments = $loan_amount / $num_of_months;

        $monthly_installments = [];

        for ($month = 1; $month <= $num_of_months; $month++) {
            $monthly_installments[ $month ] = $principal_installments + (((($loan_amount - (($month - 1) * $principal_installments))) * ($interest_rate / 100)) / 12);
        }

        return $monthly_installments;
    }
}

// ------------------------------------------------------------------------

/**
 * calculate_monthly_annuity_rate_installments
 *
 * Calculation of credit with annuity interest rates.
 *
 * @param  float $loan_amount
 * @param  int   $interest_rate
 * @param  int   $num_of_months
 *
 * @return array
 */
if ( ! function_exists('calculate_monthly_annuity_rate_installments')) {
    function calculate_monthly_annuity_rate_installments($loan_amount, $interest_rate, $num_of_months = 12)
    {
        $monthly_interest = ($interest_rate / 12) / 100;
        $dividing_factor = 1 - (1 / pow(1 + $monthly_interest, $num_of_months));
        $monthly_installment = $loan_amount / ($dividing_factor / $monthly_interest);

        $monthly_installments = [];

        for ($month = 1; $month <= $num_of_months; $month++) {
            $monthly_installments[ $month ] = $monthly_installment;
        }

        return $monthly_installments;
    }
}

// ------------------------------------------------------------------------

/**
 * calculate_monthly_flat_floating_rate_installments
 *
 * Calculation of loans with fixed and floating interest rates.
 *
 * @example  calculate_monthly_flat_floating_rate_installments(500000000, [1 => 10, 4 => 12, 8 => 14])
 *
 * @param  float $loan_amount
 * @param  int   $interest_rates
 * @param  int   $num_of_months
 *
 * @return array
 */
if ( ! function_exists('calculate_monthly_flat_floating_rate_installments')) {
    function calculate_monthly_flat_floating_rate_installments(
        $loan_amount,
        array $interest_rates,
        $num_of_months = 120
    ) {
        $principal_installments = $loan_amount / $num_of_months;

        $periods_years = array_keys($interest_rates);
        $periods_years_interest_rates = array_values($interest_rates);
        $num_of_periods_years = count($periods_years);

        $periods = [];
        $monthly_installments = [];
        $periods_rates = [];

        for ($n = 0; $n < $num_of_periods_years; $n++) {
            if ($n == 0) {
                $periods[ $n ][ 'start_month' ] = 1;
                $periods[ $n ][ 'end_month' ] = ($periods_years[ $n + 1 ] - $periods_years[ $n ]) * 12;
            } else {
                $periods[ $n ][ 'start_month' ] = $periods[ $n - 1 ][ 'end_month' ] + 1;

                if (isset($periods_years[ $n + 1 ])) {
                    $periods[ $n ][ 'end_month' ] = ($periods[ $n ][ 'start_month' ] - 1) + (($periods_years[ $n + 1 ] - $periods_years[ $n ]) * 12);
                } else {
                    $periods[ $n ][ 'end_month' ] = $num_of_months;
                }
            }

            $period_months = range($periods[ $n ][ 'start_month' ], $periods[ $n ][ 'end_month' ]);
            $num_of_period_months = count($period_months);

            foreach ($period_months as $period_month) {
                $periods_rates[ $period_month ][ 'interest_rate' ] = $periods_years_interest_rates[ $n ];
                $periods_rates[ $period_month ][ 'start_month' ] = $periods[ $n ][ 'start_month' ];
                $periods_rates[ $period_month ][ 'principal_installments' ] = $loan_amount / $num_of_period_months;
            }
        }

        $num_month = 1;
        foreach ($periods_rates as $period_month => $period_rate) {
            if ($period_month == $period_rate[ 'start_month' ] && $period_month > 1) {
                $num_month = 1;
            }

            $monthly_installments[ $period_month ] =  $period_rate[ 'principal_installments' ] + (((($loan_amount - (($num_month - 1) * $period_rate[ 'principal_installments' ]))) * ($period_rate[ 'interest_rate' ] / 100)) / 12);

            $num_month++;
        }
        
        return $monthly_installments;
    }
}
