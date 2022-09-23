<?php

namespace Tests\Feature\Http\Controllers\api;

use App\Helpers\HelperFunctions;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanPayments;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanControllerTest extends TestCase
{
    use RefreshDatabase;
    private $loan;
    private $adminToken;
    private $customerToken;
    const LOAN_AMOUNT = 10;
    const LOAN_TERM = 3;

    private function customerAuthenticate()
    {
        $customer = Customer::factory()->create();
        $token = $customer->createToken('loan-app', ['customer'])->plainTextToken;
        return $token;
    }

    private function adminAuthenticate()
    {
        $admin = Admin::factory()->create();
        $token = $admin->createToken('loan-app', ['admin'])->plainTextToken;
        return $token;
    }


    public function test_customer_apply_for_a_loan()
    {
        //Generate customer token
        $token = $this->customerAuthenticate();

        //Apply loan
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/loan/apply', [
            "amount" => self::LOAN_AMOUNT,
            "term" => self::LOAN_TERM
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'code' => $response['code'],
        ]);
        $this->assertEquals('Loan applied successfully!', $response['message']);
        $this->assertArrayHasKey('code', $response);
        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertArrayHasKey('payload', $response);
        $this->assertEquals(10, $response['payload']['amount']);
        $this->assertEquals(3, $response['payload']['term']);
        $this->assertEquals(0, $response['payload']['total_paid']);
        $this->assertEquals(10, $response['payload']['total_remain']);
    }

    public function test_approve_customer_loan_by_admin()
    {
        //create customer
        $customer = Customer::factory()->create();

        //create loan
        $loan = Loan::factory()->for($customer)->create();

        //Generate admin token
        $adminToken = $this->adminAuthenticate();

        //Approved loan
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $adminToken,
        ])->put("/api/loan/{$loan->id}/approve", []);

        $response->assertStatus(200);
        $this->assertEquals('Loan approved successfully!', $response['message']);
        $this->assertArrayHasKey('code', $response);
        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertArrayHasKey('payload', $response);
        $this->assertEquals($loan['amount'], $response['payload']['amount']);
        $this->assertEquals($loan['term'], $response['payload']['term']);
        $this->assertEquals(0, $response['payload']['total_paid']);
        $this->assertEquals($loan['amount'], $response['payload']['total_remain']);
        $this->assertArrayHasKey('loan_payments', $response['payload']);


        //loan payments should be equl to loan term
        $this->assertEquals($loan['term'], count($response['payload']['loan_payments']));

        $installmentDate = date("Y-m-d");
        $totalLoanInstallmentDue = 0;
        foreach ($response['payload']['loan_payments'] as $loan_payment) {
            //verify that installment create weekly
            $installmentDate = date("Y-m-d", strtotime($installmentDate . "+1 week"));
            $totalLoanInstallmentDue += $loan_payment['due_amount'];

            $this->assertEquals($installmentDate, $loan_payment['payment_due_date']);
            $this->assertEquals('pending', $loan_payment['status']);
            $this->assertEquals($loan['id'], $loan_payment['loan_id']);
        }

        $this->assertEquals($loan['amount'], $totalLoanInstallmentDue);
    }

    public function test_customer_paid_first_installment_of_loan()
    {
        //create new customer
        $customer = Customer::factory()->create();

        //generate access token
        $token = $customer->createToken('loan-app', ['customer'])->plainTextToken;

        //create new loan
        $loan = Loan::factory()->for($customer)->create();

        //Calculate loan installments
        $installments = HelperFunctions::calculateLoanInstallment($loan->amount,$loan->term, 'week');

        //Create loan installments
        $loanPayments = [];
        for ($i=0;$i<count($installments);$i++) {
            $loanPayments[] = LoanPayments::factory()->for($loan)->state([
                'due_amount' => $installments[$i]['amount'],
                'payment_due_date' => $installments[$i]['payment_due_date'],
            ])->create();
        }

        //Pay first installment
        $payment = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->put("api/loan/{$loanPayments[0]['id']}/pay", [
            'amount' => $loanPayments[0]['due_amount']
        ]);

        $payment->assertStatus(200);
        $this->assertEquals('Installment paid successfully.', $payment['message']);

        //check total paid amount should match with first installment amount
        $this->assertEquals($loanPayments[0]['due_amount'], $payment['payload']['total_paid']);
    }

    public function test_loan_status_automatically_paid_once_all_loan_installment_paid()
    {
        //create new customer
        $customer = Customer::factory()->create();

        //generate access token
        $token = $customer->createToken('loan-app', ['customer'])->plainTextToken;

        //create new loan
        $loan = Loan::factory()->for($customer)->create();

        //Calculate loan installments
        $installments = HelperFunctions::calculateLoanInstallment($loan->amount,$loan->term, 'week');

        //Create loan installments
        $loanPayments = [];
        for ($i=0;$i<count($installments);$i++) {
            $loanPayments[] = LoanPayments::factory()->for($loan)->state([
                'due_amount' => $installments[$i]['amount'],
                'payment_due_date' => $installments[$i]['payment_due_date'],
            ])->create();
        }

        //Paid full amount
        $payment = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->put("api/loan/{$loanPayments[0]['id']}/pay", [
            'amount' => $loan->amount
        ]);
        $payment->assertStatus(200);
        $this->assertEquals('Installment paid successfully.', $payment['message']);

        //verify customer loan amount
        $this->assertEquals($loan->amount, $payment['payload']['amount']);

        //verify customer total paid amount is actual loan amount
        $this->assertEquals($loan->amount, $payment['payload']['total_paid']);

        //loan status should be paid
        $this->assertEquals('paid', $payment['payload']['status']);

        //No remain amount for loan left
        $this->assertEquals(0, $payment['payload']['total_remain']);
    }

    public function test_admin_can_view_all_customers_and_loans()
    {
        //create new customer
        $customer = Customer::factory()->create();

        //create loan
        Loan::factory()->for($customer)->create();

        //Generate admin token
        $adminToken = $this->adminAuthenticate();

        //loans
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $adminToken,
        ])->get("api/loan/customers_loans");

        $response->assertStatus(200);
        $this->assertEquals('Loans', $response['message']);
        $this->assertArrayHasKey('code', $response);
        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertArrayHasKey('payload', $response);
    }

    public function test_customer_can_view_own_loans()
    {
        //create new customer
        $customer = Customer::factory()->create();

        //generate access token
        $token = $customer->createToken('loan-app', ['customer'])->plainTextToken;

        //create new loan
        Loan::factory()->for($customer)->create();

        //loans
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get("api/customer/loans");

        $response->assertStatus(200);
        $this->assertEquals('Loans', $response['message']);
        $this->assertArrayHasKey('code', $response);
        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertArrayHasKey('payload', $response);
    }
}
