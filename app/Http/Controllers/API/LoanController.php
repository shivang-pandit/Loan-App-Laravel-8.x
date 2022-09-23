<?php

namespace App\Http\Controllers\API;

use App\Helpers\HelperFunctions;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanPayments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use RestResponseFactory;

class LoanController extends Controller
{
    public function apply(Request $request) {
        $req = $request->all();
        $validatorObj = Validator::make($req, [
            'amount' => 'required|numeric|gt:0',
            'term' => 'required|integer|gt:0',
        ]);

        $errorsArr = array();

        if ($validatorObj->fails()) {
            $errorsArr = array_merge($errorsArr, $validatorObj->messages()->all(':message'));
        }

        if (count($errorsArr) > 0) {
            return RestResponseFactory::badrequest([], implode("", $errorsArr))->toJSON();
        }


        $customer = Auth::user();
        if(!$customer) {
            return RestResponseFactory::not_found([], 'Customer not found.')->toJSON();
        }

        $activeLoan = Loan::where('customer_id',$customer['id'])->whereIn('status',['pending','approved'])->first();
        if($activeLoan) {
            return RestResponseFactory::forbidden([], 'You can not apply another loan if your loan is currently active.')->toJSON();
        }

        $loan = Loan::create([
            'customer_id' => $customer['id'],
            'amount' => $req['amount'],
            'term' => $req['term'],
        ]);


        return RestResponseFactory::success($loan, 'Loan applied successfully!')->toJSON();
    }

    public function approveLoan($loan_id) {
        $admin = Auth::user();
        if(!$admin) {
            return RestResponseFactory::not_found([], 'Admin not found.')->toJSON();
        }

        $loan = Loan::find($loan_id);
        if(!$loan) {
            return RestResponseFactory::not_found([], 'Loan not found.')->toJSON();
        }

        if($loan->status === "approved") {
            return RestResponseFactory::error([], 'Loan already processed.')->toJSON();
        }

        if($loan->status === "disapproved") {
            return RestResponseFactory::error([], 'Loan already disapproved.')->toJSON();
        }

        if($loan->status === "paid") {
            return RestResponseFactory::error([], 'Loan already closed.')->toJSON();
        }

        $loan->update([
            'status' => 'approved',
            'approved_by_id' => $admin['id']
        ]);


        $installments = HelperFunctions::calculateLoanInstallment($loan->amount,$loan->term, 'week');
        $loanPayments = [];
        for ($i=0;$i<count($installments);$i++) {
            $loanPayments[] = new LoanPayments([
                'due_amount' => $installments[$i]['amount'],
                'payment_due_date' => $installments[$i]['payment_due_date'],

            ]);
        }
        if(count($loanPayments)>0) {
            $loan->loanPayments()->saveMany($loanPayments);
        }

        return RestResponseFactory::success($loan->load('loanPayments'), 'Loan approved successfully!')->toJSON();
    }

    public function customerLoans() {
        $customer = Auth::user();
        if(!$customer) {
            return RestResponseFactory::not_found([], 'Customer not found.')->toJSON();
        }

        $loan = Loan::with('loanPayments')->where('customer_id',$customer['id'])->get();
        if(!$loan) {
            return RestResponseFactory::not_found([], 'You haven\'t applied any loan yet.')->toJSON();
        }

        return RestResponseFactory::success($loan, 'Loans')->toJSON();
    }

    public function customersLoan() {
        $admin = Auth::user();
        if(!$admin) {
            return RestResponseFactory::not_found([], 'Admin not found.')->toJSON();
        }


        $loan = Customer::with('loans.loanPayments')->get();
        if(!$loan) {
            return RestResponseFactory::not_found([], 'You haven\'t applied any loan yet.')->toJSON();
        }

        return RestResponseFactory::success($loan, 'Loans')->toJSON();
    }

    public function payInstallment($loan_payment_id, Request $request) {
        $req = $request->all();
        $validatorObj = Validator::make($req, [
            'amount' => 'required|numeric|gt:0',
        ]);

        $errorsArr = array();

        if ($validatorObj->fails()) {
            $errorsArr = array_merge($errorsArr, $validatorObj->messages()->all(':message'));
        }

        if (count($errorsArr) > 0) {
            return RestResponseFactory::badrequest([], implode("", $errorsArr))->toJSON();
        }

        $customer = Auth::user();
        if(!$customer) {
            return RestResponseFactory::not_found([], 'Customer not found.')->toJSON();
        }

        $loanPayment = LoanPayments::find($loan_payment_id);
        if(!$loanPayment) {
            return RestResponseFactory::not_found([], 'Invalid payment loan detail.')->toJSON();
        }

        $loan = Loan::find($loanPayment->loan_id);

        if($loan['customer_id'] !== $customer['id']) {
            return RestResponseFactory::badrequest([], 'Invalid loan detail.')->toJSON();
        }

        if($loan['status'] === "paid") {
            return RestResponseFactory::badrequest([], 'Loan has been fully paid.')->toJSON();
        }

        if($loanPayment['status'] === "paid") {
            return RestResponseFactory::badrequest([], 'Installment already paid.')->toJSON();
        }

        if($req['amount'] > $loan['total_remain']) {
            return RestResponseFactory::badrequest([], 'You can not pay more than '.$loan['total_remain'])->toJSON();
        }

        if(($req['amount'] < $loan['total_remain']) && $loanPayment['due_amount'] > $req['amount']) {
            return RestResponseFactory::badrequest([], 'Your payment amount should not less then due amount('.$loanPayment['due_amount'].')')->toJSON();
        }

        $loanPayment->update([
            'paid_amount' => (float)$req['amount'],
            'status' => 'paid'
        ]);

        return RestResponseFactory::success($loan->refresh(), 'Installment paid successfully.')->toJSON();
    }
}
