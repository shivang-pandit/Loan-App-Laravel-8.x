<?php
namespace App\Helpers;
class HelperFunctions
{
    public static function calculateLoanInstallment($total, $term, $type)
    {
        $installments = [];
        $terms = array_fill(0, $term, round($total/$term, 5));

        if($total%$term > 0) {
            $terms[count($terms)-1] += $total - array_sum($terms);
        }

        $installmentDate = date("Y-m-d");

        for($i=0;$i<count($terms);$i++) {
            $installmentDate = date("Y-m-d", strtotime($installmentDate . "+1 ".$type));
            $installments[] = [
                'amount' => $terms[$i],
                'payment_due_date' => $installmentDate
            ];
        }

        return $installments;

    }
}