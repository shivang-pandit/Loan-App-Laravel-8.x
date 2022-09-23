<?php

namespace Tests\Unit;

use App\Helpers\HelperFunctions;
use PHPUnit\Framework\TestCase;

class HelperFuncationsTest extends TestCase
{
    public function test_check_installments_calculated_properly()
    {
        $loanAmount = 10;
        $term = 3;
        $loanPayments = HelperFunctions::calculateLoanInstallment($loanAmount, $term, 'week');

        $installmentDate = date("Y-m-d");
        $totalLoanInstallmentDue = 0;
        foreach ($loanPayments as $loan_payment) {
            //verify that installment created weekly
            $installmentDate = date("Y-m-d", strtotime($installmentDate . "+1 week"));
            $totalLoanInstallmentDue += $loan_payment['amount'];

            $this->assertEquals($installmentDate, $loan_payment['payment_due_date']);
        }

        $this->assertEquals(10, $totalLoanInstallmentDue);
        $this->assertTrue(true);
    }
}
